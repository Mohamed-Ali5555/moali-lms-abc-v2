<!DOCTYPE html>
<html lang="en">

  @include('theme::layouts.head')
  <body>
    <!-- Start Main Background Animation -->
    {{-- <div id="tsparticles"></div> --}}
    <!-- Start Main Background Animation -->


    @include('theme::layouts.nav')

    @yield('content')
    @yield('scripts')

    @include('theme::layouts.footer')


    @include('theme::layouts.footer_script')
    @include('frontend.default.toaster')
    @stack('js')

    <style>
      /*Bootstrap toaster*/
.toast {
    border-radius: 10px;
}

.toast-header {
    color: #fff;
    border-radius: 8px 8px 0px 0px;
    border-bottom: none;
}

.toast-header .btn-close {
    width: 20px;
    height: 10px;
    padding: 0px 1px;
    filter: invert(1);
}

.toast-body {
    color: #fff;
    border-radius: 0px 0px 8px 8px;
    padding: 0px 14px 14px 14px;
}

.toast.success .toast-header,
.toast.success .toast-body {
    background-color: #13a96c;
}

.toast.warning .toast-header,
.toast.warning .toast-body {
    background-color: rgb(229 153 40);
}

.toast.error .toast-header,
.toast.error .toast-body {
    background-color: rgb(255 85 119);
}

/*End toaster*/
    </style>
  </body>
</html>
