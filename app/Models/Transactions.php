<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Traits\Uuid;
use App\Traits\EncryptPersonalData;

class Transactions extends Model
{
    use Sortable, SoftDeletes, Uuid, EncryptPersonalData;
    protected $table = "transactions";
    protected $guarded = [];
    protected $dates = ['completed_at'];
    public $sortable = [
        'first_name',
        'last_name',
        'business_id',
        'email',
        'status',
        'amount',
        'type',
        'receiver_id',
        'created_at',
        'ref_id',
    ];

    protected $encryptable = [
        'card_number',
        'card_reference',
        'trans_id',
        'charge_id',
        'refund_id',
        'consent'
    ];

    public function link()
    {
        return $this->belongsTo(Paymentlink::class, 'payment_link');
    }    
    public function balance()
    {
        return $this->belongsTo(Balance::class, 'payment_link');
    }
    public function api()
    {
        return $this->belongsTo(Exttransfer::class, 'payment_link');
    }
    public function getBank()
    {
        return $this->belongsTo(Banksupported::class, 'bank_name');
    }
    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'receiver_id');
    }
    public function getCurrency()
    {
        return $this->belongsTo(Countrysupported::class, 'currency');
    }
    public function business(){
        return Business::wherereference($this->business_id)->first();
    } 
}
