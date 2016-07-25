<?php

class RPCController extends Controller {

    public $rpc;

    public function init() {
        parent::init();
        $this->rpc = new RPC();
    }

    
    public function getServiceUrl($name){
        return Yii::app()->params[$name];
    }
}
