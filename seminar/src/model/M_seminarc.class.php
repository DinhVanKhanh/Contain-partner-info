<?php
	class M_seminarc extends Database {
        protected $table = 'infoseminar_sumary';
        protected $_prefix = 'SEMINAR_C_';

        public function getList() : array {
            $query = "SELECT sumary.*, todous.TodouhukenCode, todous.TodouhukenDisplay\n
                    FROM {$this->table} sumary LEFT JOIN infoseminar_todouhukens todous\n
                    ON todous.TodouhukenId = sumary.TodouhukenId\n
                    WHERE sumary.TypesId = 3\n
                    ORDER BY todous.TodouhukenCode,sumary.CompanyName,sumary.VenueName,sumary.VenueAddress,YEAR(sumary.Date) ASC, MONTH(sumary.Date) ASC, DAY(sumary.Date) ASC,sumary.TimeStart";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT SeminarId, SeminarName, TodouhukenId, CompanyName, VenueName, VenueAddress, `Date`, TimeStart, TimeEnd, Note, ContactFax, ContactTel, CountPerson, AppDate, PDF  FROM {$this->table} WHERE SeminarId = {$id}"  );
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
				$curPdf       = $data['curPdf'];

				$note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
				$note = preg_replace('/&nbsp;/', ' ', $note);

				// Check record exists
				$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
												WHERE SeminarName='{$seminarName}' AND CompanyName='{$companyName}' AND VenueName='{$venueName}'
												AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}'" );
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					throw new Exception('セミナー が既に存在しています。');
				}

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

				$query = "INSERT INTO {$this->table}(SeminarName,CompanyName,TodouhukenId,ContactTel,ContactFax,VenueName,VenueAddress,`Date`,TimeStart,TimeEnd,CountPerson,Person,FormLink,CheckFull,Note,AppDate,TypesId %s) VALUES (" .
					"'{$seminarName}'," .
					"'{$companyName}'," .
					"{$todouhukenId}," .
					"'{$contactTel}'," .
					"'{$contactFax}'," .
					"'{$venueName}'," .
					"'{$venueAddress}'," .
					"'{$scDate}'," .
					"'{$timeStart}'," .
					"'{$timeEnd}'," .
					"{$countPerson}," .
					"0," .
					"1," .
					"0," .
					"'{$note}'," .
					"'{$appDate}'," .
					"3" .
					"%s" .
				")";
				$query = empty($pdf) ? sprintf( $query, '', '' ) : sprintf( $query, ',PDF', ",'$pdf'" );

                $this->conn->exec( $query );
                $rs['success'] = true;
            }
            catch (PDOException $e) {
				$rs['errMsg'] = '新しい失敗を追加' . $e->getMessage();
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

				// Person
				$countPerson  = $data['CountPerson'];
				$stmt = $this->conn->prepare( "SELECT Person FROM {$this->table} WHERE SeminarId=" . $data['id'] );
				$stmt->execute();
				$person = $stmt->fetch(PDO::FETCH_ASSOC)["Person"];
				if ( $countPerson > 0 && $person > $countPerson ) {
					throw new Exception( $countPerson . '名が登録しています。' );
				}

				$oldPdf       = $data['oldPdf'];
				$curPdf       = $data['curPdf'];

				$note = preg_replace('/\\s+/iu', '　', trim($data['Note']));
				$note = preg_replace('/&nbsp;/', ' ', $note);

				// Check record exists
				$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
												WHERE SeminarName='{$seminarName}' AND CompanyName='{$companyName}' AND VenueName='{$venueName}'
												AND VenueAddress='{$venueAddress}' AND `Date`='{$scDate}' AND TimeStart='{$timeStart}' AND TimeEnd='{$timeEnd}' AND SeminarId <> " . $data['id'] );
				$stmt->execute();
				if ($stmt->rowCount() > 0) {
					throw new Exception('セミナー が既に存在しています。');
				}

				// TH : PDF名をコーピー
				// Upload PDF
				$pdf = $oldPdf;
				if ( $data['file'] != null ) {
					if ($data['file']['error'] == 0) {
						$upload = __DIR__ . '/../../../data_files/';
						$stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE `PDF`='{$oldPdf}'");
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

				$query = "UPDATE {$this->table} SET " .
					"SeminarName = '{$seminarName}'," .
					"CompanyName = '{$companyName}'," .
					"TodouhukenId = {$todouhukenId}," .
					"ContactTel = '{$contactTel}'," .
					"ContactFax = '{$contactFax}'," .
					"VenueName = '{$venueName}'," .
					"VenueAddress = '{$venueAddress}'," .
					"`Date` = '{$scDate}'," .
					"TimeStart = '{$timeStart}'," .
					"TimeEnd = '{$timeEnd}'," .
					"CountPerson = {$countPerson}," .
					"PDF = '{$pdf}'," .
					"Note = '{$note}'," .
					"AppDate = '{$appDate}'" .
					" WHERE SeminarId = " . $data['id'];

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
                // Check in todouhukens
                $stmt = $this->conn->prepare( "SELECT Pdf FROM {$this->table} WHERE SeminarId = {$id}" );
				$stmt->execute();
                if ( $stmt->rowCount() > 0 ) {
					if ( !empty( $stmt->fetch(PDO::FETCH_ASSOC)['Pdf'] ) ) {
						$file = __DIR__ . '/../../../data_files/' . $this->_prefix . $stmt->fetch(PDO::FETCH_ASSOC)['Pdf'];
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
						$TodouhukenId = $this->conn->prepare("SELECT `TodouhukenId` FROM `infoseminar_todouhukens` WHERE `TodouhukenDisplay`='" . $data["TodouhukenDisplay"] . "'");
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
						date_add( $AppDate = date_create( $data["Date"] ), date_interval_create_from_date_string( ($day - $sample['SampleDeadline']) . ' day' ) );
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

					// PDF
					$data["PDF"] = $sheet->getCell('J' . $row)->getFormattedValue();

					// Note
					$data["Note"] = $sheet->getCell('K' . $row)->getFormattedValue();

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
						$stmt = $this->conn->prepare( "SELECT SeminarName as total FROM {$this->table}\n
														WHERE TypesId = 3 AND SeminarName='" . $data["SeminarName"] . "' AND CompanyName='" . $data["CompanyName"] . "' AND VenueName='" . $data["VenueName"] . "'
														AND VenueAddress='" . $data["VenueAddress"] . "' AND `Date`='" . $data["Date"] . "' AND TimeStart='" . $data["TimeStart"] . "' AND TimeEnd='" . $data["TimeEnd"] . "'" );
						$stmt->execute();
						if ($stmt->rowCount() > 0) {
							$errMsg .= 'セミナー が既に存在しています。';
						}
						else {
							$query  = "INSERT INTO {$this->table} (SeminarName,CompanyName,TodouhukenId,VenueName,VenueAddress,ContactTel,ContactFax,`Date`,TimeStart,TimeEnd,CountPerson,FormLink,TypesId,Person,CheckFull,Note,PDF,AppDate)
									VALUES ('%s','%s',%d,'%s','%s','%s','%s','%s','%s','%s',%d,%d,%d,%d,%d,'%s','%s','%s')";
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
								$data["CountPerson"],
								0,
								3,
								0,
								0,
								htmlspecialchars( strip_tags( $data["Note"] ) ),
								$data["PDF"],
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
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} where TypesId = 3" );
			$stmt->execute();
			if ( $stmt->rowCount() > 0 ) {
				$mgs ='全てのセミナーを削除してよろしいでしょうか？';
				$btn ='<button name="btnDel_a" id="del_a" onclick="deleteAllSeminarC();" value="" class="btnDel btnDel_a">はい</button>
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

			// Delete all file pdf
			$upload = __DIR__ .'/../../../data_files/';
			array_map('unlink', glob($upload . $this->_prefix . "*"));

			$this->conn->exec( "DELETE FROM {$this->table} WHERE TypesId = 3" );
			$result['success'] = true;
			return $result;
		}

		public function getClientList() {
			$query = "SELECT *\n
					FROM {$this->table} sumary ,infoseminar_todouhukens todous\n
					WHERE sumary.TypesId = 3 AND sumary.TodouhukenId = todous.TodouhukenId\n
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
				$sheet->setCellValue( 'J' . $i, $value["PDF"] );
				$sheet->setCellValue( 'K' . $i, $value["Note"] );
				$sheet->setCellValue( 'L' . $i, $value["errMsg"] );
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
