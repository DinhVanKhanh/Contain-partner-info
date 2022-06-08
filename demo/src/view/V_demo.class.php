<?php
    class V_demo {
        private $_prefix = 'DEMO_';

        public function loadTodouhukenList( array $data ) : array {
            $selectTo  = '<select name="searchTo" id="searchTo">';
            $selectTo .= '<option value="-1" selected="true">すべて</option>';
            if ( count($data) > 0 ) {
                foreach ( $data as $value ) {
                    $selectTo .= '<option value="' . $value["TodouhukenId"] . '">' . $value["TodouhukenName"] . '</option>';
                }
            }
            $selectTo .= '</select>';
            $result['view'] = $selectTo;
            return $result;
        }

        public function loadResultScheduleFromSearch( array $data ) : array {
            // イベント日付一覧
            $events = array();

            // 「住所」オートコンプリートのデータ
            $autoAddress = array();

            // 「販売店名」オートコンプリートのデータ
            $autoShopName = array();

            if ( count( $data ) < 1 ) {
                $view = '<div class="inner">
                            <table class="table-area">
                                <tr>
                                    <th class="th_date">開催日</th>
                                    <th class="th_time">時間</th>
                                    <th class="th_to">都市</th>
                                    <th>会場</th>
                                </tr>
                                <tr>
                                    <td colspan="4" class="td_dataEmpty">現在、店頭デモ開催の予定はありません。</td>
                                </tr>
                            </table>
                        </div>';
            }
            else {
                $view = '<div class="inner">
                            <table class="table-area">
                                <tr>
                                    <th class="th_date">開催日</th>
                                    <th class="th_time">時間</th>
                                    <th class="th_to">都市</th>
                                    <th colspan="2">会場</th>
                                </tr>';

                foreach ( $data as $value ) {
                    $is_active    = (int) $value["IsActive"];
                    $is_highlight = (int) $value["IsHighlight"];

                    $ex_name        = $value["TodouhukenName"];
                    $ex_address1    = $value["Address_1"];
                    $ex_address2    = $value["Address_2"];
                    $ex_storeN1     = $value["storeName1"];
                    $ex_storeN2     = $value["storeName2"];
                    $ex_shopname    = $value["ShopName"];
                    $ex_map         = $value["Map"];
                    $ex_tel         = $value["Tel"];
                    $time_start     = $value["TimeFrom"];
                    $time_end       = $value["TimeTo"];
                    $ex_description = nl2br( $value["Description"] );
                    $ex_pdf         = $value["Pdf"];
                    $ex_date        = $value["ScheduleDate"];
                    list( $events[], $ex_day_w, $ex_day_m, $ex_month ) = explode( " ", date('Y-m-d D j n', strtotime( $ex_date ) ) );

                    // イベント日付一覧

                    // 「住所」オートコンプリートのデータ
                    if ( !in_array( $ex_shopname, $autoShopName ) ) {
                        $autoShopName[] = $ex_shopname;
                    }

                    // 「販売店名」オートコンプリートのデータ
                    $address = $ex_storeN1 . ' ' . $ex_storeN2;
                    if ( !in_array( $address, $autoAddress ) ) {
                        $autoAddress[] = $address;
                    }

                    $view .= '<tr>' .
                                '<td class="td_date' . ( $is_active == 1 ? ' del' : '' ) . '">' .
                                    '<span>' . $ex_month . '.' . $ex_day_m . '</span>' . $ex_day_w .
                                    ( $is_highlight == 1 ? '</br><img src="assets/images/pickup.png"/>' : '' ) .
                                '</td>' .
                                '<td class="td_time' . ( $is_active == 1 ? ' del' : '' ) . '">' .
                                    $time_start . ' <img src="assets/images/icon_01.jpg" alt="" class="hide-sp" />' .
                                    '<img src="assets/images/icon_04.jpg" alt="" class="hide-pc" />' . $time_end .
                                '</td>' .
                                '<td class="td_to' . ( $is_active == 1 ? ' del' : '' ) . '">' .
                                    $ex_name .
                                '</td>' .
                                '<td class="td_address">' .
                                    '<span class="ttl' . ( $is_active == 1 ? ' del' : '' ) . '">' .
                                        ( !empty( $ex_map ) ? '<a href="' . $ex_map . '" target="_blank" > ' . $ex_storeN1 . '</a>' : ' ' . $ex_storeN1 ) .
                                        ' </br> ' . $ex_storeN2 . '<br />' .
                                    '</span>' .
                                    ( $is_active == 1 ? '<span style="text-decoration: none !important;">Tel: </span>' : 'Tel: ' . $ex_tel ) .
                                    '<span class="note">' . $ex_description . '</span>' .
                                '</td>' .
                                '<td class="td_link">' .
                                    ( !empty( $ex_pdf ) ? '<a href="../data_files/' . $this->_prefix . $ex_pdf . '" target="_blank"><img src="assets/images/icon_02.jpg" alt="" /></a>' : '' ) .
                                    ( ( !empty( $ex_address1 ) || !empty( $ex_address2 ) ) ? '<a href="http://maps.google.com/?q=' . $ex_address1 . $ex_address2 . '" target="_blank"><img src="assets/images/icon_03.jpg" alt="" /></a>' : '' ) .
                                    '</td>' .
                            '</tr>';
                }

                $view .= '</table>' .
                        '</div>';
            }

            $result['view'] = $view;
            $result['events'] = $events;
            $result['autoData'] = array_merge( $autoShopName, $autoAddress );
            return $result;
        }
    }
?>
