<?php
    class M_mail extends Database {
        protected $table = 'infoseminar_mail';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY SampleId" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE EmailId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function edit( Array $data ) : array {
            try {
                $query = "UPDATE {$this->table} SET `Host`='" . $data["Host"] . "', 
                    `Port`=" . $data["Port"] . ", `Username`='" . $data['Username'] . "',
                    `Password`='" . $data['Password'] . "', FromEmail='" . $data['FromMail'] . "',
                    `FromName`='" . $data['FromName'] . "', EncriptionType=" . $data['EncriptionType'] . ", MailTest='" . $data['MailTest'] . "' WHERE EmailId=" . $data['id'];
                $this->conn->exec( $query );
                $rs['success'] = true;
            }
            catch (PDOException $e) {
                $rs['errMsg'] = $e->getMessage();
                goto Result;
            }
    
            Result:
            return $rs;
        }
    }
?>