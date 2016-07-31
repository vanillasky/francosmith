<?php
$location = "�⼮üũ���� > �⼮üũ ����Ʈ";
include "../_header.php";

$attd = Core::loader('attendance');

$page = (int)$_GET['page']?(int)$_GET['page']:1;
$name = (string)$_GET['name'];
$ar_mobile_useyn=(array)$_GET['mobile_useyn'];
$ar_status=(array)$_GET['status'];
$ar_condition_type=(array)$_GET['condition_type'];
$ar_check_method=(array)$_GET['check_method'];


// ���ǽ� �����
$ar_where=array();
$curdate = date('Y-m-d');

if($name) {
	$name = $db->_escape($name);
	$ar_where[] = "a.name like '%$name%'";
}

if (count($ar_mobile_useyn)) {
	$ar_where[] = "a.mobile_useyn IN('".implode("','", $ar_mobile_useyn)."')";
}

$ar_where_date=array();

if(in_array('before',$ar_status)) {
	$ar_where_date[] = "(a.start_date > '{$curdate}' and a.manual_stop='n')";
}
if(in_array('progress',$ar_status)) {
	$ar_where_date[] = "(a.start_date <= '{$curdate}' and a.end_date >= '{$curdate}' and a.manual_stop='n')";
}
if(in_array('done',$ar_status)) {
	$ar_where_date[] = "(a.end_date < '{$curdate}' and a.manual_stop='n')";
}
if(in_array('stop',$ar_status)) {
	$ar_where_date[] = "a.manual_stop = 'y'";
}

if(count($ar_where_date)) {
	$ar_where[] = implode(' or ',$ar_where_date);
}

if(count($ar_condition_type)) {
	$ar_where[] = $db->_query_print("a.condition_type in [v]",$ar_condition_type);
}

if(count($ar_check_method)) {
	$ar_where[] = $db->_query_print("a.check_method in [v]",$ar_check_method);
}

$strWhere='';
if(count($ar_where)) {
	$strWhere = ' where '.implode(' and ',$ar_where);
}

$db = Core::loader('db');
$page = (int)$page;
$query = "
	select
		a.attendance_no,a.name,
		a.start_date,a.end_date,
		a.provide_method,
		a.mobile_useyn,
		a.condition_type,a.condition_period,a.auto_reserve,
		a.check_method,a.check_message_type,
		a.reg_date,a.manual_stop,
		count(ac.member_no) as member_count,
		sum( if(ac.reserve>0,1,0) ) as member_provided,
		sum( if(ac.check_period>=a.condition_period,1,0) ) as member_case
	from
		gd_attendance_check AS ac
		inner join gd_member as m on ac.member_no=m.m_no
		right join gd_attendance AS a ON a.attendance_no = ac.attendance_no
	{$strWhere}
	group by
		a.attendance_no
";

$result = $db->_select_page(15,$page,$query);

?>
<script type="text/javascript">

document.observe("dom:loaded", function() {
	var frm = $('frmSearch');
	<? foreach($ar_status as $v): ?>
	frm.setValue('status[]',"<?=$v?>");
	<? endforeach; ?>
	<? foreach($ar_condition_type as $v): ?>
	frm.setValue('condition_type[]',"<?=$v?>");
	<? endforeach; ?>
	<? foreach($ar_check_method as $v): ?>
	frm.setValue('check_method[]',"<?=$v?>");
	<? endforeach; ?>
});


function delete_attendance(attendance_no) {
	if(window.confirm("�����Ͻ� �⼮üũ�� ���� ��絥���Ͱ� �����˴ϴ�.")) {
		ifrmHidden.location.href = 'attendance.indb.php?mode=attd_delete&attendance_no='+attendance_no;
	}
}

function stop_attendance(attendance_no) {
	if(window.confirm("�����Ͻ� �⼮üũ�� �����Ͻðڽ��ϱ�?")) {
		ifrmHidden.location.href = 'attendance.indb.php?mode=attd_stop&attendance_no='+attendance_no;
	}
}
function copyText(text) {
	window.clipboardData.setData('Text',text);
	alert('Ŭ�����忡 ����Ǿ����ϴ�');
}
</script>
<div class="title title_top">�⼮üũ ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmSearch" method='get'>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><font class="small1">�⼮üũ��</font></td>
	<td>
		<input type="text" name="name" value="<?=$name?>" class="line">
	</td>
</tr>
<tr>
	<td><span class="small1">�⼮üũ ����</span></td>
	<td class="noline">
		<input id="mobile_useyn_n" type="checkbox" name="mobile_useyn[]" value="n"/>
		<label for="mobile_useyn_n">PC</label>
		<input id="mobile_useyn_y" type="checkbox" name="mobile_useyn[]" value="y"/>
		<label for="mobile_useyn_y">PC + ����ϼ�</label>
	</td>
</tr>
<tr>
	<td><font class="small1">�⼮üũ ����</font></td>
	<td class="noline">
		<input type="checkbox" name="status[]" value="before">������
		<input type="checkbox" name="status[]" value="progress">������
		<input type="checkbox" name="status[]" value="done">����Ϸ�
		<input type="checkbox" name="status[]" value="stop">��������
	</td>
</tr>
<tr>
	<td><font class="small1">�⼮üũ ����</font></td>
	<td class="noline">
		<input type="checkbox" name="condition_type[]" value="straight">���� �⼮��
		<input type="checkbox" name="condition_type[]" value="sum">Ƚ�� �⼮��

	</td>
</tr>
<tr>
	<td><font class="small1">�⼮üũ ���</font></td>
	<td class="noline">
		<input type="checkbox" name="check_method[]" value="stamp">������
		<input type="checkbox" name="check_method[]" value="comment">���
		<input type="checkbox" name="check_method[]" value="login">�α���
	</td>
</tr>

</table>


<table width="100%">
<tr>
	<td align="center">
	<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
	</td>
</tr>
</table>


</form>

<br><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="15"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th colspan="3">�⼮üũ��</th>
	<th>����</th>
	<th>�Ⱓ</th>
	<th>����</th>
	<th>����</th>
	<th>���</th>
	<th colspan="2">�����⼮</th>
	<th>����������</th>

	<th colspan="2">����</th>
	<th>����/����</th>

</tr>
<tr><td class="rnd" colspan="16"></td></tr>

<col align="center" width="40"/>
<col align="center" />
<col align="center" width="50" />
<col align="center" width="50" />
<col align="center" width="110" />
<col align="center" width="90" />
<col align="center" width="70" />
<col align="center" width="60" />
<col align="center" width="70" />
<col align="center" width="50" />
<col align="center" width="100" />

<col align="center" width="70" />
<col align="center" width="50" />
<col align="center" width="80" />

<?
//$ar_provide_method = array('manual'=>'�����ݼ�������','auto'=>'�������ڵ�');
$ar_check_method = array('stamp'=>'������','comment'=>'���','login'=>'�α���');
$int_today = (int)date('Ymd');
?>


<? foreach($result['record'] as $k=>$data): // ��� ���� ���� ?>
<?
	$data['start_date']=str_replace('-','.',$data['start_date']);
	$data['end_date']=str_replace('-','.',$data['end_date']);
	$data['int_start_date']=(int)str_replace('.','',$data['start_date']);
	$data['int_end_date']=(int)str_replace('.','',$data['end_date']);


	// ��ý ���� ǥ��
	$data['condition_str'] = $data['condition_period'].'��';
	if($data['condition_type']=='straight')
		$data['condition_str'] .= ' �����⼮';
	else
		$data['condition_str'] .= ' �⼮';

	// ��ý ���� ǥ��
	if($data['provide_method']=='manual') {
		$data['provide_method_str'] = '��������';
	}
	else {
		$data['provide_method_str'] = number_format($data['auto_reserve']).'�� �ڵ�����';
	}

	// ��ý ���� ǥ��
	if($data['manual_stop']=='y') {
		$data['status']='��������';
		$data['isStop']=false;
	}
	else {
		if($int_today <= $data['int_end_date'] && $int_today >= $data['int_start_date']) {
			$data['status']='������';
			$data['isStop']=true;
		}
		elseif($int_today > $data['int_start_date']) {
			$data['status']='����Ϸ�';
			$data['isStop']=false;
		}
		else {
			$data['status']='������';
			$data['isStop']=false;
		}
	}

?>

<tr><td height="4" colspan="16"></td></tr>
<tr height="25">
	<td><font class="ver81" color="#616161"><?=$data['attendance_no']?></font></td>
	<td><font class="ver81" color="#616161">
		<?=$data['name']?>
	</font></td>
	<td>
		<a href="../../member/attendance.php?attendance_no=<?=$data['attendance_no']?>" target="_blank">
			<img src="../img/btn_s_screenview.gif">
		</a>
	</td>
	<td>
		<img src="../img/btn_s_urlcopy2.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('../member/attendance.php?attendance_no=<?=$data['attendance_no']?>')"><br>
		<img src="../img/btn_s_urlcopy1.gif" align="absmiddle" style="cursor:pointer" onclick="copyText('<?=$sitelink->link('member/attendance.php?attendance_no='.$data['attendance_no'],'auto',true)?>')">
	</td>
	<td>
		<?php if ($data['mobile_useyn'] === 'y') { ?>
		<span class="ver81" style="color: #616161;">PC + ����ϼ�</span>
		<?php } else { ?>
		<span class="ver81" style="color: #616161;">PC</span>
		<?php } ?>
	</td>
	<td><font class="ver81" color="#616161"><?=$data['start_date']?>~<br><?=$data['end_date']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['condition_str']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['provide_method_str']?></font></td>
	<td><font class="ver81" color="#616161"><?=$ar_check_method[$data['check_method']]?></font></td>
	<td><font class="ver81" color="#616161">
		<?=$data['member_count']?>

	</font></td>
	<td><font class="ver81" color="#616161">
		<a href="javascript:popupLayer('attendance_detail.php?attendance_no=<?=$data['attendance_no']?>',850,600);">
			<img src="../img/btn_s_attendance.gif">
		</a>
	</font></td>
	<td><font class="ver81" color="#616161"><?=$data['member_provided']?> / <?=$data['member_case']?></font></td>

	<td><font class="ver81" color="#616161">
	<?=$data['status']?>
	</font></td>
	<td><font class="ver81" color="#616161">
	<? if($data['isStop']): ?>
		<a href="javascript:stop_attendance(<?=$data['attendance_no']?>)">
			<img src="../img/btn_s_quit.gif">
		</a>
	<? endif; ?>
	</font></td>
	<td><font class="ver81" color="#616161">
		<a href="attendance_form.php?attendance_no=<?=$data['attendance_no']?>&mode=modify"><img src="../img/i_edit.gif" /></a>
		<a href="javascript:delete_attendance(<?=$data['attendance_no']?>)"><img src="../img/i_del.gif" /></a>
	</font></td>
</tr>


<? endforeach; // ��� ���� ���� ?>

<tr><td height="4"></td></tr>
<tr><td colspan="165" class="rndline"></td></tr>
</table>


<? $pageNavi = &$result['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">�� </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">��</a>
	<? endif; ?>
</div>


<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�⼮üũ �Ⱓ�� �ߺ��ǰ� ���� �� �� �����ϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ϵ� �⼮üũ�� ������ �⼮üũ��� �������������θ� �����մϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�⼮üũ �̺�Ʈ�� �����߿� ������ ���� �� �� �ֽ��ϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�⼮üũ ���� �� ȸ������ ���޵� �������� ��ȯ���� ������, ȸ���� ������ ������ ���� �ֽ��ϴ�</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ȸ���� �ڽ��� �⼮üũ ��Ȳ�� '�⼮üũ ������' �Ǵ� ���������� �� '�⼮üũ ����'�޴����� Ȯ�� �� �� �ֽ��ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
