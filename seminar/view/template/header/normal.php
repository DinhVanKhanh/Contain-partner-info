<?php
	global $scripts, $linkCss, $title;

	require_once __DIR__ . '/../../../libs/webserver_flg.class.php';
	require_once __DIR__ . "/../../../config/database.class.php";
	require_once __DIR__ . "/../../../libs/redirect.class.php";
	use Redirect\Redirect as Redirect;

	date_default_timezone_set( "Asia/Tokyo" );
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="format-detection" content="telephone=no">
    <title><?= $title ?></title>

    <link type="text/css" href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/styles.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/styles_y.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/responsive.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/jquery.timepicker.css" rel="stylesheet" />
    <link type="text/css" href="assets/css/ui.datepicker.css" rel="stylesheet" />
	<link type="text/css" href="assets/css/jquery.fancybox.css?v=2.1.5" rel="stylesheet" />
	<link type="text/css" href="assets/css/jquery.classyedit.css" rel="stylesheet" />
	<?= $linkCss ?>

    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <script type="text/javascript" src="assets/js/init.js"></script>
    <script type="text/javascript" src="assets/js/alert.js"></script>
    <script type="text/javascript" src="assets/js/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.datepair.js"></script>
    <script type="text/javascript" src="assets/js/Datepair.js"></script>
    <script type="text/javascript" src="assets/js/ui.datepicker.js"></script>
    <script type="text/javascript" src="assets/js/jquery.maskedinput.min.js"></script>
    <script type="text/javascript" src="assets/js/scripts.js"></script>
    <script type="text/javascript" src="assets/js/numeral.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.fancybox.js?v=2.1.5"></script>
    <script type="text/javascript">
		$('.fancybox').fancybox();
		$('.fancy_h450').fancybox({
			'height': 450
		});
		$('.fancy_h500').fancybox({
			'height': 500
		});
		$('.fancy_h600').fancybox({
			'height': 600
		});
		$('.fancy_h620').fancybox({
			'height': 620
		});
		$('.fancy_h650').fancybox({
			'height': 650
		});
		$('.fancybox_map').fancybox({
			type: "iframe"
		});
		$('.btnConfirm').fancybox({
			'modal': true,
			'minHeight': 50,
			'height': 120,
			'width': 300
		});
		$('.fancy_area_del').fancybox({
			'modal': true,
			'minHeight': 50,
			'height': 150,
			'width': 300
		});
		$('.btnConfirm1').fancybox({
			'modal': true,
			'minHeight': 50,
			'height': 120,
			'width': 300
		});
		$('.fancybox2').fancybox({
			'width': 1100
		});
		$('.fancybox3').fancybox({
			'width': 600,
			'height': 350
		});
		$('.fancybox1').fancybox({
			'width': 600,
			'height': 300
		});
		$('.fancybox4').fancybox({
			'width': 700,
			'height': 450
		});
		$('.fancybox5').fancybox({
			'width': 850,
			'height': 650
		});
		$('.fancybox6').fancybox({
			'width': 350,
			'height': 50
		});
		$('.fancymaster').fancybox({
			'width': 750,
			'height': 530
		});
		$('.fancyboxmail').fancybox({
			'width': 580,
			'height': 550
		});
		$('.fancybox7').fancybox({
			'width': 700,
			'height': 370
		});
    </script>
	<?= $scripts ?>
</head>

<body>
    <section id="wrapper">
        <header id="header" class="clear">
            <hgroup id="head-box" class="clearfix">
                <h1 id="head-logo"></h1>

                <h2 class="Title_header">セミナー情報</h2>
            </hgroup>
            <div style="position:absolute;top:18%;right:2%;">
                <?php
					if ( isset($_GET['logout']) && $_GET['logout'] == true ) {
						unset( $_SESSION['userSM'] );
						new Redirect( 'login.php' );
					}

					if( !empty( $_SESSION['userSM'] ) ) {
						echo '<a href="?logout=true" class="buttonLog">ログアウト</a>';
					}
				?>
            </div>
        </header>
