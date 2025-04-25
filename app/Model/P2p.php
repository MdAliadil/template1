<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class P2p extends Model
{
    protected $fillable = ['midname','midid','bank','account','ifsc','accountholder','upiid','username','password','user_id','status'];
    
  
    public $appends = ['usernameuser'];
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function getUsernameuserAttribute()
    {
        $data = '';
        if($this->user_id){
            $user = \App\User::where('id' , $this->user_id)->first(['name', 'id', 'role_id']);
            $data = $user->name." (".$user->id.") (".$user->role->name.")";
        }
        return $data;
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
    public function getCreatedAtAttribute($value)
    {
        return date('d M - H:i', strtotime($value));
    }
}
