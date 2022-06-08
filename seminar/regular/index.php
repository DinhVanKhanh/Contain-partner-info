<?php
    $title   = "ソリマチ製品 使い方セミナー";
    $scripts = '<script type="text/javascript" src="../assets/js/proccess/P_regular.js"></script>';
    $linkCss = '';
    require_once __DIR__ . "/../view/template/header/client.php";

    $optionProducts = $optionAreas = "";
    
    // Products
    $Conn = new Database;
    $stmt = $Conn->conn->prepare( "SELECT ItemId, ItemName FROM infoseminar_items WHERE `Type` = 2" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $product ) {
            $optionProducts .= "<option value='" . $product["ItemId"] . "'>" . htmlspecialchars( $product["ItemName"] ) . "</option>";
        }
    }

    // Areas
    $stmt = $Conn->conn->prepare( "SELECT AreaId, AreaName FROM infoseminar_areas ORDER BY DisplayNo" );
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ( $result != false && count( $result ) > 0 ) {
        foreach ( $result as $area ) {
            $optionAreas .= "<option value='" . $area["AreaId"] . "'>" . htmlspecialchars( $area["AreaName"] ) . "</option>";
        }
    }
?>
<article id="main" class="clearfix">
    <img id="scLoading"
        style="position: absolute;right:550px;top:500px;z-index:9999; display:none; background-color:#333; padding:2%;"
        src="../assets/images/icon_loading.gif" />
    <section id="main_content">
        <h1 class="title-seminar">ソリマチ製品 使い方セミナー</h1>
        <p class="ttl-des">受講したいセミナーと地域を指定してください。</p>
        <form action="" method="post">
            <table class="frm-search">
                <tbody>
                    <tr bgcolor="E8E8E8">
                        <td class="title">受講希望セミナー</td>
                        <td style="padding:3px;">
                            <select name="seProduct" id="seProduct"
                                onchange="loadSemianrAClientList(this.value,document.getElementById('seArea').value);"
                                style="font-family:'メイリオ',Meiryo,sans-serif;">
                                <?= $optionProducts ?>
                            </select></td>
                    </tr>
                    <tr bgcolor="F0F0F0">
                        <td class="title">受講地域</td>
                        <td style="padding:3px;">
                            <select name="seArea" id="seArea"
                                onchange="loadSemianrAClientList(document.getElementById('seProduct').value,this.value);"
                                style="font-family:'メイリオ',Meiryo,sans-serif;">
                                <option value="-1" selected="true">選択してください</option>
                                <?= $optionAreas ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <h2 class="bHead">会計王・みんなの青色申告セミナー</h2>
        <p class="bHead-des">※セミナーのお申込みは、開催日の３営業日前までにお願いいたします。<br>
            ※最低催行人数に満たない場合は、やむを得ず中止させていただく場合がありますのでご了承ください。</p>
        <div class="table_client">
            <div id="tableContent" class="tableContentC"></div>
            <p class="registerByFax">FAXでのお申し込み方法については [ <a target="_blank"
                    href="http://www.sorimachi.co.jp/usersupport/seminar/seminar_moushikomi.html"><b>こちらのページ</b></a> ]
                をご覧ください。</p>
            <table class="tblContact" style="border-collapse:collapse;">
                <tbody>
                    <tr>
                        <td nowrap="" class="center"
                            style="padding:5px 15px; background-color:#F0F0F0; border:1px #888888 solid; font-size: 14px; font-family: 'ＭＳ Ｐゴシック',Osaka,sans-serif">
                            <b>お問い合わせ先</b></td>
                    </tr>
                    <tr>
                        <td nowrap="" style="padding:10px 15px; border:1px #888888 solid;">
                            <b>ソリマチ株式会社&#12288;ソリマチパートナー事務局</b><br>
                            〒141-0022<br>
                            東京都品川区東五反田 3-18-6 ソリマチ第８ビル<br>
                            TEL：03-3446-1311<br>
                            FAX：03-5475-5339<br>
                            e-mail：<a href="mailto:seminar@mail.sorimachi.co.jp">seminar@mail.sorimachi.co.jp</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="buttonLink">
                <button onclick="location.href='http://www.sorimachi.co.jp/products_gyou/demo_seminar/'"
                    target="_blank">ソリマチ製品 使い方セミナー トップページへ戻る</button>
            </p>
        </div>
    </section>
</article>

<script type="text/javascript">
    $(document).ready(function() {
        loadSemianrAClientList( $('#seProduct').val(), $('#seArea').val() );
    });
</script>

<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>