<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewDoctorFiles
 *
 * @author Administrator
 */
class ApiViewDoctorFiles extends EApiViewService {

    private $id;    // User.id.
    private $userMgr;   // UserManager.
    private $files;  // array

    //初始化类的时候将参数注入

    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
        $this->userMgr = new UserManager();
        //若查询出来的为数组 则需初始化
        $this->files = array();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadDoctorFiles();
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
    private function loadDoctorFiles() {
        $attributes = null;
        $with = null;
        $models = $this->userMgr->loadUserDoctorFilesByUserId($this->id, $attributes, $with);
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
