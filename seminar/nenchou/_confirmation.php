<?php
    $SeminarId = @$_POST["SeminarId"];
    require_once __DIR__ . "/../libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $SeminarId ) ) {
        new Redirect( '../nenchou' );
    }
    require_once __DIR__ . "/../view/template/header/nenchou.php";
?>

    <img id="scLoading"
        style=" position: fixed; top: 45%; left: 45%; z-index:9999; display:none; background-color:#333; padding:2%;"
        src="../assets/images/icon_loading.gif" />
    <div align="center">
        <br>
        <FORM method="post" action="../nenchou/complete.php" name="inputform">
            <TABLE border="0" cellspacing="0" width="600">
                <tr>
                    <td align="center">
                        <TABLE border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td><IMG src="images/i_seminer.gif"></td>
                                <td align="right"><IMG src="images/pagetitle_confirmation.gif"></td>
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
                                <th nowrap colspan="2" class="smnmida2">お客さまの情報についてご確認ください
                    </td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">会社名</th>
                    <td class="smnlist2"><?= @$_POST["user_company"] ?></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">担当者名</th>
                    <td class="smnlist2"><?= @$_POST["user_name"] ?></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">ご住所</th>
                    <td class="smnlist2">
                        〒<?= @$_POST["user_postcode1"] ?>-<?= @$_POST["user_postcode2"] ?><br>
                        <?= @$_POST["user_address"] ?></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">TEL</th>
                    <td class="smnlist2"><?= @$_POST["user_tel"] ?></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">FAX</th>
                    <td class="smnlist2"><?= @$_POST["user_fax"] ?></td>
                </tr>
                <tr>
                    <th nowrap class="smnlist2">E-Mail</th>
                    <td class="smnlist2"><?= @$_POST["user_email"] ?></td>
                </tr>
                <tr>
                    <th nowrap colspan="2" class="smnmida2">お申し込みに関する注意事項</th>
                </tr>
                <tr>
                    <td colspan="2" class="smncaution">
                        <div style="margin:4px 0;">
                            <font color="#FF3300">以下の内容を必ずご確認のうえ、お申し込みください。</FONT>
                        </div>
                        <div class="id1_2" style="margin-bottom:4px;">※定員になり次第締切とさせていただきます。</div>
                        <div class="id1_2" style="margin-bottom:4px;">※最低開催人員に満たない場合は開催を中止する場合がございますのでご了承下さい。</div>
                        <div class="id1_2" style="margin-bottom:4px;">
                            ※当ホームページに掲載している各セミナーの空席状況は、リアルタイムではないため、最新の情報ではない場合があります。くわしくは各スクールへ直接お問合せください。</div>
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
                <td align="center" height="60"><input type="button" value="入力画面へ戻る" style="padding:5px 15px;"
                        onClick="document.inputform.action = '../nenchou/inputform.php'; document.inputform.submit();">　　<input
                        type="button" value="この内容で申し込む" style="padding:5px 15px;"
                        onClick="sendClientMailD('<?= $SeminarId ?>')"></td>
            </tr>
            </table>
            <input type="hidden" name="SeminarId" value="<?= $SeminarId ?>">
            <input type="hidden" name="user_company" value="<?= @$_POST["user_company"] ?>">
            <input type="hidden" name="user_name" value="<?= @$_POST["user_name"] ?>">
            <input type="hidden" name="user_email" value="<?= @$_POST["user_email"] ?>">
            <input type="hidden" name="user_postcode1" value="<?= @$_POST["user_postcode1"] ?>">
            <input type="hidden" name="user_postcode2" value="<?= @$_POST["user_postcode2"] ?>">
            <input type="hidden" name="user_address" value="<?= @$_POST["user_address"] ?>">
            <input type="hidden" name="user_tel" value="<?= @$_POST["user_tel"] ?>">
            <input type="hidden" name="user_fax" value="<?= @$_POST["user_fax"] ?>">
        </form>
    </div>

    <script type="text/javascript">
        showAttendSeminarD('<?= $SeminarId ?>');
    </script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>