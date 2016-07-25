<?php

class HomeController extends Controller {
    
    public function actionTest() {
        echo 3333;
        exit;
    }
    
    public function actionDbtest(){
        $weixinpub_id = 'myzdtest';
        $wechatAccount = new WechatAccount();
        //echo var_export($wechatAccount->getByPubId($weixinpub_id));
        $wechatAccount = $wechatAccount->getByPubId($weixinpub_id);
        $appId = $wechatAccount->getAppId();
        echo "$appId";
    }
    
    public function actionDbtest1(){
        $weixinpub_id = 'myzdtest';
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId($weixinpub_id);
        $appId = $result->getAppId();
        echo "$appId";
    }
    
    /**
     * rpc demo
     */

}
