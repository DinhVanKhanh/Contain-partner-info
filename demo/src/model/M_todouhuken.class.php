<?php
	class M_todouhuken extends Database {
		protected $table = 'infodemo_todouhukens';

		public function getList() : array {
			$query = "SELECT todo.TodouhukenId, todo.TodouhukenCode, todo.TodouhukenName, ar.AreaName\n
					FROM {$this->table} todo, infodemo_areas ar\n
					WHERE ar.AreaId = todo.AreaId\n
					ORDER BY TodouhukenId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE todouhukenId = {$id}"  );
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	//　↓↓　<2020/09/23> <YenNhi> <check record is exist>
        private function dataExists(Array $data, $flag = self::FLAG_ADD) : bool {
            extract($data);
            $query = "SELECT TodouhukenCode FROM {$this->table} WHERE (TodouhukenCode= :TodouhukenCode OR TodouhukenName= :TodouhukenName)";
            if ( $flag == self::FLAG_EDIT )
			{
				$query .= " AND TodouhukenId <> :TodouhukenId";
				$todous = $this->conn->prepare($query);
				$todous->bindParam(':TodouhukenId', $id, PDO::PARAM_INT, 11);
			}
			else 
			{
				$todous = $this->conn->prepare($query);
			}
                 
			$todous->bindParam(':TodouhukenCode', $code, PDO::PARAM_STR, 30);
			$todous->bindParam(':TodouhukenName', $name, PDO::PARAM_STR, 50);
			$todous->execute();
			return ($todous->rowCount() > 0)  ? true : false;
        }
	//　↑↑　<2020/09/23> <YenNhi> <check record is exist>
		
		public function add( Array $data ) : array  {
			try {
			//　↓↓　<2020/09/24> <YenNhi> <avoid SQL injection>
				// Check record exists
				// $todous = $this->conn->prepare( "SELECT TodouhukenCode FROM {$this->table} WHERE TodouhukenCode='" . $data['code'] . "' OR TodouhukenName='" . $data['name'] . "'" );
				// $todous->execute();
				
				//$query = "INSERT INTO {$this->table}(TodouhukenCode, TodouhukenName, AreaId)
				//		VALUES ('" . $data["code"] . "','" . $data["name"] . "'," . $data["areaId"] . ")";
				//$this->conn->exec( $query );

				// Check record exists
				if ( $this->dataExists($data) ) {
					throw new Exception( '重複する値' );
				}
				extract($data);
				$query = "INSERT INTO {$this->table}(TodouhukenCode, TodouhukenName, AreaId) VALUES (:TodouhukenCode, :TodouhukenName, :AreaId)";
				$todous = $this->conn->prepare($query);
				$todous->bindParam(':TodouhukenCode', $code, PDO::PARAM_STR, 30);
				$todous->bindParam(':TodouhukenName', $name, PDO::PARAM_STR, 50);
				$todous->bindParam(':AreaId', $areaId, PDO::PARAM_INT, 11);
				$todous->execute();
			//　↑↑　<2020/09/24> <YenNhi> <avoid SQL injection>
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
			//　↓↓　<2020/09/24> <YenNhi> <avoid SQL injection>
				// Check record exists
				// $todous = $this->conn->prepare( "SELECT TodouhukenCode FROM {$this->table} WHERE (TodouhukenCode='" . $data['code'] . "' OR TodouhukenName='" . $data['name'] . "') AND TodouhukenId <> " . $data['id'] );
				// $todous->execute();
				// $query = "UPDATE {$this->table} SET TodouhukenCode='" . $data["code"] . "',
				// 	TodouhukenName='" . $data["name"] . "', AreaId=" . $data["areaId"] . "\n
				// 	WHERE TodouhukenId=" . $data['id'];
				// $this->conn->exec( $query );

				// Check record exists
				if ( $this->dataExists($data, self::FLAG_EDIT) ) {
					throw new Exception( '重複する値' );
				}
				extract($data);
				$query ="UPDATE {$this->table} 
						SET TodouhukenCode= :TodouhukenCode, 
							TodouhukenName= :TodouhukenName, 
							AreaId= :AreaId 
						WHERE TodouhukenId= :TodouhukenId";
				$todous = $this->conn->prepare($query);
				$todous->bindParam(':TodouhukenCode', $code, PDO::PARAM_STR, 30);
				$todous->bindParam(':TodouhukenName', $name, PDO::PARAM_STR, 50);
				$todous->bindParam(':AreaId', $areaId, PDO::PARAM_INT, 11);
				$todous->bindParam(':TodouhukenId', $id, PDO::PARAM_INT, 11);
				$todous->execute();
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
				// Check in meetingplaces
				$mt = $this->conn->prepare( "SELECT MeetingPlaceId FROM infodemo_meetingplaces WHERE TodouhukenId = {$id}" );
				$mt->execute();
				if ( $mt->rowCount() > 0 ) {
					throw new Exception( 'この会場は削除できません。 会場管理で都市コードいます' );
				}
				$this->conn->exec( "DELETE FROM {$this->table} WHERE TodouhukenId = {$id}" );
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

			$outPut = $this->_prefix . 'EXPORT_CITY_' . date('Ymd') . '.csv';
			$upload = __DIR__ . '/../../../data_files/' . $outPut;
			if ( file_exists( $upload ) ) {
				unlink( $upload );
			}

			// Put title
			$file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
			fputcsv( $file, [
                $this->convertJP("地区"),
                $this->convertJP("都市コード"),
                $this->convertJP("都市名")
            ]);

			// Put content
			$content = "";
			foreach ($list as $item) {
				// Name
				$name = $this->convertJP($item["AreaName"]);

				// TodouhukenCode
				$todouhukenCode = $this->convertJP($item["TodouhukenCode"]);

				// TodouhukenName
				$todouhukenName = $this->convertJP($item["TodouhukenName"]);
				fputcsv( $file, [$name, $todouhukenCode, $todouhukenName] );
			}

			fclose( $file );
			$rs['fileUrl'] = '../data_files/' . $outPut;

			Result:
			return $rs;
		}

		// Fitler record by area
		public function filterByArea( int $id ) {
			$query ="SELECT todo.TodouhukenId, todo.TodouhukenCode, todo.TodouhukenName, ar.AreaName 
					FROM infodemo_todouhukens todo, infodemo_areas ar 
					WHERE ar.AreaId = todo.AreaId AND todo.AreaId= :id ORDER BY TodouhukenId";
			$stmt = $this->conn->prepare($query);
			$stmt->bindParam(':id', $id);
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>
