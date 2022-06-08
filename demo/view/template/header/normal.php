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
	<title><?= $title ?></title>
	<meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
	<meta name="format-detection" content="telephone=no">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/styles.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/styles_y.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/jquery.timepicker.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/ui.datepicker.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/jquery.fancybox.css?v=2.1.5" rel="stylesheet" type="text/css" />
	<link href="assets/css/jquery.classyedit.css" rel="stylesheet" type="text/css" />
	<?= $linkCss ?>
	<style>
		.buttonLog {
			/* background-color: #4CAF50; Green */
			border: none;
			color: white;
			padding: 15px 32px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			font-size: 16px;
			font-weight:bold;
			margin: 4px 2px;
			cursor: pointer;
			-webkit-transition-duration: 0.4s; /* Safari */
			transition-duration: 0.4s;
		}

		.buttonLog:hover {
			box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24), 0 17px 50px 0 rgba(0,0,0,0.19);
		}
	</style>
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/js/init.js"></script>
	<script type="text/javascript" src="assets/js/alert.js"></script>
	<script type="text/javascript" src="assets/js/jquery.timepicker.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.datepair.js"></script>
	<script type="text/javascript" src="assets/js/Datepair.js"></script>
	<script type="text/javascript" src="assets/js/ui.datepicker.js"></script>
	<script type="text/javascript" src="assets/js/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="assets/js/scripts.js"></script>
	<script src="assets/js/jquery.fancybox.js?v=2.1.5" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.fancybox').fancybox();
			$('.fancy_h500').fancybox({'height': 500});
			$('.fancy_h520').fancybox({'height': 520});
			$('.fancy_h650').fancybox({'height': 650});
			$('.fancybox_map').fancybox({type: "iframe"});
			$('.btnConfirm').fancybox({'modal' : true, 'minHeight': 50, 'height':120, 'width':300});
			$('.fancy_area_del').fancybox({'modal' : true, 'minHeight': 50, 'height':150, 'width':300});
			$('.btnConfirm1').fancybox({'modal' : true, 'minHeight': 50, 'height':120, 'width':300});
			$('.fancybox2').fancybox({'width':1100});
			$('.fancybox3').fancybox({'width':600, 'height':350});
			$('.fancybox4').fancybox({'width':700,'height':450});
			$('.fancybox5').fancybox({'width':850, 'height':650});
			$('.fancybox6').fancybox({'width':350, 'height':50});
		});
	</script>
	<?= $scripts ?>
</head>

<body>
	<section id="wrapper">
		<header id="header" class="clear">
			<hgroup id="head-box" class="clearfix">
				<h1 id="head-logo"></h1>
				<h2 class="Title_header">店頭デモ　管理者画面</h2>
			</hgroup>
			<div style="position:absolute;top:18%;right:2%;">
				<?php
					if ( isset( $_GET['logout'] ) && $_GET['logout'] == true ) {
						unset( $_SESSION['userDM'] );
						new Redirect( 'login.php' );
					}

					if( !empty( $_SESSION['userDM'] ) ) {
						echo '<a href="?logout=true" class="buttonLog">ログアウト</a>';
					}
				?>
			</div>
		</header>
