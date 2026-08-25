@extends('theme::layouts.master')
@section('content')
@include('theme::includes.banner')
@php
    $accrVideoStatus = get_theme_settings('accr_video_status') != '0';
    $accrVideoUrl    = trim((string) get_theme_settings('accr_video_url'));
    $accrVideoThumb  = get_theme_settings('accr_video_thumbnail');

    $youtubeId = null;
    if ($accrVideoUrl !== '') {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|shorts\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $accrVideoUrl, $ytMatch)) {
            $youtubeId = $ytMatch[1];
        }
    }

    $videoThumbSrc = $accrVideoThumb
        ? get_image($accrVideoThumb)
        : ($youtubeId ? 'https://img.youtube.com/vi/' . $youtubeId . '/maxresdefault.jpg' : '');

    $showAccrVideo = $accrVideoStatus && ($youtubeId || $accrVideoUrl !== '' || $accrVideoThumb);

    if ($showAccrVideo) {
        $acColors      = get_active_theme_colors();
        $videoAccent   = $acColors['accent'];
        $videoAccentRgb = hex_to_rgb_csv($videoAccent);
        $videoSecondaryRgb = hex_to_rgb_csv($acColors['secondary']);
    }
@endphp
@if ($showAccrVideo)
    <section class="home-video-section" id="homeVideoSection" dir="rtl">
        <div class="home-video-section__bg" aria-hidden="true">
            <span class="home-video-section__pattern"></span>
            <span class="home-video-section__blob home-video-section__blob--1"></span>
            <span class="home-video-section__blob home-video-section__blob--2"></span>
            <span class="home-video-section__glow"></span>
        </div>

        <div class="container">
            <div class="home-video-section__inner" id="accredVideoBlock">
                <div class="accred-video-banner">
                    <button type="button"
                        class="accred-video-banner__trigger"
                        id="accredVideoTrigger"
                        aria-label="تشغيل الفيديو التعريفي"
                        @if ($youtubeId) data-youtube-id="{{ $youtubeId }}" @endif
                        @if ($accrVideoUrl && !$youtubeId) data-video-url="{{ $accrVideoUrl }}" @endif>
                        <span class="accred-video-banner__media">
                            @if ($videoThumbSrc)
                            <img src="{{ $videoThumbSrc }}"
                                alt=""
                                class="accred-video-banner__thumb"
                                loading="lazy"
                                onerror="this.onerror=null;this.src='{{ $youtubeId ? 'https://img.youtube.com/vi/'.$youtubeId.'/hqdefault.jpg' : '' }}';this.classList.add('accred-video-banner__thumb--fallback');">
                            @else
                            <span class="accred-video-banner__thumb accred-video-banner__thumb--fallback" aria-hidden="true"></span>
                            @endif
                            <span class="accred-video-banner__overlay" aria-hidden="true"></span>
                        </span>
                        <span class="accred-video-banner__play" aria-hidden="true">
                            <i class="fa-solid fa-play"></i>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="accred-video-modal" id="accredVideoModal" hidden aria-hidden="true">
        <div class="accred-video-modal__backdrop" id="accredVideoBackdrop"></div>
        <div class="accred-video-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="accredVideoModalTitle">
            <button type="button" class="accred-video-modal__close" id="accredVideoClose" aria-label="إغلاق الفيديو">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
            <h3 class="visually-hidden" id="accredVideoModalTitle">فيديو تعريفي</h3>
            <div class="accred-video-modal__player" id="accredVideoPlayer"></div>
        </div>
    </div>

    <style>
    .home-video-section {
        --ac: {{ $videoAccent }};
        --ac-rgb: {{ $videoAccentRgb }};
        --sec-rgb: {{ $videoSecondaryRgb }};
        position: relative;
        padding: 3rem 0 3.5rem;
        overflow: hidden;
        isolation: isolate;
        background:
            radial-gradient(820px 420px at 88% -5%, rgba(var(--c-accent-rgb), .28), transparent 62%),
            radial-gradient(700px 380px at 8% 105%, rgba(var(--c-secondary-rgb), .24), transparent 58%),
            linear-gradient(165deg, rgba(255, 255, 255, .04) 0%, rgba(0, 0, 0, .18) 100%),
            rgb(var(--c-primary-rgb));
    }
    .home-video-section__bg {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 0;
    }
    .home-video-section__pattern {
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='32' height='32' viewBox='0 0 32 32'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1.2' opacity='0.06'%3E%3Cpath d='M16 10v12M10 16h12'/%3E%3C/g%3E%3C/svg%3E");
        background-size: 32px 32px;
        -webkit-mask-image: radial-gradient(130% 110% at 50% 40%, #000 35%, transparent 88%);
        mask-image: radial-gradient(130% 110% at 50% 40%, #000 35%, transparent 88%);
    }
    .home-video-section__blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(70px);
        opacity: .55;
    }
    .home-video-section__blob--1 {
        width: 360px;
        height: 360px;
        top: -140px;
        right: -80px;
        background: radial-gradient(circle, rgba(var(--c-accent-rgb), .5), transparent 70%);
    }
    .home-video-section__blob--2 {
        width: 320px;
        height: 320px;
        bottom: -150px;
        left: -70px;
        background: radial-gradient(circle, rgba(var(--c-secondary-rgb), .45), transparent 70%);
    }
    .home-video-section__glow {
        position: absolute;
        inset-inline: 8%;
        bottom: 0;
        height: 2px;
        background: linear-gradient(
            90deg,
            rgba(var(--c-secondary-rgb), 0),
            rgba(var(--c-accent-rgb), .85),
            rgba(var(--c-secondary-rgb), 0)
        );
        opacity: .75;
    }
    .home-video-section .container {
        position: relative;
        z-index: 1;
    }
    .home-video-section__inner {
        opacity: 0;
        transform: translateY(22px);
        transition: opacity .7s ease, transform .7s ease;
    }
    .home-video-section__inner.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .accred-video-banner {
        --ac: {{ $videoAccent }};
        position: relative;
        z-index: 1;
        width: 100%;
        border-radius: 1.15rem;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .14);
        box-shadow:
            0 24px 60px rgba(0, 0, 0, .32),
            0 0 0 1px rgba(var(--c-accent-rgb), .12),
            inset 0 1px 0 rgba(255, 255, 255, .08);
    }
    .accred-video-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 3;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px rgba(var(--c-accent-rgb), .18);
    }
    .accred-video-banner__trigger {
        display: block;
        width: 100%;
        padding: 0;
        border: 0;
        background: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .accred-video-banner__trigger:focus-visible {
        outline: 3px solid rgba(var(--c-secondary-rgb), .85);
        outline-offset: 3px;
    }
    .accred-video-banner__media {
        display: block;
        position: relative;
        width: 100%;
        aspect-ratio: 1024 / 285;
        min-height: 220px;
        max-height: 520px;
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(var(--c-accent-rgb), .15), rgba(var(--c-secondary-rgb), .12)),
            #111827;
    }
    .accred-video-banner__thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transform: scale(1.03);
        transition: transform .7s ease;
    }
    .accred-video-banner__thumb--fallback {
        background:
            linear-gradient(135deg, rgba(var(--c-accent-rgb), .35), rgba(var(--c-secondary-rgb), .25)),
            rgb(var(--c-primary-rgb));
    }
    .accred-video-banner__trigger:hover .accred-video-banner__thumb {
        transform: scale(1.06);
    }
    .accred-video-banner__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(
            180deg,
            rgba(var(--c-primary-rgb), .08) 0%,
            rgba(0, 0, 0, .42) 55%,
            rgba(var(--c-primary-rgb), .22) 100%
        );
        pointer-events: none;
        transition: background .35s ease;
    }
    .accred-video-banner__trigger:hover .accred-video-banner__overlay {
        background: linear-gradient(
            180deg,
            rgba(var(--c-accent-rgb), .1) 0%,
            rgba(0, 0, 0, .32) 55%,
            rgba(var(--c-secondary-rgb), .18) 100%
        );
    }
    .accred-video-banner__play {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        width: clamp(64px, 7vw, 84px);
        height: clamp(64px, 7vw, 84px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: var(--ac);
        font-size: clamp(1.35rem, 2.2vw, 1.75rem);
        padding-inline-start: .28rem;
        box-shadow:
            0 10px 40px rgba(0, 0, 0, 0.3),
            0 0 0 6px rgba(var(--c-accent-rgb), .18);
        pointer-events: none;
        transition: transform .3s ease, box-shadow .3s ease;
    }
    .accred-video-banner__trigger:hover .accred-video-banner__play {
        transform: translate(-50%, -50%) scale(1.06);
        box-shadow:
            0 14px 48px rgba(0, 0, 0, 0.38),
            0 0 0 8px rgba(var(--c-accent-rgb), .24);
    }
    .accred-video-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
    }
    .accred-video-modal[hidden] { display: none; }
    .accred-video-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(8, 12, 24, 0.82);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
    }
    .accred-video-modal__dialog {
        position: relative;
        z-index: 1;
        width: min(960px, 100%);
        border-radius: 1.1rem;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.55);
        animation: accredModalIn .35s ease;
    }
    @keyframes accredModalIn {
        from { opacity: 0; transform: translateY(16px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .accred-video-modal__close {
        position: absolute;
        top: .75rem;
        left: .75rem;
        z-index: 2;
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background .2s, transform .2s;
    }
    .accred-video-modal__close:hover {
        background: rgba(0, 0, 0, 0.75);
        transform: scale(1.06);
    }
    .accred-video-modal__player {
        position: relative;
        aspect-ratio: 16 / 9;
        background: #000;
    }
    .accred-video-modal__player iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }
    @media (max-width: 767px) {
        .home-video-section { padding: 2rem 0 2.5rem; }
        .accred-video-banner { border-radius: .9rem; }
    }
    @media (max-width: 480px) {
        .accred-video-banner__media { min-height: 200px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .accred-video-banner__trigger:hover .accred-video-banner__thumb { transform: none; }
        .accred-video-banner__trigger:hover .accred-video-banner__play { transform: translate(-50%, -50%); }
    }
    </style>

    <script>
    (function () {
        var block = document.getElementById('accredVideoBlock');
        var trigger = document.getElementById('accredVideoTrigger');
        var modal   = document.getElementById('accredVideoModal');
        var player  = document.getElementById('accredVideoPlayer');
        var closeBtn = document.getElementById('accredVideoClose');
        var backdrop = document.getElementById('accredVideoBackdrop');
        if (!block || !trigger || !modal || !player) return;

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    block.classList.add('is-visible');
                    io.unobserve(block);
                }
            });
        }, { threshold: 0.06 });
        io.observe(block);

        function closeVideoModal() {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
            player.innerHTML = '';
            document.body.style.overflow = '';
        }

        function openVideoModal() {
            var ytId = trigger.getAttribute('data-youtube-id');
            var extUrl = trigger.getAttribute('data-video-url');

            player.innerHTML = '';

            if (ytId) {
                var iframe = document.createElement('iframe');
                iframe.src = 'https://www.youtube.com/embed/' + ytId + '?autoplay=1&rel=0&modestbranding=1';
                iframe.title = trigger.getAttribute('aria-label') || 'Video';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                player.appendChild(iframe);
                modal.hidden = false;
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                if (closeBtn) closeBtn.focus();
                return;
            }

            if (extUrl) {
                window.open(extUrl, '_blank', 'noopener,noreferrer');
            }
        }

        trigger.addEventListener('click', openVideoModal);
        if (closeBtn) closeBtn.addEventListener('click', closeVideoModal);
        if (backdrop) backdrop.addEventListener('click', closeVideoModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.hidden) closeVideoModal();
        });
    })();
    </script>
@endif

    <section class="academic-years-tilt-section" id="years-section" dir="rtl">
        <div class="section-bg-shapes" aria-hidden="true">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>
        </div>
      <div class="container">
        <div class="ay-head">
            <span class="ay-head__eyebrow">مسارك للتفوق</span>
            <h2 class="section-title-modern display-5 ay-head__title">الدوارات التدريبية </h2>
            <p class="section-subtitle description-text ay-head__desc">
              كل ما تحتاجه للتفوق في مكان واحد. اختر  دورتك التدريبية وانطلق نحو مستقبل
              مشرق.
            </p>
        </div>

        <div class="row g-4 g-xl-5 justify-content-center">
            @foreach($categories as $category)
                <div class="col-lg-4 col-md-6 card-tilt-wrapper">
                    <a href="{{ route('theme.category', $category->id) }}" class="year-portal h-100">
                        <span class="year-portal__shine" aria-hidden="true"></span>
                        <div class="year-portal__media">
                            <img
                                src="{{ get_image($category->thumbnail ?? '') }}"
                                class="year-portal__img"
                                alt="{{ $category->title }}"
                                loading="lazy"
                            />
                            <span class="year-portal__veil" aria-hidden="true"></span>
                            <span class="year-portal__orbit" aria-hidden="true"></span>
                            <span class="year-portal__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="year-portal__body">
                            <span class="year-portal__eyebrow">السنة الدراسية</span>
                            <h3 class="year-portal__title">{{ $category->title }}</h3>
                            @if (!empty(trim(strip_tags($category->description ?? ''))))
                                <div class="year-portal__desc">{!! $category->description !!}</div>
                            @endif
                            <span class="year-portal__cta">
                                <span>عرض الكورسات</span>
                                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
      </div>
    </section>



  <!-- Start Courses Section-->
  {{-- <section class="courses py-5">
    <div class="container">
      <h2 class="section-title" data-title="السنوات الدراسية">
        السنوات الدراسية
      </h2>
      <div class="row mb-5 flex-row-reverse">
        @foreach($categories as $category)
        <div class="col-lg-4 col-md-6 mt-5">
          <div class="classroom-card">
            <div class="classroom-card-header">
              <img src="{{ get_image($category->thumbnail ?? '') }}" alt="course-1" />
            </div>
            <div class="classroom-card-body">
              <a href="{{route('theme.category',$category->id)}}">
                <div class="">
                  <h4>{{$category->title}}</h4>
                  <hr />
                  <p>{!! $category->description !!}</p>
                </div>
              </a>
            </div>
          </div>
        </div>

        @endforeach


      </div>
    </div>
  </section> --}}
  <!-- End Courses Section-->

  @include('theme::includes.home_courses_sidebar')

  @include('theme::includes.book')
<!-- Start Features Section -->
@include('theme::includes.features',['features'=>$features])
  <!-- End Features Section -->

  {{-- ===== قسم الاعتماديات (شريط متحرك) ===== --}}
  @include('theme::includes.accreditations')

  {{-- ===== قسم الموقع الجغرافي (خريطة) ===== --}}
  @include('theme::includes.location_map')

    <script>
      document.addEventListener("DOMContentLoaded", function () {

        // Animate cards on scroll into view
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                const wrappersInView =
                  entry.target.querySelectorAll(".card-tilt-wrapper");
                wrappersInView.forEach((wrapper, index) => {
                  setTimeout(() => {
                    wrapper.classList.add("is-visible");
                  }, index * 150);
                });
                observer.unobserve(entry.target);
              }
            });
          },
          {
            threshold: 0.1,
          }
        );

        const sectionToObserve = document.getElementById("years-section");
        if (sectionToObserve) {
          observer.observe(sectionToObserve);
        }

        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        if (!prefersReducedMotion) {
          const tiltWrappers = document.querySelectorAll(".card-tilt-wrapper");
          tiltWrappers.forEach((wrapper) => {
            const tiltCard = wrapper.querySelector(".year-portal");
            if (!tiltCard) return;

            wrapper.addEventListener("mousemove", (e) => {
              const rect = wrapper.getBoundingClientRect();
              const x = e.clientX - rect.left;
              const y = e.clientY - rect.top;
              const centerX = rect.width / 2;
              const centerY = rect.height / 2;
              const rotateX = ((y - centerY) / centerY) * -8;
              const rotateY = ((x - centerX) / centerX) * 8;

              tiltCard.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
              tiltCard.style.setProperty("--mouse-x", `${x}px`);
              tiltCard.style.setProperty("--mouse-y", `${y}px`);
            });

            wrapper.addEventListener("mouseleave", () => {
              tiltCard.style.transform = "rotateX(0deg) rotateY(0deg) translateY(0)";
            });
          });
        }
      });
    </script>

@endsection
