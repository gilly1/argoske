@extends('layouts.app')


@section('headSection')

    @include('main/table/listHeader')

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
        <div class="col-md-12">

            <div class="card">
                
                <div class="card-header" >
                    {{-- <h3 class="card-title">Amotization Schedule</h3> --}}
                    <div  class="form-group float-left ml-1">
                        {{-- @if (isset($canCreate) && isset($canImport))                     
                            @if (auth()->user()->can($canImport))   --}}
                                <form action="{{route('contract_month',[$subdomain,$month])}}" class="form-inline" method="post">
                                    @csrf
                                    <div class="input-group">
                                    <select name="employer" class="form-control select2" style="width: 100%;">
                                        <option selected="selected" disabled>Select Comapany</option>
                                        @foreach ($employers as $employer)
                                        @if ($employer_id == $employer->id)
                                            <option selected value="{{$employer->id}}">{{$employer->name}}</option>                                             
                                        @else
                                            <option value="{{$employer->id}}">{{$employer->name}}</option>                                            
                                        @endif
                                        @endforeach
                                    </select>
                                        <div class="input-group-append">
                                            <input class="input-group-text" type="submit" value="Get List">
                                        </div>
                                    </div>
                                </form> 
                            {{-- @endif
                        @endif --}}
                        </div>
                    
                    <div  class="form-group float-right mr-1">
                        {{-- @if (isset($canCreate) && isset($canImport))                     
                            @if (auth()->user()->can($canImport))   --}}
                                <form action="{{route('import.contract',[$subdomain])}}" class="form-inline" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="exampleInputFile" name="file">
                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <input class="input-group-text" type="submit" value="Upload">
                                        </div>
                                    </div>
                                </form>
                                {{-- <span><a class="btn btn-xs btn-info float-left mr-1 mt-1" href="{{route($route.'.sample',[$subdomain])}}">Download Sample Excel</a></span>   --}}
                            {{-- @endif
                        @endif --}}
                            {{-- @if (isset($canExport))
                                @if (auth()->user()->can($canExport)) --}}
                                    @if (isset($employer_id))
                                        <span><a class="btn btn-xs btn-success float-left mt-1" href="{{route('contract_month.export',[$subdomain,'xlsx',$month,$employer_id])}}">Export Excel</a></span>
                                        <span><a class="btn btn-xs btn-success float-left mt-1 ml-1" href="{{route('contract_month.export',[$subdomain,'pdf',$month,$employer_id])}}">Export PDF</a></span>                                    
                                    @endif
                                {{-- @endif
                            @endif --}}
                        </div>
                     
                  </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="listDataTable" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Contract</th>
                        <th>Customer</th>
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
                        $allGroupedData = [];
                            foreach($data as $key => $s_item){
                                $key = [
                                    'id'=>'',
                                    'contract'=>'',
                                    'trans_no'=>'',
                                    'name'=>'',
                                    'month'=>'',
                                    'principle'=> 0,
                                    'intrest'=> 0,
                                    'loading'=> 0,
                                    'installment'=> 0,
                                    'balance'=> 0,
                                    'paid'=> null,
                                    'actual_balance'=> null,
                                ];
                                $new_data = App\Http\Controllers\Logic\ContractLogic::group_by($s_item,'month');
                                foreach($new_data as $s_value){
                                    foreach($s_value as $t_value){
                                        foreach ($t_value as $skey => $value) {
                                            if($skey == 'month' || $skey == 'contract' || $skey == 'name' || $skey == 'trans_no'){
                                                $key[$skey] = $value;
                                            }elseif($skey == 'id'){
                                                $key[$skey] = $value;
                                            }else{
                                                $key[$skey] += $value;
                                            }
                                        }
                                        $allData[] = $key;
                                    }
                                }
                                $allGroupedData[] = $allData;
                            }
                            // dd($allData);
                        @endphp 
                        @foreach ($allData as $key => $payment)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$payment['contract']}}</td>
                                <td>{{$payment['name']}}</td>
                                <td>{{Carbon\Carbon::parse($payment['month'])->format('d M Y')}}</td>
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
                        <th>Contract</th>
                        <th>Customer</th>
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

    @include('main/table/listFooter')

@endsection