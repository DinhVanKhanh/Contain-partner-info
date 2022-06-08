<?php
	require_once __DIR__ . '/../model/M_demo.class.php';
	require_once __DIR__ . '/../view/V_demo.class.php';
	$model = new M_demo;
	$view  = new V_demo;

	switch ( $_POST['action'] ) {
        case 'searchSchedule':
            header('Content-Type: application/json');
            $result = $model->getResultScheduleFromSeach(
                $_POST["areaId"],
                $_POST["shopId"],
                $_POST["todouhukenId"],
                trim( $_POST["date"] ),
                trim( $_POST["address"] )
            );
            http_response_code(200);
			echo json_encode( $view->loadResultScheduleFromSearch( $result ) );
            break;

        case 'loadTodohukenListByAreaId':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getTodouhukenListByAreaId( intval( $_POST['areaId'] ) );

            http_response_code(200);
            echo json_encode( $view->loadTodouhukenList( $result ) );
            break;
        
        case 'loadTodohukenByShopId':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getTodouhukenListByShopId( $_POST["shopId"] );

            http_response_code(200);
            echo json_encode( $view->loadTodouhukenList( $result ) );
            break;

        case 'getBanner':
            header('Content-Type: application/json');
            $result = $model->getBanner( $_POST["parentId"], $_POST["isShop"] );

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'getAreaList':
            header('Content-Type: application/json');
            $result = $model->getAreaList();

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'getShopName':
            header('Content-Type: application/json');
            $result = $model->getShopName();

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'getListSpecialShop':
            header('Content-Type: application/json');
            $result = $model->getListSpecialShop();
            
            http_response_code(200);
			echo json_encode( $result );
            break;

        case 'getListNormalShop':
            header('Content-Type: application/json');
            $result = $model->getListNormalShop();
            
            http_response_code(200);
			echo json_encode( $result );
            break;
	}
?>