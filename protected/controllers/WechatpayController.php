<?php


/**
 * 微信支付接口
 *
 * @author zhongtw
 */
define("APPID", "wx7d25d315fe1ea4dc");
define("MCHID", "1346722601");
require_once 'protected/config/WxPayApi.php';
header("Content-type: text/html; charset=utf-8");
class WechatpayController extends Controller {
    
    public function actionTest(){
        echo 12345;
        exit;
        //Yii::app()->exit();
    }
    
    /**
     * 微信公众号支付
     */
    public function actionJsapipay(){
        $reqStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $reqJson = json_decode($reqStr, true);
        
        $out_trade_no = $reqJson['out_trade_no'];//商户系统内部的订单号,32个字符内、可包含字母
        $total_fee = $reqJson['total_fee'];//订单总金额，单位为分
        $openid = $reqJson['openid'];//trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识
        $body = $reqJson['body'];//商品或支付单简要描述
        
        $input = new WxPayUnifiedOrder();
        $input->SetBody($body);//商品或支付单简要描述
        $input->SetOut_trade_no($out_trade_no);//商户系统内部的订单号,32个字符内、可包含字母
        $input->SetTotal_fee($total_fee);//订单总金额，单位为分
        $input->SetOpenid($openid);//trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识
        
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/weixinpub/wechatpay/callback");//接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        $input->SetTrade_type("JSAPI");//交易类型
        $input->SetAppid(APPID);//公众账号ID
        $input->SetMch_id(MCHID);//商户号
               
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $this->GetJsApiParameters($order);
        
        $output = new stdClass();
        $output->rspStr = $jsApiParameters;        
        return $this->renderJsonOutput($output);
    }
    
    
   /**
    * 
    * 获取jsapi支付的参数
    * @param array $UnifiedOrderResult 统一支付接口返回的数据
    * @throws WxPayException
    * 
    * @return json数据，可直接填入js函数作为参数
    */
    public function GetJsApiParameters($UnifiedOrderResult){
        if(!array_key_exists("appid", $UnifiedOrderResult) || !array_key_exists("prepay_id", $UnifiedOrderResult) || $UnifiedOrderResult['prepay_id'] == ""){
            echo var_export($UnifiedOrderResult);
            Yii::app()->end();
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
