<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transactions;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Settings;
use App\Models\beneficiary;
use App\Jobs\SendEmail;
use App\Models\Virtual;
use App\Models\User;
use App\Models\Countrysupported;
use App\Jobs\SendPaymentEmail;
use App\Events\GenerateReceipt;
use PDF;
use Illuminate\Support\Facades\Log;
use Curl\Curl;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendWithdrawEmail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function transactions()
    {
        return view('user.transactions.index', ['title' => 'Transactions']);
    }    
    public function walletTransactions($country)
    {
        return view('user.transactions.history', ['title' => 'Transactions', 'trans' => User::find(auth()->guard('user')->user()->id)->getTransactionsCurrency($country)]);
    }    
    public function walletPayout(Countrysupported $country)
    {
        return view('user.transactions.transfer', ['title' => 'Payout', 'val' => $country]);
    }
    public function viewTransactions($id, $type)
    {
        $data = Transactions::whereref_id($id)->first();
        if ($data->type == 1) {
            $tt = "Payment";
        } elseif ($data->type == 2) {
            $tt = "API";
        } elseif ($data->type == 3) {
            $tt = "Payout";
        } elseif ($data->type == 4) {
            $tt = "Funding";
        } elseif ($data->type == 5) {
            $tt = "Swapping";
        }
        return view('user.transactions.view', ['title' => $tt, 'type' => $type, 'val' => Transactions::whereref_id($id)->first()]);
    }
    public function initiateRefund($id)
    {
        $data = Transactions::wheretrans_id($id)->first();
        $post = [
            'amount' => $data->amount
        ];
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post("https://api.flutterwave.com/v3/transactions/" . $id . "/refund", $post);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            if ($response != null) {
                return back()->with('alert', $response->message);
            } else {
                return back()->with('alert', 'An Error Occured');
            }
        } else {
            $data->refund_id = $response->data->id;
            $data->status = 3;
            $data->save();
            return back()->with('success', 'Refund will take 5 to 15 working days to process');
        }
    }

    public function chargeback()
    {
        $data['title'] = 'Charge Backs';
        return view('user.transactions.chargeback', $data);
    }

    public function goBack()
    {
        return view('user.merchant.popup.authenticate', ['title' => 'Redirect to Bank']);
    }

    public function generatereceipt($id)
    {
        $data['link'] = $transaction = Transactions::whereref_id($id)->first();
        if ($transaction->status == 1) {
            if ($transaction->type == 2) {
                $data['url'] = ($transaction->api->callback_url != null) ? $transaction->api->callback_url : null;
            }
            $data['title'] = "Receipt from " . $transaction->receiver->first_name . ' ' . $transaction->receiver->last_name;
            if($transaction->popup==1){
                Cache::forget('popup');
                Cache::forget('popup_url');
                Cache::forget('popup_previous_url');
                return view('user.merchant.popup.receipt', $data);
            }
            return view('user.transactions.receipt', $data);
        } else {
            if ($transaction->type == 2) {
                $data['url'] = ($transaction->api->callback_url != null) ? $transaction->api->callback_url : null;
            }
            $data['title'] = 'Error Message';
            $data['back'] = route('pop.checkout.url', ['id' => $transaction->api->ref_id]);
            if($transaction->popup==1){
                Cache::forget('popup');
                Cache::forget('popup_url');
                Cache::forget('popup_previous_url');
                return view('user.merchant.popup.failed', $data);
            }
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }

    public function downloadreceipt($id)
    {
        $data['link'] = $trans = Transactions::whereref_id($id)->first();
        if ($trans->status == 1) {
            $data['title'] = "Receipt from " . $trans->receiver->first_name . ' ' . $trans->receiver->last_name;
            $pdf = PDF::loadView('user.transactions.download', $data)->setPaper('a4');
            return $pdf->download($id . '.pdf');
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }

    public function withdrawSubmit(Request $request, $id)
    {
        $balance = Balance::whereref_id($id)->first();
        $country = getCountry($balance->country_id);
        $payout_type = explode('*', $request->payout_type);
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'required|integer|min:1|max:' . auth()->guard('user')->user()->getBalance($country->id)->amount,
            ]
        );
        if ($payout_type[0] == 1) {
            if ($country->bank_format == "us") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'routing_no' => 'required|string|max:9',
                    ]
                );
            }
            if ($country->bank_format == "eur") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'iban' => 'required|string|max:16',
                    ]
                );
            }
            if ($country->bank_format == "uk") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'acct_no' => 'required|string|max:8',
                        'sort_code' => 'required|string|max:6',
                    ]
                );
            }
            if ($country->bank_format == "normal") {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'bank_name' => 'required',
                        'acct_no' => 'required|string|max:10',
                        'acct_name' => 'required|string|max:255',
                    ]
                );
            }
        }
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $charge = $country->withdraw_fiat_charge + ($request->amount * $country->withdraw_percent_charge / 100);
        if ($request->amount > $charge || $request->amount == $charge) {
            $sav = new Transactions();
            $sav->ref_id = randomNumber(11);
            $sav->type = 3;
            $sav->mode = 1;
            $sav->amount = $request->amount - $charge;
            $sav->charge = $charge;
            $sav->payment_type = 'bank';
            $sav->ip_address = user_ip();
            $sav->currency = $balance->country_id;
            $sav->receiver_id = auth()->guard('user')->user()->id;
            $sav->business_id = auth()->guard('user')->user()->business_id;
            if ($payout_type[0] == 2) {
                $data = beneficiary::find($request->beneficiary);
                if ($country->bank_format == "us") {
                    $sav->routing_no = $data->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $sav->iban = $data->iban;
                } elseif ($country->bank_format == "uk") {
                    $sav->acct_no = $data->acct_no;
                    $sav->sort_code = $data->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $sav->bank_name = $data->bank_name;
                    $sav->acct_no = $data->acct_no;
                    $sav->acct_name = $data->acct_name;
                }
            } else {
                if ($country->bank_format == "us") {
                    $sav->routing_no = $request->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $sav->iban = $request->iban;
                } elseif ($country->bank_format == "uk") {
                    $sav->acct_no = $request->acct_no;
                    $sav->sort_code = $request->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $sav->bank_name = $request->bank_name;
                    $sav->acct_no = $request->acct_no;
                    $sav->acct_name = $request->acct_name;
                }
            }
            $sav->name = $request->name;
            $sav->next_settlement = nextPayoutDate($country->duration);
            $sav->save();
            //Balance
            $balance->amount = $balance->amount - $request->amount;
            $balance->save();
            //Save Audit Log
            $audit = new Audit();
            $audit->user_id = auth()->guard('user')->user()->id;
            $audit->trx = $sav->ref_id;
            $audit->log = 'Sent Payout request ' . $sav->ref_id;
            $audit->save();
            //Notify users
            if ($this->settings->email_notify == 1) {
                dispatch(new SendWithdrawEmail($sav->ref_id));
            }
            //Send Webhook
            if (auth()->guard('user')->user()->receive_webhook == 1) {
                if (auth()->guard('user')->user()->webhook != null) {
                    send_webhook($sav->ref_id);
                }
            }
            if (!empty($request->new_beneficiary)) {
                $data = new beneficiary();
                if ($country->bank_format == "us") {
                    $data->routing_no = $request->routing_no;
                } elseif ($country->bank_format == "eur") {
                    $data->iban = $request->iban;
                } elseif ($country->bank_format == "uk") {
                    $data->acct_no = $request->acct_no;
                    $data->sort_code = $request->sort_code;
                } elseif ($country->bank_format == "normal") {
                    $data->bank_name = $request->bank_name;
                    $data->acct_no = $request->acct_no;
                    $data->acct_name = $request->acct_name;
                }
                $data->business_id = auth()->guard('user')->user()->business_id;
                $data->user_id = auth()->guard('user')->user()->id;
                $data->country = $balance->country_id;
                $data->name = $request->name;
                $data->save();
            }
            return back()->with('success', 'Request submitted');
        } else {
            return back()->with('alert', 'Charge can\'t be greater than amount');
        }
    }
    public function Fundaccount($id, $type = null)
    {
        $data['link'] = $link = Balance::whereref_id($id)->first();
        $data['title'] = 'Fund account';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->user->kyc_status != "DECLINED") {
                return view('user.transactions.fund', $data);
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }
    public function verifyMobileMoney($id)
    {
        $transaction = Transactions::whereRefId($id)->firstOrFail();
        if ($transaction->status == 1) {
            if ($transaction->type == 2) {
                if ($transaction->api->callback_url != null) {
                    return redirect()->away($transaction->api->callback_url . '?tx_ref=' . $transaction->api->tx_ref);
                }
                return redirect()->route('generate.receipt', ['id' => $id])->with('success', 'Transaction verified');
            }
            return redirect()->route('generate.receipt', ['id' => $id])->with('success', 'Transaction verified');
        } else {
            return back()->with('alert', 'Transaction not verified');
        }
    }
    public function popupMobile($id)
    {
        return view('user.merchant.popup.mobile', ['title' => 'Pending OTP', 'ref_id' => $id]);
    }
    public function normalMobile($id)
    {
        return view('user.transactions.mobile', ['title' => 'Pending OTP', 'ref_id' => $id]);
    }
    public function webhook(Request $request)
    {
        $signature = $request->header('verif-hash');
        if (!$signature || ($signature !== $this->settings->secret_hash)) {
            //abort(401);
        };
        $payload = $request->all();
        Log::info($payload);
        if (array_key_exists('event', (array)$payload)) {
            if ($payload['event'] == "charge.completed") {
                if ($payload['data']['status'] == "successful") {
                    if($payload['data']['auth_model'] == "MOBILEMONEY"){
                        $sav = Transactions::whereRefId($payload['data']['tx_ref'])->first();
                        $sav->status = 1;
                        $sav->completed_at = Carbon::now();
                        $sav->save();
                        session::forget('trace_id');
                        session::forget('first_name');
                        session::forget('mobile');
                        session::forget('last_name');
                        session::forget('tx_ref');
                        session::forget('email');
                        $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                        $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        $balance->save();
                        $audit = new Audit();
                        $audit->user_id = $sav->receiver_id;
                        $audit->trx = $sav->ref_id;
                        $audit->log = 'Received test payment ' . $sav->link->ref_id;
                        $audit->save();
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                        }
                        if ($sav->link->user->business()->receive_webhook == 1) {
                            if ($sav->link->user->business()->webhook != null) {
                                send_webhook($sav->ref_id);
                            }
                        }
                        event(new GenerateReceipt($sav->ref_id));
                    }
                }
            }
        } else {
            Log::info($payload['Status']);
            $vcard = Virtual::wherecard_hash($payload['CardId'])->first();
            if ($payload['Status'] == 'Pending Auth') {
                if ($payload['Description'] == 'OTP') {
                    dispatch(new SendEmail($vcard->user->email, $vcard->user->first_name, 'Transaction OTP', "OTP code;" . $payload['Otp']));
                }
            }
            /*
            if ($payload['Description'] == 'CHARGE') {
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.flutterwave.com/v3/virtual-cards/" . $payload['CardId']);
                $curl->close();
                $vcard->amount = $payload['Balance'];
                $vcard->save();
                if ($curl->response->data->is_active == false) {
                    $vcard->status = 0;
                    $vcard->amount = 0;
                    $vcard->save();
                }
                $sav = new Virtualtransactions();
                $sav->user_id = $vcard->user_id;
                $sav->business_id = $vcard->business_id;
                $sav->amount = $payload['Amount'];
                $sav->description = "Charge";
                $sav->ref_id = $payload['TransactionId'];
                $sav->card_hash = $payload['CardId'];
                $sav->status = $payload['Status'];
                $sav->gate = $payload['MaskedPan'];
                if ($payload['Type'] == 'Credit') {
                    $sav->type = "Credit";
                } elseif ($payload['Type'] == 'Debit') {
                    $sav->type = "Debit";
                }
                $sav->save();
            }
            */
        }
        return response(200);
    }
}
