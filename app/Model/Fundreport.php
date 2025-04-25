<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Fundreport extends Model
{
    use LogsActivity;
    protected $fillable = ['type', 'fundbank_id', 'ref_no', 'paydate', 'payslip', 'remark', 'status', 'user_id', 'credited_by', 'paymode', 'amount'];

    protected static $logAttributes = ['type', 'fundbank_id', 'ref_no', 'paydate', 'payslip', 'remark', 'status', 'user_id', 'credited_by', 'paymode', 'amount'];
    protected static $logOnlyDirty = true;
    
    public $with = ['fundbank'];

    public $appends = ['username', 'sendername'];

    public function fundbank(){
        return $this->belongsTo('App\Model\Fundbank');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function sender(){
        return $this->belongsTo('App\User', 'credited_by');
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
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

    public function getSendernameAttribute()
    {
        $data = '';
        if($this->credited_by){
            $user = \App\User::where('id' , $this->credited_by)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") (".$user->role->name.")";
        }
        return $data;
    }
}
