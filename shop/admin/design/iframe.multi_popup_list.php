<?php
/**
 * 멀티 팝업 리스트 페이지
 * @author cjb3333 , artherot @ godosoft development team.
 */

$scriptLoad	= '<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

// 멀티 팝업 Class
$multipopup	= Core::loader('MultiPopup');
$popupList	= $multipopup->getPopupList();

// 검색 설정
$selected['skey'][$_GET['skey']]	= 'selected';
$checked['use'][$_GET['use']]		= 'checked';
$checked['type'][$_GET['type']]		= 'checked';

// 리스트 내용 배열처리
foreach ( $popupList as $popupData )
{
	$popupSearch	= $popupData;

	// 키워드 검색
	if ($_GET['skey'] && $_GET['sword']) {
		if (preg_match('/'.$_GET['sword'].'/i',$popupData[$_GET['skey']])) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// 출력일자
	if ($_GET['sregdt']) {
		if ($popupSearch['popup_sdt'] <= $_GET['sregdt'] && $popupSearch['popup_edt'] >= $_GET['sregdt']) {
			$popupSearch	= $popupSearch;
		} else {
			if (empty($popupSearch['popup_sdt']) || empty($popupSearch['popup_edt'])) {
				$popupSearch	= $popupSearch;
			} else {
				unset($popupSearch);
			}
		}
		$searchYN			= 'Y';
	}

	// 출력여부 검색
	if ($_GET['use']) {
		if ($popupSearch['popup_use'] == $_GET['use']) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// 창타입 검색
	if ($_GET['type']) {
		if ($popupSearch['popup_type'] == $_GET['type']) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// 검색여부에 따른 데이타 처리
	if ($searchYN == 'Y') {
		if (is_array($popupSearch)) {
			$popupConf[]	= $popupSearch;
		}
	} else {
		$popupConf[]		= $popupData;
	}
}
?>
<form method="get" name="frmSearch">

<div class="title title_top">멀티 팝업 관리<span>멀티 팝업에 대한 설정을 추가 / 변경하실 수 있습니다.</span> <a href="javascript:manual('<?php echo $guideUrl;?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?php echo $workSkinStr;?>
<table class="tb">
<col class="cellC"><col class="cellL" style="width:170px"><col class="cellC"><col class="cellL">
<tr>
	<td>키워드검색</td>
	<td colspan="3">
		<select name="skey">
			<option value="text" <?php echo $selected['skey']['text'];?>> 팝업제목 </option>
			<option value="code" <?php echo $selected['skey']['code'];?>> 팝업코드 </option>
		</select>
		<input type="text" NAME="sword" value="<?php echo $_GET['sword'];?>" class="line" />
	</td>
</tr>
<tr>
	<td>출력일자</td>
	<td colspan="3">
		<input type="text" name="sregdt" value="<?php echo $_GET['sregdt'];?>" onclick="calendar(event);" size="10" maxlength="8" class="tline center" readonly="readonly" />
		<img src="../img/sicon_today.gif" align="absmiddle" alert="오늘" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd");?>';" />
		<img src="../img/sicon_week.gif" align="absmiddle" alert="일주일" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-7 day"));?>';" />
		<img src="../img/sicon_twoweek.gif" align="absmiddle" alert="15일" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-15 day"));?>';" />
		<img src="../img/sicon_month.gif" align="absmiddle" alert="한달" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-1 month"));?>';" />
		<img src="../img/sicon_twomonth.gif" align="absmiddle" alert="두달" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-2 month"));?>';" />
		<img src="../img/sicon_all.gif" align="absmiddle" alert="전체" class="hand" onclick="javascript:document.frmSearch.sregdt.value='';" />
	</td>
</tr>
<tr>
	<td>출력여부</td>
	<td class="noline">
		<input type="radio" name="use" value="" <?php echo $checked['use'][''];?> />전체
		<input type="radio" name="use" value="Y" <?php echo $checked['use']['Y'];?> />출력
		<input type="radio" name="use" value="N" <?php echo $checked['use']['N'];?> />미출력
	</td>
	<td>창타입</td>
	<td class="noline" nowrap>
		<input type="radio" name="type" value="" <?php echo $checked['type'][''];?> />전체
		<input type="radio" name="type" value="layerMove" <?php echo $checked['type']['layerMove'];?> />이동레이어
		<input type="radio" name="type" value="layer" <?php echo $checked['type']['layer'];?> />고정레이어
		<input type="radio" name="type" value="window" <?php echo $checked['type']['window'];?> />일반팝업
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" alert="검색" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">총(검색) <font class="ver8"><b><?php echo count($popupConf);?></b>개</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>팝업제목</th>
	<th>팝업코드</th>
	<th>출력기간/시간</th>
	<th>창위치</th>
	<th>창크기</th>
	<th>출력여부</th>
	<th>창타입</th>
	<th>보기</th>
	<th>수정</th>
	<th>복사</th>
	<th>삭제</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="30">
<col style="padding-left:20px;">
<col width="80">
<col width="115">
<col width="65">
<col width="65">
<col width="55">
<col width="65">
<col width="55">
<col width="40">
<col width="40">
<col width="40">
<?
	// 데이타가 있는 경우
	if (is_array($popupConf)) {
		krsort($popupConf);
		foreach ( $popupConf as $pKey => $pVal ){
			// 출력여부
			if ($pVal['popup_use'] == "Y") {
				$popup_use	= "<font color=\"0074ba\">출력</font>";
			} else {
				$popup_use	= "<font color=\"ff0000\">미출력</font>";
			}

			// 팝업 타입
			if ($pVal['popup_type'] == "layerMove") {
				$popup_type	= "<font color=\"ff8000\">이동레이어</font>";
			} else if ($pVal['popup_type'] == "layer") {
				$popup_type	= "<font color=\"ff8000\">고정레이어</font>";
			} else {
				$popup_type	= "<font color=\"0074ba\">일반팝업</font>";
			}

			// 특정기간동안 팝업창 열림
			if ($pVal['popup_dt2tm'] == 'Y') {
				$tmp1	= substr($pVal['popup_sdt'],0,4).'-'.substr($pVal['popup_sdt'],4,2).'-'.substr($pVal['popup_sdt'],6,2).' '.substr($pVal['popup_stime'],0,2).':'.substr($pVal['popup_stime'],2,2).' ~';
				$tmp2	= substr($pVal['popup_edt'],0,4).'-'.substr($pVal['popup_edt'],4,2).'-'.substr($pVal['popup_edt'],6,2).' '.substr($pVal['popup_etime'],0,2).':'.substr($pVal['popup_etime'],2,2);

				$popupDateStr	= '<font color="0074ba">'.$tmp1.'<br />'.$tmp2.'</font>';
				unset($tmp1,$tmp2);
			}
			// 특정기간동안 특정시간에만 팝업창 열림
			else if ($pVal['popup_dt2tm'] == 'T') {
				$tmp1	= substr($pVal['popup_sdt'],0,4).'-'.substr($pVal['popup_sdt'],4,2).'-'.substr($pVal['popup_sdt'],6,2).' ~ '.substr($pVal['popup_edt'],0,4).'-'.substr($pVal['popup_edt'],4,2).'-'.substr($pVal['popup_edt'],6,2);
				$tmp2	= substr($pVal['popup_stime'],0,2).':'.substr($pVal['popup_stime'],2,2).' ~ '.substr($pVal['popup_etime'],0,2).':'.substr($pVal['popup_etime'],2,2);

				$popupDateStr	= '<font color="ff8000">'.$tmp1.'<br />'.$tmp2.'</font>';
				unset($tmp1,$tmp2);
			}
			// 항상 팝업창 열림
			else {
				$popupDateStr	= "<font color=\"0074ba\">항상 팝업창이 열림</font>";
			}
?>
<tr height="30">
	<td align="center"><font class="ver81" color=616161><?=($pKey + 1)?></font></td>
	<td><font color="0074ba"><b><?=$pVal['text']?></b></font></td>
	<td align="center"><font class="ver81"><?=$pVal['code']?></font></td>
	<td align="center"><font class="ver81"><?=$popupDateStr?></font></td>
	<td align="center"><font class="ver81"><?=$pVal['popup_spotw']?> x <?=$pVal['popup_spoth']?></font></td>
	<td align="center"><font class="ver81"><?=$pVal['popup_sizew']?> x <?=$pVal['popup_sizeh']?></font></td>
	<td align="center"><font class="ver81"><?=$popup_use?></font></td>
	<td align="center"><font class="ver81"><?=$popup_type?></font></td>
	<td align="center"><a href="javascript:popup2('../../proc/multipopup_content.php?code=<?=$pVal['code']?>','<?=$pVal['popup_sizew']?>','<?=$pVal['popup_sizeh']?>')"><img src="../img/i_view_popup.gif" alert="화면보기" /></a></td>
	<td align="center"><a href="iframe.multi_popup_register.php?code=<?=$pVal['code']?>"><img src="../img/i_edit.gif" alert="수정" /></a></td>
	<td align="center"><a href="./indb.multipopup.php?mode=copyPopup&code=<?=$pVal['code']?>" onclick="return confirm('동일한 팝업을 하나 더 자동등록합니다')"><img src="../img/i_copy.gif" alert="복사" /></a></td>
	<td align="center"><a href="./indb.multipopup.php?mode=delPopup&code=<?=$pVal['code']?>" onclick="return confirm('팝업을 삭제하시겠습니까?')"><img src="../img/i_del.gif" alert="삭제" /></a></td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
<?
		}
	}
?>
</table>

<table width="100%">
<tr><td height=10></td></tr>
<tr>
	<td align="center"><a href="iframe.multi_popup_register.php?file="><img src="../img/btn_popup_make.gif" alert="멀티팝업만들기" /><a/></td>
</tr>
</table>


<div style="padding-top:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'팝업창만들기'를 클릭하면 멀티 팝업을 새로 만들수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'화면보기'를 클릭하면 멀티 팝업 화면을 볼 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />멀티 팝업마다 노출출력기간을 설정 할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>
<script>
table_design_load();
setHeight_ifrmCodi();
</script>