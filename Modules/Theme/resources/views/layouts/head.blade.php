
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{get_theme_settings('jop_title') ?? '' }} {{get_theme_settings('name') ?? ''}}  </title>
    <link rel="icon" type="image/x-icon" href="{{ asset(get_theme_settings('logo') ?? '') }}" />

    <link rel="icon" type="image/png" sizes="32x32" href="{{asset(get_theme_settings('logo'))}}">
    <link rel="apple-touch-icon" href="{{asset(get_theme_settings('logo'))}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset(get_theme_settings('logo'))}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset(get_theme_settings('dark_logo'))}}">

    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="author" content="{{ get_theme_settings('name') ?? '' }}">
    <meta name="keywords" content="{{ seoField()->meta_keywords ?? '' }}">
    <meta name="description" content="{{ seoField()->meta_description ?? '' }}">
    <meta name="robots" content="{{ seoField()->meta_robot ?? 'index, follow' }}">
    <link rel="canonical" href="{{ seoField()->canonical_url ?? url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ get_theme_settings('jop_title') ?? '' }} {{get_theme_settings('name') ?? ''}}">
    <meta name="twitter:description" content="{{ seoField()->meta_description ?? '' }}">
    <meta name="twitter:image" content="{{ get_image(get_theme_settings('thumbnail') ?? '') }}">


    {{-- Open Graph --}}
    <meta property="og:title" content="{{ seoField()->og_title ?? '' }}">
    <meta property="og:description" content="{{ seoField()->og_description ?? '' }}">
    @if(seoField()->og_image)
        <meta property="og:image" content="{{ asset(seoField()->og_image ?? '') }}">
    @endif

    {{-- Custom Json/Script --}}
    {!! seoField()->json_id ?? '' !!}
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ get_theme_settings('jop_title') ?? '' }} {{get_theme_settings('name') ?? ''}}",
        "alternateName": "{{ get_theme_settings('jop_title') ?? '' }} {{get_theme_settings('name') ?? ''}}",
        "url": "{{ url()->current() }}",
        "potentialAction": {
          "@type": "SearchAction",
          "target": "{{ url()->current() }}/search?query={search_term_string}",
          "query-input": "required name=search_term_string"
        }
      }
      </script>
    <!-- Css Files -->
    <link rel="stylesheet" href="{{asset('modules/theme/css/swiper.min.css')}}" />
    <link rel="stylesheet" href="{{asset('modules/theme/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('modules/theme/css/style.css')}}" />
    @include('theme::layouts.dynamic_colors')
    <link rel="stylesheet" href="{{asset('modules/theme/css/home-courses-sidebar.css')}}" />
    <link rel="stylesheet" href="{{asset('modules/theme/css/footer-modern.css')}}" />
    <link rel="stylesheet" href="{{asset('modules/theme/css/sweetalert2.min.css')}}" />

    <!-- Css Files -->
    <link rel="stylesheet" type="text/css" href="{{asset('modules/theme/css/global/animation.css') }}">

    <!-- Fontawasome Css -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/default/css/all.min.css') }}">

    @stack('css')

    <!-- Js Files -->
    <script src="{{asset('assets/frontend/default/js/jquery-3.7.1.min.js')}}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="{{asset('modules/theme/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('modules/theme/js/swiper.min.js')}}"></script>
    <script src="{{ asset('assets/global/course_player/vendors/fontawesome/fontawesome.all.min.js') }}"></script>
    <script src="{{asset('modules/theme/js/main.js')}}" defer></script>
    <script src="{{asset('modules/theme/js/sweetalert2.min.js')}}" defer></script>
    <!-- Js Files -->
  </head>
