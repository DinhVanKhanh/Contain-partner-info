<?php
    class V_item {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblTypes" class="show_ban"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblTypes" class="show_ban">
                        <tr>
                            <th class="w27">種類</th>
                            <th class="w25">項目コード</th>
                            <th>項目名</th>
                            <th class="center areaBtn padd_10 w17">操作</th>
                        </tr>';

            $i = 1;
            foreach ( $data as $value ) {
                switch ( $value['Type'] ) {
                    case 1:
                        $type = '運営';
                        break;

                    case 2:
                        $type = '対象製品';
                        break;

                    default:
                        $type = 'コース';
                        break;
                }
                $showdata .= '<tr>
                                <td class="center types">' . $type . '</td>
                                <td class="center typesCode">' . htmlspecialchars($value['ItemCode']) . '</td>
                                <td class="center typesName">' . htmlspecialchars($value['ItemName']) . '</td>
                                <td class="center typesBtn padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#ItemId\').val(this.id)" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['ItemId'] . '" value="' . $value['ItemId'] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox3" id="' . $value['ItemId'] . '" value="' . $value['ItemId'] . '" href="#inline0">修正</button>
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