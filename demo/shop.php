<?php
	if ( !session_id() ) {
		session_start();
	}
	require_once __DIR__ . "/libs/redirect.class.php";
	use Redirect\Redirect as Redirect;

	if ( empty( $_SESSION['userDM'] ) ) {
		new Redirect( 'login.php' );
	}

	$title   = "販売店管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_shop.js"></script>' .
            '<script type="text/javascript" src="assets/js/ready/R_shop.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
		<?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <!-- DIALOG Create new shop programs -->
        <section class="section fancyboxSection" id="inline0">
            <form action="" method="POST">
                <h2 class="h2Title"><span>販売店を登録します。</span></h2>
                <div class="frmSection">
                    <p class="error error_inline0 pb0">&nbsp;</p>
                    <div class="show_parent">
                        <table>
                            <tr>
                                <td class="w14"><label for="txtStCode">販売店コード </label></td>
                                <td><input type="text" maxlength="30" name="txtStCode" id="txtStCode" size="60" class="img-responsive" maxlength="30" /></td>
                            </tr>
                            <tr>
                                <td><label for="txtStName">販売店名 </label></td>
                                <td><input type="text" name="txtStName" id="txtStName" size="60" class="img-responsive" maxlength="50" /></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td valign="middle">
                                    <input type="checkbox" name="ckSpecial" id="ckSpecial" size="10" class="img-responsive" style="float: left; margin-right:15px; width:30px;" />
                                    <label for="ckSpecial">特定 </label>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="txtStDescript">備考 </label></td>
                                <td>
                                    <textarea name="txtStDescript" id="txtStDescript" class="classy-editor" rows="10" cols="52"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <p class="center pb0">
                        <input type="button" name="submit_a" onclick="saveShop();" value="登録" style="width: 150px; font-size: 15px;" />
                        <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                    </p>
                </div>
            </form>
        </section>

        <div class="boxStyle01">
            <p class="boxTitle">販売店管理</p>
            <p class="boxBtn">
                <button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0"
                    style="width: 130px; margin-right: 3px;">CSV 出カ</button>
                <button name="btnCreate" class="fancybox5 btn btnAdd" onclick="openDialog(this.id,false);"
                    href="#inline0" style="width: 130px;">追加</button>
            </p>

			<!-- Content -->
            <div id="tableContent"></div>
        </div>

        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message">販売店を削除してよろしいでしょうか？</p>
                <p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close();">いいえ</a>
                </p>
            </div>
        </div>

        <input type="hidden" id="shopTypeId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>