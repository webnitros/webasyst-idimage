<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
$path = dirname(__FILE__).'/wa-config/SystemConfig.class.php';

if (file_exists($path)) {
    require_once($path);
    waSystem::getInstance(null, new SystemConfig())->dispatch();
} else {
    $path = dirname(__FILE__).'/wa-installer/install.php';
    if (file_exists($path)) {
        require_once($path);
    } else {
        //404
    }
}
