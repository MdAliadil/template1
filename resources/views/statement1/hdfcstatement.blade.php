@extends('layouts.app')
@section('title', "Exp Upi Account Statement")
@section('pagetitle',  "Exp Upi Account Statement")

@php
    $table = "yes";
    $agentfilter ="hide";
    
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Exp-UPI Account Statement</h4>
                </div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th width="150px">Refrence Details</th>
                            <th>Product</th>
                            <th>Provider</th>
                            <th>Txnid</th>
                            <th>Number</th>
                            <th width="100px">ST Type</th>
                            <th>Status</th>
                            <th width="130px">Opening Bal.</th>
                            <th >Amount.</th>
                            <th >Charge</th>
                            <th >GST</th>
                            <th >Total CH.</th>
                            <th width="130px">Closing Bal.</th>
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
        var url = "{{url('statement/fetch')}}/idfcaccountstatement/{{$id}}";
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
            { "data" : "full.username",
                render:function(data, type, full, meta){
                    var uid = "{{Auth::id()}}";
                    if(full.credited_by == uid){
                        var name = full.username;
                    }else{
                        var name = full.sendername;
                    }
                    return name;
                }
            },
            { "data" : "product"},
            { "data" : "providername"},
            { "data" : "id"},
            { "data" : "number"},
            { "data" : "rtype"},
            { "data" : "status"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `<i class="fa fa-inr"></i> `+full.balance;
                }
            },
            { "data" : "amount"},
            { "data" : "charge"},
            { "data" : "gst"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                   return (parseFloat(full.charge) + parseFloat(full.gst) )
                }
            },
           
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.status == "pending" || full.status == "success" || full.status == "reversed"||full.status == "refunded" ||full.status == "failed"){
                        if(full.trans_type == "credit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance)  - (parseFloat(full.charge)+parseFloat(full.gst)));
                        }else if(full.trans_type == "debit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) + parseFloat(full.charge)));
                        }else if(full.trans_type == "none"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) - parseFloat(full.charge)));
                        }
                    }else{
                        return `<i class="fa fa-inr"></i> `+full.balance;
                    }
                }
            },
        ];

        datatableSetup(url, options, onDraw , '#datatable', {columnDefs: [{
                    orderable: false,
                    width: '80px',
                    targets: [0]
                }]});
    });
</script>
@endpush