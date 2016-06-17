<?php

class IExpertMember extends IDoctor {

    public function initModel($model, $attributesMap = null) {
        parent::initModel($model, $attributesMap);
    }

    public function attributesMapping() {
        return parent::attributesMapping();
    }

}
