<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Report extends Model
{
    use LogsActivity;

    protected $fillable = ['number','mobile','provider_id','api_id','amount','charge','profit','gst','bank','mytxnid','terminalid','authcode','type','transtype','TxnMedium','credited_by','aepstype','payeeVPA','payer_vpa','payerMobile','payerAccName','payerIFSC','aadhar','tds','apitxnid','txnid','payid','refno','description','remark','option1','option2','option3','option4','status','user_id','credit_by','rtype','via','adminprofit','balance','trans_type','product','wid','wprofit','mdid','mdprofit','disid','disprofit','oid','oprofit'];

    protected static $logAttributes = ['number','mobile','provider_id','api_id','amount','charge','profit','txnid','payid','refno','remark','status','user_id','credit_by','balance','trans_type'];
    protected static $logOnlyDirty = true;
    
    public $appends = ["fundbank", 'apicode', 'apiname', 'username', 'sendername', 'providername'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function sender(){
        return $this->belongsTo('App\User', 'credit_by');
    }

    public function api(){
        return $this->belongsTo('App\Model\Api');
    }

    public function provider(){
        return $this->belongsTo('App\Model\Provider');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getAmountAttribute($value)
    {
        return round($value, 2);
    }

    public function getFundbankAttribute($value)
    {
        $data = '';
        if($this->product == "fund request"){
            $data = Fundbank::where('id', $this->option1)->first();
        }
        return $data;
    }

    public function getApicodeAttribute()
    {
        $data = Api::where('id' , $this->api_id)->first(['code']);
        return $data->code;
    }

    public function getApinameAttribute()
    {
        $data = Api::where('id' , $this->api_id)->first(['name']);
        return $data->name;
    }

    public function getProvidernameAttribute()
    {
        $data = '';
        if($this->provider_id){
            $provider = Provider::where('id' , $this->provider_id)->first(['name']);
            $data = $provider->name;
        }
        return $data;
    }

    public function getUsernameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") <br>(".$user->role->name.")";
        }
        return $data;
    }

    public function getSendernameAttribute()
    {
        $data = '';
        if($this->credit_by){
            $user = \App\User::where('id' , $this->credit_by)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.")<br>(".$user->role->name.")";
        }
        return $data;
    }
}
