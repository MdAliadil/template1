  <?php echo $__env->make('layouts.links', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  
<?php $__env->startSection('title', "Payout Statement"); ?>
<?php $__env->startSection('pagetitle', "Payout Statement"); ?>

<?php
    $table = "yes";
    $export = "upi";

    $status['type'] = "Report";
    $status['data'] = [
        "success" => "Success",
        "pending" => "Pending",
        "failed" => "Failed",
        "reversed" => "Reversed",
        "refunded" => "Refunded",
        "dispute" => "Dispute",
    ];
?>

<?php $__env->startSection('content'); ?>
 
<main class="page-content">
                <!--breadcrumb-->
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <a href="<?php echo e(route('home')); ?>" class="breadcrumb-title pe-3" style="color:black">Home</a>
                    
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-menu"style="color:black"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"style="color:black">List Statement</li>
                            </ol>
                        </nav>
                    </div>
                   
                </div>
            
       <?php echo $__env->make('layouts.filter', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
   
                
                <div class="card">
                    <div class="card-header">
    <h4 class="mb-0 text-uppercase"style="color:black">List Statement</h4>

                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="dataTables" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <thead>
                                      <tr>
                                            <th>TXN Time</th>
                                            <th>TXN ID</th>
                                            <th>Transaction Details</th>
                                            <th>Status</th>
                                            <th>Opening Bal</th>
                                            <th>Amount(+)</th>
                                            <th>Charge (-)</th>
                                            <th>Closing Bal</th>
                                            
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
   $(document).ready(function () {
        var url = "<?php echo e(url('statement/fetch')); ?>/payoutstatement/<?php echo e($id); ?>";
        var onDraw = function() {
        };
        var options = [
            { "data" : "name",
                render:function(data, type, full, meta){
                    return `<span style='font-size:13px' class="pull=right">`+full.created_at+`</span>`;
                }
            },
            { "data" : "id"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `UTR - `+full.refno;
                }
            },
          { "data" : "status",
                 render:function(data, type, full, meta){
                    if(full.status == "success"){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-success text-uppercase"><b>'+full.status+'</b></span>';
                    }else if(full.status== 'pending'){
                        var btn = '<span class="badge badge-light rounded-pill text-bg-warning text-uppercase"><b>'+full.status+'</b></span>';
                    }else{
                        var btn = '<span class="badge badge-light rounded-pill text-bg-danger text-uppercase"><b>'+full.status+'</b></span>';
                    }
                    return btn;
                } 
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    return `<i class="fa fa-inr"></i> `+full.balance;
                }
            },
            { "data" : "amount"},
            { "data" : "bank",
                render:function(data, type, full, meta){
                   
                        return `<i class="text-danger icon-dash"></i> `+ (parseFloat(full.charge) );
                    
                }
            },
            { "data" : "bank",
                render:function(data, type, full, meta){
                    if(full.status == "pending" || full.status == "success" || full.status == "reversed"||full.status == "refunded" ||full.status == "failed"){
                        if(full.trans_type == "credit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) + parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }else if(full.trans_type == "debit"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }else if(full.trans_type == "none"){
                            return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(parseFloat(full.amount) - parseFloat(full.charge))).toFixed(2);
                        }
                    }else{
                        return `<i class="fa fa-inr"></i> `+ (parseFloat(full.balance) - parseFloat(full.charge) ).toFixed(2);
                    }
                }
            } 
        ];

        datatableSetup(url, options, onDraw);
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\template\resources\views/statement/upiaccount.blade.php ENDPATH**/ ?>