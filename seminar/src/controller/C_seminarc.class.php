<?php
    require_once __DIR__ . '/../model/M_seminarc.class.php';
    require_once __DIR__ . '/../view/V_seminarc.class.php';
    require_once __DIR__ . "/../../libs/mailer.class.php";

    $model = new M_seminarc;
    $view  = new V_seminarc;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 3' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadList( $result, $sample ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['seminarId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
			$result = $model->getById( intval( $_POST["seminarId"] ) );

			// Sample
			$conn = new Database;
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 3' );
            $stmt->execute();
            $result['sample'] = $stmt->fetch(PDO::FETCH_ASSOC)['SampleAppMonth'];
            echo json_encode( $result );
			break;

		case 'getSample':
			$conn = new Database;

            // Sample
			$stmt = $conn->conn->prepare( 'SELECT * FROM infoseminar_sample WHERE TypesId = 3' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            $result['today'] = date('Y-n-j');
            $today = date_create();
            $day = intval( date("j") ) - intval( $sample['SampleDeadline'] );
            date_add( $today, date_interval_create_from_date_string( $day . ' day' ) );
			$deadline = $today->format('Y-n-j');
			list($result['year'], $result['month'], $result['day']) = explode('-', $deadline);
			$result['sampleName'] = $sample['SampleName'];

			// Todouhuken
			$stmt = $conn->conn->prepare( 'SELECT * FROM infoseminar_todouhukens ORDER BY TodouhukenCode' );
			$stmt->execute();
			$todous = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$result['todouhukenOption'] = '';
			foreach ( $todous as $todou ) {
				$result['todouhukenOption'] .= "<option value='" . $todou['TodouhukenId'] . "'>" . htmlspecialchars($todou['TodouhukenDisplay']) . "</option>";
			}

			echo json_encode( $result );
			break;

        case 'add':
            $param = [
				'file'         => $_FILES['file'] ?? null,
				'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Todouhuken'   => $_POST['Todouhuken'],
				'CompanyName'  => htmlspecialchars( strip_tags( $_POST['CompanyName'] ) ),
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
				'TimeStart'    => $_POST['TimeStart'],
				'TimeEnd'      => $_POST['TimeEnd'],
				'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
				'CountPerson'  => $_POST['CountPerson'],
				'ContactTel'   => $_POST['ContactTel'],
				'ContactFax'   => $_POST['ContactFax'],
				'curPdf'       => $_POST['curPdf'],
				'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) ),
				'AppDate'      => date( 'Y-n-j', strtotime($_POST['AppDate']) )
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
                'file'         => $_FILES['file'] ?? null,
                'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Todouhuken'   => $_POST['Todouhuken'],
				'CompanyName'  => htmlspecialchars( strip_tags( $_POST['CompanyName'] ) ),
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
                'TimeStart'    => $_POST['TimeStart'],
                'TimeEnd'      => $_POST['TimeEnd'],
                'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
                'CountPerson'  => $_POST['CountPerson'],
                'ContactTel'   => $_POST['ContactTel'],
                'ContactFax'   => $_POST['ContactFax'],
                'curPdf'       => $_POST['curPdf'],
                'oldPdf'       => $_POST['oldPdf'],
                'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) ),
                'AppDate'      => date( 'Y-n-j', strtotime($_POST['AppDate']) ),
                'deletePdf'    => intval( $_POST['deletePdf'] ),
                'id'           => intval( $_POST['id'] )
            ];
            $result = $model->edit( $param );
            echo json_encode( $result );
            break;

        case 'delete':
            if ( preg_match( '/^(\s|\s+|\D)$/', $_POST['seminarId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
            $result = $model->delete( intval( $_POST["seminarId"] ) );
            echo $result;
            break;

        case 'uploadData':
            $fileName = 'SEMINAR_C_EXCEL_' . $_FILES["file"]["name"];

            if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files';
                if (file_exists( $direct . '/' . $fileName )) {
                    unlink( $direct . '/' . $fileName );
                }
			    move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $fileName );

                // Get sample name
                $conn = new Database;
                $stmt = $conn->conn->prepare("SELECT SampleName, SampleDeadline FROM infoseminar_sample WHERE TypesId = 3");
				$stmt->execute();
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
                $result = $model->uploadData( $fileName, $sample );
            }
            else {
                $result['errMsg'] = "??????????????????";
            }

            unlink( $direct . '/' . $fileName );
            echo json_encode( $result );
            break;

        case 'uploadMultiPdf':
            if ($_POST['image_form_submit'] == 1) {
                $images_arr = array();
                $err = array();
                $errMsg = $alert = "";

                foreach ($_FILES['pdf']['name'] as $key => $val) {
                    $image_name = $_FILES['pdf']['name'][$key];
                    $size 		= $_FILES["pdf"]["size"][$key];
                    $tmp_name 	= $_FILES['pdf']['tmp_name'][$key];

                    $target_dir  = __DIR__ . '/../../../data_files';
                    $target_file = $target_dir . '/SEMINAR_C_' . $_FILES['pdf']['name'][$key];

                    if ( !preg_match( "/\.pdf$/i", $image_name ) ) {
                        echo "PDF????????????????????????????????????";
                    }
                    elseif ($size > 2000000) {
                        echo "PDF????????????????????????????????????????????????????????????????????????";
                    }
                    else {
                        $conn = new Database;
                        $stmt = $conn->conn->prepare("SELECT PDF FROM infoseminar_sumary WHERE TypesId = 3 ORDER BY PDF");
                        $stmt->execute();

                        $flag = 1;
                        if ($stmt->rowCount() > 0) {
                            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ( $result as $value ) {
                                $key1 = $value['PDF'];

                                if ($image_name == $key1) {
                                    move_uploaded_file($_FILES['pdf']['tmp_name'][$key], $target_file);
                                    $images_arr[] = $target_file;
                                    $flag = 1;
                                    echo '<script>$(function(){ loadSeminarList(); });</script>';
                                    break;
                                }
                                else {
                                    $flag = 0;
                                }
                            }

                            if ($flag == 0) {
                                $err[count($err)] = $image_name;
                            }
                        }
                        else {
                            $alert='???????????????????????????????????????';
                        }
                    }
                }

                if (!empty($err)) {
                    $alert = '???????????????????????????';
                    for ($j = 0; $j < count($err); $j++) {
                        $alert .= ' [ ' . $err[$j] . ' ] ';
                    }
                    $alert .= '??????????????????????????????';
                }
                echo '<script>$(function(){ $("label#inputExcel").html("' . $alert .'"); });</script>';

                //Generate images view
                if (!empty( $images_arr )) {
                    $count = 0;
                    foreach ($images_arr as $image_src) {
                        $count++; ?>
<ul class="reorder_ul reorder-photos-list">
    <li id="image_li_<?= $count; ?>" class="ui-sortable-handle"></li>
</ul><?php
                    }
                }
            }
            break;

        case 'checkExistAll':
            echo json_encode( $model->checkExistAll() );
            break;

        case 'deleteAll':
            echo json_encode( $model->deleteAll() );
            break;

        case 'FullSeminarCId':
            $model->updateColFull( $_POST['seminarId'] );
            echo 1;
            break;

        case 'loadClientList':
            $result = $model->getClientList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 1' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadClientList( $result, $sample ) );
            break;

        case "showAttendSeminarC":
            $SeminarId = @$_POST['SeminarId'];

            $conn = new Database;
            $stmt = $conn->conn->prepare( "SELECT * FROM infoseminar_sumary WHERE SeminarId = " . $SeminarId );
            $stmt->execute();

            if ( $stmt->rowCount() < 1 ) {
                $showdata = '  <tr>
                                    <td align="center" style="height:150px; font:normal 100%/150% \'????????????\',Meiryo,sans-serif">????????????????????????????????????????????????</td>
                                </tr>
                                <tr>
                                    <td><font size="1">&nbsp;</font></td>
                                </tr>';
                goto Result;
            }
            $seminars = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($seminars);

            // Todouhuken
            if ( !empty( $TodouhukenId ) ) {
                $stmt = $conn->conn->prepare( "SELECT TodouhukenName FROM infoseminar_todouhukens WHERE TodouhukenId = " . $TodouhukenId );
                $stmt->execute();
                $TodouhukenName = $stmt->fetch(PDO::FETCH_ASSOC)["TodouhukenName"];
            }
            else {
                $TodouhukenName = "";
            }

            $showdata = '
                        <tr>
                            <th nowrap colspan="2" class="smnmida1">???????????? ????????????????????????</th>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">????????????</th>
                            <td class="smnlist1">'. $TodouhukenName .'</td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">????????????</th>
                            <td class="smnlist1">'. $CompanyName .'</td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">????????????</th>
                            <td class="smnlist1">
                                '. $VenueName .'<br>
                                '. $VenueAddress .'<br>';

            $showdata .= ($VenueMap != "") ? '[ <A href="'. $VenueMap .'" target="_blank">??????</a> ]' : '';
            $showdata .= '
                                [TEL] '. $ContactTel .'??????[FAX] '. $ContactFax .'
                            </td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">????????????</th>
                            <td class="smnlist1nw">'. $Date .'???'. $TimeStart .'???'. $TimeEnd .'</td>
                        </tr>
                        <tr>
                        <th nowrap class="smnlist1">??????</th>
                            <td class="smnlist1">'. $CountPerson .'</td>
                        </tr>';

            Result:
            echo json_encode( ["view" => $showdata] );
            break;

        case "sendClientMailC":
            $SeminarId = $_POST["SeminarId"];

            $conn = new Database;
            $stmt = $conn->conn->prepare( "SELECT * FROM infoseminar_sumary WHERE SeminarId = " . $SeminarId );
            $stmt->execute();

            if ( $stmt->rowCount() < 1 ) {
                $result['success'] = false;
                goto Resulta;
            }
            $seminars = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($seminars);

            // Todouhuken
            if ( !empty( $TodouhukenId ) ) {
                $stmt = $conn->conn->prepare( "SELECT TodouhukenName FROM infoseminar_todouhukens WHERE TodouhukenId = " . $TodouhukenId );
                $stmt->execute();
                $TodouhukenName = $stmt->fetch(PDO::FETCH_ASSOC)["TodouhukenName"];
            }
            else {
                $TodouhukenName = "";
            }

            $subject = "??????????????? ?????????????????????????????????";

            $body  = "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "?????????????????? ???????????????????????????????????????????????????????????????" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "\n";
            $body .= " ??????????????? ???????????????????????????" . "\n";
            $body .= "\n";
            $body .= "???[????????????]???" . $TodouhukenName . "\n";
            $body .= "???[??????????????????]???" . $CompanyName . "\n";
            $body .= "???[????????????]???" . $VenueName . "\n";
            $body .= "???[????????????]???" . $VenueAddress . "\n";
            $body .= "???[???????????????]???TEL???" . $ContactTel . "???FAX???" . $ContactFax . "\n";
            $body .= "???[?????????]???" . $Date . "\n";
            $body .= "???[????????????]???" . $TimeStart . "???" . $TimeEnd . "\n";
            $body .= "???[??????]???" . $CountPerson . "\n";
            $body .= "\n";
            $body .= "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "?????????????????????" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "\n";
            $body .= "???[?????????]???" . @$_POST["user_company"] . "\n";
            $body .= "???[????????????]???" . @$_POST["user_name"] . "\n";
            $body .= "???[?????????]???" . @$_POST["user_postcode1"] . "-" . @$_POST["user_postcode2"] . "\n";
            $body .= "??????????????????" . @$_POST["user_address"] . "\n";
            $body .= "???[TEL]???" . @$_POST["user_tel"] . "\n";
            $body .= "???[FAX]???" . @$_POST["user_fax"] . "\n";
            $body .= "???[E-Mail]???" . @$_POST["user_email"] . "\n";

            global $MAILFROM_INFO_SEMINAR_C;
            global $MAILTO_INFO_SEMINAR_C;
            global $MAILBCC_INFO_SEMINAR_C;

            global $SMTP_SERVER_SORIMACHI_NAME;
            global $USER_SMTP_SERVER_SORIMACHI;
            global $PASS_SMTP_SERVER_SORIMACHI;

            $FromMail       = $MAILFROM_INFO_SEMINAR_C;
            $FromName       = '';
            $ToMail         = $MAILTO_INFO_SEMINAR_C;
            $EncriptionType = 3;
            $Host           = $SMTP_SERVER_SORIMACHI_NAME;
            $Port           = 587;
            $Username       = $USER_SMTP_SERVER_SORIMACHI;
            $Password       = $PASS_SMTP_SERVER_SORIMACHI;
            $checkSmtp      = 1;
            $multiEmail     = $MAILBCC_INFO_SEMINAR_C;

            $err = sendmail1($Host, $Port, $EncriptionType, $checkSmtp, $Username, $Password, $FromMail, $FromName, $ToMail, $subject, $body, $multiEmail);

            if ($err !== true) {
                $result['success'] = false;
                $result['error'] = $err;
            }
            else {
                $result['success'] = true;
            }

            Resulta:
            echo json_encode($result);
            break;
    }
?>