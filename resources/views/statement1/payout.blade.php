@extends('layouts.app')
@section('title', "Payout Statement")
@section('pagetitle', "Payout Statement")

@php
    $table = "yes";
    $export = "upi";

    $status['type'] = "Report";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "reversed" => "Reversed",
        "refunded" => "Refunded",
        "dispute" => "Dispute",
    ];
@endphp

@section('content')
 
<main class="page-content">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Payout Statement</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Payout Statement</li>
                    </ol>
                </div>
            </div>
        </div>
         @include('layouts.filter')
    </div>
   
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Payout Transactions</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-bordered table-striped" style="width: 100%;">

                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Bank Deatils</th>
                                            <th>Transaction Details</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dynamic rows will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


@push('style')
<!-- DataTables Bootstrap 5 Styling -->
<link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('script')
<!-- DataTables and Bootstrap 5 JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

<script type="text/javascript">
   $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/payoutstatement/{{$id}}";
        var onDraw = function() {
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<div>
                            <span class='text-inverse m-l-10'><b>`+full.id +`</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                }
            },{ "data" : "bank",
                render:function(data, type, full, meta){
                    return `Account - `+full.number+`<br>Bank Name - `+full.option3+`<br>Bank Name - `+full.option4;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Client Txn Id. - `+full.apitxnid+`<br>Api Txnid - `+full.txnid+`<br>PayID - `+full.payid+`<br>UTR - `+full.refno;
                }
            },
           { "data" : "bank",
                render:function(data, type, full, meta){
                   
                        return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Charge - <i class="fa fa-inr"></i> `+full.charge;
                    
                }
            },
            
            { "data" : "status",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var out = `<span class="badge badge-light rounded-pill text-bg-success">`+full.status+`</span>`;
                    }else if(full.status == "complete"){
                        var out = `<span class="label label-primary">`+full.status+`</span>`;
                    }else if(full.status == "pending"){
                        var out = `<span class="badge badge-light rounded-pill text-bg-warning">Pending</span>`;
                    }else if(full.status == "reversed"){
                        var out = `<span class="badge badge-light rounded-pill text-bg-theme-accent-1">Reversed</span>`;
                    }else{
                        var out = `<span class="badge badge-light rounded-pill text-bg-danger">`+full.status+`</span>`;
                    }

                    var menu = ``;
                    @if (Myhelper::can('aeps_status'))
                    menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="status(`+full.id+`, 'upi')"><i class="icon-info22"></i>Check Status</a></li>`;
                    @endif

                    @if (Myhelper::can('aeps_statement_edit'))
                    menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'payout')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @endif

                    menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="complaint(`+full.id+`, 'upi')"><i class="icon-cogs"></i> Complaint</a></li>`;
                    

                    out +=  ``;

                    return out;
                }
            }
        ];

        datatableSetup(url, options, onDraw);
    });
</script>
@endpush