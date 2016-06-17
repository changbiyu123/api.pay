<?php

class ApiViewDoctorPatientInfo extends EApiViewService {

    private $id;
    private $creatorId;  // User.id
    private $patientMgr;
    private $patientInfo;  // array
    private $patientBooking;

    //初始化类的时候将参数注入

    public function __construct($id, $creatorId) {
        parent::__construct();
        $this->creatorId = $creatorId;
        $this->id = $id;
        $this->patientMgr = new PatientManager();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadPatienInfo();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => array('patientInfo' => $this->patientInfo, 'patientBooking' => $this->patientBooking),
            );
        }
    }

    //调用model层方法
    private function loadPatienInfo() {
        $attributes = null;
        $with = array('patientBookings');
        $options = null;
        $model = $this->patientMgr->loadPatientInfoByIdAndCreateorId($this->id, $this->creatorId, $attributes, $with, $options);
        if (isset($model)) {
            $this->setPatientInfo($model);
            $booking = $model->getBookings();
            if (arrayNotEmpty($booking)) {
                $this->setPatientBooking($booking[0]);
            } else {
                $this->patientBooking = null;
            }
        }
    }

    //查询到的数据过滤
    private function setPatientInfo(PatientInfo $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $data->age = $model->getAge();
        $data->ageMonth = $model->getAgeMonth();
        $data->cityName = $model->getCityName();
        $data->gender = $model->getGender();
        $data->mobile = $model->getMobile();
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $this->patientInfo = $data;
    }

    private function setPatientBooking(PatientBooking $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->creatorId = $model->getCreatorId();
        $data->status = $model->getStatus();
        $data->stateName = $model->getStateName();
        $data->birthYear = $model->getBirthYear();
        $data->birthMonth = $model->getBirthMonth();

        $data->statusCode = $model->getStatus(false);
        $data->travelType = $model->getTravelType();
        $data->dateStart = $model->getDateStart();
        $data->dateEnd = $model->getDateEnd();
        $data->detail = $model->getDetail(false);
        $data->apptDate = $model->getApptDate();
        $data->dateConfirm = $model->getDateConfirm();
        $data->remark = $model->getRemark(false);
        $data->dateCreated = $model->getDateCreated();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $data->dateNow = date('Y-m-d H:i', time());
        $this->patientBooking = $data;
    }

}
