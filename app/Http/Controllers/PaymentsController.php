<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引用綠界SDK
use ECPay_PaymentMethod as ECPayMethod;
use ECPay_AllInOne as ECPay;


class PaymentsController extends Controller
{
      //訂單付款
      public function checkout_order(Request $request){
            $input=$request->all();

            $user_id=Auth::guard('user_account')->user()->user_id;    

            if(Auth::guard('user_account')->user()){

                $input['user_id']=$user_id;
                $input['order_no']='T'.time();
                $order=Order::create($input);
                //dd($order);
                
                $order_id=$order->order_id;
                
                


               
            }else{
                return redirect('/signin');
            }
    }

    //綠界付費信用卡付款
    public function ECPay(Order $order){
        /**
        *    Credit信用卡付款產生訂單範例
        */
            
            //載入SDK(路徑可依系統規劃自行調整)
            try {
                
                $obj = new \ECPay_AllInOne(); //記得要再new前加斜線，因為namespace
        
                //服務參數
                $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";   //服務位置
                $obj->HashKey     = env('ECPAY_HASHKEY','5294y06JbISpM5x9');                                    //測試用Hashkey，請自行帶入ECPay提供的HashKey
                $obj->HashIV      = env('ECPAY_HASHIV','v77hoKGq4kWxNNIS');                                
                //測試用HashIV，請自行帶入ECPay提供的HashIV
                $obj->MerchantID  = '2000132';                                                      
                //測試用MerchantID，請自行帶入ECPay提供的MerchantID =>合作特店編號
                $obj->EncryptType = '1';                                                          
                //CheckMacValue加密類型，請固定填入1，使用SHA256加密
                //基本參數(請依系統規劃自行調整)
                $MerchantTradeNo = $order->order_no; //特店交易編號 我們這的訂單號碼
                $obj->Send['ReturnURL']         =  env('ECPAY_RETURN_URL');    //付款完成通知回傳的網址
                $obj->Send['OrderResultURL']    = env('ECPAY_RETURN_URL');
                $obj->Send['MerchantTradeNo']   = $MerchantTradeNo;                          //訂單編號
                $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                       //交易時間
                $obj->Send['TotalAmount']       = $order->order_total;                       //交易金額
                $obj->Send['TradeDesc']         = $order->order_no;                          //交易描述
                $obj->Send['ChoosePayment']     = \ECPay_PaymentMethod::Credit ;              //付款方式:Credit
                $obj->Send['IgnorePayment']     = \ECPay_PaymentMethod::GooglePay ;           //不使用付款方式:GooglePay
                //訂單的商品資料
                //foreach($OrderCarts as $OrderCart){
                    array_push($obj->Send['Items'], array('Name' => '測試付費', 'Price' => (int)"100",'Currency' => "元", 'Quantity' => (int) "1", 'URL' => ""));
                //}
                // array_push($obj->Send['Items'], array('Name' => '運費', 'Price' => (int)"100",
                //     'Currency' => "元", 'Quantity' => (int)"1", 'URL' => ""));
                /*array_push($obj->Send['Items'], array('Name' => "歐付寶黑芝麻豆漿", 'Price' => (int)"2000",
                        'Currency' => "元", 'Quantity' => (int) "1", 'URL' => "dedwed"));*/
                
                        //Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
                //以下參數不可以跟信用卡定期定額參數一起設定
                $obj->SendExtend['CreditInstallment'] = '' ;    //分期期數，預設0(不分期)，信用卡分期可用參數為:3,6,12,18,24
                $obj->SendExtend['InstallmentAmount'] = 0 ;    //使用刷卡分期的付款金額，預設0(不分期)
                $obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
                $obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;
                //Credit信用卡定期定額付款延伸參數(可依系統需求選擇是否代入)
                //以下參數不可以跟信用卡分期付款參數一起設定
                // $obj->SendExtend['PeriodAmount'] = '' ;    //每次授權金額，預設空字串
                // $obj->SendExtend['PeriodType']   = '' ;    //週期種類，預設空字串
                // $obj->SendExtend['Frequency']    = '' ;    //執行頻率，預設空字串
                // $obj->SendExtend['ExecTimes']    = '' ;    //執行次數，預設空字串
                
                # 電子發票參數
                /*
                $obj->Send['InvoiceMark'] = ECPay_InvoiceState::Yes;
                $obj->SendExtend['RelateNumber'] = "Test".time();
                $obj->SendExtend['CustomerEmail'] = 'test@ecpay.com.tw';
                $obj->SendExtend['CustomerPhone'] = '0911222333';
                $obj->SendExtend['TaxType'] = ECPay_TaxType::Dutiable;
                $obj->SendExtend['CustomerAddr'] = '台北市南港區三重路19-2號5樓D棟';
                $obj->SendExtend['InvoiceItems'] = array();
                // 將商品加入電子發票商品列表陣列
                foreach ($obj->Send['Items'] as $info)
                {
                    array_push($obj->SendExtend['InvoiceItems'],array('Name' => $info['Name'],'Count' =>
                        $info['Quantity'],'Word' => '個','Price' => $info['Price'],'TaxType' => ECPay_TaxType::Dutiable));
                }
                $obj->SendExtend['InvoiceRemark'] = '測試發票備註';
                $obj->SendExtend['DelayDay'] = '0';
                $obj->SendExtend['InvType'] = ECPay_InvType::General;
                */
                //產生訂單(auto submit至ECPay)
                //dd($obj);
                $obj->CheckOut();
                //$Response =$obj->CheckOutString();
                
                // dd($Response);
            
            } catch (Exception $e) {
                echo $e->getMessage();
            } 
    }

    public function opay(Order $order){
        try {

            $obj = new OpayAllInOne();
    
            //服務參數
            $obj->ServiceURL  = "https://payment-stage.opay.tw/Cashier/AioCheckOut/V5";         //服務位置
            $obj->HashKey     = env('OPAY_HASHKEY','5294y06JbISpM5x9');                                                              //測試用Hashkey，請自行帶入OPay提供的HashKey
            $obj->HashIV      = env('OPAY_HASHIV','v77hoKGq4kWxNNIS');                                            //測試用HashIV，請自行帶入OPay提供的HashIV
            $obj->MerchantID  = '2000132';                                                      //測試用MerchantID，請自行帶入OPay提供的MerchantID
            $obj->EncryptType = OpayEncryptType::ENC_SHA256;                                    //CheckMacValue加密類型，請固定填入1，使用SHA256加密
            $MerchantTradeNo = $order->order_no; //特店交易編號 我們這的訂單號碼
            //基本參數(請依系統規劃自行調整)
            $obj->Send['ReturnURL']         = env('OPAY_RETURN_URL');                                //付款完成通知回傳的網址
            $obj->Send['MerchantTradeNo']   = $MerchantTradeNo ;                                      //訂單編號
            $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');                                    //交易時間
            $obj->Send['TotalAmount']       = $order->order_total;                                    //交易金額
            $obj->Send['TradeDesc']         = $order->order_no;                                       //交易描述
            $obj->Send['ChoosePayment']     = OpayPaymentMethod::Credit ;                             //付款方式:Credit
              

            //訂單的商品資料
            array_push($obj->Send['Items'], array('Name' => '測試付費', 'Price' => (int)"100",'Currency' => "元", 'Quantity' => (int) "1", 'URL' => ""));

            //Credit信用卡分期付款延伸參數(可依系統需求選擇是否代入)
            //以下參數不可以跟信用卡定期定額參數一起設定
            $obj->SendExtend['CreditInstallment'] = '' ;   //分期期數，預設0(不分期)，信用卡分期可用參數為:3,6,12,18,24
            $obj->SendExtend['Redeem'] = false ;           //是否使用紅利折抵，預設false
            $obj->SendExtend['UnionPay'] = false;          //是否為聯營卡，預設false;
    
            //Credit信用卡定期定額付款延伸參數(可依系統需求選擇是否代入)
            //以下參數不可以跟信用卡分期付款參數一起設定
            // $obj->SendExtend['PeriodAmount'] = '2000' ; //每次授權金額，預設空字串
            // $obj->SendExtend['PeriodType']   = 'M' ;    //週期種類，預設空字串
            // $obj->SendExtend['Frequency']    = '1' ;    //執行頻率，預設空字串
            // $obj->SendExtend['ExecTimes']    = '2' ;    //執行次數，預設空字串
    
            //產生訂單(auto submit至OPay)
            $obj->CheckOut();
    
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}
