<?php

/**
 * app 患者详情
 * Class ApiViewPatientInfo
 */
class ApiViewPatientInfo extends EApiViewService {

    private $patientId;
    private $creatorId;  // User.id
    private $patientBookingId;
    private $patientMgr;
    private $orderMgr;
    private $patientInfo;  // array
    private $patientBooking;

    //初始化类的时候将参数注入

    public function __construct($patientId, $creatorId) {
        parent::__construct();
        $this->creatorId = $creatorId;
        $this->patientId = $patientId;
        $this->patientMgr = new PatientManager();
        $this->orderMgr = new OrderManager();
        $this->results = new stdClass();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadPatienInfo();
        $this->loadSalesOrder();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
//            array('patientInfo' => $this->patientInfo, 'patientBooking' => $this->patientBooking, 'salesOrder'=>$this->salesOrder),
            );
        }
    }

    //调用model层方法
    private function loadPatienInfo() {
        $attributes = null;
        $with = array('patientBookings');
        $options = null;
        $model = $this->patientMgr->loadPatientInfoByIdAndCreateorId($this->patientId, $this->creatorId, $attributes, $with, $options);
        if (isset($model)) {
            $this->setPatientInfo($model);
            $booking = $model->getBookings();
            if (arrayNotEmpty($booking)) {
                $this->setPatientBooking($booking[0]);
            } else {
                $this->patientBooking = new stdClass();
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
        $data->birthYear = $model->getBirthYear();
        $data->birthMonth = $model->getBirthMonth();
        $data->stateName = $model->getStateName();
        $data->cityName = $model->getCityName();
        $data->gender = $model->getGender();
        $data->mobile = $model->getMobile();
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail();
        $data->dateUpdated = $model->getDateUpdated('Y年m月d日 h:i');
        $this->results->patientInfo = $data;
    }

    private function setPatientBooking(PatientBooking $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->creatorId = $model->getCreatorId();
        $data->status = $model->getStatus(false);
        $data->statusCode = $model->getStatus();
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
        $this->patientBookingId = $data->id;
        $this->results->patientBooking = $data;
    }

    //查询预约支付情况
    private function loadSalesOrder() {
        $bkType = StatCode::TRANS_TYPE_PB;
        if($this->patientBookingId){
            $models = $this->orderMgr->loadSalesOrderByBkIdAndBkType($this->patientBookingId, $bkType);
            if (arrayNotEmpty($models)) {
                $this->setSalesOrder($models);
            }
        }

    }

    private function setSalesOrder(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->refNo = $model->ref_no;
            $data->subject = $model->getSubject();
            $data->description = $model->getDescription();
            $data->finalAmount = $model->getFinalAmount();
            $data->isPaid = $model->getIsPaid(false);
            $this->results->salesOrder[] = $data;
        }
    }
}
