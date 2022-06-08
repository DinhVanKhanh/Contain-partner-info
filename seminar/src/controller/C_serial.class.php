<?php
    require_once __DIR__ . '/../model/M_serial.class.php';
    require_once __DIR__ . '/../view/V_serial.class.php';
    $model = new M_serial;
    $view  = new V_serial;

    switch ( $_POST['action'] ) {
        case 'loadList':
            // Sample
            $conn = new Database;
            $stmt = $conn->conn->prepare( 'SELECT SampleId, SampleName FROM infoseminar_sample' );
            $stmt->execute();
            $sample = $stmt->fetchAll( PDO::FETCH_ASSOC );

            $result = $model->getList();
            echo json_encode( $view->loadList( $result, $sample ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['SerialId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["SerialId"] ) );
            echo json_encode( $result );
            break;

        case 'add':
            $param = [
                'SampleId'     => $_POST['SampleId'],
                'SerialNumber' => $_POST['SerialNumber'],
                'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) )
            ];
            $result = $model->add( $param );
            echo json_encode( $result );
            break;

        case 'edit':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['SerialId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'SampleId'     => $_POST['SampleId'],
                'SerialNumber' => $_POST['SerialNumber'],
                'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) ),
                'SerialId'     => intval( $_POST['SerialId'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['SerialId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["SerialId"] ) );
            echo $result;
            break;
    }
?>