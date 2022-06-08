<?php
    require_once __DIR__ . "/../view/template/header/nenchou.php";
    $SeminarId = (isset($_GET["id"])) ? @$_GET["id"] : @$_POST["SeminarId"];
?>

    <div align="center">
        <br>
        <form method="post" action="../nenchou/confirmation.php" name="inputform" id="inputform">
            <table border="0" cellspacing="0" width="600">
                <tr>
                    <td align="center">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td><img src="images/n_seminer.gif"></td>
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
                    <th nowrap class="smnlist2">会社名</th>
                    <td class="smnlist2 tab"><input type="text" name="user_company" style="width:400px;"
                            value="<?= @$_POST['user_company'] ?>"></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">担当者名</th>
                    <td class="smnlist2hissu"><input type="text" name="user_name" style="width:400px;"
                            value="<?= @$_POST["user_name"] ?>"><BR>
                        <FONT class="error_blue" id="id_UserName"></FONT>
                    </td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">ご住所</th>
                    <td class="smnlist2hissu">
                        <div style="margin-bottom:5px;">
                            〒<input type="text" name="user_postcode1" style="width:45px; ime-mode:disabled;"
                                maxlength="3" value="<?= @$_POST["user_postcode1"] ?>"
                                onkeydown="isNumber(event)">-<input type="text" name="user_postcode2"
                                style="width:60px; ime-mode:disabled;" maxlength="4"
                                value="<?= @$_POST["user_postcode2"] ?>" onkeydown="isNumber(event)">
                        </div>
                        <input type="text" name="user_address" style="width:400px;"
                            value="<?= @$_POST["user_address"] ?>"><BR>
                        <FONT class="error_blue" id="id_UserAddress"></FONT>
                    </td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">TEL</th>
                    <td class="smnlist2hissu"><input type="text" name="user_tel" style="width:150px; ime-mode:disabled;"
                            maxlength="15" value="<?= @$_POST["user_tel"] ?>" onkeydown="serialFormat(event)"><BR>
                        <FONT class="error_blue" id="id_UserTel"></FONT>
                    </td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">FAX</th>
                    <td class="smnlist2 tab"><input type="text" name="user_fax" style="width:150px; ime-mode:disabled;"
                            maxlength="15" value="<?= @$_POST["user_fax"] ?>" onkeydown="serialFormat(event)"></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">E-mail</th>
                    <td class="smnlist2hissu"><input type="text" name="user_email"
                            style="width:400px; ime-mode:disabled;" value="<?= @$_POST["user_email"] ?>"><BR>
                        <FONT class="error_blue" id="id_UserEmail"></FONT>
                    </td>
                </tr>
                <tr>
                    <th nowrap colspan="2" class="smnmida2">お申し込みに関する注意事項</th>
                </tr>
                <tr>
                    <td colspan="2" class="smncaution">
                        <div style="margin:4px 0;">
                            <FONT color="#FF3300">以下の内容を必ずご確認のうえ、お申し込みください。</FONT>
                        </div>
                        <div class="id1_2" style="margin-bottom:4px;">※定員になり次第締切とさせていただきます。</div>
                        <div class="id1_2" style="margin-bottom:4px;">※最低開催人員に満たない場合は開催を中止する場合がございますのでご了承下さい。</div>
                        <div class="id1_2" style="margin-bottom:4px;">
                            ※当ホームページに掲載している各セミナーの空席状況は、リアルタイム更新ではないため、最新の情報ではない場合があります。くわしくは各スクールへ直接ご連絡ください。</div>
                        <div class="id1_2" style="margin-bottom:4px;">
                            ※受講料は開催スクールによって前入金制、もしくは当日会場にてお支払いいただきます。なお、領収書は各スクールが発行しますのでご了承下さい。</div>
                        <div class="id1_2" style="margin-bottom:4px;">※当日のキャンセルはお受けできません。後日、請求させていただきますのでご了承ください。</div>
                        <div class="id1_2" style="margin-bottom:4px;">※ご不明な点がございましたら、下記までお問い合わせください。</div>
                    </td>
                </tr>
            </table>
            </td>
            </tr>
            <tr>
                <td align="center" height="60"><input type="button" value="確認画面へ進む" style="padding:10px 40px;"
                        onClick="javascript:checkForm(inputform);"></td>
            </tr>
            </table>
            <input type="hidden" name="SeminarId" id="SeminarId" value="<?= $SeminarId ?>">
        </form>
    </div>

    <script type="text/javascript">
        showAttendSeminarD( $("#SeminarId").val() );
    </script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>