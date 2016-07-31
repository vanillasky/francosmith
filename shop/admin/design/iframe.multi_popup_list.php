<?php
/**
 * ��Ƽ �˾� ����Ʈ ������
 * @author cjb3333 , artherot @ godosoft development team.
 */

$scriptLoad	= '<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

// ��Ƽ �˾� Class
$multipopup	= Core::loader('MultiPopup');
$popupList	= $multipopup->getPopupList();

// �˻� ����
$selected['skey'][$_GET['skey']]	= 'selected';
$checked['use'][$_GET['use']]		= 'checked';
$checked['type'][$_GET['type']]		= 'checked';

// ����Ʈ ���� �迭ó��
foreach ( $popupList as $popupData )
{
	$popupSearch	= $popupData;

	// Ű���� �˻�
	if ($_GET['skey'] && $_GET['sword']) {
		if (preg_match('/'.$_GET['sword'].'/i',$popupData[$_GET['skey']])) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// �������
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

	// ��¿��� �˻�
	if ($_GET['use']) {
		if ($popupSearch['popup_use'] == $_GET['use']) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// âŸ�� �˻�
	if ($_GET['type']) {
		if ($popupSearch['popup_type'] == $_GET['type']) {
			$popupSearch	= $popupSearch;
		} else {
			unset($popupSearch);
		}
		$searchYN			= 'Y';
	}

	// �˻����ο� ���� ����Ÿ ó��
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

<div class="title title_top">��Ƽ �˾� ����<span>��Ƽ �˾��� ���� ������ �߰� / �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?php echo $guideUrl;?>board/view.php?id=design&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<?php echo $workSkinStr;?>
<table class="tb">
<col class="cellC"><col class="cellL" style="width:170px"><col class="cellC"><col class="cellL">
<tr>
	<td>Ű����˻�</td>
	<td colspan="3">
		<select name="skey">
			<option value="text" <?php echo $selected['skey']['text'];?>> �˾����� </option>
			<option value="code" <?php echo $selected['skey']['code'];?>> �˾��ڵ� </option>
		</select>
		<input type="text" NAME="sword" value="<?php echo $_GET['sword'];?>" class="line" />
	</td>
</tr>
<tr>
	<td>�������</td>
	<td colspan="3">
		<input type="text" name="sregdt" value="<?php echo $_GET['sregdt'];?>" onclick="calendar(event);" size="10" maxlength="8" class="tline center" readonly="readonly" />
		<img src="../img/sicon_today.gif" align="absmiddle" alert="����" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd");?>';" />
		<img src="../img/sicon_week.gif" align="absmiddle" alert="������" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-7 day"));?>';" />
		<img src="../img/sicon_twoweek.gif" align="absmiddle" alert="15��" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-15 day"));?>';" />
		<img src="../img/sicon_month.gif" align="absmiddle" alert="�Ѵ�" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-1 month"));?>';" />
		<img src="../img/sicon_twomonth.gif" align="absmiddle" alert="�δ�" class="hand" onclick="javascript:document.frmSearch.sregdt.value='<?php echo date("Ymd",strtotime("-2 month"));?>';" />
		<img src="../img/sicon_all.gif" align="absmiddle" alert="��ü" class="hand" onclick="javascript:document.frmSearch.sregdt.value='';" />
	</td>
</tr>
<tr>
	<td>��¿���</td>
	<td class="noline">
		<input type="radio" name="use" value="" <?php echo $checked['use'][''];?> />��ü
		<input type="radio" name="use" value="Y" <?php echo $checked['use']['Y'];?> />���
		<input type="radio" name="use" value="N" <?php echo $checked['use']['N'];?> />�����
	</td>
	<td>âŸ��</td>
	<td class="noline" nowrap>
		<input type="radio" name="type" value="" <?php echo $checked['type'][''];?> />��ü
		<input type="radio" name="type" value="layerMove" <?php echo $checked['type']['layerMove'];?> />�̵����̾�
		<input type="radio" name="type" value="layer" <?php echo $checked['type']['layer'];?> />�������̾�
		<input type="radio" name="type" value="window" <?php echo $checked['type']['window'];?> />�Ϲ��˾�
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" alert="�˻�" /></div>

<table width="100%">
<tr>
	<td class="pageInfo">��(�˻�) <font class="ver8"><b><?php echo count($popupConf);?></b>��</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�˾�����</th>
	<th>�˾��ڵ�</th>
	<th>��±Ⱓ/�ð�</th>
	<th>â��ġ</th>
	<th>âũ��</th>
	<th>��¿���</th>
	<th>âŸ��</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
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
	// ����Ÿ�� �ִ� ���
	if (is_array($popupConf)) {
		krsort($popupConf);
		foreach ( $popupConf as $pKey => $pVal ){
			// ��¿���
			if ($pVal['popup_use'] == "Y") {
				$popup_use	= "<font color=\"0074ba\">���</font>";
			} else {
				$popup_use	= "<font color=\"ff0000\">�����</font>";
			}

			// �˾� Ÿ��
			if ($pVal['popup_type'] == "layerMove") {
				$popup_type	= "<font color=\"ff8000\">�̵����̾�</font>";
			} else if ($pVal['popup_type'] == "layer") {
				$popup_type	= "<font color=\"ff8000\">�������̾�</font>";
			} else {
				$popup_type	= "<font color=\"0074ba\">�Ϲ��˾�</font>";
			}

			// Ư���Ⱓ���� �˾�â ����
			if ($pVal['popup_dt2tm'] == 'Y') {
				$tmp1	= substr($pVal['popup_sdt'],0,4).'-'.substr($pVal['popup_sdt'],4,2).'-'.substr($pVal['popup_sdt'],6,2).' '.substr($pVal['popup_stime'],0,2).':'.substr($pVal['popup_stime'],2,2).' ~';
				$tmp2	= substr($pVal['popup_edt'],0,4).'-'.substr($pVal['popup_edt'],4,2).'-'.substr($pVal['popup_edt'],6,2).' '.substr($pVal['popup_etime'],0,2).':'.substr($pVal['popup_etime'],2,2);

				$popupDateStr	= '<font color="0074ba">'.$tmp1.'<br />'.$tmp2.'</font>';
				unset($tmp1,$tmp2);
			}
			// Ư���Ⱓ���� Ư���ð����� �˾�â ����
			else if ($pVal['popup_dt2tm'] == 'T') {
				$tmp1	= substr($pVal['popup_sdt'],0,4).'-'.substr($pVal['popup_sdt'],4,2).'-'.substr($pVal['popup_sdt'],6,2).' ~ '.substr($pVal['popup_edt'],0,4).'-'.substr($pVal['popup_edt'],4,2).'-'.substr($pVal['popup_edt'],6,2);
				$tmp2	= substr($pVal['popup_stime'],0,2).':'.substr($pVal['popup_stime'],2,2).' ~ '.substr($pVal['popup_etime'],0,2).':'.substr($pVal['popup_etime'],2,2);

				$popupDateStr	= '<font color="ff8000">'.$tmp1.'<br />'.$tmp2.'</font>';
				unset($tmp1,$tmp2);
			}
			// �׻� �˾�â ����
			else {
				$popupDateStr	= "<font color=\"0074ba\">�׻� �˾�â�� ����</font>";
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
	<td align="center"><a href="javascript:popup2('../../proc/multipopup_content.php?code=<?=$pVal['code']?>','<?=$pVal['popup_sizew']?>','<?=$pVal['popup_sizeh']?>')"><img src="../img/i_view_popup.gif" alert="ȭ�麸��" /></a></td>
	<td align="center"><a href="iframe.multi_popup_register.php?code=<?=$pVal['code']?>"><img src="../img/i_edit.gif" alert="����" /></a></td>
	<td align="center"><a href="./indb.multipopup.php?mode=copyPopup&code=<?=$pVal['code']?>" onclick="return confirm('������ �˾��� �ϳ� �� �ڵ�����մϴ�')"><img src="../img/i_copy.gif" alert="����" /></a></td>
	<td align="center"><a href="./indb.multipopup.php?mode=delPopup&code=<?=$pVal['code']?>" onclick="return confirm('�˾��� �����Ͻðڽ��ϱ�?')"><img src="../img/i_del.gif" alert="����" /></a></td>
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
	<td align="center"><a href="iframe.multi_popup_register.php?file="><img src="../img/btn_popup_make.gif" alert="��Ƽ�˾������" /><a/></td>
</tr>
</table>


<div style="padding-top:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'�˾�â�����'�� Ŭ���ϸ� ��Ƽ �˾��� ���� ����� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'ȭ�麸��'�� Ŭ���ϸ� ��Ƽ �˾� ȭ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />��Ƽ �˾����� ������±Ⱓ�� ���� �� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>
<script>
table_design_load();
setHeight_ifrmCodi();
</script>