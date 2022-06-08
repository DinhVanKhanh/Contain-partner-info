<?php
    $SeminarId = $_POST["SeminarId"] ?? '';
    require_once __DIR__ . "/../libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $SeminarId ) ) {
        new Redirect( '../regular' );
    }
    require_once __DIR__ . "/../view/template/header/regular.php";
?>

<img id="scLoading"
    style=" position: fixed; top: 45%; left: 45%; z-index:9999; display:none; background-color:#333; padding:2%;"
    src="../assets/images/icon_loading.gif" />
<div align="center">
    <br>
    <FORM method="post" action="../regular/complete.php" name="inputform">
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
                <th nowrap class="smnlist2">貴社名</th>
                <td class="smnlist2"><?= @$_POST["user_company"] ?></td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">部署名</th>
                <td class="smnlist2"><?= @$_POST["user_section"] ?></td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">受講者氏名</th>
                <td class="smnlist2"><?= @$_POST["user_name"] ?></td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">メールアドレス</th>
                <td class="smnlist2"><?= @$_POST["user_email"] ?></td>
            </tr>
            <tr>
                <th nowrap class="smnlist2">住所</th>
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
                <th nowrap class="smnlist2">所有製品の<br>シリアルNo.</th>
                <td class="smnlist2"><?= @$_POST["user_serialno"] ?></td>
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
            <td align="center" height="60"><input type="button" value="入力画面へ戻る" style="padding:5px 15px;"
                    onClick="document.inputform.action = '../regular/inputform.php'; document.inputform.submit();">　　<input
                    type="button" value="この内容で申し込む" style="padding:5px 15px;"
                    onClick="sendClientMailA('<?= $SeminarId ?>')"></td>
        </tr>
        <tr>
            <td align="center" style="height:80px; font:normal 90%/150% 'メイリオ',Meiryo,sans-serif">FAXでのお申し込み方法については
                [ <A href="seminar_moushikomi.html" target="_blank"><B>こちらのページ</B></A> ] をご覧ください。</td>
        </tr>
        </table>
        <input type="hidden" name="SeminarId" id="SeminarId" value="<?= $SeminarId ?>">
        <input type="hidden" name="user_company" value="<?= @$_POST["user_company"] ?>">
        <input type="hidden" name="user_section" value="<?= @$_POST["user_section"] ?>">
        <input type="hidden" name="user_name" value="<?= @$_POST["user_name"] ?>">
        <input type="hidden" name="user_email" value="<?= @$_POST["user_email"] ?>">
        <input type="hidden" name="user_postcode1" value="<?= @$_POST["user_postcode1"] ?>">
        <input type="hidden" name="user_postcode2" value="<?= @$_POST["user_postcode2"] ?>">
        <input type="hidden" name="user_address" value="<?= @$_POST["user_address"] ?>">
        <input type="hidden" name="user_tel" value="<?= @$_POST["user_tel"] ?>">
        <input type="hidden" name="user_fax" value="<?= @$_POST["user_fax"] ?>">
        <input type="hidden" name="user_serialno" value="<?= @$_POST["user_serialno"] ?>">
    </form>
</div>

<script type="text/javascript">
showAttendSeminarA($("#SeminarId").val());
</script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>