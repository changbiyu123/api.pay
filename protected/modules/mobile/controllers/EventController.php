<?php

class EventController extends MobileController {

    public $urlUpload;
    public $page_list;
    public $current_page;
    private $defaultPage = 'dandao';

    public function actionView($name) {
        //$this->content_container = "container-fluid";
        $list = $this->getPageList();

        if (isset($list[$name])) {
            $view = $name;
            $folder = $name;
            $this->current_page = $name;
        } else {
            $this->redirect(array('view', 'name' => $this->defaultPage));
        }

        $this->render('pages/' . $view, array(
            'urlUpload' => $this->urlUpload . $folder . '/'
        ));
    }

    public function actionAjaxDandao() {
        if (isset($_POST['EventDandao'])) {

            $output = array();
            $model = new EventDandao();
            $model->attributes = $_POST['EventDandao'];
            $model->user_ip = $this->getUserIp();
            $model->user_agent = Yii::app()->request->getUserAgent();

            $this->performAjaxValidation($model);

            $success = $model->save();
            if ($this->isAjaxRequest()) {
                if ($success) {
                    // success.
                    echo CJSON::encode(array(
                        'status' => 'true'
                    ));
                    Yii::app()->end();
                } else {
                    // error message.
                    $error = CActiveForm::validate($model);
                    if ($error != '[]') {
                        echo $error;
                    }
                    Yii::app()->end();
                }
            } else {
                if ($success) {
                    $this->setFlashMessage('event.dandao.success', '恭喜！您已成功报名！');
                }
            }
        } else {
            $this->throwPageNotFoundException();
        }
    }

    /**
     * @param integer $_POST['Event[event_id]'] 1 => EventYangying
     * @param string $_POST['Event[author]']
     * @param string $_POST['Event[comment]']
     */
    public function actionAjaxAddComment() {
        //$eventId=2;

        if (isset($_POST['Event'])) {
            $values = $_POST['Event'];
            if (isset($values['event_id']) === false || $values['event_id'] != 1) {
                $this->throwPageNotFoundException();
            }
            $output = array();
            $model = new EventYangying();
            $model->attributes = $_POST['Event'];
            $model->visible = 1;

            $this->performAjaxValidation($model);

            $success = $model->save();
            if ($this->isAjaxRequest()) {
                if ($success) {
                    //reload model from db.
                    $model = EventYangying::model()->getById($model->getId());
                    // success.
                    $output['status'] = 'true';
                    $output['author'] = $model->getAuthor();
                    $output['comment'] = $model->getComment();
                    $output['date'] = $model->getDateCreated();
                    echo CJSON::encode($output);
                    
                    Yii::app()->end();
                } else {
                    // error message.
                    $error = CActiveForm::validate($model);
                    if ($error != '[]') {
                        echo $error;
                    }
                    Yii::app()->end();
                }
            } else {
                if ($success) {
                    $this->setFlashMessage('event.success', '留言成功！');
                }
            }
        } else {
            $this->throwPageNotFoundException();
        }
    }

    /**
     *
     * @param integer $id
     * @param integer $limit 
     * @param integer $offset     
     */
    public function actionAjaxLoadComment($id=null, $limit=0, $offset=0) {
        $output = array();
        //check event id == 1.
        if ($id != 1) {
            $output['status'] = 'false';
            $output['error'] = 'Unknown event';
            echo CJSON::encode($output);
            Yii::app()->end();
        }
        if ($limit < 1) {
            $limit = 10;
        }
        if ($offset < 1) {
            $offset = 0;
        }

        $totalComments = EventYangying::model()->count();
        $output['total'] = $totalComments;
        $output['limit'] = $limit;
        $output['offset'] = $offset;

        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->order = 't.date_created DESC';
        $criteria->limit = $limit;
        $criteria->offset = $offset;

        $models = EventYangying::model()->findAll($criteria);
        $output['count'] = count($models);
        $output['remain'] = $output['total'] - $output['offset'] - $output['count'];
        $output['data'] = array();

        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $data = array();
                $data['author'] = $model->getAuthor();
                $data['comment'] = $model->getComment();
                $data['date'] = $model->getDateCreated();
                //$output['data'][$model->id] = $data;
                $output['data'][] = $data;
            }
        }

        echo CJSON::encode($output);
        //   echo json_encode($output);
        Yii::app()->end();
    }

    public function getPageList() {
        if ($this->page_list === null) {
            $this->page_list = array('dandao' => '上海胆道疾病会诊中心', 'liubaochi' => '刘保池专家', 'yangying' => '杨颖案例');
        }
        return $this->page_list;
    }

    public function getCurrentPageLabel() {
        $list = $this->getPageList();
        if (isset($list[$this->current_page])) {
            return $list[$this->current_page];
        }
    }

    /**
     * Performs the AJAX validation.
     * @param User $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'event-dandao-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
