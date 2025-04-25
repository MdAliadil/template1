<?php $__env->startSection('title', 'Payout'); ?>
<?php $__env->startSection('pagetitle', 'UPIoii'); ?>
<?php
    $table = 'yes';
    $export = 'upi';

    $status['type'] = 'Report';
    $status['data'] = [
        'success' => 'Success',
        'pending' => 'Pending',
        'failed' => 'Failed',
        'reversed' => 'Reversed',
        'refunded' => 'Refunded',
        'dispute' => 'Dispute',
    ];
?>

<?php $__env->startSection('content'); ?>

    <main class="page-content">
        <!--breadcrumb-->
        
        <div class="d-flex align-items-center mb-3">
            <a href="<?php echo e(route('home')); ?>"> <span class="me-1 mb-1 h3 card-title d-inline-block">Home</span></a>
            <span class="h3 text-muted card-title">/</span>
            <span class="h3 ms-2 card-title">Payout Report</span>
        </div>

        <?php echo $__env->make('layouts.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



        <div class="card">
            <div class="card-header d-flex justify-content-between pb-0">
                <h1 class="card-title">Payout Statement</h1>
              
            </div>
            <div class="card-body">
                <div id="custom-loader" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="dataTables" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bank Deatils</th>
                                    <th>Transaction Details</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        <tbody>
                            <!-- Dynamic rows will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('style'); ?>
    <!-- DataTables Bootstrap 5 Styling -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <!-- DataTables and Bootstrap 5 JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var url = "<?php echo e(url('statement/fetch')); ?>/payoutstatement/<?php echo e($id); ?>";
            var onDraw = function() {
                // Hide loader after table redraws from ddb
                $('#loadingOverlay').hide();
            };
            var options = [{
                    "data": "name",
                    render: function(data, type, full, meta) {
                        return `<div>
                            <span class='text-inverse m-l-10'><b>` + full.id + `</b> </span>
                            <div class="clearfix"></div>
                        </div><span style='font-size:13px' class="pull=right">` + full.created_at + `</span>`;
                    }
                }, {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        return `Account - ` + full.number + `<br>Bank Name - ` + full.option3 +
                            `<br>Bank Name - ` + full.option4;
                    }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {
                        return `Client Txn Id. - ` + full.apitxnid + `<br>Api Txnid - ` + full.txnid +
                            `<br>PayID - ` + full.payid + `<br>UTR - ` + full.refno;
                    }
                },
                {
                    "data": "bank",
                    render: function(data, type, full, meta) {

                        return `Amount - <i class="fa fa-inr"></i> ` + full.amount +
                            `<br>Charge - <i class="fa fa-inr"></i> ` + full.charge;

                    }
                },

                {
                    "data": "status",
                    render: function(data, type, full, meta) {
                        if (full.status == "success") {
                            var out = `<span class="badge bg-success rounded-pill text-uppercase">` + full
                                .status + `</span>`;
                        } else if (full.status == "complete") {
                            var out = `<span class="label label-primary badge bg-success text-uppercase">` +
                                full.status + `</span>`;
                        } else if (full.status == "pending") {
                            var out =
                                `<span class=" badge bg-warning rounded-pill text-uppercase">Pending</span>`;
                        } else if (full.status == "reversed") {
                            var out =
                                `<span class="badge bg-warning rounded-pill text-uppercase">Reversed</span>`;
                        } else {
                            var out = `<span class="badge bg-danger rounded-pill text-uppercase">` + full
                                .status + `</span>`;
                        }

                        var menu = ``;
                        <?php if(Myhelper::can('aeps_status')): ?>
                            menu +=
                                `<li class="dropdown-item"><a href="javascript:void(0)" onclick="status(` +
                                full.id + `, 'upi')"><i class="icon-info22"></i>Check Status</a></li>`;
                        <?php endif; ?>

                        <?php if(Myhelper::can('aeps_statement_edit')): ?>
                            menu +=
                                `<li class="dropdown-item"><a href="javascript:void(0)" onclick="editReport(` +
                                full.id + `,'` + full.refno + `','` + full.txnid + `','` + full.payid +
                                `','` + full.remark + `', '` + full.status +
                                `', 'payout')"><i class="icon-pencil5"></i> Edit</a></li>`;
                        <?php endif; ?>

                        menu +=
                            `<li class="dropdown-item"><a href="javascript:void(0)" onclick="complaint(` +
                            full.id + `, 'upi')"><i class="icon-cogs"></i> Complaint</a></li>`;


                        out += ``;

                        return out;
                    }
                }
            ];
            $(document).on('click', '#searchBtn', function() {
                $('#loadingOverlay').show();
            });



            datatableSetup(url, options, onDraw);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\template1\resources\views/statement/payout.blade.php ENDPATH**/ ?>