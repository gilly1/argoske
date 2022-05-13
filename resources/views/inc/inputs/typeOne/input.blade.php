
@foreach($inputName as $obj)
    
    @if($obj->mainType==='text')

        <div class="form-group has-feedback col-md-{{$obj->col}}">
            <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span> @endif</label>

            <input type="{{$obj->type}}" class="form-control{{ $errors->has($obj->id) ? ' is-invalid' : '' }}" id="{{$obj->id}}" 
                name="{{$obj->name}}" value="@if( $forEdit ) {{$forEdit->{$obj->id} }}@else{{old($obj->name)}}@endif"  
                placeholder="{{$obj->placeHolder}}" @if($obj->required) required @endif>

            @if ($errors->has($obj->name))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first($obj->name) }}</strong>
                </span>
            @endif
        </div>
    @elseif($obj->mainType==='password')

        <div class="form-group has-feedback col-md-{{$obj->col}}">
            <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span> @endif</label>

            <input type="{{$obj->type}}" class="form-control{{ $errors->has($obj->id) ? ' is-invalid' : '' }}" id="{{$obj->id}}" 
                name="{{$obj->name}}" value=""  
                placeholder="{{$obj->placeHolder}}" @if(!$forEdit) required @endif>

            @if ($errors->has($obj->name))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first($obj->name) }}</strong>
                </span>
            @endif
        </div>

        
    @elseif($obj->mainType==='dateTime')

        <div class="form-group has-feedback col-md-{{$obj->col}}">
            <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span>@endif</label>
            <div class="input-group date">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                <input type="text" class="form-control pull-right{{ $errors->has($obj->id) ? ' is-invalid' : '' }}"
                    value="@if( $forEdit  ){{\Carbon\Carbon::parse( $forEdit->{$obj->id} )->format('m/d/Y')}}@else{{old($obj->name)}}@endif"  
                    name="{{$obj->name}}" id="{{$obj->id}}"  placeholder="{{$obj->placeHolder}}" @if($obj->required) required @endif>
                
            </div>
            @if ($errors->has($obj->name))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first($obj->name) }}</strong>
                </span>
            @endif
        </div>

    @elseif($obj->mainType==='checkbox')

        <div class="form-check has-feedback col-md-{{$obj->col}}">
            <div class="custom-control custom-{{$obj->type}} {{$obj->switch}}">
                <input type="{{$obj->type}}" class="custom-control-input {{ $errors->has($obj->name) ? ' is-invalid' : '' }}" id="{{$obj->id}}" name="{{$obj->name}}"
                @if($forEdit) @if($forEdit->{$obj->id}  == 1) checked @endif @endif value="@if($forEdit)checked @else{{old($obj->name)}}@endif"
                @if($obj->required) required @endif>
                
                <label class="custom-control-label" for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span> @endif</label>    
                @if ($errors->has($obj->name))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first($obj->name) }}</strong>
                    </span>
                @endif
            </div>
        </div>

    @elseif($obj->mainType==='textarea')

        <div class="form-group has-feedback col-md-{{$obj->col}}">
            <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span>@endif</label>
            <textarea class="textarea{{ $errors->has($obj->id) ? ' is-invalid' : '' }}" name="{{$obj->name}}"  id="{{$obj->id}}" placeholder="{{$obj->placeHolder}}"
                style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"
                >@if($forEdit){{ $forEdit->{$obj->id}  }}@else{{old($obj->id)}}@endif
            </textarea>
                @if ($errors->has($obj->id))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first($obj->id) }}</strong>
                    </span>
                @endif
        </div>

        @elseif($obj->mainType==='file')
          
        <div class="form-group has-feedback col-md-{{$obj->col}}">
            <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}<span class="text-danger">[min 150 X 150 size and max 200kb] @if($obj->span)*@endif</span></label>
            <div class="input-group">
                 <div class="custom-file">
                     <input  type="{{$obj->type}}" class="custom-file-input" accept=".jpeg, .jpg, .png" name="{{$obj->name}}" @if($obj->required) required @endif>
                     <label class="custom-file-label" for="{{$obj->id}}">{{$obj->placeHolder}}</label>
                 </div>
                @if ($errors->has($obj->name))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first($obj->name) }}</strong>
                    </span>
                @endif
            </div>
        </div>


    @elseif($obj->mainType==='select')
    <div class="form-group has-feedback col-md-{{$obj->col}}">
        <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}} @if($obj->span)<span class="text-danger">*</span> @endif</label>
        <select class="form-control{{ $errors->has($obj->name) ? ' is-invalid' : '' }} select2" name="{{$obj->name}}">
            <option selected disabled="disabled">Choose...</option>
            @foreach($obj->loop as $coll)
                @if($forEdit && $obj->model)
                <?php 
                    $name = $obj->model;     
                    foreach(array_keys($name->toArray()) as $keys){
                        $checkIfId = strpos($keys, 'id');
                        if($checkIfId !== false)
                        {
                            continue;
                        }
                        $name_key = $keys;
                        break;
                    }
                 ?>
                
                    @if($coll->id == $name->id)
                        <option selected value="{{$name->id}}">{{$name->$name_key}} {{isset($name->full_name) ? ' - '. $name->full_name : '' }}</option>
                    @else
                        <option value="{{$coll->id}}">{{$coll->$name_key}} {{isset($coll->full_name) ? ' - '. $coll->full_name : '' }}</option>
                    @endif
    
                @else
                @php
                    $array_key_first = array_key_first($coll->toArray());
                    $new_id = strpos($array_key_first, 'id');
                    if($new_id !== false){
                        $id = $array_key_first;
                    }else{
                        $id = 'id';
                    }
                    foreach(array_keys($coll->toArray()) as $keys){
                        $checkIfId = strpos($keys, 'id');
                        if($checkIfId !== false)
                        {
                            continue;
                        }
                        $name = $keys;
                        break;
                    }
                @endphp
                    {{-- @if(old($obj->name))
                        <option selected value="{{old($obj->$name)}}">{{old($obj->$name)}} </option>
                    @endif --}}
                    <option value="{{$coll->$id}}">{{$coll->$name}} {{isset($coll->full_name) ? ' - '. $coll->full_name : '' }}</option>
                @endif
                
            @endforeach
        </select>
    
        @if ($errors->has($obj->name))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first($obj->name) }}</strong>
            </span>
        @endif
    </div>

    @endif

@endforeach