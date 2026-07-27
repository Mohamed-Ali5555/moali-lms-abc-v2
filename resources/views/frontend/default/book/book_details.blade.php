@extends('layouts.default')
@push('title', get_phrase('book Details'))
@push('meta')@endpush
@push('css')@endpush
@section('content')
  
    <!------------------- Breadcum Area Start  ------>
    <section class="breadcum-area page-content-pb-100 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 px-4">
                    <div class="eNtry-breadcum mt-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb d-flex flex-nowrap">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ get_phrase('Home') }}</a></li>
                                <li class="breadcrumb-item ellipsis-line-1 active" aria-current="page">{{ $book_details->title }}
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="course-details pe-auto pe-lg-5">

                        <h2 class="g-title ellipsis-line-4">{{ $book_details->title }}</h2>
                     
                        <div class="row row-gap-4">
                            <div class="col-6 col-sm-6 col-md-4">
                                <a class="d-flex align-items-center text-dark" href="{{ route('instructor.details', ['name' => slugify($book_details->creator->name), 'id' => $book_details->creator->id]) }}">
                                    <img class="pro-32 me-2" src="{{ get_image(course_by_instructor($book_details->id)->photo) }}" alt="instructor-image">
                                    {{ course_by_instructor($book_details->id)->name }}
                                </a>
                            </div>

                 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 order-2 order-lg-1">

                     <div class="details-page-content">
                        <div class="ps-box static-menu mt-5 w-100">
                            <ul class="nav nav-bordered" id="pills-tab" role="tablist">
                                <li class="nav-item active" role="presentation">
                                    <button class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" data-bs-target="#pills-overview" type="button" role="tab" aria-controls="pills-overview" aria-selected="true">{{ get_phrase('Overview') }}</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab" tabindex="0">
                                    @include('frontend.default.book.overview_area')
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
                <div class="col-lg-4 order-1 order-lg-2">
                    @include('frontend.default.book.pricing_card')
                </div>
            </div>
            <!------------------- Player Feature Area End  --------->
        </div>
    </section>

    <!------------------- Breadcum Area End  --------->


  

    <script>
        "use strict";
        const myModalElement = document.getElementById('exampleModal')
        myModalElement.addEventListener('hidden.bs.modal', event => {
            promoPlayer.pause();
            $('#exampleModal').toggleClass('in');
        });
        myModalElement.addEventListener('shown.bs.modal', event => {
            promoPlayer.play();
            $('#exampleModal').toggleClass('in');
        });
    </script>

@endsection
@push('js')
    <script>
        "use strict";
        $(document).ready(function() {
            $('#more_description').on('click', function(e) {
                e.preventDefault();

                let ellipsis = $('.description').attr('id');
                $('.description').toggleClass(ellipsis);

                $(this).toggleClass('active');
                if ($(this).hasClass('active')) {
                    $(this).text('See less');
                } else {
                    $(this).html('See more <i class="fa-solid fa-angle-right me-2"></i>');
                }
            });
        });
    </script>
@endpush
