@extends('layouts.admin')
@push('title', get_phrase('WaPilot Integration'))
@section('content')
    @php
        $activeTab = session('tab', request('tab', 'settings'));
        $isEnabled = get_settings('wapilot_enabled') == '1';
        $apiUrl = get_settings('wapilot_api_url') ?: 'https://api.wapilot.net';
        $templatesCount = $templates->count();
        $activeTemplates = $templates->where('is_active', 1)->count();
        $logsCount = $logs->count();
        $successLogs = $logs->where('status', 'success')->count();
        $failedLogs = $logs->where('status', 'failed')->count();
    @endphp

    <div class="admin-page wp-page">
        <div class="wp-hero">
            <div class="wp-hero__content">
                <p class="wp-hero__eyebrow">{{ get_phrase('WhatsApp Integration') }}</p>
                <h1 class="wp-hero__title">{{ get_phrase('WaPilot Integration') }}</h1>
                <p class="wp-hero__desc">
                    {{ get_phrase('اربط النظام بـ WaPilot لإرسال إشعارات واتساب للطلبة وأولياء الأمور تلقائياً') }}
                </p>
                <div class="wp-hero__chips">
                    <span class="wp-chip {{ $isEnabled ? 'is-on' : 'is-off' }}">
                        <i class="{{ $isEnabled ? 'fi-rr-check' : 'fi-rr-cross' }}"></i>
                        {{ $isEnabled ? get_phrase('مفعّل') : get_phrase('موقوف') }}
                    </span>
                    <span class="wp-chip">
                        <i class="fi-rr-apps"></i>
                        {{ $activeTemplates }}/{{ $templatesCount }} {{ get_phrase('قوالب نشطة') }}
                    </span>
                    <span class="wp-chip">
                        <i class="fi-rr-time-past"></i>
                        {{ $logsCount }} {{ get_phrase('سجل حديث') }}
                    </span>
                </div>
            </div>
            <div class="wp-hero__aside">
                <div class="wp-hero__badge">
                    <i class="fi-rr-comment-alt"></i>
                    <strong>WaPilot</strong>
                    <span>{{ get_phrase('WhatsApp API') }}</span>
                </div>
                <div class="wp-hero__stats">
                    <div>
                        <strong>{{ $successLogs }}</strong>
                        <span>{{ get_phrase('نجاح') }}</span>
                    </div>
                    <div>
                        <strong>{{ $failedLogs }}</strong>
                        <span>{{ get_phrase('فشل') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="wp-shell">
            <nav class="wp-tabs nav" role="tablist">
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'settings' ? 'active is-active' : '' }}"
                    data-wp-target="#wapilot-settings" role="tab"
                    aria-controls="wapilot-settings" aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}">
                    <i class="fi-rr-settings"></i>
                    <span>{{ get_phrase('إعدادات API') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'templates' ? 'active is-active' : '' }}"
                    data-wp-target="#wapilot-templates" role="tab"
                    aria-controls="wapilot-templates" aria-selected="{{ $activeTab === 'templates' ? 'true' : 'false' }}">
                    <i class="fi-rr-edit"></i>
                    <span>{{ get_phrase('القوالب') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'broadcast' ? 'active is-active' : '' }}"
                    data-wp-target="#wapilot-broadcast" role="tab"
                    aria-controls="wapilot-broadcast" aria-selected="{{ $activeTab === 'broadcast' ? 'true' : 'false' }}">
                    <i class="fi-rr-megaphone"></i>
                    <span>{{ get_phrase('إرسال جماعي') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'test' ? 'active is-active' : '' }}"
                    data-wp-target="#wapilot-test" role="tab"
                    aria-controls="wapilot-test" aria-selected="{{ $activeTab === 'test' ? 'true' : 'false' }}">
                    <i class="fi-rr-paper-plane"></i>
                    <span>{{ get_phrase('اختبار الإرسال') }}</span>
                </button>
                <button type="button"
                    class="wp-tab nav-link {{ $activeTab === 'logs' ? 'active is-active' : '' }}"
                    data-wp-target="#wapilot-logs" role="tab"
                    aria-controls="wapilot-logs" aria-selected="{{ $activeTab === 'logs' ? 'true' : 'false' }}">
                    <i class="fi-rr-time-past"></i>
                    <span>{{ get_phrase('السجلات') }}</span>
                </button>
            </nav>

            <div class="tab-content wp-content">
                {{-- Settings --}}
                <div class="tab-pane fade {{ $activeTab === 'settings' ? 'show active' : '' }}" id="wapilot-settings"
                    role="tabpanel">
                    <form action="{{ route('admin.wapilot.settings.update') }}" method="post" class="wp-panel">
                        @csrf

                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('اتصال WaPilot') }}</h2>
                                <p>{{ get_phrase('بيانات الـ API ومفتاح الإرسال وكود الدولة') }}</p>
                            </div>
                        </div>

                        <label class="wp-enable" for="wapilot_enabled">
                            <span class="wp-enable__icon"><i class="fi-rr-comment-alt"></i></span>
                            <span class="wp-enable__text">
                                <strong>{{ get_phrase('تفعيل WhatsApp عبر WaPilot') }}</strong>
                                <small>{{ get_phrase('عند الإيقاف لن يتم إرسال أي رسائل تلقائية') }}</small>
                            </span>
                            <span class="wp-enable__switch">
                                <input class="wp-switch-input" type="checkbox" name="wapilot_enabled" value="1"
                                    id="wapilot_enabled" {{ $isEnabled ? 'checked' : '' }}>
                                <span class="wp-switch-ui"></span>
                            </span>
                        </label>

                        <div class="wp-grid">
                            <div class="wp-field wp-field--full">
                                <label class="form-label ol-form-label">{{ get_phrase('API Base URL') }}</label>
                                <input type="text" name="wapilot_api_url" class="form-control ol-form-control"
                                    value="{{ $apiUrl }}" placeholder="https://api.wapilot.net">
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label">{{ get_phrase('Send Path') }}</label>
                                <input type="text" name="wapilot_send_path" class="form-control ol-form-control"
                                    value="{{ get_settings('wapilot_send_path') ?: '/api/send' }}"
                                    placeholder="/api/send">
                                <small class="wp-help">{{ get_phrase('Relative path or full URL for the send-message endpoint') }}</small>
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label">{{ get_phrase('Default Country Code') }}</label>
                                <input type="text" name="wapilot_default_country_code"
                                    class="form-control ol-form-control"
                                    value="{{ get_settings('wapilot_default_country_code') ?: '20' }}">
                                <small class="wp-help">{{ get_phrase('Used to normalize local numbers (e.g. 20 for Egypt)') }}</small>
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label">{{ get_phrase('API Key') }}</label>
                                <input type="password" name="wapilot_api_key" class="form-control ol-form-control"
                                    value="{{ get_settings('wapilot_api_key') ?: '' }}"
                                    onfocus="this.type='text'" onblur="this.type='password'"
                                    autocomplete="new-password">
                            </div>

                            <div class="wp-field">
                                <label class="form-label ol-form-label">{{ get_phrase('Sender Number') }}</label>
                                <input type="text" name="wapilot_sender" class="form-control ol-form-control"
                                    value="{{ get_settings('wapilot_sender') ?: '' }}"
                                    placeholder="{{ get_phrase('Optional') }}">
                            </div>
                        </div>

                        <div class="wp-panel__footer">
                            <span class="wp-hint">{{ get_phrase('احفظ الإعدادات قبل تجربة الإرسال') }}</span>
                            <button type="submit" class="tf-btn tf-btn--primary">
                                <i class="fi-rr-check"></i>
                                {{ get_phrase('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Templates --}}
                <div class="tab-pane fade {{ $activeTab === 'templates' ? 'show active' : '' }}" id="wapilot-templates"
                    role="tabpanel">
                    <div class="wp-panel">
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('قوالب الرسائل') }}</h2>
                                <p>{{ get_phrase('حدّد نص كل إشعار ولمن يُرسل (طالب / ولي أمر)') }}</p>
                            </div>
                        </div>

                        @forelse ($templates as $template)
                            <form action="{{ route('admin.wapilot.template.update', $template->id) }}" method="post"
                                class="wp-template">
                                @csrf
                                <div class="wp-template__head">
                                    <div class="wp-template__identity">
                                        <span class="wp-template__icon"><i class="fi-rr-comment"></i></span>
                                        <div>
                                            <strong>{{ $template->title }}</strong>
                                            <code>{{ $template->event_key }}</code>
                                        </div>
                                    </div>
                                    <div class="wp-template__toggles">
                                        <label class="wp-mini-toggle" for="active_{{ $template->id }}">
                                            <input type="checkbox" name="is_active" value="1"
                                                id="active_{{ $template->id }}"
                                                {{ $template->is_active ? 'checked' : '' }}>
                                            <span>{{ get_phrase('Active') }}</span>
                                        </label>
                                        <label class="wp-mini-toggle" for="student_{{ $template->id }}">
                                            <input type="checkbox" name="send_to_student" value="1"
                                                id="student_{{ $template->id }}"
                                                {{ $template->send_to_student ? 'checked' : '' }}>
                                            <span>{{ get_phrase('Student') }}</span>
                                        </label>
                                        <label class="wp-mini-toggle" for="parent_{{ $template->id }}">
                                            <input type="checkbox" name="send_to_parent" value="1"
                                                id="parent_{{ $template->id }}"
                                                {{ $template->send_to_parent ? 'checked' : '' }}>
                                            <span>{{ get_phrase('Parent') }}</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="wp-grid">
                                    <div class="wp-field wp-field--full">
                                        <label class="form-label ol-form-label">{{ get_phrase('Title') }}</label>
                                        <input type="text" name="title" class="form-control ol-form-control"
                                            value="{{ $template->title }}">
                                    </div>
                                    <div class="wp-field wp-field--full">
                                        <label class="form-label ol-form-label">{{ get_phrase('Message Body') }}</label>
                                        <textarea name="body" rows="5"
                                            class="form-control ol-form-control wp-template__body">{{ $template->body }}</textarea>
                                        @if ($template->placeholders_hint)
                                            <small class="wp-help">{{ $template->placeholders_hint }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="wp-template__footer">
                                    <button type="submit" class="tf-btn tf-btn--primary">
                                        <i class="fi-rr-disk"></i>
                                        {{ get_phrase('Update Template') }}
                                    </button>
                                </div>
                            </form>
                        @empty
                            <div class="wp-empty">
                                <i class="fi-rr-folder-open"></i>
                                <strong>{{ get_phrase('No templates found. Run migrations.') }}</strong>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Broadcast --}}
                <div class="tab-pane fade {{ $activeTab === 'broadcast' ? 'show active' : '' }}" id="wapilot-broadcast"
                    role="tabpanel">
                    @php
                        $categories = $categories ?? collect();
                        $courses = $courses ?? collect();
                    @endphp
                    <form action="{{ route('admin.wapilot.broadcast') }}" method="post" class="wp-panel" id="wpBroadcastForm">
                        @csrf

                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('إرسال جماعي مباشر') }}</h2>
                                <p>{{ get_phrase('أرسل رسالة مرة واحدة لطلبة سنة دراسية أو كورس معيّن') }}</p>
                            </div>
                        </div>

                        <div class="wp-broadcast">
                            <div class="wp-broadcast__main">
                                <div class="wp-seg" role="group" aria-label="{{ get_phrase('نوع الجمهور') }}">
                                    <label class="wp-seg__btn">
                                        <input type="radio" name="audience_type" value="category" checked>
                                        <span><i class="fi-rr-apps"></i> {{ get_phrase('حسب السنة الدراسية') }}</span>
                                    </label>
                                    <label class="wp-seg__btn">
                                        <input type="radio" name="audience_type" value="course">
                                        <span><i class="fi-rr-e-learning"></i> {{ get_phrase('حسب الكورس') }}</span>
                                    </label>
                                </div>

                                <div class="wp-field" id="wpAudienceCategoryWrap">
                                    <label class="form-label ol-form-label" for="wp_category_id">
                                        {{ get_phrase('السنة الدراسية') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control ol-form-control ol-select2" id="wp_category_id"
                                        data-audience="category">
                                        <option value="">{{ get_phrase('اختر السنة') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="wp-field" id="wpAudienceCourseWrap" hidden>
                                    <label class="form-label ol-form-label" for="wp_course_id">
                                        {{ get_phrase('الكورس') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control ol-form-control ol-select2" id="wp_course_id"
                                        data-audience="course">
                                        <option value="">{{ get_phrase('اختر الكورس') }}</option>
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="hidden" name="audience_id" id="wp_audience_id" value="">

                                <div class="wp-template__toggles wp-broadcast__targets">
                                    <label class="wp-mini-toggle" for="broadcast_student">
                                        <input type="checkbox" name="send_to_student" value="1" id="broadcast_student" checked>
                                        <span>{{ get_phrase('الطلبة') }}</span>
                                    </label>
                                    <label class="wp-mini-toggle" for="broadcast_parent">
                                        <input type="checkbox" name="send_to_parent" value="1" id="broadcast_parent">
                                        <span>{{ get_phrase('أولياء الأمور') }}</span>
                                    </label>
                                </div>

                                <div class="wp-field">
                                    <label class="form-label ol-form-label" for="broadcast_title">
                                        {{ get_phrase('عنوان الرسالة') }}
                                    </label>
                                    <input type="text" name="title" id="broadcast_title"
                                        class="form-control ol-form-control"
                                        placeholder="{{ get_phrase('مثال: تنويه هام لأولياء الأمور') }}"
                                        maxlength="180">
                                </div>

                                <div class="wp-field">
                                    <label class="form-label ol-form-label" for="broadcast_body">
                                        {{ get_phrase('نص الرسالة') }} <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="body" id="broadcast_body" rows="7"
                                        class="form-control ol-form-control wp-template__body"
                                        placeholder="{{ get_phrase('اكتب نص الرسالة هنا...') }}"
                                        required maxlength="2000"></textarea>
                                    <small class="wp-help">
                                        {{ get_phrase('متغيرات متاحة') }}:
                                        <code>[student_name]</code>
                                        <code>[category_title]</code>
                                        <code>[course_title]</code>
                                        <code>[system_name]</code>
                                        <code>[title]</code>
                                    </small>
                                </div>

                                <div class="wp-broadcast__confirm">
                                    <label class="wp-confirm-check">
                                        <input type="checkbox" name="confirm" value="1" id="broadcast_confirm" required>
                                        <span>{{ get_phrase('أؤكد أنني راجعت الجمهور والنص وأريد الإرسال الآن') }}</span>
                                    </label>
                                </div>

                                <div class="wp-panel__footer">
                                    <span class="wp-hint">{{ get_phrase('الرسائل تُجدول في الطابور وتظهر في السجلات') }}</span>
                                    <button type="submit" class="tf-btn tf-btn--primary" id="wpBroadcastSubmit" disabled>
                                        <i class="fi-rr-megaphone"></i>
                                        {{ get_phrase('إرسال الآن') }}
                                    </button>
                                </div>
                            </div>

                            <aside class="wp-broadcast__side">
                                <div class="wp-audience" id="wpAudiencePreview">
                                    <div class="wp-audience__head">
                                        <strong>{{ get_phrase('معاينة الجمهور') }}</strong>
                                        <span id="wpAudienceHint">{{ get_phrase('اختر السنة أو الكورس') }}</span>
                                    </div>
                                    <div class="wp-audience__stats">
                                        <div>
                                            <strong id="wpCountStudents">0</strong>
                                            <span>{{ get_phrase('طالب') }}</span>
                                        </div>
                                        <div>
                                            <strong id="wpCountPhones">0</strong>
                                            <span>{{ get_phrase('برقم هاتف') }}</span>
                                        </div>
                                        <div>
                                            <strong id="wpCountParents">0</strong>
                                            <span>{{ get_phrase('ولي أمر') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="wp-tip">
                                    <i class="fi-rr-info"></i>
                                    <div>
                                        <strong>{{ get_phrase('كيف يعمل؟') }}</strong>
                                        <p>{{ get_phrase('العنوان يظهر أعلى الرسالة، والنص يُرسل لكل مستلم مع استبدال المتغيرات تلقائياً') }}</p>
                                    </div>
                                </div>

                                <div class="wp-tip wp-tip--soft">
                                    <i class="fi-rr-bulb"></i>
                                    <div>
                                        <strong>{{ get_phrase('مثال') }}</strong>
                                        <p>مرحباً [student_name]، تنويه بخصوص [category_title].</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>
                </div>

                {{-- Test --}}
                <div class="tab-pane fade {{ $activeTab === 'test' ? 'show active' : '' }}" id="wapilot-test"
                    role="tabpanel">
                    <form action="{{ route('admin.wapilot.test') }}" method="post" class="wp-panel wp-panel--test">
                        @csrf
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('إرسال تجريبي') }}</h2>
                                <p>{{ get_phrase('أرسل رسالة واتساب فورية للتأكد من صحة الإعدادات') }}</p>
                            </div>
                        </div>

                        <div class="wp-test-layout">
                            <div class="wp-test-form">
                                <div class="wp-field">
                                    <label class="form-label ol-form-label">{{ get_phrase('Phone Number') }}</label>
                                    <input type="text" name="test_phone" class="form-control ol-form-control"
                                        placeholder="01xxxxxxxxx" required>
                                </div>
                                <div class="wp-field">
                                    <label class="form-label ol-form-label">{{ get_phrase('Message') }}</label>
                                    <textarea name="test_message" rows="5" class="form-control ol-form-control"
                                        placeholder="{{ get_phrase('Optional custom test message') }}"></textarea>
                                </div>
                                <button type="submit" class="tf-btn tf-btn--primary">
                                    <i class="fi-rr-paper-plane"></i>
                                    {{ get_phrase('Send Test Message') }}
                                </button>
                            </div>
                            <aside class="wp-test-aside">
                                <div class="wp-tip">
                                    <i class="fi-rr-info"></i>
                                    <div>
                                        <strong>{{ get_phrase('نصيحة') }}</strong>
                                        <p>{{ get_phrase('تأكد أن التكامل مفعّل وأن مفتاح API صحيح قبل الإرسال') }}</p>
                                    </div>
                                </div>
                                <div class="wp-tip wp-tip--soft">
                                    <i class="fi-rr-phone-call"></i>
                                    <div>
                                        <strong>{{ get_phrase('صيغة الرقم') }}</strong>
                                        <p>{{ get_phrase('يمكن إدخال الرقم المحلي وسيتم تطبيعه بكود الدولة الافتراضي') }}</p>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </form>
                </div>

                {{-- Logs --}}
                <div class="tab-pane fade {{ $activeTab === 'logs' ? 'show active' : '' }}" id="wapilot-logs"
                    role="tabpanel">
                    <div class="wp-panel">
                        <div class="wp-panel__head">
                            <div>
                                <h2>{{ get_phrase('سجلات الإرسال') }}</h2>
                                <p>{{ get_phrase('آخر العمليات المرسلة عبر WaPilot') }}</p>
                            </div>
                        </div>

                        <div class="table-responsive wp-logs">
                            <table class="table eTable eTable-2 wp-logs__table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ get_phrase('Event') }}</th>
                                        <th>{{ get_phrase('Recipient') }}</th>
                                        <th>{{ get_phrase('Phone') }}</th>
                                        <th>{{ get_phrase('Status') }}</th>
                                        <th>{{ get_phrase('Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td><span class="wp-logs__id">{{ $log->id }}</span></td>
                                            <td><code class="wp-logs__event">{{ $log->event_key }}</code></td>
                                            <td>{{ $log->recipient_type }}</td>
                                            <td dir="ltr">{{ $log->phone }}</td>
                                            <td>
                                                <span
                                                    class="wp-log-status wp-log-status--{{ $log->status === 'success' ? 'ok' : ($log->status === 'failed' ? 'fail' : 'muted') }}">
                                                    {{ $log->status }}
                                                </span>
                                            </td>
                                            <td>{{ $log->created_at }}</td>
                                        </tr>
                                        @if ($log->response)
                                            <tr class="wp-logs__response-row">
                                                <td colspan="6">
                                                    <div class="wp-logs__response">
                                                        {{ \Illuminate\Support\Str::limit($log->response, 300) }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="wp-empty wp-empty--table">
                                                    <i class="fi-rr-time-past"></i>
                                                    <strong>{{ get_phrase('No logs yet') }}</strong>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    // Keep visual state correct on first paint
    var initial = tabRoot.querySelector('.wp-tab.active, .wp-tab.is-active') || tabRoot.querySelector('.wp-tab');
    if (initial) {
        syncWpTabs(initial);
    }

    var previewUrl = @json(route('admin.wapilot.broadcast.preview'));
    var $form = $('#wpBroadcastForm');
    if (!$form.length) return;

    var $typeRadios = $form.find('input[name="audience_type"]');
    var $categoryWrap = $('#wpAudienceCategoryWrap');
    var $courseWrap = $('#wpAudienceCourseWrap');
    var $category = $('#wp_category_id');
    var $course = $('#wp_course_id');
    var $audienceId = $('#wp_audience_id');
    var $submit = $('#wpBroadcastSubmit');
    var $hint = $('#wpAudienceHint');
    var req = null;
    var phrases = {
        pick: @json(get_phrase('اختر السنة أو الكورس')),
        loading: @json(get_phrase('جاري حساب العدد...')),
        error: @json(get_phrase('تعذر تحميل المعاينة')),
        ready: @json(get_phrase('جاهز للإرسال')),
    };

    function currentType() {
        return $form.find('input[name="audience_type"]:checked').val() || 'category';
    }

    function syncAudienceType() {
        var type = currentType();
        $categoryWrap.prop('hidden', type !== 'category');
        $courseWrap.prop('hidden', type !== 'course');

        if (type === 'category') {
            $course.val('').trigger('change.select2');
            $audienceId.val($category.val() || '');
        } else {
            $category.val('').trigger('change.select2');
            $audienceId.val($course.val() || '');
        }

        loadPreview();
        refreshSubmit();
    }

    function loadPreview() {
        var type = currentType();
        var id = $audienceId.val();

        if (!id) {
            $('#wpCountStudents, #wpCountPhones, #wpCountParents').text('0');
            $hint.text(phrases.pick);
            return;
        }

        $hint.text(phrases.loading);
        if (req) req.abort();

        req = $.get(previewUrl, { audience_type: type, audience_id: id })
            .done(function (res) {
                if (!res || !res.ok) {
                    $hint.text(phrases.error);
                    return;
                }
                $('#wpCountStudents').text(res.counts.students || 0);
                $('#wpCountPhones').text(res.counts.with_phone || 0);
                $('#wpCountParents').text(res.counts.with_parent_phone || 0);
                $hint.text(phrases.ready);
                refreshSubmit();
            })
            .fail(function (xhr) {
                if (xhr.statusText === 'abort') return;
                $hint.text(phrases.error);
            });
    }

    function refreshSubmit() {
        var hasAudience = !!$audienceId.val();
        var hasBody = $.trim($('#broadcast_body').val() || '').length > 0;
        var hasTarget = $('#broadcast_student').is(':checked') || $('#broadcast_parent').is(':checked');
        var confirmed = $('#broadcast_confirm').is(':checked');
        $submit.prop('disabled', !(hasAudience && hasBody && hasTarget && confirmed));
    }

    $typeRadios.on('change', syncAudienceType);
    $category.on('change', function () {
        if (currentType() === 'category') {
            $audienceId.val($(this).val() || '');
            loadPreview();
            refreshSubmit();
        }
    });
    $course.on('change', function () {
        if (currentType() === 'course') {
            $audienceId.val($(this).val() || '');
            loadPreview();
            refreshSubmit();
        }
    });

    $form.on('change input', '#broadcast_body, #broadcast_student, #broadcast_parent, #broadcast_confirm', refreshSubmit);

    $form.on('submit', function () {
        if (currentType() === 'category') {
            $audienceId.val($category.val() || '');
        } else {
            $audienceId.val($course.val() || '');
        }
        $submit.prop('disabled', true);
    });

    syncAudienceType();
})();
</script>
@endpush
