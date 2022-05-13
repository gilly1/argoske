@extends('layouts.app')

@section('title')Logs @endsection

@section('level')

    <li class="breadcrumb-item"><a href="#">  Dashboard  </a></li>
    <li class="breadcrumb-item active"> Logs</li>

@endsection


@section('content')


    <!-- general form elements -->
    <div class="card card-primary">
        <div class="card-header with-border">
            <h3 class="card-title">All Logs </h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        
        <div class="table-responsive mailbox-messages">
            {{-- <task-draggable /> --}}
            <task-draggable :table-columns="{{ $tableColumns }}" ></task-draggable>
            <!-- /.table -->
        </div>
    </div>

@endsection

