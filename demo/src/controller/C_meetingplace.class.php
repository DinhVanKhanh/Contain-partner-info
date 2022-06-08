<?php
	require_once __DIR__ . '/../model/M_meetingplace.class.php';
	require_once __DIR__ . '/../view/V_meetingplace.class.php';
	$model = new M_meetingplace;
	$view  = new V_meetingplace;

	switch ( $_POST['action'] ) {
		case 'loadList':
			header('Content-Type: application/json');
			$result = $model->getList();

			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["mtId"] ) );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'add':
			header('Content-Type: application/json');
			$param = [
				'code'       => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'address_1'  => htmlspecialchars( strip_tags( $_POST['address_1'] ) ),
				'address_2'  => htmlspecialchars( strip_tags( $_POST['address_2'] ) ),
				'storeName1' => htmlspecialchars( strip_tags( $_POST['storeName1'] ) ),
				'storeName2' => htmlspecialchars( strip_tags( $_POST['storeName2'] ) ),
				'todouId'    => $_POST['todouId'],
				'tel'        => $_POST['tel'],
				'fax'        => $_POST['fax'],
				'map'        => $_POST['map'],
				'posCode'    => $_POST['posCode']
			];
			$result = $model->add( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'edit':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
				'code'       => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'address_1'  => htmlspecialchars( strip_tags( $_POST['address_1'] ) ),
				'address_2'  => htmlspecialchars( strip_tags( $_POST['address_2'] ) ),
				'storeName1' => htmlspecialchars( strip_tags( $_POST['storeName1'] ) ),
				'storeName2' => htmlspecialchars( strip_tags( $_POST['storeName2'] ) ),
				'todouId'    => $_POST['todouId'],
				'tel'        => $_POST['tel'],
				'fax'        => $_POST['fax'],
				'map'        => $_POST['map'],
				'posCode'    => $_POST['posCode'],
				'id'         => $_POST['id']
			];
			$result = $model->edit( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["mtId"] ) );

			http_response_code(200);
			echo $result;
			break;

		case 'exportCSV':
			header('Content-Type: application/json');
			http_response_code(200);
			echo json_encode( $model->exportCSV() );
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
	}
?>