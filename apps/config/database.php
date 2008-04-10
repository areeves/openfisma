<?php

    Zend_Registry::set('datasource', new Zend_Config(
        array(
        'default' => array(
            'adapter' => 'mysqli',
            'params' => array(
                'host' => 'localhost',
                'port' => '',
                'username' => 'sws_live',
                'password' => '123456',
                'dbname' => 'fisma2',
                'profiler' => true
            )
        ))
    ));

?>
