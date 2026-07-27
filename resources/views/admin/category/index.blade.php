@extends('layouts.admin')

@push('title', get_phrase('Categories'))

@push('meta')
@endpush

@push('css')
@endpush

@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-apps"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('All Category') }}
                        <span class="admin-toolbar__count">{{ $categories->count() }}</span>
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Organize courses into parent and child categories') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (has_permission('admin.category.edit') || has_permission('admin.category.transfer'))
                    <a onclick="ajaxModal('{{ route('modal', ['admin.category.transfer']) }}', '{{ get_phrase('نقل بين السنوات') }}', 'modal-lg')"
                        href="#" class="admin-btn admin-btn--ghost">
                        <span class="fi-rr-exchange"></span>
                        <span>{{ get_phrase('نقل الطلبة') }}</span>
                    </a>
                @endif
                @if (has_permission('admin.category.create'))
                    <a onclick="ajaxModal('{{ route('modal', ['admin.category.create', 'parent_id' => 0]) }}', '{{ get_phrase('Add new category') }}')"
                        href="#" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new category') }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if (count($categories) > 0)
            <div class="admin-media-grid">
                @foreach ($categories as $category)
                    <article class="admin-media-card">
                        <div class="admin-media-card__media">
                            <img src="{{ get_image($category->thumbnail) }}" alt="{{ $category->title }}">
                            @if ($category->status == 1)
                                <span class="admin-media-card__badge admin-media-card__badge--on">
                                    <i class="fi-rr-eye"></i> {{ get_phrase('Active') }}
                                </span>
                            @else
                                <span class="admin-media-card__badge admin-media-card__badge--off">
                                    <i class="fas fa-eye-slash"></i> {{ get_phrase('Inactive') }}
                                </span>
                            @endif
                        </div>

                        <div class="admin-media-card__body">
                            <div class="admin-media-card__title-row">
                                <h2 class="admin-media-card__title">{{ $category->title }}</h2>
                                <span class="admin-media-card__chip">{{ $category->childs->count() }}</span>
                            </div>

                            @if ($category->childs->count() > 0)
                                <ul class="admin-media-card__children">
                                    @foreach ($category->childs as $child_category)
                                        <li class="admin-media-card__child">
                                            <div class="admin-media-card__child-name">
                                                <span class="admin-status-dot {{ $child_category->status == 1 ? 'admin-status-dot--on' : 'admin-status-dot--off' }}"></span>
                                                <span>{{ $child_category->title }}</span>
                                            </div>
                                            <div class="admin-media-card__child-actions">
                                                @if (has_permission('admin.sub_categories.edit'))
                                                    <a onclick="ajaxModal('{{ route('modal', ['admin.category.edit', 'id' => $child_category->id]) }}', '{{ get_phrase('Edit category') }}')"
                                                        class="admin-icon-btn" data-bs-toggle="tooltip"
                                                        title="{{ get_phrase('Edit') }}" href="#">
                                                        <i class="fi fi-rr-pen-clip"></i>
                                                    </a>
                                                @endif
                                                @if (has_permission('admin.sub_categories.delete'))
                                                    <a onclick="confirmModal('{{ route('admin.category.delete', $child_category->id) }}')"
                                                        class="admin-icon-btn admin-icon-btn--danger" data-bs-toggle="tooltip"
                                                        title="{{ get_phrase('Delete') }}" href="#">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="admin-toolbar__desc mb-0">{{ get_phrase('No subcategories yet') }}</p>
                            @endif
                        </div>

                        <div class="admin-media-card__footer">
                            <div class="admin-media-card__footer-actions">
                                @if (has_permission('admin.category.edit') || has_permission('admin.category.transfer'))
                                    <a onclick="ajaxModal('{{ route('modal', ['admin.category.transfer', 'from_id' => $category->id]) }}', '{{ get_phrase('نقل بين السنوات') }}', 'modal-lg')"
                                        href="#" class="admin-icon-btn" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('نقل الطلبة') }}">
                                        <i class="fi-rr-exchange"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.sub_categories.create'))
                                    <a onclick="ajaxModal('{{ route('modal', ['admin.category.create', 'parent_id' => $category->id]) }}', '{{ get_phrase('Add new category') }}')"
                                        href="#" class="admin-icon-btn" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Add subcategory') }}">
                                        <i class="fi fi-rr-plus"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.category.edit'))
                                    <a href="#"
                                        onclick="ajaxModal('{{ route('modal', ['admin.category.edit', 'id' => $category->id]) }}', '{{ get_phrase('Edit category') }}')"
                                        class="admin-icon-btn" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Edit') }}">
                                        <i class="fi fi-rr-pen-clip"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.category.delete'))
                                    <a href="#"
                                        onclick="confirmModal('{{ route('admin.category.delete', $category->id) }}')"
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
