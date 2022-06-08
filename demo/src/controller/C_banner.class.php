<?php
	require_once __DIR__ . '/../model/M_banner.class.php';
	require_once __DIR__ . '/../view/V_banner.class.php';
	$model = new M_banner;
	$view  = new V_banner;

	switch ( $_POST['action'] ) {
		case 'loadList':
			$result = $model->getList();
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['bannerId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["bannerId"] ) );
			echo json_encode( $result );
			break;

		case 'add':
			$param = [
                'ParentId'    => intval( $_POST['ParentId'] ),
                'Description' => htmlspecialchars( strip_tags( $_POST['Description'] ) ),
                'IsShop'      => intval( $_POST['IsShop'] ),

                // Banner1
                'Banner1' => $_FILES['Banner1'] ?? null,
                'IsShow1' => intval( $_POST['IsShow1'] ),

                // Banner2
                'Banner2' => $_FILES['Banner2'] ?? null,
                'IsShow2' => intval(  $_POST['IsShow2'] ),

                // Banner3
                'Banner3' => $_FILES['Banner3'] ?? null,
                'IsShow3' => intval( $_POST['IsShow3'] ),
			];
			$result = $model->add( $param );
			echo json_encode( $result );
			break;

		case 'edit':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
                'id'          => intval( $_POST['id'] ),
                'ParentId'    => intval( $_POST['ParentId'] ),
                'Description' => htmlspecialchars( strip_tags( $_POST['Description'] ) ),
                'IsShop'      => intval( $_POST['IsShop'] ),

                // Banner1
                'Banner1' => $_FILES['Banner1'] ?? null,
                'IsShow1' => intval( $_POST['IsShow1'] ),

                // Banner2
                'Banner2' => $_FILES['Banner2'] ?? null,
                'IsShow2' => intval(  $_POST['IsShow2'] ),

                // Banner3
                'Banner3' => $_FILES['Banner3'] ?? null,
                'IsShow3' => intval( $_POST['IsShow3'] ),

                'oldBanner1' => $_POST['oldBanner1'],
                'oldBanner2' => $_POST['oldBanner2'],
                'oldBanner3' => $_POST['oldBanner3']
			];
			$result = $model->edit( $param );
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['bannerId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["bannerId"] ) );
			echo $result;
			break;

		case 'exportCSV':
			echo json_encode( $model->exportCSV() );
			break;
	}
?>
