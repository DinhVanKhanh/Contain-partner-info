<?php
	class M_seminarb extends Database {
        protected $table = 'infoseminar_sumary';
        protected $_prefix = 'SEMINAR_B_';

        public function getList() : array {
            $query = "SELECT su.*, area.AreaName\n
                    FROM {$this->table} su, infoseminar_areas area\n
                    WHERE su.TypesId = 2 AND area.AreaId = su.AreaId\n
                    ORDER BY YEAR(su.Date) DESC, MONTH(su.Date) DESC, DAY(su.DATE) DESC, su.AreaId, su.VenueName, su.VenueAddress, su.TimeStart";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT SeminarId, SeminarName, AreaId, VenueName, VenueAddress, VenueMap, `Date`, TimeStart, TimeEnd, AppDate FROM {$this->table} WHERE SeminarId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
				$areaId       = $data['Area'];
				$seminarName  = $this->formatString( $data['SeminarName'] );
				$venueName    = $this->formatString( $data['VenueName'] );
				$venueAddress = $this->formatString( $data['VenueAddress'] );
				$venueMap     = $this->formatString( $data['VenueMap'] );
				$scDate       = $data['scDate'];
				$timeStart    = $data['TimeStart'];
				$timeEnd      = $data['TimeEnd'];
				$appDate      = $data['AppDate'];

				// Check record exists
				$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
												WHERE SeminarName='{$seminarName}' AND AreaId='{$areaId}' AND VenueName='{$venueName}'
												AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}'" );
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					throw new Exception('セミナー が既に存在しています。');
				}

				$query = "INSERT INTO {$this->table}(SeminarName,AreaId,VenueName,VenueAddress,VenueMap,`Date`,TimeStart,TimeEnd,AppDate,FormLink,Person,CheckFull,TypesId) VALUES (" .
					"'{$seminarName}'," .
					$areaId . "," .
					"'{$venueName}'," .
					"'{$venueAddress}'," .
					"'{$venueMap}'," .
					"'{$scDate}'," .
					"'{$timeStart}'," .
					"'{$timeEnd}'," .
					"'{$appDate}'," .
					"1," .
					"0," .
					"0," .
					"2" .
				")";

                $this->conn->exec( $query );
                $rs['success'] = true;
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
				$areaId       = $data['Area'];
				$seminarName  = $this->formatString( $data['SeminarName'] );
				$venueName    = $this->formatString( $data['VenueName'] );
				$venueAddress = $this->formatString( $data['VenueAddress'] );
				$venueMap     = $this->formatString( $data['VenueMap'] );
				$scDate       = $data['scDate'];
				$timeStart    = $data['TimeStart'];
				$timeEnd      = $data['TimeEnd'];
				$appDate      = $data['AppDate'];

				// Check record exists
				$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
												WHERE SeminarName='{$seminarName}' AND AreaId='{$areaId}' AND VenueName='{$venueName}'
												AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}' AND SeminarId <> " . $data['id'] );
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					throw new Exception('セミナー が既に存在しています。');
				}

				$query = "UPDATE {$this->table} SET " .
							"SeminarName='{$seminarName}'," .
							"AreaId={$areaId}," .
							"VenueName='{$venueName}'," .
							"VenueAddress='{$venueAddress}'," .
							"VenueMap='{$venueMap}'," .
							"`Date`='{$scDate}'," .
							"TimeStart='{$timeStart}'," .
							"TimeEnd='{$timeEnd}'," .
							"AppDate='{$appDate}' WHERE SeminarId = " . $data['id'];

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

        public function delete( int $id ) : string {
            try {
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
				if ( $file['highEstRow'] < 2 ) {
					throw new Exception( '無効なファイル' );
				}

				for ($row = 2; $row <= $file['highEstRow']; $row++) {
					$errMsg = "";
					$data = array();

					// Area ID
					$data['AreaCode'] = $sheet->getCell('A' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["AreaCode"] ) ) {
						$errMsg .= "地区コードは未入力です。";
					}
					elseif (mb_strlen($data["AreaCode"]) > 30) {
						$errMsg .= "地区コードを30文字以内で入力してください。";
					}
					else {
						$AreaId = $this->conn->prepare("SELECT `AreaId` FROM `infoseminar_areas` WHERE `AreaCode`='" . $data["AreaCode"] . "'");
						$AreaId->execute();
						if ( $AreaId->rowCount() > 0 ) {
							$data["AreaId"] = $AreaId->fetch(PDO::FETCH_ASSOC)['AreaId'];
						}
						else {
							$errMsg .= '地区コードが存在しません。';
						}
					}

					// Seminar Name
					$data["SeminarName"] = $sheet->getCell('B' . $row)->getFormattedValue();
					if ( $checkEmpty( $data['SeminarName'] ) ) {
                        $errMsg .= "セミナー名は未入力です。";
					}
					elseif (mb_strlen( $data['SeminarName'] ) > 50) {
						$errMsg .= "セミナー名を50文字以内で入力してください。";
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

					//VenueMap
					$data['VenueMap'] = $sheet->getCell('E' . $row, $row)->getFormattedValue();
					if ( !$checkEmpty( $data['VenueMap'] ) && $data['VenueMap'] > 1000 ) {
						$errMsg .= "地図(URL)を1000文字以内で入力してください。";
					}

					// Date
					$data["Date"] = $sheet->getCell('F' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["Date"] ) || strtotime( $data["Date"] ) == false ) {
						$errMsg .= "開催日程は未入力です。";
					}
					else {
						$data["Date"] = date('Y-n-j', strtotime($data["Date"]));
						$day = substr( $data["Date"], strrpos( $data["Date"], '-' ) + 1 );
						date_add( $AppDate = date_create( $data["Date"] ), date_interval_create_from_date_string( ($day - $sample['SampleDeadline']) . ' day' ) );
						$data["AppDate"] = $AppDate->format("Y-n-j");
					}

					// AppDate
					$data['AppDate'] = $sheet->getCell('G' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["AppDate"] ) || strtotime( $data["AppDate"] ) == false ) {
						$errMsg .= "申込期限 は未入力です。";
					}
					else {
                        $days = $sample['SampleDeadline'] == "" ? 0 : $sample['SampleDeadline'];
                        $data['AppDate'] = date("Y/m/d", strtotime($data['AppDate'] . "-" . $days . " day"));
                    }

					// Time Start
					$TimeStart = $sheet->getCell('H' . $row)->getFormattedValue();
					$data["TimeStart"] = preg_replace('/\s/', '', $TimeStart);
					$data["TimeStart"] = preg_replace('/\：/', ':', $data['TimeStart']);
					if ( preg_match( '/^\d{1,2}\:\d{1,2}$/', trim( $data['TimeStart'] ) ) ) {
						$arrTF = explode(":", $data["TimeStart"]);
						$hF = $arrTF[0];
						$mF = $arrTF[1];
						if (strlen($hF) < 2) {
							$hF = '0' . $hF;
						}
						elseif (strlen($mF) < 2) {
							$mF = '0' . $mF;
						}
					}
					else {
						$errMsg .= '開始時間が有効ではない。';
					}

					// Time End
					$TimeEnd = $sheet->getCell('I' . $row)->getFormattedValue();
					$data["TimeEnd"] = preg_replace('/\s/', '', $TimeEnd);
					$data["TimeEnd"] = preg_replace('/\：/', ':', $data['TimeEnd']);
					if ( preg_match( '/^\d{1,2}\:\d{1,2}$/', trim( $data["TimeEnd"] ) ) ) {
						$arrTT = explode(":", $data["TimeEnd"]);
						$hT = $arrTT[0];
						$mT = $arrTT[1];
						if (strlen($hT) < 2) {
							$hT = '0' . $hT;
						}
						elseif (strlen($mT) < 2) {
							$mT = '0' . $mT;
						}
					}
					else {
						$errMsg .= '終了時間が有効ではない。';
					}

					if ( !empty( $arrTF ) && !empty( $arrTT ) ) {
						$ckTime = strtotime( $data['TimeStart'] ) <=> strtotime( $data['TimeEnd'] );
						if ( $ckTime == 0 || $ckTime == 1 ) {
							$errMsg .= "開始時間が有効ではない。";
						}
					}

					if ( $checkEmpty( $errMsg ) ) {
						if ( ($hF == $hT && $mF == $mT) || $hF > $hT ) {
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
						$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
														WHERE TypesId = 1 AND SeminarName='" . $data["SeminarName"] . "' AND AreaId='" . $data["AreaId"] . "' AND VenueName='" . $data["VenueName"] . "'
															AND VenueAddress='" . $data["VenueAddress"] . "' AND VenueAddress='" . $data["VenueAddress"] ."'
															AND `Date`='" . $data["Date"] . "' AND TimeStart='" . $data["TimeStart"] . "' AND TimeEnd='" . $data["TimeEnd"] . "'" );
						$stmt->execute();
						if ($stmt->rowCount() > 0) {
							$errMsg .= 'セミナー が既に存在しています。';
						}
						else {
							$query = "INSERT INTO {$this->table}(SeminarName,AreaId,VenueName,VenueAddress,VenueMap,`Date`,TimeStart,TimeEnd,AppDate,FormLink,Person,CheckFull,TypesId) VALUES (" .
								"'" . $data['SeminarName'] . "'," .
								$data['AreaId'] . "," .
								"'" . $data['VenueName'] . "'," .
								"'" . $data['VenueAddress'] . "'," .
								( !empty( $data['VenueMap'] && strtolower( $data['VenueMap'] ) != 'null' ) ? "'" . $data['VenueMap'] . "'" : "NULL" ) . "," .
								"'" . $data['Date'] . "'," .
								"'" . $data['TimeStart'] . "'," .
								"'" . $data['TimeEnd'] . "'," .
								"'" . $data['AppDate'] . "'," .
								"1," .
								"0," .
								"0," .
								"2" .
							")";

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
						$data['errMsg']    = $errMsg;
						$data['TimeStart'] = $TimeStart;
						$data['TimeEnd']   = $TimeEnd;
						$errArg[$countErr]   = $data;
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
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} where TypesId = 2" );
			$stmt->execute();
			if ( $stmt->rowCount() > 0 ) {
				$mgs ='全てのセミナーを削除してよろしいでしょうか？';
				$btn ='<button name="btnDel_a" id="del_a" onclick="deleteAllSeminarB();" value="" class="btnDel btnDel_a">はい</button>
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

			$this->conn->exec( "DELETE FROM {$this->table} WHERE TypesId = 2" );
			$result['success'] = true;
			return $result;
		}

		public function getClientList() {
			$query = "SELECT *\n
					FROM {$this->table} sumary ,infoseminar_todouhukens todous\n
					WHERE sumary.TypesId = 2 AND sumary.TodouhukenId = todous.TodouhukenId\n
					ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function updateColFull( $seminarId ) {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE SeminarId=" . $seminarId );
			$stmt->execute();
			if ( $stmt->rowCount() > 0 ) {
				$result = $stmt->fetch(PDO::FETCH_ASSOC);
				if ($result["CheckFull"] == 0 && $result["Person"] == 0) {
					$result["CheckFull"] = $result["Person"] = 1;
				}
				elseif ($result["CheckFull"] == 1 && $result["Person"] == 1) {
					$result["CheckFull"] = $result["Person"] = 0;
					$this->conn->exec( "DELETE FROM infoseminar_customers WHERE SeminarId =" . $seminarId );
				}

				$this->conn->exec( "UPDATE {$this->table} SET CheckFull = " . $result["CheckFull"] . ",Person=" . $result["Person"] . " WHERE SeminarId = " . $seminarId );
			}
			return true;
		}

		private function exportError($data) {
			$objReader = PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
			$workSheet = $objReader->load( __DIR__ . "/../../template-report/errSeminarBImport.xlsx");
			$sheet = $workSheet->getSheet(0);

			$i = 2;
			foreach ( $data as $value ) {
				$sheet->setCellValue( 'A' . $i, $value["AreaCode"] );
				$sheet->setCellValue( 'B' . $i, $value["SeminarName"] );
				$sheet->setCellValue( 'C' . $i, $value["VenueName"] );
				$sheet->setCellValue( 'D' . $i, $value["VenueAddress"] );
				$sheet->setCellValue( 'E' . $i, $value["VenueMap"] );
				$sheet->setCellValue( 'F' . $i, $value["Date"] );
				$sheet->setCellValue( 'G' . $i, $value["AppDate"] );
				$sheet->setCellValue( 'H' . $i, $value["TimeStart"] );
				$sheet->setCellValue( 'I' . $i, $value["TimeEnd"] );
				$sheet->setCellValue( 'J' . $i, $value["errMsg"] );
				$i++;
			}

			$writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($workSheet);
			$outPut = $this->_prefix . 'ERROR_' . date('His') . '.xlsx';
			$writer->save( __DIR__ . '/../../../data_files/' . $outPut );
			return '../data_files/' . $outPut;
		}

		private function formatString( $text ) {
			$string = preg_replace('/\s+/',' ', trim($text));
			$string = preg_replace('/　+/','^', $string);
			$string = preg_replace('/\^+/','　', $string);
			return $string;
		}
	}
?>
