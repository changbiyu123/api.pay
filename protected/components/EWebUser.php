<?php

class EWebUser extends CWebUser {

    /**
     *
     * @param UserIdentity $identity
     * @param integer $duration 
     */
    public function login($identity, $duration = 0) {
        parent::login($identity, $duration);

        $this->setState('role', $identity->getRole());
    }

    public function getRole() {
        return $this->getState('role');
    }

    public function isPatient() {
        $role = $this->getRole();
        return $role == User::ROLE_PATIENT;
    }

    public function isDoctor() {
        $role = $this->getRole();
        return $role == User::ROLE_DOCTOR;
    }

}
