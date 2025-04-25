<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Fundbank extends Model
{
    use LogsActivity;
    protected $fillable = ['name', 'account', 'ifsc', 'branch', 'status', 'user_id'];

    protected static $logAttributes = ['name', 'account', 'ifsc', 'branch', 'status', 'user_id'];
    protected static $logOnlyDirty = true;
    
    public $appends = ['username'];

    public function user(){
        return $this->belongsTo('App\User');
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
}
