@extends('layouts.app')

@section('headSection')

@endsection

@section('title'){{$title}} @endsection
{{-- @section('subtitle') @if($data) Update {{$title}} @else Add {{$title}} @endif @endsection --}}

@section('level')

<li class="breadcrumb-item"><a href="{{ route('home',[$subdomain]) }}">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="{{ route($route.'.edit',[$subdomain,$data->id]) }}"> Edit {{$title}}  </a></li>
<li class="breadcrumb-item active">  {{$title}}  </li>

@endsection


@section('content')


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-9">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h3 class="card-title">{{$title}}  </h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">

                    @php
                        $forEdit = $data;
                    @endphp

                    @include('inc/inputs/typeOne/display')
                    
                </div>
                <div class="card-footer">
                    @if (isset($canEdit))
                       @if (auth()->user()->can($canEdit))
                            <a href="{{ route($route.'.edit',[$subdomain,$data->id]) }}" class="btn btn-primary">Edit</a> &nbsp;&nbsp;&nbsp;
                        @endif
                    @endif
                    <a href="{{ route($route.'.index',[$subdomain]) }}" class="btn btn-warning">Back</a> &nbsp;&nbsp;&nbsp;
                </div>
            </div>

            @if (isset($include))

                @include($include)
                
            @endif
            

        </div>

    </div>
@endsection


@section('footerSection')


@endsection