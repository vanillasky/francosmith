<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";
include "../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php";

### 팝업설정 불러오기
$tmp	= array_keys( $design_skin );
$keys	= array_ereg( "'^popup/[^/]*$'si", $tmp );

### 검색 설정
$selected['skey'][$_GET['skey']]	= "selected";
$checked['use'][$_GET['use']]		= "checked";
$checked['type'][$_GET['type']]		= "checked";

foreach ( $keys as $filenm ){

	$design_skin[$filenm]['file']	= str_replace(".php",".htm",str_replace("popup/","",str_replace("main/html.php?htmid=popup/","",$design_skin[$filenm]['linkurl'])));
	$popupSearch	= $design_skin[$filenm];

	# 키워드 검색
	if($_GET['skey'] && $_GET['sword']){
		if(eregi($_GET['sword'],$design_skin[$filenm][$_GET['skey']])){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# 출력일자
	if($_GET['sregdt'][0] && $_GET['sregdt'][1]){
		if($popupSearch['popup_sdt'] >= $_GET['sregdt'][0] && $popupSearch['popup_edt'] <= $_GET['sregdt'][1]){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# 출력여부 검색
	if($_GET['use']){
		if($popupSearch['popup_use'] == $_GET['use']){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# 창타입 검색
	if($_GET['type']){
		if($_GET['type'] == "layer" || $_GET['type'] == "layerMove"){
			if($popupSearch['popup_type'] == $_GET['type']){
				$popupSearch	= $popupSearch;
			}else{
				unset($popupSearch);
			}
		}else{
			if($popupSearch['popup_type'] == ""){
				$popupSearch	= $popupSearch;
			}else{
				unset($popupSearch);
			}
		}
		$searchYN	= "Y";
	}

	if($searchYN == "Y"){
		if(is_array($popupSearch))$popupConf[]	= $popupSearch;
	}else{
		$popupConf[]	= $design_skin[$filenm];
	}
}
?>
<form>

<div class="title title_top">메인팝업창 관리<span>메인 팝업창에 대한 설정을 추가 변경하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?=$workSkinStr?>
<table class="tb">
<col class="cellC"><col class="cellL" style="width:170px">
<col class="cellC"><col class="cellL">
<tr>
	<td>키워드검색</td>
	<td colspan="3">
	<select name="skey">
	<option value="text" <?=$selected['skey']['text']?>> 팝업제목 </option>
	<option value="file" <?=$selected['skey']['file']?>> 팝업화일명 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
</tr>
<tr>
	<td>출력기간</td>
	<td colspan="3">
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event);" class="cline" /> -
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>출력여부</td>
	<td class="noline">
	<input type="radio" name="use" value="" <?=$checked['use']['']?> />전체
	<input type="radio" name="use" value="Y" <?=$checked['use']['Y']?> />출력
	<input type="radio" name="use" value="N" <?=$checked['use']['N']?> />미출력
	</td>
	<td>창타입</td>
	<td class="noline" nowrap>
	<input type="radio" name="type" value="" <?=$checked['type']['']?> />전체
	<input type="radio" name="type" value="layerMove" <?=$checked['type']['layerMove']?> />이동레이어
	<input type="radio" name="type" value="layer" <?=$checked['type']['layer']?> />고정레이어
	<input type="radio" name="type" value="win" <?=$checked['type']['win']?> />일반팝업창
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">총(검색) <font class="ver8"><b><?=count($popupConf)?></b>개</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="11"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>팝업제목</th>
	<th>팝업파일명</th>
	<th>출력기간/시간</th>
	<th>창위치</th>
	<th>창크기</th>
	<th>출력여부</th>
	<th>창타입</th>
	<th>보기</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
<tr><td class="rnd" colspan="11"></td></tr>
<col width="30" align="center">
<col style="padding-left:20px;">
<col width="115" align="center">
<col width="115" align="center">
<col width="65" align="center">
<col width="65" align="center">
<col width="55" align="center">
<col width="65" align="center">
<col width="55" align="center">
<col width="40" align="center">
<col width="40" align="center">
<?
	if(is_array($popupConf)){
		krsort($popupConf);
		foreach ( $popupConf as $pKey => $pVal ){
			### 출력여부
			if($pVal['popup_use'] == "Y"){
				$popup_use	= "<font color=\"0074ba\">출력</font>";
			}else{
				$popup_use	= "<font color=\"ff0000\">미출력</font>";
			}

			### 팝업창타입
			if($pVal['popup_type'] == "layerMove"){
				$popup_type	= "<font color=\"ff8000\">이동레이어</font>";
			}else if($pVal['popup_type'] == "layer"){
				$popup_type	= "<font color=\"ff8000\">고정레이어</font>";
			}else{
				$popup_type	= "<font color=\"0074ba\">일반팝업창</font>";
			}

			### 출력일자 설정
			if ( $pVal['popup_sdt'] && $pVal['popup_sdt'] != "00000000" && $pVal['popup_edt'] && $pVal['popup_edt'] != "00000000" ){
				$popupDate1	= substr($pVal['popup_sdt'],2,2)."-".substr($pVal['popup_sdt'],4,2)."-".substr($pVal['popup_sdt'],6,2);
				$popupDate2	= substr($pVal['popup_edt'],2,2)."-".substr($pVal['popup_edt'],4,2)."-".substr($pVal['popup_edt'],6,2);
				$popupDate	= $popupDate1 . " ~ " . $popupDate2;
				if( $pVal['popup_sdt'] > date("Ymd") || $pVal['popup_edt'] < date("Ymd") ){
					$popupPeriodChk	= "N";
				}else{
					$popupPeriodChk	= "Y";
				}
			}else{
				$popupDate	= "일자제한 없음";
				$popupPeriodChk	= "A";
			}

			### 출력일자 종속
			if($pVal['popup_dt2tm'] == "Y"){
				$popup_dt2tm	= "●";
			}

			### 출력시간 설정
			if ( $pVal['popup_stime'] && $pVal['popup_stime'] != "0000" && $pVal['popup_etime'] && $pVal['popup_etime'] != "0000" ){
				$popupTime1	= substr($pVal['popup_stime'],0,2).":".substr($pVal['popup_stime'],2,2);
				$popupTime2	= substr($pVal['popup_etime'],0,2).":".substr($pVal['popup_etime'],2,2);
				$popupTime	= $popupTime1 . " ~ " . $popupTime2;

				if($popupPeriodChk == "N"){
					$popupDateStr	= "<font color=\"ff0000\">".$popupDate."<br />".$popup_dt2tm.$popupTime."</font>";
				}else{
					$popupDateStr	= "<font color=\"0074ba\">".$popupDate."<br />".$popup_dt2tm.$popupTime."</font>";
				}
			}else{
				if($popupPeriodChk == "N"){
					$popupDateStr	= "<font color=\"ff0000\">".$popupDate."<br />시간제한 없음</font>";
				}else if($popupPeriodChk == "Y"){
					$popupDateStr	= "<font color=\"0074ba\">".$popupDate."<br />시간제한 없음</font>";
				}else{
					$popupDateStr	= "<font color=\"0074ba\">기간제한 없음</font>";
				}
			}
?>
<tr height="30">
	<td><font class="ver81" color=616161><?=($pKey + 1)?></font></td>
	<td><font color="0074ba"><b><?=$pVal['text']?></b></font></td>
	<td><font class="ver81"><?=$pVal['file']?></font></td>
	<td><font class="ver81"><?=$popupDateStr?></font></td>
	<td><font class="ver81"><?=$pVal['popup_spotw']?> x <?=$pVal['popup_spoth']?></font></td>
	<td><font class="ver81"><?=$pVal['popup_sizew']?> x <?=$pVal['popup_sizeh']?></font></td>
	<td><font class="ver81"><?=$popup_use?></font></td>
	<td><font class="ver81"><?=$popup_type?></font></td>
	<td><a href="javascript:popup2('../../<?=$pVal['linkurl']?>','<?=$pVal['popup_sizew']?>','<?=$pVal['popup_sizeh']?>')"><img src="../img/i_view_popup.gif"></a></td>
	<td><a href="iframe.popup_register.php?file=<?=$pVal['file']?>"><img src="../img/i_edit.gif"></a></td>
	<td><a href="javascript:file_del('popup/<?=$pVal['file']?>');"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td colspan="11" class="rndline"></td></tr>
<?
		}
	}
?>
</table>

<table width="100%">
<tr><td height=10></td></tr>
<tr>
	<td align=center><a href="iframe.popup_register.php?file="><img src="../img/btn_popup_make.gif"><a/></td>
</tr>
</table>


<div style="padding-top:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'팝업창만들기'를 클릭하면 팝업창을 새로 만들수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'화면보기'를 클릭하면 팝업창 화면을 볼 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />팝업창마다 노출출력기간을 설정 할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>