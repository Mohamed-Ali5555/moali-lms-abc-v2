@php
    $current_route = Route::currentRouteName();
    $user = auth()->user();
    $email = $user->email ?? '';
    $isStudent = !in_array($user->role, ['admin', 'instructor'], true);

    $isCourses = $current_route === 'theme.my.courses';
    $isBooks = $current_route === 'theme.my.books';
    $isPerformance = $current_route === 'theme.my.performance';
    $isProfile = $current_route === 'theme.my.profile';
    $isWallet = in_array($current_route, ['theme.my.wallet', 'theme.wallet.charging'], true);
    $isInvoices = in_array($current_route, ['theme.purchase.history', 'theme.invoice'], true);
@endphp

<link rel="stylesheet" href="{{ asset('modules/theme/css/student-sidebar-modern.css') }}">

<div class="col-lg-3 ss-wrap">
    <div class="gradient-border">
        <aside class="ss-card course-sideBar" aria-label="{{ get_phrase('قائمة حساب الطالب') }}">
            <div class="ss-card__hero ss-desktop-only">
                <p class="ss-card__eyebrow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ get_phrase('حسابي') }}
                </p>
            </div>

            <div class="ss-profile profile-info">
                <div class="ss-avatar">
                    <div class="ss-avatar__ring">
                        <img class="photo" src="{{ get_image($user->photo) }}" alt="{{ $user->name }}">
                    </div>
                    <a href="#"
                        class="ss-avatar__upload"
                        onclick="ajaxModal('{{ route('modal', ['frontend.default.upload_profile_pic', 'id' => $user->id]) }}', '{{ get_phrase('Upload picture') }}'); return false;">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            <br>
                            {{ get_phrase('تغيير') }}
                        </span>
                    </a>
                    <span class="ss-avatar__badge" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </span>
                </div>

                <div class="ss-profile__info">
                    <h5 class="ss-profile__name name">{{ $user->name }}</h5>
                    <p class="ss-profile__email email ss-desktop-only" title="{{ $email }}">
                        {{ strlen($email) > 28 ? substr($email, 0, 28) . '…' : $email }}
                    </p>
                    <span class="ss-profile__pill">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        {{ $isStudent ? get_phrase('طالب') : get_phrase(ucfirst($user->role)) }}
                    </span>
                </div>
            </div>

            <div class="ss-tabs-shell" data-ss-nav-wrap>
                <button type="button"
                    class="ss-nav__arrow ss-nav__arrow--prev"
                    data-ss-nav-prev
                    aria-label="{{ get_phrase('السابق') }}"
                    hidden>
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                </button>

                <ul class="ss-nav couses-tab-list" id="ssStudentTabs" data-ss-nav>
                    <li class="ss-nav__label ss-desktop-only">{{ get_phrase('القائمة') }}</li>

                    <li class="ss-nav__item {{ $isCourses ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.my.courses') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('كورساتي') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__item {{ $isBooks ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.my.books') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('كتبي') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__item {{ $isPerformance ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.my.performance') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('أدائي') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__item {{ $isProfile ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.my.profile') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('حسابي') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__item {{ $isWallet ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.my.wallet') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('محفظتي') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__item {{ $isInvoices ? 'is-active active' : '' }}">
                        <a href="{{ route('theme.purchase.history') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('فواتيري') }}</span>
                            <span class="ss-nav__chev ss-desktop-only" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        </a>
                    </li>

                    <li class="ss-nav__divider ss-desktop-only" aria-hidden="true"></li>

                    <li class="ss-nav__item is-logout">
                        <a href="{{ route('logout') }}">
                            <span class="ss-nav__icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            </span>
                            <span class="ss-nav__text">{{ get_phrase('تسجيل خروج') }}</span>
                        </a>
                    </li>
                </ul>

                <button type="button"
                    class="ss-nav__arrow ss-nav__arrow--next"
                    data-ss-nav-next
                    aria-label="{{ get_phrase('التالي') }}"
                    hidden>
                    <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                </button>
            </div>
        </aside>
    </div>
</div>

<script>
    (function () {
        var wrap = document.querySelector('[data-ss-nav-wrap]');
        var nav = document.querySelector('[data-ss-nav]');
        var prevBtn = document.querySelector('[data-ss-nav-prev]');
        var nextBtn = document.querySelector('[data-ss-nav-next]');
        var mq = window.matchMedia('(max-width: 991.98px)');

        function updateNavArrows() {
            if (!wrap || !nav || !prevBtn || !nextBtn) return;

            if (!mq.matches) {
                prevBtn.hidden = true;
                nextBtn.hidden = true;
                wrap.classList.remove('is-scrollable', 'has-prev', 'has-next');
                return;
            }

            var maxScroll = nav.scrollWidth - nav.clientWidth;
            var canScroll = maxScroll > 4;

            wrap.classList.toggle('is-scrollable', canScroll);

            if (!canScroll) {
                prevBtn.hidden = true;
                nextBtn.hidden = true;
                wrap.classList.remove('has-prev', 'has-next');
                return;
            }

            var scrollStart = Math.abs(nav.scrollLeft);
            var atStart = scrollStart <= 4;
            var atEnd = scrollStart >= maxScroll - 4;

            prevBtn.hidden = false;
            nextBtn.hidden = false;
            prevBtn.disabled = atStart;
            nextBtn.disabled = atEnd;
            wrap.classList.toggle('has-prev', !atStart);
            wrap.classList.toggle('has-next', !atEnd);
        }

        function scrollNav(direction) {
            if (!nav) return;
            var amount = Math.max(160, Math.round(nav.clientWidth * 0.7));
            var rtl = getComputedStyle(nav).direction === 'rtl';
            var delta = direction * amount * (rtl ? -1 : 1);
            nav.scrollBy({ left: delta, behavior: 'smooth' });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                scrollNav(-1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                scrollNav(1);
            });
        }

        if (nav) {
            nav.addEventListener('scroll', updateNavArrows, { passive: true });
            window.addEventListener('resize', updateNavArrows);
            if (mq.addEventListener) {
                mq.addEventListener('change', updateNavArrows);
            } else if (mq.addListener) {
                mq.addListener(updateNavArrows);
            }
        }

        var active = document.querySelector('#ssStudentTabs .ss-nav__item.is-active, #ssStudentTabs .ss-nav__item.active');
        if (active && mq.matches) {
            requestAnimationFrame(function () {
                active.scrollIntoView({ inline: 'center', block: 'nearest', behavior: 'smooth' });
                setTimeout(updateNavArrows, 320);
            });
        }

        updateNavArrows();
        setTimeout(updateNavArrows, 100);
        setTimeout(updateNavArrows, 400);
    })();
</script>
