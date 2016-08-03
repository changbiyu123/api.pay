<?php
/**
 * 微信红包
 *
 * @author zhongtw
 */
class WxRedpack extends WxPayDataBase {
    
    protected $values = array();
    
    public function SetSign(){
        $sign = $this->MakeSign();
        $this->values['sign'] = $sign;
        return $sign;
    }
    
     public function MakeSign(){
        $wechatAccount = new WechatAccount();
        $api_key = $wechatAccount->getByAppId($this->values['wxappid'])->getApiKey();
        
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=" . $api_key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    
    public function SetNonce_str($value){
        $this->values['nonce_str'] = $value;
    }
    
    public function SetMch_billno($value){
        $this->values['mch_billno'] = $value;
    }
    
    public function SetMch_id($value){
        $this->values['mch_id'] = $value;
    }
    
    public function SetWxappid($value){
        $this->values['wxappid'] = $value;
    }
    
    public function SetSend_name($value){
        $this->values['send_name'] = $value;
    }
    
    public function SetRe_openid($value){
        $this->values['re_openid'] = $value;
    }
    
    public function SetTotal_amount($value){
        $this->values['total_amount'] = $value;
    }
    
    public function SetTotal_num($value){
        $this->values['total_num'] = $value;
    }
    
    public function SetWishing($value){
        $this->values['wishing'] = $value;
    }
    
    public function SetClient_ip($value){
        $this->values['client_ip'] = $value;
    }
    
    public function SetAmt_type($value){
        $this->values['amt_type'] = $value;
    }
    
    public function SetAct_name($value){
        $this->values['act_name'] = $value;
    }
    
    public function SetRemark($value){
        $this->values['remark'] = $value;
    }
    
}
