@extends('layouts.admin')
@push('title', get_phrase('Edit bootcamp'))

@section('content')
    <div class="admin-page tf-edit-shell">
        <div class="tf-workspace tf-workspace--wide">
            <div class="tf-hero">
                <div>
                    <div class="tf-hero__kicker">
                        <span class="edit-badge py-1 px-2">{{ get_phrase('Editing') }}</span>
                    </div>
                    <h1 class="tf-hero__title">{{ $bootcamp_details->title }}</h1>
                    <p class="tf-hero__desc">{{ get_phrase('Update curriculum, pricing and media settings') }}</p>
                </div>
                <div class="tf-hero__actions">
                    <a href="{{ route('admin.bootcamps') }}" class="tf-btn tf-btn--ghost">
                        <i class="fi-rr-arrow-small-left"></i>
                        {{ get_phrase('Back') }}
                    </a>
                    <a href="https://creativeitem.com/docs" class="tf-btn tf-btn--ghost" target="_blank">
                        <i class="fi-rr-arrow-up-right-from-square"></i>
                        {{ get_phrase('Help') }}
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.bootcamp.update', $bootcamp_details->id) }}" method="post"
                enctype="multipart/form-data">@csrf
                <div class="ol-card" style="border:none;box-shadow:none;background:transparent;">
                    <div class="ol-card-body p-20px mb-3">

                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-8">
                                <a href="{{ route('bootcamp.details', $bootcamp_details->slug) }}"
                                    class="tf-btn tf-btn--ghost">
                                    {{ get_phrase('Frontent View') }}
                                    <i class="fi-rr-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                            <div class="col-sm-4 mt-3 mt-sm-0 d-flex justify-content-start justify-content-sm-end">
                                <button type="submit"
                                    class="tf-btn tf-btn--primary @if (request('tab') == 'curriculum') opacity-0 @endif">
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

                                    <a class="nav-link @if ($tab == 'curriculum') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'curriculum']) }}">
                                        <span class="fi-rr-edit"></span>
                                        <span>{{ get_phrase('Curriculum') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'basic') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'basic']) }}">
                                        <span class="icon fi-rr-duplicate"></span>
                                        <span>{{ get_phrase('Basic') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'pricing') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'pricing']) }}">
                                        <span class="fi-rr-comment-dollar"></span>
                                        <span>{{ get_phrase('Pricing') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'info') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'info']) }}">
                                        <span class="fi-rr-tags"></span>
                                        <span>{{ get_phrase('Info') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'media') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'media']) }}">
                                        <span class="fi fi-rr-gallery"></span>
                                        <span>{{ get_phrase('Media') }}</span>
                                    </a>

                                    <a class="nav-link @if ($tab == 'seo') active @endif"
                                        href="{{ route('admin.bootcamp.edit', [$param, 'tab' => 'seo']) }}">
                                        <span class="fi-rr-note-medical"></span>
                                        <span>{{ get_phrase('SEO') }}</span>
                                    </a>
                                </div>
                            </div>
                            <div class="tab-content w-100">
                                @includeWhen($tab == 'curriculum', 'admin.bootcamp.curriculum')
                                @includeWhen($tab == 'basic', 'admin.bootcamp.edit_basic')
                                @includeWhen($tab == 'pricing', 'admin.bootcamp.edit_pricing')
                                @includeWhen($tab == 'info', 'admin.bootcamp.edit_info')
                                @includeWhen($tab == 'media', 'admin.bootcamp.edit_media')
                                @includeWhen($tab == 'seo', 'admin.bootcamp.edit_seo')
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
