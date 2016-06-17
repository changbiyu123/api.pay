<?php

class IDoctorCert extends IFile {

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);
    }

    /**
     * corresponds to IFile
     * @return array.
     */
    public function attributesMapping() {
        return array_merge(parent::attributesMapping(), array(
            'doctorId' => 'doctor_id',
        ));
    }

}
