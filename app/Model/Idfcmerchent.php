<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Idfcmerchent extends Model
{
	use LogsActivity;
    protected $fillable = ['clientCode', 'user_id', 'encToken', 'cookie', 'authTken','mobile','username','password','type'];
    
    protected static $logAttributes = ['clientCode', 'user_id', 'encToken', 'cookie', 'authTken','mobile','username','password','type'];
    protected static $logOnlyDirty = true;
    
    public $appends = ['displayname'];

    public function user(){
        return $this->belongsTo('App\User');
    }


    public function getDisplaynameAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") <br>(".$user->role->name.")";
        }
        return $data;
    }
}
