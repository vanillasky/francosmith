<?

$scriptLoad='<script src="../mobileShop/codi/_codi.js"></script>';
include "../_header.popup.php";

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_goods_list_js";

?>


<form method="post" action="../mobileShop/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">��ǰ ����Ʈ ��ũ��Ʈ<span>��ǰ ����Ʈ�� ����Ǵ� �ڹٽ�ũ��Ʈ�Դϴ�.</span></div>

<?=gen_design_history_tag('skin_mobile', $cfg['tplSkinMobileWork'], '/common/js/goods_list_action.js'); ?>

<?
{ // �������ڵ���

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="��ǰ ����Ʈ ��ũ��Ʈ"';
	$tmp['tplFile']		= "/common/js/goods_list_action.js";

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
