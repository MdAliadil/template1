@extends('layouts.app')
@section('title', "IDFC UPI Statement")
@section('pagetitle',  "IDFC UPI Statement")

@php
    $table = "yes";

    $status['type'] = "Idfcreport";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "reversed" => "Reversed",
        "refunded" => "Refunded",
    ];
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">IDFC UPI Statement</h4>
                    
                </div>
                <div class="table-responsive">
                    
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Type</th>
                            <th>User Details</th>
                            <th>Payee Details</th>
                            <th>Payer Details</th>
                            <th>Refrence Details</th>
                            <th>Amount/Charge/Commission</th>
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
</div>

@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/idfcupistatement/{{$id}}";
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
                    return `Payer VPA - `+full.payer_vpa+`<br>Name - `+full.payerAccName;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `NPCI Id. - `+full.refno+`<br>Txnid - `+full.txnid+`<br>Client Id- `+full.mytxnid+`<br>Bank Id - `+full.payid;
                }
            },
           { "data" : "bank",
                render:function(data, type, full, meta){
                   
                        return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Charge - <i class="fa fa-inr"></i> `+full.charge+`<br>GST - <i class="fa fa-inr"></i> `+full.gst;
                    
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
        $( "#upiRequestForm").validate({
            rules: {
                amount: {
                    required: true
                },
                
            },
            messages: {
                amount: {
                    required: "Please enter request amount",
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
                var form = $('#upiRequestForm');
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button:submit').button('loading');
                    },
                    complete: function () {
                        form.find('button:submit').button('reset');
                    },
                    success:function(data){
                        console.log(data);
                        if(data.statuscode == "TXN"){
                            form.closest('.modal').modal('hide');
                            
                            window.open(data.data.url, '_blank');
                            notify("Redirected Successfully", 'success');
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