<?php


/**
 * 微信支付接口
 *
 * @author zhongtw
 */
require_once 'protected/modules/pay/components/WxPayApi.php';
header("Content-type: text/html; charset=utf-8");
class WechatpayController extends Controller {
    

    /**
     * 微信公众号支付
     */
    public function actionJsapipay(){
        $output = new stdClass();
        
        $reqStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $reqJson = json_decode($reqStr, true);       
        if(!isset($reqJson['weixinpub_id']) || !isset($reqJson['out_trade_no']) || !isset($reqJson['total_fee']) || !isset($reqJson['openid']) || !isset($reqJson['body'])){
            $output->flag = 1;
            $output->info = 'missing payment parameters';
            return $this->renderJsonOutput($output);
        }
        
        $input = new WxPayUnifiedOrder();
        //根据微信账号获取微信基本信息
        $weixinpub_id = $reqJson['weixinpub_id'];
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId($weixinpub_id);
        if(!isset($result)){
            $output->flag = 1;
            $output->info = '缺少商户信息';
            return $this->renderJsonOutput($output);
        }
        $input->SetAppid($result->getAppId());//公众账号ID
        $input->SetMch_id($result->getMchId());//商户号

        $input->SetBody($reqJson['body']);//商品或支付单简要描述
        $input->SetOut_trade_no($reqJson['out_trade_no']);//商户系统内部的订单号,32个字符内、可包含字母
        $input->SetTotal_fee($reqJson['total_fee']);//订单总金额，单位为分
        $input->SetOpenid($reqJson['openid']);//trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识        
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/pay/wechatpaynotify/callback");//接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("JSAPI");//交易类型
        try{
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $this->GetJsApiParameters($order);
            if(!is_array($jsApiParameters) || array_key_exists('return_msg', $jsApiParameters)){//返回信息，如果 return_msg 非空，则表明接口返回错误
                $output->flag = 1;
            }else{
                $output->flag = 0;
            }
            $output->info = json_encode($jsApiParameters); 
        } catch (Exception $exc) {
            $output->flag = 1;
            $output->info = 'system exceptions';
        }        
        return $this->renderJsonOutput($output);
    }
    
    
    /**
     * 微信扫码支付
     * 采用模式二，二维码有效时间为2小时
     */
    public function actionScancodepay() {
        $output = new stdClass();
        
        $reqStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $reqJson = json_decode($reqStr, true);       
        if(!isset($reqJson['weixinpub_id']) || !isset($reqJson['out_trade_no']) || !isset($reqJson['total_fee']) || !isset($reqJson['product_id']) || !isset($reqJson['body'])){
            $output->flag = 1;
            $output->info = 'missing payment parameters';
            return $this->renderJsonOutput($output);
        }
        try{
            $input = new WxPayUnifiedOrder();

            $weixinpub_id = $reqJson['weixinpub_id'];
            $wechatAccount = new WechatAccount();
            $result = $wechatAccount->getByPubId($weixinpub_id);
            if(!isset($result)){
                $output->flag = 1;
                $output->info = '缺少商户信息';
                return $this->renderJsonOutput($output);
            }
            $input->SetAppid($result->getAppId());//公众账号ID
            $input->SetMch_id($result->getMchId());//商户号

            $input->SetBody($reqJson['body']);//商品或支付单简要描述
            $input->SetOut_trade_no($reqJson['out_trade_no']);//商户系统内部的订单号,32个字符内、可包含字母
            $input->SetTotal_fee($reqJson['total_fee']);//订单总金额，单位为分
            $input->SetProduct_id($reqJson['product_id']);//trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。

            $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/pay/wechatpaynotify/callback");//接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
            $input->SetTrade_type("NATIVE");//交易类型

            $result = WxPayApi::unifiedOrder($input);  
            if(array_key_exists('code_url', $result)){
                $url = $result["code_url"];
                $output->flag = 0;
                $output->info = $url;
            }else if(array_key_exists('err_code_des', $result)){
                $err_code_des = $result["err_code_des"];
                $output->flag = 1;
                $output->info = $err_code_des;
            }else{
                $output->flag = 1;
                $output->info = 'return exceptions';
            }            
        } catch (Exception $exc) {
            $output->flag = 1;
            $output->info = 'system exceptions';
        }
        return $this->renderJsonOutput($output);		
    }
    
    
   /**
    * 获取jsapi支付的参数
    * @param array $UnifiedOrderResult 统一支付接口返回的数据
    * @return json数据，可直接填入js函数作为参数
    */
    public function GetJsApiParameters($UnifiedOrderResult){
        if(!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == ""){
            return $UnifiedOrderResult;
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = $jsapi->GetValues();
        return $parameters;
    }

    
}
