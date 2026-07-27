@extends('layouts.admin')
@push('title', get_phrase('الشروط والخصوصية'))

@section('content')
@php
    $activeTab = old('active_tab', request('tab', 'terms'));
@endphp

<style>
    .lg-page { direction: rtl; }

    .lg-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 20px 22px;
        border-radius: 18px;
        margin-bottom: 16px;
        color: #e2e8f0;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(13, 148, 136, 0.18), transparent 55%),
            linear-gradient(135deg, #0b1220 0%, #132033 100%);
    }

    .lg-hero h1 {
        margin: 0 0 6px;
        font-size: 22px;
        font-weight: 800;
        color: #f8fafc;
    }

    .lg-hero p {
        margin: 0;
        color: #94a3b8;
        font-size: 14px;
        line-height: 1.6;
        max-width: 560px;
    }

    .lg-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .lg-tabs {
        display: flex;
        gap: 8px;
        padding: 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        flex-wrap: wrap;
    }

    .lg-tab {
        appearance: none;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-weight: 700;
        font-size: 14px;
        min-height: 42px;
        padding: 8px 16px;
        border-radius: 999px;
        cursor: pointer;
        transition: .2s ease;
    }

    .lg-tab.is-active {
        color: #fff;
        border-color: transparent;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        box-shadow: 0 8px 18px rgba(13, 148, 136, 0.25);
    }

    .lg-pane { display: none; padding: 18px; }
    .lg-pane.is-active { display: block; }

    .lg-list { display: flex; flex-direction: column; gap: 14px; }

    .lg-row {
        position: relative;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        background: #fff;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
    }

    .lg-row__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .lg-row__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 13px;
        color: #0f766e;
        background: rgba(13, 148, 136, 0.12);
    }

    .lg-row__remove {
        appearance: none;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #dc2626;
        font-weight: 700;
        font-size: 13px;
        min-height: 36px;
        padding: 6px 12px;
        border-radius: 10px;
        cursor: pointer;
    }

    .lg-row__remove:hover { background: #fee2e2; }

    .lg-field { margin-bottom: 12px; }
    .lg-field:last-child { margin-bottom: 0; }

    .lg-field label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .lg-field input,
    .lg-field textarea {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        outline: none;
    }

    .lg-field textarea {
        min-height: 110px;
        resize: vertical;
        font-weight: 500;
        line-height: 1.7;
    }

    .lg-field input:focus,
    .lg-field textarea:focus {
        border-color: rgba(13, 148, 136, 0.5);
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
        background: #fff;
    }

    .lg-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 14px;
    }

    .lg-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
        border-radius: 12px;
        border: none;
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
    }

    .lg-btn--add {
        color: #0f766e;
        background: rgba(13, 148, 136, 0.1);
        border: 1px dashed rgba(13, 148, 136, 0.35);
    }

    .lg-btn--add:hover { background: rgba(13, 148, 136, 0.16); }

    .lg-btn--save {
        color: #fff;
        background: linear-gradient(135deg, #0d9488, #0f766e);
        box-shadow: 0 10px 22px rgba(13, 148, 136, 0.25);
    }

    .lg-empty {
        padding: 28px 16px;
        text-align: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #f8fafc;
    }

    .lg-preview {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #e2e8f0;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, 0.25);
        background: rgba(255,255,255,0.06);
    }

    .lg-preview:hover { color: #fff; background: rgba(255,255,255,0.1); }
</style>

<div class="lg-page">
    <div class="lg-hero">
        <div>
            <h1>{{ get_phrase('الشروط وسياسة الخصوصية') }}</h1>
            <p>{{ get_phrase('أضف بنود الشروط والخصوصية كسطور مستقلة، ويمكنك إضافة بند جديد في أي وقت.') }}</p>
        </div>
        <a class="lg-preview" href="{{ route('theme.terms.condition') }}" target="_blank" rel="noopener">
            <i class="fi-rr-eye"></i>
            {{ get_phrase('معاينة الصفحة') }}
        </a>
    </div>

    <form class="lg-panel" action="{{ route('admin.theme.legal.store') }}" method="post" id="legal-form">
        @csrf
        <input type="hidden" name="active_tab" id="active_tab" value="{{ $activeTab }}">

        <div class="lg-tabs" role="tablist">
            <button type="button" class="lg-tab {{ $activeTab === 'terms' ? 'is-active' : '' }}" data-tab="terms">
                {{ get_phrase('شروط الاستخدام') }}
                <span class="badge bg-secondary ms-1" id="terms-count">{{ $terms->count() }}</span>
            </button>
            <button type="button" class="lg-tab {{ $activeTab === 'privacy' ? 'is-active' : '' }}" data-tab="privacy">
                {{ get_phrase('سياسة الخصوصية') }}
                <span class="badge bg-secondary ms-1" id="privacy-count">{{ $privacy->count() }}</span>
            </button>
        </div>

        <div class="lg-pane {{ $activeTab === 'terms' ? 'is-active' : '' }}" id="pane-terms" data-type="terms">
            <div class="lg-list" id="terms-list">
                @foreach ($terms as $index => $row)
                    @include('theme::setting.partials.legal_row', [
                        'type' => 'terms',
                        'index' => $index,
                        'title' => old("terms.$index.title", $row->title),
                        'body' => old("terms.$index.body", $row->body),
                    ])
                @endforeach
            </div>
            @if ($terms->isEmpty() && !old('terms'))
                <div class="lg-empty" id="terms-empty">{{ get_phrase('لا توجد بنود بعد. اضغط إضافة بند جديد.') }}</div>
            @endif
            <div class="lg-toolbar">
                <button type="button" class="lg-btn lg-btn--add" data-add="terms">
                    <i class="fi-rr-plus"></i>
                    {{ get_phrase('إضافة بند جديد') }}
                </button>
            </div>
        </div>

        <div class="lg-pane {{ $activeTab === 'privacy' ? 'is-active' : '' }}" id="pane-privacy" data-type="privacy">
            <div class="lg-list" id="privacy-list">
                @foreach ($privacy as $index => $row)
                    @include('theme::setting.partials.legal_row', [
                        'type' => 'privacy',
                        'index' => $index,
                        'title' => old("privacy.$index.title", $row->title),
                        'body' => old("privacy.$index.body", $row->body),
                    ])
                @endforeach
            </div>
            @if ($privacy->isEmpty() && !old('privacy'))
                <div class="lg-empty" id="privacy-empty">{{ get_phrase('لا توجد بنود بعد. اضغط إضافة بند جديد.') }}</div>
            @endif
            <div class="lg-toolbar">
                <button type="button" class="lg-btn lg-btn--add" data-add="privacy">
                    <i class="fi-rr-plus"></i>
                    {{ get_phrase('إضافة بند جديد') }}
                </button>
            </div>
        </div>

        <div class="lg-toolbar" style="padding: 0 18px 18px;">
            <div></div>
            <button type="submit" class="lg-btn lg-btn--save">
                <i class="fi-rr-disk"></i>
                {{ get_phrase('حفظ الكل') }}
            </button>
        </div>
    </form>
</div>

<template id="legal-row-template">
    <div class="lg-row" data-row>
        <div class="lg-row__head">
            <span class="lg-row__badge" data-index-label>#1</span>
            <button type="button" class="lg-row__remove" data-remove>
                <i class="fi-rr-trash"></i> {{ get_phrase('حذف') }}
            </button>
        </div>
        <div class="lg-field">
            <label>{{ get_phrase('عنوان البند') }}</label>
            <input type="text" data-name-title required placeholder="{{ get_phrase('مثال: قبول الشروط') }}">
        </div>
        <div class="lg-field">
            <label>{{ get_phrase('المحتوى') }}</label>
            <textarea data-name-body required rows="4" placeholder="{{ get_phrase('اكتب نص البند هنا...') }}"></textarea>
        </div>
    </div>
</template>

<script>
(function () {
    const form = document.getElementById('legal-form');
    if (!form) return;

    const tabs = form.querySelectorAll('.lg-tab');
    const panes = form.querySelectorAll('.lg-pane');
    const activeTabInput = document.getElementById('active_tab');
    const template = document.getElementById('legal-row-template');

    function switchTab(tab) {
        tabs.forEach(btn => btn.classList.toggle('is-active', btn.dataset.tab === tab));
        panes.forEach(pane => pane.classList.toggle('is-active', pane.id === 'pane-' + tab));
        activeTabInput.value = tab;
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    function reindex(type) {
        const list = document.getElementById(type + '-list');
        const rows = list.querySelectorAll('[data-row]');
        rows.forEach((row, index) => {
            const badge = row.querySelector('[data-index-label]');
            if (badge) badge.textContent = '#' + (index + 1);

            const title = row.querySelector('[data-name-title], input[name*="[title]"]');
            const body = row.querySelector('[data-name-body], textarea[name*="[body]"]');
            if (title) {
                title.setAttribute('name', type + '[' + index + '][title]');
                title.setAttribute('data-name-title', '');
            }
            if (body) {
                body.setAttribute('name', type + '[' + index + '][body]');
                body.setAttribute('data-name-body', '');
            }
        });

        const counter = document.getElementById(type + '-count');
        if (counter) counter.textContent = String(rows.length);
    }

    function addRow(type) {
        const list = document.getElementById(type + '-list');
        const empty = document.getElementById(type + '-empty');
        if (empty) empty.remove();
        const node = template.content.cloneNode(true);
        const row = node.querySelector('[data-row]');
        list.appendChild(row);
        reindex(type);
        row.querySelector('input')?.focus();
    }

    form.querySelectorAll('[data-add]').forEach(btn => {
        btn.addEventListener('click', () => addRow(btn.dataset.add));
    });

    form.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('[data-remove]');
        if (!removeBtn) return;

        const row = removeBtn.closest('[data-row]');
        const list = row?.parentElement;
        if (!row || !list) return;

        const type = list.id.replace('-list', '');
        row.remove();
        reindex(type);

        if (list.querySelectorAll('[data-row]').length === 0 && !document.getElementById(type + '-empty')) {
            const empty = document.createElement('div');
            empty.className = 'lg-empty';
            empty.id = type + '-empty';
            empty.textContent = '{{ get_phrase('لا توجد بنود بعد. اضغط إضافة بند جديد.') }}';
            list.after(empty);
        }
    });

    // Ensure names are correct on load
    reindex('terms');
    reindex('privacy');
})();
</script>
@endsection
