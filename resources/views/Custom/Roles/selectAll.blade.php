
@php
    $collection = ['','View','Add','Edit','Delete','Export','Import','Action'];
@endphp

@foreach ($collection as $item)
    
<div class="form-check has-feedback col-md-1">
    <div class="custom-control icheck-success">

        <input type="checkbox" class="custom-control-input"  name="{{$item}}">
        <label class="custom-control-label" for="{{$item}}"> </label>    

    </div>
</div>
<br>
<br>
@endforeach
