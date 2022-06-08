<?php
    class V_master {
        public function loadList( Array $data ) : array {
            if ( $data == false ) {
                $result["view"] = '<table id="tblResult_u" class="show_ban table-master"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
                goto Result;
			}

            $showdata = '<table id="tblResult_u" class="show_ban table-master">
						<tr>
							<th style="width: 18%">セミナー名</th>
							<th style="width: 8%">標準申込期限</th>
							<th style="width: 12%">受講料</th>
							<th style="width: 10%">受講料消費税区分</th>
							<th>担当者のメールアドレス</th>
							<!-- <th>開催時期</th> -->
							<th style="width: 6%">操作</th>
						</tr>';

			foreach ($data as $value) {
				$flag = strpos($value['SampleEmail'], ',');
				$SampleEmail = $value['SampleEmail'];
				if ($flag == true) { 
					$SampleEmail = str_replace( ',', '<br>', $SampleEmail);
				}

				$showdata .= '<tr>
								<td class="center">' . $value['SampleName'] . '</td>
								<td class="center">' . $value['SampleDeadline'] . '</td>
								<td class="center">' . ( $value['SampleFeesChk'] == 0 ? '無料' : $value['SampleFees'] ) . '</td>
								<td class="center">' . ( $value['SampleTaxChk'] == 0 ? '税抜き' : '税込' ) . '</td>
								<td>' . $SampleEmail . '</td>
								<td class="center padd_10">
									<button name="btnEdit" onclick=openDialog(this.id) class="btnEdit fancymaster" id="' . $value['SampleId'] . '" value="' . $value['SampleId'] . '" href="#inline0">修正</button>
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