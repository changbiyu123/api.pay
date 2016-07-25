<?php

class RPC {

    function __construct() {
        
    }

    /**
     * rpc client
     */
    public function rpcClient($url) {
        require_once 'jsonRPCClient.php';
        //$url = 'http://localhost/rpc_server/server.php';
        $myExample = new jsonRPCClient($url);
        // 客户端调用
        try {
            return $myExample;
        } catch (Exception $e) {
            echo nl2br($e->getMessage()) . '<br />' . "\n";
        }
    }

    /**
     * rpc server
     */
    public function rpcServer($class, $module, $dir = '') {
        require_once 'jsonRPCServer.php';
        try {
            if ($dir != '') {
                $file = dirname(__FILE__) . '/../modules/' . $module . '/server/' . $dir . '/' . $class . '.php';
            } else {
                $file = dirname(__FILE__) . '/../modules/' . $module . '/server/' . $class . '.php';
            }
            if (file_exists($file)) {
                include $file;
                $myExample = new $class();
                // 注入实例
                jsonRPCServer::handle($myExample)
                        or print 'no request';
            } else {
                $errors = array();
                $errors[] = "Not Found " . $class . " class";
                throw new CException("Not Found " . $class . " class.");
            }
        } catch (CException $ex) {
            $output['status'] = false;
            $output['errors'] = $errors;
            $this->renderJsonOutput($output);
        }
        // 服务端调用
    }

}

?>