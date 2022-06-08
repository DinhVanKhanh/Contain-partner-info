<?php
    class M_type extends Database {
        protected $table = 'infoseminar_types';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY TypesId" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE TypesId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                // Check record
                $types = $this->conn->prepare( "SELECT TypesName FROM {$this->table} WHERE TypesName='" . $data['TypesName'] . "'" );

                $types->execute();
                if ( $types->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "INSERT INTO {$this->table}(TypesName, `Description`) VALUES ('" . $data["TypesName"] . "','" . $data["Description"] . "')";
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
                $types = $this->conn->prepare( "SELECT TypesName FROM {$this->table} WHERE TypesName='" . $data['TypesName'] . "' AND TypesId <> " . $data['TypesId'] );

                $types->execute();
                if ( $types->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "UPDATE {$this->table} SET
                            TypesName = '" . $data["TypesName"] . "',
                            `Description` = '" . $data['Description'] . "' WHERE TypesId=" . $data['TypesId'];
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
                $this->conn->exec( "DELETE FROM infoseminar_items WHERE `Type` = {$id}" );
                $this->conn->exec( "DELETE FROM infoseminar_serial WHERE TypesId = {$id}" );

                // Sumary
                $stmt = $this->conn->prepare( "SELECT SeminarId FROM infoseminar_sumary WHERE TypesId = {$id}" );
                $stmt->execute();
                if ( $stmt->rowCount() > 0 ) {
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $ids = "";
                    foreach ( $result as $su ) {
                        $ids .= $su['SeminarId'] . ",";
                    }
                    $this->conn->exec( "DELETE FROM infoseminar_customers WHERE SemianrId IN (" . trim( $ids, "," ) . ")" );
                    $this->conn->exec( "DELETE FROM infoseminar_sumary WHERE SeminarId IN (" . trim( $ids, "," ) . ")" );
                }

                // Sample
                $stmt = $this->conn->prepare( "SELECT SampleId FROM infoseminar_sample WHERE TypesId = {$id}" );
                $stmt->execute();
                if ( $stmt->rowCount() > 0 ) {
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $ids = "";
                    foreach ( $result as $sample ) {
                        $ids .= $sample['SampleId'] . ",";
                    }
                    $this->conn->exec( "DELETE FROM infoseminar_mail WHERE SampleId IN (" . trim( $ids, "," ) . ")" );
                    $this->conn->exec( "DELETE FROM infoseminar_serial WHERE SampleId IN (" . trim( $ids, "," ) . ")" );
                    $this->conn->exec( "DELETE FROM infoseminar_sample WHERE SampleId IN (" . trim( $ids, "," ) . ")" );
                }
                $this->conn->exec( "DELETE FROM {$this->table} WHERE TypesId = {$id}" );
                return '削除しました';
            }
            catch (PDOException $e) {
                return "削除失敗";
            }
        }
    }
?>
