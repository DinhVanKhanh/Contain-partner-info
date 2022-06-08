<?php
    require_once __DIR__ . '/../model/M_master.class.php';
    require_once __DIR__ . '/../view/V_master.class.php';
    $model = new M_master;
    $view  = new V_master;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            echo json_encode( $view->loadList( $result ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['SampleId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->getById( intval( $_POST["SampleId"] ) );
            echo json_encode( $result );
            break;

        case 'save':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['SampleId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'SampleId'       => $_POST['SampleId'],
                'SampleName'     => htmlspecialchars( strip_tags( $_POST['SampleName'] ) ),
                'SampleDeadline' => $_POST['SampleDeadline'],
                'SampleFeesChk'  => $_POST['SampleFeesChk'],
                'SampleFees'     => $_POST['SampleFees'],
                'SampleTaxChk'   => $_POST['SampleTaxChk'],
                'SampleAppMonth' => $_POST['SampleAppMonth'],
                'SampleAlways'   => $_POST['SampleAlways'],
                'SampleEmail'    => strip_tags( $_POST['SampleEmail'] )
            ];
            $result = $model->save( $param );
            echo json_encode( $result );
            break;
    }
?>