<?php
/**
 * 用于企业向微信用户个人发送金红包
 *
 * @author zhongtw
 */
class RedpackapiController extends Controller {
    
    public function actionTest001() {
        $output = new stdClass();
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId('123');
        
        if(isset($result)){

        } else{
            $output->flag = 1;
            $output->info = 'pub_id is not exist';
            return $this->renderJsonOutput($output);
        }
    }
    
    
    //向微信用户发送普通现金红包
    public function actionSendredpack(){
        
        $output = new stdClass();
        $reqStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        $reqJson = json_decode($reqStr, true);
        if(!isset($reqJson['pub_id']) || !isset($reqJson['mch_billno']) || !isset($reqJson['re_openid'])
                || !isset($reqJson['total_amount']) || !isset($reqJson['wishing']) || !isset($reqJson['act_name']) || !isset($reqJson['remark'])){
            $output->flag = 1;
            $output->info = 'missing payment parameters';
            return $this->renderJsonOutput($output);
        }
        $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";//普通红包发放接口
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId($reqJson['pub_id']);
        if(!isset($result)){
            $output->flag = 1;
            $output->info = 'pub_id is not exist';
            return $this->renderJsonOutput($output);
        }
        $wxRedpack = new WxRedpack();
        $wxRedpack->SetNonce_str(WxPayApi::getNonceStr());//随机字符串
        $wxRedpack->SetMch_billno($reqJson['mch_billno']);//商户订单号
        $wxRedpack->SetMch_id($result->getMchId());//微信支付分配的商户号
        $wxRedpack->SetWxappid($result->getAppId());//公众账号appid
        $wxRedpack->SetSend_name("名医主刀");//商户名称
        $wxRedpack->SetRe_openid($reqJson['re_openid']);//用户openid
        $wxRedpack->SetTotal_amount($reqJson['total_amount']);//付款金额
        $wxRedpack->SetTotal_num("1");//红包发放总人数
        $wxRedpack->SetWishing($reqJson['wishing']);//红包祝福语
        $wxRedpack->SetClient_ip($_SERVER['REMOTE_ADDR']);//调用接口的机器Ip地址
        $wxRedpack->SetAct_name($reqJson['act_name']);//活动名称
        $wxRedpack->SetRemark($reqJson['remark']);//备注信息
        $wxRedpack->SetSign();//生成签名
        $returnXml = WxPayCert::curl_post_ssl($url, $wxRedpack->ToXml());//调用微信发送红包接口,并接受返回的XML数据
        Yii::log($returnXml);
        $wxResponse = $wxRedpack->FromXml($returnXml);//将XML格式数据转换为数组格式
        if($wxResponse['return_code'] == 'SUCCESS' && $wxResponse['result_code'] == 'SUCCESS'){//发送成功
            $output->flag = 0;
            $output->info = 'send success';
        }else{//发送失败
            $output->flag = 1;
            $output->info = 'send fail';
        }
        return $this->renderJsonOutput($output);
    }
    
}
