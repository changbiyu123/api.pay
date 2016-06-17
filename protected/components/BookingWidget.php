<?php

class BookingWidget extends CWidget {

    public $type;   // booking_type    
    public $did;    // doctor_id
    public $tid;    // expteam_id
    public $form;

    public function run() {
        $userId = Yii::app()->user->id;
        if (isset($this->tid)) {
            // 预约专家团队
            $form = new BookExpertTeamForm();
            $form->initModel();
            $form->setExpertTeamId($this->tid);
            $form->setExpertTeamData();
        //    $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            //@TEST:
            //     $data = $this->testDataDoctorBook();
            //   $form->setAttributes($data, true);    
            $this->form = $form;
            $this->render('bookExpertteam', array('model' => $this->form));
        } elseif (isset($this->did)) {
            // 预约医生
            $form = new BookDoctorForm();
            $form->initModel();
            $form->setDoctorId($this->did);
            $form->setDoctorData();
        //    $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            //@TEST:
            //   $data = $this->testDataDoctorBook();
            //    $form->setAttributes($data, true);
            $this->form = $form;
            $this->render('bookDoctor', array('model' => $this->form));
        } else {
            // 快速预约
            $form = new BookQuickForm();
            $form->initModel();
        //    $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            //@TEST:
            //    $data = $this->testDataQuickBook();
            //    $form->setAttributes($data, true);
            $this->form = $form;
            $this->render('bookQuick', array('model' => $this->form));
        }
    }

}
