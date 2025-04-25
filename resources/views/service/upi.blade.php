@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Instant Wallet Load")
@section('pagetitle', "Instant Wallet Load")
@php
    $table = "yes";
    if($van){
        $van1 = explode("/", $van->vpa1);
        $van2 = explode("/", $van->vpa2);
    }
@endphp

@section('content')
<div class="content">
    @if(!$upi)
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">VPA Registration</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('upipay')}}" method="post" class="vpaTransactionForm">
                            <input type="hidden" name="serviceType" value="upi"> 
                            <input type="hidden" name="vpaAddress" value="vpa{{Auth::user()->id}}"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Merchant Business Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="businessName" placeholder="Enter Your businessName" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Pancard</label>
                                    <input type="text" class="form-control" name="panNo" autocomplete="off" placeholder="Enter Your Pancard" value="{{Auth::user()->pancard}}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Bank Account</label>
                                    <input type="text" class="form-control" placeholder="Enter value" name="bankAccountNo" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Bank Ifsc </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bankIfsc" placeholder="Enter value"required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="mobile" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="contactEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Gstn </label>
                                    <input type="text" class="form-control" autocomplete="off" name="gstn"  placeholder="Enter Your GST No">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>State</label>
                                    <select name="state" class="form-control select"  required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="city"  value="{{Auth::user()->city}}" placeholder="Enter Your City" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Pincode </label>
                                    <input type="text" class="form-control" autocomplete="off" name="pinCode" placeholder="Enter Your Pincode" pattern="[0-9]*" value="{{Auth::user()->pincode}}" maxlength="6" minlength="6" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Address </label>
                                    <input type="text" class="form-control" autocomplete="off" name="address" placeholder="Enter Your Address" value="{{Auth::user()->address}}" required>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-info bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>

            <!--<div class="col-sm-6">-->
            <!--    <div class="panel panel-default">-->
            <!--        <div class="panel-heading">-->
            <!--            <h4 class="panel-title">Virtual Account Registration</h4>-->
            <!--        </div>-->
            <!--        <div class="panel-body">-->
            <!--            <form action="{{route('upipay')}}" method="post" class="vanTransactionForm">-->
            <!--                <input type="hidden" name="serviceType" value="van"> -->
            <!--                {{ csrf_field() }}-->
            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Merchant Business Name </label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="businessName" placeholder="Enter Your merchantBusinessName" value="{{isset($name[0]) ? $name[0] : ''}}" required>-->
            <!--                    </div>-->

            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Pancard</label>-->
            <!--                        <input type="text" class="form-control" name="panNo" autocomplete="off" placeholder="Enter Your Pancard" value="{{Auth::user()->pancard}}" required>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Bank Account</label>-->
            <!--                        <input type="text" class="form-control" placeholder="Enter value" name="bankAccountNo" required>-->
            <!--                    </div>-->
                                
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Bank Ifsc </label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="bankIfsc" placeholder="Enter value"required>-->
            <!--                    </div>-->
            <!--                </div>-->
                            
            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Mobile</label>-->
            <!--                        <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="mobile" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>-->
            <!--                    </div>-->
                                
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Email </label>-->
            <!--                        <input type="email" class="form-control" autocomplete="off" name="contactEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Gstn </label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="gstn"  placeholder="Enter Your GST No">-->
            <!--                    </div>-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>State</label>-->
            <!--                        <select name="state" class="form-control select"  required>-->
            <!--                            <option value="">Select State</option>-->
            <!--                            @foreach ($mahastate as $state)-->
            <!--                            <option value="{{$state->stateid}}">{{$state->statename}}</option>-->
            <!--                            @endforeach-->
            <!--                        </select>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>City</label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="city"  value="{{Auth::user()->city}}" placeholder="Enter Your City" required>-->
            <!--                    </div>-->

            <!--                    <div class="form-group col-md-6">-->
            <!--                        <label>Pincode </label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="pinCode" placeholder="Enter Your Pincode" pattern="[0-9]*" value="{{Auth::user()->pincode}}" maxlength="6" minlength="6" required>-->
            <!--                    </div>-->
            <!--                </div>-->

            <!--                <div class="row">-->
            <!--                    <div class="form-group col-md-12">-->
            <!--                        <label>Address </label>-->
            <!--                        <input type="text" class="form-control" autocomplete="off" name="address" placeholder="Enter Your Address" value="{{Auth::user()->address}}" required>-->
            <!--                    </div>-->
            <!--                </div>-->
                            
            <!--                <div class="form-group text-center">-->
            <!--                    <button type="submit" class="btn bg-info bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>-->
            <!--                </div>-->
            <!--            </form>-->
            <!--        </div> -->
            <!--    </div>-->
            <!--</div>-->
        </div>
    @endif
    
    @if($upi || $van)
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Scan & Pay</h4>
                    </div>
                    <form action="{{route('upipay')}}" method="post" id="transactionForm">
                        <input type="hidden" name="type" value="collect">
                        <div class="panel-body" style="text-align: center;">
                            <div>
                                <img src="{{$vpa1qr->qr ?? ''}}" width="250px" height="250px"/>
                            </div>
                            <h4>{{$upi->businessName ?? ''}}</h4>
                            <h5>{{$upi->vpa1 ?? ''}}</h5>
                            <h5>Powered By YesBank</h5>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Scan & Pay</h4>
                    </div>
                    <form action="{{route('upipay')}}" method="post" id="transactionForm">
                        <input type="hidden" name="type" value="collect">
                        <div class="panel-body" style="text-align: center;">
                            <div>
                                <img src="{{$vpa2qr->qr ?? ''}}" width="250px" height="250px" />
                            </div>
                            <h4>{{$upi->businessName ?? ''}}</h4>
                            <h5>{{$upi->vpa2 ?? ''}}</h5>
                            <h5>Powered By Icici Bank</h5>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($van)
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Send Money To Virtaul Account</h4>
                    </div>
                    <form action="{{route('upipay')}}" method="post" id="transactionForm">
                        <input type="hidden" name="type" value="collect">
                        <div class="panel-body" style="text-align: center;">
                            <h5>Name - {{$van->businessName ?? ''}}</h5>
                            <h5>Account - {{$van1[0] ?? ''}}</h5>
                            <h5>Ifsc Code - {{$van1[1] ?? ''}}</h5>
                            <h5>Powered By Yes Bank</h5>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Send Money To Virtaul Account</h4>
                    </div>
                    <form action="{{route('upipay')}}" method="post" id="transactionForm">
                        <input type="hidden" name="type" value="collect">
                        <div class="panel-body" style="text-align: center;">
                            <h5>Name - {{$van->businessName ?? ''}}</h5>
                            <h5>Account - {{$van2[0] ?? ''}}</h5>
                            <h5>Ifsc Code - {{$van2[1] ?? ''}}</h5>
                            <h5>Powered By Idfc Bank</h5>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    @endif
    
    @if(!$van)
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Virtual Account Registration</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('upipay')}}" method="post" class="vanTransactionForm">
                            <input type="hidden" name="serviceType" value="van"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Merchant Business Name </label>
                                    <input type="text" class="form-control" autocomplete="off" name="businessName" placeholder="Enter Your merchantBusinessName" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Pancard</label>
                                    <input type="text" class="form-control" name="panNo" autocomplete="off" placeholder="Enter Your Pancard" value="{{Auth::user()->pancard}}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Bank Account</label>
                                    <input type="text" class="form-control" placeholder="Enter value" name="bankAccountNo" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Bank Ifsc </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bankIfsc" placeholder="Enter value"required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="mobile" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>
                                
                                <div class="form-group col-md-6">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="contactEmail" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Gstn </label>
                                    <input type="text" class="form-control" autocomplete="off" name="gstn"  placeholder="Enter Your GST No">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>State</label>
                                    <select name="state" class="form-control select"  required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="city"  value="{{Auth::user()->city}}" placeholder="Enter Your City" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Pincode </label>
                                    <input type="text" class="form-control" autocomplete="off" name="pinCode" placeholder="Enter Your Pincode" pattern="[0-9]*" value="{{Auth::user()->pincode}}" maxlength="6" minlength="6" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Address </label>
                                    <input type="text" class="form-control" autocomplete="off" name="address" placeholder="Enter Your Address" value="{{Auth::user()->address}}" required>
                                </div>
                            </div>
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-info bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function () {
        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });

        // var vpa1 = "{{$upi->vpa1 ?? ''}}";
        // var vpastring = 'upi://pay?pa='+vpa1+'&pn={{$upi->businessName ?? ''}}&tr=EZV2021101113322400027817&am=&cu=INR';
        // jQuery(".qrimage1").qrcode({
        //     width  : 250,
        //     height : 250,
        //     text: vpastring
        // });

        // var vpa2 = "{{$upi->vpa2 ?? ''}}";
        // var vpastring2 = 'upi://pay?pa='+vpa2+'&pn={{$upi->businessName ?? ''}}&tr=EZV2021101113322400027817&am=&cu=INR';
        // jQuery(".qrimage2").qrcode({
        //     width  : 250,
        //     height : 250,
        //     text: vpastring
        // });

        $( ".vpaTransactionForm" ).validate({
            rules: {
                businessName: {
                    required: true
                   
                },
                panNo: {
                    required: true
                    
                },
                contactEmail: {
                    required: true
                    
                },
                merchantBusinessType: {
                    required: true
                    
                },
                mobile: {
                    required: true,
                    number : true,
                    min: 10
                },
                panNo: {
                    required: true
                    
                },
                state: {
                    required: true
                    
                },
                city: {
                    required: true
                
                },
                picode: {
                    required: true,
                    number : true,
                    min: 6
                },
                address: {
                    required: true
                   
                },
            },
            messages: {
                businessName: {
                    required: "Please enter merchantBusinessName",
                    
                },
                panNo: {
                    required: "Please enter panNo"
                    
                },
                contactEmail: {
                    required: "Please enter contactEmail"
                    
                },
                mobile: {
                    required: "Please enter mobile",
                    number : "Mobile should be numeric",
                    min: "minimum lenght should be 10 digit"
                    
                },
                state: {
                    required: "Please enter state"
                    
                },
                gstn: {
                    required: "Please enter gstn"
                    
                },
                picode: {
                    required: "Please enter picode",
                    number:"Pincode should be numeric",
                    min: "Minimum length should be 6"
                    
                },
                address: {
                    required: "Please enter address"
                    
                },
                city: {
                    required: "Please enter city"
                    
                },
                
                
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function (form) {
                var id = $(form).find('[name="id"]').val();
                $(form).ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        $(form).find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        $(form).find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN"){
                            notify("Request Successfully Submitted", 'success');
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        }else{
                            notify("Recharge Failed! "+data.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, $(form));
                    }
                });
            }
        });

        $( ".vanTransactionForm" ).validate({
            rules: {
                businessName: {
                    required: true
                   
                },
                panNo: {
                    required: true
                    
                },
                contactEmail: {
                    required: true
                    
                },
                merchantBusinessType: {
                    required: true
                    
                },
                mobile: {
                    required: true,
                    number : true,
                    min: 10
                },
                panNo: {
                    required: true
                    
                },
                state: {
                    required: true
                    
                },
                city: {
                    required: true
                
                },
                picode: {
                    required: true,
                    number : true,
                    min: 6
                },
                address: {
                    required: true
                   
                },
            },
            messages: {
                businessName: {
                    required: "Please enter merchantBusinessName",
                    
                },
                panNo: {
                    required: "Please enter panNo"
                    
                },
                contactEmail: {
                    required: "Please enter contactEmail"
                    
                },
                mobile: {
                    required: "Please enter mobile",
                    number : "Mobile should be numeric",
                    min: "minimum lenght should be 10 digit"
                    
                },
                state: {
                    required: "Please enter state"
                    
                },
                gstn: {
                    required: "Please enter gstn"
                    
                },
                picode: {
                    required: "Please enter picode",
                    number:"Pincode should be numeric",
                    min: "Minimum length should be 6"
                    
                },
                address: {
                    required: "Please enter address"
                    
                },
                city: {
                    required: "Please enter city"
                    
                },
            },
            errorElement: "p",
            errorPlacement: function ( error, element ) {
                if ( element.prop("tagName").toLowerCase() === "select" ) {
                    error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                } else {
                    error.insertAfter( element );
                }
            },
            submitHandler: function (form) {
                var id = $(form).find('[name="id"]').val();
                $(form).ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        $(form).find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        $(form).find('button[type="submit"]').button('reset');
                        if(data.statuscode == "TXN" ){
                            notify("Request Successfully Submitted", 'success');
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        }else{
                            $(form).prepend(`<div class="alert bg-danger alert-styled-left">
                                    <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                                    <span class="text-semibold">Oops !</span> `+data.message+`
                                </div>`);
                            setTimeout(function () {
                                form.find('div.alert').remove();
                            }, 10000);
                    
                            notify("Request Failed! "+data.message, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, $(form));
                    }
                });
            }
        });
        
        $( "#upiTransactionForm" ).validate({
            rules: {
                vpa: {
                    required: true
                   
                },
                amount: {
                    required: true
                    
                },
                txnNote: {
                    required: true
                }
            },
            messages: {
                vpa: {
                    required: "Please enter value",
                    
                },
                amount: {
                    required: "Please enter value"
                   
                },
                txnNote: {
                    required: "Please enter value"
                    
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
                var form = $('#upiTransactionForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "TXN"){
                            notify("Collect Request Submitted Successfully", 'success');
                            
                        }else{
                            notify(data.message, 'warning');
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