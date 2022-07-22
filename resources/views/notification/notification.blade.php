@extends('layouts.app')

@section('headSection')
<link rel="stylesheet" href="{{ asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">

@endsection

@section('title')Notification @endsection

@section('level')
    
    <li class="breadcrumb-item"><a href="#">  Dashboard  </a></li>
    <li class="breadcrumb-item"><a href="#">  Notification  </a></li>
    <li class="breadcrumb-item active"> All Notification</li>

@endsection


@section('content')

        <!-- ./Section header -->
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-10">
                    <div class="card card-default">
                        <div class="card-header">
                            <div class="card-tools pull-right m-1">
                                <a class="btn btn-warning mr-1 @if($type == 'unread') disabled @endif" href="{{ URL::route('notification',[$subdomain]) }}"><i class="fa fa-envelope"></i> Unread</a>
                                <a class="btn btn-info mr-1 @if($type == 'read') disabled @endif" href="{{ URL::route('notification.read',[$subdomain]) }}"><i class="fa fa-envelope-open"></i> Read</a>
                                <a class="btn btn-primary mr-1 @if($type == 'all') disabled @endif" href="{{ URL::route('notification.all',[$subdomain]) }}"><i class="fa fa-list-alt"></i> All</a>
                                <a class="btn btn-primary @if($type == 'userLogs') disabled @endif" href="{{ URL::route('notification.userLogs',[$subdomain]) }}"><i class="fa fa-list-alt"></i> Logs</a>
                            </div>
                           
                        @if(count($messages))
                            @if($type == 'unread') <a class="btn btn-info btn_mark_as_read" href="?action=mark_as_read"><i class="fa fa-envelope-open"></i> Mark as Read</a> @endif
                                <a class="btn btn-danger" href="?action=delete"><i class="fa fa-trash"></i> Delete</a>
                            @endif
                            @if($type != 'userLogs')
                                <div class="table-responsive mailbox-messages">
                                    <table class="table table-hover table-striped">
                                        <tbody>

                                            @foreach($messages as $message)
                                                <tr>
                                                    <td class="mailbox-subject {{$message['type']}}">
                                                        <a href="{{url($message['route'])}}">
                                                            <i class="fa @if($message['type'] == 'info') fa-info-circle text-info @elseif($message['type'] == 'warning') fa-warning text-yellow @elseif($message['type'] == 'success') fa-check-circle text-success @else fa-times-circle text-danger @endif"></i> {!! $message['message'] !!}
                                                        </a>
                                                    </td>
                                                    <td class="mailbox-date">{{\Carbon\Carbon::parse($message['created_at'])->diffForHumans()}}</td>
                                                </tr>
                                            @endforeach
                                        
                                        </tbody>
                                    </table>
                                    <!-- /.table -->
                                </div>
                            @else
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <tbody>

                                        @foreach($userLogs as $message)
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

                            @endif
                        </div>

                            </div>
                        </div>
                    </div>
    
        </section>
        <!-- /.content -->

@endsection


@section('footerSection')

@endsection

