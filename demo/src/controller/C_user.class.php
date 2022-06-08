<?php
	require_once __DIR__ . '/../model/M_user.class.php';
	require_once __DIR__ . '/../view/V_user.class.php';
	$model = new M_user;
	$view  = new V_user;

	switch ( $_POST['action'] ) {
		case 'loadList':
			header('Content-Type: application/json');
			$result = $model->getList();
			http_response_code(200);
			echo json_encode( $view->loadList( $result ) );
			break;

		case 'loadById':
			header('Content-Type: application/json');
			if ( preg_match( '/^(\s|\D)*$/', $_POST['userId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->getById( intval( $_POST["userId"] ) );
			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'add':
			header('Content-Type: application/json');
			$param = [
				'UserCd'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
				'UserName' => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'Password' => trim( $_POST['password'] ),
				'KengenKbn'	 => trim( $_POST['role'] )
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
				'UserName'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
				'Password'   => trim( $_POST['password'] ),
				'UserId'     => (int) trim( $_POST['id'] ),
				'isPwChange' => (bool) trim( $_POST['isPwChange'] ),
				'KengenKbn'	 => $_POST['role'] ?? $_SESSION['roleDM'] ?? ''
			];
			$result = $model->edit( $param );

			http_response_code(200);
			echo json_encode( $result );
			break;

		case 'delete':
			if ( preg_match( '/^(\s|\D)*$/', $_POST['userId'] ) ) {
				http_response_code(400);
				echo json_encode( ['errMsg' => 'Error id'] );
				return;
			}
			$result = $model->delete( intval( $_POST["userId"] ) );

			http_response_code(200);
			echo $result;
			break;

		case 'exportCSV':
			header('Content-Type: application/json');
			http_response_code(200);
			echo json_encode( $model->exportCSV() );
			break;
		
		case 'loadInfoPersonal':
			header('Content-Type: application/json');
			$result = $model->getById( intval( $_SESSION["idDM"] ) );
			unset($result['KengenKbn']);
			http_response_code(200);
			echo json_encode( $model->getById( intval( $_SESSION["idDM"] ) ) );
			break;
	}
?>