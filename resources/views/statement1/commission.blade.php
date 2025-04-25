@extends('layouts.app')
@section('title', "Commission Statement")
@section('pagetitle',  "Commission Statement")

@php
    $table = "yes";
    $export = "commissionStatement";
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Commission Statement</h4>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="150px">Refrence Details</th>
                            <th>Product/Txnid/Number/Operator</th>
                            <th width="100px">ST Type</th>
                            <th>Status</th>
                            <th width="130px">Amount</th>
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

@push('style')

@endpush

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/commissionstatement/{{$id}}";
        var onDraw = function() {
            $('[data-popup="tooltip"]').tooltip();
            $('[data-popup="popover"]').popover({
                template: '<div class="popover border-teal-400"><div class="arrow"></div><h3 class="popover-title bg-teal-400"></h3><div class="popover-content"></div></div>'
            });
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    var out = "";
                    out += `</a><span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                    return out;
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    var uid = "{{Auth::id()}}";
                    if(full.credited_by == uid){
                        var name = full.user.name.split(" ");
                        return `<a href="javascript:void(0)" data-popup="popover" title="" data-trigger="click" data-content="Mobile - `+full.user.mobile+`" data-original-title="`+full.user.role.name+`">`+name[0]+` ( `+full.user.id+` )</a>`;
                    }else{
                        var name = full.sender.name.split(" ");
                        return `<a href="javascript:void(0)" data-popup="popover" title="" data-trigger="click" data-content="Mobile - `+full.sender.mobile+`" data-original-title="`+full.sender.role.name+`">`+name[0]+` ( `+full.sender.id+` )</a>`;
                    }
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.product == "Fund Transfer" || full.product == "Fund Request" || full.product == "Fund Return"){
                        return `<b>`+full.product+`</b>/ `+full.report.id+` / Ref : `+full.report.ref_no+` / `+full.report.paydate ;
                    }else if(full.product == "Pancard"){
                        return `<b>`+full.product+`</b> `+full.report.id+` / Vle id : `+full.report.vleid+` / No of tokens : `+full.report.tokens;
                    }else if(full.product == "recharge"){
                        return `<b>`+full.product+`</b> / `+full.id+` / `+full.number+` / `+full.provider.name;
                    }else if(full.product == "billpay"){
                        return `<b>`+full.product+`</b> / `+full.id+` / `+full.number+` / `+full.provider.name;
                    }else if(full.product == "Money Transfer"){
                        return `<b>`+full.product+`</b> / `+full.report.id+` / `+full.report.beneaccount+` / `+full.report.benename;
                    }else{
                        return `<b>`+full.product+`</b>`;
                    }
                }
            },
            { "data" : "rtype"},
            { "data" : "status"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.trans_type == "credit"){
                        return `<i class="text-success icon-plus22"></i> <i class="fa fa-inr"></i> `+ (full.amount - full.profit);
                    }else if(full.trans_type == "debit"){
                        return `<i class="text-danger icon-dash"></i> <i class="fa fa-inr"></i> `+ (full.amount - full.profit);
                    }else{
                        return `<i class="fa fa-inr"></i> `+ (full.amount - full.profit);
                    }
                }
            }
        ];

        datatableSetup(url, options, onDraw , '#datatable', {columnDefs: [{
                    orderable: false,
                    width: '80px',
                    targets: [0]
                }]});
    });
</script>
@endpush