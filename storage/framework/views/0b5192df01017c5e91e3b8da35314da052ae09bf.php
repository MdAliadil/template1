<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('pagetitle', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>




<div class="container-fluid p-0">

    <h1 class="h3 mb-3">Dashboard</h1>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12 d-flex ">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">UPI Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹<?php echo e(Auth::user()->upiwallet); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Payout Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat" style="background: #F7931A; color: white;">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹<?php echo e(Auth::user()->mainwallet); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-12 d-flex">
            <div class="card flex-fill">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Dispute Wallet</h5>
                        </div>

                        <div class="col-auto">
                            <div class="stat" style="background: #345D9D; color: white;">
                                <i class="fa-solid fa-indian-rupee-sign align-middle"></i>
                            </div>
                        </div>
                    </div>
                    <h4 class="mt-0 mb-1">₹<?php echo e(Auth::user()->disputewallet); ?></h4>
                </div>
            </div>
        </div>
        
    
    </div>
    
    <div class="row">
        <div class="col-12 col-xl-6">
            <div class="card ">
                <div class="pay-heading-text mt-2 mx-3">
                    <h3>Pay-in Summary</h3>
                </div>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Monthly Report </th>
                            <th class="text-end">Amount</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>This Month's Amount</td>
                            <td class="text-end" id="thisMonthPayinAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Last Month's Amount</td>
                            <td class="text-end" id="lastMonthPayinAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Today's Transactions</td>
                            <td class="text-end" id="todayPayinTxn">0</td>
                        </tr>
                        <tr>
                            <td>This Month's Transactions</td>
                            <td class="text-end" id="thisMonthPayinTxn">0</td>
                        </tr>
                        <tr>
                            <td>Last Months Transactions</td>
                            <td class="text-end" id="lastMonthPayinTxn">0</td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <div class="card">
                <div class="pay-heading-text mt-2 mx-3">
                    <h3>Payout Summary</h3>
                </div>
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th> Monthly Report </th>
                            <th class="text-end">Amount</th>
                        
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>This Month's Amount</td>
                            <td class="text-end" id="thisMonthPayoutAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Last Month's Amount</td>
                            <td class="text-end" id="lastMonthPayoutAmount">₹0</td>
                        </tr>
                        <tr>
                            <td>Today's Transactions</td>
                            <td class="text-end" id="todayPayoutTxn">0</td>
                        </tr>
                        <tr>
                            <td>This Month's Transactions</td>
                            <td class="text-end" id="thisMonthPayoutTxn">0</td>
                        </tr>
                        <tr>
                            <td>Last Months Transactions</td>
                            <td class="text-end" id="lastMonthPayoutTxn">0</td>
                        </tr>
                        
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('script'); ?>
      <script>
          $(document).ready(function() {
              function fetchDashboardData() {
                  $.ajax({
                      url: "<?php echo e(route('getdatas')); ?>",
                      type: "GET",
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      dataType: 'json',
                      success: function(result) {
                          $('#todayPayinAmount').text('₹ ' + result.datas.payin[0].todayAmount);
                          $('#thisMonthPayinAmount').text('₹ ' + result.datas.payin[0].thisMonthAmount);
                          $('#lastMonthPayinAmount').text('₹ ' + result.datas.payin[0].lastMonthAmount);
                          $('#todayPayinTxn').text(result.datas.payin[0].todayTxnCount);
                          $('#thisMonthPayinTxn').text(result.datas.payin[0].thisMonthTxnCount);
                          $('#lastMonthPayinTxn').text(result.datas.payin[0].lastMonthTxnCount);

                          $('#todayPayoutAmount').text('₹ ' + result.datas.payout[0].todayAmount);
                          $('#thisMonthPayoutAmount').text('₹ ' + result.datas.payout[0].thisMonthAmount);
                          $('#lastMonthPayoutAmount').text('₹ ' + result.datas.payout[0].lastMonthAmount);
                          $('#todayPayoutTxn').text(result.datas.payout[0].todayTxnCount);
                          $('#thisMonthPayoutTxn').text(result.datas.payout[0].thisMonthTxnCount);
                          $('#lastMonthPayoutTxn').text(result.datas.payout[0].lastMonthTxnCount);
                      },
                      error: function() {
                          console.error("Error fetching data");
                      }
                  });
              }

              fetchDashboardData();
          });
      </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\template1\resources\views/dashboard/home.blade.php ENDPATH**/ ?>