<?php
class V_seminarb {
    private $_prefix = 'SEMINAR_B_';

    public function loadList(array $data, array $sample): array {
        if ($data == false) {
            $result["view"] = '<table id="tblResult_u" class="show_ban hide-sp" style="font-size:94%;"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
            goto Result;
        }

        /* Check the current month will be allowed to register or not */
        $now             = new \DateTime('now');
        $currentMonth    = $now->format('n');
        $months          = explode(',', $sample['SampleAppMonth']);
        $chkCurrentMonth = in_array($currentMonth, $months) ? true : false;

        $showdata = '<table id="tblResult_u" class="show_ban hide-sp" style="font-size:94%;">
                        <thead>
                            <tr>
                                <th colspan="2" >開催会場</th>
                                <th>開催日時</th>
                                <th>時間帯</th>';

        if ($chkCurrentMonth) {
            $showdata .= '<th class="w7">操作</th>';
        }

        $showdata .= '    	</tr>
                        </thead>
                        <tbody>';

        $hiCha = ["日", "月", "火", "水", "木", "金", "土"];

        $i = 0;
        $size_a = count( $data );
        while ($i < $size_a) {
            $flag = 0;
            $key = (object)$data[$i];
            $j = $i + 1;
            $count = 1;

            $hiNum = date( 'w', strtotime( $key->Date ) );
            $date  = date( 'n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime( $key->Date ) );

            $map = !empty($key->VenueMap) ? $key->VenueMap : '';

            while ($j < $size_a) {
                $key_se = (object)$data[$j];
                if ($key_se->AreaId == $key->AreaId &&
                $key_se->VenueName == $key->VenueName &&
                $key_se->VenueAddress == $key->VenueAddress &&
                $key_se->Date == $key->Date) {
                    if ($map == '' && !empty($key_se->VenueMap)) {
                        $map = $key_se->VenueMap;
                    }
                    elseif (!empty($key_se->VenueMap)) {
                        $map = $key_se->VenueMap;
                    }
                    $count++;
                    $flag = 1;
                }
                $j++;
            }
            for ($is = 1; $is <= $count; $is++) {
                if ($is == 1) {
                    $showdata .= '<tr class="clear">
                                    <td class="center bold" style=" width:8%!important;" rowspan =' . $count . '>' . $key->AreaName . '</td>
                                    <td class="center" rowspan =' . $count . '>
                                        <strong>' . $key->VenueName . '</strong></br><span style=" font-size:90%;">' . $key->VenueAddress;

                    if ( !empty( $map ) ) {
                        $showdata .= '[ <a href="' . $map . '" target="_blank"><strong>地図</strong></a> ]</td>';
                    }
                    elseif ( $key->VenueMap == "" ) {
                        $showdata .= '</td>';
                    }
                    else {
                        $showdata .= '[ <a href="' . $key->VenueMap . '" target="_blank"><strong>地図</strong></a> ]</td>';
                    }

                    $showdata .= '<td class="center w15" rowspan =' . $count . '>' . $hiNum . '年 </br>' . $date . '</td>
                                    <td class="areaName center w15">' . $key->TimeStart . ' ～ ' . $key->TimeEnd . '</td>';

                    if ( $chkCurrentMonth ) {
                        $showdata .= '<td class="center padd_10 w15">
                                        <button name="btnConfirmDel" onclick="$(\'#seminarId\').val(this.id); afterDelete(0);" class="btnDel btnConfirmDel btnConfirm1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '" href="#confirmBox">削除</button>
                                        <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox7" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '" href="#inline0">修正</button>';
                        if ( $key->CheckFull == 0 && $key->Person == 0 ) {
                            $showdata .= '<button name="btnConfirmFull" onclick="FullSeminarBId(this.id)" style="width:80px;" class="btnFull btnStyle1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '">満席</button>';
                        }
                        else {
                            $showdata .= '<button name="btnConfirmFull" onclick="FullSeminarBId(this.id)" style="width:80px;" class="btnFull btnStyle1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '">満席取消</button>';
                        }
                        $showdata .= '</td>';
                    }
                    $showdata .= '</tr>';
                }
                else {
                    $key = (object)$data[$i + $is - 1];
                    $showdata .= '<tr class="clear " >
                        <td class="areaName center w15">' . $key->TimeStart . ' ～ ' . $key->TimeEnd . '</td>';
                    if ( $chkCurrentMonth ) {
                        $showdata .= '<td class="center padd_10 w15">
                                        <button name="btnConfirmDel" onclick="$(\'#seminarId\').val(this.id); afterDelete(0);" class="btnDel btnConfirmDel btnConfirm1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '" href="#confirmBox">削除</button>
                                        <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancybox7" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '" href="#inline0">修正</button>';
                        if ( $key->CheckFull == 0 && $key->Person == 0 ) {
                            $showdata .= '<button name="btnConfirmFull" onclick="FullSeminarBId(this.id)" style="width:80px;" class="btnFull btnStyle1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '">満席</button>';
                        }
                        else {
                            $showdata .= '<button name="btnConfirmFull" onclick="FullSeminarBId(this.id)" style="width:80px;" class="btnFull btnStyle1" id="' . $key->SeminarId . '" value="' . $key->SeminarId . '">満席取消</button>';
                        }
                        $showdata .= '</td>';
                    }
                    $showdata .= '</tr>';
                }
            }
            if ($flag == 1) {
                $i += $count;
            }
            else {
                $i++;
            }
        }

        $showdata .= '  </tbody>
					</table>';
        $result['view'] = $showdata;

        Result:
        return $result;
    }

    public function loadClientList(array $data, array $sample): array {
        if ($data == false) {
            $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
            goto Result;
        }

        /* Check the current month will be allowed to register or not */
        $now             = new \DateTime('now');
        $currentMonth    = $now->format('n');
        $months          = explode(',', $sample['SampleAppMonth']);
        $chkCurrentMonth = in_array($currentMonth, $months) ? true : false;

        $showsp ='<div class="box_smnmida1">
                    <div style="font-size:16px; line-height:130%; text-align:left;">2019年&#12288;青色申告 直前対策セミナー</div>
                    <div style="font-size:12px; font-weight:normal; text-align:left; line-height:1.5;">※各セミナーの空席状況はリアルタイムではないため、最新の情報ではない場合があります。くわしくは各スクールへ直接お問合せください。</div>
                </div>
                <ul class="show_ban_sp hide-pc">';

        $showdata = '<table id="tblResult_u" class="show_ban hide-sp" style="font-size:90%;">
                    <thead>
                        <tr>
                            <th class="smnmida1" colspan="7">
                                <div style="font-size:20px; line-height:150%;">2019年&#12288;青色申告 直前対策セミナー</div>
                                <!--<div style="font-size:14px; line-height:150%; font-weight:normal; color:#f30; font-weight:bold;">※【関西地区】の青色申告セミナーにつきましては現在掲載準備中です。お申込開始まで少々お待ちください。</div>-->
                                <div style="font-size:13px; line-height:150%; font-weight:normal;">※各セミナーの空席状況はリアルタイムではないため、最新の情報ではない場合があります。くわしくは各スクールへ直接お問合せください。</div>
                            </th>
                        </tr>
                        <tr>
                            <th class="w7">都道府県</th>
                            <th class="w25">スクール</th>
                            <th>開催会場（住所及び連絡先）</th>
                            <th class="w14">開催日時</th>
                            <th class="w5">定員</th>
                            <th>WEB<br>申込</th>
                            <th class="w7">FAX申込<br>（PDF）</th>
                        </tr>
                    </thead>
                    <tbody>';

        $hiCha = ["日", "月", "火", "水", "木", "金", "土"];
        foreach ($data as $value) {
            $hiNum = date('w', strtotime($value['Date']));
            $date  = date('n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime($value['Date']));

            $showsp.='<li>
                        <dl>
                            <dt>青色申告 直前対策セミナー</dt>
                            <dd class="bold">' . $value['TodouhukenDisplay'] . '</dd>
                            <dd>' . $value['CompanyName'] . '</dd>
                            <dd><span class="bold">' . $value['VenueName'] . '</span><br/>' . $value['VenueAddress'];

            $showdata .= '<tr>
								<td class="center bold">' . $value['TodouhukenDisplay'] . '</td>
								<td>' . $value['CompanyName'] . '</td>
								<td>
									<span class="bold">' . $value['VenueName'] . '</span>
									<br>' . $value['VenueAddress'];
            if (!empty($value['ContactTel'])) {
                $showdata .= '<br><span class="nowrap">[TEL] ' . $value['ContactTel'] . '</span>';
                $showsp .= '<br/>[TEL] ' . $value['ContactTel'];
			}

            if (!empty($value['ContactFax'])) {
                $showdata .= '　<span class="nowrap">[FAX] ' . $value['ContactFax'] . '</span>';
                $showsp .= '<br/>[FAX] '.$value['ContactFax'];
            }

            $showsp.='  </dd>
                        <dd class="center">' . $date . '  ' . $value['TimeStart'] . '～' . $value['TimeEnd'] . '</dd>
				        <dd class="center">席数 ' . $value['CountPerson'] . '</dd>';

			$showdata .= '		</td>
								<td class="center nowrap">' .
									$date . '<br>' .
									$value['TimeStart'] . '～' . $value['TimeEnd'] .
								'</td>
								<td class="center">' . $value['CountPerson'] . '</td>';

            if ($chkCurrentMonth) {
                $today = date('Y-n-j');
                if (strtotime($today) > strtotime($value['AppDate'])) {
                    $showdata .= '<td class="center padd_10" colspan="2"><span class="greenDark" ><strong>受付終了</strong></span></td>';
                    $showsp   .= '<dd class="center"><span class="greenDark" ><strong>受付終了-' . date('Y-n-j', strtotime($value['AppDate'])) . '</strong></span></dd>';
                }
                elseif ($value["Person"] == $value["CountPerson"] || $value["CheckFull"] == 1) {
                    $showdata .= '<td class="center" colspan="2"><span class="greenDark" ><strong>満席</strong></span></td>';
                    $showsp   .= '<dd class="center"><span class="greenDark" ><strong>満席</strong></span></dd>';
                }
                else {
                    $showdata .= '<td class="center w7"><button name="btnEdit" onclick=openpopup("inputform_wp.php?id=' . $value["SeminarId"] . '",700,800); class="btnEditclient" id="' . $value["SeminarId"] . '" value="' . $value["SeminarId"] . '" href="#inline0" style="width:100%;">申込</button></td>';
                    $showsp   .= '<dd><button name="btnEdit" onclick=openpopup("inputform_wp.php?id=' . $value["SeminarId"] . '",700,800); class="btnEditclient" id="' . $value["SeminarId"] . '" value="' . $value["SeminarId"] . '" href="#inline0" style="width:25%; margin-right:2%;">申込</button>';

                    if ( empty( $value["PDF"] )) {
                        $showdata .= '<td class="center">&nbsp;</td>';
                        $showsp   .= '';
                    }
                    else{
                        $showdata .= '<td class="center"><a href="../data_files/' . $this->_prefix . $value["PDF"] . '" download target="_blank" ><img src="../assets/images/bt_01.jpg" alt="ダウンロード"></a></td>';
                        $showsp   .= '<a href="../data_files/' . $this->_prefix . $value["PDF"] . '" download target="_blank" ><img src="../assets/images/bt_01.jpg" alt="ダウンロード"></a></dd>';
                    }
                }
            }
            else {
                $showdata .= '<td class="center" colspan="2">終了</td>';
                $showsp   .= '<dd class="center" colspan="2">終了</dd>';
            }
            $showdata .= '</tr>';
            $showsp   .= '</dl></li>';
        }
        $showsp .= '</ul>';
        $showdata .= '  </tbody>
					</table>';
        $result['view'] = $showdata . $showsp;

        Result:
        return $result;
    }
}
