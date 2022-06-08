<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userDM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "店頭デモ実施情報";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_scheldules.js"></script>' .
            '<script type="text/javascript" src="assets/js/ready/R_scheldules.js"></script>';
    $linkCss = '<style>
                    .ui-timepicker-wrapper {
                        width: 14% !important;
                    }
                </style>';
    require_once __DIR__ . "/view/template/header/normal.php";

    $Conn = new Database;
    $optionAreas = $optionShops = $optionMeet = "";

    // Option Areas
    $stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infodemo_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }

    // Option MeetingPlaces
    $stmt = $Conn->conn->prepare( "SELECT MeetingPlaceId, storeName1, storeName2 FROM infodemo_meetingplaces" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $meet ) {
            $optionMeet .= "<option value='" . $meet["MeetingPlaceId"] . "'>" . htmlspecialchars( $meet["storeName1"] . ' ' . $meet["storeName2"] ) . "</option>";
        }
    }

    // Process delete all temp file in exports folder; delete all files which created 5 days ago
    try {
        $dir   = __DIR__ . '/../data_files/';
        $loadFiles = scandir( $dir );
        foreach ( $loadFiles as $index => $file ) {
            if ( !in_array( $file, [".", ".."] ) ) {
                $datediff  = time() - filemtime( $dir . $file );
                $extension = substr( $file, strrpos( $file, "." ) );

                if ( floor( $datediff / (60 * 60 * 24) ) >= 5 ) {
                    if ( in_array( strtolower( $extension ), ["csv", "xls", "xlsx"] ) ) {
                        unlink( $dir . $file );
                    }
                }
            }
        }
    }
    catch (Exception $ex) {
        file_put_contents( __DIR__ . "/logs/" . date( '\D\E\M\O\_\R\E\P\O\R\T\_\S\C\H\E\D\U\L\E-Y-m-d\.\t\x\t' ), 'Error proccess delete all files which created 5 days ago' );
    };
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:500px; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0"> <span>店頭デモ実施情報</span> </p>
                <div class="scheduleCon">
                    <p class="fLeft pb0">
                        <label for="area" style="float:left; margin-right:4px;  margin-top: 5px; ">地区 </label>
                        <span id="toTag" class="se">
                            <select id="area" name="area" onchange="filterScheduleByArea();" style="margin-right:0px;"
                                class="selected">
                                <option value="-1" selected="selected">すべて</option>
                                <?= $optionAreas; ?>
                            </select>
                        </span>

                        <input style="display: none;" type="file" id="ipExcel" name="ipExcel"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                            onchange="$('#inputExcel').html($(this).val());" style="display: none">
                        <button onclick="document.getElementById('ipExcel').click();" class="btn"
                            style="margin-left:3px; margin-right:2px; background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white; padding:8px">ファイルを選択</button>
                    </p>

                    <p class="fRight right pb0">
                        <button
                            style="padding-left:2px;width:60px; margin-right: 3px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white; padding:8px"
                            name="btnImport" id="btnImport" class="btn">取込</button>
                        <button style="padding:8px;width:60px; margin-right: 3px;" name="btnDel" id="btnDel"
                            class="btn btnAdd">削除</button>
                        <button style="padding:8px;width:60px;" name="btnCreate" class="fancyboxSC btn btnAdd"
                            onclick="openDialog(this.id,false);" href="#inline0">追加</button>
                    </p>
                </div>

                <p class="scheduleSe1" style="float:left; width:100%; text-align:center;">
                    <label id="inputExcel"></label>
                </p>
            </div>

            <!-- Content -->
            <div id="tableContent" style="margin-top:10px;"></div>
        </div>

        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message">地区を削除してよろしいでしょうか？</p>
                <p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                    <a title="Close" id="btnCloseFc" class="btn btnClose" href="javascript:;" onclick=" $.fancybox.close();">いいえ</a>
                </p>
            </div>
        </div>

        <!-- DIALOG Create new shedule -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>店頭デモ実施情報</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <!-- dialog content -->
                    <form action="" accept-charset="utf-8">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align: left;vertical-align: middle;width:16%;">会場</td>
                                <td>
                                    <select style="width:93%;" id="mtPlace">
                                        <?= $optionMeet; ?>
                                    </select>
                                </td>

                                <td style="text-align: left;vertical-align: middle;width:16%;">
                                    販売店<span style="color:red">(必須)</span>
                                </td>

                                <td style="position: relative;">
                                    <p style="position: absolute;top:40px;left:-116px;">
                                        <input type="checkbox" id="ckSpecial"> <label for="ckSpecial">特定</label>
                                    </p>
                                    <select style="width:92%;" id="shopId">
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: left;vertical-align: middle;">
                                    開催日<span style="color:red">(必須)</span>
                                </td>

                                <td style="vertical-align: middle;">
                                    <input type="text" id="scDate" placeholder="YYYY-MM-DD" style="width:93%;">
                                </td>

                                <td style="text-align: left;vertical-align: middle;">
                                    時間<span style="color:red">(必須)</span>
                                </td>

                                <td style="vertical-align: middle;">
                                    <p id="datepair" style="vertical-align: middle;">
                                        <input type="text" id="scFromTime" class="time start" style="width:41%">
                                        ~
                                        <input type="text" id="scToTime" class="time end" style="width:41%">
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: left;vertical-align: middle;">
                                    <span id="pdfLabel">PDF</span>
                                    <p id="dlgPdf" style="height: 0px; position: relative; top: -28px;right:-48px;">
                                        <a id="pdfUrl"><img width="26px;" height="33px;"
                                                src="assets/images/icon_pdf.gif"></a>
                                        <span style="color:red;cursor: pointer;" id="btnPdfDel">削除</span>
                                    </p>
                                </td>

                                <td>
                                    <input type="text" style="width:78%;" id="inputPdf" disabled />
                                    <input type="file" id="ipPdf" name="ipPdf"
                                        onchange="$('#inputPdf').val($(this)[0].files[0].name);" style="display: none"
                                        accept="application/pdf">
                                    <input type="button" style="position:relative;left:0;padding:4px;" name="btnImport"
                                        onclick="document.getElementById('ipPdf').click();" class="btn btnAdd"
                                        value="参照">
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align: left;vertical-align: middle;">備考</td>

                                <td colspan="3">
                                    <textarea name="scDescript" id="scDescript" class="classy-editor" maxlength="500"
                                        rows="10" cols="45"></textarea>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="4" style="text-align: center ; vertical-align: middle; ">
                                    <input type="checkbox" id="scIsActive">
                                    <label for="scIsActive" style="margin-right: 20px;"> 中止告知</label>
                                    <input type="checkbox" id="scIsHighLight">
                                    <label for="scIsHighLight">　ピックアップ</label>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>

                <p class="center">
                    <input type="hidden" id="possiton" />
                    <input type="submit" name="submit_ud" id="submit_ud" value="登録"
                        style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
                        style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>

        <input type="hidden" id="scId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="oldPdf">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>
