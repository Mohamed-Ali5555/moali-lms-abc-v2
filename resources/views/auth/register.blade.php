@extends('layouts.' . get_frontend_settings('theme'))
@push('title', get_phrase('Sign Up'))
@push('meta')@endpush
@push('css')
    <style>
        .form-icons .right {
            right: 20px;
            cursor: pointer !important;
        }
    </style>
@endpush
@section('content')
    <section class="login-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-6">
                    <div class="login-img">
                        <img src="{{ asset('assets/frontend/' . get_frontend_settings('theme') . '/image/signup.gif') }}" alt="register-banner">
                    </div>
                </div>
                <div class="col-lg-5 col-md-6">
                    <form action="{{ route('register') }}" class="global-form login-form mt-25" id="login-form" method="post">@csrf
                        <h4 class="g-title">{{ get_phrase('Sign Up') }}</h4>
                        <p class="description">{{ get_phrase('See your growth and get consulting support! ') }}</p>
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('Name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="Your Name">

                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('Email') }}</label>
                            <input type="email" name="email" class="form-control" placeholder="Your Email">

                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('Password') }}</label>
                            <input type="password" name="password" class="form-control" placeholder="*********">

                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


{{-- new fields --}}
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('phone') }}</label>
                            <input type="number" name="phone" class="form-control" placeholder="Your phone">

                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('parent_phone') }}</label>
                            <input type="number" name="parent_phone" class="form-control" placeholder="Your parent_phone">

                            @error('parent_phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('رقم الإقامة') }}</label>
                            <input type="text" name="national_id" class="form-control"
                                placeholder="{{ get_phrase('10 أرقام — يبدأ بـ 1 أو 2') }}"
                                maxlength="10" inputmode="numeric" pattern="[12][0-9]{9}" required>

                            @error('national_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- category --}}
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('category') }}</label>
                            <select class="ol-select2 form-control ol-select2-multiple"  id="category" name="category" required>
                                <option value="" disabled>please select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        {{-- goverment --}}
                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('المنطقة') }}</label>
                            <select class="ol-select2 form-control ol-select2-multiple" id="goverment" name="goverment" required>
                                <option value="" disabled>{{ get_phrase('اختر المنطقة') }}</option>
                                @foreach (get_saudi_regions() as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('goverment')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>


                        <div class="form-group mb-5">
                            <label for="" class="form-label">{{ get_phrase('address') }}</label>
                            <input type="text" name="address" class="form-control" placeholder="Your address">

                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
{{-- end new fields --}}

                        @if(get_frontend_settings('recaptcha_status'))
                            <button class="eBtn gradient w-100 g-recaptcha" data-sitekey="{{ get_frontend_settings('recaptcha_sitekey') }}" data-callback='onLoginSubmit' data-action='submit'>{{ get_phrase('Sign Up') }}</button>
                        @else
                            <button type="submit" class="eBtn gradient w-100">{{ get_phrase('Sign Up') }}</button>
                        @endif

                        <p class="mt-20">{{ get_phrase('Already have account.') }} <a href="{{ route('login') }}">{{ get_phrase('Sign in') }}</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('js')
    <script>
        "use strict";

        $(document).ready(function() {
            $('#showpassword').on('click', function(e) {
                e.preventDefault();
                const type = $('#password').attr('type');

                if (type == 'password') {
                    $('#password').attr('type', 'text');
                } else {
                    $('#password').attr('type', 'password');
                }
            });
        });

        $(document).ready(function() {
            $('#showcpassword').on('click', function(e) {
                e.preventDefault();
                const type = $('#cpassword').attr('type');

                if (type == 'password') {
                    $('#cpassword').attr('type', 'text');
                } else {
                    $('#cpassword').attr('type', 'password');
                }
            });
        });

        function onLoginSubmit(token) {
            document.getElementById("login-form").submit();
        }

    </script>
@endpush
