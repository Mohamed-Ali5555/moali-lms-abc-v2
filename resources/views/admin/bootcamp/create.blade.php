@extends('layouts.admin')
@push('title', get_phrase('Create bootcamp'))

@section('content')
    <div class="row mb-5">
        <div class="col-lg-12">
            <div class="ol-card radius-8px">
                <div class="ol-card-body my-3 py-4 px-20px">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap flex-md-nowrap">
                        <h4 class="title fs-16px">
                            <i class="fi-rr-settings-sliders me-2"></i>
                            {{ get_phrase('Add new Bootcamp') }}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="ol-card p-3">
                <div class="ol-card-body">
                    <form action="{{ route('admin.bootcamp.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 pb-2">
                                <div class="eForm-layouts">
                                    <div class="fpb-7 mb-3">
                                        <label class="form-label ol-form-label"
                                            for="title">{{ get_phrase('Title') }}<span
                                                class="text-danger ms-1">*</span></label>
                                        <input type="text" name = "title" class="form-control ol-form-control"
                                            placeholder="{{ get_phrase('Enter Bootcamp Title') }}" required>
                                    </div>

                                    <div class="fpb-7 mb-3">
                                        <label class="form-label ol-form-label"
                                            for="description">{{ get_phrase('Description') }}</label>
                                        <textarea name="description" placeholder="{{ get_phrase('Enter Description') }}"
                                            class="form-control ol-form-control text_editor"></textarea>
                                    </div>

                                    <div class="fpb-7 mb-2 ">
                                        <label for="course_status"
                                            class="col-sm-2 col-form-label">{{ get_phrase('Create as') }}
                                            <span class="text-danger ms-1">*</span></label>
                                        <div class="eRadios">
                                            <div class="form-check">
                                                <input type="radio" value="1" name="status"
                                                    class="form-check-input eRadioSuccess" id="status_active" required
                                                    checked>
                                                <label for="status_active"
                                                    class="form-check-label">{{ get_phrase('Active') }}</label>
                                            </div>
                                            {{--
                                            <div class="form-check">
                                                <input type="radio" value="private"  name="status" class="form-check-input eRadioPrimary" id="status_private" required>
                                                <label for="status_private" class="form-check-label">{{ get_phrase('Private') }}</label>
                                            </div>

                                            <div class="form-check">
                                                <input type="radio" value="upcoming" name="status" class="form-check-input eRadioInfo" id="status_upcoming" required>
                                                <label for="status_upcoming" class="form-check-label">{{ get_phrase('Upcoming') }}</label>
                                            </div>

                                            <div class="form-check">
                                                <input type="radio" value="pending" name="status" class="form-check-input eRadioDanger" id="status_pending" required>
                                                <label for="status_pending" class="form-check-label">{{ get_phrase('Pending') }}</label>
                                            </div>

                                            <div class="form-check">
                                                <input type="radio" value="draft" name="status" class="form-check-input eRadioSecondary" id="status_draft" required>
                                                <label for="status_draft" class="form-check-label">{{ get_phrase('Draft') }}</label>
                                            </div> --}}

                                            <div class="form-check">
                                                <input type="radio" value="0" name="status"
                                                    class="form-check-input eRadioDark" id="status_inactive" required>
                                                <label for="status_inactive"
                                                    class="form-check-label">{{ get_phrase('Inactive') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="eForm-layouts">
                                    <div class="fpb-7 mb-3">
                                        <label for="category_id"
                                            class="form-label ol-form-label">{{ get_phrase('Category') }}<span
                                                class="text-danger ms-1">*</span></label>
                                        <select class="ol-select2" name="category_id" id="category_id" required>
                                            <option value="">{{ get_phrase('Select a category') }}</option>
                                            @foreach (App\Models\BootcampCategory::orderBy('title', 'asc')->get() as $category)
                                                <option value="{{ $category->id }}"> {{ $category->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="fpb-7 mb-3">
                                        <label
                                            class="form-label ol-form-label col-sm-2 col-form-label w-100">{{ get_phrase('Pricing type') }}<span
                                                class="text-danger ms-1">*</span></label>

                                        <div class="eRadios">
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input type="radio" name="is_paid" value="1"
                                                        class="form-check-input eRadioSuccess" id="paid"
                                                        onchange="$('#paid-section').slideDown(200)" checked>
                                                    <label for="paid"
                                                        class="form-check-label">{{ get_phrase('Paid') }}</label>
                                                </div>

                                                <div class="form-check">
                                                    <input type="radio" name="is_paid" value="0"
                                                        class="form-check-input eRadioSuccess" id="free"
                                                        onchange="$('#paid-section').slideUp(200)">
                                                    <label for="free"
                                                        class="form-check-label">{{ get_phrase('Free') }}</label>
                                                </div>
                                            </div>
                                            <div class="paid-section" id="paid-section">
                                                <div class="fpb-7 mb-3">
                                                    <label for="price"
                                                        class="form-label ol-form-label">{{ get_phrase('Price') }}
                                                        <small>({{ currency() }})</small><span
                                                            class="text-danger ms-1">*</span></label>

                                                    <input type="number" name="price"
                                                        class="form-control ol-form-control" id="price" min="1"
                                                        step=".01"
                                                        placeholder="{{ get_phrase('Enter your bootcamp price') }} ({{ currency() }})">
                                                </div>

                                                <div class="fpb-7 mb-3">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="discount_flag" value="1"
                                                            class="form-check-input eRadioSuccess" id="discount_flag">
                                                        <label for="discount_flag"
                                                            class="form-check-label">{{ get_phrase('Check if this bootcamp has discount') }}</label>
                                                    </div>
                                                </div>

                                                <div class="row mb-3" id="discount_price_row" style="display: none;">
                                                    <label for="discount_price"
                                                        class="form-label ol-form-label">{{ get_phrase('Discounted price') }}</label>

                                                    <input type="number" name="discount_price"
                                                        class="form-control ol-form-control" id="discount_price"
                                                        min="1" step=".01"
                                                        placeholder="{{ get_phrase('Enter your discount price') }} ({{ currency() }})">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="fpb-7 mb-3">
                                    <label for="thumbnail"
                                        class="form-label ol-form-label">{{ get_phrase('Thumbnail') }}</label>
                                    <input type="file" name="thumbnail" class="form-control ol-form-control"
                                        id="thumbnail" accept="image/*" />
                                </div>
                                <div class="fpb-7">
                                    <label class="form-label ol-form-label"
                                        for="publish_date">{{ get_phrase('Publish Date') }}</label>
                                    <input type="date" name="publish_date" id="publish_date"
                                        class="form-control ol-form-control">
                                </div>
                            </div>
                            <div class="pt-2">
                                <button type="submit"
                                    class="btn ol-btn-primary float-end">{{ get_phrase('Submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        "Use strict";

        //Start progress
        var totalSteps = $('#v-pills-tab .nav-link').length
        var progressVal = 100 / totalSteps;
        $(function() {
            var pValPerItem = progressVal;
            $('#courseFormProgress .progress-bar').attr('aria-valuemin', 0);
            $('#courseFormProgress .progress-bar').attr('aria-valuemax', pValPerItem);
            $('#courseFormProgress .progress-bar').attr('aria-valuenow', pValPerItem);
            $('#courseFormProgress .progress-bar').width(pValPerItem + '%');
            $('#courseFormProgress .progress-bar').text("Step 1 out of " + totalSteps);
        });

        $("#v-pills-tab .nav-link").click(function() {
            var currentStep = $("#v-pills-tab .nav-link").index(this) + 1;
            var pValPerItem = currentStep * progressVal;
            $('#courseFormProgress .progress-bar').attr('aria-valuemin', 0);
            $('#courseFormProgress .progress-bar').attr('aria-valuemax', pValPerItem);
            $('#courseFormProgress .progress-bar').attr('aria-valuenow', pValPerItem);
            $('#courseFormProgress .progress-bar').width(pValPerItem + '%');
            $('#courseFormProgress .progress-bar').text("Step " + currentStep + " out of " + totalSteps);

            if (currentStep == totalSteps) {
                $('#courseFormProgress .progress-bar').text("{{ get_phrase('Finish!') }}");
                $('#courseFormProgress .progress-bar').addClass('bg-success');
            } else {
                $('#courseFormProgress .progress-bar').removeClass('bg-success');
            }
        });
        //End progress


        $(document).ready(function() {
            if ($('#discount_flag').is(':checked')) {
                $('#discount_price_row').show();
            }

            $('#discount_flag').change(function() {
                if ($(this).is(':checked')) {
                    $('#discount_price_row').fadeIn();
                } else {
                    $('#discount_price_row').fadeOut();
                }
            });
        });
    </script>
@endpush
