@extends('layouts.app')
@section('title', 'NSDL Agency Report')

@section('content')
@if($pancard->status == "completed")
    <input type="hidden" name="ref_no" id="MainContentPlace_hdnAcknowledgementNumber" value="{{$pancard->ref_no}}" />
@endif
<td id="MainContentPlace_LblSTD" style="display: none"><b></b></td>
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{$pancard->firstname}} Pancard Details</h3>
                </div>

                <div class="panel-body p-0">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Applicant Category</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>
                            <tr>
                                <td>1.</td>
                                <td>Category</td>
                                <td id="MainContentPlace_LblCategory"><b>{{$pancard->statusapplicant}}</b></td>
                                <td id="MainContentPlace_BtnCategory"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->statusapplicant}}'><i class='fa fa-copy'></i></span></td>

                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Applicant Name</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Applicant Title</td>
                                <td id="MainContentPlace_LblApplicantTitle"><b>{{$pancard->title}}</b></td>
                                <td id="MainContentPlace_BtnApplicantTitle"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->title}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>Last Name</td>
                                <td id="MainContentPlace_LblLastName"><b>{{$pancard->lastname}}</b></td>
                                <td id="MainContentPlace_BtnLastName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->lastname}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>3.</td>
                                <td>First Name</td>
                                <td id="MainContentPlace_LblFirstName"><b>{{$pancard->firstname}}</b></td>
                                <td id="MainContentPlace_BtnFirstName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->firstname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                            <tr>
                                <td>4.</td>
                                <td>Middle Name</td>
                                <td id="MainContentPlace_LblMiddleName"><b>{{$pancard->middlename}}</b></td>
                                <td id="MainContentPlace_BtnMiddleName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->middlename}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                        </tbody>

                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Name On Card</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Card Name</td>
                                <td id="MainContentPlace_LblCardName"><b>{{$pancard->panname}}</b></td>

                                <td id="MainContentPlace_BtnCardName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->panname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                        </tbody>


                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Father Name</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Father Last Name</td>
                                <td id="MainContentPlace_LblFLName"><b>{{$pancard->flname}}</b></td>

                                <td id="MainContentPlace_BtnFLName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->flname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>Father First Name</td>
                                <td id="MainContentPlace_LblFFName"><b>{{$pancard->ffname}}</b></td>
                                <td id="MainContentPlace_BtnFFName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->ffname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                            <tr>
                                <td>3.</td>
                                <td>Father Middle Name</td>
                                <td id="MainContentPlace_LblFMName"><b>{{$pancard->fmname}}</b></td>
                                <td id="MainContentPlace_BtnFMName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->fmname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                        </tbody>


                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">DOB Info.</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>
                            @if($pancard->status != "completed")
                                <tr>
                                    <td>2.</td>
                                    <td>Date of Birth (DD/MM/YYYY)</td>
                                    <td id="MainContentPlace_LblDOB"><b>{{$pancard->dob}}</b></td>
                                    <td id="MainContentPlace_BtnDOB"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->dob}}'><i class='fa fa-copy'></i></span></td>
                                </tr>
                            @else
                                <tr>
                                    <td>2.</td>
                                    <td>Date of Birth (DD/MM/YYYY)</td>
                                    <td id="MainContentPlace_LblAadharDate"><b>{{$pancard->dob}}</b></td>
                                    <td id="MainContentPlace_BtnAadharDate"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->dob}}'><i class='fa fa-copy'></i></span></td>
                                </tr>
                            @endif

                        </tbody>

                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Contact Info.</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>ISD Code</td>
                                <td id="MainContentPlace_LblISD"><b>+91</b></td>
                                <td id="MainContentPlace_BtnISD"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='+91'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr style="display: none;">
                                <td>2.</td>
                                <td>STD Code</td>
                                <td id="MainContentPlace_LblSTD"><b></b></td>
                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>Mobile No.</td>
                                <td id="MainContentPlace_LblMobile"><b>{{$pancard->mobile}}</b></td>
                                <td id="MainContentPlace_BtnMobile"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->mobile}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>3.</td>
                                <td>Email</td>
                                <td id="MainContentPlace_LblEmail"><b>{{$pancard->email}}</b></td>
                                <td id="MainContentPlace_BtnEmail"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->email}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr class="{{($pancard->type == "new") ? 'hide' : ''}}">
                                <td>4.</td>
                                <td>Old Pan Number</td>
                                <td id="MainContentPlace_LblPANNo"><b>{{$pancard->old_pan_no}}</b></td>
                                <td id="MainContentPlace_BtnPANNo"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->old_pan_no}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr class="{{($pancard->type == "new") ? 'hide' : ''}}">
                                <td>5.</td>
                                <td>Correction Value</td>
                                <td><b>{{$pancard->correction_value}}</b></td>
                                <td><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->correction_value}}'><i class='fa fa-copy'></i></span></td>
                            </tr>
                        </tbody>

                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Aadhar Card Info.</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Aadhar Card Name</td>
                                <td id="MainContentPlace_LblAadharName"><b>{{$pancard->adhaarname}}</b></td>
                                <td id="MainContentPlace_BtnAadharName"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->adhaarname}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>Aadhar Card No</td>
                                <td id="MainContentPlace_LblAadharNo"><b>{{$pancard->adhaarnumber}}</b></td>
                                <td id="MainContentPlace_BtnAadharNo"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->adhaarnumber}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            @if($pancard->status == "completed")
                                <tr>
                                <td>3.</td>
                                <td>Name of Varifier</td>
                                <td id="MainContentPlace_LblNameOfVarifier" class="text-bold"></td>
                                <td id="MainContentPlace_BtnNameOfVarifier"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text=''><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>4.</td>
                                <td>Place of Varification</td>
                                <td id="MainContentPlace_LblPlaceofVafi" class="text-bold">{{$pancard->place}}</td>
                                <td id="MainContentPlace_BtnPlaceofVafi"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->place}}'><i class='fa fa-copy'></i></span></td>
                            </tr>
                            @endif
                        </tbody>
                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Dispatched Info.</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Dispatched State</td>
                                <td id="MainContentPlace_LblDispatchedState"><b>{{$pancard->pan_dispatch_state}}</b></td>
                                <td id="MainContentPlace_BtnDispatchedState"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->pan_dispatch_state}}'><i class='fa fa-copy'></i></span></td>

                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>Created Date</td>
                                <td id="MainContentPlace_LblCreatedDate"><b>{{$pancard->created_at}}</b></td>
                                <td id="MainContentPlace_BtnCreatedDate"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->created_at}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>3.</td>
                                <td>Modified Date</td>
                                <td id="MainContentPlace_LblModifiedDate"><b>{{$pancard->update_at}}</b></td>
                                <td id="MainContentPlace_BtnModifiedDate"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->update_at}}'><i class='fa fa-copy'></i></span></td>

                            </tr>
                        </tbody>

                        <tbody id="MainContentPlace_PnlAreaCode">
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Area Code</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>

                            <tr>
                                <td>1.</td>
                                <td>Area Code.</td>
                                <td id="MainContentPlace_LblAreaCode"><b>{{$pancard->acode}}</b></td>
                                <td id="MainContentPlace_BtnAreaCode"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->acode}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>2.</td>
                                <td>AO Type.</td>
                                <td id="MainContentPlace_LblAOType"><b>{{$pancard->aotype}}</b></td>
                                <td id="MainContentPlace_BtnAOType"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->aotype}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>3.</td>
                                <td>Range Code.</td>
                                <td id="MainContentPlace_LblRangeCode"><b>{{$pancard->rangecode}}</b></td>
                                <td id="MainContentPlace_BtnRangeCode"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->rangecode}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                            <tr>
                                <td>4.</td>
                                <td>AO No.</td>
                                <td id="MainContentPlace_LblAONo"><b>{{$pancard->aono}}</b></td>
                                <td id="MainContentPlace_BtnAONo"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->aono}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                        </tbody>

                        <tbody>
                            <tr>
                                <th style="width: 30px">#</th>
                                <th style="width: 250px">Gender Info</th>
                                <th style="width: 230px;">Value</th>
                                <th>Copy</th>
                            </tr>
                            @if($pancard->status != "completed")
                                <tr>
                                    <td>1.</td>
                                    <td>Gender</td>
                                    <td id="MainContentPlace_LblGender"><b>{{$pancard->gender}}</b></td>
                                    <td id="MainContentPlace_BtnGender"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->gender}}'><i class='fa fa-copy'></i></span></td>
                                </tr>
                            @else
                                <tr>
                                    <td>1.</td>
                                    <td>Gender</td>
                                    <td id="MainContentPlace_LblAadharGender"><b>{{$pancard->gender}}</b></td>
                                    <td id="MainContentPlace_BtnAadharGender"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->update_at}}'><i class='fa fa-copy'></i></span></td>
                                </tr>
                            @endif
                        </tbody>

                         <tbody>
                                <tr>
                                    <th style="background-color: #669999; height: 37px; color: #FFFFFF; width: 10px;">#</th>
                                    <th style="background-color: #669999; height: 37px; color: #FFFFFF; width: 200px;">Addreaa Detail's <span id="MainContentPlace_LblAddressFlag" class="label label-warning pull-right">RESIDENCE</span></th>
                                    <th style="background-color: #669999; height: 37px; color: #FFFFFF; width: 200px;">Value</th>
                                    <th style="background-color: #669999; height: 37px; color: #FFFFFF;">Copy</th>
                                </tr>

                                <tr>
                                    <td>1.</td>
                                    <td>Flat/Door/Block No</td>
                                    <td id="MainContentPlace_LblFlat" class="text-bold">{{$pancard->radd1}}</td>
                                    <td id="MainContentPlace_BtnFlat"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->radd1}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>2.</td>
                                    <td>Premises/Building/Village</td>
                                    <td id="MainContentPlace_LblPremises" class="text-bold">{{$pancard->radd2}}</td>
                                    <td id="MainContentPlace_BtnPremises"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->radd2}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>3.</td>
                                    <td>Road/Street/Lane/Post</td>
                                    <td id="MainContentPlace_LblRoad" class="text-bold">{{$pancard->radd3}}</td>
                                    <td id="MainContentPlace_BtnRoad"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->radd3}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>4.</td>
                                    <td>Area/Taluka/Sub-Division</td>
                                    <td id="MainContentPlace_LblArea" class="text-bold">{{$pancard->radd4}}</td>
                                    <td id="MainContentPlace_BtnArea"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->radd4}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>5.</td>
                                    <td>Select State/Union Terriory</td>
                                    <td id="MainContentPlace_LblState" class="text-bold">{{$pancard->raddstate}}</td>
                                    <td id="MainContentPlace_BtnState"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->raddstate}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>6.</td>
                                    <td>Town/District</td>
                                    <td id="MainContentPlace_LblDistrict" class="text-bold">{{$pancard->raddcity}}</td>
                                    <td id="MainContentPlace_BtnDistrict"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->raddcity}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>7.</td>
                                    <td>Pin/Zip Code</td>
                                    <td id="MainContentPlace_LblZip" class="text-bold">{{$pancard->raddpincode}}</td>
                                    <td id="MainContentPlace_BtnZip"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->raddpincode}}'><i class='fa fa-copy'></i></span></td>
                                </tr>


                            </tbody>

                            <tbody style="display: none">
                                <tr>
                                    <th style="width: 30px">#</th>
                                    <th style="width: 250px">Proof Details</th>
                                    <th style="width: 230px;">Value</th>
                                    <th>Copy</th>
                                </tr>
                                <tr>
                                    <td>1.</td>
                                    <td>Identity Proof</td>
                                    <td id="MainContentPlace_LblProofofIdentity"><b>{{$pancard->id_proof}}</b></td>
                                    <td id="MainContentPlace_BtnProofofIdentity"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->id_proof}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>2.</td>
                                    <td>Address Proof</td>
                                    <td id="MainContentPlace_LblProofofAddress"><b>{{$pancard->address_proof}}</b></td>
                                    <td id="MainContentPlace_BtnProofofAddress"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->address_proof}}'><i class='fa fa-copy'></i></span></td>
                                </tr>

                                <tr>
                                    <td>3.</td>
                                    <td>Dob Proof</td>
                                    <td id="MainContentPlace_LblProofofBirth"><b>{{$pancard->dob_proof}}</b></td>
                                    <td id="MainContentPlace_BtnProofofBirth"><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->dob_proof}}'><i class='fa fa-copy'></i></span></td>
                                </tr>
                            </tbody>
                        <tbody>
                            <tr>
                                <th colspan="4" style="background-color: #669999; height: 37px; color: #FFFFFF; width: 200px;">Documents</th>
                            </tr>
                            <tr class="{{($pancard->type != "new") ? '' : 'hide'}}">
                                <td>1.</td>
                                <td>Old Pan Proof</td>
                                <td id="" class="text-bold">{{$pancard->pan_proof}}</td>
                                <td id=""><span onclick="USERSYSTEM.COPYFUNCTION(this)" class='btn btn-primary btn-xs' data-clipboard-text='{{$pancard->pan_proof}}'><i class='fa fa-copy'></i></span></td>
                            </tr>

                                <tr >
                                    <td>1.</td>
                                    <td>Photo</td>
                                    <td class="text-bold">
                                        <a class="photopic" target="_blank" href="{{asset("public/nsdlpanforms")}}/{{$pancard->photopic}}" download>Download</a></td>
                                    <td></td>
                                </tr>

                               <tr>
                                    <td>2.</td>
                                    <td>Signature</td>
                                    <td class="text-bold">
                                        <a class="signaturepic" target="_blank" href="{{asset("public/nsdlpanforms")}}/{{$pancard->signaturepic}}" download>Download</a></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td>3.</td>
                                    <td>Documment File</td>
                                    <td class="text-bold">
                                        <a class="adhaarpic" target="_blank" href="{{asset("public/nsdlpanforms")}}/{{$pancard->adhaarpic}}" download>Download</a></td>
                                    <td></td>
                                </tr>
                            </tbody>
                    </table>
                </div>  
            </div>
        </div>
    </div>
</div>

@endsection

@push('style')
    <style type="text/css">
        .col-md-3, .col-md-4{
            border-right: 1px solid #cccc;
        }
    </style>
@endpush

@push('script')
    <script type="text/javascript">
        var USERSYSTEM, STOCK={}, DT=!1;

        $(document).ready(function () {
            USERSYSTEM = {
                DEFAULT: function () {
                    USERSYSTEM.BEFORESUBMIT();
                    $('.copyBtn').click(function(event) {
                        USERSYSTEM.COPYFUNCTION(this);
                    });
                },

                COPYFUNCTION: function (ele) {
                    var aux = document.createElement("input");
                    aux.setAttribute("value", $(ele).attr('data-clipboard-text'));
                    document.body.appendChild(aux);
                    aux.select();
                    document.execCommand("copy");
                    document.body.removeChild(aux);
                },

                STATUSCHANGE: function (ele) {
                    var value = $(ele).val();
                    if(value == "completed"){
                        $('[name="receipts"]').closest('.form-group').show();
                        $('[name="reason"]').closest('.form-group').hide();
                    }else if(value == "rejected"){
                        $('[name="receipts"]').closest('.form-group').hide();
                        $('[name="reason"]').closest('.form-group').show();
                    }
                },
            }

            USERSYSTEM.DEFAULT();
        });
    </script>
@endpush