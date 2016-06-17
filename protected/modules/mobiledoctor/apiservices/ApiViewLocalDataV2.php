<?php

class ApiViewLocalDataV2 extends EApiViewService {
    private $country_id = 1;
    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {
        $this->loadAcademicTitle();
        $this->loadClinicalTitle();
        $this->loadGender();
        $this->loadBookingTravelType();
        $this->loadCity();
    }

    protected function createOutput() {

        if (is_null($this->output)) {
            $this->results->url = array(
                'sendCodeUrl' => Yii::app()->createAbsoluteUrl('/apimd/smsverifycode'),
                'loginUrl' => Yii::app()->createAbsoluteUrl('/apimd/userlogin'),
                'createPatientUrl' => Yii::app()->createAbsoluteUrl('/apimd/patient'),
                'createPatientfileUrl' => Yii::app()->createAbsoluteUrl('/apimd/patientfile'),
                'createPatientbooking' =>  Yii::app()->createAbsoluteUrl('/apimd/patientbooking'),
                'myPatientUrl' => Yii::app()->createAbsoluteUrl('/apimd/patient'),
                'sendBookingUrl' => Yii::app()->createAbsoluteUrl('/apimd/sendbooking'),
                'receiveBookingUrl' => Yii::app()->createAbsoluteUrl('/apimd/receivebooking'),
                'patientinfoUrl' => Yii::app()->createAbsoluteUrl('/apimd/view', array('model'=>'patientinfo', 'id'=>'')),
                'patientfileUrl' => Yii::app()->createAbsoluteUrl('/apimd/view', array('model'=>'patientfile', 'id'=>'')),
                'doctorpatientinfoUrl' => Yii::app()->createAbsoluteUrl('/apimd/view', array('model'=>'doctorpatientinfo', 'id'=>'')),
                'doctorpatientfileUrl' => Yii::app()->createAbsoluteUrl('/apimd/view', array('model'=>'doctorpatientfile', 'id'=>'')),
                'profileUrl' => Yii::app()->createAbsoluteUrl('/apimd/profile'),
                'profilefileUrl' => Yii::app()->createAbsoluteUrl('/apimd/profilefile'),
                'applycontractUrl' => Yii::app()->createAbsoluteUrl('/apimd/applycontract'),
                'paymentUrl' => 'http://m.mingyizhudao.com/mobile/order/view?refNo=#&os=#&header=0&footer=0&addBackBtn=0',
            );

            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    public function loadAcademicTitle(){
        $data = StatCode::getOptionsAcademicTitle();
        $this->setAcademicTitle($data);
    }

    private function setAcademicTitle($data){
        $this->results->academicTitle = $data;
    }

    public function loadClinicalTitle(){
        $data = StatCode::getOptionsClinicalTitle();
        $this->setClinicalTitle($data);
    }

    private function setClinicalTitle($data){
        $this->results->clinicalTitle = $data;
    }

    public function loadGender(){
        $data = StatCode::getOptionsGender();
        $this->setGender($data);
    }

    private function setGender($data){
        $this->results->gender = $data;
    }

    public function loadBookingTravelType(){
        $data = StatCode::getOptionsBookingTravelType();
        $this->setBookingTravelType($data);
    }

    private function setBookingTravelType($data){
        $this->results->bookingTravelType = $data;
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
        $this->results->city = $data;
    }


}
