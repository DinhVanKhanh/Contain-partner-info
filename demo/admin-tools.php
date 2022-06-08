<?php
	if( !session_id() ) {
		session_start();
	}

	$title = "Demo";
	$linkCss = $scripts = "";
	require_once __DIR__ . "/view/template/header/normal.php";

	$Conn = new Database;
?>

<article id="main" class="clearfix">
	<section id="main_content">
		<section class="section">
			<?php 
				if ( empty( $_SESSION['userDM'] ) ) {
					echo '<p class="right indexLogin"><a href="login.php">ログイン</a></p>';
				}
				else {
					echo '<p class="right indexLogin"><a href="schedule.php">店頭デモ　管理者画面</a></p>';
				}
			?>

			<table summary="開催地域を選ぶ" class="table_top">
				<tr>
					<th>開催地域を選ぶ</th>
				</tr>
				<?php
					// Area
					$areas = $Conn->conn->prepare( "SELECT `AreaId`, `AreaName` FROM `infodemo_areas` ORDER BY DisplayNo DESC" );
					$areas->execute();
					$result = $areas->fetchAll(PDO::FETCH_ASSOC);
					if ( $result != false && count( $result ) > 0 ) {
						foreach ( $result as $index => $area ) {
							echo '<tr>
									<td>
										<a href="demo_program.php?area_id=' . $area["AreaId"] . '">'
											. htmlentities( $area["AreaName"] ) . '地区
										</a>
									</td>
								</tr>';
						}
					}
					unset($areas);
				?>
			</table>
		
			<?php
				// Shop where IsSpecial = 1
				$shop_special = $Conn->conn->prepare( "SELECT `ShopId`, `Name` FROM `infodemo_shops` WHERE IsSpecial=1" );
				$shop_special->execute();
				$result = $shop_special->fetchAll(PDO::FETCH_ASSOC);
				if ( $result != false && count( $result ) > 0 ) {
					echo '<table class="table_top" summary="特別な店頭を選ぶ">
							<tr>
								<th>特別な店頭を選ぶ</th>
							</tr>';

					foreach ( $result as $index => $shop ) {
						echo '<tr>
								<td>
									<a href="demo_program.php?shop_id=' . $shop["ShopId"]  . '">'
										. htmlentities( $shop["Name"] ) .
									'</a>
								</td>
							</tr>';
					}					
					echo '</table>';
				}
				unset($shop_special);
			?>
		</section>
	</section>
</article>

<?php require_once __DIR__ . "/view/template/footer/normal.php" ?>