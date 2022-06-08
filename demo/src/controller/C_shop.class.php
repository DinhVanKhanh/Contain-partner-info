<?php
    require_once __DIR__ . '/../model/M_shop.class.php';
    require_once __DIR__ . '/../view/V_shop.class.php';
    $model = new M_shop;
    $view  = new V_shop;

    switch ( $_POST['action'] ) {
        case 'loadList':
            header('Content-Type: application/json');
            $result = $model->getList();
            http_response_code(200);
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            header('Content-Type: application/json');
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["shopId"] ) );

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'add':
            header('Content-Type: application/json');
            $param = [
                'code'      => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'      => htmlspecialchars( strip_tags( $_POST['name'] ) ),
                'IsSpecial' => $_POST['IsSpecial'],
                'Descript'  => htmlspecialchars( strip_tags( $_POST['Descript'] ) )
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
                'code'      => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'      => htmlspecialchars( strip_tags( $_POST['name'] ) ),
                'IsSpecial' => $_POST['IsSpecial'],
                'Descript'  => htmlspecialchars( strip_tags( $_POST['Descript'] ) ),
                'id'        => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );

            http_response_code(200);
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                http_response_code(400);
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            http_response_code(200);
            echo $model->delete( intval( $_POST["shopId"] ) );
            break;

        case 'exportCSV':
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode( $model->exportCSV() );
            break;
    }
?>