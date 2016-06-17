<?php

class IExpertLeader extends IDoctor {

    public function initModel($model, $attributesMap = null) {
        parent::initModel($model, $attributesMap);
        if (isset($this->honour) === false) {
            $this->honour = array();
        }
    }

    public function attributesMapping() {
        return array_merge(parent::attributesMapping(), array(
            'honour' => 'honour'
        ));
    }

}
