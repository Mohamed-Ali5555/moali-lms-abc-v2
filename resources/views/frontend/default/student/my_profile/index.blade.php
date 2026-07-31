@extends('layouts.default')
@push('title', get_phrase('My profile'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
    <!------------ My profile area start  ------------>
    <section class="course-content">
        <div class="profile-banner-area"></div>
        <div class="container profile-banner-area-container">
            <div class="row">
                @include('frontend.default.student.left_sidebar')
                <div class="col-lg-9">
                    <h4 class="g-title mb-5">{{ get_phrase('Personal Information') }}</h4>
                    <div class="my-panel message-panel edit_profile">
                        <form action="{{ route('update.profile', $user_details->id) }}" method="POST">@csrf
                            <div class="row">
                                <div class="col-lg-12 mb-20">
                                    <div class="form-group">
                                        <label for="name" class="form-label">{{ get_phrase('Full Name') }}</label>
                                        <input type="text" class="form-control" name="name" value="{{ $user_details->name }}" id="name">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="email" class="form-label">{{ get_phrase('Email Address') }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ $user_details->email }}" id="email">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">{{ get_phrase('Phone Number') }}</label>
                                        <input type="tel" class="form-control" name="phone" value="{{ $user_details->phone }}" id="phone">
                                    </div>
                                </div>



                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">{{ get_phrase('parent Phone Number') }}</label>
                                        <input type="tel" class="form-control" name="parent_phone" value="{{ old('parent_phone',$user_details->parent_phone) }}" id="parent_phone">
                                    </div>
                                </div>

                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="national_id" class="form-label">{{ get_phrase('رقم الإقامة') }}</label>
                                        <input type="text" class="form-control" name="national_id"
                                            value="{{ old('national_id',$user_details->national_id) }}" id="national_id"
                                            maxlength="10" inputmode="numeric" pattern="[12][0-9]{9}" required>
                                    </div>
                                </div>


                               <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="goverment" class="form-label">{{ get_phrase('المنطقة') }}</label>
                                        <select class="ol-select2 form-control ol-select2-multiple" id="goverment" name="goverment" required>
                                            <option value="" disabled>{{ get_phrase('اختر المنطقة') }}</option>
                                            @foreach (get_saudi_regions() as $region)
                                                <option value="{{ $region }}" {{ isset($user_details) && $user_details->goverment == $region ? 'selected' : '' }}>
                                                    {{ $region }}
                                                </option>
                                            @endforeach
                                    </select>                     
                                
                                    </div>
                                </div>



                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="category" class="form-label">{{ get_phrase('category') }}</label>
                                        <select class="ol-select2 form-control ol-select2-multiple" id="category" name="category" required>
                                            <option value="" disabled>please select category</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                {{ $category->id == $user_details->category ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
                                                    @endforeach
                                        </select>
                                
                                        @error('category')
                                            <div id="validationServer04Feedback" class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror         
                                
                                    </div>
                                </div>
                               <div class="col-lg-12 mb-20">
                                    <div class="form-group">
                                        <label for="address" class="form-label">{{ get_phrase('address') }}</label>
                                        <input type="text" class="form-control" name="address" value="{{ $user_details->address }}" id="address">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="password" class="form-label">{{ get_phrase('Old-password') }}</label>
                                        <input type="password" class="form-control" name="old_password" value="{{ old('old_password') }}" id="old_password">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-20">
                                    <div class="form-group">
                                        <label for="new_password" class="form-label">{{ get_phrase('New-password') }}</label>
                                        <input type="password" class="form-control" name="new_password"  value="{{ old('new_password') }}"  id="new_password">
                                    </div>
                                </div>
                            </div>
                            <button class="eBtn btn gradient mt-10">{{ get_phrase('Save Changes') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!------------ My profile area end  ------------>
@endsection
@push('js')

@endpush
