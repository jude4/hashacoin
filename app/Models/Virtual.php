<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virtual extends Model {
    use Uuid, SoftDeletes;
    protected $table = "virtual_cards";
    protected $guarded = [];
    protected $hidden = ['user_id', 'charge'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }    
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class, 'currency');
    } 
}
