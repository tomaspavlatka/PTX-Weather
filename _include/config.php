<?php
if(!defined('GRAND_ACCESS') || GRAND_ACCESS != 1) {
    exit('__Restricted Area__');
}

define('MMV_SHAMROCKOILS_URL','http://shamrockoils.com');
define('MMV_LANG','en_us');
define('MMV_MODE','localhost');

if(MMV_MODE == 'production' || MMV_MODE == 'test') {
    define('MMV_DB_DRIVER','mysql');
    define('MMV_DB_HOST','localhost');
    define('MMV_DB_USER','shamroil_reg3695');
    define('MMV_DB_PASSWORD',';FsaJFCM$AT9{1dSZQ');
    define('MMV_DB_NAME','shamroil_cake159');
    define('MMV_DB_CHARSET','utf8');
} else if(MMV_MODE == 'localhost') {
    define('MMV_DB_DRIVER','mysql');
    define('MMV_DB_HOST','localhost');
    define('MMV_DB_USER','root');
    define('MMV_DB_PASSWORD','');
    define('MMV_DB_NAME','mmv_shamrockoils');
    define('MMV_DB_CHARSET','utf8');
}
 

// Connect database.
dibi::connect(array('driver'=>MMV_DB_DRIVER,'host'=>MMV_DB_HOST,'username'=>MMV_DB_USER, 'password'=>MMV_DB_PASSWORD, 'database'=>MMV_DB_NAME, 'charset'=>MMV_DB_CHARSET));

/**
* Debug.
* 
* fast way how to debug something
* @param mixed $menu
*/
function debug($menu) {
    echo __FILE__.' ('.__LINE__.')';
    echo '<pre>';
        print_r($menu);
    echo '</pre>';
}

/**
* Escape.
* 
* escape string
* @param string $string
*/
function escape($string, $echo = true) {    
    $escaped = trim(htmlspecialchars($string));
    
    if($echo) {
        echo $escaped;
    } else {
        return (string)$escaped;
    }
}