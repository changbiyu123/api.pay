<?php

namespace frontend\component;

use frontend\component\AlipaySubmit;

class AliwappayPay {

    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    /**
     * @var String 合作身份者id，以2088开头的16位纯数字
     */
    public $partner = '';
    
    public $sign_type = 'RSA';
    /**
     * @var String 收款支付宝账号
     */
    public $seller_id = '';
    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
    public $input_charset = "utf-8";

    /**
     * @var String 服务器异步通知页面路径
     * 需http://格式的完整路径，不能加?id=123这类自定义参数
     */
    public $notify_url = '';

    /**
     * @var String 页面跳转同步通知页面路径
     * 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
     */
    public $return_url = '';
    //超时时间 选填

    public $it_b_pay = "";
    //钱包token 选填
    public $extern_token = "";
    //商户的私钥（后缀是.pen）文件相对路径
    public $private_key_path = "../component/rsa_private_key.pem";
    //支付宝公钥（后缀是.pen）文件相对路径
    public $ali_public_key_path = "../component/rsa_public_key.pem";
    
    //请保证cacert.pem文件在当前文件夹目录中
    public $cacert = "../component/cacert.pem";
    
    public $transport = "http";

    //public $extra_common_param = '';

    /**
     * @name requestPay
     * @desc
     * @param $out_trade_no String 商户订单号，商户网站订单系统中唯一订单号，必填
     * @param $subject String 订单名称
     * @param $total_fee String 付款金额
     * @param $body String 订单描述
     * @param $show_url String 商品展示地址
     * @return String 跳转HTML
     */
    public function requestPay($out_trade_no, $subject, $total_fee, $body, $show_url) {
        /*         * ************************请求参数************************* */
        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //防钓鱼时间戳 

        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.create.direct.pay.by.user",
            "partner" => trim($this->partner),
            "seller_id" => trim($this->seller_id),
            "payment_type" => $payment_type,
            "notify_url" => $this->notify_url,
            "return_url" => $this->return_url,
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "show_url" => $show_url,
            "body" => $body,
            "it_b_pay" => $this->it_b_pay,
            "extern_token" => $this->extern_token,
            "_input_charset" => trim(strtolower($this->input_charset))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($this->bulidConfig());

        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        return $html_text;
    }

    public function verifyReturn() {
        $alipayNotify = new AlipayNotify($this->bulidConfig());
        $verify_result = $alipayNotify->verifyReturn();

        return $verify_result;
    }

    private function bulidConfig() {
        //构造要请求的配置数组
        $alipay_config = array(
            'partner' => trim($this->partner),
            'seller_id' => trim($this->seller_id),
            'sign_type' => $this->sign_type,
            'input_charset' => $this->input_charset,
            'cacert' => $this->cacert,
            'transport' => $this->transport,
            "ali_public_key_path"=>$this->ali_public_key_path,
            "private_key_path"=>$this->private_key_path,
        );

        return $alipay_config;
    }

}
