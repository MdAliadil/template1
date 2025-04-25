<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Idfcupi extends Model
{
	use LogsActivity;
    protected $fillable = ['mobile', 'username', 'password', 'accountType', 'user_id'];
    
    protected static $logAttributes = ['mobile', 'username', 'password', 'accountType', 'user_id'];
    protected static $logOnlyDirty = true;
}
