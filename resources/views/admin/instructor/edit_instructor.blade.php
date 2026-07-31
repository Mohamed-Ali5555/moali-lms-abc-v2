@extends('layouts.admin')

@push('title', get_phrase('Edit Instructor'))

@push('meta')
@endpush

@push('css')
@endpush


@section('content')
    <div class="admin-page">
        <div class="tf-workspace tf-workspace--wide">
            <div class="tf-hero">
                <div>
                    <div class="tf-hero__kicker">
                        <i class="fi-rr-chalkboard-user"></i>
                        {{ get_phrase('Instructors') }}
                    </div>
                    <h1 class="tf-hero__title">{{ get_phrase('تعديل بيانات المدرس') }}</h1>
                    <p class="tf-hero__desc">{{ get_phrase('تحديث البيانات الأساسية وبيانات الدخول') }}</p>
                </div>
                <div class="tf-hero__actions">
                    <a href="{{ route('admin.instructor.index') }}" class="tf-btn tf-btn--ghost">
                        <i class="fi-rr-arrow-small-left"></i>
                        {{ get_phrase('Back') }}
                    </a>
                </div>
            </div>

            <div class="ol-card" style="border:none;box-shadow:none;background:transparent;">
                <form action="{{ route('admin.instructor.update', ['id' => $instructor->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex gap-3 flex-wrap flex-md-nowrap">
                        <div class="ol-sidebar-tab">
                            <div class="nav flex-column nav-pills" id="myv-pills-tab" role="tablist" aria-orientation="vertical">
                                <button class="nav-link text-start active" id="v-pills-tab1-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tab1" type="button" role="tab"
                                    aria-controls="v-pills-tab1" aria-selected="true">
                                    <span class="icon fi-rr-duplicate"></span>
                                    <span>{{ get_phrase('Basic') }}</span>
                                </button>
                                <button class="nav-link text-start" id="v-pills-tab2-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tab2" type="button" role="tab"
                                    aria-controls="v-pills-tab2" aria-selected="false">
                                    <span class="fi-rr-key"></span>
                                    <span>{{ get_phrase('Login Credentials') }}</span>
                                </button>

                                {{-- <button class="nav-link text-start" id="v-pills-tab3-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tab3" type="button" role="tab"
                                    aria-controls="v-pills-tab3" aria-selected="false">
                                    <span class="fi-rr-credit-card"></span>
                                    <span>{{ get_phrase('Payment Information') }}</span>
                                </button>

                                <button class="nav-link text-start" id="v-pills-tab4-tab" data-bs-toggle="pill" data-bs-target="#v-pills-tab4" type="button" role="tab"
                                    aria-controls="v-pills-tab4" aria-selected="false">
                                    <span class="fi-rr-link"></span>
                                    <span>{{ get_phrase('Social Links') }}</span>
                                </button> --}}
                            </div>
                        </div>
                        <div class="tab-content w-100" id="myv-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-tab1" role="tabpanel" aria-labelledby="v-pills-tab1-tab" tabindex="0">
                                @include('admin.instructor.edit_instructor_basic')
                            </div>
                            <div class="tab-pane fade" id="v-pills-tab2" role="tabpanel" aria-labelledby="v-pills-tab2-tab" tabindex="0">
                                @include('admin.instructor.edit_login')
                            </div>
                            {{-- <div class="tab-pane fade" id="v-pills-tab3" role="tabpanel" aria-labelledby="v-pills-tab3-tab" tabindex="0">
                                @include('admin.instructor.edit_payment')
                            </div>
                            <div class="tab-pane fade" id="v-pills-tab4" role="tabpanel" aria-labelledby="v-pills-tab4-tab" tabindex="0">
                                @include('admin.instructor.edit_social')
                            </div> --}}

                            <button type="submit" class="tf-btn tf-btn--primary mt-3">
                                <span>{{ get_phrase('تحديث') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
