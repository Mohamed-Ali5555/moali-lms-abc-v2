@extends('layouts.admin')

@push('title', $book->title)

@section('content')
    @php
        $embedUrl = null;
        if ($book->file_type === 'link' && $book->file_url) {
            $embedUrl = $book->file_url;
            if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $book->file_url, $m)) {
                $embedUrl = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
            }
        }
        $fileLabel = $book->file_type === 'file' ? get_phrase('Uploaded PDF') : get_phrase('External link');
    @endphp

    <div class="admin-page admin-book-viewer">
        <div class="admin-toolbar">
            <div class="admin-toolbar__meta">
                <a href="{{ route('admin.bookstore') }}" class="admin-book-viewer__back" title="{{ get_phrase('Back') }}">
                    <i class="fi-rr-angle-left"></i>
                </a>
                <div class="admin-book-viewer__thumb">
                    <img src="{{ get_image($book->thumbnail) }}" alt="{{ $book->title }}">
                </div>
                <div>
                    <h1 class="admin-toolbar__title">{{ $book->title }}</h1>
                    <p class="admin-toolbar__desc">
                        <i class="fi-rr-book-open-cover me-1"></i>
                        {{ get_phrase('Book preview') }}
                        <span class="admin-book-viewer__dot">&middot;</span>
                        {{ $fileLabel }}
                    </p>
                </div>
            </div>
            <div class="admin-toolbar__actions">
                @if ($book->file_type === 'link' && $book->file_url)
                    <a href="{{ $book->file_url }}" target="_blank" rel="noopener noreferrer"
                        class="admin-btn admin-btn--ghost">
                        <i class="fi-rr-external-link"></i>
                        <span>{{ get_phrase('Open link') }}</span>
                    </a>
                @elseif ($book->file_type === 'file' && $book->file_path)
                    <a href="{{ asset($book->file_path) }}" target="_blank" rel="noopener noreferrer"
                        class="admin-btn admin-btn--ghost">
                        <i class="fi-rr-download"></i>
                        <span>{{ get_phrase('Open file') }}</span>
                    </a>
                @endif
                <a href="{{ route('admin.bookstore') }}" class="admin-btn admin-btn--primary">
                    <i class="fi-rr-books"></i>
                    <span>{{ get_phrase('All Books') }}</span>
                </a>
            </div>
        </div>

        <div class="admin-book-viewer__frame">
            @if ($book->file_type === 'file' && $book->file_path)
                <iframe src="{{ asset($book->file_path) }}#toolbar=1&navpanes=0"
                    title="{{ $book->title }}" loading="lazy"></iframe>
            @elseif ($embedUrl)
                <iframe src="{{ $embedUrl }}" title="{{ $book->title }}" allowfullscreen
                    loading="lazy"></iframe>
            @else
                <div class="admin-book-viewer__empty">
                    <i class="fi-rr-book-alt"></i>
                    <p>{{ get_phrase('No book file available') }}</p>
                    <a href="{{ route('admin.bookstore') }}" class="admin-btn admin-btn--primary">
                        {{ get_phrase('Back') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
