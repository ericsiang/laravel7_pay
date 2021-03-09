<?php

namespace App\Http\Controllers;

use App\PayAccount;
use Validator;//新增自訂驗證時須加
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;//登入Auth用
use Illuminate\Support\Facades\Hash;//密碼加密用
use Illuminate\Foundation\Auth\AuthenticatesUsers; //需login驗證時一定要加這行

class SignInController extends Controller
{
    use AuthenticatesUsers; //需login驗證時一定要加這行

    //修改驗證時使用的 guard
    protected function guard(){
        return \Auth::guard('pay_account');
    }

    public function register(Request $request)
    {   
       
        $input=$request->except(['_token']);
        
        $rule=[
            'email'=>'required|email|unique:pay_accounts,email',
            'password'=>'required|min:8',
            're_password'=>'required|same:password',
        ];

        $msg=[
            'email'=>'email格式有誤',
            //'email.unique'=>'email已存在',
            'password.min'=>'最少8個',
            'required'=>':attribute欄位必填',
            're_password.required'=>'repeat password欄位必填',
            're_password.same'=>'確認密碼不一致',
        ];

        $validator=Validator::make($input,$rule,$msg);
       
        if($validator->passes())
        {   
            $input=$request->except(['_token','re_password']);
            
            //密碼加密
            $input['password']=Hash::make($input['password']);

            PayAccount::create($input);
            return redirect('/signin')
                                ->with('msg','加入成功');;
        }
        else
        {
            //dd($validator);
            return redirect('signin')
                        ->withErrors($validator)//傳送欄位錯誤的error
                        ->withInput()  //傳送原本填寫表單的值
                        ->with('type','register'); //回傳一次性session  
        }
    }


    //登入
    public function checkLogin(Request $request)
    {
        $input=$request->except(['_token']);

        $rule=['email'=>'required','password'=>'required',];
        $msg=['required'=>'請輸入 :attribute'];

        $validator=Validator::make($input,$rule,$msg);

        if($validator->passes())
        {   
            if(Auth::guard('pay_account')->attempt(['email'=>$input['email'],'password'=>$input['password']]))
            {   
                //登入成功
                return redirect('/'); 
            }else
            {
                //登入失敗，帳號密碼錯誤
                return redirect('signin')
                        ->withInput()  //傳送原本填寫表單的值
                        ->with('type','login') //回傳一次性session  
                        ->with('msg','登入失敗，請確認帳號密碼是否正確'); //回傳一次性session
            }

        }else
        {
            return redirect('signin')
                        ->withErrors($validator)//傳送欄位錯誤的error
                        ->withInput()  //傳送原本填寫表單的值
                        ->with('type','login'); //回傳一次性session  
        }

    }

    //登出
    public function logout(Request $request)
    {
        $this->guard()->logout();//登出pay_account的guard
        $request->session()->flush();
        $request->session()->regenerate();
        Auth::logout();
		return redirect('/signin');
    }



}
