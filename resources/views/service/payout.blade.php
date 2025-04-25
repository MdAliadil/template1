@extends('layouts.app')
@section('title', "Payout Service")
@section('pagetitle', "Payout Service")


@section('content')

<div class="content">
    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Payout Service</h4>
                </div>
                <form id="serachForm" action="{{route('transaction')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="actiontype" value="payout">
                   
                    <div class="panel-body">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text"  name="f_name" class="form-control" placeholder="Enter Name" required="">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text"  name="l_name" class="form-control" placeholder="Enter Name" required="">
                        </div>
                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="number" step="any" name="mobile" class="form-control" placeholder="Enter Mobile Number" required="">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" step="any" name="email" class="form-control" placeholder="Enter email" required="">
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="number" step="any" name="account" class="form-control" placeholder="Enter Account Number" required="">
                        </div>
                        <div class="form-group">
                            <label>IFSC Number</label>
                            <input type="text" step="any" name="ifsc" class="form-control" placeholder="Enter IFSC" required="">
                        </div>
                        <div class="form-group">
                            <label>Bank Name</label>
                            <input type="text" step="any" name="bank" class="form-control" placeholder="Enter Bank Name" required="">
                        </div>
                        
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" step="any" name="amount" class="form-control" placeholder="Enter Amount" required="">
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        <button type="submit"  id="pay" class="btn bg-slate btn-labeled btn-xs legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paperplane"></i></b> Pay Now</button>                    </div>
                </form>
            </div>

            
        </div>
        
    </div>
</div>


@endsection

@push('script')
<script src="{{ asset('/assets/js/core/jQuery.print.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        

        $('#print').click(function(){
            $('#receptTable').print();
        });

        

        $( "#serachForm" ).validate({
            rules: {
                mobile: {
                    required: true,
                    number : true,
                    minlength:10,
                    maxlength:10
                },
                f_name: {
                    required: true
                    
                },
                l_name: {
                    required: true
                    
                },
                email: {
                    required: true
                    
                },
                account: {
                    required: true,
                    number : true
                    
                },
                ifsc: {
                    required: true
                    
                }
                
            },
            messages: {
                mobile: {
                    required: "Please enter mobile number",
                    number: "Mobile number should be numeric",
                    minlenght: "Mobile number length should be 10 digit",
                    maxlenght: "Mobile number length should be 10 digit",
                },
                f_name: {
                    required: "Please enter first name"
                    
                },
                l_name: {
                    required: "Please enter last name"
                    
                },
                email: {
                    required: "Please enter email"
                    
                },
                account: {
                    required: "Please enter account"
                    
                },
                ifsc: {
                    required: "Please enter ifsc"
                    
                }
                
                
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#serachForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            
                             notify("Payout Submited Successfully", 'success');
                             //location.reload();
                        }
                        else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
        $( "#payoutForm" ).validate({
            rules: {
                amount: {
                    required: true,
                    number : true
                    
                },
                mode: {
                    required: true
                    
                },
                type: {
                    required: true
                    
                }
                
                
            },
            messages: {
                amount: {
                    required: "Please enter amount",
                    number: "amount should be numeric"
                    
                },
                mode: {
                    required: "Please select mode"
                    
                },
                type: {
                    required: "Please select type"
                    
                }
                
                
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function () {
                var form = $('#payoutForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            
                            $('#contact_id').val(data.data);
                            
                             notify("Contact created Successfully", 'success');
                        }
                        else{
                            notify(data.message, 'danger', "inline",form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

       
    });

    
</script>
@endpush