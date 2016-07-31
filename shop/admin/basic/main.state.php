<?
	include "../lib.php";
	include "../../lib/json.class.php"; //json을 include

	//header('Content-Type: text/html; charset=euc-kr');  //charset 설정

	ob_start();

	// 관리자 그룹 권한에 따라 현황 호출 추가
	@include "../../conf/groupAuth.php";
	if($rAuthStatistics[$sess['level']] == 'y' || $sess['level'] == 100) {

		### 메인 현황 호출
		include "./main.state.array.php";
		@include "../../conf/admin_main_state.php";

		# 날짜 설정
		$m7Date	= date("Y-m-d", strtotime("-7 day"));
		$m2Date	= date("Y-m-d", strtotime("-2 day"));
		$m1Date	= date("Y-m-d", strtotime("-1 day"));
		$sDate	= date("Y-m-d", strtotime("-30 day"));
		$eDate	= date("Y-m-d");
		$mDate	= date("Y-m-01");

		# Where 문
		$whereStr1	= " and date_format(orddt,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by orddate ";
		$whereStr2	= " and date_format(cdt,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by orddate ";
		$whereStr3	= " and date_format(confirmdt,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by orddate ";
		$whereStr4	= " and date_format(regdt,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by orddate ";
		$whereStr5	= " and date_format(day,'%Y-%m-%d') between '".$m7Date."' and '".$eDate."' group by orddate order by orddate ";

		$whereStrM1	= " and date_format(orddt,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";
		$whereStrM2	= " and date_format(cdt,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";
		$whereStrM3	= " and date_format(confirmdt,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";
		$whereStrM4	= " and date_format(regdt,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";
		$whereStrM5	= " and date_format(day,'%Y-%m-%d') between '".$mDate."' and '".$eDate."' ";

		foreach($adminMainState AS $mKey => $mVal){

			# 매출액 (원)
			if($mVal['code'] == "main01" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(cdt,'%Y-%m-%d') as orddate, sum(prn_settleprice) as value from ".GD_ORDER."
					where step > 0 and step2 = 0 ".$whereStr2;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select sum(prn_settleprice) from ".GD_ORDER." where step > 0 and step2 = 0".$whereStrM2);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select sum(prn_settleprice) from ".GD_ORDER." where step > 0 and step2 = 0");
			}

			# 주문건수 (건)
			if($mVal['code'] == "main02" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(orddt,'%Y-%m-%d') as orddate, count(ordno) as value from ".GD_ORDER."
					where 1 ".$whereStr1;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where 1 ".$whereStrM1);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER);
			}

			# 입금확인 (건)
			if($mVal['code'] == "main03" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(cdt,'%Y-%m-%d') as orddate, count(ordno) as value from ".GD_ORDER."
					where step = 1 and step2 = 0 ".$whereStr2;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step = 1 and step2 = 0".$whereStrM2);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step = 1 and step2 = 0");
			}

			# 배송완료 (건)
			if($mVal['code'] == "main04" && $mVal['chk'] == "on"){
				$strSQL = "
select date_format(confirmdt,'%Y-%m-%d') as orddate, count(ordno) as value from ".GD_ORDER."
					where step = 4 and step2 = 0 ".$whereStr3;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step = 4 and step2 = 0".$whereStrM3);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step = 4 and step2 = 0");
			}

			# 취소 / 환불 / 반품 (건)
			if($mVal['code'] == "main05" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(orddt,'%Y-%m-%d') as orddate, count(ordno) as value from ".GD_ORDER."
					where step2 >= 40 and step2 < 50 ".$whereStr1;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step2 >= 40 and step2 < 50".$whereStrM1);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(ordno) from ".GD_ORDER." where step2 >= 40 and step2 < 50");
			}

			# 상품후기 (건)
			if($mVal['code'] == "main06" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_GOODS_REVIEW."
					where sno=parent ".$whereStr4;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(sno) from ".GD_GOODS_REVIEW." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(sno) from ".GD_GOODS_REVIEW." where sno=parent");
			}

			# 상품문의 (건)
			if($mVal['code'] == "main07" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_GOODS_QNA."
					where sno=parent ".$whereStr4;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(sno) from ".GD_GOODS_QNA." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(sno) from ".GD_GOODS_QNA." where sno=parent");
			}

			# 1:1문의 (건)
			if($mVal['code'] == "main08" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(sno) as value from ".GD_MEMBER_QNA."
					where sno=parent ".$whereStr4;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(sno) from ".GD_MEMBER_QNA." where sno=parent".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(sno) from ".GD_MEMBER_QNA." where sno=parent");
			}

			# 회원가입 (명)
			if($mVal['code'] == "main09" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(regdt,'%Y-%m-%d') as orddate, count(m_no) as value from ".GD_MEMBER."
					where 1 ".$whereStr4;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select count(m_no) from ".GD_MEMBER." where 1 ".$whereStrM4);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select count(m_no) from ".GD_MEMBER);
			}

			# 방문자수 (명)
			if($mVal['code'] == "main10" && $mVal['chk'] == "on"){
				$strSQL = "
					select date_format(day,'%Y-%m-%d') as orddate, uniques as value from ".MINI_COUNTER."
					where 1 ".$whereStr5;
				$res = $db->query($strSQL);
				# 한달
				list( $allData['monthcnt'] ) = $db->fetch("select sum(uniques) from ".MINI_COUNTER." where 1 ".$whereStrM5);
				# 전체
				list( $allData['allcnt'] ) = $db->fetch("select uniques from ".MINI_COUNTER." where day = '0'");
			}

			if($mVal['chk'] == "on"){
				while ($data=$db->fetch($res)){
					# 2일전
					if($data['orddate'] == $m2Date)	$mainState[$mVal['code']][0] = $data['value'];
					# 1일전
					if($data['orddate'] == $m1Date)	$mainState[$mVal['code']][1] = $data['value'];
					# 오늘
					if($data['orddate'] == $eDate)	$mainState[$mVal['code']][2] = $data['value'];
					# 일주일
					if($data['orddate'] >= $m7Date && $data['orddate'] <= $eDate)	$mainState[$mVal['code']][3] = $mainState[$mVal['code']][3] + $data['value'];
					# 30일
					# $mainState[$mVal['code']][4] = $mainState[$mVal['code']][4] + $data['value'];
				}

				# 한달
				$mainState[$mVal['code']][4] = $allData['monthcnt'];
				# 전체
				$mainState[$mVal['code']][5] = $allData['allcnt'];
			}

			unset($allData['allcnt']);
		}
	}
?>
	<table cellpadding="0" cellspacing="0" border="0" width="600">
	<tr>
		<td width="100%" height="25" background="../img/main_view_order.gif" colspan="10">

		<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr align="center">
			<td width="20%" class="left" style="padding-left:5px"><span onclick="popupLayer('./main.state.conf.php',500,360);" style="cursor:pointer;"><img src="../img/btn_mainstate.gif" align="absmiddle"></span></td>
			<td width="13%" class="ta8" style="color:#333333"><?=date("m/d", strtotime("-2 day"));?></td>
			<td width="13%" class="ta8" style="color:#333333"><?=date("m/d", strtotime("-1 day"));?></td>
			<td width="13%" class="ta8" style="color:#333333"><?=date("m/d");?> <font class="greenp ta7"><b>today</b></font></td>
			<td width="13%" class="end1" style="color:#333333">최근<font class="ta8">1</font>주</td>
			<td width="13%" class="end1" style="color:#333333">이번달</td>
			<td width="15%" class="end1" style="color:#333333">전체</td>
		</tr>
		</table>
		</td>
	</tr>

    <tr>
    	<td>
<? if($rAuthStatistics[$sess['level']] == 'y' || $sess['level'] == 100) { ?>
		<table cellpadding="4" cellspacing="0" border="1" bordercolor="#cccccc" style="border-collapse:collapse" width="100%">
<?
	foreach($adminMainState AS $mKey => $mVal){
		if($mVal['chk'] == "on"){
?>
		<tr align="center">
			<td width="20%" class="end1 left" style="padding-top:6px; padding-left:8px; color:#333333"><?=$mVal['title']?></td>
			<td width="13%" class="ta8" style="color:#666666"><?=number_format($mainState[$mVal['code']][0])?></td>
			<td width="13%" class="ta8" style="color:#666666"><?=number_format($mainState[$mVal['code']][1])?></td>
			<td width="13%" class="ta8" style="color:#1d9bf4"><b><?=number_format($mainState[$mVal['code']][2])?></b></td>
			<td width="13%" class="ta8" style="color:#666666"><?=number_format($mainState[$mVal['code']][3])?></td>
			<td width="13%" class="ta8" style="color:#666666"><?=number_format($mainState[$mVal['code']][4])?></td>
			<td width="15%" class="ta8" style="color:#666666"><?=number_format($mainState[$mVal['code']][5])?></td>
		</tr>
<?
		}
	}
?>
		</table>
<? } else { ?>
	<img src="../img/table_blind.gif">
<? } ?>
		</td>
	</tr>
	</table>

<?
		$returnForm = ob_get_contents();
	ob_end_clean();

	if ($_CFG['global']['charset'] != 'utf-8') {
		$returnForm = iconv($_CFG['global']['charset'], 'utf-8', $returnForm);
	}

	//--- ajax 리턴
	$data = array('returnForm'=>$returnForm);

	echo json_encode($data);
	exit();
?>
