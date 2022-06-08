<?php
    class M_banner extends Database {
        protected $table = 'infodemo_banners';

        public function getList() : array {
            $query = "SELECT bn.*, sh.Name AS name\n
                FROM {$this->table} bn, infodemo_shops sh\n
                WHERE sh.IsSpecial= 1 AND sh.ShopId = bn.ParentId AND bn.IsShop = 1\n
                UNION\n
                SELECT bn.*, ar.AreaName AS name\n
                FROM {$this->table} bn, infodemo_areas ar\n
                WHERE ar.AreaId = bn.ParentId AND bn.IsShop <> 1";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getById( int $id ) : array {
            $stmt = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE BannerId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                $upload = __DIR__ . '/../../../data_files/';
                $arrImg = ['png','jpeg','jpg','gif'];
                // Banner1, Banner2, Banner3
                for ( $i = 1; $i <= 3; $i++ ) {
                    if ( $data['Banner' . $i] != null ) {
                        if ( $data['Banner' . $i]["size"] > 1000000 ) {
                            throw new Exception( "可能の容量を超えります" );
                        }

                        //<2020/08/03>↓↓<KhanhDinh>
                        $file_ext= strtolower(explode(".",$data['Banner' . $i]['name'])[1]);
                        
                        if(!in_array($file_ext, $arrImg))
                            throw new Exception( "画像ではありません" );
                        //<2020/08/03>↑↑ <KhanhDinh>
                         $rs['aa'] = $file_ext;
                        if ( file_exists( $upload . $this->_prefix . $data['Banner' . $i]["name"] ) ) {
                            unlink( $upload . $this->_prefix . $data['Banner' . $i]["name"] );
                        }

                        $name['Banner' . $i] = $data['Banner' . $i]["name"];
                        move_uploaded_file( $data['Banner' . $i]["tmp_name"], $upload . $this->_prefix . $name['Banner' . $i] );
                    }
                }

                $query = "INSERT INTO {$this->table} (`Banner1`, `IsShow1`, `Banner2`, `IsShow2`, `Banner3`, `IsShow3`, `Description`, `IsShop`, `ParentId`)" .
                        "VALUES (" .
                        ( !empty($name['Banner1']) ? "'" . $name['Banner1'] . "'" : 'NULL') . ',' .
                        $data["IsShow1"] . "," .
                        ( !empty($name['Banner2']) ? "'" . $name['Banner2'] . "'" : 'NULL') . ',' .
                        $data["IsShow2"] . "," .
                        ( !empty($name['Banner3']) ? "'" . $name['Banner3'] . "'" : 'NULL') . ',' .
                        $data["IsShow3"] . "," .
                        "'" . $data["Description"] . "'," .
                        $data["IsShop"] . "," .
                        $data["ParentId"] . ")";
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
                $upload = __DIR__ . '/../../../data_files/';
                $arrImg = ['png','jpeg','jpg','gif'];
                // Banner1, Banner2, Banner3
                for ( $i = 1; $i <= 3; $i++ ) {
                    if ( $data['Banner' . $i] != null ) {
                        if ( $data['Banner' . $i]["size"] > 1000000 ) {
                            throw new Exception( "可能の容量を超えります" );
                        }

                        //<2020/08/03>↓↓<KhanhDinh>
                        $file_ext= strtolower(explode(".",$data['Banner' . $i]['name'])[1]);
                        
                        if(!in_array($file_ext, $arrImg))
                            throw new Exception( "画像ではありません" );
                        //<2020/08/03>↑↑ <KhanhDinh>

                        if ( file_exists( $upload . $this->_prefix . $data['Banner' . $i]["name"] ) ) {
                            unlink( $upload . $this->_prefix . $data['Banner' . $i]["name"] );
                        }

                        if ( !empty( $data['oldBanner1'] )
                        && file_exists( $upload . $this->_prefix . $data['oldBanner1'] ) ) {
                            unlink( $upload . $this->_prefix . $data['oldBanner1'] );
                        }

                        $name['Banner' . $i] = $data['Banner' . $i]["name"];
                        move_uploaded_file( $data['Banner' . $i]["tmp_name"], $upload . $this->_prefix . $name['Banner' . $i] );
                    }
                    else {
                        $name['Banner' . $i] = !empty( $data['oldBanner' . $i] ) ? $data['oldBanner' . $i] : 'NULL';
                    }
                }

                // SQL Update
                $query = "UPDATE {$this->table} SET " .
                        "`Banner1`=" . ( $name['Banner1'] == 'NULL' ? 'NULL' : "'" . $name['Banner1'] . "'" ) . "," .
                        "`IsShow1`=" . $data["IsShow1"] . "," .
                        "`Banner2`=" . ( $name['Banner2'] == 'NULL' ? 'NULL' : "'" . $name['Banner2'] . "'" ) . "," .
                        "`IsShow2`=" . $data["IsShow2"] . "," .
                        "`Banner3`=" . ( $name['Banner3'] == 'NULL' ? 'NULL' : "'" . $name['Banner3'] . "'" ) . "," .
                        "`IsShow3`=" . $data["IsShow3"] . "," .
                        "`Description`='" . $data["Description"] . "'," .
                        "`IsShop`=" . $data["IsShop"] . "," .
                        "`ParentId`=" . $data["ParentId"] . " WHERE `BannerId`=" . $data["id"];
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
                $banner = $this->conn->prepare( "SELECT * FROM {$this->table} WHERE BannerId = {$id}" );
                $banner->execute();
                $result = $banner->fetch(PDO::FETCH_ASSOC);

                if ( $result != false && count( $result ) > 0 ) {
                    $upload = __DIR__ . "/../../../data_files/";

                    // Banner1
                    if ( !empty( $result["Banner1"] ) ) {
                        if ( file_exists( $upload . $this->_prefix . $result["Banner1"] ) ) {
                            unlink( $upload . $this->_prefix . $result["Banner1"] );
                        }
                    }

                    // Banner2
                    if ( !empty( $result["Banner2"] ) ) {
                        if ( file_exists( $upload . $this->_prefix . $result["Banner2"] ) ) {
                            unlink( $upload . $this->_prefix . $result["Banner2"] );
                        }
                    }

                    // Banner3
                    if ( !empty( $result["Banner3"] ) ) {
                        if ( file_exists( $upload . $this->_prefix . $result["Banner3"] ) ) {
                            unlink( $upload . $this->_prefix . $result["Banner3"] );
                        }
                    }
                    $this->conn->exec( "DELETE FROM {$this->table} WHERE BannerId = {$id}" );
                }
                return "削除しました";
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
            $query = "SELECT bn.*, sh.Name AS name\n
                FROM {$this->table} bn, infodemo_shops sh\n
                WHERE sh.IsSpecial= 1 AND sh.ShopId = bn.ParentId AND bn.IsShop = 1\n
                UNION\n
                SELECT bn.*, ar.AreaName AS name\n
                FROM {$this->table} bn, infodemo_areas ar\n
                WHERE ar.AreaId = bn.ParentId AND bn.IsShop <> 1";
            $stmt = $this->conn->prepare( $query );
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check result statement
            if ( !($result != false && count($result) > 0) ) {
                $rs['empty'] = "空のデータ";
                goto Result;
            }

            $outPut = $this->_prefix . 'EXPORT_BANNERS_' . date('Ymd') . '.csv';
            $upload = __DIR__ . '/../../../data_files/' . $outPut;
            if ( file_exists( $upload ) ) {
                unlink( $upload );
            }

            // Put title
            $file = fopen( $upload, 'a' ) or die( 'ファイルを開くことができませんでした' );
            fputcsv( $file, [
                $this->convertJP( "種類" ),
                $this->convertJP( "バナー1" ),
                $this->convertJP( "バナー2" ),
                $this->convertJP( "バナー3" ),
                $this->convertJP( "備考" )
            ]);

            // Put content
            $content = "";
            foreach ( $result as $data ) {
                // Name
                $name = $data["name"] . ( intval( $data["IsShop"] ) == 1 ? "店" : "区" );
                $name = $this->convertJP( $name );

                // Banner1
                $banner1 = $data["Banner1"];

                // Banner2ss
                $banner2 = $data["Banner2"];

                // Banner3
                $banner3 = $data["Banner3"];

                // Description
                $description = $this->convertJP( $data["Description"] );
                fputcsv( $file, [$name, $banner1, $banner2, $banner3, $description] );
            }

            fclose( $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }
    }
?>
