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
    $scripts = '<script type="text/javascript" src="assets/js/proccess/P_master.js"></script>' .
                '<script type="text/javascript" src="assets/js/ready/R_master.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";
?>

<article id="main" class="clearfix" >
    <aside id="sidebar">
        <?php require_once __DIR__ . "/view/template/sidebar/normal.php";?>
    </aside>

    <img id="scLoading" style="position:absolute; right:42%; top:50%; z-index:9999; display:none; background-color:#333; padding:2%;" src="assets/images/icon_loading.gif" />
    <section id="main_content" >
        <div class="boxStyle02">
            <div class="clearfix" style="text-align: justify;vertical-align: middle;">
                <p class="scheduleTitle pb0"> <span>セミナーマスター管理</span> </p>
            </div>

            <!-- Content -->
            <div id="tableContent" style="margin-top: 10px;"></div>
        </div>
        
        <!-- Confirm delete dialog -->
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
            <h2 class="h2Title"><span>セミナーマスター管理</span></h2>
            <img class="dialogLoading" src="assets/images/icon_loading.gif" />
            <div class="frmSection">
                <p class="error error_inline0 pb0">&nbsp;</p>
                <div class="show_parent">
                    <form action="" accept-charset="utf-8">
                        <table>
                            <tr>
                                <td style="width: 35%">セミナー名 <span style="color:red">(必須)</span></td>
                                <td colspan="3"><input type="text" style="width:95%;" maxlength="50" id="SampleName"></td>
                            </tr>
                            <tr>
                                <td>標準申込期限 <span style="color:red">(必須)</span></td>
                                <td style="width: 17%"><input type="text" style="width:100%;" maxlength="50" id="SampleDeadline"></td>
                                    <td class="vMiddle"><span class="span_date">日前</span></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>受講料 <span style="color:red">(必須)</span></td>
                                <td><input type="radio" name="SampleFeesChk" id="raNoFee" value="0">
                                    <label for="raNoFee">無料</label></td>
                                <td style="width: 12%"><input type="radio" name="SampleFeesChk" id="raHaveFee" value="1">
                                    <label for="raHaveFee">有料</label></td>
                                <td><input type="text" style="width:30%;" maxlength="500" id="SampleFees">
                                    <span class="span_note">※金額は定額の場合のみ入力</span></td>
                            </tr>
                            <tr>
                                <td>受講料消費税区分 <span style="color:red">(必須)</span></td>
                                <td><input type="radio" name="SampleTaxChk" id="raHaveTax" value="1">
                                    <label for="raHaveTax">税込</label></td>
                                <td><input type="radio" name="SampleTaxChk" id="raNoTax" value="0">
                                    <label for="raNoTax">税抜き</label></td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>担当者のメールアドレス</td>
                                <td colspan="3"><input type="text" style="width:95%;" maxlength="100" id="SampleEmail"></td>
                            </tr>
                            <tr>
                            	<td>開催時期 <span style="color:red">(必須)</span></td>
                            	<td colspan="3">
                                	<table class="tableMonth">
                                        <tr>
                                            <?php
                                                for ($i = 1; $i <= 4; $i++) {
                                                    echo '<td class="w25">
                                                                <label for="SampleAppMonth' . $i . '">' . $i . '月</label>
                                                                <input type="checkbox" name="SampleAppMonth" id="SampleAppMonth' . $i . '" value="' . $i . '" checked="true">
                                                            </td>';
                                                }
                                            ?>
                                        </tr>

                                        <tr>
                                            <?php
                                                for ($i = 5; $i <= 8; $i++) {
                                                    echo '<td class="w25">
                                                                <label for="SampleAppMonth' . $i . '">' . $i . '月</label>
                                                                <input type="checkbox" name="SampleAppMonth" id="SampleAppMonth' . $i . '" value="' . $i . '" checked="true">
                                                            </td>';
                                                }
                                            ?>
                                        </tr>

                                        <tr>
                                            <?php
                                                for ($i = 9; $i <= 12; $i++) {
                                                    echo '<td class="w25">
                                                                <label for="SampleAppMonth' . $i . '">' . $i . '月</label>
                                                                <input type="checkbox" name="SampleAppMonth" id="SampleAppMonth' . $i . '" value="' . $i . '" checked="true">
                                                            </td>';
                                                }
                                            ?>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <p class="center">
                    <input type="hidden" id="SampleId"/>
                    <input type="submit" name="submit_se" id="submit" onclick="saveMaster();" value="登録" style="width: 150px; font-size: 15px;" />
                    <a title="Close" class="btn btnClose" href="javascript:;" onclick="$.fancybox.close()" style="width: 150px; font-size: 15px;">キャンセル</a>
                </p>
            </div>
        </section>
    </section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>