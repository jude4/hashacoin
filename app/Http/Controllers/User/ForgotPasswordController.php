<?php

namespace App\Http\Controllers\User;

use App\GeneralSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Password;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\Settings;
use App\Jobs\SendEmail;
use DB;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;
    public function __construct()
    {
        $basic= Settings::first();

    }

    public function showLinkRequestForm()
    {
        $data['title'] = "Forgot password";
        return view('auth.passwords.email',$data);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);
        $check = User::whereEmail($request->email)->count();
        if ($check == 0)
        {
            return back()->with('alert', 'There is no user with that e-mail address.');
        }else{
            $user = User::whereEmail($request->email)->first();
            $to =$user->email;
            $name = $user->name;
            $subject = 'Password Reset';
            $code = str_random(30);
            $link = url('/user-password/').'/reset/'.$code;
            $data = new PasswordReset();
            $data->email = $user->email;
            $data->token = $code;
            $data->save();
            dispatch(new SendEmail($to,  $name, $subject, "Use This Link to Reset Password: <br> <a href='".$link."'>".$link."</a>"));
            return back()->with('success', 'Password Reset Link sent to your email');
        }
    }
}
