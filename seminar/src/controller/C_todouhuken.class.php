<?php
	require_once __DIR__ . '/../model/M_todouhuken.class.php';
	require_once __DIR__ . '/../view/V_todouhuken.class.php';
	$model = new M_todouhuken;
	$view  = new V_todouhuken;

	switch ( $_POST['action'] ) {
		case 'loadList':
			$result = $model->getList();
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['todouId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["todouId"] ) );
			echo json_encode( $result );
			break;

		case 'add':
			$param = [
				'code'    => $_POST['code'],
				'name'    => $_POST['name'],
				'display' => $_POST['display'],
				'areaId'  => intval( $_POST['areaId'] )
			];
			$result = $model->add( $param );
			echo json_encode( $result );
			break;

		case 'edit':
			if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['id'] ) 
			|| preg_match( '/^(\s|\s+|\D)$/', $_POST['areaId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}

			$param = [
				'code'    => $_POST['code'],
				'name'    => $_POST['name'],
				'display' => $_POST['display'],
				'areaId'  => intval( $_POST['areaId'] ),
				'id'     => intval( $_POST['id'] )
			];
			$result = $model->edit( $param );
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['todouId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["todouId"] ) );
			echo $result;
			break;

		case 'exportCSV':
			echo json_encode( $model->exportCSV() );
			break;
	}
?>