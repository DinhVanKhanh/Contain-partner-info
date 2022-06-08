<?php
    $title   = "青色申告 直前対策セミナー";
    $scripts = '<script type="text/javascript" src="../assets/js/proccess/P_aoiro.js"></script>';
    $linkCss = '';
	require_once __DIR__ . "/../view/template/header/client.php";
?>

<article id="main" class="clearfix" >	
    <img id="scLoading" style="position:absolute; right:45%; top:50%; z-index:9999; display:none; background-color:#333; padding:2%;" src="../assets/images/icon_loading.gif" />
	<section id="main_content" >
		<!--/tablebox start/-->

		<div class="table_client">
			<h1 class="h1_cd"><img src="../assets/images/i_seminer.gif"></h1>	

            <!-- Content -->
			<div id="tableContent" class="tableContentC"></div>

            <table class="tblContact" style="border-collapse:collapse;">
                <tbody>
                    <tr>
                        <td nowrap="" class="center" style="padding:5px 15px; background-color:#F0F0F0; border:1px #888888 solid; font-size: 14px; font-family: 'ＭＳ Ｐゴシック',Osaka,sans-serif"><b>お問い合わせ先</b></td>
                    </tr>
                    <tr>
                        <td nowrap="" style="padding:10px 15px; border:1px #888888 solid;"><b>ソリマチ株式会社&#12288;ソリマチパートナー事務局</b><br>
                            〒141-0022<br>
                            東京都品川区東五反田 3-18-6 ソリマチ第８ビル<br>
                            TEL：03-3446-1311<br>
                            FAX：03-5475-5339<br>
                            e-mail：<a href="mailto:seminar@mail.sorimachi.co.jp">seminar@mail.sorimachi.co.jp</a></td>
                    </tr>
                </tbody>
            </table>
		</div>
        
		<input type="hidden" id="seminarId">
    	<input type="hidden" id="isNew">
	</section>
</article>

<script type="text/javascript">
    $(document).ready(function(){	
        $('#mnSeminar').css('color','#cc3300');			
        loadSemianrCClientList();
    });
</script>

<?php require_once __DIR__ . "/../view/template/footer/client.php"; ?>