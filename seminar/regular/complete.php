<?php
    $SeminarId = @$_POST["SeminarId"];
    require_once __DIR__ . "/../libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $SeminarId ) ) {
        new Redirect( '../regular' );
    }
    require_once __DIR__ . "/../view/template/header/regular.php";
?>

    <div align="center">
        <form name="inputform">
            <table border="0" cellspacing="0" width="600">
                <tr>
                    <td align="center">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td align="center"><img src="images/pagetitle_complete.gif"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" class="screenonly"
                        style="height:40px; font:normal 90%/150% 'メイリオ',Meiryo,sans-serif">
                        <input type="button" name="print_b" value="このページを印刷する"
                            onClick="javascript:window.print();">　　<input type="button" name="close_b" value="ウインドウを閉じる"
                            onClick="javascript:window.close();">
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
                                <th nowrap colspan="4" class="smnmida2">お客さまの情報</th>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">貴社名</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_company"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">部署名</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_section"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">受講者氏名</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_name"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">メールアドレス</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_email"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">住所</th>
                                <td class="smnlist2" colspan="3">
                                    〒<?= @$_POST["user_postcode1"] ?>-<?= @$_POST["user_postcode2"] ?>　<?= @$_POST["user_address"] ?>
                                    </th>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2" width="15%">TEL</th>
                                <td class="smnlist2" width="35%"><?= @$_POST["user_tel"] ?></td>
                                <th nowrap class="smnlist2" width="15%">FAX</th>
                                <td class="smnlist2" width="35%"><?= @$_POST["user_fax"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">シリアルナンバー</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_serialno"] ?></th>
                            </tr>
                            <tr>
                                <th nowrap colspan="4" class="smnmida2">お申し込みに関する注意事項</th>
                            </tr>
                            <tr>
                                <td colspan="4" class="smncaution">
                                    <div class="id1_2" style="margin-bottom:2px;">1.
                                        セミナーのお申し込みは、開催日の3営業日前までにソリマチパートナー事務局へお願いいたします。</div>
                                    <div class="id1_2" style="margin-bottom:2px;">2. お申し込み後、セミナー教室より受付のご連絡をいたします。</div>
                                    <div class="id1_2" style="margin-bottom:2px;">3.
                                        セミナー料金のお支払方法については、お申し込みのセミナー教室へお問い合わせください。</div>
                                    <div class="id1_2" style="margin-bottom:2px;">4. 領収書の受取人は受講されたセミナー教室の名義となります。</div>
                                    <!--<div class="id1_2" style="margin-bottom:2px;">5. ソリマチ製品セミナー使用ソフトは「会計王15」「給料王15」「販売王15 販売・仕入・在庫」となります。</div>-->
                                    <div class="id1_2" style="margin-bottom:0px;">5.
                                        セミナーの内容などの詳細に関しましては、セミナー教室にお問い合わせください。</div>
                                </td>
                            </tr>
                            <tr>
                                <th nowrap colspan="4" class="smnmida2">お問い合わせ先</th>
                            </tr>
                            <tr>
                                <td colspan="4" class="smncaution" align="center">
                                    <B>ソリマチ株式会社　ソリマチパートナー事務局</B><br>
                                    〒141-0022　東京都品川区東五反田 3-18-6 ソリマチ第８ビル<br>
                                    TEL：03-3446-1311　　FAX：03-5475-5339<br>
                                    e-mail：<a
                                        href="mailto:seminar@mail.sorimachi.co.jp">seminar@mail.sorimachi.co.jp</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" class="screenonly"
                        style="height:60px; font:normal 90%/150% 'メイリオ',Meiryo,sans-serif">
                        <input type="button" name="print_b" value="このページを印刷する"
                            onClick="javascript:window.print();">　　<input type="button" name="close_b" value="ウインドウを閉じる"
                            onClick="javascript:window.close();">
                        <input type="hidden" name="SeminarId" id="SeminarId" value="<?= $SeminarId ?>">
                    </td>
                </tr>
            </table>
    </div>

    <script type="text/javascript">
        showAttendSeminarA( $("#SeminarId").val() );
    </script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>