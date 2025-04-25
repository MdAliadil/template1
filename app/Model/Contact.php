<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['firstName', 'lastName', 'email', 'mobile', 'accountNumber', 'ifsc', 'type', 'accountType', 'referenceId', 'user_id', 'contactId'];


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
