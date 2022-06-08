<?php
    require_once __DIR__ . '/../model/M_seminara.class.php';
    require_once __DIR__ . '/../view/V_seminara.class.php';
    require_once __DIR__ . "/../../libs/mailer.class.php";

    $model = new M_seminara;
    $view  = new V_seminara;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth, SampleTaxChk FROM infoseminar_sample WHERE TypesId = 1' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            // Item
            $stmt = $conn->conn->prepare( 'SELECT ItemId, ItemName FROM infoseminar_items' );
            $stmt->execute();
            $item = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadList( $result, $sample, $item ) );
            break;

        case 'loadById':
            if ( preg_match( '/^(\s*|\D)$/', $_POST['seminarId'] ) ) {
                echo json_encode( ['errMsg' => 'Error id'] );
                return;
            }
			$result = $model->getById( intval( $_POST["seminarId"] ) );

			// Sample
			$conn = new Database;
            $stmt = $conn->conn->prepare( 'SELECT SampleTaxChk FROM infoseminar_sample WHERE TypesId = 1' );
            $stmt->execute();
            $result['SampleTaxChk'] = $stmt->fetch(PDO::FETCH_ASSOC)['SampleTaxChk'];
            echo json_encode( $result );
			break;

		case 'getSample':
			$conn = new Database;

            // Sample
			$stmt = $conn->conn->prepare( 'SELECT SampleName, SampleFees, SampleTaxChk FROM infoseminar_sample WHERE TypesId = 1' );
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode( $result );
			break;

        case 'add':
            $param = [
				'file'         => $_FILES['file'] ?? null,
				'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Area'         => $_POST['AreaId'],
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
				'VenueMap'     => strip_tags( $_POST['VenueMap'] ),
				'VenueStation' => htmlspecialchars( strip_tags( $_POST['VenueStation'] ) ),
				'TimeStart'    => $_POST['TimeStart'],
				'TimeEnd'      => $_POST['TimeEnd'],
				'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
				'CountPerson'  => $_POST['CountPerson'],
				'ContactTel'   => $_POST['ContactTel'],
				'ContactFax'   => $_POST['ContactFax'],
				'Course'       => $_POST['Course'],
				'Company'      => $_POST['Company'],
                'Product'      => $_POST['Product'],
                'SeminarFees'   => $_POST['SeminarFees'],
				'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) )
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
                'file'         => $_FILES['file'] ?? null,
				'SeminarName'  => htmlspecialchars( strip_tags( $_POST['SeminarName'] ) ),
				'Area'         => $_POST['AreaId'],
				'VenueName'    => htmlspecialchars( strip_tags( $_POST['VenueName'] ) ),
				'VenueAddress' => htmlspecialchars( strip_tags( $_POST['VenueAddress'] ) ),
				'VenueMap'     => strip_tags( $_POST['VenueMap'] ),
				'VenueStation' => htmlspecialchars( strip_tags( $_POST['VenueStation'] ) ),
				'TimeStart'    => $_POST['TimeStart'],
				'TimeEnd'      => $_POST['TimeEnd'],
				'scDate'       => date( 'Y-n-j', strtotime($_POST['scDate']) ),
				'CountPerson'  => $_POST['CountPerson'],
				'ContactTel'   => $_POST['ContactTel'],
				'ContactFax'   => $_POST['ContactFax'],
				'Course'       => $_POST['Course'],
				'Company'      => $_POST['Company'],
                'Product'      => $_POST['Product'],
                'SeminarFees'   => $_POST['SeminarFees'],
				'Note'         => htmlspecialchars( strip_tags( $_POST['Note'] ) ),
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
            $fileName = 'SEMINAR_A_EXCEL_' . $_FILES["file"]["name"];

            if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files';

                if (file_exists( $direct . '/' . $fileName )) {
                    unlink( $direct . '/' . $fileName );
                }
                move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $fileName );

                // Get sample name
                $conn = new Database;
                $stmt = $conn->conn->prepare("SELECT SampleDeadline FROM infoseminar_sample WHERE TypesId = 1");
				$stmt->execute();
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);

                // Get items
                $stmt = $conn->conn->prepare("SELECT ItemId, ItemCode FROM infoseminar_items");
				$stmt->execute();
                $item = $stmt->fetchAll(PDO::FETCH_ASSOC);

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
                $result = $model->uploadData( $fileName, $sample, $item );
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

        case 'FullSeminarAId':
            $model->updateColFull( $_POST['seminarId'] );
            echo 1;
            break;

        case 'loadClientList':
            $result = $model->getClientList( $_POST['AreaId'], $_POST['ProductId'] );
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleDeadline, SampleTaxChk FROM infoseminar_sample WHERE TypesId = 1' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            // Area
            $stmt = $conn->conn->prepare( 'SELECT AreaId, AreaName FROM infoseminar_areas' );
            $stmt->execute();
            $area = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Item
            $stmt = $conn->conn->prepare( 'SELECT ItemId, ItemName FROM infoseminar_items' );
            $stmt->execute();
            $item = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadClientList( $result, $sample, $area, $item ) );
            break;

        case 'full':
            $conn = new Database;
            $SeminarId = $_POST['SeminarId'];
            $stmt = $conn->conn->prepare( "SELECT CheckFull FROM infoseminar_sumary WHERE SeminarId = " . $SeminarId );
            $stmt->execute();
            $CheckFull = $stmt->fetch(PDO::FETCH_ASSOC)["CheckFull"];

            if ($CheckFull == 0) {
                $CheckFullVal = 1;
                $result['text'] = '満席取消';
            }
            else {
                $CheckFullVal = 0;
                $result['text'] = '満席';
            }


            $stmt = $conn->conn->prepare( "UPDATE infoseminar_sumary SET CheckFull = " . $CheckFullVal . " WHERE SeminarId = " . $SeminarId );
            $stmt->execute();

            $result['success'] = true;
            $result['btnFullId'] = $SeminarId;
            echo json_encode($result);
            break;

        case "showAttendSeminarA":
            $SeminarId = $_POST['SeminarId'] ?? '';

            $conn = new Database;
            $stmt = $conn->conn->prepare( "SELECT * FROM infoseminar_sumary WHERE SeminarId = " . $SeminarId );
            $stmt->execute();

            if ( $stmt->rowCount() < 1 ) {
                $showdata = '  <tr>
                                    <td align="center" style="height:150px; font:normal 100%/150% \'メイリオ\',Meiryo,sans-serif">対象となるセミナーはありません。</td>
                                </tr>
                                <tr>
                                    <td><font size="1">&nbsp;</font></td>
                                </tr>';
                goto Result;
            }
            $seminars = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($seminars);

            // Area
            if ( !empty( $AreaId ) ) {
                $stmt = $conn->conn->prepare( "SELECT AreaName FROM infoseminar_areas WHERE AreaId = " . $AreaId );
                $stmt->execute();
                $areaName = $stmt->fetch(PDO::FETCH_ASSOC)["AreaName"];
            }
            else {
                $areaName = "";
            }

            // Course
            if ( !empty( $SeminarClass3 ) ) {
                $stmt = $conn->conn->prepare( "SELECT ItemName FROM infoseminar_items WHERE ItemId = " . $SeminarClass3 );
                $stmt->execute();
                $courseName = $stmt->fetch(PDO::FETCH_ASSOC)["ItemName"];
            }
            else {
                $courseName = "";
            }

            // Product
            if ( !empty( $SeminarClass2 ) ) {
                $stmt = $conn->conn->prepare( "SELECT ItemName FROM infoseminar_items WHERE ItemId = " . $SeminarClass2 );
                $stmt->execute();
                $productName = $stmt->fetch(PDO::FETCH_ASSOC)["ItemName"];
            }
            else {
                $productName = "";
            }

            // Sample
            if ( !empty( $TypesId ) ) {
                $stmt = $conn->conn->prepare( "SELECT SampleTaxChk FROM infoseminar_sample WHERE TypesId = " . $TypesId );
                $stmt->execute();
                $SampleTaxChk = $stmt->fetch(PDO::FETCH_ASSOC)["SampleTaxChk"];
            }
            else {
                $SampleTaxChk = "";
            }
            $tax = ($SampleTaxChk == 1) ? '税込' : '税抜き';

            $showdata = '   <tr>
                                <th nowrap colspan="2" class="smnmida1">本セミナーについて（地域：'. $areaName .'）</th>
                            </tr>';

            if ( !empty( $_POST['isGet'] ) ) {
                $showdata .= '  <tr>
                                    <th nowrap class="smnlist1">セミナー名</th>
                                    <td class="smnlist1">'. $SeminarName .'</td>
                                </tr>';
            }
            else {
                $showdata .= '  <tr>
                                    <th nowrap class="smnlist1">対象製品</th>
                                    <td class="smnlist1">'. $productName .'</td>
                                </tr>';
            }

            $showdata .= '  <tr>
                                <th nowrap class="smnlist1">コース</th>
                                <td class="smnlist1">'. $courseName .'</td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">開催日時</th>
                                <td class="smnlist1nw">'. $Date .'　'. $TimeStart .'～'. $TimeEnd .'</td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">受講料（'. $tax .'）</th>
                                <td class="smnlist1">'. $SeminarFees .'円</td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">席数</th>
                                <td class="smnlist1">'. $CountPerson .'</td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">開催教室名</th>
                                <td class="smnlist1">'. $VenueName .'</td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">住所・最寄駅</th>
                                <td class="smnlist1">
                                    '. $VenueAddress;

            $showdata .= ($VenueMap != "") ? '[ <A href="'. $VenueMap .'" target="_blank">地図</a> ]' : '';
            $showdata .= ($VenueStation != "") ? '<div class="moyorieki">'. $VenueStation .'</div>' : '';

            $showdata .= '      </td>
                            </tr>
                            <tr>
                                <th nowrap class="smnlist1">会場連絡先</th>
                                <td class="smnlist1nw">[TEL] '. $ContactTel .'　　[FAX] '. $ContactFax .'</td>
                            </tr>';
            $showdata .= ($Note != "") ? ' <tr><th nowrap class="smnlist1">備考</font></th><td class="smnlist1">'. nl2br($Note) .'</td></tr>' : '';

            Result:
            echo json_encode( ["view" => $showdata] );
            break;

        case "sendClientMailA":
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

            // Area
            if ( !empty( $AreaId ) ) {
                $stmt = $conn->conn->prepare( "SELECT AreaName FROM infoseminar_areas WHERE AreaId = " . $AreaId );
                $stmt->execute();
                $areaName = $stmt->fetch(PDO::FETCH_ASSOC)["AreaName"];
            }
            else {
                $areaName = "";
            }

            // Course
            if ( !empty( $SeminarClass3 ) ) {
                $stmt = $conn->conn->prepare( "SELECT ItemName FROM infoseminar_items WHERE ItemId = " . $SeminarClass3 );
                $stmt->execute();
                $courseName = $stmt->fetch(PDO::FETCH_ASSOC)["ItemName"];
            }
            else {
                $courseName = "";
            }

            // Sample
            if ( !empty( $TypesId ) ) {
                $stmt = $conn->conn->prepare( "SELECT SampleTaxChk FROM infoseminar_sample WHERE TypesId = " . $TypesId );
                $stmt->execute();
                $SampleTaxChk = $stmt->fetch(PDO::FETCH_ASSOC)["SampleTaxChk"];
            }
            else {
                $SampleTaxChk = "";
            }
            $tax = ($SampleTaxChk == 1) ? '税込' : '税抜き';

            $subject = "【ソリマチセミナー申込】";

            $body = "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "　■セミナー情報" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "" . "\n";
            $body .= "　[セミナー名]　" . $SeminarName . "\n";
            $body .= "　[コース]　" . $courseName . "\n";
            $body .= "　[開催日時]　" . $Date . "　" . $TimeStart . "～" . $TimeEnd . "\n";
            $body .= "　[地域]　" . $areaName . "\n";
            $body .= "　[開催教室]　" . $VenueName . "\n";
            $body .= "　[会場住所]　" . $VenueAddress . "\n";
            $body .= "　[会場最寄駅]　" . $VenueStation . "\n";
            $body .= "　[会場連絡先]　TEL：" . $ContactTel . "　FAX：" . $ContactFax . "\n";
            $body .= "　[受講料（" . $tax . "）]　" . $SeminarFees . "\n";
            $body .= "　[席数]　" . $CountPerson . "\n";
            $body .= "　[備考]　" . $Note . "\n";
            $body .= "" . "\n";
            $body .= "" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "　■お客様情報" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "" . "\n";
            $body .= "　[会社名]　" . @$_POST["user_company"] . "\n";
            $body .= "　[部署名]　" . @$_POST["user_section"] . "\n";
            $body .= "　[受講者氏名]　" . @$_POST["user_name"] . "\n";
            $body .= "　[メールアドレス]　" . @$_POST["user_email"] . "\n";
            $body .= "　[住所]　" . @$_POST["user_postcode1"] . "-" . @$_POST["user_postcode2"] . "\n";
            $body .= "　　　　　　" . @$_POST["user_address"] . "\n";
            $body .= "　[電話番号]　" . @$_POST["user_tel"] . "\n";
            $body .= "　[FAX番号]　" . @$_POST["user_fax"] . "\n";
            $body .= "　[シリアルNo.]　" . @$_POST["user_serialno"] . "\n";

            global $MAILFROM_INFO_SEMINAR_A;
            global $MAILTO_INFO_SEMINAR_A;
            global $MAILBCC_INFO_SEMINAR_A;

            global $SMTP_SERVER_SORIMACHI_NAME;
            global $USER_SMTP_SERVER_SORIMACHI;
            global $PASS_SMTP_SERVER_SORIMACHI;

            $FromMail       = $MAILFROM_INFO_SEMINAR_A;
            $FromName       = '';
            $ToMail         = $MAILTO_INFO_SEMINAR_A;
            $EncriptionType = 3;
            $Host           = $SMTP_SERVER_SORIMACHI_NAME;
            $Port           = 587;
            $Username       = $USER_SMTP_SERVER_SORIMACHI;
            $Password       = $PASS_SMTP_SERVER_SORIMACHI;
            $checkSmtp      = 1;
            $multiEmail     = $MAILBCC_INFO_SEMINAR_A;

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