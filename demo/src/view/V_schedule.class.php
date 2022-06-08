<?php
    class V_schedule {
        private $_prefix = 'DEMO_';

        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showData = '<table id="tblResult_a" class="show_ban">
                        <tr>
                            <th style = "width:3%;"></th>       <!--check box-->
                            <th style = "width:10%;">開催日</th><!--date-->
                            <th class = "w8" >都市名</th>       <!--todouhuken name-->
                            <th style = "width:17%;">会場</th>  <!--address, phone-->
                            <th style = "width:6%;">時間帯</th> <!--time-->
                            <th style = "width:9%;">販売店</th> <!--shop-->

                            <th class = "center" style    = "width:4%;" >中止告知</th> <!--isActive-->
                            <th style = "width:5%;" class = "center">ピックアップ</th> <!--isSpecial-->
                            <th style = "width:20%;">備考</th>                         <!--description-->
                            <th class = "w5">PDF</th>                                  <!--PDF-->
                            <th style = "width:5.7%;">操作</th>                        <!--Button-->
                        </tr>';

            foreach ($data as $value) {
                $showData .= '<tr>
                                <td class="center areaIndex"><input onclick="checkBoxPromp(this.id);" type="checkbox" id="' . $value["ScheduleId"] . '"/></td>
                                <td class="center areaCode">' . $value["Date"] . '</td>
                                <td>' . $value["TodouhukenName"] . '</td>
                                <td>'
                                    . $value["storeName1"] . '</br>'
                                    . $value["storeName2"] . '</br>'
                                    . $value["Tel"] .
                                '</td>
                                <td class="areaName center">'
                                    . $value["TimeFrom"] . " <br>"
                                    . '<img src="assets/images/icon_01.jpg" alt="" /><br>'
                                    . $value["TimeTo"] .
                                '</td>
                                <td>' . $value["Name"] . '</td>' .
                                ( $value["IsActive"] == 1 ? '<td class="center">○</td>' : '<td></td>' ).
                                ( $value["IsHighlight"] == 1 ? '<td class="center"> ○ </td>' : '<td class="center"></td>' ) .
                                '<td>' . nl2br( $value["Description"] ) . '</td>';
                //pdf
                if ( !empty($value["Pdf"]) ) {
                    $showData .= '<td class="td_edit">
                                    <a href="../data_files/' . $this->_prefix . $value["Pdf"] . '" target="_blank">
                                        <img width="25px" src="assets/images/icon_pdf.gif"/>
                                    </a>
                                </td>';
                }
                else {
                    $showData .= '<td class="td_edit"></td>';
                }

                $showData .= '<td class="center areaBtn td_edit">';
                $showData .= '<button id="' . $value["ScheduleId"] . '" class="btnEdit fancy_h650" href="#inline0" value="todo1" onclick="openDialog(this.id,true);" name="btnEdit">修正</button>';
                $showData .= '</td></tr>';
            }
            $showData .= '</table>';
            $result['view'] = $showData;

            Result:
            return $result;
        }
    }
?>
