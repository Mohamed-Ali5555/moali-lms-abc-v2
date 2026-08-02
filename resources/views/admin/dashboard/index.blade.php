@extends('layouts.admin')
@push('title', get_phrase('Dashboard'))
@push('meta')@endpush
@push('css')
@endpush
@section('content')
    @php
        $adminName = auth()->user()->name ?? get_phrase('Admin');
        $paymentLegendColors = ['#0d9488', '#0284c7', '#f59e0b', '#f43f5e', '#8b5cf6', '#64748b'];
    @endphp

    <div class="admin-dash">
        <section class="admin-dash__hero">
            <div class="admin-dash__hero-inner">
                <div>
                    <div class="admin-dash__eyebrow">
                        <i class="fi-rr-dashboard"></i>
                        {{ get_phrase('Admin Panel') }}
                    </div>
                    <h1 class="admin-dash__title">
                        {{ get_phrase('Welcome back') }}, {{ $adminName }}
                    </h1>
                    <p class="admin-dash__subtitle">
                        {{ get_phrase('Overview of courses, learners, and revenue across your LMS.') }}
                    </p>
                </div>
                <div class="admin-dash__balance">
                    <p class="admin-dash__balance-label">{{ get_phrase('الرصيد') }}</p>
                    <p class="admin-dash__balance-value">{{ number_format((float) $adminBalance, 2) }}</p>
                </div>
            </div>
        </section>

        <div class="admin-dash__stats">
            <a href="{{ route('admin.courses') }}" class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--courses">
                            <i class="fi-rr-e-learning"></i>
                        </span>
                        <i class="fi-rr-arrow-small-right admin-dash__stat-arrow"></i>
                    </div>
                    <p class="admin-dash__stat-value">{{ $coursesCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of Courses') }}</p>
                </div>
            </a>

            <div class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--lessons">
                            <i class="fi-rr-book-alt"></i>
                        </span>
                    </div>
                    <p class="admin-dash__stat-value">{{ $lessonsCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of Lessons') }}</p>
                </div>
            </div>

            <a href="{{ route('admin.exams.list') }}" class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--exams">
                            <i class="fi-rr-clipboard-list-check"></i>
                        </span>
                        <i class="fi-rr-arrow-small-right admin-dash__stat-arrow"></i>
                    </div>
                    <p class="admin-dash__stat-value">{{ $examsCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of exams') }}</p>
                </div>
            </a>

            <a href="{{ route('admin.assignments.list') }}" class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--assignments">
                            <i class="fi-rr-document-signed"></i>
                        </span>
                        <i class="fi-rr-arrow-small-right admin-dash__stat-arrow"></i>
                    </div>
                    <p class="admin-dash__stat-value">{{ $assignmentsCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of assingment') }}</p>
                </div>
            </a>

            <a href="{{ route('admin.enroll.history') }}" class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--enrollments">
                            <i class="fi-rr-user-add"></i>
                        </span>
                        <i class="fi-rr-arrow-small-right admin-dash__stat-arrow"></i>
                    </div>
                    <p class="admin-dash__stat-value">{{ $enrollmentsCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of Enrollment') }}</p>
                </div>
            </a>

            <a href="{{ route('admin.student.index') }}" class="admin-dash__stat">
                <div class="admin-dash__stat-card">
                    <div class="admin-dash__stat-top">
                        <span class="admin-dash__stat-icon admin-dash__stat-icon--students">
                            <i class="fi-rr-users"></i>
                        </span>
                        <i class="fi-rr-arrow-small-right admin-dash__stat-arrow"></i>
                    </div>
                    <p class="admin-dash__stat-value">{{ $studentsCount }}</p>
                    <p class="admin-dash__stat-label">{{ get_phrase('Number of Students') }}</p>
                </div>
            </a>
        </div>

        <div class="admin-dash__panels">
            @if (has_permission('admin.revenue'))
                <section class="admin-dash__panel">
                    <div class="admin-dash__panel-head">
                        <div>
                            <h2 class="admin-dash__panel-title">{{ get_phrase('Admin Revenue This Year') }}</h2>
                            <p class="admin-dash__panel-desc">{{ get_phrase('Monthly admin revenue overview') }}</p>
                        </div>
                        <a class="admin-dash__panel-link" href="{{ route('admin.revenue') }}">
                            {{ get_phrase('Admin Revenue') }}
                            <i class="fi-rr-arrow-small-right"></i>
                        </a>
                    </div>
                    <div class="admin-dash__chart-wrap">
                        <canvas id="myChart" height="280"></canvas>
                    </div>
                </section>
            @endif

            <section class="admin-dash__panel">
                <div class="admin-dash__panel-head">
                    <div>
                        <h2 class="admin-dash__panel-title">{{ get_phrase('Course Status') }}</h2>
                        <p class="admin-dash__panel-desc">{{ get_phrase('Active vs inactive courses') }}</p>
                    </div>
                    <a class="admin-dash__panel-link" href="{{ route('admin.courses') }}">
                        {{ get_phrase('Explore Courses') }}
                        <i class="fi-rr-arrow-small-right"></i>
                    </a>
                </div>
                <div class="admin-dash__status">
                    <div class="admin-dash__donut">
                        <canvas id="pie2"></canvas>
                    </div>
                    <div class="admin-dash__legend">
                        <div class="admin-dash__legend-item">
                            <span class="admin-dash__legend-dot admin-dash__legend-dot--active"></span>
                            <div class="admin-dash__legend-meta">
                                <span class="admin-dash__legend-label">{{ get_phrase('Active') }}</span>
                                <span class="admin-dash__legend-count">{{ $active }} {{ get_phrase('Courses') }}</span>
                            </div>
                        </div>
                        <div class="admin-dash__legend-item">
                            <span class="admin-dash__legend-dot admin-dash__legend-dot--inactive"></span>
                            <div class="admin-dash__legend-meta">
                                <span class="admin-dash__legend-label">{{ get_phrase('Inactive') }}</span>
                                <span class="admin-dash__legend-count">{{ $inactive }} {{ get_phrase('Courses') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div class="admin-dash__panels admin-dash__panels--secondary">
            <section class="admin-dash__panel">
                <div class="admin-dash__panel-head">
                    <div>
                        <h2 class="admin-dash__panel-title">{{ get_phrase('Enrollments Last 12 Months') }}</h2>
                        <p class="admin-dash__panel-desc">{{ get_phrase('Monthly learner enrollment trend') }}</p>
                    </div>
                    <a class="admin-dash__panel-link" href="{{ route('admin.enroll.history') }}">
                        {{ get_phrase('Enrollment history') }}
                        <i class="fi-rr-arrow-small-right"></i>
                    </a>
                </div>
                <div class="admin-dash__chart-wrap">
                    <canvas id="enrollmentsChart" height="280"></canvas>
                </div>
            </section>

            <section class="admin-dash__panel">
                <div class="admin-dash__panel-head">
                    <div>
                        <h2 class="admin-dash__panel-title">{{ get_phrase('Payment Methods') }}</h2>
                        <p class="admin-dash__panel-desc">{{ get_phrase('Distribution of paid transactions') }}</p>
                    </div>
                    <a class="admin-dash__panel-link" href="{{ route('admin.purchase.history') }}">
                        {{ get_phrase('Purchase history') }}
                        <i class="fi-rr-arrow-small-right"></i>
                    </a>
                </div>
                <div class="admin-dash__status">
                    <div class="admin-dash__donut">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="admin-dash__legend">
                        @forelse ($paymentMethodLabels as $index => $label)
                            <div class="admin-dash__legend-item">
                                <span class="admin-dash__legend-dot" style="background: {{ $paymentLegendColors[$index % count($paymentLegendColors)] }}"></span>
                                <div class="admin-dash__legend-meta">
                                    <span class="admin-dash__legend-label">{{ $label }}</span>
                                    <span class="admin-dash__legend-count">{{ $paymentMethodCounts[$index] }} {{ get_phrase('Payments') }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="admin-dash__empty">{{ get_phrase('No payment data yet') }}</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>

        <div class="admin-dash__panels admin-dash__panels--full">
            <section class="admin-dash__panel">
                <div class="admin-dash__panel-head">
                    <div>
                        <h2 class="admin-dash__panel-title">{{ get_phrase('Top Courses by Enrollment') }}</h2>
                        <p class="admin-dash__panel-desc">{{ get_phrase('Most popular courses based on total enrollments') }}</p>
                    </div>
                    <a class="admin-dash__panel-link" href="{{ route('admin.courses') }}">
                        {{ get_phrase('Explore Courses') }}
                        <i class="fi-rr-arrow-small-right"></i>
                    </a>
                </div>
                <div class="admin-dash__chart-wrap admin-dash__chart-wrap--bars">
                    <canvas id="topCoursesChart" height="260"></canvas>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/backend/vendors/chart-js/chart.js') }}"></script>

    <script>
        "use strict";

        const isMobileDash = window.matchMedia('(max-width: 767.98px)').matches;
        const axisTickSize = isMobileDash ? 9 : 11;
        const axisMaxRotation = isMobileDash ? 40 : 0;

        const chartTooltip = {
            backgroundColor: '#0f172a',
            titleFont: { family: 'Plus Jakarta Sans', weight: '600' },
            bodyFont: { family: 'Plus Jakarta Sans' },
            padding: 12,
            cornerRadius: 10
        };

        const monthLabels = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];

        const revenueEl = document.getElementById('myChart');
        if (revenueEl) {
            const revenueData = <?php echo json_encode(array_slice($monthly_amount, 1)); ?>;
            const ctx = revenueEl.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(13, 148, 136, 0.28)');
            gradient.addColorStop(1, 'rgba(13, 148, 136, 0)');

            new Chart(revenueEl, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: "{{ get_phrase('Admin revenue') }}",
                        data: revenueData,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#0d9488',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#0d9488',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: chartTooltip
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans', size: axisTickSize },
                                maxRotation: axisMaxRotation,
                                autoSkip: true,
                                maxTicksLimit: isMobileDash ? 6 : 12
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(226, 232, 240, 0.9)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans', size: axisTickSize },
                                autoSkip: true,
                                maxTicksLimit: isMobileDash ? 5 : 8
                            }
                        }
                    }
                }
            });
        }

        const project_progress2 = document.getElementById('pie2');
        if (project_progress2) {
            new Chart(project_progress2, {
                type: 'doughnut',
                data: {
                    labels: ['{{ get_phrase('Active') }}', '{{ get_phrase('Inactive') }}'],
                    datasets: [{
                        backgroundColor: ['#0d9488', '#fb7185'],
                        hoverBackgroundColor: ['#0f766e', '#f43f5e'],
                        label: '{{ get_phrase('Courses') }}',
                        data: [{{ $active }}, {{ $inactive }}],
                        borderWidth: 4,
                        borderColor: '#fff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: chartTooltip
                    }
                }
            });
        }

        const enrollmentsEl = document.getElementById('enrollmentsChart');
        if (enrollmentsEl) {
            const enrollmentLabels = @json($enrollmentLabels);
            const enrollmentCounts = @json($enrollmentCounts);
            const enrollCtx = enrollmentsEl.getContext('2d');
            const enrollGradient = enrollCtx.createLinearGradient(0, 0, 0, 280);
            enrollGradient.addColorStop(0, 'rgba(99, 102, 241, 0.28)');
            enrollGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            new Chart(enrollmentsEl, {
                type: 'line',
                data: {
                    labels: enrollmentLabels,
                    datasets: [{
                        label: "{{ get_phrase('Enrollments') }}",
                        data: enrollmentCounts,
                        fill: true,
                        backgroundColor: enrollGradient,
                        borderColor: '#6366f1',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#6366f1',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: chartTooltip
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans', size: axisTickSize },
                                maxRotation: axisMaxRotation,
                                autoSkip: true,
                                maxTicksLimit: isMobileDash ? 5 : 6
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans', size: axisTickSize }
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.9)',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        const paymentMethodsEl = document.getElementById('paymentMethodsChart');
        if (paymentMethodsEl) {
            const paymentLabels = @json($paymentMethodLabels);
            const paymentCounts = @json($paymentMethodCounts);
            const paymentColors = @json($paymentLegendColors);

            new Chart(paymentMethodsEl, {
                type: 'doughnut',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        label: "{{ get_phrase('Payments') }}",
                        data: paymentCounts,
                        backgroundColor: paymentLabels.map((_, i) => paymentColors[i % paymentColors.length]),
                        hoverOffset: 6,
                        borderWidth: 4,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: chartTooltip
                    }
                }
            });
        }

        const topCoursesEl = document.getElementById('topCoursesChart');
        if (topCoursesEl) {
            const topLabels = @json($topCourseLabels);
            const topCounts = @json($topCourseCounts);

            new Chart(topCoursesEl, {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: "{{ get_phrase('Enrollments') }}",
                        data: topCounts,
                        backgroundColor: [
                            'rgba(13, 148, 136, 0.85)',
                            'rgba(2, 132, 199, 0.85)',
                            'rgba(99, 102, 241, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                            'rgba(244, 63, 94, 0.75)'
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                        barThickness: isMobileDash ? 16 : 22
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: isMobileDash ? { left: 0, right: 4 } : undefined
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: chartTooltip
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#94a3b8',
                                font: { family: 'Plus Jakarta Sans', size: axisTickSize }
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.9)',
                                drawBorder: false
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                color: '#334155',
                                font: {
                                    family: 'Plus Jakarta Sans',
                                    size: isMobileDash ? 10 : 12,
                                    weight: '600'
                                },
                                autoSkip: false,
                                callback: function(value) {
                                    const label = this.getLabelForValue(value);
                                    if (!isMobileDash || label.length <= 18) {
                                        return label;
                                    }
                                    return label.slice(0, 16) + '…';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
