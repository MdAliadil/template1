<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('')}}assets/css/register.css">
    <title>SahajMoney</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer"
    />
       <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="header">
        <div class="row col-md-12">
            <div class="text-center col-md-4">
                <img src="{{asset('')}}assets/img2/SAHAJ MONEY LOGO APS.jpg" width="21%" alt="">
            </div>
            <div class="text-center pt-3 col-6 col-md-4">
                <img src="{{asset('')}}assets/img/SSRD.jpg" width="100%">
                <!--<h1 style="color: rgb(0, 245, 245);font-family: 'Assistant', sans-serif;">Sahaj<span style="color: rgb(240, 240, 0);">Money</span></h1>-->
            </div>
            <div class="text-center col-6 col-md-4">
                <div class="col-md-12 mt-1">
                    <h6><i class="fas fa-phone-square-alt"></i> Support - +91 7570001354 & 7570001355</h6>
                    <h6><i class="fas fa-envelope"></i> Email - support@sahajmoney.org</h6>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar">
        <div class="row justify-content-between m-auto col-md-12 text-center">
            <div class="col-md-3 col-6 my-1">
                <a href="https://login.sahajmoney.org"><i class="fas fa-home"></i> Home</a>
            </div>
            <div class="col-md-3 col-6 my-2">
                <a href="{{route('service')}}"><i class="fas fa-th"></i> Services</a>
            </div>
            <div class="col-md-3 col-6 my-2">
                <a href="{{route('regis')}}"><i class="fas fa-user"></i> Register </a>
            </div>
            <div class="col-md-3 col-6 my-2">
                <a href="{{route('contact')}}"><i class="fas fa-phone-alt"></i> Contact</a>
            </div>
        </div>
    </nav>
    <div class="">
        <div class="row container-fluid p-0 main_row">
            <div class="col-md-4">
                <img height="420px" src="{{asset('')}}assets/img/mobile-login-bro-786.png" width="100%" alt="">
                <!--<div class="container">-->
                <!--    <h3>Bank Details</h3>-->
                <!--    <p class="py-0 my-0">Bank Name :- State Bank of India</p>-->
                <!--    <p class="py-0 my-0">Account Name :- Shantai Computers</p>-->
                <!--    <p class="py-0 my-0">Account Number :- 35236731692</p>-->
                <!--    <p class="py-0 my-0">IFSC Code :- SBIN0003407</p>-->
                <!--</div>-->
            </div>
            <div class="col-md-8 row">
                <div class="card my-4 col-md-12">
                    <form id="registerForm" action="{{route('register')}}" method="post">
                        {{ csrf_field() }}
                    <h3 class="text-center m-auto py-0 my-0">रजिस्ट्रेशन</h3>
                     <div class="row">
                         <div class="form-group col-md-6">
                     <select name="slug" class="form-control select" required="">
                                    <option value="">Select Member Type</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->slug}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                    </div> 
                    </div> 
                    <div class="row">
                        <div class="form-group col-md-4">
                            <input type="text" name="name" placeholder="Full Name" required="">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="email" placeholder="Email ID" required="">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="mobile" placeholder="Mobile Number" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <select name="state" class="form-control" required="">
                                    <option value="">Select State</option>
                                   @foreach ($state as $state)
                                        <option value="{{$state->state}}">{{$state->state}}</option>
                                    @endforeach
                                </select>
                            
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="city" placeholder="City name" required="">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="pincode" placeholder="Pincode Number" required="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                             <input type="text" name="shopname"  value="" required="" placeholder="Enter Shopname">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" name="pancard"  value="" required="" placeholder="Enter pancard">
                        </div>
                        <div class="form-group col-md-4">
                             <input type="text" name="aadharcard" required=""  placeholder="Enter Aadhar" pattern="[0-9]*" maxlength="12" minlength="12">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="A">Address</label>
                             <textarea name="address" class="form-control" rows="3" required="" placeholder="Enter Value"></textarea>
                        </div>
                        
                    </div>
                    
                    <div class="form-group">
                        <button class="register-btn"  type="submit">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <footer>
        <div class="row">
            <div class="col-md-6 col-12">
                <!--<h6>Copyright &copy; 2021 SahajMoney All Rights Reserved</h6>-->
            </div>
            <div style="text-align: right;" class="col-md-6 col-12">
                <h6>Powered BY Suryanshi & shreya Rural Development Pvt Ltd</h6>
            </div>
        </div>
    </footer>



    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.min.js" integrity="sha384-Atwg2Pkwv9vp0ygtn1JAojH0nYbwNJLPhwyoVbhoPwBhjQPR5VtM2+xf0Uwh9KtT" crossorigin="anonymous"></script>
    -->
     <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/libraries/bootstrap.min.js"></script>
    <!--<script type="text/javascript" src="{{asset('')}}assets/js/core/app.js"></script>-->
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/jquery.form.min.js"></script>
    <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>
    <script src="{{asset('')}}assets/js/core/snackbar.js"></script>
    <script>
 $( document ).ready(function() {
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
                                $('#registerModal').modal('hide');
                                swal({
                                    type: 'success',
                                    title: 'Welcome',
                                    text: 'Your request has been submitted successfully, please wait for confirmation',
                                    showConfirmButton: true
                                });
                            }else{
                                notify(data.message, 'warning');
                            }
                        },
                        error: function(errors) {
                            console.log(errors.responseJSON.errors.mobile);
                            swal.close();
                            if(errors.status == '422'){
                               // notify(errors.responseJSON.errors[0], 'warning');
                               $('#emailError').text(errors.responseJSON.errors.email);
                               $('#mobileError').text(errors.responseJSON.errors.mobile);
                               $('#shopnameError').text(errors.responseJSON.errors.shopname);
                               $('#pancardError').text(errors.responseJSON.errors.pancard);
                               $('#aadharcardError').text(errors.responseJSON.errors.aadharcard);
                              
                            }else{
                                notify('Something went wrong, try again later.', 'warning');
                            }
                        }
                    });
                }
            });
        });
        </script>
</body>

</html>