<?php

class ApiViewReceivePatientBookingList extends EApiViewService {

    private $doctorId;  // User.id
    private $patientMgr;
    private $patientBookings;  // array
    private $pagesize = 10;
    private $page = 1;

    //初始化类的时候将参数注入
    public function __construct($doctorId, $pagesize = 100, $page = 1) {
        parent::__construct();
        $this->doctorId = $doctorId;
        $this->pagesize = $pagesize;
        $this->page = $page;
        $this->patientMgr = new PatientManager();
        $this->patientBookings = array();
        $this->results = new stdClass();
    }

    protected function loadData() {
        // load PatientBooking by doctorId.
        $this->loadPatientBookings();
    }

    //返回的参数
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

    //调用model层方法
    private function loadPatientBookings() {
        $attributes = null;
        $with = array('pbPatient');
        $options = array('limit' => $this->pagesize, 'offset' => (($this->page - 1) * $this->pagesize), 'order' => 't.date_updated DESC');
        $models = $this->patientMgr->loadPatientBookingListByDoctorId($this->doctorId, $attributes, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setPatientBookings($models);
        }
    }

    public function loadCount() {
        return $this->patientMgr->loadPatientBookingNumberByDoctorId($this->doctorId);
    }

    //查询到的数据过滤
    private function setPatientBookings(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->refNo = $model->getRefNo();
            $data->status = $model->getStatus(false);
            $data->statusCode = $model->getStatus();
            $data->is_deposit_paid = $model->getIsDepositPaid();
            $data->date_start = $model->getDateStart();
            $data->date_end = $model->getDateEnd();
            $data->dateUpdated = $model->getDateUpdated('m月d日');
            $data->travelType = $model->getTravelType();
            $patientInfo = $model->getPatient();
            if (isset($patientInfo)) {
                $data->patientId = $patientInfo->getId();
                $data->name = $patientInfo->getName();
                $data->dataCreate = $patientInfo->getDateCreated();
                $data->diseaseName = $patientInfo->getDiseaseName();
                $data->diseaseDetail = $patientInfo->getDiseaseDetail();
                $data->age = $patientInfo->getAge();
                $data->ageMonth = $patientInfo->getAgeMonth();
            } else {
                $data->patientId = '';
                $data->name = '';
                $data->dataCreate = '';
                $data->diseaseName = '';
                $data->diseaseDetail = '';
                $data->age = '';
                $data->ageMonth = '';
            }
            $this->results->data[] = $data;
//            switch($model->status)
//            {
//                case StatCode::BK_STATUS_NEW:
//                    $this->results->status_new[] = $data;
//                    break;
//                case StatCode::BK_STATUS_DONE:
//                    $this->results->status_done[] = $data;
//                    break;
//            }
        }
    }

}
