<?php
$title   = "給料王 年末調整セミナー";
$scripts = '<script type="text/javascript" src="../assets/js/proccess/P_nenchou.js"></script>';
$linkCss = '';
//use when responsive
// require_once __DIR__ . "/../view/template/header/client.php";
// ↓↓　<2021/08/31> <VanKhanh> <Because not responsive,so get file in view\template\header\client.php>
require_once __DIR__ . '/../libs/webserver_flg.class.php';
require_once __DIR__ . "/../config/database.class.php";
require_once __DIR__ . "/../libs/redirect.class.php";

date_default_timezone_set("Asia/Tokyo");
// ↑↑　<2021/08/31> <VanKhanh> <Because not responsive,so get file in view\template\header\client.php>

$conn = new Database;
$query2 = "SELECT sumary.*
					FROM infoseminar_sumary sumary ,infoseminar_todouhukens todous, infoseminar_areas areas
					WHERE sumary.TypesId = 4
                    AND sumary.TodouhukenId = todous.TodouhukenId
                    AND todous.AreaId = areas.AreaId
                    AND areas.AreaCode = '1'";
$stmt = $conn->conn->prepare($query2);
$stmt->execute();
$b = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- ↓↓　<2021/08/31> <VanKhanh> <Because not responsive,so get file in view\template\header\client.php> -->
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=0"> -->
    <!-- <meta name="viewport" content="width=device-width,user-scalable=no"> -->
    <meta name="viewport" content="width=1024">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= $title ?></title>

    <link href="../assets/css/styles.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/styles_y.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/client.css" rel="stylesheet" type="text/css" />
    <!-- use when responsive -->
    <!-- <link href="../assets/css/responsive.css" rel="stylesheet" type="text/css" /> -->
    <?= $linkCss ?>

    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/init.js" defer></script>
    <script type="text/javascript" src="../assets/js/scripts.js" defer></script>
    <?= $scripts ?>
</head>

<body>
    <section id="wrapper" class="client">
        <!-- ↑↑　<2021/08/31> <VanKhanh> <Because not responsive,so get file in view\template\header\client.php> -->
        <article id="main" class="clearfix nenchou">
            <img id="scLoading" style="position:absolute; right:45%; top:50%; z-index:9999; display:none; background-color:#333; padding:2%;" src="../assets/images/icon_loading.gif" />
            <section id="main_content">
                <!--/tablebox start/-->

                <div class="table_client">
                    <p class="hide_sp"><img src="../assets/images/nenchou2021.png"></p>

                    <!--for mobile if use responsive-->
                    <!-- <p class="show_ban_sp"><img src="../assets/images/n_seminer.gif" width="100%"></p> -->
                    <!-- show area -->
                    <div id="tableAra" class="tableAreaD"></div>

                    <!-- area comment 2021/10/08 add -->
                    <div id="tableAreaDComment" class="tableAreaDComment" style="font-size:13px; padding-bottom:10px;"><img src="../assets/images/seminar_d_mark_online.gif" width="120">&nbsp;のセミナーは、全国どの地域からでもお申込みいただけます。</div>

                    <!-- Content -->
                    <div id="tableContent" class="tableContentD" style="margin-top: 10px;"></div>

                    <table class="tblContact" style="border-collapse:collapse;">
                        <tbody>
                            <tr>
                                <td nowrap="" class="center" style="padding:5px 15px; background-color:#F0F0F0; border:1px #888888 solid; font-size: 14px; font-family: 'ＭＳ Ｐゴシック',Osaka,sans-serif"><b>お問い合わせ先</b></td>
                            </tr>
                            <tr>
                                <td nowrap="" style="padding:10px 15px; border:1px #888888 solid;"><b>ソリマチ株式会社&#12288;ソリマチパートナー事務局</b><br>
                                    〒141-0022<br>
                                    東京都品川区東五反田 3-18-6 ソリマチ第８ビル<br>
                                    TEL：03-3446-1311<br>
                                    FAX：03-5475-5339<br>
                                    e-mail：<a href="mailto:seminar@mail.sorimachi.co.jp">seminar@mail.sorimachi.co.jp</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" id="seminarId">
                <input type="hidden" id="isNew">
            </section>
        </article>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#mnSeminar').css('color', '#cc3300');
                loadSemianrDClientList();
                loadSemianrDAreaList();
            });
        </script>

        <?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>
        <div id="area-list">
            <ul>
                <li></li>
            </ul>
        </div>