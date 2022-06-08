<?php
    require_once __DIR__ . '/../model/M_shop.class.php';
    require_once __DIR__ . '/../view/V_shop.class.php';
    $model = new M_shop;
    $view  = new V_shop;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["shopId"] ) );
            echo json_encode( $result );
            break;

        case 'add':
            $param = [
                'code'      => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'      => htmlspecialchars( strip_tags( $_POST['name'] ) ),
                'IsSpecial' => $_POST['IsSpecial'],
                'Descript'  => htmlspecialchars( strip_tags( $_POST['Descript'] ) )
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
                'code'      => htmlspecialchars( strip_tags( $_POST['code'] ) ),
                'name'      => htmlspecialchars( strip_tags( $_POST['name'] ) ),
                'IsSpecial' => $_POST['IsSpecial'],
                'Descript'  => htmlspecialchars( strip_tags( $_POST['Descript'] ) ),
                'id'        => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\D)*$/', $_POST['shopId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["shopId"] ) );
            echo $result;
            break;

        case 'exportCSV':
            echo json_encode( $model->exportCSV() );
            break;
    }
?>