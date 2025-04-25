@extends('layouts.app')
@section('title', "Aeps Fund Pending Request")
@section('pagetitle',  "Aeps Fund Pending Request")

@php
    $table = "yes";
    $export = "aepsfundrequestviewall";
    $status['type'] = "Fund";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "approved" => "Approved",
        "rejected" => "Rejected",
    ];

    $product['type'] = "Transaction";
    $product['data'] = [
        "wallet" => "Move To Wallet",
        "bank" => "Move To Bank"
    ];

@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Aeps Fund Pending Request</h4>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th width="160px">#</th>
                            <th>User Details</th>
                            <th>Bank Details</th>
                            <th>Reference Details</th>
                            <th>Amount</th>
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

@if (Myhelper::hasRole('employee'))
<div id="transferModal" class="modal fade" role="dialog" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <h4 class="modal-title">Fund Request From <span class="payeename text-capitalize"></span> </h4>
            </div>

            <form id="transferForm" method="post" action="{{ route('fundtransaction') }}">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id">
                    <input type="hidden" name="type" value="aepstransfer">
                    <div class="row">       
                        <div class="form-group"> 
                            <label>Action Type</label>
                            <select class="form-control" name="status" required="">
                                <option value="">Select Action Type</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ref No</label>
                            <input text="text" name="refno" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Enter Value"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->

<div id="payouttransferModal" class="modal fade" role="dialog" data-backdrop="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                 <h4 class="modal-title">Fund Request From <span class="payeename text-capitalize"></span> </h4>
            </div>

            <form id="payouttransferForm" method="post" action="{{ route('statementUpdate') }}">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="payout">
                    <div class="form-group">
                        <label>Action Type</label>
                        <select class="form-control select" name="status" required="">
                            <option value="">Select Action Type</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Ref No</label>
                        <input text="text" name="payoutref" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Enter Value"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
@endif

@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/aepsfundrequestviewall/0";
        var onDraw = function() {};
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    console.log(full);
                    var out = '';
                    if(full.api){
                        out +=  `<span class='myspan'>`+full.api.api_name +`</span><br>`;
                    }
                    out += `<span class='text-inverse'>`+full.id +`</span><br><span style='font-size:12px'>`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        return full.number +" ( "+full.bank+" )<br>";
                    }
                }
            },
            { "data" : "account",
                render:function(data, type, full, meta){
                    if(full.type != "wallet"){
                        return "RRN - "+full.payid+"<br>Payout Id - "+full.apitxnid+"<br>Ref Id - "+full.refno;
                    }else{
                        return full.type;
                    }
                }
            },
            { "data" : "description",
                render:function(data, type, full, meta){
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> `+full.amount;
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
                    
                    var menu = ``;
                   @if (Myhelper::can('payout_status'))
                    menu += `<li class="dropdown-header">Status</li>
                            <li><a href="javascript:void(0)" onclick="status(`+full.id+`, 'payout')"><i class="icon-info22"></i>Check Status</a></li>`;
                    @endif

                    
                    // menu += `<li class="dropdown-header">Setting</li>
                    //         <li><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'payout')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    

                    // menu += `<li class="dropdown-header">Complaint</li>
                    //         <li><a href="javascript:void(0)" onclick="complaint(`+full.id+`, 'payout')"><i class="icon-cogs"></i> Complaint</a></li>`;
                    

                    btn +=  `<ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        `+menu+`
                                    </ul>
                                </li>
                            </ul>`;

                   
                    
                    if(full.pay_type != "payout" && full.status== 'pending'){
                        @if(Myhelper::hasRole('employee'))
                            btn += `<br><button class="btn bg-slate btn-xs waves-effect mt-10" onclick="transfer('`+full.id+`', '`+full.username+`')"><i class="fa fa-pencil"></i> Edit</button>`;
                        @endif
                    }else if(full.pay_type == "payout" && (full.status== 'pending' || full.status== 'approved')){
                        @if(Myhelper::hasRole('employee'))
                            btn += `<br><button class="btn bg-slate btn-xs waves-effect mt-10" onclick="payouttransfer('`+full.id+`', '`+full.username+`')"><i class="fa fa-pencil"></i> Edit</button>`;
                        @endif
                    }
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $('form#transferForm').submit(function() {
            var form = $(this);
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button:submit').button('loading');
                },
                success:function(data){
                    if(data.status == "success"){
                        form.find('button:submit').button('reset');
                        form[0].reset();
                        notify('Fund request successfully updated', 'success');
                        $('#transferModal').modal('hide');
                        $('#datatable').dataTable().api().ajax.reload();
                    }else{
                        notify('Something went wrong', 'danger');
                    }
                },
                error: function(errors) {
                    form.find('button:submit').button('reset');
                    notify(errors.statusText, 'Oops!', 'error');
                }
            });
            return false;
        });

        $('form#payouttransferForm').submit(function() {
            var form = $(this);
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button:submit').button('loading');
                },
                success:function(data){
                    if(data.status == "success"){
                        form.find('button:submit').button('reset');
                        form[0].reset();
                        notify('Fund request successfully updated', 'success');
                        $('#transferModal').modal('hide');
                        $('#datatable').dataTable().api().ajax.reload();
                    }else{
                        notify('Something went wrong', 'danger');
                    }
                },
                error: function(errors) {
                    form.find('button:submit').button('reset');
                    notify(errors.statusText, 'Oops!', 'error');
                }
            });
            return false;
        });

        $("#transferModal").on('hidden.bs.modal', function () {
            $('#transferModal').find('form')[0].reset();
            $('#transferForm').find('input[name="id"]').val('');
            $('#transferModal').find('.payeename').text('');
        });
    });

function transfer(id, name) {
    $('#transferModal').find('.payeename').text(name);
    $('#transferForm').find('input[name="id"]').val(id);
    $('#transferModal').modal();
}

function payouttransfer(id, name) {
    $('#payouttransferModal').find('.payeename').text(name);
    $('#payouttransferForm').find('input[name="id"]').val(id);
    $('#payouttransferModal').modal();
}
</script>
@endpush