<?php

/**
* PTX Debug.
*   
* alias for my debug function
* @param mixed $data - data which should be debugged.
*/
function ptx_debug($data) {
    echo '<pre>';
        print_r($data);
    echo '</pre>';
}
