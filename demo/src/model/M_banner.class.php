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
            $stmt = $this->conn->prepare( "SELECT IsShop, ParentId, Banner1, IsShow1, Banner2, IsShow2, Banner3, IsShow3, `Description`FROM {$this->table} WHERE BannerId = {$id}"  );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function add( Array $data ) : array  {
            try {
                if ( $this->dataExists( $data ) ) {
                    throw new Exception( "このバナーが登録されていました。" );
                }

                $upload = __DIR__ . '/../../../data_files/';
                $arrImg = ['png','jpeg','jpg','gif'];

                // Banner1, Banner2, Banner3
                for ( $i = 1; $i <= 3; $i++ ) {
                    if ( $data['Banner' . $i] != null ) {
                        if ( $data['Banner' . $i]["size"] > 1000000 ) {
                            throw new Exception( "可能の容量を超えります" );
                        }

                    //<2020/08/03>↓↓<KhanhDinh>
                        //　↓↓　＜2020/09/24＞　＜VinhDao＞　＜修正＞
                            // $file_ext = strtolower(explode(".",$data['Banner' . $i]['name'])[1]);
                            $file_ext = mb_strrpos( $data['Banner' . $i]['name'], '.' ) + 1;
                            $file_ext = strtolower( mb_substr($data['Banner' . $i]['name'], $file_ext) );
                        //　↑↑　＜2020/09/24＞　＜VinhDao＞　＜修正＞
                        
                        if (!in_array($file_ext, $arrImg)) {
                            throw new Exception("画像ではありません");
                        }
                    //<2020/08/03>↑↑ <KhanhDinh>

                        if ( file_exists( $upload . $this->_prefix . $data['Banner' . $i]["name"] ) ) {
                            unlink( $upload . $this->_prefix . $data['Banner' . $i]["name"] );
                        }

                        $name['Banner' . $i] = $data['Banner' . $i]["name"];
                        if ( !move_uploaded_file( $data['Banner' . $i]["tmp_name"], $upload . $this->_prefix . $name['Banner' . $i] ) ) {
                            throw new Exception('ファイルのアップロード時にエラーが発生しました');
                        }
                    }
                }

                extract($data);
                $query = "INSERT INTO {$this->table} (`Banner1`, `IsShow1`, `Banner2`, `IsShow2`, `Banner3`, `IsShow3`, `Description`, `IsShop`, `ParentId`)" .
                        "VALUES (:Banner1, :IsShow1, :Banner2, :IsShow2, :Banner3, :IsShow3, :Description, :IsShop, :ParentId)";

                $Banner1 = $name['Banner1'] ?? null;
                $Banner2 = $name['Banner2'] ?? null;
                $Banner3 = $name['Banner3'] ?? null;

                $stmt = $this->conn->prepare( $query );
                $stmt->bindParam(':ParentId', $ParentId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':IsShop', $IsShop, PDO::PARAM_INT, 1);
                $stmt->bindParam(':Banner1', $Banner1, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow1', $IsShow1, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Banner2', $Banner2, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow2', $IsShow2, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Banner3', $Banner3, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow3', $IsShow3, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Description', $Description, PDO::PARAM_STR, 500);
                $stmt->execute();
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
                if ( $this->dataExists( $data, self::FLAG_EDIT ) ) {
                    throw new Exception( "このバナーが登録されていました。" );
                }

                $upload = __DIR__ . '/../../../data_files/';
                $arrImg = ['png','jpeg','jpg','gif'];

                // Banner1, Banner2, Banner3
                for ( $i = 1; $i <= 3; $i++ ) {
                    if ( $data['Banner' . $i] != null ) {
                        if ( $data['Banner' . $i]["size"] > 1000000 ) {
                            throw new Exception( "可能の容量を超えります" );
                        }

                    //<2020/08/03>↓↓<KhanhDinh>
                        //　↓↓　＜2020/09/24＞　＜VinhDao＞　＜修正＞
                            // $file_ext = strtolower(explode(".",$data['Banner' . $i]['name'])[1]);
                            $file_ext = mb_strrpos( $data['Banner' . $i]['name'], '.' ) + 1;
                            $file_ext = strtolower( mb_substr($data['Banner' . $i]['name'], $file_ext) );
                        //　↑↑　＜2020/09/24＞　＜VinhDao＞　＜修正＞
                        
                        if  ( !in_array($file_ext, $arrImg) ) {
                            throw new Exception("画像ではありません");
                        }
                    //<2020/08/03>↑↑ <KhanhDinh>

                        if ( file_exists( $upload . $this->_prefix . $data['Banner' . $i]["name"] ) ) {
                            unlink( $upload . $this->_prefix . $data['Banner' . $i]["name"] );
                        }

                        if ( !empty( $data['oldBanner1'] )
                        && file_exists( $upload . $this->_prefix . $data['oldBanner1'] ) ) {
                            unlink( $upload . $this->_prefix . $data['oldBanner1'] );
                        }

                        $name['Banner' . $i] = $data['Banner' . $i]["name"];
                        if ( !move_uploaded_file( $data['Banner' . $i]["tmp_name"], $upload . $this->_prefix . $name['Banner' . $i] ) ) {
                            throw new Exception('ファイルのアップロード時にエラーが発生しました');
                        }
                    }
                    else {
                        $name['Banner' . $i] = !empty( $data['oldBanner' . $i] ) ? $data['oldBanner' . $i] : null;
                    }
                }

                // SQL Update
                $query = "UPDATE {$this->table} SET " .
                            "`Banner1` = :Banner1," .
                            "`IsShow1` = :IsShow1," .
                            "`Banner2` = :Banner2," .
                            "`IsShow2` = :IsShow2," .
                            "`Banner3` = :Banner3," .
                            "`IsShow3` = :IsShow3," .
                            "`Description` = :Description," .
                            "`IsShop` = :IsShop," .
                            "`ParentId` = :ParentId WHERE `BannerId` = :BannerId";

                extract($data);
                $Banner1 = $name['Banner1'] ?? null;
                $Banner2 = $name['Banner2'] ?? null;
                $Banner3 = $name['Banner3'] ?? null;

                $stmt = $this->conn->prepare( $query );
                $stmt->bindParam(':BannerId', $BannerId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':ParentId', $ParentId, PDO::PARAM_INT, 11);
                $stmt->bindParam(':IsShop', $IsShop, PDO::PARAM_INT, 1);
                $stmt->bindParam(':Banner1', $Banner1, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow1', $IsShow1, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Banner2', $Banner2, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow2', $IsShow2, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Banner3', $Banner3, PDO::PARAM_STR | PDO::PARAM_NULL, 500);
                $stmt->bindParam(':IsShow3', $IsShow3, PDO::PARAM_INT, 11);
                $stmt->bindParam(':Description', $Description, PDO::PARAM_STR, 500);
                $stmt->execute();
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
            $list = $this->getList();
            if ( !$list ) {
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
            foreach ( $list as $item ) {
                // Name
                $name = $item["name"] . ( intval( $item["IsShop"] ) == 1 ? "店" : "区" );
                $name = $this->convertJP( $name );

                // Banner1
                $banner1 = $item["Banner1"];

                // Banner2ss
                $banner2 = $item["Banner2"];

                // Banner3
                $banner3 = $item["Banner3"];

                // Description
                $description = $this->convertJP( $item["Description"] );
                fputcsv( $file, [$name, $banner1, $banner2, $banner3, $description] );
            }

            fclose( $file );
            $rs['fileUrl'] = '../data_files/' . $outPut;

            Result:
            return $rs;
        }

        function dataExists( Array $data, $flag = self::FLAG_ADD ) : bool {
            try {
				extract($data);
				$query = "SELECT BannerId FROM {$this->table} WHERE ParentId = :ParentId AND IsShop = :IsShop";
				if ( $flag == self::FLAG_EDIT ) {
					$query .= " AND BannerId <> :BannerId;";
					$stmt = $this->conn->prepare( $query );
					$stmt->bindParam(':BannerId', $BannerId, PDO::PARAM_INT, 11);
				}
				else {
					$stmt = $this->conn->prepare( $query );
				}

				$stmt->bindParam(':ParentId', $ParentId, PDO::PARAM_INT, 11);
				$stmt->bindParam(':IsShop', $IsShop, PDO::PARAM_INT, 1);
				$stmt->execute();
				return $stmt->rowCount() > 0 ? true : false;
			}
			catch ( PDOException $e ) {
				return $e->getMessage();
			}
        }
    }
?>
