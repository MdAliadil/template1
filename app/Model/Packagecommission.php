<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Packagecommission extends Model
{
    protected $fillable = ['slab', 'type', 'value', 'scheme_id'];

    public $with = ['provider'];

    public function provider(){
        return $this->belongsTo('App\Model\Provider', 'slab');
    }
}
