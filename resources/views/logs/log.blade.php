@extends('layouts.app')

@section('title')Logs @endsection

@section('level')

    <li class="breadcrumb-item"><a href="#">  Dashboard  </a></li>
    <li class="breadcrumb-item active"> Logs</li>

@endsection


@section('content')


    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header with-border">
                    <h3 class="card-title">All Logs </h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                
                <div class="table-responsive mailbox-messages">
                    <table class="table table-hover table-striped">
                    <tbody>

                        @foreach($messages as $message)
                            <tr>
                                <td class="mailbox-subject">{!! ucwords(implode(' ',preg_split('/(?=[A-Z])/', ucfirst(str_replace('_', ' ', $message['text']))))) !!}</td>
                                <td class="mailbox-name">
                                    {{$message->type}}
                                </td>
                                <td class="mailbox-date">{{$message->ip}}</td>
                                <td class="mailbox-date">{{$message->created_at}}</td>
                            </tr>
                        @endforeach
                    
                    </tbody>
                    </table>
                    <!-- /.table -->
                </div>
            </div>
            <!-- /.box -->
        </div>

    </div>
@endsection

