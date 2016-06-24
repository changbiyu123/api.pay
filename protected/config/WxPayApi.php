<?php


/**
 * Description of WxPayApi
 *
 * @author zhongtw
 */
require_once "WxPayException.php";
require_once "WxPayConfig.php";
class WxPayApi {

    
    /**
     * 
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * @param type $timeOut
     * @return type
     * @throws WxPayException
     */
    public static function unifiedOrder($inputObj, $timeOut = 6){
        
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        
        //检测必填参数
        if(!$inputObj->IsOut_trade_noSet()) {
                throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
        }else if(!$inputObj->IsBodySet()){
                throw new WxPayException("缺少统一支付接口必填参数body！");
        }else if(!$inputObj->IsTotal_feeSet()) {
                throw new WxPayException("缺少统一支付接口必填参数total_fee！");
        }else if(!$inputObj->IsTrade_typeSet()) {
                throw new WxPayException("缺少统一支付接口必填参数trade_type！");
        }

        //关联参数
        if($inputObj->GetTrade_type() == "JSAPI" && !$inputObj->IsOpenidSet()){
                throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        }
        if($inputObj->GetTrade_type() == "NATIVE" && !$inputObj->IsProduct_idSet()){
                throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        }

        //异步通知url未设置，则使用配置文件中的url
        if(!$inputObj->IsNotify_urlSet()){
                $inputObj->SetNotify_url(WxPayConfig::NOTIFY_URL);//异步通知url
        }

        $inputObj->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip	   	    
        $inputObj->SetNonce_str(self::getNonceStr());//随机字符串

        //签名
        $inputObj->SetSign();
        $xml = $inputObj->ToXml();
        //echo var_export($inputObj);//测试用
        //Yii:app()->end();
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        $result = WxPayResults::Init($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间

        return $result;
    }  
    
    
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32) {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {  
                $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        } 
        return $str;
    }
    
    
    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond(){
        //获取毫秒的时间戳
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
    
    
    /**
     * 以post方式提交xml到对应的接口url
     * 
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    private static function postXmlCurl($xml, $url, $useCert = false, $second = 30){	
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if(WxPayConfig::CURL_PROXY_HOST != "0.0.0.0" && WxPayConfig::CURL_PROXY_PORT != 0){
            curl_setopt($ch,CURLOPT_PROXY, WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, WxPayConfig::CURL_PROXY_PORT);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){  
            curl_close($ch);
            return $data;
        } else { 
            $error = curl_errno($ch);
            curl_close($ch);
            throw new WxPayException("curl出错，错误码:$error");
        }
    }
    
    
    /**
     * 
     * 上报数据， 上报的时候将屏蔽所有异常流程
     * @param string $usrl
     * @param int $startTimeStamp
     * @param array $data
     */
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
            //如果不需要上报数据
            if(WxPayConfig::REPORT_LEVENL == 0){
                    return;
            } 
            //如果仅失败上报
            if(WxPayConfig::REPORT_LEVENL == 1 &&
                     array_key_exists("return_code", $data) &&
                     $data["return_code"] == "SUCCESS" &&
                     array_key_exists("result_code", $data) &&
                     $data["result_code"] == "SUCCESS")
             {
                    return;
             }

            //上报逻辑
            $endTimeStamp = self::getMillisecond();
            $objInput = new WxPayReport();
            $objInput->SetInterface_url($url);
            $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
            //返回状态码
            if(array_key_exists("return_code", $data)){
                    $objInput->SetReturn_code($data["return_code"]);
            }
            //返回信息
            if(array_key_exists("return_msg", $data)){
                    $objInput->SetReturn_msg($data["return_msg"]);
            }
            //业务结果
            if(array_key_exists("result_code", $data)){
                    $objInput->SetResult_code($data["result_code"]);
            }
            //错误代码
            if(array_key_exists("err_code", $data)){
                    $objInput->SetErr_code($data["err_code"]);
            }
            //错误代码描述
            if(array_key_exists("err_code_des", $data)){
                    $objInput->SetErr_code_des($data["err_code_des"]);
            }
            //商户订单号
            if(array_key_exists("out_trade_no", $data)){
                    $objInput->SetOut_trade_no($data["out_trade_no"]);
            }
            //设备号
            if(array_key_exists("device_info", $data)){
                    $objInput->SetDevice_info($data["device_info"]);
            }

            try{
                    self::report($objInput);
            } catch (WxPayException $e){
                    //不做任何处理
            }
    }
 
   
}
