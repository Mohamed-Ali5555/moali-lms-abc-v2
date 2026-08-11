@php $current_route = Route::currentRouteName(); @endphp

<div class="sidebar-logo-area">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logos">
        <img class="sidebar-logo-lg" height="50" src="{{ get_image(get_theme_settings('logo') ?? '') }}"
            alt="{{ get_settings('system_title') }}">
        <img class="sidebar-logo-sm" height="40" src="{{ get_image(get_frontend_settings('favicon')) }}" alt="">
    </a>
    <button type="button" class="sidebar-cross menu-toggler d-flex d-lg-none" aria-label="{{ get_phrase('Close menu') }}">
        <span class="fi-rr-cross" aria-hidden="true"></span>
    </button>
</div>
<h3 class="sidebar-title fs-14px px-30px pb-20px mt-4">{{ get_phrase('القائمة الرئيسية') }}</h3>
<div class="sidebar-nav-area">
    <nav class="sidebar-nav">
        <ul class="px-14px pb-24px">

            @if (has_permission('admin.dashboard'))
                <li class="sidebar-first-li {{ $current_route == 'admin.dashboard' ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <span class="icon fi-rr-house-blank"></span>
                        <div class="text">
                            <span>{{ get_phrase('لوحة التحكم') }}</span>
                        </div>
                    </a>
                </li>
            @endif


            @if (has_permission('admin.categories'))
                <li class="sidebar-first-li {{ $current_route == 'admin.categories' ? 'active' : '' }}">
                    <a href="{{ route('admin.categories') }}">
                        <span class="icon fi-rr-apps"></span>
                        <div class="text">
                            <span>{{ get_phrase('التصنيفات') }}</span>
                        </div>
                    </a>
                </li>
            @endif

            @if (has_permission('admin.bookstore'))
                <li class="sidebar-first-li {{ $current_route == 'admin.bookstore' ? 'active' : '' }}">
                    <a href="{{ route('admin.bookstore') }}">
                        <span class="icon fi-rr-book"></span>
                        <div class="text">
                            <span>{{ get_phrase('الكتب') }}</span>
                        </div>
                    </a>
                </li>
            @endif


            @if (has_permission('admin.courses'))
                <li class="sidebar-first-li first-li-have-sub @if (
                    $current_route == 'admin.courses' ||
                        $current_route == 'admin.course.create' ||
                        $current_route == 'admin.course.show_users' ||
                        $current_route == 'admin.coupon.users_coupon' ||

                        $current_route == 'admin.course.edit' ||
                        $current_route == 'admin.coupons') active showMenu @endif">
                    <a href="javascript:void(0);">
                        <span class="icon fi fi-rr-e-learning"></span>
                        <div class="text">
                            <span>{{ get_phrase('الدورات') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('الدورات') }}</li>

                        @if (has_permission('admin.courses'))
                            <li class="sidebar-second-li @if ($current_route == 'admin.courses' ||   $current_route == 'admin.course.show_users' ||
                            $current_route == 'admin.course.edit') active @endif">
                                <a href="{{ route('admin.courses') }}">{{ get_phrase('إدارة الدورات') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.course.create'))
                            <li class="sidebar-second-li @if ($current_route == 'admin.course.create') active @endif">
                                <a href="{{ route('admin.course.create') }}">{{ get_phrase('إضافة دورة جديدة') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.coupons'))
                            <li class="sidebar-second-li @if ($current_route == 'admin.coupons' || $current_route == 'admin.coupon.users_coupon') active @endif">
                                <a href="{{ route('admin.coupons') }}">{{ get_phrase('أكواد الخصم') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif



            @if (has_permission('admin.bank.question.index'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.bank.quizs.index' ||$current_route == 'admin.bank.quizs.show' || $current_route == 'admin.category.bank.questions.index' || $current_route == 'admin.bank.question.index' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-clipboard-list-check"></span>
                        <div class="text">
                            <span>{{ get_phrase('بنك الأسئلة') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        @if (has_permission('admin.category.bank.questions.index'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.category.bank.questions.index' ? 'active' : '' }}">
                                <a href="{{ route('admin.category.bank.questions.index') }}">{{ get_phrase('التصنيفات') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.bank.quizs.index'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.bank.quizs.index' ? 'active' : '' }}">
                                <a href="{{ route('admin.bank.quizs.index') }}">{{ get_phrase('اختبارات البنك') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.bank.question.index'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.bank.question.index' ? 'active' : '' }}"><a
                                    href="{{ route('admin.bank.question.index') }}">{{ get_phrase('قائمة أسئلة البنك') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


        @if (has_permission('admin.wallet.index'))
            <li class="sidebar-first-li first-li-have-sub
                {{ $current_route == 'admin.wallet.index' || $current_route == 'admin.wallet_category.index' ? 'active' : ''}}">
                <a href="javascript:void(0);">
                    <span class="icon fi-rr-wallet"></span>
                        <div class="text">
                        <span>{{ get_phrase('المحفظة') }}</span>
                    </div>
                </a>
                <ul class="first-sub-menu">
                    <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('المحفظة') }}</li>

                    @if (has_permission('admin.wallet.index'))
                        <li class="sidebar-second-li @if ($current_route == 'admin.wallet.index' || $current_route == 'admin.course.edit') active @endif">
                            <a href="{{ route('admin.wallet.index') }}">{{ get_phrase('إدارة المحفظة') }}</a>
                        </li>
                    @endif

                    @if (has_permission('admin.wallet_category.index'))
                        <li class="sidebar-second-li @if ($current_route == 'admin.wallet_category.index') active @endif">
                            <a href="{{ route('admin.wallet_category.index') }}">{{ get_phrase('تصنيفات المحفظة') }}</a>
                        </li>
                    @endif

                </ul>
            </li>
        @endif


            @if (has_permission('admin.bootcamps'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.bootcamps' || $current_route == 'admin.bootcamp.create' || $current_route == 'admin.bootcamp.edit' || $current_route == 'admin.bootcamp.purchase.history' || $current_route == 'admin.bootcamp.purchase.invoice' || $current_route == 'admin.bootcamp.categories' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi fi-sr-users-alt"></span>
                        <div class="text">
                            <span>{{ get_phrase('Bootcamp') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">

                           <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('Bootcamp') }}</li>
                        @if (has_permission('admin.bootcamps'))

                           <li class="sidebar-second-li @if (($current_route == 'admin.bootcamps' || $current_route == 'admin.bootcamp.edit') && request('type') == '') active @endif"><a
                                  href="{{ route('admin.bootcamps') }}">{{ get_phrase('Manage Bootcamps') }}</a></li>
                        @endif

                        @if (has_permission('admin.bootcamp.create'))

                            <li class="sidebar-second-li @if ($current_route == 'admin.bootcamp.create') active @endif">
                                <a href="{{ route('admin.bootcamp.create') }}">{{ get_phrase('Add New Bootcamp') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.bootcamp.purchase.history'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.bootcamp.purchase.history' || $current_route == 'admin.bootcamp.purchase.invoice' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.bootcamp.purchase.history') }}">{{ get_phrase('Purchase History course') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.bootcamp.categories'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.bootcamp.categories' ? 'active' : '' }}">
                                <a href="{{ route('admin.bootcamp.categories') }}">{{ get_phrase('التصنيفات') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif
{{--
            @if (has_permission('admin.team.packages'))
                <li class="sidebar-first-li first-li-have-sub @if ($current_route == 'admin.team.packages' || $current_route == 'admin.team.packages.create' || $current_route == 'admin.team.packages.edit' || $current_route == 'admin.team.packages.purchase.history' || $current_route == 'admin.team.packages.purchase.invoice') active showMenu @endif">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-users-alt"></span>
                        <div class="text">
                            <span>{{ get_phrase('Team Training') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('Team Training') }}</li>
                        <li class="sidebar-second-li @if ($current_route == 'admin.team.packages' || $current_route == 'admin.team.packages.edit') active @endif">
                            <a href="{{ route('admin.team.packages') }}">{{ get_phrase('Manage Packages') }}</a>
                        </li>
                        <li class="sidebar-second-li @if ($current_route == 'admin.team.packages.create') active @endif">
                            <a href="{{ route('admin.team.packages.create') }}">{{ get_phrase('Add New Package') }}</a>
                        </li>
                        <li class="sidebar-second-li {{ $current_route == 'admin.team.packages.purchase.history' || $current_route == 'admin.team.packages.purchase.invoice' ? 'active' : '' }}">
                            <a href="{{ route('admin.team.packages.purchase.history') }}">{{ get_phrase('Purchase History') }}</a>
                        </li>
                    </ul>
                </li>
            @endif --}}

            {{-- @if (has_permission('admin.tutor_categories'))
                <li
                    class="sidebar-first-li first-li-have-sub @if ($current_route == 'admin.tutor_subjects' || $current_route == 'admin.tutor_categories') active showMenu @endif">
                    <a href="javascript:void(0);">
                        <span class="icon fi fi-rr-document-signed"></span>
                        <div class="text">
                            <span>{{ get_phrase('Tutor Booking') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('Tutor Booking') }}</li>
                        <li class="sidebar-second-li @if ($current_route == 'admin.tutor_subjects') active @endif">
                            <a href="{{ route('admin.tutor_subjects') }}">{{ get_phrase('Subjects') }}</a>
                        </li>
                        <li class="sidebar-second-li @if ($current_route == 'admin.tutor_categories') active @endif">
                            <a href="{{ route('admin.tutor_categories') }}">{{ get_phrase('Subject Category') }}</a>
                        </li>
                    </ul>
                </li>
            @endif --}}

            @if (has_permission('admin.enroll.history') || has_permission('admin.student.enroll'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.student.enroll' || $current_route == 'admin.enroll.history' ||  $current_route == 'admin.student.not_enroll'? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-user-add"></span>
                        <div class="text">
                            <span>{{ get_phrase('تسجيل الطلاب') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('تسجيل الدورات') }}</li>

                        @if (has_permission('admin.enroll.history'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.enroll.history' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.enroll.history') }}">{{ get_phrase('سجل التسجيل') }}</a>
                            </li>
                        @endif

                        @if (has_permission('admin.student.enroll'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.student.enroll' ? 'active' : '' }}">
                                <a href="{{ route('admin.student.enroll') }}">{{ get_phrase('تسجيل طالب') }}</a>
                            </li>
                        @endif

                        @if (has_permission('admin.student.not_enroll'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.student.not_enroll' ? 'active' : '' }}">
                                <a href="{{ route('admin.student.not_enroll') }}">{{ get_phrase('الطلاب غير المسجلين') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif


            @if (has_permission('admin.offline.payments') ||
                    has_permission('admin.revenue') ||
                    has_permission('admin.instructor.revenue') ||
                    has_permission('admin.purchase.history')  ||
                    has_permission('admin.purchase.history_book'))

                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.offline.payments' || $current_route == 'admin.revenue' || $current_route == 'admin.instructor.revenue' || $current_route == 'admin.purchase.history' || $current_route == 'admin.purchase.history_book'|| $current_route == 'admin.purchase.history.invoice' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-chart-histogram"></span>
                        <div class="text">
                            <span>{{ get_phrase('تقارير الدفع') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('تقارير الدفع') }}</li>

                        {{-- @if (has_permission('admin.offline.payments'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.offline.payments' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.offline.payments') }}">{{ get_phrase('Offline payments') }}</a>
                            </li>
                        @endif --}}

                        {{-- @if (has_permission('admin.revenue'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.revenue' ? 'active' : '' }}"><a
                                    href="{{ route('admin.revenue') }}">{{ get_phrase('Admin Revenue') }}</a></li>
                        @endif
                        @if (has_permission('admin.instructor.revenue'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.instructor.revenue' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.instructor.revenue') }}">{{ get_phrase('Instructor Revenue') }}</a>
                            </li>
                        @endif --}}
                        @if (has_permission('admin.purchase.history.course'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.purchase.history' || $current_route == 'admin.purchase.history.invoice' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.purchase.history') }}">{{ get_phrase('سجل مدفوعات الدورات') }}</a>
                            </li>
                        @endif


                        @if (has_permission('admin.purchase.history.book'))
                        <li
                            class="sidebar-second-li {{ $current_route == 'admin.purchase.history_book' || $current_route == 'admin.purchase.history.invoice' ? 'active' : '' }}">
                            <a
                                href="{{ route('admin.purchase.history_book') }}">{{ get_phrase('سجل مدفوعات الكتب') }}</a>
                        </li>
                    @endif






                    </ul>
                </li>
            @endif

            @if (has_permission('admin.admins.index') ||
                    has_permission('admin.instructor.index') ||
                    has_permission('admin.student.index'))
                <li class="sidebar-first-li first-li-have-sub
                 @if (
                    $current_route == 'admin.instructor.index' ||
                        $current_route == 'admin.instructor.create' ||
                        $current_route == 'admin.instructor.edit' ||
                        $current_route == 'admin.instructor.payout' ||
                        $current_route == 'admin.instructor.payout.filter' ||
                        $current_route == 'admin.instructor.setting' ||
                        $current_route == 'admin.instructor.application' ||
                        $current_route == 'admin.admins.index' ||
                        $current_route == 'admin.admins.create' ||
                        $current_route == 'admin.admins.edit' ||
                        $current_route == 'admin.admins.permission' ||
                        $current_route == 'admin.student.index' ||
                        $current_route == 'admin.student.edit' ||
                        $current_route == 'admin.student.view_course' ||

                        $current_route == 'admin.student.create') active @endif">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-users"></span>
                        <div class="text">
                            <span>{{ get_phrase('المستخدمون') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('المستخدمون') }}</li>
                        @if (has_permission('admin.admins.index'))
                            <li
                                class="sidebar-second-li second-li-have-sub @if (
                                    $current_route == 'admin.admins.index' ||
                                        $current_route == 'admin.admins.create' ||
                                        $current_route == 'admin.admins.edit' ||
                                        $current_route == 'admin.admins.permission') active @endif">
                                <a href="javascript:void(0);">{{ get_phrase('الموظفون') }}</a>
                                <ul class="second-sub-menu">
                                    <li class="sidebar-third-li @if (
                                        $current_route == 'admin.admins.index' ||
                                            $current_route == 'admin.admins.permission' ||
                                            $current_route == 'admin.admins.edit') active @endif">
                                        <a
                                            href="{{ route('admin.admins.index') }}">{{ get_phrase('إدارة الموظفين') }}</a>
                                    </li>
                                    <li class="sidebar-third-li @if ($current_route == 'admin.admins.create') active @endif">
                                        <a
                                            href="{{ route('admin.admins.create') }}">{{ get_phrase('إضافة موظف جديد') }}</a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        {{-- @if (has_permission('admin.instructor.index'))
                            <li
                                class="sidebar-second-li second-li-have-sub @if (
                                    $current_route == 'admin.instructor.index' ||
                                        $current_route == 'admin.instructor.create' ||
                                        $current_route == 'admin.instructor.edit' ||
                                        $current_route == 'admin.instructor.payout' ||
                                        $current_route == 'admin.instructor.payout.filter' ||
                                        $current_route == 'admin.instructor.setting' ||
                                        $current_route == 'admin.instructor.application') active @endif">
                                <a href="javascript:void(0);">{{ get_phrase('Instructor') }}</a>
                                <ul class="second-sub-menu">
                                    <li class="sidebar-third-li @if ($current_route == 'admin.instructor.index' || $current_route == 'admin.instructor.edit') active @endif">
                                        <a
                                            href="{{ route('admin.instructor.index') }}">{{ get_phrase('Manage Instructors') }}</a>
                                    </li>
                                    <li class="sidebar-third-li @if ($current_route == 'admin.instructor.create') active @endif">
                                        <a
                                            href="{{ route('admin.instructor.create') }}">{{ get_phrase('Add new Instructor') }}</a>
                                    </li>
                                    <li class="sidebar-third-li @if ($current_route == 'admin.instructor.payout' || $current_route == 'admin.instructor.payout.filter') active @endif">
                                        <a
                                            href="{{ route('admin.instructor.payout') }}">{{ get_phrase('Instructor Payout') }}</a>
                                    </li>
                                    <li class="sidebar-third-li @if ($current_route == 'admin.instructor.setting') active @endif">
                                        <a
                                            href="{{ route('admin.instructor.setting') }}">{{ get_phrase('Instructor Setting') }}</a>
                                    </li>
                                    <li class="sidebar-third-li @if ($current_route == 'admin.instructor.application') active @endif">
                                        <a
                                            href="{{ route('admin.instructor.application') }}">{{ get_phrase('Application') }}</a>
                                    </li>
                                </ul>
                            </li>
                        @endif --}}
                        @if (has_permission('admin.student.index'))
                            <li
                                class="sidebar-second-li second-li-have-sub @if (
                                    $current_route == 'admin.student.index' ||
                                    $current_route == 'admin.student.view_course' ||
                                    $current_route == 'admin.student.edit' ||
                                    $current_route == 'admin.student.create') active @endif">
                                <a href="javascript:void(0);">{{ get_phrase('الطلاب') }}</a>
                                <ul class="second-sub-menu">

                                @if (has_permission('admin.student.index'))
                                    <li class="sidebar-third-li @if ($current_route == 'admin.student.index' || $current_route == 'admin.student.edit' || $current_route == 'admin.student.view_course') active @endif">
                                        <a
                                            href="{{ route('admin.student.index') }}">{{ get_phrase('إدارة الطلاب') }}</a>
                                    </li>
                                @endif
                                  @if (has_permission('admin.student.create'))
                                        <li class="sidebar-third-li @if ($current_route == 'admin.student.create') active @endif">
                                            <a
                                                href="{{ route('admin.student.create') }}">{{ get_phrase('إضافة طالب جديد') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- @if (has_permission('admin.message'))
                <li class="sidebar-first-li {{ $current_route == 'admin.message' ? 'active' : '' }}">
                    <a href="{{ route('admin.message') }}">
                        <svg height="30px" width="30px" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M2 11.6C2 8.23969 2 6.55953 2.65396 5.27606C3.2292 4.14708 4.14708 3.2292 5.27606 2.65396C6.55953 2 8.23969 2 11.6 2H20.4C23.7603 2 25.4405 2 26.7239 2.65396C27.8529 3.2292 28.7708 4.14708 29.346 5.27606C30 6.55953 30 8.23969 30 11.6V20.4C30 23.7603 30 25.4405 29.346 26.7239C28.7708 27.8529 27.8529 28.7708 26.7239 29.346C25.4405 30 23.7603 30 20.4 30H11.6C8.23969 30 6.55953 30 5.27606 29.346C4.14708 28.7708 3.2292 27.8529 2.65396 26.7239C2 25.4405 2 23.7603 2 20.4V11.6Z" fill="url(#paint0_linear_87_7269)"></path> <path d="M16 23C20.9706 23 25 19.6421 25 15.5C25 11.3579 20.9706 8 16 8C11.0294 8 7 11.3579 7 15.5C7 18.1255 8.61889 20.4359 11.0702 21.7758C10.9881 22.4427 10.7415 23.3327 10 24C11.4021 23.7476 12.5211 23.2405 13.3571 22.6714C14.1928 22.885 15.0803 23 16 23Z" fill="white"></path> <defs> <linearGradient id="paint0_linear_87_7269" x1="16" y1="2" x2="16" y2="30" gradientUnits="userSpaceOnUse"> <stop stop-color="#5AF575"></stop> <stop offset="1" stop-color="#13BD2C"></stop> </linearGradient> </defs> </g></svg>
                        <div class="text">
                            <span>{{ get_phrase('Message') }}</span>
                        </div>
                        @if (
                            $unread_msg =
                                App\Models\Message::where('receiver_id', auth()->user()->id)->where('read', '')->count() > 0)
                            <span class="d-inline-block mt-2px badge bg-danger ms-auto">{{ $unread_msg }}</span>
                        @endif
                    </a>
                </li>
            @endif --}}
{{--
            @if (has_permission('admin.newsletter'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.newsletter' || $current_route == 'admin.subscribed_user' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-envelope"></span>
                        <div class="text">
                            <span>{{ get_phrase('Newsletter') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('Newsletter') }}</li>

                        <li class="sidebar-second-li {{ $current_route == 'admin.newsletter' ? 'active' : '' }}"><a
                                href="{{ route('admin.newsletter') }}">{{ get_phrase('Manage Newsletters') }}</a>
                        </li>
                        @if (has_permission('admin.subscribed_user'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.subscribed_user' ? 'active' : '' }}">
                                <a href="{{ route('admin.subscribed_user') }}">{{ get_phrase('Subscribed User') }}</a>
                            </li>
                        @endif

                    </ul>
                </li>
            @endif --}}


            @if (has_permission('admin.exams.list') || has_permission('admin.assignments.list'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.exams.list' || $current_route == 'admin.assignments.list' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-clipboard-list"></span>
                        <div class="text">
                            <span>{{ get_phrase('الاختبارات') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        @if (has_permission('admin.exams.list'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.exams.list' ? 'active' : '' }}"><a
                                    href="{{ route('admin.exams.list') }}">{{ get_phrase('الاختبارات القصيرة') }}</a>
                            </li>
                        @endif

                        @if (has_permission('admin.assignments.list'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.assignments.list' ? 'active' : '' }}"><a
                                    href="{{ route('admin.assignments.list') }}">{{ get_phrase('الواجبات') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif

            {{-- @if (has_permission('admin.contacts'))
                <li class="sidebar-first-li {{ $current_route == 'admin.contacts' ? 'active' : '' }}">
                    <a href="{{ route('admin.contacts') }}">
                        <svg height="30px" width="30px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#C75C5C;} .st1{opacity:0.2;} .st2{fill:#231F20;} .st3{fill:#77B3D4;} .st4{fill:#FFFFFF;} .st5{fill:#76C2AF;} .st6{fill:#E0995E;} .st7{fill:#4F5D73;} .st8{fill:#E0E0D1;} .st9{fill:#F5CF87;} </style> <g id="Layer_1"> <g> <circle class="st0" cx="32" cy="32" r="32"></circle> </g> <g> <g class="st1"> <path class="st2" d="M49,26c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,25.1,50.1,26,49,26L49,26z"></path> </g> <g class="st1"> <path class="st2" d="M49,35c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,34.1,50.1,35,49,35L49,35z"></path> </g> <g class="st1"> <path class="st2" d="M49,44c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,43.1,50.1,44,49,44L49,44z"></path> </g> <g> <path class="st3" d="M49,24c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,23.1,50.1,24,49,24L49,24z"></path> </g> <g> <path class="st4" d="M49,33c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,32.1,50.1,33,49,33L49,33z"></path> </g> <g> <path class="st5" d="M49,42c-1.1,0-2-0.9-2-2v-3c0-1.1,0.9-2,2-2l0,0c1.1,0,2,0.9,2,2v3C51,41.1,50.1,42,49,42L49,42z"></path> </g> <g class="st1"> <path class="st2" d="M49,50c0,2.2-1.8,4-4,4H21c-2.2,0-4-1.8-4-4V18c0-2.2,1.8-4,4-4h24c2.2,0,4,1.8,4,4V50z"></path> </g> <g> <path class="st6" d="M48,48c0,2.2-1.8,4-4,4H20c-2.2,0-4-1.8-4-4V16c0-2.2,1.8-4,4-4h24c2.2,0,4,1.8,4,4V48z"></path> </g> <g> <rect x="22" y="12" class="st7" width="3" height="40"></rect> </g> <g class="st1"> <g> <path class="st2" d="M20,22c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,20,20,20.9,20,22L20,22z"></path> </g> <g> <path class="st2" d="M20,30c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,28,20,28.9,20,30L20,30z"></path> </g> <g> <path class="st2" d="M20,38c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,36,20,36.9,20,38L20,38z"></path> </g> <g> <path class="st2" d="M20,46c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,44,20,44.9,20,46L20,46z"></path> </g> </g> <g> <g> <path class="st8" d="M20,20c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,18,20,18.9,20,20L20,20z"></path> </g> <g> <path class="st8" d="M20,28c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,26,20,26.9,20,28L20,28z"></path> </g> <g> <path class="st8" d="M20,36c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,34,20,34.9,20,36L20,36z"></path> </g> <g> <path class="st8" d="M20,44c0,1.1-0.9,2-2,2h-3c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h3C19.1,42,20,42.9,20,44L20,44z"></path> </g> </g> <g> <circle class="st9" cx="36" cy="27" r="4"></circle> </g> <g> <path class="st9" d="M42,39.3c0,3-2.7,2.7-6,2.7s-6,0.3-6-2.7c0-3,2.7-7.3,6-7.3S42,36.2,42,39.3z"></path> </g> </g> </g> <g id="Layer_2"> </g> </g></svg>
                        <div class="text">
                            <span>{{ get_phrase('Contacts') }}</span>
                        </div>
                    </a>
                </li>
            @endif --}}
{{--
            @if (has_permission('admin.blogs') ||
                    has_permission('admin.blog.pending') ||
                    has_permission('admin.blog.category') ||
                    has_permission('admin.blog.category'))
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.blogs' || $current_route == 'admin.blog.create' || $current_route == 'admin.blog.edit' || $current_route == 'admin.blog.pending' || $current_route == 'admin.blog.category' || $current_route == 'admin.blog.settings' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi-rr-blog-text"></span>
                        <div class="text">
                            <span>{{ get_phrase('Blogs') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('Blogs') }}</li>
                        @if (has_permission('admin.blogs'))
                            <li class="sidebar-second-li {{ $current_route == 'admin.blogs' ? 'active' : '' }}"><a
                                    href="{{ route('admin.blogs') }}">{{ get_phrase('Manage Blogs') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.blog.pending'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.blog.pending' ? 'active' : '' }}">
                                <a href="{{ route('admin.blog.pending') }}">{{ get_phrase('Pending Blogs') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.blog.category'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.blog.category' ? 'active' : '' }}">
                                <a href="{{ route('admin.blog.category') }}">{{ get_phrase('التصنيفات') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.blog.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.blog.settings' ? 'active' : '' }}">
                                <a href="{{ route('admin.blog.settings') }}">{{ get_phrase('Settings') }}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endif --}}
        </ul>
    </nav>



    @if (has_permission('admin.system.settings') ||
            has_permission('admin.website.settings') ||
            has_permission('admin.payment.settings') ||
            has_permission('admin.manage.language') ||
            has_permission('admin.notification.settings') ||
            has_permission('admin.live.class.settings') ||
            has_permission('admin.certificate.settings') ||
            has_permission('admin.player.settings') ||
            has_permission('admin.open.ai.settings') ||
            has_permission('admin.seo.settings') ||
            has_permission('admin.wapilot.settings'))

        <nav class="sidebar-nav">
            <h3 class="sidebar-title fs-12px px-30px pb-3">{{ get_phrase('الإعدادات') }}</h3>
            <ul class="px-14px pb-24px mb-5 pb-5">
                <li
                    class="sidebar-first-li first-li-have-sub {{ $current_route == 'admin.system.settings' || $current_route == 'admin.website.settings' || $current_route == 'admin.language.phrase.edit' || $current_route == 'admin.payment.settings' || $current_route == 'admin.manage.language' || $current_route == 'admin.notification.settings' || $current_route == 'admin.live.class.settings' || $current_route == 'admin.live.class.settings' || $current_route == 'admin.certificate.settings' || $current_route == 'admin.player.settings' || $current_route == 'admin.open.ai.settings' || $current_route == 'admin.seo.settings' || $current_route == 'admin.wapilot.settings' || $current_route == 'admin.theme.social'  || $current_route == 'admin.theme.feature' || $current_route == 'admin.theme.settings' || $current_route == 'admin.theme.legal' ? 'active' : '' }}">
                    <a href="javascript:void(0);">
                        <span class="icon fi fi-rr-settings"></span>
                        <div class="text">
                            <span>{{ get_phrase('إعدادات النظام') }}</span>
                        </div>
                    </a>
                    <ul class="first-sub-menu">
                        <li class="first-sub-menu-title fs-14px mb-18px">{{ get_phrase('إعدادات النظام') }}</li>
                        @if (has_permission('admin.system.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.system.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.system.settings') }}">{{ get_phrase('إعدادات النظام') }}</a>
                            </li>
                        @endif


                        {{-- /////sidebar new theme///// --}}
                        @include('theme::layouts.sidebar_theme')

                        {{-- /////sidebar new theme///// --}}


                        @if (has_permission('admin.website.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.website.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.website.settings') }}">{{ get_phrase('إعدادات الموقع') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.payment.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.payment.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.payment.settings') }}">{{ get_phrase('إعدادات الدفع') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.manage.language'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.manage.language' || $current_route == 'admin.language.phrase.edit' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.manage.language') }}">{{ get_phrase('إدارة اللغات') }}</a>
                            </li>
                        @endif
                        {{-- @if (has_permission('admin.live.class.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.live.class.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.live.class.settings') }}">{{ get_phrase('Live Class Settings') }}</a>
                            </li>
                        @endif --}}
                        @if (has_permission('admin.settings.smtp'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.notification.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.notification.settings') }}">{{ get_phrase('إعدادات البريد الإلكتروني') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.wapilot.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.wapilot.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.wapilot.settings') }}">{{ get_phrase('تكامل WaPilot') }}</a>
                            </li>
                        @endif
                        {{-- @if (has_permission('admin.certificate.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.certificate.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.certificate.settings') }}">{{ get_phrase('Certificate Settings') }}</a>
                            </li>
                        @endif --}}
                        @if (has_permission('admin.player.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.player.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.player.settings') }}">{{ get_phrase('إعدادات المشغّل') }}</a>
                            </li>
                        @endif
                        @if (has_permission('admin.open.ai.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.open.ai.settings' ? 'active' : '' }}">
                                <a
                                    href="{{ route('admin.open.ai.settings') }}">{{ get_phrase('إعدادات الذكاء الاصطناعي') }}</a>
                            </li>
                        @endif

                        @if (has_permission('admin.seo.settings'))
                            <li
                                class="sidebar-second-li {{ $current_route == 'admin.seo.settings' ? 'active' : '' }}">
                                <a href="{{ route('admin.seo.settings') }}">{{ get_phrase('إعدادات تحسين محركات البحث') }}</a>
                            </li>
                        @endif

                    </ul>
                </li>

                @if (has_permission('admin.manage.profile'))
                    <li class="sidebar-first-li {{ $current_route == 'admin.manage.profile' ? 'active' : '' }}">
                        <a href="{{ route('admin.manage.profile') }}">
                            <span class="icon fi-rr-circle-user"></span>
                        <div class="text">
                                <span>{{ get_phrase('إدارة الملف الشخصي') }}</span>
                            </div>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
</div>

<script>
    "use strict";
    document.addEventListener("DOMContentLoaded", function() {
        // Restore scroll position if it exists in localStorage
        const scrollPos = localStorage.getItem('navScrollPos');
        const sidebarNavArea = document.querySelector('.sidebar-nav-area');
        if (scrollPos) {
            sidebarNavArea.scrollTop = scrollPos;
        }

        // Ensure the active element is visible
        const activeElement = document.querySelector('.sidebar-nav-area .active');
        if (activeElement) {
            const activeElementTop = activeElement.getBoundingClientRect().top;
            const navAreaTop = sidebarNavArea.getBoundingClientRect().top;
            const navAreaBottom = navAreaTop + sidebarNavArea.clientHeight;

            if (activeElementTop < navAreaTop || activeElementTop > navAreaBottom) {
                sidebarNavArea.scrollTop = activeElement.offsetTop - sidebarNavArea.offsetTop;
            }
        }

        // Save scroll position before page unload
        window.addEventListener('beforeunload', function() {
            localStorage.setItem('navScrollPos', sidebarNavArea.scrollTop);
        });

        // Close mobile sidebar after navigating to a page link
        document.querySelectorAll(
            '.sidebar-first-li:not(.first-li-have-sub) > a[href]:not([href="javascript:void(0);"]), ' +
            '.sidebar-second-li > a[href]:not([href="javascript:void(0);"]), ' +
            '.sidebar-third-li > a[href]:not([href="javascript:void(0);"])'
        ).forEach(function(link) {
            link.addEventListener('click', function() {
                if (!window.matchMedia('(max-width: 991.98px)').matches) {
                    return;
                }
                const sidebar = document.querySelector('.ol-sidebar');
                const backdrop = document.querySelector('.ol-sidebar-backdrop');
                sidebar?.classList.remove('hide');
                document.body.classList.remove('sidebar-open');
                backdrop?.classList.remove('active');
                document.querySelectorAll('.menu-toggler').forEach(function(btn) {
                    btn.classList.remove('active');
                });
            });
        });
    });
</script>
