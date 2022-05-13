       <div class="card card-primary ">
                <div class="card-header" >
                  <h3 class="card-title">{{$table_name}}</h3>
                  @if (isset($canCreate))
                       @if (auth()->user()->can($canCreate))
                            <a href="{{route($route.'.create',[$subdomain])}}" class="btn btn-warning float-right">Add {{$table_name}}</a>
                        @endif
                  @endif
                  
                  <div  class="form-group float-right mr-1">
                        @if (isset($canCreate) && isset($canImport))                     
                            @if (auth()->user()->can($canImport))  
                                <form action="{{route($route.'.import',[$subdomain])}}" class="form-inline" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="exampleInputFile" name="file">
                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <input class="input-group-text" type="submit" value="Upload">
                                        </div>
                                    </div>
                                </form>
                                <span><a class="btn btn-xs btn-info float-left mr-1 mt-1" href="{{route($route.'.sample',[$subdomain])}}">Download Sample Excel</a></span>  
                            @endif
                        @endif
                            @if (isset($canExport))
                                @if (auth()->user()->can($canExport))
                                    <span><a class="btn btn-xs btn-success float-left mt-1" href="{{route($route.'.export',[$subdomain,'xlsx'])}}">Export Excel</a></span>
                                    <span><a class="btn btn-xs btn-success float-left mt-1 ml-1" href="{{route($route.'.export',[$subdomain,'pdf'])}}">Export PDF</a></span>
                                    <span><a class="btn btn-xs btn-success float-left mt-1 ml-1" href="{{route('reports.index',[$subdomain,'name'=>$route])}}">Custom Report</a></span>
                                @endif
                            @endif
                        </div>
                   
                </div>
            
                  <!-- /.box-header -->
                  <div class="card-body">
                    <table id="listDataTable" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <?php $names=[] ?>

                                @foreach ($columns as $key => $column)
                                    <th>{{$key}}</th>

                                    <?php array_push($names, $column)?>
                                @endforeach
                                <th>Action</th>
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
                                            <button class="btn btn-primary mr-1 btn-xs item" data-toggle="tooltip" data-placement="top" title="Edit">
                                                <i class="fa fa-edit fa-fw"></i>
                                            </button>
                                        </a>                                     
                                    @endif                                     
                                 @endif


                                 @if (isset($canView))
                                    @if (auth()->user()->can($canView))
                                        <a href="{{route($route.'.show',[$subdomain,$coll->id])}}">
                                            <button class="btn btn-primary mr-1 btn-xs item" data-toggle="tooltip" data-placement="top" title="view">
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
                                                <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="Delete">
                                                    <i class="fa fa-fw fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>                                     
                                    @endif                                     
                                 @endif
                                
                             </td>

                            </tr>
                        @endforeach


                        @if (isset($columnsSum))

                            <tr>
                                {{-- make it fixed at the bottom always --}}
                                <td>Total :</td> 
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
                                <td></td>
                            </tr>
                            
                        @endif
            
            
                      </tbody>
                    </table>
                  </div>
                  <!-- /.box-body -->
                </div>
            
                
