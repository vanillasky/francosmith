<?

$scriptLoad='<script src="../todayshop/codi/_codi.js"></script>';
include "../_header.popup.php";
@include "../../conf/design_skinToday_" . $cfg['tplSkinTodayWork'] . ".php";

### �˾����� �ҷ�����
$tmp	= @array_keys( $design_skinToday );
$keys	= @array_ereg( "'^popup/[^/]*$'si", $tmp );

### �˻� ����
$selected['skey'][$_GET['skey']]	= "selected";
$checked['use'][$_GET['use']]		= "checked";
$checked['type'][$_GET['type']]		= "checked";

foreach ( $keys as $filenm ){

	$design_skinToday[$filenm]['file']	= str_replace(".php",".htm",str_replace("popup/","",str_replace("todayshop/html.php?htmid=popup/","",$design_skinToday[$filenm]['linkurl'])));
	$popupSearch	= $design_skinToday[$filenm];

	# Ű���� �˻�
	if($_GET['skey'] && $_GET['sword']){
		if(eregi($_GET['sword'],$design_skinToday[$filenm][$_GET['skey']])){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# �������
	if($_GET['sregdt'][0] && $_GET['sregdt'][1]){
		if($popupSearch['popup_sdt'] >= $_GET['sregdt'][0] && $popupSearch['popup_edt'] <= $_GET['sregdt'][1]){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# ��¿��� �˻�
	if($_GET['use']){
		if($popupSearch['popup_use'] == $_GET['use']){
			$popupSearch	= $popupSearch;
		}else{
			unset($popupSearch);
		}
		$searchYN	= "Y";
	}

	# âŸ�� �˻�
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
		$popupConf[]	= $design_skinToday[$filenm];
	}
}
?>
<form>

<div class="title title_top">�����̼� �����˾�â ����<span>�����̼� ���� �˾�â�� ���� ������ �߰� �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?=$workSkinTodayStr?>
<table class="tb">
<col class="cellC"><col class="cellL" style="width:170px">
<col class="cellC"><col class="cellL">
<tr>
	<td>Ű����˻�</td>
	<td colspan="3">
	<select name="skey">
	<option value="text" <?=$selected['skey']['text']?>> �˾����� </option>
	<option value="file" <?=$selected['skey']['file']?>> �˾�ȭ�ϸ� </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
</tr>
<tr>
	<td>��±Ⱓ</td>
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
	<td>��¿���</td>
	<td class="noline">
	<input type="radio" name="use" value="" <?=$checked['use']['']?> />��ü
	<input type="radio" name="use" value="Y" <?=$checked['use']['Y']?> />���
	<input type="radio" name="use" value="N" <?=$checked['use']['N']?> />�����
	</td>
	<td>âŸ��</td>
	<td class="noline" nowrap>
	<input type="radio" name="type" value="" <?=$checked['type']['']?> />��ü
	<input type="radio" name="type" value="layerMove" <?=$checked['type']['layerMove']?> />�̵����̾�
	<input type="radio" name="type" value="layer" <?=$checked['type']['layer']?> />�������̾�
	<input type="radio" name="type" value="win" <?=$checked['type']['win']?> />�Ϲ��˾�â
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">��(�˻�) <font class="ver8"><b><?=count($popupConf)?></b>��</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="11"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�˾�����</th>
	<th>�˾����ϸ�</th>
	<th>��±Ⱓ/�ð�</th>
	<th>â��ġ</th>
	<th>âũ��</th>
	<th>��¿���</th>
	<th>âŸ��</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
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
			### ��¿���
			if($pVal['popup_use'] == "Y"){
				$popup_use	= "<font color=\"0074ba\">���</font>";
			}else{
				$popup_use	= "<font color=\"ff0000\">�����</font>";
			}

			### �˾�âŸ��
			if($pVal['popup_type'] == "layerMove"){
				$popup_type	= "<font color=\"ff8000\">�̵����̾�</font>";
			}else if($pVal['popup_type'] == "layer"){
				$popup_type	= "<font color=\"ff8000\">�������̾�</font>";
			}else{
				$popup_type	= "<font color=\"0074ba\">�Ϲ��˾�â</font>";
			}

			### ������� ����
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
				$popupDate	= "�������� ����";
				$popupPeriodChk	= "A";
			}

			### ������� ����
			if($pVal['popup_dt2tm'] == "Y"){
				$popup_dt2tm	= "��";
			}

			### ��½ð� ����
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
					$popupDateStr	= "<font color=\"ff0000\">".$popupDate."<br />�ð����� ����</font>";
				}else if($popupPeriodChk == "Y"){
					$popupDateStr	= "<font color=\"0074ba\">".$popupDate."<br />�ð����� ����</font>";
				}else{
					$popupDateStr	= "<font color=\"0074ba\">�Ⱓ���� ����</font>";
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'�˾�â�����'�� Ŭ���ϸ� �˾�â�� ���� ����� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'ȭ�麸��'�� Ŭ���ϸ� �˾�â ȭ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�˾�â���� ������±Ⱓ�� ���� �� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>