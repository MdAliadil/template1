<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Xettlemerchant extends Model
{
    protected $fillable = ['merchantBusinessName', 'merchantVirtualAddress','requestUrl','panNo','contactEmail','gstn','merchantBusinessType','perDayTxnCount','perDayTxnLmt','perDayTxnAmt','mobile','address','state','city','pinCode','mcc','vpaaddress','subMerchantId','contact_id','f_name','l_name','payout_mobile','payout_email','account','ifsc','user_id'];
    
    public $appends = ['username'];
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function getUsernameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") (".$user->role->name.")";
        }
        return $data;
    }
}
