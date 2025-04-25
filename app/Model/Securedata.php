<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Securedata extends Model
{
	protected $fillable = ['apptoken', 'ip','user_id', 'last_activity'];
}
