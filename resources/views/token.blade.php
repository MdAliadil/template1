@extends('layouts.app')
@section('title', "Token Delete")
@section('pagetitle', "Token Delete")

@php
    $table = "yes";
@endphp

@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Api Tokens</h4>
                    
                    <div class="heading-elements">
                        
                    </div>
                </div>
                <table class="table table-striped table-hover mt-20" id="datatable">
                    <thead>
                        <tr>
                            
                            <th>Agent Id</th>
                            <th>IP</th>
                            <th>Last Login</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</div>

@endsection

@push('script')

<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/securedata/0";

        var onDraw = function() {};

        var options = [
            { "data" : "user_id"},
            { "data" : "ip"},
            { "data" : "updated_at"},
            { "data" : "action",
                render:function(data, type, full, meta){
                    console.log(full);
                    return `<button type="button" class="btn btn-sm btn-danger btn-raised heading-btn legitRipple" onclick="deleteToken(`+full.id+`)"> <i class="fa fa-trash"></i></button>`;
                }
            },
        ];
        datatableSetup(url, options, onDraw);

         
    });

    
    
    function deleteToken(id){
        swal({
            title: 'Are you sure ?',
            text: "You want to Logout This user From Application",
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: 'Yes delete it!',
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !swal.isLoading(),
            preConfirm: () => {
                return new Promise((resolve) => {
                    $.ajax({
                        url: "{{ route('tokenDelete') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType:'json',
                        data: {'id':id},
                        success: function(result){
                            resolve(result);
                        },
                        error: function(error){
                            resolve(error);
                        }
                    });
                });
            },
        }).then((result) => {
            if(result.value.status == "1"){
                notify("Token Successfully Deleted", 'success');
                $('#datatable').dataTable().api().ajax.reload();
            }else{
                notify('Something went wrong, try again', 'Oops', 'error');
            }
        });
    }
</script>
@endpush
<style>
    .modal-title {
    margin-bottom: 0;
    line-height: 1.5;
    margin-left: -261px;
    margin-right: 198px;
}
</style>