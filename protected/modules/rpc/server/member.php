<?php

class member {

    public function getName() {

        $arr = array("one", "two");
        return $arr;
    }

    public function getData() {
        $member_1 = new member_1();
        $tmp = $member_1->getData1();
        return CJSON::encode($tmp);
    }

}
