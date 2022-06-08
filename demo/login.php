<?php
	if (!session_id()) {
		session_start();
	}

	$title = "ログイン";
	$linkCss = '<link href="assets/css/styles.css" rel="stylesheet" type="text/css"/>';
	$scripts = "";

	require_once __DIR__ . "/view/template/header/normal.php";
	use Redirect\Redirect as Redirect;

	if ( isset( $_SESSION['userDM'] ) ) {
		new Redirect( "schedule.php" );
	}

	$chkLogin = true;
	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
		if ( empty( $_POST['txtUname'] ) ) {
			$chkLogin = false;
			$err = 'ユーザーIDは未入力です。';
			goto Error;
		}

		if ( empty( $_POST['txtUpass'] ) ) {
			$chkLogin = false;
			$err = 'パスワードは未入力です。';
			goto Error;
		}

		$u_code = $_POST['txtUname'];
		$u_pass = $_POST['txtUpass'];

		$Conn = new Database;
		$isUser = $Conn->conn->prepare("Select `UserCd` from `infodemo_users` where `UserCd` = '" . $u_code . "' and `Password` = '" . md5( $u_pass ) . "'");
		$isUser->execute();
		if ( $isUser->rowCount() < 1  ) {
			$chkLogin = false;
			$err = 'ログインできません。';
			goto Error;
		}

		$_SESSION['userDM'] = $u_code;
		new Redirect( "schedule.php" );
	}
?>

<?php Error: ?>
<section class="form_login">
    <h2 class="h2Title"><span>店頭デモ　管理者画面　ログイン</span></h2>
    <div class="login_text">
        <div id="message" class="center">
		<?php
			if ( !$chkLogin ) {
				echo $err;
			}
		?>
        </div>

        <form class="form-horizontal" method="post" role="form">
            <div class="form-group">
                <label class="control-label  col-sm-3" for="username">ユーザーID</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="txtUname" id="txtUname" size="20" maxlength="12"
                        value="<?php if ( !empty( $_POST['txtUname'] ) ) { echo $_POST['txtUname']; }?>">
                </div>
            </div>

            <div class="form-group clear">
                <label class="control-label col-sm-3" for="pwd1">パスワード</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="txtUpass" id="txtUpass" maxlength="12">
                </div>
            </div>

            <div class="form-group clear">
                <div class="col-sm-offset-3">
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary" value=" ログイン">
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>