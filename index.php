<?php
// Access.
define('GRAND_ACCESS',1);
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__)));    

// Required.
require_once './_include/functions.php';  
require_once './classes/weather_location.php';
require_once './classes/xml_parser.php';
require_once './classes/exception.php';

try {
    $params = array(
        'wunderground' => array( 
            'api_key' => '3a2124116191bb5d',
            'refresh' => 10,
            'forecast' => true));
    $weatheObj = new PTX_Weather_Location('AU/Sydney',$params);
} catch(PTX_Exception $e) {
    echo $e->getMessage();
}
