
<div class="form-check has-feedback col-md-1">
    <div class="custom-control icheck-success">

        <input type="checkbox" class="custom-control-input {{ $errors->has($permission->name) ? ' is-invalid' : '' }} {{$name}}" id="{{$permission->name}}" name="roles[]"
        @if($data) 
        @foreach ($data->permissions as $perm)
            @if ($perm->name == $permission->name)
                checked
            @endif
        @endforeach
        @endif 
        value="{{$permission->name}}">
        <label class="custom-control-label" for="{{$permission->name}}"> </label>    

    </div>
</div>
<br>
<br>
