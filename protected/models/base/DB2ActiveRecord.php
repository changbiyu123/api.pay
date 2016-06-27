<?php

class DB2ActiveRecord extends EActiveRecord {

    public function getDbConnection() {
        return Yii::app()->db;
    }

}