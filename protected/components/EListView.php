<?php

Yii::import('zii.widgets.CListView');

class EListView extends CListView {

    public function init() {
        parent::init();

        $this->pager = array(
            'htmlOptions'=>array('class'=>'pagination'),
            'class' => 'CLinkPager',
            'header' =>'',
            'footer'=>'',
            'firstPageLabel' => '&lt;&lt;',
            'prevPageLabel' => '&lt;',
            'nextPageLabel'=>'&gt;',
            'lastPageLabel' => '&gt;&gt;',
        );
    }

}