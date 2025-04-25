
@extends('layouts.app')
@section('title', 'NSDL Agency Report')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">{{$pancard->firstname}} Pancard Details</h3>
            </div>

            <div class="panel-body">
                
                <legend>Assessing officer (AO code)</legend>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Copy</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th>Area Code</th>
                            <td><p class="acode">{{$pancard->acode}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>AO type</th>
                            <td><p class="aotype">{{$pancard->aotype}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Range Code</th>
                            <td><p class="rangecode">{{$pancard->rangecode}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Ao No.</th>
                            <td><p class="aono">{{$pancard->aono}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Status of Applicant</th>
                            <td><p class="statusapplicant">{{$pancard->statusapplicant}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Aadhar number</th>
                            <td><p class="adhaarnumber">{{$pancard->adhaarnumber}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Name on Adhaar</th>
                            <td><p class="adhaarname">{{$pancard->adhaarname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>
                    </tbody>
                </table>

                <legend>Personal Information</legend>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Copy</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th>Title</th>
                            <td><p class="acode">{{$pancard->title}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Firstname</th>
                            <td><p class="aotype">{{$pancard->firstname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Middlename</th>
                            <td><p class="rangecode">{{$pancard->middlename}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Lastname</th>
                            <td><p class="aono">{{$pancard->lastname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Name on Pancard</th>
                            <td><p class="statusapplicant">{{$pancard->panname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Gender</th>
                            <td><p class="adhaarnumber">{{$pancard->gender}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Date Of Birth</th>
                            <td><p class="adhaarname">{{$pancard->dob}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Father Firstname</th>
                            <td><p class="adhaarname">{{$pancard->ffname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Father Middlename</th>
                            <td><p class="adhaarname">{{$pancard->fmname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Father Lastname</th>
                            <td><p class="adhaarname">{{$pancard->flname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><p class="adhaarname">{{$pancard->email}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Mobile</th>
                            <td><p class="adhaarname">{{$pancard->mobile}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr class="{{($pancard->type == "new") ? 'hide' : ''}}">
                            <th>PAN Card Dispatched State</th>
                            <td><p class="adhaarname">{{$pancard->pan_dispatch_state}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr class="{{($pancard->type == "new") ? 'hide' : ''}}">
                            <th>Old Pan Number</th>
                            <td><p class="adhaarname">{{$pancard->old_pan_no}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr class="{{($pancard->type == "new") ? 'hide' : ''}}">
                            <th>Correction Value</th>
                            <td><p class="adhaarname">{{$pancard->correction_value}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>
                    </tbody>
                </table>

                <legend>Address Information</legend>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Copy</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th>Care Of Title</th>
                            <td><p class="acode">{{$pancard->careoftitle}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Care Of Name</th>
                            <td><p class="acode">{{$pancard->careofname}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Flat / Room / Door / Block No.</th>
                            <td><p class="acode">{{$pancard->radd1}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Building / Village</th>
                            <td><p class="acode">{{$pancard->radd2}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Road / Street / Lane/Post Office</th>
                            <td><p class="acode">{{$pancard->radd3}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Area / Locality</th>
                            <td><p class="acode">{{$pancard->radd4}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                        <tr>
                            <th>Town / City / District</th>
                            <td><p class="acode">{{$pancard->raddcity}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                         <tr>
                            <th>State / Union Territory</th>
                            <td><p class="acode">{{$pancard->raddstate}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                         <tr>
                            <th>Pincode / Zip code</th>
                            <td><p class="acode">{{$pancard->raddpincode}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>

                         <tr>
                            <th>Country Name</th>
                            <td><p class="acode">{{$pancard->raddcountry}}</p></td>
                            <td><button class="btn btn-xs btn-primary copyBtn"><i class="fa fa-copy"></i> Copy</button></td>
                        </tr>
                    </tbody>
                </table>

                <legend>Document Information</legend>
                <div class="row  {{($pancard->type == "new") ? '' : 'hide'}}">
                    <div class="col-md-4">
                        <h5>Pan Form Doucment</h5>
                        <a class="adhaarpic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->adhaarpic}}" download>Download</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Photo</h5>
                        <a class="photopic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->photopic}}" download>Download</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Signature</h5>
                        <a class="signaturepic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->signaturepic}}" download>Download</a>
                    </div>
                </div>
                <hr>
                <div class="row {{($pancard->type != "new") ? '' : 'hide'}}">
                    <div class="col-md-8">
                        <h5>Old Pancard Proof</h5>
                        <p class="pan_proof">{{$pancard->pan_proof}}</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Pan Form Doucment</h5>
                        <a class="adhaarpic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->adhaarpic}}" download>Download</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Photo</h5>
                        <a class="photopic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->photopic}}" download>Download</a>
                    </div>
                    <div class="col-md-4">
                        <h5>Signature</h5>
                        <a class="signaturepic" target="_blank" href="{{asset("public/nsdlpanforms")}}/'+{{$pancard->signaturepic}}" download>Download</a>
                    </div>
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
        var USERSYSTEM, STOCK={}, DT=!1, STATUSURL='{{route('reportUpdate')}}';;

        $(document).ready(function () {
            USERSYSTEM = {
                DEFAULT: function () {
                    $('.copyBtn').click(function(event) {
                        USERSYSTEM.COPYFUNCTION(this);
                    });
                },

                COPYFUNCTION: function (ele) {
                    var aux = document.createElement("input");
                    aux.setAttribute("value", $(ele).closest('tr').find('p').text());
                    document.body.appendChild(aux);
                    aux.select();
                    document.execCommand("copy");
                    document.body.removeChild(aux);
                }
            }

            USERSYSTEM.DEFAULT();
        });
    </script>
@endpush