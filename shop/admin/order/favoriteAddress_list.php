<?
$location = "�ֹ����� > ���� ���� �ּҷ�";
include "../_header.php";
include "../../lib/page.class.php";

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_FAVORITE_ADDRESS); # �� ���ڵ��

### �����Ҵ�
	$db_table	= GD_FAVORITE_ADDRESS;
	$orderby	= ($_GET['sort'])		? $_GET['sort']			: "regdt DESC";		// ���� ����
	$page_num	= ($_GET['page_num'])	? $_GET['page_num']		: 10;				// ������ ���ڵ� ��

	$selected['page_num'][$page_num]			= "selected";
	$selected['sort'][$orderby]					= "selected";
	$selected['skey'][$_GET['skey']]			= "selected";
	$selected['fa_group'][$_GET['fa_group']]	= "selected";

### �˻�����
	if($_GET['skey'] && $_GET['sword']){
		if($_GET['skey']== 'all') $where[] = "CONCAT( fa_group, fa_name, fa_phone ) LIKE '%".$_GET['sword']."%'";
		else $where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
	}

	if($_GET['fa_group'] && $_GET['fa_group'] != "all") $where[] = "fa_group LIKE '%".$_GET['fa_group']."%'";

	if($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt BETWEEN DATE_FORMAT(".$_GET['sregdt'][0].", '%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['sregdt'][1].", '%Y-%m-%d 23:59:59')";

### ���
	$pg = new Page($_GET['page'], $page_num);

	$pg->field = "fa_no, fa_group, fa_name, fa_email, fa_zipcode, fa_zonecode, fa_address, fa_road_address, fa_address_sub, fa_phone, fa_mobile, fa_memo, regdt";
	$pg->setQuery($db_table, $where, $orderby);
	$pg->exec();
	$res = $db->query($pg->query);

### �׷���
	$groupQuery = "SELECT fa_group FROM ".GD_FAVORITE_ADDRESS." GROUP BY fa_group ORDER BY fa_group DESC";
	$groupResult = $db->query($groupQuery);
?>
<style type="text/css">
	#fa_memoBox { z-index:1000; display:none; position:absolute; top:0; left:0; width:500px; padding:10px; -moz-opacity:.90; filter:alpha(opacity=90); opacity:.90; line-height:140%; background:#FFFFFF; color:#000000; border:1px #000000 solid; }
</style>
<script>
function act_delete_case (idx){
	fmList.delList.value = $$('input[name="confirmyn"]')[idx].value;
	fmList.mode.value = "faDelete";
	fmList.submit() ;
}

function act_delete(){

	if ( PubChkSelect( fmList['confirmyn'] ) == false ){
		alert( "�����Ͻ� ������ �����Ͽ� �ֽʽÿ�." );
		return;
	}

	if ( confirm( "������ �������� ���� �����Ͻðڽ��ϱ�?\n���� �� ������ �� �����ϴ�." ) == false ) return;

	var idx = 0;
	var codes = new Array();
	var count = fmList['confirmyn'].length;

	if ( count == undefined ) codes[ idx++ ] = fmList['confirmyn'].value;
	else {

		for ( i = 0; i < count ; i++ ){
			if ( fmList['confirmyn'][i].checked ) codes[ idx++ ] = fmList['confirmyn'][i].value;
		}
	}

	fmList.delList.value = codes.join( ";" );
	fmList.mode.value = "faDelete";
	fmList.submit() ;
}

function tooltipShow(obj) {

	var tooltip = document.getElementById('fa_memoBox');
	tooltip.innerText = obj.getAttribute('tooltip');

	var pos_x = event.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
	var pos_y = event.clientY + document.body.scrollTop + document.documentElement.scrollTop;

	tooltip.style.top = (pos_y + 10) + 'px';
	tooltip.style.left = (pos_x - 510) + 'px';
	tooltip.style.display = 'block';
}

function tooltipHide(obj) {
	var tooltip = document.getElementById('fa_memoBox');
	tooltip.innerText = '';
	tooltip.style.display = 'none';
}
</script>

<div id="fa_memoBox"></div>

<div class="title title_top">���� ���� �ּҷ� <span>���� �ֹ��� ���� ����ϴ� �ּҸ� �̸� ����Ͽ� ����� �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=35')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<form name="frmList">
<table class="tb">
<col class="cellC" width="15%"><col class="cellL" width="35%"><col class="cellC" width="15%"><col class="cellL" width="35%">
<tr>
	<td>Ű���� �˻�</td>
	<td>
		<select name="skey">
		<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
		<option value="fa_group" <?=$selected['skey']['fa_group']?>>�׷�</option>
		<option value="fa_name" <?=$selected['skey']['fa_name']?>>����</option>
		<option value="fa_phone" <?=$selected['skey']['fa_phone']?>>����ó</option>
		</select> <input type="text" class="line" NAME="sword" value="<?=$_GET['sword']?>">
	</td>
	<td>�׷�</td>
	<td>
		<select name="fa_group">
			<option value="all" <?= $selected["fa_group"]["all"]?>>-�׷��� �������ּ���-</option>
<? while($groupData = $db->fetch($groupResult)) { ?>
			<option value="<?=$groupData['fa_group']?>" <?= $selected["fa_group"][$groupData['fa_group']]?>><?=$groupData['fa_group']?></option>
<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�ۼ���</td>
	<td colspan="3">
	<input type=text name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event)" class="line" /> -
	<input type=text name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event)" class="line" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<div style="padding-top:5px"></div>

<table width="100%">
<tr>
	<td class="pageInfo"><font class="ver8">�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
		<option value="regdt DESC" <?=$selected['sort']['regdt DESC']?>>- �ۼ��� ���ġ�</option>
		<option value="regdt ASC" <?=$selected['sort']['regdt ASC']?>>- �ۼ��� ���ġ�</option>
		<option value="fa_group DESC" <?=$selected['sort']['fa_group DESC']?>>- �׷� ���ġ�</option>
		<option value="fa_group ASC" <?=$selected['sort']['fa_group ASC']?>>- �׷� ���ġ�</option>
		<option value="fa_name DESC" <?=$selected['sort']['fa_name DESC']?>>- �̸� ���ġ�</option>
		<option value="fa_name ASC" <?=$selected['sort']['fa_name ASC']?>>- �̸� ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10, 20, 40, 60, 100);
	foreach ($r_pagenum as $v){
	?>
		<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form name="fmList" method="post" action="../order/indb.favorite_address.php">
<input type="hidden" name="delList" id="delList" />
<input type="hidden" name="mode" id="mode" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th width="40" onclick="javascript:PubAllSordes( 'reflect', fmList['confirmyn'] );" style="cursor:pointer;">����</th>
	<th width="50">��ȣ</th>
	<th width="100">�׷�</th>
	<th width="100">����</th>
	<th width="">�ּ�</th>
	<th width="150">�̸���</th>
	<th width="100">����ó</th>
	<th width="100">�޴���</th>
	<th width="60">�޸�</th>
	<th width="80">�ۼ���</th>
	<th width="45">����</th>
	<th width="45">����</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<?
while($data=$db->fetch($res)) {
	$i = 0;
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25" bgcolor="#ffffff" align="center">
	<td><input type="checkbox" name="confirmyn" value="<?=$data['fa_no']?>" style="border:0"></td>
	<td><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td><?=$data['fa_group']?></td>
	<td><?=$data['fa_name']?></td>
	<td align="left"><?=$data['fa_zonecode']." "."(".$data['fa_zipcode'].")"." ".$data['fa_address']." ".$data['fa_address_sub']?><?if($data['fa_road_address']) { ?><div style="padding:5px 0 0 0px;font:12px dotum;color:#999;" id="div_road_address">[<?=$data['fa_zonecode']." "."(".$data['fa_zipcode'].")"." ".$data['fa_road_address']." ".$data['fa_address_sub']?>]</div><? } ?></td>
	<td><?=$data['fa_email']?></td>
	<td><?=$data['fa_phone']?></td>
	<td><?=$data['fa_mobile']?></td>
	<? if($data['fa_memo']) { ?>
	<td style="cursor:pointer; color:#3482CA;" onmouseover="tooltipShow(this)" onmousemove="tooltipShow(this)" onmouseout="tooltipHide(this)" tooltip="<?=$data['fa_memo']?>">[����]</td>
	<? } else { ?>
	<td>-</td>
	<? } ?>
	<td><?=substr($data['regdt'], 0, 10)?></td>
	<td><a href="javascript:popup2('../order/popup.favorite_address.php?idx=<?=($data['fa_no'])?>',650,450,1)"><img src="../img/i_edit.gif"></a></td>
	<td class="noline"><a href="javascript:act_delete_case(<?=$i++?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height="4"></td></tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>
</form>

<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>

<div style="float:left">
<img src="../img/btn_allselect_s.gif" alt="��ü����" align='absmiddle' style="cursor:pointer;" onclick="javascript:<?=($pg->recode['total']) ? "PubAllSordes( 'select', fmList['confirmyn'] );" : "alert( '����Ÿ�� �������� �ʽ��ϴ�.' );";?>">
<img src="../img/btn_alldeselect_s.gif" alt="��������" align='absmiddle' style="cursor:pointer;" onclick="javascript:<?=($pg->recode['total']) ? "PubAllSordes( 'deselect', fmList['confirmyn'] );" : "alert( '����Ÿ�� �������� �ʽ��ϴ�.' );";?>">
<img src="../img/btn_alldelet_s.gif" alt="���û���" align='absmiddle' style="cursor:pointer;" onclick="javascript:<?=($pg->recode['total']) ? "act_delete();" : "alert( '����Ÿ�� �������� �ʽ��ϴ�.' );";?>">
</div>
<div style="float:right"><img src="../img/btn_address_add.gif" alt="�ּҷ� ���" border="0" align='absmiddle' style="cursor:hand" onclick="javascript:popup2('../order/popup.favorite_address.php',800,600)"></div>
<div style="clear:both;padding-top:15px;"></div>

<? include "../_footer.php"; ?>