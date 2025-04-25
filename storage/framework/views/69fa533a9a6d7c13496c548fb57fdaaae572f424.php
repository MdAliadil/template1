<?php $__env->startSection('title', 'Payin Statement'); ?>
<?php $__env->startSection('pagetitle', 'Payin Statement'); ?>

<?php $__env->startSection('content'); ?>

    <div class="container-fluid p-0 ">
        
        <header class="bg-primary text-white text-center py-5 bg-dark">
            <h1 class="text-light">Welcome to the API Documentation</h1>
            <p class="lead">Everything you need to get started with our API</p>
        </header>
    </div>

    <div class="container-api-documentation mt-3">
        <!-- API Endpoint 1 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> UPI Initiate </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                            <strong>Endpoint:</strong> <code style="color:black;"><span style="color:green;">GET</span> /api/users/{id}</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark mb-4" data-bs-toggle="collapse" data-bs-target="#fullDetails">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">
            <div class="row">
                <div class="col-sm-4 ml-5">
                    <strong>Response:</strong>
                    {
                        "message": "Record found"
                        "id": <?php echo e(Auth::user()->id); ?>

                        "name": <?php echo e(Auth::user()->name); ?>

                        "email": <?php echo e(Auth::user()->email); ?>

                        "mobile": <?php echo e(Auth::user()->mobile); ?>

                        "parent": <?php echo e(Auth::user()->parent_id); ?>

                        "kyc": <?php echo e(Auth::user()->kyc); ?>

                        "created_at": <?php echo e(Auth::user()->created_at); ?>

                        "Updated_at": <?php echo e(Auth::user()->updated_at); ?>

                    }

                </div>
                        <div class="col-sm-8">
                            <strong> Request:</strong>
                            {
                                    "token": "YFNMtZDRtkpbMqZCsUYqUCVdeiHV1U",
                                    "clientOrderId": "SM8994120w2827"
                             }
                        </div>
            </div>
                            </pre>
                </div>
            </div>
        </div>

        <!-- API Endpoint 2 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> UPI Status Check </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                          <strong>Endpoint:</strong> <code style="color:black;"><span style="color:red;">POST</span> /api/upi/statusCheck</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark" data-bs-toggle="collapse" data-bs-target="#fullDetails3">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails3" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">
            <div class="row">
                <div class="col-sm-4 ml-5">
                    <strong>Response:</strong>
                    {
                        "statuscode": "TXN",
                        "message": "Transction Found",
                        "data": {
                            "clientOrderId": "SM1726415171279829",
                            "amount": 102,
                            "payer_vpa": null,
                            "payerAccName": null,
                            "status": "success",
                            "payId": "SM2024091521161184641033",
                            "txnId": "425939807924",
                            "bankTxnId": "425939807924",
                            "npciTxnId": "425939807924"
                        }
                    }
                    

                </div>
                        <div class="col-sm-8">
                            <strong> Request:</strong>
                            {
                                "token":"xxxxxxxxx",
                                "clientOrderId":"H-623194b9002c77d66"
                              }
                        </div>
            </div>
                            </pre>
                </div>
            </div>
        </div>
        
        <!-- API Endpoint 3 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> UPI Webhook </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                            <strong>Endpoint:</strong> <code style="color:black;"><span style="color:red;"><span style="color:red;">POST</span></span> /api/users/{id}</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark" data-bs-toggle="collapse" data-bs-target="#fullDetails2">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails2" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">status=success&clientid=SM2024121011325281905&txnid=AD2024121011325256782&vpaadress=&npciTxnId=070173699357&payId=AD2024121011325256782&amount=100.0&bankTxnId=E2412100BW0X12&payerVpa=9087420932@ibl&payerAccName=Mihir&orderAmount=100
                            </pre>
                </div>
            </div>
        </div>


        <!-- API Endpoint 4 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> Payout Initiate </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                            <strong>Endpoint:</strong> <code style="color:black;"><span style="color:red;">POST</span> /api/smartpay/transaction</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark" data-bs-toggle="collapse" data-bs-target="#fullDetails4">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails4" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">
            <div class="row">
                <div class="col-sm-4 ml-5">
                    <strong>Response:</strong>
                    {
                        "statuscode": "TXN",
                        "status": "TXN",
                        "message": "In Process",
                        "rrn": ""
                    }

                </div>
                        <div class="col-sm-8">
                            <strong> Request:</strong>
                            {
                                "token": "xxxxxxxxxxx",
                                "transactionType": "spayout",
                                "apitxnid": "SMTE3ST533236264",
                                "amount": 10,
                                "Name": <?php echo e(Auth::user()->name); ?>,
                               
                                "email": <?php echo e(Auth::user()->email); ?>,
                                "mobile": <?php echo e(Auth::user()->mobile); ?>,
                                "mode": "imps",
                                "accountNumber": "922010067734917",
                                "ifsc": "UTIB0004668",
                                "bank": "AXIS"
                            }
                        </div>
            </div>
                            </pre>
                </div>
            </div>
        </div>

        <!-- API Endpoint 5 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> Payout Status Check </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                            <strong>Endpoint:</strong> <code style="color:black;"><span style="color:red;">POST</span> /api/smartpay/transactionStatus</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark" data-bs-toggle="collapse" data-bs-target="#fullDetails5">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails5" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">
            <div class="row">
                <div class="col-sm-4 ml-5">
                    <strong>Response:</strong>
                    {
                        "statuscode": "TXN",
                        "message": "Record found",
                        "status": "success",
                        "refno": "222222222222"
                    }

                </div>
                        <div class="col-sm-8">
                            <strong> Request:</strong>
                            {
                                    "token": "YFNMtZDRtkpbMqZCsUYqUCVdeiHV1U",
                                    "clientOrderId": "SM8994120w2827"
                             }
                        </div>
            </div>
                            </pre>
                </div>
            </div>
        </div>


        <!-- API Endpoint 6 -->
        <div class="card mb-3  border border-dark">
            <div class="card-header bg-light">
                <h3> Payout Webhook  </h3>
                <div class="row">
                    <div class="col-sm-10">
                        <p style="color:black;">
                            <strong>Endpoint:</strong> <code style="color:black;"><span style="color:green;">GET</span> /api/users/{id}</code>
                        </p>
                    </div>
                    <div class="col-sm-2 ">
                        <!-- DETAILS BUTTON -->
                        <button class="btn btn-dark" data-bs-toggle="collapse" data-bs-target="#fullDetails6">
                            Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Collapsible Full Details Section -->
            <div id="fullDetails6" class="collapse">
                <div class="card-body">
                    <button class="copy-btn btn btn-sm btn-outline-secondary mb-2" onclick="copyText('combined-json')">
                        Copy
                    </button>
                    <pre class="bg-dark text-white  rounded  " id="combined-json" style="font-family: monospace;">
            <div class="row">
                <div class="col-sm-4 ml-5">
                    <strong>Response:</strong>
                    {
                        "statuscode": "TXN",
                        "balance": {
                            "payoutWallet": "185.85",
                            "upiWallet": "10.56",
                            "disputeWallet": "0.00"
                        }
                    }

                </div>
                        <div class="col-sm-8">
                            <strong> Request:</strong>
                            {
                                "statuscode": "TXN",  
                             }
                        </div>
            </div>
                            </pre>
                </div>
            </div>
        </div>
        

        

       

        
        
    

    
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        function copyText(elementId) {
            const text = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert("Copied to clipboard!");
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\template1\resources\views/apitools/document.blade.php ENDPATH**/ ?>