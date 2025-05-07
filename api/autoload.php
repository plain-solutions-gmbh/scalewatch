<?php

require 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    $class_name = ucfirst($class_name);
    include __DIR__ . "/classes/$class_name.php";
});