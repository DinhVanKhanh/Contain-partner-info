<?php
    class V_banner {
        private $_prefix = 'DEMO_';

        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblResult_u" class="show_ban">
                        <tr>
                            <th>種類</th>
                            <th>バナー1</th>
                            <th>バナー2</th>
                            <th>バナー3</th>
                            <th>備考</th>
                            <th class="w_td_btn">操作</th>
                        </tr>';

            foreach ( $data as $value ) {
                $showdata .= '<tr>';
                // Name
                $showdata .= '<td class="center areaIndex">' . $value["name"] . '</td>';

                // Banner 1
                $showdata .= '<td class="center areaIndex">';
                if ( empty( $value["Banner1"] ) ) {
                    $showdata .= '<strong>DUMMY</strong>';
                }
                else {
                    $showdata .= '<img src="../data_files/' . $this->_prefix . $value["Banner1"] . '" width="100" />';
                    $showdata .= ( intval( $value["IsShow1"] ) == 1 ) ? '' : '<br>(無効)';
                }
                $showdata .= '</td>';

                // Banner 2
                $showdata .= '<td class="center areaIndex">';
                if ( empty( $value["Banner2"] ) ) {
                    $showdata .= '<strong>DUMMY</strong>';
                }
                else {
                    $showdata .= '<img src="../data_files/' . $this->_prefix . $value["Banner2"] . '" width="100" />';
                    $showdata .= ( intval( $value["IsShow2"] ) == 1 ) ? '' : '<br>(無効)';
                }
                $showdata .= '</td>';

                // Banner 3
                $showdata .= '<td class="center areaIndex">';
                if ( empty( $value["Banner3"] ) ) {
                    $showdata .= '<strong>DUMMY</strong>';
                }
                else {
                    $showdata .= '<img src="../data_files/' . $this->_prefix . $value["Banner3"] . '" width="100" />';
                    $showdata .= ( intval( $value["IsShow3"] ) == 1 ) ? '' : '<br>(無効)';
                }
                $showdata .= '</td>';

                // Description
                $showdata .= '<td class="center areaIndex">' . nl2br( $value["Description"] ) . '</td>';

                // Submit form
                $showdata .= '<td class="center areaBtn padd_10 ">
                                    <button name="btnConfirmDel" onclick=$("#bannerId").val(this.id) class="btnDel btnConfirmDel btnConfirm" id="' . $value["BannerId"] . '" value="' . $value["BannerId"] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancy_h650" id="' . $value["BannerId"] . '" value="' . $value["BannerId"] . '" href="#inline0">修正</button>
                                </td>';
                $showdata .= '</tr>';
            }
            $showdata .= '</table>';
            $result['view']  = $showdata;

            Result:
            return $result;
        }
    }
?>
