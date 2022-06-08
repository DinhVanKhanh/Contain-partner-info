<?php
	require_once __DIR__ . '/../model/M_demo.class.php';
	require_once __DIR__ . '/../view/V_demo.class.php';
	$model = new M_demo;
	$view  = new V_demo;

	switch ( $_POST['action'] ) {
        case 'searchSchedule':
            $result = $model->getResultScheduleFromSeach(
                $_POST["areaId"],
                $_POST["shopId"],
                $_POST["todouhukenId"],
                trim( $_POST["date"] ),
                trim( $_POST["address"] )
            );
			echo json_encode( $view->loadResultScheduleFromSearch( $result ) );
            break;

        case 'loadTodohukenListByAreaId':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getTodouhukenListByAreaId( intval( $_POST['areaId'] ) );
            echo json_encode( $view->loadTodouhukenList( $result ) );
            break;
        
        case 'loadTodohukenByShopId':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getTodouhukenListByShopId( $_POST["shopId"] );
            echo json_encode( $view->loadTodouhukenList( $result ) );
            break;

        case 'getBanner':
            $result = $model->getBanner( $_POST["parentId"], $_POST["isShop"] );
            echo json_encode( $result );
            break;

        case 'getAreaList':
            $result = $model->getAreaList();
            echo json_encode( $result );
            break;

        case 'getShopName':
            $result = $model->getShopName( intval( $_POST["shopId"] ) );
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