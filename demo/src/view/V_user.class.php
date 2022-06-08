<?php
    class V_user {
		public $role = ['admin', 'user'];
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
                goto Result;
            }

            $showdata = '<table id="tblResult_u" class="show_ban">
						<tr>
							<th class="w5">No.</th>
							<th class="w14">ユーザーID</th>
							<th>ユーザー名</th>
							<th>権限</th>
							<th class="w_td_btn">操作</th>
						</tr>';

			$count = count($data);
			$i     = 1;
			foreach ($data as $value) {
				$showdata .= '<tr>
								<td class="center areaIndex">' . $i . '</td>
								<td class="center areaCode">' . $value['UserCd'] . '</td>
								<td class="areaName">' . $value['UserName'] . '</td>
								<td class="center">' . $this->role[(int) $value['KengenKbn']] . '</td>
								<td class="center areaBtn padd_10 ">';

				if ($count > 1) {
					$showdata .= '<button name="btnConfirmDel" onclick=$("#userId").val(this.id) class="btnDel btnConfirmDel btnConfirm" id="' . $value['UserId'] . '" value="' . $value['UserId'] . '" href="#confirmBox">削除</button>';
				}

				if ($count == 1) {
					$showdata .= '<button name="btnConfirmDel" style="visibility:hidden" >削除</button>';
				}

				$showdata .= '		<button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox4" id="' . $value['UserId'] . '" value="' . $value['UserId'] . '" href="#inline0">修正</button>
								</td>
							</tr>';
				$i++;
			}
			$showdata .= '</table>';
			$result['view'] = $showdata;

            Result:
            return $result;
		}
    }
?>