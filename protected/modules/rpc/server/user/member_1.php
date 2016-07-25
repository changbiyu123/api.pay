<?php

class member_1 {

    public function getData1() {
        return CJSON::encode(AdminBooking::model()->findAll());
    }

}
