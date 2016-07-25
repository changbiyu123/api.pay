<?php

class RpcServerController extends RPCController {

    function actionMember() {
//        echo 3333;exit;
        $this->rpc->rpcServer('member_1', 'rpc','user');
    }

}
