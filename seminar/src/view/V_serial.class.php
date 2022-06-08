<?php
    class V_serial {
        public function loadList( Array $data, Array $sample ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="tbl_searchmng td_vmiddle"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
                goto Result;
            }

            $getSampleNameById = function ( $id ) {
                global $sample;
                foreach ( $sample as $val ) {
                    if ( intval( $val['SampleId'] ) == intval( $id ) ) {
                        return $val['SampleName'];
                    }
                }
                return '';
            };

            $showdata = '<table id="tblResult_u" class="tbl_searchmng td_vmiddle">
                        <tr>
                            <th class="w20">セミナー名</th>
                            <th >シリアルNo</th>    
                            <th class="w30">備考</th>
                            <th class="w10">操作</th>
                        </tr>';

            foreach ( $data as $value ) {
                $showdata .= '<tr>
                                <td class="center">' . $getSampleNameById( $value['SampleId'] ) . '</td>
                                <td class="center">' . htmlspecialchars( $value['SerialNumber'] ) . '</td>
                                <td>' . nl2br( $value['Note'] ) . '</td>
                                <td class="center typesBtn padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#SerialId\').val(this.id)" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['SerialId'] . '" value="' . $value['SerialId'] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox4" id="' . $value['SerialId'] . '" value="' . $value['SerialId'] . '" href="#inline0">修正</button>
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