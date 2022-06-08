<?php
	require_once __DIR__ . "/../../../libs/webserver_flg.class.php";
	global $SORIMACHI_HOME;
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <title>店頭デモ</title>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />

    <link href="assets/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/ui.datepicker.css" rel="stylesheet" type="text/css" />

    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <script type="text/javascript" src="assets/js/scripts.js"></script>
    <script type="text/javascript" src="assets/js/ui.datepicker.js"></script>
    <script type="text/javascript" src="assets/js/fixfooter.js"></script>
    <script type="text/javascript" src="assets/js/init.js"></script>
    <script type="text/javascript" src="assets/js/proccess/P_demo.js"></script>
    <script type="text/javascript" src="assets/js/ready/R_demo.js"></script>
    <script type="text/javascript" src="assets/js/jquery-ui-1.11.3.min.js"></script>
    <script type="text/javascript">
        //イベント日付一覧
        var flagLoaded = false;
        var events = ["2016-4-21","2016-04-20","2016-4-18","2016-5-20"];
        $(document).ready(function(){
            $( "#searchDate" ).datepicker({
            showOn: "both",
            buttonImage: "assets/images/icon_calendar.jpg",
            buttonImageOnly: true,
            buttonText: "開催日",
            dateFormat: 'yy-mm-dd',
            beforeShowDay: function(date) {
                var m = ("0" + (date.getMonth()+1)).slice(-2), d = ("0" + date.getDate()).slice(-2), y = date.getFullYear();
                if($.inArray(y + '-' + m + '-' + d,events) >= 0) {
                    return [true, 'events', ''];
                }
                return [true];
            }
            });
        });
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-2097811-12"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-2097811-12');
    </script>
</head>

<body>
    <section id="wrapper">
        <header id="header-area">
            <div class="inner">
                <h1 id="hLogo">
                    <a href="<?= $SORIMACHI_HOME ?>">
                        <img src="assets/images/logo.png" width="180" height="46" alt=""/>
                    </a>
                </h1>
                <h2 class="hTitle">店頭デモンストレーション</h2>
                <p class="hDes">全国の家電量販店の店頭にて弊社の担当者が「会計王シリーズ」の操作方法をご案内いたします。製品に関するご質問や導入に関するご相談などございましたらぜひお気軽にお越しください。</p>
            </div>
        </header>
        <article id="main-area" class="clearfix">
