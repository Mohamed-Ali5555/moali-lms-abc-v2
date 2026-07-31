@php
    $acColors  = get_active_theme_colors();
    $accent    = $acColors['accent'];
    $accentRgb = hex_to_rgb_csv($accent);
    $secondary = $acColors['secondary'];
    $secRgb    = hex_to_rgb_csv($secondary);

    $mapEmbedUrl = get_theme_settings('map_embed_url')
        ?: 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.3055758082715!2d46.72018547538058!3d24.65260427815087!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f04cf9e2c0d5f%3A0x1cf26b40c2f87e5b!2z2YXYsdmG2KkgYWxRYWRhIGxpbFRhZHJlZWI!5e0!3m2!1sar!2ssa!4v1690000000000!5m2!1sar!2ssa';

    $mapDirectionUrl = get_theme_settings('map_link')
        ?: 'https://maps.app.goo.gl/QGHM4MJH15226';

    $mapAddress = get_theme_settings('map_address')
        ?: get_theme_settings('contact_address')
        ?: 'الرياض – حي اشبيلية – طريق الملك عبدالله – عمارة المحمدية الدور الثالث';

    $mapHours = get_theme_settings('contact_hours')
        ?: 'السبت – الخميس: ٩ صباحاً – ٩ مساءً';

    $mapPhone = get_settings('site_phone')
        ?: get_theme_settings('contact_phone')
        ?: '';

    $defaultInfoCards = [
        ['label' => 'العنوان',      'text' => $mapAddress, 'icon' => 'fa-location-dot'],
        ['label' => 'أوقات الدوام', 'text' => $mapHours,   'icon' => 'fa-clock'],
        ['label' => 'للتواصل',      'text' => $mapPhone,   'icon' => 'fa-phone'],
    ];

    $infoCardsJson = get_theme_settings('loc_info_cards');
    $infoCardsArr  = ($infoCardsJson && is_array(json_decode($infoCardsJson, true)))
        ? json_decode($infoCardsJson, true)
        : null;

    $infoCards = [];
    if ($infoCardsArr && count($infoCardsArr) > 0) {
        foreach ($infoCardsArr as $i => $card) {
            $fallbackText = $defaultInfoCards[$i]['text'] ?? '';
            $text = trim((string) ($card['text'] ?? ''));
            $infoCards[] = [
                'label' => $card['label'] ?? ($defaultInfoCards[$i]['label'] ?? ''),
                'text'  => $text !== '' ? $text : $fallbackText,
                'icon'  => $card['icon'] ?? ($defaultInfoCards[$i]['icon'] ?? 'fa-circle-info'),
            ];
        }
    } else {
        $infoCards = $defaultInfoCards;
    }

    $infoCards = array_values(array_filter($infoCards, fn ($c) => trim((string) ($c['text'] ?? '')) !== ''));
@endphp

{{-- ============================================================
     قسم الموقع الجغرافي – Location Map Section
     ============================================================ --}}
<section class="loc-section" id="location-section" dir="rtl">

    <div class="loc-section__bg" aria-hidden="true">
        <span class="loc-section__pattern"></span>
        <span class="loc-section__blob loc-section__blob--1"></span>
        <span class="loc-section__blob loc-section__blob--2"></span>
        <span class="loc-section__blob loc-section__blob--3"></span>
    </div>

    {{-- شريط العنوان العلوي --}}
    @if ($mapAddress)
    <div class="loc-section__address-bar">
        <div class="container">
            <p class="loc-section__address-text">
                <span class="loc-section__address-icon"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                {{ $mapAddress }}
            </p>
        </div>
    </div>
    @endif

    {{-- الخريطة – بطاقة عائمة بحواف مستديرة --}}
    <div class="loc-section__map-outer">
        <div class="container">
            <div class="loc-section__map-card">
                <span class="loc-section__map-glow" aria-hidden="true"></span>
                <div class="loc-section__map-wrap">
                    <iframe
                        src="{{ $mapEmbedUrl }}"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="موقعنا على الخريطة"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>

    {{-- البيانات المرتبطة – 3 أعمدة --}}
    @if (count($infoCards) > 0)
    <div class="loc-section__info-wrap">
        <div class="container">
            <div class="loc-section__info-grid">
                @foreach ($infoCards as $card)
                <div class="loc-info-card">
                    <div class="loc-info-card__icon">
                        <i class="fa-solid {{ $card['icon'] ?? 'fa-circle-info' }}" aria-hidden="true"></i>
                    </div>
                    <div class="loc-info-card__content">
                        <span class="loc-info-card__label">{{ $card['label'] ?? '' }}</span>
                        <p class="loc-info-card__text">{!! nl2br(e($card['text'] ?? '')) !!}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @if ($mapDirectionUrl)
            <div class="loc-section__actions">
                <a
                    href="{{ $mapDirectionUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="loc-section__directions-btn"
                >
                    <i class="fa-solid fa-diamond-turn-right" aria-hidden="true"></i>
                    <span>احصل على الاتجاهات</span>
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

</section>

<style>
/* ===================================================
   Location Section – layered gradient + glow orbs
   =================================================== */
.loc-section {
    --ac: {{ $accent }};
    --ac-rgb: {{ $accentRgb }};
    --sec-rgb: {{ $secRgb }};

    position: relative;
    overflow: hidden;
    isolation: isolate;
    background:
        radial-gradient(900px 480px at 8% -15%, rgba(var(--c-accent-rgb), .32), transparent 62%),
        radial-gradient(760px 460px at 108% 115%, rgba(var(--c-secondary-rgb), .28), transparent 60%),
        linear-gradient(160deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, .35) 100%),
        rgb(var(--c-primary-rgb));
}

.loc-section__bg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}

.loc-section__pattern {
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 28 28'%3E%3Cg fill='none' stroke='%23ffffff' stroke-width='1.4' opacity='0.07'%3E%3Cpath d='M14 8.5v11M8.5 14h11'/%3E%3C/g%3E%3C/svg%3E");
    background-size: 28px 28px;
    -webkit-mask-image: radial-gradient(120% 120% at 50% 0%, #000 40%, transparent 90%);
    mask-image: radial-gradient(120% 120% at 50% 0%, #000 40%, transparent 90%);
}

.loc-section__blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    opacity: .55;
}

.loc-section__blob--1 {
    width: 420px;
    height: 420px;
    top: -180px;
    right: -120px;
    background: radial-gradient(circle, rgba(var(--c-accent-rgb), .55), transparent 70%);
}

.loc-section__blob--2 {
    width: 380px;
    height: 380px;
    bottom: -200px;
    left: -100px;
    background: radial-gradient(circle, rgba(var(--c-secondary-rgb), .5), transparent 70%);
}

.loc-section__blob--3 {
    width: 260px;
    height: 260px;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(255, 255, 255, .08), transparent 70%);
    opacity: .6;
}

.loc-section > .loc-section__address-bar,
.loc-section > .loc-section__map-outer,
.loc-section > .loc-section__info-wrap {
    position: relative;
    z-index: 1;
}

/* ---- Address bar ---- */
.loc-section__address-bar {
    padding: 1.1rem 0;
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.loc-section__address-text {
    margin: 0;
    text-align: center;
    font-size: clamp(.88rem, 2vw, 1.02rem);
    font-weight: 700;
    color: #fff;
    line-height: 1.7;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .65rem;
    flex-wrap: wrap;
}

.loc-section__address-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    min-width: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(var(--c-accent-rgb), .9), rgba(var(--c-secondary-rgb), .9));
    color: #fff;
    font-size: .8rem;
    box-shadow: 0 4px 14px rgba(var(--c-accent-rgb), .4);
}

/* ---- Floating map card ---- */
.loc-section__map-outer {
    padding: 2.5rem 0 .5rem;
}

.loc-section__map-card {
    position: relative;
    border-radius: 1.5rem;
    padding: .5rem;
    background: linear-gradient(140deg, rgba(255, 255, 255, .22), rgba(255, 255, 255, .04));
    box-shadow: 0 24px 60px -18px rgba(0, 0, 0, .55), 0 2px 0 rgba(255, 255, 255, .06) inset;
}

.loc-section__map-glow {
    position: absolute;
    inset: -1px;
    border-radius: inherit;
    padding: 1px;
    background: linear-gradient(120deg, rgba(var(--c-accent-rgb), .8), rgba(255, 255, 255, 0) 40%, rgba(var(--c-secondary-rgb), .8));
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
    opacity: .8;
}

/* ---- Full-width map ---- */
.loc-section__map-wrap {
    width: 100%;
    height: clamp(300px, 40vw, 460px);
    position: relative;
    border-radius: 1.15rem;
    overflow: hidden;
    background: rgba(0, 0, 0, 0.2);
}

.loc-section__map-wrap iframe {
    display: block;
    width: 100%;
    height: 100%;
    filter: saturate(1.05) contrast(1.02);
}

/* ---- Info grid ---- */
.loc-section__info-wrap {
    padding: 3rem 0 3.5rem;
}

.loc-section__info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

@media (max-width: 991px) {
    .loc-section__info-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

@media (min-width: 992px) and (max-width: 1199px) {
    .loc-section__info-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: .85rem;
    }
}

.loc-info-card {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    background: linear-gradient(180deg, rgba(255, 255, 255, .1), rgba(255, 255, 255, .04));
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 1.1rem;
    padding: 1.35rem 1.4rem;
    min-height: 120px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
    transition: border-color .25s, box-shadow .25s, transform .25s, background .25s;
    opacity: 0;
    transform: translateY(20px);
}

.loc-info-card::before {
    content: '';
    position: absolute;
    inset: 0 0 auto 0;
    height: 2px;
    background: linear-gradient(90deg, rgba(var(--c-accent-rgb), 0), rgba(var(--c-accent-rgb), .9), rgba(var(--c-secondary-rgb), 0));
    opacity: 0;
    transition: opacity .3s;
}

.loc-section.is-visible .loc-info-card {
    opacity: 1;
    transform: translateY(0);
}

.loc-section.is-visible .loc-info-card:nth-child(1) { transition: opacity .55s ease .05s, transform .55s ease .05s, border-color .25s, box-shadow .25s, background .25s; }
.loc-section.is-visible .loc-info-card:nth-child(2) { transition: opacity .55s ease .15s, transform .55s ease .15s, border-color .25s, box-shadow .25s, background .25s; }
.loc-section.is-visible .loc-info-card:nth-child(3) { transition: opacity .55s ease .25s, transform .55s ease .25s, border-color .25s, box-shadow .25s, background .25s; }

.loc-info-card:hover {
    border-color: rgba(255, 255, 255, 0.28);
    background: linear-gradient(180deg, rgba(255, 255, 255, .16), rgba(255, 255, 255, .05));
    box-shadow: 0 18px 42px rgba(0, 0, 0, 0.3);
    transform: translateY(-5px);
}

.loc-info-card:hover::before { opacity: 1; }

.loc-info-card__icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: .9rem;
    background: linear-gradient(135deg, rgba(var(--c-accent-rgb), .95), rgba(var(--c-secondary-rgb), .85));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: transform .3s, box-shadow .3s;
    box-shadow: 0 8px 20px rgba(var(--c-accent-rgb), 0.35);
}

.loc-info-card:hover .loc-info-card__icon {
    transform: scale(1.08) rotate(-4deg);
    box-shadow: 0 10px 26px rgba(var(--c-accent-rgb), 0.5);
}

.loc-info-card__content { flex: 1; }

.loc-info-card__label {
    display: block;
    font-size: .78rem;
    font-weight: 800;
    color: rgb(var(--c-secondary-rgb));
    letter-spacing: .03em;
    margin-bottom: .35rem;
}

.loc-info-card__text {
    color: rgba(255, 255, 255, 0.88);
    font-size: .92rem;
    line-height: 1.65;
    margin: 0;
    font-weight: 600;
}

/* ---- Directions CTA ---- */
.loc-section__actions {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.loc-section__directions-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .65rem;
    padding: .95rem 2rem;
    border-radius: .9rem;
    background: linear-gradient(120deg, rgb(var(--c-accent-rgb)), rgb(var(--c-secondary-rgb)));
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    transition: transform .25s, box-shadow .25s;
    box-shadow: 0 10px 28px rgba(var(--c-accent-rgb), 0.4);
}

.loc-section__directions-btn i {
    transition: transform .25s;
}

.loc-section__directions-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 38px rgba(var(--c-accent-rgb), 0.5);
    color: #fff;
    text-decoration: none;
}

.loc-section__directions-btn:hover i {
    transform: translateX(-4px);
}
</style>

<script>
(function () {
    const locSection = document.getElementById('location-section');
    if (!locSection) return;
    const io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                locSection.classList.add('is-visible');
                io.unobserve(locSection);
            }
        });
    }, { threshold: 0.06 });
    io.observe(locSection);
})();
</script>
