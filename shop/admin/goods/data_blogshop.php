<?
$location = "��ǰ���� > ��α׼� ��ǰ���� �ϰ�����";
include "../_header.php";
$blogshop = new blogshop();
$result=array();
if($blogshop->linked) {
	$result = $blogshop->get_inip2p_goods();

}

?>
<script src="../../lib/js/categoryBox.js"></script>
<script type="text/javascript">
function confirmBox() {
	
	var ar_chk=document.getElementsByName('chk[]');
	var is_checked=false;
	for(i=0;i<ar_chk.length;i++) {
		if(ar_chk[i].checked) {
			is_checked=true;
		}
	}
	if(!is_checked) {
		alert("��ȯ��ų ��ǰ�� ������ �ּ���");
	}
	
	var is_selected=false;
	var ar_cate=document.getElementsByName('cate[]');
	for(i=0;i<ar_cate.length;i++) {
		if(ar_cate[i].selectedIndex) {
			is_selected=true;
		}
	}
	
	if(!is_selected) {
		alert("���θ��� ī�װ��� �������ּ���");
	}

	if(is_selected && is_checked) {
		return true;
	}
	else {
		return false;
	}
}
</script>

<form name="fmList" method="post" onsubmit="return confirmBox()" action="data_blogshop.process.php">

<div class="title title_top">��α׼� ��ǰ���� �ϰ�����<span>�̴�P2P�� ������ ��α׼� ��ǰ�� ���θ� ��ǰ���� ��ȯ��ŵ�ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<br>

<div style="padding:8px 13px;backg��ound:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;color:#777777;" id="goodsInfoBox">
<div><font color="#EA0095"><b>�ʵ�! ��α׼� ��ǰ �̵��� ���ؼ�</b></font></div>
<div style="padding-top:2">�� ��α׼��� �̿��Ͻôٰ� ���θ��� �߰��Ͻ� ��� ��α׼��� �ִ� INIP2P������ǰ�� ���θ�������ǰ���� ��ȯ�ϴ� ����Դϴ�.</div>
<div style="padding-top:2">�� ��ȯ�� ���Ŀ��� ��α׼��� �ִ� ��ǰ���Ź�ư Ŭ���� INIP2P�� �ƴ� ���θ��� �̵��˴ϴ�.</div>
<div style="padding-top:2">�� ...</div>

</div>

<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���θ� ��ǰ���� ��ȯ �� ��α׼� ��ǰ�� �����ϼ���</b></font></div>
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width=70><col width=70><col><col width=150>
<tr><td class=rnd colspan=5></td></tr>
<tr height=35 bgcolor=4a3f38>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>����</b></a></th>
	<th><font class=small1 color=white><b>��ȣ</b></th>
	<th><font class=small1 color=white><b>��ǰ��</b></th>
	<th><font class=small1 color=white><b>����</b></th>
</tr>
<tr><td class=rnd colspan=5></td></tr>

<? foreach ($result as $k=>$v) :?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type='checkbox' name="chk[]" value="<?=$v['goodsno']?>"></td>
	<td align=center><font class="ver8" color="#616161"><?=($k+1)?></td>
	<td><?=$v['goodsnm']?></td>
	<td align=center><?=number_format($v['price'])?>��</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? endforeach; ?>
</table>

<br><br>
<div style="padding:10 0 5 5"><font class="def1" color="#EA0095"><b><font size="3">��</font> ���� �� ���θ� ī�װ��� ���� �մϴ�</b></font></div>

<div style="padding:5px;border:1px solid #cccccc">
	<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
</div>




<div style="text-align:center">
<input type="submit" value="����" style="width:150px;height:40px;padding:15px;margin:20px">
</div>
</form>

<? include "../_footer.php"; ?>