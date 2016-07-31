<?php
include "../_header.popup.php";

$attendance_no = (int)$_GET['attendance_no'];
$attd = Core::loader('attendance');

$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;
$search_period = $_GET['search_period'];
$search_member_key = $_GET['search_member_key'];
$search_member_value = $_GET['search_member_value'];
$search_reserve = $_GET['search_reserve'];
if(!$search_member_key) {
	$search_member_key='id';
}
if(!$search_reserve) {
	$search_reserve='all';
}


// ��ý�� �⺻ ������
$query = "select * from gd_attendance where attendance_no='{$attendance_no}'";
$result = $db->_select($query);
$attd_info = $result[0];

$attd_info['condition_str'] = $attd_info['condition_period'].'��';
if($attd_info['condition_type']=='straight')
	$attd_info['condition_str'] .= ' �����⼮';
else
	$attd_info['condition_str'] .= ' �⼮';

if($attd_info['provide_method']=='manual') {
	$attd_info['provide_method_str'] = '��������';
}
else {
	$attd_info['provide_method_str'] = number_format($attd_info['auto_reserve']).'�� �ڵ�����';
}

// ��ý ���� ǥ��
$attd_info['int_start_date']=(int)str_replace('.','',$attd_info['start_date']);
$attd_info['int_end_date']=(int)str_replace('.','',$attd_info['end_date']);
if($attd_info['manual_stop']=='y') {
	$attd_info['status']='��������';
}
else {
	if($int_curdate < $attd_info['int_end_date']) {
		$attd_info['status']='����Ϸ�';
	}
	elseif($int_curdate > $attd_info['int_start_date']) {
		$attd_info['status']='������';
	}
	else {
		$attd_info['status']='������';
	}
}


// ��ý��Ȳ���ϱ�
$query = "
	select
		count(ac.member_no) as member_count,
		sum( if(ac.reserve>0,1,0) ) as member_provided,
		sum( if(ac.check_period>={$attd_info['condition_period']},1,0) ) as member_case
	from
		gd_attendance_check AS ac
		inner join gd_member as m on ac.member_no=m.m_no
	where
		ac.attendance_no='{$attendance_no}'
";

$result = $db->_select($query);
$stat = $result[0];


$arWhere = array();
$arWhere[] = "ac.attendance_no='{$attendance_no}'";
if($search_period) {
	$search_period = (int)$search_period;
	$arWhere[] = "ac.check_period >= {$search_period}";
}

if($search_member_value) {
	$search_member_value = $db->_escape($search_member_value);
	if($search_member_key=='id') {
		$arWhere[] = "m.m_id = '{$search_member_value}'";
	}
	else {
		$arWhere[] = "m.name = '{$search_member_value}'";
	}
}
if($search_reserve!='all') {

	if($search_reserve=='yes') {
		$arWhere[] = "ac.reserve <> 0";
	}
	else {
		$arWhere[] = "ac.reserve = 0";
	}
}
$strWhere = 'where '.implode(' and ',$arWhere);

// ��ý��� ���ϱ�
$query = "
	select
		ac.check_no,
		ac.check_period,
		ac.provide_method,
		ac.reserve,
		m.m_no,
		m.m_id,
		m.name,
		m.dormant_regDate
	from
		gd_attendance_check as ac
		inner join gd_member as m on ac.member_no=m.m_no
	{$strWhere}
	order by
		ac.check_no asc
";
$check_list = $db->_select_page(10,$page,$query);

foreach($check_list['record'] as $k=>$v) {
	if($check_list['record'][$k]['check_period'] >= $attd_info['condition_period']) {
		$check_list['record'][$k]['case'] = true;
	}
	else {
		$check_list['record'][$k]['case'] = false;
	}
}




?>

<script type="text/javascript">

var chkValue=false;
function allCheck() {
	if(chkValue) chkValue=false;
	else chkValue=true;
	var frmList = $('frmList');
	frmList.getInputs('checkbox','check_no[]').each(function(e){
		if(e.disabled==true) {
			return;
		}
		e.checked=chkValue;
	});
}

function send_reserve() {
	var frmList = $('frmList');
	var chkList=[];
	frmList.getInputs('checkbox','check_no[]').each(function(e){
		if(e.disabled==false && e.checked==true) {
			chkList.push(e.value);
		}
	});
	popupLayer('attendance.reserve.php?attendance_no='+<?=$attendance_no?>+'&check_no='+chkList.join(','),550,400);
}

document.observe("dom:loaded", function() {
	var frm = $('frmSearch');
	frm.setValue('search_member_key',"<?=$search_member_key?>");
	frm.setValue('search_member_value',"<?=$search_member_value?>");
	frm.setValue('search_period',"<?=$search_period?>");
	frm.setValue('search_reserve',"<?=$search_reserve?>");


});



</script>

<div class="title title_top" style="">�⼮�� ��Ȳ - <?=$attd_info['name']?></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th>��ý�Ⱓ</th>
	<th>����</th>
	<th>����</th>
	<th>���</th>
	<th>����������</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>

<col align="center" width="150"/>
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="100" />

<?
$ar_check_method = array('stamp'=>'������','comment'=>'���','login'=>'�α���');
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25">
	<td><font class="ver81" color="#616161">
		<?=$attd_info['start_date']?> ~ <?=$attd_info['end_date']?>
	</font></td>
	<td><font class="ver81" color="#616161"><?=$attd_info['condition_str']?></font></td>
	<td><font class="ver81" color="#616161"><?=$attd_info['provide_method_str']?></font></td>
	<td><font class="ver81" color="#616161"><?=$ar_check_method[$attd_info['check_method']]?></font></td>
	<td><font class="ver81" color="#616161"><?=(int)$stat['member_provided']?> / <?=(int)$stat['member_case']?></font></td>
	<td><font class="ver81" color="#616161"><?=$attd_info['status']?></font></td>
</tr>
</table>
<br>

<form name="frmSearch" method="get" action="" id="frmSearch">
<input type="hidden" name="attendance_no" value="<?=$attendance_no?>">
<table style="border-collapse:collapse" cellspacing="0" cellpadding="7" width="100%" border="1" bordercolor="#cccccc">
<tr>
	<td style="background-color:#eeeeee;" width="100" nowrap><font class="small1">�⼮�� �˻�</font></td>
	<td style="padding:0px 0px 0px 15px">
		<select name="search_member_key">
		<option value="id">���̵�</option>
		<option value="name">�̸�</option>
		</select>
		<input type="text" name="search_member_value" value="" size="15" class="line">
	</td>
	<td style="background-color:#eeeeee;" width="100" nowrap><font class="small1">���� �˻�</font></td>
	<td style="padding:0px 0px 0px 15px">
		<? if($attd_info['condition_type']=='straight'):?>
			<input type="text" name="search_period" size="5" class="line"> �� ���� ���� �⼮�� ȸ��
		<? else: ?>
			��ý�Ⱓ���� <input type="text" name="search_period" size="5"> ȸ �̻� ��ý�� ȸ��
		<? endif; ?>
	</td>


</tr>

<tr>
	<td style="background-color:#eeeeee;"><font class="small1">���޿���</font></td>
	<td style="padding:0px 0px 0px 15px" class="noline" colspan="3">
		<input type="radio" name="search_reserve" value="all" checked>���
		<input type="radio" name="search_reserve" value="yes">���޿Ϸ�
		<input type="radio" name="search_reserve" value="no">������
	</td>

</tr>
</table>
<div style="text-align:center;padding:5px">
		<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
</div>
</form>


<form name="frmList" id="frmList">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th style="cursor:pointer" onclick="allCheck()">����</th>
	<th>��ȣ</th>
	<th>���̵�</th>
	<th>�̸�</th>
	<th>�⼮��</th>
	<th>���ô��</th>
	<th colspan="2">����������</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>

<col align="center" width="50"/>
<col align="center" width="50" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="90" />
<col align="center" width="60" />

<? foreach($check_list['record'] as $v): ?>
<?
if($v['reserve'] && $v['provide_method']=='auto') {
	$v['status']='�ڵ������Ϸ�';
	$v['checkbox_status']='disabled';
}
elseif($v['reserve'] && $v['provide_method']=='manual') {
	$v['status']='���������Ϸ�';
	$v['checkbox_status']='disabled';
}
elseif($v['case'] && $attd_info['provide_method']=='auto') {
	$v['status']='�ڵ��������';
	$v['checkbox_status']='';
}
elseif($v['case'] && $attd_info['provide_method']=='manual') {
	$v['status']='�����������';
	$v['checkbox_status']='';
}
else {
	$v['checkbox_status']='disabled';
}

$popupEmoney = "<a href=\"javascript:popupLayer('../member/popup.emoney.php?m_no=".$v['m_no']."',600,500)\"><img src=\"../img/btn_pointview.gif\"></a>";
$checkBoxDisabled = '';
if($v['dormant_regDate'] != '0000-00-00 00:00:00'){
	$v['m_id'] = '�޸�ȸ��';
	$v['status'] = $v['case'] = $popupEmoney = $checkBox = '';
	$checkBoxDisabled = 'disabled';
}
?>
<tr><td height="4" colspan="12"></td></tr>
<tr height="25">
	<td><font class="ver81" color="#616161"><input type="checkbox" style="border-width:0px" name="check_no[]" value="<?=$v['check_no']?>" <?=$v['checkbox_status']?> <?php echo $checkBoxDisabled; ?>></font></td>
	<td><font class="ver81" color="#616161"><?=$v['_rno']?></font></td>
	<td><font class="ver81" color="#616161"><?=$v['m_id']?></font></td>
	<td><font class="ver81" color="#616161"><?=$v['name']?></font></td>
	<td><font class="ver81" color="#616161"><?=$v['check_period']?></font></td>
	<td><font class="ver81" color="#616161"><?=($v['case']?'��':'�ƴϿ�')?></font></td>
	<td><font class="ver81" color="#616161"><?=$v['status']?></font></td>
	<td><font class="ver81" color="#616161"><?=$popupEmoney?></font></td>
</tr>
<? endforeach; ?>
</table>
<img src="../img/btn_moneyprovide.gif" onclick="send_reserve()" style="cursor:pointer">
</form>

<? $pageNavi = &$check_list['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">����</a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">����</a>
	<? endif; ?>
</div>
</body>
</html>
