<?php
	require_once __DIR__ . '/../model/M_schedule.class.php';
	require_once __DIR__ . '/../view/V_schedule.class.php';
	$model = new M_schedule;
	$view  = new V_schedule;

	switch ( $_POST['action'] ) {
		case 'loadList':
			header('Content-Type: application/json');
			$result = $model->getList();

			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['schId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["schId"] ) );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'add':
			header('Content-Type: application/json');
			$param = [
				'ShopId'         => (int) $_POST['shopId'],
				'MeetingPlaceId' => (int) $_POST['mtId'],
				'Date'           => $_POST['scDate'],
				'TimeFrom'       => $_POST['scFTime'],
				'TimeTo'         => $_POST['scTTime'],
				'Description'    => htmlspecialchars( strip_tags( $_POST['scDescript'] ) ),
				'IsActive'       => (int) trim( $_POST['isActive'] ?? '' ),
				'IsHighlight'    => (int) trim( $_POST['isHighLight'] ?? '' ),
				'oldPdf'         => trim( $_POST['oldPdf'] ?? '' ),
				'curPdf'         => trim( $_POST['curPdf'] ?? '' ),
				'file'           => $_FILES['file'] ?? null,
			];
			$result = $model->add( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'edit':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] )
			|| preg_match( '/^(\s|\D)*$/', $_POST['shopId'] )
			|| preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
				'ShopId'         => (int) $_POST['shopId'],
				'MeetingPlaceId' => (int) $_POST['mtId'],
				'Date'           => $_POST['scDate'],
				'TimeFrom'       => $_POST['scFTime'],
				'TimeTo'         => $_POST['scTTime'],
				'Description'    => htmlspecialchars( strip_tags( $_POST['scDescript'] ) ),
				'IsActive'       => (int) trim( $_POST['isActive'] ?? '' ),
				'IsHighlight'    => (int) trim( $_POST['isHighLight'] ?? '' ),
				'oldPdf'         => trim( $_POST['oldPdf'] ?? '' ),
				'curPdf'         => trim( $_POST['curPdf'] ?? '' ),
				'file'           => $_FILES['file'] ?? null,
				'ScheduleId'     => (int) $_POST['id'],
			];
			$result = $model->edit( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'delete':
			http_response_code(200);
			$result = $model->delete( $_POST["idList"] );
			echo $result;
			break;

		case 'filterByArea':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->filterByArea( intval( $_POST["areaId"] ) );

			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'uploadData':
			header('Content-Type: application/json');
			if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files/';
                if (file_exists( $direct . '/' . $_FILES["file"]["name"] )) {
                    unlink( $direct . '/' . $_FILES["file"]["name"] );
                }
			    move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $_FILES["file"]['name'] );

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
				$result = $model->uploadData( $direct . '/' . $_FILES["file"]["name"] );
				http_response_code(200);
            }
            else {
				$result['errMsg'] = "エラーがある";
				http_response_code(400);
            }
			unlink( $direct . '/' . $_FILES["file"]['name'] );
			echo json_encode( $result );
			break;

		case 'getListSpecialShop':
			header('Content-Type: application/json');
			http_response_code(200);
			$result = $model->getListSpecialShop();
			echo json_encode( $result );
            break;

		case 'getListNormalShop':
			header('Content-Type: application/json');
			http_response_code(200);
			$result = $model->getListNormalShop();
			echo json_encode( $result );
            break;
	}
?>
