@extends('layouts.app')
@section('title', "UPI Statement")
@section('pagetitle',  "UPI Statement")

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
                            <p class="h5 mt-4">UPI Statement</p>
                             <table id="dataTables" class="table w-100 nowrap table-bordered">
                                <thead>
                                    <tr style="background: #471ba8;">
                                        <th class="all">MID</th>
                                        <th class="xs sm md">Message</th>
                                        
                                       
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
        var url = window.location.href;
        var midid = url.split('/').pop();
        
        var table = $('#dataTables').DataTable({
            processing: true,
            searching: false,
            serverSide: true,
            pageLength: 10, // Set default page length to 10
            orderable: false, // Disable sorting for all columns
            order: [], // Disable initial sorting
            orderClasses: false, // Disable the sorting class
            ajax: {
                url: '{{ url("getotps") }}/' + midid,
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
                { data: 'midid'},
                { data: 'message'},
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