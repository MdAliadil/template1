<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends Model
{
	use LogsActivity;
    protected $fillable = ['name', 'slug', 'type'];

    protected static $logAttributes = ['name', 'slug', 'type'];
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
