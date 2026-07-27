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
                        <span class="qr-participant__hint">{{ get_phrase('View result') }}</span>
                    </span>
                </label>
            @empty
                <div class="text-center text-muted py-4 px-2" style="font-size:13px;font-weight:500;">
                    {{ get_phrase('No students found') }}
                </div>
            @endforelse
        </div>
    </aside>

    <div class="qr-preview result-preview" data-empty="{{ get_phrase('Select a student to view their result') }}"></div>
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
            $.ajax({
                type: "get",
                url: "{{ route('instructor.quiz.result.preview') }}",
                data: {
                    quizId: quizId,
                    participantId: participantId,
                },
                success: function(response) {
                    $('.result-preview').html(response);
                }
            });
        }
    }
</script>
@include('instructor.init')
