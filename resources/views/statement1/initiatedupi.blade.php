@extends('layouts.app')
@section('title', "Pending UPI Statement")
@section('pagetitle',  "Pending UPI Statement")

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
                            <p class="h5 mt-4">Pending UPI Statement</p>
                             <table id="dataTables" class="table w-100 nowrap table-bordered">
                                <thead>
                                    <tr style="background: #471ba8;">
                                        <th class="all">Order ID</th>
                                        <th class="xs sm md">Type</th>
                                        <th class="all">User Details</th>
                                        <th class="xs sm">Payer Details</th>
                                        <th class="xs sm md">Refrence Details</th>
                                        <th class="xs sm md">Amount/Charge/Commission</th>
                                        <th class="xs sm md">Status</th>
                                        
                                       
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
        
        var table = $('#dataTables').DataTable({
            processing: true,
            searching: false,
            serverSide: true,
            pageLength: 10, // Set default page length to 10
            orderable: false, // Disable sorting for all columns
            order: [], // Disable initial sorting
            orderClasses: false, // Disable the sorting class
            ajax: {
                url: '{{ url('initiatedUpi') }}',
                data: function (d) {
                    console.log(d);
                    d.datefrom = $('input[name="from_date"]').val();
                    d.dateto = $('input[name="to_date"]').val();
                    d.searchtext = $('input[name="searchtext"]').val();
                    d.statustext = $('select[name="status"]').val();
                    d.agentid = $('input[name="agent"]').val();
                }
            },
            columns: [
                { data: 'id', name: 'id',
                    render: function(data, type, full, meta) {
                        return `<div>
                                    <span class='text-inverse m-l-10'><b>${full.id}</b> </span>
                                    <div class="clearfix"></div>
                                </div><span style='font-size:13px' class="pull=right">${full.created_at}</span>`;
                    }
                },
                { data: 'aepstype', name: 'aepstype',
                    render: function(data, type, full, meta) {
                        return `<div>
                                    <span class='text-inverse m-l-10'><b>${full.aepstype}</b> </span>
                                    <div class="clearfix"></div>
                                </div>`;
                    }
                },
                { data: 'username', name: 'username' },
                { data: 'payeeVPA', name: 'payeeVPA', 
                    render: function(data, type, full, meta) {
                        return `Payee-VPA - ${full.payeeVPA}`;
                    }
                },
                { data: 'aadhar', name: 'aadhar',
                    render: function(data, type, full, meta) {
                        return `PayId - ${full.txnid}<br>ClientOrdId - ${full.mytxnid}<br>UTR - ${full.refno}`;
                    }
                },
                { data: 'amount', name: 'amount',
                    render: function(data, type, full, meta) {
                        return `Amount - <i class="fa fa-inr"></i> ${full.amount}<br>Charge - <i class="fa fa-inr"></i> ${full.charge}`;
                    }
                },
                { data: 'status', name: 'status',
                    render: function(data, type, full, meta) {
                        let out = '';
                        if(full.status == "success"){
                            out = `<span class="badge badge-light rounded-pill text-bg-success">${full.status}</span>`;
                        }else if(full.status == "complete"){
                            out = `<span class="label label-primary">${full.status}</span>`;
                        }else if(full.status == "pending"){
                            out = `<span class="badge badge-light rounded-pill text-bg-warning">Pending</span>`;
                        }else if(full.status == "reversed"){
                            out = `<span class="badge badge-light rounded-pill text-bg-theme-accent-1">Reversed</span>`;
                        }else{
                            out = `<span class="badge badge-light rounded-pill text-bg-danger">${full.status}</span>`;
                        }

                        let menu = ``;
                        @if (Myhelper::can('aeps_status'))
                        menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="status(${full.id}, 'upi')"><i class="icon-info22"></i>Check Status</a></li>`;
                        @endif
                        @if (Myhelper::hasRole('employee'))
                        menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="chargeBackUpdate(${full.id}, 'chargeBackUpdate')"><i class="icon-info22"></i>Raise Charge Back</a></li>`;
                        @endif
                        
                        @if (Myhelper::can('aeps_statement_edit'))
                        menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="editReport(${full.id},'${full.refno}','${full.txnid}','${full.payid}','${full.remark}', '${full.status}', 'upi')"><i class="icon-pencil5"></i> Edit</a></li>`;
                        @endif

                        menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="complaint(${full.id}, 'upi')"><i class="icon-cogs"></i> Complaint</a></li>`;
                        
                        menu += `<li class="dropdown-item"><a href="javascript:void(0)" onclick="resendCallback(${full.id}, 'callbackresend')"><i class="icon-info22"></i>Resend Callback</a></li>`;  
                        out +=  `<div class="dropdown d-inline-block">
                                    <a class="btn btn-link no-caret" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end" style="">
                                        ${menu}
                                    </ul>
                                </div>`;
                        return out;
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