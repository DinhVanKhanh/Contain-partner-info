<?php
	class M_meetingplace extends Database {
		protected $table = 'infodemo_meetingplaces';

		public function getList() : array {
			$query = "SELECT * FROM {$this->table} ORDER BY MeetingPlaceId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

	//　↓↓　<2020/09/23> <YenNhi> <avoid SQL injection>
		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE MeetingPlaceId = :id" );
			$stmt->bindParam(':id', $id, PDO::PARAM_INT , 11);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	//　↑↑　<2020/09/23> <YenNhi> <avoid SQL injection>

	//　↓↓　<2020/09/23> <YenNhi> <check record is exist>
        private function dataExists(Array $data, $flag = self::FLAG_ADD) : bool {
            extract($data);
            $query = "SELECT MeetingPlaceId FROM {$this->table} WHERE Code= :Code";
			if ( $flag == self::FLAG_EDIT )
			{
				$query .= " AND MeetingPlaceId <> :MeetingPlaceId";
				$meeting_places = $this->conn->prepare($query);
				$meeting_places->bindParam(':MeetingPlaceId', $id, PDO::PARAM_INT, 11);
			}
			else 
			{
				$meeting_places = $this->conn->prepare($query);
			}
			$meeting_places->bindParam(':Code', $code, PDO::PARAM_STR, 30);
			$meeting_places->execute();
			return ($meeting_places->rowCount() > 0)  ? true : false;
		}
	//　↑↑　<2020/09/23> <YenNhi> <check record is exist>

		public function add( Array $data ) : array  {
			try {
			//　↓↓　<2020/09/24> <YenNhi> <avoid SQL injection>
				// Check record exists
				//$mtps = $this->conn->prepare( "SELECT MeetingPlaceId FROM {$this->table} WHERE Code= :Code" );
				//$mtps->execute();

				// $query .= "VALUES (" .
				// 			"'" . $data["code"] . "'" .
				// 			",'" . $data["address_1"] . "'" .
				// 			",'" . $data["address_2"] . "'" .
				// 			",'" . $data["posCode"] . "'" .
				// 			",'" . $data["tel"] . "'" .
				// 			",'" . $data["fax"] . "'" .
				// 			",'" . $data["map"] . "'" .
				// 			"," . $data["todouId"] .
				// 			",'" . $data["storeName1"] . "'" .
				// 			",'" . $data["storeName2"] . "'" .
				// 		")";
				// $this->conn->exec( $query );

				// Check record exists
				if ( $this->dataExists($data) ) {
					throw new Exception( 'コードは既に存在しています。' );
				}
				
				extract($data);
				$query = "INSERT INTO {$this->table}(Code,Address_1,Address_2,postalCode,Tel,Fax,Map,TodouhukenId,storeName1,storeName2) VALUES 
													(:Code, :Address_1, :Address_2, :postalCode, :Tel, :Fax, :Map, :TodouhukenId, :storeName1, :storeName2 )";
				$meeting_place  = $this->conn->prepare($query);
				$meeting_place->bindParam(':Code', $code, PDO::PARAM_STR, 30);
				$meeting_place->bindParam(':Address_1', $address_1, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':Address_2', $address_2, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':postalCode', $posCode, PDO::PARAM_STR, 50);
				$meeting_place->bindParam(':Tel', $tel, PDO::PARAM_STR, 20);
				$meeting_place->bindParam(':Fax', $fax, PDO::PARAM_STR, 20);
				$meeting_place->bindParam(':Map', $map, PDO::PARAM_STR, 255);
				$meeting_place->bindParam(':TodouhukenId', $todouId,PDO::PARAM_INT, 11);
				$meeting_place->bindParam(':storeName1', $storeName1, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':storeName2', $storeName2, PDO::PARAM_STR, 500);
				$meeting_place->execute();
			//　↑↑　<2020/09/24> <YenNhi> <avoid SQL injection>
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
			//　↓↓　<2020/09/24> <YenNhi> <avoid SQL injection>
				// Check record exists
				//$mtps = $this->conn->prepare( "SELECT MeetingPlaceId FROM {$this->table} WHERE Code='" . $data['code'] . "' AND MeetingPlaceId <> " . $data['id'] );
				//$mtps->execute();
				// $query = "UPDATE {$this->table} SET " .
				// 	" Code='" . $data["code"] . "'" .
				// 	", Address_1='" . $data["address_1"] . "'" .
				// 	", Address_2='" . $data["address_2"] . "'" .
				// 	", postalCode='" . $data["posCode"] . "'" .
				// 	", Tel='" . $data["tel"] . "'" .
				// 	", Fax='" . $data["fax"] . "'" .
				// 	", Map='" . $data["map"] . "'" .
				// 	", TodouhukenId=" . $data["todouId"] .
				// 	", storeName1='" . $data["storeName1"] . "'" .
				// 	", storeName2='" . $data["storeName2"] . "'" .
				// 	" WHERE MeetingPlaceId=" . $data['id'];
				// $this->conn->exec( $query );

				// Check record exists
				if ( $this->dataExists($data, self::FLAG_EDIT) ) {
					throw new Exception( 'コードは既に存在しています。' );
				}
				extract($data);
				$query =   "UPDATE {$this->table} 
						    SET Code= :Code, Address_1= :Address_1, Address_2= :Address_2, postalCode = :postalCode, 
								Tel= :Tel, Fax= :Fax, Map = :Map, TodouhukenId = :TodouhukenId ,storeName1 = :storeName1, storeName2 = :storeName2 
							WHERE MeetingPlaceId= :MeetingPlaceId ";
				$meeting_place = $this->conn->prepare($query);
				$meeting_place->bindParam(':Code', $code, PDO::PARAM_STR, 30);
				$meeting_place->bindParam(':Address_1', $address_1, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':Address_2', $address_2, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':postalCode', $posCode, PDO::PARAM_STR, 50);
				$meeting_place->bindParam(':Tel', $tel, PDO::PARAM_STR, 20);
				$meeting_place->bindParam(':Fax', $fax, PDO::PARAM_STR, 20);
				$meeting_place->bindParam(':Map', $map, PDO::PARAM_STR, 255);
				$meeting_place->bindParam(':TodouhukenId', $todouId,PDO::PARAM_INT, 11);
				$meeting_place->bindParam(':storeName1', $storeName1, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':storeName2', $storeName2, PDO::PARAM_STR, 500);
				$meeting_place->bindParam(':MeetingPlaceId', $id, PDO::PARAM_INT, 11);
				$meeting_place->execute();
			//　↑↑　<2020/09/24> <YenNhi> <avoid SQL injection>
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
				// Check in schedules
				$schedule = $this->conn->prepare( "SELECT scheduleId FROM infodemo_schedules WHERE MeetingPlaceId = :MeetingPlaceId" );
				$schedule->bindParam(':MeetingPlaceId', $id, PDO::PARAM_INT, 11);
				$schedule->execute();
				if ( $schedule->rowCount() > 0 ) {
					throw new Exception( "この会場を削除できません。店頭デモに既に存在しています。" );
				}
				$query = $this->conn->prepare( "DELETE FROM {$this->table} WHERE MeetingPlaceId = :MeetingPlaceId" );
				$query->bindParam(':MeetingPlaceId', $id, PDO::PARAM_INT, 11);
				$query->execute();

				return "削除しました";
			}
			catch (PDOException $e) {
				return "削除失敗";
			}
			catch (Exception $e) {
				return $e->getMessage();
			}
		}

		// Export data to csv
		public function exportCSV() : array {
			$list = $this->getList();

			// Check result statement
			if ( !$list ) {
				$rs['empty'] = "空のデータ";
				goto Result;
			}

			$outPut = $this->_prefix . 'EXPORT_MTP_' . date('Ymd') . '.csv';
			$upload = __DIR__ . '/../../../data_files/' . $outPut;
			if ( file_exists( $upload ) ) {
				unlink( $upload );
			}

			// Put title
			$file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
			fputcsv( $file, [
				$this->convertJP("会場コード"),
				$this->convertJP("会場住所"),
				"TEL",
				"FAX"
			]);

			// Put content
			foreach ($list as $item) {
				// Code
				$code = $this->convertJP($item["Code"]);

				// Address 1
				$address1 = $this->convertJP($item["Address_1"]);

				// Address 2
				$address2 = $this->convertJP($item["Address_2"]);

				fputcsv( $file, [$code, $address1 . ' ' . $address2, $item['Tel'], $item['Fax']] );
			}

			fclose( $file );
			$rs['fileUrl'] = '../data_files/' . $outPut;

			Result:
			return $rs;
		}

		// Fitler record by area
		public function filterByArea( int $id ) {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE TodouhukenId IN ( SELECT TodouhukenId FROM infodemo_todouhukens WHERE areaId = :areaId) ORDER BY MeetingPlaceId" );
			$stmt->bindParam(':areaId', $id, PDO::PARAM_INT, 11);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}

?>
