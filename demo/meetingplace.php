<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userDM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "会場管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_meetingplace.js"></script>' .
            '<script type="text/javascript" src="assets/js/ready/R_meetingplace.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";
	
	$Conn = new Database;
	$optionAreas = $optionTodo = "";

	// Option Areas
    $stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infodemo_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }
	
    // Option todouhukens
    $stmt = $Conn->conn->prepare( "SELECT TodouhukenId, TodouhukenName FROM infodemo_todouhukens" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $todo ) {
            $optionTodo .= "<option value='" . $todo["TodouhukenId"] . "'>" . htmlspecialchars( $todo["TodouhukenName"] ) . "</option>";
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
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="boxTitle" style="width: auto;">会場管理</p>
                <div class="scheduleCon" style="width: auto; padding:5px;">
                    <p class="scheduleSe1">
                        <label for="area" style="float:left; margin-right:2px; margin-top: 5px; font-size: 15px;" >地区 </label>
                        <span id="toTag" class="se">
                        <select id="area" name="area" onchange="filterMeetingPlaceByArea();" class="selected">
                            <option value="-1" selected="selected">すべて</option>
                            <?= $optionAreas ?>
                        </select>
                        </span> </p>
                    <p class="fLeft pb0">
                        <button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0" style="width: 130px; margin-right: 3px;" >CSV 出カ</button>
                        <button name="btnCreate" class="fancy_h520 btn btnAdd" onclick="openDialog(this.id,false);" href="#inline0" style="width: 130px;" >追加</button>
                    </p>
                </div>
            </div>

            <!-- Content -->
            <div id="tableContent"></div>
		</div>

        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox"> 
            <div class="cell-middle">
				<p class="message">会場を削除してよろしいでしょうか？</p>
				<p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
					<a title="Close" class="btn btnClose" onclick="$.fancybox.close()">いいえ</a>
				</p>
			</div>
        </div>
        
        <!-- DIALOG Create new meeting places -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>会場を登録します。</span></h2>
            <img class="dialogLoading" style="display:none;" src="assets/images/icon_loading.gif" />
            <div class="frmSection" style="padding:0px;">
                <p class="error error_inline0 pb0">&nbsp;</p>
				<!-- dialog content -->
                <div class="show_parent">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="text-align: left;vertical-align: middle;width:14%;">都市</td>
                            <td><select style="width:250px;" id="todouhuken">
                                    <?= $optionTodo ?>
                                </select></td>
                            <td style="text-align: left;vertical-align: middle;width:18%;">会場コード<span style="color:red">(必須)</span></td>
                            <td><input style="width:100%" type="text" maxlength="30" name="txtMtCode" id="txtMtCode" /></td>
                        </tr>
                        <!-- SHOPNAME1 - TEL -->
                        <tr>
                            <td style="text-align: left;vertical-align: middle;">店名1<span style="color:red">(必須)</span></td>
                            <td><input style="width:100%" type="text" maxlength="20" name="txtMtShopName1" id="txtMtShopName1"  /></td>
                            <td style="text-align: left;vertical-align: middle;">Tel</td>
                            <td><input style="width:100%" type="text" maxlength="20" name="txtMtTel" id="txtMtTel"  /></td>
                        </tr>
                        <!-- SHOPNAME2 - FAX -->
                        <tr>
                            <td style="text-align: left;vertical-align: middle;">店名2</td>
                            <td><input style="width:100%" type="text" maxlength="20" name="txtMtShopName2" id="txtMtShopName2"  /></td>
                            <td style="text-align: left;vertical-align: middle;">Fax</td>
                            <td><input style="width:100%" type="text" maxlength="20" name="txtMtFax" id="txtMtFax" /></td>
                        </tr>
                        
                        <!-- Postal code - Map -->
                        <tr>
                            <td style="text-align: left;vertical-align: middle;">郵便番号</td>
                            <td><input style="width:50%" type="text" maxlength="50" name="txtMtPos" id="txtMtPos"  /></td>
                            <td style="text-align: left;vertical-align: middle;">地図(URL)</td>
                            <td><input style="width:100%" type="text" name="txtMtMap" maxlength="500" id="txtMtMap" /></td>
                        </tr>
                        
                        <!-- Addrress1 - Address2 -->
                        <tr>
                            <td style="text-align: left;vertical-align: middle;">住所1</td>
                            <td><input  style="width:100%" type="text" maxlength="500" name="txtMtAddress" id="txtMtAddress"  /></td>
                            <td style="text-align: left;vertical-align: middle;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="text-align: left;vertical-align: middle;">住所2</td>
                            <td><input style="width:100%" type="text" maxlength="500" name="txtMtAddress1" id="txtMtAddress1" /></td>
                            <td style="text-align: left;vertical-align: middle;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4">
								<p class="p_2btn">
                                    <input type="button" name="submit_a" onclick="saveMeetingPlaces();" value="登録" style="width: 150px; font-size: 15px;" />
									<a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;" >キャンセル</a>
								</p>
							</td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
        <input type="hidden" id="MtId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>