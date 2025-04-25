<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo $__env->yieldContent('title'); ?></title>
        
        <link rel="preconnect" href="https://fonts.gstatic.com/">
        <link rel="shortcut icon" href="<?php echo e(asset('assetsss/img/icons/icon-48x48.png')); ?>" />
        <link rel="canonical" href="pages-blank-2.html" />
        <link class="js-stylesheet" href="<?php echo e(asset('assetsss/css/light.css')); ?>" rel="stylesheet" >
        

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&amp;display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

       
         

        <!-- BEGIN SETTINGS -->
        <!-- Remove this after purchasing -->
        <script src="<?php echo e(asset('assetsss/js/settings.js')); ?>"></script>
        
        <style>
            body {
                opacity: 0;
            }
           
            
        </style>
        <?php echo $__env->yieldPushContent('style'); ?>
        <!-- END SETTINGS -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120946860-10"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-120946860-10', {
                'anonymize_ip': true
            });
        </script>
    </head>
    <!--
    HOW TO USE:
    data-theme: default (default), dark, light, colored
    data-layout: fluid (default), boxed
    data-sidebar-position: left (default), right
    data-sidebar-layout: default (default), compact
    -->

    <body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
        <div class="wrapper">
            
            <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <div class="main">
                
                <?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <main class="content">
                    <div class="container-fluid p-0">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div> 
                   
                </main>
                
                <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
       
        <script src="<?php echo e(asset('assets/js/app.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/jquery.validate.min.js')); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <?php if(isset($table) && $table == "yes"): ?>
        <script type="text/javascript" src="<?php echo e(asset('assetsss/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
        <?php endif; ?>
        <?php echo $__env->yieldPushContent('script'); ?>

        <script>
            document.addEventListener("DOMContentLoaded", function(event) {
                setTimeout(function() {
                    if (localStorage.getItem('popState') !== 'shown') {
                        window.notyf.open({
                            type: "success",
                            message: "Get access to all 500+ components and 45+ pages with AdminKit PRO. <u><a class=\"text-white\" href=\"https://adminkit.io/pricing\" target=\"_blank\">More info</a></u> 🚀",
                            duration: 10000,
                            ripple: true,
                            dismissible: false,
                            position: {
                                x: "left",
                                y: "bottom"
                            }
                        });

                        localStorage.setItem('popState', 'shown');
                    }
                }, 15000);
            });


            function datatableSetup(urls, datas, onDraw=function () {}, ele="#dataTables", element={}) {
                var options = {
                    processing: true,
                    searching: false,
                    serverSide: true,
                    pageLength: 10, // Set default page length to 10
                    orderable: false, // Disable sorting for all columns
                    order: [], // Disable initial sorting
                    orderClasses: false,  
                    ajax:{
                        url : urls,
                        type: "post",
                        data:function( d )
                            {
                                d._token = $('meta[name="csrf-token"]').attr('content');
                                d.fromdate = $('#searchForm').find('[name="from_date"]').val();
                                d.todate = $('#searchForm').find('[name="to_date"]').val();
                                d.searchtext = $('#searchForm').find('[name="searchtext"]').val();
                                d.agent = $('#searchForm').find('[name="agent"]').val();
                                d.status = $('#searchForm').find('[name="status"]').val();
                                d.product = $('#searchForm').find('[name="product"]').val();
                            },
                        beforeSend: function(){
                        },
                        complete: function(){
                            // $('#searchForm').find('button:submit').button('reset');
                            // $('#formReset').button('reset');
                        },
                        error:function(response) {
                        }
                    },
                    columns: datas
                };
    
                $.each(element, function(index, val) {
                    options[index] = val; 
                });
    
                var DT = $(ele).DataTable(options).on('draw.dt', onDraw);
                return DT;
            }
            $(document).ready(function() {
            
            
            
                const today = moment(); // Current date
                const oneWeekLater = moment().add(7, 'days'); // One week from today
    
                let fromDate = "";
                let toDate = "";
    
    
                 $('#mydate').daterangepicker({
                        "minYear": 2023,
                        "autoApply": true,
                        "linkedCalendars": false,
                        "alwaysShowCalendars": true,
                        "startDate": today.format('DD/MM/YYYY'),
                        "endDate": oneWeekLater.format('DD/MM/YYYY'),
                        "opens": "center",
                        "buttonClasses": "btn",
                        "drops": "down",
                        "locale": {
                            "format": 'DD/MM/YYYY'
                        },
                        "applyButtonClasses": "btn-theme",
                        "cancelClass": "btn-light"
                    }, function (start, end, label) {
                     //console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
                         fromDate = start.format('YYYY-MM-DD');
                         toDate = end.format('YYYY-MM-DD');
                    });
    
                $('#reportExport').click(function(){
                    $('.pageloader').fadeIn();
                    var type = $(this).attr('product');
                    var fromdate =  fromDate;
                    var todate =  toDate;
                    var searchtext =  $('#searchForm').find('input[name="searchtext"]').val();
                    var agent =  $('#searchForm').find('input[name="agent"]').val();
                    var status =  $('#searchForm').find('[name="status"]').val();
                    var product =  $('#searchForm').find('[name="product"]').val();
    
                    window.location.href = "<?php echo e(url('statement/export')); ?>/"+type+"?fromdate="+fromdate+"&todate="+todate+"&searchtext="+searchtext+"&agent="+agent+"&status="+status+"&product="+product;
                $('.pageloader').fadeOut();
                    
                });
                
                $('form#searchForm').submit(function(){
                    $('.pageloader').fadeIn();
                    //$('#searchForm').find('button[type=submit]').prop('disabled', true).html('<b><i class="fa fa-spin fa-spinner"></i></b> Loading...');
                    var fromdate =fromDate;
                    var todate =  toDate;
                    
                    $('#searchForm').find('input[name="from_date"]').val(fromDate);
                    $('#searchForm').find('input[name="to_date"]').val(toDate);
                    
                    $('#dataTables').dataTable().api().ajax.reload();
                    $('.pageloader').fadeOut();
                    return false;
                });
    
                $('#formReset').click(function () {
                    $('form#searchForm')[0].reset();
                    
                   
                    $('#searchForm').find('input[name="from_date"]').val('');
                        $('#searchForm').find('input[name="to_date"]').val('');
                        $('form#searchForm').find('select').select2().val('success').trigger('change')
    
                        //$('#formReset').button('loading');
                        $('#dataTables').dataTable().api().ajax.reload();
                    });
                
                
            });
        </script>


    </body>

</html>
<?php /**PATH C:\wamp64\www\template1\resources\views/layouts/app.blade.php ENDPATH**/ ?>