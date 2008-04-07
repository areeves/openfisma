<?php
    require_once(APPS . DS .'Exception.php');

    function uses() {
        $args = func_get_args();
        foreach ($args as $file) {
            require_once(LIBS . DS . strtolower($file) . '.php');
        }
    }

    function import() {
        $args = func_get_args();
        $target_path = null;
        foreach ($args as $dir) {
            if( is_dir($dir) ) {
                $target_path .= $dir . PATH_SEPARATOR ;
            }else{
                throw new Sws_Exception();
            }
        }
        if(! empty($target_path) ){
            $include_path = ini_get('include_path');
            ini_set('include_path',  $target_path . $include_path);
        }
    }

?>
