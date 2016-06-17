<?php

class ApiRequestUrl {

    public $hostInfoProd = 'http://crm.mingyizd.com';
    private $admin_salesbooking_create = 'api/adminbooking';

    public function getHostInfo() {
        $hostInfo = strtolower(Yii::app()->request->hostInfo);
        if (strStartsWith($hostInfo, $this->hostInfoProd)) {
            $this->hostInfoProd = $hostInfo . '/admin';
        }
    }

    public function getUrl($url) {
        $this->getHostInfo();
        return $this->hostInfoProd . '/' . $url;
    }

    public function getUrlAdminSalesBookingCreate() {
        return $this->getUrl($this->admin_salesbooking_create);
    }

}
