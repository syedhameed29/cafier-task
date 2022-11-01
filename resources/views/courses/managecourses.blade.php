<?php
use App\Category;
?>


@extends('master')
@section('pageheader')
<h2>Car Listing</h2>
@stop
@section('maincontent')

<div class="row">

    <div class="col">
        <section class="card">
            <header class="card-header">
                <meta name="csrf-token" content="{{ csrf_token() }}">
                @if($user->userType != 'user' && $user->userType != 'member')
                <a href="{{ route('addcourses') }}" class="btn btn-primary btn-sm pull-right">Add Car </a>
                @endif
                @if($user->userType != 'user' && $user->userType == 'member')
                <a href="{{ route('addcourses') }}" class="btn btn-primary btn-sm pull-right">Add Car </a>
                @endif
                <h2 class="card-title">Car Listing</h2>
            </header>
            <div class="card-body">
                @if (Session::has('success'))
                <div class="alert alert-danger">{{ Session::get('success') }}</div>
                @endif     
                @if (Session::has('error'))
                <div class="alert alert-danger">{{ Session::get('error') }}</div>
                @endif 
                <table class="table table-bordered table-striped mb-0" id="datatable-default">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Year</th>
                            <th>Model Name</th>
                            <th>Color</th>
                            <th>Mileage</th>
                            <th>Status</th>
                            @if($user->userType != 'member' || $user->userType != 'user' )
                            <th>Edit</th>
                            @endif
                            @if($user->userType != 'member' && $user->userType != 'user')
                            <th>Delete</th>
                            @endif

                            @if($user->userType != 'user' && $user->userType != 'member' && $user->userType != 'admin')
                            <th>Delete</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $key => $record)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $record->year }}</td>
                            <td>{{ $record->model_name }}</td>
                            <td>{{ $record->color}}</td>
                            <td>{{ $record->mileage}}</td>
                            <?php 
                            $record->status == "active" ? $class = "success" : $class = "danger"; 
                            ?>
                            <td><span class="status btn btn-xs btn-{!! $class !!}" data-id="{!! $record->id !!}" id="">{!! ucfirst($record->status) !!}</span></td>
                            @if($user->userType != 'member' || $user->userType != 'user')
                            <td><a href="{!! URL::to('admin/courses/edit/'.$record->id) !!}"><span class="btn btn-xs btn-primary">Edit</span></a></td>
                            @endif
                            @if($user->userType != 'member' && $user->userType != 'user')
                            <td><span class="btn btn-xs btn-primary delete"  data-id="{!!$record->id!!}" id="">Delete</span></td>
                            @endif

                            @if($user->userType != 'user' && $user->userType != 'member' && $user->userType != 'admin')
                            <td><span class="btn btn-xs btn-primary delete"  data-id="{!!$record->id!!}" id="">Delete</span></td>
                            @endif
                        </tr>                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
    // $(document).ready(function(){
    //  $("#datatable-default").dataTable();
    // });

    $(document).on('click', '.delete', function(){
        var clicked = $(this);
        var id = clicked.attr('data-id');
        alert('Are you sure want Delete This Car');
        $.ajax({
            type: 'POST',
            url: '{!! route('deletecourses') !!}',
            data: {id:id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success:function(data){
                if(data.status=='success'){
                    clicked.closest('tr').hide();
                }   
            },
            error:function(e){
                console.log(e.responseText);
                return false;
            }
        });/* end of ajax */

        
    });/* end of delete click function */

    $(document).on('click', '.status', function(){
        var clicked = $(this);
        var id = clicked.attr('data-id');
        alert("Do you really want to change this Car Status");

        $.ajax({
            type: 'POST',
            url: '{!! route('statuscourses') !!}',
            data: {id:id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(data){
                console.log(data);
                if(data.success == true){
                    var colour = "";
                    data.status == "active" ? colour = "btn-success":colour = "btn-danger";
                    clicked.removeClass("btn-success btn-danger").addClass(colour).text(data.status.charAt(0).toUpperCase() + data.status.slice(1));
                }
            },
            error: function(e){
                console.log(e.responseText);
                return false;
            }
        });/* end of ajax */

        
    });/* end of status click function */
    /* end of ready function */
</script>
@stop