<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Scheme extends Model
{
	use LogsActivity;
    protected $fillable = ['name', 'user_id', 'status'];

    protected static $logAttributes = ['name', 'user_id', 'status'];
    protected static $logOnlyDirty = true;

    public function getUpdatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date('d M y - h:i A', strtotime($value));
    }
}
