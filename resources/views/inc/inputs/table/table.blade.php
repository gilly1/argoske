{{-- 
// <?php

// $title = 'User Lists';
// $columns = array("name"=>"name", "email"=>"email");
// $route = 'users.user';
// $collection = $users

// ?> --}}
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{$title}}t</h3>
    </div>

        <!-- /.box-header -->
    <div class="card-body table-responsive">
        <table id="listDataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <?php $names=[] ?>

                    @foreach ($columns as $key => $column)
                        <th>{{$column}}</th>

                        <?php array_push($names, $key)?>
                    @endforeach
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            @foreach ($collection as $coll)
                <tr>
                    @for ($i = 0; $i < count($names); $i++)
                        <td>{!! $coll->{$names[$i]} !!}</td>
                    @endfor
                    
                    <td>
                    <a href="{{route($route.'.edit',$coll->id)}}">
                        <button class="btn btn-primary item" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="fa fa-edit fa-fw"></i>
                        </button>
                    </a> &emsp;
                    
                    
                    <div class="btn-group">
                        <form  class="myAction" method="POST" action="{{URL::route($route.'.destroy', $coll->id)}}">
                            @csrf
                            <input name="_method" type="hidden" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Delete">
                                <i class="fa fa-fw fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    </td>

                </tr>
            @endforeach


            </tbody>
        </table>
    </div>
        <!-- /.box-body -->
</div>
            
                
