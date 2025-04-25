<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('')}}assets/css/contact.css">
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

    <div class="mt-3">
        <div class="row main_row">
            <div class="col-md-3">
                <div class="card">
                    <div class="form-group">
                        <input type="text" placeholder="First Name">
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Last Name">
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Phone">
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <textarea name="" id="" placeholder="Message"></textarea>
                    </div>
                    <div class="form-group">
                        <button class="register-btn">Send Message</button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="box">
                    <div class="head m-auto text-center col-4">
                        <div class="boxed text-center">
                            <h5>Contact US</h5>
                        </div>
                    </div>
                    <div class="row mt-4 col-12">
                        <div class="col-4">
                            <h5><i class="fas fa-map-marker-alt"></i> Address:</h5>
                        </div>
                        <div class="col-8 ">
                            <img src="{{asset('')}}assets/img/SSRD.jpg" width="70%">
                            <!--<h5>SahajMoney</h5>-->
                            <!--<p class="m-0" style="font-size:12px;">A unit of SURYANSHI & SHREYA RURAL DEVELOPMENT PVT LTD</p>-->
                            <!--<h6>SahajMoney</h6>-->
                            <h6>JUNGLE AMWA, POST- BELWA JUNGLE KUSHINAGAR UttarPradesh, India, 274304</h6>
                        </div>
                    </div>
                    <div class="row mt-4 col-12">
                        <div class="col-4">
                            <h5><i class="fas fa-phone-square-alt"></i> Contact:</h5>
                        </div>
                        <div class="col-8 ">
                            <h6><i class="fas fa-phone-square-alt"></i> Support - +91 7570001354 & 7570001355</h6>
                            <!-- <h6><i class="fas fa-envelope"></i> Email - support@sahajmoney.org</h6> -->
                        </div>
                    </div>
                    <div class="row mt-4 col-12">
                        <div class="col-4">
                            <h5><i class="fas fa-envelope"></i> Email:</h5>
                        </div>
                        <div class="col-8 ">
                            <h5>support@sahajmoney.org</h5>
                        </div>
                    </div>
                    <div class="row mt-4 col-12">
                        <div class="col-4">
                            <h5><i class="fas fa-globe"></i> Website:</h5>
                        </div>
                        <div class="col-8">
                            <h5><a href="http://sahajmoney.org">www.sahajmoney.org</a></h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <img height="420px" src="{{asset('')}}assets/img/regi des.jpg" width="100%" alt="">
            </div>
        </div>

    </div>
    <footer>
        <div class="row">
            <div class="col-md-6 col-12">
                <!--<h6>Copyright &copy; 2021 Sahajmoney All Rights Reserved</h6>-->
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
</body>

</html>