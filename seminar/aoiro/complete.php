<?php
    $SeminarId = @$_POST["SeminarId"];
    require_once __DIR__ . "/../libs/redirect.class.php";
    use Redirect\Redirect as Redirect;

    if ( empty( $SeminarId ) ) {
        new Redirect( '../aoiro' );
    }
    require_once __DIR__ . "/../view/template/header/aoiro.php";
?>

<body>
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
                                <th nowrap class="smnlist2">会社名</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_company"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">担当者名</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_name"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">ご住所</th>
                                <td class="smnlist2" colspan="3">
                                    〒<?= @$_POST["user_postcode1"] ?>-<?= @$_POST["user_postcode2"] ?>　<?= @$_POST["user_address"] ?>
                                    </th>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2" width="15%">TEL</th>
                                <td class="smnlist2" width="35%"><?= @$_POST["user_tel"] ?></th>
                                <th nowrap class="smnlist2" width="15%">FAX</th>
                                <td class="smnlist2" width="35%"><?= @$_POST["user_fax"] ?></th>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist2">E-Mail</th>
                                <td class="smnlist2" colspan="3"><?= @$_POST["user_email"] ?></td>
                            </tr>
                            <tr>
                                <th nowrap colspan="4" class="smnmida2">お申し込みに関する注意事項</th>
                            </tr>
                            <tr>
                                <td colspan="4" class="smncaution">
                                    <div class="id1_2" style="margin-bottom:4px;">※定員になり次第締切とさせていただきます。</div>
                                    <div class="id1_2" style="margin-bottom:4px;">
                                        ※最低開催人員に満たない場合は開催を中止する場合がございますのでご了承下さい。</div>
                                    <div class="id1_2" style="margin-bottom:4px;">
                                        ※受講料は開催スクールによって前入金制、もしくは当日会場にてお支払いいただきます。なお、領収書は各スクールが発行しますのでご了承下さい。</div>
                                    <div class="id1_2" style="margin-bottom:4px;">
                                        ※当ホームページに掲載している各セミナーの空席状況は、リアルタイムではないため、最新の情報ではない場合があります。くわしくは各スクールへ直接お問合せください。
                                    </div>
                                    <div class="id1_2" style="margin-bottom:4px;">
                                        ※当日のキャンセルはお受けできません。後日、請求させていただきますのでご了承ください。</div>
                                    <div class="id1_2" style="margin-bottom:4px;">※このセミナーは個人事業者の青色申告を対象としております。</div>
                                    <div class="id1_2" style="margin-bottom:4px;">※ご不明な点がございましたら、下記までお問い合わせください。</div>
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
        showAttendSeminarC( $("#SeminarId").val() );
    </script>
<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>