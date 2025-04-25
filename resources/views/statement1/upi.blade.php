@extends('layouts.app')
@section('title', "UPI Statement")
@section('pagetitle', "UPI Statement")

@php
    $table = "yes";
    $export = "upidata";

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
                            <h3 class="card-title">UPI Transactions</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dataTables" class="table table-bordered table-striped" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
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
        $('#dataTables').DataTable({
            processing: true,
                searching: false,
                serverSide: true,
                pageLength: 10, // Set default page length to 10
                orderable: false, // Disable sorting for all columns
                order: [], // Disable initial sorting
                orderClasses: false, 
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
                { data: 'id', render: (data, type, full) => `<b>${full.id}</b><br><small>${full.created_at}</small>` },
                
                {
                    data: 'aadhar',
                    render: (data, type, full) => `TXN ID: ${full.txnid}<br>Client TXN ID: ${full.mytxnid}<br>REF NO: ${full.refno}`
                },
                {
                    data: 'amount',
                    render: (data, type, full) => `Amount: ₹${full.amount}<br>Charge: ₹${full.charge}`
                },
                {
                    data: 'status',
                    render: (data, type, full) => {
                        const statusClasses = {
                            success: 'text-bg-success',
                            pending: 'text-bg-warning',
                            failed: 'text-bg-danger',
                            reversed: 'text-bg-info'
                        };
                        const badgeClass = statusClasses[full.status] || 'text-bg-secondary';
                        return `<span class="badge ${badgeClass}">${full.status}</span>`;
                    }
                }
            ]
        });
    });
</script>
@endpush