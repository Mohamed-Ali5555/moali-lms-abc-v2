@extends('theme::layouts.master')
@section('content')
@include('theme::includes.banner')

  
    <section class="academic-years-tilt-section" id="years-section" dir="rtl">
        <div class="section-bg-shapes" aria-hidden="true">
            <div class="shape-1"></div>
            <div class="shape-2"></div>
            <div class="shape-3"></div>
            <div class="shape-4"></div>
        </div>
      <div class="container">
        <div class="ay-head">
            <span class="ay-head__eyebrow">مسارك للتفوق</span>
            <h2 class="section-title-modern display-5 ay-head__title">الدوارات التدريبية </h2>
            <p class="section-subtitle description-text ay-head__desc">
              كل ما تحتاجه للتفوق في مكان واحد. اختر  دورتك التدريبية وانطلق نحو مستقبل
              مشرق.
            </p>
        </div>

        <div class="row g-4 g-xl-5 justify-content-center">
            @foreach($categories as $category)
                <div class="col-lg-4 col-md-6 card-tilt-wrapper">
                    <a href="{{ route('theme.category', $category->id) }}" class="year-portal h-100">
                        <span class="year-portal__shine" aria-hidden="true"></span>
                        <div class="year-portal__media">
                            <img
                                src="{{ get_image($category->thumbnail ?? '') }}"
                                class="year-portal__img"
                                alt="{{ $category->title }}"
                                loading="lazy"
                            />
                            <span class="year-portal__veil" aria-hidden="true"></span>
                            <span class="year-portal__orbit" aria-hidden="true"></span>
                            <span class="year-portal__index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="year-portal__body">
                            <span class="year-portal__eyebrow">السنة الدراسية</span>
                            <h3 class="year-portal__title">{{ $category->title }}</h3>
                            @if (!empty(trim(strip_tags($category->description ?? ''))))
                                <div class="year-portal__desc">{!! $category->description !!}</div>
                            @endif
                            <span class="year-portal__cta">
                                <span>عرض الكورسات</span>
                                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
      </div>
    </section>



  <!-- Start Courses Section-->
  {{-- <section class="courses py-5">
    <div class="container">
      <h2 class="section-title" data-title="السنوات الدراسية">
        السنوات الدراسية
      </h2>
      <div class="row mb-5 flex-row-reverse">
        @foreach($categories as $category)
        <div class="col-lg-4 col-md-6 mt-5">
          <div class="classroom-card">
            <div class="classroom-card-header">
              <img src="{{ get_image($category->thumbnail ?? '') }}" alt="course-1" />
            </div>
            <div class="classroom-card-body">
              <a href="{{route('theme.category',$category->id)}}">
                <div class="">
                  <h4>{{$category->title}}</h4>
                  <hr />
                  <p>{!! $category->description !!}</p>
                </div>
              </a>
            </div>
          </div>
        </div>

        @endforeach


      </div>
    </div>
  </section> --}}
  <!-- End Courses Section-->

  @include('theme::includes.home_courses_sidebar')

  @include('theme::includes.book')
<!-- Start Features Section -->
@include('theme::includes.features',['features'=>$features])
  <!-- End Features Section -->

  {{-- ===== قسم الاعتماديات (شريط متحرك) ===== --}}
  @include('theme::includes.accreditations')

  {{-- ===== قسم الموقع الجغرافي (خريطة) ===== --}}
  @include('theme::includes.location_map')

    <script>
      document.addEventListener("DOMContentLoaded", function () {

        // Animate cards on scroll into view
        const observer = new IntersectionObserver(
          (entries) => {
            entries.forEach((entry) => {
              if (entry.isIntersecting) {
                const wrappersInView =
                  entry.target.querySelectorAll(".card-tilt-wrapper");
                wrappersInView.forEach((wrapper, index) => {
                  setTimeout(() => {
                    wrapper.classList.add("is-visible");
                  }, index * 150);
                });
                observer.unobserve(entry.target);
              }
            });
          },
          {
            threshold: 0.1,
          }
        );

        const sectionToObserve = document.getElementById("years-section");
        if (sectionToObserve) {
          observer.observe(sectionToObserve);
        }

        const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        if (!prefersReducedMotion) {
          const tiltWrappers = document.querySelectorAll(".card-tilt-wrapper");
          tiltWrappers.forEach((wrapper) => {
            const tiltCard = wrapper.querySelector(".year-portal");
            if (!tiltCard) return;

            wrapper.addEventListener("mousemove", (e) => {
              const rect = wrapper.getBoundingClientRect();
              const x = e.clientX - rect.left;
              const y = e.clientY - rect.top;
              const centerX = rect.width / 2;
              const centerY = rect.height / 2;
              const rotateX = ((y - centerY) / centerY) * -8;
              const rotateY = ((x - centerX) / centerX) * 8;

              tiltCard.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
              tiltCard.style.setProperty("--mouse-x", `${x}px`);
              tiltCard.style.setProperty("--mouse-y", `${y}px`);
            });

            wrapper.addEventListener("mouseleave", () => {
              tiltCard.style.transform = "rotateX(0deg) rotateY(0deg) translateY(0)";
            });
          });
        }
      });
    </script>

@endsection
