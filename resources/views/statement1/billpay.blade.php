@extends('layouts.app')
@section('title', "Billpayment Statement")
@section('pagetitle',  "Billpayment Statement")

@php
    $table = "yes";
    $export = "billpay";

    $billers = App\Model\Provider::whereIn('type', ['electricity'])->get(['id', 'name']);
    foreach ($billers as $item){
        $product['data'][$item->id] = $item->name;
    }
    $product['type'] = "Biller";

    $status['type'] = "Report";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
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
                    <h4 class="panel-title">Billpayment Statement</h4>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Order Id</th>
                            <th>User Details</th>
                            <th>Transaction Details</th>
                            <th>Biller Details</th>
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
                                <th style="padding: 10px 0px">Receipt - <span class="created_at"></span></th>
                                <th style="padding: 10px 0px; text-align: right;">
                                    @if(Auth::user()->company->logo)
                                        <img src="{{asset('')}}public/logos/{{Auth::user()->company->logo}}" class=" img-responsive" alt="" style="width: 260px;height: 56px;">
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
                                    <address class="m-b-10">
                                        <strong>Consumer Name : </strong> <span class="option1"></span><br>
                                        <strong>Bill Board : </strong> <span class="providername"></span><br>
                                        <strong>K No : </strong> <span class="number"></span>
                                    </address>
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
                                            <th style="padding: 10px 0px">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="id" style="padding: 10px 0px"></td>
                                            <td class="amount" style="padding: 10px 0px"></td>
                                            <td class="refno" style="padding: 10px 0px"></td>
                                            <td class="status" style="padding: 10px 0px"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="border-radius: 0px;">
                        <div class="col-md-6 col-md-offset-6">
                            <h5 class="text-right">Bill Amount : <span class="amount"></span></h5>
                        </div>
                    </div>
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

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/billpaystatement/{{$id}}";
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
                    return `Number - `+full.number+`<br>Operator - `+full.providername;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Name - `+full.option1+`<br>Due Date - `+full.option2;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Ref No.  - `+full.refno+`<br>Txnid - `+full.txnid;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Profit - <i class="fa fa-inr"></i> `+full.profit;
                }
            },
            { "data" : "status",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var out = `<span class="label label-success">Success</span>`;
                    }else if(full.status == "pending"){
                        var out = `<span class="label label-warning">Pending</span>`;
                    }else if(full.status == "reversed" || full.status == "refunded"){
                        var out = `<span class="label bg-slate">`+full.status+`</span>`;
                    }else{
                        var out = `<span class="label label-danger">Failed</span>`;
                    }

                    var menu = ``;
                    if(full.status == "success" || full.status == "pending"){
                        menu += `<li><a href="javascript:void(0)" class="print"><i class="icon-info22"></i>Print Invoice</a></li>`;
                        @if (Myhelper::can('billpayment_status'))
                        menu += `<li class="dropdown-header">Status</li>
                                <li><a href="javascript:void(0)" onclick="status(`+full.id+`, 'billpayment')"><i class="icon-info22"></i>Check Status</a></li>`;
                        @endif

                        @if (Myhelper::can('billpay_statement_edit'))
                        menu += `<li class="dropdown-header">Setting</li>
                                <li><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'billpay')"><i class="icon-pencil5"></i> Edit</a></li>`;
                        @endif
                    }

                    menu += `<li class="dropdown-header">Complaint</li>
                                <li><a href="javascript:void(0)" onclick="complaint(`+full.id+`, 'billpay')"><i class="icon-cogs"></i> Complaint</a></li>`;

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
</script>
@endpush