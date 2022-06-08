<?php 
    if ( !session_id() ) {
        session_start();
    }
    require_once __DIR__ . "/libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $_SESSION['userSM'] ) ) {
        new Redirect( 'login.php' );
    }

    $title   = "セミナーシリアルNo";
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_serial.js"></script>' .
                '<script type="text/javascript" src="assets/js/ready/R_serial.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";

    $conn = new Database;
    $stmt = $conn->conn->prepare( "SELECT SampleId, SampleName FROM infoseminar_sample" );
    $stmt->execute();

    $seminarOption = "";
    if ( $stmt->rowCount() > 0 ) {
        foreach ( $stmt->fetchAll(PDO::FETCH_ASSOC) as $sample ) {
            $seminarOption .= "<option value='" . $sample['SampleId'] . "'>" . $sample['SampleName'] . "</option>";
        }
    }
?>

<article id="main" class="clearfix" >
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:50%; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content" >
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0"><span>セミナーシリアルNo</span></p>
                <p class="fRight right pb0">
                    <button style="padding:5px;width:60px;" name="btnCreate" class="fancybox4 btn btnAdd" onclick="openDialog(this.id, false);" href="#inline0">追加</button>
                </p>
                <p class="center clear pb0" style="width:100%; height:10px">
                    <label id="inputExcel" style="margin-right:4px; margin-top:10px; margin:0 auto;"></label>
                </p> 
            </div>

            <!-- Content -->
            <div id="tableContent"></div>
        </div>
        
        <!-- Confirm dialog -->
        <div class="fancyboxConfirm" id="confirmBox">
            <div class="cell-middle">
                <p class="message">セミナーを削除してよろしいでしょうか？</p>
                <p class="btnCenter">
                    <button id="submit_del_ok" class="btnDel btnDel_a" style='display:none'>OK</button>
                    <button name="btnDel_a" id="submit_del" class="btnDel btnDel_a">はい</button>
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()">いいえ</a>
                </p>
            </div>
        </div>
        
        <!-- DIALOG Create new shedule -->
        <section class="section fancyboxSection" id="inline0">
            <h2 class="h2Title"><span>セミナーシリアルNo</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <form action="" accept-charset="utf-8">
                        <table class="tbl_fixed">
                            <tr>
                                <td class="search_col1" style="width: 23%">セミナー名 <span style="color:red">(必須)</span></td>
                                <td>
                                    <select  style="width:60%;" id="SampleId">
                                        <?= $seminarOption ?>
                                    </select>
                                 </td>                                
                            </tr>
                            <tr>                                
                                <td class="search_col1">所有製品のシリアルNo.<span style="color:red">(必須)</span></td>
                                <td><input type="text" style="width:100%;" maxlength="500" id="SerialNumber" ></td>
                            </tr>
                            
                            <tr>
                                <td>備考</td>
                                <td>
                                    <textarea name="note" id="Note" class="classy-editor" maxlength="500" rows="3" cols="35" style="width:100%;"></textarea>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <p class="center">
                    <input type="hidden" id="possiton"/>
                    <input type="submit" name="submit_se" id="submit_se" onclick="saveSeminarSerial();" value="登録" style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
        <input type="hidden" id="SerialId">
        <input type="hidden" id="isEdit">
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>