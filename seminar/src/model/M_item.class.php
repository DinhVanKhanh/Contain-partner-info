<?php
    class M_item extends Database {
        protected $table = 'infoseminar_items';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY ItemCode,ItemName ASC" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE ItemId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                // Check record items
                $items = $this->conn->prepare( "SELECT ItemCode FROM {$this->table} WHERE ItemCode='" . $data['code'] . "' OR ItemName='" . $data['name'] . "'" );
                $items->execute();
                if ( $items->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "INSERT INTO {$this->table}(ItemCode, ItemName, `Type`) VALUES ('" . $data["code"] . "','" . $data["name"] . "', " . $data['type'] . ")";
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
                $items = $this->conn->prepare( "SELECT ItemCode FROM {$this->table} WHERE (ItemCode='" . $data['code'] . "' OR ItemName='" . $data['name'] . "') AND ItemId <> " . $data['id'] );
                $items->execute();
                if ( $items->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "UPDATE {$this->table} SET ItemCode='" . $data["code"] . "', 
                    ItemName='" . $data["name"] . "', `Type`=" . $data['type'] . " WHERE ItemId=" . $data['id'];
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
                $this->conn->exec( "DELETE FROM infoseminar_sumary WHERE SeminarClass1 = {$id} OR SeminarClass2 = {$id} OR SeminarClass3 = {$id}" );
                $this->conn->exec( "DELETE FROM {$this->table} WHERE ItemId = {$id}" );
                return '削除しました';
            }
            catch (PDOException $e) {
                return "削除失敗";
            }
        }
    }
?>