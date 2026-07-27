{{-- Dynamic brand colors from admin theme settings --}}
@php
    $colors = get_active_theme_colors();
@endphp
<style id="theme-dynamic-colors">
:root {
    --c-accent-rgb: {{ hex_to_rgb_csv($colors['accent']) }};
    --c-accent-hover-rgb: {{ hex_to_rgb_csv($colors['accent_hover']) }};
    --c-primary-rgb: {{ hex_to_rgb_csv($colors['primary']) }};
    --c-secondary-rgb: {{ hex_to_rgb_csv($colors['secondary']) }};
    --c-gray-rgb: {{ hex_to_rgb_csv($colors['gray']) }};
    --main-gradient: linear-gradient(
        to right,
        rgb(var(--c-secondary-rgb)) 0%,
        rgb(var(--c-accent-rgb)) 51%,
        rgb(var(--c-accent-hover-rgb)) 100%
    );
    --theme-accent: {{ $colors['accent'] }};
    --theme-accent-hover: {{ $colors['accent_hover'] }};
    --theme-primary: {{ $colors['primary'] }};
    --theme-secondary: {{ $colors['secondary'] }};
    --theme-gray: {{ $colors['gray'] }};
}
</style>
