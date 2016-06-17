<?php

class ILocationState extends EViewModel {

    public function initModel($model, $attributes = null) {
        parent::initModel($model, $attributes);
    }

    public function attributesMapping() {
        return array(
            'id' => 'id',
            'name' => 'name',
        );
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

}
