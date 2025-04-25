@extends('layouts.app')
@section('title', "Scheme Manager")
@section('pagetitle',  "Scheme Manager")

@php
    $table = "yes";
    $agentfilter = "hide";

    $status['type'] = "Scheme";
    $status['data'] = [
        "1" => "Active",
        "0" => "De-active"
    ];
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
                            <p class="h5 mt-4">Scheme Manager</p>
                             <table id="dataTables" class="table w-100 nowrap table-bordered">
                                <thead style="background: #471ba8;">
                                    <tr>
                                        <th class="all">#</th>
                                        <th class="xs sm md">Name</th>
                                        <th class="all">Status</th>
                                        <th class="xs sm">Action</th>
                                        
                                       
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
            $('input#schemeStatus').on('click', function(evt){
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "0";
                if($(this).prop('checked')){
                    status = "1";
                }
                
                $.ajax({
                    url: '{{ route('resourceupdate') }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    data: {'id':id, 'status':status, "actiontype":"scheme"}
                })
                .done(function(data) {
                    if(data.status == "success"){
                        notify("Scheme Updated", 'success');
                        $('#datatable').dataTable().api().ajax.reload();
                    }else{
                        if(status == "1"){
                            ele.prop('checked', false);
                        }else{
                            ele.prop('checked', true);
                        }
                        notify("Something went wrong, Try again." ,'warning');
                    }
                })
                .fail(function(errors) {
                    if(status == "1"){
                        ele.prop('checked', false);
                    }else{
                        ele.prop('checked', true);
                    }
                    showError(errors, "withoutform");
                });
            });
        };
        var options = [
           { "data" : "id"},
            { "data" : "name"},
            { "data" : "name",
                render:function(data, type, full, meta){
                    var check = "";
                    if(full.status == "1"){
                        check = "checked='checked'";
                    }

                    return `<div class="form-check form-switch form-check-md"><input class="form-check-input" type="checkbox" role="switch" id="schemeStatus" `+check+` value="`+full.id+`" actionType="`+type+`"> <label class="form-check-label" for="md"></label></div>`;
                }
            },
            { "data" : "action",
                render:function(data, type, full, meta){
                    var menu = ``;

                        menu += `<li class="dropdown-header">Charge</li><li><a href="javascript:void(0)" onclick="commission(`+full.id+`, 'upi','upiModal')"><i class="fa fa-inr"></i>Upi/a></li>`;
                        menu += `<li><a href="javascript:void(0)" onclick="commission(`+full.id+`, 'payout','payoutModal')"><i class="fa fa-inr"></i>Payout</a></li>`;


                    var out =  `<button type="button" class="btn bg-slate btn-raised legitRipple btn-xs" onclick="editSetup(this)">Edit</button>
                                <div class="btn-group btn-group-fade">
                                    <button type="button" class="btn bg-slate btn-raised legitRipple btn-xs" data-toggle="dropdown" aria-expanded="false">Commission/Charge <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        `+menu+`
                                    </ul>
                                </div>`;

                    return out;
                }
            },
        ];

        datatableSetup(url, options, onDraw);
         $( "#setupManager" ).validate({
            rules: {
                name: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter bank name",
                }
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#setupManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            if(id == "new"){
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').button('reset');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

        $('form.commissionForm').submit(function(){
            var form= $(this);
            form.closest('.modal').find('tbody').find('span.pull-right').remove();
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button[type="submit"]').button('loading');
                },
                complete: function(){
                    form.find('button[type="submit"]').button('reset');
                },
                success:function(data){
                    $.each(data.status, function(index, values) {
                        if(values.id){
                            form.find('input[value="'+index+'"]').closest('tr').find('td').eq(0).append('<span class="pull-right text-success"><i class="fa fa-check"></i></span>');
                        }else{
                            form.find('input[value="'+index+'"]').closest('tr').find('td').eq(0).append('<span class="pull-right text-danger"><i class="fa fa-times"></i></span>');
                            if(values != 0){
                                form.find('input[value="'+index+'"]').closest('tr').find('input[name="value[]"]').closest('td').append('<span class="text-danger pull-right"><i class="fa fa-times"></i> '+values+'</span>');
                            }
                        }
                    });
    
                    setTimeout(function () {
                        form.find('span.pull-right').remove();
                    }, 10000);
                },
                error: function(errors) {
                    showError(errors, form);
                }
            });
            return false;
        });

        $("#setupModal").on('hidden.bs.modal', function () {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });
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
    function addSetup(){
        $('#setupModal').find('.msg').text("Add");
        $('#setupModal').find('input[name="id"]').val("new");
        $('#setupModal').modal('show');
    }

    function editSetup(ele){
        var id = $(ele).closest('tr').find('td').eq(0).text();
        var name = $(ele).closest('tr').find('td').eq(1).text();

        $('#setupModal').find('.msg').text("Edit");
        $('#setupModal').find('input[name="id"]').val(id);
        $('#setupModal').find('input[name="name"]').val(name);
        $('#setupModal').modal('show');
    }
    
    function commission(id, type, modal) {
        $.ajax({
            url: '{{ url('resources/get') }}/'+type+"/commission",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data:{'scheme_id':id}
        })
        .done(function(data) {
            if(data.length > 0){
                $.each(data, function(index, values) {
                    if(type != "gst" && type != "itr"){
                        @if (Myhelper::hasRole('employee'))
                            $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('select[name="type[]"]').val(values.type);
                        @endif
                    }
                    $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="apiuser[]"]').val(values.apiuser);
                    $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="whitelable[]"]').val(values.whitelable);
                    $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="reseller[]"]').val(values.reseller);
                    $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="distributor[]"]').val(values.distributor);
                    $('#'+modal).find('input[value="'+values.slab+'"]').closest('tr').find('input[name="retailer[]"]').val(values.retailer);
                });
            }
        })
        .fail(function(errors) {
            notify('Oops', errors.status+'! '+errors.statusText, 'warning');
        });
    
        $('#'+modal).find('input[name="scheme_id"]').val(id);
        $('#'+modal).modal();
    }

    @if(isset($mydata['schememanager']) && $mydata['schememanager']->value == "all")
        function viewCommission(id, name) {
            if(id != ''){
                $.ajax({
                    url: '{{route("getMemberPackageCommission")}}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data : {"scheme_id" : id},
                    beforeSend : function(){
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    swal.close();
                    $('#commissionModal').find('.schemename').text(name);
                    $('#commissionModal').find('.commissioData').html(data);
                    $('#commissionModal').modal('show');
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
            }
        }
    @else
    function viewCommission(id, name) {
        if(id != ''){
            $.ajax({
                url: '{{route("getMemberCommission")}}',
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {"scheme_id" : id},
                beforeSend : function(){
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are fetching commission details',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                }
            })
            .success(function(data) {
                swal.close();
                $('#commissionModal').find('.schemename').text(name);
                $('#commissionModal').find('.commissioData').html(data);
                $('#commissionModal').modal('show');
            })
            .fail(function() {
                swal.close();
                notify('Somthing went wrong', 'warning');
            });
        }
    }
    @endif
</script>
@endpush