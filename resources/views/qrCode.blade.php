@extends('layouts.app')
@section('title', "QR Code Request")
@section('pagetitle',  "QR Code Request")

@php
    $table = "yes";
   
    
@endphp

@section('content')
 @if(!$agent)
 <div class="row">
        <div class="col-sm-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">QR</h4>
                </div>
                <div><p>Please complete your onboaring process</p></div>
            </div>
        </div>
        
    </div>
    @else
    <div class="row">
        <div class="col-sm-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">QR Code</h4>
                </div>
                
                <div id="qrimage"></div>
            </div>
        </div>
        
    </div>
@endif    
@endsection

@push('style')

@endpush

@push('script')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
<script type="text/javascript">
    $(document).ready(function () {
        var vpa="{{$agent->vpaaddress}}";
        console.log(vpa);
        var vpastring='upi://pay?pa='+vpa+'&pn=SafepayIndia&tr=EZV2021101113322400027817&am=&cu=INR';
        console.log(vpastring);
        jQuery("#qrimage").qrcode({
                //render:"table"
                width: 500,
                height: 500,
                text: vpastring
            });
        
        
        
        var url = "{{url('statement/fetch')}}/aepsfundrequest/0";
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
                        var btn = '<span class="label label-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="label label-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="label label-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
                }
            }
        ];

        datatableSetup(url, options, onDraw);

        $( "#fundRequestForm").validate({
            rules: {
                amount: {
                    required: true
                },
                type: {
                    required: true
                },
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
                },
                type: {
                    required: "Please select request type",
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
                var form = $('#fundRequestForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button:submit').button('loading');
                    },
                    complete: function () {
                        form.find('button:submit').button('reset');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            form.closest('.modal').modal('hide');
                            notify("Fund Request submitted Successfull", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status , 'warning');
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