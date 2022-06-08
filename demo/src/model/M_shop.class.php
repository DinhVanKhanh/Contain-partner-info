<?php
    class M_shop extends Database {
        protected $table = 'infodemo_shops';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY ShopId" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE ShopId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                // Check record exists
                $shops = $this->conn->prepare( "SELECT Name FROM {$this->table} WHERE Code='" . $data['code'] . "'" );
                $shops->execute();
                if ( $shops->rowCount() > 0 ) {
                   throw new Exception( '販売店コード が既に存在しています。' );
                }

                $query = "INSERT INTO {$this->table} (`Code`, `Name`, `IsSpecial`, `Description`)
                        VALUES ('" . $data["code"] . "', '" . $data["name"] .
                        "', " . $data["IsSpecial"] . ", '" . $data["Descript"] . "')";
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
                $shops = $this->conn->prepare( "SELECT Name FROM {$this->table} WHERE Code='" . $data['code'] . "' AND ShopId <> " . $data['id'] );
                $shops->execute();
                if ( $shops->rowCount() > 0 ) {
                    throw new Exception( '販売店コード が既に存在しています。' );
                }

                $query = "UPDATE {$this->table} SET `Code`='" . $data["code"] . "',
                        `Name`='" . $data["name"] . "',
                        `IsSpecial`=" . $data["IsSpecial"] . ",
                        `Description`='" . $data["Descript"] . "'\n
                        WHERE `ShopId`=" . $data["id"];
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
                $sch = $this->conn->prepare( "SELECT ScheduleId FROM infodemo_schedules WHERE ShopId = {$id}" );
                $sch->execute();
                if ( $sch->rowCount() > 0 ) {
                    throw new Exception( "この会場は削除できません。 スケジュールデモにはすでに存在しています。" );
                }

                $this->conn->exec( "DELETE FROM {$this->table} WHERE ShopId = {$id}" );
                return '削除しました';
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
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY ShopId" );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check result statement
            if ( !($result != false && count($result) > 0) ) {
                $rs['empty'] = "空のデータ";
                goto Result;
            }

            $outPut = $this->_prefix . 'EXPORT_SHOPS_' . date('Ymd') . '.csv';
            $upload = __DIR__ . '/../../../data_files/' . $outPut;
            if ( file_exists( $upload ) ) {
                unlink( $upload );
            }

            // Put title
            $file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
            fputcsv( $file, [
                $this->convertJP( "販売店コード" ) ,
                $this->convertJP( "販売店名" ) ,
                $this->convertJP( "特定" ) ,
                $this->convertJP( "備考" )
            ]);
            // Put content
            $content = "";

            foreach ( $result as $data ) {
                // Code
                $data["Code"] = $this->convertJP( $data["Code"] );

                // Name
                $data["Name"] = $this->convertJP( $data["Name"] );

                // Is special
                $data["IsSpecial"] = (int) $data["IsSpecial"];

                // Description
                $data['description'] = $this->convertJP( $data["Description"] );

                fputcsv( $file, [$data["Code"], $data["Name"], $data["IsSpecial"], $data['description']] );
            }
            fclose(  $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }
    }
?>
