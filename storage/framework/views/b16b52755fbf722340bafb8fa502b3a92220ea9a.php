<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> 
    <title><?php echo $__env->yieldContent('title'); ?> - <?php echo e(Auth::user()->company->companyname); ?></title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
    <!--end::Fonts-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"
      integrity="sha256-tZHrRjVqNSRyWg2wbppGnT833E/Ys0DHWGwT04GiqQg="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->
 
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      integrity="sha256-9kPW/n5nn53j4WMRYAxe9c1rCY96Oogo/MKSVdKzPmI="
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
    <!--begin::Required Plugin(AdminLTE)-->
   
    <!--end::Required Plugin(AdminLTE)-->
    <style>
        div#dataTables_filter {
            display: none;
        }
        a.page-link {
            color: black;
        }
        .page-link.active, .active > .page-link{
            z-index: 3 !important;
            color: var(--bs-pagination-active-color) !important;
            background-color: #031633;
            border-color: var(--bs-pagination-active-border-color) !important;
            color: white !important;
        }
        .table-responsive {
    overflow-x: auto; /* Enable horizontal scrolling if needed */
}

.dataTables_wrapper {
    width: 100%; /* Ensure the table fits the container */
}
div#dataTables_length {
    display: none;
}
table.dataTable {
    width: 100% !important; /* Enforce full width for the table */
}
div#dataTables_paginate {
    float: right;
}
    </style>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">

      <?php echo $__env->make('layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <!--begin::Sidebar-->
       <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <!--end::Sidebar-->
      <!--begin::App Main-->
     
     <?php echo $__env->yieldContent('content'); ?>
      <!--end::App Main-->
      <!--begin::Footer-->
     
       
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <!--begin::Script-->
   
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
  
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    <?php if(isset($table) && $table == "yes"): ?>
    <script type="text/javascript" src="<?php echo e(asset('')); ?>assetsss/js/plugins/tables/datatables/datatables.min.js"></script>
    <?php endif; ?>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <!-- <script src="<?php echo e(asset('')); ?>assets/admintheme/js/adminlte.js"></script> -->
    <?php echo $__env->yieldPushContent('script'); ?>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script>
   $(document).ready(function () {
       
    var currentUrl = window.location.href;

    $('.nav-link').each(function () {
        var linkUrl = $(this).attr('href');

        if (linkUrl && currentUrl.includes(linkUrl)) {
            // Add 'active' class to the matching link
            $(this).addClass('active');

            // Multi-level menu handling
            var parentTreeview = $(this).closest('.has-treeview');
            if (parentTreeview.length > 0) {
                // Expand the parent menu
                parentTreeview.addClass('menu-open');

                // Highlight the parent link
                parentTreeview.find('> .nav-link').addClass('active');
            }
        } else {
            $(this).removeClass('active');
        }
    });
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
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
<?php /**PATH C:\wamp64\www\pg\resources\views/layouts/app.blade.php ENDPATH**/ ?>