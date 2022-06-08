<?php
	require_once __DIR__ . '/../model/M_user.class.php';
	require_once __DIR__ . '/../view/V_user.class.php';
	$model = new M_user;
	$view  = new V_user;

	switch ( $_POST['action'] ) {
		case 'loadList':
			$result = $model->getList();
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['userId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["userId"] ) );
			echo json_encode( $result );
			break;

		case 'add':
			$param = [
				'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'password'   => $_POST['password']
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
				'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'password'   => $_POST['password'],
				'id'         => $_POST['id'],
				'isPwChange' => $_POST['isPwChange']
			];
			$result = $model->edit( $param );
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['userId'] ) ) {
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["userId"] ) );
			echo $result;
			break;

		case 'exportCSV':
			echo json_encode( $model->exportCSV() );
			break;
	}
?>