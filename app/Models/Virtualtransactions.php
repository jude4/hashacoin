<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virtualtransactions extends Model {
    use Uuid, SoftDeletes;
    protected $table = "virtual_transactions";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }    
    public function card()
    {
        return $this->belongsTo('App\Models\Virtual','virtual_id');
    }   

}
