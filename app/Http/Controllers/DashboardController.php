<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Payment_history;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $year = (int) date('Y');
        $monthly_amount = array_fill(0, 13, 0);

        $rows = Payment_history::query()
            ->selectRaw('MONTH(created_at) as month_num, SUM(admin_revenue) as total')
            ->whereBetween('created_at', [
                sprintf('%d-01-01 00:00:00', $year),
                sprintf('%d-12-31 23:59:59', $year),
            ])
            ->groupBy('month_num')
            ->pluck('total', 'month_num');

        foreach ($rows as $month => $total) {
            $monthly_amount[(int) $month] = (float) $total;
        }

        $coursesByStatus = Course::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        [$enrollmentLabels, $enrollmentCounts] = $this->lastTwelveMonthsEnrollments();
        [$topCourseLabels, $topCourseCounts] = $this->topCoursesByEnrollment(5);
        [$paymentMethodLabels, $paymentMethodCounts] = $this->paymentMethodsBreakdown();

        $page_data = [
            'monthly_amount' => $monthly_amount,
            'coursesCount' => Course::count(),
            'lessonsCount' => Lesson::where('lesson_type', '!=', 'quiz')->count(),
            'examsCount' => Lesson::where(['lesson_type' => 'quiz', 'type' => '1'])->count(),
            'assignmentsCount' => Lesson::where(['lesson_type' => 'quiz', 'type' => '2'])->count(),
            'enrollmentsCount' => Enrollment::count(),
            'studentsCount' => User::where('role', 'student')->count(),
            'adminBalance' => Payment_history::sum('admin_revenue'),
            'active' => (int) ($coursesByStatus['active'] ?? 0),
            'inactive' => (int) ($coursesByStatus['inactive'] ?? 0),
            'enrollmentLabels' => $enrollmentLabels,
            'enrollmentCounts' => $enrollmentCounts,
            'topCourseLabels' => $topCourseLabels,
            'topCourseCounts' => $topCourseCounts,
            'paymentMethodLabels' => $paymentMethodLabels,
            'paymentMethodCounts' => $paymentMethodCounts,
        ];

        return view('admin.dashboard.index', $page_data);
    }

    private function lastTwelveMonthsEnrollments(): array
    {
        $start = Carbon::now()->subMonths(11)->startOfMonth();
        $raw = Enrollment::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->where('created_at', '>=', $start)
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $counts = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $labels[] = $month->format('M Y');
            $counts[] = (int) ($raw[$key] ?? 0);
        }

        return [$labels, $counts];
    }

    private function topCoursesByEnrollment(int $limit = 5): array
    {
        $rows = Enrollment::query()
            ->selectRaw('course_id, COUNT(*) as total')
            ->whereNotNull('course_id')
            ->groupBy('course_id')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        if ($rows->isEmpty()) {
            return [[], []];
        }

        $titles = Course::query()
            ->whereIn('id', $rows->pluck('course_id'))
            ->pluck('title', 'id');

        $labels = [];
        $counts = [];

        foreach ($rows as $row) {
            $title = (string) ($titles[$row->course_id] ?? get_phrase('Unknown course'));
            $labels[] = Str::limit($title, 36, '…');
            $counts[] = (int) $row->total;
        }

        return [$labels, $counts];
    }

    private function paymentMethodsBreakdown(): array
    {
        $rows = Payment_history::query()
            ->selectRaw('payment_type, COUNT(*) as total')
            ->where('status', 'paid')
            ->groupBy('payment_type')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $counts = [];

        foreach ($rows as $row) {
            $type = trim((string) $row->payment_type);
            $labels[] = $type !== '' ? ucfirst($type) : get_phrase('Other');
            $counts[] = (int) $row->total;
        }

        return [$labels, $counts];
    }
}
