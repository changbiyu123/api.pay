<?php
class jsonRPCServer {
    /**
     *处理一个request类，这个类中绑定了一些请求参数
     * @param object $object
     * @return boolean
     */
    public static function handle($object) {
       // 判断是否是一个rpc json请求
        if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SERVER['CONTENT_TYPE'])
            ||$_SERVER['CONTENT_TYPE'] != 'application/json') {
            return false;
        }
        // reads the input data
        $request = json_decode(file_get_contents('php://input'),true);
        // 执行请求类中的接口
        try {
            if ($result = @call_user_func_array(array($object,$request['method']),$request['params'])) {
                $response = array ( 'id'=> $request['id'],'result'=> $result,'error'=> NULL );
            } else {
                $response = array ( 'id'=> $request['id'], 'result'=> NULL,
                                        'error' => 'unknown method or incorrect parameters' );}
        } catch (Exception $e) {
            $response = array ('id' => $request['id'],'result' => NULL, 'error' =>$e->getMessage());
        }
       // json 格式输出
        if (!empty($request['id'])) { // notifications don't want response
            header('content-type: text/javascript');
            echo json_encode($response);
        }
        return true;
    }
}