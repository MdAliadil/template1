  {{--  @include('layouts.links')  --}}
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
                <!--breadcrumb-->
                {{--  <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <h4 ><a href="{{ route('home') }}" class="breadcrumb-title " style="color:black">Home</a></h4>
                    <i class="bi bi-chevron-right text-muted mx-1 mb-2"></i>
                    <i class="bi bi-qr-code text-muted mx-1 mb-2 pe-1"></i>
       
       
                    <h5 style="color: black;">Wallet Pay</h5>
       
       
                     <div class="breadcrumb-title pe-3" style="color:black">Home</div>
       
       
                </div>  --}}
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('home') }}"> <span class="me-1 mb-1 h3 card-title d-inline-block">Home</span></a>
                     <span class="h3 text-muted card-title">/</span>
                     <span class="h3 ms-2 card-title"> Payout Wallet</span>
                   </div>
       @include('layouts.filter') 
   
    
                <div class="card">
                    <div class="card-header d-flex justify-content-between pb-0">
                <h4 class="mb-0 text-muted text-uppercase" >Payout Wallet</h4>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTables" class="table table-striped table-bordered" style="width:100%">
                                <thead>
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
               
                   
                    

</main>
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
   $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/payoutstatement/{{$id}}";
        var onDraw = function() {
            $('#loadingOverlay').hide();
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                }
            },
            { "data" : "id"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `UTR - `+full.refno;
                }
            },
          { "data" : "status",
                 render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var btn = '<span class=" badge bg-success text-uppercase rounded-pill"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class=" badge bg-warning text-uppercase rounded-pill"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class=" badge bg-danger text-uppercase rounded-pill"><b>'+full.status+'</b></span>';
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
            { "data" : "bank",
                render:function(data, type, full, meta){
                   
                        return `<i class="text-danger icon-dash"></i> `+ (parseFloat(full.charge) );
                    
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.status == "pending" || full.status == "success" || full.status == "reversed"||full.status == "refunded" ||full.status == "failed"){
                        if(full.trans_type == "credit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) + parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }else if(full.trans_type == "debit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }else if(full.trans_type == "none"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }
                    }else{
                        return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(full.charge) ).toFixed(2);
                    }
                }
            } 
        ];
        $(document).on('click', '#searchBtn', function () {
            $('#loadingOverlay').show();
        });
        datatableSetup(url, options, onDraw);
    });
</script>
@endpush