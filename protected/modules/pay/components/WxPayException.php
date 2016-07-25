<?php


/**
 * 微信支付异常处理
 *
 * @author zhongtw
 */
class WxPayException extends Exception {
    
    public function errorMessage(){
	return $this->getMessage();
    }
        
}
