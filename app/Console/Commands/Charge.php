<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Curl\Curl;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Transactions;
use App\Jobs\SendPaymentEmail;
use App\Models\Settings;
use Carbon\Carbon;
use Stripe\StripeClient;

class Charge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for chargebacks every 1 hour';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings = Settings::find(1);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transactions = Transactions::wherestatus(0)->orwhere('status', '=', null)->where('type', '!=', 3)->wherepayment_type('bank')->wheremode(1)->get();
        foreach ($transactions as $val) {
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Basic ' . base64_encode($val->getCurrency->auth_key . ':' . $val->getCurrency->auth_secret));
            $curl->setHeader('Consent', $val->consent);
            $curl->setHeader('Content-Type', 'application/json');
            $curl->get("https:api.yapily.com/payments/" . $val->charge_id . "/details");
            $response = $curl->response;
            if(array_key_exists('data', (array)$response)){
                if ($response->data->payments[0]->status == "FAILED") {
                    $val->status = 2;
                    $val->save();
                } elseif ($response->data->payments[0]->status == "COMPLETED") {
                    $val->status = 1;
                    $val->save();
                    $balance = Balance::whereuser_id($val->receiver_id)->wherebusiness_id($val->business_id)->wherecountry_id($val->currency)->first();
                    $balance->amount = $balance->amount + $val->amount - $val->charge;
                    $balance->save();
                    //Save Audit Log
                    $audit = new Audit();
                    $audit->user_id = $val->receiver_id;
                    $audit->trx = $val->ref_id;
                    if ($val->type == 2) {
                        $audit->log = 'Received test payment ' . $val->api->ref_id;
                    } else {
                        $audit->log = 'Received test payment ' . $val->link->ref_id;
                    }
                    $audit->save();
                    if ($val->type == 1) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($val->link->ref_id, $val->ref_id));
                        }
                        //Send Webhook
                        if ($val->link->user->business()->receive_webhook == 1) {
                            if ($val->link->user->business()->webhook != null) {
                                send_webhook($val->ref_id);
                            }
                        }
                    } else {
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($val->api->ref_id, $val->ref_id));
                        }
                        //Send Webhook
                        if ($val->api->user->business()->receive_webhook == 1) {
                            if ($val->api->user->business()->webhook != null) {
                                send_webhook($val->ref_id);
                            }
                        }
                    }
                }
            }
        }
        $this->info('Bank transaction updated!!!');
        $transactions = Transactions::wherestatus(0)->wheretype(3)->get();
        foreach ($transactions as $val) {
            if ($val->next_settlement < Carbon::now()) {
                $val->next_settlement = nextPayoutDate($val->getCurrency->duration);
                $val->save();
            }
        }
        $this->info('Settlement updated!!!');
        $transactions = Transactions::wherestatus(0)->where('type', '!=', 3)->get();
        foreach ($transactions as $val) {
            $diff = Carbon::parse($val->created_at)->diffInHours(Carbon::now());
            if ($diff > 1 || $diff == 1) {
                $val->status = 2;
                $val->save();
            }
        }
        $this->info('Failed Transaction updated!!!');
        $transactions = Transactions::wherepending(1)->wherestatus(1)->wherepayment_type('card')->get();
        foreach ($transactions as $val) {
            $diff = Carbon::parse($val->created_at)->diffInDays(Carbon::parse($val->disburse_date));
            if ($diff > $val->getCurrency->pending_balance_duration || $diff == $val->getCurrency->pending_balance_duration) {
                $val->pending = 0;
                $val->save();
                $balance = Balance::whereuser_id($val->receiver_id)->wherebusiness_id($val->business_id)->wherecountry_id($val->currency)->first();
                $balance->amount = $balance->amount + $val->pending_amount;
                $balance->save();
            }
        }
        $this->info('Balance updated!!!');
        $stripe = new StripeClient($this->settings->secret_key);
        $dispute=$stripe->disputes->all();
        foreach($dispute as $val){
            if($val['status']=='lost'){
                $trx = Transactions::wherestatus(1)->where('type', '!=', 3)->wherepayment_type('card')->wherecharge_id($val['charge'])->wheremode(1)->get();
                $balance = Balance::whereuser_id($trx->receiver_id)->wherebusiness_id($trx->business_id)->wherecountry_id($trx->currency)->first();
                $balance->amount = $balance->amount - $trx->amount + $trx->charge;
                $balance->save();
                $trx->status = 4;
                $trx->save();
            }
        }
        $this->info('Chargeback updated!!!');
    }
}
