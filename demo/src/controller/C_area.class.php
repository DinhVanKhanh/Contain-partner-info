<?php
    require_once __DIR__ . '/../model/M_area.class.php';
    require_once __DIR__ . '/../view/V_area.class.php';
    $model = new M_area;
    $view  = new V_area;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["areaId"] ) );
            echo json_encode( $result );
            break;

        case 'add':
            $param = [
                'code'   => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'   => htmlspecialchars( strip_tags( $_POST['name'] ) )
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
                'id'     => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['areaId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["areaId"] ) );
            echo $result;
            break;

        case 'exportCSV':
            echo json_encode( $model->exportCSV() );
            break;
        
        case 'changeOrderRow':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['curId'] ) ) {
                echo json_encode( ['errMsg' => 'Error current id'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['upId'] ) ) {
                echo json_encode( ['errMsg' => 'Error change id'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['curIdx'] ) ) {
                echo json_encode( ['errMsg' => 'Error current idx'] );
            }
            elseif ( preg_match( '/^(\s|\D)*$/', $_POST['upIdx'] ) ) {
                echo json_encode( ['errMsg' => 'Error change idx'] );
            }
            else {
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