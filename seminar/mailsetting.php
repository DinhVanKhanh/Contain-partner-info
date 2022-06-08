<?php
	if ( !session_id() ) {
		session_start();
	}
	require_once __DIR__ . "/libs/redirect.class.php";
	use Redirect\Redirect as Redirect;

	if ( empty( $_SESSION['userSM'] ) ) {
		new Redirect( 'login.php' );
	}

	$title   = "メール設定";
	$scripts = '<script type="text/javascript" src="assets/js/proccess/P_mailsetting.js"></script>' .
			'<script type="text/javascript" src="assets/js/ready/R_mailsetting.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:50%; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="tableLayout">
            <!-- DIALOG Create new shop programs -->
            <section class="section fancyboxSection" id="inline0">
                <form action="" method="POST">
                    <h2 class="h2Title"><span>メール設定</span></h2>
                    <div class="frmSection">
                        <p class="error error_inline0 pb0">&nbsp;</p>
                        <div class="show_parent">
                            <table>                                                           
                                <tr>
                                    <td><label>差出人<span class="red_import">(必須)</span></label></td>
                                    <td><input type="text" name="FromMail" id="FromMail" value="" size="25" class="img-responsive" maxlength="100" /></td>
                                </tr>
                                <tr>
                                    <td><label>差出名<span class="red_import">(必須)</span></label></td>
                                    <td><input type="text" name="FromName" id="FromName" value="" size="25" class="img-responsive" maxlength="100" /></td>
                                </tr>                                
                                
                                <tr>
                                    <td><label>SMTPサーバー<span class="red_import">(必須)</span></label></td>
                                    <td><input type="text" name="Host" id="Host" value="" size="25" class="img-responsive" maxlength="30" /></td>
                                </tr>                                
                                <tr>
                                    <td><label for="itemsname">暗号化方式<span class="red_import">(必須)</span></label></td>
                                    <td>
                                    	<input type="radio" name="EncriptionType" id="raNone" value="0"><label for="raNone">なし</label>
                                    	<input type="radio" name="EncriptionType" id="raSSL" value="1" checked="checked"><label for="raSSL">SSL</label>
										<input type="radio" name="EncriptionType" id="raTLS" value="2"><label for="raTLS">TLS</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label>SMTPポート<span class="red_import">(必須)</span></label></td>
                                    <td><input type="text" name="Port" id="Port" value="" size="25" class="img-responsive" maxlength="10" /></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td style="padding-top:10px;"><input type="checkbox" id="checkSmtp" name="checkSmtp"> <label for="checkSmtp">SMTPを有効にします</label></td>
                                </tr>
                                <tr>
                                    <td><label>SMTPユーザ名</label><span class="red_import">(必須)</span></td>
                                    <td><input type="text" name="Username" id="Username" value="" size="25" class="img-responsive" maxlength="50" /></td>
                                </tr>
                                <tr>
                                    <td><label>SMTPパスワード<span class="red_import">(必須)</span></label></td>
                                    <td><input type="password" name="Password" id="Password" value="" size="25" class="img-responsive" maxlength="15" /></td>
                                </tr>
                                <tr>
                                    <td><label>テスト用の宛先</label></td>
                                    <td><input type="text" name="MailTest" id="MailTest" value="test@mail.sorimachi.co.jp" size="25" class="img-responsive" maxlength="50" /></td>
                                </tr>
                            </table>
                        </div>
                        <p class="center pb0">
                            <input type="button" name="submit_se" onclick="testSendMail();" value="テスト用の送信" />
                            <input type="button" name="submit_se" onclick="saveSeminarMail();" value="修正" style="width: 150px; font-size: 15px;" />                     
                            <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a> </p>
                    </div>
                </form>
            </section>

            <div class="boxStyle01">
                <p class="boxTitle">メール設定</p>                
                <div id="tableContent"></div>
            </div>
        </div>
        <input type="hidden" id="EmailId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="change"/>
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>