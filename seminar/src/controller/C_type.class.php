<?php
    require_once __DIR__ . '/../model/M_type.class.php';
    require_once __DIR__ . '/../view/V_type.class.php';
    $model = new M_type;
    $view  = new V_type;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['typesId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["typesId"] ) );
            echo json_encode( $result );
            break;

        case 'add':
            $param = [
                'TypesName'   => $_POST['typesName'],
                'Description'   => htmlspecialchars( strip_tags( $_POST['Description'] ) )
            ];
            $result = $model->add( $param );
            echo json_encode( $result );
            break;

        case 'edit':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['typesId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'TypesName'   => $_POST['typesName'],
                'Description'   => htmlspecialchars( strip_tags( $_POST['Description'] ) ),
                'TypesId'     => intval( $_POST['typesId'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['typesId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["typesId"] ) );
            echo $result;
            break;
    }
?>