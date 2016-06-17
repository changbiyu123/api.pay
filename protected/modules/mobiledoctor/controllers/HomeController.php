<?php

class HomeController extends MobiledoctorController {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionIndex() {
        $this->redirect(Yii::app()->user->loginUrl);
        /*
          $apiService = new ApiViewAppNav1V2();
          $data = $apiService->loadApiViewData();

          $this->render('index', array(
          'data' => $data
          ));
         */
    }

    public function actionSetBrowser($browser) {
        if ($browser == 'pc') {
            $this->setBrowserInSession($browser);
            $this->redirect(Yii::app()->params['baseUrl'] . '/site/index?browser=pc');
        } else {
            $this->setBrowserInSession($browser);
            $this->redirect($this->getHomeUrl());
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        //$this->redirect(array('index'));
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax'])) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
