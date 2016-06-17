<?php

function isNullOrEmpty($value) {
    if ($value === null || empty($value))
        return true;
    else
        return false;
}

function roundToNearestN($num, $n) {
    return (round($num / $n) * $n);
}

function getDecimal($num, $digits=2) {
    $t = explode('.', $num);
    if (isset($t[1]))
        return str_pad($t[1], $digits, '0', STR_PAD_RIGHT);
    else
        return str_pad('', $digits, '0', STR_PAD_RIGHT);
}

function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}