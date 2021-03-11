<?php

namespace App\Http\Controllers;

use App\PayAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        $Socialite_data=Socialite::driver($provider)->stateless()->user();
        //dd($Socialite_data);
        $provider_user_id=$Socialite_data->getId();
        $email=$Socialite_data->getEmail();
        $email_check=PayAccount::WHERE('provider_user_id','!=',$provider_user_id)
                                    ->orWhereNull('provider_user_id')
                                    ->WHERE('email',$email) 
                                    ->get();
       
        //dd($email_check,$email,$provider_user_id);
        //判斷是否已被註冊
        if(count($email_check)>0){
            //dd($email_check);
            $error_provider=$email_check[0]->provider;    
            return redirect('/signin')->with('msg','此帳號已用'.$error_provider.'帳號註冊會員!');
        }                           

        $pay_account=PayAccount::WHERE('provider_user_id',$provider_user_id)->get();
        //dd($pay_account);
        if($pay_account->count()>0){
            Auth::guard('pay_account')->login($pay_account[0]);
            return redirect('/');   
        }else{
            $pay_account=PayAccount::create([
                'email'=> $email,
                'password'=>"",
                'provider_user_id'=>$provider_user_id,
                'provider'=>$provider,
            ]);

           //dd($user_account);
            Auth::guard('pay_account')->login($pay_account);
            return redirect('/');   
        }

    }

}
