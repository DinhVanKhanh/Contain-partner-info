<?php
	if ( !session_id() ) {
		session_start();
	}
	require_once __DIR__ . "/libs/redirect.class.php";
	use Redirect\Redirect as Redirect;

	if ( empty( $_SESSION['userDM'] ) ) {
		new Redirect( "login.php" );
	}

	$title   = "バナー設置管理";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_banner.js"></script>' .
            '<script type="text/javascript" src="assets/js/ready/R_banner.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/view/template/header/normal.php";

	$Conn = new Database;
	$optionAreas = $optionShops = "";

	// Areas
	$stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infodemo_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }

    // Shops
    $stmt = $Conn->conn->prepare( "SELECT ShopId, Name  FROM infodemo_shops WHERE IsSpecial = 1" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $shop ) {
			$optionShops .= '<option value="'. $shop["ShopId"] . '">' . $shop["Name"] . "</option>";
		}
    }
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
		<?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <p class="boxTitle">バナー情報管理</p>
        <div class="boxStyle01">
            <p class="boxBtn">
                <button name="btnCreate" class="btn btnAdd" onclick="exportCSV();" href="#inline0" style="width: 130px; margin-right: 3px;">CSV 出カ</button>
                <button name="btnCreate" class="btn btnAdd fancybox5" onclick="openDialog(this.id,false);" href="#inline0" style="width: 130px;">追加</button>
            </p>

 			<!-- Content -->
            <div id="tableContent" style="margin-top: 20px;"></div>
        </div>

        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message">このバナーを削除してよろしいでしょうか？</p>
                <p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                </p>
            </div>
        </div>
        <!-- DIALOG Create new shop programs -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>バナー設置管理</span></h2>

            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <table class="">
                        <tr>
                            <th class="w14">地区
                                <input type="radio" name="IsShop" value="0" id="IsArea"/></th>
                            <td>
								<select name="parent_area" class="parent_type" id="parent_area">
									<?= $optionAreas ?>
								</select>
							</td>
                            <td style="text-align: right"><label for="IsShop">特定販売店</label>
                                <input type="radio" name="IsShop" value="1" id="IsShop" />
								<select name="parent_shop" class="parent_type" id="parent_shop" >
									<?= $optionShops ?>
								</select>
                            </td>
                        </tr>
                        <tr>
                            <th>バナー1
                                <span style="color:red;cursor: pointer;display:none;" id="btnDelBn1" onclick="$('#inputImage1').val(''); $('#img1').val('');">削除</span>
                            </th>
                            <td colspan="2">
                                <input type="text" name="txtTop" disabled="disabled" id="inputImage1" style="width: 450px;" />
                                <button name="btnUploadTop" class="btnEdit" id="btnUploadTop" onclick="$('#ipImg1').click();">参照</button>
                                <input type="checkbox" name="chkTop" id="chkTop" value="0"/>
                                <label for="chkTop">無効</label>
							</td>
                        </tr>
                        <tr>
                            <th>バナー2
                                <span style="color:red;cursor: pointer;display:none;" id="btnDelBn2" onclick="$('#inputImage2').val(''); $('#img2').val('');">削除</span>
                            </th>
                            <td colspan="2">
                                <input type="text" name="txtLeft" disabled="disabled" id="inputImage2" style="width: 450px;" />
                                <button name="btnUploadLeft" class="btnEdit" id="btnUploadLeft" onclick="$('#ipImg2').click();">参照</button>
                                <input type="checkbox" name="chkLeft" id="chkLeft" value="0"/>
                                <label for="chkLeft">無効</label>
							</td>
                        </tr>
                        <tr>
                            <th>バナー3
                                <span style="color:red;cursor: pointer;display:none;" id="btnDelBn3" onclick="$('#inputImage3').val(''); $('#img3').val('');">削除</span>
                            </th>
                            <td colspan="2">
                                <input type="text" name="txtRight" disabled="disabled" id="inputImage3" style="width: 450px;" />
                                <button name="btnUploadRight" class="btnEdit" id="btnUploadRight" onclick="$('#ipImg3').click();">参照</button>
                                <input type="checkbox" name="chkRight" id="chkRight" value="0"/>
                                <label for="chkRight">無効</label>
                            </td>
                        </tr>
                        <tr>
                            <th>備考</th>
                            <td colspan="2">
                                <textarea name="description" id="description" class="classy-editor" rows="10" cols="45"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <p class="center">
                    <input type="submit" onclick="saveBanner();" value="登録" style="width: 150px; font-size: 15px;" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000">
                    <input type="file" id="ipImg1" name="ipImg" onchange="$('#inputImage1').val( $(this)[0].files[0].name );" style="display: none;" accept="image/png, image/gif, image/jpeg">
                    <input type="file" id="ipImg2" name="ipImg" onchange="$('#inputImage2').val( $(this)[0].files[0].name );" style="display: none;" accept="image/png, image/gif, image/jpeg">
                    <input type="file" id="ipImg3" name="ipImg" onchange="$('#inputImage3').val( $(this)[0].files[0].name );" style="display: none;" accept="image/png, image/gif, image/jpeg">
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close();" style="width: 150px; font-size: 15px;">キャンセル</a>
				</p>
            </div>
        </section>
        <input type="hidden" id="bannerId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="img1">
        <input type="hidden" id="img2">
        <input type="hidden" id="img3">
        <a id="link" href="" target="_blank"></a>
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>