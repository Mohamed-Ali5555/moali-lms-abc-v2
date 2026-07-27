
@php
    $users = App\Models\User::where('role', 'student')->get();
    $students = App\Models\User::where('role', 'student')->get();


@endphp

<form action="{{ route('admin.wallet.store') }}" method="post" enctype="multipart/form-data">
    @CSRF

    <div class="tf-modal-form">
        <div class="mb-3">
            <label for="balance" class="form-label ol-form-label">{{ get_phrase('balance') }}</label>
            <input type="number" name="balance" class="form-control ol-form-control" id="balance" oninput="this.value = Math.max(1, Math.abs(this.value))"
               value="" required>
            <small class="form-text text-muted">{{ get_phrase('Amount to add to the selected student wallets.') }}</small>
        </div>

        <div class="mb-3">
            <label for="icon-picker" class="form-label ol-form-label">{{ get_phrase('method') }}</label>
            <select class="form-control ol-form-control" name="type">
                <option value="" disabled>please method of payment</option>
                <option value="by_hand" {{ old('type') == 'by_hand' ? 'selected' : '' }}>
                    كاش
                </option>
                <option value="gift" {{ old('type') == 'gift' ? 'selected' : '' }}>
                    هديه
                </option>
            </select>

            @error('type')
                <div id="validationServer04Feedback" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type"
                class="form-label ol-form-label">{{ get_phrase('students') }}<span
                    class="text-danger ms-1">*</span></label>
            <select for='multiple_student_id' class="form-control ol-form-control ol-select2" id="users" data-toggle="select2" multiple="multiple" name="student_id[]"
                id="multiple_student_id" data-placeholder="Choose ..." required>
                <option value="" disabled>please select student</option>
                @foreach ($users as $user)
                    <option value="{{$user->id}}">{{$user->name}} ({{$user->phone}})</option>
                @endforeach
            </select>
            <small class="form-text text-muted">{{ get_phrase('You can select more than one student.') }}</small>

            @error('student_id')
                <div id="validationServer04Feedback" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="note" class="form-label ol-form-label">{{ get_phrase(' note') }}
                <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <textarea name="note" rows="4" class="form-control ol-form-control" id="note"
                placeholder="{{ get_phrase('Enter your note') }}" aria-label="{{ get_phrase('Enter your note') }}"></textarea>
        </div>

        <div class="mb-2">
            <button type="submit" class="btn ol-btn-primary w-100 mt-2">{{ get_phrase('Submit') }}</button>
        </div>
    </div>
</form>


<script type="text/javascript">
    "use strict";

    $(function() {
        if ($('.icon-picker').length) {
            $('.icon-picker').iconpicker();
        }
    });



$(document).ready(function() {

 $('#users').select2()

});
</script>
