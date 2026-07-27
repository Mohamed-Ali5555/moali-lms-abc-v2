@php
    $questions = App\Models\Question::where('quiz_id', $id)->orderBy('sort')->get();
    $typeLabels = [
        'mcq' => get_phrase('اختيار من متعدد'),
        'fill_blanks' => get_phrase('أكمل الفراغ'),
        'true_false' => get_phrase('صح أو خطأ'),
    ];
@endphp

@if ($questions->count() > 0)
    <div class="ql-panel" dir="rtl">
        <div class="ql-toolbar">
            <div class="ql-toolbar__meta">
                <span class="ql-toolbar__label">{{ get_phrase('الأسئلة') }}</span>
                <span class="ql-toolbar__count">{{ $questions->count() }}</span>
            </div>
            <div class="ql-toolbar__actions">
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['admin.questions.create', 'id' => $id]) }}', '{{ get_phrase('إضافة سؤال') }}', 'modal-xl')"
                    class="ql-btn ql-btn--primary">
                    <i class="fi-rr-add"></i> {{ get_phrase('إضافة سؤال') }}
                </a>
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['admin.questions.choose', 'id' => $id]) }}', '{{ get_phrase('اختيار سؤال') }}', 'modal-xl')"
                    class="ql-btn">
                    <i class="fi-rr-list"></i> {{ get_phrase('اختيار سؤال') }}
                </a>
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['admin.questions.sort', 'id' => $id]) }}', '{{ get_phrase('ترتيب الأسئلة') }}', 'modal-lg')"
                    class="ql-btn">
                    <i class="fi-rr-sort"></i> {{ get_phrase('ترتيب الأسئلة') }}
                </a>
            </div>
        </div>

        <ul class="ql-list">
            @foreach ($questions as $question)
                <li class="ql-item">
                    <div class="ql-item__main">
                        <span class="ql-item__num">{{ $loop->iteration }}</span>
                        <div class="ql-item__body">
                            <div class="ql-item__title">{!! $question->title !!}</div>
                            @if (!empty($question->question_image))
                                <div class="ql-item__media">
                                    <img src="/{{ ltrim($question->question_image, '/') }}" alt=""
                                        onerror="this.src='/uploads/system/placeholder.png'">
                                </div>
                            @endif
                            <span class="ql-item__type">{{ $typeLabels[$question->type] ?? $question->type }}</span>
                        </div>
                    </div>
                    <div class="ql-item__actions">
                        <a href="#"
                            onclick="ajaxModal('{{ route('modal', ['admin.questions.edit', 'id' => $question->id]) }}', '{{ get_phrase('تعديل سؤال') }}', 'modal-xl')"
                            class="ql-icon-btn ql-icon-btn--edit" data-bs-toggle="tooltip"
                            title="{{ get_phrase('تعديل') }}">
                            <i class="fi-rr-pencil"></i>
                        </a>
                        <a href="#"
                            onclick="confirmModal('{{ route('admin.course.question.delete', $question->id) }}'); event.stopPropagation();"
                            class="ql-icon-btn ql-icon-btn--delete" data-bs-toggle="tooltip"
                            title="{{ get_phrase('حذف') }}">
                            <i class="fi-rr-trash"></i>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="ql-empty" dir="rtl">
        <a onclick="ajaxModal('{{ route('modal', ['admin.questions.create', 'id' => $id]) }}', '{{ get_phrase('إضافة سؤال') }}', 'modal-xl')"
            href="#" class="ql-empty__card">
            <i class="fi-rr-add"></i>
            <span>{{ get_phrase('إضافة سؤال') }}</span>
        </a>
        <a onclick="ajaxModal('{{ route('modal', ['admin.questions.choose', 'id' => $id]) }}', '{{ get_phrase('اختيار سؤال') }}', 'modal-xl')"
            href="#" class="ql-empty__card">
            <i class="fi-rr-list"></i>
            <span>{{ get_phrase('اختيار سؤال') }}</span>
        </a>
    </div>
@endif
