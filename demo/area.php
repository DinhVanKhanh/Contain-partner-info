<?php
	if ( !session_id() ) {
		session_start();
	}
	require_once __DIR__ . "/libs/redirect.class.php";
	use Redirect\Redirect as Redirect;

	if ( empty( $_SESSION['userDM'] ) ) {
		new Redirect( 'login.php' );
	}

	$title   = "地区管理";
	$scripts = '<script type="text/javascript" src="assets/js/proccess/P_area.js"></script>' .
			'<script type="text/javascript" src="assets/js/ready/R_area.js"></script>
			<script>
				$(document).ready(function () {
					document.getElementById("arrow-up-down").style.top = $("#main").offset().top + 30 + "px";
					document.getElementById("arrow-up-down").style.left = $("#main").offset().left + 1010 + "px";
				});

				$(window).resize(function(){
					document.getElementById("arrow-up-down").style.top = $("#main").offset().top + 30 + "px";
					document.getElementById("arrow-up-down").style.left = $("#main").offset().left + 1010 + "px";
				});
			</script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

	<img id="scLoading" style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="tableLayout">
				<div id="arrow-up-down" style="position: absolute;">
					<img style="cursor: pointer;" width="22px" height="22px" src="assets/images/up.jpg" id="up" /><br/>
                	<img style="cursor: pointer;" width="22px" height="22px" src="assets/images/down.jpg" id="down" />
				</div>

				<!-- DIALOG Create new shop programs -->
				<section class="section fancyboxSection" id="inline0">
					<form action="" method="POST">
						<h2 class="h2Title"><span>地区を登録します。</span></h2>
						<div class="frmSection">
							<p class="error error_inline0 pb0">&nbsp;</p>
							<div class="show_parent">
								<table>
									<tr>
										<td class="w20"><label for="txtStCode">地区コード </label></td>
										<td><input type="text" name="txtAcode" id="txtAcode" size="60" class="img-responsive" maxlength="30" /></td>
									</tr>
									<tr>
										<td><label for="txtStName">地区名</label></td>
										<td><input type="text" name="txtAname" id="txtAname" size="60" class="img-responsive" maxlength="50" /></td>
									</tr>
								</table>
							</div>
							<p class="center pb0">
								<input type="button" name="submit_a" onclick="saveAreas()" value="登録" style="width: 150px; font-size: 15px;" />
								<a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
									style="width: 150px; font-size: 15px;">キャンセル</a> </p>
						</div>
					</form>
				</section>
				<div class="boxStyle01">
					<p class="boxTitle">地区管理</p>
					<p class="boxBtn">
						<button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0" style="width: 130px; margin-right: 3px;">CSV 出カ</button>
						<button name="btnCreate" class="fancybox3 btn btnAdd" onclick="openDialog(this.id,false);" href="#inline0" style="width: 130px;">追加</button>
					</p>

					<!-- Content -->
					<div id="tableContent"></div>
				</div>

				<!-- Confirm dialog -->
				<div class="fancyboxConfirm" id="confirmBox">
					<div class="cell-middle">
						<p class="message" id="msg">この領域を削除してもよろしいですか？</p>
						<p class="btnCenter">
							<button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    		<button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
							<a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
						</p>
					</div>
				</div>
        	</div>
			<input type="hidden" id="areaId">
			<input type="hidden" id="isEdit">
			<input type="hidden" id="checkRowId">
		</div>
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>