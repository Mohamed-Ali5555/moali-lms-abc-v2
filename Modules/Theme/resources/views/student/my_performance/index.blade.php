@extends('theme::layouts.master')

@push('title', get_phrase('أدائي'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-performance-modern.css') }}">
@endpush

@section('content')
<section class="main_content mp-page" dir="rtl">
    <div class="profile-banner-area"></div>
    <div class="container profile-banner-area-container">
        <div class="row">
            @include('theme::student.left_sidebar')

            <div class="col-lg-9">
                <div class="mp-header">
                    <div class="mp-header__intro">
                        <div class="mp-header__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                        </div>
                        <div>
                            <h1 class="mp-header__title">{{ get_phrase('أدائي') }}</h1>
                            <p class="mp-header__sub">
                                {{ get_phrase('تابع نشاطك في المشاهدة، نتائج الامتحانات، وترتيبك داخل فئتك التعليمية.') }}
                            </p>
                        </div>
                    </div>

                    <form method="get" action="{{ route('theme.my.performance') }}" class="mp-period">
                        <input type="hidden" name="period" id="mpPeriodInput" value="{{ $period }}">
                        <button type="submit" class="mp-period__btn {{ $period === 'week' ? 'is-active' : '' }}"
                            onclick="document.getElementById('mpPeriodInput').value='week'">
                            {{ get_phrase('أسبوعي') }}
                        </button>
                        <button type="submit" class="mp-period__btn {{ $period === 'month' ? 'is-active' : '' }}"
                            onclick="document.getElementById('mpPeriodInput').value='month'">
                            {{ get_phrase('شهري') }}
                        </button>
                    </form>
                </div>

                <div class="mp-kpis">
                    <div class="mp-kpi">
                        <span class="mp-kpi__label">{{ get_phrase('كورساتي') }}</span>
                        <strong class="mp-kpi__value">{{ $kpis['courses'] }}</strong>
                    </div>
                    <div class="mp-kpi">
                        <span class="mp-kpi__label">{{ get_phrase('متوسط الإنجاز') }}</span>
                        <strong class="mp-kpi__value">{{ $kpis['avg_progress'] }}%</strong>
                    </div>
                    <div class="mp-kpi">
                        <span class="mp-kpi__label">{{ get_phrase('متوسط الامتحانات') }}</span>
                        <strong class="mp-kpi__value">{{ $kpis['avg_score'] }}%</strong>
                    </div>
                    <div class="mp-kpi mp-kpi--accent">
                        <span class="mp-kpi__label">{{ get_phrase('ترتيبي') }}</span>
                        <strong class="mp-kpi__value">
                            {{ $kpis['rank'] ? '#' . $kpis['rank'] : '—' }}
                        </strong>
                        @if ($kpis['peers'] > 0)
                            <small>{{ get_phrase('من') }} {{ $kpis['peers'] }}</small>
                        @endif
                    </div>
                </div>

                <div class="mp-grid">
                    <section class="mp-card">
                        <div class="mp-card__head">
                            <h2>{{ get_phrase('نشاط المشاهدة') }}</h2>
                            <p>{{ $period === 'week' ? get_phrase('آخر 7 أيام') : get_phrase('آخر 30 يوم') }}</p>
                        </div>
                        <div class="mp-chart-wrap">
                            <canvas id="mpWatchChart" height="220"></canvas>
                        </div>
                    </section>

                    <section class="mp-card">
                        <div class="mp-card__head">
                            <h2>{{ get_phrase('أدائي في الامتحانات') }}</h2>
                            <p>{{ get_phrase('متوسط الدرجة % حسب اليوم') }}</p>
                        </div>
                        <div class="mp-chart-wrap">
                            <canvas id="mpExamChart" height="220"></canvas>
                        </div>
                    </section>
                </div>

                <div class="mp-grid mp-grid--bottom">
                    <section class="mp-card">
                        <div class="mp-card__head">
                            <h2>{{ get_phrase('إنجاز الكورسات') }}</h2>
                            <p>{{ get_phrase('نسبة التقدّم في كل كورس') }}</p>
                        </div>

                        @if (count($courseProgress) > 0)
                            <div class="mp-progress-list">
                                @foreach ($courseProgress as $course)
                                    <div class="mp-progress-item">
                                        <div class="mp-progress-item__top">
                                            <span class="mp-progress-item__title">{{ $course['title'] }}</span>
                                            <strong>{{ $course['progress'] }}%</strong>
                                        </div>
                                        <div class="mp-progress-item__track">
                                            <span style="width: {{ $course['progress'] }}%"></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mp-empty">{{ get_phrase('لا توجد كورسات مسجّلة بعد.') }}</div>
                        @endif
                    </section>

                    <section class="mp-card mp-rank">
                        <div class="mp-card__head">
                            <h2>{{ get_phrase('ترتيب الفئة') }}</h2>
                            <p>
                                {{ $leaderboard['category_title'] ?: get_phrase('حسب نتائج الامتحانات') }}
                            </p>
                        </div>

                        @if (count($leaderboard['top']) > 0)
                            <div class="mp-podium">
                                @foreach ($leaderboard['top'] as $row)
                                    <article class="mp-podium__item mp-podium__item--{{ $row['rank'] }} {{ $row['is_me'] ? 'is-me' : '' }}">
                                        <span class="mp-podium__rank">{{ $row['rank'] }}</span>
                                        <img src="{{ get_image($row['photo'] ?? '') }}" alt="{{ $row['name'] }}">
                                        <strong>{{ $row['name'] }}</strong>
                                        <small>{{ $row['score'] }}%</small>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mp-my-rank">
                                @if ($leaderboard['my_rank'])
                                    <span>{{ get_phrase('ترتيبك') }}: <strong>#{{ $leaderboard['my_rank'] }}</strong></span>
                                    <span>{{ get_phrase('متوسطك') }}: <strong>{{ $leaderboard['my_score'] }}%</strong></span>
                                @else
                                    <span>{{ get_phrase('حل اختبارات لتظهر في الترتيب') }}</span>
                                @endif
                            </div>
                        @else
                            <div class="mp-empty">{{ get_phrase('لا يوجد ترتيب متاح حاليًا لهذه الفئة.') }}</div>
                        @endif
                    </section>
                </div>

                <section class="mp-card mp-tips">
                    <div class="mp-card__head">
                        <h2>{{ get_phrase('نصائح لتحسين أدائك') }}</h2>
                        <p>{{ get_phrase('توصيات مبنية على نشاطك ونتائجك') }}</p>
                    </div>
                    <div class="mp-tips__grid">
                        @foreach ($tips as $tip)
                            <article class="mp-tip mp-tip--{{ $tip['type'] }}">
                                <h3>{{ $tip['title'] }}</h3>
                                <p>{{ $tip['text'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>

                @if (count($examStats['recent'] ?? []) > 0)
                    <section class="mp-card">
                        <div class="mp-card__head">
                            <h2>{{ get_phrase('أحدث نتائج الامتحانات') }}</h2>
                            <p>{{ get_phrase('أفضل محاولة لكل اختبار') }}</p>
                        </div>
                        <div class="mp-exam-list">
                            @foreach ($examStats['recent'] as $exam)
                                <div class="mp-exam-row">
                                    <div>
                                        <strong>{{ $exam['title'] }}</strong>
                                        <small>{{ \Carbon\Carbon::parse($exam['at'])->diffForHumans() }}</small>
                                    </div>
                                    <span class="mp-exam-score {{ $exam['passed'] ? 'is-pass' : 'is-fail' }}">
                                        {{ $exam['score'] }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script src="{{ asset('assets/backend/vendors/chart-js/chart.js') }}"></script>
<script>
    (function () {
        var labels = @json($chartLabels);
        var watchData = @json($watchSeries);
        var examData = @json($examSeries);
        var rootStyles = getComputedStyle(document.documentElement);
        var accentRgb = (rootStyles.getPropertyValue('--c-accent-rgb') || '0, 156, 204').trim();
        var accent = 'rgb(' + accentRgb + ')';
        var accentSoft = 'rgba(' + accentRgb + ', 0.18)';

        function lineOpts(label, data, color, soft) {
            return {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        backgroundColor: soft,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 5,
                        pointBackgroundColor: color
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return ctx.parsed.y + (label.indexOf('%') >= 0 || label.indexOf('امتحان') >= 0 ? '%' : '');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', maxRotation: 0, autoSkip: true, maxTicksLimit: 8 }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148,163,184,0.2)' },
                            ticks: { color: '#64748b' }
                        }
                    }
                }
            };
        }

        var watchEl = document.getElementById('mpWatchChart');
        var examEl = document.getElementById('mpExamChart');
        if (watchEl && window.Chart) {
            new Chart(watchEl.getContext('2d'), lineOpts(@json(get_phrase('نشاط المشاهدة')), watchData, accent, accentSoft));
        }
        if (examEl && window.Chart) {
            var examCfg = lineOpts(@json(get_phrase('متوسط الامتحان')), examData, '#0f766e', 'rgba(15,118,110,0.14)');
            examCfg.options.scales.y.max = 100;
            new Chart(examEl.getContext('2d'), examCfg);
        }
    })();
</script>
@endpush
