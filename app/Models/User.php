<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use App\Traits\Uuid;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, Sortable, Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $sortable = [
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
    ];
    protected $fillable = [
        'facebook', 
        'twitter', 
        'instagram', 
        'linkedin', 
        'youtube'
    ];
    protected $guard = 'user';

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    //stat
    public function tranStat($duration, $currency)
    {
        if($duration == "today"){
            return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->get();
        }elseif($duration == "week"){
            return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->get();
        }elseif($duration == "month"){
            return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->get();
        }elseif($duration == "year"){
            return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->get();
        }
    }
    public function channel($type, $currency, $duration)
    {
        if($duration == "today"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereDay('created_at', '=', date('d'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereDay('created_at', '=', date('d'))->count()/Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->count()*100;
            }
        }elseif($duration == "week"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()/Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()*100;
            }
        }elseif($duration == "month"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereMonth('created_at', '=', date('m'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereMonth('created_at', '=', date('m'))->count()/Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->count()*100;
            }
        }elseif($duration == "year"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereYear('created_at', '=', date('Y'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode(1)->wherePaymentType($type)->whereYear('created_at', '=', date('Y'))->count()/Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->count()*100;
            }
        }
    }
    public function successStat($duration, $currency)
    {
        if($duration == "today"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->count()/Transactions::where('receiver_id', $this->id)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->count()*100;
            }
        }elseif($duration == "week"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()/Transactions::where('receiver_id', $this->id)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count()*100;
            }
        }elseif($duration == "month"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->count()/Transactions::where('receiver_id', $this->id)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->count()*100;
            }
        }elseif($duration == "year"){
            if(Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->count()==0){
                return 0;
            }else{
                return Transactions::where('receiver_id', $this->id)->whereStatus(1)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->count()/Transactions::where('receiver_id', $this->id)->whereCurrency($currency)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->count()*100;
            }
        }
    }
    public function getTransactionsExceptPayout($duration, $id)
    {
        if($duration == "today"){
            return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->where('type', '!=', 3)->wherestatus(1)->wheremode($this->business()->live)->whereDay('created_at', '=', date('d'))->latest()->get();
        }elseif($duration == "week"){
            return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->where('type', '!=', 3)->wherestatus(1)->wheremode($this->business()->live)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->latest()->get();
        }elseif($duration == "month"){
            return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->where('type', '!=', 3)->wherestatus(1)->wheremode($this->business()->live)->whereMonth('created_at', '=', date('m'))->latest()->get();
        }elseif($duration == "year"){
            return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->where('type', '!=', 3)->wherestatus(1)->wheremode($this->business()->live)->whereYear('created_at', '=', date('Y'))->latest()->get();
        }
    }
    public function nextPay($currency)
    {
        return Transactions::where('receiver_id', $this->id)->whereStatus(0)->whereType(3)->whereCurrency($currency)->wherebusiness_id($this->business_id)->orderBy('created_at', 'desc')->first();
    }
    //

    public function business()
    {
        return Business::wherereference($this->business_id)->first();
    }  
    public function getCountrySupported()
    {
        return Countrysupported::find($this->country_id);
    }    
    public function getCountry()
    {
        return Country::find($this->getCountrySupported()->country_id);
    }   
    public function getVcard()
    {
        return Virtual::whereUser_id($this->id)->wherebusiness_id($this->business_id)->orderby('id', 'DESC')->paginate(6);
    }   
    public function getState()
    {
        return Shipstate::wherecountry_code($this->getCountry()->iso2)->orderby('name', 'asc')->get();
    }  
    public function myState()
    {
        return Shipstate::whereid($this->state)->first();
    }    
    public function myCity()
    {
        return Shipcity::whereid($this->city)->first();
    }
    public function getPayment($limit)
    {
        return Paymentlink::whereuser_id($this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->orderby('id', 'desc')->paginate($limit);
    }    
    public function getTransactions()
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->latest()->get();
    }       
    public function getTransactionsCurrency($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->wherecurrency($id)->wheremode($this->business()->live)->latest()->get();
    }    
    public function getUniqueTransactions($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wheremode($this->business()->live)->latest()->get();
    }    
    public function getNoPayout()
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->where('type', '!=', 3)->latest()->get();
    }    
    public function getPayout()
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->wheremode($this->business()->live)->whereType(3)->latest()->get();
    }
    public function getPendingTransactions($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wherepending(1)->wheremode($this->business()->live)->latest()->sum('pending_amount');
    }        
    public function getBalance($id)
    {
        return Balance::where('user_id', $this->id)->wherebusiness_id($this->business_id)->where('country_id', $id)->first();
    }    
    public function getAllBalance()
    {
        return Balance::where('user_id', $this->id)->wherebusiness_id($this->business_id)->get();
    }    
    public function getFirstBalance()
    {
        return Balance::where('user_id', $this->id)->wherebusiness_id($this->business_id)->where('country_id', $this->country_id)->first();
    }    
    public function getLastTransaction($id)
    {
        return Transactions::where('receiver_id', $this->id)->wherebusiness_id($this->business_id)->where('currency', $id)->wheremode($this->business()->live)->orderby('id', 'desc')->first();
    }    
    public function getChargeBacks()
    {
        return Transactions::wherereceiver_id($this->id)->wherebusiness_id($this->business_id)->wheremode(1)->wherechargebacks(1)->latest()->get();
    }    
    public function getBeneficiary($country=null)
    {
        if($country==null){
            return beneficiary::whereuser_id($this->id)->wherebusiness_id($this->business_id)->latest()->get();
        }else{
            return beneficiary::whereuser_id($this->id)->wherebusiness_id($this->business_id)->wherecountry($country)->latest()->get();
        }
    }
}
