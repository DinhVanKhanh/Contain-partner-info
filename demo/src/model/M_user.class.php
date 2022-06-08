<?php
	class M_user extends Database {
		protected $table = 'infodemo_users';
		const ROLE_ADMIN = 0, ROLE_CLIENT = 1;

		public function getList() : array {
			$stmt = $this->conn->prepare( "SELECT UserId, UserCd, UserName, KengenKbn FROM {$this->table}" );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getById( int $id ) : array {
			$stmt = $this->conn->prepare( "SELECT UserId, UserCd, UserName, KengenKbn FROM {$this->table} WHERE UserId = {$id}"  );
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function add( Array $data ) : array  {
			try {
				// Check record exists
				if ( $this->dataExists( $data ) ) {
					throw new Exception( "ユーザーID " . $data['UserCd'] . "が登録されています。" );
				}

				extract($data);
				//var_dump($data);exit;
				$Password = $this->decrypt($Password);
				$stmt  = $this->conn->prepare( "INSERT INTO {$this->table}(UserCd, UserName, `Password`, `SakuseiDate`, `KengenKbn`) VALUES (:UserCd, :UserName, MD5(:Password), CURDATE(), :KengenKbn)" );
				$stmt->bindParam( ':UserCd', $UserCd, PDO::PARAM_STR, 12 );
				$stmt->bindParam( ':UserName', $UserName, PDO::PARAM_STR, 50 );
				$stmt->bindParam( ':Password', $Password, PDO::PARAM_STR, 12 );
				$stmt->bindParam( ':KengenKbn', $KengenKbn, PDO::PARAM_INT, 4 );
				$stmt->execute();
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
				if ( $this->dataExists( $data, self::FLAG_EDIT ) ) {
					throw new Exception( "ユーザーID " . $data['UserCd'] . "が登録されています。" );
				}

				extract($data);
				$query = "UPDATE {$this->table} SET UserName = :UserName,KengenKbn = :KengenKbn %s WHERE UserId = :UserId";
				if ( $isPwChange ) {
					$query = sprintf($query, ', `Password` = MD5(:Password)');
					$stmt  = $this->conn->prepare( $query );
					$Password = $this->decrypt($Password);
					$stmt->bindParam( ':Password', $Password, PDO::PARAM_STR, 12 );	
				}
				else {
					$query = sprintf($query, '');
					$stmt  = $this->conn->prepare( $query );
				}
				
				$stmt->bindParam( ':UserName', $UserName, PDO::PARAM_STR, 50 );
				$stmt->bindParam( ':KengenKbn', $KengenKbn, PDO::PARAM_INT, 4 );
				$stmt->bindParam( ':UserId', $UserId, PDO::PARAM_INT, 11 );
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
			$list = $this->getList();
			if ( !$list ) {
				$rs['empty'] = "空のデータ";
				goto Result;
			}

			$outPut = $this->_prefix . 'EXPORT_USERS_' . date("Ymd") . '.csv';
			$upload = __DIR__ . '/../../../data_files/' . $outPut;
			if ( file_exists( $upload ) ) {
				unlink( $upload );
			}

			// Put title
			$file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
			fputcsv( $file, [
				$this->convertJP("ユーザーID"),
				$this->convertJP("ユーザー名"),
				$this->convertJP("権限")
			]);

			$role = ['admin', 'user'];

			// Put content
			foreach ($list as $item) {
				fputcsv(
					$file, 
					[
						$this->convertJP($item['UserCd']),
						$this->convertJP($item['UserName']),
						$this->convertJP($role[(int)$item['KengenKbn']])
					]
				);
			}

			fclose( $file );
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

		// Check data exists or not
		private function dataExists( Array $data, $method = self::FLAG_ADD ) : bool {
			try {
				extract($data);
				$query = "SELECT UserCd FROM {$this->table} WHERE UserCd = :UserCd OR UserName = :UserName";
				if ( $method == self::FLAG_EDIT ) {
					$query .= " AND UserId <> :UserId;";
					$stmt = $this->conn->prepare( $query );
					$stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT, 11);
				}
				else {
					$stmt = $this->conn->prepare( $query );
				}

				$stmt->bindParam(':UserCd', $UserCd, PDO::PARAM_STR, 12);
				$stmt->bindParam(':UserName', $UserName, PDO::PARAM_STR, 50);
				$stmt->execute();
				return $stmt->rowCount() > 0 ? true : false;
			}
			catch ( PDOException $e ) {
				return $e->getMessage();
			}
		}
	}
?>