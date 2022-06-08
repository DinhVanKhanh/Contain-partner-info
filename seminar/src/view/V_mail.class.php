<?php
    class V_mail {
        public function loadList( Array $data, Array $sample ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
                goto Result;
			}
			
			$getSampleNameById = function ( $value ) {
				global $sample;
				foreach ( $sample as $val ) {
					if ( intval( $val['SampleId'] ) == intval( $value ) ) {
						return $val['SampleName'];
					}
				}
				return '';
			};

            $showdata = '<table id="tblResult_u" class="show_ban">
						<tr>
							<th class="w25">セミナー名</th>
							<th class="w25">SMTPサーバー</th>
							<th>暗号化方式</th>
							<th>SMTPユーザ名</th>
							<th>操作</th>
						</tr>';

			foreach ($data as $value) {
				$strEncriptionType = "";
				switch ($value['EncriptionType']) {
					case "0":
						$strEncriptionType = "なし";
						break;
					case "1":
						$strEncriptionType = "SSL（" . $value['Port'] . "）";
						break;
					case "2":
						$strEncriptionType = "TLS（" . $value['Port'] . "）";
						break;
				}

				$showdata .= '<tr>
								<td class="center">' . $getSampleNameById( $value['SampleId'] ) . '</td>
								<td class="center">' . $value['Host'] . '</td>
								<td class="">' . $strEncriptionType . '</td>
								<td class="">' . $value['Username'] . '</td>                
								<td class="center padd_10" style="width:7%!important;">
									<button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancyboxmail" id="' . $value['EmailId'] . '" value="' . $value['EmailId'] . '" href="#inline0">修正</button>
								</td>
							</tr>';
			}
			$showdata .= '</table>';
			$result['view'] = $showdata;

            Result:
            return $result;
        }
    }
?>