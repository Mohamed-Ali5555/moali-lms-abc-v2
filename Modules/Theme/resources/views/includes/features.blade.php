    <section class="features-final-section" id="features-section" dir="rtl">
      <div class="container">
        <h2 class="section-title-modern display-5">رحلتك نحو التفوق</h2>
        <p class="section-subtitle">
          نأخذ بيدك خطوة بخطوة من خلال مميزات فريدة تضمن لك تجربة تعليمية
          متكاملة وممتعة.
        </p>

        <div class="row g-5 justify-content-center features-grid">
            @php
            $features = \Modules\Theme\App\Models\theme_feature::where('status',1)->get();
            @endphp
            @foreach ($features as $row )
                <div class="col-xl-3 col-lg-6 feature-card-wrapper">
                    <div class="feature-card-final">
                        <div class="card-number-orb"></div>
                        <div class="card-accent-orb"><i class="far fa-star"></i></div>
                        <h3 class="feature-title">{{$row->title}} </h3>
                    </div>
                </div>
            @endforeach

        </div>
      </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const wrappersInView = entry.target.querySelectorAll(
                        ".feature-card-wrapper"
                        );
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

            const sectionToObserve = document.getElementById("features-section");
            if (sectionToObserve) {
                observer.observe(sectionToObserve);
            }
        });
    </script>
{{-- <section class="features">
    <div class="features_bg"></div>
    <div class="container">
        <div class="row flex-row-reverse">

            @php
            $features = \Modules\Theme\App\Models\theme_feature::where('status',1)->get();
            @endphp
            @foreach ($features as $row )
                <div class="col-lg-3 col-md-6 mt-3">
                    <div class="features_card">
                        <div class="features_card_icon">
                            <img src="{{ get_image($row->thumbnail) }}" alt="features-01" />
                        </div>
                        <div class="features_card_title title-1" data-background="{{$row->color}}">
                            <h6 class="m-0">{{$row->title}}   </h6>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section> --}}

