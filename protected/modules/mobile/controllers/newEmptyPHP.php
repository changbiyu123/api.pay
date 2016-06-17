<?php

$status = 'ok';
$resultCount = 1;
$balance = '';
$score = '';
$errorMsg = '';
$errorCode = '';
$results = array();
for ($i = 0; $i < 3; $i++) {
    $data = new stdClass();
    $data->id = $i;
    $data->name = 'name_' . $i;
    $data->time = time();
    $data->files = array();
    for ($j = 0; $j < 3; $j++) {
        $file = new stdClass();
        $file->id = $j;
        $file->dataId = $data->id;
        $file->name = 'file_name_' . $j;
        $data->files[] = $file;
    }
    $results[] = $data;
}

$booking = new stdClass();
$booking->id = 123;
$booking->refNo = time();
$booking->hospital = 'Hospital Name';
$booking->doctor_name = 'Doctor ABC';

$output = array(
    'status' => $status,
    'errorMsg' => $errorMsg,
    'errorCode' => $errorCode,
    'resultCount' => $resultCount,
    'balance' => $balance,
    'results' => $results,
    'booking' => $booking
);


var_dump($results);