<?php
	class M_seminara extends Database {
		protected $table = 'infoseminar_sumary';
		protected $_prefix = 'SEMINAR_A_';

        public function getList() : array {
            $query = "SELECT su.*, area.AreaName\n
                    FROM {$this->table} su, infoseminar_areas area\n
                    WHERE su.TypesId = 1 AND area.AreaId = su.AreaId\n
                    ORDER BY su.SeminarId ASC, su.SeminarName, su.AreaId, YEAR(su.Date) DESC, MONTH(su.Date) DESC, DAY(su.DATE) DESC, su.TimeStart";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT SeminarId, SeminarName, AreaId, VenueName, VenueAddress, VenueMap, VenueStation, `Date`, TimeStart, TimeEnd, Note, ContactFax, ContactTel, CountPerson, SeminarClass1, SeminarClass2, SeminarClass3, SeminarFees FROM {$this->table} WHERE SeminarId = {$id}"  );
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
				$venueStation = $this->formatString( $data['VenueStation'] );
				$contactTel   = preg_replace('/\-+/', '-', trim($data['ContactTel'],'-'));
				$contactFax   = preg_replace('/\-+/', '-', trim($data['ContactFax'],'-'));
				$scDate       = $data['scDate'];
				$timeStart    = $data['TimeStart'];
				$timeEnd      = $data['TimeEnd'];
				$countPerson  = $data['CountPerson'];
				$seminarFees  = $data['SeminarFees'];
				$course        = $data['Course'];
				$company      = $data['Company'];
				$product      = $data['Product'];

				$note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
				$note = preg_replace('/&nbsp;/', ' ', $note);

				// Check record exists
				$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
												WHERE SeminarName='{$seminarName}' AND AreaId='{$areaId}' AND VenueName='{$venueName}'
												AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}'" );
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					throw new Exception('セミナーが既に存在しています。');
				}

				$query = "INSERT INTO {$this->table}(SeminarName,SeminarClass1,SeminarClass2,SeminarClass3,AreaId,ContactTel,ContactFax,VenueName,VenueAddress,VenueMap,VenueStation,`Date`,TimeStart,TimeEnd,SeminarFees,CountPerson,FormLink,Person,CheckFull,Note,TypesId) VALUES (" .
					"'{$seminarName}'," .
					$company . "," .
					$product . "," .
					$course . "," .
					$areaId . "," .
					"'{$contactTel}'," .
					"'{$contactFax}'," .
					"'{$venueName}'," .
					"'{$venueAddress}'," .
					"'{$venueMap}'," .
					"'{$venueStation}'," .
					"'{$scDate}'," .
					"'{$timeStart}'," .
					"'{$timeEnd}'," .
					$seminarFees . "," .
					$countPerson . "," .
					"1," .
					"0," .
					"0," .
					"'{$note}'," .
					"1" .
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
				$venueStation = $this->formatString( $data['VenueStation'] );
				$contactTel   = preg_replace('/\-+/', '-', trim($data['ContactTel'],'-'));
				$contactFax   = preg_replace('/\-+/', '-', trim($data['ContactFax'],'-'));
				$scDate       = $data['scDate'];
				$timeStart    = $data['TimeStart'];
				$timeEnd      = $data['TimeEnd'];
				$countPerson  = $data['CountPerson'];
				$seminarFees  = $data['SeminarFees'];
				$course        = $data['Course'];
				$company      = $data['Company'];
				$product      = $data['Product'];

				$note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
				$note = preg_replace('/&nbsp;/', ' ', $note);

				// Person
				$stmt = $this->conn->prepare( "SELECT Person FROM {$this->table} WHERE SeminarId=" . $data['id'] );
				$stmt->execute();
				$person = $stmt->fetch(PDO::FETCH_ASSOC)["Person"];
				if ( $countPerson > 0 && $person > $countPerson ) {
					throw new Exception( $countPerson . '名が登録しています。' );
				}

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
							"SeminarClass1={$company}," .
							"SeminarClass2={$product}," .
							"SeminarClass3={$course}," .
							"AreaId={$areaId}," .
							"ContactTel='{$contactTel}'," .
							"ContactFax='{$contactFax}'," .
							"VenueName='{$venueName}'," .
							"VenueAddress='{$venueAddress}'," .
							"VenueMap='{$venueMap}'," .
							"VenueStation='{$venueStation}'," .
							"`Date`='{$scDate}'," .
							"TimeStart='{$timeStart}'," .
							"TimeEnd='{$timeEnd}'," .
							"SeminarFees={$seminarFees}," .
							"CountPerson={$countPerson}," .
							"Note='{$note}' WHERE SeminarId = " . $data['id'];

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

		public function uploadData( $inputFileName, $sample, $item ) {
			$countErr = $countSuc = 0;
			$direct = __DIR__ . '/../../../data_files';

			// Check empty
			$checkEmpty = function ( $value ) {
				if ( preg_match('/^\s*$/', $value) ) {
					return true;
				}
				return false;
			};

			// Items
			$getItemByCode = function ( $value ) {
				global $item;
				foreach ( $item as $val ) {
					if ( trim( $val['ItemCode'] ) == trim( $value ) ) {
						return $val['ItemId'];
					}
				}
				return '';
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

					// Seminar Name
					$data["SeminarName"] = $sheet->getCell('A' . $row)->getFormattedValue();
					if ( $checkEmpty( $data['SeminarName'] ) ) {
                        $errMsg .= "セミナー名は未入力です。";
					}
					elseif (mb_strlen( $data['SeminarName'] ) > 50) {
						$errMsg .= "セミナー名を50文字以内で入力してください。";
					}

					// Area ID
					$data['AreaCode'] = $sheet->getCell('B' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["AreaCode"] ) ) {
						$errMsg .= "地区コードは未入力です。";
					}
					elseif (mb_strlen($data["AreaCode"]) > 30) {
						$errMsg .= "地区コードを30文字以内で入力してください。";
					}
					else {
						$areaId = $this->conn->prepare("SELECT `AreaId` FROM `infoseminar_areas` WHERE `AreaCode`='" . $data["AreaCode"] . "'");
						$areaId->execute();
						if ( $areaId->rowCount() > 0 ) {
							$data["AreaId"] = $areaId->fetch(PDO::FETCH_ASSOC)['AreaId'];
						}
						else {
							$errMsg .= '地区コードが存在しません。';
						}
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

					// Date
					$data["Date"] = $sheet->getCell('E' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["Date"] ) || strtotime( $data["Date"] ) == false ) {
						$errMsg .= "開催日程は未入力です。";
					}
					else {
						$data["Date"] = date('Y-n-j', strtotime($data["Date"]));
						$day = substr( $data["Date"], strrpos( $data["Date"], '-' ) + 1 );
						date_add( $AppDate = date_create( $data["Date"] ), date_interval_create_from_date_string( ($day - $sample['SampleDeadline']) . ' day' ) );
						$data["AppDate"] = $AppDate->format("Y-n-j");
					}

					//VenueMap
					$data['VenueMap'] = $sheet->getCell('F' . $row, $row)->getFormattedValue();
					if ( $checkEmpty( $data['VenueMap'] ) > 1000 ) {
                        $errMsg .= "地図(URL)を1000文字以内で入力してください。";
                    }

					// Time Start
					$TimeStart = $sheet->getCell('G' . $row)->getFormattedValue();
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
					$TimeEnd = $sheet->getCell('H' . $row)->getFormattedValue();
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

					// SeminarFees
					$data['SeminarFees'] = $sheet->getCell('I' . $row)->getFormattedValue();
					if ( empty($data['SeminarFees']) ) {
						$errMsg .= "セミナー受講料は未入力です。";
					}
					elseif ( preg_match( '/^\D+$/', $data['SeminarFees'] ) && $data['SeminarFees'] < 0 ) {
						$errMsg .= "セミナー受講料が有効ではない。";
					}

					// ContactTel
					$data["ContactTel"] = $sheet->getCell('J' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["ContactTel"] ) ) {
						$test_tel_fax = str_replace( '-', '', $data["ContactTel"] );
						if (!is_numeric($test_tel_fax) || ($test_tel_fax <= 0)) {
							$errMsg .= "電話番号が有効ではない。";
						}
					}

					// ContactFax
					$data["ContactFax"] = $sheet->getCell('K' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["ContactFax"] ) ) {
						$test_tel_fax = (int) str_replace( '-', '', $data["ContactFax"] );
						if (!is_numeric($test_tel_fax) || ($test_tel_fax <= 0)) {
							$errMsg .= "ファクス番号が有効ではない。";
						}
					}

					//VenueStation
					$data['VenueStation'] = $sheet->getCell('L' . $row)->getFormattedValue();
					if ( $checkEmpty( $data['VenueStation'] ) ) {
                        $errMsg .= "最寄駅は未入力です。";
					}
					elseif ( mb_strlen( $data['VenueStation'] ) > 500 ) {
						$errMsg .= "最寄駅を500文字以内で入力してください。";
					}

					// CountPerson
					$data["CountPerson"] = $sheet->getCell('M' . $row)->getFormattedValue();
					if ( $checkEmpty( $data["CountPerson"] ) ) {
						$errMsg .= "定員は未入力です。";
					}
					elseif (!is_numeric($data["CountPerson"]) || $data["CountPerson"] < 0) {
						$errMsg .= "定員が有効ではない。";
					}

					// SeminarClass1 - Company
					$company = $sheet->getCell('N' . $row)->getFormattedValue();
                    if ( $checkEmpty( $company ) ) {
                        $errMsg .= "会社コードは未入力です。";
					}
					else {
						$data['Company'] = $getItemByCode( $company );
						if ( empty( $data['Company'] ) ) {
							$errMsg .= $data['Company'] . "会社コードが存在しません。";
						}
					}

					// SeminarClass3 - couse
					$course = $sheet->getCell('O' . $row)->getFormattedValue();
                    if ( $checkEmpty( $course ) ) {
                        $errMsg .= "コースコードは未入力です。";
					}
					else {
						$data['Course'] = $getItemByCode( $course );
						if ( empty( $data['Course'] ) ) {
							$errMsg .= $data['Course'] . "コースコードが存在しません。";
						}
					}

					// SeminarClass2 - product
					$product = $sheet->getCell('P' . $row)->getFormattedValue();
                    if ( $checkEmpty( $product ) ) {
                        $errMsg .= "対象製品コードは未入力です。";
					}
					else {
						$data['Product'] = $getItemByCode( $product );
						if ( empty( $data['Product'] ) ) {
							$errMsg .= $data['Product'] . "対象製品コードが存在しません。";
						}
					}

					// Note
					$data["Note"] = $sheet->getCell('Q' . $row)->getFormattedValue();

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
															AND VenueAddress='" . $data["VenueAddress"] . "' AND VenueAddress='" . $data["VenueAddress"] ."' AND VenueStation='" . $data['VenueStation'] . "'
															AND `Date`='" . $data["Date"] . "' AND TimeStart='" . $data["TimeStart"] . "' AND TimeEnd='" . $data["TimeEnd"] . "'
															AND SeminarClass1=" . $data['Company'] . " AND SeminarClass2=" . $data['Product'] . " AND SeminarClass3=" . $data['Course'] );
						$stmt->execute();
						if ($stmt->rowCount() > 0) {
							$errMsg .= 'セミナー が既に存在しています。';
						}
						else {
							$query = "INSERT INTO {$this->table}(SeminarName,SeminarClass1,SeminarClass2,SeminarClass3,AreaId,ContactTel,ContactFax,VenueName,VenueAddress,VenueMap,VenueStation,`Date`,TimeStart,TimeEnd,SeminarFees,CountPerson,FormLink,Person,CheckFull,Note,TypesId) VALUES (" .
								"'" . $data['SeminarName'] . "'," .
								$data['Company'] . "," .
								$data['Product'] . "," .
								$data['Course'] . "," .
								$data['AreaId'] . "," .
								"'" . $data['ContactTel'] . "'," .
								"'" . $data['ContactFax'] . "'," .
								"'" . $data['VenueName'] . "'," .
								"'" . $data['VenueAddress'] . "'," .
								"'" . $data['VenueMap'] . "'," .
								"'" . $data['VenueStation'] . "'," .
								"'" . $data['Date'] . "'," .
								"'" . $data['TimeStart'] . "'," .
								"'" . $data['TimeEnd'] . "'," .
								$data['SeminarFees'] . "," .
								$data['CountPerson'] . "," .
								"1," .
								"0," .
								"0," .
								"'" . $data['Note'] . "'," .
								"1" .
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
						$data['Company']   = $company;
						$data['Course']    = $course;
						$data['Product']   = $product;
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
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} where TypesId = 3" );
			$stmt->execute();
			if ( $stmt->rowCount() > 0 ) {
				$mgs ='全てのセミナーを削除してよろしいでしょうか？';
				$btn ='<button name="btnDel_a" id="del_a" onclick="deleteAllSeminarA();" value="" class="btnDel btnDel_a">はい</button>
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

			$this->conn->exec( "DELETE FROM {$this->table} WHERE TypesId = 1" );
			$result['success'] = true;
			return $result;
		}

		public function getClientList( $areaId, $productId ) {
			$query = "SELECT * FROM {$this->table} WHERE TypesId = 1";
			if ($productId == -1 && $areaId != -1) {
				$query .= " AND AreaId = " . $areaId;
			}
			elseif ($productId != -1 && $areaId == -1) {
				$query .= " AND SeminarClass2 = " . $productId;
			}
			elseif ($productId != -1 && $areaId != -1) {
				$query .= " AND SeminarClass2 = " . $productId . " AND AreaId = " . $areaId;
			}
			$query .= " ORDER BY SeminarId ASC, SeminarName, AreaId ASC, YEAR(Date) DESC, MONTH(Date) DESC, DAY(DATE) DESC, TimeStart";

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
			$workSheet = $objReader->load( __DIR__ . "/../../template-report/errSearchImport.xlsx");
			$sheet = $workSheet->getSheet(0);

			$i = 2;
			foreach ( $data as $value ) {
				$sheet->setCellValue( 'A' . $i, $value["SeminarName"] );
				$sheet->setCellValue( 'B' . $i, $value["AreaCode"] );
				$sheet->setCellValue( 'C' . $i, $value["VenueName"] );
				$sheet->setCellValue( 'D' . $i, $value["VenueAddress"] );
				$sheet->setCellValue( 'E' . $i, $value["Date"] );
				$sheet->setCellValue( 'F' . $i, $value["VenueMap"] );
				$sheet->setCellValue( 'G' . $i, $value["TimeStart"] );
				$sheet->setCellValue( 'H' . $i, $value["TimeEnd"] );
				$sheet->setCellValue( 'I' . $i, $value["SeminarFees"] );
				$sheet->setCellValue( 'J' . $i, $value["ContactTel"] );
				$sheet->setCellValue( 'K' . $i, $value["ContactFax"] );
				$sheet->setCellValue( 'L' . $i, $value["VenueStation"] );
				$sheet->setCellValue( 'M' . $i, $value["CountPerson"] );
				$sheet->setCellValue( 'N' . $i, $value["Company"] );
				$sheet->setCellValue( 'O' . $i, $value["Course"] );
				$sheet->setCellValue( 'P' . $i, $value["Product"] );
				$sheet->setCellValue( 'Q' . $i, $value["Note"] );
				$sheet->setCellValue( 'R' . $i, $value["errMsg"] );
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
