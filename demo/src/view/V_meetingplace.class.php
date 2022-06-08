<?php
    class V_meetingplace {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showData = '<table id="tblResult_a" class="show_ban">
						<tr>
							<th class="w5">No.</th>
							<th class="w14">会場コード</th>
							<th>会場名称</th>
							<th class="w_td_btn">Tel</th>
							<th class="w_td_btn">Fax</th>
							<th class="w_td_btn">地図</th>
							<th class="w_td_btn">操作</th>
						</tr>';

			$i = 1;
			foreach ($data as $value) {
				$showData .= '<tr>
								<td class="center areaIndex">' . $i . '</td>
								<td class="center areaCode">' . $value["Code"] . '</td>
								<td class="areaName">' . $value["storeName1"] . ' ' . $value["storeName2"] . '</td>
								<td class="areaName">' . $value["Tel"] . '</td>
								<td class="areaName">' . $value["Fax"] . '</td>
								<td class="center">';

				if ( !empty($value["Map"]) ) {
					$showData .= '<a href="' . $value["Map"] . '" target="_blank">販売店マップ</a>';
				}

				if ( !empty($value["Address_1"]) || !empty($value["Address_2"]) ) {
					$showData .= '<a href="http://maps.google.com/?q=' . $value["Address_1"] . ' ' . $value["Address_2"] . '" target="_blank">
									<img src="assets/images/icon_03.jpg" />
								</a>';
				}
				$showData .= '</td>';

				$showData .= '	<td class="center areaBtn padd_10 ">
								<button name="btnConfirmDel" class="btnDel btnConfirmDel btnConfirm1" onclick="$(\'#MtId\').val(this.id)" id="' . $value["MeetingPlaceId"] . '" value="' . $value["MeetingPlaceId"] . '"  href="#confirmBox">削除</button>
								<button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancy_h520" id="' . $value["MeetingPlaceId"] . '" value="' . $value["Code"] . '" href="#inline0">修正</button>
								</td>
							</tr>';
				$i++;
			}
			$showData .= '</table>';
			$result['view'] = $showData;

            Result:
            return $result;
        }
    }
?>