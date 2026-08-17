@extends('theme::layouts.master')

@push('title', get_phrase('My Bootcamps'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-bootcamps-modern.css') }}">
@endpush

@section('content')
    @php
        \Carbon\Carbon::setLocale('ar');
        $user = get_user_info($bootcamp->user_id);
        $modules = App\Models\BootcampModule::where('bootcamp_id', $bootcamp->id)->get();
        $purchase = App\Models\BootcampPurchase::where('user_id', auth()->user()->id)
            ->where('bootcamp_id', $bootcamp->id)
            ->where('status', 1)
            ->first();
    @endphp

    <section class="my-course-content main_content mb-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="mb-header">
                        <div class="mb-header__intro">
                            <div class="mb-header__icon" aria-hidden="true">
                                <i class="fa-solid fa-campground"></i>
                            </div>
                            <div>
                                <h1 class="mb-header__title">{{ get_phrase('My Bootcamps') }}</h1>
                                <p class="mb-header__sub">
                                    {{ get_phrase('محتوى المعسكر والحصص المباشرة والموارد التعليمية.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-detail-hero">
                        <div class="mb-detail-hero__thumb">
                            <img src="{{ get_image($bootcamp->thumbnail ?? '') }}" alt="{{ $bootcamp->title }}">
                        </div>

                        <div>
                            <h2 class="mb-detail-hero__title">
                                <a href="{{ route('theme.bootcamp.details', $bootcamp->slug) }}">
                                    {{ $bootcamp->title }}
                                </a>
                            </h2>

                            <p class="mb-detail-hero__instructor">
                                {{ get_phrase('By ') }}
                                
                            </p>

                            <div class="mb-detail-hero__meta">
                                <span>
                                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                    {{ date('d M, Y', $bootcamp->publish_date) }}
                                </span>
                                <span>
                                    <i class="fa-solid fa-video" aria-hidden="true"></i>
                                    {{ count_bootcamp_classes($bootcamp->id) }}
                                    {{ get_phrase('Live class') }}
                                </span>
                            </div>

                            <div class="mb-detail-hero__links">
                                @if ($purchase)
                                    <a href="{{ route('theme.my.bootcamp.invoice', ['id' => $purchase->id]) }}">
                                        <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                                        {{ get_phrase('Invoice') }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('theme.my.bootcamps') }}" class="mb-btn mb-btn--ghost">
                            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                            {{ get_phrase('Back') }}
                        </a>
                    </div>

                    @if ($modules->count() > 0)
                        <div class="mb-modules" id="bootcamp-modules">
                            @foreach ($modules as $module)
                                @php
                                    $isAvailable = 1;

                                    if ($module->restriction == 1) {
                                        $isAvailable = time() >= $module->publish_date ? 1 : 0;
                                    } elseif ($module->restriction == 2) {
                                        $isAvailable = time() >= $module->publish_date && time() <= $module->expiry_date ? 1 : 0;
                                    }

                                    $liveClasses = App\Models\BootcampLiveClass::where('module_id', $module->id)->get();
                                    $resources = App\Models\BootcampResource::where('module_id', $module->id)->get();
                                @endphp

                                <div class="mb-module">
                                    <button class="mb-module__head collapsed" type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#module-content-{{ $module->id }}"
                                        aria-expanded="false"
                                        aria-controls="module-content-{{ $module->id }}">
                                        <div>
                                            <h3 class="mb-module__title">{{ $module->title }}</h3>
                                            @if ($module->restriction == 1)
                                                <p class="mb-module__hint">
                                                    {{ get_phrase('Available from : ') }}
                                                    {{ date('d-M-Y', $module->publish_date) }}
                                                </p>
                                            @elseif ($module->restriction == 2)
                                                <p class="mb-module__hint">
                                                    {{ get_phrase('Available within : ') }}
                                                    {{ date('d-M-Y', $module->publish_date) }} -
                                                    {{ date('d-M-Y', $module->expiry_date) }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="mb-module__meta">
                                            <span>
                                                <i class="fa-solid fa-video" aria-hidden="true"></i>
                                                {{ count_bootcamp_classes($module->id, 'module') }}
                                                {{ get_phrase('Live class') }}
                                            </span>
                                            @if (! $isAvailable)
                                                <i class="fa-solid fa-lock" aria-hidden="true"></i>
                                            @endif
                                            <i class="fa-solid fa-chevron-down mb-module__chevron" aria-hidden="true"></i>
                                        </div>
                                    </button>

                                    <div id="module-content-{{ $module->id }}" class="collapse mb-module__body"
                                        data-bs-parent="#bootcamp-modules">
                                        @if ($isAvailable)
                                            @if ($liveClasses->count() > 0)
                                                <ul class="mb-class-list">
                                                    @foreach ($liveClasses as $class)
                                                        <li class="mb-class-item">
                                                            <div>
                                                                <p class="mb-class-item__title">{{ $class->title }}</p>
                                                                <div class="mb-class-item__meta">
                                                                    @if ($class->status == 'live')
                                                                        <span class="mb-badge mb-badge--live">{{ $class->status }}</span>
                                                                    @elseif ($class->status == 'upcoming')
                                                                        <span class="mb-badge mb-badge--upcoming">{{ $class->status }}</span>
                                                                    @elseif ($class->status == 'completed')
                                                                        <span class="mb-badge mb-badge--completed">{{ $class->status }}</span>
                                                                    @endif
                                                                    <span>{{ date('d M, y', $class->start_time) }}</span>
                                                                    <span>
                                                                        ({{ date('h:i a', $class->start_time) }} -
                                                                        {{ date('h:i a', $class->end_time) }})
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <a href="{{ class_started($class->id) ? route('theme.bootcamp.live.class.join', slugify($class->title)) : 'javascript:void(0);' }}"
                                                                class="mb-btn mb-btn--primary {{ class_started($class->id) ? '' : 'is-disabled' }}">
                                                                {{ get_phrase('Join Now') }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            @if ($resources->count() > 0)
                                                <h4 class="mb-section-title">{{ get_phrase('Resource files') }}</h4>
                                                <ul class="mb-class-list">
                                                    @foreach ($resources as $resource)
                                                        <li class="mb-class-item">
                                                            <div>
                                                                <p class="mb-class-item__title">{{ $resource->title }}</p>
                                                                <div class="mb-class-item__meta">
                                                                    @if ($resource->upload_type == 'resource')
                                                                        <span class="mb-badge mb-badge--resource">{{ get_phrase('Resource') }}</span>
                                                                    @elseif ($resource->upload_type == 'record')
                                                                        <span class="mb-badge mb-badge--record">{{ get_phrase('Record') }}</span>
                                                                    @endif
                                                                    <span>{{ date('d M, Y', $resource->create_at) }}</span>
                                                                </div>
                                                            </div>

                                                            @if ($resource->upload_type == 'resource')
                                                                <a href="{{ route('theme.bootcamp.resource.download', $resource->id) }}"
                                                                    class="mb-btn mb-btn--ghost">
                                                                    {{ get_phrase('Download') }}
                                                                </a>
                                                            @else
                                                                <a href="{{ route('theme.bootcamp.resource.play', $resource->title) }}"
                                                                    class="mb-btn mb-btn--primary">
                                                                    {{ get_phrase('Play Now') }}
                                                                </a>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            @if ($liveClasses->count() < 1 && $resources->count() < 1)
                                                <p class="mb-no-data">{{ get_phrase('Module has no class available.') }}</p>
                                            @endif
                                        @else
                                            <p class="mb-no-data">{{ get_phrase('Module is not available.') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-empty">
                            <div class="mb-empty__icon" aria-hidden="true">
                                <i class="fa-solid fa-folder-open"></i>
                            </div>
                            <h2>{{ get_phrase('لا يوجد محتوى') }}</h2>
                            <p>{{ get_phrase('Module has no class available.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.mb-module__head').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    setTimeout(function () {
                        btn.setAttribute('aria-expanded', btn.classList.contains('collapsed') ? 'false' : 'true');
                    }, 0);
                });
            });
        });
    </script>
@endsection
