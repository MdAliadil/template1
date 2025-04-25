@extends('layouts.app')
@section('title', "MicroAtm Request")
@section('pagetitle',  "MicroAtm Request")

@php
    $table = "yes";
    $export = "microfundrequest";
    $status['type'] = "Fund";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "approved" => "Approved",
        "rejected" => "Rejected",
    ];
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">MicroAtm Request</h4>
                    <div class="heading-elements">
                        <button type="button" data-toggle="modal" data-target="#fundRequestModal" class="btn bg-slate btn-xs btn-labeled legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching"><b><i class="icon-plus2"></i></b> New Request</button>
                    </div>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th width="160px">#</th>
                            <th>User Details</th>
                            <th>Bank Details</th>
                            <th>Refrence Details</th>
                            <th width="200px">Amount</th>
                            <th>Remark</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="fundRequestModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title">Wallet Fund Request</h6>
            </div>
            <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    @if(Auth::user()->bank != '' && Auth::user()->ifsc != '' && Auth::user()->account != '')
                        <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Bank</th>
                                    <th>Ifsc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{Auth::user()->account}}</td>
                                    <td>{{Auth::user()->bank}}</td>
                                    <td>{{Auth::user()->ifsc}}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif

                    <table class="table table-bordered p-b-15" cellspacing="0" style="margin-bottom: 30px">
                        <tbody>
                            <tr>
                                <th>Settlement Charge</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Settlement Timing</th>
                                <td>Bank</td>
                            </tr>
                        </tbody>
                    </table>

                    <input type="hidden" name="user_id">
                    {{ csrf_field() }}
                    @if(Auth::user()->bank == '' && Auth::user()->ifsc == '' && Auth::user()->account == '')
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="account" placeholder="Enter Value" required="" value="{{Auth::user()->account}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Ifsc Code</label>
                            <input type="text" class="form-control" name="ifsc" placeholder="Enter Value" required="" value="{{Auth::user()->ifsc}}">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="bank" placeholder="Enter Value" required="" value="{{Auth::user()->bank}}">
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Wallet Type</label>
                            <select name="type" class="form-control select" required>
                                <option value="">Select Wallet</option>
                                <option value="bank">Move To Bank</option>
                                <option value="wallet">Move To Wallet</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter Value" required="">
                        </div>
                    </div>
                    <p class="text-danger">Note - If you want to change bank details, please send mail with account details to update your bank details.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/microatmfundrequest/0";
        var onDraw = function() {};
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    var out = '';
                    if(full.api){
                        out +=  `<span class='myspan'>`+full.api.api_name +`</span><br>`;
                    }
                    out += `<span class='text-inverse'>`+full.id +`</span><br><span style='font-size:12px'>`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "account",
                render:function(data, type, full, meta){
                    return full.user.name+`<br>`+full.user.mobile;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        if(full.account != '' && full.account != null){
                            return full.account +" ( "+full.bank+" )<br>"+full.ifsc;
                        }else{
                            return full.user.account +" ( "+full.user.bank+" )<br>"+full.user.ifsc;
                        }
                    }
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        if(full.pay_type == "payout"){
                            return "Ref - "+full.payoutref +"<br>Txnid - "+full.payoutid;
                        }else{
                            return "Manual";
                        }
                    }
                }
            },
            { "data" : "description",
                render:function(data, type, full, meta){
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> `+full.amount +`</span> / `+full.type;
                }
            },
            { "data" : "remark"},
            { 
                "data": "action",
                render:function(data, type, full, meta){
                    if(full.status == "approved"){
                        var btn = '<span class="label label-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="label label-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="label label-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $( "#fundRequestForm").validate({
            rules: {
                amount: {
                    required: true
                },
                type: {
                    required: true
                },
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
                },
                type: {
                    required: "Please select request type",
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
                var form = $('#fundRequestForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button:submit').button('loading');
                    },
                    complete: function () {
                        form.find('button:submit').button('reset');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            form.closest('.modal').modal('hide');
                            notify("Fund Request submitted Successfull", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status , 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });

</script>
@endpush