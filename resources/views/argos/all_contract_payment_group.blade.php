@extends('layouts.app')

@section('headSection')

@endsection

@section('title'){{$title}} @endsection
{{-- @section('subtitle') @if($data) Update {{$title}} @else Add {{$title}} @endif @endsection --}}

@section('level')

<li class="breadcrumb-item"><a href="{{ route('home',[$subdomain]) }}">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="{{ route($route.'.index',[$subdomain]) }}">  {{$title}}  </a></li>
<li class="breadcrumb-item active"> Contract Payment  </li>

@endsection


@section('content')


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-9">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Amotization Schedule</h3>
                  
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($month as $key => $month)
                            <tr>
                                <td> <a href="{{route('contract_month',[$subdomain,$month])}}">{{Carbon\Carbon::parse($month)->format('M')}}</a> </td>
                                <td> <a href="{{route('contract_year',[$subdomain,$month])}}">{{Carbon\Carbon::parse($month)->format('Y')}}</a> </td>
                            </tr>
                        @endforeach
                        

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Month</th>
                        <th>Year</th>
                    </tr>
                    </tfoot>
                  </table>
                </div>
                <!-- /.card-body -->
              </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6"></div>

    </div>
@endsection


@section('footerSection')


@endsection