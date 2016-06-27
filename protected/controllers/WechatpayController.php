<?php


/**
 * 微信支付接口
 *
 * @author zhongtw
 */
require_once 'protected/config/WxPayApi.php';
header("Content-type: text/html; charset=utf-8");
class WechatpayController extends Controller {
    
    public function actionTest(){
        echo 12345;
        Yii::app()->end();
    }
    
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
        
        //根据微信账号获取微信基本信息
        $weixinpub_id = $reqJson['weixinpub_id'];
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId($weixinpub_id);

        //$out_trade_no = $reqJson['out_trade_no'];//商户系统内部的订单号,32个字符内、可包含字母
        //$total_fee = $reqJson['total_fee'];//订单总金额，单位为分
        //$openid = $reqJson['openid'];//trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识
        //$body = $reqJson['body'];//商品或支付单简要描述

        $input = new WxPayUnifiedOrder();
        $input->SetBody($reqJson['body']);//商品或支付单简要描述
        $input->SetOut_trade_no($reqJson['out_trade_no']);//商户系统内部的订单号,32个字符内、可包含字母
        $input->SetTotal_fee($reqJson['total_fee']);//订单总金额，单位为分
        $input->SetOpenid($reqJson['openid']);//trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识        
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/weixinpub/wechatpay/callback");//接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("JSAPI");//交易类型
        $input->SetAppid($result->getAppId());//公众账号ID
        $input->SetMch_id($result->getMchId());//商户号
        $input->SetApi_key($result->getApiKey());//商户号
        
        try {
            $order = WxPayApi::unifiedOrder($input);
            $jsApiParameters = $this->GetJsApiParameters($order);
            $output->flag = 0;
            $output->info = $jsApiParameters; 
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
            return var_export($UnifiedOrderResult);
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

    
}
