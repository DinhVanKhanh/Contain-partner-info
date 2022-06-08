<?php
    class M_master extends Database {
        protected $table = 'infoseminar_sample';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY SampleId ASC" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE SampleId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function save( Array $data ) : array {
            try {
                // Check record exists
                $query = "SELECT SampleName FROM {$this->table} 
                        WHERE SampleName = '" . $data['SampleName'] . "' AND SampleDeadline = " . $data['SampleDeadline'] .
                        " AND SampleFeesChk = " . $data['SampleFeesChk'] . " AND SampleTaxChk = " . $data['SampleTaxChk'] .
                        " AND SampleId <> " . $data['SampleId'];
                $sample = $this->conn->prepare( $query );
                $sample->execute();
                if ( $sample->rowCount() > 0 ) {
                    $rs['errMsg'] = 'セミナーが既に存在しています。';
                    goto Result;
                }

                $query = "UPDATE {$this->table} SET 
                            SampleName = '" . $data['SampleName'] . "',
                            SampleDeadline = " . $data['SampleDeadline'] . ",
                            SampleFeesChk = " . $data['SampleFeesChk'] . ",
                            SampleFees = " . ( empty( $data['SampleFees'] ) || empty( $data['SampleFeesChk'] ) ? "NULL" : $data['SampleFees'] ) . ",
                            SampleTaxChk = " . $data['SampleTaxChk'] . ",
                            SampleAlways = " . $data['SampleAlways'] . ",
                            SampleAppMonth = '" . $data['SampleAppMonth'] . "',
                            SampleEmail = '" . $data['SampleEmail'] . "' WHERE SampleId = " . $data['SampleId'];
                $this->conn->exec( $query );
                $rs['success'] = true;
            }
            catch (PDOException $e) {
                $rs['errMsg'] = '更新失敗' . $e->getMessage();
                goto Result;
            }
            catch (Exception $e) {
                $rs['errMsg'] = $e->getMessage();
                goto Result;
            }
    
            Result:
            return $rs;
        }
    }
?>