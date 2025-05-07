<?php

//Autoload Classes
require_once(__DIR__ . '/autoload.php');

//This is the root config:
Config::init([
    'locations' => [
        'root' => __DIR__ . '/../data',
        'icons' => '/_config/icons',
        'plant' => '/_config/plant',
        'routines' => '_routines'
    ],
    'date_format'   => 'ymd'
]);

//Disable Warnings and Notices
//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

//Set Headers
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


date_default_timezone_set('Europe/Zurich');


//Make inputs available as variables
extract($_REQUEST);

try {

    $action ??= 'get';
    $params ??= [];
    $class = new $modul();

    $response = [
        'success' => true,
        'data' => $class->$action(...$params)
    ];

} catch (\Throwable $th) {

    $response = [
        'success'   => false,
        'data'      => [],
        'error'     => [
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'line'  => $th->getLine()
        ]
    ];    

};

echo json_encode($response);
