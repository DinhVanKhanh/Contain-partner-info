<?php
    class M_seminard extends Database {
        protected $table = 'infoseminar_sumary';
        protected $_prefix = '';

        public function getList() : array {
            $query = "SELECT sumary.*, todous.TodouhukenCode, todous.TodouhukenDisplay\n
                    FROM {$this->table} sumary LEFT JOIN infoseminar_todouhukens todous\n
                    ON todous.TodouhukenId = sumary.TodouhukenId\n
                    WHERE sumary.TypesId = 4\n
                    ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare("SELECT SeminarId, SeminarName, TodouhukenId, CompanyName, VenueName, VenueAddress, `Date`, TimeStart, TimeEnd, Note, ContactFax, ContactTel, CountPerson, SeminarFees, SeminarFees2Member, SeminarType, OrganizerURL, AppDate, PDF  FROM {$this->table} WHERE SeminarId = {$id}");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    
        public function add( Array $data ) : array  {
            try {
                $todouhukenId = $data['Todouhuken'];
                $seminarName  = $this->formatString( $data['SeminarName'] );
                $companyName  = $this->formatString( $data['CompanyName'] );
                $venueName    = $this->formatString( $data['VenueName'] );
                $venueAddress = $this->formatString( $data['VenueAddress'] );
                $contactTel   = preg_replace('/\-+/', '-', trim($data['ContactTel'],'-'));
                $contactFax   = preg_replace('/\-+/', '-', trim($data['ContactFax'],'-'));
                $scDate       = $data['scDate'];
                $timeStart    = $data['TimeStart'];
                $timeEnd      = $data['TimeEnd'];
                $appDate      = $data['AppDate'];
                $countPerson  = $data['CountPerson'];
                //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                $SeminarFees  = $data['SeminarFees'];
                $SeminarFees2Member  = $data['SeminarFees2Member'];
                $SeminarType  = $data['SeminarType'];
                $OrganizerURL  = $data['OrganizerURL'];
                //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>

                $curPdf       = $data['curPdf'];

                $note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
                $note = preg_replace('/&nbsp;/', ' ', $note);

                // Check record exists
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection> 
                // $stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
                //                                 WHERE SeminarName='{$seminarName}' AND CompanyName='{$companyName}' AND VenueName='{$venueName}'
                //                                 AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}'" );
                // $stmt->execute();
                // if ($stmt->rowCount() > 0) {
                //     throw new Exception('セミナー が既に存在しています。');
                // }
                if ($this->dataExists($data))
                {
                    throw new Exception('セミナー が既に存在しています。');
                }
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                // TH : PDF名をコーピー
                // Upload PDF
                $pdf = $curPdf;
                if ($data['file'] != null) {
                    if ($data['file']['error'] == 0) {
                        $upload = __DIR__ . '/../../../data_files/';
                        move_uploaded_file( $data['file']['tmp_name'], $upload . $this->_prefix . $curPdf );
                    }
                    else {
                        throw new Exception( 'PDFのアップロードに失敗しました' );
                    }
                }
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>   
                // $query = "INSERT INTO {$this->table}(SeminarName,CompanyName,TodouhukenId,ContactTel,ContactFax,VenueName,VenueAddress,`Date`,TimeStart,TimeEnd,CountPerson,Person,FormLink,CheckFull,Note,AppDate,TypesId %s) VALUES (" .
                //     "'{$seminarName}'," .
                //     "'{$companyName}'," .
                //     "{$todouhukenId}," .
                //     "'{$contactTel}'," .
                //     "'{$contactFax}'," .
                //     "'{$venueName}'," .
                //     "'{$venueAddress}'," .
                //     "'{$scDate}'," .
                //     "'{$timeStart}'," .
                //     "'{$timeEnd}'," .
                //     "{$countPerson}," .
                //     "0," .
                //     "1," .
                //     "0," .
                //     "'{$note}'," .
                //     "'{$appDate}'," .
                //     "4" .
                //     "%s" .
                // ")";
                // $query = empty($pdf) ? sprintf( $query, '', '' ) : sprintf( $query, ',PDF', ",'$pdf'" );
                $query = "INSERT INTO {$this->table}(SeminarName, CompanyName, TodouhukenId, ContactTel, ContactFax, VenueName, VenueAddress, `Date`, TimeStart, TimeEnd, CountPerson, SeminarFees, SeminarFees2Member, SeminarType, OrganizerURL, Person, FormLink, CheckFull, Note, AppDate, TypesId %s) VALUES (
                          :SeminarName,
                          :CompanyName,
                          :TodouhukenId,
                          :ContactTel,
                          :ContactFax,
                          :VenueName,
                          :VenueAddress,
                          :Date, 
                          :TimeStart,
                          :TimeEnd,
                          :CountPerson,
                          :SeminarFees,
                          :SeminarFees2Member,
                          :SeminarType,
                          :OrganizerURL,
                          0, 
                          1,
                          0,
                          :Note, 
                          :AppDate,
                          4 %s
                        )";
                $query = empty($pdf) ? sprintf( $query, '', '' ) : sprintf( $query, ',PDF', ',:PDF' );
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":SeminarName", $seminarName, PDO::PARAM_STR, 50);    
                $stmt->bindParam(":CompanyName", $companyName, PDO::PARAM_STR, 500 );
                $stmt->bindParam(":TodouhukenId", $todouhukenId, PDO::PARAM_INT, 11);
                $stmt->bindParam(":ContactTel", $contactTel, PDO::PARAM_STR, 20);
                $stmt->bindParam(":ContactFax", $contactFax, PDO::PARAM_STR, 20);
                $stmt->bindParam(":VenueName", $venueName, PDO::PARAM_STR, 1000);    
                $stmt->bindParam(":VenueAddress", $venueAddress, PDO::PARAM_STR, 1000);
                $stmt->bindParam(":Date", $scDate);
                $stmt->bindParam(":TimeStart", $timeStart, PDO::PARAM_STR, 12);
                $stmt->bindParam(":TimeEnd", $timeEnd, PDO::PARAM_STR, 12);
                $stmt->bindParam(":CountPerson", $countPerson, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarFees", $SeminarFees, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarFees2Member", $SeminarFees2Member, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarType", $SeminarType, PDO::PARAM_STR);
                $stmt->bindParam(":OrganizerURL", $OrganizerURL, PDO::PARAM_STR);
                $stmt->bindParam(":Note", $note, PDO::PARAM_STR, 500);
                $stmt->bindParam(":AppDate", $appDate);
                if (!empty($pdf))
                {
                    $stmt->bindParam(":PDF", $pdf, PDO::PARAM_STR, 500);
                }
                $stmt->execute();
                $rs['success'] = true;
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
            }
            catch (PDOException $e) {
                $rs['errMsg'] = '新しい失敗を追加';
                goto Result;
            }
            catch (Exception $e) {
                $rs['errMsg'] = $e->getMessage();
                goto Result;
            }

            Result:
            return $rs;
        }

        public function edit( Array $data ) : array {
            try {
                $todouhukenId = $data['Todouhuken'];
                $seminarName  = $this->formatString( $data['SeminarName'] );
                $companyName  = $this->formatString( $data['CompanyName'] );
                $venueName    = $this->formatString( $data['VenueName'] );
                $venueAddress = $this->formatString( $data['VenueAddress'] );
                $contactTel   = preg_replace('/\-+/', '-', trim($data['ContactTel'],'-'));
                $contactFax   = preg_replace('/\-+/', '-', trim($data['ContactFax'],'-'));
                $scDate       = $data['scDate'];
                $timeStart    = $data['TimeStart'];
                $timeEnd      = $data['TimeEnd'];
                $appDate      = $data['AppDate'];
                $countPerson  = $data['CountPerson'];
                //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                $SeminarFees  = $data['SeminarFees'];
                $SeminarFees2Member  = $data['SeminarFees2Member'];
                $SeminarType  = $data['SeminarType'];
                $OrganizerURL  = $data['OrganizerURL'];
                //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                // Person
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>       
                // $stmt = $this->conn->prepare( "SELECT Person FROM {$this->table} WHERE SeminarId=" . $data['id'] );
                $stmt = $this->conn->prepare( "SELECT Person FROM {$this->table} WHERE SeminarId= :SeminarId" );
                $stmt->bindParam(":SeminarId", $data['id'], PDO::PARAM_INT, 11);
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                $stmt->execute();
                $person = $stmt->fetch(PDO::FETCH_ASSOC)["Person"];
                if ( $countPerson > 0 && $person > $countPerson ) {
                    throw new Exception( $countPerson . '名が登録しています。' );
                }

                $oldPdf       = $data['oldPdf'];
                $curPdf       = $data['curPdf'];

                $note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
                $note = preg_replace('/&nbsp;/', ' ', $note);
            
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>  
                // Check record exists
                // $stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
                //                                 WHERE SeminarName='{$seminarName}' AND CompanyName='{$companyName}' AND VenueName='{$venueName}'
                //                                 AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}' AND SeminarId <> " . $data['id'] );
                // $stmt->execute();
                // if ($stmt->rowCount() > 0) {
                //     throw new Exception('セミナー が既に存在しています。');
                // }
            
                if ($this->dataExists($data, self::EDIT))
                {
                    throw new Exception('セミナー が既に存在しています。');
                }
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                // TH : PDF名をコーピー
                // Upload PDF
                $pdf = $oldPdf;
                if ( $data['file'] != null ) {
                    if ($data['file']['error'] == 0) {
                        $upload = __DIR__ . '/../../../data_files/';
                    //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>      
                        // $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE `PDF`='{$oldPdf}'");
                        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE `PDF`= :PDF");
                        $stmt->bindParam(":PDF", $oldPdf);
                    //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                        if ( !empty($oldPdf) && file_exists( $upload . $this->_prefix . $oldPdf ) && $row == 1 ) {
                            unlink( $upload . $this->_prefix . $oldPdf );
                        }
                        move_uploaded_file( $data['file']['tmp_name'], $upload . $this->_prefix . $curPdf );
                        $pdf = $curPdf;
                    }
                    else {
                        throw new Exception( 'PDFのアップロードに失敗しました' );
                    }
                }
                else {
                    if ( $data['deletePdf'] == 1 ) {
                        $pdf = $curPdf;
                    }
                }
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>  
                // $query = "UPDATE {$this->table} SET " .
                //     "SeminarName = '{$seminarName}'," .
                //     "CompanyName = '{$CompanyName}'," .
                //     "TodouhukenId = {$todouhukenId}," .
                //     "ContactTel = '{$contactTel}'," .
                //     "ContactFax = '{$contactFax}'," .
                //     "VenueName = '{$venueName}'," .
                //     "VenueAddress = '{$venueAddress}'," .
                //     "`Date` = '{$scDate}'," .
                //     "TimeStart = '{$timeStart}'," .
                //     "TimeEnd = '{$timeEnd}'," .
                //     "CountPerson = {$countPerson}," .
                //     "PDF = '{$pdf}'," .
                //     "Note = '{$note}'," .
                //     "AppDate = '{$appDate}'" .
                //     " WHERE SeminarId = " . $data['id'];
                // $this->conn->exec( $query );
                $query = "UPDATE {$this->table} SET 
                                SeminarName        = :SeminarName, 
                                CompanyName        = :CompanyName,
                                TodouhukenId       = :TodouhukenId,
                                ContactTel         = :ContactTel,
                                ContactFax         = :ContactFax,
                                VenueName          = :VenueName,
                                VenueAddress       = :VenueAddress,
                                `Date`             = :Date,
                                TimeStart          = :TimeStart,
                                TimeEnd            = :TimeEnd,
                                CountPerson        = :CountPerson,
                                SeminarFees        = :SeminarFees,
                                SeminarFees2Member = :SeminarFees2Member,
                                SeminarType        = :SeminarType,
                                OrganizerURL       = :OrganizerURL,
                                PDF                = :PDF,
                                Note               = :Note,
                                AppDate            = :AppDate
                        WHERE SeminarId = :SeminarId";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":SeminarName", $seminarName, PDO::PARAM_STR, 50);
                $stmt->bindParam(":CompanyName", $companyName, PDO::PARAM_STR, 500);
                $stmt->bindParam(":TodouhukenId", $todouhukenId, PDO::PARAM_INT, 11);
                $stmt->bindParam(":ContactTel", $contactTel, PDO::PARAM_STR, 20);
                $stmt->bindParam(":ContactFax", $contactFax, PDO::PARAM_STR, 20);
                $stmt->bindParam(":VenueName", $venueName, PDO::PARAM_STR, 1000);
                $stmt->bindParam(":VenueAddress", $venueAddress, PDO::PARAM_STR, 1000);
                $stmt->bindParam(":Date", $scDate);
                $stmt->bindParam(":TimeStart", $timeStart, PDO::PARAM_STR, 12);
                $stmt->bindParam(":TimeEnd", $timeEnd, PDO::PARAM_STR, 12);
                $stmt->bindParam(":CountPerson", $countPerson, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarFees", $SeminarFees, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarFees2Member", $SeminarFees2Member, PDO::PARAM_INT);
                $stmt->bindParam(":SeminarType", $SeminarType, PDO::PARAM_STR);
                $stmt->bindParam(":OrganizerURL", $OrganizerURL, PDO::PARAM_STR);
                $stmt->bindParam(":PDF", $pdf, PDO::PARAM_STR, 500);
                $stmt->bindParam(":Note", $note, PDO::PARAM_STR, 500);
                $stmt->bindParam(":AppDate", $appDate);
                $stmt->bindParam(":SeminarId", $data['id'], PDO::PARAM_INT, 11);
                $stmt->execute();
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                $rs['success'] = true;
            }
            catch (PDOException $e) {
                $rs['errMsg'] = '更新失敗';
                goto Result;
            }
            catch (Exception $e) {
                $rs['errMsg'] = $e->getMessage();
                goto Result;
            }

            Result:
            return $rs;
        }

        public function delete( int $id ) : string {
            try {
                // Check in todouhukens
                $stmt = $this->conn->prepare( "SELECT PDF FROM {$this->table} WHERE SeminarId = {$id}" );
                $stmt->execute();
                $PDF =  $stmt->fetch(PDO::FETCH_ASSOC)["PDF"];
                if ( $stmt->rowCount() > 0 ) {
                    if ( !empty( $PDF ) ) {
                        $file = __DIR__ . '/../../../data_files/' . $this->_prefix . $PDF;
                        if ( file_exists( $file ) ) {
                            unlink( $file );
                        }
                    }
                }

                $this->conn->exec( "DELETE FROM {$this->table} WHERE SeminarId = {$id}" );
                return '削除しました';
            }
            catch (PDOException $e) {
                return "削除失敗";
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        }

        public function uploadData( $inputFileName, $sample ) {
            $countErr = $countSuc = 0;
            $direct = __DIR__ . '/../../../data_files';

            // Check empty
            $checkEmpty = function ( $value ) {
                if ( preg_match('/^\s*$/', $value) ) {
                    return true;
                }
                return false;
            };

            try {
                $inputFileType = PhpOffice\PhpSpreadsheet\IOFactory::identify($direct . '/' . $inputFileName);
                $objReader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($direct . '/' . $inputFileName);

                $sheet = $objPHPExcel->getSheet(0);
                $file['highEstRow'] = $sheet->getHighestDataRow();
                if ( $file['highEstRow'] < 3 ) {
                    throw new Exception( '無効なファイル' );
                }

                for ($row = 3; $row <= $file['highEstRow']; $row++) {
                    $errMsg = "";
                    $data = array();

                    // TodouhukenDisplay
                    $data["TodouhukenDisplay"] = $sheet->getCell('A'. $row)->getFormattedValue();
                    if ( $checkEmpty( $data["TodouhukenDisplay"] ) ) {
                        $errMsg .= "開催地区は未入力です。";
                    }
                    elseif (mb_strlen($data["TodouhukenDisplay"]) > 50) {
                        $errMsg .= "開催地区を50文字以内で入力してください。";
                    }
                    else {
                    //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>   
                        //$TodouhukenId = $this->conn->prepare("SELECT `TodouhukenId` FROM `infoseminar_todouhukens` WHERE `TodouhukenDisplay`='" . $data["TodouhukenDisplay"] . "'");
                        $TodouhukenId = $this->conn->prepare("SELECT `TodouhukenId` FROM `infoseminar_todouhukens` WHERE `TodouhukenDisplay`= :TodouhukenDisplay");
                        $TodouhukenId->bindParam(":TodouhukenDisplay", $data["TodouhukenDisplay"], PDO::PARAM_STR);
                    //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>
                        $TodouhukenId->execute();
                        if ( $TodouhukenId->rowCount() > 0 ) {
                            $data["TodouhukenId"] = $TodouhukenId->fetch(PDO::FETCH_ASSOC)['TodouhukenId'];

                        }
                        else {
                            $errMsg .= '開催地区存在しません。';
                        }
                    }

                    // Seminar Name
                    $data["SeminarName"] = $sample['SampleName'];

                    // CompanyName
                    $data["CompanyName"] = $sheet->getCell('B' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["CompanyName"] ) ) {
                        $errMsg .= "スクールは未入力です。";
                    }
                    elseif (mb_strlen($data["CompanyName"]) > 500) {
                        $errMsg .= "スクールを500文字以内で入力してください。";
                    }

                    // VenueName
                    $data["VenueName"] = $sheet->getCell('C' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["VenueName"] ) ) {
                        $errMsg .= "会場名は未入力です。";
                    }
                    elseif (mb_strlen($data["VenueName"]) > 1000) {
                        $errMsg .= "開催会場名を1000文字以内で入力してください。";
                    }

                    // VenueAddress
                    $data["VenueAddress"] = $sheet->getCell('D' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["VenueAddress"] ) ) {
                        $errMsg .= "会場住所は未入力です。";
                    }
                    elseif (mb_strlen($data["VenueAddress"]) > 1000) {
                        $errMsg .= "開催会場住所を1000文字以内で入力してください。";
                    }

                    // ContactTel
                    $data["ContactTel"] = $sheet->getCell('E' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["ContactTel"] ) ) {
                        $test_tel_fax = str_replace( '-', '', $data["ContactTel"] );
                        if (!is_numeric($test_tel_fax) || ($test_tel_fax <= 0)) {
                            $errMsg .= "電話番号が有効ではない。";
                        }
                    }

                    // ContactFax
                    $data["ContactFax"] = $sheet->getCell('F' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["ContactFax"] ) ) {
                        $test_tel_fax = (int) str_replace( '-', '', $data["ContactFax"] );
                        if (!is_numeric($test_tel_fax) || ($test_tel_fax <= 0)) {
                            $errMsg .= "ファクス番号が有効ではない。";
                        }
                    }

                    // Date
                    $data["Date"] = $sheet->getCell('G' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["Date"] ) || strtotime( $data["Date"] ) == false ) {
                        $errMsg .= "開催日程は未入力です。";
                    }
                    else {
                        $data["Date"] = date('Y-n-j', strtotime($data["Date"]));
                        $day = substr( $data["Date"], strrpos( $data["Date"], '-' ) + 1 );
                    //↓↓　<2020/10/30> <YenNhi> <fix appdate>    
                        //date_add( $AppDate = date_create( $data["Date"] ), date_interval_create_from_date_string( ($day - $sample['SampleDeadline']) . ' day' ) );
                        $AppDate = date_sub( $AppDate = date_create( $data["Date"] ), date_interval_create_from_date_string( $sample['SampleDeadline'] . ' day' ) );
                    //↑↑　<2020/10/30> <YenNhi> <fix appdate>
                        $data["AppDate"] = $AppDate->format("Y-n-j");
                    }

                    // Time
                    $time = $sheet->getCell('H' . $row)->getFormattedValue();
                    $data["Time"] = preg_replace('/\s/', '', $time);
                    $data["Time"] = preg_replace('/\：/', ':', $data['Time']);
                    $data["Time"] = preg_replace('/～/', '~', $data['Time']);
                    if ( preg_match( '/^\d{1,2}\:\d{1,2}~\d{1,2}\:\d{1,2}$/', trim( $data["Time"] ) ) ) {
                        list($data["TimeStart"], $data["TimeEnd"]) = explode( '~', $data["Time"] );

                        // Time start
                        $arrTF = explode(":", $data["TimeStart"]);
                        $hF = $arrTF[0];
                        $mF = $arrTF[1];
                        if (strlen($hF) < 2) {
                            $hF = '0' . $hF;
                        }
                        elseif (strlen($mF) < 2) {
                            $mF = '0' . $mF;
                        }

                        // Time end
                        $arrTT = explode(":", $data["TimeEnd"]);
                        $hT = $arrTT[0];
                        $mT = $arrTT[1];
                        if (strlen($hT) < 2) {
                            $hT = '0' . $hT;
                        }
                        elseif (strlen($mT) < 2) {
                            $mT = '0' . $mT;
                        }

                        $ckTime = strtotime( $data['TimeStart'] ) <=> strtotime( $data['TimeEnd'] );
                        if ( $ckTime == 0 || $ckTime == 1 ) {
                            $errMsg .= "開始時間が有効ではない。";
                        }
                    }
                    else {
                        $errMsg .= '時間は未入力です。';
                    }

                    // CountPerson
                    $data["CountPerson"] = $sheet->getCell('I' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["CountPerson"] ) ) {
                        $errMsg .= "定員は未入力です。";
                    }
                    elseif (!is_numeric($data["CountPerson"]) || $data["CountPerson"] < 0) {
                        $errMsg .= "定員が有効ではない。";
                    }
                    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                    //SeminarType
                    $data["SeminarType"] = $sheet->getCell('J' . $row)->getFormattedValue();
                    if ($checkEmpty($data["SeminarType"])) {
                        $errMsg .= "開催形式は未入力です。";
                    }
                    elseif(!in_array($data["SeminarType"],["対面", "オンライン"]))
                        $errMsg .= "開催形式は未入力です。";

                    //SeminarFees
                    $data["SeminarFees"] = $sheet->getCell('K' . $row)->getFormattedValue();
                    $data["SeminarFees"] = str_replace(',','', $data["SeminarFees"]);
                    // if ($checkEmpty($data["SeminarFees"])) {
                    //     $errMsg .= "価格1は未入力です。";
                    // }
                    if (!$checkEmpty($data["SeminarFees"]) && (!is_numeric($data["SeminarFees"]) || $data["SeminarFees"] < 0))
                        $errMsg .= "価格1が有効ではない。";

                    //SeminarFees2Member
                    $data["SeminarFees2Member"] = $sheet->getCell('L' . $row)->getFormattedValue();
                    $data["SeminarFees2Member"] = str_replace(',', '', $data["SeminarFees2Member"]);
                    // if ($checkEmpty($data["SeminarFees2Member"])) {
                    //     $errMsg .= "価格2は未入力です。";
                    // }
                    if (!$checkEmpty($data["SeminarFees2Member"]) && (!is_numeric($data["SeminarFees2Member"]) || $data["SeminarFees2Member"] < 0))
                        $errMsg .= "価格2が有効ではない。";
                    // ↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>

                    // PDF
                    $data["PDF"] = $sheet->getCell('M' . $row)->getFormattedValue();
                    if(!empty($data["PDF"]) && !preg_match('/.pdf/', $data["PDF"])){
                        $data["PDF"] = $data["PDF"] . ".pdf";
                    }
                    //↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>
                    //OrganizerURL
                    $data["OrganizerURL"] = $sheet->getCell('N' . $row)->getFormattedValue();
                    //↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210826>

                    // Note
                    $data["Note"] = $sheet->getCell('O' . $row)->getFormattedValue();

                    if ( $checkEmpty( $errMsg ) ) {
                        if (($hF == $hT && $mF == $mT) || $hF > $hT) {
                            $errMsg .= "開始時間が有効ではない。";
                        }
                        else {
                            if ( !$checkEmpty( $hF ) || !$checkEmpty( $mF ) ) {
                                $TimeStart = $hF . ':' . $mF;
                            }

                            if ( !$checkEmpty( $hT ) || !$checkEmpty( $mT ) ) {
                                $TimeEnd = $hT . ':' . $mT;
                            }
                        }
                    }

                    if ( $checkEmpty( $errMsg ) ) {
                        // Check record exists
                    //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>   
                        // $stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
                        //                                 WHERE TypesId = 4 AND SeminarName='" . $data["SeminarName"] . "' AND CompanyName='" . $data["CompanyName"] . "' AND VenueName='" . $data["VenueName"] . "'
                        //                                 AND VenueAddress='" . $data["VenueAddress"] . "' AND `Date`='" . $data["Date"] . "' AND TimeStart='" . $data["TimeStart"] . "' AND TimeEnd='" . $data["TimeEnd"] . "'" );
                        $stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
                                                         WHERE TypesId = 4 AND SeminarName= :SeminarName AND CompanyName= :CompanyName AND VenueName= :VenueName
                                                         AND VenueAddress= :VenueAddress AND `Date`= :Date AND TimeStart= :TimeStart AND TimeEnd= :TimeEnd" );
                        $stmt->bindParam(":SeminarName", $data["SeminarName"], PDO::PARAM_STR, 50);
                        $stmt->bindParam(":CompanyName", $data["CompanyName"], PDO::PARAM_STR, 500);
                        $stmt->bindParam(":VenueName", $data["VenueName"], PDO::PARAM_STR, 1000);
                        $stmt->bindParam(":VenueAddress", $data["VenueAddress"], PDO::PARAM_STR, 1000);
                        $stmt->bindParam(":Date",  $data["Date"]);
                        $stmt->bindParam(":TimeStart", $data["TimeStart"], PDO::PARAM_STR, 12);
                        $stmt->bindParam(":TimeEnd", $data["TimeEnd"], PDO::PARAM_STR, 12);
                    //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>    
                        $stmt->execute();
                        if ($stmt->rowCount() > 0) {
                            $errMsg .= 'セミナー が既に存在しています。';
                        }
                        else {
                            $query  = "INSERT INTO {$this->table} (SeminarName,CompanyName,TodouhukenId,VenueName,VenueAddress,ContactTel,ContactFax,`Date`,TimeStart,TimeEnd,SeminarType,SeminarFees,SeminarFees2Member,CountPerson,FormLink,TypesId,Person,CheckFull,Note,PDF,OrganizerURL,AppDate)
                                    VALUES ('%s','%s',%d,'%s','%s','%s','%s','%s','%s','%s','%s',%d,%d,%d,%d,%d,%d,%d,'%s','%s','%s','%s')";
                            $stmt = $this->conn->prepare( sprintf( $query,
                                htmlspecialchars( strip_tags( $data["SeminarName"] ) ),
                                htmlspecialchars( strip_tags( $data["CompanyName"] ) ),
                                $data["TodouhukenId"],
                                htmlspecialchars( strip_tags( $data["VenueName"] ) ),
                                htmlspecialchars( strip_tags( $data["VenueAddress"] ) ),
                                $data["ContactTel"],
                                $data["ContactFax"],
                                $data["Date"],
                                $data["TimeStart"],
                                $data["TimeEnd"],
                                (string)$data["SeminarType"],
                                (int)$data["SeminarFees"],
                                (int)$data["SeminarFees2Member"],
                                $data["CountPerson"],
                                0,
                                4,
                                0,
                                0,
                                htmlspecialchars( strip_tags( $data["Note"] ) ),
                                $data["PDF"],
                                (string)$data["OrganizerURL"],
                                $data["AppDate"]
                            ) );
                            $stmt->execute();
                            if ( $stmt->rowCount() > 0 ) {
                                $countSuc++;
                            }
                            else {
                                $errMsg .= '挿入に失敗しました';
                            }
                        }
                    }

                    if ( !$checkEmpty( $errMsg ) ) {
                        $data['errMsg'] = $errMsg;
                        $data["Time"] = $time;
                        $errArg[$countErr] = $data;
                        $countErr++;
                    }
                }
            }
            catch(PDOException $e) {
                $result['errMsg'] = "エラー " . $e->getMessage();
            }
            catch(Exception $e) {
                $result['errMsg'] = "エラー " . $e->getMessage();
            }

            if ($countErr > 0) {
                $result['errFile'] = $this->exportError($errArg);
            }

            $result['numFailRows'] = $countErr;
            $result['numSuccess'] = $countSuc;
            return $result;
        }

        public function checkExistAll() {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} where TypesId = 4" );
            $stmt->execute();
            if ( $stmt->rowCount() > 0 ) {
                $mgs ='全てのセミナーを削除してよろしいでしょうか？';
                $btn ='<button name="btnDel_a" id="del_a" onclick="deleteAllSeminarD();" value="" class="btnDel btnDel_a">はい</button>
                    <a title="Close" id="btnCloseFc" class="btn btnClose" href="javascript:;" onclick=" $.fancybox.close();">いいえ</a>';
            }
            else {
                $mgs ='セミナーは存在しません。';
                $btn ='<a title="Close" id="btnCloseFc" class="btn btnClose" href="javascript:;" onclick=" $.fancybox.close();">閉じる</a>';
            }

            $result['msg']= $mgs;
            $result['btn']= $btn;
            return $result;
        }

        public function deleteAll() {
            $mgs ='全てのセミナーを削除してよろしいでしょうか？';
            $result['msg']= $mgs;

            // ↓↓　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210929-remove file pdf in source when don't use name SEMINAR_D_*>
            $stmt = $this->conn->prepare("SELECT PDF FROM {$this->table} where TypesId = 4 AND PDF <> ''");
            $stmt->execute();
            $PDF =  $stmt->fetchAll(PDO::FETCH_ASSOC); 
            if ($stmt->rowCount() > 0) {
                foreach ($PDF as $key => $value) {
                    $file = __DIR__ . '/../../../data_files/' . $this->_prefix . $value["PDF"];
                    if (file_exists($file)) {
                        unlink($file);
                    }
                }
            }
            // ↑↑　<2021/08/31> <VanKhanh> <【HP合同PRJ】info-seminarD_リニューアル資料_210929-remove file pdf in source when don't use name SEMINAR_D_*>
            
            // Delete all file excel error
            $upload = __DIR__ .'/../../../data_files/';
            // gc_collect_cycles();
            array_map('unlink', glob($upload . $this->_prefix . "SEMINAR_D_*"));

            $this->conn->exec( "DELETE FROM {$this->table} WHERE TypesId = 4" );
            $result['success'] = true;
            return $result;
        }

        public function getClientList() {
            $query = "SELECT *\n
                    FROM {$this->table} sumary ,infoseminar_todouhukens todous\n
                    WHERE sumary.TypesId = 4 AND sumary.TodouhukenId = todous.TodouhukenId\n
                    ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //↓↓　<2021/08/31> <VanKhanh> <show list area>
        public function getAreaList()
        {
            $query = "SELECT * FROM infoseminar_areas ORDER BY DisplayNo";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getArea($AreaCode)
        {
            if($AreaCode == 1000){
                $seminarType = 'オンライン';
                $query = "SELECT *
                            FROM infoseminar_sumary sumary ,infoseminar_todouhukens todous, infoseminar_areas areas
                            WHERE sumary.TypesId = 4
                                AND sumary.TodouhukenId = todous.TodouhukenId
                                AND todous.AreaId = areas.AreaId
                                AND sumary.SeminarType = :seminarType
                            ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":seminarType", $seminarType, PDO::PARAM_STR);
            }else{
                if ($AreaCode == "1")
                    $AreaCode = '1,2';
                elseif ($AreaCode == "5")
                    $AreaCode = '5,6';
                $query = "SELECT *
                            FROM infoseminar_sumary sumary ,infoseminar_todouhukens todous, infoseminar_areas areas
                            WHERE sumary.TypesId = 4
                                AND sumary.TodouhukenId = todous.TodouhukenId
                                AND todous.AreaId = areas.AreaId
                                AND areas.AreaCode IN ($AreaCode)
                            ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
                $stmt = $this->conn->prepare($query);
            }
            // $stmt->bindParam(":AreaCode", $AreaCode, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        //↑↑　<2021/08/31> <VanKhanh> <show list area>



        public function updateColFull( $seminarId ) {
        //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>
            //$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE SeminarId=" . $seminarId );
            $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE SeminarId= :SeminarId");
            $stmt->bindParam(":SeminarId", $seminarId, PDO::PARAM_INT, 11);
        //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>    
            $stmt->execute();
            if ( $stmt->rowCount() > 0 ) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result["CheckFull"] == 0 && $result["Person"] == 0) {
                    $result["CheckFull"] = $result["Person"] = 1;
                }
                elseif ($result["CheckFull"] == 1 && $result["Person"] == 1) {
                    $result["CheckFull"] = $result["Person"] = 0;
                //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>
                    //$this->conn->exec( "DELETE FROM infoseminar_customers WHERE SeminarId =" . $seminarId );
                    $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE SeminarId= :SeminarId");
                    $stmt->bindParam(":SeminarId", $seminarId, PDO::PARAM_INT, 11);
                    $stmt->execute();
                //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>    
                }
            //↓↓　<2020/10/07> <YenNhi> <avoid SQL injection>    
                //$this->conn->exec( "UPDATE {$this->table} SET CheckFull = " . $result["CheckFull"] . ",Person=" . $result["Person"] . " WHERE SeminarId = " . $seminarId );
                $query = $this->conn->prepare("UPDATE {$this->table} SET CheckFull = :CheckFull, Person = :Person WHERE SeminarId = :SeminarId");
                $query->bindParam(":CheckFull", $result["CheckFull"], PDO::PARAM_INT, 1);
                $query->bindParam(":Person", $result["Person"], PDO::PARAM_INT, 10);
                $query->bindParam(":SeminarId", $seminarId, PDO::PARAM_INT, 11);
                $query->execute();
            //↑↑　<2020/10/07> <YenNhi> <avoid SQL injection>        
            }
            return true;
        }

        private function exportError($data) {
            $objReader = PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
            $workSheet = $objReader->load( __DIR__ . "/../../template-report/errSeminarDImport.xlsx");
            $sheet = $workSheet->getSheet(0);

            $i = 3;
            foreach ( $data as $value ) {
                $sheet->setCellValue( 'A' . $i, $value["TodouhukenDisplay"] );
                $sheet->setCellValue( 'B' . $i, $value["CompanyName"] );
                $sheet->setCellValue( 'C' . $i, $value["VenueName"] );
                $sheet->setCellValue( 'D' . $i, $value["VenueAddress"] );
                $sheet->setCellValue( 'E' . $i, $value["ContactTel"] );
                $sheet->setCellValue( 'F' . $i, $value["ContactFax"] );
                $sheet->setCellValue( 'G' . $i, $value["Date"] );
                $sheet->setCellValue( 'H' . $i, $value["Time"] );
                $sheet->setCellValue( 'I' . $i, $value["CountPerson"] );
                $sheet->setCellValue( 'J' . $i, $value["SeminarType"] );
            // $sheet->setCellValueExplicit( 'K' . $i, $value["SeminarFees"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            // $sheet->setCellValueExplicit( 'L' . $i, $value["SeminarFees2Member"], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
                $sheet->setCellValue('K' . $i, $value["SeminarFees"]);
                $sheet->setCellValue('L' . $i, $value["SeminarFees2Member"]);
                $sheet->setCellValue('M' . $i, $value["PDF"]);
                $sheet->setCellValue('N' . $i, $value["OrganizerURL"]);
                $sheet->setCellValue('O' . $i, $value["Note"]);
                $sheet->setCellValue('P' . $i, $value["errMsg"]);
                $i++;
            }

            $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($workSheet);
            $outPut = $this->_prefix . 'SEMINAR_D_ERROR_' . date('His') . '.xlsx';
            $writer->save( __DIR__ . '/../../../data_files/' . $outPut );
            // return '../data_files/' . $outPut;
            return ($data);
        }

        private function formatString( $text ) {
            $string = preg_replace('/\s+/',' ', trim($text));
            $string = preg_replace('/　+/','^', $string);
            $string = preg_replace('/\^+/','　', $string);
            return $string;
        }

        // Check data exists or not
        private function dataExists( Array $data, $flag = self::ADD ) {
            try {
                extract($data);

			//　↓↓　＜2020/11/04＞　＜VinhDao＞　＜修正＞
                // $query = "SELECT SeminarName as total FROM {$this->table}\n
                //             WHERE SeminarName= :SeminarName AND CompanyName= :CompanyName AND VenueName= :VenueName
                //             AND VenueAddress= :VenueAddress AND `Date`= :Date AND TimeStart = :TimeStart AND TimeEnd = :TimeEnd";
                // if ( $flag == self::EDIT ) {
                //     $query .= " AND SeminarId <> :SeminarId";
                //     $stmt = $this->conn->prepare( $query );
                //     $stmt->bindParam(':SeminarId', $id, PDO::PARAM_INT, 11);
                // }
                // else {
                //     $stmt = $this->conn->prepare( $query );
				// }
			
                // $stmt->bindParam(':SeminarName', $SeminarName, PDO::PARAM_STR, 50);
                // $stmt->bindParam(':CompanyName', $CompanyName, PDO::PARAM_STR, 500);
                // $stmt->bindParam(':VenueName', $VenueName, PDO::PARAM_STR, 1000);
                // $stmt->bindParam(':VenueAddress', $VenueAddress, PDO::PARAM_STR, 1000);
                // $stmt->bindParam(':Date', $scDate);
                // $stmt->bindParam(':TimeStart', $TimeStart, PDO::PARAM_STR, 12);
                // $stmt->bindParam(':TimeEnd', $TimeEnd, PDO::PARAM_STR, 12);
                // $stmt->execute();
				// return $stmt->rowCount() > 0 ? true : false;

				$query = "SELECT TimeStart FROM {$this->table}\n
                            WHERE SeminarName = :SeminarName AND CompanyName = :CompanyName AND VenueName = :VenueName
							AND VenueAddress = :VenueAddress AND `Date`= :Date AND %s\n
							ORDER BY TimeEnd ASC LIMIT 1";

				// Check TimeFrom
				if ( $flag == self::EDIT ) {
                    $query1 = sprintf($query, 'TimeEnd >= :TimeStart AND SeminarId <> :SeminarId');
                    $stmt = $this->conn->prepare( $query1 );
                    $stmt->bindParam(':SeminarId', $id, PDO::PARAM_INT, 11);
                }
                else {
					$query1 = sprintf($query, 'TimeEnd >= :TimeStart');
                    $stmt = $this->conn->prepare( $query1 );
				}

				$stmt->bindParam(':SeminarName', $SeminarName, PDO::PARAM_STR, 50);
                $stmt->bindParam(':CompanyName', $CompanyName, PDO::PARAM_STR, 500);
                $stmt->bindParam(':VenueName', $VenueName, PDO::PARAM_STR, 1000);
                $stmt->bindParam(':VenueAddress', $VenueAddress, PDO::PARAM_STR, 1000);
                $stmt->bindParam(':Date', $scDate, PDO::PARAM_STR);
                $stmt->bindParam(':TimeStart', $TimeStart, PDO::PARAM_STR, 12);
				$stmt->execute();
				
				if ( $stmt->rowCount() > 0 ) {
                    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ( strtotime( $rs['TimeStart'] ) <= strtotime( $TimeStart ) ) {
                        return true;
                    }
                }
				
				// Check Time End
				if ( $flag == self::EDIT ) {
                    $query2 = sprintf($query, 'TimeEnd >= :TimeEnd AND SeminarId <> :SeminarId');
                    $stmt = $this->conn->prepare( $query2 );
                    $stmt->bindParam(':SeminarId', $id, PDO::PARAM_INT, 11);
                }
                else {
					$query2 = sprintf($query, 'TimeEnd >= :TimeEnd');
                    $stmt = $this->conn->prepare( $query2 );
				}

				$stmt->bindParam(':SeminarName', $SeminarName, PDO::PARAM_STR, 50);
                $stmt->bindParam(':CompanyName', $CompanyName, PDO::PARAM_STR, 500);
                $stmt->bindParam(':VenueName', $VenueName, PDO::PARAM_STR, 1000);
                $stmt->bindParam(':VenueAddress', $VenueAddress, PDO::PARAM_STR, 1000);
                $stmt->bindParam(':Date', $scDate, PDO::PARAM_STR);
                $stmt->bindParam(':TimeEnd', $TimeEnd, PDO::PARAM_STR, 12);
				$stmt->execute();
				
				if ( $stmt->rowCount() > 0 ) {
                    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ( strtotime( $rs['TimeStart'] ) <= strtotime( $TimeEnd ) ) {
                        return true;
                    }
                }
			//　↑↑　＜2020/11/04＞　＜VinhDao＞　＜修正＞
            }
            catch ( PDOException $e ) {
                return $e->getMessage();
            }
        }
    }
