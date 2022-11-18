<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Exttransfer;
use App\Models\Shipcity;
use App\Models\Audit;
use App\Models\Paymentlink;
use App\Models\Transactions;
use App\Models\Plugins;
use App\Models\Shipstate;
use App\Models\Ticket;
use App\Models\Balance;
use App\Models\beneficiary;
use App\Models\Virtual;
use App\Models\Virtualtransactions;
use App\Models\Business;
use App\Jobs\SendEmail;
use Carbon\Carbon;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Illuminate\Support\Facades\Session;
use Image;
use App;



class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->settings = Settings::find(1);
        $self = $this;
        $this->middleware(function (Request $request, $next) use ($self) {
            $self->user = auth()->guard('user')->user();
            return $next($request);
        });
    }

    public function submitBusiness(Request $request)
    {
        if(Business::wherename($request->business_name)->count()==0){
            $user = User::find($this->user->id);
            $business = new Business();
            $business->user_id = $this->user->id;
            $business->name=$request->business_name;
            $business->reference=randomNumber(7);
            $business->public_key = 'pub-live-' . Str::random(32);
            $business->secret_key = 'sec-live-' . Str::random(32);
            $business->test_public_key = 'pub-test-' . Str::random(32);
            $business->test_secret_key = 'sec-test-' . Str::random(32);
            $business->save();
            $user->business_id = $business->reference;
            $user->save();
            if(count(getBusinessUser($user->id))>0){
                foreach (getAcceptedCountry() as $val) {
                    $data = new Balance();
                    $data->user_id = $user->id;
                    $data->country_id = $val->id;
                    $data->ref_id = Str::random(32);
                    $data->business_id = $business->reference;
                    $data->save();
                }
            }else{
                foreach(Balance::whereuser_id($user->id)->get() as $data){
                    $data->business_id = $business->reference;
                    $data->save();
                }
            }
            return redirect()->route('user.dashboard')->with('success', 'Business created');
        }else{
            return back()->with('alert', 'This name is already taken, try another one.');
        }
    }
    public function checkBusiness(Request $request)
    {   
        return response()->json(['st' => (Business::wherename($request->business_name)->count()==0) ? 1 : 2]);
    }
    public function accountmode($id)
    {
        $business = Business::whereuser_id($this->user->id)->wherereference($this->user->business_id)->first();
        if ($id == 0) {
            $business->live = 0;
            $business->save();
            return back()->with('success', 'Test mode activated');
        } elseif ($id == 1) {
            if ($this->user->business()->kyc_status == "APPROVED") {
                $business->live = 1;
                $business->save();
                return back()->with('success', 'Live mode activated, you can now receive payments');
            } elseif ($this->user->business()->kyc_status == "PROCESSING") {
                return back()->with('alert', 'We are still reviewing your compliance');
            } else {
                return back()->with('alert', "To enable live mode, We need more information about you, <a href=" . route('user.compliance') . ">Click here to do this</a>");
            }
        }
    }    
    public function defaultBusiness($id)
    {
        $this->user->business_id = $id;
        $this->user->save();
        $business = Business::whereuser_id($this->user->id)->wherereference($id)->first();
        return redirect()->route('user.dashboard')->with('success', 'Switched to '.$business->name);
    }
    public function webhookResend($id)
    {
        send_webhook($id);
        return back()->with('success', 'Webhook sent');
    }
    public function addressstate(Request $request)
    {
        if ($request->country != null) {
            $country = explode('*', $request->country);
            if (session('state')) {
                $state = Shipstate::wherecountry_id($country[0])->where('id', '!=', session('coutry'))->orderby('name', 'asc')->get();
                $getState = Shipstate::wherename(session('state'))->wherecountry_id(session('country'))->first();
                echo "<option value='$getState->name' selected>$getState->name</option>";
                foreach ($state as $val) {
                    echo "<option value='$val->name'>$val->name</option>";
                }
            } else {
                $state = Shipstate::wherecountry_id($country[0])->orderby('name', 'asc')->get();
                foreach ($state as $val) {
                    echo "<option value='$val->name'>$val->name</option>";
                }
            }
        }
    }
    public function cardRecordLog(Request $request)
    {
        if ($request->type != null && session('trace_id') != null) {
            $check = Transactions::wheretrace_id(session('trace_id'))->count();
            if ($request->type == "cardnumber") {
                if ($check == 0) {
                    cardError(session('trace_id'), "Filled this field: card number", "log");
                } else {
                    cardError(session('trace_id'), "Changed this field: card number", "log");
                }
            } elseif ($request->type == "expiry") {
                if ($check == 0) {
                    cardError(session('trace_id'), "Filled this field: card expiry", "log");
                } else {
                    cardError(session('trace_id'), "Changed this field: card expiry", "log");
                }
            } else {
                if ($check == 0) {
                    cardError(session('trace_id'), "Filled this field: card cvv", "log");
                } else {
                    cardError(session('trace_id'), "Changed this field: card cvv", "log");
                }
            }
        }
    }
    public function dashboard($currency = null, $duration = null)
    {
        $val = (route('user.dashboard')==url()->current()) ? $this->user->getFirstBalance()->getCurrency : getBalance($currency)->getCurrency;
        return view('user.dashboard.index', ['title' => 'dashboard', 'currency' => $currency, 'duration' => ($duration == null) ? 'today' : $duration, 'val' => $val]);
    }
    public function delaccount(Request $request)
    {
        if (Hash::check($request->password, $this->user->password)) {
            User::whereId($this->user->id)->delete();
            Business::whereId($this->user->id)->delete();
            Ticket::whereUser_id($this->user->id)->delete();
            Exttransfer::whereUser_id($this->user->id)->delete();
            Paymentlink::whereUser_id($this->user->id)->delete();
            Transactions::whereReceiver_id($this->user->id)->delete();
            Virtual::whereUser_id($this->user->id)->delete();
            Virtualtransactions::whereUser_id($this->user->id)->delete();
            Auth::guard('user')->logout();
            if ($this->settings->email_notify == 1) {
                dispatch(new SendEmail($this->settings->email, $this->settings->site_name, 'A user just left ' . $this->settings->site_name, 'Reason:' . $request->reason));
                dispatch(new SendEmail($this->user->email, $this->user->first_name.' '.$this->user->last_name, 'Your account has been deleted', 'Your account has been deactivated for the next 30 days before it can be finally deleted, click the link below to reactivate your account <a href=' . route('reactivate', ['user' => $this->user->id]) . '>' . route('reactivate', ['user' => $this->user->id]) . '</a>'));
            }
            auth()->guard('user')->logout();
            session()->forget('fakey');
            return redirect()->route('login')->with('success', 'Account was successfully deleted');
        }else{
            return back()->with('alert', 'Invalid password');
        }
    }
    public function reactivate($user)
    {
        $xx = User::withTrashed()->whereId($user)->first();
        User::whereId($xx->id)->restore();
        Business::whereId($xx->id)->restore();
        Ticket::whereUser_id($xx->id)->restore();
        Exttransfer::whereUser_id($xx->id)->restore();
        Paymentlink::whereUser_id($xx->id)->restore();
        Transactions::whereReceiver_id($xx->id)->restore();
        Virtual::whereUser_id($xx->id)->restore();
        Virtualtransactions::whereUser_id($xx->id)->restore();
        return redirect()->route('login')->with('success', 'Account restored');
    }
    public function deltest()
    {
        Exttransfer::whereUser_id($this->user->id)->wherebusiness_id($this->user->business_id)->wheremode(0)->delete();
        Paymentlink::whereUser_id($this->user->id)->wherebusiness_id($this->user->business_id)->wheremode(0)->delete();
        Transactions::whereReceiver_id($this->user->id)->wherebusiness_id($this->user->business_id)->wheremode(0)->delete();
        foreach (Balance::whereuser_id($this->user->id)->wherebusiness_id($this->user->business_id)->get() as $val) {
            $val->test = null;
            $val->save();
        }
        return back()->with('success', 'Test data deleted');
    }
    public function documentation()
    {
        return view('user.merchant.index', ['title' => 'Documentation']);
    }
    public function pluginDownload(Plugins $plugin)
    {
        return response()->download(public_path("asset/plugins/".$plugin->link), $plugin->link, ['Content-Type' => 'application/zip']);
    }

    //Verification
    public function blocked()
    {
        if ($this->user->status == 0) {
            return redirect()->route('user.dashboard');
        } else {
            return view('auth.blocked', ['title' => 'Account suspended']);
        }
    }
    
    public function verifyEmail()
    {
        return view('auth.verify-email', ['title' => 'Verify email address']);
    }

    public function sendEmail()
    {
        if (Carbon::parse($this->user->email_time)->addMinutes(5) > Carbon::now()) {
            $time = Carbon::parse($this->user->email_time)->addMinutes(5);
            $delay = $time->diffInSeconds(Carbon::now());
            $delay = gmdate('i:s', $delay);
            return back()->with('alert', 'You can resend link after ' . $delay . ' minutes');
        } else {
            $code = strtoupper(Str::random(32));
            $this->user->email_time = Carbon::now();
            $this->user->verification_code = $code;
            $this->user->save();
            dispatch(new SendEmail($this->user->email, $this->user->first_name . ' ' . $this->user->last_name, 'We need to verify your email address', 'Thanks you for signing up to ' . $this->settings->site_name . '.<br> As part of our securtiy checks we need to verify your email address. Simply click on the link below and job done.<br><a href=' . route('user.confirm-email', ['id' => $code]) . '>' . route('user.confirm-email', ['id' => $code]) . '</a>'));
            return back()->with('success', 'Verification Code Sent');
        }
    }

    public function confirmEmail($id)
    {
        if (User::whereverification_code($id)->count() == 0) {
            return view('errors.email', ['title' => 'Error Message'])->withErrors('Invalid Token');
        } else {
            $user = User::whereVerificationCode($id)->first();
            if ($user->email_verify == 1) {
                $data['title'] = 'Email Verification';
                return view('errors.email', $data)->withErrors('Email has already been verified');
            } else {
                $user->email_verify = 1;
                $user->save();
                $data['title'] = 'Email Verification';
                return redirect()->route('user.dashboard')->with('success', 'Email verified');
            }
        }
    }
    //End of verification       

    //Settings
    public function profile()
    {
        $data['title'] = "Settings";
        $g = new GoogleAuthenticator();
        $secret = $g->generateSecret();
        $data['secret'] = $secret;
        $data['image'] = GoogleQrUrl::generate($this->user->email, $secret, $this->settings->site_name);
        return view('user.profile.index', $data);
    }
    public function deleteBeneficiary($id){
        beneficiary::whereid($id)->delete();
        return back()->with('success', 'Beneficiary deleted');
    }

    public function compliance()
    {
        if ($this->user->business()->kyc_status == null || $this->user->business()->kyc_status == "DECLINED" || $this->user->business()->kyc_status == "PENDING" || business()->$this->user->kyc_status == "RESUBMIT") {
            return view('user.profile.compliance', ['title' => 'Compliance']);
        } elseif ($this->user->business()->kyc_status == "PROCESSING") {
            return back()->with('alert', 'We are reviewing your compliance');
        } elseif ($this->user->business()->kyc_status == "DECLINED") {
            return back()->with('alert', 'Compliance has been rejected');
        } else {
            return back()->with('alert', 'Compliance is already approved');
        }
    }
    public function logout()
    {
        if (Auth::guard('user')->check()) {
            $this->user->fa_expiring = Carbon::now()->subMinutes(30);
            $this->user->save();
            session()->forget('oldLink');
            session()->forget('uniqueid');
            Auth::guard('user')->logout();
            session()->flash('message', 'Just Logged Out!');
            return redirect()->route('login');
        } else {
            return redirect()->route('login');
        }
    }
    public function submitPassword(Request $request)
    {
        if (Hash::check($request->password, $this->user->password)) {
            $this->user->password = Hash::make($request->new_password);
            $this->user->save();
            createAudit('Changed Password');
            return back()->with('success', 'Password Changed successfully.');
        } elseif (!Hash::check($request->password, $this->user->password)) {
            return back()->with('alert', 'Invalid password');
        }
    }
    public function account(Request $request)
    {
        $business = Business::whereuser_id($this->user->id)->wherereference($this->user->business_id)->first();
        $this->user->first_name = $request->first_name;
        $this->user->last_name = $request->last_name;
        $business->charges = $request->charges;
        $this->user->language = $request->language;
        App::setLocale($this->user->language);
        session()->put('locale', $this->user->language);
        if ($request->card != null || $request->bank_acount != null || $request->mobile_money != null) {
            if(empty($request->card)){
                $business->card=0;	
            }else{
                $business->card=$request->card;
            }              
            if(empty($request->bank_account)){
                $business->bank_account=0;	
            }else{
                $business->bank_account=$request->bank_account;
            }              
            if(empty($request->mobile_money)){
                $business->mobile_money=0;	
            }else{
                $business->mobile_money=$request->mobile_money;
            } 
            if(empty($request->email_sender)){
                $business->email_sender=0;	
            }else{
                $business->email_sender=$request->email_sender;
            } 
            if(empty($request->email_receiver)){
                $business->email_receiver=0;	
            }else{
                $business->email_receiver=$request->email_receiver;
            } 
            $this->user->save();
            $business->save();
            createAudit('Updated account details');
            if ($this->user->email != $request->email) {
                $check = User::whereEmail($request->email)->get();
                if (count($check) < 1) {
                    $this->user->email_verify = 0;
                    $this->user->email = $request->email;
                    $this->user->save();
                } else {
                    return back()->with('alert', 'Email already in use.');
                }
            }
            return back()->with('success', 'Profile Updated Successfully.');
        } else {
            return back()->with('alert', 'Select a payment method');
        }
    }
    public function useraddressstate(Request $request)
    {
        $state = explode('*', $request->state);
        if ($state[1] != null) {
            if (Session::has('city')) {
                $city = Shipcity::wherestate_code($state[1])->where('id', '!=', session('city'))->orderby('name', 'asc')->get();
                $getCity = Shipcity::whereid(session('city'))->first();
                echo "<option value='$getCity->id' selected>$getCity->name</option>";
                foreach ($city as $val) {
                    echo "<option value='$val->id'>$val->name</option>";
                }
            } else {
                $city = Shipcity::wherestate_code($state[1])->orderby('name', 'asc')->get();
                if (count($city) > 0) {
                    echo "<option value=''>Select your city</option>";
                }
                foreach ($city as $val) {
                    echo "<option value='$val->id'>$val->name</option>";
                }
            }
        }
    }
    public function submitcompliance(Request $request)
    {
        $set = getSettings();
        $state = explode('*', $request->state);
        $user = Business::whereuser_id($this->user->id)->wherereference($this->user->business_id)->first();
        $user->type = $request->type;
        $user->industry = $request->industry;
        $user->category = $request->category;
        $user->staff_size = $request->staff_size;
        if($request->type==2){
            $user->legal_name = $request->legal_name;
            $user->tax_id = $request->tax_id;
            $user->vat_id = $request->vat_id;
            $user->reg_no = $request->reg_no;
            $user->registration_type = $request->registration_type;
            if ($request->hasFile('business_document')) {
                $image = $request->file('business_document');
                $filename = 'business_document' . time() . '.' . $image->extension();
                $location = public_path('asset/profile/' . $filename);
                if ($user->business_document != null) {
                    $path = public_path('asset/profile');
                    $link = $path . $user->business_document;
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                }
                Image::make($image)->save($location);
                $user->business_document = $filename;
            }
            if ($request->hasFile('business_proof_of_address')) {
                $image = $request->file('business_proof_of_address');
                $filename = 'business_proof_of_address' . time() . '.' . $image->extension();
                $location = public_path('asset/profile/'.$filename);
                if ($user->proof_of_address != null) {
                    $path = public_path('asset/profile');
                    $link = $path . $user->business_proof_of_address;
                    if (file_exists($link)) {
                        @unlink($link);
                    }
                }
                Image::make($image)->save($location);
                $user->business_proof_of_address = $filename;
            }
            $business_state = explode('*', $request->business_state);
            $user->business_line_1 = $request->business_line_1;
            $user->business_line_2 = $request->business_line_2;
            $user->business_state = $business_state[0];
            $user->business_postal_code = $request->business_postal_code;
            $user->business_city = $request->business_city;
        }
        $user->gender = $request->gender;
        $user->b_day = $request->b_day;
        $user->b_month = $request->b_month;
        $user->b_year = $request->b_year;
        $user->line_1 = $request->line_1;
        $user->line_2 = $request->line_2;
        $user->state = $state[0];
        $user->postal_code = $request->postal_code;
        $user->city = $request->city;
        $user->doc_type = $request->doc_type;
        $user->kyc_status = "PROCESSING";
        if ($request->hasFile('document')) {
            $image = $request->file('document');
            $filename = 'document' . time() . '.' . $image->extension();
            $location = public_path('asset/profile/' . $filename);
            if ($user->document != null) {
                $path = public_path('asset/profile');
                $link = $path . $user->document;
                if (file_exists($link)) {
                    @unlink($link);
                }
            }
            Image::make($image)->save($location);
            $user->document = $filename;
        }
        if ($request->hasFile('proof_of_address')) {
            $image = $request->file('proof_of_address');
            $filename = 'proof_of_address' . time() . '.' . $image->extension();
            $location = public_path('asset/profile/'.$filename);
            if ($user->proof_of_address != null) {
                $path = public_path('asset/profile');
                $link = $path . $user->proof_of_address;
                if (file_exists($link)) {
                    @unlink($link);
                }
            }
            Image::make($image)->save($location);
            $user->proof_of_address = $filename;
        }
        $user->save();
        if ($set->email_notify == 1) {
            dispatch(new SendEmail($set->email, $set->site_name, 'New Compliance request:' . $user->receiver->first_name, "Just submitted a new compliance form, please review it"));
        }
        $audit['user_id'] = $this->user->id;
        $audit['trx'] = str_random(16);
        $audit['log'] = 'Updated compliance form';
        Audit::create($audit);
        return redirect()->route('user.dashboard')->with('success', 'We will get back to you.');
    }
    public function generateapi()
    {
        $data = Business::whereuser_id($this->user->id)->wherereference($this->user->business_id)->first();
        $data->public_key = 'PUB-' . Str::random(32);
        $data->secret_key = 'SEC-' . Str::random(32);
        $data->test_public_key = 'PUB-TEST-' . Str::random(32);
        $data->test_secret_key = 'SEC-TEST-' . Str::random(32);
        $data->save();
        return redirect()->route('user.api')->with('success', 'New API keys generated');
    }
    public function savewebhook(Request $request)
    {
        $data = Business::whereuser_id($this->user->id)->wherereference($this->user->business_id)->first();
        if ($request->has('receive_webhook')) {
            if ($request->webhook == null) {
                return redirect()->route('user.api')->with('alert', 'Add a url to webhook');
            } elseif ($request->webhook_secret == null) {
                return redirect()->route('user.api')->with('alert', 'Add a webhook secret');
            } else {
                $data->webhook = $request->webhook;
                $data->webhook_secret = $request->webhook_secret;
                $data->receive_webhook = $request->receive_webhook;
                $data->save();
                return redirect()->route('user.api')->with('success', 'Webhook enabled');
            }
        } else {
            $data->receive_webhook = 0;
            $data->save();
            return redirect()->route('user.api')->with('success', 'Webhook disabled');
        }
    }
    public function submit2fa(Request $request)
    {
        $user = User::findOrFail($this->user->id);
        $g = new GoogleAuthenticator();
        $secret = $request->vv;
        $set = Settings::first();
        if ($request->type == 0) {
            $check = $g->checkcode($user->googlefa_secret, $request->code, 3);
            if ($check) {
                $user->fa_status = 0;
                $user->googlefa_secret = null;
                $user->save();
                createAudit('Deactivated 2fa');
                if ($set->email_notify == 1) {
                    dispatch(new SendEmail($user->email, $user->username, 'Two Factor Security Disabled', ' 2FA security on your account was just disabled, contact us immediately if this was not done by you.'));
                }
                return back()->with('success', '2fa disabled.');
            } else {
                return back()->with('alert', 'Invalid code.');
            }
        } else {
            $check = $g->checkcode($secret, $request->code, 3);
            if ($check) {
                $user->fa_status = 1;
                $user->googlefa_secret = $request->vv;
                $user->save();
                createAudit('Activated 2fa');
                if ($set->email_notify == 1) {
                    dispatch(new SendEmail($user->email, $user->username, 'Two Factor Security Enabled', ' 2FA security on your account was just enabled, contact us immediately if this was not done by you.'));
                }
                return back()->with('success', '2fa enabled.');
            } else {
                return back()->with('alert', 'Invalid code.');
            }
        }
    }
    //End of Settings

}
