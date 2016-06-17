<?php

class UserController extends MobileController {

    public function actionRegister() {
        $this->render("register");
    }

    public function actionLogin() {
        $this->render('login');
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect($this->getReturnUrl());
    }

}
