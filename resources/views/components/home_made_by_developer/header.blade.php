{{-- To make a editable image or text need to be add a "builder editable" class and builder identity attribute with a unique value --}}
{{-- builder identity and builder editable --}}
{{-- builder identity value have to be unique under a single file --}}

@php
    $parent_categories = DB::table('categories')->where('parent_id', 0)->latest('id')->get();
    $current_route = Route::currentRouteName();
@endphp

<!-----------  Header Area Start  ------------->
<header class="header-area">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <!-- Left Side (Logo & Theme Toggle) -->
            <div class="col-auto d-flex gap-2 gap-md-3 align-items-center">
                <div class="logo-image">
                    <a href="{{ route('home') }}">
                        <img src="{{ get_image(get_frontend_settings('dark_logo')) }}" alt="system logo"
                            class="object-fit-cover rounded header-dark-logo">
                        <img src="{{ get_image(get_frontend_settings('light_logo')) }}" alt="system logo"
                            class="object-fit-cover rounded header-light-logo d-none">
                    </a>
                </div>
                <div class="theme">
                    <input type="checkbox" id="toggle_mode" class="toggle--checkbox">
                    <label for="toggle_mode" class="toggle--label">
                        <span class="toggle--label-background"></span>
                    </label>
                </div>
            </div>

            <!-- Middle Navigation Links (Desktop Only) -->
            <div class="col-auto d-none d-lg-block">
                <div class="header-menu">
                    <div class="nav-menu">
                        <ul class="primary-menu main-menu-ul d-flex align-items-center mb-0 gap-4">
                            <li><a href="{{ route('home') }}" class="@if ($current_route == 'home') active @endif">{{ get_phrase('Home') }}</a></li>
                            <li class="have-mega-menu position-relative">
                                <a class="menu-parent-a @if ($current_route == 'courses') active @endif" href="{{ route('courses') }}">{{ get_phrase('Courses') }}</a>
                                <ul class="mega-dropdown-menu mega main-mega-menu">
                                    <div class="mega-menu-items">
                                        <ul class="mega_list">
                                            @foreach ($parent_categories->take(10) as $parent_category)
                                                <li>
                                                    <a href="{{ route('courses', $parent_category->slug) }}">
                                                        <span class="me-3"><i class="{{ $parent_category->icon }}"></i></span>
                                                        <span class="me-auto">{{ ucfirst($parent_category->title) }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                            <li>
                                                <a href="{{ route('courses') }}">
                                                    <span class="me-3"><i class="fas fa-list-ul"></i></span>
                                                    <span class="me-auto">{{ get_phrase('All Courseses') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </ul>
                            </li>
                            <li><a href="{{ route('bootcamps') }}" class="@if ($current_route == 'bootcamps' || $current_route == 'bootcamp.details') active @endif">{{ get_phrase('Bootcamp') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side (Cart, User Profile / Auth Buttons, Hamburger Toggle) -->
            <div class="col-auto">
                <div class="primary-end d-flex align-items-center gap-2">
                    @isset(auth()->user()->id)
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('cart') }}" class="position-relative" data-bs-toggle="tooltip"
                                data-bs-title="{{ get_phrase('Cart') }}" data-bs-placement="bottom">

                                @php
                                    $numberof_wishlist_item = App\Models\CartItem::where(
                                        'user_id',
                                        auth()->user()->id,
                                    )->count();
                                @endphp
                                @if ($numberof_wishlist_item > 0)
                                    <span class="cart-top-number">
                                        {{ $numberof_wishlist_item }}
                                    </span>
                                @endif

                                <dotlottie-player id="cart_icon_player"
                                    src="{{ asset('assets/frontend/default/image/icons/cart.lottie') }}"
                                    background="transparent" speed="1" style="width: 50px; height: 50px;"
                                    part="lottie-svg" loop autoplay></dotlottie-player>
                            </a>
                        </div>
                    @endisset

                    @if (isset(auth()->user()->id))
                        <div class="Userprofile me-0 me-md-2 ms-2 ms-md-3 d-none d-lg-inline-block">
                            <button class="us-btn dropdown-toggle py-1" type="button" data-bs-toggle="dropdown"
                                aria-expanded="true">
                                <img class="image-40" src="{{ get_image(Auth()->user()->photo) }}" alt="user-img">
                            </button>
                            <ul class="dropdown-menu dropmenu-end userDropDown" data-popper-placement="bottom-start">
                                <li class="figure_user d-flex">
                                    <img src="{{ get_image(Auth()->user()->photo) }}" alt="user-img">
                                    <div class="figure_text">
                                        <h4>{{ ucfirst(Auth()->user()->name) }}</h4>
                                        <p>{{ ucfirst(Auth()->user()->role) }}</p>
                                    </div>
                                </li>

                                @if (in_array(auth()->user()->role, ['admin', 'instructor']))
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route(auth()->user()->role . '.dashboard') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/dashboard.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>

                                            {{ get_phrase('Dashboard') }}
                                        </a>
                                    </li>
                                @endif

                                @if (Auth()->user()->role != 'admin' && Auth()->user()->role != 'instructor')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('my.courses') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/courses.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>
                                            {{ get_phrase('My Courses') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('my.profile') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/user.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>
                                            {{ get_phrase('My Profile') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('my.wallet') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/wallet.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>

                                            {{ get_phrase('My Wallet') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('my.bootcamps') }}" id="bootcamp-icon">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/history.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>
                                            {{ get_phrase('My Bootcamps') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('message') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/message.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>
                                            {{ get_phrase('Message') }}
                                        </a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('purchase.history') }}">
                                            <dotlottie-player
                                                src="{{ asset('assets/frontend/default/image/icons/history.json') }}"
                                                background="transparent" speed="1"
                                                style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                                hover></dotlottie-player>
                                            {{ get_phrase('Purchase History') }}
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <a class="dropdown-item mb-0" href="{{ route('logout') }}">
                                        <dotlottie-player
                                            src="{{ asset('assets/frontend/default/image/icons/logout.json') }}"
                                            background="transparent" speed="1"
                                            style="width: 30px; height: 30px;" part="lottie-svg" loop autoplay
                                            hover></dotlottie-player>
                                        {{ get_phrase('Log Out') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <div class="d-none d-lg-flex gap-2 align-items-center">
                            <a href="{{ route('theme.show_login') }}"
                                class="eBtn btn gradient mb-1">{{ get_phrase('Login') }}</a>
                            <a href="{{ route('theme.show_register') }}"
                                class="eBtn btn gradient mb-1">{{ get_phrase('Sign Up') }}</a>
                        </div>
                    @endif

                    <span class="toggle-bar text-dark ms-0 d-lg-none"
                        data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions"
                        aria-controls="offcanvasWithBothOptions" role="button">
                        <i class="fa-sharp fa-solid fa-bars"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Off Canvas Menu For Mobile Device -->
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header pb-0">
                <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel"></h5>
            </div>
            <div class="offcanvas-body px-4 pt-2">
                <div class="off-menu">
                    <div class="logo-image d-flex align-items-center justify-content-between mb-4">
                        <a href="{{ route('home') }}">
                            <img src="{{ get_image(get_frontend_settings('dark_logo')) }}" alt="system logo">
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="mt-3 flex-shrink-0">
                        <ul class="list-unstyled ps-0">
                            @auth
                                <li>
                                    <div class="d-flex align-items-center my-4">
                                        <img src="{{ get_image(Auth()->user()->photo) }}" alt="user-img"
                                            class="image-45">
                                        <div class="ms-3 pt-2">
                                            <h3>{{ Auth()->user()->name }}</h3>
                                            <small class="text-muted">{{ Auth()->user()->email }}</small>
                                        </div>
                                        <a data-bs-toggle="tooltip" title="{{ get_phrase('Logout') }}"
                                            class="btn btn-light text-14px me-3 ms-auto" href="{{ route('logout') }}"> <i
                                                class="fi-rr-arrow-right-to-bracket"></i> </a>
                                    </div>
                                </li>
                                @if (auth()->user() && auth()->user()->role == 'student')
                                    @php
                                        $numberof_wishlist_item = App\Models\Wishlist::where(
                                            'user_id',
                                            auth()->user()->id,
                                        )->count();
                                    @endphp
                                    <li><a href="{{ route('my.courses') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('My Courses') }}</a></li>
                                    <li><a href="{{ route('wishlist') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('Wishlist') }} <span
                                                class="badge bg-pink ms-auto">{{ $numberof_wishlist_item }}</span></a>
                                    </li>
                                    <li><a href="{{ route('my.profile') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('My profile') }}</a></li>
                                    <li><a href="{{ route('message') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('Message') }}</a></li>
                                    <li><a href="{{ route('purchase.history') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('Purchase History') }}</a></li>
                                @elseif (auth()->user() && auth()->user()->role == 'admin')
                                    <li><a href="{{ route('admin.dashboard') }}"
                                            class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                            {{ get_phrase('Admin Dashboard') }}</a></li>
                                @endif
                                <li>
                                    <hr>
                                </li>
                            @endauth

                            <li><a href="{{ route('home') }}"
                                    class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">
                                    {{ get_phrase('Home') }}</a></li>
                            <li>
                                <button
                                    class="btn btn-toggle d-inline-flex align-items-center text-16px fw-500 w-100 collapsed rounded border-0 py-3"
                                    data-bs-toggle="collapse" data-bs-target="#category-collapse"
                                    aria-expanded="false">
                                    {{ get_phrase('Courses') }}
                                    <span class="icons float-end ms-auto"><i
                                            class="fa-solid fa-angle-down"></i></span>
                                </button>
                                <div class="collapse" id="category-collapse">
                                    <ul class="btn-toggle-nav list-unstyled fw-normal small bg-white pb-1 pb-3 pt-0">
                                        @php
                                            $parent_categories = DB::table('categories')
                                                ->where('parent_id', 0)
                                                ->latest('id')
                                                ->get();
                                        @endphp
                                        @foreach ($parent_categories->take(30) as $parent_category)
                                            <li>
                                                <a class="w-100 px-3 py-2"
                                                    href="{{ route('courses', $parent_category->slug) }}">{{ $parent_category->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                            <li><a href="{{ route('bootcamps') }}"
                                    class="btn btn-toggle-list d-inline-flex align-items-center text-16px fw-500 w-100 rounded border-0 py-3">{{ get_phrase('Bootcamp') }}</a>
                            </li>
                        </ul>
                    </div>
                    @guest
                        <div class="btn-off mt-4">
                            <a href="{{ route('theme.show_login') }}" class="eBtn btn gradient mb-3 w-100 text-center">{{ get_phrase('Login') }}</a>
                            <a href="{{ route('theme.show_register') }}"
                                class="eBtn btn gradient sign w-100 text-center">{{ get_phrase('Sign Up') }}</a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</header>
<!-----------  Header Area End   ------------->

@push('js')
    <script>
        "use strict";
    </script>
@endpush
