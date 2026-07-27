@extends('layouts.admin')
@push('title', get_phrase('حسابات التواصل'))

@section('content')
<style>
    .ts-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 16px;
    }

    @media (min-width: 576px) {
        .ts-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (min-width: 992px) {
        .ts-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }

    @media (min-width: 1200px) {
        .ts-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .ts-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 18px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        height: 100%;
        transition: .2s ease;
    }

    .ts-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        border-color: #cbd5e1;
    }

    .ts-card__top {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .ts-card__icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0f766e, #134e4a);
    }

    .ts-card__icon.is-facebook { background: linear-gradient(135deg, #1877f2, #0b5fcc); }
    .ts-card__icon.is-instagram { background: linear-gradient(135deg, #f58529, #dd2a7b 50%, #8134af); }
    .ts-card__icon.is-youtube { background: linear-gradient(135deg, #ff0000, #cc0000); }
    .ts-card__icon.is-x-twitter,
    .ts-card__icon.is-twitter { background: linear-gradient(135deg, #111827, #374151); }
    .ts-card__icon.is-linkedin,
    .ts-card__icon.is-linkedin-in { background: linear-gradient(135deg, #0a66c2, #004182); }
    .ts-card__icon.is-whatsapp { background: linear-gradient(135deg, #25d366, #128c7e); }
    .ts-card__icon.is-telegram { background: linear-gradient(135deg, #2aabee, #229ed9); }
    .ts-card__icon.is-tiktok { background: linear-gradient(135deg, #111827, #69c9d0); }
    .ts-card__icon.is-snapchat { background: linear-gradient(135deg, #fffc00, #f7e600); color: #111; }
    .ts-card__icon.is-pinterest { background: linear-gradient(135deg, #e60023, #ad081b); }
    .ts-card__icon.is-discord { background: linear-gradient(135deg, #5865f2, #404eed); }
    .ts-card__icon.is-github { background: linear-gradient(135deg, #24292f, #010409); }
    .ts-card__icon.is-threads { background: linear-gradient(135deg, #111827, #000); }
    .ts-card__icon.is-google { background: linear-gradient(135deg, #4285f4, #34a853); }

    .ts-card__meta h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .ts-card__meta p {
        margin: 4px 0 0;
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        word-break: break-all;
        line-height: 1.4;
    }

    .ts-card__badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-inline-start: auto;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ts-card__badge--on {
        background: #dcfce7;
        color: #166534;
    }

    .ts-card__badge--off {
        background: #f1f5f9;
        color: #64748b;
    }

    .ts-card__footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding-top: 4px;
        border-top: 1px dashed #e2e8f0;
    }

    .ts-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: #fee2e2;
        color: #dc2626;
        text-decoration: none !important;
    }

    .ts-icon-btn:hover {
        background: #fecaca;
        color: #b91c1c;
    }
</style>

@php
    $iconSlug = function ($row) {
        $thumb = (string) ($row->thumbnail ?? '');
        if ($thumb !== '' && !str_contains($thumb, '/') && !str_contains($thumb, '\\')) {
            return preg_replace('/^fa-brands\s+fa-/', '', $thumb);
        }
        return strtolower((string) $row->title);
    };
@endphp

<div class="admin-page" dir="rtl">
    <div class="admin-toolbar">
        <div class="admin-toolbar__meta">
            <span class="admin-toolbar__icon">
                <i class="fi-rr-following"></i>
            </span>
            <div>
                <h1 class="admin-toolbar__title">
                    {{ get_phrase('حسابات التواصل') }}
                    <span class="admin-toolbar__count">{{ $social->count() }}</span>
                </h1>
                <p class="admin-toolbar__desc">{{ get_phrase('إدارة روابط وأيقونات السوشيال ميديا في الثيم') }}</p>
            </div>
        </div>
        <div class="admin-toolbar__actions">
            @if (has_permission('theme.social.create') || has_permission('admin.theme.social.create'))
                <a href="{{ route('admin.theme.social.create') }}" class="admin-btn admin-btn--primary">
                    <span class="fi-rr-plus"></span>
                    <span>{{ get_phrase('إضافة حساب') }}</span>
                </a>
            @endif
        </div>
    </div>

    @if ($social->count() > 0)
        <div class="ts-grid">
            @foreach ($social as $row)
                @php $slug = $iconSlug($row); @endphp
                <article class="ts-card">
                    <div class="ts-card__top">
                        <div class="ts-card__icon is-{{ $slug }}">
                            <i class="fa-brands fa-{{ $slug }}"></i>
                        </div>
                        <div class="ts-card__meta">
                            <h3>{{ $row->title }}</h3>
                            <p>{{ $row->url }}</p>
                        </div>
                        @if ((int) $row->status === 1)
                            <span class="ts-card__badge ts-card__badge--on">
                                <i class="fi-rr-eye"></i> {{ get_phrase('نشط') }}
                            </span>
                        @else
                            <span class="ts-card__badge ts-card__badge--off">
                                <i class="fi-rr-eye-crossed"></i> {{ get_phrase('موقوف') }}
                            </span>
                        @endif
                    </div>
                    <div class="ts-card__footer">
                        @if (has_permission('admin.theme.social.delete') || has_permission('theme.social.delete'))
                            <a href="#"
                                onclick="confirmModal('{{ route('admin.theme.social.delete', $row->id) }}'); return false;"
                                class="ts-icon-btn"
                                data-bs-toggle="tooltip"
                                title="{{ get_phrase('حذف') }}">
                                <i class="fi-rr-trash"></i>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @else
        @include('admin.no_data')
    @endif
</div>
@endsection
