<?php
	require_once __DIR__ . '/../model/M_meetingplace.class.php';
	require_once __DIR__ . '/../view/V_meetingplace.class.php';
	$model = new M_meetingplace;
	$view  = new V_meetingplace;

	switch ( $_POST['action'] ) {
		case 'loadList':
			$result = $model->getList();
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["mtId"] ) );
			echo json_encode( $result );
			break;

		case 'add':
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
			echo json_encode( $result );
			break;

		case 'edit':
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
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['mtId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["mtId"] ) );
			echo $result;
			break;

		case 'exportCSV':
			echo json_encode( $model->exportCSV() );
			break;

		case 'filterByArea':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->filterByArea( intval( $_POST["areaId"] ) );
			echo json_encode( $view->loadList( $result ) );
			break;
	}
?>