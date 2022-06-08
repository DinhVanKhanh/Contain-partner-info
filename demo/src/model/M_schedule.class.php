<?php
    class M_schedule extends Database {
		protected $table = 'infodemo_schedules';

		public function getList() : array {
			$query = "SELECT DISTINCT mp.TodouhukenId, mp.Tel, mp.storeName1, mp.storeName2, td.TodouhukenName, shops.Name, scd.*\n
                    FROM infodemo_todouhukens td, infodemo_meetingplaces mp, {$this->table} scd, infodemo_shops shops\n
                    WHERE mp.TodouhukenId = td.TodouhukenId AND (scd.MeetingPlaceId = mp.MeetingPlaceId AND scd.ShopId = shops.ShopId)\n
                    ORDER BY scd.Date";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE scheduleId = {$id}"  );
            $stmt->execute();
            $result['schedule'] = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( $stmt->rowCount() > 0 ) {
                $stmt = $this->conn->prepare( "SELECT IsSpecial FROM infodemo_shops WHERE ShopId = " . $result['schedule']["ShopId"]  );
                $stmt->execute();
                $result['isSpecialShop'] = $stmt->fetch(PDO::FETCH_ASSOC)["IsSpecial"];

                if ( !empty( $result['schedule']["Pdf"] ) ) {
                    $result['servUrl'] = '../data_files/';
                }
            }
			return $result;
		}

		public function add( Array $data ) : array  {
            try {
                $rs["success"] = false;

                // Check record exists
                if ( $this->dataExists($data) ) {
                    throw new Exception( "本スケジュールは既に存在しています。" );
                }

                extract($data);

                if ( file_exists( $file["tmp_name"] ) ) {
                    if ( $file['error'] == 0 ) {
                        $upload = __DIR__ . '/../../../data_files/';

                        if ( !empty( $curPdf ) && !empty( $file["name"] ) ) {
                            $file_ext = mb_substr( $curPdf, mb_strrpos( $curPdf, "." ) + 1 );
                            $file_ext = strtolower($file_ext);

                            if ( $file_ext != 'pdf' ) {
                                throw new Exception( "PDFファイルが有効ではない。" );
                            }
                            elseif ( !file_exists( $upload . $curPdf ) ) {
                                throw new Exception( "指定されたPDFファイルが存在しません" );
                            }
                        }

                        $inputFileName = $file["name"];
                        $dirFile = $upload . $this->_prefix . $inputFileName;

                        if ( file_exists( $dirFile ) ) {
                            unlink( $dirFile );
                        }

                        if ( !move_uploaded_file( $file["tmp_name"], $dirFile ) ) {
                            throw new Exception( 'ファイルのアップロード時にエラーが発生しました' );
                        }
                    }
                    else {
                        throw new Exception( "PDFファイルのアップロードのエラーであります。" );
                    }
                }

                $query = "INSERT INTO {$this->table}( `ShopId`, `MeetingPlaceId`, `Date`, `TimeFrom`, `TimeTo`, `Description`, `IsActive`, `IsHighlight`, `Pdf` )" .
                        " VALUES ( :ShopId, :MeetingPlaceId, :Date, :TimeFrom, :TimeTo, :Description, :IsActive, :IsHighlight, :Pdf )";

                $Pdf = !empty( $data["file"] ) ? $curPdf : null;
                $Date = date( 'Y-m-d', strtotime( $Date ) );

                $stmt = $this->conn->prepare( $query );
                $stmt->bindParam(':ShopId', $ShopId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':MeetingPlaceId', $MeetingPlaceId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Date', $Date, PDO::PARAM_STR);
                $stmt->bindParam(':TimeFrom', $TimeFrom, PDO::PARAM_STR, 5);
                $stmt->bindParam(':TimeTo', $TimeTo, PDO::PARAM_STR, 5);
                $stmt->bindParam(':Description', $Description, PDO::PARAM_STR, 500);
                $stmt->bindParam(':IsActive', $IsActive, PDO::PARAM_INT, 1);
                $stmt->bindParam(':IsHighlight', $IsHighlight, PDO::PARAM_INT, 1);
                $stmt->bindParam(':Pdf', $Pdf, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->execute();
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

		public function edit( Array $data ) : array {
            try {
                $rs["success"] = false;

                // Check record exists
                if ( $this->dataExists($data, self::FLAG_EDIT) ) {
                    throw new Exception( "本スケジュールは既に存在しています。" );
                }

                extract($data);

                if ( file_exists( $file["tmp_name"] ) ) {
                    if ( $file['error'] == 0 ) {
                        $upload = __DIR__ . '/../../../data_files/';

                        if ( !empty( $curPdf ) && empty( $file["name"] ) ) {
                            $file_ext = mb_substr( $curPdf, mb_strrpos( $curPdf, "." ) );
                            $file_ext = strtolower($file_ext);

                            if ( $file_ext != "pdf" ) {
                                throw new Exception(  "PDFファイルが有効ではない。" );
                            }
                            elseif ( !file_exists( $upload . $curPdf ) ) {
                                throw new Exception( "指定されたPDFファイルが存在しません" );
                            }
                        }

                        $inputFileName = $file["name"];
                        $dirFile = $upload . $this->_prefix . $inputFileName;

                        if ( file_exists( $dirFile ) ) {
                            unlink( $dirFile );
                        }

                        if ( empty( $curPdf ) && !empty( $oldPdf ) ) {
                            $stmt = $this->conn->prepare( $Conn, "SELECT ShopId FROM {$this->table} WHERE `Pdf`='" . $oldPdf . "'" );
                            $stmt->execute();
                            if ( $stmt->rowCount() > 0 ) {
                                unlink( $upload . $this->_prefix . $oldPdf );
                            }
                        }
                        move_uploaded_file( $file["tmp_name"], $dirFile );
                    }
                    else {
                        throw new Exception( "PDFファイルのアップロードのエラーであります。" );
                    }
                }

                $query = "UPDATE {$this->table} SET " .
                            "`ShopId` = :ShopId" .
                            ",`MeetingPlaceId` = :MeetingPlaceId" .
                            ",`Date` = :Date" .
                            ",`TimeFrom` = :TimeFrom" .
                            ",`TimeTo` = :TimeTo" .
                            ",`Description` = :Description" .
                            ",`IsActive` = :IsActive" .
                            ",`IsHighlight` = :IsHighlight" .
                            ",`Pdf` = :Pdf" .
                            " WHERE `ScheduleId` = :ScheduleId";
                $Pdf = !empty( $data["file"] ) ? $curPdf : null;
                $Date = date( 'Y-m-d', strtotime( $Date ) );

                $stmt = $this->conn->prepare( $query );
                $stmt->bindParam(':ScheduleId', $ScheduleId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':ShopId', $ShopId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':MeetingPlaceId', $MeetingPlaceId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Date', $Date, PDO::PARAM_STR);
                $stmt->bindParam(':TimeFrom', $TimeFrom, PDO::PARAM_STR, 5);
                $stmt->bindParam(':TimeTo', $TimeTo, PDO::PARAM_STR, 5);
                $stmt->bindParam(':Description', $Description, PDO::PARAM_STR, 500);
                $stmt->bindParam(':IsActive', $IsActive, PDO::PARAM_INT, 1);
                $stmt->bindParam(':IsHighlight', $IsHighlight, PDO::PARAM_INT, 1);
                $stmt->bindParam(':Pdf', $Pdf, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->execute();
                
                $rs['success'] = true;
            }
            catch (PDOException $e) {
                $rs['errMsg'] = '更新失敗' . $e->getMessage();
                goto Result;
            }
            catch (Exception $e) {
                $rs['errMsg'] = $e->getMessage();
                goto Result;
            }

			Result:
			return $rs;
		}

		public function delete( $idList ) : string {
            try {
                $query = "SELECT Pdf, COUNT( Pdf )\n
                        FROM {$this->table}\n
                        WHERE ScheduleId IN ({$idList}) AND (Pdf IS NOT NULL AND Pdf <> '')\n
                        GROUP BY Pdf\n
                        HAVING COUNT(Pdf) = 1";

                $stmt = $this->conn->prepare( $query );
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ( $stmt->rowCount() > 0 ) {
                    foreach ( $result as $value ) {
                        $upload = __DIR__ . '/../../../data_files/' . $this->_prefix . $value["Pdf"];
                        if ( file_exists( $upload ) ) {
                            unlink( $upload );
                        }
                    }
                }
                $this->conn->exec( "DELETE FROM {$this->table} WHERE ScheduleId IN ({$idList})" );
                return "削除しました";
            }
            catch (PDOException $e) {
              return "削除失敗";
            }
            catch (Exception $e) {
              return $e->getMessage();
            }
		}

		// Fitler record by area
		public function filterByArea( int $id ) : array {
            $query = "SELECT DISTINCT mp.TodouhukenId, mp.Tel, mp.storeName1, mp.storeName2, td.TodouhukenName, shops.Name, scd.*\n
                  FROM infodemo_todouhukens td, infodemo_meetingplaces mp, {$this->table} scd, infodemo_shops shops\n
                  WHERE td.areaId = {$id} AND mp.TodouhukenId = td.TodouhukenId AND (scd.MeetingPlaceId = mp.MeetingPlaceId AND scd.ShopId = shops.ShopId)\n
                  ORDER BY scd.Date";
            $stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function uploadData( $inputFileName ) {
            $countErr = $countSuc = 0;

            // Check empty
            $checkEmpty = function ( $value ) {
				if ( preg_match('/^\s*$/', $value) ) {
					return true;
				}
				return false;
            };

            try {
                $inputFileType = PhpOffice\PhpSpreadsheet\IOFactory::identify( $inputFileName );
                $objReader     = PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objPHPExcel   = $objReader->load( $inputFileName );

                $sheet = $objPHPExcel->getSheet(0);
                $file['highEstRow'] = $sheet->getHighestDataRow();
                if ( $file['highEstRow'] < 2 ) {
                    throw new Exception( '無効なファイル' );
        				}

                for ($row = 2; $row <= $file['highEstRow']; $row++) {
                    $errMsg = "";
                    $data = array();

                    // Meeting place code
                    $mtCode = $sheet->getCell('A' . $row)->getFormattedValue();
                    if ( $checkEmpty( $mtCode ) ) {
                        $errMsg .= "会場は未入力です。";
                    }
                    else {
                        $stmt = $this->conn->prepare( "SELECT MeetingPlaceId FROM infodemo_meetingplaces WHERE UPPER(Code) = '" . strtoupper( trim( $mtCode ) ) . "'" );
                        $stmt->execute();
                        if ( $stmt->rowCount() < 1 ) {
                            $errMsg .= $mtCode . " 会場が存在しません。";
                        }
                        else {
                            $data['MeetingPlaceId'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['MeetingPlaceId'];
                        }
                    }

                    // Shop code
                    $shopCode = $sheet->getCell('B' . $row)->getFormattedValue();
                    if ( $checkEmpty( $shopCode ) ) {
                        $errMsg .= "販売店は未入力です。";
                    }
                    else {
                        $stmt = $this->conn->prepare( "SELECT ShopId FROM infodemo_shops WHERE UPPER(Code) = '" . strtoupper( trim( $shopCode ) ) . "'" );
                        $stmt->execute();
                        if ( $stmt->rowCount() < 1 ) {
                            $errMsg .= $shopCode . " 販売店が存在しません。";
                        }
                        else {
                            $data['ShopId'] = (int) $stmt->fetch(PDO::FETCH_ASSOC)['ShopId'];
                        }
                    }

                    // Date
                    $data['Date'] = $sheet->getCell('C' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["Date"] ) || strtotime( $data["Date"] ) == false ) {
                        $errMsg .= "開催日程は未入力です。";
                    }
                    else {
                        $data['Date'] = date( 'Y-m-d', strtotime( $data['Date'] ) );
                    }

                    // TimeFrom
                    $timeFrom = $sheet->getCell('D' . $row)->getFormattedValue();
                    $data["TimeFrom"] = preg_replace('/\s/', '', $timeFrom);
                    $data["TimeFrom"] = preg_replace('/\：/', ':', $data['TimeFrom']);
                    if ( $checkEmpty( $data["TimeFrom"] ) ) {
                        $errMsg .= "開始時間は未入力です。";
                    }
                    elseif ( !preg_match( '/^\d{1,2}\:\d{1,2}$/', $data['TimeFrom'] ) ) {
                        $errMsg .= "開始時間が有効ではない。";
                    }
                    else {
                        list($hF, $mF) = explode(":", $data["TimeFrom"]);
                        if (strlen($hF) < 2) {
                            $hF = '0' . $hF;
                        }
                        elseif (strlen($mF) < 2) {
                            $mF = '0' . $mF;
                        }
                    }

                    // TimeTo
                    $timeTo = $sheet->getCell('E' . $row)->getFormattedValue();
                    $data["TimeTo"] = preg_replace('/\s/', '', $timeTo);
                    $data["TimeTo"] = preg_replace('/\：/', ':', $data['TimeTo']);
                    if ( $checkEmpty( $data["TimeTo"] ) ) {
                        $errMsg .= "終了時間は未入力です。";
                    }
                    elseif ( !preg_match( '/^\d{1,2}\:\d{1,2}$/', $data['TimeTo'] ) ) {
                        $errMsg .= "終了時間が有効ではない。";
                    }
                    else {
                        list($hT, $mT) = explode(":", $data["TimeTo"]);
                        if (strlen($hT) < 2) {
                            $hT = '0' . $hT;
                        }
                        elseif (strlen($mT) < 2) {
                            $mT = '0' . $mT;
                        }
                    }

                    if ( !empty( $hF ) && !empty( $ht ) ) {
                        $ckTime = strtotime( $data['TimeFrom'] ) <=> strtotime( $data['TimeTo'] );
                        if ( $ckTime == 0 || $ckTime == 1 ) {
                            $errMsg .= "開始時間が有効ではない。";
                        }
                    }

                    // Description
                    $data['Description'] = $sheet->getCell('F' . $row)->getFormattedValue();
                    if ( strlen( $data['Description'] ) > 1000 ) {
                        $errMsg .= "備考を1000文字以内で入力してください。";
                    }

                    // IsActive
                    $strIsActive = $sheet->getCell('G' . $row)->getFormattedValue();
                    $data['IsActive']    = ( !preg_match( '/^\s*$/', $strIsActive ) && strcmp( trim($strIsActive), 'あり') == 0 ) ? 1 : 0;

                    // IsHighlight
                    $strIsHighlight = $sheet->getCell('H' . $row)->getFormattedValue();
                    $data['IsHighlight'] = ( !preg_match( '/^\s*$/', $strIsActive ) && strcmp( trim($strIsHighlight), 'あり' ) == 0 ) ? 1 : 0;

                    if ( $checkEmpty( $errMsg ) ) {
                        if ( $this->dataExists($data) ) {
                            $errMsg .= "本スケジュールは既に存在しています。";
                        }
                        else {
                            $query = "INSERT INTO {$this->table}(`ShopId`, `MeetingPlaceId`, `Date`, `TimeFrom`, `TimeTo`, `Description`, `IsActive`, `IsHighlight`)
                                    VALUE ( :ShopId, :MeetingPlaceId, :Date, :TimeFrom, :TimeTo, :Description, :IsActive, :IsHighlight);";

                            $Description = htmlspecialchars( strip_tags( $data['Description'] ) );
                            $stmt = $this->conn->prepare($query);
                            $stmt->bindParam(':ShopId', $data['ShopId'], PDO::PARAM_INT, 11);
                            $stmt->bindParam(':MeetingPlaceId', $data['MeetingPlaceId'], PDO::PARAM_INT, 11);
                            $stmt->bindParam(':Date', $data['Date'], PDO::PARAM_STR);
                            $stmt->bindParam(':TimeFrom', $data['TimeFrom'], PDO::PARAM_STR, 5);
                            $stmt->bindParam(':TimeTo', $data['TimeTo'], PDO::PARAM_STR, 5);
                            $stmt->bindParam(':Description', $Description, PDO::PARAM_STR, 500);
                            $stmt->bindParam(':IsActive', $data['IsActive'], PDO::PARAM_INT, 1);
                            $stmt->bindParam(':IsHighlight', $data['IsHighlight'], PDO::PARAM_INT, 1);
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
                        $data['MeetingPlaceId'] = $mtCode;
                        $data['ShopId'] = $shopCode;
                        $data["TimeTo"] = $timeTo;
                        $data["TimeFrom"] = $timeFrom;
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

            if ( $countErr > 0 ) {
                $result['errFile'] = $this->exportError( $errArg );
            }

            $result['numFailRows'] = $countErr;
            $result['numSuccess'] = $countSuc;
            return $result;
        }

        // Export error file for import data
        function exportError( Array $data ) : string {
            $objReader = PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
            $workSheet = $objReader->load( __DIR__ . "/../../template-report/errImport.xlsx" );
            $sheet = $workSheet->getSheet(0);

            $i = 2;
            foreach ( $data as $value ) {
                $sheet->setCellValue( 'A' . $i, $value['MeetingPlaceId'] );
                $sheet->setCellValue( 'B' . $i, $value['ShopId'] );
                $sheet->setCellValue( 'C' . $i, $value['Date'] );
                $sheet->setCellValue( 'D' . $i, $value['TimeFrom'] );
                $sheet->setCellValue( 'E' . $i, $value['TimeTo'] );
                $sheet->setCellValue( 'F' . $i, htmlspecialchars( $value['Description'] ) );
                $sheet->setCellValue( 'G' . $i, $value['IsActive'] == 0 ? 'あり' : 'なし' );
                $sheet->setCellValue( 'H' . $i, $value['IsHighlight'] == 0 ? 'あり' : 'なし' );
                $sheet->setCellValue( 'I' . $i, $value["errMsg"] );
                $i++;
            }

            $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($workSheet);
            $outPut = $this->_prefix . 'ERROR_SCHEDULE_' . date('Ymd') . '.xlsx';
            $writer->save( __DIR__ . '/../../../data_files/' . $outPut );
            return '../data_files/' . $outPut;
        }

        public function getListSpecialShop() {
            $query = "SELECT ShopId, Name FROM infodemo_shops WHERE IsSpecial = 1";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getListNormalShop() {
            $query = "SELECT ShopId, Name FROM infodemo_shops WHERE IsSpecial = 0";
      			$stmt = $this->conn->prepare( $query );
      			$stmt->execute();
      			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        private function dataExists( Array $data, $flag = self::FLAG_ADD ) {
             try {
				extract($data);

                $query = 'SELECT TimeFrom FROM ' . $this->table . ' WHERE MeetingPlaceId = :MeetingPlaceId' .
                            ' AND ShopId = :ShopId' .
                            ' AND `Date` = :Date AND %s' .
                            ' ORDER BY TimeTo ASC LIMIT 1';

                if ( $flag == self::FLAG_EDIT ) {
                    $query1 = sprintf( $query, 'TimeTo >= :TimeFrom AND ScheduleId <> :ScheduleId' );
                    $stmt = $this->conn->prepare( $query1 );
                    $stmt->bindParam(':ScheduleId', $ScheduleId, PDO::PARAM_INT, 11);
                }
                else {
                    $query1 = sprintf( $query, 'TimeTo >= :TimeFrom' );
                    $stmt = $this->conn->prepare( $query1 );
                }

                $stmt->bindParam(':MeetingPlaceId', $MeetingPlaceId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':ShopId', $ShopId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Date', $Date, PDO::PARAM_STR);
                $stmt->bindParam(':TimeFrom', $TimeFrom, PDO::PARAM_STR, 5);
                $stmt->execute();

                if ( $stmt->rowCount() > 0 ) {
                    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ( strtotime( $rs['TimeFrom'] ) <= strtotime( $TimeFrom ) ) {
                        return true;
                    }
                }

                if ( $flag == self::FLAG_EDIT ) {
                    $query3 = sprintf( $query, 'TimeTo >= :TimeTo AND ScheduleId <> :ScheduleId' );
                    $stmt = $this->conn->prepare( $query3 );
                    $stmt->bindParam(':ScheduleId', $ScheduleId, PDO::PARAM_INT, 11);
                }
                else {
                    $query3 = sprintf( $query, 'TimeTo >= :TimeTo' );
                    $stmt = $this->conn->prepare( $query3 );
                }

                $stmt->bindParam(':MeetingPlaceId', $MeetingPlaceId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':ShopId', $ShopId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Date', $Date, PDO::PARAM_STR);
                $stmt->bindParam(':TimeTo', $TimeTo, PDO::PARAM_STR, 5);
                $stmt->execute();

                if ( $stmt->rowCount() > 0 ) {
                    $rs = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ( strtotime( $rs['TimeFrom'] ) <= strtotime( $TimeTo ) ) {
                        return true;
                    }
                }

                return false;
			}
			catch ( PDOException $e ) {
				return $e->getMessage();
			}
        }
    }
?>
