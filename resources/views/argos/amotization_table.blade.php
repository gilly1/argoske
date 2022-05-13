@extends('layouts.app')

@section('headSection')

@endsection

@section('title'){{$title}} @endsection
{{-- @section('subtitle') @if($data) Update {{$title}} @else Add {{$title}} @endif @endsection --}}

@section('level')

<li class="breadcrumb-item"><a href="{{ route('home',[$subdomain]) }}">  Dashboard  </a></li>
<li class="breadcrumb-item"><a href="{{ route($route.'.index',[$subdomain]) }}">  {{$title}}  </a></li>
<li class="breadcrumb-item active"> Amotization Schedule   </li>

@endsection


@section('content')


    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-9">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Total Value</span>
                            <span class="info-box-number text-center text-muted mb-0">{{$currency}} {{round($total_cash_pay,2)}}</span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-4">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Estimated Intrest</span>
                            <span class="info-box-number text-center text-muted mb-0">{{$currency}} {{round($intrest_for_one_month * $duration,2)}} <span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-5">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Estimated Payment Period</span>
                            <span class="info-box-number text-center text-muted mb-0">{{ceil($duration)}} Months</span>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-1"></div>
        <div class="col-md-9">

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Amotization Schedule</h3>
                  <a href="{{route($route.'.create',[$subdomain])}}" class="btn btn-warning float-right">New Calculations</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Intrest</th>
                        <th>Principle</th>
                        <th>Loading</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        function createSchedule($amount,$installment,$intrest,$loading,$principle_for_one_month,$i){
                            $i++;
                            if($amount > $installment ){
                                $balance = $amount -  $installment;
                                ?>

                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$installment}}</td>
                                    <td>{{$intrest}}</td>
                                    {{-- <td>{{$installment - $intrest - $loading}}</td> --}}
                                    <td>{{$principle_for_one_month}}</td>
                                    <td>{{$loading}}</td>
                                    <td>{{$balance}}</td>
                                </tr>
                                <?php
                                createSchedule($balance,$installment,$intrest,$loading,$principle_for_one_month,$i);
                            }else {
                                
                                $intrest_ratio = $intrest/$installment;
                                $loading_ratio = $loading/$installment;
                                $principle_ratio =1-($intrest_ratio+$loading_ratio);
                                ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{round($amount,2)}}</td>
                                    <td>{{$amount * $intrest_ratio}}</td>
                                    <td>{{$amount * $principle_ratio}}</td>
                                    <td>{{$amount * $loading_ratio}}</td>
                                    <td>0</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        {!! createSchedule( $total_cash_pay,$installments,$intrest_for_one_month,$loading,$principle_for_one_month,0 ) !!}

                    </tbody>
                    <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Intrest</th>
                        <th>Principle</th>
                        <th>Loading</th>
                        <th>Balance</th>
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