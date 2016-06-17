<?php
class LanguageBox extends CWidget
{
    public function run()
    {
        $currentLang = Yii::app()->language;
        $this->render('languageBox', array('currentLang' => $currentLang));
    }
}