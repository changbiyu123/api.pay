<?php
class TranslateServerController  extends RPCController{

	
	function actionMember(){
	    $this->rpc->rpcServer('member', 'translate');	
	}
	
    
    
}