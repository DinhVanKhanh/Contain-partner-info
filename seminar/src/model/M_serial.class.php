<?php
    class M_serial extends Database {
        protected $table = 'infoseminar_serial';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY SampleId" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE SerialId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                // Check record serial
                $serial = $this->conn->prepare( "SELECT SerialNumber FROM {$this->table} WHERE SampleId=" . $data['SampleId'] );
                $serial->execute();
                if ( $serial->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "INSERT INTO {$this->table}(SampleId, SerialNumber, Note) VALUES (" . $data["SampleId"] . ",'" . $data["SerialNumber"] . "', '" . $data["Note"] . "')";
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
                $serial = $this->conn->prepare( "SELECT SerialNumber FROM {$this->table} WHERE SampleId=" . $data['SampleId'] . " AND SerialId <> " . $data['SerialId'] );
                $serial->execute();
                if ( $serial->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "UPDATE {$this->table} SET 
                    SampleId = " . $data['SampleId'] .  ",
                    SerialNumber = '" . $data["SerialNumber"] . "', 
                    Note = '" . $data['Note'] . "' WHERE SerialId = " . $data['SerialId'];
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
                $this->conn->exec( "DELETE FROM {$this->table} WHERE SerialId = {$id}" );
                return '削除しました';
            }
            catch (PDOException $e) {
                return "削除失敗";
            }
        }
    }
?>