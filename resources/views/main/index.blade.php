@extends('layouts.app')


@section('headSection')

    @include('main/table/listHeader')

@endsection


@section('title'){{$title}} @endsection
{{-- @section('subtitle') @if($test) Update {{$title}} @else Add {{$title}} @endif @endsection --}}

@section('level')

<li class="breadcrumb-item"><a href="{{ route('home',[$subdomain]) }}">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="{{ route($route.'.index',[$subdomain]) }}">  {{$title}}  </a></li>
<li class="breadcrumb-item active"> All </li>

@endsection

@section('content')
    
    @if (isset($subColumns))
        @include('main/expandableTable')
    @else
        @include('main/table')
    @endif

@endsection

@section('footerSection')

    @include('main/table/listFooter')

@endsection
