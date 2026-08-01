@php
    $social = \Modules\Theme\App\Models\theme_social::where('status', 1)->get();
    $siteName = trim((get_theme_settings('jop_title') ?: '') . ' ' . (get_theme_settings('name') ?: ''));
    $supportPhone = trim((string) (get_theme_settings('technical') ?: ''));
    $supportEnabled = (string) get_theme_settings('technical_status') !== '0' && $supportPhone !== '';
    $waDigits = preg_replace('/\D+/', '', $supportPhone) ?: '';
    $mapLink    = trim((string) (get_theme_settings('map_link') ?: ''));
    $mapAddress = trim((string) (get_theme_settings('map_address') ?: ''));
@endphp

<footer class="ft site-footer" dir="rtl">
    <div class="ft-wave" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path class="ft-wave__fill" d="M0,64 C240,120 480,0 720,40 C960,80 1200,120 1440,48 L1440,0 L0,0 Z"></path>
        </svg>
    </div>

    <div class="ft-atmosphere" aria-hidden="true">
        <span class="ft-orb ft-orb--a"></span>
        <span class="ft-orb ft-orb--b"></span>
        <span class="ft-gridline"></span>
    </div>

    <div class="container-fluid">
        <div class="ft-main">
            <div class="ft-brand">
                @if (get_theme_settings('footer_image'))
                    <div class="ft-brand__visual" aria-label="{{ get_phrase('الاعتمادية') }}">
                        <img src="{{ asset(get_theme_settings('footer_image')) }}" alt="{{ $siteName }}" loading="lazy">
                    </div>
                @endif

                <div class="ft-brand__content">
                    <a href="{{ route('theme.home') }}" class="ft-brand__logo" aria-label="{{ $siteName }}">
                        <img src="{{ asset(get_theme_settings('logo') ?? '') }}" alt="{{ $siteName }}" class="logo light">
                        <img src="{{ asset(get_theme_settings('dark_logo') ?? '') }}" alt="{{ $siteName }}" class="logo dark">
                    </a>

                    @if ($siteName !== '')
                        <p class="ft-brand__name">{{ $siteName }}</p>
                    @endif

                    <div class="ft-brand__quote">
                        {!! get_theme_settings('footer_description') !!}
                    </div>
                </div>
            </div>

            <nav class="ft-links" aria-label="{{ get_phrase('روابط سريعة') }}">
                <h3 class="ft-title">{{ get_phrase('استكشف') }}</h3>
                <ul>
                    <li>
                        <a href="{{ route('theme.home') }}">
                            <i class="fa-solid fa-house"></i>
                            <span>{{ get_phrase('الرئيسية') }}</span>
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('theme.my.courses') }}">
                                <i class="fa-solid fa-graduation-cap"></i>
                                <span>{{ get_phrase('كورساتي') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('theme.my.performance') }}">
                                <i class="fa-solid fa-chart-line"></i>
                                <span>{{ get_phrase('أدائي') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('theme.my.wallet') }}">
                                <i class="fa-solid fa-wallet"></i>
                                <span>{{ get_phrase('محفظتي') }}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('theme.show_login') }}">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                <span>{{ get_phrase('تسجيل الدخول') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('theme.show_register') }}">
                                <i class="fa-solid fa-user-plus"></i>
                                <span>{{ get_phrase('إنشاء حساب') }}</span>
                            </a>
                        </li>
                    @endauth

                    @if (get_theme_settings('about_status') !== '0')
                        <li>
                            <a href="{{ route('theme.about.us') }}">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>{{ get_phrase('من نحن') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (get_theme_settings('contact_status') !== '0')
                        <li>
                            <a href="{{ route('theme.contact.us') }}">
                                <i class="fa-solid fa-envelope"></i>
                                <span>{{ get_phrase('تواصل معنا') }}</span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ $mapLink ?: route('theme.accreditation') }}" @if($mapLink) target="_blank" rel="noopener" @endif>
                            <i class="fa-solid fa-location-dot"></i>
                            <span>{{ get_phrase('موقعنا الجغرافي') }}</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('theme.accreditation') }}">
                            <i class="fa-solid fa-award"></i>
                            <span>{{ get_phrase('الاعتمادية') }}</span>
                        </a>
                    </li>

                    @if (get_theme_settings('terms_status') == 1)
                        <li>
                            <a href="{{ route('theme.terms.condition') }}">
                                <i class="fa-solid fa-file-shield"></i>
                                <span>{{ get_phrase('الشروط والخصوصية') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <div class="ft-contact">
                <h3 class="ft-title">{{ get_phrase('تواصل معنا') }}</h3>
                <p class="ft-contact__text">
                    {{ get_phrase('لو احتجت مساعدة، فريق الدعم جاهز يرد عليك بسرعة عبر واتساب.') }}
                </p>

                @if ($supportEnabled)
                    <a class="ft-support" href="https://wa.me/{{ $waDigits }}" target="_blank" rel="noopener">
                        <span class="ft-support__icon">
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <span class="ft-support__meta">
                            <small>{{ get_phrase('الدعم الفني') }}</small>
                            <strong dir="ltr">{{ $supportPhone }}</strong>
                        </span>
                        <i class="fa-solid fa-arrow-left ft-support__arrow"></i>
                    </a>
                @endif

                @if($mapAddress || $mapLink)
                    <div class="ft-location">
                        <div class="ft-location__icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="ft-location__body">
                            @if($mapAddress)
                                <span class="ft-location__address">{{ $mapAddress }}</span>
                            @endif
                            @if($mapLink)
                                <a href="{{ $mapLink }}" target="_blank" rel="noopener" class="ft-location__link">
                                    <i class="fa-solid fa-map-location-dot"></i>
                                    {{ get_phrase('فتح في خرائط جوجل') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($social->isNotEmpty())
                    <div class="ft-social">
                        @foreach ($social as $row)
                            @php
                                $icon = (string) ($row->thumbnail ?? '');
                                if ($icon === '' || str_contains($icon, '/') || str_contains($icon, '\\')) {
                                    $icon = strtolower((string) $row->title);
                                }
                                $icon = preg_replace('/^fa-brands\s+fa-/', '', $icon);
                            @endphp
                            <a href="{{ $row->url }}" target="_blank" rel="noopener" aria-label="{{ $row->title }}" class="ft-social__btn">
                                <i class="fa-brands fa-{{ $icon }}"></i>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="ft-bottom">
        <div class="container">
            <div class="ft-bottom__row">
                @if (get_theme_settings('copyright_status') != 0)
                    <p class="ft-copy">&copy; <span>
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                        </span> {{ get_theme_settings('copyright_prefix') ?: 'جميع الحقوق محفوظة لـ' }} <a href="{{ get_theme_settings('copyright_url') ?: 'https://wa.me/+201044445330' }}" target="_blank" rel="noopener">{{ get_theme_settings('copyright_text') ?: 'Arkan' }}</a>
                    </p>
                @endif

                @if (get_theme_settings('terms_status') == 1)
                    <a class="ft-legal" href="{{ route('theme.terms.condition') }}">الشروط والأحكام وسياسة الخصوصية</a>
                @endif
            </div>
        </div>
    </div>
</footer>
