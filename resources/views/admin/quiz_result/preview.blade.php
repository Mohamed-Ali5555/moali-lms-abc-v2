@php
    $questionCount = max(1, $questions->count());
@endphp

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if ($results->count() === 0)
    <div class="qr-empty">
        <div class="qr-empty__icon"><i class="fi-rr-clipboard"></i></div>
        <strong>{{ get_phrase('لا توجد محاولات') }}</strong>
        <p>{{ get_phrase('هذا الطالب لم يقدّم أي محاولة بعد') }}</p>
    </div>
@else
<div class="qr-attempts accordion" id="qrAttemptsAccordion">
    @foreach ($results as $key => $result)
        @php
            $submits = $result->submits ? json_decode($result->submits, true) : [];
            $correct_answers = $result->correct_answer ? json_decode($result->correct_answer, true) : [];
            $wrong_answers = $result->wrong_answer ? json_decode($result->wrong_answer, true) : [];
            $mark_per_question = $quiz->total_mark / $questionCount;
            $obtained = round(count($correct_answers) * $mark_per_question, 2);
            $passed = $obtained >= $quiz->pass_mark;
            $correctCount = count($correct_answers);
            $wrongCount = count($wrong_answers);
            $duration = explode(':', (string) $quiz->duration);
            $durationH = $duration[0] ?? '0';
            $durationM = $duration[1] ?? '0';
            $durationS = $duration[2] ?? '0';
            $attemptNo = $key + 1;
            $isFirst = $key === 0;
        @endphp

        <div class="qr-attempt accordion-item" id="accordion-item-{{ $result->id }}">
            <h2 class="accordion-header qr-attempt__header">
                <button class="qr-attempt__toggle accordion-button {{ $isFirst ? '' : 'collapsed' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse-{{ $result->id }}"
                    aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                    aria-controls="collapse-{{ $result->id }}">
                    <span class="qr-attempt__meta">
                        <span class="qr-attempt__badge {{ $passed ? 'is-pass' : 'is-fail' }}">
                            {{ $passed ? get_phrase('ناجح') : get_phrase('راسب') }}
                        </span>
                        <span class="qr-attempt__title">
                            {{ get_phrase('محاولة') }} {{ $attemptNo }}
                        </span>
                        <span class="qr-attempt__date">
                            <i class="fi-rr-calendar"></i>
                            {{ date('d M, Y H:i', strtotime($result->created_at)) }}
                        </span>
                    </span>
                    <span class="qr-attempt__score">
                        <strong>{{ $obtained }}</strong>
                        <small>/ {{ $quiz->total_mark }}</small>
                    </span>
                </button>
                <button type="button"
                    class="qr-attempt__delete"
                    data-submission-id="{{ $result->id }}"
                    onclick="deleteAttempt({{ $result->id }})"
                    title="{{ get_phrase('Delete Attempt') }}"
                    aria-label="{{ get_phrase('Delete Attempt') }}">
                    <i class="fi fi-rr-trash"></i>
                </button>
            </h2>

            <div id="collapse-{{ $result->id }}"
                class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                data-bs-parent="#qrAttemptsAccordion">
                <div class="accordion-body qr-attempt__body">

                    <div class="qr-status {{ $passed ? 'is-pass' : 'is-fail' }}">
                        <div class="qr-status__icon">
                            <i class="{{ $passed ? 'fi-rr-check' : 'fi-rr-cross' }}"></i>
                        </div>
                        <div class="qr-status__text">
                            <strong>{{ $passed ? get_phrase('اجتاز الاختبار') : get_phrase('لم يجتز الاختبار') }}</strong>
                            <span>
                                {{ get_phrase('الدرجة') }}: {{ $obtained }} {{ get_phrase('من') }} {{ $quiz->total_mark }}
                                · {{ get_phrase('درجة النجاح') }}: {{ $quiz->pass_mark }}
                            </span>
                        </div>
                    </div>

                    <div class="qr-stats">
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--teal"><i class="fi-rr-star"></i></span>
                            <div>
                                <strong>{{ $quiz->total_mark }}</strong>
                                <span>{{ get_phrase('مجموع العلامات') }}</span>
                            </div>
                        </div>
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--sky"><i class="fi-rr-clock"></i></span>
                            <div>
                                <strong>{{ (int) $durationH }}:{{ str_pad((int) $durationM, 2, '0', STR_PAD_LEFT) }}:{{ str_pad((int) $durationS, 2, '0', STR_PAD_LEFT) }}</strong>
                                <span>{{ get_phrase('المدة') }}</span>
                            </div>
                        </div>
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--green"><i class="fi-rr-check"></i></span>
                            <div>
                                <strong>{{ $correctCount }}</strong>
                                <span>{{ get_phrase('إجابات صحيحة') }}</span>
                            </div>
                        </div>
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--red"><i class="fi-rr-cross"></i></span>
                            <div>
                                <strong>{{ $wrongCount }}</strong>
                                <span>{{ get_phrase('إجابات خاطئة') }}</span>
                            </div>
                        </div>
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--amber"><i class="fi-rr-flag"></i></span>
                            <div>
                                <strong>{{ $quiz->pass_mark }}</strong>
                                <span>{{ get_phrase('درجة النجاح') }}</span>
                            </div>
                        </div>
                        <div class="qr-stat">
                            <span class="qr-stat__icon qr-stat__icon--violet"><i class="fi-rr-medal"></i></span>
                            <div>
                                <strong>{{ $obtained }}</strong>
                                <span>{{ get_phrase('الدرجة المحصّلة') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="qr-questions-head">
                        <strong>{{ get_phrase('مراجعة الأسئلة') }}</strong>
                        <span>{{ $questions->count() }} {{ get_phrase('سؤال') }}</span>
                    </div>

                    <div class="qr-questions">
                        @foreach ($questions as $k => $question)
                            @php
                                $given_answer = $question->type == 'true_false'
                                    ? $question->answer
                                    : implode(', ', json_decode($question->answer, true) ?? []);
                                $user_answers = array_key_exists($question->id, $submits) ? $submits[$question->id] : [];
                                $isCorrectQ = in_array($question->id, $correct_answers);
                                $isWrongQ = in_array($question->id, $wrong_answers);
                                $qState = $isCorrectQ ? 'is-correct' : ($isWrongQ ? 'is-wrong' : 'is-neutral');
                                $correctOptions = $question->type == 'true_false'
                                    ? [(string) $question->answer]
                                    : (json_decode($question->answer, true) ?? []);
                            @endphp

                            <article class="qr-q {{ $qState }}">
                                <header class="qr-q__head">
                                    <span class="qr-q__num">{{ $k + 1 }}</span>
                                    <div class="qr-q__title">{!! $question->title !!}</div>
                                    <span class="qr-q__state">
                                        @if ($isCorrectQ)
                                            <i class="fi-rr-check"></i> {{ get_phrase('صحيح') }}
                                        @elseif ($isWrongQ)
                                            <i class="fi-rr-cross"></i> {{ get_phrase('خطأ') }}
                                        @else
                                            <i class="fi-rr-minus"></i> {{ get_phrase('بدون إجابة') }}
                                        @endif
                                    </span>
                                </header>

                                @if (!empty($question->question_image))
                                    <div class="qr-q__image question-image">
                                        <div class="image-loader" id="loader-preview-{{ $result->id }}-{{ $question->id }}">
                                            <div class="spinner"></div>
                                        </div>
                                        <img src="{{ asset($question->question_image) }}"
                                            class="img-fluid"
                                            alt="Question Image"
                                            data-question-id="{{ $question->id }}"
                                            onload="hideImageLoader('preview-{{ $result->id }}-{{ $question->id }}')"
                                            onerror="hideImageLoader('preview-{{ $result->id }}-{{ $question->id }}')">
                                    </div>
                                @endif

                                <div class="qr-q__options">
                                    @if ($question->type == 'mcq')
                                        @php $options = json_decode($question->options, true) ?? []; @endphp
                                        @foreach ($options as $option)
                                            @php
                                                $isSelected = is_array($user_answers) && in_array($option, $user_answers);
                                                $isRight = in_array($option, $correctOptions);
                                                $optClass = '';
                                                if ($isRight) $optClass = 'is-right';
                                                if ($isSelected && !$isRight) $optClass = 'is-picked-wrong';
                                                if ($isSelected && $isRight) $optClass = 'is-picked-right';
                                                $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $option);
                                            @endphp
                                            <div class="qr-opt {{ $optClass }}">
                                                <span class="qr-opt__check">
                                                    @if ($isRight)
                                                        <i class="fi-rr-check"></i>
                                                    @elseif ($isSelected)
                                                        <i class="fi-rr-cross"></i>
                                                    @else
                                                        <i class="fi-rr-circle"></i>
                                                    @endif
                                                </span>
                                                <span class="qr-opt__label">
                                                    @if ($isImage)
                                                        <img src="{{ asset($option) }}" alt="Option">
                                                    @else
                                                        {{ $option }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @elseif ($question->type == 'fill_blanks')
                                        <input type="text" class="form-control tagify qr-q__tags" data-role="tagsinput"
                                            value="{{ is_array($user_answers) ? json_encode($user_answers) : $user_answers }}" disabled>
                                    @elseif ($question->type == 'true_false')
                                        @foreach (['true' => get_phrase('True'), 'false' => get_phrase('False')] as $tfVal => $tfLabel)
                                            @php
                                                $isSelected = (string) $user_answers === (string) $tfVal;
                                                $isRight = (string) $question->answer === (string) $tfVal;
                                                $optClass = '';
                                                if ($isRight) $optClass = 'is-right';
                                                if ($isSelected && !$isRight) $optClass = 'is-picked-wrong';
                                                if ($isSelected && $isRight) $optClass = 'is-picked-right';
                                            @endphp
                                            <div class="qr-opt {{ $optClass }}">
                                                <span class="qr-opt__check">
                                                    @if ($isRight)
                                                        <i class="fi-rr-check"></i>
                                                    @elseif ($isSelected)
                                                        <i class="fi-rr-cross"></i>
                                                    @else
                                                        <i class="fi-rr-circle"></i>
                                                    @endif
                                                </span>
                                                <span class="qr-opt__label">{{ $tfLabel }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <footer class="qr-q__answer">
                                    <i class="fi-rr-bulb"></i>
                                    <div>
                                        <strong>{{ get_phrase('الإجابة الصحيحة') }}</strong>
                                        <span>
                                            @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $given_answer))
                                                <img src="{{ asset($given_answer) }}" alt="Answer">
                                            @else
                                                {{ $given_answer }}
                                            @endif
                                        </span>
                                    </div>
                                </footer>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

<script>
$('.result .tagify:not(.inited), .qr-q__tags:not(.inited)').each(function(index, element) {
    if (typeof Tagify === 'undefined') return;
    new Tagify(element, {
        placeholder: '{{ get_phrase('Enter your keywords') }}'
    });
    $(element).addClass('inited');
});

function hideImageLoader(loaderId) {
    const loader = document.getElementById('loader-' + loaderId);
    if (loader) {
        loader.style.display = 'none';
    }
    const questionImage = loader ? loader.closest('.question-image') : null;
    if (questionImage) {
        const img = questionImage.querySelector('img');
        if (img) {
            img.classList.add('loaded');
        }
    }
}

function deleteAttempt(submissionId) {
    Swal.fire({
        title: '{{ get_phrase("Are you sure?") }}',
        text: '{{ get_phrase("You want to delete this attempt? This action cannot be undone!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '{{ get_phrase("Yes, delete it!") }}',
        cancelButtonText: '{{ get_phrase("Cancel") }}',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '{{ get_phrase("Deleting...") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "{{ route('admin.quiz.submission.delete', '') }}/" + submissionId,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status) {
                        $('#accordion-item-' + submissionId).fadeOut(300, function() {
                            $(this).remove();
                            if ($('.qr-attempt').length === 0) {
                                $('.qr-attempts').replaceWith(
                                    '<div class="qr-empty"><div class="qr-empty__icon"><i class="fi-rr-clipboard"></i></div><strong>{{ get_phrase("لا توجد محاولات") }}</strong><p>{{ get_phrase("No attempts found") }}</p></div>'
                                );
                            }
                        });

                        Swal.fire({
                            icon: 'success',
                            title: '{{ get_phrase("Deleted!") }}',
                            text: response.success,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON?.error || '{{ get_phrase("An error occurred") }}';
                    Swal.fire({
                        icon: 'error',
                        title: '{{ get_phrase("Error!") }}',
                        text: errorMessage
                    });
                }
            });
        }
    });
}
</script>
