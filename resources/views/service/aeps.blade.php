@php
    $name = explode(" ", Auth::user()->name);
@endphp

@extends('layouts.app')
@section('title', "Aeps Service")
@section('pagetitle', "Aeps Service")
@php
    $table = "yes";
@endphp

@section('content')
<div class="content">
    @if(!$agent)
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Aeps Service Registration</h4>
                    </div>
                    <div class="panel-body">
                        <form action="{{route('aepskyc')}}" method="post" id="transactionForm"> 
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Firstname </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_f_name" placeholder="Enter Your Firstame" value="{{isset($name[0]) ? $name[0] : ''}}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Lastname </label>
                                    <input type="text" class="form-control" name="bc_l_name" autocomplete="off" placeholder="Enter Your Lastname" value="{{isset($name[1]) ? $name[1] : ''}}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Email </label>
                                    <input type="email" class="form-control" autocomplete="off" name="emailid" placeholder="Enter Your Email" value="{{Auth::user()->email}}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Mobile</label>
                                    <input type="text" pattern="[0-9]*" maxlength="10" minlength="10" class="form-control" name="phone1" autocomplete="off" placeholder="Enter Your Mobile" value="{{Auth::user()->mobile}}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Alternate Mobile </label>
                                    <input type="text" class="form-control" autocomplete="off" name="phone2" pattern="[0-9]*" maxlength="10" minlength="10" placeholder="Enter Your Alternate Mobile">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>DOB </label>
                                    <input type="text" class="form-control mydatepic" autocomplete="off" name="bc_dob" placeholder="Enter Your DOB (DD-MM-YYYY)" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>State</label>
                                    <select name="bc_state" class="form-control select" onchange="getDistrict(this)" required>
                                        <option value="">Select State</option>
                                        @foreach ($mahastate as $state)
                                        <option value="{{$state->stateid}}">{{$state->statename}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>District</label>
                                    <select name="bc_district" class="form-control select" required>
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Address </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_address" placeholder="Enter Your Address" value="{{Auth::user()->address}}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Block</label>
                                    <input type="text" class="form-control" name="bc_block" autocomplete="off" placeholder="Enter Your Block" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>City</label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_city"  value="{{Auth::user()->city}}" placeholder="Enter Your City" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Landmark </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_landmark" placeholder="Enter Your Landmark" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Mohalla</label>
                                    <input type="text" class="form-control" name="bc_mohhalla" autocomplete="off" placeholder="Enter Your Mohhalla" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Location</label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_loc" placeholder="Enter Your Location" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Pincode </label>
                                    <input type="text" class="form-control" autocomplete="off" name="bc_pincode" placeholder="Enter Your Pincode" pattern="[0-9]*" value="{{Auth::user()->pincode}}" maxlength="6" minlength="6" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Pancard</label>
                                    <input type="text" class="form-control" name="bc_pan" autocomplete="off" placeholder="Enter Your Pancard" value="{{Auth::user()->pancard}}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Shopname</label>
                                    <input type="text" class="form-control" autocomplete="off" name="shopname" value="{{Auth::user()->shopname}}" placeholder="Enter Your Shopname" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>shop Type </label>
                                    <input type="text" class="form-control" autocomplete="off" name="shopType" placeholder="Enter Your Shop type" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Qualification</label>
                                    <select name="qualification" class="form-control">
                                        <option value="SSC">SSC</option>
                                        <option value="HSC">HSC</option>
                                        <option value="Graduate">Graduate</option>
                                        <option value="Post Graduate">Post Graduate</option>
                                        <option value="Diploma">Diploma</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Population</label>
                                    <select name="population" class="form-control">
                                        <option value="0 to 2000">0 to 2000</option>
                                        <option value="2000 to 5000">2000 to 5000</option>
                                        <option value="5000 to 10000">5000 to 10000</option>
                                        <option value="10000 to 50000">10000 to 50000</option>
                                        <option value="50000 to 100000">50000 to 100000</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Location Type</label>
                                    <select name="locationType" class="form-control">
                                        <option value="Rural">Rural</option>
                                        <option value="Urban">Urban</option>
                                        <option value="Metro Semi Urban">Metro Semi Urban</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Submitting"><b><i class=" icon-paperplane"></i></b> Submit</button>
                            </div>
                        </form>
                    </div> 
                </div>
            </div>
        </div>
    @else
        <div class="row">
        <div class="col-sm-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Aeps</h4>
                </div>
                <div class="panel-body p-0">
                    <table class="table table-bordered">
                        <tr><td>BC ID</td><td>{{$agent->bc_id}}</td></tr>
                        <tr><td>Phone Number</td><td>{{$agent->phone1}}</td></tr>
                    </tbody></table>
                </div>
                <form action="{{route('aepsinitiate')}}" method="get" target="_blank">
                    <div class="panel-footer text-center">
                        <button type="submit" class="btn bg-teal-400 btn-labeled btn-rounded legitRipple btn-lg" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Paying"><b><i class=" icon-paperplane"></i></b> Initiate Transaction</button>
                    </div>
                </form>
                @if(isset($error))
                    <div class="panel-footer text-center text-danger">
                        Error - {{$error}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
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
            $(this).ajaxSubmit({
                dataType:'json',
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

    function getDistrict(ele){
        $.ajax({
            url:  "{{route('dmt1pay')}}",
            type: "POST",
            dataType:'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend:function(){
                swal({
                    title: 'Wait!',
                    text: 'We are fetching district.',
                    allowOutsideClick: () => !swal.isLoading(),
                    onOpen: () => {
                        swal.showLoading()
                    }
                });
            },
            data: {'type':"getdistrict", 'stateid':$(ele).val()},
            success: function(data){
                swal.close();
                var out = `<option value="">Select District</option>`;
                $.each(data.message, function(index, value){
                    out += `<option value="`+value.districtid+`">`+value.districtname+`</option>`;
                });

                $('[name="bc_district"]').html(out);
            }
        });
    }
</script>
@endpush