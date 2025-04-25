{{--  @include('layouts.links')  --}}
@extends('layouts.app')
@section('title', 'UPI')
@section('pagetitle', 'UPI')
@php
$table = 'yes';
$export = 'wallet';

$status['type'] = 'Report';
$status['data'] = [
    'success' => 'Success',
    'pending' => 'Pending',
    'failed' => 'Failed',
    'reversed' => 'Reversed',
    'refunded' => 'Refunded',
    'dispute' => 'Dispute',
];
@endphp
@section('content')
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3" ">
            <h4 ><a href="{{ route('home') }}" class="breadcrumb-title" style="color:black">Home</a></h4>
            <i class="bi bi-chevron-right text-muted mx-1 mb-2"></i>
            <i class="bi bi-credit-card  pe-2 mb-2"></i>
            <h5  style="mb-0 color: black;">UPI Statement</h5>


            {{-- <div class="breadcrumb-title pe-3" style="color:black">Home</div> --}}
            

        </div>
        @include('layouts.filter')
        <!--end breadcrumb-->

        <div class="card">
            <div class="card-header">

                <h4 class="mb-0 text-uppercase" style="color: black;">UPI Statement</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTables" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction Details</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>



                        </tbody>

                    </table>
                </div>
            </div>
        </div>

    
@endsection
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
    $(document).ready(function() {
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
                data: function(d) {
                    d.datefrom = $('input[name="from_date"]').val();
                    d.dateto = $('input[name="to_date"]').val();
                    d.searchtext = $('input[name="searchtext"]').val();
                    d.statustext = $('select[name="status"]').val();
                    d.agentid = $('input[name="agent"]').val();
                }
            },
            columns: [{
                    data: 'id',
                    render: (data, type, full) =>
                        `<b>${full.id}</b><br><small>${full.created_at}</small>`
                },

                {
                    data: 'aadhar',
                    render: (data, type, full) =>
                        `TXN ID: ${full.txnid}<br>Client TXN ID: ${full.mytxnid}<br>REF NO: ${full.refno}`
                },
                {
                    data: 'amount',
                    render: (data, type, full) =>
                        `Amount: ₹${full.amount}<br>Charge: ₹${full.charge}`
                },
                {
                    data: 'status',
                    render: (data, type, full) => {
                        const statusClasses = {
                            success: ' badge bg-success text-uppercase',
                            pending: 'badge bg-warning text-uppercase',
                            failed: 'badge bg-danger text-uppercase',
                            reversed: 'badge bg-info text-uppercase'    
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
