<?
$location = "���� > ���� ȯ�漳��";
include "../_header.php";

### ����� ���� / ���� ����
@include "../../conf/config.selly.php";
@include "../selly/code.php";

// ����
$checked['delivery_type'][$selly['set']['delivery_type']] = "checked";
$selected['origin'][$selly['set']['origin']] = " selected";

// �������� Ȯ��
list($selly['cust_cd']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");
list($selly['cust_seq']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($selly['domain']) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'domain'");
?>

<div class="title title_top">���� ȯ�漳�� <span>SELLY�� ��ǰ ������ ����, �� ���θ��� �⺻������ �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=2')"><img src="../img/btn_q.gif" align="absmiddle" /></a></div>

<form method="post" action="../selly/indb.php">
<input type="hidden" name="mode" value="set">

<? if(!$selly['cust_cd'] or !$selly['cust_seq']) { ?>
<div style="width:550px; border:3px #DCE1E1 solid; padding:10px; margin:10px 0px;">
<strong>�� ������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.</strong> <a href="../selly/index.php" class="extext" style="font-weight:bold;">[���� ��û�Ϸ� ����]</a>
</div>
<? } ?>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="35">
	<td>���� ����</td>
	<td><a href="../selly/indb.php?mode=idshop"><img src="../img/btn_apply_shop.gif" align="absbottom" /></a> &nbsp;<span class="extext" ><? if(!$selly['cust_cd'] || !$selly['cust_seq'] || !$selly['domain']) { ?>* ������ �����ϼ̴��� ���� ������ ���ǵǸ� �ٽ� ��������� ��û�� �ֽñ� �ٶ��ϴ�.<? } else { ?>���� Ȯ�� �Ǽ̽��ϴ�.<? } ?></span></td>
</tr>
<tr height="35">
	<td>��ǰ ī�װ�</td>
	<td>
		<a href="../selly/indb.php?mode=category"><img src="../img/btn_apply_cate.gif" align="absbottom" /></a>
		&nbsp;<span class="extext">* ��ǰ ī�װ��� ������� ������ ��ǰ DATA ���۽� ������ �߻��մϴ�.</span>
	</td>
</tr>
<tr height="35">
	<td>��ۺ� ��å</td>
	<td>
		<? foreach($selly['delivery_type'] as $k => $v) { ?>
		<input type="radio" name="delivery_type" value="<?=$k?>" class="null" <?=$checked['delivery_type'][$k]?> /> <?=$v?>
		<? } ?>
		<span style="margin-left:20px;">��ۺ� : </span><input type="text" name="delivery_price" value="<?=$selly['set']['delivery_price']?>" class="line" style="width:80px;" /> ��
	</td>
</tr>
<tr height="35">
	<td>������</td>
	<td>
		<select name="origin" id="origin">
			<option value="">= ������ =</option>
			<? foreach($selly['origin'] as $k => $v) { ?>
			<option value="<?=$k?>"<?=$selected['origin'][$k]?>><?=$v?></option>
			<? } ?>
		</select>
	</td>
</tr>
<tr height="35">
	<td>�����ּ�</td>
	<td>
		http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?> <a href="javascript:;" onclick="window.clipboardData.setData('Text', 'http://<?=$_SERVER['HTTP_HOST'].$cfg['rootDir']?>');alert('�����ּҰ� ����Ǿ����ϴ�.\n\n    Ctrl + V �� ����� �ٿ��ֱ� ���ּ���.\n\n�� �������� ������� ������ ���� ������� �ʽ��ϴ�.');"><img src="../img/btn_catelink_copy.gif" align="absmiddle" /></a>
		&nbsp;&nbsp;&nbsp; <span class="extext">* �����ּҴ� SELLY���� ���θ� ��Ͻ� ���Ǵ� �ּ��Դϴ�.</span>
	</td>
</tr>
</table>

<div style="height:20px"></div>

<table cellpadding="0" cellspacing="0" width="650">
<tr>
	<td align="center"><input type="image" src="../img/btn_regist.gif" class="null"></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<b>�� ���� ����/���</b><br />
SELLY�� ��ǰ DATA ������ ���� SELLY�� ���� �� ������ �޾ƾ� �����մϴ�.<br />
�� ������ ������ ���� �ʿ� ������û�� ���� ��ǰ DATA ���۽� �ʿ��� ���� ������ �����ڵ�� ����Ű�� �޾� �����Ϳ� �����ϵ��� �ϴ� ����� �մϴ�.<br />
������ ���� �ʾ� ���������� ������ ī�װ� �� ��ǰ DATA�� ������ �� �����ϴ�.<br />
<br /><br />
<b>�� ��ǰ ī�װ� ���</b><br />
������ ī�װ� ������ SELLY�� �����մϴ�. ������ ī�װ� ������ �����Ǹ� ī�װ� ����� �ٽ� �ؾ� �ϸ�, ī�װ��� ������� ���� ��� ��ǰ DATA ���۽� ������ �߻��մϴ�.<br />
��ǰ ī�װ��� �׻� ���� �ֽ��� ī�װ��� ��ϵǾ�� �մϴ�.<br />
<br /><br />
<b>�� ��ۺ� ��å</b><br />
e������ ���� SELLY���� ��ۺ� ��å�� �����մϴ�. �⺻���� ��ۺ� ��å�� �����Ͽ� SELLY�� ����ϸ�, �ǸŻ�ǰ ����ϱ⿡�� �� ��ǰ�� ��ۺ� ��å ������ �����մϴ�.<br />
<br /><br />
<b>�� ������</b><br />
������ ������ SELLY���� ��ǰ�ǸŽ� ���Ǵ� �����ڵ��Դϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>
<? include "../_footer.php"; ?>