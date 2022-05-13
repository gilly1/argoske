@extends('layouts.app')

@section('headSection')

@endsection

@section('title'){{$title}} @endsection
{{-- @section('subtitle') @if($data) Update {{$title}} @else Add {{$title}} @endif @endsection --}}

@section('level')

<li class="breadcrumb-item"><a href="{{ route('home',[$subdomain]) }}">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="{{ route($route.'.index',[$subdomain]) }}">  {{$title}}  </a></li>
<li class="breadcrumb-item active"> @if($data) Update {{$title}} @else Add {{$title}} @endif  </li>

@endsection


@section('content')


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-9">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h3 class="card-title">@if($data) Update {{$title}} @else Add {{$title}} @endif  </h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="@if($data){{ route($route.'.update',[$subdomain,$data->id]) }}@else {{ route($route.'.store',[$subdomain]) }} @endif " enctype="multipart/form-data" >
                        @csrf
                        @if($data) {{ method_field('PATCH') }} @endif 

                        <div class="card-body row">

                            @php
                                $forEdit = $data;
                            @endphp

                            @include('inc/inputs/typeOne/input')
                            
                            @if (isset($vue))
                                {!! $vue !!}
                            @endif
                            

                        </div>
                        
                    <!-- /.box-body -->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">@if($data) Update @else Add @endif  </button>&nbsp;&nbsp;&nbsp;
                        <a href="{{ route($route.'.index',[$subdomain]) }}" class="btn btn-warning">Back</a> 
                    </div>
                </form>
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6"></div>

    </div>
@endsection


@section('footerSection')


@endsection