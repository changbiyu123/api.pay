<?php



class AlipayController extends Controller {

    public $_requestData;

    public function init() {
        $getData = Yii::$app->request->get();
        $postData = Yii::$app->request->post();
        $this->_requestData = array_filter(array_merge($getData, $postData));
    }

    public function actionAlipay() {
        if (!isset($this->_requestData["out_trade_no"]) || !isset($this->_requestData["return"]) || !isset($this->_requestData["show"])) {
            echo json_encode(["code" => 40002, "msg" => "参数不正确"]);
            exit;
        }
        $ali = new AlipayPay();
        $params = Yii::$app->params["ali"];

        $ali->partner = $params["partner"];
        $ali->key = $params["key"];
        $ali->seller_email = $params["seller_email"];
        $ali->sign_type = $params["sign_type"];
        $ali->transport = $params["transport"];
        $ali->cacert = $params["cacert"];

        $ali->notify_url = $params["notify_url"];
        $ali->return_url = $this->_requestData["return"];

        $out_trade_no = $this->_requestData["out_trade_no"];

        $master = LinshiOrderMaster::getInfoById($out_trade_no);
        if (empty($master)) {
            echo CJSON::encode(["code" => 40021, "err_msg" => "订单不存在"]);
            exit;
        }
        $detail = $master->detail;

        if (empty($detail)) {
            echo CJSON::encode(["code" => 40021, "err_msg" => "订单不存在"]);
            exit;
        }
        $subject = "订单:" . $detail->course_name;

        $body = [
            "订单时间:" . $detail->class_begin_time,
        ];
        $fee = $master->payment_price;

        $html = $ali->requestPay($out_trade_no, $subject, $fee, implode(".", $body), $this->_requestData["show"]);
        //echo json_encode(["code"=>40000,"msg"=>"获取信息成功","data"=>["html"=>$html]]);exit;
        echo $html;
        exit;
    }

    public function actionCallback() {
        $params = Yii::$app->params["ali"];
        $pay_status = Yii::$app->params["pay_status"];
        $pay_method = Yii::$app->params["pay_method"];

        parent::write_log("aliwappay/callback", $this->_requestData);

        $alipay_config = array(
            'partner' => $params["partner"],
            'key' => $params["key"],
            'sign_type' => $params["sign_type"],
            'cacert' => $params["cacert"],
            'transport' => $params["transport"],
        );

        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            //商户订单号
            $out_trade_no = $this->_requestData['out_trade_no'];

            //支付宝交易号
            $trade_no = $this->_requestData['trade_no'];

            //交易状态
            $trade_status = $this->_requestData['trade_status'];

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            $model = new LinshiOrderMaster();
            $model->updateOrder($out_trade_no, $pay_status["success"], $pay_method["ali"]);
            echo "success";  //请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
    }

    /**
     * 支付日志
     */
    private function _aliLog() {

        Yii::log($message, $level, $category);
    }

}
