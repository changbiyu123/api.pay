<?php

class ApiViewLocalData extends EApiViewService {
    public function __construct() {
        parent::__construct();
        $this->results = new stdClass();
    }

    protected function loadData() {
        $this->loadAcademicTitle();
        $this->loadClinicalTitle();
        $this->loadGender();
        $this->loadBookingTravelType();
        $this->loadPayment();
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

    public function loadPayment(){
        $this->setPayment('http://m.mingyizhudao.com/mobile/order/view?refNo=#&os=#&header=0&footer=0&addBackBtn=0');
    }

    private function setPayment($data){
        $this->results->paymentUrl = $data;
    }


}
