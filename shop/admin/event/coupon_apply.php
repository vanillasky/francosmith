<?
$location = "����������� > �����߱޳�������";
include "../_header.php";
include "../../lib/page.class.php";

$_GET[mode] = 'list';

$query = "select a.grpnm,a.level,a.sno,count(b.m_no) cnt from ".GD_MEMBER_GRP." a left join ".GD_MEMBER." b on a.level=b.level WHERE b.".MEMBER_DEFAULT_WHERE." group by a.grpnm,a.level,a.sno";
$res5 = $db->query($query);
while($data5 = $db->fetch($res5)){
	$r_mgrp[] = $data5;
	$total += $data5[cnt];
}

$query = "select * from ".GD_COUPON." where couponcd='$_GET[couponcd]'";
$data = $db->fetch($query);
if(substr($data[price],-1) != '%') $data[price] .= "��";

if($data[coupontype] == 0){
	$_GET[mode] = 'applyAdd';
}
if($_GET[sno])$_GET[mode] = 'applyMod';
?>
<script language=javascript>
function del_options(el)
{
	idx = el.rowIndex;
	var obj = document.getElementById('m_ids');
	obj.deleteRow(idx);
}
function checkform1(f){

	calSmsCnt();

	if(f.membertype[1].checked == true && f.member_grp_sno.selectedIndex == 0){
		alert('ȸ���׷��� �����ϼ���!!');
		f.member_grp_sno.focus();
		return false;
	}
	if(f.membertype[2].checked == true){
		if(!document.getElementsByName('m_ids[]').length){
			alert('ȸ���� �������ּ���!!');
			return false;
		}
	}
	if(f.smsyn.checked){
		if(!f.msg.value){
			alert('���� �޽����� �����ּ���!!');
			f.msg.focus();
			return false;
		}
		if(!f.callback.value){
			alert('�������̸��� �����ּ���!!');
			f.callback.focus();
			return false;
		}

	}
	return true;
}

var sgrp = new Array();
<?foreach($r_mgrp as $v){?>
sgrp[<?=$v[sno]?>] = <?=$v[cnt]?>;
<?}?>
var tot_mem = <?=$total?>;
function calSmsCnt(){
	var f = document.forms[1];
	if(f.membertype[0].checked)	var tt = tot_mem;
	if(f.membertype[1].checked){
		if(f.member_grp_sno.selectedIndex > 0){
			var tt = sgrp[f.member_grp_sno[f.member_grp_sno.selectedIndex].value];
		}else{
			var tt = 0;
		}
	}

	if(f.membertype[2].checked) var tt = document.getElementsByName('m_ids[]').length;

	var tot = uncomma(document.getElementById('span_sms').innerHTML);
	if(tt > tot && f.smsyn.checked){
		alert('�����Ǽ��� �ʹ� �����ϴ�.');
		f.smsyn.checked = false;
		return;
	}

	document.getElementById('span_sms_send').innerHTML = comma(tt);
}
function delApply(sno){
	var f = document.hiddenform;
	f.mode.value = "delApply";
	if ( confirm("�׷��̳� ��üȸ���� �߱��� ������ ���� ��� �˴ϴ�") ) {
		f.action = "indb.coupon.php?couponcd=<?=$_GET[couponcd]?>&sno="+sno;
		f.submit();
	}
}
function delApply2(sno,m_no){
	var f = document.hiddenform;
	f.mode.value = "delApply2";
	f.action = "indb.coupon.php?couponcd=<?=$_GET[couponcd]?>&sno="+sno+"&m_no="+m_no;
	f.submit();
}
</script>
<form name=hiddenform method=post>
	<input type=hidden name=mode>
</form>
<div class="title title_top">�����߱�/��ȸ<span>������ ���� �߱��ϰ� ������ �� �ֽ��ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:3 0 5 8"><img src="../img/ico_arrow_down.gif" align=absmiddle> <font color=0074BA><b>�����߱޳���</b></font></div>
<table class=tb>
<tr class=cellC align=center height=25>
	<th align=center width=100>��ȣ</th>
	<th align=center>������</th>
	<th align=center>�����߱޹��</th>
	<th align=center width=120>������</th>
	<th align=center width=100>���αݾ�(��)</th>
	<th align=center width=150>���Ⱓ</th>
	<th align=center width=70>���</th>
</tr>
<tr height=25>
	<td align=center><font class=ver71 color=444444><?=$data[couponcd]?></td>
	<td class=cellL align=center>
		<a href="coupon_register.php?couponcd=<?=$data[couponcd]?>" target='_blank'><font color=0074BA><?=$data[coupon]?></font></a>
	</td>
	<td align=center><?=$r_couponType[$data[coupontype]]?></td>
	<td align=center><font class=ver71 color=444444><?=$data[regdt]?></td>
	<td align=center><font class=ver71 color=444444><?=$data[price]?></td>
	<td align=center><font class=ver71 color=444444>
		<?
		if($data[priodtype] == 0)
			echo $data[sdate] ."<br>~". $data[edate];
		else
			echo "�߱� �� ".$data[sdate]. " ��";
		?>
	</td>
	<td align=center><font class=small1 color=444444><?=$r_couponAbility[$data[ability]]?></td>
</tr>

</table>
<p>
<?
$db_table = "".GD_COUPON_APPLY." a
	left join ".GD_MEMBER_GRP." b on b.sno=a.member_grp_sno and a.membertype ='1' ";
$where[] = "a.couponcd='$_GET[couponcd]'";
$pg = new Page($_GET[page]);
$pg -> field = "*,a.sno,a.regdt";
$pg->setQuery($db_table,$where,"a.regdt");
$pg->exec();

$res = $db->query($pg->query);
?>
<div style="padding:3 0 5 8"><img src="../img/ico_arrow_down.gif" align=absmiddle> <font color=0074BA><b>�� ������ �߱޹��� ȸ������Ʈ</b></font> <font class=extext>(������ư�� Ŭ���ϸ� �ش� ȸ������ �߱޵� ������ ��ҵ˴ϴ�)</font></div>
<table class=tb>
<tr class=cellC align=center height=25>
	<th width=50 align=center>����</th>
	<?if($data[coupontype] =='1'){?>
	<th align=center>�߱� ��ǰ</th>
	<?}?>
	<th align=center>�߱޹��� ȸ��</th>
	<th align=center>�߱���/�����</th>
	<th align=center>����</th>
</tr>
<?
$i=0;
while($row = $db->fetch($res)){
	$goods = $db->fetch("select * from ".GD_GOODS." where goodsno ='".$row['goodsno']."'");
	$msg = ''; $arr = '';
	if($row['membertype'] == 0) list($cnt2) = $db->fetch("select count(*) from ".GD_MEMBER);
	if($row['membertype'] == 1) list($cnt2) = $db->fetch("select count(*) from ".GD_MEMBER." where level = '".$row['level']."'");
	if($row['membertype'] == 2){
		list($cnt2) = $db->fetch("select count(*) from ".GD_COUPON_APPLYMEMBER." where applysno='".$row['sno']."'");
		if($cnt2 == 0) continue;
		$row2 = $db->fetch("select * from ".GD_COUPON_APPLYMEMBER." a left join ".GD_MEMBER." b on a.m_no=b.m_no where a.applysno='".$row['sno']."'");
		if($row2['dormant_regDate'] != '0000-00-00 00:00:00'){
			$row2['m_id'] = '�޸�ȸ��';
		}
		$rcnt2 = $cnt2-1;
		if($rcnt2 > 0) $r2m_id = $row2['m_id']." �� ".$rcnt2."��";
		else $r2m_id = $row2['m_id'];
	}
	list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_ORDER." where applysno='".$row['sno']."'");
	$i++;
?>
<tr height=25>
	<td align=center><font color=777777><?=$i?></td>
	<?if($data['coupontype'] =='1'){?>
	<td  align=left style="padding-left:5"><div style="text-overflow:ellipsis;overflow:hidden;width:300px" nowrap>
		<div style="float:left"><a href="../../goods/goods_view.php?goodsno=<?=$goods['goodsno']?>" target="_blank"><?=goodsimg($goods['img_s'],30,"style='border:1 solid #cccccc'",1)?></a></div>
		<div style="float:left;padding:15,0,0,10"><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$goods['goodsno']?>',825,600)"><font  color=0074BA><?=$goods['goodsnm']?></font></a></div>
	</div></td>
	<?}?>
	<td align=left style="padding-left:9"><?if($row['membertype'] == 2) echo $r2m_id;?><?if($row['membertype'] == 0) echo "��üȸ��";?><?if($row['membertype'] == 1)echo $row['grpnm'];?></b></font><font color="00899d"> (<?=$cnt2?>�� �� <b><?=$cnt?></b>�� ���)</font></td>
	<td align=center><font color=666666><?=$row['regdt']?> <a href="javascript:popup('popup.coupon_user.php?couponcd=<?=$_GET['couponcd']?>&applysno=<?=$row['sno']?>',650,850)"><font color="00899d">[�߱�/��� ȸ������]</font></a></td>
	<td align=center style="padding-top:4">
		<?if($cnt != $cnt2){?>
			<a href="javascript:delApply(<?=$row['sno']?>);"><img src="../img/btn_coupon_cancel.gif"></a>
		<?}else{?>
			<font class=small1 color=888888><b>�������Ϸ�</b></font>
		<?}?>
	</td>
</tr>
<?
}
if($i < 1){?>
<tr height=25>
	<td align=center colspan=4><font class=small1 color=6d6d6d>�߱� ������ �����ϴ�.</td>
</tr>
<?}?>
</table>
<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>
<p>

<?
if($data[coupontype] == '0' || $_GET[sno] ) include "_form.couponapply.php";
?>
<div style="padding-top:15px"></div>
<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������ư�� Ŭ���ϸ� �ش� ȸ������ �߱޵������� ��ҵ˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>'�������Ϸ�'�� �ش� ȸ���� �̹� ������ ����Ͽ� �Ϸ���� �ǹ��մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>
<script>window.onload = function(){ UNM.inner();};</script>