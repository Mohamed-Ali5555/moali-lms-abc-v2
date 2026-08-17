<?php

namespace App\Services\WaPilot;

use App\Jobs\SendWhatsAppMessageJob;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizSubmission;
use App\Models\User;
use App\Models\WhatsappTemplate;

class WhatsAppNotifier
{
    public function __construct(protected WaPilotClient $client)
    {
    }

    public function notifyCourseStudents(string $eventKey, int $courseId, array $placeholders = []): void
    {
        if (!$this->client->isEnabled()) {
            return;
        }

        $template = WhatsappTemplate::where('event_key', $eventKey)->first();
        if (!$template || !$template->is_active) {
            return;
        }

        $course = Course::find($courseId);
        if (!$course) {
            return;
        }

        $basePlaceholders = array_merge([
            'course_title' => $course->title,
            'system_name' => get_settings('system_name') ?: config('app.name'),
            'link' => url('/'),
        ], $placeholders);

        Enrollment::with('user')
            ->where('course_id', $courseId)
            ->chunkById(100, function ($enrollments) use ($template, $basePlaceholders) {
                foreach ($enrollments as $enrollment) {
                    $user = $enrollment->user;
                    if (!$user) {
                        continue;
                    }
                    $this->queueForUser($template, $user, $basePlaceholders);
                }
            });
    }

    public function notifyUser(string $eventKey, User $user, array $placeholders = []): void
    {
        if (!$this->client->isEnabled()) {
            return;
        }

        $template = WhatsappTemplate::where('event_key', $eventKey)->first();
        if (!$template || !$template->is_active) {
            return;
        }

        $basePlaceholders = array_merge([
            'system_name' => get_settings('system_name') ?: config('app.name'),
            'link' => url('/'),
        ], $placeholders);

        $this->queueForUser($template, $user, $basePlaceholders);
    }

    public function notifyLessonPublished(Lesson $lesson): void
    {
        if (($lesson->lesson_type ?? '') === 'quiz') {
            return;
        }

        $this->notifyCourseStudents('lesson_published', (int) $lesson->course_id, [
            'lesson_title' => $lesson->title,
            'start_time' => $lesson->start_time ?: '-',
            'end_time' => $lesson->end_time ?: '-',
        ]);
    }

    public function notifyQuizActivated(Lesson $quiz): void
    {
        if (($quiz->lesson_type ?? '') !== 'quiz') {
            return;
        }

        // Only notify when quiz/assignment becomes active
        if ((int) $quiz->status !== 1) {
            return;
        }

        $this->notifyCourseStudents('quiz_activated', (int) $quiz->course_id, [
            'quiz_title' => $quiz->title,
            'start_time' => $quiz->start_time ?: '-',
            'end_time' => $quiz->end_time ?: '-',
            'total_mark' => $quiz->total_mark ?? '-',
            'pass_mark' => $quiz->pass_mark ?? '-',
        ]);
    }

    public function notifyQuizResult(QuizSubmission $submission, Lesson $quiz, User $user): void
    {
        $questionCount = max(1, Question::where('quiz_id', $quiz->id)->count());
        $correctAnswers = $submission->correct_answer ? json_decode($submission->correct_answer, true) : [];
        if (!is_array($correctAnswers)) {
            $correctAnswers = [];
        }

        $total = (float) ($quiz->total_mark ?? 0);
        $score = round(count($correctAnswers) * ($total / $questionCount), 2);
        $passed = $score >= (float) ($quiz->pass_mark ?? 0);
        $course = Course::find($quiz->course_id);

        $this->notifyUser('quiz_result', $user, [
            'student_name' => $user->name,
            'course_title' => $course->title ?? '-',
            'quiz_title' => $quiz->title,
            'score' => $score,
            'total' => $total,
            'pass_status' => $passed ? 'ناجح' : 'راسب',
        ]);
    }

    public function sendTest(string $phone, string $message): array
    {
        $normalized = $this->client->normalizePhone($phone);
        if (!$normalized) {
            return ['success' => false, 'response' => 'Invalid phone number.'];
        }

        return $this->client->send($normalized, $message);
    }

    /**
     * Queue a one-off custom WhatsApp broadcast to students by category or course.
     *
     * @return array{queued:int,skipped:int,audience:int}
     */
    public function broadcastCustom(
        string $audienceType,
        int $audienceId,
        string $title,
        string $body,
        bool $sendToStudent = true,
        bool $sendToParent = false
    ): array {
        if (!$this->client->isEnabled()) {
            return ['queued' => 0, 'skipped' => 0, 'audience' => 0, 'error' => 'disabled'];
        }

        if (!$sendToStudent && !$sendToParent) {
            return ['queued' => 0, 'skipped' => 0, 'audience' => 0, 'error' => 'no_recipients'];
        }

        $users = $this->resolveAudienceUsers($audienceType, $audienceId);
        $queued = 0;
        $skipped = 0;

        $context = $this->resolveAudienceContext($audienceType, $audienceId);

        foreach ($users as $user) {
            $placeholders = [
                'student_name' => $user->name,
                'parent_name' => $user->name,
                'system_name' => get_settings('system_name') ?: config('app.name'),
                'link' => url('/'),
                'category_title' => $context['category_title'] ?? '-',
                'course_title' => $context['course_title'] ?? '-',
                'title' => $title,
            ];

            $message = $this->composeBroadcastMessage($title, $body, $placeholders);
            if (trim($message) === '') {
                $skipped++;
                continue;
            }

            $sentAny = false;

            if ($sendToStudent) {
                $phone = $this->client->normalizePhone($user->phone);
                if ($phone) {
                    SendWhatsAppMessageJob::dispatch(
                        $phone,
                        $message,
                        'manual_broadcast',
                        $user->id,
                        'student'
                    );
                    $queued++;
                    $sentAny = true;
                }
            }

            if (!$sentAny) {
                $skipped++;
            }
        }

        return [
            'queued' => $queued,
            'skipped' => $skipped,
            'audience' => $users->count(),
        ];
    }

    public function countAudience(string $audienceType, int $audienceId): array
    {
        $users = $this->resolveAudienceUsers($audienceType, $audienceId);
        $withPhone = 0;

        foreach ($users as $user) {
            if ($this->client->normalizePhone($user->phone)) {
                $withPhone++;
            }
           
        }

        return [
            'students' => $users->count(),
            'with_phone' => $withPhone,
        ];
    }

    protected function resolveAudienceUsers(string $audienceType, int $audienceId)
    {
        if ($audienceType === 'course') {
            $userIds = Enrollment::where('course_id', $audienceId)->pluck('user_id');

            return User::where('role', 'student')
                ->whereIn('id', $userIds)
                ->get(['id', 'name', 'phone']);
        }

        // category (academic year / parent category)
        return User::where('role', 'student')
            ->where('category', $audienceId)
            ->get(['id', 'name', 'phone']);
    }

    protected function resolveAudienceContext(string $audienceType, int $audienceId): array
    {
        if ($audienceType === 'course') {
            $course = Course::with('category')->find($audienceId);

            return [
                'course_title' => $course->title ?? '-',
                'category_title' => $course?->category?->title ?? '-',
            ];
        }

        $category = \App\Models\Category::find($audienceId);

        return [
            'course_title' => '-',
            'category_title' => $category->title ?? '-',
        ];
    }

    protected function composeBroadcastMessage(string $title, string $body, array $placeholders): string
    {
        $title = trim($this->render($title, $placeholders));
        $body = trim($this->render($body, $placeholders));

        if ($title !== '' && $body !== '') {
            return $title . "\n\n" . $body;
        }

        return $title !== '' ? $title : $body;
    }

    public function notifyEnrollment(User $user, Course $course, $amount = null): void
    {
        $this->notifyUser('enrollment_confirmed', $user, [
            'course_title' => $course->title,
            'amount' => $amount === null || $amount === '' ? 'مجاني / حسب الفاتورة' : currency($amount),
        ]);
    }

    public function notifyPasswordReset(User $user, string $resetLink): void
    {
        if (!$this->client->isEnabled()) {
            return;
        }

        $template = WhatsappTemplate::where('event_key', 'password_reset')->first();
        if (!$template || !$template->is_active) {
            return;
        }

        $placeholders = [
            'student_name' => $user->name,
            'phone' => $user->phone,
            'reset_link' => $resetLink,
            'system_name' => get_settings('system_name') ?: config('app.name'),
            'link' => $resetLink,
        ];

        $message = $this->render($template->body, $placeholders);
        $phone = $this->client->normalizePhone($user->phone);

        if (!$phone) {
            return;
        }

        // Password reset links go only to the student's phone.
        SendWhatsAppMessageJob::dispatch(
            $phone,
            $message,
            $template->event_key,
            $user->id,
            'student'
        );
    }

    protected function queueForUser(WhatsappTemplate $template, User $user, array $placeholders): void
    {
        $placeholders = array_merge([
            'student_name' => $user->name,
            'parent_name' => $user->name,
        ], $placeholders);

        $message = $this->render($template->body, $placeholders);

        if ($template->send_to_student) {
            $phone = $this->client->normalizePhone($user->phone);
            if ($phone) {
                SendWhatsAppMessageJob::dispatch(
                    $phone,
                    $message,
                    $template->event_key,
                    $user->id,
                    'student'
                );
            }
        }

       
    }

    public function render(string $body, array $placeholders): string
    {
        $replacements = [];
        foreach ($placeholders as $key => $value) {
            $replacements['[' . $key . ']'] = (string) $value;
        }

        return strtr($body, $replacements);
    }
}
