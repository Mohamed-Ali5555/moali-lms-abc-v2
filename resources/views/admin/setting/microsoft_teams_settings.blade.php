@extends('layouts.admin')
@push('title', get_phrase('Microsoft Teams Integration'))
@section('content')
    @php
        $activeTab = session('tab', request('tab', 'settings'));
        $tenantId = get_settings('teams_tenant_id');
        $clientId = get_settings('teams_client_id');
        $clientSecret = get_settings('teams_client_secret');
        $organizerEmail = get_settings('teams_organizer_email');
        $isConfigured = $tenantId && $clientId && $clientSecret && $organizerEmail;
    @endphp

    <div class="admin-page wp-page">
        <div class="wp-hero">
            <div class="wp-hero__content">
                <p class="wp-hero__eyebrow">{{ get_phrase('Live Class Integration') }}</p>
                <h1 class="wp-hero__title">{{ get_phrase('تكامل Microsoft Teams') }}</h1>
                <p class="wp-hero__desc">
                    {{ get_phrase('اربط النظام بحساب Microsoft 365 لإنشاء اجتماعات Teams تلقائياً للحصص المباشرة في البوت كامب بدلاً من Zoom') }}
                </p>
                <div class="wp-hero__chips">
                    <span class="wp-chip {{ $isConfigured ? 'is-on' : 'is-off' }}">
                        <i class="{{ $isConfigured ? 'fi-rr-check' : 'fi-rr-cross' }}"></i>
                        {{ $isConfigured ? get_phrase('مفعّل ومهيّأ') : get_phrase('غير مهيّأ بعد') }}
                    </span>
                    @if ($organizerEmail)
                        <span class="wp-chip">
                            <i class="fi-rr-user"></i>
                            {{ $organizerEmail }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="wp-hero__aside">
                <div class="wp-hero__badge">
                    <i class="fi-rr-video-camera-alt"></i>
                    <strong>Microsoft Teams</strong>
                    <span>Graph API</span>
                </div>
            </div>
        </div>

        <div class="wp-shell">
            <nav class="wp-tabs nav" role="tablist">
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'settings' ? 'active is-active' : '' }}"
                    data-wp-target="#teams-settings" role="tab"
                    aria-controls="teams-settings" aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}">
                    <i class="fi-rr-settings"></i>
                    <span>{{ get_phrase('الإعدادات') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'steps' ? 'active is-active' : '' }}"
                    data-wp-target="#teams-steps" role="tab"
                    aria-controls="teams-steps" aria-selected="{{ $activeTab === 'steps' ? 'true' : 'false' }}">
                    <i class="fi-rr-list-check"></i>
                    <span>{{ get_phrase('خطوات الربط') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'test' ? 'active is-active' : '' }}"
                    data-wp-target="#teams-test" role="tab"
                    aria-controls="teams-test" aria-selected="{{ $activeTab === 'test' ? 'true' : 'false' }}">
                    <i class="fi-rr-paper-plane"></i>
                    <span>{{ get_phrase('اختبار الاتصال') }}</span>
                </button>
            </nav>

            <div class="tab-content wp-content">
                {{-- Settings --}}
                <div class="tab-pane fade {{ $activeTab === 'settings' ? 'show active' : '' }}" id="teams-settings"
                    role="tabpanel">
                    <form class="required-form wp-panel" action="{{ route('admin.teams.settings.update') }}" method="post">
                        @csrf
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('بيانات اتصال Azure AD') }}</h2>
                                <p>{{ get_phrase('بيانات تطبيق Azure Active Directory المسجّل للوصول إلى Microsoft Graph API') }}</p>
                            </div>
                        </div>

                        <div class="wp-grid">
                            <div class="wp-field wp-field--full">
                                <label class="form-label ol-form-label" for="teams_tenant_id">{{ get_phrase('Tenant ID (Directory ID)') }}<span class="required">*</span></label>
                                <input type="text" name="teams_tenant_id" id="teams_tenant_id" class="form-control ol-form-control" dir="ltr" value="{{ old('teams_tenant_id', $tenantId) }}" placeholder="00000000-0000-0000-0000-000000000000" required>
                                <small class="wp-help">{{ get_phrase('معرّف المستأجر (Directory ID) من Azure Active Directory') }}</small>
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label" for="teams_client_id">{{ get_phrase('Client ID (Application ID)') }}<span class="required">*</span></label>
                                <input type="text" name="teams_client_id" id="teams_client_id" class="form-control ol-form-control" dir="ltr" value="{{ old('teams_client_id', $clientId) }}" placeholder="00000000-0000-0000-0000-000000000000" required>
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label" for="teams_client_secret">{{ get_phrase('Client Secret') }}<span class="required">*</span></label>
                                <input type="text" name="teams_client_secret" id="teams_client_secret" class="form-control ol-form-control" dir="ltr" value="{{ old('teams_client_secret', $clientSecret) }}" placeholder="{{ get_phrase('قيمة الـ Secret (Value) وليس الـ Secret ID') }}" required>
                            </div>

                            <div class="wp-field wp-field--full">
                                <label class="form-label ol-form-label" for="teams_organizer_email">{{ get_phrase('Organizer Email (UPN)') }}<span class="required">*</span></label>
                                <input type="email" name="teams_organizer_email" id="teams_organizer_email" class="form-control ol-form-control" dir="ltr" value="{{ old('teams_organizer_email', $organizerEmail) }}" placeholder="host@yourcompany.com" required>
                                <small class="wp-help">{{ get_phrase('بريد حساب Microsoft 365 الذي سيتم إنشاء اجتماعات Teams باسمه كمنظّم (يجب أن يملك ترخيص Teams، راجع تبويب "خطوات الربط")') }}</small>
                            </div>
                        </div>

                        <div class="wp-panel__footer">
                            <span class="wp-hint">
                                <i class="fi-rr-info"></i>
                                {{ get_phrase('سيتم استخدام هذه البيانات لإنشاء وتحديث وحذف اجتماعات Teams للحصص المباشرة في البوت كامب') }}
                            </span>
                            <button type="submit" class="admin-btn admin-btn--primary">
                                <i class="fi-rr-disk"></i>
                                <span>{{ get_phrase('حفظ الإعدادات') }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Steps --}}
                <div class="tab-pane fade {{ $activeTab === 'steps' ? 'show active' : '' }}" id="teams-steps" role="tabpanel">
                    <div class="wp-panel">
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('خطوات ربط Microsoft Teams خطوة بخطوة') }}</h2>
                                <p>{{ get_phrase('اتبع هذه الخطوات في Azure Portal و Microsoft Teams Admin Center قبل حفظ الإعدادات') }}</p>
                            </div>
                        </div>

                        <div class="ts-steps">
                            <div class="ts-step">
                                <span class="ts-step__num">1</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('تسجيل تطبيق جديد في Azure Active Directory') }}</strong>
                                    <p>
                                        {{ get_phrase('ادخل إلى') }} <a href="https://portal.azure.com" target="_blank" rel="noopener">portal.azure.com</a>
                                        ← <code>Azure Active Directory</code> ← <code>App registrations</code> ← <code>New registration</code>.
                                        {{ get_phrase('أعطِ التطبيق اسماً (مثال: LMS Teams Integration) ثم اضغط Register.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ts-step">
                                <span class="ts-step__num">2</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('نسخ Tenant ID و Client ID') }}</strong>
                                    <p>
                                        {{ get_phrase('من صفحة Overview الخاصة بالتطبيق، انسخ') }}
                                        <code>Directory (tenant) ID</code> {{ get_phrase('و') }} <code>Application (client) ID</code>
                                        {{ get_phrase('وضعهما في حقلي Tenant ID و Client ID بجانب.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ts-step">
                                <span class="ts-step__num">3</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('إنشاء Client Secret') }}</strong>
                                    <p>
                                        {{ get_phrase('من القائمة الجانبية') }} <code>Certificates & secrets</code> ←
                                        <code>New client secret</code> {{ get_phrase('ثم انسخ قيمة الـ Value فوراً (تظهر مرة واحدة فقط) وضعها في حقل Client Secret.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ts-step">
                                <span class="ts-step__num">4</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('إضافة صلاحيات Microsoft Graph') }}</strong>
                                    <p>
                                        {{ get_phrase('من القائمة الجانبية') }} <code>API permissions</code> ← <code>Add a permission</code> ←
                                        <code>Microsoft Graph</code> ← <code>Application permissions</code>
                                        {{ get_phrase('وأضف الصلاحية') }} <code>OnlineMeetings.ReadWrite.All</code>
                                        {{ get_phrase('ثم اضغط') }} <code>Grant admin consent</code> {{ get_phrase('لتفعيلها.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ts-step">
                                <span class="ts-step__num">5</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('منح التطبيق صلاحية إنشاء اجتماعات باسم المستخدم (Application Access Policy)') }}</strong>
                                    <p>
                                        {{ get_phrase('هذه الخطوة إلزامية من مايكروسوفت حتى يستطيع التطبيق إنشاء اجتماعات نيابة عن مستخدم. شغّل الأوامر التالية في') }}
                                        <code>Microsoft Teams PowerShell</code> {{ get_phrase('(بعد تثبيت موديول MicrosoftTeams وتسجيل الدخول بحساب Admin عبر Connect-MicrosoftTeams):') }}
                                    </p>
                                    <pre class="ts-code">New-CsApplicationAccessPolicy -Identity TeamsMeetingPolicy -AppIds "{Client-ID}" -Description "LMS Teams Integration"
Grant-CsApplicationAccessPolicy -PolicyName TeamsMeetingPolicy -Identity "{organizer-email@yourcompany.com}"</pre>
                                    <p>
                                        {{ get_phrase('استبدل {Client-ID} بمعرّف التطبيق و {organizer-email} ببريد الحساب المُنظِّم الذي أدخلته في حقل Organizer Email، وتأكد أن هذا الحساب يملك ترخيص Microsoft Teams سارياً.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="ts-step">
                                <span class="ts-step__num">6</span>
                                <div class="ts-step__body">
                                    <strong>{{ get_phrase('حفظ الإعدادات واختبار الاتصال') }}</strong>
                                    <p>
                                        {{ get_phrase('ارجع لتبويب "الإعدادات"، أدخل البيانات الأربعة واضغط "حفظ الإعدادات"، ثم افتح تبويب "اختبار الاتصال" للتأكد من صحة البيانات قبل إنشاء أي حصة مباشرة.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Test --}}
                <div class="tab-pane fade {{ $activeTab === 'test' ? 'show active' : '' }}" id="teams-test" role="tabpanel">
                    <form action="{{ route('admin.teams.settings.test') }}" method="post" class="wp-panel wp-panel--test">
                        @csrf
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('اختبار الاتصال بـ Microsoft Graph') }}</h2>
                                <p>{{ get_phrase('يقوم هذا الاختبار بمحاولة توليد Access Token فقط، دون إنشاء أي اجتماع فعلي') }}</p>
                            </div>
                        </div>

                        <div class="wp-test-layout">
                            <div class="wp-test-form">
                                <p class="wp-help">
                                    {{ get_phrase('تأكد من حفظ الإعدادات أولاً، ثم اضغط الزر أدناه للتحقق من صحة Tenant ID و Client ID و Client Secret.') }}
                                </p>
                                <button type="submit" class="tf-btn tf-btn--primary">
                                    <i class="fi-rr-plug"></i>
                                    {{ get_phrase('اختبار الاتصال الآن') }}
                                </button>
                            </div>
                            <aside class="wp-test-aside">
                                <div class="wp-tip">
                                    <i class="fi-rr-info"></i>
                                    <div>
                                        <strong>{{ get_phrase('نصيحة') }}</strong>
                                        <p>{{ get_phrase('إذا فشل الاختبار، تأكد من صحة الـ Client Secret (استخدم Value وليس Secret ID) ومن منح موافقة الأدمن على صلاحية OnlineMeetings.ReadWrite.All') }}</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .ts-steps { display: flex; flex-direction: column; gap: 14px; }
        .ts-step { display: flex; align-items: flex-start; gap: 14px; padding: 14px; border-radius: 14px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .ts-step__num { flex-shrink: 0; width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #4b53bc, #6264a7); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; }
        .ts-step__body strong { display: block; margin-bottom: 6px; font-size: 14px; color: var(--darkColor, #0f172a); }
        .ts-step__body p { margin: 0 0 8px; font-size: 13px; line-height: 1.8; color: var(--grayColor, #64748b); }
        .ts-step__body code { background: #eef2ff; color: #4338ca; padding: 1px 6px; border-radius: 6px; font-size: 12px; direction: ltr; display: inline-block; }
        .ts-code { direction: ltr; text-align: left; background: #0f172a; color: #e2e8f0; padding: 12px 14px; border-radius: 10px; font-size: 12px; overflow-x: auto; margin: 8px 0; }
    </style>
@endsection

@push('js')
<script>
(function () {
    var tabRoot = document.querySelector('.wp-shell');
    if (!tabRoot) return;

    function syncWpTabs(activeBtn) {
        var target = activeBtn && activeBtn.getAttribute('data-wp-target');
        tabRoot.querySelectorAll('.wp-tab').forEach(function (el) {
            var on = el === activeBtn;
            el.classList.toggle('active', on);
            el.classList.toggle('is-active', on);
            el.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        tabRoot.querySelectorAll('.wp-content > .tab-pane').forEach(function (pane) {
            var on = target && ('#' + pane.id) === target;
            pane.classList.toggle('active', on);
            pane.classList.toggle('show', on);
        });
    }

    tabRoot.querySelectorAll('.wp-tab').forEach(function (tab) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();
            syncWpTabs(tab);
        });
    });

    var initial = tabRoot.querySelector('.wp-tab.active, .wp-tab.is-active') || tabRoot.querySelector('.wp-tab');
    if (initial) {
        syncWpTabs(initial);
    }
})();
</script>
@endpush
