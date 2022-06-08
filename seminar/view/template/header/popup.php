<?php
	global $scripts, $linkCss, $title;

    require_once __DIR__ . '/../../../libs/webserver_flg.class.php';
	require_once __DIR__ . "/../../../config/database.class.php";
	require_once __DIR__ . "/../../../libs/redirect.class.php";
	date_default_timezone_set( "Asia/Tokyo" );
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $title ?></title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/styles_popup.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <?= $linkCss ?>

    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <script type="text/javascript" src="assets/js/init.js"></script>
    <script type="text/javascript" src="assets/js/scripts.js"></script>
    <?= $scripts ?>
</head>

<body>
    <section class="popup">
