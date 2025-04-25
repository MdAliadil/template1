@extends('layouts.app')
@section('title', "UPI Statement")
@section('pagetitle', "UPI Statement")

@php
    $table = "yes";
    $export = "wallet";

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
                    <h3 class="mb-0">UPI Statement</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">UPI Statement</li>
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
                            <h3 class="card-title">UPI Wallet</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-bordered table-striped" style="width: 100%;">

                                    <thead>
                                        <tr>
                                            <th>TXN Time</th>
                                            <th>TXN ID</th>
                                            <th>Transaction Details</th>
                                            <th>Status</th>
                                            <th>Opening Bal</th>
                                            <th>Amount(+)</th>
                                            <th>Charge (-)</th>
                                            <th>Closing Bal</th>
                                            
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
        var table = $('#dataTables').DataTable({
            processing: true,
            searching: false,
            serverSide: true,
            pageLength: 10, // Set default page length to 10
            orderable: false, // Disable sorting for all columns
            order: [], // Disable initial sorting
            orderClasses: false, // Disable the sorting class
            ajax: {
                url: '{{ url('getUpiOrdersSuccess') }}',
                data: function (d) {
                    d.datefrom = $('input[name="from_date"]').val();
                    d.dateto = $('input[name="to_date"]').val();
                    d.searchtext = $('input[name="searchtext"]').val();
                    d.statustext = $('select[name="status"]').val();
                    d.agentid = $('input[name="agent"]').val();
                }
            },
            columns: [
                { "data" : "name",
                render:function(data, type, full, meta){
                    var out = "";
                    out += `</a><span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "id"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `UTR - `+full.refno;
                }
            },
            { 
                "data": "action",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="badge badge-light rounded-pill text-bg-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
                }
            }, 
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `<i class="fa fa-inr"></i> `+full.balance;
                }
            },
            { "data" : "amount"},
            { "data" : "charge"},
            {
                "data" : "bank",
                render: function(data, type, full, meta) {
                    if(full.status == "pending" || full.status == "success" || full.status == "reversed" || full.status == "refunded") {
                        if(full.trans_type == "credit") {
                            return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) + parseFloat(full.amount) - parseFloat(full.charge)).toFixed(2);
                        } else if(full.trans_type == "debit") {
                            return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) - (parseFloat(full.amount) + parseFloat(full.charge))).toFixed(2);
                        } else if(full.trans_type == "none") {
                            return `<i class="fa fa-inr"></i> ` + (parseFloat(full.balance) - (parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }
                    } else {
                        return `<i class="fa fa-inr"></i> ` + parseFloat(full.balance).toFixed(2);
                    } 
                }
            } 
            ],
            drawCallback: function(settings) {
                // Any additional callback logic goes here.
                // $('#searchForm').find('button:submit').button('reset');
                // $('#formReset').button('reset');
            }
        });

        $('#search').on('click', function() {
            table.draw();
            
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
</script>
@endpush