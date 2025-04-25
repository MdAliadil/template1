@extends('layouts.app')
@section('title', "Upi Fund Request")
@section('pagetitle',  "Upi Fund Request")

@php
    $table = "yes";
    $export = "aepsfundrequest";
    
    $status['type'] = "Fund";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "approved" => "Approved",
        "rejected" => "Rejected",
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
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="h5 mt-4">UPI Fund Request</p>
                                <div class="col-auto py-1">
                                    
                                    <button class="btn btn-theme" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus me-0 me-md-1">
                                            <line x1="12" y1="5" x2="12" y2="19"></line>
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                        </svg> 
                                        Wallet Move Request
                                    </button>
                                </div>
                            </div>
                             <table id="dataTables" class="table w-100 nowrap table-bordered">
                                <thead>
                                    <tr style="background: #471ba8;">
                                        <th class="all">Order ID</th>
                                        <th class="xs sm md">User Details</th>
                                        <th class="all">Bank Details</th>
                                        <th class="xs sm">Refrence Details</th>
                                        <th class="xs sm md">Amount</th>
                                        <th class="xs sm md">Remark</th>
                                        <th class="xs sm md">Status</th>
                                        
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel" aria-modal="true" role="dialog">
                        <div class="offcanvas-header">
                            <p class="offcanvas-title h5" id="offcanvasScrollingLabel">Wallet Move Request</p>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                           <div class="offcanvas-body">
                            <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                                {{ csrf_field() }}
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="text" class="form-control" id="amount" name="amount" placeholder="Enter amount" min="10" required>
                                    <p class="text-danger" id="amountError" style="display:none;"></p>
                                </div>
                                <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                            </form>
                            <p class="text-secondary"><code>This is for Payin Wallet To Payout wallet move request</code></p>
                        </div>

                    </div>

                </div>
                
                
            </main>

@endsection
<style>
    .btn.bg-slate.btn-raised.legitRipple{
  background: #471ba8;
  color: white;
}
</style>
@push('script')

<script type="text/javascript">
    $(document).ready(function () {

        var url = "{{url('statement/fetch')}}/upifundrequest/0";
        var onDraw = function() {};
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    var out = '';
                    if(full.api){
                        out +=  `<span class='myspan'>`+full.api.api_name +`</span><br>`;
                    }
                    out += `<span class='text-inverse'>`+full.id +`</span><br><span style='font-size:12px'>`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        return full.account +" ( "+full.bank+" )<br>"+full.ifsc;
                    }
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.type == "wallet"){
                        return "Wallet"
                    }else{
                        if(full.pay_type == "payout"){
                            return "Ref - "+full.payoutref +"<br>Txnid - "+full.payoutid;
                        }else{
                            return "Manual";
                        }
                    }
                }
            },
            { "data" : "description",
                render:function(data, type, full, meta){
                    return `<span class='text-inverse'><i class="fa fa-rupee"></i> `+full.amount +`</span> / `+full.type;
                }
            },
            { "data" : "remark"},
            { 
                "data": "action",
                render:function(data, type, full, meta){
                    if(full.status == "approved"){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="badge badge-light rounded-pill text-bg-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $('#fundRequestForm').on('submit', function (event) {
            event.preventDefault(); 
        $('#amountError').hide().text('');

        var amountValue = $('#amount').val().trim();

        if (!amountValue) {
            $('#amountError').text("Please enter an amount").show();
            event.preventDefault(); // Prevent form submission
            return;
        }

        var amountNumber = Number(amountValue);
        if (isNaN(amountNumber) || amountNumber < 10) {
            $('#amountError').text("Amount must be at least 10").show();
            event.preventDefault(); // Prevent form submission
            return;
        }
        var TXN_AMOUNT=$("#amount").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        // If everything is okay, the form will submit
               var form = $('#fundRequestForm');
               var submitButton = form.find('button[type="submit"]');

                $.ajax({
                    type: 'POST',
                    url: '{{route('fundtransaction')}}',
                    data: {_token: CSRF_TOKEN,amount:TXN_AMOUNT,type:'wallet'},
                    beforeSubmit:function(){
                        submitButton.prop('disabled', true); // Disable the button
                        submitButton.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');


                    },
                    complete: function () {
                       submitButton.prop('disabled', false); // Enable the button again
                        submitButton.html('Submit'); // Reset button text

                    },
                    success:function(data){
                        
                        if(data.status == "success"){
                            form[0].reset(); 
                            //notify("Fund Request submitted Successfull", 'success');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: "Fund Request submitted Successfull",
                                confirmButtonText: 'OK'
                            }); 
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status , 'warning');
                        }
                    },
                    error: function(errors) {
                        form[0].reset(); 
                        //console.log(errors.responseJSON.status);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errors.responseJSON.status,
                            confirmButtonText: 'OK'
                        });
                    }
                }); 
    });
    });
</script>
@endpush