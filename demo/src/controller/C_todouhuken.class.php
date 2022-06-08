<?php
	require_once __DIR__ . '/../model/M_todouhuken.class.php';
	require_once __DIR__ . '/../view/V_todouhuken.class.php';
	$model = new M_todouhuken;
	$view  = new V_todouhuken;

	switch ( $_POST['action'] ) {
		case 'loadList':
			header('Content-Type: application/json');
			$result = $model->getList();

			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['todouId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["todouId"] ) );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'add':
			header('Content-Type: application/json');
			$param = [
				'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'areaId' => intval( $_POST['areaId'] )
			];
			$result = $model->add( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'edit':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['id'] ) 
			|| preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
				'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'areaId' => intval( $_POST['areaId'] ),
				'id'     => intval( $_POST['id'] )
			];
			$result = $model->edit( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['todouId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["todouId"] ) );

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