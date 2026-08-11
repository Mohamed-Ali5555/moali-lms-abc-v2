@extends('theme::layouts.master')
@push('title', get_phrase('Cart'))
{{-- @push('meta')@endpush
@push('css')@endpush --}}
@section('content')
    <style>
        :root {

            --c-gradient: linear-gradient(90deg,
                    rgb(var(--c-accent-rgb)),
                    rgb(var(--c-text-rgb)),
                    rgb(var(--c-accent-rgb)));
            --c-gradient-hover: linear-gradient(90deg,
                    rgb(var(--c-accent-rgb)),
                    rgba(var(--c-text-rgb), 0.8),
                    rgb(var(--c-accent-rgb)));
            --c-success-gradient: linear-gradient(90deg, #10b981, #059669);
            --c-error-gradient: linear-gradient(90deg, #ef4444, #dc2626);
            --radius-xl: 28px;
            --radius-lg: 20px;
            --radius-md: 14px;
            --radius-sm: 8px;
            --shadow-deep: 0 25px 50px rgba(0, 0, 0, 0.15);
            --shadow-card: 0 20px 40px rgba(var(--c-accent-rgb), 0.08);
            --shadow-card-hover: 0 35px 70px rgba(var(--c-accent-rgb), 0.2);
            --shadow-light: 0 10px 30px rgba(0, 0, 0, 0.05);
            --transition-smooth: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        }
    </style>
    </head>
    @php
        $categories = App\Models\BootcampCategory::get();
        $active_category = request()->route()->parameter('category');
        $route_queries = request()->query();
        $route_queries = collect($route_queries)->except('page')->all();
    @endphp

    <body style="direction: rtl">
        <!-- Hero Header -->
        <header class="hero-header">
            <div class="hero-content">
                <h1 class="hero-title reveal">البوتكامبات المتخصصة</h1>
                <p class="hero-subtitle reveal">انطلق في رحلة التعلم مع دوراتنا التدريبية المتخصصة التي تم تصميمها لتحويل
                    أحلامك المهنية إلى واقع ملموس</p>

                <div class="stats-container">
                    <div class="stat-item reveal">
                        <span class="stat-number">{{ count($bootcamps) }}</span>
                        <span class="stat-label">بوتكامب متاح</span>
                    </div>
                    <div class="stat-item reveal">
                        <span class="stat-number">{{ $bootcamps->total() }}</span>
                        <span class="stat-label">إجمالي الدورات</span>
                    </div>
                    <div class="stat-item reveal">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">رضا العملاء</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Container -->
        <main class="main-container">
            <!-- Filters Section -->
            <div class="filters-section reveal">
                <form class="search-box" action="{{ route('theme.bootcamps', $active_category) }}" method="get">
                    <input type="text" name="search" placeholder="🔍 ابحث عن بوتكامب، مهارة، أو مدرب..."
                        value="{{ request('search') }}">
                    <a href="#" class="search-btn bootcamp-search">
                        <i class="fas fa-search"></i>
                    </a>
                </form>
            </div>

            <!-- Content Layout -->
            <div class="content-layout">
                <!-- Sidebar Filters -->
                <aside class="sidebar-filters reveal">
                    <div class="filter-group">
                        <h3 class="filter-title">
                            <i class="fas fa-layer-group"></i> فئات البوتكامبات
                        </h3>
                        <ul class="category-list">
                            @foreach ($categories as $category)
                                @php
                                    $route_queries['category'] = $category->slug;
                                    $isActive = $category->slug == $active_category;
                                @endphp
                                <li class="category-item">
                                    <a href="{{ route('theme.bootcamps', $route_queries) }}"
                                        class="category-link {{ $isActive ? 'active' : '' }}">
                                        <span>{{ $category->title }}</span>
                                        <span class="category-count">{{ count_bootcamps_by_category($category->id) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <!-- Bootcamps Grid -->
                <div class="bootcamps-grid">
                    @forelse ($bootcamps as $bootcamp)
                        @php
                            $review = App\Models\Review::where('course_id', $bootcamp->id)
                                ->orderBy('id', 'DESC')
                                ->get();

                            $total = $review->count();
                            $rating = array_sum(array_column($review->toArray(), 'rating'));
                            $average_rating = $total > 0 ? $rating / $total : 0;

                            $btn_url = route('theme.purchase.bootcamp', $bootcamp->id);
                            $btn_text = $bootcamp->is_paid ? 'اشترك الآن' : 'ابدأ مجانا';
                            $btn_class = 'card-button';
                            $badge_class = 'card-badge';

                            if ($bootcamp->discount_flag == 1) {
                                $badge_class .= ' hot';
                            } elseif (!$bootcamp->is_paid) {
                                $badge_class .= ' free';
                            }

                            if (auth()->check()) {
                                $my_bootcamp = App\Models\BootcampPurchase::where('user_id', auth()->user()->id)
                                    ->where('bootcamp_id', $bootcamp->id)
                                    ->where('status', 1)
                                    ->first();

                                if ($my_bootcamp) {
                                    $btn_text = 'اشتراكاتي';
                                    $btn_url = route('theme.my.bootcamp.details', $bootcamp->slug);
                                    $btn_class .= ' purchased';
                                }

                                $pending_payment = DB::table('offline_payments')
                                    ->where('user_id', auth()->user()->id)
                                    ->where('item_type', 'bootcamp')
                                    ->where('items', $bootcamp->id)
                                    ->where('status', 0)
                                    ->first();

                                if ($pending_payment) {
                                    $btn_text = 'قيد المعالجة';
                                    $btn_url = 'javascript:void(0);';
                                }
                            }

                            if (!$bootcamp->is_paid) {
                                $btn_class .= ' free';
                            }
                        @endphp

                        <article class="bootcamp-card reveal">
                            @if ($bootcamp->discount_flag == 1)
                                <div class="{{ $badge_class }}">خصم
                                    {{ round((($bootcamp->price - $bootcamp->discount_price) / $bootcamp->price) * 100) }}%
                                </div>
                            @elseif(!$bootcamp->is_paid)
                                <div class="{{ $badge_class }}">مجاني</div>
                            @endif

                            <div class="card-image-container">
                                <img src="{{ get_image($bootcamp->thumbnail ?? '') }}" alt="{{ $bootcamp->title }}"
                                    class="card-image"
                                    onerror="this.src='https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                            </div>

                            <div class="card-content">
                                <span class="card-category">
                                    {{ $bootcamp->category->title ?? 'عام' }}
                                </span>

                                <h3 class="card-title">
                                    <a href="{{ route('theme.bootcamp.details', $bootcamp->slug) }}">
                                        {{ $bootcamp->title }}
                                    </a>
                                </h3>

                                <p class="card-description">
                                    {{ Str::limit(strip_tags($bootcamp->description), 150) }}
                                </p>

                                <div class="card-meta">
                                    <div class="meta-item">
                                        <i class="far fa-calendar-alt"></i>
                                        <span>{{ date('d M Y', $bootcamp->publish_date) }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-graduation-cap"></i>
                                        <span>{{ count_bootcamp_classes($bootcamp->id) }} فصل</span>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="price-container">
                                        @if ($bootcamp->is_paid)
                                            @if ($bootcamp->discount_flag == 1)
                                                <span class="price old">{{ $bootcamp->price }} جنيه</span>
                                                <span class="price">{{ $bootcamp->discount_price }} جنيه</span>
                                            @else
                                                <span class="price">{{ $bootcamp->price }} جنيه</span>
                                            @endif
                                        @else
                                            <span class="price free">مجاناً</span>
                                        @endif
                                    </div>

                                    <button class="{{ $btn_class }}"
                                        onclick="window.location.href='{{ $btn_url }}'">
                                        <i class="fas fa-rocket"></i> {{ $btn_text }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-state reveal">
                            <div class="empty-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h2 class="empty-title">لا توجد بوتكامبات متاحة</h2>
                            <p class="empty-message">عذراً، لم يتم العثور على بوتكامبات تطابق معايير البحث الخاصة بك. حاول
                                البحث باستخدام مصطلحات أخرى أو تصفح جميع البوتكامبات المتاحة.</p>
                            <button class="card-button" onclick="window.location.href='{{ route('theme.bootcamps') }}'"
                                style="max-width: 300px; margin: 0 auto;">
                                <i class="fas fa-eye"></i> عرض جميع البوتكامبات
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if ($bootcamps->hasPages())
                <div class="pagination reveal">
                    @if ($bootcamps->onFirstPage())
                        <span class="page-link disabled">
                            <i class="fas fa-arrow-right"></i> السابق
                        </span>
                    @else
                        <a href="{{ $bootcamps->previousPageUrl() }}" class="page-link">
                            <i class="fas fa-arrow-right"></i> السابق
                        </a>
                    @endif

                    @foreach (range(1, $bootcamps->lastPage()) as $page)
                        @if ($page == $bootcamps->currentPage())
                            <span class="page-link active">{{ $page }}</span>
                        @else
                            <a href="{{ $bootcamps->url($page) }}" class="page-link">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($bootcamps->hasMorePages())
                        <a href="{{ $bootcamps->nextPageUrl() }}" class="page-link">
                            التالي <i class="fas fa-arrow-left"></i>
                        </a>
                    @else
                        <span class="page-link disabled">
                            التالي <i class="fas fa-arrow-left"></i>
                        </span>
                    @endif
                </div>
            @endif
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Scroll reveal animation
                const revealElements = document.querySelectorAll('.reveal');

                const revealOnScroll = () => {
                    const windowHeight = window.innerHeight;
                    const revealPoint = 150;

                    revealElements.forEach(element => {
                        const revealTop = element.getBoundingClientRect().top;

                        if (revealTop < windowHeight - revealPoint) {
                            element.classList.add('active');
                        }
                    });
                };

                // Initial check
                revealOnScroll();

                // Check on scroll
                window.addEventListener('scroll', revealOnScroll);

                // Enhanced card hover effects
                const cards = document.querySelectorAll('.bootcamp-card');

                cards.forEach(card => {
                    card.addEventListener('mouseenter', function(e) {
                        const rect = this.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;

                        this.style.setProperty('--mouse-x', `${x}px`);
                        this.style.setProperty('--mouse-y', `${y}px`);

                        this.style.transform = 'translateY(-20px) translateZ(50px) scale(1.03)';

                        // Animate badge
                        const badge = this.querySelector('.card-badge');
                        if (badge) {
                            badge.style.transform = 'translateZ(80px) scale(1.1)';
                        }
                    });

                    card.addEventListener('mousemove', function(e) {
                        const rect = this.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width - 0.5) * 10;
                        const y = ((e.clientY - rect.top) / rect.height - 0.5) * 10;

                        this.style.transform =
                            `translateY(-20px) translateZ(50px) scale(1.03) rotateY(${x}deg) rotateX(${-y}deg)`;
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform =
                            'translateY(0) translateZ(0) scale(1) rotateY(0deg) rotateX(0deg)';

                        // Reset badge
                        const badge = this.querySelector('.card-badge');
                        if (badge) {
                            badge.style.transform = 'translateZ(50px) scale(1)';
                        }
                    });
                });

                // Enhanced button hover effects
                const buttons = document.querySelectorAll('.card-button, .search-btn');

                buttons.forEach(btn => {
                    btn.addEventListener('mouseenter', function(e) {
                        const rect = this.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;

                        this.style.setProperty('--btn-x', `${x}px`);
                        this.style.setProperty('--btn-y', `${y}px`);

                        if (!this.classList.contains('disabled')) {
                            if (this.classList.contains('search-btn')) {
                                this.style.transform = 'translateY(-50%) scale(1.15) rotate(5deg)';
                            } else {
                                this.style.transform = 'translateY(-5px) scale(1.08)';
                            }
                        }
                    });

                    btn.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('disabled')) {
                            if (this.classList.contains('search-btn')) {
                                this.style.transform = 'translateY(-50%) scale(1) rotate(0deg)';
                            } else {
                                this.style.transform = 'translateY(0) scale(1)';
                            }
                        }
                    });
                });

                // 👇 submit الفورم عند الضغط على search icon
                document.querySelectorAll('.search-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        this.closest('form')?.submit();
                    });
                });

                // Category hover effects
                const categories = document.querySelectorAll('.category-link');

                categories.forEach(cat => {
                    cat.addEventListener('mouseenter', function(e) {
                        if (!this.classList.contains('active')) {
                            const rect = this.getBoundingClientRect();
                            const x = e.clientX - rect.left;

                            this.style.setProperty('--cat-x', `${x}px`);
                            this.style.transform = 'translateX(-15px) scale(1.05)';

                            // Animate count
                            const count = this.querySelector('.category-count');
                            if (count) {
                                count.style.transform = 'scale(1.1)';
                            }
                        }
                    });

                    cat.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('active')) {
                            this.style.transform = 'translateX(0) scale(1)';

                            // Reset count
                            const count = this.querySelector('.category-count');
                            if (count) {
                                count.style.transform = 'scale(1)';
                            }
                        }
                    });
                });

                // Page load animation
                setTimeout(() => {
                    document.body.style.opacity = '1';
                }, 100);
            });

            // Add 3D effect on window resize
            window.addEventListener('resize', () => {
                document.querySelectorAll('.bootcamp-card').forEach(card => {
                    card.style.transform = 'translateY(0) translateZ(0) scale(1)';
                });
            });
        </script>
    </body>
@endsection
