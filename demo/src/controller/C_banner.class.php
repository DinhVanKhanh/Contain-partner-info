<?php
	require_once __DIR__ . '/../model/M_banner.class.php';
	require_once __DIR__ . '/../view/V_banner.class.php';
	$model = new M_banner;
	$view  = new V_banner;

	switch ( $_POST['action'] ) {
		case 'loadList':
			header('Content-Type: application/json');
			$result = $model->getList();

			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['bannerId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["bannerId"] ) );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'add':
			header('Content-Type: application/json');
			$param = [
                'ParentId'    => (int) trim( $_POST['ParentId'] ),
                'Description' => htmlspecialchars( strip_tags( $_POST['Description'] ) ),
                'IsShop'      => (int) trim( $_POST['IsShop'] ),

                // Banner1
                'Banner1' => $_FILES['Banner1'] ?? null,
                'IsShow1' => (int) trim( $_POST['IsShow1'] ),

                // Banner2
                'Banner2' => $_FILES['Banner2'] ?? null,
                'IsShow2' => (int) trim( $_POST['IsShow2'] ),

                // Banner3
                'Banner3' => $_FILES['Banner3'] ?? null,
                'IsShow3' => (int) trim( $_POST['IsShow3'] )
			];
			$result = $model->add( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'edit':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
                'BannerId'    => (int) trim( $_POST['id'] ),
                'ParentId'    => (int) trim( $_POST['ParentId'] ),
                'Description' => htmlspecialchars( strip_tags( $_POST['Description'] ) ),
                'IsShop'      => (int) trim( $_POST['IsShop'] ),

                // Banner1
				'Banner1'    => $_FILES['Banner1'] ?? null,
				'oldBanner1' => empty( $_POST['oldBanner1'] ) ? null : trim($_POST['oldBanner1']),
				'IsShow1'    => (int) trim( $_POST['IsShow1'] ),

                // Banner2
				'Banner2'    => $_FILES['Banner2'] ?? null,
				'oldBanner2' => empty( $_POST['oldBanner2'] ) ? null : trim($_POST['oldBanner2']),
				'IsShow2'    => (int) trim( $_POST['IsShow2'] ),

                // Banner3
				'Banner3'    => $_FILES['Banner3'] ?? null,
				'oldBanner3' => empty( $_POST['oldBanner3'] )? null : trim($_POST['oldBanner3']),
				'IsShow3'    => (int) trim( $_POST['IsShow3'] ),
			];
			$result = $model->edit( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['bannerId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["bannerId"] ) );

			http_response_code(200);
			echo $result;
			break;

		case 'exportCSV':
			header('Content-Type: application/json');
			http_response_code(200);
			echo json_encode( $model->exportCSV() );
			break;
	}
?>
