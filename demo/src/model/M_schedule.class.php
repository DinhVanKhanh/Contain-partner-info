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
                if ( file_exists( $data["file"]["tmp_name"] ) ) {
                    if ( $data["file"]['error'] == 0 ) {
                        $upload = __DIR__ . '/../../../data_files/';

                        if ( !empty( $data["curPdf"] ) && empty( $data["file"]["name"] ) ) {
                            $file_ext = substr( $data["curPdf"], strrpos( $data["curPdf"], "." ) );

                            if ( strcasecmp( $file_ext, "pdf" ) != 0 ) {
                                throw new Exception( "PDFファイルが有効ではない。" );
                            }
                            elseif ( !file_exists( $upload . $curPdf ) ) {
                                throw new Exception( "指定されたPDFファイルが存在しません" );
                            }
                        }

                        $inputFileName = $data["file"]["name"];
                        $dirFile = $upload . $this->_prefix . $inputFileName;

                        if ( file_exists( $dirFile ) ) {
                            unlink( $dirFile );
                        }
                        move_uploaded_file( $data["file"]["tmp_name"], $dirFile );
                    }
                    else {
                        throw new Exception( "PDFファイルのアップロードのエラーであります。" );
                    }
                }

                // Check record exists
                $query = "SELECT ScheduleId FROM {$this->table} WHERE MeetingPlaceId = " . $data['meetingPlaceId'] .
                                            " AND ShopId = " . $data['shopId'] .
                                            " AND `Date` = '" . date( 'Y-m-d', strtotime( trim( $data['scDate'] ) ) ) . "'" .
                                            " AND (TimeFrom = '" . trim( $data['scFTime'] ) . "'" .
                                            " AND TimeTo = '" . trim( $data['scTTime'] ) . "')";
                $stmt = $this->conn->prepare( $query );
                $stmt->execute();
                if ( $stmt->rowCount() > 0 ) {
                    throw new Exception( "本スケジュールは既に存在しています。" );
                }

                $query = "INSERT INTO {$this->table}( `ShopId`, `MeetingPlaceId`, `Date`, `TimeFrom`, `TimeTo`, `Description`, `IsActive`, `IsHighlight`, `Pdf` )" .
                        " VALUES (" .
                            $data["shopId"] . ',' .
                            $data["meetingPlaceId"] . ',' .
                            "'" . date( "Y-m-d", strtotime( $data["scDate"] ) ) . "'," .
                            "'" . $data["scFTime"] . "'," .
                            "'" . $data["scTTime"] . "'," .
                            "'" . $data["scDescript"] . "'," .
                            $data["isActive"] . ',' .
                            $data["isHighLight"] . ',' .
                            "'" . ( $data["file"] != null ? $data["curPdf"] : "" ) . "'"
                        . ")";
                $this->conn->exec( $query );
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
                if ( file_exists( $data["file"]["tmp_name"] ) ) {
                    if ( $data["file"]['error'] == 0 ) {
                        $upload = __DIR__ . '/../../../data_files/';

                        if ( !empty( $data["curPdf"] ) && empty( $data["file"]["name"] ) ) {
                            $file_ext = substr( $data["curPdf"], strrpos( $data["curPdf"], "." ) );

                            if ( strcasecmp( $file_ext, "pdf" ) != 0 ) {
                                throw new Exception(  "PDFファイルが有効ではない。" );
                            }
                            elseif ( !file_exists( $upload . $curPdf ) ) {
                                throw new Exception( "指定されたPDFファイルが存在しません" );
                            }
                        }

                        $inputFileName = $data["file"]["name"];
                        $dirFile = $upload . $this->_prefix . $inputFileName;

                        if ( file_exists( $dirFile ) ) {
                            unlink( $dirFile );
                        }

                        if ( empty( $data["curPdf"] ) && !empty( $data["oldPdf"] ) ) {
                            $stmt = $this->conn->prepare( $Conn, "SELECT ShopId FROM {$this->table} WHERE `Pdf`='" . $oldPdf . "'" );
                            $stmt->execute();
                            if ( $stmt->rowCount() > 0 ) {
                                unlink( $upload . $this->_prefix . $oldPdf );
                            }
                        }
                        move_uploaded_file( $data["file"]["tmp_name"], $dirFile );
                    }
                    else {
                        throw new Exception( "PDFファイルのアップロードのエラーであります。" );
                    }
                }

                // Check record exists
                $query = "SELECT ScheduleId FROM {$this->table} WHERE MeetingPlaceId = " . $data['meetingPlaceId'] .
                        " AND ShopId = " . $data['shopId'] .
                        " AND `Date` = '" . date( 'Y-m-d', strtotime( $data['scDate'] ) ) . "'" .
                        " AND (TimeFrom = '" . $data['scFTime'] . "'" .
                        " AND TimeTo = '" . $data['scTTime'] . "')" .
                        " AND ScheduleId <> " . $data['id'];
                $stmt = $this->conn->prepare( $query );
                $stmt->execute();
                if ( $stmt->rowCount() > 0 ) {
                    throw new Exception( "本スケジュールは既に存在しています。" );
                }

                $query = "UPDATE {$this->table} SET " .
                        "`ShopId` = " . $data["shopId"] . "," .
                        "`MeetingPlaceId` = " . $data["meetingPlaceId"] . "," .
                        "`Date` = '" . date( "Y-m-d", strtotime( $data["scDate"] ) ) . "'," .
                        "`TimeFrom` = '" . $data["scFTime"] . "'," .
                        "`TimeTo` = '" . $data["scTTime"] . "'," .
                        "`Description` = '" . $data["scDescript"] . "'," .
                        "`isActive` = " . $data["isActive"] . "," .
                        "`isHighLight` = " . $data["isHighLight"] . "," .
                        "`Pdf` = '" . $data["curPdf"] . "'"  .
                        " WHERE `scheduleId` = " . $data["id"];
                $this->conn->exec( $query );
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
                            $data['meetingPlaceId'] = $stmt->fetch(PDO::FETCH_ASSOC)['MeetingPlaceId'];
                        }
                    }

                    // Shop code
                    $shopCode = $sheet->getCell('B' . $row)->getFormattedValue();
                    if ( $checkEmpty( $shopCode ) ) {
			                  $errMsg .= "販売店は未入力です。";
                    }
                    else {
                        $stmt = $this->conn->prepare( "SELECT shopId FROM infodemo_shops WHERE UPPER(Code) = '" . strtoupper( trim( $shopCode ) ) . "'" );
                        $stmt->execute();
                        if ( $stmt->rowCount() < 1 ) {
                            $errMsg .= $shopCode . " 販売店が存在しません。";
                        }
                        else {
                            $data['shopId'] = $stmt->fetch(PDO::FETCH_ASSOC)['shopId'];
                        }
                    }

                    // Date
                    $data['scDate'] = $sheet->getCell('C' . $row)->getFormattedValue();
                    if ( $checkEmpty( $data["scDate"] ) || strtotime( $data["scDate"] ) == false ) {
			                  $errMsg .= "開催日程は未入力です。";
                    }
                    else {
                        $data['scDate'] = date( 'Y-m-d', strtotime( $data['scDate'] ) );
                    }

                    // TimeFrom
                    $timeFrom = $sheet->getCell('D' . $row)->getFormattedValue();
                    $data["scFTime"] = preg_replace('/\s/', '', $timeFrom);
	                  $data["scFTime"] = preg_replace('/\：/', ':', $data['scFTime']);
                    if ( $checkEmpty( $data["scFTime"] ) ) {
                        $errMsg .= "開始時間は未入力です。";
                    }
                    elseif ( !preg_match( '/^\d{1,2}\:\d{1,2}$/', $data['scFTime'] ) ) {
                        $errMsg .= "開始時間が有効ではない。";
                    }
                    else {
                        list($hF, $mF) = explode(":", $data["scFTime"]);
                        if (strlen($hF) < 2) {
                            $hF = '0' . $hF;
                        }
                        elseif (strlen($mF) < 2) {
                            $mF = '0' . $mF;
                        }
                    }

                    // TimeTo
                    $timeTo = $sheet->getCell('E' . $row)->getFormattedValue();
                    $data["scTTime"] = preg_replace('/\s/', '', $timeTo);
                    $data["scTTime"] = preg_replace('/\：/', ':', $data['scTTime']);
                    if ( $checkEmpty( $data["scTTime"] ) ) {
                        $errMsg .= "終了時間は未入力です。";
                    }
                    elseif ( !preg_match( '/^\d{1,2}\:\d{1,2}$/', $data['scTTime'] ) ) {
                        $errMsg .= "終了時間が有効ではない。";
                    }
                    else {
                        list($hT, $mT) = explode(":", $data["scTTime"]);
                        if (strlen($hT) < 2) {
                            $hT = '0' . $hT;
                        }
                        elseif (strlen($mT) < 2) {
                            $mT = '0' . $mT;
                        }
                    }

                    if ( !empty( $hF ) && !empty( $ht ) ) {
                        $ckTime = strtotime( $data['scFTime'] ) <=> strtotime( $data['scTTime'] );
                        if ( $ckTime == 0 || $ckTime == 1 ) {
                            $errMsg .= "開始時間が有効ではない。";
                        }
                    }

                    // Description
                    $data['scDescript'] = $sheet->getCell('F' . $row)->getFormattedValue();
                    if ( strlen( $data['scDescript'] ) > 1000 ) {
                        $errMsg .= "備考を1000文字以内で入力してください。";
                    }

                    // IsActive
                    $strIsActive = $sheet->getCell('G' . $row)->getFormattedValue();
                    $data['isActive']    = ( !preg_match( '/^\s*$/', $strIsActive ) && strcmp( trim($strIsActive), 'あり') == 0 ) ? 1 : 0;

                    // IsHighlight
                    $strIsHighlight = $sheet->getCell('H' . $row)->getFormattedValue();
                    $data['isHighLight'] = ( !preg_match( '/^\s*$/', $strIsActive ) && strcmp( trim($strIsHighlight), 'あり' ) == 0 ) ? 1 : 0;

                    if ( $checkEmpty( $errMsg ) ) {
                        $sqlCheck = "SELECT ScheduleId FROM {$this->table} WHERE MeetingPlaceId = " . intval( trim( $data['meetingPlaceId'] ) ) .
                                " AND ShopId = " . intval( trim( $data['shopId'] ) ) .
                                " AND `Date` = '" . date( 'Y-m-d', strtotime( trim( $data['scDate'] ) ) ) . "'" .
                                " AND (TimeFrom = '" . trim( $data['scFTime'] ) . "'" .
                                " AND TimeTo = '" . trim( $data['scTTime'] ) . "')";
                        $stmt = $this->conn->prepare( $sqlCheck );
                        $stmt->execute();
                        if ( $stmt->rowCount() > 0 ) {
                            $errMsg .= "本スケジュールは既に存在しています。";
                        }
                        else {
                            $query = "INSERT INTO {$this->table}( `ShopId`, `MeetingPlaceId`, `Date`, `TimeFrom`, `TimeTo`, `Description`, `IsActive`, `IsHighlight`)
                                    VALUE (" . $data["shopId"] . "," .
                                        $data["meetingPlaceId"] . "," .
                                        "'" . $data["scDate"] . "'," .
                                        "'" . $data['scFTime'] . "'," .
                                        "'" . $data['scTTime'] . "'," .
                                        "'" . htmlentities( strip_tags( $data['scDescript'] ) ) . "'," .
                                        $data['isActive'] . "," .
                                        $data['isHighLight'] .
                                    ");";
                            $result['query'] = $query;
                            $stmt = $this->conn->prepare( $query );
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
                        $data['meetingPlaceId'] = $mtCode;
                        $data['shopId'] = $shopCode;
                        $data["scTTime"] = $timeTo;
                        $data["scFTime"] = $timeFrom;
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
                $sheet->setCellValue( 'A' . $i, $value['meetingPlaceId'] );
                $sheet->setCellValue( 'B' . $i, $value['shopId'] );
                $sheet->setCellValue( 'C' . $i, $value['scDate'] );
                $sheet->setCellValue( 'D' . $i, $value['scFTime'] );
                $sheet->setCellValue( 'E' . $i, $value['scTTime'] );
                $sheet->setCellValue( 'F' . $i, htmlspecialchars( $value['scDescript'] ) );
                $sheet->setCellValue( 'G' . $i, $value['isActive'] == 0 ? 'あり' : 'なし' );
                $sheet->setCellValue( 'H' . $i, $value['isHighLight'] == 0 ? 'あり' : 'なし' );
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
    }
?>
