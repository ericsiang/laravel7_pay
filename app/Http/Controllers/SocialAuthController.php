<?php

namespace App\Http\Controllers;

use App\PayAccount;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        //dd($provider);
        return Socialite::driver($provider)->redirect();   
    }  


    public function callback($provider)
    {
        $Socialite_data=Socialite::driver($provider)->user();
        $provider_user_id=$Socialite_data->getId();
        $email=$Socialite_data->getEmail();
        $email_check=PayAccount::WHERE('provider_user_id','!=',$provider_user_id)
                                    ->WHERE('email',$email) 
                                    ->get();

        //判斷是否已被註冊
        if($email_check){
            $error_provider=$email_check[0]->provider;    
            return redirect('/login')->with('msg','此帳號已用'.$error_provider.'帳號註冊會員!');
        }                           


    }

}
