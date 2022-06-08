<?php
    class V_shop {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result['view'] = '<table id="tblResult_a" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }
    
            $showdata = '<table id="tblResult_a" class="show_ban" >' .
                            '<tr>
                                <th class="w5">No.</th>
                                <th>販売店コード</th>
                                <th>販売店名</th>
                                <th>特定</th>
                                <th>備考</th>
                                <th class="w_td_btn">操作</th>
                            </tr>';
            $i = 1;
            foreach ( $data as $value ) {
                $maru = ( $value["IsSpecial"] == 1 ) ? "○" : '';
                $showdata .= '<tr>
                                <td class="center areaIndex">' . $i . '</td>
                                <td class="center areaCode">' . $value["Code"] . '</td>
                                <td class="areaName">' . $value["Name"] . '</td>
                                <td class="areaName center">' . $maru . '</td>
                                <td class="areaName">' . nl2br($value["Description"]) . '</td>
                                <td class="center areaBtn padd_10 ">
                                    <button name="btnConfirmDel" onclick=$("#shopTypeId").val(this.id) class="btnDel btnConfirmDel btnConfirm" id="' . $value["ShopId"] . '" value="' . $value["ShopId"] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox5" id="' . $value["ShopId"] . '" value="' . $value["ShopId"] . '" href="#inline0">修正</button>
                                </td>
                            </tr>';
                $i++;
            }
            $showdata .= '</table>';
            $result['view']  = $showdata;

            Result:
            return $result;
        }
    }
?>