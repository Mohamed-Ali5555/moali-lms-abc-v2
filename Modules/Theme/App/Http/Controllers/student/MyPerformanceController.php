<?php

namespace Modules\Theme\App\Http\Controllers\student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use App\Models\User;
use App\Models\Watch_history;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('ar');
        $period = in_array($request->get('period'), ['week', 'month'], true)
            ? $request->get('period')
            : 'week';

        $now = Carbon::now();
        if ($period === 'month') {
            $start = $now->copy()->subDays(29)->startOfDay();
            $buckets = $this->buildDayBuckets($start, $now, 30);
            $bucketMode = 'day';
        } else {
            $start = $now->copy()->subDays(6)->startOfDay();
            $buckets = $this->buildDayBuckets($start, $now, 7);
            $bucketMode = 'day';
        }

        $courseIds = Enrollment::where('user_id', $user->id)->pluck('course_id')->unique()->values();

        $watchSeries = $this->buildWatchSeries($user->id, $buckets, $start);
        $examSeries = $this->buildExamSeries($user->id, $buckets, $start);
        $courseProgress = $this->buildCourseProgress($user->id, $courseIds);
        $examStats = $this->buildExamStats($user->id);
        $leaderboard = $this->buildCategoryLeaderboard($user);
        $tips = $this->buildTips($courseProgress, $examStats, $watchSeries, $leaderboard);

        $avgProgress = collect($courseProgress)->avg('progress');
        $avgProgress = $avgProgress === null ? 0 : round($avgProgress, 1);

        return view('theme::student.my_performance.index', [
            'period' => $period,
            'bucketMode' => $bucketMode,
            'chartLabels' => array_column($buckets, 'label'),
            'watchSeries' => array_column($watchSeries, 'value'),
            'examSeries' => array_column($examSeries, 'value'),
            'examCountSeries' => array_column($examSeries, 'count'),
            'courseProgress' => $courseProgress,
            'examStats' => $examStats,
            'leaderboard' => $leaderboard,
            'tips' => $tips,
            'kpis' => [
                'courses' => $courseIds->count(),
                'avg_progress' => $avgProgress,
                'quizzes' => $examStats['attempts'],
                'avg_score' => $examStats['avg_score'],
                'pass_rate' => $examStats['pass_rate'],
                'rank' => $leaderboard['my_rank'],
                'peers' => $leaderboard['peers_count'],
            ],
        ]);
    }

    protected function buildDayBuckets(Carbon $start, Carbon $end, int $days): array
    {
        $buckets = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->format('Y-m-d');
            $buckets[$key] = [
                'key' => $key,
                'label' => $day->format($days > 10 ? 'd/m' : 'D d/m'),
                'start' => $day->copy()->startOfDay(),
                'end' => $day->copy()->endOfDay(),
            ];
        }

        return array_values($buckets);
    }

    protected function buildWatchSeries(int $userId, array $buckets, Carbon $start): array
    {
        $rows = Watch_history::query()
            ->where('student_id', $userId)
            ->where('updated_at', '>=', $start)
            ->get(['updated_at', 'course_progress', 'completed_lesson']);

        $byDay = [];
        foreach ($buckets as $bucket) {
            $byDay[$bucket['key']] = 0;
        }

        foreach ($rows as $row) {
            $key = Carbon::parse($row->updated_at)->format('Y-m-d');
            if (!array_key_exists($key, $byDay)) {
                continue;
            }

            $completed = json_decode($row->completed_lesson ?? '[]', true);
            $lessonCount = is_array($completed) ? count($completed) : 0;
            // Weight activity by progress snapshot so empty updates still count as 1.
            $byDay[$key] += max(1, (int) round(((float) ($row->course_progress ?? 0)) / 20) + $lessonCount);
        }

        return array_map(static function ($bucket) use ($byDay) {
            return [
                'key' => $bucket['key'],
                'label' => $bucket['label'],
                'value' => (int) ($byDay[$bucket['key']] ?? 0),
            ];
        }, $buckets);
    }

    protected function buildExamSeries(int $userId, array $buckets, Carbon $start): array
    {
        $submissions = QuizSubmission::query()
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereNull('status');
            })
            ->where('updated_at', '>=', $start)
            ->with('lesson:id,total_mark,pass_mark,title,lesson_type')
            ->get();

        $scoresByDay = [];
        $countsByDay = [];
        foreach ($buckets as $bucket) {
            $scoresByDay[$bucket['key']] = [];
            $countsByDay[$bucket['key']] = 0;
        }

        $questionCounts = $this->questionCountsFor(
            $submissions->pluck('quiz_id')->unique()->filter()->all()
        );

        foreach ($submissions as $submission) {
            $lesson = $submission->lesson;
            if (!$lesson || ($lesson->lesson_type ?? '') !== 'quiz') {
                continue;
            }

            $key = Carbon::parse($submission->updated_at)->format('Y-m-d');
            if (!array_key_exists($key, $scoresByDay)) {
                continue;
            }

            $scorePct = $this->scorePercent($submission, $lesson, $questionCounts);
            $scoresByDay[$key][] = $scorePct;
            $countsByDay[$key]++;
        }

        return array_map(static function ($bucket) use ($scoresByDay, $countsByDay) {
            $scores = $scoresByDay[$bucket['key']] ?? [];
            $avg = count($scores) ? round(array_sum($scores) / count($scores), 1) : 0;

            return [
                'key' => $bucket['key'],
                'label' => $bucket['label'],
                'value' => $avg,
                'count' => (int) ($countsByDay[$bucket['key']] ?? 0),
            ];
        }, $buckets);
    }

    protected function buildCourseProgress(int $userId, $courseIds): array
    {
        if ($courseIds->isEmpty()) {
            return [];
        }

        $courses = DB::table('courses')
            ->whereIn('id', $courseIds)
            ->where('status', 'active')
            ->get(['id', 'title', 'thumbnail']);

        $items = [];
        foreach ($courses as $course) {
            $progress = (float) progress_bar_admin($course->id, $userId);
            $items[] = [
                'id' => $course->id,
                'title' => $course->title,
                'thumbnail' => $course->thumbnail,
                'progress' => round(min(100, max(0, $progress)), 1),
            ];
        }

        usort($items, static fn ($a, $b) => $b['progress'] <=> $a['progress']);

        return $items;
    }

    protected function buildExamStats(int $userId): array
    {
        $submissions = QuizSubmission::query()
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereNull('status');
            })
            ->with('lesson:id,total_mark,pass_mark,title,lesson_type,course_id')
            ->orderByDesc('id')
            ->get();

        $bestByQuiz = [];
        $questionCounts = $this->questionCountsFor(
            $submissions->pluck('quiz_id')->unique()->filter()->all()
        );

        foreach ($submissions as $submission) {
            $lesson = $submission->lesson;
            if (!$lesson || ($lesson->lesson_type ?? '') !== 'quiz') {
                continue;
            }

            $pct = $this->scorePercent($submission, $lesson, $questionCounts);
            $quizId = (int) $submission->quiz_id;

            if (!isset($bestByQuiz[$quizId]) || $pct > $bestByQuiz[$quizId]['score']) {
                $correct = json_decode($submission->correct_answer ?? '[]', true);
                if (!is_array($correct)) {
                    $correct = [];
                }
                $qCount = max(1, (int) ($questionCounts[$quizId] ?? 1));
                $totalMark = max(0.0, (float) ($lesson->total_mark ?? 0));
                $absolute = $totalMark > 0 ? count($correct) * ($totalMark / $qCount) : 0;

                $bestByQuiz[$quizId] = [
                    'quiz_id' => $quizId,
                    'title' => $lesson->title,
                    'score' => $pct,
                    'passed' => $absolute >= (float) ($lesson->pass_mark ?? 0),
                    'pass_mark' => (float) ($lesson->pass_mark ?? 0),
                    'total_mark' => (float) ($lesson->total_mark ?? 0),
                    'at' => $submission->updated_at,
                ];
            }
        }

        $scores = array_column($bestByQuiz, 'score');
        $passed = collect($bestByQuiz)->where('passed', true)->count();
        $attempts = count($bestByQuiz);
        $recent = array_values($bestByQuiz);
        usort($recent, static function ($a, $b) {
            return strtotime((string) $b['at']) <=> strtotime((string) $a['at']);
        });

        return [
            'attempts' => $attempts,
            'avg_score' => $attempts ? round(array_sum($scores) / $attempts, 1) : 0,
            'pass_rate' => $attempts ? round(($passed / $attempts) * 100, 1) : 0,
            'passed' => $passed,
            'recent' => array_slice($recent, 0, 5),
        ];
    }

    protected function buildCategoryLeaderboard(User $user): array
    {
        $empty = [
            'top' => [],
            'my_rank' => null,
            'my_score' => 0,
            'peers_count' => 0,
            'category_title' => optional($user->get_category)->title,
        ];

        $categoryId = $user->category;
        if (!$categoryId) {
            return $empty;
        }

        $peerIds = User::query()
            ->where('role', 'student')
            ->where('category', $categoryId)
            ->where('status', 1)
            ->pluck('id')
            ->all();

        if ($peerIds === []) {
            return $empty;
        }

        $submissions = QuizSubmission::query()
            ->whereIn('user_id', $peerIds)
            ->where(function ($q) {
                $q->where('status', 'completed')->orWhereNull('status');
            })
            ->with('lesson:id,total_mark,pass_mark,lesson_type')
            ->get();

        $questionCounts = $this->questionCountsFor(
            $submissions->pluck('quiz_id')->unique()->filter()->all()
        );

        $bestByUserQuiz = [];
        foreach ($submissions as $submission) {
            $lesson = $submission->lesson;
            if (!$lesson || ($lesson->lesson_type ?? '') !== 'quiz') {
                continue;
            }

            $uid = (int) $submission->user_id;
            $qid = (int) $submission->quiz_id;
            $pct = $this->scorePercent($submission, $lesson, $questionCounts);

            if (!isset($bestByUserQuiz[$uid][$qid]) || $pct > $bestByUserQuiz[$uid][$qid]) {
                $bestByUserQuiz[$uid][$qid] = $pct;
            }
        }

        $averages = [];
        foreach ($peerIds as $peerId) {
            $scores = array_values($bestByUserQuiz[$peerId] ?? []);
            if ($scores === []) {
                continue;
            }
            $averages[$peerId] = round(array_sum($scores) / count($scores), 2);
        }

        arsort($averages);
        $rankedIds = array_keys($averages);
        $peersCount = count($rankedIds);

        $users = User::whereIn('id', array_slice($rankedIds, 0, 3))
            ->get(['id', 'name', 'photo'])
            ->keyBy('id');

        $top = [];
        foreach (array_slice($rankedIds, 0, 3, true) as $index => $uid) {
            $top[] = [
                'rank' => $index + 1,
                'user_id' => $uid,
                'name' => optional($users->get($uid))->name ?? 'طالب',
                'photo' => optional($users->get($uid))->photo,
                'score' => $averages[$uid],
                'is_me' => (int) $uid === (int) $user->id,
            ];
        }

        $myRank = null;
        $myScore = $averages[$user->id] ?? 0;
        foreach ($rankedIds as $index => $uid) {
            if ((int) $uid === (int) $user->id) {
                $myRank = $index + 1;
                break;
            }
        }

        return [
            'top' => $top,
            'my_rank' => $myRank,
            'my_score' => $myScore,
            'peers_count' => $peersCount,
            'category_title' => optional($user->get_category)->title,
        ];
    }

    protected function buildTips(array $courseProgress, array $examStats, array $watchSeries, array $leaderboard): array
    {
        $tips = [];
        $avgProgress = collect($courseProgress)->avg('progress') ?? 0;
        $watchTotal = array_sum(array_column($watchSeries, 'value'));

        if ($watchTotal <= 0) {
            $tips[] = [
                'type' => 'warn',
                'title' => 'نشاط المشاهدة منخفض',
                'text' => 'ابدأ بمشاهدة درس واحد يوميًا عشان تحافظ على انتظامك وتطلع تقدّمك في الشارت.',
            ];
        }

        if ($avgProgress < 40 && count($courseProgress) > 0) {
            $tips[] = [
                'type' => 'info',
                'title' => 'كمّل كورساتك',
                'text' => 'متوسط إنجازك أقل من 40%. ركّز على كورس واحد وخلّص دروسه قبل ما تبدأ كورس جديد.',
            ];
        }

        if ($examStats['attempts'] === 0) {
            $tips[] = [
                'type' => 'info',
                'title' => 'جرّب اختبارًا',
                'text' => 'لسه مفيش نتائج امتحانات. حل اختبار متاح عشان تعرف مستواك ويظهر ترتيبك.',
            ];
        } elseif ($examStats['avg_score'] < 60) {
            $tips[] = [
                'type' => 'warn',
                'title' => 'راجع قبل الامتحان',
                'text' => 'متوسط درجاتك يحتاج تحسين. راجع الدروس المرتبطة بالاختبارات اللي درجتك فيها أقل من درجة النجاح.',
            ];
        } elseif ($examStats['pass_rate'] >= 80) {
            $tips[] = [
                'type' => 'success',
                'title' => 'أداء ممتاز في الامتحانات',
                'text' => 'نسبة نجاحك عالية. حافظ على نفس الإيقاع وجرب اختبارات أصعب لو متاحة.',
            ];
        }

        if ($leaderboard['my_rank'] !== null && $leaderboard['my_rank'] > 3) {
            $tips[] = [
                'type' => 'accent',
                'title' => 'اقترب من المراكز الأولى',
                'text' => 'ترتيبك الحالي #' . $leaderboard['my_rank'] . ' داخل فئتك. حسّن متوسط درجات الاختبارات عشان تدخل التوب 3.',
            ];
        }

        if ($tips === []) {
            $tips[] = [
                'type' => 'success',
                'title' => 'أداؤك متوازن',
                'text' => 'استمر على نفس المعدل، وراجع الشارت أسبوعيًا عشان تثبت تقدّمك.',
            ];
        }

        return $tips;
    }

    protected function questionCountsFor(array $quizIds): array
    {
        if ($quizIds === []) {
            return [];
        }

        return Question::query()
            ->whereIn('quiz_id', $quizIds)
            ->selectRaw('quiz_id, COUNT(*) as aggregate')
            ->groupBy('quiz_id')
            ->pluck('aggregate', 'quiz_id')
            ->map(fn ($v) => (int) $v)
            ->all();
    }

    protected function scorePercent(QuizSubmission $submission, Lesson $lesson, array $questionCounts): float
    {
        $totalMark = (float) ($lesson->total_mark ?? 0);
        $qCount = max(1, (int) ($questionCounts[$submission->quiz_id] ?? 1));
        $correct = json_decode($submission->correct_answer ?? '[]', true);
        if (!is_array($correct)) {
            $correct = [];
        }

        $score = count($correct) * ($totalMark / $qCount);
        if ($totalMark <= 0) {
            return 0;
        }

        return round(min(100, max(0, ($score / $totalMark) * 100)), 1);
    }
}
