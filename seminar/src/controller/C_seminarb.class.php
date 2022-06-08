<?php
    require_once __DIR__ . '/../model/M_seminarb.class.php';
    require_once __DIR__ . '/../view/V_seminarb.class.php';
    $model = new M_seminarb;
    $view  = new V_seminarb;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 2' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadList( $result, $sample ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['seminarId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
			$result = $model->getById( intval( $_POST["seminarId"] ) );
            echo json_encode( $result );
			break;

		case 'getSample':
			$conn = new Database;

            // Sample
			$stmt = $conn->conn->prepare( 'SELECT SampleName, SampleDeadline FROM infoseminar_sample WHERE TypesId = 2' );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $now = date("h:i:sa");
            $days = ($result['SampleDeadline'] == "") ? 0 : $result['SampleDeadline'];
            $date_minus = date("Y-n-j", strtotime("$now - $days day"));
			list($result['year'], $result['month'], $result['day']) = explode('-', $date_minus);
            $result['today'] = date( 'Y-n-j' );

			echo json_encode( $result );
			break;

        case 'add':
            $param = [
				'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Area'         => $_POST['AreaId'],
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
				'VenueMap'     => strip_tags( $_POST['VenueMap'] ),
				'TimeStart'    => $_POST['TimeStart'],
				'TimeEnd'      => $_POST['TimeEnd'],
				'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
                'AppDate'      => date( 'Y-n-j', strtotime($_POST['AppDate']) )
            ];

            $result = $model->add( $param );
            echo json_encode( $result );
            break;

        case 'edit':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['id'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }

            $param = [
                'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Area'         => $_POST['AreaId'],
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
				'VenueMap'     => strip_tags( $_POST['VenueMap'] ),
				'TimeStart'    => $_POST['TimeStart'],
				'TimeEnd'      => $_POST['TimeEnd'],
				'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
                'AppDate'      => date( 'Y-n-j', strtotime($_POST['AppDate']) ),
                'id'           => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['seminarId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["seminarId"] ) );
            echo $result;
            break;

        case 'uploadData':
            $fileName = 'SEMINAR_B_EXCEL_' . $_FILES["file"]["name"];

            if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files';
                if (file_exists( $direct . '/' . $fileName )) {
                    unlink( $direct . '/' . $fileName );
                }
                move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $fileName );

                // Get sample name
                $conn = new Database;
                $stmt = $conn->conn->prepare("SELECT SampleName, SampleDeadline FROM infoseminar_sample WHERE TypesId = 2");
				$stmt->execute();
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
                $result = $model->uploadData( $fileName, $sample);
            }
            else {
                $result['errMsg'] = "エラーがある";
            }

            unlink( $direct . '/' . $fileName );
            echo json_encode( $result );
            break;

        case 'checkExistAll':
            echo json_encode( $model->checkExistAll() );
            break;

        case 'deleteAll':
            echo json_encode( $model->deleteAll() );
            break;

        case 'FullSeminarBId':
            $model->updateColFull( $_POST['seminarId'] );
            echo 1;
            break;

        case 'loadClientList':
            $result = $model->getClientList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 2' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadClientList( $result, $sample ) );
            break;
    }
?>
