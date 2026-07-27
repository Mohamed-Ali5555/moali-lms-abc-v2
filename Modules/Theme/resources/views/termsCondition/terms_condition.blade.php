@extends('theme::layouts.master')

@push('title', get_phrase('الشروط والخصوصية'))
@push('css')
    <link rel="stylesheet" href="{{ asset('modules/theme/css/terms-modern.css') }}">
@endpush

@section('content')
@php
    $terms = $terms ?? collect();
    $privacy = $privacy ?? collect();
    $legacyTerms = $legacy_terms ?? null;
    $defaultTab = $terms->isNotEmpty() || $legacyTerms ? 'terms' : 'privacy';
@endphp

<section class="lp-page" dir="rtl">
    <div class="container">
        <div class="lp-hero">
            <span class="lp-hero__eyebrow">{{ get_phrase('Legal') }}</span>
            <h1 class="lp-hero__title">{{ get_phrase('الشروط والأحكام وسياسة الخصوصية') }}</h1>
            <p class="lp-hero__sub">
                {{ get_phrase('اطّلع على بنود استخدام المنصة وكيفية التعامل مع بياناتك بشفافية ووضوح.') }}
            </p>
        </div>

        <div class="lp-shell">
            <div class="lp-tabs" role="tablist">
                <button type="button" class="lp-tab {{ $defaultTab === 'terms' ? 'is-active' : '' }}" data-tab="terms" role="tab">
                    {{ get_phrase('شروط الاستخدام') }}
                </button>
                <button type="button" class="lp-tab {{ $defaultTab === 'privacy' ? 'is-active' : '' }}" data-tab="privacy" role="tab">
                    {{ get_phrase('سياسة الخصوصية') }}
                </button>
            </div>

            <div class="lp-pane {{ $defaultTab === 'terms' ? 'is-active' : '' }}" id="lp-terms" role="tabpanel">
                @if ($terms->isNotEmpty())
                    <div class="lp-list">
                        @foreach ($terms as $index => $item)
                            <article class="lp-item">
                                <div class="lp-item__num">{{ $index + 1 }}</div>
                                <div>
                                    <h2 class="lp-item__title">{{ $item->title }}</h2>
                                    <div class="lp-item__body">{!! nl2br(e($item->body)) !!}</div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($legacyTerms)
                    <div class="lp-item">
                        <div class="lp-item__num">1</div>
                        <div>
                            <h2 class="lp-item__title">{{ get_phrase('الشروط والأحكام') }}</h2>
                            <div class="lp-item__body">{!! $legacyTerms !!}</div>
                        </div>
                    </div>
                @else
                    <div class="lp-empty">
                        <strong>{{ get_phrase('لا توجد بنود حالياً') }}</strong>
                        {{ get_phrase('سيتم إضافة شروط الاستخدام قريباً.') }}
                    </div>
                @endif
            </div>

            <div class="lp-pane {{ $defaultTab === 'privacy' ? 'is-active' : '' }}" id="lp-privacy" role="tabpanel">
                @if ($privacy->isNotEmpty())
                    <div class="lp-list">
                        @foreach ($privacy as $index => $item)
                            <article class="lp-item">
                                <div class="lp-item__num">{{ $index + 1 }}</div>
                                <div>
                                    <h2 class="lp-item__title">{{ $item->title }}</h2>
                                    <div class="lp-item__body">{!! nl2br(e($item->body)) !!}</div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="lp-empty">
                        <strong>{{ get_phrase('لا توجد بنود حالياً') }}</strong>
                        {{ get_phrase('سيتم إضافة سياسة الخصوصية قريباً.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('js')
<script>
(function () {
    const tabs = document.querySelectorAll('.lp-tab');
    const panes = {
        terms: document.getElementById('lp-terms'),
        privacy: document.getElementById('lp-privacy'),
    };

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.tab;
            tabs.forEach(t => t.classList.toggle('is-active', t === tab));
            Object.keys(panes).forEach(k => {
                panes[k]?.classList.toggle('is-active', k === key);
            });
        });
    });
})();
</script>
@endpush
