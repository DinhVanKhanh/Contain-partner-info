<?php 
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userSM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "種類管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_item.js"></script>' .
                '<script type="text/javascript" src="assets/js/ready/R_item.js"></script>';
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

            <div class="boxStyle02 clearfix">
                <div class="groupTitle clearfix">
                    <p class="scheduleTitle pb0"><span>種類管理</span></p>
                    <p class="fRight right pb0">
                        <button name="btnCreate" class="fancybox3 btn btnAdd"
                            onclick="openDialog(this.id,false);" href="#inline0"
                            style="padding:5px;width:60px;">追加</button>
                    </p>
                </div>

                <!-- Content -->
                <div id="tableContent"></div>
            </div>

            <!-- Confirm dialog -->
            <div class="fancyboxConfirm" id="confirmBox">
                <div class="cell-middle">
                    <p class="message">種類を削除してよろしいでしょうか？</p>
                    <p class="btnCenter">
                        <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                        <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                        <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                    </p>
                </div>
            </div>

            <!-- DIALOG Create new register user dialog -->
            <section class="section fancyboxSection" id="inline0">
                <h2 class="h2Title"><span>種類管理</span></h2>
                <img class="dialogLoading"
                    src="assets/images/icon_loading.gif" />
                <div class="frmSection">
                    <p class="error error_inline0 pb0">&nbsp;</p>
                    <div class="show_parent">
                        <form action="" accept-charset="utf-8">
                            <table class="tableItems">
                                <tr>
                                    <td style="width: 25%;">種類<span style="color:red">(必須)</span></td>
                                    <td><select name="Type" id="Type" style="width:95%;">
                                            <option value="1">運営</option>
                                            <option value="2">対象製品</option>
                                            <option value="3">コース</option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <td>項目コード<span style="color:red">(必須)</span></td>
                                    <td><input type="text" name="ItemCode" id="ItemCode" maxlength="12"
                                            style="width:95%;" /></td>
                                </tr>
                                <tr>
                                    <td>項目名<span style="color:red">(必須)</span></td>
                                    <td><input type="text" name="ItemName" id="ItemName" maxlength="30"
                                            style="width:95%;" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <p class="center pb0">
                        <input type="button" name="submit_se" onclick="saveItems();" value="登録" style="width: 150px; font-size: 15px;" />
                        <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
                            style="width: 150px; font-size: 15px;">キャンセル</a> </p>
                </div>
            </section>
        </div>
        <input type="hidden" id="ItemId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>