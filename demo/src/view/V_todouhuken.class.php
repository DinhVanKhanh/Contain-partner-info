<?php
    class V_todouhuken {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblResult_u" class="show_ban">
						<tr>
							<th class="w5">No.</th>
							<th class="w14">地区</th>
							<th>都市コード</th>
							<th>都市名</th>
							<th class="w_td_btn">操作</th>
						</tr>';

			$i = 1;
			foreach ( $data as $value ) {
				$showdata .= '<tr>
								<td class="center areaIndex">' . $i . '</td>
								<td class="center areaIndex">' . $value['AreaName'] . '</td>
								<td class="areaCode">' . $value['TodouhukenCode'] . '</td>
								<td class="areaName">' . $value['TodouhukenName'] . '</td>
								<td class="center areaBtn padd_10">
									<button name="btnConfirmDel" onclick="$(\'#todouId\').val(this.id)" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['TodouhukenId'] . '" value="' . $value['TodouhukenId'] . '" href="#confirmBox">削除</button>
									<button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox4" id="' . $value['TodouhukenId'] . '" value="' . $value['TodouhukenCode'] . '" href="#inline0">修正</button>
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