<div class="card card-primary">
    <div class="card-header">
      <h3 class="card-title">{{$table_name}}</h3>
      @if (auth()->user()->can($canCreate))
            <a href="{{route($route.'.create',[$subdomain])}}" class="btn btn-small btn-warning float-right">Add {{$table_name}}</a>
        @endif
    </div>

      <!-- /.box-header -->
      <div class="card-body" style="overflow-x:auto;">
        <table id="listDataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <td>#</td>
                    <?php $names=[] ?>

                    @foreach ($columns as $key => $column)
                        <th>{{$key}}</th>

                        <?php array_push($names, $column)?>
                    @endforeach
                    <th>Action</th>
                    <th>Expand</th>
                </tr>
            </thead>
          <tbody>

            @foreach ($collection as $coll)

                <tr>
                    <td>{{$loop->iteration }}</td>
                        @include('main/tableColumns')
                        
                    <td>
                    
                        @if (isset($canEdit))
                           @if (auth()->user()->can($canEdit))
                               <a href="{{route($route.'.edit',[$subdomain,$coll->id])}}">
                                   <button class="btn btn-sm btn-primary item" data-toggle="tooltip" data-placement="top" title="Edit">
                                       <i class="fa fa-edit fa-fw"></i>
                                   </button>
                               </a>                                
                           @endif                                     
                        @endif

                        @if (isset($canView))
                           @if (auth()->user()->can($canView))
                               <a href="{{route($route.'.show',[$subdomain,$coll->id])}}">
                                   <button class="btn btn-sm btn-primary item" data-toggle="tooltip" data-placement="top" title="view">
                                       <i class="fa fa-eye fa-fw"></i>
                                   </button>
                               </a>                                 
                           @endif                                     
                        @endif

                       
                        @if (isset($canDelete))
                           @if (auth()->user()->can($canDelete))
                               <div class="btn-group">
                                   <form  class="myAction" method="POST" action="{{URL::route($route.'.destroy', [$subdomain,$coll->id])}}">
                                       @csrf
                                       <input name="_method" type="hidden" value="DELETE">
                                       <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Delete">
                                           <i class="fa fa-fw fa-trash"></i>
                                       </button>
                                   </form>
                               </div>                                     
                           @endif                                     
                        @endif
                       
                    </td>
                    <td>
                        @php
                            $collapseHref = '#collapse'.$coll->id; 
                            $collapseId = 'collapse'.$coll->id; 
                        @endphp
                        <a class="btn btn-info btn-sm mr-5" data-toggle="collapse" href="{{$collapseHref}}" role="button" aria-expanded="false" aria-controls="{{$collapseId}}"> <i class="fa fa-plus"></i></a>
                    </td>
                    
                 
                </tr>
                @if (count($subColumns) > 0)
                    <tr  class="collapse" id="{{$collapseId}}">
                        <td colspan="5">                         
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <?php $subNames=[] ?>
                    
                                        @foreach ($subColumns as $key => $subColumn)
                                            <th>{{$key}}</th>
                    
                                            <?php array_push($subNames, $subColumn)?>
                                        @endforeach
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            <tbody>
                                @foreach ($coll->{$nestedRelationship} as $subColl)
                                    <tr>    
                                        
                                        @include('main/tableSubColumns')

                                        <td>
                                            
                    
                                        @if (isset($canEditSubTable))
                                            @if (auth()->user()->can($canEditSubTable))
                                                <a href="{{route($routeSubTable.'.edit',[$subdomain,$subColl->id])}}">
                                                    <button class="btn btn-sm btn-primary item" data-toggle="tooltip" data-placement="top" title="Edit">
                                                        <i class="fa fa-edit fa-fw"></i>
                                                    </button>
                                                </a>&emsp;                                
                                            @endif                                     
                                        @endif

                                        @if (isset($canViewSubTable))
                                            @if (auth()->user()->can($canViewSubTable))
                                                <a href="{{route($routeSubTable.'.show',[$subdomain,$subColl->id])}}">
                                                    <button class="btn btn-sm btn-primary item" data-toggle="tooltip" data-placement="top" title="view">
                                                        <i class="fa fa-eye fa-fw"></i>
                                                    </button>
                                                </a>&emsp;                              
                                            @endif                                     
                                        @endif

                                        
                                        @if (isset($canDeleteSubTable))
                                            @if (auth()->user()->can($canDeleteSubTable))
                                                <div class="btn-group">
                                                    <form  class="myAction" method="POST" action="{{URL::route($routeSubTable.'.destroy', [$subdomain,$subColl->id])}}">
                                                        @csrf
                                                        <input name="_method" type="hidden" value="DELETE">
                                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Delete">
                                                            <i class="fa fa-fw fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>                                     
                                            @endif                                     
                                        @endif
                                        </td>
                                        
                                    </tr>                                    
                                @endforeach

                            </tbody>
                            </table>
                        </td>
                    </tr>
                @endif
                
            @endforeach


            @if (isset($columnsSum))

            {{-- start of adding blank row --}}
                <tr>
                    @for ($i = 0; $i < count($names); $i++)
                        <td></td>
                    @endfor
                    <td></td>
                </tr>
            {{-- end of adding blank row --}}

                <tr>
                    @for ($i = 0; $i < count($names); $i++)

                        @if ( !is_array( $names[$i] ) )
                            @if ( isset( $columnsSum[$names[$i]] ) )
                                <td>{!!  $columnsSum[$names[$i]] !!}</td>  
                            @else                              
                                <td></td>
                            @endif
                        @else                              
                            <td></td>
                        @endif
                        
                    @endfor
                    <td>Total :</td>
                </tr>
                
            @endif


          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>

    
