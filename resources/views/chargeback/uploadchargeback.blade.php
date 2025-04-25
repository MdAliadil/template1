@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Chargeback Service")
@section('pagetitle', "Chargeback Service")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    
        <div class="row">
            <div class="col-sm-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Chargeback Services</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('uploadchargebackupdate')}}" method="post" id="transactionForm"> 
                            {{ csrf_field() }}
                           
                            <div class="row">
                                

                                <div class="form-group col-md-12">
                                    <label>Upload Chargeback</label>
                                    <input type="file" class="form-control" name="uploadChargeback" id="uploadChargeback" autocomplete="off" >
                                </div>
                            </div>

                            
                        <div class="form-group text-center">
                            <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting">
                                <b><i class="icon-paperplane"></i></b> Submit
                            </button>
                        </div>

                            
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {

        $('.mydatepic').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd-mm-yyyy',
        });
        
      

        $('form#transactionForm').submit(function() {
            var form= $(this);
            var type = form.find('[name="type"]');
            var collectionAmount = $('#collectionAmount').val();
            var noOfTransactions=  $('#noOfTransactions').val();
            var chargeBackAmount = $('#chargeBackAmount').val();
            $(this).ajaxSubmit({
                dataType:'json',
                data: {
                collectionAmount:collectionAmount,
                noOfTransactions:noOfTransactions,
                chargeBackAmount:chargeBackAmount
            },
                beforeSubmit:function(){
                    swal({
                        title: 'Wait!',
                        text: 'We are working on request.',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                success:function(data){
                    swal.close();
                    console.log(type);
                    switch(data.statuscode){
                        case 'TXN':
                            swal({
                                title:'Suceess', 
                                text : data.message, 
                                type : 'success',
                                onClose: () => {
                                    window.location.reload();
                                }
                            });
                            break;
                        
                        default:
                            notify(data.message, 'danger');
                            break;
                    }
                },
                error: function(errors) {
                    swal.close();
                    if(errors.status == '400'){
                        notify(errors.responseJSON.message, 'danger');
                    }else{
                        swal(
                          'Oops!',
                          'Something went wrong, try again later.',
                          'error'
                        );
                    }
                }
            });
            return false;
        });
    });
    
    
</script>
@endpush