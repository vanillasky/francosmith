<?
$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";
?>

<form name="fm" method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="intro_save" />
<input type=hidden name=tplSkinWork value="<?=$cfg['tplSkinWork']?>">

<div class="title title_top">��Ʈ��(ȸ��) ������ ������<span>ȸ������ ��Ʈ�� �������� �������� �����մϴ�.</span></div>

<?=$workSkinStr?>


<!--<div style="margin:10px 0 10px 0;"><font class=extext>������ �������� ������ '<a href="/shop/main/intro.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�</u></b></font></a>' �� Ŭ���ϼ���.</div>
<div style="margin:10px 0 10px 0;"><font class=extext>������������ ������ '<a href="/shop/main/index.php" target="_blank"><font class=ver7 color="#0074BA"><b><u>http://�����θ�/shop/main/index.php</u></b></font></a>' �� Ŭ���ϼ���.</div>-->

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], 'main/intro_member.htm'); ?>

<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="HTML �ҽ�"';
	$tmp['tplFile']		= "/main/intro_member.htm";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>
<input type="hidden" name="skin_file" value="<?=$tmp['tplFile']?>"/>
<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class=small_ex>
<tr><td>�������� ��Ʈ�� �������� ������ ���� 2������ �����˴ϴ�.</td></tr>
<tr><td>�� ���� ������ ������ ���� �Ǵ� ȸ���� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ���� �Ǵ� ȸ���� ������ ������ ����Ʈ�� ���˴ϴ�. ������ ������ �� �ִ� ����Ȯ�� �������񽺸� ��û�ϰ� �̿��Ͽ� �ּ���.</td></tr>
<tr><td>�� ���� ������ ������ ȸ���� ���� ������ ��Ʈ�� ������</td></tr>
<tr><td>&nbsp;- ȸ���� ������ ������ ����Ʈ�� ���Ǹ�, ��ǰ ���Ŵ� ȸ���� �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<script>
table_design_load();
setHeight_ifrmCodi();
</script>
