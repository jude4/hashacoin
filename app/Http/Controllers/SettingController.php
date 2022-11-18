<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Settings;
use App\Models\Admin;


class SettingController extends Controller
{

    public function Settings()
    {
        return view('admin.settings.index', ['title' => 'General settings', 'val' => Admin::first()]);
    }    
    
    public function Email()
    {
        return view('admin.settings.email', ['title' => 'Email settings']);
    }    
    
    public function Template()
    {
        return view('admin.settings.template', ['title' => 'Email template']);
    }  

    public function AccountUpdate(Request $request)
    {
        $data = Admin::whereid(1)->first();
        $data->username=$request->username;
        $data->password=Hash::make($request->password);
        $data->save();
        return back()->with('success', 'Update was Successful!');
    }  
        
    public function SettingsUpdate(Request $request)
    {
        $data = Settings::findOrFail(1);
        $data->fill($request->all())->save();
        return back()->with('success', 'Update was Successful!');
    }    

    public function Features(Request $request)
    {
        $data = Settings::findOrFail(1);  
        $data->email_verification = (empty($request->email_verification)) ? 0 : $request->email_verification;            
        $data->email_notify = (empty($request->email_notify)) ? 0 : $request->email_notify;                 
        $data->registration = (empty($request->registration)) ? 0 : $request->registration;                               
        $data->recaptcha = (empty($request->recaptcha)) ? 0 : $request->recaptcha;                               
        $data->maintenance = (empty($request->maintenance)) ? 0 : $request->maintenance;                               
        $data->language = (empty($request->language)) ? 0 : $request->language;                               
        $data->preloader = (empty($request->preloader)) ? 0 : $request->preloader;                                           
        $data->save();
        return back()->with('success', 'Update was Successful!');
    }      
    
}
