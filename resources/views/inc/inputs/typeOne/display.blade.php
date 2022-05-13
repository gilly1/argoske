
<fieldset class="scheduler-border">
    {{-- <legend class="scheduler-border">Title</legend> --}}
    <div class="row">
        @foreach($inputName as $obj)
            
            @if($obj->mainType==='text')

                <div class="form-group has-feedback col-md-{{$obj->col}}">
                    <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label> 
                    <p>{{$forEdit->{$obj->id} }}</p>
                </div>

                
            @elseif($obj->mainType==='datepicker')

                <div class="form-group has-feedback col-md-{{$obj->col}}">
                    <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label>
                    <p>{{\Carbon\Carbon::parse( $forEdit->{$obj->id} )->format('d M Y')}}</p>
                </div>

            @elseif($obj->mainType==='checkbox')

                <div class="form-check has-feedback col-md-{{$obj->col}}">
                        
                        <label  for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label>    
                        
                        @if($forEdit->{$obj->id}  == 1) <p>Yes</p> @else <p>No</p> @endif
                </div>

            @elseif($obj->mainType==='textarea')

                <div class="form-group has-feedback col-md-{{$obj->col}}">
                    <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label>
                    <p>{!!$forEdit->{$obj->id}  !!}</p>
                </div>

                @elseif($obj->mainType==='file')
                
                <div class="form-group has-feedback col-md-{{$obj->col}}">
                    <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label>
                    <div class="input-group">
                       
                        <a target="_blank" href="{{asset('storage/'.$forEdit->{$obj->id})}}">
                            <img width="100" height="100" src="{{asset('storage/'.$forEdit->{$obj->id})}}" alt="{{$obj->placeHolder}}">
                        </a>
                    </div>
                </div>


            @elseif($obj->mainType==='select')
            <div class="form-group has-feedback col-md-{{$obj->col}}">
                <label for="{{$obj->id}}">{{ucwords(str_replace("_", " ", $obj->title))}}</label>
                
                    @foreach($obj->loop as $coll)
                        @if($forEdit)
                        <?php $name = $obj->model ?>
                        
                            @if($coll->id == $name->id)
                            <p>{{$name->name ?? ''}}</p>
                            @endif
                        @endif
                        
                    @endforeach
            </div>

            @endif

        @endforeach
    </div>
</fieldset>