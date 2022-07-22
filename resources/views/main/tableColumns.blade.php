@for ($i = 0; $i < count($names); $i++)

    @if (is_array($names[$i]))
        @if ($names[$i][0]  == 'relationship' )
        @php
            $relationship1 = $names[$i][1];
            $relationship2 = $names[$i][2];
        @endphp
            <td>{!! $coll->$relationship1->$relationship2 ?? '' !!}</td>                                            
        @elseif ($names[$i][0]  == 'hasManyRelationship' )
        @php
            $hasManyRelationship1 = $names[$i][1];
            $hasManyRelationship2 = $names[$i][2];
        @endphp
        <td>{!! isset($coll->$hasManyRelationship1) ?$coll->$hasManyRelationship1->first()->$hasManyRelationship2 : '' !!}</td>  
        @elseif ($names[$i][0]  == 'enum' )
            <td>{{$coll->{$names[$i][1] } == $names[$i][2] ? $names[$i][3] : $names[$i][4]}}</td>                                      
        @elseif ($names[$i][0]  == 'date' )
            <td>{{ Carbon\Carbon::parse($coll->{$names[$i][1]})->format($names[$i][2]) }}</td>                                      
        @elseif ($names[$i][0]  == 'view_link' )
            @if (isset($canView))
                @if (auth()->user()->can($canView))
                    <td> <a href="{{route($route.'.show',[$subdomain,$coll->id])}}">{!! $coll->{$names[$i][1]} !!}</a> </td>                                                                                          
                @else
                    <td> {!! $coll->{$names[$i][1]} !!} </td>   
                @endif
            @else
                <td> {!! $coll->{$names[$i][1]} !!} </td>  
            @endif
        @elseif ($names[$i][0]  == 'label' )
        @php
            $the_status = null;
            if(isset($names[$i][3])){
                foreach ($names[$i][3] as $key => $value) {
                    if($coll->{$names[$i][2]} == $key){
                        $the_status = $value;
                    }
                }
            }
        @endphp
            <td><span class="right badge badge-primary">{!! $the_status ?? $coll->{$names[$i][2]} !!}</span></td>
            {{-- <td> <a href="{{route($route.'.show',$coll->id)}}">{!! $coll->{$names[$i][1]} !!}</a> </td>                                       --}}
        @elseif ($names[$i][0]  == 'dynamicrelationship' )
        @php
            $relationship1 = $names[$i][1];
            $relationship2 = $names[$i][2];
            $relationship3 = $names[$i][3];
            $relationship4 = $names[$i][4];
            $relationship5 = $names[$i][5];
            $allModels = $relationship3::all();
            $selectedModel = $allModels->where('id',$coll->$relationship1->$relationship2 ?? '')->first();;
        @endphp
            <td>{!! $selectedModel->model::where('id',$coll->approver_model_id)->first()[$relationship5] !!}</td>  
        @else
        <td></td>
        @endif
    @else   
        @if ($coll->{$names[$i]} || $coll->{$names[$i]} == 0)
            <td>{!! $coll->{$names[$i]} !!}</td>  
        @else                                             
            <td>{!! $names[$i] !!}</td>                                    
        @endif 

        @if ( is_numeric($coll->{$names[$i]}) )
            @php
                isset($columnsSum[$names[$i]]) ? $columnsSum[$names[$i]] += $coll->{$names[$i]} : '';
            @endphp
        @endif
    @endif
@endfor