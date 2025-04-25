<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Commission extends Model
{
	use LogsActivity;
    protected $fillable = ['slab', 'type', 'apiuser', 'whitelable', 'md', 'distributor', 'retailer', 'scheme_id','reseller'];

    protected static $logAttributes = ['slab', 'type', 'whitelable', 'md', 'distributor', 'retailer', 'scheme_id'];
    protected static $logOnlyDirty = true;
    
    public $with = ['provider'];

    public function provider(){
        return $this->belongsTo('App\Model\Provider', 'slab');
    }
}
