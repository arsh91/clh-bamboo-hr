<!DOCTYPE html>
<html lang="en">
@include('layouts.includes.head')

<!--begin::Body-->
<body id="kt_body" style="background-image: url(assets/media/patterns/header-bg.jpg)" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled">
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                @include('layouts.sections.header')
                <div id="app">
                    <div class="container">
                        <div class="row">
                            {{-- @include('layouts.sections.sidebar') --}}

                            <main class="col">
                                <!--begin::Main-->
                                @yield('content')
                                <!--end::Main-->
                            </main>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    @include('layouts.sections.footer')

</body>
<!--end::Body-->
  <!--begin::Javascript-->
  @include('layouts.includes.js')
  @yield('custom_js')
  <!--end::Javascript-->
</html>
