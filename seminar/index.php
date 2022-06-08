<?php 
    if ( !session_id() ) {
        session_start();
    }

    $title   = "セミナー";
    $scripts = '';
    $linkCss = '';
    require_once __DIR__ . "/view/template/header/normal.php";
?>
<article id="main" class="clearfix">
	<section id="main_content">
		<section class="section">
			<?php
				if ( empty( $_SESSION['userSM'] ) ) {
					echo '<p class="right indexLogin"><a href="login.php">ログイン</a></p>';
				}
			?>

			<br/><br/>
			<table summary="クライアント" class="table_top">
				<tr>
					<th>クライアント</th>
				</tr>
				<tr>
					<td><a href="regular">セミナーA</a></td>
				</tr>
				<tr>
					<td><a href="zeimusoudankai">セミナーB</a></td>
				</tr>
				<tr>
					<td><a href="aoiro">セミナーC</a></td>
				</tr>
				<tr>
					<td><a href="nenchou">セミナーD</a></td>
				</tr>
			</table>
			<h3></h3>
		</section>
	</section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php"; ?>