<?php

class ApiViewCity extends EApiViewService {
    private $country_id = 1;
    public function __construct() {
        parent::__construct();
        $this->results = new stdClass();
    }

    protected function loadData() {
        $this->loadCity();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }
    public function loadCity(){
        $data = array();
        $states = CHtml::listData(RegionState::model()->getAllByCountryId($this->country_id), 'id', 'name');
        foreach($states as $state_id=>$state_name){
            $subCity = array();
            $cities = CHtml::listData(RegionCity::model()->getAllByStateId($state_id), 'id', 'name');
            foreach($cities as $city_id=>$city_name){
                $subCity[] = array('id'=>$city_id, 'city'=>$city_name);
            }
            $data[] = array('id'=>$state_id, 'state'=>$state_name, 'subCity'=>$subCity);
        }
        $this->setCity($data);
    }

    private function setCity($data){
        $this->results = $data;
    }


}
