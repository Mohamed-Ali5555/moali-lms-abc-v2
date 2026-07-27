@php
    $participants = DB::table('quiz_submissions')
        ->join('users', 'quiz_submissions.user_id', 'users.id')
        ->where('quiz_submissions.quiz_id', $id)
        ->select('users.name', 'users.id')
        ->distinct('quiz_submissions.user_id')
        ->get();
@endphp

<div class="qr-layout">
    <aside class="qr-sidebar">
        <div class="qr-sidebar__head">
            <h6 class="qr-sidebar__title">
                <i class="fi fi-rr-users"></i>
                {{ get_phrase('Students') }}
            </h6>
            <span class="qr-sidebar__count">{{ $participants->count() }}</span>
        </div>

        <div class="qr-participants">
            @forelse ($participants as $participant)
                @php
                    $name = trim((string) $participant->name);
                    $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
                    $initials = '';
                    if (count($parts) >= 2) {
                        $initials = mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1);
                    } elseif (count($parts) === 1) {
                        $initials = mb_substr($parts[0], 0, 2);
                    } else {
                        $initials = '?';
                    }
                @endphp
                <input type="radio" class="btn-check" name="participants" id="participant-{{ $participant->id }}"
                    value="{{ $participant->id }}" autocomplete="off">
                <label class="qr-participant" for="participant-{{ $participant->id }}"
                    data-participant-id="{{ $participant->id }}" onclick="loadResult(this)">
                    <span class="qr-participant__avatar">{{ $initials }}</span>
                    <span class="qr-participant__meta">
                        <span class="qr-participant__name">{{ $name }}</span>
                        <span class="qr-participant__hint">{{ get_phrase('عرض النتيجة') }}</span>
                    </span>
                </label>
            @empty
                <div class="qr-empty qr-empty--sidebar">
                    <div class="qr-empty__icon"><i class="fi-rr-users"></i></div>
                    <p>{{ get_phrase('لا يوجد طلاب بعد') }}</p>
                </div>
            @endforelse
        </div>
    </aside>

    <div class="qr-preview result-preview" data-empty="{{ get_phrase('اختر طالباً لعرض نتيجته') }}">
        <div class="qr-empty">
            <div class="qr-empty__icon"><i class="fi-rr-eye"></i></div>
            <strong>{{ get_phrase('معاينة النتيجة') }}</strong>
            <p>{{ get_phrase('اختر طالباً من القائمة لعرض محاولاته وتفاصيل الإجابات') }}</p>
        </div>
    </div>
</div>

<script>
    "use strict";

    function loadResult(elem) {
        let participantId = $(elem).data('participant-id') || $(elem).attr('for');
        if (participantId && String(participantId).indexOf('participant-') === 0) {
            participantId = String(participantId).replace('participant-', '');
        }
        let quizId = "{{ $id }}";

        $('.qr-participant').removeClass('is-active');
        $(elem).addClass('is-active');

        if (quizId && participantId) {
            $('.result-preview').html(
                '<div class="qr-loading"><span class="qr-loading__spinner"></span><span>{{ get_phrase('جاري تحميل النتيجة...') }}</span></div>'
            );

            $.ajax({
                type: "get",
                url: "{{ route('admin.quiz.result.preview') }}",
                data: {
                    quizId: quizId,
                    participantId: participantId,
                },
                success: function(response) {
                    $('.result-preview').html(response);
                },
                error: function() {
                    $('.result-preview').html(
                        '<div class="qr-empty"><div class="qr-empty__icon"><i class="fi-rr-exclamation"></i></div><strong>{{ get_phrase('حدث خطأ') }}</strong><p>{{ get_phrase('تعذر تحميل نتيجة الطالب') }}</p></div>'
                    );
                }
            });
        }
    }
</script>
@include('admin.init')
