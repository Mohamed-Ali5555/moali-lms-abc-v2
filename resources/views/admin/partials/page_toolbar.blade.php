{{--
  Reusable admin page toolbar.
  @param string $title
  @param string|null $description
  @param string|null $icon (uicons class, default fi-rr-apps)
  @param int|string|null $count
  @param string|null $actionsSlot HTML for right-side actions (passed via $slot or $actions)
--}}
@php
    $icon = $icon ?? 'fi-rr-apps';
@endphp
<div class="admin-toolbar">
    <div class="admin-toolbar__meta">
        <span class="admin-toolbar__icon">
            <i class="{{ $icon }}"></i>
        </span>
        <div>
            <h1 class="admin-toolbar__title">
                {{ $title }}
                @isset($count)
                    <span class="admin-toolbar__count">{{ $count }}</span>
                @endisset
            </h1>
            @isset($description)
                <p class="admin-toolbar__desc">{{ $description }}</p>
            @endisset
        </div>
    </div>
    @if (!empty($actions))
        <div class="admin-toolbar__actions">
            {!! $actions !!}
        </div>
    @endif
</div>
