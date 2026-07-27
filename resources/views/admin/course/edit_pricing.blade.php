<div class="mb-3">
    <h6 class="mb-1">{{ get_phrase('Pricing') }}</h6>
    <small class="text-muted">{{ get_phrase('Set paid or free access, discounts, and expiry.') }}</small>
</div>

<div class="row mb-3">
    <label class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Pricing type') }}<span
            class="text-danger ms-1">*</span></label>
    <div class="col-sm-10">
        <div class="eRadios">
            <div class="form-check">
                <input type="radio" name="is_paid" value="1" class="form-check-input eRadioSuccess" id="paid"
                    onchange="$('#paid-section').slideDown(200)" @if ($course_details->is_paid == 1) checked @endif>
                <label for="paid" class="form-check-label">{{ get_phrase('Paid') }}</label>
            </div>

            <div class="form-check">
                <input type="radio" name="is_paid" value="0" class="form-check-input eRadioSuccess"
                    id="free" onchange="$('#paid-section').slideUp(200)"
                    @if ($course_details->is_paid != 1) checked @endif>
                <label for="free" class="form-check-label">{{ get_phrase('Free') }}</label>
            </div>
        </div>
    </div>
</div>

<div class="paid-section @if ($course_details->is_paid != 1) d-hidden @endif" id="paid-section">
    <div class="row mb-3">
        <label for="price" class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Price') }}
            <small>({{ currency() }})</small><span class="text-danger ms-1">*</span></label>
        <div class="col-sm-10">
            <input type="number" name="price" value="{{ $course_details->price }}"
                class="form-control ol-form-control" id="price" min="1" step=".01"
                placeholder="{{ get_phrase('Enter your course price') }} ({{ currency() }})">
        </div>
    </div>

    <div class="row mb-3">
        <label class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Discount type') }}</label>
        <div class="col-sm-10">
            <div class="eRadios">
                <div class="form-check">
                    <input type="checkbox" name="discount_flag" value="1" class="form-check-input eRadioSuccess"
                        id="discount_flag" @if ($course_details->discount_flag == 1) checked @endif>
                    <label for="discount_flag"
                        class="form-check-label">{{ get_phrase('Check if this course has discount') }}</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3" id="discount_price_row" style="display: none;">
        <label for="discount_price"
            class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Discounted price') }}</label>
        <div class="col-sm-10">
            <input type="number" name="discount_price" value="{{ $course_details->discount_price }}"
                class="form-control ol-form-control" id="discount_price" min="1" step=".01"
                placeholder="{{ get_phrase('Enter your discount price') }} ({{ currency() }})">
        </div>
    </div>
</div>

<div class="row mb-3">
    <label class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Expiry period') }}</label>
    <div class="col-sm-10">
        <div class="eRadios">
            <div class="form-check">
                <input type="radio" id="lifetime_expiry_period" name="expiry_period"
                    class="form-check-input eRadioSuccess" value="lifetime"
                    onchange="$('#number_of_month').slideUp(200)"
                    {{ $course_details->expiry_period ? '' : 'checked' }}>
                <label class="form-check-label" for="lifetime_expiry_period">{{ get_phrase('Lifetime') }}</label>
            </div>
            <div class="form-check">
                <input type="radio" id="limited_expiry_period" name="expiry_period"
                    class="form-check-input eRadioSuccess" value="limited_time"
                    onchange="$('#number_of_month').slideDown(200)"
                    {{ $course_details->expiry_period ? 'checked' : '' }}>
                <label class="form-check-label" for="limited_expiry_period">{{ get_phrase('Limited time') }}</label>
            </div>
        </div>
    </div>
</div>
<div class="number-of-month @if (is_null($course_details->expiry_period)) d-hidden @endif" id="number_of_month">

    <div class="row mb-3">
        <label class="form-label ol-form-label col-sm-2 col-form-label">{{ get_phrase('Number of month') }}</label>
        <div class="col-sm-10">
            <input class="form-control ol-form-control" type="number" name="number_of_month" min="1"
                value="{{ $course_details->expiry_period }}"
                placeholder="{{ get_phrase('After purchase, students can access the course until your selected month.') }}">
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        function toggleDiscountPrice() {
            if ($('#discount_flag').is(':checked')) {
                $('#discount_price_row').fadeIn();
                $('#discount_price').prop('disabled', false);
            } else {
                $('#discount_price_row').fadeOut();
                $('#discount_price').prop('disabled', true);
            }
        }

        toggleDiscountPrice();

        $('#discount_flag').change(function() {
            toggleDiscountPrice();
        });
    });
</script>
