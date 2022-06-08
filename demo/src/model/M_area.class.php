<?php
    class M_area extends Database {
        protected $table = 'infodemo_areas';

        public function getList() : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} ORDER BY DisplayNo" );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE AreaId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

    //↓↓　<2020/09/23> <YenNhi> <check record is exist>
        private function dataExists(Array $data, $flag = self::FLAG_ADD) : bool {
            extract($data);
            $query = "SELECT AreaCode FROM {$this->table} WHERE (AreaCode= :AreaCode OR AreaName= :AreaName)";
            if ($flag == self::FLAG_EDIT)
            {
                $query .= " AND AreaId <> :AreaId";
                $areas = $this->conn->prepare($query);
                $areas->bindParam(':AreaId', $id, PDO::PARAM_INT, 11);
            } 
            else 
            {
                $areas = $this->conn->prepare($query);
            }
            $areas->bindParam(':AreaCode', $code, PDO::PARAM_STR, 30);
            $areas->bindParam(':AreaName', $name, PDO::PARAM_STR, 50);
            $areas->execute();

            return ($areas->rowCount() > 0)  ? true : false;
        }
    //↑↑　<2020/09/23> <YenNhi> <check record is exist>

        public function add( Array $data ) : array  {
            try {
            //　↓↓　<2020/09/23> <YenNhi> <avoid SQL injection>
                // Check record exists
                // $areas = $this->conn->prepare( "SELECT AreaCode FROM {$this->table} WHERE AreaCode= :AreaCode OR AreaName= :AreaName" );
                // $areas->bindParam(':AreaCode', $code, PDO::PARAM_STR, 30);
                // $areas->bindParam(':AreaName', $name, PDO::PARAM_STR, 50);
                // $areas->execute();

                // Check record exists
                if ( $this->dataExists($data) ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }
                extract($data);
            //　↑↑　<2020/09/23> <YenNhi> <avoid SQL injection>

                // Get max display no
                $displayNo = $this->conn->prepare( "SELECT MAX(DisplayNo) as NO1 FROM {$this->table}" );
                $displayNo->execute();
                $displayNo = $displayNo->rowCount() > 0 ? $displayNo->fetch(PDO::FETCH_ASSOC)['NO1'] : 0;

            //　↓↓　<2020/09/23> <YenNhi> <avoid SQL injection>
                $area = $this->conn->prepare("INSERT INTO {$this->table}(AreaCode, AreaName, DisplayNo) VALUES (:AreaCode, :AreaName, :DisplayNo)");
                $area->bindParam(':AreaCode', $code, PDO::PARAM_STR, 30);
                $area->bindParam(':AreaName', $name, PDO::PARAM_STR, 50);
                $area->bindParam(':DisplayNo', $displayNo, PDO::PARAM_INT, 11);
                $area->execute();
            //　↑↑　<2020/09/23> <YenNhi> <avoid SQL injection>
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
            //↓↓　<2020/09/23> <YenNhi> < avoid SQL injection>
                // Check record exists
                // $areas = $this->conn->prepare( "SELECT AreaCode FROM {$this->table} WHERE (AreaCode= :AreaCode OR AreaName= :AreaName) AND AreaId <> :AreaId");
                // $areas->bindParam(':AreaCode', $code, PDO::PARAM_STR, 30);
                // $areas->bindParam(':AreaName', $name, PDO::PARAM_STR, 50);
                // $areas->bindParam(':AreaId', $id, PDO::PARAM_INT, 11);
                // $areas->execute();

                // Check record exists
                if ( $this->dataExists($data, self::FLAG_EDIT) ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }
                extract($data);
                $query = $this->conn->prepare( "UPDATE {$this->table} SET AreaCode = :AreaCode, AreaName = :AreaName  where AreaId = :AreaId" );
                $query->bindParam(':AreaCode', $code, PDO::PARAM_STR, 30);
                $query->bindParam(':AreaName', $name, PDO::PARAM_STR, 50);
                $query->bindParam(':AreaId', $id, PDO::PARAM_INT, 11);
                $query->execute();                
            //↑↑　<2020/09/23> <YenNhi> < avoid SQL injection>
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
            //　↓↓　<2020/09/23> <YenNhi> <avoid SQL injection>
                // Check in todouhukens
                $todou = $this->conn->prepare( "SELECT TodouhukenCode FROM infodemo_todouhukens WHERE AreaId = :AreaId" );
                $todou->bindParam(':AreaId', $id, PDO::PARAM_INT, 11);
                $todou->execute();
                if ( $todou->rowCount() > 0 ) {
                    $msgCheck1 = "都市管理";
                }

                // Check in banners
                $banner = $this->conn->prepare( "SELECT BannerId FROM infodemo_banners WHERE ParentId = :ParentId" );
                $banner->bindParam(':ParentId', $id, PDO::PARAM_INT, 11);
                $banner->execute();
                if ( $banner->rowCount() > 0 ) {
                    $msgCheck2 = "バナー";
                }
            //　↑↑　<2020/09/23> <YenNhi> <avoid SQL injection>


                if ( !empty($msgCheck1) && !empty($msgCheck2) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck1 . '、' . $msgCheck2 . "で地区います" );
                }
                elseif ( !empty($msgCheck1) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck1 . "で地区います" );
                }
                elseif ( !empty($msgCheck2) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck2 . "で地区います" );
                }

            //　↓↓　<2020/09/23> <YenNhi> <avoid SQL injection>
                $query = $this->conn->prepare( "DELETE FROM {$this->table} WHERE AreaId = :AreaId" );
                $query->bindParam(':AreaId', $id, PDO::PARAM_INT, 11);
                $query->execute();
            //　↑↑　<2020/09/23> <YenNhi> <avoid SQL injection>
                return '削除しました';

            }
            catch (PDOException $e) {
                return "削除失敗";
            }
            catch (Exception $e) {
                return $e->getMessage();
            }
        }

        // Change order row
        public function changeOrderRow( int $curId, int $upId, int $curIdx, int $upIdx ) : bool {
           $query_curId = $this->conn->prepare("UPDATE {$this->table} SET DisplayNo = :DisplayNo WHERE areaId = :curId");
           $query_curId->bindParam(':DisplayNo', $upIdx, PDO::PARAM_INT, 11);
           $query_curId->bindParam(':curId', $curId, PDO::PARAM_INT, 11);
           $query_curId->execute();

           $query_upId = $this->conn->prepare("UPDATE {$this->table} SET DisplayNo = :DisplayNo WHERE areaId = :upId");
           $query_upId->bindParam(':DisplayNo', $curIdx, PDO::PARAM_INT, 11);
           $query_upId->bindParam(':upId', $upId, PDO::PARAM_INT, 11);
           $query_upId->execute();
            return true;
        }

        // Export data to csv
        public function exportCSV() : array {
            $list = $this->getList();

            // Check result statement
            if ( !$list ) {
                $rs['empty'] = "空のデータ";
                goto Result;
            }

            $outPut = $this->_prefix . 'EXPORT_AREAS_' . date('Ymd') . '.csv';
            $upload = __DIR__ . '/../../../data_files/' . $outPut;
            if ( file_exists( $upload ) ) {
                unlink( $upload );
            }

            // Put title
            $file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
            fputcsv( $file, [
                                $this->convertJP( "地区コード" ),
                                $this->convertJP( "地区名" )
                            ]);

            // Put content
            $content = "";
            foreach ( $list as $item ) {
                // Code
                $code = $this->convertJP( $item["AreaCode"] );

                // Name
                $name = $this->convertJP( $item["AreaName"] );
                fputcsv( $file, [$code, $name] );
            }

            fclose( $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }
    }
