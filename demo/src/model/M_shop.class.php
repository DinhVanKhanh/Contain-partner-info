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

    //↓↓　<2020/09/23> <YenNhi> <check record is exist>
        private function dataExists(Array $data, $flag = self::FLAG_ADD) : bool {
            extract($data);
            $query = "SELECT Name FROM {$this->table} WHERE Code= :Code";
            if ($flag == self::FLAG_EDIT)
            {
                $query .= " AND ShopId <> :ShopId";
                $shops = $this->conn->prepare($query);
                $shops->bindParam(':ShopId', $id, PDO::PARAM_INT, 11);
            } 
            else
            {
                $shops = $this->conn->prepare($query);
            } 
            $shops->bindParam(':Code', $code, PDO::PARAM_STR, 30);
            $shops->execute();
            return ($shops->rowCount() > 0)  ? true : false;
        }
    //↑↑　<2020/09/23> <YenNhi> <check record is exist>

        public function add( Array $data ) : array  {
            try {
            //　↓↓　<2020/09/24> <YenNhi> <avoid SQL injection>
                // Check record exists
                // $shops = $this->conn->prepare( "SELECT Name FROM {$this->table} WHERE Code='" . $data['code'] . "'" );
                // $shops->execute();
                
                // $query = "INSERT INTO {$this->table} (`Code`, `Name`, `IsSpecial`, `Description`)
                //         VALUES ('" . $data["code"] . "', '" . $data["name"] .
                //         "', " . $data["IsSpecial"] . ", '" . $data["Descript"] . "')";
                //$this->conn->exec( $query );

                // Check record exists
                if ( $this->dataExists($data) ) {
                   throw new Exception( '販売店コード が既に存在しています。' );
                }
                extract($data);
                $query = "INSERT INTO {$this->table} (`Code`, `Name`, `IsSpecial`, `Description`) VALUES ( :Code, :Name, :IsSpecial , :Description)";
                $shop  = $this->conn->prepare( $query );
                $shop->bindParam(':Code', $code, PDO::PARAM_STR, 30);
                $shop->bindParam(':Name', $name, PDO::PARAM_STR, 50);
                $shop->bindParam(':IsSpecial', $IsSpecial, PDO::PARAM_INT, 11);
                $shop->bindParam(':Description', $Descript, PDO::PARAM_STR, 500);
                $shop->execute();
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
                // $shops = $this->conn->prepare( "SELECT Name FROM {$this->table} WHERE Code='" . $data['code'] . "' AND ShopId <> " . $data['id'] );
                // $shops->execute();

                // $query = "UPDATE {$this->table} SET `Code`='" . $data["code"] . "',
                //         `Name`='" . $data["name"] . "',
                //         `IsSpecial`=" . $data["IsSpecial"] . ",
                //         `Description`='" . $data["Descript"] . "'\n
                //         WHERE `ShopId`=" . $data["id"];
                // $this->conn->exec( $query );

                // Check record exists
                if ( $this->dataExists($data, self::FLAG_EDIT) ) {
                    throw new Exception( '販売店コード が既に存在しています。' );
                }

                extract($data);
                $query = "UPDATE {$this->table} 
                          SET Code = :Code, Name = :Name, IsSpecial = :IsSpecial, Description = :Description
                          WHERE ShopId = :ShopId";
                $shop = $this->conn->prepare($query);
                $shop->bindParam(':Code', $code, PDO::PARAM_STR, 30);
                $shop->bindParam(':Name', $name, PDO::PARAM_STR, 50);
                $shop->bindParam(':IsSpecial', $IsSpecial, PDO::PARAM_INT, 11);
                $shop->bindParam(':Description', $Descript, PDO::PARAM_STR, 500);
                $shop->bindParam(':ShopId', $id, PDO::PARAM_STR, 500);
                $shop->execute();
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
            $list = $this->getList();

            // Check result statement
            if ( !$list ) {
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

            foreach ( $list as $item ) {
                // Code
                $item["Code"] = $this->convertJP( $item["Code"] );

                // Name
                $item["Name"] = $this->convertJP( $item["Name"] );

                // Is special
                $item["IsSpecial"] = (int) $item["IsSpecial"];

                // Description
                $item['description'] = $this->convertJP( $item["Description"] );

                fputcsv( $file, [$item["Code"], $item["Name"], $item["IsSpecial"], $item['description']] );
            }
            fclose(  $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }
    }
?>
