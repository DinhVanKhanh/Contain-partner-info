<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userDM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "都市管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_todouhuken.js"></script>' .
            '<script type="text/javascript" src="assets/js/ready/R_todouhuken.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";
	
	$Conn = new Database;
    $optionAreas = "";

    // Option Areas
    $stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infodemo_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
		<?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="boxStyle01">
            <p class="boxTitle">都市管理</p>
            <div class="scheduleCon" style="width: auto; padding:5px;">
                <p class="scheduleSe1">
                    <label for="area" style="float:left; margin-right:3px; font-size:15px; margin-top: 5px;">地区 </label>
                    <span id="toTag" class="se">
                        <select id="mainarea" onchange="filterTodouhukenByArea();" class="selected">
                            <option value="-1" selected="selected">すべて</option>
                            <?= $optionAreas ?>
                        </select>
                    </span>
                </p>
                <p class="fLeft pb0">
                    <button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0"
                        style="width: 130px; margin-right: 3px;">CSV 出カ</button>
                    <button name="btnCreate" class="fancybox4 btn btnAdd" value=""
                        onclick="openDialog(this.id,false);" href="#inline0" style="width: 130px; ">追加</button>
                </p>
            </div>

            <!-- Content -->
            <div id="tableContent"></div>
        </div>

        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message" id="msg">都市コードを削除してよろしいでしょうか？</p>
                <p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                </p>
            </div>
        </div>

        <!-- DIALOG Create new todouhukens -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>都市を登録します。</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <form action="" method="POST">
                        <table>
                            <tr>
                                <th class="w16" style="width:22%; text-align:right"><label for="txtStCode">地区 </label></th>
                                <td>
                                    <select id="area" style="width:50%;">
                                        <?= $optionAreas ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th class="w20" style="text-align:right"><label for="txtStCode">都市 コード <span
                                            style="color:red">(必須)</span></label></th>
                                <td>
                                    <input type="text" name="txtStCode" id="txtStCode" size="40"
                                        class="img-responsive" maxlength="30" />
                                </td>
                            </tr>
                            <tr>
                                <th class="w20" style="text-align:right"><label for="txtStName">都市 名<span
                                            style="color:red">(必須)</span> </label></th>
                                <td>
                                    <input type="text" name="txtStName" id="txtStName" size="40"
                                        class="img-responsive" maxlength="50" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <p class="center pb0">
                    <input type="button" name="submit_a" onclick="saveTodouhuken();" value="登録" style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
                        style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
        <input type="hidden" id="todouId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>