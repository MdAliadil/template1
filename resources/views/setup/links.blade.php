@extends('layouts.app')
@section('title', 'Quick Links')
@section('pagetitle',  'Quick Links')
@php
    $table = "yes";
    $agentfilter = "hide";
@endphp

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
					<h4 class="panel-title">Quick Links</h4>
					<div class="heading-elements">
                        <button type="button" class="btn btn-sm bg-slate btn-raised heading-btn legitRipple" onclick="addSetup()">
                            <i class="icon-plus2"></i> Add New
                        </button>
                    </div>
				</div>
				<div class="panel-body">
				</div>
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="setupModal" class="modal fade" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h6 class="modal-title"><span class="msg">Add</span> Links</h6>
            </div>
            <form id="setupManager" action="{{route('setupupdate')}}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <input type="hidden" name="actiontype" value="links">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Name" required="">
                    </div>

                    <div class="form-group">
                        <label>Link</label>
                        <input type="text" name="value" class="form-control" placeholder="Enter Link" required="">
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
@endsection

@push('script')
	<script type="text/javascript">
    $(document).ready(function () {
        var url = "{{url('statement/fetch')}}/setup{{$type}}/0";

        var onDraw = function() {};

        var options = [
            { "data" : "id"},
            { "data" : "name"},
            { "data" : "value"},
            { "data" : "action",
                render:function(data, type, full, meta){
                    return `<button type="button" class="btn bg-slate btn-raised legitRipple btn-xs" onclick="editSetup(`+full.id+`, \``+full.name+`\`, \``+full.value+`\`)"> Edit</button>`;
                }
            },
        ];
        datatableSetup(url, options, onDraw);

        $( "#setupManager" ).validate({
            rules: {
                name: {
                    required: true,
                },
                value: {
                    required: true,
                }
            },
            messages: {
                name: {
                    required: "Please enter value",
                },
                value: {
                    required: "Please enter value",
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
                var form = $('#setupManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            if(id == "new"){
                                form[0].reset();
                            }
                            form.find('button[type="submit"]').button('reset');
                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        }else{
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });

    	$("#setupModal").on('hidden.bs.modal', function () {
            $('#setupModal').find('.msg').text("Add");
            $('#setupModal').find('form')[0].reset();
        });
    
    });

    function addSetup(){
    	$('#setupModal').find('.msg').text("Add");
    	$('#setupModal').find('input[name="id"]').val("new");
    	$('#setupModal').modal('show');
	}

	function editSetup(id, name, value){
		$('#setupModal').find('.msg').text("Edit");
    	$('#setupModal').find('input[name="id"]').val(id);
    	$('#setupModal').find('[name="name"]').val(name);
        $('#setupModal').find('[name="value"]').val(value);
    	$('#setupModal').modal('show');
    }
</script>
@endpush