@extends('layouts.app')
@section('title', "Certificate")
@section('pagetitle',  "Certificate")

@section('content')
<div class="content">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Complaints</h4>
                    <div class="heading-elements">
                        <button class="btn bg-slate btn-raised legitRipple" type="button" id="print"><i class="fa fa-print"></i></button>
                    </div>
                </div>

                <div class="panel-body" id="printable">
                    <table style="background-image:url('{{asset('assets/')}}/bcg.jpg');width:600px!important;height:424px;text-align: center;position: relative;">
                    <tr style="width: 100%">
                        <td colspan="2" style="position: relative;top:180px;width: 100%">{{Auth::user()->shopname}}</td>
                    </tr>
                    <tr><td colspan="2" style="padding:0px 50px;position: relative;top: 80px;">This is a certify that the above mentioned company/person is our authorized 
                    Business Correspondent Agent</td></tr>
                    <tr style="width: 100%">
                        <td style="position: relative;top:5px;width:45%;text-align:center;padding-left:100px">{{\Carbon\Carbon::createFromFormat('d M y - h:i A', Auth::user()->created_at)->format('d M Y')}}</td>
                        <td style="position: relative;top:5px;width: 55%;text-align: left;">{{Auth::user()->company->companyname}}</td>
                    </tr>
                    </table>
                </div>      
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#print').click(function(){
            openWin();
        });
    });

</script>
<script type="text/javascript">
  function openWin()
  {
    var body = $('#printable').html();
    var myWindow = window.open('','', 'width=800,height=600');

    myWindow.document.write(body);

    myWindow.document.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();

 }
</script>
@endpush