<?
$location = "��ǰ���� > ���������� ����";
include "../_header.php";
@include "../../conf/config.display.php";

if (!$displayCfg['displayType']) $displayCfg['displayType'] = 'consumer';

?>
<div class="title title_top">���ΰ��� ���⼳��<span> ���� �������� ����� ���� ������ �����մϴ�. </span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=49')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<form name="frm" method="post" action="indb.disp_config.php">
<table class=tb>
<col class=cellC>
<tr>
	<th rowspan=2>���ΰ��� ���⼳��</th>
	<td style="border-right-width: 0px; border-bottom-width: 0px; width:300px;">
		<div class="noline"><input type="radio" name="displayType" value="consumer" <?=$displayCfg['displayType'] === 'consumer' ? 'checked' : ''?> onclick="display_image()"/>�Һ��ڰ���(��Ҽ�)�� �ǸŰ������� ǥ��</div>
		<div class="noline"><input type="radio" name="displayType" value="discount" <?=$displayCfg['displayType'] === 'discount' ? 'checked' : ''?> onclick="display_image()"/>�ǸŰ���(��Ҽ�)�� ��ǰ�� ���ΰ������� ǥ��</div>
	</td>
	<td style="border-left-width: 0px; border-bottom-width: 0px;">
	<img name="displayImage">
	</td>
</tr>
<tr><td colspan=2 style="border-top-width: 0px;"><span class=small id="ext" style="display:none;"><font class=extext>�� �з������� > ����/���� ���ݼ� ��ǰ���� �ÿ��� �ǸŰ��� �������� ���ĵ˴ϴ�.</font></span></td></tr>
</table>
<div style="margin-top:15px"><span class=small><font class=extext><font style="color:red;"><b>��2015�� 12�� 23�� ���� ���� ���� ��Ų</b></font>�� ����Ͻô� ��� �ݵ�� ��Ų��ġ�� �����ؾ� ��� ����� �����մϴ�.</font></span><a href="http://www.godo.co.kr/customer_center/patch.php?sno=2287" class="extext" style="font-weight:bold"> [��ġ �ٷΰ���]</a></div>
<div class="button">
<input type="image" src="../img/btn_save.gif" />
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<script type="text/javascript">
window.onload = function(){
	display_image();
}

function display_image() {
	var f = document.frm;
	if (f.displayType[0].checked) {
		f.displayImage.src = "../img/consumer_display.png";
		document.getElementById('ext').style.display = "none";
	}
	else if (f.displayType[1].checked) {
		f.displayImage.src = "../img/discount_display.png";
		document.getElementById('ext').style.display = "block";
	}
}

</script>

<? include "../_footer.php"; ?>