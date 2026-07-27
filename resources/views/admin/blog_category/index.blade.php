@extends('layouts.admin')
@push('title', get_phrase('Blog category'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-apps"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('Blog Category') }}
                        <span class="admin-toolbar__count">{{ $categories->count() }}</span>
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Organize blog posts into categories') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.blog.category.create'))
                    <a href="javascript:void(0);"
                        class="admin-btn admin-btn--primary"
                        onclick="ajaxModal('{{ route('modal', ['admin.blog_category.create']) }}', '{{ get_phrase('Add Category') }}')">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new category') }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if ($categories->count() > 0)
            <div class="admin-media-grid">
                @foreach ($categories as $category)
                    <article class="admin-media-card">
                        <div class="admin-media-card__body">
                            <div class="admin-media-card__title-row">
                                <h2 class="admin-media-card__title">{{ $category->title }}</h2>
                                <span class="admin-media-card__chip">{{ count_blogs_by_category($category->id) }}</span>
                            </div>
                            <p class="admin-toolbar__desc mb-0">{{ get_phrase('Total number of blog') }} {{ count_blogs_by_category($category->id) }}</p>
                        </div>
                        <div class="admin-media-card__footer">
                            <div class="admin-media-card__footer-actions">
                                @if (has_permission('admin.blog.category.edit'))
                                    <a href="javascript:void(0);"
                                        onclick="ajaxModal('{{ route('modal', ['admin.blog_category.edit', 'id' => $category->id]) }}', '{{ get_phrase('Edit Category') }}')"
                                        class="admin-icon-btn" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Edit') }}">
                                        <i class="fi fi-rr-pen-clip"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.blog.category.delete'))
                                    <a href="javascript:void(0);"
                                        onclick="confirmModal('{{ route('admin.blog.category.delete', $category->id) }}')"
                                        class="admin-icon-btn admin-icon-btn--danger" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Delete') }}">
                                        <i class="fi-rr-trash"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            @include('admin.no_data')
        @endif
    </div>
@endsection
@push('js')@endpush
