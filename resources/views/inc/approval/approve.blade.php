              
<div id="approval">
    
    <div class="form-group col-sm-12">
        <div class="row">
            <div class="col-md-2">
                <form method="POST" action="{{ route('approver_statuses.update',[$subdomain,$approve_status]) }}" class="approve" >
                    @csrf
                    {{ method_field('PATCH') }}
                    <input type="hidden" name="approved" value="1"> 
                    <input type="hidden" name="status" value="1"> 
                    <input type="hidden" name="reason" value=''>   
                    <input type="hidden" name="modelName" value={{$modelName}}> 
                    <input type="hidden" name="modelNameId" value={{$modelNameId}}>       
                        <button type="submit" class="btn btn-success" id="approve">Approve  </button>
        
                </form>
            </div>
            <div class="col-md-2">
                <form method="POST" action="{{ route('approver_statuses.update',[$subdomain,$approve_status]) }}" class="reject" >
                    @csrf
                    {{ method_field('PATCH') }}
        
                        <button type="submit" class="btn btn-danger" id="reject">Reject  </button>
        
                </form>
            </div>
            <div class="col-md-8"></div>
        </div>
    </div>
</div>