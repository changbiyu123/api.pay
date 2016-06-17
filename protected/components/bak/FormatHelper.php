<?php

if (phpversion() < 5.3) {

    function date_parse_from_format($format, $date) {
        $dMask = array(
            'H' => 'hour',
            'i' => 'minute',
            's' => 'second',
            'y' => 'year',
            'm' => 'month',
            'd' => 'day'
        );
        $format = preg_split('//', $format, -1, PREG_SPLIT_NO_EMPTY);
        $date = preg_split('//', $date, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($date as $k => $v) {
            if ($dMask[$format[$k]])
                $dt[$dMask[$format[$k]]] .= $v;
        }
        return $dt;
    }

}

function calYearsFromDatetime($datetimeFrom, $datetimeTo='today') {
    return date_diff(date_create($datetimeFrom), date_create($datetimeTo))->y;
}

function textToDisplay($value, $nullTxt='无') {
    if ($value === null || $value == '')
        return $nullTxt;
    else
        return $value;
}

function currencyToDisplay($value, $symbol = null) {
    //TODO: currency symbol shall be loaded from locale setting.
    $ret = number_format(round($value, 2, PHP_ROUND_HALF_UP), 2);
    if (is_null($symbol) === false)
        $ret = $symbol . $ret;
    return $ret;
}

function deliveryChargeToDisplay($value) {
    if ($value == 0)
        return 'FREE!';
    else
        return currencyToDisplay($value);
}

function deliveryChargeColorToDisplay($value) {
    if (deliveryChargeToDisplay($value) == 0)
        return "#41A317"; // return green color
    else
        return "#000000"; // return black color
}

function booleanToDisplay($value) {
    if ($value == true || $value == 1) {
        return 'Yes';
    } else {
        return 'No';
    }
}

function datetimeToDisplay($date, $split=false) {
    if (isset($date) && (strtotime($date) !== false)) {
        $date = new DateTime($date);
    }
    if ($split) {
        return $date->format('Y年m月d日') . '<br />' . $date->format('H:i');
    } else {
        return ($date->format('Y年m月d日 H:i'));
    }
}

/*
  function datetimeToDisplay($date) {
  if ($date instanceof DateTime) {
  return ($date->format('d-M-Y H:i:s'));
  } else if (isset($date) && (strtotime($date) !== false)) {
  $date = new DateTime($date);
  return ($date->format('d-M-Y H:i:s'));
  }
  else
  return ('');
  }
 * 
 */

function dateToDisplay($date) {
    if ($date instanceof DateTime) {
        return($date->format('d-M-Y'));
    } else if (isset($date) && (strtotime($date) !== false)) {
        $date = new DateTime($date);
        return($date->format('d-M-Y'));
    }
    else
        return ('');
}

function timeToDisplay($date, $sec = false) {
    $format = 'H:i';
    if ($sec)
        $format .= ':s';
    if ($date instanceof DateTime) {
        return ($date->format($format));
    } else if (isset($date) && (strtotime($date) !== false)) {
        $date = new DateTime($date);
        return ($date->format($format));
    }
    else
        return ('');
}

function dateToDB($date) {
    if ($date instanceof DateTime) {
        return($date->format('Y-m-d H:i:s'));
    } else if (isset($date) && (strtotime($date) !== false)) {
        $date = new DateTime($date);
        return($date->format('Y-m-d H:i:s'));
    }
    else
        return ('');
}

/**
 * format datetime/string as given format
 * @param datetime/string $datetime
 * @param string $format
 * @return string
 */
function datetimeToStringAsFormat($datetime, $format) {
    if ($datetime instanceof DateTime) {
        return ($datetime->format($format));
    } else if (isset($datetime) && (strtotime($datetime) !== false)) {
        $datetime = new DateTime($datetime);
        return ($datetime->format($format));
    } else {
        return ('');
    }
}

/**
 * Dropdown list attribute constructor
 * @param array $dropDownList
 * @param array $attrs
 * @param id $selected
 * @return multitype:multitype:string boolean
 */
function dropDownListOptionsAdditionAttr($dropDownList, $attrs, $selected) {
    $options = array();
    $keys = array_keys($dropDownList);
    foreach ($keys as $key) {
        $optionsAttrs = array();
        foreach ($attrs as $attr) {
            $optionsAttrs[$attr] = '';
        }
        if ($key == $selected) {
            $optionsAttrs['selected'] = true;
        }
        $options[$key] = $optionsAttrs;
    }
    return $options;
}

/**
 * 
 * @param array(key=>value) $dropDownList
 * @param string $promote
 * @param array(attr...) $attrs
 * @param string $selected
 * @return multitype:multitype:string boolean
 */
function dropDownListOptionsRender($dropDownList, $promote, $attrs, $selected) {

    $optionItems = array();
    if (!is_null($promote)) {
        $optionItems[''] = $promote;
        foreach ($attrs as $attr) {
            $optionsAttrs[$attr] = '';
        }
        $optionItems[''] = $optionsAttrs;
        $dropDownList = array('' => $promote) + $dropDownList;
    }

//     var_dump($dropDownList);
//     var_dump($selected);

    $keys = array_keys($dropDownList);
    foreach ($keys as $key) {
        $optionsAttrs = array();
        foreach ($attrs as $attr) {
            $optionsAttrs[$attr] = '';
        }
        if (NULL != $selected && "" != trim($selected) && $key == trim($selected)) {
            $optionsAttrs['selected'] = true;
        }
        $optionItems[$key] = $optionsAttrs;
    }
//     var_dump($optionItems);
//     exit;
    $optionList = array();
    $optionList['main'] = $dropDownList;
    $optionList['options'] = $optionItems;
    return $optionList;
}