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

		public function add( Array $data ) : array  {
			try {
				// Check record exists
				$todous = $this->conn->prepare( "SELECT TodouhukenCode FROM {$this->table} WHERE TodouhukenCode='" . $data['code'] . "' OR TodouhukenName='" . $data['name'] . "'" );
				$todous->execute();
				if ( $todous->rowCount() > 0 ) {
					throw new Exception( '重複する値' );
				}

				$query = "INSERT INTO {$this->table}(TodouhukenCode, TodouhukenName, AreaId)
						VALUES ('" . $data["code"] . "','" . $data["name"] . "'," . $data["areaId"] . ")";
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
				// Check record exists
				$todous = $this->conn->prepare( "SELECT TodouhukenCode FROM {$this->table} WHERE (TodouhukenCode='" . $data['code'] . "' OR TodouhukenName='" . $data['name'] . "') AND TodouhukenId <> " . $data['id'] );
				$todous->execute();
				if ( $todous->rowCount() > 0 ) {
					throw new Exception( '重複する値' );
				}

				$query = "UPDATE {$this->table} SET TodouhukenCode='" . $data["code"] . "',
					TodouhukenName='" . $data["name"] . "', AreaId=" . $data["areaId"] . "\n
					WHERE TodouhukenId=" . $data['id'];
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
			$query = "SELECT todo.TodouhukenCode, todo.TodouhukenName, ar.AreaName\n
					FROM {$this->table} todo, infodemo_areas ar\n
					WHERE ar.AreaId = todo.AreaId\n
					ORDER BY TodouhukenId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Check result statement
			if ( !($result != false && count($result) > 0) ) {
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
			foreach ($result as $data) {
				// Name
				$name = $this->convertJP($data["AreaName"]);

				// TodouhukenCode
				$todouhukenCode = $this->convertJP($data["TodouhukenCode"]);

				// TodouhukenName
				$todouhukenName = $this->convertJP($data["TodouhukenName"]);
				fputcsv( $file, [$name, $todouhukenCode, $todouhukenName] );
			}

			fclose( $file );
			$rs['fileUrl'] = '../data_files/' . $outPut;

			Result:
			return $rs;
		}

		// Fitler record by area
		public function filterByArea( int $id ) {
			$query = "SELECT todo.TodouhukenId, todo.TodouhukenCode, todo.TodouhukenName, ar.AreaName\n
					FROM infodemo_todouhukens todo, infodemo_areas ar\n
					WHERE ar.AreaId = todo.AreaId AND todo.AreaId=" . $id . "\n
					ORDER BY TodouhukenId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
	}
?>
