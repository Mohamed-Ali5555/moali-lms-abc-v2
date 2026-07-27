@php
    $questions = App\Models\Question::where('quiz_id', $id)->orderBy('sort')->get();
@endphp

@if ($questions->count() > 0)
    <div class="ql-panel">
        <div class="ql-toolbar">
            <div class="ql-toolbar__meta">
                <span class="ql-toolbar__label">{{ get_phrase('Questions') }}</span>
                <span class="ql-toolbar__count">{{ $questions->count() }}</span>
            </div>
            <div class="ql-toolbar__actions">
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['instructor.questions.create', 'id' => $id]) }}', '{{ get_phrase('Add Question') }}', 'modal-xl')"
                    class="ql-btn ql-btn--primary">
                    <i class="fi-rr-add"></i>{{ get_phrase('Add Question') }}
                </a>
                <a href="#"
                    onclick="ajaxModal('{{ route('modal', ['instructor.questions.sort', 'id' => $id]) }}', '{{ get_phrase('Sort Questions') }}', 'modal-lg')"
                    class="ql-btn">
                    <i class="fi-rr-sort"></i>{{ get_phrase('Sort Questions') }}
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
                            <span class="ql-item__type">{{ ucfirst(str_replace('_', ' ', $question->type ?? 'mcq')) }}</span>
                        </div>
                    </div>
                    <div class="ql-item__actions">
                        <a href="#"
                            onclick="ajaxModal('{{ route('modal', ['instructor.questions.edit', 'id' => $question->id]) }}', '{{ get_phrase('Edit Question') }}', 'modal-xl')"
                            class="ql-icon-btn ql-icon-btn--edit" data-bs-toggle="tooltip"
                            title="{{ get_phrase('Edit') }}">
                            <i class="fi-rr-pencil"></i>
                        </a>
                        <a href="#"
                            onclick="confirmModal('{{ route('instructor.course.question.delete', $question->id) }}'); event.stopPropagation();"
                            class="ql-icon-btn ql-icon-btn--delete" data-bs-toggle="tooltip"
                            title="{{ get_phrase('Delete') }}">
                            <i class="fi-rr-trash"></i>
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@else
    <div class="ql-empty">
        <a onclick="ajaxModal('{{ route('modal', ['instructor.questions.create', 'id' => $id]) }}', '{{ get_phrase('Add Question') }}', 'modal-xl')"
            href="#" class="ql-empty__card ql-empty__card--solo">
            <i class="fi-rr-add"></i>
            <span>{{ get_phrase('Add Question') }}</span>
        </a>
    </div>
@endif
