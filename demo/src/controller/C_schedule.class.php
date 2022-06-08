<?php
	require_once __DIR__ . '/../model/M_schedule.class.php';
	require_once __DIR__ . '/../view/V_schedule.class.php';
	$model = new M_schedule;
	$view  = new V_schedule;

	switch ( $_POST['action'] ) {
		case 'loadList':
			$result = $model->getList();
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['schId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["schId"] ) );
			echo json_encode( $result );
			break;

		case 'add':
			$param = [
				'shopId'         => intval( $_POST['shopId'] ),
				'meetingPlaceId' => intval( $_POST['mtId'] ),
				'scDate'         => $_POST['scDate'],
				'scFTime'        => $_POST['scFTime'],
				'scTTime'        => $_POST['scTTime'],
				'scDescript'     => htmlspecialchars( strip_tags( $_POST['scDescript'] ) ),
				'isActive'       => intval( $_POST['isActive'] ),
				'isHighLight'    => intval( $_POST['isHighLight'] ),
				'oldPdf'         => $_POST['oldPdf'],
				'curPdf'         => $_POST['curPdf'] ?? "",
				'file'           => $_FILES['file'] ?? null,
			];
			$result = $model->add( $param );
			echo json_encode( $result );
			break;

		case 'edit':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] )
			|| preg_match( '/^(\s|\D)*$/', $_POST['shopId'] )
			|| preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
				'shopId'         => intval( $_POST['shopId'] ),
				'meetingPlaceId' => intval( $_POST['mtId'] ),
				'scDate'         => $_POST['scDate'],
				'scFTime'        => $_POST['scFTime'],
				'scTTime'        => $_POST['scTTime'],
				'scDescript'     => htmlspecialchars( strip_tags( $_POST['scDescript'] ) ),
				'isActive'       => intval( $_POST['isActive'] ),
				'isHighLight'    => intval( $_POST['isHighLight'] ),
				'oldPdf'         => $_POST['oldPdf'],
				'curPdf'         => $_POST['curPdf'] ?? "",
				'file'           => $_FILES['file'] ?? null,
				'id'             => intval( $_POST['id'] ),
			];
			$result = $model->edit( $param );
			echo json_encode( $result );
			break;

		case 'delete':
			$result = $model->delete( $_POST["idList"] );
			echo $result;
			break;

		case 'filterByArea':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->filterByArea( intval( $_POST["areaId"] ) );
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'uploadData':
			if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files/';
                if (file_exists( $direct . '/' . $_FILES["file"]["name"] )) {
                    unlink( $direct . '/' . $_FILES["file"]["name"] );
                }
			    move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $_FILES["file"]['name'] );

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
                $result = $model->uploadData( $direct . '/' . $_FILES["file"]["name"] );
            }
            else {
                $result['errMsg'] = "エラーがある";
            }
			unlink( $direct . '/' . $_FILES["file"]['name'] );
			echo json_encode( $result );
			break;

        case 'getListSpecialShop':
			$result = $model->getListSpecialShop();
			echo json_encode( $result );
            break;

        case 'getListNormalShop':
			$result = $model->getListNormalShop();
			echo json_encode( $result );
            break;
	}
?>
