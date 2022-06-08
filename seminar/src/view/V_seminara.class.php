<?php
class V_seminara {
    public function loadList(array $data, array $sample, array $item): array {
        if ($data == false) {
            $result["view"] = '<table id="tblResult_u" class="tbl_searchmng"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
            goto Result;
        }

        /* Check the current month will be allowed to register or not */
        $now             = new \DateTime('now');
        $currentMonth    = $now->format('n');
        $months          = explode(',', $sample['SampleAppMonth']);
        $chkCurrentMonth = in_array($currentMonth, $months) ? true : false;

        $showdata = '<table id="tblResult_u" class="tbl_searchmng">
                        <thead>
                            <tr>
                                <th style="width: 8%">セミナー名</th>
                                <th class="w5">地域</th>
                                <th>開催日時</th>
                                <th>コース</th>
                                <th style="width: 8%; padding-left: 0; padding-right:0">
                                    受講料<br>（' . ( ($sample['SampleTaxChk'] == 1) ? '税込' : '税抜き' ) . '）
                                </th>
                                <th class="w5">席数</th>
                                <th>開催教室名・住所・最寄駅</th>
                                <th style="width: 100px">
                                    会場連絡先<br>
                                    （TEL・FAX）
                                </th>
                                <th style="width: 7%">備考</th>
                                <th>ソリマチ／他社</th>
                                <th style="width: 7%">対象製品</th>';

        if ($chkCurrentMonth) {
            $showdata .= '<th class="w7">操作</th>';
        }

        $showdata .= '    	</tr>
                        </thead>
                        <tbody>';

        $getItem = function ( $value ) {
            global $item;
            foreach ( $item as $val ) {
                if ( intval( $val['ItemId'] ) == intval( $value ) ) {
                    return $val['ItemName'];
                }
            }
            return '';
        };

        $hiCha = ["日", "月", "火", "水", "木", "金", "土"];
        foreach ($data as $value) {
            $hiNum = date('w', strtotime($value['Date']));
            $date  = date('n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime($value['Date']));
            $showdata .= '<tr>
                            <td class="center">' . $value['SeminarName'] . '</td>
                            <td class="center bold">' . $value['AreaName'] . '</td>
                            <td class="center nowrap">' . $date . '<br>' . $value['TimeStart'] . '<br>～<br>' . $value['TimeEnd'] . '</td>
                            <td class="center">' . $getItem( $value['SeminarClass3'] ) . '</td>
                            <td class="center">' . $value['SeminarFees'] . '円</td>
                            <td class="center">' . number_format( $value['CountPerson'] ) . '</td>
                            <td><span class="bold">' . $value['VenueName'] . '</span>';

            if ( !empty($value['VenueMap'])
            && strpos($value['VenueMap'], "http://") === FALSE
            && strpos($value['VenueMap'], "https://") === FALSE ) {
                $showdata .= ' [<a href="http://' . $value['VenueMap'] . '" target="_blank">地図</a>]';
            }

            $showdata .= '<br>' . $value['VenueAddress'] . '<br><img src="assets/images/icon_eki.gif" alt="最寄駅" /> <br>' . $value['VenueStation'] . '</td>
                            <td class="center">
                                TEL: ' . $value['ContactTel'] . '<br>
                                FAX: ' . $value['ContactFax'] . '
                            </td>
                            <td>' . nl2br( $value['Note'] ) . '</td>
                            <td class="center">' . $getItem( $value['SeminarClass1'] ) . '</td>
                            <td class="center">' . $getItem( $value['SeminarClass2'] ) . '</td>';

            if ($chkCurrentMonth) {
                $showdata .= '<td class="center padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#SeminarId\').val(this.id); afterDelete(0);" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancy_h620" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#inline0">修正</button>
                                    <button name="btnFull" id="btnFull' . $value['SeminarId'] . '" onclick=FullSeminarAId($(this).attr("rel")) class="btnFull btnStyle1" rel="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '">' .
										($value['CheckFull'] == 0 ? '満席' : '満席取消') .
									'</button>
                                </td>';
            }
            $showdata .= '</tr>';
        }

        $showdata .= '  </tbody>
					</table>';
        $result['view'] = $showdata;

        Result:
        return $result;
    }

    public function loadClientList(array $data, array $sample, array $area, array $item): array {
        if ($data == false) {
            $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">セミナー開催のデータがありません。</td></tr></table>';
            goto Result;
        }

        $getAreaNameById = function ( $value ) {
            global $area;
            foreach ( $area as $val ) {
                if ( intval( $val['AreaId'] ) == intval( $value ) ) {
                    return $val['AreaName'];
                }
            }
            return '';
        };

        $getProductNameById = function ( $value ) {
            global $item;
            foreach ( $item as $val ) {
                if ( intval( $val['ItemId'] ) == intval( $value ) ) {
                    return $val['ItemName'];
                }
            }
            return '';
        };

        $showdata = '<table id="tblResult_u" class="show_ban hide-sp" >';
        $showsp = '<ul class="show_ban_sp hide-pc">';

        $tax = ($sample['SampleTaxChk'] == 0) ? '税抜き' : '税込';

        $showdata .= '<thead class="hide-sp"><tr>
                        <th style="width: 3%">地域</th>
                        <th style="width: 11%">開催日時</th>
                        <th style="width: 10%">コース</th>
                        <th style="width: 7%" class="nowrap">受講料<br>（'.$tax.'）</th>
                        <th style="width: 4%" class="nowrap">席数</th>
                        <th style="width: 32%">開催教室名・住所・最寄駅</th>
                        <th style="width: 15%">会場連絡先<br>（TEL・FAX）</th>
                        <th>備考</th>
                        <th style="width: 7%" >申込</th>
                    </tr></thead><tbody>';

        $today = date('Y-m-d');

        foreach ( $data as $value ) {
            $ex_date = $value['Date'];
            $ex_day_w = date('Y', strtotime($value['Date']));
            $ex_day_m = date('j', strtotime($value['Date']));
            $ex_month = date('n', strtotime($value['Date']));

            $dy  = date("w", strtotime($value['Date']));
            $dys = array("日","月","火","水","木","金","土");
            $dyj = $dys[$dy];
            $date = $ex_month . '月' . $ex_day_m . '日' . '(' . $dyj . ')';

            $seminarName = $value['SeminarName'];
            $areaId = $value['AreaId'];
            $areaName = $getAreaNameById( $value['AreaId'] );

            $timeStart = $value['TimeStart'];
            $timeEnd = $value['TimeEnd'];

            $seminarClass3 = $value['SeminarClass3'];
            $couse = $getProductNameById( $value['SeminarClass3'] );

            $seminarFees = $value['SeminarFees'];
            $countPerson = $value['CountPerson'];
            $venueName = $value['VenueName'];

            $map = $value['VenueMap'];
            if (!empty($map) && strpos($map,"http://") === FALSE && strpos($map,"https://") === FALSE) {
                $map ="http://".$map;
            }

            $address = $value['VenueAddress'];
            $station = $value['VenueStation'];
            $tel = $value['ContactTel'];
            $fax = $value['ContactFax'];
            $note = nl2br($value['Note']);

            $seminarClass1 = $value['SeminarClass1'];
            $company = $getProductNameById( $value['SeminarClass1'] );

            $seminarClass2 = $value['SeminarClass2'];
            $product = $getProductNameById( $value['SeminarClass2'] );

            $Person = $value['Person'];
            $CheckFull = $value['CheckFull'];

            // begin if...........
            $dateSub = intval( ( strtotime($ex_date) - time()  ) / (60 * 60 * 24) );
            $flagDate = 0;

            // 申込期限の延長条件を計算。土曜・日曜日はカウントしない。(2016.12.22 mod Kentaro.Watanabe)
            // SampleDeadlineFD の値を追加。（2016.12.22 mod Kentaro.Watanabe）
            if (in_array($dyj, array('月', '火', '水'))) {
                $flagDate = 2;
            }
            elseif ($dyj == '日') {
                $flagDate = 1;
            }

            $SampleDeadlineFD = $sample['SampleDeadline'] ?? 0;
            if ($flagDate > 0) {
                $SampleDeadlineFD += $flagDate;
            }

            // 条件を SampleDeadlineFD に変更。（2016.12.22 mod Kentaro.Watanabe）
            if ($SampleDeadlineFD <= $dateSub) {
                $showsp .= '<li>
                                <dl>
                                    <dt>ソリマチ製品 使い方セミナー</dt>
                                    <dd class="bold">' . $areaName . '</dd>
                                    <dd>' . $date . '<br class="hide-sp"><span class="hide-pc"> </span>' . $timeStart . '<br class="hide-sp">～<br class="hide-sp">' . $timeEnd . '</dd>
                                    <dd><span class="span-th">会計王・みんなの青色申告セミナー</span><br>' . $couse . '</dd>
                                    <dd>' . number_format($seminarFees) . '円<span class="hide-pc">（' . $tax . '）</span></dd>
                                    <dd>席数 ' . $countPerson . '</dd>
                                    <dd><span class="bold">' . $venueName . '</span>';

                if (!empty($map)) {
                    $showsp .= ' [<a href="' . $map . '" target="_blank">地図</a>]';
                }

                $showsp .= '<br>' . $address . '<br><img src="../assets/images/icon_eki.gif'.'" alt="最寄駅" /> ' . $station . '</dd>';
                if (!empty($tel) || !empty($fax)) {
                    $showsp .= '<dd>';
                    if (!empty($tel)) {
                        $showsp .= 'TEL: '. $tel;
                    }

                    if (!empty($fax)) {
                        $showsp .= '<br> FAX: '. $fax;
                    }
                    $showsp .= '</dd>';
                }
                $showsp .= '<dd';

                if ($note == '') {
                    $showsp .= ' class="hide-sp"';
                }
                $showsp .= '>' . $note . '</dd>
                            <dd class="areaBtn padd_10">';

                // check seminar master for seminar A: Deadline = 開催日(Date:seminars) - 標準申込期限 (SampleDeadline:seminar_sample)<=0
                // if today < Deadline : 受付終了
                $SeDate = date('Y-m-d', strtotime($ex_date));
                if (strtotime($today) > strtotime($ex_date)) {
                    $showsp .= '<span class="greenDark" ><strong>受付終了</strong></span>';
                }
                elseif ($Person == $countPerson || $CheckFull == 1) {
                    $showsp .= '<span class="greenDark" ><strong>満席</strong></span>';
                }
                else {
                    $showsp .= '<button name="btnEdit" onclick=openpopup("inputform.php?id=' . $value['SeminarId'] . '",800,600); class="btnEditclient" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#inline0" style="width:100%;">申込</button>';
                }
                $showsp .= '</dd></dl></li>';

                $showdata .= '<tr class="hide-sp">
                            <td class="center bold nowrap">' . $areaName . '</td>
                            <td class="nowrap">' . $date . '<br class="hide-sp"><span class="hide-pc"> </span>' . $timeStart . '～' . $timeEnd . '</td>
                            <td class="center">' . $couse . '</td>
                            <td class="center nowrap">' . number_format($seminarFees) . '円<span class="hide-pc">（' . $tax . '）</span></td>
                            <td class="center">' . $countPerson . '</td>
                            <td><span class="bold">' . $venueName . '</span>';
                if (!empty($map)) {
                    $showdata .= ' [<a href="' . $map . '" target="_blank">地図</a>]';
                }

                $showdata .= '<br>' . $address . '<br><img src="../assets/images/icon_eki.gif'.'" alt="最寄駅" /> ' . $station . '</td>
                                <td class="center nowrap">';
                if (!empty($tel)) {
                    $showdata .= 'TEL:' . $tel;
                }
                if (!empty($fax)) {
                    $showdata .= '<br> FAX:' . $fax;
                }
                $showdata .= '</td><td';
                if ($note == '') {
                    $showdata .= ' class="hide-sp"';
                }
                $showdata .= '>' . $note . '</td>
                    <td class="center areaBtn">';

                $SeDate = date('Y-m-d', strtotime($ex_date));
                if (time() > strtotime($ex_date)) {
                    $showdata .= '<span class="greenDark" ><strong>受付終了</strong></span>';
                }
                elseif ($Person == $countPerson || $CheckFull == 1) {
                    $showdata .= '<span class="greenDark" ><strong>満席</strong></span>';
                }
                else {
                    $showdata .= '<button name="btnEdit" onclick=openpopup("inputform.php?id=' . $value['SeminarId'] . '",800,600); class="btnEditclient" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#inline0" style="width:100%;">申込</button>';
                }
                $showdata .= '</td></tr>';
            }
        }
        $showsp .='</ul>';
        $showdata .= '</tbody></table>';
        $result['view'] = $showdata . $showsp;

        Result:
        return $result;
    }
}
