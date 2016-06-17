<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewDoctorInfo
 *
 * @author Administrator
 */
class ApiViewDoctorInfo extends EApiViewService {

    private $userId;    // User.id.
    private $userMgr;   // UserManager.
    private $doctorInfo;

    //初始化类的时候将参数注入
    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->userMgr = new UserManager();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadDoctorInfoById();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->doctorInfo,
            );
        }
    }

    private function loadDoctorInfoById() {
        if (is_null($this->doctorInfo)) {
            $attributes = null;
            $with = null;
            $model = $this->userMgr->loadUserDoctorProflieByUserId($this->userId, $attributes, $with);
            if (isset($model)) {
                $this->setDoctorInfo($model);
            }
        }
    }

    private function setDoctorInfo(UserDoctorProfile $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        if ($model->isVerified()) {
            $data->isVerified = '已认证';
        } else {
            $data->isVerified = '未认证';
        }
        $data->stateName = $model->getStateName();    //省会
        $data->cityName = $model->getCityName();
        $data->hospitalName = $model->getHospitalName();
        $data->hpDeptName = $model->getHpDeptName();    //科室
        $data->cTitle = $model->getClinicalTitle();
        $data->aTitle = $model->getAcademictitle();
        $data->clinical_title = $model->getClinicalTitle();
        $data->academic_title = $model->getAcademictitle();
        $data->preferred_patient = $model->getPreferredPatient();
        $this->doctorInfo = $data;
    }

}
