<!doctype html>
<html lang="en">

<head>
    <title>Login || BlinkPe</title>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{asset('')}}assets/login/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="{{asset('')}}assets/login/media.css" rel="stylesheet" />
        <link href="{{asset('')}}assets/login/landing-page.css" rel="stylesheet" />
        
        <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/pace.min.js"></script>
	    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
	    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
        <script type="text/javascript" src="{{asset('')}}assets/js/plugins/loaders/blockui.min.js"></script>
        <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
        <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
        <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
        <script type="text/javascript" src="{{asset('')}}assets/js/plugins/forms/selects/select2.min.js"></script>
        <script src="{{asset('')}}/assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="{{asset('')}}assets/js/core/snackbar.js"></script>

    
    <style type="text/css">
        /* captchaImg */
        .error{
            color:red;
        }
        .captchaImg {
            padding: 0px;
            background-color: transparent;
            border: 0;
            outline: none;
            font-size: 24px;
            line-height: 20px;
            font-weight: bold;
            width: 100%;
            text-align: center;
        }

        .bgWhite {
            background-color: #fff;
            border-radius: 3px;
        }

        .captchaField {
            padding: 5px 10px;
            border-radius: 3px;
            outline: none;
            border: solid 1px #ccc;
            width: 100%;
        }

        .padding-10 {
            padding: 10px;
        }

        .padding-5 {
            padding: 5px;
        }
#registerModal{
    overflow-x: hidden;
    overflow-y: auto;
}
    </style>

</head>

<body class="landing-page landing-page1" ng-app="myApp" ng-cloak ng-controller="SignupSigninController as vm" ng-init="vm.epactivate()">
    <nav class="navbar navbar-transparent navbar-top" role="navigation">
        <div class="container">
            <div class="header_arrange">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
                    <a href="#" class="logo-container">
      </a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <div class="top_contact" style="margin-top: 10px;">
                        <ul>
                            <li>
                                <div class="pull-right">
                                    <i class="fa fa-phone-square" aria-hidden="true" class="pull-left" style="margin-right:10px; font-size: 18px; "></i>
                                    <span class="font_small" id="customerCare">Customer Care (Mo.-Sat, 10 to 7)</span><br>
                                </div>
                            </li>
                            <li>
                                <div class="pull-right">
                                    <i class="fa fa-envelope" aria-hidden="true" class="pull-left" style="margin-right:10px; font-size: 18px; ;"></i>
                                    <span class="font_small" id="mail">Write to us</span><br>
                                    
                                </div>
                            </li>
                            <!-- <li><a href="#" id="hi">हिंदी</a></li>
                            <li><a href="#" id="en">English</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="sliderPart">
        <div class="parallax" style="background-color: mintcream;">
            <div class="container" style="margin-top: 75px;">
                <div class="row">
                    <div class="col-md-4 col-sm-5 col-xs-12">
                        <div class="login-form">
                         
                            <div class="sign-in-htm" id="loginPart">

                                <form action="{{route('authCheck')}}" method="post" id="login_form" class="loginform" name="loginForm" autocomplete="off" novalidate>
                                    {{ csrf_field() }}
                                    <p class="lineheading" id="loginProceed" data-token="translatable.loginProceed">Login to proceed</p>
                                    <p style="color:red"><b class="errorText"></b></p>
                                    <p style="color:teal"><b class="successText"></b></p>
                                    <div class="loginPartbodr" style="background-color:#fff;">

                                        <div class="group col-md-12 col-sm-12 col-xs-12 padding-0">
                                            <label id="mobileLabel" >Agent/User Code</label>
                                            <input id="inputMobile" type="text" class="input" placeholder="Enter Mobile Number" name="mobile" tabindex="1"  >
                                        </div>
                                        <div class="group col-md-12 col-sm-12 col-xs-12 padding-0">
                                            <label id="passwordLabel" >Password</label>
                                            <input id="inputPassword" type="password" class="input" placeholder="Password" name="password" tabindex="2" maxlength="100" required  >
                                        </div>

                                        
                                        <div class="group col-md-12 col-sm-12 col-xs-12 padding-0" style="margin-bottom:10px;">
                                            <!-- <a class="button login_btn" id="button" href="#" ><span id="loginBtn" data-token="translatable.loginBtn">Login</span></a> -->
                                            <button class="btn button login_btn" id="sblf" ng-click="vm.loginSubmit($event)" type="submit" value="Sign In" data-token="translatable.sblf">Login</button>
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    
            </div>
        </div>

        <footer class="footer" style="margin-top: 55px;">
            <div class="container">
                <nav class="pull-left">
                    <ul>
                        <li><a href="#"><span id="footerLinkOne" style="color: rgb(56, 55, 55);"><b>Privacy Policy</b></span></a></li>
                        <li><a href="#"><span id="footerLinkTwo" style="color: rgb(56, 55, 55);"><b>Terms and Conditions</b></span></a></li>
                    </ul>
                </nav>
                <div class="copyright pull-right"> &copy;
                    <a href="https://easypay.in/" target="_blank" style="color: rgb(56, 55, 55);"><b>
                        <script>
                            document.write(new Date().getFullYear())
                        </script>
                        <span id="footerEPText" data-token="translatable.footerEPText">Blink Pe</span></a>
                    <span id="footerAllrightRes" data-token="translatable.footerAllrightRes" style="color: rgb(56, 55, 55); margin-left: 5px;">All Right Reserved.</span></b></div>
            </div>
        </footer>

        <!-- Modal Start -->
        <div class="modal fade" id="passOtp" role="dialog">
            <div class="modal-dialog modal-sm modal-center">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">x</button>
                        <h4 class="modal-title">OTP Verification</h4>
                    </div>
                    <div class="modal-body">
                        <p>OTP has been sent to your Register Mobile No.</p>
                        <div class="">
                            <input name="otp" type="text" class="textfield" placeholder="Enter OTP">
                        </div>
                        <div class="text-right margin-top-10">
                            <a href="#">Resend</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" data-dismiss="modal" id="otpVerify">Verify</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->
    </div>
    
        <div id="passwordResetModal" class="modal fade" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title pull-left">Password Reset Request</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="passwordRequestForm" action="{{route('authReset')}}" method="post">
                        <b><p class="text-danger"></p></b>
                        <input type="hidden" name="type" value="request">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile Number" required="">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block text-uppercase waves-effect waves-light" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting">Reset Request</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
        <div id="passwordModal" class="modal fade" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-slate">
                    <h5 class="modal-title pull-left">Password Reset</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert bg-success alert-styled-left no-margin mb-15">
                        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                        <span class="text-semibold">Success!</span> Your password reset token successfully sent on your registered e-mail id & Mobile number.
                    </div>
                    <form id="passwordForm" action="{{route('authReset')}}" method="post">
                        <b><p class="text-danger"></p></b>
                        <input type="hidden" name="mobile">
                        <input type="hidden" name="type" value="reset">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Reset Token</label>
                            <input type="text" name="token" class="form-control" placeholder="Enter OTP" required="">
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter New Password" required="">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary btn-block text-uppercase waves-effect waves-light" type="submit" data-loading-text="<i class='fa fa-spin fa-spinner'></i> Resetting">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    
        <div class="modal right fade " id="registerModal" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header" style="background-color: #709ce9;">
                    <h4 class="modal-title" id="myModalLabel2" style="color:white;">Member Registration
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:white;">&times;</span></button>
                    </h4>
                    

                </div>

                

            </div><!-- modal-content -->
        </div><!-- modal-dialog   "https://secure.payu.in/_payment" -->
    </div>
    
    
    
    <div id="otpModal" class="modal fade" role="dialog" data-backdrop="false" data-keyboard="false">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <h4 class="modal-title pull-left">Otp Verification <button type="button" class="close pull-right" data-dismiss="modal">&times;</button></h4>
                
            </div>
            
        </div>
    </div>
</div>
</body>
<script>
    var randCode = randomCode(6);
    $('#randomCode').text(randCode);
    var quality = 60; //(1 to 100) (recommanded minimum 55)
        var timeout = 10; // seconds (minimum=10(recommanded), maximum=60, unlimited=0 )
		var nooffinger = '1';
        $( document ).ready(function() {
            $('#aadharcard').keyup(function() {
            $('#registerForm').find('[name="address"]').val("");
            $("#address").prop('readonly', false);  
            $('#registerForm').find('[name="name"]').val("");
             $("#name").prop('readonly', false);
            $('#registerForm').find('[name="city"]').val("");
             $("#city").prop('readonly', false);
            $('#registerForm').find('[name="pincode"]').val("");
             $("#pincode").prop('readonly', false);
              var $aadharcard= $('#aadharcard').val();
              
            
         
        });
        
     
            $( "#otpForm" ).validate({
                rules: {
                    otp: {
                        required: true,
                        number : true
                    }
                   
                },
                messages: {
                    otp: {
                        required: "Please enter otp",
                        number: "Reset otp should be numeric",
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
                    var form = $('#otpForm');
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your details',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            swal.close();
                            if(data.status == "TXN"){
                                $('#otpModal').modal('hide');
                            
                           // $('#registerForm').find(':input[type=submit]').removeAttr('disabled');
                            $('#registerForm').find('[name="address"]').val(data.address);
                            $("#address").prop('readonly', true);  
                            $('#registerForm').find('[name="name"]').val(data.name);
                             $("#name").prop('readonly', true);
                            $('#registerForm').find('[name="city"]').val(data.city);
                             $("#city").prop('readonly', true);
                            $('#registerForm').find('[name="pincode"]').val(data.pin);
                             $("#pincode").prop('readonly', true);
                            $('#registerForm').find('[name="state"]').select2().val(data.state).trigger('change');
                             $("state").prop('readonly', true);
                           // $('#registerForm').find('[name="state"]').val();
                        
                            }else{
                               swal({
                                    type: 'warning',
                                    title: '!ERROR',
                                    text: data.message,
                                    showConfirmButton: true
                                }); 
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '400'){
                                notify(errors.responseJSON.status, 'warning');
                            }else{
                                notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });
// const constraints = {
//   width: 1920,
//   height: 1080,
//   aspectRatio: 1.777777778
// };
/*$("#videokyc").click(function(){
   var successCallback = function(error) {
  // user allowed access to camera
};
var errorCallback = function(error) {
  if (error.name == 'NotAllowedError') {
    // user denied access to camera
  }
};
navigator.mediaDevices.getUserMedia(constraints)
  .then(successCallback, errorCallback);
});*/


  




        
        
            $( ".loginform" ).validate({
                rules: {
                    mobile: {
                        required: true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter agent number"
                    },
                    password: {
                        required: "Please enter password",
                    }
                },
                errorElement: "p",
                errorPlacement: function ( error, element ) {
                    if ( element.prop("tagName").toLowerCase() === "select" ) {
                        error.insertAfter( element.closest( ".form-group" ).find(".select2") );
                    } else {
                        error.insertAfter( element );
                        $
                    }
                },
                submitHandler: function () {
                    var form = $('.loginform');
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            swal.close();
                            if(data.status == "emp"){
                                //alert(data.status);
                                Match(data.fingdata);
                               
                                
                            }else if(data.status == "Login"){
                                swal({
                                    type: 'success',
                                    title : 'Success',
                                    text: 'Successfully logged in.',
                                    showConfirmButton: false,
                                    timer: 2000,
                                    onClose: () => {
                                        window.location.reload();
                                    },
                                });
                            }else if(data.status == "otpsent" || data.status == "preotp"){
                                $('div.formdata').html(`<div class="form-group has-feedback has-feedback-left">
                                <input type="password" class="form-control" placeholder="Enter Otp" name="otp" required>
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                                <a href="javascript:void(0)" onclick="OTPRESEND()" class="text-primary pull-right">Resend Otp</a>
                                <div class="clearfix"></div>
                            </div> `);

                                if(data.status == "preotp"){
                                    $('b.successText').text('Please use previous otp sent on your mobile.');
                                    setTimeout(function(){
                                        $('b.successText').text('');
                                    }, 5000);
                                }
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '400'){
                                $('b.errorText').text(errors.responseJSON.status);
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }else{
                                $('b.errorText').text('Something went wrong, try again later.');
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }
                        }
                    });
                }
            });

            $( "#passwordForm" ).validate({
                rules: {
                    token: {
                        required: true,
                        number : true
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter reset token",
                        number: "Reset token should be numeric",
                    },
                    password: {
                        required: "Please enter password",
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
                    var form = $('#passwordForm');
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are checking your login credential',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            if(data.status == "TXN"){
                                $('#passwordModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Reset!',
                                    text: 'Password Successfully Changed',
                                    showConfirmButton: true
                                });
                            }else{
                                notify(data.message, 'warning');
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '400'){
                                notify(errors.responseJSON.status, 'warning');
                            }else if(errors.status == '422'){
                                $.each(errors.responseJSON.errors, function (index, value) {
                                form.find('[name="'+index+'"]').closest('div.form-group').append('<p class="error">'+value+'</span>');
                                });
                                form.find('p.error').first().closest('.form-group').find('input').focus();
                                setTimeout(function () {
                                form.find('p.error').remove();
                                }, 5000);
                            }else{
                                notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });
            
            
             $( "#registerForm" ).validate({
                rules: {
                    slug: {
                        required: true
                    }
                },
                messages: {
                    slug: {
                        required: "Please select member type",
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
                    var form = $('#registerForm');
                    var code = $('#randomCode').text();
                    var VoiceCode = $('#voiceCode').val(code);
                    
                    var blobLink = $('#recording').attr('src');
                    var fileName = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
                    fetch(blobLink).then(response => response.blob())
                    .then(blob => { 
                      const fd = new FormData();
                      fd.append("videoKyc", blob, `${fileName}.mp4`); // where `.ext` matches file `MIME` type  
                       fetch("/auth/KycUpload", {method:"POST", body:fd})
                    });
                    
                    var videoData = $('#videokycData').val(`${fileName}.mp4`);
                    form.ajaxSubmit({
                        dataType:'json',
                        beforeSubmit:function(){
                            swal({
                                title: 'Wait!',
                                text: 'We are working on your request',
                                onOpen: () => {
                                    swal.showLoading()
                                },
                                allowOutsideClick: () => !swal.isLoading()
                            });
                        },
                        success:function(data){
                            if(data.status == "TXN"){
                                form[0].reset();
                                $('#registerModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Welcome',
                                    text: 'Your request has been submitted successfully, please wait for confirmation',
                                    showConfirmButton: true
                                });
                            }else{
                                 swal.close();
                                $('b.errorText').text(data.message);
                                setTimeout(function(){
                                    $('b.errorText').text('');
                                }, 5000);
                            }
                        },
                        error: function(errors) {
                            swal.close();
                            if(errors.status == '422'){
                               // notify(errors.responseJSON.errors[0], 'warning');
                               $('#emailError').text(errors.responseJSON.errors.email);
                               $('#mobileError').text(errors.responseJSON.errors.mobile);
                               $('#shopnameError').text(errors.responseJSON.errors.shopname);
                               $('#pancardError').text(errors.responseJSON.errors.pancard);
                               $('#aadharcardError').text(errors.responseJSON.errors.aadharcard);
                              
                            }else if(errors.status=="400"){
                                swal({
                                    type: 'error',
                                    title: 'oops!',
                                    text: errors.responseJSON.message,
                                    showConfirmButton: true
                                });
                    
                            }else{
                                notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });
             
        $( "#verifyForm" ).validate({
            rules: {
                otp: {
                    required: true,
                     minlength: 6,
                     number : true,
                },
              
            },
            messages: {
                otp: {
                    required: "Please enter Otp",
                    number: "Otp should be numeric",
                    minlength: "Your Otp must be 6 digit",
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
                var form = $('#verifyForm');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType:'json',
                    beforeSubmit:function(){
                        form.find('button[type="submit"]').button('loading');
                    },
                    success:function(data){
                        if(data.status == "success"){
                            form.find('button[type="submit"]').button('reset');
                               swal("Good job!", "Aadhar verified Successfully!!", "success");
                          
                        }else{
                          swal("Oh noes!", "Please Try again!", "error");
                        }
                    },
                    error: function(errors) {
                   //     showError(errors, form);
                    }
                });
            }
        });    
            
        });


        function notify(msg, type="success"){
            let snackbar  = new SnackBar;
            snackbar.make("message",[
                msg,
                null,
                "bottom",
                "right",
                "text-"+type
            ], 5000);
        }

        function forgetPassword() {
            var mobile = $('.login-form').find('[name="mobile"]').val();

            if(mobile != ''){

                $.ajax({
                    url: '{{route('authReset')}}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType:'json',
                    data: {'type': 'request', "mobile": mobile},
                    beforeSend: function () {
                        swal({
                            title: 'Wait!',
                            text: 'We are processing your request',
                            onOpen: () => {
                                swal.showLoading()
                            },
                            allowOutsideClick: () => !swal.isLoading()
                        });
                    }
                }).done(function(data) {
                    swal.close();
                    if(data.status == "TXN"){
                        $('#passwordResetModal').modal('hide');
                        $('#passwordForm').find('input[name="mobile"]').val(mobile);
                        $('#passwordModal').modal('show');
                    }else{
                        $('b.errorText').text(data.message);
                        setTimeout(function(){
                            $('b.errorText').text('');
                        }, 5000);
                    }
                }).fail(function(errors) {
                    swal.close();
                    if(errors.status == '400'){
                        $('b.errorText').text(errors.responseJSON.message);
                        setTimeout(function(){
                            $('b.errorText').text('');
                        }, 5000);
                    }else{
                        $('b.errorText').text("Something went wrong, try again later.");
                        setTimeout(function(){
                            $('b.errorText').text('');
                        }, 5000);
                    }
                });

            }else{
                $('b.errorText').text('Enter your registered mobile number');
                setTimeout(function(){
                    $('b.errorText').text('');
                }, 5000);
            }
        }

        function OTPRESEND() {
            var mobile = $('input[name="mobile"]').val();
            var password = $('input[name="password"]').val();
            if(mobile.length > 0){
                $.ajax({
                    url: '{{ route("authCheck") }}',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data :  {'mobile' : mobile, 'password' : password , 'otp' : "resend"},
                    beforeSend:function(){
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are working on your request',
                            onOpen: () => {
                                swal.showLoading()
                            }
                        });
                    },
                    complete: function(){
                        swal.close();
                    }
                })
                .done(function(data) {
                    if(data.status == "otpsent"){
                        $('b.successText').text('Otp sent successfully');
                        setTimeout(function(){
                            $('b.successText').text('');
                        }, 5000);
                    }else{
                        $('b.errorText').text(data.message);
                        setTimeout(function(){
                            $('b.errorText').text('');
                        }, 5000);
                    }
                })
                .fail(function() {
                    $('b.errorText').text('Something went wrong, try again');
                    setTimeout(function(){
                        $('b.errorText').text('');
                    }, 5000);
                });
            }else{
                $('b.errorText').text('Enter your registered mobile number');
                setTimeout(function(){
                    $('b.errorText').text('');
                }, 5000);
            }
        }
        

        
        function registerOtpSend() {
            var mobile = $('#registerForm input[name="mobile"]').val();
            if(mobile.length > 0){
                $.ajax({
                    url: '{{ route("getotp") }}',
                    
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data :  {'mobile' : mobile, 'type' : "registerotp"},
                    beforeSend:function(){
                        swal({
                            title: 'Wait!',
                            text: 'Please wait, we are working on your request',
                            onOpen: () => {
                                swal.showLoading()
                            }
                        });
                    },
                    complete: function(){
                        swal.close();
                    }
                })
                .done(function(data) {
                    if(data.status=="TXN"){
                        $('p.otpText').text(data.message);
                        setTimeout(function(){
                            $('p.otpText').text('');
                        }, 5000);
                    }
                })
                .fail(function() {

                });
            }else{
                $('p.otpText').text("Please Enter Mobile number 10 digit");
                setTimeout(function(){
                    $('p.otpText').text('');
                }, 5000);
            }
        }
        
    function Match(fingdata) {
            //  swal({
            //     type: 'warning',
            //     title : 'Success',
            //     text: 'Veryfy your finget',
            //     showConfirmButton: false,
            //     timer: 2000,
                
            // });
            try {
                var isotemplate = fingdata;
                var res = MatchFinger(quality, timeout, isotemplate);

                if (res.httpStaus) {
                    if (res.data.Status) {
                        //alert("Finger matched2");
                         swal({
                            type: 'success',
                            title : 'Finger Verified',
                            text: 'Finger Verified Successfully',
                            showConfirmButton: false,
                            timer: 2000,
                            
                        });
                        var form  = $('.login-form');
                        var mobile = form.find('[name="mobile"]').val();
                        var password = form.find('[name="password"]').val();

                        $.ajax({
                            url: "{{route('authCheck')}}",
                            type: "POST",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType:'json',
                            data: {'mobile':mobile, 'password':password,'fingerVerify':"Success"},
                            beforeSend:function(){
                                swal({
                                    title: 'Wait!',
                                    text: 'We are processing your request.',
                                    allowOutsideClick: () => !swal.isLoading(),
                                    onOpen: () => {
                                        swal.showLoading()
                                    }
                                });
                            },
                            success: function(data){
                                swal.close();
                                //alert(data.status);
                                 if(data.status == "Login"){
                                    swal({
                                        type: 'success',
                                        title : 'Success',
                                        text: 'Successfully logged in.',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        
                                    });
                                    window.location.reload();
                            }else{
                                    notify(data.message, 'danger', "inline",form);
                                }
                            },
                            error: function(error){
                                swal.close();
                                notify("Something went wrong", 'danger', "inline",form);
                            }
                        });
                    }
                    else {
                        if (res.data.ErrorCode != "0") {
                            swal({
                            type: 'success',
                            title : 'Finger Verified',
                            text: res.data.ErrorDescription,
                            showConfirmButton: false,
                            timer: 2000,
                            
                        });
                           // alert(res.data.ErrorDescription);
                        }
                        else {
                             swal({
                                type: 'error',
                                title : 'Error',
                                text: "Finger not matched",
                                showConfirmButton: false,
                                timer: 2000,
                            
                        });
                            //alert("Finger not matched");
                        }
                    }
                }
                else {
                    alert(res.err);
                }
            }
            catch (e) {
                alert(e);
            }
            return false;

        } 
   

let preview = document.getElementById("preview");
let recording = document.getElementById("recording");
let startButton = document.getElementById("startButton");
// let stopButton = document.getElementById("stopButton");
let downloadButton = document.getElementById("downloadButton");
let logElement = document.getElementById("log");
$('#recording').hide();
$('#downloadButton').hide();

let recordingTimeMS = 15000;
function log(msg) {
  logElement.innerHTML += msg + "\n";
}
function wait(delayInMS) {
  return new Promise(resolve => setTimeout(resolve, delayInMS));
}
function startRecording(stream, lengthInMS) {
  let recorder = new MediaRecorder(stream);
  let data = [];
    
  recorder.ondataavailable = event => data.push(event.data);
  recorder.start();
  log(recorder.state + " for " + (lengthInMS/1000) + " seconds...");
 
  let stopped = new Promise((resolve, reject) => {
    recorder.onstop = resolve;
    recorder.onerror = event => reject(event.name);
  });

 let recorded = wait(lengthInMS).then(
    () => recorder.state == "recording" && recorder.stop()
 );
 
  return Promise.all([
    stopped,
    recorded
  ])
  .then(() => data);
}
function stop(stream) {
  stream.getTracks().forEach(track => track.stop());
}
startButton.addEventListener("click", function() {
    var adharfront=$('#adharfront').val();
    var adharback=$('#adharback').val();
    var panfront=$('#panfront').val();
    var photo=$('#photo').val();
    var slug =$('#slug').val();
    var aadharcard =$('#aadharcard').val();
    var pancard =$('#pancard').val();
    var name =$('#name').val();
    var email =$('#email').val();
    var mobile =$('#mobile').val();
    var state =$('#state').val();
    var district =$('#district').val();
    var city =$('#city').val();
    var pincode =$('#pincode').val();
    var address =$('#address').val();
    
if(adharfront.length>0 && adharback.length>0 && panfront.length>0 && photo.length>0 
    && slug.length>0 && aadharcard.length>0 && pancard.length>0 && name.length>0 && email.length>0 && mobile.length>0
    && state.length>0 && district.length>0 && city.length>0 && pincode.length>0 && address.length>0 ){
  navigator.mediaDevices.getUserMedia({
    video: true,
    audio: true
  }).then(stream => {
    preview.srcObject = stream;
    downloadButton.href = stream;
    preview.captureStream = preview.captureStream || preview.mozCaptureStream;
    return new Promise(resolve => preview.onplaying = resolve);
  }).then(() => startRecording(preview.captureStream(), recordingTimeMS))
  .then (recordedChunks => {
    let recordedBlob = new Blob(recordedChunks, { type: "video/webm" });
    console.log(recordedBlob);
    $('#preview').hide();
    $('#recording').show();
    $('#downloadButton').show();
    recording.src = URL.createObjectURL(recordedBlob);
    downloadButton.href = recording.src;
     var ekycvideo = downloadButton.download = "RecordedVideo.webm";
    downloadButton.download = "RecordedVideo.webm";
     // console.log(ekycvideo);
       $('.evideokyc').val(ekycvideo);
     // fd.append
    log("Successfully recorded " + recordedBlob.size + " bytes of " +
        recordedBlob.type + " media.");
  })
  .catch(log);
    }else{
        swal({
            type: 'warning',
            title: '!ERROR',
            text: 'Please fill all fields',
            showConfirmButton: true
        });
    }
}, false);
//stopButton.addEventListener("click", function() {
//   stop(preview.srcObject);
// }, false);

function blobToFile(theBlob, fileName){
    //A Blob() is almost a File() - it's just missing the two properties below which we will add
    theBlob.lastModifiedDate = new Date();
    theBlob.name = fileName;
    return theBlob;
}
function randomCode(len) {
  let result = Math.floor(Math.random() * Math.pow(10, len));

  return (result.toString().length < len) ? random(len) : result;
}
</script>

</html>