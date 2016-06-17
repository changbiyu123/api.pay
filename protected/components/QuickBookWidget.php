<?php

class QuickBookWidget extends CWidget {

    public $type;   // booking_type
    public $fid;    // faculty_id
    public $did;    // doctor_id
    public $tid;    // expteam_id       
    public $hid;    // hospital_id
    public $dept;   // hospital_dept    

    public function run() {
        $this->render('quickbook', array(
            'type' => $this->type,
            'fid' => $this->fid,
            'did' => $this->did,
            'tid' => $this->tid,
            'hid' => $this->hid,
            'dept' => $this->dept
        ));
    }

}
