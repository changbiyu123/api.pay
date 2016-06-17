<?php

function emptyArray($arr) {
    return (is_array($arr) === false || count($arr) === 0);
}

/**
 *  checks if is array, remove all empty elements.
 * if is not array, return the param.
 * @param type $arr
 * @return type array
 */
function arrayFilterEmptyValues($arr) {
    if (is_array($arr))
        return array_filter($arr);
    else
        return $arr;
}

function arrayExtractDistinctValue($models, $field) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field}) && isset($ret[$model->{$field}]) === false) {
                $ret[$model->{$field}] = $model->{$field};
            }
        }
    }
    return $ret;
}

function arrayExtractValue($models, $field) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field})) {
                $ret[] = $model->{$field};
            }
        }
    }
    return $ret;
}

function arrayExtractKeyValue($models, $field_key, $field_value) {
    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$field_key}) && isset($model->{$field_value})) {
                $ret[$model->{$field_key}] = $model->{$field_value};
            }
        }
    }

    return $ret;
}

function arrayToKeyValuePairArray($models, $key, $value) {

    $ret = array();
    if (isset($models) && is_array($models)) {
        foreach ($models as $model) {
            if (isset($model->{$key}) && isset($model->{$value})) {
                $ret[$model->{$key}] = $model->{$value};
            }
        }
    }
    return $ret;
}

function arrayToKeyValuePairNumericArray($start, $end, $format=null) {
    $ret = array();
    if (isset($start) && is_numeric($start) && isset($end) && is_numeric($end)) {
        for ($idx = $start; $idx <= $end; $idx++) {
            $ret[$idx] = sprintf($format, $idx);
        }
    }
    return $ret;
}

/*
 * @param $arr array key value pair
 * @param $valueToLower bool if true then converts value to lower case.
 * @return an array containing lower case key and lower/normal case value.
 */

function arrayKeyToLower(array $arr, $valueToLower = false) {
    $ret = array();
    if ($valueToLower) {
        foreach ($arr as $key => $value) {
            $ret[strtolower($key)] = strtolower($value);
        }
    } else {
        foreach ($arr as $key => $value) {
            $ret [strtolower($key)] = $value;
        }
    }
    return $ret;
}

function arrayToCsv(array $arr, $delimiter = ', ') {
    $ret = implode($delimiter, $arr);
    return $ret;
}