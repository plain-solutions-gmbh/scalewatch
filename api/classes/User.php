<?php

require_once(__DIR__ . '/../autoload.php');

class User extends Item {

    public function __construct() {
        parent::__construct($_REQUEST['user'], 0);
    }

}