<?php
namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'username', 
        'password', 
        'profile',
        'support',
        'promo',
        'message',
        'deposit',
        'settlement',
        'transfer',
        'request_money',
        'donation',
        'single_charge',
        'subscription',
        'merchant',
        'invoice',
        'charges',
        'store',
        'blog'
    ];
    protected $guard = 'admin';

    protected $table = "admin";

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function pendingPayout()
    {
        return Transactions::wheremode(1)->wheretype(3)->count();
    }    
    
    public function unread()
    {
        return Contact::whereseen(0)->count();
    } 

    public function pticket()
    {
        return Ticket::where('status', 0)->get();
    }
}
