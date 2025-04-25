   @include('layouts.links')
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
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <a href="{{ route('home') }}" class="breadcrumb-title pe-3" style="color:black">Home</a>

					{{-- <div class="breadcrumb-title pe-3" style="color:black">Home</div> --}}
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item" ><a href="javascript:;"><i class="bx bx-mobile-alt" style="color: black;"></i></a>
								</li>
								<li class="breadcrumb-item active" aria-current="page" style="color: black;">UPI Statement</li>
							</ol>
						</nav>
					</div>
					
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
				
			</main>
       <!--end page main-->

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
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

@endpush
