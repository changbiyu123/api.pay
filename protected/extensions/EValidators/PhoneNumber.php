<?php

class PhoneNumber extends CValidator {
    const MOBILE_CHINA=1;

    public $type;

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $model the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($model, $attribute) {
        if ($model->getError($attribute) === null) {    //already has error, so skip validation.
            if (is_null($this->type)) {
                $this->type = self::MOBILE_CHINA;
            }
            // check the type parameter used in the validation rule of our model
            if ($this->type === self::MOBILE_CHINA) {
                $errorMsg = '请输入正确的中国' . $model->getAttributeLabel($attribute);
                if (preg_match('/^\d{11}$/', $model->{$attribute}) === 0) {// check if mobile is 11 digits.
                    $model->addError($attribute, $errorMsg);
                }
            } else {
                $model->addError($model, $attribute, 'Unknown Phone type.');
            }
        }
    }

}
