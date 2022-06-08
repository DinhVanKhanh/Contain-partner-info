<?php
if (!session_id()) {
    session_start();
}
require_once __DIR__ . "/libs/redirect.class.php";

use Redirect\Redirect as Redirect;

if (empty($_SESSION['userSM'])) {
    new Redirect('login.php');
}

$title   = "給料王 年末調整セミナー";
$scripts = '<script type="text/javascript" src="assets/js/jquery.form.js"></script>
                <script type="text/javascript" src="assets/js/proccess/P_seminard.js"></script>
                <script type="text/javascript" src="assets/js/ready/R_seminard.js"></script>';
$linkCss = '';
require_once __DIR__ . "/view/template/header/normal.php";

$Conn = new Database;
$optionTodous = "";

// 地域 -> 都道府県
$stmt = $Conn->conn->prepare("SELECT * FROM infoseminar_todouhukens ORDER BY TodouhukenCode");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($result != false && count($result) > 0) {
    foreach ($result as $todou) {
        $optionTodous .= "<option value='" . $todou["TodouhukenId"] . "'>" . htmlspecialchars($todou["TodouhukenDisplay"]) . "</option>";
    }
}

// Check the current month will be allowed to register or not
$stmt = $Conn->conn->prepare("SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 4");
$stmt->execute();
$result = explode(',', $stmt->fetch(PDO::FETCH_ASSOC)['SampleAppMonth']);
$now = new \DateTime('now');
$chkCurrentMonth = in_array($now->format('n'), $result) ? true : false;

//↓↓　<2020/10/30> <YenNhi> <fix select seminar D app_deadline>
//$stmt = $Conn->conn->prepare( "SELECT SampleDeadline FROM infoseminar_sample WHERE SampleId = 3" );
$stmt = $Conn->conn->prepare("SELECT SampleName, SampleDeadline FROM infoseminar_sample WHERE TypesId = 4");
//↑↑　<2020/10/30> <YenNhi> <fix select seminar D app_deadline>    
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC)['SampleDeadline'];
$today = date("Y-n-j");
$date = date("Y-n-j", strtotime("$today - $result day"));
list($year, $month, $day) = explode('-', $date);
?>

<article id="main" class="clearfix">
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php"; ?>
    </aside>

    <img id="scLoading" style="position: absolute;right:42%;top:50%;z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content">
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0  float_none"> <span>給料王 年末調整セミナー</span> </p><?php
                                                                                        if ($chkCurrentMonth == true) : ?>
                    <div class="right">
                        <div class="upload_div">
                            <form method="post" name="multiple_upload_form" id="multiple_upload_form" enctype="multipart/form-data" action="src/index.php">
                                <p class="upload btn"><span>PDF一式アップロード</span>
                                    <input type="hidden" name="image_form_submit" value="1" />
                                    <input type="hidden" name="controller" value="seminard" />
                                    <input type="hidden" name="action" value="uploadMultiPdf" />
                                    <input type="file" name="pdf[]" id="pdf" multiple style="width:150px">
                                </p>
                            </form>
                        </div>
                        <p class="pb0 float">
                            <input style="display: none;" type="file" id="ipExcel" name="ipExcel" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="$('#inputExcel').html($(this).val());" style="display: none">
                            <button onclick="document.getElementById('ipExcel').click();" class="btn" style="margin-left:3px;margin-right:2px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;">EXCELファイルを選択</button>
                            <button style="padding-left:2px;width:60px; margin-right: 3px;background-image: linear-gradient(to bottom, #f9a03a, #fd660d);color:white;" name="btnImport" id="btnImport" class="btn">取込</button>

                            <button name="btnDeleteAll" id="btnDeleteAll" class="btn" style="padding-left:2px;width:80px; margin-right: 3px;" onclick="checkExistAllSeminarD()">一括削除</button>
                            <button style="padding:5px;width:60px;" name="btnCreate" class="fancy_h620 btn btnAdd" onclick="openDialog(this.id,false);" href="#inline0">追加</button>
                        </p>
                    </div>
                    <div id="images_preview" class="clear center"></div>

                    <p class="center clear pb0" style="width:100%;">
                        <label id="inputExcel" style="margin-right:4px; margin-top:10px; margin:0 auto; color:#fff;"></label>
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
        <!-- end Confirm dialog -->

        <!-- DIALOG Create new shedule -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>給料王 年末調整セミナー</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <!-- dialog content -->
                    <form action="" accept-charset="utf-8">
                        <table class="tbl_fixed">
                            <tr>
                                <td class="search_col1" style="width: 23%">セミナー名 <span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:93%;" maxlength="50" id="SeminarName"></td>
                                <td class="search_col1">都道府県 <span style="color:red">(必須)</span></td>
                                <td>
                                    <select style="width:93%;" id="seArea">
                                        <?= $optionTodous; ?>
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
                                <td><input type="text" id="scDate" placeholder="<?= $today ?>" style="width:93%;"></td>

                                <td style="text-align: left;vertical-align: middle;">
                                    申込期限 <span class="red_import">(必須)</span>
                                </td>
                                <td style="vertical-align: middle;" class="pb0">
                                    <p id="" style="vertical-align: middle;">
                                        <input type="text" id="year" value="<?= $year; ?>" style="width:30%" placeholder="<?= $year; ?>">年
                                        <input type="text" id="month" value="<?= $month; ?>" style="width:20%" placeholder="<?= $month; ?>">月
                                        <input type="text" id="day" value="<?= $day; ?>" style="width:20%" placeholder="<?= $day; ?>">日
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="search_col1">スクール <span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:93%;" maxlength="50" id="CompanyName"></td>
                                <td class="search_col1">時間帯 <span style="color:red">(必須)</span></td>
                                <td>
                                    <p id="datepair" style="vertical-align: middle;">
                                        <input type="text" id="TimeStart" class="time start" style="width:41%">
                                        ~
                                        <input type="text" id="TimeEnd" class="time end" style="width:41%">
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="search_col1">Tel</td>
                                <td><input type="text" id="ContactTel" maxlength="20" style="width:93%"></td>
                                <td class="search_col1">Fax</td>
                                <td><input type="text" id="ContactFax" maxlength="20" style="width:93%"></td>
                            </tr>
                            <tr>
                                <td>席数 <span style="color:red">(必須)</span></td>
                                <td><input type="text" id="CountPerson" maxlength="10" style="width:93%"></td>
                                <td><span id="pdfLabel">PDF</span>
                                    <p id="dlgPdf" style="height: 0px; position: relative; top: 0px;right:-8px;">
                                        <a href="javascript:;" onclick="$('#inputPdf').val(''); $('#deletePdf').val(1);"><img width="26px;" height="33px;" src="assets/images/icon_pdf.gif">

                                            <span style="color:red;cursor: pointer;" id="btnPdfDel" onclick="$('#inputPdf').val('');">削除</span></a>

                                    </p>
                                </td>
                                <td>
                                    <div style="display: flex; justify-content: space-between;">
                                        <input type="text" style="width:78%;" id="inputPdf" />
                                        <input type="file" id="ipPdf" name="ipPdf" onchange="$('#inputPdf').val($(this)[0].files[0].name); $('#deletePdf').val(0);" style="display: none" accept="application/pdf">
                                        <input type="button" style="padding:4px;" name="btnImport" onclick="document.getElementById('ipPdf').click();" class="btn btnAdd" value="参照">
                                    </div>
                                </td>
                            </tr>
                            <!-- ↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826> -->
                            <tr>
                                <td>価格1（一般・税込）</td>
                                <td><input type="text" id="SeminarFees" maxlength="10" style="width:93%"></td>
                                <td>価格2（会員・税込）</td>
                                <td><input type="text" id="SeminarFees2Member" maxlength="10" style="width:93%"></td>
                            </tr>
                            <tr>
                                <td>セミナー形式 <span style="color:red">(必須)</span></td>
                                <td>
                                    <div style="display: flex;justify-content:space-between;">
                                        <div>
                                            <input type="radio" id="SeminarTypeOffline" name="SeminarType" value="対面" checked>
                                            <label for="SeminarTypeOffline">対面</label><br>
                                        </div>
                                        <div>
                                            <input type="radio" id="SeminarTypeOnline" name="SeminarType" value="オンライン">
                                            <label for="SeminarTypeOnline">オンライン</label><br>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>主催者URL</td>
                                <td colspan=3><input type="text" id="OrganizerURL" style="width: 100%;"></td>
                            </tr>

                            <!-- ↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826> -->
                            <tr>
                                <td>備考</td>
                                <td colspan=3><textarea name="note" id="note" class="classy-editor" maxlength="500" rows="3" cols="38" style="width:100%;"></textarea>
                            </tr>

                        </table>
                    </form>
                </div>
                <p class="center">
                    <input type="hidden" id="possiton" />
                    <input type="submit" name="submit_se" onclick="saveSeminarD();" id="submit_se" value="登録" style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
        <input type="hidden" id="SeminarId">
        <input type="hidden" id="AppDate">
        <input type="hidden" id="isEdit">
        <input type="hidden" id="deletePdf">
        <input type="hidden" id="oldPdf">
        <input type="hidden" name="dateHide" id="dateHide" value="<?= $result; ?>">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>