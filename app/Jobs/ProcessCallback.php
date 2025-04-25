<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\User;

class ProcessCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $myaepsreport;

    public function __construct($myaepsreport)
    {
        $this->myaepsreport = $myaepsreport;
    }

    public function handle()
    {
       /* \Log::info('Making new directory'.$this->myaepsreport);
       // sleep(2);
        $user = User::where('id',$this->myaepsreport->user_id)->first();
        $output = [
            'status' => $this->myaepsreport->status =='success'?'TXN':"TXF",
            'statuscode' => $this->myaepsreport->status =='success'?'TXN':"TXF",
            'utr' => $this->myaepsreport->refno,
            'message' => 'Transaction Successfull',
            'amount' => $this->myaepsreport->amount,
            'clientTxnid' => $this->myaepsreport->apitxnid,
            'apitxnid' => $this->myaepsreport->txnid,
            'product' => 'spayout'
        ];

        \Myhelper::curl($user->callbackurl . '?' . http_build_query($output), "GET", "", [], "yes", "eayoutCallback", $this->myaepsreport->apitxnid);
         \Log::info('Callback Send Success  '.$user->callbackurl . '?' . http_build_query($output));*/
    }
}