<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userDM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "ユーザー管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_user.js"></script>' .
                '<script type="text/javascript" src="assets/js/ready/R_user.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";
    require_once __DIR__ . "/src/model/M_user.class.php";
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading"
        style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;"
        src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="tableLayout">
            <div class="boxStyle01">
                <p class="boxTitle">ユーザー管理</p><?php
            if ( $_SESSION['roleDM'] == M_user::ROLE_ADMIN ) : ?>
                <p class="boxBtn">
                    <button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0"
                        style="width: 130px; margin-right: 3px;">CSV 出カ</button>
                    <button name="btnCreate" class="fancybox4 btn btnAdd" value="" onclick="openDialog(this.id,false);"
                        href="#inline0" style="width: 130px;">追加</button>
                </p>

                <!-- Content -->
                <div id="tableContent"></div>
            </div>

            <!-- Confirm dialog -->
            <div class="fancyboxConfirm" id="confirmBox">
                <div class="cell-middle">
                    <p class="message">ユーザーを削除してよろしいでしょうか？</p>
                    <p class="btnCenter">
                        <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                        <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                        <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                    </p>
                </div>
            </div><?php
            else: ?>
                <!-- Content -->
                <div id="tableContent">

                </div>
            </div><?php
            endif; ?>

        <!-- DIALOG Create new register user dialog -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>ユーザーを登録します。</span></h2>
            <div class="frmSection" id="logina">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <table class="">
                        <tr>
                            <th class="w20"><label for="role">権限</label></th>
                            <td>
                                <select name="role" id="role">
                                    <option value="0">admin</option>
                                    <option value="1">user</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th class="w20"><label for="username">ユーザーID</label></th>
                            <td><input type="text" name="username" id="username" size="40"
                                    class="img-responsive num_alphabet" maxlength="12" /></td>
                        </tr>
                        <tr>
                            <th><label for="fullname">ユーザー名</label></th>
                            <td><input type="text" name="fullname" id="fullname" size="40" class="img-responsive"
                                    maxlength="12" /></td>
                        </tr>
                        <tr class="pass-add">
                            <th class="w20"><label>パスワード</label></th>
                            <td>
                                <input type="password" name="pwd1" size="40" maxlength="12" />
                            </td>
                        </tr>
                        <tr class="pass-edit">
                            <th class="w20"><label>パスワード</label></th>
                            <td>
                                <button id="cPass1" type="button" onclick="$(this).hide(); $('#cPass2').show(); " style="padding:5px; margin:0; font-size:14px;">変更</button>
                                <div style="display:none;">
                                    <input type="password" name="pwd1" size="20" maxlength="12" /> <button id="cPass2" type="button" onclick="$('#cPass2').hide(); $('#cPass1').show();" style="margin:0; padding:5px; font-size:14px;">終了</button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <p class="center pb0">
                    <input type="submit" name="submit_a" onclick="saveUser();" value="登録"
                        style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
                        style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
        </div>
        <input type="hidden" id="userId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="hidePw1" />
        <input type="hidden" id="change" />
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>