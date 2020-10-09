 
@extends('layouts.master')
@section('styles') 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
@stop
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Users</h2>
    </div>
</header>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Users</li>
        <li class="breadcrumb-item active">List Users</li>
    </ul>
</div>
<!-- Dashboard Header Section    -->
<section class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if(session()->has('success'))
                <div class="alert alert-success mb-4 alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session()->get('success') }}
                </div>
                @endif
                @if(session()->has('error'))
                <div class="alert alert-danger mb-4  alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session()->get('error') }}
                </div>
                @endif
                <div class="card"> 
                    <div class="card-header ">
                        <div class="card-caption">
                            <h3 class="h4">List Users</h3>
                        </div>
                        <?php if (Auth::User()->can('add-user')) { ?>
                        <div class="card-action">
                             <a href="{{ url('add-user')}}" class="btn btn-primary">Add New</a>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">
                        <form action="{{url('event-list')}}">
                        <div class="table-responsive">                       
                            <table  class="table table-striped table-hover" id="listusers">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>First Name</th>
                                        <!--<th>Last Name</th>-->
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Join Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</section>
@stop 
@section('scripts')   
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> 
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> 
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    listuser = $('#listusers').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 15,
        serverMethod: "POST",
        lengthMenu: [15, 25, 50],
        ajax: APP_URL + '/list-users-record',
        columns: [
            {data: 'user_id', name: 'user_id'},
            {data: 'firstname', name: 'firstname'},
//            {data: 'lastname', name: 'lastname'},
            {data: 'username', name: 'username'},
            {data: 'email', name: 'email'},
            {data: 'name', name: 'tod_roles.name'},
            {data: 'status', name: 'status'},
            {data: 'created_date', name: 'tod_users.created_date'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ], "order": [[0, 'asc']]
    });
});

function deleteUser(obj) {
    var user_id = $(obj).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#796AEE',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve) {
                $.ajax({
                    url: APP_URL + "/delete-user",
                    type: 'POST',
                    dataType: 'json',
                    data: {'user_id': user_id},
                    beforeSend: function (xhr) {

                    }, success: function (data, textStatus, jqXHR) {
                        if (data.status) {
                            Swal.fire({
                                type: 'success',
                                title: 'Deleted!',
                                text: 'Your User has been deleted.',
                                confirmButtonColor: '#796AEE',
                            })
                            listuser.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                type: 'error',
                                title: 'Oops...',
                                text: data.data,
                                confirmButtonColor: '#796AEE',
                            })
                        }
                    }, error: function (jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: textStatus,
                            confirmButtonColor: '#796AEE',
                        })
                    }
                });
            });
        },
        allowOutsideClick: false
    })
}


$(document).on('change', '.changestatus', function () {
    var user_id = $(this).attr('data-id');
    var value = $(this).val();
    $.ajax({
        url: APP_URL + '/change-status',
        type: 'POST',
        data: {
            user_id: user_id,
            'value': value
        },
        dataType: 'JSON',
        beforeSend: function () {
        },
        success: function (data) {
            if (data.status) {
                Swal.fire({
                    position: 'top-end',
                    showConfirmButton: false,
                    html: 'User status has been updated',
                    width: '20rem',
                    timer: 1000
                })
            } else {
                Swal.fire({
                    position: 'top-end',
                    type: 'error',
                    html: data.data,
                    showConfirmButton: false,
                    width: '20rem',
                    timer: 1500
                })
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    })
})
</script>
@stop