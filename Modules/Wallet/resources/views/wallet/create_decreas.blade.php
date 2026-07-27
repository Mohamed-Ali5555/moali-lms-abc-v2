
@php
    $users = App\Models\User::where('role', 'student')->get();
    $students = App\Models\User::where('role', 'student')->get();


@endphp

<form action="{{ route('admin.wallet.store_decreas') }}" method="post" enctype="multipart/form-data">
    @CSRF

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label for="balance" class="form-label ol-form-label">{{ get_phrase('balance') }}</label>
                <input type="number" name="balance" class="form-control ol-form-control" id="balance"  oninput="this.value = Math.max(1, Math.abs(this.value))"
                   value="" required>
            </div>



            <div class="mb-3">
                <label for="type"
                    class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('students') }}<span
                        class="text-danger ms-1">*</span></label>
                        <select for='multiple_student_id' class="ol-select2" id="users"  data-toggle="select2" multiple="multiple" name="student_id[]"
                        id="multiple_student_id" data-placeholder="Choose ..." required>
                        <option value="" disabled>please select student</option>
                        @foreach ($users as $user)
                            <option value="{{$user->id}}">{{$user->name}} ({{$user->phone}})</option>
                        @endforeach
                    </select>

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
                <button class="btn ol-btn-primary">{{ get_phrase('Submit') }}</button>
            </div>
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
