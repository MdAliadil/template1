@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "UPI Collect")
@section('pagetitle', "UPI Collect")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
   
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">UPI Collect</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('upicollectpay')}}" method="post" id="transactionForm"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label>UPI ID </label>
                                    <input type="text" class="form-control" autocomplete="off" name="vpa" placeholder="Enter Your vpa" value="" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label>Amount</label>
                                    <input type="text" class="form-control" name="amount" autocomplete="off" placeholder="Enter Your amount" value="" required>
                                </div>
                                
                                <div class="form-group col-md-3">
                                    <label>Remark</label>
                                    <input type="text" class="form-control" name="txnNote" autocomplete="off" placeholder="Enter Your remark" value="" required>
                                </div>
                                
                            </div>
                            
                            
                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
            <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
          <h4 class="panel-title">Recent UPI Collect</h4>
        </div>
        <div class="panel-body">
        </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>User Details</th>
                            <th>Payee Details</th>
                            <th>Payer Details</th>
                            <th>Bank Details</th>
                            <th>Refrence Details</th>
                            <th>Amount/Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
   
@endsection

@push('script')
<!--<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 

<script type="text/javascript">
    $(document).ready(function () {
       var url = "{{url('statement/fetch')}}/upicollectstatement/0";
        var onDraw = function() {
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<div>
                            <span class='text-inverse m-l-10'><b>`+full.id +`</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                }
            },
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<div>
                            <span class='text-inverse m-l-10'><b>`+full.aepstype +`</b> </span>
                            <div class="clearfix"></div>
                        </div>`;
                }
            },
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Payee-VPA - `+full.payeeVPA;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `payer_vpa - `+full.payer_vpa;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Adhaar - `+full.aadhar+`<br>Mobile - `+full.mobile;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Ref No. - `+full.refno+`<br>Txnid - `+full.mytxnid+`<br>Payid - `+full.payid;
                }
            },
           { "data" : "bank",
                render:function(data, type, full, meta){
                   
                        return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Commission - <i class="fa fa-inr"></i> `+full.charge;
                    
                }
            },
            
            { "data" : "status",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var out = `<span class="label label-success">`+full.status+`</span>`;
                    }else if(full.status == "complete"){
                        var out = `<span class="label label-primary">`+full.status+`</span>`;
                    }else if(full.status == "pending"){
                        var out = `<span class="label label-warning">Pending</span>`;
                    }else if(full.status == "reversed"){
                        var out = `<span class="label bg-slate">Reversed</span>`;
                    }else{
                        var out = `<span class="label label-danger">`+full.status+`</span>`;
                    }

                    var menu = ``;
                    @if (Myhelper::can('aeps_status'))
                    menu += `<li class="dropdown-header">Status</li>
                            <li><a href="javascript:void(0)" onclick="status(`+full.id+`, 'upi')"><i class="icon-info22"></i>Check Status</a></li>`;
                    @endif

                    @if (Myhelper::can('aeps_statement_edit'))
                    menu += `<li class="dropdown-header">Setting</li>
                            <li><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'upi')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @endif

                    menu += `<li class="dropdown-header">Complaint</li>
                            <li><a href="javascript:void(0)" onclick="complaint(`+full.id+`, 'upi')"><i class="icon-cogs"></i> Complaint</a></li>`;
                    

                    out +=  `<ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-menu9"></i>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        `+menu+`
                                    </ul>
                                </li>
                            </ul>`;

                    return out;
                }
            }
        ];

        datatableSetup(url, options, onDraw);
        
        
        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });
        
        $( "#transactionForm" ).validate({
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
                    required: "Please enter vpa",
                    
                },
                amount: {
                    required: "Please enter amount"
                   
                },
                txnNote: {
                    required: "Please enter txnNote"
                    
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
                var form = $('#transactionForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "success" || data.status == "pending"){
                            
                            notify("Transaction Collect request initiated successfully", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            
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