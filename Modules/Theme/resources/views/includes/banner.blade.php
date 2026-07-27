<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="hero-section" id="hero-section" dir="rtl">
    <div class="shapes-background">
        <div class="animated-shape shape-1"></div>
        <div class="animated-shape shape-2"></div>
        <div class="animated-shape shape-3"></div>
    </div>

    <div class="container">
        <div class="row align-items-center flex-row-reverse row-gap-5">
            <div class="col-lg-6">
                <div class="hero-image-wrapper">
                    <div class="hero-image-container">
                        <div class="pulsing-glow"></div>
                        <img src="{{ asset(get_theme_settings('thumbnail') ?? '') }}" alt="{{ get_theme_settings('name') }}" class="hero-image logo light" />
                        <img src="{{ asset(get_theme_settings('dark_thumbnail') ?? '') }}" alt="{{ get_theme_settings('name') }}" class="hero-image logo dark" />

                        <div class="orbiting-ring ring-1">
                            <div class="floating-icon icon-1">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="floating-icon icon-2">
                                <i class="fas fa-book-open"></i>
                            </div>
                        </div>

                        <div class="orbiting-ring ring-2">
                            <div class="floating-icon icon-3">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div class="floating-icon icon-4">
                                <i class="fas fa-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-content-wrapper">
                    <p class="hero-welcome">أهلاً بكم في منصة</p>
                    <div class="hero-teacher-name-wrapper">
                        <h1 class="hero-teacher-name">{{ get_theme_settings('jop_title') }} / <br />
                            {{ get_theme_settings('name') }}</h1>
                    </div>
                    <span class="hero-subtitle">{!! get_theme_settings('instructor_description') !!}</span>

                    @php
                        $heroStatsEnabled = get_theme_settings('hero_stats_status') === false
                            || get_theme_settings('hero_stats_status') == 1;
                        $heroStats = [
                            [
                                'value' => (int) (get_theme_settings('hero_stats_students') ?: 0),
                                'label' => get_theme_settings('hero_stats_students_label') ?: 'طالب مشترك',
                                'icon' => 'fas fa-user-graduate',
                                'modifier' => 'students',
                            ],
                            [
                                'value' => (int) (get_theme_settings('hero_stats_youtube') ?: 0),
                                'label' => get_theme_settings('hero_stats_youtube_label') ?: 'مشترك يوتيوب',
                                'icon' => 'fab fa-youtube',
                                'modifier' => 'youtube',
                            ],
                            [
                                'value' => (int) (get_theme_settings('hero_stats_facebook') ?: 0),
                                'label' => get_theme_settings('hero_stats_facebook_label') ?: 'متابع فيسبوك',
                                'icon' => 'fab fa-facebook-f',
                                'modifier' => 'facebook',
                            ],
                        ];
                    @endphp

                    @if ($heroStatsEnabled)
                        <div class="hero-stats" aria-label="إحصائيات المنصة">
                            @foreach ($heroStats as $stat)
                                <div class="hero-stat hero-stat--{{ $stat['modifier'] }}">
                                    <span class="hero-stat__icon" aria-hidden="true">
                                        <i class="{{ $stat['icon'] }}"></i>
                                    </span>
                                    <div class="hero-stat__body">
                                        <strong class="hero-stat__value" data-count="{{ $stat['value'] }}">0</strong>
                                        <span class="hero-stat__label">{{ $stat['label'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-flex gap-3 hero-buttons justify-content-center justify-content-lg-start">

                        @if (!auth()->check())
                            <div class="d-flex gap-3 hero-buttons justify-content-center justify-content-lg-start mt-3">
                                <a href="{{ route('theme.show_register') }}" class="btn btn-hero-primary">
                                    <span><i class="fas fa-user-plus me-2"></i> انشئ حسابك الآن</span>
                                </a>
                                <a href="{{ route('theme.show_login') }}" class="btn btn-hero-secondary">
                                    <span>تسجيل الدخول</span>
                                </a>
                            </div>
                        @endif

                        @if (auth()->check())

                            @if (get_theme_settings('sub_status') == 1)
                                <div class="d-flex gap-3 hero-buttons justify-content-center justify-content-lg-start">
                                    <a href="{{ route('theme.my.courses') }}" class="btn btn-hero-primary">
                                        <span> اشتراكاتي </span>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const heroSection = document.getElementById("hero-section");
        if (heroSection) {
            setTimeout(() => {
                heroSection.classList.add("is-visible");
            }, 100);
        }

        const imageWrapper = document.querySelector(".hero-image-wrapper");
        const imageContainer = document.querySelector(".hero-image-container");
        if (imageWrapper && imageContainer) {
            imageWrapper.addEventListener("mousemove", (e) => {
                const rect = imageWrapper.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;

                const rotateY = (x / rect.width) * 10;
                const rotateX = (y / rect.height) * -10;

                imageContainer.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });

            imageWrapper.addEventListener("mouseleave", () => {
                imageContainer.style.transform = "rotateX(0deg) rotateY(0deg)";
            });
        }

        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        const counters = document.querySelectorAll(".hero-stat__value[data-count]");
        let countersStarted = false;

        const formatCount = (value) => {
            try {
                return Number(value).toLocaleString("ar-EG") + "+";
            } catch (error) {
                return value.toLocaleString() + "+";
            }
        };

        const animateCounter = (el) => {
            const target = parseInt(el.getAttribute("data-count"), 10) || 0;

            if (prefersReducedMotion || target <= 0) {
                el.textContent = formatCount(target);
                return;
            }

            const duration = 1200;
            const start = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                const current = Math.round(target * eased);
                el.textContent = formatCount(current);

                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        };

        const startCounters = () => {
            if (countersStarted || !counters.length) {
                return;
            }
            countersStarted = true;
            counters.forEach(animateCounter);
        };

        if (counters.length) {
            if ("IntersectionObserver" in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            startCounters();
                            observer.disconnect();
                        }
                    });
                }, { threshold: 0.35 });

                const statsStrip = document.querySelector(".hero-stats");
                if (statsStrip) {
                    observer.observe(statsStrip);
                } else {
                    startCounters();
                }
            } else {
                setTimeout(startCounters, 450);
            }
        }
    });
</script>

<!-- Start Header -->
{{-- <header class="main-header">
    <!-- Start Main Section -->
    <main>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6">
                    <div class="main-image">
                        <img src="{{ asset(get_theme_settings('thumbnail') ?? '') }}" />
                    </div>
                </div>

                <div class="col-lg-5 col-md-6">
                    <div class="main-content">
                        <h2>{{ get_theme_settings('jop_title') }}</h2>
                        <div class="position-relative" style="width: fit-content">
                            <h1>{{ get_theme_settings('name') }}</h1>
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 152.9 43.4"
                                style="enable-background: new 0 0 152.9 43.4" xml:space="preserve">
                                <path
                                    d="M151.9,13.6c0,0,3.3-9.5-85-8.3c-97,1.3-58.3,29-58.3,29s9.7,8.1,69.7,8.1c68.3,0,69.3-23.1,69.3-23.1 s1.7-10.5-14.7-18.4" />
                            </svg>
                        </div>
                        <p>{!! get_theme_settings('instructor_description') !!}</p>
                        @if (!auth()->check())
                            <div class="buttons">
                                <a href="{{ route('theme.show_login') }}">
                                    <span>تسجيل الدخول</span>
                                </a>
                                <a href="{{ route('theme.show_register') }}">
                                    <span>انشئ حسابك الآن</span>
                                </a>
                            </div>
                        @endif


                        @if (get_theme_settings('sub_status') == 1)
                            <div class="buttons">
                                <a href="{{ route('theme.my.courses') }}">
                                    <span> اشتراكاتي </span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- End Main Section -->
</header> --}}
<!-- End Header -->
