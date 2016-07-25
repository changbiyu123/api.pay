<?php

class PayRPCClientController extends RPCController {

    private $server_translate_url;

    function init() {
        parent::init();
        $this->server_translate_url = 'http://' . $_SERVER['HTTP_HOST'] . '/rpc/RpcServer/';
//        $this->server_translate_url =$this->getServiceUrl('translateUrl');
        //http://api.pay.com/rpc/RpcServer/Member
    }

    public function actionTest() {
        $url = $this->server_translate_url . 'member';
        $x = $this->rpc->rpcClient($url);
        $f = $x->getData1();
        print_r($f);
        exit;
    }

}
