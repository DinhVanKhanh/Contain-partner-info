<?php
    require_once __DIR__ . '/../libs/webserver_flg.class.php';
    require_once __DIR__ . '/../config/database.class.php';
    date_default_timezone_set( "Asia/Tokyo" );
    require_once __DIR__ . '/controller/C_' . strtolower( $_POST['controller'] ) . '.class.php';
?>