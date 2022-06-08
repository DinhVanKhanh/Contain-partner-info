<?php
    class V_area {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblResult_u" class="show_ban">
                        <tr>
                            <th class="w5"></th> <!--CHECK BOX-->
                            <th class="w7">No.</th>
                            <th class="w16">地区コード</th>
                            <th>地区名</th>
                            <th class="w_td_btn">操作</th>
                        </tr>';

            $i = 1;
            foreach ( $data as $value ) {
                $showdata .= '<tr>
                                <td class="center areaBtn"><input type="checkbox" id="' . $value["AreaId"] . '"/></td>
                                <td class="center areaCode">' . $i . '</td>
                                <td class="center areaCode">' . $value["AreaCode"] . '</td>
                                <td class="areaName">' . $value["AreaName"] . '</td>
                                <td class="center areaBtn padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#areaId\').val(this.id)" class="btnDel btnConfirmDel btnConfirm1" id="' . $value["AreaId"] . '" value="' . $value["AreaId"] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox3" id="' . $value["AreaId"] . '" value="' . $value["AreaId"] . '" href="#inline0">修正</button>
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