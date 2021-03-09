<?php

namespace App\Http\Controllers;

use App\Order;
//引用綠界SDK
use ECPay_AllInOne as ECPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ECPay_PaymentMethod as ECPayMethod;


class PaymentsController extends Controller
{
      //訂單付款
      public function checkout_order(Request $request){
            $input=$request->except(['_token','ecommerce_checkout_place_order']);

            if(Auth::guard('pay_account')->user()){
                $user_id=Auth::guard('pay_account')->user()->id;    
                $input['user_id']=$user_id;
                $input['order_no']='T'.time();
                $input['order_total']=100;
                $pay_way=$input['pay_way'];
                unset($input['pay_way']);
                $order=Order::create($input);
                
                
                $order_id=$order->order_id;
               
                switch($pay_way){
                    case 'ecpay';
                        $this->ECPay($order);
                        break;
                    case 'opay';
                        $this->Opay($Opay);
                        break;
                }


               
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
                return $obj->CheckOut();
                //$Response =$obj->CheckOutString();
                
                // dd($Response);
            
            } catch (Exception $e) {
                return $e->getMessage();
            } 
    }

    public function Opay(Order $order){
        try {
            dd($order);
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
            return $obj->CheckOut();
    
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ecpayOrderStatus(Request $request){
        return view('checkout_status');
        /* 接收到的回傳陣列
            array(
                "CustomField1" => null,
                "CustomField2" => null,
                "CustomField3" => null,
                "CustomField4" => null,
                "MerchantID" => "2000132",
                "MerchantTradeNo" => "T1576824229",
                "PaymentDate" => "2019/12/20 14:44:57",
                "PaymentType" => "Credit_CreditCard",
                "PaymentTypeChargeFee" => "18",
                "RtnCode" => "1",
                "RtnMsg" => "Succeeded",
                "SimulatePaid" => "0",
                "StoreID" => null,
                "TradeAmt" => "900",
                "TradeDate" => "2019/12/20 14:43:52",
                "TradeNo" => "1912201443520341",
                "CheckMacValue" => "C8EF745A91ABDC0F5C58B716A807306D27DE5C37681B9FFB54EE16C2FEB5637C"
            );
        */ 

        $order_no=$request->MerchantTradeNo;
        if($request->RtnCode==1){
            $order=Order::WHERE('order_no',$order_no)
                            ->update([
                                'order_status'=>1//交易成功
                            ]);
            $msg='訂單交易成功';                
        }else{
            $order=Order::WHERE('order_no',$order_no)
                            ->update([
                                'order_status'=>2//交易失敗
                            ]);
            $msg='訂單交易失敗';     
        }                    
        
        $order=Order::WHERE('order_no',$order_no)->get(); //取得訂單資訊
        //dd($order);
        //$order_id=$order[0]->order_id;
        //$OrderCarts=OrderCart::WHERE('order_id',$order_id)->get(); //取得訂單內購物車資訊
        //dd($order_id);
        return view('checkout_status',compact('order_no','msg'));
    }

}
