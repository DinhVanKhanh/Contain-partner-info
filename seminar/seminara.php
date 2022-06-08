<?php
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userSM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "ソリマチ製品 使い方セミナー";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_seminara.js"></script>
                <script type="text/javascript" src="assets/js/ready/R_seminara.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";

    $Conn = new Database;
    $optionAreas = $optionSeminars = "";
    $optionCourses = $optionCompanys = $optionProducts = "";

    // Areas
    $stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infoseminar_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }

    // Seminars
    $stmt = $Conn->conn->prepare( "SELECT SeminarId, SeminarName FROM infoseminar_sumary ORDER BY Date" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $seminar ) {
            $optionSeminars .= "<option value='" . $seminar["SeminarId"] . "'>" . htmlspecialchars( $seminar["SeminarName"] ) . "</option>";
        }
    }

    // Courses
    $stmt = $Conn->conn->prepare( "SELECT ItemId, ItemName FROM infoseminar_items WHERE `Type` = 3" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $course ) {
            $optionCourses .= "<option value='" . $course["ItemId"] . "'>" . htmlspecialchars( $course["ItemName"] ) . "</option>";
        }
    }

    // Companys
    $stmt = $Conn->conn->prepare( "SELECT ItemId, ItemName FROM infoseminar_items WHERE `Type` = 1" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $company ) {
            $optionCompanys .= "<option value='" . $company["ItemId"] . "'>" . htmlspecialchars( $company["ItemName"] ) . "</option>";
        }
    }

    // Products
    $stmt = $Conn->conn->prepare( "SELECT ItemId, ItemName FROM infoseminar_items WHERE `Type` = 2" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $product ) {
            $optionProducts .= "<option value='" . $product["ItemId"] . "'>" . htmlspecialchars( $product["ItemName"] ) . "</option>";
        }
    }

    // Check the current month will be allowed to register or not
    $stmt = $Conn->conn->prepare( "SELECT SampleAppMonth, SampleTaxChk FROM infoseminar_sample WHERE TypesId = 1" );
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $now = new \DateTime('now');
    $sampleMonth = explode( ',', $result['SampleAppMonth'] );
    $chkCurrentMonth = in_array( $now->format('n'), $sampleMonth ) ? true : false;
?>

<article id="main" class="clearfix" >
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <img id="scLoading" style="position: absolute;right:42%;top:50%;z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content" >
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0" style="width:350px"> <span>ソリマチ製品 使い方セミナー</span> </p><?php
                if ($chkCurrentMonth) : ?>
                    <p class="fRight right pb0">
                        <input style="display: none;" type="file" id="ipExcel" name="ipExcel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="$('#inputExcel').html($(this).val());" style="display: none">
                        <button onclick="document.getElementById('ipExcel').click();" class="btn" style="margin-left:3px;margin-right:2px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;">ファイルを選択</button>
                        <button style="padding-left:2px;width:60px; margin-right: 3px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;" name="btnImport" id="btnImport" class="btn">取込</button>
                        <button name="btnDeleteAll" id="btnDeleteAll" class="btn" style="padding-left:2px;width:80px; margin-right: 3px;" onclick="checkExistAllSeminarA()">一括削除</button>
                        <button style="padding:5px;width:60px;" name="btnCreate" class="fancy_h620 btn btnAdd" onclick="openDialog(this.id,false);" href="#inline0">追加</button>
                    </p>
                    <p class="center clear pb0" style="width:100%;">
                        <label id="inputExcel" style="margin-right:4px; margin-top:10px; margin:0 auto; color:white"></label>
                    </p><?php
                endif; ?>
            </div>

            <!-- Content -->
            <div id="tableContent"></div>
        </div>
        
        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message">セミナーを削除してよろしいでしょうか？</p>
                <p class="btnCenter"></p>
            </div>
        </div>

        <!-- DIALOG Create new shedule -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>ソリマチ製品 使い方セミナー</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent"><!-- dialog content -->
                    <form action="" accept-charset="utf-8">
                        <table class="tbl_fixed">
                            <tr>
                                <td class="search_col1" style="width: 23%">セミナー名 <span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:93%;" maxlength="50" id="SeminarName"></td>
                                <td class="search_col1">地域 <span style="color:red">(必須)</span></td>
                                <td>
                                    <select style="width:93%;" id="seArea">
                                        <?= $optionAreas ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="search_col1">開催会場名 <span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:93%;" maxlength="1000" id="VenueName"></td>
                                <td class="search_col1">開催会場住所 <span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:93%;" maxlength="1000" id="VenueAddress"></td>
                            </tr>
                            <tr>
                                <td class="search_col1">開催日 <span style="color:red">(必須)</span></td>
                                <td><input type="text" id="scDate" placeholder="<?= date('Y-m-d') ?>" style="width:93%;"></td>
                                <td class="search_col1">地図(URL)</td>
                                <td><input type="text" style="width:93%;" maxlength="1000" id="VenueMap"></td>
                            </tr>
                            <tr>
                                <td class="search_col1">時間帯 <span style="color:red">(必須)</span></td>
                                <td><p id="datepair" style="vertical-align: middle;">
                                    <input type="text" id="TimeStart"  class="time start" style="width:41%">
                                    ~
                                    <input type="text" id="TimeEnd"  class="time end" style="width:41%">
                                </p></td>
                                <td class="search_col1">受講料（<span class="searchTax">
                                    <?= ($result["SampleTaxChk"] == 1) ? '税込' : '税抜き'; ?>
                                    </span>）<span style="color:red">(必須)</span></td>
                                <td><input type="text" id="SeminarFees" style="width:93%" maxlength="20"></td>
                            </tr>
                            <tr>
                                <td class="search_col1">Tel</td>
                                <td><input type="text" id="ContactTel" maxlength="20" style="width:93%"></td>
                                <td class="search_col1">Fax</td>
                                <td><input type="text" id="ContactFax" maxlength="20" style="width:93%"></td>
                            </tr>
                            <tr>
                                <td class="search_col1">最寄駅 <span style="color:red">(必須)</span></td>
                                <td><input type="text" id="VenueStation" maxlength="500" style="width:93%"></td>
                                <td class="search_col1 w20">席数 <span style="color:red">(必須)</span></td>
                                <td><input type="text" id="CountPerson" maxlength="10" style="width:93%"></td>
                            </tr>
                            <tr>
                                <td class="search_col1">ソリマチ／他社<span style="color:red">(必須)</span></td>
                                <td>
                                    <select style="width:93%;" id="seCompany">
                                        <?= $optionCompanys ?>
                                    </select>
                                </td>
                                <td class="search_col1">コース <span style="color:red">(必須)</span></td>
                                <td><select style="width:93%;" id="seCouse">
                                        <?= $optionCourses ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <td class="search_col1">対象製品 <span style="color:red">(必須)</span></td>
                                <td>
                                    <select style="width:93%;" id="seProduct">
                                        <?= $optionProducts ?>
                                    </select>
                                </td>
                                <td class="search_col1" style="width: 20%"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="search_col1">備考</td>
                                <td colspan=3><textarea name="note" id="note" class="classy-editor" maxlength="500" rows="3" cols="38" style="width:100%;"></textarea>
                            </tr>
                        </table>
                    </form>
                </div>
                <p class="center">
                    <input type="hidden" id="possiton"/>
                    <input type="submit" name="submit_se" id="submit_se" onclick="saveSeminarA();" value="登録" style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>

        <input type="hidden" id="SeminarId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="FormLink">
        <input type="hidden" id="AppDate">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>