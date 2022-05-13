@extends('layouts.app')


@section('headSection')

    @include('main/table/listHeader')

@endsection



@section('content')
   <section class="content">
        <div class="row">
        <div class="col-12">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title">Errors Found In The Excel Sheet</h3>
                <a href="{{route($route.'.index',[$subdomain])}}" class="btn btn-warning float-right">Back</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                <thead>
                
                    <tr>
                        <th>Row</th>
                        <th>Attribute</th>
                        <th>Error</th>
                        <th>Values</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach( $failures as $validation )
                        <tr>
                            <td>{{ $validation->row() ?? '' }}</td>
                            <td>{{ $validation->attribute() ?? '' }}</td>
                            <td>
                                <ul>
                                    @foreach($validation->errors() as $e)
                                    <li>{{$e ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    @foreach($validation->values() as $v)
                                    <li>{{$v ?? '' }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>

  @endsection
  
  @section('footerSection')
  
      @include('main/table/listFooter')
  
  @endsection