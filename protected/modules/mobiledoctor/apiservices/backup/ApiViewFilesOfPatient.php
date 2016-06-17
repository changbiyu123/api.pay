<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiFilesOfPatient
 *
 * @author Administrator
 */
class ApiViewFilesOfPatient extends EApiViewService {

    private $creatorId;  // User.id
    private $patientId;       //患者id
    private $patientMgr;
    private $files;  // array

    //初始化类的时候将参数注入

    public function __construct($patientId, $creatorId) {
        parent::__construct();
        $this->patientId = $patientId;
        $this->creatorId = $creatorId;
        $this->patientMgr = new PatientManager();
        //若查询出来的为数组 则需初始化
        $this->files = array();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadPatients();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->files,
            );
        }
    }

    //调用model层方法
    private function loadPatients() {
        $attributes = null;
        $with = null;
        $options = null;
        $type = StatCode::MR_REPORTTYPE_MR;
        $models = $this->patientMgr->loadFilesOfPatientByPatientIdAndCreaterIdAndType($this->patientId, $this->creatorId, $type, $attributes, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setFiles($models);
        }
    }

    //查询到的数据过滤
    private function setFiles(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->absFileUrl = $model->getAbsFileUrl();
            $data->absThumbnailUrl = $model->getAbsThumbnailUrl();
            $this->files[] = $data;
        }
    }

}
