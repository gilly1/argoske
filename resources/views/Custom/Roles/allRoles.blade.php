
@php

    $permissionKey = [];
        foreach ($permissions as $permission){
            if(!in_array($permission->permission_key, $permissionKey, true)){
                array_push($permissionKey,$permission->permission_key);
            }
        }
    
@endphp

<div class="form-check has-feedback col-md-3">
    <b>Module</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>View</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>Add</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>Edit</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>Delete</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>Export</b>
</div>
<div class="form-check has-feedback col-md-1">
    <b>Import</b>
</div>
<div class="form-check has-feedback col-md-3">Action</div>

<br>
<br>
 

@for ($i = 0; $i < count($permissionKey); $i++)
    <div class="form-check has-feedback col-md-3">
        {{ucfirst( str_replace('_',' ',isset($permissionKey[$i]) ? $permissionKey[$i] : '' ) ) }}
    </div>
    @foreach ($permissions as $permission)
        @if(isset($permissionKey[$i]) && $permissionKey[$i] == $permission->permission_key)
            @php
                $name =preg_split("/[_,\- ]+/", $permission->name)[0];
                
            @endphp
            @if($name == "view")

                @include('Custom/Roles/singleRole')   
    
            @elseif($name == 'create')

                @include('Custom/Roles/singleRole')

            @elseif($name == 'edit')

                @include('Custom/Roles/singleRole')    
    
            @elseif($name == 'delete')

                @include('Custom/Roles/singleRole')  
            @elseif($name == 'export')

                @include('Custom/Roles/singleRole')  
            @elseif($name == 'import')

                @include('Custom/Roles/singleRole')   

            @endif
        @endif  

        @if ($errors->has($permission->name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($permission->name) }}</strong>
            </span>
        @endif
    @endforeach
    @if (isset($permissionKey[$i]))
        <div class="form-check has-feedback col-md-3">
            <div class="custom-control icheck-success">
                <input type="checkbox" class="custom-control-input" id="{{$permissionKey[$i]}}" name="{{$permissionKey[$i]}}">
                <label class="custom-control-label" for="{{$permissionKey[$i]}}"> </label> 
            </div>
        </div>        
    @endif
@endfor
    
