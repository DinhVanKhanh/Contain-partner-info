<?php
    class M_demo extends Database {
        public function getResultScheduleFromSeach( $areaId, $shopId, $todokukenId, $scheduleDate, $address ) : array {
            $query = "SELECT sch.Date as 'ScheduleDate', sch.TimeFrom, sch.TimeTo, t.TodouhukenName,
                            t.TodouhukenId, sh.Name as 'ShopName', m.storeName1, m.storeName2,
                            m.Address_1, m.Address_2, sch.Description, sch.Pdf, m.Map, m.Tel, sch.IsActive, sch.IsHighlight \n
                    FROM infodemo_schedules sch INNER JOIN infodemo_shops sh ON sh.ShopId = sch.ShopId\n
                        INNER JOIN infodemo_meetingplaces m ON m.MeetingPlaceId = sch.MeetingPlaceId\n
                        INNER JOIN infodemo_todouhukens t ON t.TodouhukenId = m.TodouhukenId \n
                    WHERE %s\n
                    ORDER BY sch.Date ASC";

            // 地区ID、または、販売店IDで探す
            $keyId = 0;
            $where = !empty( $areaId ) ? "t.AreaId = " . $areaId : "sh.ShopId = " . $shopId;

            // 都市で探す
            if ( !empty( $todokukenId ) && $todokukenId != -1 ) {
                $where .= " AND t.TodouhukenId = " . $todokukenId;
            }

            // 開催日で探す
            if ( !empty( $scheduleDate ) ) {
                $where .= " AND sch.Date = '" . date( "Y-m-d", strtotime( $scheduleDate ) ) . "'";
            }

            // 住所で探す
            if ( !empty( $address ) ) {
                $where .= " AND (sh.Name LIKE '%" . $address . "%' OR CONCAT(m.storeName1, ' ', m.storeName2) LIKE '%" . $address . "%' )";
            }

            $stmt = $this->conn->prepare( sprintf( $query, $where ) );
            $stmt->execute();

            $result = $stmt->rowCount() > 0 ? $stmt->fetchAll( PDO::FETCH_ASSOC ) : array();
            return $result;
        }

        public function getTodouhukenListByAreaId( int $area_id ) : array {
            $query = "SELECT `TodouhukenId`, `TodouhukenName` FROM `infodemo_todouhukens` %s ORDER BY TodouhukenName";
            $stmt = $this->conn->prepare( sprintf( $query, $area_id != -1 ? "WHERE AreaId =" . $area_id : "" ) );
            $stmt->execute();

            $result = $stmt->rowCount() > 0 ? $stmt->fetchAll( PDO::FETCH_ASSOC ) : array();
            return $result;
        }

        public function getTodouhukenListByShopId( int $shopId ) : array {
            $query = "SELECT DISTINCT t.TodouhukenId, t.TodouhukenName\n
                    FROM infodemo_schedules sch\n
                        INNER JOIN infodemo_meetingplaces m ON m.MeetingPlaceId = sch.MeetingPlaceId\n
                        INNER JOIN infodemo_todouhukens t ON t.TodouhukenId = m.TodouhukenId \n
                    WHERE sch.ShopId = " . $shopId . " \n
                    ORDER BY t.TodouhukenId";

            $stmt = $this->conn->prepare( $query );
            $stmt->execute();

            $result = $stmt->rowCount() > 0 ? $stmt->fetchAll( PDO::FETCH_ASSOC ) : array();
            return $result;
        }

        public function getBanner( int $parentId, int $isShop) : array {
            $query = "SELECT * FROM infodemo_banners WHERE ParentId=" . $parentId . " AND IsShop=" . $isShop;
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();

            $result = array();
            if ( $stmt->rowCount() > 0 ) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $result["Banner1"] = (intval($data["IsShow1"]) == 1 && !empty($data["Banner1"])) ? $this->_prefix . $data["Banner1"] : "";
                $result["Banner2"] = (intval($data["IsShow2"]) == 1 && !empty($data["Banner2"])) ? $this->_prefix . $data["Banner2"] : "";
                $result["Banner3"] = (intval($data["IsShow3"]) == 1 && !empty($data["Banner3"])) ? $this->_prefix . $data["Banner3"] : "";
            }
            return $result;
        }

        public function getAreaList() {
            $query = "SELECT AreaId, AreaCode, AreaName FROM infodemo_areas ORDER BY DisplayNo";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getShopName( int $shopId ) {
            $query = "SELECT `Name` FROM infodemo_shops WHERE ShopId = " . $shopId;
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getListSpecialShop() {
            $query = "SELECT ShopId, Name FROM infodemo_shops WHERE IsSpecial = 1";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getListNormalShop() {
            $query = "SELECT ShopId, Name FROM infodemo_shops WHERE IsSpecial = 0";
			$stmt = $this->conn->prepare( $query );
			$stmt->execute();
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>
