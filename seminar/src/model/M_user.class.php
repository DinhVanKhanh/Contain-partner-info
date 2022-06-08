<?php
	class M_user extends Database {
		protected $table = 'infoseminar_users';

		public function getList() : array {
			$stmt = $this->conn->prepare( "SELECT UserId, UserCd, UserName FROM {$this->table}" );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE UserId = {$id}"  );
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function add( Array $data ) : array  {
			try {
				// Check record exists
				$users = $this->conn->prepare( "SELECT UserCd FROM {$this->table} WHERE UserCd='" . $data['code'] . "'" );
				$users->execute();
				if ( $users->rowCount() > 0 ) {
					throw new Exception( "ユーザーID " . $data['code'] . "が登録されています。" );
				}

				$query = "INSERT INTO {$this->table}(UserCd, UserName, `Password`)
						VALUES ('" . $data["code"] . "','" . $data["name"] . "','" . md5( $this->decrypt($data["password"]) ) . "')";
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
				$users = $this->conn->prepare( "SELECT UserCd FROM {$this->table} WHERE UserCd='" . $data['code'] . "' AND UserId <> " . $data['id'] );
				$users->execute();
				if ( $users->rowCount() > 0 ) {
					throw new Exception( "ユーザーID " . $data['code'] . "が登録されています。" );
				}

				$query  = "UPDATE {$this->table} SET UserName = '" . $data["name"] . "', `Password` = '" . md5( $this->decrypt($data["password"]) ) . "' WHERE UserId = " . $data['id'];
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
				$this->conn->exec( "DELETE FROM {$this->table} WHERE UserId = {$id}" );
				return "削除しました";
			}
			catch (PDOException $e) {
				return "削除失敗";
			}
		}

		// Export data to csv
		public function exportCSV() : array {
			$query = "SELECT UserCd, UserName FROM {$this->table} ORDER BY UserId";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// Check result statement
			if ( !($result != false && count($result) > 0) ) {
				$rs['empty'] = "空のデータ";
				goto Result;
			}

			$outPut = $this->_prefix . 'EXPORT_USERS_' . date('His') . '.csv';
			$upload = __DIR__ . '/../../../data_files/' . $outPut;
			if ( file_exists( $upload ) ) {
				unlink( $upload );
			}

            // Put title
			$file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
			fputcsv( $file, [
				$this->convertJP("ユーザーID"),
                $this->convertJP("ユーザー名")
			]);

			// Put content
			foreach ($result as $data) {
				// Code
				$code = htmlspecialchars($data["UserCd"]);
				$code = $this->convertJP($code);

				// Name
				$name = htmlspecialchars($data["UserName"]);
				$name = $this->convertJP($name);
                fputcsv( $file, [ $code, $name ]);
			}

			$rs['fileUrl'] = '../data_files/' . $outPut;

			Result:
			return $rs;
		}

		// Decrypt password
		private function decrypt( string $string ) : string {
			$hash = base64_encode('qweadszxc');
			$text = str_replace( $hash, '',  $string );
			return base64_decode( $text );
		}
	}
?>
