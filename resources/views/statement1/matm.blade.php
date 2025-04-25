@extends('layouts.app')
@section('title', "Micro-Atm Statement")
@section('pagetitle',  "Micro-Atm Statement")

@php
    $table = "yes";
    $export = "aeps";

    $status['type'] = "Report";
    $status['data'] = [
        "complete" => "Complete",
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "reversed" => "Reversed",
        "refunded" => "Refunded",
    ];
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Micro-Atm Statement</h4>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User Details</th>
                            <th>Bank Details</th>
                            <th>Refrence Details</th>
                            <th>Amount/Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="receipt" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Receipt</h4>
            </div>
            <div class="modal-body">
                <div id="receptTable">
                    <table class="table m-t-10">
                        <thead>
                            <tr>
                                <th style="padding: 10px 0px">Receipt</th>
                                <th style="padding: 10px 0px; text-align: right;">
                                    @if(Auth::user()->company->logo)
                                        <img src="{{asset('')}}public/logos/{{Auth::user()->company->logo}}" class=" img-responsive pull-right" alt="" style="width: 260px;height: 56px;">
                                    @else
                                        {{Auth::user()->company->companyname}}
                                    @endif 
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 10px 0px">
                                    <address class="m-b-10">
                                        <strong>Agent :</strong> <span>{{Auth::user()->name}}</span><br>
                                        <strong>Shop Name :</strong> <span>{{Auth::user()->shopname}}</span><br>
                                        <strong>Phone :</strong> <span>{{Auth::user()->mobile}}</span>
                                    </address>
                                </td>
                                <td style="padding: 10px 0px" class="text-right">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <h5>Transaction Details :</h5>
                                <table class="table m-t-10">
                                    <thead>
                                        <tr>
                                            <th style="padding: 10px 0px">Order Id</th>
                                            <th style="padding: 10px 0px">Amount</th>
                                            <th style="padding: 10px 0px">Ref. No.</th>
                                            <th style="padding: 10px 0px">Date</th>
                                            <th style="padding: 10px 0px">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="mytxnid" style="padding: 10px 0px"></td>
                                            <td class="amount" style="padding: 10px 0px"></td>
                                            <td class="refno" style="padding: 10px 0px"></td>
                                            <td class="created_at" style="padding: 10px 0px"></td>
                                            <td class="status" style="padding: 10px 0px"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="border-radius: 0px;">
                        <div class="col-md-6 col-md-offset-6">
                            <h5 class="text-right">Withdraw Amount : <span class="amount"></span></h5>
                        </div>
                    </div>
                    <p>Note* - This is computer generated receipt no need to sign</p>
                    <hr>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                <button class="btn bg-slate btn-raised legitRipple" type="button" id="print"><i class="fa fa-print"></i></button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/matmstatement/{{$id}}";
        $('#print').click(function(){
            $('#receptTable').print();
        });

        var onDraw = function() {
            $('.print').click(function(event) {
                var data = DT.row($(this).parent().parent().parent().parent().parent()).data();
                $.each(data, function(index, values) {
                    $("."+index).text(values);
                });
                $('#receipt').modal();
            });
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<div>
                            <span class='text-inverse m-l-10'><b>`+full.id +`</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                }
            },
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Adhaar - `+full.aadhar+`<br>Mobile - `+full.mobile;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Ref No. - `+full.refno+`<br>Txnid - `+full.txnid+`<br>Payid - `+full.payid;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Commission - <i class="fa fa-inr"></i> `+full.charge;
                }
            },
            { "data" : "status",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var out = `<span class="label label-success">`+full.status+`</span>`;
                    }else if(full.status == "complete"){
                        var out = `<span class="label label-primary">`+full.status+`</span>`;
                    }else if(full.status == "pending"){
                        var out = `<span class="label label-warning">Pending</span>`;
                    }else if(full.status == "reversed"){
                        var out = `<span class="label bg-slate">Reversed</span>`;
                    }else{
                        var out = `<span class="label label-danger">`+full.status+`</span>`;
                    }

                    var menu = ``;
                    menu += `<li><a href="javascript:void(0)" class="print"><i class="icon-info22"></i>Print Invoice</a></li>`;
                    @if (Myhelper::can('matm_status'))
                    menu += `<li class="dropdown-header">Status</li>
                            <li><a href="javascript:void(0)" onclick="status(`+full.id+`, 'matm')"><i class="icon-info22"></i>Check Status</a></li>`;
                    @endif

                    @if (Myhelper::can('matm_statement_edit'))
                    menu += `<li class="dropdown-header">Setting</li>
                            <li><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'matm')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @endif

                    if(full.status != "reversed"){
                        menu += `<li class="dropdown-header">Complaint</li>
                                <li><a href="javascript:void(0)" onclick="complaint(`+full.id+`, 'matm')"><i class="icon-cogs"></i> Complaint</a></li>`;
                    }
                    

                    out +=  `<ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        `+menu+`
                                    </ul>
                                </li>
                            </ul>`;

                    return out;
                }
            }
        ];

        var DT = datatableSetup(url, options, onDraw);
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