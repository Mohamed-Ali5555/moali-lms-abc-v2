@extends('theme::layouts.master')

@push('title', get_phrase('Bootcamp Details'))
@push('meta')@endpush
@push('css')@endpush


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
@section('content')
    <!-- Hero Header -->
    <header class="bootcamp-hero-header">
        <div class="bootcamp-hero-content">
            <!-- Breadcrumb -->


            <!-- Course Title & Description -->
            <h1 class="bootcamp-hero-title">{{ $bootcamp_details->title }}</h1>
            <p class="bootcamp-hero-subtitle">{{ $bootcamp_details->short_description }}</p>

            <!-- Stats Grid -->
            <div class="bootcamp-stats-container">
                <div class="bootcamp-stat-card">
                    <i class="fas fa-calendar-alt stat-icon"></i>
                    <span class="bootcamp-stat-label">{{ get_phrase('تاريخ النشر') }}</span>
                    <span class="bootcamp-stat-value">{{ date('d M, Y', $bootcamp_details->publish_date) }}</span>
                </div>

                <div class="bootcamp-stat-card">
                    <i class="fas fa-layer-group stat-icon"></i>
                    <span class="bootcamp-stat-label">{{ get_phrase('الوحدات') }}</span>
                    <span class="bootcamp-stat-value">{{ count_bootcamp_modules($bootcamp_details->id) }}</span>
                </div>

                <div class="bootcamp-stat-card">
                    <i class="fas fa-video stat-icon"></i>
                    <span class="bootcamp-stat-label">{{ get_phrase('الحصص') }}</span>
                    <span class="bootcamp-stat-value">{{ count_bootcamp_classes($bootcamp_details->id) }}</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="bootcamp-main-container">
        <div class="bootcamp-content-wrapper">
            <!-- Left Column - Content -->
            <div class="bootcamp-content-column">
                <!-- Featured Image -->
                <div class="bootcamp-featured-image">
                    <img src="{{ get_image($bootcamp_details->thumbnail) }}" alt="{{ $bootcamp_details->title }}">
                </div>

                <!-- Course Content Section -->
                <div class="bootcamp-content-section">
                    <h4 class="bootcamp-g-title">{{ get_phrase('محتوى البوتكامب') }}</h4>

                    @if ($modules->count() > 0)
                        <div class="bootcamp-accordion" id="bootcamp-classes">
                            @foreach ($modules as $module)
                                @php
                                    $classCount = DB::table('bootcamp_live_classes')
                                        ->where('module_id', $module->id)
                                        ->count();
                                @endphp
                                <div class="bootcamp-accordion-item">
                                    <h2 class="bootcamp-accordion-header">
                                        <button class="bootcamp-accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse_{{ $module->id }}"
                                            aria-expanded="false" aria-controls="collapse_{{ $module->id }}">
                                            <i class="fas fa-folder me-3"></i>
                                            {{ ucfirst($module->title) }}
                                            @if ($classCount > 0)
                                                <span class="bootcamp-module-badge">{{ $classCount }}
                                                    {{ $classCount == 1 ? 'حصّة' : 'حصص' }}</span>
                                            @endif
                                        </button>
                                    </h2>
                                    <div id="collapse_{{ $module->id }}" class="bootcamp-accordion-collapse collapse"
                                        data-bs-parent="#bootcamp-classes">
                                        <div class="bootcamp-accordion-body">
                                            @if ($classCount > 0)
                                                <ul class="bootcamp-lesson-list">
                                                    @foreach (DB::table('bootcamp_live_classes')->where('module_id', $module->id)->get() as $class)
                                                        <li>
                                                            <a href="{{ $bootcamp_details->is_paid ? 'javascript:void(0);' : route('course.player', $bootcamp_details->slug) }}"
                                                                class="bootcamp-d-flex justify-content-between align-items-center">
                                                                <div class="bootcamp-d-flex align-items-center">
                                                                    <i class="fas fa-play-circle"></i>
                                                                    <span>{{ ucfirst($class->title) }}</span>
                                                                </div>
                                                                {{-- @if ($class->duration)
                                                                    <small class="bootcamp-text-muted">{{ $class->duration }}</small>
                                                                @endif --}}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="bootcamp-empty-content">
                                                    <i class="fas fa-folder-open"></i>
                                                    <h5>{{ get_phrase('لا توجد حصص') }}</h5>
                                                    <p>{{ get_phrase('سيتم إضافة المحتوى قريباً') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bootcamp-empty-content">
                            <i class="fas fa-book-open"></i>
                            <h5>{{ get_phrase('لا يوجد محتوى') }}</h5>
                            <p>{{ get_phrase('سيتم إضافة المحتوى قريباً') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Pricing Card -->
            <div class="bootcamp-sidebar-column">
                <div class="bootcamp-pricing-card">
                    <div class="bootcamp-pricing-header">
                        <h4 class="bootcamp-mb-3" style="color: rgba(var(--c-text-rgb)); font-weight: 800;">
                            {{ get_phrase('اشترك الآن') }}</h4>

                        <div class="bootcamp-price-display">
                            @if ($bootcamp_details->is_paid == 0)
                                <span class="bootcamp-current-price">{{ get_phrase('مجاني') }}</span>
                            @elseif ($bootcamp_details->discount_flag == 1)
                                <span
                                    class="bootcamp-current-price">{{ currency($bootcamp_details->price - $bootcamp_details->discount_price, 2) }}</span>
                                <span class="bootcamp-original-price">{{ currency($bootcamp_details->price, 2) }}</span>
                            @else
                                <span class="bootcamp-current-price">{{ currency($bootcamp_details->price, 2) }}</span>
                            @endif
                        </div>

                        @php
                            $is_purchased = false;
                            $pending_bootcamp_payment = null;

                            if (isset(auth()->user()->id)) {
                                $is_purchased = DB::table('bootcamp_purchases')
                                    ->where('user_id', auth()->user()->id)
                                    ->where('bootcamp_id', $bootcamp_details->id)
                                    ->where('status', 1)
                                    ->exists();

                                $pending_bootcamp_payment = DB::table('offline_payments')
                                    ->where('user_id', auth()->user()->id)
                                    ->where('item_type', 'bootcamp')
                                    ->where('items', $bootcamp_details->id)
                                    ->where('status', 0)
                                    ->first();
                            }
                        @endphp

                        @if (isset(auth()->user()->id))
                            @if ($pending_bootcamp_payment)
                                <button class="bootcamp-purchase-btn processing" disabled>
                                    <i class="fas fa-spinner fa-spin"></i>
                                    {{ get_phrase('قيد المعالجة') }}
                                </button>
                            @else
                                @if ($is_purchased)
                                    <a href="{{ route('theme.my.bootcamp.details', $bootcamp_details->slug) }}"
                                        class="bootcamp-purchase-btn purchased">
                                        <i class="fas fa-check-circle"></i>
                                        {{ get_phrase('الذهاب للبوتكامب') }}
                                    </a>
                                @else
                                    <a href="{{ route('theme.purchase.bootcamp', $bootcamp_details->id) }}"
                                        class="bootcamp-purchase-btn {{ $bootcamp_details->is_paid == 0 ? 'free' : '' }}">
                                        <i class="fas fa-shopping-cart"></i>
                                        {{ get_phrase($bootcamp_details->is_paid ? 'شراء الآن' : 'اشترك مجاناً') }}
                                    </a>
                                @endif
                            @endif
                        @else
                            <a href="{{ route('theme.purchase.bootcamp', $bootcamp_details->id) }}"
                                class="bootcamp-purchase-btn {{ $bootcamp_details->is_paid == 0 ? 'free' : '' }}">
                                <i class="fas fa-shopping-cart"></i>
                                {{ get_phrase($bootcamp_details->is_paid ? 'شراء الآن' : 'اشترك مجاناً') }}
                            </a>
                        @endif
                    </div>

                    <!-- Features List -->
                    <ul class="bootcamp-features-list">
                        <li>
                            <div class="bootcamp-feature-info">
                                <i class="fas fa-layer-group"></i>
                                <p>{{ get_phrase('الوحدات') }}</p>
                            </div>
                            <span class="feature-value">{{ count_bootcamp_modules($bootcamp_details->id) }}</span>
                        </li>
                        <li>
                            <div class="bootcamp-feature-info">
                                <i class="fas fa-video"></i>
                                <p>{{ get_phrase('الحصص المباشرة') }}</p>
                            </div>
                            <span class="bootcamp-feature-value">{{ count_bootcamp_classes($bootcamp_details->id) }}</span>
                        </li>
                        <li>
                            <div class="bootcamp-feature-info">
                                <i class="fas fa-file-alt"></i>
                                <p>{{ get_phrase('الموارد') }}</p>
                            </div>
                            <span class="bootcamp-feature-value">{{ get_phrase('نعم') }}</span>
                        </li>
                        <li>
                            <div class="bootcamp-feature-info">
                                <i class="fas fa-play-circle"></i>
                                <p>{{ get_phrase('تسجيلات الحصص') }}</p>
                            </div>
                            <span class="bootcamp-feature-value">{{ get_phrase('نعم') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Accordion animation
            const accordionButtons = document.querySelectorAll('.accordion-button');
            accordionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (icon) {
                        if (this.classList.contains('collapsed')) {
                            icon.classList.remove('fa-folder');
                            icon.classList.add('fa-folder-open');
                        } else {
                            icon.classList.remove('fa-folder-open');
                            icon.classList.add('fa-folder');
                        }
                    }
                });
            });

            // Animate stats on scroll
            const statCards = document.querySelectorAll('.stat-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.animation = 'fadeIn 0.6s ease forwards';
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 150);
                    }
                });
            }, {
                threshold: 0.3
            });

            statCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                observer.observe(card);
            });

            // Hover effects for lesson items
            const lessonItems = document.querySelectorAll('.lesson-list li');
            lessonItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.style.transform = 'scale(1.2) rotate(10deg)';
                        icon.style.color = 'rgb(var(--c-accent-rgb))';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.style.transform = 'scale(1) rotate(0deg)';
                        icon.style.color = 'rgb(var(--c-accent-rgb))';
                    }
                });
            });

            // Purchase button animation
            const purchaseBtn = document.querySelector('.purchase-btn:not(.processing)');
            if (purchaseBtn) {
                purchaseBtn.addEventListener('mouseenter', function() {
                    if (!this.disabled) {
                        this.style.transform = 'translateY(-3px) scale(1.02)';
                    }
                });

                purchaseBtn.addEventListener('mouseleave', function() {
                    if (!this.disabled) {
                        this.style.transform = 'translateY(0) scale(1)';
                    }
                });
            }

            // Featured image hover effect
            const featuredImage = document.querySelector('.featured-image');
            if (featuredImage) {
                featuredImage.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 30px 60px rgba(0, 0, 0, 0.3)';
                });

                featuredImage.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'var(--shadow-deep)';
                });
            }
        });
    </script>
@endpush
