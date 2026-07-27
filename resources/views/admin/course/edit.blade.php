@extends('layouts.admin')
@push('title', get_phrase('Edit course'))

@section('content')
    <div class="admin-page tf-edit-shell">
        <div class="tf-workspace tf-workspace--wide">
            <div class="tf-hero">
                <div>
                    <div class="tf-hero__kicker">
                        <span class="edit-badge py-1 px-2">{{ get_phrase('Editing') }}</span>
                    </div>
                    <h1 class="tf-hero__title">{{ $course_details->title }}</h1>
                    <p class="tf-hero__desc">{{ get_phrase('Update curriculum, pricing and access settings') }}</p>
                </div>
                <div class="tf-hero__actions">
                    <a href="{{ route('admin.courses') }}" class="tf-btn tf-btn--ghost">
                        <i class="fi-rr-arrow-small-left"></i>
                        {{ get_phrase('Back') }}
                    </a>
                </div>
            </div>

            <form class="ajaxForm" action="{{ route('admin.course.update', $course_details->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="ol-card">
                    <div class="ol-card-body p-20px mb-3">
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-8">
                                @php
                                    $watch_history = App\Models\Watch_history::where('course_id', $course_details->course_id)
                                        ->where('student_id', auth()->user()->id)
                                        ->first();

                                    $lesson = App\Models\Lesson::where('course_id', $course_details->course_id)
                                        ->orderBy('sort', 'asc')
                                        ->first();

                                    if (!$watch_history && $lesson) {
                                        $url['slug'] = $course_details->slug;
                                        $lesson_id = '';
                                    } else {
                                        if ($watch_history) {
                                            $lesson_id = $watch_history->watching_lesson_id;
                                        } elseif ($lesson) {
                                            $lesson_id = $lesson->id;
                                        } else {
                                            $lesson_id = '';
                                        }
                                        $url['id'] = $lesson_id;
                                    }
                                @endphp

                                <a href="{{ route('course.player', ['slug' => $course_details->slug, 'id' => $lesson_id ?? '']) }}"
                                    target="_blank" class="tf-btn tf-btn--ghost">
                                    {{ get_phrase('Course Player') }}
                                    <i class="fi-rr-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                            <div class="col-sm-4 mt-3 mt-sm-0 d-flex justify-content-start justify-content-sm-end">
                                <button type="submit"
                                    class="tf-btn tf-btn--primary @if (request('tab') == 'live-class' || request('tab') == 'curriculum') opacity-0 @endif">
                                    <i class="fi-rr-check"></i>
                                    {{ get_phrase('Save Changes') }}
                                </button>
                            </div>
                        </div>

                        <div class="d-flex gap-3 flex-wrap flex-md-nowrap">
                            <div class="ol-sidebar-tab">
                                <div class="d-flex flex-column">
                                    @php
                                        $param = request()->route()->parameter('id');
                                        $tab = request('tab');
                                    @endphp

                                    <input type="hidden" name="tab" value="{{ $tab }}">

                                    <a class="nav-link @if ($tab == 'curriculum' || $tab == '') active @endif"
                                        href="{{ route('admin.course.edit', [$param, 'tab' => 'curriculum']) }}">
                                        <span class="fi-rr-edit"></span>
                                        <span>{{ get_phrase('Curriculum') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'basic') active @endif"
                                        href="{{ route('admin.course.edit', [$param, 'tab' => 'basic']) }}">
                                        <span class="icon fi-rr-duplicate"></span>
                                        <span>{{ get_phrase('Basic') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'pricing') active @endif"
                                        href="{{ route('admin.course.edit', [$param, 'tab' => 'pricing']) }}">
                                        <span class="fi-rr-comment-dollar"></span>
                                        <span>{{ get_phrase('Pricing') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'drip-content') active @endif"
                                        href="{{ route('admin.course.edit', [$param, 'tab' => 'drip-content']) }}">
                                        <span class="fi-rr-settings-sliders"></span>
                                        <span>{{ get_phrase('Drip Content') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="tab-content w-100">
                                @includeWhen($tab == 'curriculum' || $tab == '', 'admin.course.curriculum')
                                @includeWhen($tab == 'basic', 'admin.course.edit_basic')
                                @includeWhen($tab == 'live-class', 'admin.course.live_class')
                                @includeWhen($tab == 'pricing', 'admin.course.edit_pricing')
                                @includeWhen($tab == 'info', 'admin.course.edit_info')
                                @includeWhen($tab == 'seo', 'admin.course.edit_seo')
                                @includeWhen($tab == 'drip-content', 'admin.course.edit_drip_settings')
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
