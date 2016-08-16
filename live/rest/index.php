<?php

require_once "my_api.php";

ob_start('ob_gzhandler');

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $API = new MyAPI($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    $result = $API->processAPI();
    echo $result;
} 
catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

?>