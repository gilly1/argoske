<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    
    @include('inc/main/head')
        @section('headSection')
            @show

</head>
<body class="hold-transition sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">


    @if(auth()->user() !== null && $_SERVER['HTTP_HOST'] != env('APP_URL_NAME','tenancy.test'))
        @include('inc/navigation/nav')
        {{-- @include('inc/navigation/aside_db') --}}
        @include('inc/navigation/aside')
    @else        
        @include('inc/navigation/aside_blank')
    @endif

    <div id="app">
        <div class="content-section">
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">


                
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                
                            <h1>
                                @yield("title")

                                <small> @yield("subtitle") </small>
                            </h1>
                            {{-- <p><gits-commit/></p> --}}
                            </div>
                            <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield("level") 
                            </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>



                <!-- Main content -->
                <section class="content">                    
                    @section('content')
                        @show
            
                </section>
                <!-- /.content -->
                

                
            </div>
            <!-- /.content-wrapper -->
                
        </div>
        
    </div>


    @include('inc/navigation/aside2')
    
    @include('inc/navigation/footer')
    

</div>
<!-- ./footer -->
@include('inc/main/footer')
    <script>
        @if (session()->has('success') || session()->has('error') || session()->has('warning'))
            @if(session()->has('success'))
                toastr.success("{{ Session::get('success') }}");
            @elseif(session()->has('error'))
                toastr.error("{{ Session::get('error') }}");
            @elseif(session()->has('warning'))
                toastr.warning("{{ Session::get('warning') }}");
            @elseif(session()->has('message'))
                toastr.info("{{ Session::get('message') }}");
            @endif
        @endif
        
</script>
        @section('footerSection')
            @show

</body>
</html>
