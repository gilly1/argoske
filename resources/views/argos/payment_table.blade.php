<table id="listDataTable" class="table table-bordered table-hover"  style="overflow-x: auto">
    <thead>
    <tr>
        <th>id</th>
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
                $new_data = App\Http\Controllers\Logic\ContractLogic::group_by($s_item,'id');
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
                <td>{{$payment['id']}}</td>
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
  </table>