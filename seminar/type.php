<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userSM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "セミナー分類";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_type.js"></script>' .
                '<script type="text/javascript" src="assets/js/ready/R_type.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <section id="main_content">
        <div class="tableLayout">
            <!-- DIALOG CREATE NEW TYPES ▼▼▼ -->
            <section class="section fancyboxSection" id="inline0">
                <form action="" method="POST">
                    <h2 class="h2Title"><span>セミナー分類を登録します。</span></h2>
                    <div class="frmSection">
                        <p class="error error_inline0 pb0">&nbsp;</p>
                        <div class="show_parent">
                            <table>
                                <tr id="showCode">
                                    <td>種類コード</td>
                                    <td><div name="TypesId" id="TypesId"></div></td>
                                </tr>
                                <tr>
                                    <td>種類名<span style="color:red">(必須)</span></td>
                                    <td><input type="text" name="TypesName" id="TypesName" value="" maxlength="30" style="width:95%;" /></td>
                                </tr>
                                <tr>
                                    <td>説明</td>
                                    <td><textarea name="Description" id="Description" class="classy-editor" maxlength="50" rows="10" cols="28" style="width:100%;"></textarea>
                                </tr>
                            </table>
                        </div>
                        <p class="center pb0">
                            <input type="button" name="submit_se" onclick="saveTypes();" value="登録" style="width: 150px; font-size: 15px;" />
                            <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                        </p>
                    </div>
                </form>
            </section>

            <!-- DIALOG CREATE NEW TYPES　▲▲▲ -->
            <div class="boxStyle01 clearfix">
                <p class="boxTitle">種類管理</p>
                <p class="boxBtn">
                    <button name="btnCreate" class="fancybox3 btn btnAdd" value="" onclick="openDialog(this.id, false);" href="#inline0" style="width: 130px;">追加</button>
                </p>

                <!-- Content -->
                <div id="tableContent"></div>
            </div>

            <!-- Confirm dialog -->
            <div class="fancyboxConfirm" id="confirmBox">
                <div class="cell-middle">
                    <p class="message" id="msg">ユーザー管理を削除してよろしいでしょうか？</p>
                    <p class="btnCenter">
                        <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                        <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                        <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                    </p>
                </div>
            </div>
        </div>
        <input type="hidden" id="typesId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>
