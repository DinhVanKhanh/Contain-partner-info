<?php
    require_once __DIR__ . '/../model/M_area.class.php';
    require_once __DIR__ . '/../view/V_area.class.php';
    $model = new M_area;
    $view  = new V_area;

    switch ( $_POST['action'] ) {
        case 'loadList':
            header('Content-Type: application/json');
            $result = $model->getList();
            http_response_code(200);
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["areaId"] ) );

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'add':
            header('Content-Type: application/json');
            $param = [
                'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) )
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
                'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) ),
                'id'     => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'delete':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["areaId"] ) );

            http_response_code(200);
            echo $result;
            break;

        case 'exportCSV':
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode( $model->exportCSV() );
            break;
        
        case 'changeOrderRow':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['curId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error current id'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['upId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error change id'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['curIdx'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error current idx'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['upIdx'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error change idx'] );
            }
            else {
                http_response_code(200);
                echo json_encode( $model->changeOrderRow(
                    intval( $_POST['curId'] ),
                    intval( $_POST['upId'] ),
                    intval( $_POST['curIdx'] ),
                    intval( $_POST['upIdx'] )
                ));
            }
            break;
    }
?>