<?php
    require_once __DIR__ . "/view/template/header/demo.php";
    require_once __DIR__ . "/libs/webserver_flg.class.php";
    global $SORIMACHI_HOME;
?>

<section id="content-area">
    <div id="headerSp">
        <div class="inner hide-pc">
            <h1 id="sp_logo">
                <a href="<?= $SORIMACHI_HOME ?>">
                    <img src="assets/images/logo_sp.jpg" alt=""/> <span>店頭デモカレンダー</span>
                </a>
            </h1>
            <p class="icon_menu">
                <label for="searchShow"><img src="assets/images/btn_menu.jpg" alt=""/></label>
            </p>
        </div>
        <div class="searchBlock">
            <ul id="tabArea"></ul>
            <table class="table-search">
                <tr>
                    <td><label for="searchTo">都市で探す</label>
                        <span class="se" id="toTag"></span></td>
                    <td><label for="searchDate">開催日で探す</label>
                        <input type="text" name="searchDate" id="searchDate" placeholder="" class="searchTxt" /></td>
                    <td><label for="searchAddress">開催場所で探す</label>
                        <input type="text" name="searchAddress" id="searchAddress" placeholder="" class="searchTxt" /></td>
                    <td><input type="submit" class="btn btnSearch" name="btnSearch" id="btnSearch" value="検索"></td>
                </tr>
            </table>
        </div>
    </div>
    <div id="tagTable" class="ctab"> </div>
</section>

<aside id="sidebar-area">
    <ul class="sBanner">
        <li><img id="banner1" class="img-responsive" alt="" src=""/></li>
        <li><img id="banner2" class="img-responsive" alt="" src=""/></li>
        <li><img id="banner3" class="img-responsive" alt="" src=""/></li>
    </ul>
</aside>

<input type="hidden" id="hdAreaId" value="<?= $_GET['area_id'] ?? "" ?>">
<input type="hidden" id="hdShopId" value="<?= $_GET['shop_id'] ?? "" ?>">

<?php require_once __DIR__ . "/view/template/footer/demo.php"; ?>