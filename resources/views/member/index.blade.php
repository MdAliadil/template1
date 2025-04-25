@extends('layouts.app')
@section('title', ucwords($role->name??"").' List')
@section('pagetitle',  ucwords($role->name??"").' List')

@php
    $table = "yes";
    $export = $type;

    $table = "yes";
    switch($type){
        case 'kycpending':
        case 'kycsubmitted':
        case 'kycrejected':
            $status['type'] = "Kyc";
            $status['data'] = [
                "pending" => "Pending",
                "submitted" => "Submitted",
                "verified" => "Verified",
                "rejected" => "Rejected",
            ];
        break;

        default:
            $status['type'] = "member";
            $status['data'] = [
                "active" => "Active",
                "block" => "Block"
            ];
        break;
    }
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    
                    <h4 class="panel-title">{{isset($role->name) ? $role->name : $type}} List</h4>
                    @if ($role || sizeOf($roles) > 0)
                        <div class="heading-elements">
                            <a href="{{route('member', ['type' => $type, 'action' => 'create'])}}"><button type="button" class="btn btn-sm bg-slate btn-raised heading-btn legitRipple">
                                <i class="icon-plus2"></i> Add New
                            </button></a>
                        </div>
                    @endif
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Parent Details</th>
                            <th>Company Profile</th>
                            <th>Wallet Details</th>
                            @if($type =="whitelable")
                            <th>UPI Charge</th>
                            @endif
                            @if(Myhelper::hasNotRole(['reseller']))
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="transferModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title">Fund Transfer / Return</h6>
            </div>
            <form id="transferForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="user_id">
                        {{ csrf_field() }}
                        
                        <div class="form-group col-md-6">
                            <label>Wallet</label>
                            <select name="walletType" class="form-control select" id="select" required>
                                <option value="">Select Action</option>
                                @if (Myhelper::hasRole('employee'))
                                <option value="mainwallet">Main Wallet</option>
                                <option value="upiwallet">Upi Wallet</option>
                                @endif
                            </select>
                        </div>
                        
                        <div class="form-group col-md-6">
                            <label>Fund Action</label>
                            <select name="type" class="form-control select" id="select" required>
                                <option value="">Select Action</option>
                                @if (Myhelper::can('fund_transfer'))
                                <option value="transfer">Transfer</option>
                                @endif
                                @if (Myhelper::can('fund_return'))
                                <option value="return">Return</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" name="amount" step="any" class="form-control" placeholder="Enter Amount" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <textarea name="remark" class="form-control" rows="3" placeholder="Enter Remark"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div id="upichargeModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h6 class="modal-title">Upi Charge Manager</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="userwalletFormdata" action="{{route('profileUpdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="updatemerchantcharge">
                        
                        {{ csrf_field() }}
                        <div class="form-group col-md-12">
                            <label>Charge</label>
                            <input type="number" name="upicharge" step="any" class="form-control" placeholder="Enter upicharge" required="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn  btn-primary legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="userwalletLockekModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h6 class="modal-title">Wallet Locked</h6>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="userwalletForm" action="{{route('profileUpdate')}}" method="post">
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id">
                        <input type="hidden" name="actiontype" value="userlockedamount">
                        
                        {{ csrf_field() }}
                        <div class="form-group col-md-12">
                            <label>Locked Amount</label>
                            <input type="number" name="lockedamount" step="any" class="form-control" placeholder="Enter Amount" required="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn  btn-primary legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@if (isset($permissions) && $permissions && Myhelper::can('member_permission_change'))
    <div id="permissionModal" class="modal fade right" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-slate">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h6 class="modal-title">Member Permission</h6>
                </div>
                <form id="permissionForm" action="{{route('toolssetpermission')}}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="user_id">
                    <div class="modal-body p-0">
                        <table id="datatable" class="table table-hover table-bordered">
    	                    <thead>
    	                    <tr>
    	                        <th width="170px;">Section Category</th>
    	                        <th>
                                    <span class="pull-left m-t-5 m-l-10">Permissions</span> 
                                    <div class="md-checkbox pull-right">
                                        <input type="checkbox" id="selectall">
                                        <label for="selectall">Select All</label>
                                    </div>
                                </th>
    	                    </tr>
    	                    </thead>
    	                    <tbody>
                                @foreach ($permissions as $key => $value)
                                    <tr>
                                        <td>
                                            <div class="md-checkbox mymd">
                                                <input type="checkbox" class="selectall" id="{{ucfirst($key)}}">
                                                <label for="{{ucfirst($key)}}">{{ucfirst($key)}}</label>
                                            </div>
                                        </td>

                                        <td class="row">
                                            @foreach ($value as $permission)
                                                <div class="md-checkbox col-md-4 p-0" >
                                                    <input type="checkbox" class="case" id="{{$permission->id}}" name="permissions[]" value="{{$permission->id}}">
                                                    <label for="{{$permission->id}}">{{$permission->name}}</label>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
    	                    </tbody>
    	                </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                        <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endif

<div id="commissionModal" class="modal fade right" role="dialog" data-backdrop="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Scheme Manager</h4>
            </div>
            <form id="schemeForm" method="post" action="{{ route('profileUpdate') }}">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="scheme">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label>Scheme</label>
                            <select class="form-control select" name="scheme_id" required="" onchange="viewCommission(this)">
                                <option value="">Select Scheme</option>
                                @foreach ($scheme as $element)
                                    <option value="{{$element->id}}">{{$element->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label style="width:100%">&nbsp;</label>
                            <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                            <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="modal-body no-padding commissioData">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

@if (Myhelper::can('member_stock_manager'))
    <div id="idModal" class="modal fade" role="dialog" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-slate">
                    <h4 class="modal-title">Ids Stock</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                
                    <div class="modal-body">
                        @if ($type == "other")
                        <form class="idForm" method="post" action="{{ route('profileUpdate') }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="actiontype" value="wstock">
                            <input type="hidden" name="id" value="">
                            <table class="table table-bordered" cellpadding="0" cellspacing="0">
                                <tr>
                                    <th width="150px">Stock Type</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Whitelable Id</td>
                                    <td>
                                        <input type="number" name="wstock" step="any" class="form-control" placeholder="Enter Value" required="">
                                    </td>
                                    <td>
                                        <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        @endif
                        <br>
                        
                        @if ($type == "other" || $type == "whitelable")
                        <form class="idForm" method="post" action="{{ route('profileUpdate') }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="actiontype" value="mstock">
                            <input type="hidden" name="id" value="">
                            <table class="table table-bordered" cellpadding="0" cellspacing="0">
                                <tr>
                                    <th width="150px">Stock Type</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Master Id</td>
                                    <td>
                                        <input type="number" name="mstock" step="any" class="form-control" placeholder="Enter Value" required="">
                                    </td>
                                    <td>
                                        <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        @endif
                        <br>
                        @if ($type == "other" || $type == "md" || $type == "whitelable")
                        <form class="idForm" method="post" action="{{ route('profileUpdate') }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="actiontype" value="dstock">
                            <input type="hidden" name="id" value="">
                            <table class="table table-bordered" cellpadding="0" cellspacing="0">
                                <tr>
                                    <th width="150px">Stock Type</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Distributor Id</td>
                                    <td>
                                        <input type="number" name="dstock" step="any" class="form-control" placeholder="Enter Value" required="">
                                    </td>
                                    <td>
                                        <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        @endif
                        <br>
                        @if ($type == "other" || $type == "md" || $type == "whitelable" || $type == "distributor")
                        <form class="idForm" method="post" action="{{ route('profileUpdate') }}">
                            {!! csrf_field() !!}
                            <input type="hidden" name="actiontype" value="rstock">
                            <input type="hidden" name="id" value="">
                            <table class="table table-bordered" cellpadding="0" cellspacing="0">
                                <tr>
                                    <th width="150px">Stock Type</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                                <tr>
                                    <td>Retailer Id</td>
                                    <td>
                                        <input type="number" name="rstock" step="any" class="form-control" placeholder="Enter Value" required="">
                                    </td>
                                    <td>
                                        <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                        @endif     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.modal -->
    <div id="fundRequestModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title">UPI Wallet To Wallet</h6>
            </div>
            <form id="fundRequestForm" action="{{route('fundtransaction')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="user_id">
                    <input type="hidden" name="type" value="userwallet">
                    {{ csrf_field() }}
                    

                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" placeholder="Enter amount" required="">
                        </div>
                    </div>
                    <p class="text-danger">Note - If you want to change bank details, please send mail with account details to update your bank details.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-raised legitRipple" data-dismiss="modal" aria-hidden="true">Close</button>
                    <button class="btn bg-slate btn-raised legitRipple" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Submitting">Submit</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endif
@endsection

@push('style')
<style>
    .md-checkbox {
        margin: 5px 0px;
    }
</style>
@endpush

@push('script')
<script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.select').select2();

        var url = "{{url('statement/fetch')}}/{{$type}}/0";
        var onDraw = function() {
            $('input#membarStatus').on('click', function(evt){
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "block";
                if($(this).prop('checked')){
                    status = "active";
                }
                
                $.ajax({
                    url: '{{ route('profileUpdate') }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    data: {'id':id, 'status':status, 'actiontype' : 'profile'}
                })
                .done(function(data) {
                    if(data.status == "success"){
                        notify("Member Updated", 'success');
                        $('#datatable').dataTable().api().ajax.reload();
                    }else{
                        if(status == "active"){
                            ele.prop('checked', false);
                        }else{
                            ele.prop('checked', true);
                        }
                        notify("Something went wrong, Try again." ,'warning');
                    }
                })
                .fail(function(errors) {
                    if(status == "active"){
                        ele.prop('checked', false);
                    }else{
                        ele.prop('checked', true);
                    }
                    showError(errors, "withoutform");
                });
            });
        };
        var options = [
            { "data" : "name",
                'className' : "notClick",
                render:function(data, type, full, meta){
                    var check = "";
                    var type = "";
                    if(full.status == "active"){
                        check = "checked='checked'";
                    }

                    return `<div>
                            <label class="switch">
                                <input type="checkbox" id="membarStatus" `+check+` value="`+full.id+`" actionType="`+type+`">
                                <span class="slider round"></span>
                            </label>
                            <span class='text-inverse pull-right m-l-10'><b>`+full.id +`</b> </span>
                            <div class="clearfix"></div>
                        </div>
                        <span style='font-size:13px'>`+full.updated_at+`</span>`;
                }
            },
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<span class="name">`+full.name+`</span>` +`<br>`+full.mobile+`<br>`+full.role.name;
                }
            },
            { "data" : "parents"},
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<span class="name">`+full.company.companyname+`</span>` +`<br>`+full.company.website;
                }
            },
            { "data" : "name",
                render:function(data, type, full, meta){
                    //return "Connect Wallet : "+full.mainwallet;
                    return `Connect Wallet : `+full.mainwallet+`<br> Upi Wallet: ` +full.upiwallet+`<br> Locked Wallet: ` +full.lockedamount;
                }
            },
            @if($type =="whitelable")
            { "data" : "updatemerchantcharge"},
            @endif
           
            @if(Myhelper::hasNotRole(['reseller']))
            { "data" : "action",
                render:function(data, type, full, meta){
                    var out = '';
                    var menu = ``;

                    @if (Myhelper::can(['fund_transfer', 'fund_return']))
                        menu += `<li class="dropdown-header">Action</li><li><a href="javascript:void(0)" onclick="transfer(`+full.id+`)"><i class="icon-wallet"></i> Fund Transfer / Return</a></li>`;
                         menu += `<li><a href="javascript:void(0)" onclick="scheme(`+full.id+`, '`+full.scheme_id+`')"><i class="icon-wallet"></i> Scheme</a></li>`;
                    @endif
                    
                    @if(Myhelper::hasRole('employee'))
                        menu += `<li class="dropdown-header">Action</li><li><a href="javascript:void(0)" onclick="redemRequest(`+full.id+`)"><i class="icon-wallet"></i> Redem Request</a></li>`; 
                    @endif

                    menu += `<li><a href="javascript:void(0)" onclick="lockedAmount(`+full.id+`)"><i class="icon-wallet"></i> Locked Amount</a></li>`;

                    

                    @if (Myhelper::can('member_permission_change'))
                        menu += `<li class="dropdown-header">Setting</li><li><a href="javascript:void(0)" onclick="getPermission(`+full.id+`)"><i class="icon-cogs"></i> Permission</a></li>`;
                    @endif

                    @if (Myhelper::can('view_kycpending'))
                        menu += `<li><a href="{{url('profile/view')}}/`+full.id+`" target="_blank"><i class="icon-user"></i> View Profile</a></li>`;
                    @endif

                    @if (Myhelper::can('member_kyc_update'))
                        menu += `<li><a href="javascript:void(0)" onclick="kycManage(`+full.id+`, '`+full.kyc+`', '`+full.remark+`')"><i class="icon-cogs"></i> Kyc Manager</a></li>`;
                    @endif

                    out +=  `<ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle mt-10" data-toggle="dropdown">
                                        <span class="label bg-slate">Action <i class="icon-arrow-down5"></i></span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        `+menu+`
                                    </ul>
                                </li>
                            </ul>`;
                    
                    var out2 = '';
                    var menu2 = ``;

                    @if (Myhelper::can(['member_billpayment_statement_view']))
                        menu2 += `<li><a href="{{url('statement/upiaccount/')}}/`+full.id+`" target="_blank"><i class="icon-paragraph-justify3"></i> Upi Statement</a></li>`;
                    @endif

                    @if (Myhelper::can(['member_account_statement_view']))
                        menu2 += `<li><a href="{{url('statement/account/')}}/`+full.id+`" target="_blank"><i class="icon-paragraph-justify3"></i> Account Statement</a></li>`;
                    @endif

                    out2 +=  `<ul class="icons-list">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle mt-10" data-toggle="dropdown">
                                        <span class="label bg-slate">Reports <i class="icon-arrow-down5"></i></span>
                                    </a>

                                    <ul class="dropdown-menu dropdown-menu-right">
                                        `+menu2+`
                                    </ul>
                                </li>
                            </ul>`;
                    return out+out2;
                }
            }
            @endif
        ];

        datatableSetup(url, options, onDraw);

        $( "#transferForm").validate({
            rules: {
                type: {
                    required: true
                },
                amount: {
                    required: true,
                    min : 1
                }
            },
            messages: {
                type: {
                    required: "Please select transfer action",
                },
                amount: {
                    required: "Please enter amount",
                    min : "Amount value should be greater than 0"
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
            submitHandler: function () {
                var form = $('#transferForm');
                var type = $('#transferForm').find('[name="type"]').val();
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
                            getbalance();
                            form.closest('.modal').modal('hide');
                            notify("Fund "+type+" Successfull", 'success');
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

        $( "#userwalletForm").validate({
            rules: {
                lockedamount: {
                    required: true
                }
            },
            messages: {
                lockedamount: {
                    required: "Please enter your charge",
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
                var form = $('#userwalletForm');
                var type = $('#userwalletForm').find('[name="type"]').val();
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
                            notify("UPI charge Updated Successfull", 'success');
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

        $( "#schemeForm").validate({
            rules: {
                scheme_id: {
                    required: true
                }
            },
            messages: {
                scheme_id: {
                    required: "Please select scheme",
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
                var form = $('#schemeForm');
                var type = $('#schemeForm').find('[name="type"]').val();
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
                            getbalance();
                            form.closest('.modal').modal('hide');
                            notify("Member Scheme Updated Successfull", 'success');
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

        $('form.idForm').submit(function() {
            var form = $(this);
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button:submit').button('loading');
                },
                complete : function(){
                    form.find('button:submit').button('reset');
                },
                success:function(data){
                    form[0].reset();
                    if(data.status == "success"){
                        notify('Stock Updated Successfully', 'success');
                    }else{
                        notify('Transaction Failed', 'warning');
                    }

                    $('#datatable').dataTable().api().ajax.reload();
                },
                error: function(errors) {
                    if(errors.status == 422){
                        $.each(errors.responseJSON, function (index, value) {
                            form.find('input[name="'+index+'"]').closest('div.form-group').append('<span class="text-danger">'+value[0]+'</span>');
                        });
                        setTimeout(function () {
                            form.find('span.text-danger').remove();
                        }, 5000);
                    }else if(errors.status == 400){
                        notify(errors.responseJSON.status, "Sorry" , 'error');
                    }else{
                        notify(errors.statusText, errors.status , 'error');
                    }
                }
            });
            return false;
        });

        $('form#permissionForm').submit(function(){
    		var form= $(this);
            $(this).ajaxSubmit({
                dataType:'json',
                beforeSubmit:function(){
                    form.find('button[type="submit"]').button('loading');
                },
                complete: function(){
                    form.find('button[type="submit"]').button('reset');
                },
                success:function(data){
                    if(data.status == "success"){
                        notify('Permission Set Successfully', 'success');
                    }else{
                        notify('Transaction Failed', 'warning');
                    }
                },
                error: function(errors) {
                	showError(errors, form);
                }
            });
            return false;
    	});

        $('#selectall').click(function(event) {
            if(this.checked) {
                $('.case').each(function() {
                   this.checked = true;       
                });
                $('.selectall').each(function() {
                    this.checked = true;       
                });
             }else{
                $('.case').each(function() {
                   this.checked = false;
                });   
                $('.selectall').each(function() {
                    this.checked = false;       
                });
            }
        });

        $('.selectall').click(function(event) {
            if(this.checked) {
                $(this).closest('tr').find('.case').each(function() {
                   this.checked = true;       
                });
             }else{
                $(this).closest('tr').find('.case').each(function() {
                   this.checked = false;
                });      
            }
        });
    });

    function transfer(id){
        $('#transferForm').find('[name="user_id"]').val(id);
        $('#transferModal').modal();
    }
    
    function redemRequest(id){
        $('#fundRequestForm').find('[name="user_id"]').val(id);
        $('#fundRequestModal').modal();
    }

    function getPermission(id) {
        if(id.length != ''){
            $.ajax({
                url: '{{url('tools/get/permission')}}/'+id,
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            })
            .done(function(data) {
                $('#permissionForm').find('[name="user_id"]').val(id);
                $('.case').each(function() {
                   this.checked = false;
                });
                $.each(data, function(index, val) {
                    $('#permissionForm').find('input[value='+val.permission_id+']').prop('checked', true);
                });
                $('#permissionModal').modal();
            })
            .fail(function() {
                notify('Somthing went wrong', 'warning');
            });
        }
    }

    function kycManage(id, kyc, remark){
        $('#kycUpdateForm').find('[name="id"]').val(id);
        $('#kycUpdateForm').find('[name="kyc"]').select2().val(kyc).trigger('change');
        $('#kycUpdateForm').find('[name="remark"]').val(remark);
        $('#kycUpdateModal').modal();
    }

    function scheme(id, scheme){
        $('#schemeForm').find('[name="id"]').val(id);
        if(scheme != '' && scheme != null && scheme != 'null'){
            $('#schemeForm').find('[name="scheme_id"]').select2().val(scheme).trigger('change');
        }
        $('#commissionModal').modal();
    } 
    
    function lockedAmount(id){
        $('#userwalletLockekModal').find('input[name="id"]').val(id);
        $('#userwalletLockekModal').modal();
    }

    function addStock(id) {
        $('#idModal').find('input[name="id"]').val(id);
        $('#idModal').modal();
    }

    @if(isset($mydata['schememanager']) && $mydata['schememanager']->value == "all")
        function viewCommission(element) {
            var scheme_id = $(element).val();
            if(scheme_id != '' && scheme_id != 0){
                $.ajax({
                    url: '{{route("getMemberPackageCommission")}}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data : {"scheme_id" : scheme_id},
                    beforeSend : function(){
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    swal.close();
                    $('#commissionModal').find('.commissioData').html(data);
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
            }
        }
    @else
        function viewCommission(element) {
            var scheme_id = $(element).val();
            if(scheme_id != '' && scheme_id != 0){
                $.ajax({
                    url: '{{route("getMemberCommission")}}',
                    type: 'post',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data : {"scheme_id" : scheme_id},
                    beforeSend : function(){
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are fetching commission details',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                })
                .success(function(data) {
                    swal.close();
                    $('#commissionModal').find('.commissioData').html(data);
                })
                .fail(function() {
                    swal.close();
                    notify('Somthing went wrong', 'warning');
                });
            }
        }
    @endif

    
</script>
@endpush