{{-- @php
    $categories = App\Models\BootcampCategory::get();
    $active_category = request()->route()->parameter('category');
    $route_queries = request()->query();
    $route_queries = collect($route_queries)->except('page')->all();
@endphp

<div class="sidebar">
    <form class="mb-4" action="{{ route('theme.bootcamps', $active_category) }}" method="get">
        <div class="widget">
            <div class="search">
                <input type="text" class="form-control" name="search" placeholder="{{ get_phrase('Search...') }}"
                    value="{{ request('search') }}">
                <button type="submit" class="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
    </form>


    <!------------------- categories start ------------------->
    <div class="widget overlay-content overlay-content-max-h-400">
        <h4 class="widget-title">{{ get_phrase('Categories') }}</h4>
        <ul class="entry-widget overflow-hidden" id="parent-category">
            @foreach ($categories as $category)
                @php $route_queries['category'] = $category->slug; @endphp

                <li class="category @if ($category->slug == $active_category) active @endif" id="{{ $category->slud }}">
                    <a href="{{ route('theme.bootcamps', $route_queries) }}"
                        class="d-flex align-items-center justify-content-between">
                        <span>{{ $category->title }}</span>
                        <span>{{ count_bootcamps_by_category($category->id) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="down-text" id="see-more">
            {{ get_phrase('Show More') }}
        </div>
    </div>
    <!------------------- categories end ------------------->
</div>


@push('js')
    <script>
        $(document).ready(function() {
            $('#see-more').click(function(e) {
                e.preventDefault();
                $(this).toggleClass('active');
                let show_more = $(this).html();

                if ($(this).hasClass('active')) {
                    $(this).css('margin-top', '20px');
                    $(this).text('{{ get_phrase('Show Less') }}');
                } else {
                    $(this).css('margin-top', '0px');
                    $(this).html('{{ get_phrase('Show More') }}');
                }
            });

            var scrollTop = $(".scrollTop");
            $(scrollTop).click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 100);
                return false;
            });

            $('input[type="radio"]').change(function(e) {
                $('#filter-bootcamps').submit();
            });
        });
    </script>
@endpush --}}
@php
    $categories = App\Models\BootcampCategory::get();
    $active_category = request()->route()->parameter('category');
    $route_queries = request()->query();
    $route_queries = collect($route_queries)->except('page')->all();
@endphp

<div class="sidebar">
    <!-- نموذج البحث -->
    <form class="mb-4" action="{{ route('theme.bootcamps', $active_category) }}" method="get">
        <div class="widget search-widget">
            <div class="search">
                <input type="text" class="form-control" name="search" placeholder="{{ get_phrase('Search...') }}"
                    value="{{ request('search') }}">
                <button type="submit" class="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
    </form>

    <!-- الفئات -->
    <div class="widget categories-widget overlay-content overlay-content-max-h-400">
        <h4 class="widget-title">{{ get_phrase('Categories') }}</h4>
        <ul class="entry-widget overflow-hidden" id="parent-category">
            @foreach ($categories as $category)
                @php $route_queries['category'] = $category->slug; @endphp

                <li class="category @if ($category->slug == $active_category) active @endif" id="{{ $category->slug }}">
                    <a href="{{ route('theme.bootcamps', $route_queries) }}"
                        class="d-flex align-items-center justify-content-between">
                        <span>{{ $category->title }}</span>
                        <span class="category-count">{{ count_bootcamps_by_category($category->id) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="down-text" id="see-more">
            {{ get_phrase('Show More') }}
        </div>
    </div>
</div>

<style>
    /* تصميم الشريط الجانبي */
    .sidebar {
        background: #f8f9fa !important;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* تصميم عناوين الودجات */
    .widget-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #2c3e50;
        border-bottom: 2px solid #3498db;
        padding-bottom: 0.5rem;
    }

    /* تصميم نموذج البحث */
    .search-widget {
        margin-bottom: 1.5rem;
    }

    .search {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        height: 45px;
    }

    .search .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .search .submit {
        position: absolute;
        right: 5px;
        top: -20%;
        transform: translateY(-50%);
        background: #6baad4;
        border: none;
        border-radius: 6px;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        padding-left: 23px;
        padding-right: 23px;
    }

    .search .submit:hover {
        background: #2980b9;
    }

    /* تصميم قائمة الفئات */
    .categories-widget {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .entry-widget {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 300px;
        overflow-y: hidden;
        transition: max-height 0.4s ease;
    }

    .entry-widget.expanded {
        max-height: 1000px;
    }

    .category {
        margin-bottom: 8px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .category:hover {
        background-color: #f1f8ff;
    }

    .category.active {
        background-color: #e3f2fd;
        border-left: 3px solid #3498db;
    }

    .category a {
        padding: 10px 15px;
        text-decoration: none;
        color: #34495e;
        font-weight: 500;
        display: block;
        transition: color 0.3s ease;
    }

    .category a:hover {
        color: #3498db;
    }

    .category-count {
        background: #3498db;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .category.active .category-count {
        background: #2980b9;
    }

    /* تصميم زر "إظهار المزيد" */
    #see-more {
        text-align: center;
        padding: 10px;
        margin-top: 10px;
        cursor: pointer;
        color: #3498db;
        font-weight: 600;
        border-top: 1px solid #eee;
        transition: all 0.3s ease;
        border-radius: 6px;
    }

    #see-more:hover {
        background-color: #f1f8ff;
    }

    #see-more.active {
        margin-top: 20px;
    }

    /* تصميم متجاوب */
    @media (max-width: 768px) {
        .sidebar {
            padding: 15px;
            margin-bottom: 20px;
        }

        .widget-title {
            font-size: 1.1rem;
        }

        .category a {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
    }
</style>

@push('js')
    <script>
        $(document).ready(function() {
            // التحكم في إظهار/إخفاء الفئات
            $('#see-more').click(function(e) {
                e.preventDefault();
                $(this).toggleClass('active');
                $('#parent-category').toggleClass('expanded');

                if ($(this).hasClass('active')) {
                    $(this).css('margin-top', '20px');
                    $(this).text('{{ get_phrase('Show Less') }}');
                } else {
                    $(this).css('margin-top', '10px');
                    $(this).text('{{ get_phrase('Show More') }}');
                }
            });

            // زر العودة للأعلى
            var scrollTop = $(".scrollTop");
            $(scrollTop).click(function() {
                $('html, body').animate({
                    scrollTop: 0
                }, 100);
                return false;
            });

            // إرسال النموذج عند تغيير الراديو (إذا كان موجودًا)
            $('input[type="radio"]').change(function(e) {
                $('#filter-bootcamps').submit();
            });
        });
    </script>
@endpush
