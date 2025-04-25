@extends('layouts.app')
@section('title', "Connect Account")
@section('pagetitle',  "Connect Account")

@php
    $table = "yes";
    $export = "upi";

    
@endphp

@section('content')
<main class="adminuiux-content has-sidebar" onclick="contentClick()">
                <div class="container-fluid mt-2">
                    <div class="row gx-3">
                        <div class="col py-1">
                            <div class="btn-group" role="group" aria-label="Basic example">
                               
                            </div>
                        </div>
                        <div class="col-auto py-1 ms-auto ms-sm-0">
                            <button class="btn btn-link btn-square btn-icon" data-bs-toggle="collapse" data-bs-target="#filterschedule" aria-expanded="false" aria-controls="filterschedule"><i data-feather="filter"></i></button>
                        </div>
                    </div>
                </div>
                <div class="container" id="main-content">
                    @include('layouts.filter')
                    <div class="card adminuiux-card mt-3 mb-3">
                        <div class="card-body pt-0 table-responsive">
                            <p class="h5 mt-4">Connect Account</p>
                             <table id="dataTables" class="table w-100 nowrap table-bordered">
                                <thead style="background: #471ba8;">
                                    <tr>
                                       <th class="all">#</th>
                                        <th class="xs sm md">MID NAME</th>
                                        <th class="all">MID ID</th>
                                        <th class="xs sm">Bank Name</th>
                                        <th class="xs sm md">Account Num</th>
                                        <th class="xs sm md">Account IFSC</th>
                                        <th class="xs sm md">Accountholder</th>
                                        <th class="xs sm md">UPI ID</th>
                                        <th class="xs sm md">I-Username</th>
                                        <th class="xs sm md">I-Password</th>
                                        <th class="xs sm md">Action</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                
                
            </main>

@endsection

@push('script')
<script type="text/javascript"> 
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/resource{{$type}}/0";
        var onDraw = function() {
        };
        var options = [
            { "data" : "id"},
            { "data" : "midname"},
            { "data" : "midid"},
            { "data" : "bank"},
            { "data" : "account"},
            { "data" : "ifsc"},
            { "data" : "accountholder"},
            { "data" : "upiid"},
            { "data" : "username"},
            { "data" : "password"},
            { "data" : "status",
                render:function(data, type, full, meta){
                    var check = "";
                    if(full.status == "active"){
                        check = "checked='checked'";
                    }
                    return `<a href="{{url('p2p/getotps/')}}/`+full.midid+`" target="_blank" target="_blank" class="btn btn-theme" style="padding: 3px; margin-top: -10px;">
                                OTP Logs
                            </a>
                            <a href="https://login.rummypays.com/developer/api/document" class="nav-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings menu-icon"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> 
                            <span class="menu-name">Settings</span></a>`;
                }
            },
        ];

        datatableSetup(url, options, onDraw);
    });

    function viewUtiid(id){
        $.ajax({
            url: `{{url('statement/fetch')}}/utiidstatement/`+id,
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data:{'scheme_id':id}
        })
        .done(function(data) {
            $.each(data, function(index, values) {
                $("."+index).text(values);
            });
            $('#utiidModal').modal();
        })
        .fail(function(errors) {
            notify('Oops', errors.status+'! '+errors.statusText, 'warning');
        });
    }
</script>
@endpush