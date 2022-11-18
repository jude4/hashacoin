<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Balance;
use App\Models\Audit;
use App\Models\Paymentlink;
use App\Models\Transactions;
use App\Models\Exttransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPaymentEmail;
use Curl\Curl;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Redirect;
use Propaganistas\LaravelPhone\PhoneNumber;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    public function payment()
    {
        $data['title'] = "Payment";
        $data['status'] = 2;
        $data['limit'] = 6;
        $data['currency'] = 0;
        $data['links'] = auth()->guard('user')->user()->getPayment($data['limit']);
        if (count(auth()->guard('user')->user()->getPayment($data['limit'])) > 0) {
            $first = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wheremode(auth()->guard('user')->user()->business()->live)->orderby('created_at', 'desc')->first();
            $last = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wheremode(auth()->guard('user')->user()->business()->live)->orderby('created_at', 'asc')->first();
            $data['order'] = date("m/d/Y", strtotime($last->created_at)) . ' - ' . date("m/d/Y", strtotime($first->created_at));
        } else {
            $data['order'] = null;
        }

        return view('user.link.index', $data);
    }
    public function paymentSort(Request $request)
    {
        $data['title'] = "Payments";
        $data['status'] = $request->status;
        $data['limit'] = $request->limit;
        $data['order'] = $request->date;
        $data['currency'] = $request->currency;
        $date = explode('-', $request->date);
        $from = Carbon::create($date[0])->toDateString();
        $to = Carbon::create($date[1])->addDays(1)->toDateString();
        if ($request->status == "1" && $request->currency != "0") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->whereuser_id(auth()->guard('user')->user()->id)->whereactive(1)->wheremode(auth()->guard('user')->user()->live)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->whereuser_id(auth()->guard('user')->user()->id)->whereactive(1)->wheremode(auth()->guard('user')->user()->live)->paginate($data['limit']);
            }
        } elseif ($request->status == "0") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->wheremode(auth()->guard('user')->user()->live)->whereactive(0)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wheremode(auth()->guard('user')->user()->live)->whereactive(0)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            }
        } elseif ($request->status == "2") {
            if ($request->currency != "0") {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wherecurrency($request->currency)->wheremode(auth()->guard('user')->user()->live)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            } else {
                $data['links'] = Paymentlink::whereBetween('created_at', [$from, $to])->wheremode(auth()->guard('user')->user()->live)->whereuser_id(auth()->guard('user')->user()->id)->paginate($data['limit']);
            }
        }
        return view('user.link.index', $data);
    }
    public function paymentTransactions($id)
    {
        $data['title'] = "Transactions";
        $payment = Paymentlink::whereref_id($id)->first();
        $data['links'] = Transactions::wherepayment_link($payment->id)->latest()->get();
        return view('user.link.transactions', $data);
    }
    public function paymentShare($id)
    {
        $data['title'] = "New payment link";
        $data['link'] = Paymentlink::whereref_id($id)->first();
        return view('user.link.share', $data);
    }
    public function paymentPin($id)
    {
        $data['title'] = "Pin is required";
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            if ($data['link']->popup == 1) {
                return view('user.merchant.popup.pin', $data);
            }
            return view('user.card.pin', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function paymentAvs($id)
    {
        $data['title'] = "Address verification";
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            if ($data['link']->popup == 1) {
                return view('user.merchant.popup.avs', $data);
            }
            return view('user.card.avs', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function paymentOtp($id, $message)
    {
        $data['title'] = "OTP";
        $data['message'] = $message;
        $data['link'] = Transactions::whereref_id($id)->first();
        if ($data['link']->status == 0) {
            if ($data['link']->popup == 1) {
                return view('user.merchant.popup.otp', $data);
            }
            return view('user.card.otp', $data);
        } elseif ($data['link']->status == 2) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction was cancelled');
        } elseif ($data['link']->status == 1) {
            $data['title'] = 'Error Occured';
            return view('errors.error', $data)->withErrors('Transaction already paid');
        }
    }
    public function disablePayment($id)
    {
        $page = Paymentlink::whereref_id($id)->first();
        $page->active = 0;
        $page->save();
        return back()->with('success', 'Disabled');
    }
    public function enablePayment($id)
    {
        $page = Paymentlink::whereref_id($id)->first();
        $page->active = 1;
        $page->save();
        return back()->with('success', 'Enabled');
    }
    public function paymentCancel($id)
    {
        $data = Transactions::whereref_id($id)->first();
        $data->status = 2;
        $data->save();
        cardError($data->trace_id, "Cancelled Transaction", "log");
        session::forget('card_number');
        session::forget('expiry');
        session::forget('expiry_month');
        session::forget('expiry_year');
        session::forget('cvv');
        session::forget('first_name');
        session::forget('last_name');
        session::forget('tx_ref');
        session::forget('email');
        return redirect()->route('payment.link', ['id' => $data->link->ref_id])->with('success', 'Payment cancelled');
    }
    public function updatePayment(Request $request, $id)
    {
        $currency = explode('*', $request->currency);
        $link = getCountry($currency[0]);
        if ($link->max_amount != null) {
            $max = $link->max_amount;
        } else {
            $max = null;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $max,
                'description' => 'required|string',
                'name' => 'required|string|max:255'
            ]
        );
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $data = Paymentlink::whereref_id($id)->first();
        $data->amount = $request->amount;
        $data->description = $request->description;
        $data->name = $request->name;
        $data->currency = $currency[0];
        $data->save();
        return back()->with('success', 'Payment updated');
    }
    public function createPayment(Request $request)
    {
        $user = User::find(auth()->guard('user')->user()->id);
        $currency = explode('*', $request->currency);
        $link = getCountry($currency[0]);
        if ($link->max_amount != null) {
            $max = $link->max_amount;
        } else {
            $max = null;
        }
        $validator = Validator::make(
            $request->all(),
            [
                'amount' => 'max:' . $max,
                'description' => 'required|string',
                'name' => 'required|string|max:255'
            ]
        );
        if ($validator->fails()) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($validator->errors());
        }
        $data = new Paymentlink();
        $trx = 'SC-' . str_random(6);
        $data->ref_id = $trx;
        $data->amount = $request->amount;
        $data->name = $request->name;
        $data->description = $request->description;
        $data->user_id = Auth::guard('user')->user()->id;
        $data->business_id = Auth::guard('user')->user()->business_id;
        $data->mode = $user->business()->live;
        $data->currency = $currency[0];
        $data->save();
        $audit = new Audit();
        $audit->user_id = Auth::guard('user')->user()->id;
        $audit->trx = str_random(16);
        $audit->log = 'Created Payment Link - ' . $trx;
        $audit->save();
        return redirect()->route('payment.share', ['id' => $data->ref_id])->with('success', 'Payment added');
    }
    public function deletePayment($id)
    {
        $data = Paymentlink::whereref_id($id)->first();
        $data->delete();
        $transactions = Transactions::wherepayment_link($data->id)->get();
        foreach ($transactions as $val) {
            $val->delete();
        }
        return back()->with('success', 'Payment deleted!');
    }
    public function paymentLink($id, $type = null)
    {
        $data['link'] = $link = Paymentlink::whereref_id($id)->first();
        $data['title'] = 'Payment';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->status == 0) {
                if ($link->active == 1) {
                    return view(($link->mode == 1 && $link->user->business()->kyc_status != "DECLINED") ? 'user.link.live' : 'user.link.test', $data);
                } else {
                    return view('errors.error', ['title' => 'Error Message'])->withErrors('Payment link has been disabled');
                }
            } else {
                return view('errors.error', ['title' => 'Error Message'])->withErrors('Payment link has been suspended');
            }
        } else {
            return view('errors.error', ['title' => 'Error Message'])->withErrors('An Error Occured');
        }
    }
    public function paymentSubmit(Request $request, $id)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'crf' => 'required',
            ]
        );
        if ($validator->fails()) {
            return back()->with('alert', 'An error occured');
        }
        if ($request->crf == 1) {
            $link = Paymentlink::whereref_id($id)->first();
            $m_charge = ($request->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        } else if ($request->crf == 2) {
            $link = Exttransfer::whereref_id($id)->first();
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        } else {
            $link = Balance::whereref_id($id)->first();
            $m_charge = ($link->amount * $link->getCurrency->percent_charge / 100) + ($link->getCurrency->fiat_charge);
        }
        $receiver = User::whereid($link->user->id)->first();
        if ($link->getCurrency->max_amount != null) {
            $max = $link->getCurrency->max_amount;
        } else {
            $max = null;
        }
        if ($request->type == 'card') {
            $customMessages = [
                'stripeSource.unique' => 'Please fill in card details',
            ];
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'stripeSource' => 'required',
                    ],
                    $customMessages
                );
                if ($validator->fails()) {
                    return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'card'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'stripeSource' => 'required',
                    ],
                    $customMessages
                );
                if ($validator->fails()) {
                    if (Cache::get('popup') != null) {
                        return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'card'])->with('errors', $validator->errors());
                    }
                    return redirect()->route('checkout.url', ['id' => $link->ref_id, 'type' => 'card'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'stripeSource' => 'required',
                    ],
                    $customMessages
                );
                if ($validator->fails()) {
                    return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'card'])->with('errors', $validator->errors());
                }
            }
            if ($request->crf == 1) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 1;
                    $sav->mode = 1;
                    $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                    $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                    $sav->pending = ($link->getCurrency->pending_balance_duration != 0) ? 1 : 0;
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                cardError($sav->trace_id, "Attempted to pay with card", "log");
                //Store session
                session::put('first_name', $request->first_name);
                session::put('last_name', $request->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $request->email);
                $stripe = new StripeClient($this->settings->secret_key);
                try {
                    $data = $stripe->paymentIntents->create([
                        'amount' => ($sav->client == 1) ? number_format($request->amount + $m_charge, 2) * 100 : number_format($request->amount, 2) * 100,
                        'currency' => $link->getCurrency->real->currency,
                        'payment_method_types' => ['card'],
                        'description' => 'Payment link #' . $sav->ref_id,
                        'source' => $request->stripeSource,
                        'return_url' => route('webhook.card', ['id' => $sav->ref_id]),
                        'confirm' => true,
                    ]);
                    $source = $stripe->sources->retrieve($request->stripeSource);
                    $sav->card_country = strtolower($source['card']['country']);
                    $sav->save();
                    if ($data['status'] == "succeeded") {
                        cardError($sav->trace_id, "Successfully paid with card", "log");
                        $sav->status = 1;
                        $sav->completed_at = Carbon::now();
                        if ($sav->getCurrency->pending_balance_duration != 0 && $sav->type != 4) {
                            $sav->pending = 1;
                        }
                        $sav->completed_at = Carbon::now();
                        if ((new Agent())->isDesktop()) {
                            $sav->device = "tv";
                        }
                        if ((new Agent())->isMobile()) {
                            $sav->device = "mobile";
                        }
                        if ((new Agent())->isTablet()) {
                            $sav->device = "tablet";
                        }
                        $sav->trans_id = $data['id'];
                        $sav->card_number = strtolower($source['card']['last4']);
                        $sav->card_type = strtolower($source['card']['brand']);
                        $sav->save();
                        session::forget('trace_id');
                        session::forget('first_name');
                        session::forget('last_name');
                        session::forget('tx_ref');
                        session::forget('email');
                        $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                        if ($sav->pending == 1) {
                            $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                            $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                            $sav->save();
                        } else {
                            $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        }
                        $balance->save();
                        //Save Audit Log
                        $audit = new Audit();
                        $audit->user_id = $sav->receiver_id;
                        $audit->trx = $sav->ref_id;
                        if ($sav->type == 2) {
                            $audit->log = 'Received test payment ' . $sav->api->ref_id;
                        } elseif ($sav->type == 1) {
                            $audit->log = 'Received test payment ' . $sav->link->ref_id;
                        } elseif ($sav->type == 4) {
                            $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                        }
                        $audit->save();
                        if ($sav->type == 1) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->link->user->business()->receive_webhook == 1) {
                                if ($sav->link->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } elseif ($sav->type == 4) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->balance->user->business()->receive_webhook == 1) {
                                if ($sav->balance->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } else {
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->api->user->business()->receive_webhook == 1) {
                                if ($sav->api->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        }
                        if ($sav->popup == 1) {
                            return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                        } else {
                            return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                        }
                    } elseif ($data['status'] == "requires_action") {
                        return Redirect::away($data['next_action']['redirect_to_url']['url']);
                    } else {
                        return back()->with('alert', $data['error']['message']);
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    return back()->with('alert', $e->getMessage());
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    return back()->with('alert', $e->getMessage());
                }
            } else if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                    $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                    $sav->pending = ($link->getCurrency->pending_balance_duration != 0) ? 1 : 0;
                    $sav->popup = (Cache::get('popup') != null) ? 1 : 0;
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                cardError($sav->trace_id, "Attempted to pay with card", "log");
                //Store session
                session::put('first_name', $link->first_name);
                session::put('last_name', $link->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $link->email);
                $stripe = new StripeClient($this->settings->secret_key);
                try {
                    $data = $stripe->paymentIntents->create([
                        'amount' => ($sav->client == 1) ? number_format($link->amount + $m_charge, 2) * 100 : number_format($link->amount, 2) * 100,
                        'currency' => $link->getCurrency->real->currency,
                        'payment_method_types' => ['card'],
                        'description' => 'Payment link #' . $sav->ref_id,
                        'source' => $request->stripeSource,
                        'return_url' => route('webhook.card', ['id' => $sav->ref_id]),
                        'confirm' => true,
                    ]);
                    $source = $stripe->sources->retrieve($request->stripeSource);
                    $sav->card_country = strtolower($source['card']['country']);
                    $sav->save();
                    if ($data['status'] == "succeeded") {
                        cardError($sav->trace_id, "Successfully paid with card", "log");
                        $sav->status = 1;
                        $sav->completed_at = Carbon::now();
                        if ($sav->getCurrency->pending_balance_duration != 0 && $sav->type != 4) {
                            $sav->pending = 1;
                        }
                        $sav->completed_at = Carbon::now();
                        if ((new Agent())->isDesktop()) {
                            $sav->device = "tv";
                        }
                        if ((new Agent())->isMobile()) {
                            $sav->device = "mobile";
                        }
                        if ((new Agent())->isTablet()) {
                            $sav->device = "tablet";
                        }
                        $sav->trans_id = $data['id'];
                        $sav->card_number = strtolower($source['card']['last4']);
                        $sav->card_type = strtolower($source['card']['brand']);
                        $sav->save();
                        session::forget('trace_id');
                        session::forget('first_name');
                        session::forget('last_name');
                        session::forget('tx_ref');
                        session::forget('email');
                        $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                        if ($sav->pending == 1) {
                            $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                            $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                            $sav->save();
                        } else {
                            $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        }
                        $balance->save();
                        //Save Audit Log
                        $audit = new Audit();
                        $audit->user_id = $sav->receiver_id;
                        $audit->trx = $sav->ref_id;
                        if ($sav->type == 2) {
                            $audit->log = 'Received test payment ' . $sav->api->ref_id;
                        } elseif ($sav->type == 1) {
                            $audit->log = 'Received test payment ' . $sav->link->ref_id;
                        } elseif ($sav->type == 4) {
                            $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                        }
                        $audit->save();
                        if ($sav->type == 1) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->link->user->business()->receive_webhook == 1) {
                                if ($sav->link->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } elseif ($sav->type == 4) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->balance->user->business()->receive_webhook == 1) {
                                if ($sav->balance->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } else {
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->api->user->business()->receive_webhook == 1) {
                                if ($sav->api->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        }
                        return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);

                    } elseif ($data['status'] == "requires_action") {
                        if($sav->popup==1){
                            Cache::forget('popup');
                            return view('user.merchant.popup.authenticate', ['title' => 'Authenticate', 'url' => $data['next_action']['redirect_to_url']['url']]);
                        }
                        return Redirect::away($data['next_action']['redirect_to_url']['url']);
                    } else {
                        $sav->status = 2;
                        $sav->save();
                        if ($sav->popup == 1) {
                            return view('user.merchant.popup.goback', ['title' => 'Payment failed', 'ref' => $sav->ref_id, 'url' => null]);
                        }else{
                            return back()->with('alert', $data['error']['message']);
                        }
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    return back()->with('alert', $e->getMessage());
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    return back()->with('alert', $e->getMessage());
                }
            } else if ($request->crf == 3) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 4;
                    $sav->mode = 1;
                    $sav->client = 1;
                    $sav->amount = $request->amount + $m_charge;
                    $sav->charge = $m_charge;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'card';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->country_id;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = $request->amount + $m_charge;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                cardError($sav->trace_id, "Attempted to pay with card", "log");
                session::put('tx_ref', $sav->ref_id);
                $stripe = new StripeClient($this->settings->secret_key);
                try {
                    $data = $stripe->paymentIntents->create([
                        'amount' => ($sav->client == 1) ? number_format($request->amount + $m_charge, 2) * 100 : number_format($request->amount, 2) * 100,
                        'currency' => $link->getCurrency->real->currency,
                        'payment_method_types' => ['card'],
                        'description' => 'Payment link #' . $sav->ref_id,
                        'source' => $request->stripeSource,
                        'return_url' => route('webhook.card', ['id' => $sav->ref_id]),
                        'confirm' => true,
                    ]);
                    $source = $stripe->sources->retrieve($request->stripeSource);
                    $sav->card_country = strtolower($source['card']['country']);
                    $sav->save();
                    if ($data['status'] == "succeeded") {
                        cardError($sav->trace_id, "Successfully paid with card", "log");
                        $sav->status = 1;
                        $sav->completed_at = Carbon::now();
                        if ($sav->getCurrency->pending_balance_duration != 0 && $sav->type != 4) {
                            $sav->pending = 1;
                        }
                        $sav->completed_at = Carbon::now();
                        if ((new Agent())->isDesktop()) {
                            $sav->device = "tv";
                        }
                        if ((new Agent())->isMobile()) {
                            $sav->device = "mobile";
                        }
                        if ((new Agent())->isTablet()) {
                            $sav->device = "tablet";
                        }
                        $sav->trans_id = $data['id'];
                        $sav->card_number = strtolower($source['card']['last4']);
                        $sav->card_type = strtolower($source['card']['brand']);
                        $sav->save();
                        session::forget('trace_id');
                        session::forget('first_name');
                        session::forget('last_name');
                        session::forget('tx_ref');
                        session::forget('email');
                        $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                        if ($sav->pending == 1) {
                            $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                            $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                            $sav->save();
                        } else {
                            $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                        }
                        $balance->save();
                        //Save Audit Log
                        $audit = new Audit();
                        $audit->user_id = $sav->receiver_id;
                        $audit->trx = $sav->ref_id;
                        if ($sav->type == 2) {
                            $audit->log = 'Received test payment ' . $sav->api->ref_id;
                        } elseif ($sav->type == 1) {
                            $audit->log = 'Received test payment ' . $sav->link->ref_id;
                        } elseif ($sav->type == 4) {
                            $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                        }
                        $audit->save();
                        if ($sav->type == 1) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->link->user->business()->receive_webhook == 1) {
                                if ($sav->link->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } elseif ($sav->type == 4) {
                            //Notify users
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->balance->user->business()->receive_webhook == 1) {
                                if ($sav->balance->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        } else {
                            if ($this->settings->email_notify == 1) {
                                dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                            }
                            //Send Webhook
                            if ($sav->api->user->business()->receive_webhook == 1) {
                                if ($sav->api->user->business()->webhook != null) {
                                    send_webhook($sav->ref_id);
                                }
                            }
                        }
                        if ($sav->popup == 1) {
                            return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                        } else {
                            return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                        }
                    } elseif ($data['status'] == "requires_action") {
                        return Redirect::away($data['next_action']['redirect_to_url']['url']);
                    } else {
                        return back()->with('alert', $data['error']['message']);
                    }
                } catch (\Stripe\Exception\CardException $e) {
                    return back()->with('alert', $e->getMessage());
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    return back()->with('alert', $e->getMessage());
                }
            }
        }
        if ($request->type == 'test') {
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'status' => 'required',
                    ]
                );
            } else {
                if (getTransaction($link->id, $link->user_id) != null) {
                    return back()->with('alert', 'Session expired');
                }
                $validator = Validator::make(
                    $request->all(),
                    [
                        'status' => 'required',
                    ],
                    [
                        'status.required' => 'Please select a transaction status',
                    ]
                );
            }
            if ($validator->fails()) {
                return back()->with('errors', $validator->errors());
            }
            if ($request->crf == 1) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 1;
                $sav->mode = 0;
                $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                $sav->charge = $m_charge;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                $sav->save();
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherebusiness_id($link->business_id)->wherecountry_id($link->getCurrency->id)->first();
                $balance->test = $balance->test + $request->amount - $m_charge;
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->business()->receive_webhook == 1) {
                    if ($link->user->business()->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    if ($sav->popup == 1) {
                        return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                    } else {
                        return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                    }
                } else {
                    return back()->with('alert', 'Payment failed');
                }
            } else {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 0;
                $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                $sav->popup = (Cache::get('popup') != null) ? 1 : 0;
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'test';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                $sav->save();
                //Balance
                $balance = Balance::whereuser_id($link->user->id)->wherebusiness_id($link->business_id)->wherecountry_id($link->getCurrency->id)->first();
                $balance->test = $balance->test + $link->amount - $m_charge;
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $link->user->id;
                $audit->trx = $sav->ref_id;
                $audit->log = 'Received test payment ' . $link->ref_id;
                $audit->save();
                //Notify users
                if ($this->settings->email_notify == 1) {
                    dispatch(new SendPaymentEmail($link->ref_id, $sav->ref_id));
                }
                //Send Webhook
                if ($link->user->business()->receive_webhook == 1) {
                    if ($link->user->business()->webhook != null) {
                        send_webhook($sav->ref_id);
                    }
                }
                if ($request->status == 1) {
                    if ($link->callback_url != null) {
                        if ($sav->popup == 1) {
                            Cache::forget('popup');
                            return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => $link->callback_url . '?tx_ref=' . $link->tx_ref]);
                        }
                        return redirect()->away($link->callback_url . '?tx_ref=' . $link->tx_ref);
                    }
                    if ($sav->popup == 1) {
                        Cache::forget('popup');
                        return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                    }
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                } else {
                    return back()->with('alert', 'Payment failed');
                }
            }
        }
        if ($request->type == 'bank') {
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 2) {
                if ($validator->fails()) {
                    if (Cache::get('popup') != null) {
                        return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('errors', $validator->errors());
                    }
                    return redirect()->route('checkout.url', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('errors', $validator->errors());
                }
            }
            if ($request->crf == 1) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 1;
                $sav->mode = 1;
                $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                $sav->charge = $m_charge;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                $sav->save();
                $authToken = base64_encode($link->getCurrency->auth_key . ':' . $link->getCurrency->auth_secret);
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Basic ' . $authToken);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.yapily.com/institutions");
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if (empty($response)) {
                        return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', "An error occured");
                    }
                    return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 1;
                    $data['reference'] = $sav->ref_id;
                    return view('user.dashboard.institution', $data);
                }
            } else if ($request->crf == 2) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 2;
                $sav->mode = 1;
                $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                $sav->popup = (Cache::get('popup') != null) ? 1 : 0;
                $sav->charge = $m_charge;
                $sav->email = $link->email;
                $sav->first_name = $link->first_name;
                $sav->last_name = $link->last_name;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
                $sav->ip_address = user_ip();
                $sav->currency = $link->currency;
                $sav->status = $request->status;
                $sav->save();
                $authToken = base64_encode($link->getCurrency->auth_key . ':' . $link->getCurrency->auth_secret);
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Basic ' . $authToken);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.yapily.com/institutions");
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if (empty($response)) {
                        return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', "An error occured");
                    }
                    return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 2;
                    $data['reference'] = $sav->ref_id;
                    if ($sav->popup == 1) {
                        Cache::forget('popup');
                        return view('user.merchant.popup.institution', $data);
                    }
                    return view('user.dashboard.institution', $data);
                }
            } else if ($request->crf == 3) {
                $sav = new Transactions();
                $sav->ref_id = randomNumber(11);
                $sav->type = 4;
                $sav->mode = 1;
                $sav->amount = $request->amount + $m_charge;
                $sav->client = 1;
                $sav->charge = $m_charge;
                $sav->receiver_id = $link->user_id;
                $sav->business_id = $link->business_id;
                $sav->payment_link = $link->id;
                $sav->payment_type = 'bank';
                $sav->ip_address = user_ip();
                $sav->currency = $link->country_id;
                $sav->status = $request->status;
                $sav->save();
                $authToken = base64_encode($link->getCurrency->auth_key . ':' . $link->getCurrency->auth_secret);
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Basic ' . $authToken);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->get("https://api.yapily.com/institutions");
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if (empty($response)) {
                        return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', "An error occured");
                    }
                    return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'bank_account'])->with('alert', $response->error->status . '-' . $response->error->message);
                } else {
                    $data['authtoken'] = $authToken;
                    $data['institution'] = $response->data;
                    $data['title'] = 'Select Preferred Bank';
                    $data['type'] = 2;
                    $data['reference'] = $sav->ref_id;
                    return view('user.dashboard.institution', $data);
                }
            }
        }
        if ($request->type == 'mobile_money') {
            if ($request->crf == 1) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email',
                        'mobile' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 2) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'mobile' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    if (Cache::get('popup') != null) {
                        return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('errors', $validator->errors());
                    }
                    return redirect()->route('checkout.url', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('errors', $validator->errors());
                }
            } else if ($request->crf == 3) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'amount' => 'required|integer|min:' . $link->getCurrency->min_amount . '|max:' . $max,
                        'mobile' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('errors', $validator->errors());
                }
            }
            $mobile = $request->mobile;
            if ($request->crf == 1) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 1;
                    $sav->mode = 1;
                    $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                    $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'mobile';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                    $sav->charge = $m_charge;
                    $sav->email = $request->email;
                    $sav->mobile = $mobile;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                session::put('mobile', $request->mobile);
                session::put('first_name', $request->first_name);
                session::put('last_name', $request->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $request->email);
                $data = [
                    'amount' => ($sav->client == 1) ? $request->amount + $m_charge : $request->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $request->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $request->first_name . ' ' . $request->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => route('webhook.card', ['id' => $sav->ref_id])
                ];
                if ($link->getCurrency->real->currency == "RWF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_rwanda";
                } else if ($link->getCurrency->real->currency == "KES") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mpesa";
                } else if ($link->getCurrency->real->currency == "ZMW") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_zambia";
                } else if ($link->getCurrency->real->currency == "UGX") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_uganda";
                } else if ($link->getCurrency->real->currency == "GHS") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_ghana";
                } else if ($link->getCurrency->real->currency == "ZAR") {
                    $url = "https://api.flutterwave.com/v3/charges?type=ach_payment";
                } else if ($link->getCurrency->real->currency == "XAF" || $link->getCurrency->real->currency == "XOF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_franco";
                }
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post($url, $data);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', $response->message);
                    } else {
                        return redirect()->route('payment.link', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', 'An Error Occured');
                    }
                } else {
                    if (array_key_exists('redirect', (array)$response->meta->authorization) && $response->meta->authorization->redirect != null) {
                        return redirect()->away($response->meta->authorization->redirect);
                    } else {
                        if ($response->data->status == "successful") {
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
                            if ($sav->popup == 1) {
                                return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                            } else {
                                return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                            }
                        } else if ($response->data->status == "pending") {
                            return redirect()->route('normal.mobile', ['id' => $sav->ref_id]);
                        }
                    }
                }
            } else if ($request->crf == 2) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 2;
                    $sav->mode = 1;
                    $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                    $sav->client = ($link->business()->charges == 1) ? 1 : 0;
                    $sav->popup = (Cache::get('popup') != null) ? 1 : 0;
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'mobile';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->currency;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount;
                    $sav->charge = $m_charge;
                    $sav->email = $link->email;
                    $sav->mobile = $request->mobile;
                    $sav->first_name = $link->first_name;
                    $sav->last_name = $link->last_name;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                session::put('mobile', $link->mobile);
                session::put('first_name', $link->first_name);
                session::put('last_name', $link->last_name);
                session::put('tx_ref', $sav->ref_id);
                session::put('email', $link->email);
                $data = [
                    'amount' => ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $link->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $link->first_name . ' ' . $link->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => route('webhook.card', ['id' => $sav->ref_id])
                ];
                if ($link->getCurrency->real->currency == "RWF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_rwanda";
                } else if ($link->getCurrency->real->currency == "KES") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mpesa";
                } else if ($link->getCurrency->real->currency == "ZMW") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_zambia";
                } else if ($link->getCurrency->real->currency == "UGX") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_uganda";
                } else if ($link->getCurrency->real->currency == "GHS") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_ghana";
                } else if ($link->getCurrency->real->currency == "ZAR") {
                    $url = "https://api.flutterwave.com/v3/charges?type=ach_payment";
                } else if ($link->getCurrency->real->currency == "XAF" || $link->getCurrency->real->currency == "XOF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_franco";
                }
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post($url, $data);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        if ($sav->popup == 1) {
                            return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', $response->message);
                        }
                        return back()->with('alert', $response->message);
                    } else {
                        return redirect()->route('pop.checkout.url', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', 'An Error Occured');
                    }
                } else {
                    //dd($response);
                    if (array_key_exists('redirect', (array)$response->meta->authorization) && $response->meta->authorization->redirect != null) {
                        if ($sav->popup == 1) {
                            Cache::forget('popup');
                            return view('user.merchant.popup.authenticate', ['title' => 'Authenticate', 'url' => $response->meta->authorization->redirect]);
                        }
                        return redirect()->away($response->meta->authorization->redirect);
                    } else {
                        if ($response->data->status == "successful") {
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
                            if ($sav->popup == 1) {
                                return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                            } else {
                                return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                            }
                        } else if ($response->data->status == "pending") {
                            if ($sav->popup == 1) {
                                Cache::forget('popup');
                                return redirect()->route('popup.mobile', ['id' => $sav->ref_id]);
                            }
                        }
                    }
                }
            } else if ($request->crf == 3) {
                $check_card = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->count();
                if ($check_card == 0) {
                    $sav = new Transactions();
                    $sav->ref_id = randomNumber(11);
                    $sav->type = 4;
                    $sav->mode = 1;
                    $sav->amount = $request->amount + $m_charge;
                    $sav->client = 1;
                    $sav->charge = $m_charge;
                    $sav->mobile = $request->mobile;
                    $sav->receiver_id = $link->user_id;
                    $sav->business_id = $link->business_id;
                    $sav->payment_link = $link->id;
                    $sav->payment_type = 'mobile';
                    $sav->attempts = 1;
                    $sav->ip_address = $request->ip();
                    $sav->currency = $link->country_id;
                    $sav->trace_id = session('trace_id');
                    $sav->save();
                } else {
                    $sav = Transactions::wheretrace_id(session('trace_id'))->wherepayment_link($link->id)->first();
                    if ($sav->status == 1) {
                        return back()->with('alert', 'Session has expired for last transaction, please try again');
                    }
                    $sav->amount = ($link->business()->charges == 1) ? $request->amount + $m_charge : $request->amount;
                    $sav->charge = $m_charge;
                    $sav->mobile = $request->mobile;
                    $sav->attempts = $sav->attempts + 1;
                    $sav->save();
                }
                session::put('mobile', $link->mobile);
                session::put('tx_ref', $sav->ref_id);
                $data = [
                    'amount' => ($link->business()->charges == 1) ? $link->amount + $m_charge : $link->amount,
                    'currency' => $link->getCurrency->real->currency,
                    'email' => $receiver->email,
                    'phone_number' => $request->mobile,
                    'fullname' => $receiver->first_name . ' ' . $receiver->last_name,
                    'tx_ref' => $sav->ref_id,
                    'redirect_url' => route('webhook.card', ['id' => $sav->ref_id])
                ];
                if ($link->getCurrency->real->currency == "RWF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_rwanda";
                } else if ($link->getCurrency->real->currency == "KES") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mpesa";
                } else if ($link->getCurrency->real->currency == "ZMW") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_zambia";
                } else if ($link->getCurrency->real->currency == "UGX") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_uganda";
                } else if ($link->getCurrency->real->currency == "GHS") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_ghana";
                } else if ($link->getCurrency->real->currency == "ZAR") {
                    $url = "https://api.flutterwave.com/v3/charges?type=ach_payment";
                } else if ($link->getCurrency->real->currency == "XAF" || $link->getCurrency->real->currency == "XOF") {
                    $url = "https://api.flutterwave.com/v3/charges?type=mobile_money_franco";
                }
                $curl = new Curl();
                $curl->setHeader('Authorization', 'Bearer ' . $this->settings->secret_key);
                $curl->setHeader('Content-Type', 'application/json');
                $curl->post($url, $data);
                $response = $curl->response;
                $curl->close();
                if ($curl->error) {
                    if ($response != null) {
                        return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', $response->message);
                    } else {
                        return redirect()->route('fund.account', ['id' => $link->ref_id, 'type' => 'mobile_money'])->with('alert', 'An Error Occured');
                    }
                } else {
                    if (array_key_exists('redirect', (array)$response->meta->authorization) && $response->meta->authorization->redirect != null) {
                        return redirect()->away($response->meta->authorization->redirect);
                    } else {
                        if ($response->data->status == "successful") {
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
                            if ($sav->popup == 1) {
                                return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                            } else {
                                return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                            }
                        } else if ($response->data->status == "pending") {
                            return redirect()->route('normal.mobile', ['id' => $sav->ref_id]);
                        }
                    }
                }
            }
        }
    }
    public function authorize_payment($auth_token, $bank_id, $trans_type, $reference)
    {
        $transaction = Transactions::whereref_id($reference)->first();
        if ($transaction->getCurrency->real->currency == "GBP") {
            $bank_array = [
                [
                    'type' => "ACCOUNT_NUMBER",
                    'identification' => $transaction->getCurrency->acct_no,
                ], [
                    'type' => "SORT_CODE",
                    'identification' => $transaction->getCurrency->sort_code,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "USD") {
            $bank_array = [
                [
                    'type' => "ROUTING_NUMBER",
                    'identification' => $transaction->getCurrency->routing_no,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "EUR") {
            $bank_array = [
                [
                    'type' => "IBAN",
                    'identification' => $transaction->getCurrency->iban,
                ]
            ];
        }
        if ($transaction->type == 1) {
            $d_reference = "Payment";
        } elseif ($transaction->type == 2) {
            $d_reference = "API";
        } elseif ($transaction->type == 4) {
            $d_reference = "FUNDING";
        }
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Basic ' . $auth_token);
        $curl->setHeader('Content-Type', 'application/json');
        $data = [
            'applicationUserId' => $transaction->receiver->email,
            'institutionId' => $bank_id,
            'callback' => route('bankcallback'),
            'paymentRequest' => [
                'type' => "DOMESTIC_PAYMENT",
                'reference' => $d_reference,
                'paymentIdempotencyId' => $reference,
                'amount' => [
                    'amount' => number_format($transaction->amount, 2, '.', ''),
                    'currency' => $transaction->getCurrency->real->currency,
                ],
                'payee' => [
                    'name' => $transaction->getCurrency->first_name . ' ' . $transaction->getCurrency->last_name,
                    'accountIdentifications' => $bank_array,
                ],
            ],
        ];
        //dd($data);
        $curl->post("https://api.yapily.com/payment-auth-requests", $data);
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
        } else {
            $transaction->tracing_id = $response->meta->tracingId;
            $transaction->save();
            Session::put('trans', $transaction->ref_id);
            if ($transaction->popup == 1) {
                Cache::put('popup_url', $response->data->authorisationUrl);
                Cache::put('popup_previous_url', route('pop.checkout.url', ['id' => $transaction->api->ref_id]));
                //return redirect()->route('go.back');
                //return view('user.merchant.popup.authenticate', ['title' => 'Redirect to Bank']);
                return view('user.merchant.popup.gobank', ['title' => 'Bank App', 'ref' => $transaction->api->ref_id, 'url' => null]);
            }
            return Redirect::away($response->data->authorisationUrl);
        }
    }
    public function bankcallback(Request $request)
    {
        $transaction = Transactions::whereref_id(Session('trans'))->firstOrFail();
        if ($transaction->getCurrency->real->currency == "GBP") {
            $bank_array = [
                [
                    'type' => "ACCOUNT_NUMBER",
                    'identification' => $transaction->getCurrency->acct_no,
                ], [
                    'type' => "SORT_CODE",
                    'identification' => $transaction->getCurrency->sort_code,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "USD") {
            $bank_array = [
                [
                    'type' => "ROUTING_NUMBER",
                    'identification' => $transaction->getCurrency->routing_no,
                ]
            ];
        } elseif ($transaction->getCurrency->real->currency == "EUR") {
            $bank_array = [
                [
                    'type' => "IBAN",
                    'identification' => $transaction->getCurrency->iban,
                ]
            ];
        }
        if (!empty($request->consent)) {
            if ($transaction->type == 1) {
                $d_reference = "Payment";
            } elseif ($transaction->type == 2) {
                $d_reference = "API";
            } elseif ($transaction->type == 4) {
                $d_reference = "FUNDING";
            }
            $curl = new Curl();
            $curl->setHeader('Authorization', 'Basic ' . base64_encode($transaction->getCurrency->auth_key . ':' . $transaction->getCurrency->auth_secret));
            $curl->setHeader('Consent', $request->consent);
            $curl->setHeader('Content-Type', 'application/json');
            $data = [
                'type' => "DOMESTIC_PAYMENT",
                'reference' => $d_reference,
                'paymentIdempotencyId' => $transaction->ref_id,
                'amount' => [
                    'amount' => number_format($transaction->amount, 2, '.', ''),
                    'currency' => $transaction->getCurrency->real->currency,
                ],
                'payee' => [
                    'name' => $transaction->getCurrency->first_name . ' ' . $transaction->getCurrency->last_name,
                    'accountIdentifications' => $bank_array,
                ],
            ];
            $curl->post("https://api.yapily.com/payments", $data);
            $response = $curl->response;
            $curl->close();
            if ($curl->error) {
                $data['title'] = 'Error Message';
                return view('errors.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
            } else {
                $transaction->charge_id = $response->data->id;
                $transaction->consent = $request->consent;
                $transaction->tracing_id = $response->meta->tracingId;
                $transaction->save();
                if ($response->data->status == "PENDING") {
                    if ($transaction->type == 1) {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id])->with('alert', 'Payment might be pending due to bank service. Please allow up to 2 hours for the settling bank to return a successful or failed transaction');
                    } else {
                        if ($transaction->popup == 1) {
                            return view('user.merchant.popup.goback', ['title' => 'Payment pending', 'ref' => $transaction->ref_id, 'url' => null]);
                        }else{
                            return view('errors.error', ['title' => 'Error Message'])->withErrors('Payment failed');
                        }
                    }
                } elseif ($response->data->status == "FAILED") {
                    $transaction->status = 2;
                    $transaction->save();
                    if ($transaction->type == 1) {
                        return redirect()->route('payment.link', ['id' => $transaction->link->ref_id])->with('alert', 'Payment Failed');
                    } else {
                        if ($transaction->popup == 1) {
                            return view('user.merchant.popup.goback', ['title' => 'Payment failed', 'ref' => $transaction->ref_id, 'url' => null]);
                        }else{
                            return view('errors.error', ['title' => 'Error Message'])->withErrors('Payment failed');
                        }
                    }
                } elseif ($response->data->status == "COMPLETED") {
                    $transaction->status = 1;
                    $transaction->save();
                    $balance = Balance::whereuser_id($transaction->receiver_id)->wherebusiness_id($transaction->business_id)->wherecountry_id($transaction->currency)->first();
                    $balance->amount = $balance->amount + $transaction->amount - $transaction->charge;
                    $balance->save();
                    //Save Audit Log
                    $audit = new Audit();
                    $audit->user_id = $transaction->receiver_id;
                    $audit->trx = $transaction->ref_id;
                    if ($transaction->type == 2) {
                        $audit->log = 'Received test payment ' . $transaction->api->ref_id;
                    } else {
                        $audit->log = 'Received test payment ' . $transaction->link->ref_id;
                    }
                    $audit->save();
                    if ($transaction->type == 1) {
                        //Notify users
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($transaction->link->ref_id, $transaction->ref_id));
                        }
                        //Send Webhook
                        if ($transaction->link->user->business()->receive_webhook == 1) {
                            if ($transaction->link->user->business()->webhook != null) {
                                send_webhook($transaction->ref_id);
                            }
                        }
                    } else {
                        if ($this->settings->email_notify == 1) {
                            dispatch(new SendPaymentEmail($transaction->api->ref_id, $transaction->ref_id));
                        }
                        //Send Webhook
                        if ($transaction->api->user->business()->receive_webhook == 1) {
                            if ($transaction->api->user->business()->webhook != null) {
                                send_webhook($transaction->ref_id);
                            }
                        }
                    }
                    if ($transaction->popup == 1) {
                        return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $transaction->ref_id, 'url' => null]);
                    } else {
                        return redirect()->route('generate.receipt', ['id' => $transaction->ref_id]);
                    }
                }
            }
        } else {
            $transaction->status = 2;
            $transaction->save();
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors("Sorry but your payment was cancelled. <a href=" . route('bankrecall', ['id' => $transaction->ref_id]) . ">Go back?</a>");
        }
    }
    public function bankrecall($id)
    {
        $trans = Transactions::whereref_id($id)->first();
        $authToken = base64_encode($trans->getCurrency->auth_key . ':' . $trans->getCurrency->auth_secret);
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Basic ' . $authToken);
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get("https://api.yapily.com/institutions");
        $response = $curl->response;
        $curl->close();
        if ($curl->error) {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors($response->error->status . '-' . $response->error->message);
        } else {
            $data['authtoken'] = $authToken;
            $data['institution'] = $response->data;
            $data['title'] = 'Select Preferred Bank';
            $data['reference'] = $trans->ref_id;
            $data['type'] = $trans->type;
            return view('user.dashboard.institution', $data);
        }
    }
    public function webhookCard(Request $request, $id)
    {
        $sav = Transactions::whereref_id($id)->first();
        $stripe = new StripeClient($this->settings->secret_key);
        try {
            $data=$stripe->paymentIntents->retrieve($request->input('payment_intent'));
            if ($data['status']=="succeeded") {
                if ($sav->payment_type == "card") {
                    cardError($sav->trace_id, "Successfully paid with card", "log");
                }
                $sav->status = 1;
                if ($sav->getCurrency->pending_balance_duration != 0 && $sav->type != 4) {
                    $sav->pending = 1;
                }
                $sav->completed_at = Carbon::now();
                if ((new Agent())->isDesktop()) {
                    $sav->device = "tv";
                }
                if ((new Agent())->isMobile()) {
                    $sav->device = "mobile";
                }
                if ((new Agent())->isTablet()) {
                    $sav->device = "tablet";
                }
                $sav->save();
                session::forget('trace_id');
                session::forget('first_name');
                session::forget('last_name');
                session::forget('tx_ref');
                session::forget('email');
                $balance = Balance::whereuser_id($sav->receiver_id)->wherebusiness_id($sav->business_id)->wherecountry_id($sav->currency)->first();
                if ($sav->pending == 1) {
                    $sav->pending_amount = $sav->pending_amount + $sav->amount - $sav->charge;
                    $sav->disburse_date = Carbon::now()->addDays($sav->getCurrency->pending_balance_duration);
                    $sav->save();
                } else {
                    $balance->amount = $balance->amount + $sav->amount - $sav->charge;
                }
                $balance->save();
                //Save Audit Log
                $audit = new Audit();
                $audit->user_id = $sav->receiver_id;
                $audit->trx = $sav->ref_id;
                if ($sav->type == 2) {
                    $audit->log = 'Received test payment ' . $sav->api->ref_id;
                } elseif ($sav->type == 1) {
                    $audit->log = 'Received test payment ' . $sav->link->ref_id;
                } elseif ($sav->type == 4) {
                    $audit->log = 'Received test payment ' . $sav->balance->ref_id;
                }
                $audit->save();
                if ($sav->type == 1) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->link->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->link->user->business()->receive_webhook == 1) {
                        if ($sav->link->user->business()->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } elseif ($sav->type == 4) {
                    //Notify users
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->balance->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->balance->user->business()->receive_webhook == 1) {
                        if ($sav->balance->user->business()->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                } else {
                    if ($this->settings->email_notify == 1) {
                        dispatch(new SendPaymentEmail($sav->api->ref_id, $sav->ref_id));
                    }
                    //Send Webhook
                    if ($sav->api->user->business()->receive_webhook == 1) {
                        if ($sav->api->user->business()->webhook != null) {
                            send_webhook($sav->ref_id);
                        }
                    }
                }
                if ($sav->popup == 1) {
                    return view('user.merchant.popup.goback', ['title' => 'Payment successful', 'ref' => $sav->ref_id, 'url' => null]);
                } else {
                    return redirect()->route('generate.receipt', ['id' => $sav->ref_id]);
                }
            }else{
                $sav->status = 2;
                $sav->save();
                if ($sav->popup == 1) {
                    return view('user.merchant.popup.goback', ['title' => 'Payment failed', 'ref' => $sav->ref_id, 'url' => null]);
                }else{
                    return view('errors.error', ['title' => 'Error Message'])->withErrors('Payment failed');
                }
            }
        } catch (\Stripe\Exception\CardException $e) {
            return view('errors.error', ['title' => 'Error Message'])->withErrors($e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return view('errors.error', ['title' => 'Error Message'])->withErrors($e->getMessage());
        }
    }
    public function search(Request $request)
    {
        $data['title'] = "Search Result for: " . $request->search;
        $data['status'] = 0;
        $data['limit'] = 3;
        $data['currency'] = 0;
        $data['links'] = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->where('name', 'LIKE', '%' . $request->search . '%')->orwhere('amount', 'LIKE', '%' . $request->search . '%')->orwhere('description', 'LIKE', '%' . $request->search . '%')->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'desc')->paginate($data['limit']);
        if (count(auth()->guard('user')->user()->getPayment($data['limit'])) > 0) {
            $first = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'desc')->first();
            $last = Paymentlink::whereuser_id(auth()->guard('user')->user()->id)->wheremode(auth()->guard('user')->user()->live)->orderby('created_at', 'asc')->first();
            $data['order'] = date("m/d/Y", strtotime($last->created_at)) . ' - ' . date("m/d/Y", strtotime($first->created_at));
        } else {
            $data['order'] = null;
        }
        return view('user.link.index', $data);
    }
    public function swapSubmit(Request $request, $id)
    {
        $balance = Balance::whereref_id($id)->first();
        $validator = Validator::make(
            $request->all(),
            [
                'from_amount' => 'integer|required|max:' . getCountry($balance->country_id)->swap_max_amount . '|min:' . getCountry($balance->country_id)->swap_min_amount,
            ]
        );
        if ($validator->fails()) {
            return back()->with('errors', $validator->errors());
        }
        $to_currency = explode('*', $request->currency);
        if ($balance->amount > $request->from_amount || $balance->amount == $request->from_amount) {
            $sav = new Transactions();
            $sav->ref_id = randomNumber(11);
            $sav->type = 5;
            $sav->mode = 1;
            $sav->amount = $request->amount - getCountryRatesUnique($balance->country_id, $to_currency[2])->charge;
            $sav->charge = getCountryRatesUnique($balance->country_id, $to_currency[2])->charge;
            $sav->receiver_id = $balance->user_id;
            $sav->business_id = $balance->business_id;
            $sav->payment_link = $id;
            $sav->payment_type = 'swap';
            $sav->ip_address = user_ip();
            $sav->currency = $to_currency[2];
            $sav->status = 1;
            $sav->completed_at = Carbon::now();
            $sav->save();
            $balance->amount = $balance->amount - $request->from_amount;
            $balance->save();
            $credit = Balance::whereuser_id(auth()->guard('user')->user()->id)->wherebusiness_id(auth()->guard('user')->user()->business_id)->wherecountry_id($to_currency[2])->first();
            $credit->amount = $credit->amount + ($request->from_amount * getCountryRatesUnique($balance->country_id, $to_currency[2])->rate);
            $credit->save();
            return redirect()->route('user.transactions', ['balance' => $credit->ref_id])->with('success', 'Conversion successful');
        } else {
            return back()->with('alert', 'Insufficient Balance');
        }
    }
}
