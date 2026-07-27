@extends('layouts.admin')
@push('title', get_phrase('Bank Question Category'))


@section('content')
<div class="admin-page">
    <div class="admin-toolbar">
        <div class="admin-toolbar__meta">
            <span class="admin-toolbar__icon">
                <i class="fi-rr-apps"></i>
            </span>
            <div>
                <h1 class="admin-toolbar__title">
                    {{ get_phrase('Bank Question Category') }}
                    <span class="admin-toolbar__count">{{ $categories->count() }}</span>
                </h1>
                <p class="admin-toolbar__desc">{{ get_phrase('Organize bank questions into categories') }}</p>
            </div>
        </div>
        <div class="admin-toolbar__actions">
            @if (has_permission('admin.category.bank.questions.create'))
                <a onclick="ajaxModal('{{ route('modal', ['bankquestions::category.create']) }}', '{{ get_phrase('Add new category') }}')" href="#"
                    class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('Add new Category') }}</span>
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
                            <span class="admin-media-card__chip">{{ $category->questions->count() }}</span>
                        </div>
                        <p class="admin-toolbar__desc mb-0">{{ get_phrase('Category') }} | {{ $category->category->title }}</p>
                        <ul class="admin-media-card__children">
                            <li class="admin-media-card__child">
                                <div class="admin-media-card__child-name">
                                    <span>{{ get_phrase('Quizs count') }} | {{ $category->quizs->count() }}</span>
                                </div>
                            </li>
                            <li class="admin-media-card__child">
                                <div class="admin-media-card__child-name">
                                    <span>{{ get_phrase('Questions count') }} | {{ $category->questions->count() }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="admin-media-card__footer">
                        <div class="admin-media-card__footer-actions">
                            @if (has_permission('admin.category.bank.questions.edit'))
                                <a href="javascript:void(0);"
                                    onclick="ajaxModal('{{ route('modal', ['bankquestions::category.edit', 'id' => $category->id]) }}', '{{ get_phrase('Edit category') }}')"
                                    class="admin-icon-btn" data-bs-toggle="tooltip"
                                    title="{{ get_phrase('Edit') }}">
                                    <i class="fi fi-rr-pen-clip"></i>
                                </a>
                            @endif
                            @if (has_permission('admin.category.bank.questions.delete'))
                                <a href="javascript:void(0);"
                                    onclick="confirmModal('{{ route('admin.category.bank.questions.destroy', $category->id) }}')"
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
        <div class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15 mb-5">
            <p class="admin-tInfo">
                {{ get_phrase('Showing') . ' ' . count($categories) . ' ' . get_phrase('of') . ' ' . $categories->total() . ' ' . get_phrase('data') }}
            </p>
            {{ $categories->links() }}
        </div>
    @else
        @include('admin.no_data')
    @endif
</div>
@endsection
