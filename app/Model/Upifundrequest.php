<?php 

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Upifundrequest extends Model
{
    use LogsActivity;

    protected $fillable = ['amount','name', 'remark', 'status', 'type', 'user_id','account','contact_id', 'bank', 'ifsc', 'pay_type', 'payoutid','payoutref'];

    protected static $logAttributes = ['amount','name', 'remark', 'status', 'type', 'user_id','contact_id','account', 'bank', 'ifsc', 'pay_type', 'payoutid','payoutref'];

    protected static $logOnlyDirty = true;
    
    public $appends = ['username','rolename'];

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
    
    public function getRolenameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->role->slug;
        }
        return $data;
    }
    

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i:s A', strtotime($value));
    }
}

