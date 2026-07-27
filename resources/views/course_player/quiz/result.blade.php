@php
    $submission = $result;
    $ranking = $ranking ?? [
        'rank' => null,
        'participants' => 0,
        'score' => 0,
        'percentile' => 0,
        'leaderboard' => [],
        'mark_per_question' => 0,
    ];

    $userSubmits = $submission && $submission->submits ? json_decode($submission->submits, true) : [];
    $correct_answers = $submission && $submission->correct_answer ? (json_decode($submission->correct_answer, true) ?: []) : [];
    $wrong_answers = $submission && $submission->wrong_answer ? (json_decode($submission->wrong_answer, true) ?: []) : [];
    $total_questions = max($questions->count(), 1);
    $answered_questions = count($correct_answers) + count($wrong_answers);
    $unanswered_questions = max(0, $total_questions - $answered_questions);
    $mark_per_question = $ranking['mark_per_question'] > 0
        ? $ranking['mark_per_question']
        : (($quiz->total_mark ?? 0) / $total_questions);
    $obtainedScore = round(count($correct_answers) * $mark_per_question, 1);
    $passed = $obtainedScore >= ($quiz->pass_mark ?? 0);
    $successRate = $total_questions > 0 ? round((count($correct_answers) / $total_questions) * 100) : 0;
    $rank = $ranking['rank'] ?? null;
    $participants = $ranking['participants'] ?? 0;
    $percentile = $ranking['percentile'] ?? 0;
    $leaderboard = $ranking['leaderboard'] ?? [];
@endphp

<style>
    .qr-wrap {
        --qr-accent-rgb: var(--c-accent-rgb, 13, 148, 136);
        --qr-text: #0f172a;
        --qr-muted: #64748b;
        --qr-border: rgba(15, 23, 42, 0.08);
        --qr-card: #ffffff;
        --qr-soft: #f8fafc;
        --qr-radius: 18px;
        --qr-shadow: 0 14px 36px rgba(15, 23, 42, 0.08);
        --qr-ease: cubic-bezier(0.22, 1, 0.36, 1);
        direction: rtl;
        font-family: inherit;
    }

    .qr-wrap .result {
        background: transparent;
        padding: 0;
        border-radius: 0;
        box-shadow: none;
    }

    .qr-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--qr-radius);
        padding: 1.5rem 1.35rem;
        margin-bottom: 1.1rem;
        color: #fff;
        background:
            radial-gradient(120% 140% at 100% 0%, rgba(255,255,255,0.18), transparent 55%),
            linear-gradient(120deg, rgb(var(--c-secondary-rgb, 51, 65, 85)), rgb(var(--qr-accent-rgb)) 55%, rgba(var(--qr-accent-rgb), 0.85));
        box-shadow: 0 18px 40px rgba(var(--qr-accent-rgb), 0.28);
    }

    .qr-hero.is-fail {
        background:
            radial-gradient(120% 140% at 100% 0%, rgba(255,255,255,0.14), transparent 55%),
            linear-gradient(120deg, #7f1d1d, #dc2626 55%, #f97316);
        box-shadow: 0 18px 40px rgba(220, 38, 38, 0.25);
    }

    .qr-hero__top {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.1rem;
    }

    .qr-hero__eyebrow {
        display: inline-block;
        margin-bottom: 0.35rem;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        opacity: 0.88;
    }

    .qr-hero__title {
        margin: 0;
        font-size: clamp(1.25rem, 2.4vw, 1.7rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.3;
    }

    .qr-hero__status {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.65rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.22);
        font-weight: 800;
        font-size: 0.88rem;
    }

    .qr-rank {
        min-width: 150px;
        padding: 1rem 1.1rem;
        border-radius: 16px;
        background: rgba(255,255,255,0.14);
        border: 1px solid rgba(255,255,255,0.2);
        text-align: center;
        backdrop-filter: blur(8px);
    }

    .qr-rank__label {
        display: block;
        font-size: 0.78rem;
        opacity: 0.9;
        margin-bottom: 0.25rem;
    }

    .qr-rank__value {
        display: block;
        font-size: clamp(2rem, 4vw, 2.6rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .qr-rank__sub {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.82rem;
        opacity: 0.9;
    }

    .qr-score-row {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 0.85rem;
    }

    .qr-score-box,
    .qr-progress-box {
        padding: 0.95rem 1rem;
        border-radius: 14px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.18);
    }

    .qr-score-box strong {
        display: block;
        font-size: 1.8rem;
        font-weight: 900;
        line-height: 1.1;
    }

    .qr-score-box span,
    .qr-progress-box span {
        font-size: 0.8rem;
        opacity: 0.9;
    }

    .qr-progress-bar {
        margin-top: 0.55rem;
        height: 10px;
        border-radius: 999px;
        background: rgba(255,255,255,0.2);
        overflow: hidden;
    }

    .qr-progress-bar > i {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #fff;
        width: 0;
        transition: width 1s var(--qr-ease);
    }

    .qr-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.85rem;
        margin-bottom: 1.1rem;
    }

    .qr-stat {
        background: var(--qr-card);
        border: 1px solid var(--qr-border);
        border-radius: 14px;
        padding: 1rem;
        box-shadow: var(--qr-shadow);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        opacity: 0;
        transform: translateY(8px);
    }

    .qr-stat__icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        color: rgb(var(--qr-accent-rgb));
        background: rgba(var(--qr-accent-rgb), 0.12);
        font-size: 1.1rem;
    }

    .qr-stat.is-wrong .qr-stat__icon { color: #dc2626; background: rgba(220,38,38,0.1); }
    .qr-stat.is-miss .qr-stat__icon { color: #d97706; background: rgba(217,119,6,0.12); }
    .qr-stat.is-ok .qr-stat__icon { color: #059669; background: rgba(16,185,129,0.12); }

    .qr-stat__label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--qr-muted);
        margin-bottom: 0.15rem;
    }

    .qr-stat__value {
        display: block;
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--qr-text);
        line-height: 1.2;
    }

    .qr-board {
        background: var(--qr-card);
        border: 1px solid var(--qr-border);
        border-radius: var(--qr-radius);
        box-shadow: var(--qr-shadow);
        padding: 1.15rem;
        margin-bottom: 1.15rem;
    }

    .qr-board__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }

    .qr-board__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--qr-text);
    }

    .qr-board__hint {
        margin: 0;
        font-size: 0.8rem;
        color: var(--qr-muted);
    }

    .qr-board__list {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
    }

    .qr-board__row {
        display: grid;
        grid-template-columns: 42px 1fr auto;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border-radius: 12px;
        background: var(--qr-soft);
        border: 1px solid var(--qr-border);
    }

    .qr-board__row.is-me {
        background: rgba(var(--qr-accent-rgb), 0.1);
        border-color: rgba(var(--qr-accent-rgb), 0.28);
    }

    .qr-board__place {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        font-weight: 900;
        font-size: 0.85rem;
        color: #fff;
        background: #94a3b8;
    }

    .qr-board__row:nth-child(1) .qr-board__place { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .qr-board__row:nth-child(2) .qr-board__place { background: linear-gradient(135deg, #94a3b8, #64748b); }
    .qr-board__row:nth-child(3) .qr-board__place { background: linear-gradient(135deg, #d97706, #92400e); }

    .qr-board__user {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
    }

    .qr-board__avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        background: #e2e8f0;
        flex-shrink: 0;
    }

    .qr-board__name {
        margin: 0;
        font-size: 0.92rem;
        font-weight: 800;
        color: var(--qr-text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .qr-board__score {
        font-weight: 900;
        color: rgb(var(--qr-accent-rgb));
        font-size: 0.95rem;
    }

    .qr-section-title {
        margin: 0 0 0.85rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--qr-text);
    }

    .result-question {
        background: #fff;
        padding: 1.05rem 1.1rem;
        border-radius: 14px;
        margin-bottom: 0.9rem;
        border: 1px solid var(--qr-border);
        box-shadow: var(--qr-shadow);
        transition: background 0.3s, transform 0.2s, opacity 0.5s;
        opacity: 0;
        transform: translateY(8px);
    }

    .result-question.correct { background: #ecfdf5; border-color: rgba(16,185,129,0.25); }
    .result-question.wrong { background: #fef2f2; border-color: rgba(239,68,68,0.22); }
    .result-question.unanswered { background: #fffbeb; border-color: rgba(245,158,11,0.25); }
    .result-question:hover { transform: translateY(-3px); }

    .result-question .serial {
        font-weight: 800;
        background: rgb(var(--qr-accent-rgb));
        color: #fff;
        min-width: 36px;
        height: 36px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .result-question .mb-1 {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--qr-text);
    }

    .answer-container {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        background: #fff;
        transition: 0.2s;
        min-height: 46px;
    }

    .answer-container img { border-radius: 8px; }
    .answer-container input:disabled { cursor: not-allowed; }
    .answer-wrong { background: #fee2e2 !important; border-color: #ef4444 !important; color: #b91c1c; }
    .answer-correct { background: #d1fae5 !important; border-color: #10b981 !important; color: #047857; }
    .answer-missed { background: #d1fae5 !important; border-color: #10b981 !important; color: #059669; opacity: 0.95; }

    .qr-answer-label {
        margin-top: 0.65rem;
        margin-bottom: 0;
        font-weight: 800;
        color: #059669;
    }

    #backBtn {
        background: linear-gradient(135deg, rgb(var(--qr-accent-rgb)), rgba(var(--qr-accent-rgb), 0.8));
        color: #fff;
        font-weight: 800;
        padding: 0.75rem 1.4rem;
        border-radius: 12px;
        border: none;
        min-height: 46px;
        transition: 0.2s var(--qr-ease);
    }

    #backBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(var(--qr-accent-rgb), 0.28);
        color: #fff;
    }

    .quiz-image-frame {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 8px;
        gap: 8px;
    }

    .quiz-image-frame .quiz-image-preview,
    .answer-container .preview-img {
        width: auto;
        max-width: 100%;
        height: auto;
        max-height: 300px;
        object-fit: contain;
        display: block;
        border-radius: 8px;
        cursor: pointer;
    }

    .answer-container .preview-img { max-height: 120px; }

    .question-image-actions {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .view-image-btn {
        padding: 6px 12px;
        border-radius: 8px;
        background: rgb(var(--qr-accent-rgb));
        color: white;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        gap: 4px;
    }

    #imageModal .modal-dialog {
        max-width: min(96vw, 1200px);
        margin: 1rem auto;
    }

    #imageModal #modalImage {
        max-width: 100%;
        max-height: 85vh;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .question-title-content,
    .result-question-title-content {
        flex: 1 1 auto;
        min-width: 0;
        width: 100%;
    }

    .question-title-content img,
    .result-question-title-content img,
    .question-rich-image {
        width: auto !important;
        height: auto !important;
        max-width: 100% !important;
        max-height: 320px !important;
        object-fit: contain !important;
        display: block;
        margin: 0.5rem auto;
        border-radius: 8px;
        cursor: pointer;
    }

    .quiz-inline-image-scroll {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        text-align: center;
        padding: 4px 0;
        margin: 0.5rem 0;
    }

    .quiz-inline-image-scroll img {
        width: auto !important;
        max-width: none !important;
        max-height: 280px !important;
        height: auto !important;
        object-fit: contain !important;
        display: inline-block;
        margin: 0 auto;
        cursor: pointer;
    }

    .image-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 200px;
        background: #f8f9fa;
        border-radius: 8px;
        position: relative;
    }

    .image-loader .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid rgb(var(--qr-accent-rgb));
        border-radius: 50%;
        animation: qr-spin 1s linear infinite;
    }

    @keyframes qr-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .question-image .quiz-image-preview { display: none; }
    .question-image .quiz-image-preview.loaded { display: block; }

    @keyframes confettiFall {
        0% { transform: translateY(0) rotate(0deg); opacity: 1; }
        100% { transform: translateY(200px) rotate(360deg); opacity: 0; }
    }

    .confetti-piece {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        opacity: 0.9;
        top: 0;
        pointer-events: none;
        animation-name: confettiFall;
        animation-timing-function: ease-out;
        animation-fill-mode: forwards;
    }

    #encouragement.show {
        display: inline-block;
        animation: fadeUp 1.5s ease forwards;
    }

    @keyframes fadeUp {
        0% { opacity: 0; transform: translateY(10px); }
        100% { opacity: 1; transform: translateY(-10px); }
    }

    .result-status { position: relative; }

    @media (max-width: 991.98px) {
        .qr-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .qr-score-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 767px) {
        .qr-stats { grid-template-columns: 1fr; }
        .result-question .mb-1 {
            flex-direction: column;
            align-items: flex-start;
        }
        .question-title-content img,
        .result-question-title-content img,
        .question-rich-image { max-height: 260px !important; }
        .quiz-inline-image-scroll img { max-height: 240px !important; }
        .quiz-image-frame .quiz-image-preview { max-height: 240px; }
        .answer-container .preview-img { max-height: 160px; }
        #imageModal #modalImage { max-width: 96vw; max-height: 80vh; }
    }
</style>

<div class="qr-wrap">
    <div class="result">
        <div class="qr-hero {{ $passed ? '' : 'is-fail' }}">
            <div class="qr-hero__top">
                <div>
                    <span class="qr-hero__eyebrow">{{ get_phrase('نتيجة الاختبار') }}</span>
                    <h3 class="qr-hero__title">{{ $quiz->title }}</h3>
                    <div class="qr-hero__status result-status">
                        <span id="statusText">
                            {{ $passed ? get_phrase('ناجح') : get_phrase('راسب') }}
                        </span>
                        @if (! $passed)
                            <span id="encouragement" class="text-warning" style="display:none;">
                                {{ get_phrase('حاول مرة أخرى وستتحسن بالتأكيد!') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="qr-rank">
                    <span class="qr-rank__label">{{ get_phrase('ترتيبك') }}</span>
                    <span class="qr-rank__value">#{{ $rank ?: '—' }}</span>
                    <span class="qr-rank__sub">
                        {{ get_phrase('من أصل') }} {{ $participants }} {{ get_phrase('مختبر') }}
                    </span>
                </div>
            </div>

            <div class="qr-score-row">
                <div class="qr-score-box">
                    <span>{{ get_phrase('درجتك') }}</span>
                    <strong>{{ $obtainedScore }} / {{ $quiz->total_mark }}</strong>
                </div>
                <div class="qr-progress-box">
                    <span>{{ get_phrase('نسبة الإجابات الصحيحة') }} · {{ $successRate }}%</span>
                    <div class="qr-progress-bar">
                        <i id="qrSuccessBar" style="width: {{ $successRate }}%"></i>
                    </div>
                    <div class="mt-2" style="font-size:.8rem;opacity:.9;">
                        {{ get_phrase('أفضل من') }} {{ $percentile }}% {{ get_phrase('من المختبرين') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="qr-stats">
            <div class="qr-stat info-card">
                <div class="qr-stat__icon"><i class="fi fi-rr-trophy"></i></div>
                <div>
                    <span class="qr-stat__label">{{ get_phrase('إجمالي الدرجات') }}</span>
                    <span class="qr-stat__value">{{ $quiz->total_mark }}</span>
                </div>
            </div>
            <div class="qr-stat info-card is-ok">
                <div class="qr-stat__icon"><i class="fi fi-rr-check"></i></div>
                <div>
                    <span class="qr-stat__label">{{ get_phrase('الأسئلة الصحيحة') }}</span>
                    <span class="qr-stat__value">{{ count($correct_answers) }}</span>
                </div>
            </div>
            <div class="qr-stat info-card is-wrong">
                <div class="qr-stat__icon"><i class="fi fi-rr-cross-small"></i></div>
                <div>
                    <span class="qr-stat__label">{{ get_phrase('الأسئلة الخاطئة') }}</span>
                    <span class="qr-stat__value">{{ count($wrong_answers) }}</span>
                </div>
            </div>
            <div class="qr-stat info-card is-miss">
                <div class="qr-stat__icon"><i class="fi fi-rr-interrogation"></i></div>
                <div>
                    <span class="qr-stat__label">{{ get_phrase('غير مجابة') }}</span>
                    <span class="qr-stat__value">{{ $unanswered_questions }}</span>
                </div>
            </div>
        </div>

        @if (count($leaderboard) > 0)
            <div class="qr-board">
                <div class="qr-board__head">
                    <div>
                        <h4 class="qr-board__title">{{ get_phrase('لوحة المتصدرين') }}</h4>
                        <p class="qr-board__hint">{{ get_phrase('أفضل النتائج بين من اختبروا هذا الاختبار') }}</p>
                    </div>
                </div>
                <div class="qr-board__list">
                    @foreach ($leaderboard as $row)
                        <div class="qr-board__row {{ !empty($row['is_me']) ? 'is-me' : '' }}">
                            <div class="qr-board__place">{{ $row['rank'] }}</div>
                            <div class="qr-board__user">
                                <img class="qr-board__avatar"
                                    src="{{ get_image($row['photo'] ?? '') }}"
                                    alt="{{ $row['name'] }}">
                                <p class="qr-board__name">
                                    {{ $row['name'] }}
                                    @if (!empty($row['is_me']))
                                        <small>({{ get_phrase('أنت') }})</small>
                                    @endif
                                </p>
                            </div>
                            <div class="qr-board__score">{{ $row['score'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($quiz->show_answer)
            <h4 class="qr-section-title">{{ get_phrase('مراجعة الإجابات') }}</h4>
            @foreach ($questions as $key => $question)
                @php
                    if ($question->type == 'true_false') {
                        $given_answer = $question->answer;
                    } else {
                        $decoded = json_decode($question->answer, true);
                        $answers = is_array($decoded) ? $decoded : [$question->answer];
                        $given_answer = implode(', ', $answers);
                    }
                    $user_answers = array_key_exists($question->id, $userSubmits) ? $userSubmits[$question->id] : [];

                    if (in_array($question->id, $correct_answers)) {
                        $question_class = 'correct';
                    } elseif (in_array($question->id, $wrong_answers)) {
                        $question_class = 'wrong';
                    } else {
                        $question_class = 'unanswered';
                    }
                @endphp

                <div class="result-question {{ $question_class }}">
                    <div class="mb-1 d-flex align-items-center gap-3">
                        <span class="serial">{{ ++$key }}</span>
                        <div class="result-question-title-content">{!! $question->title !!}</div>
                    </div>
                    @if (isset($question->question_image))
                        <div class="question-image mt-3">
                            <div class="quiz-image-frame">
                                <div class="image-loader" id="loader-result-{{ $question->id }}">
                                    <div class="spinner"></div>
                                </div>
                                <img src="{{ asset($question->question_image) }}"
                                     class="quiz-image-preview preview-img"
                                     alt="Question Image"
                                     data-question-id="{{ $question->id }}"
                                     onload="hideImageLoader('result-{{ $question->id }}')"
                                     onerror="hideImageLoader('result-{{ $question->id }}')">
                                <div class="question-image-actions">
                                    <button type="button" class="view-image-btn" onclick="openResultImageModal('{{ asset($question->question_image) }}')" title="تكبير الصورة">
                                        <i class="fi fi-rr-zoom-in"></i>
                                        <span>تكبير الصورة</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row gap-0">
                        @if ($question->type == 'mcq')
                            @php $options = json_decode($question->options, true) ?? []; @endphp
                            @foreach ($options as $index => $option)
                                @php
                                    $isSelected = $user_answers && in_array($option, $user_answers);
                                    $isCorrect = in_array($option, json_decode($question->answer, true) ?? []);
                                    $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $option);
                                @endphp
                                <div class="col-sm-6 my-2">
                                    <div class="answer-container @if(in_array($question->id, $wrong_answers) && $isSelected) answer-wrong @elseif(in_array($question->id, $correct_answers) && $isSelected) answer-correct @elseif($isCorrect && !$isSelected) answer-missed @endif ">
                                        <input class="form-check-input" type="checkbox" value="{{ $option }}" @if ($isSelected) checked @endif disabled>
                                        @if ($isImage)
                                            <img src="{{ asset($option) }}" alt="Option Image" class="preview-img">
                                        @else
                                            {{ $option }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @elseif ($question->type == 'fill_blanks')
                            <input type="text" class="form-control tagify" data-role="tagsinput" value="{{ json_encode($user_answers) }}" disabled>
                        @elseif ($question->type == 'true_false')
                            <div class="col-sm-6">
                                <div class="answer-container @if($given_answer == 'false' && $user_answers == 'true') answer-wrong @endif">
                                    <input class="form-check-input" type="radio" disabled @if($user_answers == 'true') checked @endif>
                                    <label class="form-check-label">{{ get_phrase('True') }}</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="answer-container @if($given_answer == 'true' && $user_answers == 'false') answer-wrong @endif">
                                    <input class="form-check-input" type="radio" disabled @if($user_answers == 'false') checked @endif>
                                    <label class="form-check-label">{{ get_phrase('False') }}</label>
                                </div>
                            </div>
                        @endif

                        <p class="qr-answer-label text-capitalize">
                            {{ get_phrase('Answer : ') }}
                            @if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $given_answer))
                                <img src="{{ asset($given_answer) }}" alt="Answer Image" class="preview-img">
                            @else
                                {{ $given_answer }}
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info text-center my-4" role="alert">
                <i class="fi fi-rr-lock me-2"></i>
                {{ get_phrase('Answers review is not available yet. You can only see your score until the instructor enables answer review.') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12 d-flex gap-3 justify-content-center">
                <button type="button" class="eBtn gradient border-0 mb-4 d-flex align-items-center gap-2" id="backBtn" onclick="back()">
                    <i class="fi fi-rr-angle-small-left fs-5"></i>{{ get_phrase('رجوع') }}
                </button>
            </div>
        </div>

        <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body p-0 text-center">
                        <img src="" id="modalImage" class="rounded" alt="Preview">
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const passed = {{ $passed ? 'true' : 'false' }};
const container = document.querySelector('.result-status');

if (passed) {
    function createConfetti(){
        const confetti = document.createElement('span');
        confetti.classList.add('confetti-piece');
        confetti.style.background = `hsl(${Math.random()*360}, 70%, 65%)`;
        confetti.style.left = `${Math.random()*100}%`;
        confetti.style.width = `${8 + Math.random()*5}px`;
        confetti.style.height = confetti.style.width;
        confetti.style.animationDuration = `${1 + Math.random()*1.5}s`;
        container.appendChild(confetti);
        confetti.addEventListener('animationend', ()=> confetti.remove());
    }
    const confettiInterval = setInterval(createConfetti, 500);
    window.addEventListener('beforeunload', ()=> clearInterval(confettiInterval));
} else {
    const msg = document.getElementById('encouragement');
    if (msg) msg.classList.add('show');
}

var imageModal = new bootstrap.Modal(document.getElementById('imageModal'), {
  keyboard: true
});

function openResultImageModal(src) {
    document.getElementById('modalImage').src = src;
    imageModal.show();
}

function initQuizTitleImages() {
    document.querySelectorAll('.result-question-title-content img').forEach(function(img) {
        if (img.dataset.quizImageReady === '1') {
            return;
        }

        function setupImage() {
            if (img.dataset.quizImageReady === '1') {
                return;
            }

            img.dataset.quizImageReady = '1';
            img.classList.add('question-rich-image', 'preview-img');

            var naturalWidth = img.naturalWidth || 0;
            var naturalHeight = img.naturalHeight || 0;
            if (naturalWidth > 0 && naturalHeight > 0 && naturalWidth / naturalHeight > 1.6) {
                if (!img.closest('.quiz-inline-image-scroll')) {
                    var scrollWrap = document.createElement('div');
                    scrollWrap.className = 'quiz-inline-image-scroll';
                    img.parentNode.insertBefore(scrollWrap, img);
                    scrollWrap.appendChild(img);
                }
            }

            img.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                openResultImageModal(this.src);
            });
        }

        if (img.complete && img.naturalWidth > 0) {
            setupImage();
        } else {
            img.addEventListener('load', setupImage, { once: true });
        }
    });
}

document.querySelectorAll('.preview-img').forEach(img => {
    if (img.dataset.quizImageReady === '1') {
        return;
    }

    img.addEventListener('click', function(e){
        e.stopPropagation();
        openResultImageModal(this.src);
    });
});

initQuizTitleImages();

function back() {
    if (typeof description !== 'undefined' && description) description.classList.remove('d-none');
    if (typeof starterContainer !== 'undefined' && starterContainer) starterContainer.classList.remove('d-none');
    document.querySelector('.result')?.closest('.qr-wrap')?.remove() || document.querySelector('.result')?.remove();
}

$('.result .tagify:not(.inited)').each(function(index, element) {
    var tagify = new Tagify(element, {
        placeholder: '{{ get_phrase('Enter your keywords') }}'
    });
    $(element).addClass('inited');
});

document.querySelectorAll('.info-card, .result-question').forEach((el, i)=>{
    setTimeout(()=>{ el.style.opacity = 1; el.style.transform = 'translateY(0)'; }, i*120);
});

function hideImageLoader(loaderId) {
    const loader = document.getElementById('loader-' + loaderId);
    if (loader) {
        loader.style.display = 'none';
    }

    const questionImage = loader ? loader.closest('.question-image') : null;
    if (questionImage) {
        const img = questionImage.querySelector('.quiz-image-preview');
        if (img) {
            img.classList.add('loaded');
        }
    }
}
</script>
