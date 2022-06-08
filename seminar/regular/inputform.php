<?php
    require_once __DIR__ . "/../view/template/header/regular.php";
    // $SeminarId = (isset($_GET["id"])) ? @$_GET["id"] : @$_POST["SeminarId"];
    $SeminarId = $_GET['id'] ?? $_POST['SeminarId'] ?? '';
    // echo $SeminarId;
?>

<div align="center">
    <br>
    <form method="post" action="confirmation.php" name="inputform" id="inputform">
        <table border="0" cellspacing="0" width="600">
            <tr>
                <td align="center">
                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td><img src="images/i_seminer.gif"></td>
                            <td align="right"><img src="images/pagetitle_inputform.gif">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="smnlist showSeminar" width="600"></table>
                </td>
            </tr>
            <tr>
                <td nowrap height="20px"></td>
            </tr>
            <tr>
                <td>
                    <table class="smnlist" width="600">
                        <tr>
                            <th nowrap colspan="2" class="smnmida2">お客さまの情報を入力してください
                </td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">貴社名</th>
                <td class="smnlist2hissu"><input type="text" name="user_company" style="width:400px;"
                        value="<?= @$_POST['user_company'] ?>"><br>
                    <font class="error_blue" id="id_UserCompany"></font>
                </td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">部署名</th>
                <td class="smnlist2 tab"><input type="text" name="user_section" style="width:400px;"
                        value="<?= @$_POST["user_section"] ?>"></td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">受講者氏名</th>
                <td class="smnlist2hissu"><input type="text" name="user_name" style="width:300px;"
                        value="<?= @$_POST["user_name"] ?>"><br>
                    <font class="error_blue" id="id_UserName"></font>
                </td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">メールアドレス</th>
                <td class="smnlist2 tab"><input type="text" name="user_email" style="width:300px; ime-mode:disabled;"
                        value="<?= @$_POST["user_email"] ?>"><br>
                    <font class="error_blue" id="id_UserEmail"></font>
                    </th>
            </tr>
            <tr>
                <th nowrap class="smnlist2">住所</th>
                <td class="smnlist2hissu">
                    <div style="margin-bottom:5px;">
                        〒<input type="text" name="user_postcode1" style="width:45px; ime-mode:disabled;" maxlength="3"
                            value="<?= @$_POST["user_postcode1"] ?>" onkeydown="isNumber(event)">-<input type="text"
                            name="user_postcode2" style="width:60px; ime-mode:disabled;" maxlength="4"
                            value="<?= @$_POST["user_postcode2"] ?>" onkeydown="isNumber(event)">
                    </div>
                    <input type="text" name="user_address" style="width:400px;"
                        value="<?= @$_POST["user_address"] ?>"><br>
                    <font class="error_blue" id="id_UserAddress"></font>
                </td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">TEL</th>
                <td class="smnlist2hissu"><input type="text" name="user_tel" style="width:150px; ime-mode:disabled;"
                        maxlength="15" value="<?= @$_POST["user_tel"] ?>" onkeydown="serialFormat(event)"><br>
                    <font class="error_blue" id="id_UserTel"></font>
                    </th>
            </tr>
            <tr>
                <th nowrap class="smnlist2">FAX</th>
                <td class="smnlist2 tab"><input type="text" name="user_fax" style="width:150px; ime-mode:disabled;"
                        maxlength="15" value="<?= @$_POST["user_fax"] ?>" onkeydown="serialFormat(event)"></th>
            </tr>
            <tr>
                <th nowrap class="smnlist2">所有製品の<br>シリアルNo.</th>
                <td class="smnlist2hissu"><input type="text" name="user_serialno"
                        style="width:200px; ime-mode:disabled;" maxlength="19" value="<?= @$_POST["user_serialno"] ?>"
                        onkeydown="serialFormat(event)"><br>
                    <font class="error_blue" id="id_UserSerialNo"></font>
                    </th>
            </tr>
            <tr>
                <th nowrap colspan="2" class="smnmida2">お申し込みに関する注意事項</th>
            </tr>
            <tr>
                <td colspan="2" class="smncaution">
                    <div style="margin:4px 0;">
                        <font color="#FF3300">以下の内容を必ずご確認のうえ、お申し込みください。</font>
                    </div>
                    <div class="id1_2" style="margin-bottom:4px;">1. セミナーのお申し込みは、開催日の3営業日前までにソリマチパートナー事務局へお願いいたします。
                    </div>
                    <div class="id1_2" style="margin-bottom:4px;">2. お申し込み後、セミナー教室より受付のご連絡をいたします。</div>
                    <div class="id1_2" style="margin-bottom:4px;">3. セミナー料金のお支払方法については、お申し込みのセミナー教室へお問い合わせください。
                    </div>
                    <div class="id1_2" style="margin-bottom:4px;">4. 領収書の受取人は受講されたセミナー教室の名義となります。</div>
                    <div class="id1_2" style="margin-bottom:4px;">5. セミナーの内容などの詳細に関しましては、セミナー教室にお問い合わせください。</div>
                </td>
            </tr>
        </table>
        </td>
        </tr>
        <tr>
            <td align="center" height="60"><input type="button" value="確認画面へ進む" style="padding:10px 40px;"
                    onClick="javascript:checkForm(inputform);"></td>
        </tr>
        <tr>
            <td align="center" style="height:80px; font:normal 90%/150% 'メイリオ',Meiryo,sans-serif">FAXでのお申し込み方法については
                [ <a href="seminar_moushikomi.html" target="_blank"><b>こちらのページ</b></a> ] をご覧ください。</td>
        </tr>
        </table>
        <input type="hidden" name="SeminarId" id="SeminarId" value="<?= $SeminarId ?>">
        <input type="hidden" name="isGet" id="isGet" value="<?= isset( $_GET['id'] ) ? 1 : 0 ?>">
    </form>
</div>

<script type="text/javascript">
showAttendSeminarA($("#SeminarId").val());
</script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>