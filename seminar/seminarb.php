<?php
    if (!session_id()) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if (empty($_SESSION['userSM'])) {
        new Redirect('login.php');
    }

    $title   = "カリスマ税理士による税務相談会";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_seminarb.js"></script>
                    <script type="text/javascript" src="assets/js/ready/R_seminarb.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";
    $optionAreas = "";

    // Areas
    $Conn = new Database;
    $stmt = $Conn->conn->prepare("SELECT AreaId, AreaName FROM infoseminar_areas ORDER BY DisplayNo");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($result != false && count($result) > 0) {
        foreach ($result as $area) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars($area["AreaName"]) . "</option>";
        }
    }

    // Sample
    $stmt = $Conn->conn->prepare("SELECT SampleName FROM infoseminar_sample WHERE SampleId = 2");
    $stmt->execute();
    $samplename = $stmt->fetch(PDO::FETCH_ASSOC)["SampleName"];

    // Check the current month will be allowed to register or not
    $stmt = $Conn->conn->prepare("SELECT SampleAppMonth, SampleDeadline FROM infoseminar_sample WHERE TypesId = 2");
    $stmt->execute();
    $result          = $stmt->fetch(PDO::FETCH_ASSOC);
    $now             = new \DateTime('now');
    $days            = $result["SampleDeadline"];

    $date = date_create();
    date_add( $date, date_interval_create_from_date_string( (date("d") - $days) . " day" ) );
    $date      = $date->format("Y-n-j");
    list( $dateline_y, $dateline_d, $dateline_m ) = explode( '-', $date );
    $sampleMonth     = explode(',', $result['SampleAppMonth']);
    $chkCurrentMonth = in_array($now->format('n'), $sampleMonth) ? true : false;
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <img id="scLoading" style="position: absolute;right:42%;top:50%;z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0" style="width:350px"> <span>カリスマ税理士による税務相談会</span> </p><?php
                if ($chkCurrentMonth == true) : ?>
                    <p class="fRight right pb0">
                        <input style="display: none;" type="file" id="ipExcel" name="ipExcel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="$('#inputExcel').html($(this).val());" style="display: none">
                        <button onclick="document.getElementById('ipExcel').click();" class="btn" style="margin-left:3px;margin-right:2px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;">ファイルを選択</button>
                        <button style="padding-left:2px;width:60px; margin-right: 3px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;" name="btnImport" id="btnImport" class="btn">取込</button>
                        <button name="btnDeleteAll" id="btnDeleteAll" class="btn" style="padding-left:2px; width:80px; margin-right:3px;" onclick="checkExistAllSeminarB()">一括削除</button>
                        <button style="padding:5px;width:60px;" name="btnCreate" class="fancybox7 btn btnAdd" onclick="openDialog(this.id,false);" href="#inline0">追加</button>
                    </p>
                    <p class="clear center pb0" style="width:100%;">
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
            <h2 class="h2Title"><span> カリスマ税理士による税務相談会</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <form action="" accept-charset="utf-8">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align: left;vertical-align: middle;width:20%;">
                                    セミナー名 <span class="red_import">(必須)</span>
                                </td>
                                <td style="position: relative; width:30%;">
                                    <input type="text" id="SeminarName" maxlength="50" style="width:93%;">
                                </td>
                                <td style="text-align: left;vertical-align: middle;width:20%;">
                                    地域 <span class="red_import">(必須)</span>
                                </td>
                                <td>
                                    <select style="width:93%;" id="areaId">
                                        <?= $optionAreas; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;vertical-align: middle;width:20%;">
                                    開催会場名 <span class="red_import">(必須)</span>
                                </td>
                                <td style="position: relative;">
                                    <input type="text" id="VenueName" maxlength="1000" style="width:93%;">
                                </td>
                                <td style="text-align: left;vertical-align: middle;">
                                    開催会場住所 <span class="red_import">(必須)</span>
                                </td>
                                <td>
                                    <input type="text" id="VenueAddress" maxlength="1000" style="width:93%;">
                                </td>

                            </tr>
                            <tr>
                                <td style="text-align: left;vertical-align: middle;">
                                    開催日 <span class="red_import">(必須)</span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <input type="text" id="scDate" style="width:93%;" placeholder="<?= date('Y-n-j') ?>">
                                </td>
                                <td style="text-align: left;vertical-align: middle;">
                                    時間帯 <span class="red_import">(必須)</span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <p id="datepair" style="vertical-align: middle;">
                                        <input type="text" id="TimeStart" class="time start" style="width:41%">
                                        ~
                                        <input type="text" id="TimeEnd" class="time end" style="width:42%">
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: left;vertical-align: middle;">
                                    地図(URL)
                                </td>
                                <td colspan="">
                                    <input type="text" id="VenueMap" maxlength="1000" style="width:93%;">
                                </td>

                                <td style="text-align: left;vertical-align: middle;">
                                    申込期限 <span class="red_import">(必須)</span>
                                </td>
                                <td style="vertical-align: middle;">
                                    <p id="" style="vertical-align: middle;">
                                        <input type="text" id="year" value="<?= $dateline_y; ?>" style="width:30%" placeholder="<?= $dateline_y; ?>">年
                                        <input type="text" id="month" value="<?= $dateline_m; ?>" style="width:20%" placeholder="<?= $dateline_m; ?>">月
                                        <input type="text" id="day" value="<?= $dateline_d; ?>" style="width:20%" placeholder="<?= $dateline_d; ?>">日
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <p class="center">
                    <input type="hidden" id="possiton" />
                    <input type="submit" name="submit_se" onclick="saveSeminarB();" id="submit_se" value="登録"
                        style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()"
                        style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
        <input type="hidden" id="seminarId">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="FormLink">
        <input type="hidden" id="AppDate">
        <input type="hidden" id="TypesId">
        <input type="hidden" id="SampleId">
        <input type="hidden" id="Full">
        <input type="hidden" name="dateHide" id="dateHide" value="<?=$days;?>">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php";?>