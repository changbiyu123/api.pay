<?php

class WeixinController extends MobiledoctorController {

    private $token = 'DB0B22E5C8521B41A8750476A9F66DCD';

    /**
     * 用来接收微信消息和事件的接口URL.
     * 1. 将token、timestamp、nonce三个参数进行字典序排序
     * 2. 将三个参数字符串拼接成一个字符串进行sha1加密
     * 3. 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
     */
    public function actionNotify() {
        $get = $_GET;
        // log request url in db.
        $requestUrl = Yii::app()->request->url;
        Yii::log($requestUrl, CLogger::LEVEL_ERROR, __METHOD__);
        // validate request parameters.
        if (isset($get['signature']) === false || isset($get['timestamp']) === false || isset($get['nonce']) === false || isset($get['echostr']) === false) {
            echo 'missing parameters';
            exit;
            //$this->throwPageNotFoundException();
        }
        $signature = $get['signature'];
        $timestamp = $get['timestamp'];
        $nonce = $get['nonce'];
        //@TODO: 
        //字典序排序...
        //$sign = sha1().
        if ($this->checkSignature()) {
            echo $get['echostr'];
        } else {
            
        }
    }

    /**
     * 微信全局基础access_token. 与OAuth2的access_token不同.
     * gets access_token from weixin.
     * access_token expires in 7200 secs (2 hours).
     * 每天最多只可获取2000个access_token.
     */
    public function actionGetAccessToken() {
        $wxconfig = new WeixinConfig();
        $requestUrl = $wxconfig->getUrlAccessToken();
        // send http get request to get access_token from weixin.
        $ch = curl_init($requestUrl);
        //$ch = curl_init('http://localhost/myzd/mobiledoctor/wx/getUrlAccessToken');
        //$ch = curl_init('http://192.168.0.128/myzd/mobiledoctor/wx/getUrlAccessToken');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回  
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  
        $output = curl_exec($ch);
        $outputJson = CJSON::decode($output);
        //$output = '{"access_token":"i1hjaE1ZDg7iQYQNK7kNexCoysViuDqBjG9kt4zarcVNb0ikjayVA-nAMTMUkP4rwtXGooIGwxkTL706QdJo0HxhMVFzCpOQGu-QuCsEQ8sLFUgAJARLA","expires_in":7200}';
        //$outputJson = CJSON::decode($output);
        //@TODO: save access_token in db.
        var_dump($outputJson);
    }

    /**
     * @DEMO
     */
    /*
      public function actionGetUrlAccessToken() {
      $wxconfig = new WeixinConfigMYZDZTC();
      $url = $wxconfig->getUrlAccessToken();
      echo $url;
      }
     */

    /*     * ****** Get OpenID 2 ******* */
    /**
     * http:mingyizhudao.com/order/view  调用此方法。
     */
    
    
    public function actionGetWxCode($refno) {
        require_once('protected/sdk/pingpp-php-master/init.php');
        $redirectUrl = $this->createAbsoluteUrl("/weixin/getWxOpenId", array('ref_no'=>$refno));
        $wxConfig = new WeixinConfig();
        //$wxAppId = 'wxb6dc36522aae7df2'; //@TODO: store in db.
        $wxAppId = $wxConfig->getAppId();

        $url = \Pingpp\WxpubOAuth::createOauthUrlForCode($wxAppId, $redirectUrl);
        CoreLogPayment::log($url, 'info', null, __METHOD__);

        header('Location: ' . $url);
    }

    public function actionGetWxOpenId($refno) {
        CoreLogPayment::log(Yii::app()->request->querystring, 'info', null, __METHOD__);
        if (isset($_GET['code']) === false) {
            $output = array('status' => 'no', 'errorMsg' => '请求参数错误');
            $this->renderJsonOutput($output);
        }
        $code = $_GET['code'];
        $wxConfig = new WeixinConfig();
        //$wxAppId = 'wxb6dc36522aae7df2';
        //$wxAppSecret = 'e70db8f5ea5baa991d71c0be3047b339';
        $wxAppId = $wxConfig->getAppId();
        $wxAppSecret = $wxConfig->getAppSecret();        
        require_once('protected/sdk/pingpp-php-master/init.php');
        $openid = \Pingpp\WxpubOAuth::getOpenid($wxAppId, $wxAppSecret, $code);

        $this->redirect('http://mingyizhudao.com/order/view?openid=' . $openid.'refno='.$refno);
        //$this->redirect('http://md.mingyizhudao.com/test/pingpp-html5-one/demo/demo.php?openid=' . $openid);
    }

    /*     * ****** Get OpenID ******* */

    /**
     * step 1.
     * send request to get OAuth2 code.
     */
    public function actionGetUrlOAuth2Code() {
        $redirectUrl = urlencode('http://md.mingyizhudao.com/mobiledoctor/wx/oAuth2CodeReturn');
        $wxconfig = new WeixinConfig();
        $url = $wxconfig->getUrlOAuth2Code($redirectUrl, 'snsapi_userinfo');
        echo $url;
    }

    /**
     * step 2.
     * receives response from weixin. if(isset($_GET['code']) 代表成功获取code.
     * send request to get access_token.
     */
    // {"access_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2styS5BziXYndt5caq5Ep4FHdBeD4WSLzEkAz00KdYMtMDQ7WopQlZae6TzbeMOrVrfy-lqOc1AaVwfTGtvJgS3qA","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2stZJjNpN5slUNidfszpdJo_uYjdu3WOT7ozLJJrdxKxsixGJWtyrNtiItR3hkDoFaA44vLjnkZJLCgTIxOA9VBCw","openid":"o9D7bsrlWC5ecKJdSuyVAYLedjVc","scope":"snsapi_base"}
    // {"access_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2stxwMM6U3v8jYKfJnvh9Kw8xP6hEnokV0-hyIQw2cPBY01MSLUcnk7u4blq_rUnMDQ5tztYn-rKOx16V2_zL4XtQ","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2stvn3X8jcbE1uli7pGzLvudnGtcPAVCPLqhd7DdlzS_gRHi6J7hCCtB3EKV53WKOTzY_HbXZCjSlxwebWd2eB79g","openid":"o9D7bsrlWC5ecKJdSuyVAYLedjVc","scope":"snsapi_base"}"
    // {"access_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2stIuKchxzxPaZHHABg0T4W1Ctrd5xeC0QOYgzayMPEDTsncT9DGRueNzmltzJaG1EsMpmh4UIwVHC7EEhC05dvTA","expires_in":7200,"refresh_token":"OezXcEiiBSKSxW0eoylIeHyhm1wyf4JV24XudsExwWp7pLo3_ndNZE_efP7Me2stHYKFrfGndv1bZcsWpTy18sJeOzJnw6sXeU6g5RGpeQpfAXTjAaVUTj8t9oM_a08bVdsG_TOGr7PP08C53gpC0A","openid":"o9D7bsrlWC5ecKJdSuyVAYLedjVc","scope":"snsapi_base"}"
    public function actionOAuth2CodeReturn() {
        $requestUrl = Yii::app()->request->url;
        Yii::log($requestUrl, CLogger::LEVEL_ERROR, __METHOD__);
        //  var_dump($requestUrl);

        $get = $_GET;
        if (isset($get['code']) === false) {
            'missing code';
        } else {
            $code = $get['code'];
            echo 'code: <br>' . $code;

            $wxconfig = new WeixinConfig();
            $requestUrl = $wxconfig->getUrlOAuth2AccessToken($code);
            echo 'sending request to :<br>' . $requestUrl . '<br>';
            // send http get request to get access_token from weixin.
            $ch = curl_init($requestUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 获取数据返回  
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回  
            $output = curl_exec($ch);
            var_dump($output);
        }
    }

    // 检验signature的PHP示例代码：
    private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

}
