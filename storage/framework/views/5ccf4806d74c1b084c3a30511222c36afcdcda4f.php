<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('pagetitle', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<main class="page-content">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-4">
              <div class="col">
                <div class="card radius-10">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div>
                              <p class="mb-0 text-secondary">Total Orders</p>
                              <h4 class="my-1">4805</h4>
                              <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 5% from last week</p>
                          </div>
                          <div class="widget-icon-large bg-gradient-purple text-white ms-auto"><i class="bi bi-basket2-fill"></i>
                          </div>
                      </div>
                  </div>
                </div>
               </div>
               <div class="col">
                  <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Total Revenue</p>
                                <h4 class="my-1">$24K</h4>
                                <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 4.6 from last week</p>
                            </div>
                            <div class="widget-icon-large bg-gradient-success text-white ms-auto"><i class="bi bi-currency-exchange"></i>
                            </div>
                        </div>
                    </div>
                </div>
               </div>
               <div class="col">
                <div class="card radius-10">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div>
                              <p class="mb-0 text-secondary">Total Customers</p>
                              <h4 class="my-1">5.8K</h4>
                              <p class="mb-0 font-13 text-danger"><i class="bi bi-caret-down-fill"></i> 2.7 from last week</p>
                          </div>
                          <div class="widget-icon-large bg-gradient-danger text-white ms-auto"><i class="bi bi-people-fill"></i>
                          </div>
                      </div>
                  </div>
               </div>
               </div>
               <div class="col">
                <div class="card radius-10">
                  <div class="card-body">
                      <div class="d-flex align-items-center">
                          <div>
                              <p class="mb-0 text-secondary">Bounce Rate</p>
                              <h4 class="my-1">38.15%</h4>
                              <p class="mb-0 font-13 text-success"><i class="bi bi-caret-up-fill"></i> 12.2% from last week</p>
                          </div>
                          <div class="widget-icon-large bg-gradient-info text-white ms-auto"><i class="bi bi-bar-chart-line-fill"></i>
                          </div>
                      </div>
                  </div>
                </div>
               </div>
            </div><!--end row-->

   
    <!-- Main Content -->
    <div class="app-content mt-4">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- UPI Wallet -->
                <div class="col-lg-4 col-md-6">
                    <div class="card border-primary shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted">UPI Wallet</h5>
                                <h3 class="fw-bold text-primary">₹ <?php echo e(Auth::user()->upiwallet); ?></h3>
                            </div>
                            <i class="fas fa-wallet fa-3x text-primary"></i>
                        </div>
                    </div>
                </div>

                <!-- Payout Wallet -->
                <div class="col-lg-4 col-md-6">
                    <div class="card border-success shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted">Payout Wallet</h5>
                                <h3 class="fw-bold text-success">₹ <?php echo e(Auth::user()->mainwallet); ?></h3>
                            </div>
                            <i class="fas fa-money-check-alt fa-3x text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Chargeback Wallet -->
                <div class="col-lg-4 col-md-6">
                    <div class="card border-warning shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-muted">Chargeback Wallet</h5>
                                <h3 class="fw-bold text-warning">₹ <?php echo e(Auth::user()->disputewallet); ?></h3>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="row mt-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Pay-in Summary</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Today’s Amount <span id="todayPayinAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    This Month’s Amount <span id="thisMonthPayinAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Last Month’s Amount <span id="lastMonthPayinAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Today’s Transactions <span id="todayPayinTxn" class="fw-bold">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    This Month’s Transactions <span id="thisMonthPayinTxn" class="fw-bold">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Last Month’s Transactions <span id="lastMonthPayinTxn" class="fw-bold">0</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Payout Summary</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Today’s Amount <span id="todayPayoutAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    This Month’s Amount <span id="thisMonthPayoutAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Last Month’s Amount <span id="lastMonthPayoutAmount" class="fw-bold">₹0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Today’s Transactions <span id="todayPayoutTxn" class="fw-bold">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    This Month’s Transactions <span id="thisMonthPayoutTxn" class="fw-bold">0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Last Month’s Transactions <span id="lastMonthPayoutTxn" class="fw-bold">0</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</main>


<?php $__env->startPush('script'); ?>
<script>
$(document).ready(function(){
    function fetchDashboardData() {
        $.ajax({
            url: "<?php echo e(route('getdatas')); ?>",
            type: "GET",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
            error: function() { console.error("Error fetching data"); }
        });
    }

    fetchDashboardData();
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.appnew', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\pg\resources\views/home.blade.php ENDPATH**/ ?>