<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> 
   
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('assets/images/favicon-32x32.png')); ?>" type="image/png" />

    <!-- CSS Plugins -->
    <link href="<?php echo e(asset('assetsss/plugins/simplebar/css/simplebar.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/plugins/perfect-scrollbar/css/perfect-scrollbar.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/plugins/metismenu/css/metisMenu.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/plugins/vectormap/jquery-jvectormap-2.0.2.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet" />
    
    <!-- Bootstrap & Theme CSS -->
    <link href="<?php echo e(asset('assetsss/css/bootstrap.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/bootstrap-extended.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/style.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/icons.css')); ?>" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Theme Styles -->
    <link href="<?php echo e(asset('assetsss/css/dark-theme.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/light-theme.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/semi-dark.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assetsss/css/header-colors.css')); ?>" rel="stylesheet" />
</head>
<body>
    <?php echo $__env->yieldContent('content'); ?>

    <!-- JS Scripts -->
    <script src="<?php echo e(asset('assetsss/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/simplebar/js/simplebar.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/metismenu/js/metisMenu.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/easyPieChart/jquery.easypiechart.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/peity/jquery.peity.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/perfect-scrollbar/js/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/js/pace.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/vectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/apexcharts-bundle/js/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/plugins/datatable/js/dataTables.bootstrap5.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('assetsss/js/index.js')); ?>"></script>

    <script>
        new PerfectScrollbar(".best-product");
        new PerfectScrollbar(".top-sellers-list");
    </script>

     </body>
</html>
<?php /**PATH C:\wamp64\www\pg\resources\views/layouts/link.blade.php ENDPATH**/ ?>