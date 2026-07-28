    @if (has_permission('admin.theme.settings'))
        <li
            class="sidebar-second-li {{ $current_route == 'admin.theme.settings' ? 'active' : '' }}">
            <a
                href="{{ route('admin.theme.settings') }}">{{ get_phrase('إعدادات القالب') }}</a>
        </li>
    @endif




    @if (has_permission('admin.theme.settings.social'))
        <li
            class="sidebar-second-li {{ $current_route == 'admin.theme.social' ? 'active' : '' }}">
            <a
                href="{{ route('admin.theme.social') }}">{{ get_phrase('حسابات التواصل') }}</a>
        </li>
    @endif



    
    @if (has_permission('admin.theme.settings.feature'))
        <li
            class="sidebar-second-li {{ $current_route == 'admin.theme.feature' ? 'active' : '' }}">
            <a
                href="{{ route('admin.theme.feature') }}">{{ get_phrase('مميزات القالب') }}</a>
        </li>
    @endif

    @if (has_permission('admin.theme.settings'))
        <li
            class="sidebar-second-li {{ $current_route == 'admin.theme.legal' ? 'active' : '' }}">
            <a href="{{ route('admin.theme.legal') }}">{{ get_phrase('الشروط والخصوصية') }}</a>
        </li>
    @endif
