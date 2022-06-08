<?php
	class M_meetingplace extends Database {
		protected $table = 'infodemo_meetingplaces';

		public function getList() : array {
			$query = "SELECT * FROM {$this->table} ORDER BY MeetingPlaceId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE MeetingPlaceId = {$id}"  );
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function add( Array $data ) : array  {
			try {
				// Check record exists
				$mtps = $this->conn->prepare( "SELECT MeetingPlaceId FROM {$this->table} WHERE Code='" . $data['code'] . "'" );
				$mtps->execute();
				if ( $mtps->rowCount() > 0 ) {
					throw new Exception( 'コードは既に存在しています。' );
				}

				$query  = "INSERT INTO {$this->table}(Code,Address_1,Address_2,postalCode,Tel,Fax,Map,TodouhukenId,storeName1,storeName2)";
				$query .= "VALUES (" .
							"'" . $data["code"] . "'" .
							",'" . $data["address_1"] . "'" .
							",'" . $data["address_2"] . "'" .
							",'" . $data["posCode"] . "'" .
							",'" . $data["tel"] . "'" .
							",'" . $data["fax"] . "'" .
							",'" . $data["map"] . "'" .
							"," . $data["todouId"] .
							",'" . $data["storeName1"] . "'" .
							",'" . $data["storeName2"] . "'" .
						")";
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
				// Check record exists
				$mtps = $this->conn->prepare( "SELECT MeetingPlaceId FROM {$this->table} WHERE Code='" . $data['code'] . "' AND MeetingPlaceId <> " . $data['id'] );
				$mtps->execute();
				if ( $mtps->rowCount() > 0 ) {
					throw new Exception( 'コードは既に存在しています。' );
				}

				$query = "UPDATE {$this->table} SET " .
					" Code='" . $data["code"] . "'" .
					", Address_1='" . $data["address_1"] . "'" .
					", Address_2='" . $data["address_2"] . "'" .
					", postalCode='" . $data["posCode"] . "'" .
					", Tel='" . $data["tel"] . "'" .
					", Fax='" . $data["fax"] . "'" .
					", Map='" . $data["map"] . "'" .
					", TodouhukenId=" . $data["todouId"] .
					", storeName1='" . $data["storeName1"] . "'" .
					", storeName2='" . $data["storeName2"] . "'" .
					" WHERE MeetingPlaceId=" . $data['id'];
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
				// Check in schedules
				$sch = $this->conn->prepare( "SELECT scheduleId FROM infodemo_schedules WHERE MeetingPlaceId = {$id}" );
				$sch->execute();
				if ( $sch->rowCount() > 0 ) {
					throw new Exception( "この会場を削除できません。店頭デモに既に存在しています。" );
				}
				$this->conn->exec( "DELETE FROM {$this->table} WHERE MeetingPlaceId = {$id}" );
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
			$query = "SELECT Code, Address_1, Address_2, Tel, Fax FROM {$this->table} ORDER BY MeetingPlaceId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Check result statement
			if ( !($result != false && count($result) > 0) ) {
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
			$content = "";
			foreach ($result as $data) {
				// Code
				$code = $this->convertJP($data["Code"]);

				// Address 1
				$address1 = $this->convertJP($data["Address_1"]);

				// Address 2
				$address2 = $this->convertJP($data["Address_2"]);

				fputcsv( $file, [$code, $address1 . ' ' . $address2, $data['Tel'], $data['Fax']] );
			}

			fclose( $file );
			$rs['fileUrl'] = '../data_files/' . $outPut;

			Result:
			return $rs;
		}

		// Fitler record by area
		public function filterByArea( int $id ) {
			$query = "SELECT * FROM {$this->table}\n
				WHERE TodouhukenId IN (\n
					SELECT TodouhukenId FROM infodemo_todouhukens WHERE areaId = " . $id . "\n
				)\n
				ORDER BY MeetingPlaceId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>
