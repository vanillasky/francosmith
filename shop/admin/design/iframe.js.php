<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_js";

?>


<form method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">자바스크립트관리<span>모든 페이지에 공통적용되는 자바스크립트입니다</span></div>

<?=$workSkinStr?>

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], '/common.js'); ?>

<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="자바스크립트"';
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">주요한 페이지의 자바스크립트 소스를 관리합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">자바스크립트에 대한 지식이 있어야만 수정이 가능합니다. 신중히 수정하세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>
