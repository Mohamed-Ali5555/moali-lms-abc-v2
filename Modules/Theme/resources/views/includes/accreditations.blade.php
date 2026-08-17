@php
    if (get_theme_settings('accr_status') == '0') {
        return;
    }

    $acColors   = get_active_theme_colors();
    $accent     = $acColors['accent'];
    $accentRgb  = hex_to_rgb_csv($accent);
    $secondary  = $acColors['secondary'];
    $secRgb     = hex_to_rgb_csv($secondary);

    $accrEyebrow = get_theme_settings('accr_eyebrow') ?: 'جودة معتمدة';
    $accrTitle   = get_theme_settings('accr_title')   ?: 'اعتماداتنا وشراكاتنا';
    $accrDesc    = get_theme_settings('accr_desc')    ?: 'نفخر بحصولنا على اعتمادات دولية ومحلية تضمن لك أعلى معايير التعليم.';

    $defaultBadges = [
        ['name' => 'وزارة التعليم',          'sub' => 'المملكة العربية السعودية', 'icon' => 'fa-graduation-cap'],
        ['name' => 'الهيئة السعودية للتدريب', 'sub' => 'اعتماد مؤسسي',            'icon' => 'fa-medal'],
        ['name' => 'ISO 9001:2015',           'sub' => 'جودة المنهج التعليمي',     'icon' => 'fa-shield-halved'],
        ['name' => 'المركز الوطني للتقويم',  'sub' => 'NCAAA معتمد',             'icon' => 'fa-star'],
        ['name' => 'مركز القيادة للتدريب',   'sub' => 'شريك استراتيجي',           'icon' => 'fa-certificate'],
        ['name' => 'هيئة تقويم التعليم',     'sub' => 'Etec اعتماد رسمي',        'icon' => 'fa-book-open'],
        ['name' => 'شراكة أكاديمية',         'sub' => 'مؤسسات تعليمية رائدة',    'icon' => 'fa-handshake'],
        ['name' => 'جائزة التميز التعليمي',  'sub' => 'أفضل مركز تدريبي',        'icon' => 'fa-trophy'],
    ];

    $badgesJson = get_theme_settings('accr_badges');
    $badgesDecoded = $badgesJson ? json_decode($badgesJson, true) : null;
    $badges = (is_array($badgesDecoded) && count($badgesDecoded) > 0)
              ? $badgesDecoded
              : $defaultBadges;

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
@endphp

{{-- ============================================================
     سلايدر الاعتماديات – Accreditations Ticker Section (Dynamic)
     ============================================================ --}}
<section class="accred-section" id="accreditations-section" dir="rtl">

    <div class="accred-section__bg" aria-hidden="true">
        <span class="accred-section__pattern"></span>
        <span class="accred-section__blob accred-section__blob--1"></span>
        <span class="accred-section__blob accred-section__blob--2"></span>
    </div>

    @if ($showAccrVideo)
    <div class="accred-video-banner" id="accredVideoBlock">
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
    @endif

    <div class="container">
        <div class="accred-section__head">
            <span class="accred-section__eyebrow">
                <i class="fa-solid fa-award" aria-hidden="true"></i>
                {{ $accrEyebrow }}
            </span>
            <h2 class="accred-section__title">{{ $accrTitle }}</h2>
            <p class="accred-section__desc">{{ $accrDesc }}</p>
        </div>
    </div>

    <div class="accred-ticker-wrap">
        <div class="accred-ticker" id="accredTicker" aria-label="شركاؤنا واعتماداتنا">
            @for ($pass = 0; $pass < 2; $pass++)
            <div class="accred-ticker__track" aria-hidden="{{ $pass === 1 ? 'true' : 'false' }}">
                @foreach ($badges as $badge)
                <div class="accred-badge">
                    <div class="accred-badge__inner">
                        <div class="accred-badge__icon-wrap">
                            <i class="fa-solid {{ $badge['icon'] ?? 'fa-award' }}" aria-hidden="true"></i>
                        </div>
                        <div class="accred-badge__text">
                            <span class="accred-badge__name">{{ $badge['name'] ?? '' }}</span>
                            <span class="accred-badge__sub">{{ $badge['sub'] ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endfor
        </div>
    </div>

    <div class="container">
        <div class="accred-stats">
            <div class="accred-stat">
                <span class="accred-stat__number">{{ count($badges) }}+</span>
                <span class="accred-stat__label">جهة اعتماد</span>
            </div>
            <div class="accred-stat__sep" aria-hidden="true"></div>
            <div class="accred-stat">
                <span class="accred-stat__number">100%</span>
                <span class="accred-stat__label">معتمد رسمياً</span>
            </div>
            <div class="accred-stat__sep" aria-hidden="true"></div>
            <div class="accred-stat">
                <span class="accred-stat__number">A+</span>
                <span class="accred-stat__label">تقييم الجودة</span>
            </div>
        </div>
    </div>

</section>

<style>
.accred-section {
    --ac: {{ $accent }};
    --ac-rgb: {{ $accentRgb }};
    --sec-rgb: {{ $secRgb }};

    position: relative;
    padding: 0 0 3.5rem;
    overflow: hidden;
    isolation: isolate;
    background:
        radial-gradient(900px 480px at 92% -10%, rgba(var(--c-accent-rgb), .32), transparent 62%),
        radial-gradient(760px 460px at -8% 115%, rgba(var(--c-secondary-rgb), .28), transparent 60%),
        linear-gradient(160deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .35) 100%),
        rgb(var(--c-primary-rgb));
}

.accred-section__bg { position: absolute; inset: 0; pointer-events: none; z-index: 0; }

.accred-section__pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 28 28'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1.4' opacity='0.07'%3E%3Cpath d='M14 8.5v11M8.5 14h11'/%3E%3C/g%3E%3C/svg%3E");
    background-size: 28px 28px;
    -webkit-mask-image: radial-gradient(120% 120% at 50% 0%, #000 40%, transparent 90%);
    mask-image: radial-gradient(120% 120% at 50% 0%, #000 40%, transparent 90%);
}

.accred-section__blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(75px);
    opacity: .5;
}

.accred-section__blob--1 {
    width: 400px;
    height: 400px;
    top: -170px;
    left: -110px;
    background: radial-gradient(circle, rgba(var(--c-accent-rgb), .55), transparent 70%);
}

.accred-section__blob--2 {
    width: 360px;
    height: 360px;
    bottom: -190px;
    right: -100px;
    background: radial-gradient(circle, rgba(var(--c-secondary-rgb), .5), transparent 70%);
}

.accred-section .container { position: relative; z-index: 1; }

.accred-section__head {
    text-align: center;
    padding-top: 3.5rem;
    margin-bottom: 2.5rem;
    opacity: 0;
    transform: translateY(24px);
    transition: opacity .65s ease, transform .65s ease;
}
.accred-section.is-visible .accred-section__head {
    opacity: 1; transform: translateY(0);
}

.accred-section__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: linear-gradient(120deg, rgba(var(--c-accent-rgb), .22), rgba(var(--c-secondary-rgb), .18));
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.22);
    border-radius: 50px;
    padding: .4rem 1.2rem;
    font-size: .82rem;
    font-weight: 700;
    letter-spacing: .03em;
    margin-bottom: 1rem;
    box-shadow: 0 6px 20px rgba(var(--c-accent-rgb), .18);
}

.accred-section__eyebrow i {
    color: rgb(var(--c-secondary-rgb));
}

.accred-section__title {
    font-size: clamp(1.5rem, 3vw, 2.2rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: .6rem;
}

.accred-section__desc {
    color: rgba(255, 255, 255, 0.72);
    font-size: .95rem;
    max-width: 500px;
    margin: 0 auto;
}

/* ── Full-width cinematic video banner ── */
.accred-video-banner {
    position: relative;
    z-index: 1;
    width: 100%;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .7s ease, transform .7s ease;
}
.accred-section.is-visible .accred-video-banner {
    opacity: 1;
    transform: translateY(0);
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
    outline-offset: -3px;
}

.accred-video-banner__media {
    display: block;
    position: relative;
    width: 100%;
    aspect-ratio: 1024 / 285;
    min-height: 220px;
    max-height: 600px;
    overflow: hidden;
    background: #1a1a1a;
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
    background: rgba(0, 0, 0, 0.38);
    pointer-events: none;
    transition: background .35s ease;
}
.accred-video-banner__trigger:hover .accred-video-banner__overlay {
    background: rgba(0, 0, 0, 0.28);
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
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    pointer-events: none;
    transition: transform .3s ease, box-shadow .3s ease;
}
.accred-video-banner__trigger:hover .accred-video-banner__play {
    transform: translate(-50%, -50%) scale(1.06);
    box-shadow: 0 14px 48px rgba(0, 0, 0, 0.38);
}

/* ── Video modal ── */
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

.accred-ticker-wrap {
    position: relative;
    overflow: hidden;
    width: 100%;
    padding: .35rem 0;
    background: rgba(255, 255, 255, 0.04);
    border-block: 1px solid rgba(255, 255, 255, 0.1);
    mask-image: linear-gradient(90deg, transparent 0%, #000 6%, #000 94%, transparent 100%);
    -webkit-mask-image: linear-gradient(90deg, transparent 0%, #000 6%, #000 94%, transparent 100%);
}

.accred-ticker {
    display: flex;
    width: max-content;
    animation: accredScroll 40s linear infinite;
}

.accred-ticker:hover { animation-play-state: paused; }

.accred-ticker__track {
    display: flex;
    gap: 1.1rem;
    padding: 1.2rem .55rem;
}

@keyframes accredScroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.accred-badge { flex-shrink: 0; cursor: default; }

.accred-badge__inner {
    display: flex;
    align-items: center;
    gap: .9rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .04));
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 1.1rem;
    padding: .9rem 1.4rem .9rem 1.15rem;
    min-width: 215px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: background .25s, border-color .25s, transform .25s, box-shadow .25s;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.22);
}

.accred-badge__inner::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
    background: linear-gradient(90deg, rgba(var(--c-accent-rgb), 0), rgba(var(--c-accent-rgb), .9), rgba(var(--c-secondary-rgb), 0));
    opacity: 0;
    transition: opacity .25s;
}

.accred-badge:hover .accred-badge__inner {
    background: linear-gradient(180deg, rgba(255, 255, 255, .18), rgba(255, 255, 255, .06));
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-5px);
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.3);
}
.accred-badge:hover .accred-badge__inner::before { opacity: 1; }

.accred-badge__icon-wrap {
    width: 46px;
    height: 46px;
    min-width: 46px;
    border-radius: .85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    background: linear-gradient(135deg, rgba(var(--c-accent-rgb), .95), rgba(var(--c-secondary-rgb), .85));
    color: #fff;
    position: relative;
    z-index: 1;
    transition: transform .3s, box-shadow .3s;
    box-shadow: 0 8px 20px rgba(var(--c-accent-rgb), .35);
}
.accred-badge:hover .accred-badge__icon-wrap {
    transform: scale(1.08) rotate(-4deg);
    box-shadow: 0 10px 26px rgba(var(--c-accent-rgb), .5);
}

.accred-badge__text {
    display: flex;
    flex-direction: column;
    gap: .15rem;
    position: relative;
    z-index: 1;
}

.accred-badge__name {
    color: #fff;
    font-size: .9rem;
    font-weight: 700;
    white-space: nowrap;
}

.accred-badge__sub {
    color: rgba(255, 255, 255, .58);
    font-size: .75rem;
    white-space: nowrap;
}

.accred-stats {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2.5rem;
    margin-top: 2.2rem;
    padding: 1.5rem 2rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, .04));
    border: 1px solid rgba(255, 255, 255, 0.16);
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
    border-radius: 1.35rem;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .7s ease .25s, transform .7s ease .25s;
}
.accred-stats::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
    background: linear-gradient(90deg, rgba(var(--c-accent-rgb), 0), rgba(var(--c-accent-rgb), .8), rgba(var(--c-secondary-rgb), .8), rgba(var(--c-secondary-rgb), 0));
}
.accred-section.is-visible .accred-stats {
    opacity: 1; transform: translateY(0);
}

.accred-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .2rem;
}

.accred-stat__number {
    font-size: clamp(1.5rem, 3vw, 1.95rem);
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: -.01em;
    background: linear-gradient(120deg, #fff, rgb(var(--c-secondary-rgb)));
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.accred-stat__label {
    font-size: .78rem;
    color: rgba(255, 255, 255, 0.68);
    font-weight: 600;
}

.accred-stat__sep {
    width: 1px;
    height: 36px;
    background: linear-gradient(180deg, transparent, rgba(255, 255, 255, 0.28), transparent);
}

@media (max-width: 480px) {
    .accred-stats { flex-wrap: wrap; gap: 1.2rem; }
    .accred-stat__sep { display: none; }
    .accred-video-banner__media { min-height: 200px; }
    .accred-section__head { padding-top: 2.5rem; }
}

@media (prefers-reduced-motion: reduce) {
    .accred-ticker { animation: none; }
    .accred-ticker__track { flex-wrap: wrap; }
    .accred-ticker__track[aria-hidden="true"] { display: none; }
    .accred-video-banner__trigger:hover .accred-video-banner__thumb { transform: none; }
    .accred-video-banner__trigger:hover .accred-video-banner__play { transform: translate(-50%, -50%); }
}
</style>

<script>
(function () {
    var s = document.getElementById('accreditations-section');
    if (!s) return;
    var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (e) {
            if (e.isIntersecting) { s.classList.add('is-visible'); io.unobserve(s); }
        });
    }, { threshold: 0.06 });
    io.observe(s);

    var trigger = document.getElementById('accredVideoTrigger');
    var modal   = document.getElementById('accredVideoModal');
    var player  = document.getElementById('accredVideoPlayer');
    var closeBtn = document.getElementById('accredVideoClose');
    var backdrop = document.getElementById('accredVideoBackdrop');
    if (!trigger || !modal || !player) return;

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
