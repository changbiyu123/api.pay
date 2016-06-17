<?php
/**
 * 我的患者列表
 * Class ApiViewPatientList
 */
class ApiViewPatientListV2 extends EApiViewService {

    private $creatorId;  // User.id
    private $patientMgr;
    private $hasBookingList;  // array
    private $noBookingList;  //array
    private $pagesize = 10;
    private $page = 1;

    //初始化类的时候将参数注入
    public function __construct($creatorId, $pagesize = 10, $page = 1) {
        parent::__construct();
        $this->creatorId = $creatorId;
        $this->pagesize = $pagesize;
        $this->page = $page;
        $this->patientMgr = new PatientManager();
        $this->hasBookingList = array();
        $this->notBookingList = array();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadPatientList();
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
    private function loadPatientList() {
        $attributes = null;
        $with = array('patientBookings');
        $options = array('limit' => $this->pagesize, 'offset' => (($this->page - 1) * $this->pagesize), 'order' => 't.date_updated DESC');
        $models = $this->patientMgr->loadPatientInfoListByCreateorId($this->creatorId, $attributes, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setPatientList($models);
        }
//        else {
//            $this->hasBookingList = null;
//            $this->noBookingList = null;
//        }
    }

    //查询总数
    public function loadCount() {
        return $this->patientMgr->loadPatientCount($this->creatorId);
    }

    //查询到的数据过滤
    private function setPatientList(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->age = $model->getAge();
            $data->ageMonth = $model->getAgeMonth();
            $data->cityName = $model->getCityName();
            $data->gender = $model->getGender();
            $data->mobile = $model->getMobile();
            $data->diseaseName = $model->getDiseaseName();
            $data->dateUpdated = $model->getDateUpdated('m月d日');
            $booking = $model->getBookings();
            if (arrayNotEmpty($booking)) {
                $bookData = $this->setPatientBooking($booking[0]);
                $this->results->hasBookingList[] = array('patientInfo' => $data, 'patientBooking' => $bookData);
            } else {
                $this->results->noBookingList[] = array('patientInfo' => $data);
            }
        }
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
        return $data;
    }

}
