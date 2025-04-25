<!DOCTYPE html>
<html>
   <head>
      <meta http-equiv="Content-Type">
      <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
      <meta name="description" content="">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>QR Payment Collection - Dashboard</title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script type="text/javascript" src="{{asset('')}}assets/js/core/sweetalert2.min.js"></script>

      <style type="text/css">
         p { margin: 0 0 1px;}
         .menu {background-color: #245469;}
         .hMenuBar {background-color: #67d0ff; }
         #HeaderCont {color: #3f51b5;font-weight:600;}
         .notesection{font-size: 12px;color: #333333;background-color: transparent !important;padding:0px !important;}
          @media only screen and (max-width: 640px) {
	         #HeaderCont {color:#a12523;font-size:14px;width:478px;padding-top:0px;padding-bottom:10px;}
	     }
      </style>
   </head>
   <body>
      <div class="hMenuBar" style=" height:0px;">
         <div class="anypaylogo padT5">
             
         	 <!--<img src="https://dashboard.tejaspee.com/public/logos/logo1.jpg" height="55" border="0">-->
        
         </div><br/></a>
         <div class="clr"></div>
      </div>
      <div class="header">
         <div class="menu" style=" height:65px;">
            <div class="wid1060">
               <div class="anypaylogo marL-20 padT5">
               </div>
            </div>
         </div>
      </div>
      <div id="wrapper">
         <div class="contentContainerAM borGrey wid900">
            <div class="container">
               <div class="row">
                  <div class="col-md-12 text-center">
                     <h2 style="color: black">Awaiting QR Payment Confirmation
                        <img src="{{asset('')}}assets/img/loading.gif" align="middle">
                        <span id="countdown">05:00</span>
                     </h2>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3"></div>
                  <div class="col-md-6">
                     <div class="panel panel-default">
                        <div class="panel-body">
                           <p><b>-</b> Please do not press <b>'Refresh'</b> or <b>'Back'</b> button.</p>
                           <p><b>-</b> Transaction is being processed, towards <b></b> amounting to <b>Rs. {{$amount}} </b></p>
                           <p><b>-</b> Please approve/process the payment request, through the respective<span id="HeaderCont"> Payment service provider (PSP) </span>mapped upi application with in 5 minutes.</p>
                           <div class="col-md-12 text-center">
                              <div class="qrimage" id="qrimage"></div>
                           </div>
                           <div class="col-md-12 text-center">
                              <br>
                              <a href="<?php echo $option1;?>">
                              <button type="button" class="btn btn-primary">Click to Pay</button>
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3"></div>
               </div>
               <div class="row">
                  <div class="col-md-2"></div>
                  <div class="col-md-10">
                     <div class="notesection">
                        <b style="padding-left: 27px">Notes:</b>
                        <ul>
                           <li><b style="color:red;">Please make the payment only by scanning the QR Code, if you pay directly on the UPI ID, the payment will not be added.</b></li>

                           <li>
                           <b>Transaction Valid for next 5 Minutes from the time of payment request initiation/generation.</b></li>

                           <li>Transaction will be Marked as Failed, if Transction Status is not processed with Successful Payment with in 5 Minutes.</li>

                           <li>In case Transaction Expired, you can reinitate the transaction subjective on the Merchnat Transaction Validity.</li>

                           <li>Successful receipt can be generated through qr_collection Portal - Transaction History Option.</li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
        <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script> 
      
      <script>
      
         $(document).ready(function() {
            var vpastring="<?php echo $option1; ?>";
            console.log(vpastring);
            jQuery("#qrimage").qrcode({
                    //render:"table"
                    width:220,
                    height: 220,
                    text: vpastring
                });
                
           var seconds = 300; // 5 minutes = 300 seconds
           var countdown = setInterval(function() {
             seconds--;
             var minutes = Math.floor(seconds / 60);
             var remainingSeconds = seconds % 60;
             if (remainingSeconds < 10) {
               remainingSeconds = "0" + remainingSeconds;
             }
             $("#countdown").text(minutes + ":" + remainingSeconds);
             if (seconds == 0) {
               clearInterval(countdown);
             }
           }, 1000);
           myInterval = setInterval(function(){ checkQrPay("<?php echo $orderId; ?>") }, 5000);
         });
         
        function checkQrPay(extTransactionId) {
            $.ajax({
                url: "{{url('api/upi/statusCheckWeb')}}",
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType:'json',
                data : {"extTransactionId" : extTransactionId},
                success: function(result){
                    console.log(result);
                    if(result.data.status == "success"){
                        clearInterval(myInterval);
                        swal({
                            type: 'success',
                            title: "Transction :" +result.data.status,
                            text : "Bank Refrence :" + result.data.utr,
                            showConfirmButton: false,
                            
                            
                        });
                     location.href = result.data.returnUrl;
                    }else if(result.data.status == "failed"){
                        clearInterval(myInterval);
                        swal({
                            type: 'error',
                            title: "Transction :" +result.data.status,
                            text : "Bank Refrence :" + result.data.utr,
                            showConfirmButton: false,
                            
                            
                        });
                        location.href = result.data.returnUrl;
                    }
                }
            });
        }
      </script>
   </body>
</html>