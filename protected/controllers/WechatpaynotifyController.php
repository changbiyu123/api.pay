<?php

/**
 * 微信支付通知
 *
 * @author zhongtw
 */
class WechatpaynotifyController extends Controller {
    
    
    /**
     * 微信支付结果通知
     */
    public function actionCallback() {
        //获取通知的xml数据
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        Yii::log($xml);
        //将xml格式的数据转为数组
        $arr = $this->FromXml($xml);
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
        $wechatAccount = new WechatAccount();
        $api_key = $wechatAccount->getByAppId($arr['appid'])->getApiKey();
        ksort($arr);//签名步骤一：按字典序排序参数        
        $string = $this->ToUrlParams($arr);//格式化参数       
        $string = $string . "&key=".$api_key;//签名步骤二：在string后加入KEY        
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
            return null;
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
    
    /**
     * 将xml转为array
     * @param string $xml
     */
    public static function FromXml($xml){	
        if(!$xml){
            return null;
        }       
        libxml_disable_entity_loader(true);//禁止引用外部xml实体
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
        return $arr;
    } 
    
    /**
     * 格式化参数,格式化成url参数
     */
    public static function ToUrlParams($arr){
        $buff = "";
        foreach ($arr as $k => $v){
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
    
    
}
