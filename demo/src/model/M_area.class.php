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

        public function add( Array $data ) : array  {
            try {
                // Check record exists
                $areas = $this->conn->prepare( "SELECT AreaCode FROM {$this->table} WHERE AreaCode='" . $data["code"] . "' OR AreaName='" . $data["name"] . "'" );
                $areas->execute();
                if ( $areas->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                // Get max display no
                $displayNo = $this->conn->prepare( "SELECT MAX(DisplayNo) as NO1 FROM {$this->table}" );
                $displayNo->execute();
                $displayNo = $displayNo->rowCount() > 0 ? $displayNo->fetch(PDO::FETCH_ASSOC)['NO1'] : 0;

                $query = "INSERT INTO {$this->table}(AreaCode, AreaName, DisplayNo) VALUES ('" . $data["code"] . "','" . $data["name"] . "', " . $displayNo . ")";
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
                $areas = $this->conn->prepare( "SELECT AreaCode FROM {$this->table} WHERE (AreaCode='" . $data["code"] . "' OR AreaName='" . $data["name"] . "') AND AreaId <> " . $data['id'] );
                $areas->execute();
                if ( $areas->rowCount() > 0 ) {
                    $rs['errMsg'] = '重複する値';
                    goto Result;
                }

                $query = "UPDATE {$this->table} SET AreaCode='" . $data["code"] . "',
                    AreaName='" . $data["name"] . "' where AreaId=" . $data['id'];
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
                // Check in todouhukens
                $todo = $this->conn->prepare( "SELECT TodouhukenCode FROM infodemo_todouhukens WHERE AreaId = {$id}" );
                $todo->execute();
                if ( $todo->rowCount() > 0 ) {
                    $msgCheck1 = "都市管理";
                }

                // Check in banners
                $banner = $this->conn->prepare( "SELECT BannerId FROM infodemo_banners WHERE ParentId = {$id}" );
                $banner->execute();
                if ( $banner->rowCount() > 0 ) {
                    $msgCheck2 = "バナー";
                }

                if ( !empty($msgCheck1) && !empty($msgCheck2) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck1 . '、' . $msgCheck2 . "で地区います" );
                }
                elseif ( !empty($msgCheck1) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck1 . "で地区います" );
                }
                elseif ( !empty($msgCheck2) ) {
                    throw new Exception( 'この会場は削除できません。 ' . $msgCheck2 . "で地区います" );
                }

                $this->conn->exec( "DELETE FROM {$this->table} WHERE AreaId = {$id}" );
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
            $this->conn->exec( "UPDATE {$this->table} SET DisplayNo = " . $upIdx . " WHERE areaId = " . $curId );
            $this->conn->exec( "UPDATE {$this->table} SET DisplayNo = " . $curIdx . " where areaId = " . $upId );
            return true;
        }

        // Export data to csv
        public function exportCSV() : array {
            $stmt = $this->conn->prepare( "SELECT AreaCode, AreaName FROM {$this->table} ORDER BY AreaId" );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check result statement
            if ( !($result != false && count($result) > 0) ) {
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
            foreach ( $result as $data ) {
                // Code
                $code = $this->convertJP( $data["AreaCode"] );

                // Name
                $name = $this->convertJP( $data["AreaName"] );
                fputcsv( $file, [$code, $name] );
            }

            fclose( $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }
    }
?>
