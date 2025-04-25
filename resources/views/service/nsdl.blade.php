@extends('layouts.app')
@section('title', "Nsdl Pancard")
@section('pagetitle', "Nsdl Pancard")

@section('content')
<form id="apply" action="{{route('pancardpay')}}" method="POST" enctype="multipart/form-data">
    {!! csrf_field() !!}
    <input type="hidden" name="actiontype" value="nsdl">
    <div class="content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Pancard</h4>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <label for="">Pan Type</label>
                        <select autocomplete="off" class="form-control select" name="type" required="">
                            <option>Select Pan Type</option>
                            <option value="new">New Pan</option>
                            <option value="correction">Correction Pan</option>
                        </select>
                    </div>

                    <div class="form-group col-md-3 newpan">
                        <label for="">Select City For AO Code</label>
                        <select autocomplete="off" class="form-control" id="aocodes">
                            <option>Select City</option>
                            @foreach ($aocodes as $element)
                              <option value="{{$element->area_code}}/{{$element->ao_type}}/{{$element->ao_code}}/{{$element->range_code}}">{{$element->city_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="display: none">
            <div class="panel-heading">
                <h4 class="panel-title">Assessing officer (AO code)</h4>
            </div>

            <div class="panel-body">
                <div class="row newpan">
                    <div class="form-group col-md-3">
                        <label for="">Area Code</label>
                        <input autocomplete="off" type="text" name="acode" class="form-control" placeholder="Enter value" required="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">AO type</label>
                        <input autocomplete="off" type="text" name="aotype" class="form-control" placeholder="Enter value" required="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Range code</label>
                        <input autocomplete="off" type="text" name="rangecode" class="form-control" placeholder="Enter value" required="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">AO No.</label>
                        <input autocomplete="off" type="text" name="aono" class="form-control" placeholder="Enter value" required="">
                    </div>
                </div>
                
                <div class="row">
                    <div class="form-group col-md-3 newpan">
                        <label for="">Status of Applicant</label>
                        <input autocomplete="off" type="text" name="statusapplicant" class="form-control" placeholder="Enter value" value="Individuals" required="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">AADHAAR number</label>
                        <input autocomplete="off" type="text" name="adhaarnumber" pattern="[0-9]*" maxlength="12" minlength="12" class="form-control" placeholder="Enter value" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Name on Adhaar</label>
                        <input autocomplete="off" type="text" name="adhaarname" class="form-control" placeholder="Enter value" required>
                    </div>
                    <div class="form-group col-md-3 correctionpan">
                        <label for="">Old Pan No.</label>
                        <input autocomplete="off" type="text" name="old_pan_no" class="form-control" placeholder="Enter value" required="">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="display: none">
            <div class="panel-heading">
                <h3 class="panel-title">Personal Imformation</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3">
                    <label for="">Title</label>
                        <select autocomplete="off" name="title" class="form-control select" required="">
                            <option value="">Select Title</option>
                            <option value="Shri">Shri</option>
                            <option value="Smt">Smt</option>
                            <option value="Kumari">Kumari</option>
                            <option value="M/s">M/s</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Lastname / Surname</label>
                        <input autocomplete="off" type="text" name="lastname" class="form-control" placeholder="Enter value" required="">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Firstname</label>
                        <input autocomplete="off" type="text" name="firstname" class="form-control" placeholder="Enter value">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Middlename</label>
                        <input autocomplete="off" type="text" name="middlename" class="form-control" placeholder="Enter value">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="">Name to be Printed on PAN card</label>
                        <input autocomplete="off" type="text" name="panname" class="form-control" placeholder="Enter value">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Gender</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-radio">
                                    <input autocomplete="off" type="radio" value="Male" id="male" name="gender" required="">
                                    <label for="male">Male</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-radio">
                                    <input autocomplete="off" type="radio" value="Female" id="female" name="gender" required="">
                                    <label for="female">Female</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="">Date Of Birth (DD/MM/YYYY)</label>
                        <input autocomplete="off" type="text" name="dob" class="form-control date" required="" placeholder="Enter value">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">                    
                        <label for="">Fathers Lastname</label>
                        <input autocomplete="off" type="text" name="flname" class="form-control" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Fathers Firstname</label>
                        <input autocomplete="off" type="text" name="ffname" class="form-control" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Fathers Middlename</label>
                        <input autocomplete="off" type="text" name="fmname" class="form-control" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label> Email ID</label>
                        <input autocomplete="off" type="email" name="email" class="form-control" required="" placeholder="Enter value">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">                    
                        <div class="row">
                            <div class="col-md-3">
                                <label for="">Std Code</label>
                                <input autocomplete="off" type="text" name="ccode" class="form-control" value="91" placeholder="Enter value">
                            </div>
                            <div class="col-md-9">
                                <label for="">Telephone / Mobile number</label>
                                <input autocomplete="off" type="text" name="mobile" class="form-control" pattern="[0-9]*" maxlength="10" minlength="10" required="" placeholder="Enter value">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>PAN Card Dispatched State</label>
                        <select autocomplete="off" name="pan_dispatch_state" class="form-control select" required="">
                            <option value="">Select State</option>
                            @foreach ($state as $element)
                                <option value="{{$element->state}}">{{$element->state}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="display: none">
            <div class="panel-heading">
                <h3 class="panel-title">Address Information</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3 form-group">                    
                        <label for="">Flat / Room / Door / Block No.</label>
                        <input autocomplete="off" type="text" name="radd1" class="form-control" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Building / Village</label>
                        <input autocomplete="off" type="text" name="radd2" class="form-control" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Road / Street / Lane/Post Office</label>
                        <input autocomplete="off" type="text" name="radd3" class="form-control" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Area / Locality</label>
                        <input autocomplete="off" type="text" name="radd4" class="form-control" required="" placeholder="Enter value">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">    
                        <label>Town / City / District</label>                
                        <input autocomplete="off" type="text" name="raddcity" class="form-control" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>State / Union Territory</label>
                        <select autocomplete="off" name="raddstate" class="form-control select" required="">
                            <option value="">Select State</option>
                            @foreach ($state as $element)
                                <option value="{{$element->state}}">{{$element->state}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Pincode / Zip code</label>
                        <input autocomplete="off" type="text" name="raddpincode" class="form-control" maxlength="6" required="" placeholder="Enter value">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Country Name</label>
                        <input autocomplete="off" type="text" name="raddcountry" class="form-control" required="" value="India" required="" placeholder="Enter value">
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default correctionpan">
            <div class="panel-heading">
                <h3 class="panel-title">Feilds To Be Change</h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="name" name="correction_value[]" value="change_name">
                            <label for="name">Name</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="dob" name="correction_value[]" value="change_dob">
                            <label for="dob">DOB</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="sex" name="correction_value[]" value="change_sex">
                            <label for="sex">Gender</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="father" name="correction_value[]" value="change_father_name">
                            <label for="father">Father Name</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="phone" name="correction_value[]" value="change_contact_detail">
                            <label for="phone">Mobile/Email-Id</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="photo" name="correction_value[]" value="change_photo">
                            <label for="photo">Photo Mismatch</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="sign" name="correction_value[]" value="change_sign">
                            <label for="sign">Sign Mismatch</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <div class="md-checkbox">
                            <input type="checkbox" id="reprint" name="correction_value[]" value="reprint_pan">
                            <label for="reprint">Reprint</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default" style="display: none">
            <div class="panel-heading">
                <h3 class="panel-title">Document</h3>
            </div>
            <div class="panel-body">
                <div class="row form-group">
                    <div class="col-md-3 correctionpan">
                        <label for="">Old PAN Proof</label>
                        <select name="pan_proof" class="form-control select" required="" placeholder="">
                            <option value="">Select Old Pan Proof</option>
                            <option value="PAN card is given as PAN proof">PAN card is given as PAN proof</option>
                            <option value="A Copy of letter issued by ITD indicating PAN allotted">A Copy of letter issued by ITD indicating PAN allotted</option>
                            <option value="There is no valid proof of PAN and the application is received on Good effort basis">There is no valid proof of PAN and the application is received on Good effort basis </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="" id="oldpanlable">Pan Form with docs (Size Upto 2MB)</label>
                        <input autocomplete="off" type="file" name="adhaarpics" class="form-control" placeholder="" required="">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <button type="submit" class="btn bg-slate-600 btn-block btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Submitting">Submit</button>
            </div>
        </div>
    </div>
</form>

<div id="nsdlagency" class="modal fade" role="dialog" data-backdrop="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Note</h4>
            </div>
            <div class="modal-body p-l-0 p-r-0">
                <ol>
                    <li>हार्ड कॉपी को 7 दिनों के अन्दर अपने distributor या फिर कंपनी ऑफिस के पते पर भिजवाए अन्यथा जो भी पेनालिटी NSDL से कंपनी को लगेगी वो आपको भुगतनी पड़ेगी |</li>
                    <li>क्रपया हस्ताक्षर को काले पेन से करे और फॉर्म को काले पेन से भरे | फोटो का Background प्लेन रखे या कोशिश करे की सफ़ेद हो |</li>
                    <li>रिजेक्शन या होल्ड पर फॉर्म आने पर रिमार्क में लिखे कारणों को समझे और फिर उसे ठीक करके Submit करे |</li>
                    <li>पेन कार्ड की दो रिसीप्ट आती है जिनपे Sign करवाके एक ग्राहक को देनी होती है एक फॉर्म के साथ लगाके कंपनी को भेजनी पड़ती है |</li>
                </ol>        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning waves-effect waves-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function() {
        $(window).load(function() {
          $('.mt input, select').trigger('blur');
          $('#nsdlagency').modal();
        });

        $('select#aocodes').select2();
        $('select#aocodes').on('change', function(event) {
            var values = $(this).val().split('/');
            $('input[name="acode"]').val(values[0]);
            $('input[name="rangecode"]').val(values[3]);
            $('input[name="aotype"]').val(values[1]);
            $('input[name="aono"]').val(values[2]);
         });

        $('[name="type"]').on('change', function(event) {
            if($(this).val() == "new"){
                $('.panel').show();
                $('.newpan').show();
                $('.correctionpan').hide();
                $('.newpan').find('input[type="text"], select').each(function(){
                    $(this).attr('required', '');
                });
                $('.correctionpan').find('input[type="text"], select').each(function(){
                    $(this).removeAttr('required');
                });
            }else{
                $('.panel').show();
                $('.correctionpan').show();
                $('.newpan').hide();
                $('.correctionpan').find('input[type="text"], select').each(function(){
                    $(this).attr('required', '');
                });
                $('.newpan').find('input[type="text"], select').each(function(){
                    $(this).removeAttr('required');
                });
            }
         });

        $('input[name="lastname"], input[name="firstname"], input[name="middlename"]').blur(function(event) {
            if($(this).val() != ''){
                var name = "";
                if($('input[name="firstname"]').val() != ""){
                    name += $('input[name="firstname"]').val().trim()+" ";
                }

                if($('input[name="middlename"]').val() != ""){
                    name += $('input[name="middlename"]').val().trim()+" ";
                }

                if($('input[name="lastname"]').val() != ""){
                    name += $('input[name="lastname"]').val().trim();
                }
                
                $('input[name="panname"]').val(name);
                $('input[name="panname"]').trigger('blur');
            }
        });

        $('.mt input, select').blur(function() {
          if ($(this).val())
            $(this).addClass('used');
          else
            $(this).removeClass('used');
        });

        $('.date').datepicker({
            'autoclose':true,
            'clearBtn':true,
            'todayHighlight':true,
            'format':'dd/mm/yyyy',
        }).on('changeDate', function(e) {
            $(this).trigger('blur');
        });

        $('form#apply').submit(function() {
            var form = $(this);
            $('span.text-danger').remove();

            swal({
                title: 'Are you sure ?',
                text: "You want to submit pancard",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes Proceed",
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !swal.isLoading(),
                preConfirm: () => {
                    return new Promise((resolve) => {
                        form.ajaxSubmit({
                            dataType:'json',
                            beforeSubmit:function(){
                                $('form#apply').find('button:submit').button('loading');
                            },
                            success:function(data){
                                swal.close();
                                $('form#apply').find('button:submit').button('reset');
                                getbalance();
                                swal({
                                    type: "success",
                                    title: "Success",
                                    text : "Transaction Successfull",
                                    onClose: () => {
                                        window.location.reload();
                                    }
                                });
                            },
                            error: function(errors) {
                                swal.close();
                                $('form#apply').find('button:submit').button('reset');
                                if(errors.status == 422){
                                    $.each(errors.responseJSON, function (index, value) {
                                        form.find('input[name="'+index+'"]').closest('div').append('<span class="text-danger">'+value[0]+'</span>');
                                    });
                                    form.find('span.text-danger').first().focus();
                                    setTimeout(function () {
                                        form.find('span.text-danger').remove();
                                    }, 5000);
                                }else if(errors.status == 400){
                                    notify(errors.responseJSON.status, errors.statusText );
                                }else{
                                    notify(errors.statusText, errors.status);
                                }
                            }
                        });
                    });
                },
            });
            
            return false;
        });
    }); 
</script>
@endpush

@push('style')
<style type="text/css">
    .form-group {
        margin-bottom: 30px;
    }
    .md-radio:before {
        content: "";
    }

    .newpan, .correctionpan{
        display: none;
    }

</style>
@endpush