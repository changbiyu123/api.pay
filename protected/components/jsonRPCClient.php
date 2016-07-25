<?php

/*
 */

class jsonRPCClient {

    private $debug;
    private $url;
    // 请求id
    private $id;
    private $notification = false;

    /**
     * @param $url
     * @param bool $debug
     */
    public function __construct($url, $debug = false) {
        // server URL
        $this->url = $url;
        // proxy
        empty($proxy) ? $this->proxy = '' : $this->proxy = $proxy;
        // debug state
        empty($debug) ? $this->debug = false : $this->debug = true;
        // message id
        $this->id = 1;
    }

    /**
     *
     * @param boolean $notification
     */
    public function setRPCNotification($notification) {
        empty($notification) ? $this->notification = false : $this->notification = true;
    }

    /**
     * @param $method
     * @param $params
     * @return bool
     * @throws Exception
     */
    public function __call($method, $params) {
        // 检验request信息
        if (!is_scalar($method)) {
            throw new Exception('Method name has no scalar value');
        }
        if (is_array($params)) {
            $params = array_values($params);
        } else {
            throw new Exception('Params must be given as array');
        }

        if ($this->notification) {
            $currentId = NULL;
        } else {
            $currentId = $this->id;
        }

        // 拼装成一个request请求
        $request = array('method' => $method, 'params' => $params, 'id' => $currentId);
        $request = json_encode($request);
        $this->debug && $this->debug.='***** Request *****' . "\n" . $request . "\n" . '***** End Of request *****' . "\n\n";
        $opts = array('http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $request
        ));
        //  关键几部
        $context = stream_context_create($opts);
        if ($result = file_get_contents($this->url, false, $context)) {
            $response = json_decode($result, true);
        } else {
            throw new Exception('Unable to connect to ' . $this->url);
        }
        // 输出调试信息
        if ($this->debug) {
            echo nl2br(($this->debug));
        }
        // 检验response信息
        if (!$this->notification) {
            // check
            if ($response['id'] != $currentId) {
                throw new Exception('Incorrect response id (request id: ' . $currentId . ', response id: ' . $response['id'] . ')');
            }
            if (!is_null($response['error'])) {
                throw new Exception('Request error: ' . $response['error']);
            }
            return $response['result'];
        } else {
            return true;
        }
    }

}

?>