<?php

class RpcDemoController extends RPCController {

    /**
     * rpc demo 客户端
     */
    public function actionRpcClient() {
//        echo $this->createUrl('rpcDemo/RpcSerivce');exit;
        $this->rpc->rpcClient($this->createUrl('rpcDemo/RpcSerivce'));
    }
    /**
     * rpc demo 服务器端
     */
    public function actionRpcService() {
        echo 4444;exit;
        $this->rpc->rpcClient('');
    }

}
