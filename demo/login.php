<?php
	if (!session_id()) {
		session_start();
	}

	$title = "ログイン";
	$linkCss = '<link href="assets/css/styles.css" rel="stylesheet" type="text/css"/>';
	$scripts = "";

	require_once __DIR__ . "/view/template/header/normal.php";

	require_once __DIR__ . "/config/database.class.php";
	use Redirect\Redirect as Redirect;

	if ( isset( $_SESSION['userDM'] ) ) {
		new Redirect( "schedule.php" );
	}

	$err      = '';
	$chkLogin = true;
	$UserCd   = trim( $_POST['txtUname'] ?? '' );
	$Password = trim( $_POST['txtUpass'] ?? '' );

	if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
		if ( empty( $UserCd ) ) {
			$chkLogin = false;
			$err = 'ユーザーIDは未入力です。';
			goto Error;
		}

		if ( empty( $Password ) ) {
			$chkLogin = false;
			$err = 'パスワードは未入力です。';
			goto Error;
		}
		echo 'ss';

	$Conn = new Database;
	$stmt = $Conn->conn->prepare("Select `UserId`, `UserCd`, `KengenKbn` from `infodemo_users` where `UserCd` = :UserCd and `Password` = MD5(:Password);");
	$stmt->bindParam(':UserCd', $UserCd, PDO::PARAM_STR, 12);
	$stmt->bindParam(':Password', $Password, PDO::PARAM_STR, 12);
	$stmt->execute();

	if ( $stmt->rowCount() < 1  ) {
		$chkLogin = false;
		$err = 'ログインできません。';
		goto Error;
	}

	$stmt = $stmt->fetch(PDO::FETCH_ASSOC);

	$_SESSION['idDM']   = (int) $stmt['UserId'];
	$_SESSION['userDM'] = $stmt['UserCd'];
	$_SESSION['roleDM'] = (int) $stmt['KengenKbn'];

	new Redirect( "schedule.php" );
	// echo "<script>window.location.href = 'schedule.php'</script>"; die();
	}
?>

<?php Error: ?>
<section class="form_login">
    <h2 class="h2Title"><span>店頭デモ　管理者画面　ログイン</span></h2>
    <div class="login_text">
        <div id="message" class="center">
            <?= !$chkLogin ? $err : '' ?>
        </div>

        <form class="form-horizontal" method="post" role="form">
            <div class="form-group">
                <label class="control-label  col-sm-3" for="username">ユーザーID</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="txtUname" id="txtUname" size="20" maxlength="12"
                        value="<?= $UserCd ?>">
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