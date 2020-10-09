 
@extends('layouts.master')
@section('styles') 
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
@stop
@section('content')  
<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom">Roles</h2>
    </div>
</header>
<!-- Breadcrumb-->
<div class="breadcrumb-holder container-fluid">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
        <li class="breadcrumb-item">Roles</li>
        <li class="breadcrumb-item active">List Roles</li>
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
                            <h3 class="h4">List Roles</h3>
                        </div>
                        <div class="card-action">
                            <a href="{{ url('add-role')}}" class="btn btn-primary">Add New</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="card-body">
                        <div class="table-responsive">                       
                            <table class="table table-striped table-hover" id="listroles">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Role Name</th> 
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
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
    listroles = $('#listroles').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 15,
        serverMethod: "POST",
        lengthMenu: [15, 25, 50],
        ajax: APP_URL + '/list-roles-record',
        columns: [
            {data: 'role_id', name: 'role_id'},
            {data: 'name', name: 'name'},
            {data: 'created_date', name: 'created_date'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ], "order": [[0, 'asc']]
    });
});
function deleteRole(obj) {
    var roleid = $(obj).data('id');
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
                    url: APP_URL + "/delete-role",
                    type: 'POST',
                    dataType: 'json',
                    data: {'role_id': roleid},
                    beforeSend: function (xhr) {

                    }, success: function (data, textStatus, jqXHR) {
                        if (data.status) {
                            Swal.fire({
                                type: 'success',
                                title: 'Deleted!',
                                text: 'Your User Role has been deleted.',
                                confirmButtonColor: '#796AEE',
                            })
                            listroles.ajax.reload(null, false);
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
</script>
@stop