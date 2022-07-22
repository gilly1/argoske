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
                        <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">E.Intrest</span>
                            <span class="info-box-number text-center text-muted mb-0">{{$currency}} {{round($total_cash_pay - $amount,2)}} <span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">Comm Fee</span>
                            <span class="info-box-number text-center text-muted mb-0">{{$currency}} {{round($raw_processing_fee),0}}</span>
                            </div>
                        </div>
                        </div>
                        <div class="col-12 col-sm-3">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                            <span class="info-box-text text-center text-muted">P. Period</span>
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
                        <th>Commitment</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php
                        $loading = $raw_processing_fee/$duration;
                        function createSchedule($amount,$installment,$intrest,$loading,$principle_for_one_month,$i){
                            $i++;
                            $intrest_amount = $amount * ($intrest/12);
                            if($amount > $installment ){
                                $balance = $amount -  $installment + $intrest_amount;
                                ?>

                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{round($installment + $loading,2)}}</td>
                                    <td>{{round($intrest_amount,2)}}</td>
                                    {{-- <td>{{$installment - $intrest - $loading}}</td> --}}
                                    <td>{{round($installment - $intrest_amount,2)}}</td>
                                    <td>{{round($loading,2)}}</td>
                                    <td>{{round($balance,2)}}</td>
                                </tr>
                                <?php
                                createSchedule($balance,$installment,$intrest,$loading,$principle_for_one_month,$i);
                            }else {
                                
                                $loading_ratio = $loading/$installment;
                                ?>
                                <tr>
                                    <td>{{$i}}</td>
                                    <td>{{round($amount + $loading + $intrest_amount,2)}}</td>
                                    <td>{{round($intrest_amount,2)}}</td>
                                    <td>{{round($amount,2)}}</td>
                                    <td>{{round($loading,2)}}</td>
                                    <td>0</td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        {!! createSchedule( $amount,$installments,$intrest_for_one_month,$loading,$principle_for_one_month,0 ) !!}

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