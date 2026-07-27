<style>
    .qz-wrap {
        --qz-accent: var(--theme-color, #0d9488);
        --qz-accent-rgb: var(--c-accent-rgb, 13, 148, 136);
        --qz-text: #0f172a;
        --qz-muted: #64748b;
        --qz-border: rgba(15, 23, 42, 0.08);
        --qz-card: #ffffff;
        --qz-soft: #f8fafc;
        --qz-radius: 18px;
        --qz-shadow: 0 12px 32px rgba(15, 23, 42, 0.07);
        --qz-ease: cubic-bezier(0.22, 1, 0.36, 1);
        direction: rtl;
    }

    .qz-wrap .quiz-title {
        font-size: clamp(1.15rem, 2vw, 1.4rem);
        font-weight: 800;
        padding: 0;
        margin: 0;
        color: var(--qz-text);
        letter-spacing: -0.02em;
        line-height: 1.35;
    }

    .qz-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--qz-border);
    }

    .qz-head__main {
        display: flex;
        align-items: flex-start;
        gap: 0.9rem;
        min-width: 0;
        flex: 1;
    }

    .qz-head__icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: #fff;
        background: linear-gradient(135deg, rgb(var(--qz-accent-rgb)), rgba(var(--qz-accent-rgb), 0.75));
        box-shadow: 0 10px 22px rgba(var(--qz-accent-rgb), 0.28);
    }

    .qz-head__icon svg {
        width: 22px;
        height: 22px;
    }

    .qz-head__label {
        display: block;
        margin-bottom: 0.25rem;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: rgb(var(--qz-accent-rgb));
    }

    .qz-desc {
        margin-bottom: 1.15rem;
        padding: 1rem 1.1rem;
        border-radius: 14px;
        background: var(--qz-soft);
        border: 1px solid var(--qz-border);
        color: var(--qz-muted);
        font-size: 0.95rem;
        line-height: 1.75;
    }

    .qz-card {
        background: var(--qz-card);
        border: 1px solid var(--qz-border);
        border-radius: var(--qz-radius);
        box-shadow: var(--qz-shadow);
        padding: 1.2rem;
        margin-bottom: 1rem;
    }

    .qz-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
    }

    .qz-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.9rem 1rem;
        border-radius: 14px;
        background: var(--qz-soft);
        border: 1px solid var(--qz-border);
        transition: transform 0.2s var(--qz-ease), box-shadow 0.2s var(--qz-ease), border-color 0.2s var(--qz-ease);
    }

    .qz-item:hover {
        transform: translateY(-2px);
        border-color: rgba(var(--qz-accent-rgb), 0.25);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.06);
    }

    .qz-item__icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: rgb(var(--qz-accent-rgb));
        background: rgba(var(--qz-accent-rgb), 0.12);
    }

    .qz-item__icon svg {
        width: 18px;
        height: 18px;
    }

    .qz-item__meta {
        min-width: 0;
    }

    .qz-item__label {
        display: block;
        margin-bottom: 0.15rem;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--qz-muted);
    }

    .qz-item__value {
        display: block;
        font-size: 1rem;
        font-weight: 800;
        color: var(--qz-text);
        line-height: 1.3;
        word-break: break-word;
    }

    .qz-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .qz-btn {
        appearance: none;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        min-height: 48px;
        padding: 0.7rem 1.2rem;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 800;
        cursor: pointer;
        transition: transform 0.2s var(--qz-ease), filter 0.2s var(--qz-ease), box-shadow 0.2s var(--qz-ease);
        flex: 1 1 180px;
    }

    .qz-btn svg {
        width: 16px;
        height: 16px;
    }

    .qz-btn--primary {
        color: #fff;
        background: linear-gradient(135deg, rgb(var(--qz-accent-rgb)), rgba(var(--qz-accent-rgb), 0.8));
        box-shadow: 0 10px 22px rgba(var(--qz-accent-rgb), 0.28);
    }

    .qz-btn--primary:hover {
        transform: translateY(-1px);
        filter: brightness(1.05);
        color: #fff;
    }

    .qz-btn--ghost {
        color: var(--qz-text);
        background: #fff;
        border: 1px solid var(--qz-border);
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
    }

    .qz-btn--ghost:hover {
        transform: translateY(-1px);
        border-color: rgba(var(--qz-accent-rgb), 0.3);
        color: rgb(var(--qz-accent-rgb));
    }

    .qz-alert {
        display: flex;
        align-items: flex-start;
        gap: 0.65rem;
        width: 100%;
        margin-top: 0.85rem;
        padding: 0.9rem 1rem;
        border-radius: 12px;
        background: rgba(14, 165, 233, 0.08);
        border: 1px solid rgba(14, 165, 233, 0.18);
        color: #0369a1;
        font-size: 0.88rem;
        line-height: 1.6;
    }

    .qz-alert i {
        margin-top: 0.15rem;
    }

    .qz-wrap .timer-container > div {
        width: fit-content;
        background: rgba(var(--qz-accent-rgb), 0.1);
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        border: 1px solid rgba(var(--qz-accent-rgb), 0.18);
    }

    .qz-wrap #quizTimer {
        width: auto;
        min-width: 78px;
        background: rgb(var(--qz-accent-rgb));
        color: #fff;
        border-radius: 999px;
        padding: 0.15rem 0.65rem;
        font-weight: 800;
        margin: 0;
    }

    .qz-wrap .question {
        min-height: 155px;
    }

    .qz-wrap input[type="text"] {
        padding: 12px 50px 12px 20px;
        border-radius: 10px;
        border: 1px solid #6b738530;
        box-shadow: none !important;
    }

    .qz-wrap .gradient-border {
        background: #fff;
        border: 2px solid rgb(var(--qz-accent-rgb));
        color: #212529;
        transition: .3s;
    }

    .qz-wrap .gradient-border:hover {
        color: #fff;
        background: rgb(var(--qz-accent-rgb));
    }

    @media (max-width: 767.98px) {
        .qz-grid {
            grid-template-columns: 1fr;
        }

        .qz-head {
            flex-direction: column;
        }

        .qz-actions .qz-btn {
            flex: 1 1 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .qz-item,
        .qz-btn {
            transition: none !important;
        }
    }
</style>

@php
    $now = now()->format('Y-m-d H:i');
    $quiz = App\Models\Lesson::where('status',1)->where('id', request()->route()->parameter('id'))->active()->firstOrNew();

    $questions = DB::table('questions')
        ->where('quiz_id', $quiz->id)
        ->get();

    $examMode = get_settings('quiz_submission_mode') === 'secure';

    $submits = DB::table('quiz_submissions')
        ->where('quiz_id', $quiz->id)
        ->where('user_id', auth()->user()->id)
        ->where('status', 'completed')
        ->get();

    $questionTypes = str_replace('_', ' ', implode(', ', $questions->pluck('type')->unique()->toArray()));
    $durationLabel = lesson_durations($quiz->id);
@endphp

<div class="qz-wrap px-4">
    <div class="qz-head">
        <div class="qz-head__main">
            <div class="qz-head__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4" />
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                </svg>
            </div>
            <div class="min-w-0">
                <span class="qz-head__label">
                    @if ($quiz->type == '2')
                        {{ get_phrase('واجب') }}
                    @else
                        {{ get_phrase('اختبار') }}
                    @endif
                </span>
                <h4 class="quiz-title"><span>{{ $quiz->title }}</span></h4>
            </div>
        </div>

        <div class="timer-container d-none">
            <div class="d-flex align-items-center gap-2 justify-content-end">
                <span><i class="fi fi-rr-clock-five"></i></span>
                <span class="fw-700">{{ get_phrase('الوقت المتبقي') }}</span>
                <p class="text-center mb-0" id="quizTimer"></p>
            </div>
        </div>
    </div>

    @if (!empty(trim(strip_tags($quiz->description ?? ''))))
        <div class="description qz-desc">{!! $quiz->description !!}</div>
    @else
        <div class="description d-none"></div>
    @endif

    <div class="quiz-starter qz-card">
        <div class="qz-grid">
            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('المدة') }}</span>
                    <span class="qz-item__value">{{ $durationLabel }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20V10" />
                        <path d="M18 20V4" />
                        <path d="M6 20v-4" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('الدرجة النهائية') }}</span>
                    <span class="qz-item__value">{{ $quiz->total_mark < 10 ? '0' : '' }}{{ $quiz->total_mark }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="6" />
                        <path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('درجة النجاح') }}</span>
                    <span class="qz-item__value">{{ $quiz->pass_mark < 10 ? '0' : '' }}{{ $quiz->pass_mark }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('إجمالي التسليمات') }}</span>
                    <span class="qz-item__value">{{ $quiz->retake < 10 ? '0' : '' }}{{ $quiz->retake }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
                        <line x1="12" y1="17" x2="12.01" y2="17" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('نوع الأسئلة') }}</span>
                    <span class="qz-item__value text-capitalize">{{ $questionTypes ?: '—' }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('تسليمات الطالب') }}</span>
                    <span class="qz-item__value">{{ $submits->count() < 10 ? '0' : '' }}{{ $submits->count() }}</span>
                </div>
            </div>

            <div class="qz-item">
                <div class="qz-item__icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                </div>
                <div class="qz-item__meta">
                    <span class="qz-item__label starter-label">{{ get_phrase('عدد الأسئلة') }}</span>
                    <span class="qz-item__value">{{ $questions->count() < 10 ? '0' : '' }}{{ $questions->count() }}</span>
                </div>
            </div>
        </div>

        <div class="qz-actions">
            @foreach ($submits as $key => $submit)
                <button type="button" class="result-btn qz-btn qz-btn--ghost" onclick="getResult(this)"
                    id="{{ $submit->id }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <polyline points="14 2 14 8 20 8" />
                        <line x1="16" y1="13" x2="8" y2="13" />
                        <line x1="16" y1="17" x2="8" y2="17" />
                    </svg>
                    {{ get_phrase('عرض النتيجة') }} {{ ++$key }}
                </button>
            @endforeach

            @if ($submits->count() < $quiz->retake)
                <button type="button" class="qz-btn qz-btn--primary" id="starterBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polygon points="5 3 19 12 5 21 5 3" />
                    </svg>
                    @if ($quiz->type == '1')
                        @if ($submits->count() == 0)
                            <span>{{ get_phrase('ابدأ الامتحان') }}</span>
                        @else
                            <span>{{ get_phrase('إعادة الامتحان') }}</span>
                        @endif
                    @elseif ($quiz->type == '2')
                        @if ($submits->count() == 0)
                            <span>{{ get_phrase('ابدأ الواجب') }}</span>
                        @else
                            <span>{{ get_phrase('إعادة الواجب') }}</span>
                        @endif
                    @endif
                </button>
            @endif
        </div>

        @if ($examMode)
            <div class="qz-alert">
                <i class="fi fi-rr-info"></i>
                <div>
                    <strong>{{ get_phrase('وضع الامتحان') }}:</strong>
                    {{ get_phrase('سيتم حفظ وتصحيح كل إجابة فوراً. لو خرجت من الصفحة سيتم احتساب درجتك على الأسئلة المُجابة فقط.') }}
                </div>
            </div>
        @endif
    </div>
</div>

<div class="load-content px-4"></div>

<script src="{{ asset('assets/frontend/default/js/jquery-3.7.1.min.js') }}"></script>
<script>
    var starterContainer = document.querySelector('.quiz-starter');
    var starterBtn = document.querySelector('#starterBtn');
    var questionSection = document.querySelector('.question-section');
    var quizTimer = document.querySelector('#quizTimer');
    var description = document.querySelector('.description');
    var resultSection = document.querySelector('.result-section');
    var backBtn = document.querySelector('#backBtn');

    var examMode = {{ $examMode ? 'true' : 'false' }};
    var currentSubmissionId = null;

    if (starterBtn) {
        starterBtn.addEventListener('click', function() {
            starterContainer.classList.add('d-none');
            if (description) description.classList.add('d-none');

            if (examMode) {
                $.ajax({
                    type: "post",
                    url: "{{ route('quiz.start') }}",
                    data: {
                        quiz_id: "{{ $quiz->id }}",
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            currentSubmissionId = response.submission_id;
                            window.examSubmissionId = response.submission_id;
                            loadQuestions();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطأ',
                                text: response.message
                            });
                            starterContainer.classList.remove('d-none');
                            if (description) description.classList.remove('d-none');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء بدء الامتحان'
                        });
                        starterContainer.classList.remove('d-none');
                        if (description) description.classList.remove('d-none');
                    }
                });
            } else {
                loadQuestions();
            }
        });
    }

    function loadQuestions() {
        $.ajax({
            type: "get",
            url: "{{ route('load.quiz.questions') }}",
            data: {
                quiz_id: "{{ $quiz->id }}"
            },
            success: function(response) {
                console.log(response)
                $('.load-content').html(response);
                startTimer();
            }
        });
    }

    function startTimer() {
        let timerContainer = document.querySelector('.timer-container');
        let quizTitle = document.querySelector('.quiz-title');
        timerContainer.classList.remove('d-none');
        quizTitle.classList.remove('text-center');

        let duration = "{{ $quiz->duration }}";
        let durationArr = duration.split(":");

        let hour = parseInt(durationArr[0]);
        let minute = parseInt(durationArr[1]);
        let second = parseInt(durationArr[2]);

        quizTimer.innerHTML = (hour < 10 ? '0' + hour : hour) + ':' +
            (minute < 10 ? '0' + minute : minute) + ':' +
            (second < 10 ? '0' + second : second)

        let timerInterval = setInterval(() => {
            if (hour === 0 && minute === 0 && second === 0) {
                clearInterval(timerInterval);
                endQuiz();
                return;
            }

            if (second === 0) {
                if (minute === 0) {
                    hour--;
                    minute = 59;
                } else {
                    minute--;
                }
                second = 59;
            } else {
                second--;
            }

            quizTimer.innerHTML = (hour < 10 ? '0' + hour : hour) + ':' +
                (minute < 10 ? '0' + minute : minute) + ':' +
                (second < 10 ? '0' + second : second);
        }, 1000);
    }

    function getResult(elem) {
        let id = $(elem).attr('id');
        if (description) description.classList.add('d-none');
        starterContainer.classList.add('d-none');
        document.querySelector('.timer-container')?.classList.add('d-none');

        $.ajax({
            type: "get",
            url: "{{ route('load.quiz.result') }}",
            data: {
                submit_id: id,
                quiz_id: "{{ $quiz->id }}"
            },
            success: function(response) {
                $('.load-content').html(response);
            }
        });
    }

    function endQuiz() {
        submitQuiz();
    }

    @php $resultSubmitId = session('quiz_submitted_id') ?? request()->get('show_result'); @endphp
    @if($resultSubmitId)
    $(document).ready(function() {
        var elem = document.createElement('button');
        elem.setAttribute('id', '{{ $resultSubmitId }}');
        getResult(elem);
        if (window.history.replaceState && window.location.search.includes('show_result')) {
            var url = new URL(window.location.href);
            url.searchParams.delete('show_result');
            window.history.replaceState({}, '', url.toString());
        }
    });
    @endif
</script>
