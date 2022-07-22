@php
    if($isUserModelApproval){
        $checkIfNextApprover = $nextApprover ? !$nextApprover->where('id',$user_id->id)->first() : null;
    $checkIfSuperApprover = $superApprover ? !$superApprover->where('id',$user_id->id)->first() : null;
    }else {
        $checkIfNextApprover = $nextApprover ? !$nextApprover->whereIn('id',$user_id->id)->isEmpty() : null;
    $checkIfSuperApprover = $superApprover ? !$superApprover->whereIn('id',$user_id->id)->isEmpty() : null;
    }
    

    if( $isUserModelApproval ? ($superApprover ? $superApprover->id == $user_id->id : null) : $checkIfSuperApprover ){
        $approve_status = $superApproverStatus->id;
    }elseif( ($isUserModelApproval ? ($nextApprover->id ?? null == $user_id->id ) : $checkIfNextApprover )){
        $approve_status = $nextApproverStatus->id;
    }
    $apprv = $approve_status ?? null;
@endphp
<div class="card card-primary">

    <div class="card-body">
            
        @php
            $count = 0;
        @endphp
        @foreach ($approvers->where('status',1) as $appr)
                
            <div>
                @if ($superApprover)
                    @if ($appr->approver_model_id == $allSuperApprover->id && ($isUserModelApproval ? $superApprover->id == $user_id->id : $checkIfSuperApprover) )
                        You Super Approver ({{$allSuperApprover->name}} - {{$appr->users->name}}) <b>{{$appr->approved ? 'Approved' : 'Rejected'}}</b> on {{firstDateFormat($appr->updated_at)}}
                        @if ($appr->reason != null)
                            <b>Reason</b> : {!! $appr->reason !!}
                        @endif
                        <br>
                        <br>
                        @continue
                    @endif
                    @if ($superApproverStatus->status == 1 && $appr->super_admin == 1)
                        Super Approver ({{$allSuperApprover->name}} - {{$appr->users->name}}) <b>{{$appr->approved ? 'Approved' : 'Rejected'}}</b> on {{firstDateFormat($appr->updated_at)}}
                        @if ($appr->reason != null)
                            <b>Reason</b> : {!! $appr->reason !!}
                        @endif
                        <br>
                        <br>
                        @continue
                    @endif
                @endif
                @if (($isUserModelApproval ? ($superApprover ? $superApprover->id == $user_id->id : null) : $checkIfSuperApprover) || $user_id->id == 1)
                    Approver ({{$users->where('id',$appr->approver_model_id)->first()->name}} - {{$appr->users->name}} ) <b>{{$appr->approved ? 'Approved' : 'Rejected'}}</b> on {{firstDateFormat($appr->updated_at)}}
                    @if ($appr->reason != null)
                        <b>Reason</b> : {!! $appr->reason !!}
                    @endif
                    <br>
                @else
                    
                    @php
                    $singleApprover = null;
                    if($isUserModelApproval)
                    {
                        $singleApprover = $appr->where('status','1')->where('approved_model_id',$data->id)->where('approver_model_id',$user_id->id)->first();
                    }elseif ($modelToApprove->model == 'Spatie\Permission\Models\Role') {
                        $singleApprover = $appr->where('status','1')->where('approved_model_id',$data->id)->where('approver_model_id',$user_id->roles->first()->id)->first();
                    }elseif($user_id->designation){
                        $singleApprover = $appr->where('status','1')->where('approved_model_id',$data->id)->where('approver_model_id',$user_id->designation->id)->first();
                    }
                    @endphp
                    @if ($singleApprover && $count == 0)
                         Approver ({{$users->where('id',$singleApprover->approver_model_id)->first()->name}} - {{$appr->users->name}}) <b>{{$singleApprover->approved ? 'Approved' : 'Rejected'}}</b> on {{firstDateFormat($singleApprover->updated_at)}}
                        @if ($singleApprover->reason != null)
                            <b>Reason</b> : {!! $singleApprover->reason !!}
                        @endif
                        @php
                            $count++;
                        @endphp
                    @endif
                    @continue
                @endif
            </div>
        @endforeach

        @if ($superApproverStatus)
            @if ( ($isUserModelApproval ? ($nextApprover->id ?? null) == $user_id->id : $checkIfNextApprover ) && $superApproverStatus->status != 1 )                       
                @include('inc/approval/approve')
            @endif
            
            @if (($isUserModelApproval ? $superApprover->id == $user_id->id : $checkIfSuperApprover) && $superApproverStatus->status == 0)                        
                @include('inc/approval/approve')
            @endif

        @else
        
            @if ( ($isUserModelApproval ? ($nextApprover->id ?? null) == $user_id->id : $checkIfNextApprover ) )                       
                @include('inc/approval/approve')
            @endif
            
        @endif
    </div>
</div> 

@section('footerSection')

<script src="{{asset('js/tables.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $(document).ready(function () {
      
      $('html #approval').on('submit', 'form.approve', function (e) {
            e.preventDefault();
            var that = this;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Approve!'
            }).then((result) => {
                if (result.value) {
                    that.submit();
                }
            });
        });
      $('html #approval').on('submit', 'form.reject', function (e) {
            e.preventDefault();
            var that = this;
            $('#approve').prop('disabled', true);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            $('#reject').html(loadingText);
            $('#reject').prop('disabled', true);

                (async () => {
                    const { value: reason } = await Swal.fire({
                    input: 'textarea',
                    inputLabel: 'Message',
                    inputPlaceholder: 'Type your reason here...',
                    inputAttributes: {
                        'aria-label': 'Type your reason here'
                    },
                    showCancelButton: false
                    })

                    if (reason) {
                        var apprv = {{$apprv}};
                        var modelName = "{{$modelName}}";
                        var modelNameId = "{{$modelNameId}}";
                        axios.put('/approver_statuses/approver_statuses/'+apprv, {
                            reason: reason,
                            status : 1,
                            approved : 0,
                            modelName : modelName,
                            modelNameId : modelNameId
                        })
                        .then((response) => {
                            Swal.fire(
                            'Rejected!',
                            'Rejection was successful',
                            'success'
                        ).then((result) => {
                            if (result.value) {
                                location.reload();
                            }
                        })
                        }, (error) => {
                            console.log(error);
                        });
                    }else{
                        Swal.fire(
                        'Cancelled!',
                        'No Reason was provided.',
                        'error'
                        );
                        
                    $('#approve').prop('disabled', false);
                    var loadingText = 'Reject';
                    $('#reject').html(loadingText);
                    $('#reject').prop('disabled', false);
                    }
                    
                })()
            
        });
  
    });

</script>

@endsection
