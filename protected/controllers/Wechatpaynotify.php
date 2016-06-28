<?php

/**
 * 微信支付通知
 *
 * @author zhongtw
 */
class Wechatpaynotify {
    
    
    /**
     * 微信支付结果通知
     */
    public function actionCallback() {
        //获取通知的xml数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        Yii::log($xml);
        //将xml格式的数据转为数组
        $arr = CommonConfig::FromXml($xml);
        //如果返回的状态码是SUCCESS，并且签名验证通过
        if($arr['return_code'] == 'SUCCESS' && $arr['sign'] == $this->MakeSign($arr)){
            //在这里进行数据页面处理，然后再返回数据
            
            
            $return = array("return_code"=>"SUCCESS", "return_msg"=>"OK");
            echo $this->ToXml($return);
        }else{
            Yii::log("返回异常");
        }
     
        Yii::app()->end();
    }
    
        /**
     * 微信支付签名算法
     * @param type $arr
     */
    public function MakeSign($arr){    
        ksort($arr);//签名步骤一：按字典序排序参数        
        $string = CommonConfig::ToUrlParams($arr);//格式化参数       
        $string = $string . "&key=".WxPayConfig::KEY;//签名步骤二：在string后加入KEY        
        $string = md5($string);//签名步骤三：MD5加密        
        $result = strtoupper($string);//签名步骤四：所有字符转为大写
        return $result;
    }
    
    /**
     * 将array转为xml，输出xml字符
     * @return string
     */
    public static function ToXml($arr){
        if(!is_array($arr) || count($arr) <= 0){
            Yii::log("数组数据异常");
    	}    	
    	$xml = "<xml>";
    	foreach ($arr as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml; 
    }
    
    
}
