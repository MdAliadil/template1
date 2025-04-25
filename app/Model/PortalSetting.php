<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PortalSetting extends Model
{
	use LogsActivity;
    protected $fillable = ['name', 'code', 'value'];

    protected static $logAttributes = ['name', 'code', 'value'];
    protected static $logOnlyDirty = true;
    
    public $timestamps = false;
}
