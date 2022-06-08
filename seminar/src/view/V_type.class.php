<?php
    class V_type {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblResult_u" class="show_ban">
                        <tr>
                            <th class="w33">種類</th>
                            <th>種類名</th>
                            <th>説明</th>
                            <th class="center areaBtn padd_10 w17">操作</th>
                        </tr>';

            foreach ( $data as $value ) {
                switch ($value['TypesId']) {
                    case 1:
                        $class = 'kaisha';
                        break;

                    case 2:
                        $class = 'taishou';
                        break;
                    
                    default:
                    $class = 'kousu';
                }
                $showdata .= '<tr class="' . $class . '">
                                <td class="center types">' . $value['TypesId'] . '</td>
                                <td class="center typesName">' . $value['TypesName'] . '</td>
                                <td class="center typesName">' . nl2br( $value['Description'] ) . '</td>
                                <td class="center typesBtn padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#typesId\').val(this.id)" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['TypesId'] . '" value="' . $value['TypesId'] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox3" id="' . $value['TypesId'] . '" value="' . $value['TypesId'] . '" href="#inline0">修正</button>
                                </td>
                            </tr>';
            }
            $showdata .= '</table>';
            $result['view']  = $showdata;

            Result:
            return $result;
        }
    }
?>