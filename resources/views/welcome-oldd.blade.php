 
<!DOCTYPE html>
<html lang="en"><head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login To - {{$mydata['company']->companyname}}</title>
    <!-- Page Icons -->
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Page Title -->
    <link href="{{asset('')}}assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
   <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
   
    
    {{-- <link href="{{asset('')}}assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="{{asset('')}}assets/css/colors.css" rel="stylesheet" type="text/css"> --}}
    <link href="{{asset('')}}assets/css/snackbar.css" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@300;400;600;700&amp;display=swap">
    {{-- <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/bootstrap.min.css"> --}}
    {{-- <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/all.css"> --}}
    <link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/style.min.css">

    
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.14.0/css/all.css">
<!-- 
<link rel="stylesheet" href="css/fontawsome.css">
<link rel="stylesheet" href="css/fontawsome.min.css"> -->


<link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/style.min.css">
<link rel="stylesheet" href="{{asset('')}}assets/newlogin/css/login.min.css">
    
    <style type="text/css">
    .modal-dialog {
    margin-top: 100px;
}
.error {
    color:red
        }
input.form-control {
    font-size: 19px;
}        
    </style>

<style type="text/css">@keyframes tawkMaxOpen{0%{opacity:0;transform:translate(0, 30px);;}to{opacity:1;transform:translate(0, 0px);}}@-moz-keyframes tawkMaxOpen{0%{opacity:0;transform:translate(0, 30px);;}to{opacity:1;transform:translate(0, 0px);}}@-webkit-keyframes tawkMaxOpen{0%{opacity:0;transform:translate(0, 30px);;}to{opacity:1;transform:translate(0, 0px);}}#NsumiMp-1608839283413{outline:none!important;visibility:visible!important;resize:none!important;box-shadow:none!important;overflow:visible!important;background:none!important;opacity:1!important;filter:alpha(opacity=100)!important;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity1)!important;-moz-opacity:1!important;-khtml-opacity:1!important;top:auto!important;right:10px!important;bottom:90px!important;left:auto!important;position:fixed!important;border:0!important;min-height:0!important;min-width:0!important;max-height:none!important;max-width:none!important;padding:0!important;margin:0!important;-moz-transition-property:none!important;-webkit-transition-property:none!important;-o-transition-property:none!important;transition-property:none!important;transform:none!important;-webkit-transform:none!important;-ms-transform:none!important;width:auto!important;height:auto!important;display:none!important;z-index:2000000000!important;background-color:transparent!important;cursor:auto!important;float:none!important;border-radius:unset!important;pointer-events:auto!important}#rEV5Vmf-1608839283416.open{animation : tawkMaxOpen .25s ease!important;}</style>
<!-- Core JS files -->
   <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
    <!--<script type="text/javascript" src="{{asset('')}}assets/js/core/app.js"></script>-->
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
    <script>
        $( document ).ready(function() {

            $( ".login-form" ).validate({
                rules: {
                    mobile: {
                        required: true,
                        minlength: 10,
                        number : true,
                        maxlength: 11
                    },
                    password: {
                        required: true,
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter mobile number",
                        number: "Mobile number should be numeric",
                        minlength: "Your mobile number must be 10 digit",
                        maxlength: "Your mobile number must be 10 digit"
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
                    var form = $('.login-form');
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
                            if(data.status == "Login"){
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
                                $('div.formdata').append(`<div class="form-group has-feedback has-feedback-left">
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
                            }else{
                                notify('Something went wrong, try again later.', 'warning');
                            }
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
    </script>
</head>
<body class="shape-bg" style="padding-right: 0px;" data-new-gr-c-s-check-loaded="14.990.0" data-gr-ext-installed="">
    <header class="header">
        <div class="header__logo">
             <a href="index.php" target="_blank">
                <img src="{{asset('')}}assets/newlogin/img/logo_png.png" style="width:100px" alt="Company Logo">
            </a>
        </div>

        <div class="header__menu">
            <a href="javascript:;">Recharge</a>
            <span class="divider">|</span>
            <a href="javascripe:;">Bill Payment</a>
            <span class="divider">|</span>
            <a href="javascript:;">DMT</a>
        </div>
    </header>

    <section id="signInForm" class="formPage">
        <div class="formPage__card animated appeared fadeInUp visible" data-animation="appeared fadeInUp" data-animation-delay="200">
            <h2 class="title">Sign In</h2>
            <form form  action="{{route('authCheck')}}" method="POST" class="login-form">
                {{ csrf_field() }}
                <p style="color:red"><b class="errorText"></b></p>
                <p style="color:teal"><b class="successText"></b></p>
                <div class="form-group">
                    <label for="" class="label">Mobile Number</label>
                    <input type="text" class="form-control" name="mobile" placeholder="User name" pattern="[0-9]*" maxlength="11" minlength="10" required>
                </div>
                <div class="form-group">
                    <label for="" class="label">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>

                    <a href="javascript:void(0)" onclick="forgetPassword()">Forgot password?</a>
                </div>
                <div class="form-group margin-bottom-20 padding-top-30">
                    <button type="submit" class="btn btn-primary btn-block">Sign in <i class="icon-circle-right2 position-right"></i></button>
                </div>
            </form>

            <div class="backBlock">
                <a href="http://sahajmoney.org/" class="backBlock__button">
                    <i class="fas fa-long-arrow-alt-left"></i>
                    <span>Back to Home</span>
                </a>
            </div>
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
    </section>

    

    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__copyright">
                    © Copyright 2021  All right Reserved.
                </div>
                <div class="footer__links">
                    <a href="javascript:;">Recharge</a>
                    <a href="javascripe:;">Bill Payment</a>
                    <a href="javascript:;">DMT</a>
                </div>
            </div>
        </div>
    </footer>

    <!--<div class="siteLoaderWrap" style="display: none;">-->
    <!--    <div class="siteLoaderWrap__container">-->
    <!--        <div class="spinner1"></div>-->
    <!--        <div class="spinner2"></div>-->
    <!--        <div class="spinner3"></div>-->
    <!--        <div class="spinner4"></div>-->
    <!--        <div class="spinner5"></div>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Javascripts -->
    
    
    

 
   
        <div id="modal" class="modal fade" style="z-index:99999999 !important;" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="modalLabel" aria-hidden="true"></div>

<div style="background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.2) 2px 2px 3px; position: absolute; transition: visibility 0s linear 0.3s, opacity 0.3s linear 0s; opacity: 0; visibility: hidden; z-index: 2000000000; left: 0px; top: -10000px;"><div style="width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; z-index: 2000000000; background-color: rgb(255, 255, 255); opacity: 0.05;"></div><div class="g-recaptcha-bubble-arrow" style="border: 11px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -11px; z-index: 2000000000;"></div><div class="g-recaptcha-bubble-arrow" style="border: 10px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -10px; z-index: 2000000000;"></div><div style="z-index: 2000000000; position: relative;"><iframe title="recaptcha challenge" src="https://www.google.com/recaptcha/api2/bframe?hl=en&amp;v=qc5B-qjP0QEimFYUxcpWJy5B&amp;k=6Lf5O7cZAAAAAI_COZLYFzjFrZMFfFtPk2yfcgRZ&amp;cb=lrkeb1ov6i49" name="c-56fdvt5huc7h" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox" style="width: 100%; height: 100%;"></iframe></div></div><div style="background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.2) 2px 2px 3px; position: absolute; transition: visibility 0s linear 0.3s, opacity 0.3s linear 0s; opacity: 0; visibility: hidden; z-index: 2000000000; left: 0px; top: -10000px;"><div style="width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; z-index: 2000000000; background-color: rgb(255, 255, 255); opacity: 0.05;"></div><div class="g-recaptcha-bubble-arrow" style="border: 11px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -11px; z-index: 2000000000;"></div><div class="g-recaptcha-bubble-arrow" style="border: 10px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -10px; z-index: 2000000000;"></div><div style="z-index: 2000000000; position: relative;"><iframe title="recaptcha challenge" src="https://www.google.com/recaptcha/api2/bframe?hl=en&amp;v=qc5B-qjP0QEimFYUxcpWJy5B&amp;k=6Lf5O7cZAAAAAI_COZLYFzjFrZMFfFtPk2yfcgRZ&amp;cb=b4s0ya8ivpfx" name="c-ec2q3i4poc7q" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox" style="width: 100%; height: 100%;"></iframe></div></div><div style="background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204); box-shadow: rgba(0, 0, 0, 0.2) 2px 2px 3px; position: absolute; transition: visibility 0s linear 0.3s, opacity 0.3s linear 0s; opacity: 0; visibility: hidden; z-index: 2000000000; left: 0px; top: -10000px;"><div style="width: 100%; height: 100%; position: fixed; top: 0px; left: 0px; z-index: 2000000000; background-color: rgb(255, 255, 255); opacity: 0.05;"></div><div class="g-recaptcha-bubble-arrow" style="border: 11px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -11px; z-index: 2000000000;"></div><div class="g-recaptcha-bubble-arrow" style="border: 10px solid transparent; width: 0px; height: 0px; position: absolute; pointer-events: none; margin-top: -10px; z-index: 2000000000;"></div><div style="z-index: 2000000000; position: relative;"><iframe title="recaptcha challenge" src="https://www.google.com/recaptcha/api2/bframe?hl=en&amp;v=qc5B-qjP0QEimFYUxcpWJy5B&amp;k=6Lf5O7cZAAAAAI_COZLYFzjFrZMFfFtPk2yfcgRZ&amp;cb=1b07gscbv14m" name="c-pzc9xsbhunwc" frameborder="0" scrolling="no" sandbox="allow-forms allow-popups allow-same-origin allow-scripts allow-top-navigation allow-modals allow-popups-to-escape-sandbox" style="width: 100%; height: 100%;"></iframe></div></div><div id="NsumiMp-1608839283413" class="" style="display: none !important;"><iframe id="rEV5Vmf-1608839283416" src="about:blank" frameborder="0" scrolling="no" title="chat widget" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: auto !important; position: static !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 350px !important; height: 520px !important; z-index: 999999 !important; cursor: auto !important; float: none !important; border-radius: unset !important; pointer-events: auto !important; display: none !important;"></iframe><iframe id="t7di1wz-1608839283418" src="about:blank" frameborder="0" scrolling="no" title="chat widget" class="" style="outline: none !important; visibility: visible !important; resize: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: auto 20px 20px auto !important; position: fixed !important; border: 0px !important; padding: 0px !important; transition-property: none !important; z-index: 1000001 !important; cursor: auto !important; float: none !important; pointer-events: auto !important; box-shadow: rgba(0, 0, 0, 0.16) 0px 2px 10px 0px !important; height: 60px !important; min-height: 60px !important; max-height: 60px !important; width: 60px !important; min-width: 60px !important; max-width: 60px !important; border-radius: 50% !important; transform: rotate(0deg) translateZ(0px) !important; transform-origin: 0px center !important; margin: 0px !important; display: block !important;"></iframe><iframe id="uNk69Qx-1608839283418" src="about:blank" frameborder="0" scrolling="no" title="chat widget" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: auto 15px 60px auto !important; position: fixed !important; border: 0px !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; display: none !important; z-index: 1000003 !important; cursor: auto !important; float: none !important; border-radius: unset !important; pointer-events: auto !important; width: 21px !important; max-width: 21px !important; min-width: 21px !important; height: 21px !important; max-height: 21px !important; min-height: 21px !important;"></iframe><iframe id="rEvmDtv-1608839283419" src="about:blank" frameborder="0" scrolling="no" title="chat widget" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: auto 0px 30px auto !important; position: fixed !important; border: 0px !important; padding: 0px !important; transition-property: none !important; cursor: auto !important; float: none !important; border-radius: unset !important; pointer-events: auto !important; transform: rotate(0deg) translateZ(0px) !important; transform-origin: 0px center !important; width: 124px !important; max-width: 124px !important; min-width: 124px !important; height: 95px !important; max-height: 95px !important; min-height: 95px !important; z-index: 1000002 !important; margin: 0px !important; display: none !important;"></iframe><div class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: 0px auto auto 0px !important; position: absolute !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 100% !important; height: 100% !important; display: none !important; z-index: 1000001 !important; cursor: move !important; float: left !important; border-radius: unset !important; pointer-events: auto !important;"></div><div id="FxTnLHQ-1608839283413" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: 0px auto auto 0px !important; position: absolute !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 6px !important; height: 100% !important; display: block !important; z-index: 999998 !important; cursor: w-resize !important; float: none !important; border-radius: unset !important; pointer-events: auto !important;"></div><div id="nBnmijg-1608839283414" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: 0px 0px auto auto !important; position: absolute !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 100% !important; height: 6px !important; display: block !important; z-index: 999998 !important; cursor: n-resize !important; float: none !important; border-radius: unset !important; pointer-events: auto !important;"></div><div id="HUH3GHf-1608839283414" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: 0px auto auto 0px !important; position: absolute !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 12px !important; height: 12px !important; display: block !important; z-index: 999998 !important; cursor: nw-resize !important; float: none !important; border-radius: unset !important; pointer-events: auto !important;"></div><iframe id="NV8kzSg-1608839283553" src="about:blank" frameborder="0" scrolling="no" title="chat widget" class="" style="outline: none !important; visibility: visible !important; resize: none !important; box-shadow: none !important; overflow: visible !important; background: none transparent !important; opacity: 1 !important; inset: auto 20px 100px auto !important; position: fixed !important; border: 0px !important; min-height: auto !important; min-width: auto !important; max-height: none !important; max-width: none !important; padding: 0px !important; margin: 0px !important; transition-property: none !important; transform: none !important; width: 378px !important; height: 617px !important; display: none !important; z-index: 999999 !important; cursor: auto !important; float: none !important; border-radius: unset !important; pointer-events: auto !important;"></iframe></div><iframe src="about:blank" title="chat widget logging" style="display: none !important;"></iframe>


</body>
</html>