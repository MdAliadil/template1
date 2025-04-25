@extends('layouts.app')
@section('title', "Uti Pancard")
@section('pagetitle', "Uti Pancard")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    @if ($vledata && $vledata->status == "failed")
        <p class="text-danger">Utiid Request is Rejected, {{$vledata->remark}}</p>
    @endif
    <div class="row">
        <div class="col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title pull-left">Uti Pancard</h4>
                    <a class="btn bg-slate legitRipple pull-right" href="http://www.psaonline.utiitsl.com/psaonline/" target="_blank">Login UTI Portal</a>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body p-0">
                    <table class="table table-bordered">
                        <tr><td>1 Token</td><td>1 PAN Application</td></tr>
                        <tr><td>Username</td><td>{{($vledata) ? $vledata->vleid : ''}}</td></tr>
                        <tr><td>Password</td><td>{{($vledata) ? $vledata->vlepassword : ''}}</td></tr>
                    </table>
                </div>
                <form id="pancardForm" action="{{route('pancardpay')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="actiontype" value="purchase">
                    <div class="panel-body">
                        <div class="form-group">
                            <label>No Of Tokens</label>
                            <input type="number" class="form-control" name="tokens" placeholder="Enter No. of tokens" required="">
                        </div>
                        <div class="form-group">
                            <label>Total Price in Rs</label>
                            <input type="number" class="form-control" id = "price" value = "" readonly>
                        </div>
                        <div class="form-group">
                            <label>Vle Id</label>
                            <input type="text" class="form-control" name="vleid" value="{{($vledata) ? $vledata->vleid : ''}}" required="">
                        </div>
                        <div class="form-group">
                            <label>T-Pin</label>
                            <input type="password" name="pin" class="form-control" placeholder="Enter transaction pin" required="">
                            <a href="{{url('profile/view?tab=pinChange')}}" target="_blank" class="text-primary pull-right">Generate Or Forgot Pin??</a>
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        @if ($vledata && $vledata->status == "success")
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paperplane"></i></b> Pay Now</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title pull-left">Recent Uti Pancard Token</h4>
                    @if (!$vledata || ($vledata && $vledata->status == "failed"))
                        <a class="btn bg-slate legitRipple pull-right" href="javascript:void(0)" onclick="vlerequest()">Request For Vle-id</a>
                    @elseif ($vledata && $vledata->status != "success")
                        <button disabled="disabled" class="btn bg-danger pull-right">Utiid Request is {{$vledata->status}}, {{$vledata->remark}}</button>
                    @endif
                    <div class="clearfix"></div>
				</div>
				<div class="panel-body">
				</div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User Details</th>
                            <th>Transaction Details</th>
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
<!-- Footer -->
<div class="footer text-muted">
    <div class="row">
        <div class="col-md-6">
            <h4><strong>Important T&amp;Cs:</strong></h4>
            <ul>
                <li>The fee for processing PAN application is ₹107 inclusive of GST.</li>
                <li>PAN card application can be processed using eKYC or physical documents.</li>
            </ul>
        </div>
        <div class="col-md-6 text-right">
            <div>Powered by</div>
            <img src="{{asset('')}}/assets/images/uti.png" style="position: relative;">
        </div>
    </div>
</div>
<!-- /footer -->

@endsection

@push('script')
	<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/utipancardstatement/0";
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
            { "data" : "username"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Vle Id - `+full.number+`<br>Tokens - `+full.option1;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `Amount - <i class="fa fa-inr"></i> `+full.amount+`<br>Profit - <i class="fa fa-inr"></i> `+full.profit;
                }
            },
            { "data" : "status",
                render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var out = `<span class="label label-success">Success</span>`;
                    }else if(full.status == "pending"){
                        var out = `<span class="label label-warning">Pending</span>`;
                    }else{
                        var out = `<span class="label label-danger">Failed</span>`;
                    }

                    var menu = ``;
                    @if (Myhelper::can('Utipancard_statement_edit'))
                    menu += `<li class="dropdown-header">Setting</li>
                            <li><a href="javascript:void(0)" onclick="editReport(`+full.id+`,'`+full.refno+`','`+full.txnid+`','`+full.payid+`','`+full.remark+`', '`+full.status+`', 'utipancard')"><i class="icon-pencil5"></i> Edit</a></li>`;
                    @endif

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

        $('[name="tokens"]').keyup(function(){
             $("#price").val($(this).val() * 107);

        });

        $( "#pancardForm" ).validate({
            rules: {
                tokens: {
                    required: true,
                    number : true,
                    min : 1
                },
                vleid: {
                    required: true
                }
            },
            messages: {
                tokens: {
                    required: "Please enter token number",
                    number: "Token should be numeric",
                    min: "Minimum one token is required",
                },
                vleid: {
                    required: "Please enter vle id",
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
                var form = $('#pancardForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        form.find('button[type="submit"]').button('reset');
                        if(data.status == "success"){
                            getbalance();
                            form[0].reset();
                            notify("Pancard Token Request Successfully Submitted", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify("Pancard "+data.status+ "! "+data.description, 'warning', 'inline', form);
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });
        function vlerequest(){
            $.ajax({
                url: "{{ route('pancardpay') }}",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                data : {"actiontype" : 'vleid'},
                beforeSend:function(){
                    swal({
                        title: 'Wait!',
                        text: 'We are feching details.',
                        onOpen: () => {
                            swal.showLoading()
                        }
                    });
                }
            })
            .success(function(data) {
                if(data.status == "success"){
                    swal({
                        type: "success",
                        title: "Success",
                        text: "Uti id request submitted successfull",
                        onClose: () => {
                            window.location.reload();
                        }
                    });
                }else{
                    swal.close();
                    notify(data.status, 'warning');
                }
            })
            .error(function(errors) {
                swal.close();
                showError(errors, $('#pancardForm'));
            });
        }
</script>
@endpush