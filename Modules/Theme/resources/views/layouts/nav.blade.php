@php
    $cartCount = auth()->check()
        ? (int) App\Models\CartItem::where('user_id', auth()->user()->id)->sum('qty')
        : 0;
    $walletBalance = auth()->check() ? (float) (auth()->user()->wallet ?? 0) : 0;
    $authUser = auth()->user();
@endphp

<link rel="stylesheet" href="{{ asset('modules/theme/css/header-modern.css') }}">

<nav id="main__navbar" class="tn-nav">
    <div class="container">
        <div class="tn-bar navbar {{ auth()->check() ? '' : 'guest' }}">
            <div class="tn-brand brand">
                <a href="{{ route('theme.home') }}" class="tn-logo">
                    <img src="{{ asset(get_theme_settings('logo') ?? '') }}" alt="{{ get_theme_settings('name') }}" class="logo light">
                    <img src="{{ asset(get_theme_settings('dark_logo') ?? '') }}" alt="{{ get_theme_settings('name') }}" class="logo dark">
                </a>

                <div class="theme tn-theme">
                    <button id="theme__button" class="switch__button" data-theme="light" type="button" aria-label="{{ get_phrase('تبديل الوضع') }}">
                        <svg width="24" height="24" fill="none" class="sun" aria-hidden="true">
                            <path d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M12 4v1M18 6l-1 1M20 12h-1M18 18l-1-1M12 19v1M7 17l-1 1M5 12H4M7 7 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <svg width="24" height="24" fill="none" class="moon" aria-hidden="true">
                            <path d="M18 15.63c-.977.52-1.945.481-3.13.481A6.981 6.981 0 0 1 7.89 9.13c0-1.185-.04-2.153.481-3.13C6.166 7.174 5 9.347 5 12.018A6.981 6.981 0 0 0 11.982 19c2.67 0 4.844-1.166 6.018-3.37ZM16 5c0 2.08-.96 4-3 4 2.04 0 3 .92 3 3 0-2.08.96-3 3-3-2.04 0-3-1.92-3-4Z" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                @if (isset($page) && $page == 'play_vedio')
                    <a href="javascript:void(0);" class="tn-icon-btn video-zoom-btn" id="fullscreen" aria-label="{{ get_phrase('ملء الشاشة') }}">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M8.08917 11.9108C8.415 12.2367 8.415 12.7633 8.08917 13.0892L2.845 18.3333H6.66667C7.1275 18.3333 7.5 18.7067 7.5 19.1667C7.5 19.6267 7.1275 20 6.66667 20H2.5C1.12167 20 0 18.8783 0 17.5V13.3333C0 12.8733 0.3725 12.5 0.833333 12.5C1.29417 12.5 1.66667 12.8733 1.66667 13.3333V17.155L6.91083 11.9108C7.23667 11.585 7.76333 11.585 8.08917 11.9108ZM17.5 0H13.3333C12.8725 0 12.5 0.373333 12.5 0.833333C12.5 1.29333 12.8725 1.66667 13.3333 1.66667H17.155L11.9108 6.91083C11.585 7.23667 11.585 7.76333 11.9108 8.08917C12.0733 8.25167 12.2867 8.33333 12.5 8.33333C12.7133 8.33333 12.9267 8.25167 13.0892 8.08917L18.3333 2.845V6.66667C18.3333 7.12667 18.7058 7.5 19.1667 7.5C19.6275 7.5 20 7.12667 20 6.66667V2.5C20 1.12167 18.8783 0 17.5 0Z" fill="currentColor"/>
                        </svg>
                    </a>
                @endif

                @guest
                    <button class="menu d-flex d-md-none tn-burger" id="menu__toggle__btn" type="button" aria-label="Main Menu">
                        <svg width="100" height="60" viewBox="0 0 100 100" aria-hidden="true">
                            <path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058" />
                            <path class="line line2" d="M 20,50 H 80" />
                            <path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942" />
                        </svg>
                    </button>
                @endguest
            </div>

            @auth
                <div class="tn-actions buttons buttons-afterLogin">
                    <a href="{{ route('theme.my.wallet') }}" class="tn-icon-btn tn-wallet-btn balance-container position-relative" id="wallet-button" title="{{ get_phrase('محفظتي') }}" aria-label="{{ get_phrase('محفظتي') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        <span class="tn-badge tn-wallet-badge" id="wallet-balance">{{ number_format($walletBalance, 0) }}</span>
                    </a>

                    <a href="{{ route('theme.cart') }}" class="tn-icon-btn cart-button position-relative" id="cart-button" aria-label="{{ get_phrase('السلة') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                        <span class="tn-badge cart-number" id="cart-number">{{ $cartCount }}</span>
                    </a>

                    <div class="tn-user Userprofile dropdown">
                        <button class="tn-user__btn us-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ get_phrase('حسابي') }}">
                            <img class="image-40" src="{{ get_image($authUser->photo) }}" alt="{{ $authUser->name }}">
                            <span class="tn-user__meta d-none d-lg-flex">
                                <strong>{{ \Illuminate\Support\Str::limit($authUser->name, 16) }}</strong>
                                <small>{{ $authUser->role === 'student' ? get_phrase('طالب') : get_phrase(ucfirst($authUser->role)) }}</small>
                            </span>
                            <svg class="tn-user__chev" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>

                        <ul class="dropdown-menu dropmenu-end userDropDown tn-menu">
                            <li class="tn-menu__head figure_user">
                                <img src="{{ get_image($authUser->photo) }}" alt="{{ $authUser->name }}">
                                <div class="figure_text">
                                    <h4>{{ $authUser->name }}</h4>
                                    <p>{{ $authUser->role === 'student' ? get_phrase('طالب') : get_phrase(ucfirst($authUser->role)) }}</p>
                                </div>
                            </li>

                            @if (in_array($authUser->role, ['admin', 'instructor']))
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route($authUser->role . '.dashboard') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                                        </span>
                                        <span>{{ get_phrase('لوحة التحكم') }}</span>
                                    </a>
                                </li>
                            @endif

                            @if (!in_array($authUser->role, ['admin', 'instructor'], true))
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.courses') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                        </span>
                                        <span>{{ get_phrase('كورساتي') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.bootcamps') }}">
                                        <dotlottie-player
                                            src="{{ asset('assets/frontend/default/image/icons/history.json') }}"
                                            background="transparent" speed="1" style="width: 30px; height: 30px;"
                                            part="lottie-svg" loop autoplay hover></dotlottie-player>

                                            معسكراتي
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.books') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                        </span>
                                        <span>{{ get_phrase('كتبي') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.performance') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                                        </span>
                                        <span>{{ get_phrase('أدائي') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.profile') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        </span>
                                        <span>{{ get_phrase('حسابي') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.my.wallet') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                                        </span>
                                        <span>{{ get_phrase('محفظتي') }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item tn-menu__link" href="{{ route('theme.purchase.history') }}">
                                        <span class="tn-menu__icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                        </span>
                                        <span>{{ get_phrase('فواتيري') }}</span>
                                    </a>
                                </li>
                            @endif

                            <li class="tn-menu__divider" aria-hidden="true"></li>
                            <li>
                                <a class="dropdown-item tn-menu__link tn-menu__link--logout mb-0" href="{{ route('logout') }}">
                                    <span class="tn-menu__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    </span>
                                    <span>{{ get_phrase('خروج') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="tn-guest buttons d-none d-md-flex" id="menu__btns">
                    <a href="{{ route('theme.show_login') }}" class="tn-btn tn-btn--ghost btn btn-hero-secondary">
                        <span>{{ get_phrase('تسجيل الدخول') }}</span>
                    </a>
                    <a href="{{ route('theme.show_register') }}" class="tn-btn tn-btn--primary btn btn-hero-primary">
                        <span>{{ get_phrase('أنشئ حسابك الآن') }}</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>
    <div class="progress-indicator">
        <div class="progress-bar" id="myBar"></div>
    </div>
</nav>
