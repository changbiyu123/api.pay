<?php

/**
 * Validator class for personal identification numbers (nric, passport etc).
 */
class IdentificationNumber extends CValidator {
    const NRIC_CHINA=1;
    const PASSPORT_CHINA=2;

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
                $this->type = self::NRIC_CHINA;
            }
            // check the type parameter used in the validation rule of our model
            if ($this->type === self::NRIC_CHINA) {
                $errorMsg = '请输入正确的中国' . $model->getAttributeLabel($attribute);
                if (preg_match('/^\d{17}\w{1}$/', $model->{$attribute}) === 0) {// check if nric is 18 digits.
                    $model->addError($attribute, $errorMsg);
                } else {
                    $dobStr = substr($model->{$attribute}, 6, 8); // get dob portion from nric.
                    $year = substr($dobStr, 0, 4);  // get year from $dobStr.
                    $month = substr($dobStr, 4, 2); // get month from $dobStr.
                    $day = substr($dobStr, 6, 2); // get day from $dobStr.
                    $dateStr = $year . '-' . $month . '-' . $day;
                    $date = date_parse_from_format('Y-m-d', $dateStr);
                    if ($year < 1900) { //born before 1900, impossible.
                        $model->addError($attribute, $errorMsg);
                    } elseif ($date['warning_count'] > 0 || $date['error_count'] > 0) {
                        $model->addError($attribute, $errorMsg);
                    }
                }
            } else {
                $model->addError($model, $attribute, 'Unknown Identification Document type.');
            }
        }
    }

    /**
     * Returns the JavaScript needed for performing client-side validation.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     * @return string the client-side validation script.
     * @see CActiveForm::enableClientValidation
     */
    /*
      public function clientValidateAttribute($object, $attribute) {

      // check the strength parameter used in the validation rule of our model

      if ($this->strength == 'weak')
      $pattern = $this->weak_pattern;
      elseif ($this->strength == 'strong')
      $pattern = $this->strong_pattern;

      $condition = "!value.match({$pattern})";

      return "if(" . $condition . ") {messages.push(" . CJSON::encode('your password is too weak, you fool!') . ");}";
      }
     * 
     */
}
