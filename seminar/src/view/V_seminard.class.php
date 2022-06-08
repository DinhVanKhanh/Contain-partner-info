<?php
class V_seminard {
    private $_prefix = '';

    public function loadList(array $data, array $sample): array {
        if ($data == false) {
            $result["view"] = '<table id="tblResult_u" class="tbl_searchmng"><tr><td style="text-align:center">空のデータ</td></tr></table>';
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
								<th class="w7">都道府県</th>
								<th style="width: 20%">セミナー主催／連絡先</th>
								<th>セミナー形式、開催会場／住所</th>
								<th style="width: 11%">開催日時</th>
								<th style="width: 4%">定員</th>
								<th style="width: 4%">PDF</th>
								<th style="width: 7%">備考</th>';
        if ($chkCurrentMonth) {
            $showdata .= '<th class="w7">操作</th>';
        }

        $showdata .= '    	</tr>
                        </thead>
                        <tbody>';

        $hiCha = ["日", "月", "火", "水", "木", "金", "土"];
        foreach ($data as $value) {
            $offline = '';
            $address = true;
            if ($value['SeminarType'] == '対面')
            $value['SeminarType'] = '会場開催';
            elseif ($value['SeminarType'] == 'オンライン') {
                $value['SeminarType'] = 'オンライン開催';
                $offline = 'offline';
                $address = false;
            }

            $hiNum = date('w', strtotime($value['Date']));
            $date  = date('n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime($value['Date']));

            $showdata .= '<tr>
								<td class="center">' . $value['SeminarName'] . '</td>
								<td class="center bold">' . $value['TodouhukenDisplay'] . '</td>
								<td><span class="bold">' . $value['CompanyName'] . '</span>';
            if (!empty($value['ContactTel']))
            $showdata .=            '<br/><span class="nowrap">[TEL] ' . $value['ContactTel'] . '</span>';
            if (!empty($value['ContactFax']))
            $showdata .=            '<br/><span class="nowrap">[FAX] ' .  $value['ContactFax'] . '</span>';
            $showdata .=       '</td>
								<td>'.
                                    '<span class="bold seminar-type ' . $offline . '">' . $value['SeminarType'] . '</span>';
            if ($address) {
                $showdata .=        '<br><span class="bold nowrap">' . $value['VenueName'] . '</span>';
                $showdata .=        '<br><span class="">' . $value['VenueAddress'] . '</span>';
            }
			$showdata .= '		</td>
								<td class="center nowrap">' .
									$date . '<br>' .
									$value['TimeStart'] . '<br>～<br>' . $value['TimeEnd'] .
								'</td>
								<td class="center">' . $value['CountPerson'] . '</td>';

            if (!empty($value['PDF'])) {
				if ( file_exists( __DIR__ . '/../../../data_files/' . $this->_prefix . $value['PDF'] ) ) {
					$class_exist = "exists";
				}
				else {
					$class_exist = "notExists";
				}

                $showdata .= '<td class="td_edit vmiddle ' . $class_exist . '">
								<a href="../data_files/' . $this->_prefix . $value['PDF'] . '" target="_blank" download >
								<img width="25px" src="assets/images/icon_pdf.gif"/></a></td>';
			}
			else {
                $showdata .= '<td class="td_edit"></td>';
			}

            $showdata .= '<td>' . nl2br( $value['Note'] ) . '</td>';

            if ($chkCurrentMonth) {
                $showdata .= '<td class="center padd_10">
                                    <button name="btnConfirmDel" onclick="$(\'#SeminarId\').val(this.id); afterDelete(0);" class="btnDel btnConfirmDel btnConfirm1" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#confirmBox">削除</button>
                                    <button name="btnEdit" onclick=openDialog(this.id,true) class="btnEdit fancy_h620" id="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '" href="#inline0">修正</button>
                                    <button name="btnFull" id="btnFull' . $value['SeminarId'] . '" onclick=FullSeminarDId($(this).attr("rel")) class="btnFull btnStyle1" rel="' . $value['SeminarId'] . '" value="' . $value['SeminarId'] . '">' .
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

        // $showsp ='<ul class="show_ban_sp hide-pc">';

        $showdata = '<table id="tblResult_u" class="table-d show_ban hide-sp" style="font-size:90%;">
                    <thead>
                        <tr>
                            <th class="w7">都道府県</th>
                            <th class="w20">セミナー主催／連絡先</th>
                            <th class="">セミナー形式、開催会場／住所</th>
                            <th class="w12">開催日時</th>
                            <th class="w14">参加費(1名あたり)</th>
                            <th class="w5">定員</th>
                            <th class="w15">申込(申込画面またはFAX)</th>
                        </tr>
                    </thead>
                    <tbody>';

        $hiCha = ["日", "月", "火", "水", "木", "金", "土"];
        foreach ($data as $value) {
            $offline = '';
            $address = true;
            if($value['SeminarType'] == '対面')
                $value['SeminarType'] = '会場開催';
            elseif($value['SeminarType'] == 'オンライン'){
                $value['SeminarType'] = 'オンライン開催';
                $offline = 'offline';
                $address = false;
            }

            $hiNum = date('w', strtotime($value['Date']));
            $date  = date('n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime($value['Date']));

            // $showsp.='<li>
            //             <dl>
            //                 <dt>給料王 年末調整セミナー</dt>
            //                 <dd class="bold">' . $value['TodouhukenDisplay'] . '</dd>
            //                 <dd>' . $value['CompanyName'] . '</dd>
            //                 <dd><span class="bold">' . $value['VenueName'] . '</span><br/>' . $value['VenueAddress'];

            $showdata .= '<tr>
								<td class="center bold"><span style="font-size:150%">' . $value['TodouhukenDisplay'] . '</span></td>
								<td class="center">
                                    <span class="bold">' . $value['CompanyName'] . '</span>';
            if(!empty($value['ContactTel']))
                $showdata .=        '<br/><span class="nowrap">[TEL] '. $value['ContactTel'] . '</span>';
            if (!empty($value['ContactFax']))
                $showdata .=        '<br/><span class="nowrap">[FAX] ' .  $value['ContactFax'] . '</span>';

            $showdata .=        '</td>';
            $showdata .=	    '<td>
									<span class="bold seminar-type '.$offline.'">' . $value['SeminarType'] . '</span>';			
            if($address){
                $showdata .=        '<br><span class="bold nowrap">' . $value['VenueName'] . '</span>';
                $showdata .=        '<br><span class="">' . $value['VenueAddress'] . '</span>';
            }
			$showdata .= '		</td>
								<td class="center nowrap">' .
									'<span class="bold">'. $date .'</span>' . '<br>' .
									$value['TimeStart'] . '～' . $value['TimeEnd'] .
								'</td>
                                <td>'.
                                   '<span class="bold">' . number_format($value["SeminarFees2Member"]) . '円(税込)' . '</span>';
            if(!empty($value['SeminarFees']))
                $showdata .=       '<p style="font-size:80%">※バリューサポート未加入の場合は'. '<span class="bold">' .number_format($value["SeminarFees"]).'円(税込)' . '</span></p>';
            $showdata .=        '</td>';
            $showdata .= 		'<td class="center">' . $value['CountPerson'] . '名'. '</td>';
                                
            // //↓↓　<2021/08/31> <VanKhanh> <show list area>
            // $showdata .= "<td class='center'>{$value['SeminarFees']}</td>";
            // //↑↑　<2021/08/31> <VanKhanh> <show list area>

            if ($chkCurrentMonth) {
                $today = date('Y-n-j');
                if (strtotime($today) > strtotime($value['AppDate'])) {
                    $showdata .= '<td class="center padd_10" colspan="1"><span class="greenDark" ><strong>※受付終了</strong></span></td>';
                    // $showsp   .= '<dd class="center"><span class="greenDark" ><strong>受付終了-' . date('Y-n-j', strtotime($value['AppDate'])) . '</strong></span></dd>';
                }
                elseif ($value["Person"] == $value["CountPerson"] || $value["CheckFull"] == 1) {
                    $showdata .= '<td class="center" colspan="1"><span class="greenDark" ><strong>※満席</strong></span></td>';
                    // $showsp   .= '<dd class="center"><span class="greenDark" ><strong>満席</strong></span></dd>';
                }
                else {
                    $showdata .= '<td class="center w7">';
                    if(!empty($value["OrganizerURL"]))
                        $showdata .='<div class="application url"><a class="apply" style="margin-top:3px" href="'. $value["OrganizerURL"] . '" download target="_blank" ><img class="btn-img" src="../assets/images/seminar_d_button_web.png" alt="ダウンロード"></a></div>';
                    if (!empty($value["PDF"]))
                        $showdata .= '<div class="application pdf"><a class="apply" style="margin-top:3px" href="../../data_files/' . $this->_prefix . $value["PDF"] . '" download type="application/octet-stream" target="_blank" ><img  class="btn-img" src="../assets/images/seminar_d_button_fax.png" alt="ダウンロード"></a></div>';
                    $showdata .= '</td>';
                }
                
            }
            else {
                $showdata .= '<td class="center" colspan="1">※終了</td>';
                // $showsp   .= '<dd class="center" colspan="2">終了</dd>';
            }
            $showdata .= '</tr>';
            // $showsp   .= '</dl></li>';
        }
        // $showsp .= '</ul>';
        $showdata .= '  </tbody>
					</table>';
        //$showdata: show css pc
        //$showsp: show css reponsive
        $result['view'] = $showdata;

        Result:
        return $result;
    }
//↓↓　<2021/08/31> <VanKhanh> <show list area>


    public function getAreaList($data = null)
    {
        $showdata = "<div id='area-list'>";
        $showdata .= "<ul>";
        $showdata .= "<li onclick='getArea(0,this)'><a class='active'>" . "全地域" . "</a></li>";
        foreach ($data as $key => $value) :
            if (!in_array($value["AreaCode"], ["2", "6"])) {
                $showdata .= "<li onclick='getArea({$value["AreaCode"]},this)'><a>";
                if ($value["AreaCode"] == "1")
                    $showdata .= $value["AreaName"] . "・東北";
                elseif ($value["AreaCode"] == "5")
                    $showdata .= $value["AreaName"] . "・中四国";
                else
                    $showdata .= $value["AreaName"];
                $showdata .= "</a></li>";
            }
        endforeach;
        $showdata .= "<li onclick='getArea(1000,this)'><a>" . "オンライン開催" . "</a></li>";
        
        $showdata .= "</ul>";
        $showdata .= "</div>";
        return $showdata;

    }
    
    // public function getArea($data = null, $sample = null)
    // {
    //     if ($data == false) {
    //         $result["view"] = '<table id="tblResult_u" class="show_ban"><tr><td style="text-align:center">空のデータ</td></tr></table>';
    //         goto Result;
    //     }

    //     /* Check the current month will be allowed to register or not */
    //     $now             = new \DateTime('now');
    //     $currentMonth    = $now->format('n');
    //     $months          = explode(',', $sample['SampleAppMonth']);
    //     $chkCurrentMonth = in_array($currentMonth, $months) ? true : false;

    //     $showsp ='<ul class="show_ban_sp hide-pc">';

    //     $showdata = '<table id="tblResult_u" class="show_ban hide-sp" style="font-size:90%;">
    //                 <thead>
    //                     <tr>
    //                         <th class="w7">都道府県</th>
    //                         <th class="w25">スクール</th>
    //                         <th>開催会場（住所及び連絡先）</th>
    //                         <th class="w14">開催日時</th>
    //                         <th class="w5">定員</th>
    //                         <th>WEB<br>申込</th>
    //                         <th class="w7">FAX申込<br>（PDF）</th>
    //                     </tr>
    //                 </thead>
    //                 <tbody>';

    //     $hiCha = ["日", "月", "火", "水", "木", "金", "土"];
    //     foreach ($data as $value) {
    //         $hiNum = date('w', strtotime($value['Date']));
    //         $date  = date('n \月 j \日\(' . $hiCha[$hiNum] . '\)', strtotime($value['Date']));

    //         $showsp.='<li>
    //                     <dl>
    //                         <dt>給料王 年末調整セミナー</dt>
    //                         <dd class="bold">' . $value['TodouhukenDisplay'] . '</dd>
    //                         <dd>' . $value['CompanyName'] . '</dd>
    //                         <dd><span class="bold">' . $value['VenueName'] . '</span><br/>' . $value['VenueAddress'];

    //         $showdata .= '<tr>
	// 							<td class="center bold">' . $value['TodouhukenDisplay'] . '</td>
	// 							<td>' . $value['CompanyName'] . '</td>
	// 							<td>
	// 								<span class="bold">' . $value['VenueName'] . '</span>
	// 								<br>' . $value['VenueAddress'];
    //         if (!empty($value['ContactTel'])) {
    //             $showdata .= '<br><span class="nowrap">[TEL] ' . $value['ContactTel'] . '</span>';
    //             $showsp .= '<br/>[TEL] ' . $value['ContactTel'];
	// 		}

    //         if (!empty($value['ContactFax'])) {
    //             $showdata .= '　<span class="nowrap">[FAX] ' . $value['ContactFax'] . '</span>';
    //             $showsp .= '<br/>[FAX] '.$value['ContactFax'];
    //         }

    //         $showsp.='  </dd>
    //                     <dd class="center">' . $date . '  ' . $value['TimeStart'] . '～' . $value['TimeEnd'] . '</dd>
	// 			        <dd class="center">席数 ' . $value['CountPerson'] . '</dd>';

	// 		$showdata .= '		</td>
	// 							<td class="center nowrap">' .
	// 								$date . '<br>' .
	// 								$value['TimeStart'] . '～' . $value['TimeEnd'] .
	// 							'</td>
	// 							<td class="center">' . $value['CountPerson'] . '</td>';

    //         if ($chkCurrentMonth) {
    //             $today = date('Y-n-j');
    //             if (strtotime($today) > strtotime($value['AppDate'])) {
    //                 $showdata .= '<td class="center padd_10" colspan="2"><span class="greenDark" ><strong>受付終了</strong></span></td>';
    //                 $showsp   .= '<dd class="center"><span class="greenDark" ><strong>受付終了-' . date('Y-n-j', strtotime($value['AppDate'])) . '</strong></span></dd>';
    //             }
    //             elseif ($value["Person"] == $value["CountPerson"] || $value["CheckFull"] == 1) {
    //                 $showdata .= '<td class="center" colspan="2"><span class="greenDark" ><strong>満席</strong></span></td>';
    //                 $showsp   .= '<dd class="center"><span class="greenDark" ><strong>満席</strong></span></dd>';
    //             }
    //             else {
    //                 $showdata .= '<td class="center w7"><button name="btnEdit" onclick=openpopup("inputform.php?id=' . $value["SeminarId"] . '",700,800); class="btnEditclient" id="' . $value["SeminarId"] . '" value="' . $value["SeminarId"] . '" href="#inline0" style="width:100%;">申込</button></td>';
    //                 $showsp   .= '<dd><button name="btnEdit" onclick=openpopup("inputform.php?id=' . $value["SeminarId"] . '",700,800); class="btnEditclient" id="' . $value["SeminarId"] . '" value="' . $value["SeminarId"] . '" href="#inline0" style="width:25%; margin-right:2%;">申込</button>';

    //                 if ( empty( $value["PDF"] )) {
    //                     $showdata .= '<td class="center">&nbsp;</td>';
    //                     $showsp   .= '';
    //                 }
    //                 else{
    //                     $showdata .= '<td class="center"><a href="../../data_files/' . $this->_prefix . $value["PDF"] . '" download target="_blank" ><img src="../assets/images/bt_01.jpg" alt="ダウンロード"></a></td>';
    //                     $showsp   .= '<a href="../../data_files/' . $this->_prefix . $value["PDF"] . '" download target="_blank" ><img src="../assets/images/bt_01.jpg" alt="ダウンロード"></a></dd>';
    //                 }
    //             }
    //         }
    //         else {
    //             $showdata .= '<td class="center" colspan="2">終了</td>';
    //             $showsp   .= '<dd class="center" colspan="2">終了</dd>';
    //         }
    //         $showdata .= '</tr>';
    //         $showsp   .= '</dl></li>';
    //     }
    //     $showsp .= '</ul>';
    //     $showdata .= '  </tbody>
	// 				</table>';
    //     $result['view'] = $showdata . $showsp;

    //     Result:
    //     return $result;
    // }
}

//↑↑　<2021/08/31> <VanKhanh> <show list area>