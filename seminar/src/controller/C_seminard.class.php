<?php
    require_once __DIR__ . '/../model/M_seminard.class.php';
    require_once __DIR__ . '/../view/V_seminard.class.php';
    require_once __DIR__ . "/../../libs/mailer.class.php";

    $model = new M_seminard;
    $view  = new V_seminard;

    switch ( $_POST['action'] ) {
        case 'loadList':
            $result = $model->getList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 4' );
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
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 4' );
            $stmt->execute();
			$result['sample'] = $stmt->fetch(PDO::FETCH_ASSOC)['SampleAppMonth'];
            echo json_encode( $result );
			break;

		case 'getSample':
			$conn = new Database;

            // Sample
			$stmt = $conn->conn->prepare( 'SELECT * FROM infoseminar_sample WHERE TypesId = 4' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            $result['today'] = date('Y-n-j');
            $today = date_create();
        //↓↓　<2020/10/30> <YenNhi> <fix appdate in add modal>
            //$day = intval( date("j") ) - intval( $sample['SampleDeadline'] );
            //date_add( $today, date_interval_create_from_date_string( $day . ' day' ) );
            date_sub( $today, date_interval_create_from_date_string( $sample['SampleDeadline'] . ' day' ) );
        //↑↑　<2020/10/30> <YenNhi> <fix appdate in add modal>
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
                //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                'SeminarFees'  => $_POST['SeminarFees'],
                'SeminarFees2Member'  => $_POST['SeminarFees2Member'],
                'SeminarType'  => $_POST['SeminarType'],
                'OrganizerURL'  => $_POST['OrganizerURL'],
                //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
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
                //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                'SeminarFees'  => $_POST['SeminarFees'],
                'SeminarFees2Member'  => $_POST['SeminarFees2Member'],
                'SeminarType'  => $_POST['SeminarType'],
                'OrganizerURL' => $_POST['OrganizerURL'],
                //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
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
            $fileName = 'SEMINAR_D_EXCEL_' . $_FILES["file"]["name"];

            if ($_FILES['file']['error'] == 0) {
                $direct = __DIR__ . '/../../../data_files';
                if (file_exists( $direct . '/' . $fileName )) {
                    unlink( $direct . '/' . $fileName );
                }
			    move_uploaded_file( $_FILES["file"]["tmp_name"], $direct . '/' . $fileName );

                // Get sample name
                $conn = new Database;
                $stmt = $conn->conn->prepare("SELECT SampleName, SampleDeadline FROM infoseminar_sample WHERE TypesId = 4");
				$stmt->execute();
                $sample = $stmt->fetch(PDO::FETCH_ASSOC);

                require_once __DIR__ . '/../../libs/spreadsheet/vendor/autoload.php';
                $result = $model->uploadData( $fileName, $sample );
            }
            else {
                $result['errMsg'] = "エラーがある";
            }

            unlink( $direct . '/' . $fileName );
            echo json_encode( $result );
            break;

        case 'uploadMultiPdf':
            if ($_POST['image_form_submit'] == 1) {
                $images_arr = array();
                $err = array();
                $errMsg = $alert = "";
                if(!empty($_FILES['pdf']['name'])){
                    foreach ($_FILES['pdf']['name'] as $key => $val) {
                        $image_name = $_FILES['pdf']['name'][$key];
                        $size 		= $_FILES["pdf"]["size"][$key];
                        $tmp_name 	= $_FILES['pdf']['tmp_name'][$key];
    
                        $target_dir  = __DIR__ . '/../../../data_files';
                        $target_file = $target_dir . '/' . $_FILES['pdf']['name'][$key];
    
                        if ( !preg_match( "/\.pdf$/i", $image_name ) ) {
                            echo "PDFファイルが有効ではない。";
                        }
                        elseif ($size > 2000000) {
                            echo "PDFファイルのサイズが２ＭＢを超過しないでください。";
                        }
                        else {
                            $conn = new Database;
                            $stmt = $conn->conn->prepare("SELECT PDF FROM infoseminar_sumary WHERE TypesId = 4 ORDER BY PDF");
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
                                $alert = 'データが存在していません。';
                            }
                        }
                    }
                }


                if (!empty($err)) {
                    $alert = 'データベースの中に';
                    for ($j = 0; $j < count($err); $j++) {
                        $alert .= ' [ ' . $err[$j] . ' ] ';
                    }
                    $alert .= 'が存在していません。';
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

        case 'FullSeminarDId':
            $model->updateColFull( $_POST['seminarId'] );
            echo 1;
            break;

        case 'loadClientList':
            $result = $model->getClientList();
            $conn = new Database;

            // Sample
            $stmt = $conn->conn->prepare( 'SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 4' );
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode( $view->loadClientList( $result, $sample ) );
            break;
        //↓↓　<2021/08/31> <VanKhanh> <show list area>
        case "loadAreaList":
            $result = $model->getAreaList();
            echo json_encode($view->getAreaList($result));
            break;

        case "loadArea":
            $AreaCode = @$_POST['AreaCode'];
            $conn = new Database;
            // Sample
            $stmt = $conn->conn->prepare('SELECT SampleAppMonth FROM infoseminar_sample WHERE TypesId = 4');
            $stmt->execute();
            $sample = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($AreaCode == 0){
                $result = $model->getClientList();
                echo json_encode($view->loadClientList($result, $sample));
                break;
            }
            $result = $model->getArea($AreaCode);
            echo json_encode($view->loadClientList($result,$sample));
            break;
        //↑↑　<2021/08/31> <VanKhanh> <show list area>
        case "showAttendSeminarD":
            $SeminarId = @$_POST['SeminarId'];

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

        //　↓↓　＜2020/09/22＞　＜VinhDao＞　＜No.2のHP合同PRJ-info_seminar-Check_200922＞
            // Area
            // if ( !empty( $AreaId ) ) {
            //     $stmt = $conn->conn->prepare( "SELECT AreaName FROM infoseminar_areas WHERE AreaId = " . $AreaId );
            //     $stmt->execute();
            //     $areaName = $stmt->fetch(PDO::FETCH_ASSOC)["AreaName"];
            // }
            // else {
            //     $areaName = "";
            // }

            // Todouhuken
            if ( !empty( $TodouhukenId ) ) {
                $stmt = $conn->conn->prepare( "SELECT TodouhukenName FROM infoseminar_todouhukens WHERE TodouhukenId = " . $TodouhukenId );
                $stmt->execute();
                $TodouhukenName = $stmt->fetch(PDO::FETCH_ASSOC)["TodouhukenName"];
            }
            else {
                $TodouhukenName = "";
            }
        //　↑↑　＜2020/09/22＞　＜VinhDao＞　＜No.2のHP合同PRJ-info_seminar-Check_200922＞

            $showdata = '
                        <tr>
                            <th nowrap colspan="2" class="smnmida1">給料王 年末調整セミナー</th>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">都道府県</th>
                            <td class="smnlist1">'. $TodouhukenName .'</td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">スクール</th>
                            <td class="smnlist1">'. $CompanyName .'</td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">開催会場</th>
                            <td class="smnlist1">
                                '. $VenueName .'<br>
                                '. $VenueAddress .'<br>';

            $showdata .= ($VenueMap != "") ? '[ <A href="'. $VenueMap .'" target="_blank">地図</a> ]' : '';
            $showdata .= '
                                [TEL] '. $ContactTel .'　　[FAX] '. $ContactFax .'
                            </td>
                        </tr>
                        <tr>
                            <th nowrap class="smnlist1">開催日時</th>
                            <td class="smnlist1nw">'. $Date .'　'. $TimeStart .'～'. $TimeEnd .'</td>
                        </tr>
                        <tr>
                        <th nowrap class="smnlist1">席数</th>
                            <td class="smnlist1">'. $CountPerson .'</td>
                        </tr>';

            Result:
            echo json_encode( ["view" => $showdata] );
            break;

        case "sendClientMailD":
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

        //　↓↓　＜2020/09/22＞　＜VinhDao＞　＜No.2のHP合同PRJ-info_seminar-Check_200922＞
            // Area
            // if ( !empty( $AreaId ) ) {
            //     $stmt = $conn->conn->prepare( "SELECT AreaName FROM infoseminar_areas WHERE AreaId = " . $AreaId );
            //     $stmt->execute();
            //     $areaName = $stmt->fetch(PDO::FETCH_ASSOC)["AreaName"];
            // }
            // else {
            //     $areaName = "";
            // }

            // Todouhuken
            if ( !empty( $TodouhukenId ) ) {
                $stmt = $conn->conn->prepare( "SELECT TodouhukenName FROM infoseminar_todouhukens WHERE TodouhukenId = " . $TodouhukenId );
                $stmt->execute();
                $TodouhukenName = $stmt->fetch(PDO::FETCH_ASSOC)["TodouhukenName"];
            }
            else {
                $TodouhukenName = "";
            }
        //　↑↑　＜2020/09/22＞　＜VinhDao＞　＜No.2のHP合同PRJ-info_seminar-Check_200922＞
        //↓↓　<2020/10/15> <YenNhi> <avoid using @ symbol>
            $user_company   = $_POST["user_company"] ?? '';
            $user_name      = $_POST["user_name"] ?? '';
            $user_postcode1 = $_POST["user_postcode1"] ?? '';
            $user_postcode2 = $_POST["user_postcode2"] ?? '';
            $user_address   = $_POST["user_address"] ?? '';
            $user_tel       = $_POST["user_tel"] ?? '';
            $user_fax       = $_POST["user_fax"] ?? '';
            $user_email     = $_POST["user_email"] ?? '';
        //↑↑　<2020/10/15> <YenNhi> <avoid using @ symbol>
            $subject = "【給料王 年末調整セミナー申込】";

            $body  = "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "　■給料王 年末調整セミナー情報" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "\n";
            $body .= " 【給料王 年末調整セミナー】" . "\n";
            $body .= "\n";
            $body .= "　[地域]　" . $TodouhukenName . "\n";
            $body .= "　[開催スクール]　" . $CompanyName . "\n";
            $body .= "　[開催会場]　" . $VenueName . "\n";
            $body .= "　[会場住所]　" . $VenueAddress . "\n";
            $body .= "　[会場連絡先]　TEL：" . $ContactTel . "　FAX：" . $ContactFax . "\n";
            $body .= "　[開催日]　" . $Date . "\n";
            $body .= "　[開催時間]　" . $TimeStart . "～" . $TimeEnd . "\n";
            $body .= "　[定員]　" . $CountPerson . "\n";
            $body .= "\n";
            $body .= "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "　■お客様情報" . "\n";
            $body .= "==========================================================================" . "\n";
            $body .= "\n";
        //↓↓　<2020/10/15> <YenNhi> <avoid using @ symbol>
            // $body .= "　[会社名]　" . @$_POST["user_company"] . "\n";
            // $body .= "　[担当者名]　" . @$_POST["user_name"] . "\n";
            // $body .= "　[ご住所]　" . @$_POST["user_postcode1"] . "-" . @$_POST["user_postcode2"] . "\n";
            // $body .= "　　　　　　" . @$_POST["user_address"] . "\n";
            // $body .= "　[TEL]　" . @$_POST["user_tel"] . "\n";
            // $body .= "　[FAX]　" . @$_POST["user_fax"] . "\n";
            // $body .= "　[E-Mail]　" . @$_POST["user_email"] . "\n";
            $body .= "　[会社名]　" . $user_company . "\n";
            $body .= "　[担当者名]　" . $user_name . "\n";
            $body .= "　[ご住所]　" . $user_postcode1 . "-" . $user_postcode2 . "\n";
            $body .= "　　　　　　" . $user_address . "\n";
            $body .= "　[TEL]　" . $user_tel . "\n";
            $body .= "　[FAX]　" . $user_fax . "\n";
            $body .= "　[E-Mail]　" . $user_email . "\n";
        //↑↑　<2020/10/15> <YenNhi> <avoid using @ symbol>
            global $MAILFROM_INFO_SEMINAR_D;
            global $MAILTO_INFO_SEMINAR_D;
            global $MAILBCC_INFO_SEMINAR_D;

            global $SMTP_SERVER_SORIMACHI_NAME;
            global $USER_SMTP_SERVER_SORIMACHI;
            global $PASS_SMTP_SERVER_SORIMACHI;

            //↓↓　<2020/10/07> <YenNhi> <config mail>
            // $FromMail       = $MAILFROM_INFO_SEMINAR_D;
            // $FromName       = '';
            // $ToMail         = $MAILTO_INFO_SEMINAR_D;
            // $EncriptionType = 3;
            // $Host           = $SMTP_SERVER_SORIMACHI_NAME;
            // $Port           = 587;
            // $Username       = $USER_SMTP_SERVER_SORIMACHI;
            // $Password       = $PASS_SMTP_SERVER_SORIMACHI;
            // $checkSmtp      = 1;
            // $multiEmail     = $MAILBCC_INFO_SEMINAR_D;
            $stmt = $conn->conn->prepare("SELECT EmailId, Host, Port, Username, Password, FromEmail, FromName, EncriptionType, im.SampleId, sp.SampleEmail FROM infoseminar_mail im join (SELECT SampleId, SampleEmail FROM infoseminar_sample is2 WHERE TypesId = 4) sp on sp.SampleId = im.SampleId");
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            $ToMail         = $user_email;
            $checkSmtp      = 1;
            $multiEmail     = $MAILBCC_INFO_SEMINAR_D; //bcc mail
            
            $err = sendmail1($Host, $Port, $EncriptionType, $checkSmtp, $Username, $Password, $FromMail, $FromName, $ToMail, $subject, $body, $multiEmail);
            //↑↑　<2020/10/07> <YenNhi> <config mail>
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