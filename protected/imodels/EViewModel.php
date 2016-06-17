<?php

abstract class EViewModel {

    public function initModel($model, $attributes = null) {
        $attributesMap = $this->getAttributesMapping($attributes);

        foreach ($attributesMap as $imodelAttr => $modelAttr) {
            $this->{$imodelAttr} = $model->{$modelAttr};
        }
    }

    /**
     * gets the selected attributes mapping, defined in $attributes.
     * @param array $attributes
     * @return array attributesMapping.
     */
    public function getAttributesMapping($attributes = null) {
        if (is_null($attributes)) {
            return $this->attributesMapping();
        } else {
            $output = array();
            $mapList = $this->attributesMapping();
            foreach ($attributes as $attr) {
                if (isset($mapList[$attr])) {
                    $output[$attr] = $mapList[$attr];
                }
            }
            return $output;
        }
    }

    public function attributesMapping() {
        return array();
    }

    /*
     * This is for reference only.
      public function addRelatedModel($model, $with) {
      if (arrayNotEmpty($with)) {
      foreach ($with as $key => $relatedAttr) {
      if (is_array($relatedAttr)) {
      //$relatedAttr can be an array stating further model relations:
      //array('relatedAttr'=>array('with'=>'some relations'))
      $relatedAttr = $key;
      }
      $relatedModel = $model->{$relatedAttr};
      if (is_null($relatedModel)) {
      continue;
      }
      switch ($relatedAttr) {
      case "1":
      break;
      case "2":
      break;
      case "3":
      break;
      default:
      break;
      }
      }
      }
      }
     * 
     */

    protected function getTextAttribute($value, $ntext = true) {
        if ($ntext) {
            return Yii::app()->format->formatNtext($value);
        } else {
            return $value;
        }
    }

}
