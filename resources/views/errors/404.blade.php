@extends('layouts.app')

@section('title')Error - 404 @endsection

@section('level')

    <li class="breadcrumb-item"><a href="#">  Dashboard  </a></li>
    <li class="breadcrumb-item"><a href="#">  Error  </a></li>
    <li class="breadcrumb-item active"> 404 - Page Not Found</li>

@endsection


@section('content')


    <div class="row">

      @if (isset($response))
        <div class="col-md-1"></div>
        <div class="card card-primary col-md-10"> 
          <div class="card-header">
            <h3>Git Response</h3>  
          </div>       
          <div class="card-body">
                <p>{{$response}}</p>
          </div>
          <div class="card-footer">
            <a class="btn btn-primary" href="{{route('git.index',[$subdomain])}}">return to Git Console</a>
          </div>
        </div>        
      @else
        <div class="error-page">
          <h2 class="headline text-warning"> 404</h2>

          <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>

            <p>
              We could not find the page you were looking for.
              Meanwhile, you may <a href="{{route('home',[$subdomain])}}">return to dashboard</a> .
            </p>
          </div>
          <!-- /.error-content -->
        </div>
      @endif

        

    </div>
@endsection

