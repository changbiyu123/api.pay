<?php

class FacultyController extends MobileController {

    public function actionView($id) {
        $this->render('view', array('facultyId' => $id));
        /*
          $facultyMgr = new FacultyManager();
          $ifaculty = $facultyMgr->loadIFaculty2($id);

          $this->render('view', array(
          'model' => $ifaculty
          ));
         * 
         */
    }

}
