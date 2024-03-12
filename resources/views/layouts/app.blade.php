<!DOCTYPE html>
<html lang="en">
@include('layouts.includes.head')

<!--begin::Body-->
<body>
                @include('layouts.sections.header')

                @include('layouts.sections.sidebar') 

                <main id="main" class="main">
                    <div class="pagetitle">
                        <h1>@yield('title')</h1>
                    </div><!-- End Page Title -->  
                <div id="app">
                        <div class="row">                             
                            <main class="col">
                                <!--begin::Main-->
                                @yield('content')
                                <!--end::Main-->
                            </main>
                        </div>
                </div>
                </main>

    @include('layouts.sections.footer')


  @include('layouts.includes.js')
  
  @yield('custom_js')


</body>
<!--end::Body-->

</html>
