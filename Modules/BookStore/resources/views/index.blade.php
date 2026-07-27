@extends('layouts.admin')

@push('title', get_phrase('Books'))

@push('meta')
@endpush

@push('css')
@endpush

@section('content')
    <div class="admin-page">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <span class="admin-toolbar__icon">
                    <i class="fi-rr-book-alt"></i>
                </span>
                <div>
                    <h1 class="admin-toolbar__title">
                        {{ get_phrase('All Books') }}
                        <span class="admin-toolbar__count">{{ $books->count() }}</span>
                    </h1>
                    <p class="admin-toolbar__desc">{{ get_phrase('Manage bookstore items, prices, and visibility') }}</p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if (count($books) > 0)
                    @if (has_permission('admin.bookstore.sort'))
                        <a href="#"
                            onclick="ajaxModal('{{ route('modal', ['bookstore::book_sort']) }}', '{{ get_phrase('Sort books') }}')"
                            class="admin-btn admin-btn--ghost">
                            <i class="fi-rr-sort-alt"></i>
                            <span>{{ get_phrase('Sort book') }}</span>
                        </a>
                    @endif
                @endif
                @if (has_permission('admin.bookstore.create'))
                    <a onclick="ajaxModal('{{ route('modal', ['bookstore::create', 'parent_id' => 0]) }}', '{{ get_phrase('Add new book') }}', 'modal-lg')"
                        href="#" class="admin-btn admin-btn--primary">
                        <span class="fi-rr-plus"></span>
                        <span>{{ get_phrase('Add new book') }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if (count($books) > 0)
            <div class="admin-media-grid">
                @foreach ($books as $book)
                    <article class="admin-media-card">
                        <div class="admin-media-card__media">
                            <img src="{{ get_image($book->thumbnail) }}" alt="{{ $book->title }}">
                            @if ($book->status == 1)
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
                                <h2 class="admin-media-card__title">{{ $book->title }}</h2>
                            </div>
                            <div class="admin-media-card__price">
                                @if ($book->if_discount == 1)
                                    <span class="admin-media-card__price-now">{{ $book->discount_price }}</span>
                                    <span class="admin-media-card__price-old">{{ $book->price }}</span>
                                @else
                                    <span class="admin-media-card__price-now">{{ $book->price }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="admin-media-card__footer">
                            <div class="admin-media-card__footer-actions">
                                @if (has_permission('admin.bookstore.edit'))
                                    <a href="#"
                                        onclick="ajaxModal('{{ route('modal', ['bookstore::edit', 'id' => $book->id]) }}', '{{ get_phrase('Edit book') }}', 'modal-lg')"
                                        class="admin-icon-btn" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Edit') }}">
                                        <i class="fi fi-rr-pen-clip"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.bookstore.delete'))
                                    <a href="#"
                                        onclick="confirmModal('{{ route('admin.bookstore.delete', $book->id) }}')"
                                        class="admin-icon-btn admin-icon-btn--danger" data-bs-toggle="tooltip"
                                        title="{{ get_phrase('Delete') }}">
                                        <i class="fi-rr-trash"></i>
                                    </a>
                                @endif
                                @if (has_permission('admin.bookstore.activation'))
                                    <a href="#"
                                        onclick="confirmModal('{{ route('admin.bookstore.activation', $book->id) }}')"
                                        class="admin-icon-btn {{ $book->status == 1 ? 'admin-icon-btn--success' : '' }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ $book->status == 1 ? get_phrase('Deactivate') : get_phrase('Activate') }}">
                                        @if ($book->status == 1)
                                            <i class="fi-rr-eye"></i>
                                        @else
                                            <i class="fas fa-eye-slash"></i>
                                        @endif
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
