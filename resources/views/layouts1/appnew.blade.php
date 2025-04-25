<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>@yield('title') - {{Auth::user()->company->companyname}}</title>
    <!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <link rel="icon" type="image/x-icon" href="/images/favicon.ico">

  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      @include('layouts.pageheader')
      <!--begin::Sidebar-->
       @include('layouts.sidebar')
      <!--end::Sidebar-->
      <!--begin::App Main-->
       @include('layouts.link')
     @yield('content')
      <!--end::App Main-->
      <!--begin::Footer-->
     
       
      <!--end::Footer-->
    </div>
   
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
    @if (isset($table) && $table == "yes")
    <script type="text/javascript" src="{{asset('')}}assetsss/js/plugins/tables/datatables/datatables.min.js"></script>
    @endif
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <!-- <script src="{{asset('')}}assets/admintheme/js/adminlte.js"></script> -->
    @stack('script')
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

                window.location.href = "{{ url('statement/export') }}/"+type+"?fromdate="+fromdate+"&todate="+todate+"&searchtext="+searchtext+"&agent="+agent+"&status="+status+"&product="+product;
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
