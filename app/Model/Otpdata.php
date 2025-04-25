<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Otpdata extends Model
{
    protected $fillable = ['midid','message'];
    
  
    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
    public function getCreatedAtAttribute($value)
    {
        return date('d M - H:i', strtotime($value));
    }
}
