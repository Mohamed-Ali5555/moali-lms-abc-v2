@extends('theme::layouts.master')

@push('title', $book->title)
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/my-books-modern.css') }}">
@endpush

@section('content')
    @php
        $embedUrl = null;
        if ($book->file_type === 'link' && $book->file_url) {
            $embedUrl = $book->file_url;
            if (preg_match('/drive\.google\.com\/file\/d\/([^\/]+)/', $book->file_url, $m)) {
                $embedUrl = 'https://drive.google.com/file/d/' . $m[1] . '/preview';
            }
        }
        $fileLabel = $book->file_type === 'file' ? get_phrase('ملف PDF') : get_phrase('رابط خارجي');
    @endphp

    <section class="myCourses main_content mb-page mb-viewer-page" dir="rtl">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('theme::student.left_sidebar')

                <div class="col-lg-9">
                    <div class="mb-viewer">
                        <div class="mb-viewer__toolbar">
                            <div class="mb-viewer__meta">
                                <a href="{{ route('theme.my.books') }}" class="mb-viewer__back" title="{{ get_phrase('العودة لكتبي') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round" aria-hidden="true">
                                        <polyline points="15 18 9 12 15 6" />
                                    </svg>
                                </a>
                                <div class="mb-viewer__cover">
                                    <img src="{{ get_image($book->thumbnail ?? '') }}" alt="{{ $book->title }}">
                                </div>
                                <div class="mb-viewer__info">
                                    <span class="mb-viewer__eyebrow">{{ get_phrase('قراءة الكتاب') }}</span>
                                    <h1 class="mb-viewer__title">{{ $book->title }}</h1>
                                    <div class="mb-viewer__tags">
                                        <span class="mb-viewer__tag">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path d="M6 2h12v20H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                                                <path d="M6 2v20" />
                                            </svg>
                                            {{ $fileLabel }}
                                        </span>
                                        <span class="mb-viewer__tag mb-viewer__tag--owned">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            {{ get_phrase('تم الشراء') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-viewer__actions">
                                @if ($book->file_type === 'link' && $book->file_url)
                                    <a href="{{ $book->file_url }}" target="_blank" rel="noopener noreferrer"
                                        class="mb-btn mb-btn--ghost">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                            <polyline points="15 3 21 3 21 9" />
                                            <line x1="10" y1="14" x2="21" y2="3" />
                                        </svg>
                                        {{ get_phrase('فتح في تبويب جديد') }}
                                    </a>
                                @endif
                                <a href="{{ route('theme.my.books') }}" class="mb-btn mb-btn--primary">
                                    {{ get_phrase('كتبي') }}
                                </a>
                            </div>
                        </div>

                        <div class="mb-viewer__frame">
                            @if ($book->file_type === 'file' && $book->file_path)
                                <iframe src="{{ route('theme.my.books.file', $book->id) }}#toolbar=1&navpanes=0"
                                    title="{{ $book->title }}" loading="lazy"></iframe>
                            @elseif ($embedUrl)
                                <iframe src="{{ $embedUrl }}" title="{{ $book->title }}" allowfullscreen
                                    loading="lazy"></iframe>
                            @else
                                <div class="mb-viewer__empty">
                                    <div class="mb-viewer__empty-icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                        </svg>
                                    </div>
                                    <p>{{ get_phrase('لا يوجد ملف لهذا الكتاب حالياً') }}</p>
                                    <a href="{{ route('theme.my.books') }}" class="mb-btn mb-btn--primary">
                                        {{ get_phrase('العودة لكتبي') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
