<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_js";

?>


<form method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">�ڹٽ�ũ��Ʈ����<span>��� �������� ��������Ǵ� �ڹٽ�ũ��Ʈ�Դϴ�</span></div>

<?=$workSkinStr?>

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], '/common.js'); ?>

<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="�ڹٽ�ũ��Ʈ"';
	$tmp['tplFile']		= "/common.js";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>



<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>



<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֿ��� �������� �ڹٽ�ũ��Ʈ �ҽ��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ڹٽ�ũ��Ʈ�� ���� ������ �־�߸� ������ �����մϴ�. ������ �����ϼ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>
