@for ($i = 0; $i < count($subNames); $i++)

    @if (is_array($subNames[$i]))
        @if ($subNames[$i][0]  == 'relationship' )
        @php
            $relationship1 = $names[$i][1];
            $relationship2 = $names[$i][2];
        @endphp
            <td>{!! $coll->$relationship1->$relationship2 !!}</td>                                           
        @elseif ($subNames[$i][0]  == 'hasManyRelationship' )
        @php
            $hasManyRelationship1 = $names[$i][1];
            $hasManyRelationship2 = $names[$i][2];
        @endphp
        <td>{!! $coll->$hasManyRelationship1->first()->$hasManyRelationship2 !!}</td>  
        {{-- <td>{!! $subColl->load( $subNames[$i][1] )-> { $subNames[$i][2] } ->first() [  $subNames[$i][3]  ] !!}</td>   --}}
        @elseif ($subNames[$i][0]  == 'enum' )
            <td>{{$subColl->{$subNames[$i][1] } == $subNames[$i][2] ? $subNames[$i][3] : $subNames[$i][4]}}</td>                                      
        @elseif ($subNames[$i][0]  == 'date' )
            <td>{{ Carbon\Carbon::parse($subColl->{$subNames[$i][1]})->format($subNames[$i][2]) }}</td>                                      
        @elseif ($subNames[$i][0]  == 'view_link' )
            @if (isset($canView))
                @if (auth()->user()->can($canView))
                    <td> <a href="{{route($route.'.show',$subColl->id)}}">{!! $subColl->{$subNames[$i][1]} !!}</a> </td>                                                                                          
                @else
                    <td> {!! $subColl->{$subNames[$i][1]} !!} </td>   
                @endif
            @else
                <td> {!! $subColl->{$subNames[$i][1]} !!} </td>  
            @endif
        @elseif ($subNames[$i][0]  == 'label' )
            <td><span class="right badge badge-primary">{!! $subColl->{$subNames[$i][2]} !!}</span></td>
            {{-- <td> <a href="{{route($route.'.show',$subColl->id)}}">{!! $subColl->{$subNames[$i][1]} !!}</a> </td>                                       --}}
         @elseif ($subNames[$i][0]  == 'dynamicrelationship' )
        @php
            $relationship1 = $subNames[$i][1];
            $relationship2 = $subNames[$i][2];
            $relationship3 = $subNames[$i][3];
            $relationship4 = $subNames[$i][4];
            $relationship5 = $subNames[$i][5];
            $allModels = $relationship3::all();
            $selectedModel = $allModels->where('id',$subColl->$relationship1->$relationship2 ?? '')->first();;
        @endphp
            <td>{!! $selectedModel->model::where('id',$subColl->approver_model_id)->first()[$relationship5] !!}</td>  
        @else
        <td></td>
        @endif
    @else   
        @if ($subColl->{$subNames[$i]} || $subColl->{$subNames[$i]} == 0)
            <td>{!! $subColl->{$subNames[$i]} !!}</td>  
        @else                                             
            <td>{!! $subNames[$i] !!}</td>                                    
        @endif 

        @if ( is_numeric($subColl->{$subNames[$i]}) )
            @php
                isset($columnsSum[$subNames[$i]]) ? $columnsSum[$subNames[$i]] += $subColl->{$subNames[$i]} : '';
            @endphp
        @endif
    @endif
@endfor