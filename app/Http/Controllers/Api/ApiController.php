<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Exttransfer;
use App\Models\Settings;
use App\Models\Shipstate;
use App\Models\Business;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->settings = Settings::find(1);
    }
    
    public function verifyToken($token)
    {
        if (Business::wheresecret_key($token)->count() > 0) {
            return $token;
        } elseif (Business::wheretest_secret_key($token)->count() > 0) {
            return $token;
        } else {
            return null;
        }
    }

    //Get user
    public function getUser($token)
    {
        if ($business = Business::wheresecret_key($token)->count() > 0) {
            $business = Business::wheresecret_key($token)->first();
            return User::find($business->user_id);
        } else {
            $business = Business::wheretest_secret_key($token)->first();
            return User::find($business->user_id);
        }
    }

    public function supportedCountries(Request $request)
    {
        
        if ($this->verifyToken($request->bearerToken()) != null) {
            foreach (getAcceptedCountry() as $val) {
                $code[] = $val->country_id;
                $name[] = $val->real->currency;
            }
            return response()->json(
                ['currency_code' => $code, 'currency_name' => $name],
                200
            );
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function getCountry(Request $request)
    {
        if ($this->verifyToken($request->bearerToken()) != null) {
            return response()->json(
                ['message' => 'success', 'data' => getAllCountry()],
                201
            );
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function getState(Request $request, $id)
    {
        if ($this->verifyToken($request->bearerToken()) != null) {
            return response()->json(
                ['message' => 'success', 'data' => Shipstate::wherecountry_id($id)->orderby('name', 'asc')->get()],
                201
            );
        } else {
            return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function generate_token(Request $request)
    {
        if (auth()->guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereid(auth()->guard('user')->user()->id)->first();
            $token = $user->createToken('my-app-token')->plainTextToken;
            $user->api_token = $token;
            $user->save();
            return response([
                'token' => $token,
            ], 201);
        } else {
            return response()->json(['message' => 'Invalid credentials', 'status' => 'failed', 'data' => null], 404);
        }
    }

    public function paymentCancel($id)
    {
        $link = Exttransfer::whereref_id($id)->first();
        if ($link->return_url == null) {
            $link->status == 2;
            $link->save();
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('Payment Cancelled');
        } else {
            return redirect()->away($link->return_url);
        }
    }
    
    public function wordpressPay(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->id;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'email' => ['required', 'max:255'],
                'first_name' => ['required', 'max:100'],
                'last_name' => ['required', 'max:100'],
                'callback_url' => ['url', 'nullable'],
                'return_url' => ['url', 'nullable'],
                'logo' => ['url', 'nullable'],
                'tx_ref' => ['required', 'string'],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'meta' => ['array', 'nullable'],
            ]);
            if ($validator->fails()) {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors($validator->errors());
            }
            if (Business::wheresecret_key($request->secret_key)->count() == 1) {
                $mode = 1;
                $user = Business::wheresecret_key($request->secret_key)->first();
            } else {
                if (Business::wheretest_secret_key($request->secret_key)->count() == 1) {
                    $mode = 0;
                    $user = Business::wheretest_secret_key($request->secret_key)->first();
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Invalid secret key');
                }
            }
            if ($user->receiver->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->receiver->id)->count();
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = $user->receiver->id;
                    $sav->business_id = $user->receiver->business_id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->currency = $country_id;
                    $sav->title = $request->title;
                    $sav->description = $request->description;
                    $sav->logo = $request->logo;
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    return redirect()->route('checkout.url', ['id' => $sav->ref_id]);
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Transaction reference has been used before');
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors('User can\'t receive payments');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.payment', $data)->withErrors('Invalid currency, ' . $request->currency . ' is not supported');
        }
    }

    public function htmlPay(Request $request)
    {
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'email' => ['required', 'max:255'],
                'first_name' => ['required', 'max:100'],
                'last_name' => ['required', 'max:100'],
                'callback_url' => ['url', 'nullable'],
                'return_url' => ['url', 'nullable'],
                'logo' => ['url', 'nullable'],
                'tx_ref' => ['required', 'string'],
                'public_key' => ['required', 'string'],
                'title' => ['required', 'string', 'max:100'],
                'description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'meta' => ['array', 'nullable'],
            ]);
            if ($validator->fails()) {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors($validator->errors());
            }
            $cc = Business::wherepublic_key($request->public_key)->count();
            if ($cc == 1) {
                $mode = 1;
                $user = Business::wherepublic_key($request->public_key)->first();
            } else {
                $test = Business::wheretest_public_key($request->public_key)->count();
                if ($test == 1) {
                    $mode = 0;
                    $user = Business::wheretest_public_key($request->public_key)->first();
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Invalid public key');
                }
            }
            if ($user->receiver->status == 0) {
                $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->receiver->id)->count();
                if ($used == 0) {
                    $sav = new Exttransfer();
                    $sav->ref_id = randomNumber(11);
                    $sav->user_id = $user->receiver->id;
                    $sav->business_id = $user->receiver->business_id;
                    $sav->amount = $request->amount;
                    $sav->callback_url = $request->callback_url;
                    $sav->return_url = $request->return_url;
                    $sav->tx_ref = $request->tx_ref;
                    $sav->email = $request->email;
                    $sav->first_name = $request->first_name;
                    $sav->last_name = $request->last_name;
                    $sav->currency = $country_id;
                    $sav->title = $request->title;
                    $sav->description = $request->description;
                    $sav->logo = $request->logo;
                    $sav->meta = json_encode($request->meta);
                    $sav->mode = $mode;
                    $sav->save();
                    return redirect()->route('checkout.url', ['id' => $sav->ref_id]);
                } else {
                    $data['title'] = 'Error Message';
                    return view('errors.payment', $data)->withErrors('Transaction reference has been used before');
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.payment', $data)->withErrors('User can\'t receive payments');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.payment', $data)->withErrors('Invalid currency, ' . $request->currency . ' is not supported');
        }
    }

    public function jsPay(Request $request)
    {
        $cc = Business::wherepublic_key($request->bearerToken())->count();
        if ($cc == 1) {
            $mode = 1;
            $user = Business::wherepublic_key($request->bearerToken())->first();
        } else {
            $test = Business::wheretest_public_key($request->bearerToken())->count();
            if ($test == 1) {
                $mode = 0;
                $user = Business::wheretest_public_key($request->bearerToken())->first();
            } else {
                return response()->json(['message' => 'Invalid public key', 'status' => 'failed', 'data' => null], 400);
            }
        }
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'customer.email' => ['required', 'max:255'],
                'customer.first_name' => ['required', 'max:100'],
                'customer.last_name' => ['required', 'max:100'],
                'callback_url' => ['url', 'nullable'],
                'return_url' => ['url', 'nullable'],
                'customization.logo' => ['url', 'nullable'],
                'tx_ref' => ['required', 'string'],
                'customization.title' => ['required', 'string', 'max:100'],
                'customization.description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'meta' => ['array', 'nullable'],
            ]);
            if ($validator->fails()) {
                return response()->json(['validate' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
        }
        if ($user->receiver->status == 0) {
            $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->receiver->id)->count();
            if ($used == 0) {
                $sav = new Exttransfer();
                $sav->ref_id = randomNumber(11);
                $sav->user_id = $user->receiver->id;
                $sav->business_id = $user->receiver->business_id;
                $sav->amount = $request->amount;
                $sav->callback_url = $request->callback_url;
                $sav->return_url = $request->return_url;
                $sav->tx_ref = $request->tx_ref;
                $sav->email = $request->customer['email'];
                $sav->first_name = $request->customer['first_name'];
                $sav->last_name = $request->customer['last_name'];
                $sav->currency = $country_id;
                $sav->title = $request->customization['title'];
                $sav->description = $request->customization['description'];
                if (array_key_exists('logo', $request->customization)) {
                    $sav->logo = $request->customization['logo'];
                }
                $sav->meta = json_encode($request->meta);
                $sav->mode = $mode;
                $sav->save();
                $response = [
                    'checkout_url' => route('checkout.url', ['id' => $sav->ref_id]),
                ];
                return response()->json(['message' => 'Payment link created', 'status' => 'success', 'data' => $response], 201);
            } else {
                return response()->json(['message' => 'Transaction reference has been used before', 'status' => 'failed', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'User can\'t receive payments', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function popupPay(Request $request)
    {
        $cc = Business::wherepublic_key($request->bearerToken())->count();
        if ($cc == 1) {
            $mode = 1;
            $user = Business::wherepublic_key($request->bearerToken())->first();
        } else {
            $test = Business::wheretest_public_key($request->bearerToken())->count();
            if ($test == 1) {
                $mode = 0;
                $user = Business::wheretest_public_key($request->bearerToken())->first();
            } else {
                return response()->json(['message' => 'Invalid public key', 'status' => 'failed', 'data' => null], 400);
            }
        }
        foreach (getAcceptedCountry() as $val) {
            $currency[] = $val->real->currency;
            $country[] = $val->id;
        }
        if (in_array($request->currency, $currency)) {
            $array_key = array_keys($currency, $request->currency);
            $country_id = $country[$array_key[0]];
            if (getCountry($country_id)->max_amount != null) {
                $max = getCountry($country_id)->max_amount;
            } else {
                $max = null;
            }
            $validator = Validator::make($request->all(), [
                'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                'customer.email' => ['required', 'max:255'],
                'customer.first_name' => ['required', 'max:100'],
                'customer.last_name' => ['required', 'max:100'],
                'callback_url' => ['url', 'nullable'],
                'return_url' => ['url', 'nullable'],
                'customization.logo' => ['url', 'nullable'],
                'tx_ref' => ['required', 'string'],
                'customization.title' => ['required', 'string', 'max:100'],
                'customization.description' => ['required', 'string', 'max:255'],
                'currency' => ['required', 'max:3', 'string'],
                'meta' => ['array', 'nullable'],
            ]);
            if ($validator->fails()) {
                return response()->json(['validate' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
        }
        if ($user->receiver->status == 0) {
            $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($user->receiver->id)->count();
            if ($used == 0) {
                $sav = new Exttransfer();
                $sav->ref_id = randomNumber(11);
                $sav->user_id = $user->receiver->id;
                $sav->business_id = $user->receiver->business_id;
                $sav->amount = $request->amount;
                $sav->callback_url = $request->callback_url;
                $sav->return_url = $request->return_url;
                $sav->tx_ref = $request->tx_ref;
                $sav->email = $request->customer['email'];
                $sav->first_name = $request->customer['first_name'];
                $sav->last_name = $request->customer['last_name'];
                $sav->currency = $country_id;
                $sav->title = $request->customization['title'];
                $sav->description = $request->customization['description'];
                if (array_key_exists('logo', $request->customization)) {
                    $sav->logo = $request->customization['logo'];
                }
                $sav->meta = json_encode($request->meta);
                $sav->mode = $mode;
                $sav->save();
                Cache::put('popup', route('pop.checkout.url', ['id' => $sav->ref_id]));
                $response = [
                    'checkout_url' => route('pop.checkout.url', ['id' => $sav->ref_id]),
                ];
                return response()->json(['message' => 'Payment link created', 'status' => 'success', 'data' => $response], 201);
            } else {
                return response()->json(['message' => 'Transaction reference has been used before', 'status' => 'failed', 'data' => null], 201);
            }
        } else {
            return response()->json(['message' => 'User can\'t receive payments', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function payment(Request $request)
    {
        if ($this->getUser($request->bearerToken())->status == 0) {
            $used = Exttransfer::wheretx_ref($request->tx_ref)->whereuser_id($this->getUser($request->bearerToken())->id)->count();
            if ($request->bearerToken() != $this->getUser($request->bearerToken())->business()->test_secret_key && $request->bearerToken() != $this->getUser($request->bearerToken())->business()->secret_key) {
                return response()->json(['message' => 'Invalid secret key', 'status' => 'failed', 'data' => null], 400);
            } else {
                if ($request->bearerToken() == $this->getUser($request->bearerToken())->business()->test_secret_key) {
                    $mode = 0;
                } else {
                    $mode = 1;
                }
            }
            foreach (getAcceptedCountry() as $val) {
                $currency[] = $val->real->currency;
                $country[] = $val->id;
            }
            if (in_array($request->currency, $currency)) {
                $array_key = array_keys($currency, $request->currency);
                $country_id = $country[$array_key[0]];
                if (getCountry($country_id)->max_amount != null) {
                    $max = getCountry($country_id)->max_amount;
                } else {
                    $max = null;
                }
                $customMessages = [
                    'customization.logo.url' => 'Logo must be a url',
                ];
                $validator = Validator::make($request->all(), [
                    'amount' => ['required', 'integer', 'min:1', 'max:' . $max],
                    'email' => ['required', 'max:255'],
                    'first_name' => ['required', 'max:100'],
                    'last_name' => ['required', 'max:100'],
                    'callback_url' => ['url'],
                    'return_url' => ['url'],
                    'tx_ref' => ['required', 'string'],
                    'customization.title' => ['required', 'string', 'max:100'],
                    'customization.description' => ['required', 'string', 'max:255'],
                    'customization.logo' => ['url'],
                    'currency' => ['required', 'max:3', 'string'],

                    'meta' => ['array'],
                ], $customMessages);
                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors(), 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                return response()->json(['message' => 'Invalid currency, ' . $request->currency . ' is not supported', 'status' => 'failed', 'data' => null], 400);
            }
            if ($used == 0) {
                $sav = new Exttransfer();
                $sav->ref_id = randomNumber(11);
                $sav->user_id = $this->getUser($request->bearerToken())->id;
                $sav->business_id = $this->getUser($request->bearerToken())->business_id;
                $sav->amount = $request->amount;
                $sav->callback_url = $request->callback_url;
                $sav->return_url = $request->return_url;
                $sav->tx_ref = $request->tx_ref;
                $sav->email = $request->email;
                $sav->first_name = $request->first_name;
                $sav->last_name = $request->last_name;
                $sav->currency = $country_id;
                $sav->title = $request->customization['title'];
                $sav->description = $request->customization['description'];
                if (array_key_exists('logo', $request->customization)) {
                    $sav->logo = $request->customization['logo'];
                }
                $sav->meta = json_encode($request->meta);
                $sav->mode = $mode;
                $sav->save();
                $response = [
                    'checkout_url' => route('checkout.url', ['id' => $sav->ref_id]),
                ];
                return response()->json(['message' => 'Payment link created', 'status' => 'success', 'data' => $response], 201);
            } else {
                return response()->json(['message' => 'Transaction reference has been used before', 'status' => 'failed', 'data' => null], 400);
            }
        } else {
            return response()->json(['message' => 'User can\'t receive payments', 'status' => 'failed', 'data' => null], 400);
        }
    }

    public function paymentLink($id, $type = null)
    {
        $data['link'] = $link = Exttransfer::whereref_id($id)->first();
        $data['title'] = 'Payment';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->status == 0) {
                if ($link->mode == 1) {
                    if ($link->user->business()->kyc_status != "DECLINED") {
                        return view('user.merchant.live', $data);
                    }
                } else {
                    return view('user.merchant.test', $data);
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.error', $data)->withErrors('This payment has been cancelled');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }

    public function poppaymentLink($id, $type = null)
    {
        $data['link'] = $link = Exttransfer::whereref_id($id)->first();
        $data['title'] = 'Payment';
        $data['type'] = $type;
        if ($link->user->status == 0) {
            if ($link->status == 0) {
                if ($link->mode == 1) {
                    if ($link->user->business()->kyc_status != "DECLINED") {
                        return view('user.merchant.popup.live', $data);
                    }
                } else {
                    return view('user.merchant.popup.test', $data);
                }
            } else {
                $data['title'] = 'Error Message';
                return view('errors.error', $data)->withErrors('This payment has been cancelled');
            }
        } else {
            $data['title'] = 'Error Message';
            return view('errors.error', $data)->withErrors('An Error Occured');
        }
    }

    public function verifyPayment(Request $request, $tx_ref)
    {
        $check = Exttransfer::wheretx_ref($tx_ref)->count();
        if ($check == 0) {
            return response()->json(['message' => 'Invalid transaction', 'status' => 'failed', 'data' => null], 404);
        } else {
            $link = Exttransfer::wheretx_ref($tx_ref)->first();
            $user = User::whereid($link->user_id)->first();
            if ($link->mode == 0) {
                $pb = $user->business()->test_secret_key;
                if ($request->bearerToken() != $user->business()->test_secret_key && $request->bearerToken() == $user->business()->secret_key) {
                    return response()->json(['message' => 'live secret keys can\'t be used to verify a test transaction', 'status' => 'failed', 'data' => null], 400);
                }
            } else {
                $pb = $user->business()->secret_key;
                if ($request->bearerToken() != $user->business()->secret_key && $request->bearerToken() == $user->business()->test_secret_key) {
                    return response()->json(['message' => 'test secret keys can\'t be used to verify a live transaction', 'status' => 'failed', 'data' => null], 400);
                }
            }
            if ($pb !== $request->bearerToken()) {
                return response()->json(['message' => 'Invalid Secret Key', 'status' => 'failed', 'data' => null], 400);
            } else {
                if (getTransaction($link->id, $link->user_id) == null) {
                    return response()->json(['message' => 'Payment not submitted', 'status' => 'null', 'data' => null], 404);
                } else {
                    if ($link->getTransaction()->mode == 1) {
                        $mode = "live";
                    } else {
                        $mode = "test";
                    }
                    if ($link->getTransaction()->status == 0) {
                        $status = "pending";
                    } elseif ($link->getTransaction()->status == 1) {
                        $status = "success";
                    } elseif ($link->getTransaction()->status == 3) {
                        $status = "refunded";
                    } elseif ($link->getTransaction()->status == 4) {
                        $status = "reversed";
                    } else {
                        $status = "failed/cancelled";
                    }
                    if ($link->getTransaction()->client == 1) {
                        $amount = $link->getTransaction()->amount - $link->getTransaction()->charge;
                    } else {
                        $amount = $link->getTransaction()->amount;
                    }
                    $data = [
                        'first_name' => $link->getTransaction()->first_name,
                        'last_name' => $link->getTransaction()->last_name,
                        'email' => $link->getTransaction()->email,
                        'currency' => $link->getCurrency->real->currency,
                        'amount' => number_format($amount, 2),
                        'charge' => number_format($link->getTransaction()->charge, 2),
                        'mode' => $mode,
                        'type' => "API",
                        'status' => $status,
                        'reference' => $link->getTransaction()->ref_id,
                        'tx_ref' => $link->tx_ref,
                        'customization' => [
                            'title' => $link->title,
                            'description' => $link->description,
                            'logo' => $link->logo
                        ],
                        'meta' => json_decode($link->meta),
                        'created_at' => $link->created_at,
                        'updated_at' => $link->updated_at
                    ];
                    return response()->json(['message' => 'Payment details', 'status' => $status, 'data' => $data], 201);
                }
            }
        }
    }
}
