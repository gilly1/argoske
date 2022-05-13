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
                        <th>#</th>
                        <th>Month</th>
                        <th>Principle</th>
                        <th>Intrest</th>
                        <th>Loading</th>
                        <th>Installment</th>
                        <th>Balance</th>
                        <th>Paid</th>
                        <th>Actual Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                        $allData = [];
                            foreach($data as $key => $s_item){
                                $key = [
                                    'month'=>'',
                                    'principle'=> 0,
                                    'intrest'=> 0,
                                    'loading'=> 0,
                                    'installment'=> 0,
                                    'balance'=> 0,
                                    'paid'=> null,
                                    'actual_balance'=> null,
                                ];
                                foreach($s_item as $s_value){
                                    foreach ($s_value as $skey => $value) {
                                        if($skey == 'month'){
                                            $key[$skey] = $value;
                                        }else{
                                            $key[$skey] += $value;
                                        }
                                    }
                                }
                                $allData[] = $key;
                            }
                        @endphp 
                        @foreach ($allData as $key => $payment)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$payment['month']}}</td>
                                <td>{{$payment['principle']}}</td>
                                <td>{{$payment['intrest']}}</td>
                                <td>{{$payment['loading']}}</td>
                                <td>{{$payment['installment']}}</td>
                                <td>{{$payment['balance']}}</td>
                                <td>{{$payment['paid']}}</td>
                                <td>{{$payment['actual_balance']}}</td>
                            </tr>
                        @endforeach
                        

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Month</th>
                        <th>Principle</th>
                        <th>Intrest</th>
                        <th>Loading</th>
                        <th>Installment</th>
                        <th>Balance</th>
                        <th>Paid</th>
                        <th>Actual Balance</th>
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