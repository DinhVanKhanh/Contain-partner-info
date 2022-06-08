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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>ソリマチ株式会社 - ソリマチ認定セミナー</title>

    <style type="text/css">
        a {
            color: #2ba3a3;
            outline: none;
            text-decoration: underline;
        }

        .fs10 { font-size: 10pt; }
        .fs11 { font-size: 11pt; }
        .fs12 { font-size: 12pt; }

        TABLE.smnlist { border-collapse:collapse; border:1px #888888 solid; }
        TH.smnmida1 { background-color:#D0E0FF; border:1px #888888 solid; padding:8px 0 4px 0; white-space:nowrap; color:#405060; font:bold 92%/150% Meiryo,メイリオ,'ＭＳ Ｐゴシック',sans-serif; }
        TH.smnlist1 { background-color:#F4F8FF; border:1px #888888 solid; padding:5px; white-space:nowrap; color:#405060; font:bold 80%/130% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist1 { border:1px #888888 solid; padding:5px 10px; font:normal 92%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist1nw { border:1px #888888 solid; padding:5px 10px; white-space:nowrap; font:normal 92%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist1map { text-align:center; border:1px #888888 solid; padding:8px 5px; font:normal 80%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TH.smnmida2 { background-color:#FFE8D0; border:1px #888888 solid; padding:8px 0 4px 0; white-space:nowrap; color:#504030; font:bold 92%/150% Meiryo,メイリオ,'ＭＳ Ｐゴシック',sans-serif; }
        TH.smnlist2 { background-color:#FFF8E8; border:1px #888888 solid; padding:5px; white-space:nowrap; color:#504030; font:bold 80%/130% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist2 { border:1px #888888 solid; padding:5px 10px; font:normal 92%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist2nw { border:1px #888888 solid; padding:5px 10px; white-space:nowrap; font:normal 92%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smnlist2hissu { background:url(../assets/images/hissu_12px.gif) 10px center no-repeat; border:1px #888888 solid; padding:5px 10px 5px 60px; white-space:nowrap; font:normal 92%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        .moyorieki { background:url(../assets/images/icon_eki.gif) 3px 2px no-repeat; padding-left:53px; font:normal 80%/150% 'ＭＳ Ｐゴシック',sans-serif; }
        TD.smncaution { border:1px #888888 solid; padding:5px 10px; font:normal 80%/130% 'ＭＳ Ｐゴシック',sans-serif; }

        .error_blue {
            font-size:90%;
            font-weight:bold;
            color:#0000FF;
        }

        .tab {
            padding:5px 10px 5px 60px!important;
        }
    </style>

    <script type="text/javascript" src="../assets/js/bs_seminar_rg.js"></script>
    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/global.js"></script>
    <script type="text/javascript" src="../assets/js/proccess/P_regular.js"></script>
</head>

<body>
    <section id="wrapper" class="client">
