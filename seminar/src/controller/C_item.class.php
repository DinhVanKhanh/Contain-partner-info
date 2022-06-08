<?php
    require_once __DIR__ . '/../model/M_item.class.php';
    require_once __DIR__ . '/../view/V_item.class.php';
    $model = new M_item;
    $view  = new V_item;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['itemId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["itemId"] ) );
            echo json_encode( $result );
            break;

        case 'add':
            $param = [
                'type'   => $_POST['type'],
                'code'   => $_POST['code'],
                'name'   => $_POST['name']
            ];
            $result = $model->add( $param );
            echo json_encode( $result );
            break;

        case 'edit':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['id'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'type'   => $_POST['type'],
                'code'   => $_POST['code'],
                'name'   => $_POST['name'],
                'id'     => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['itemId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["itemId"] ) );
            echo $result;
            break;
    }
?>