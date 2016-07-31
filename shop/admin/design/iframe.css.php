<?

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

if ( !$_GET['mode'] ) $_GET['mode'] = "mod_css";

?>


<form method="post" action="../design/indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">스타일시트관리<span>모든 페이지에 공통적용되는 스타일시트입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=3')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<?=$workSkinStr?>

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], '/style.css'); ?>

<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '99%';
	$tmp['t_rows']		= 20;
	$tmp['t_property']	= ' required label="스타일시트"';
	$tmp['tplFile']		= "/style.css";

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>



<div style="padding:20px" align="center">
<input type="image" src="../img/btn_register.gif" class="null" />
</div>

</form>



<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">폰트스타일, 링크스타일 등 다양한 스타일이 기본적으로 셋팅되어 있습니다.</td></tr></table>
</div>
<script>cssRound('MSG01');</script>



<script>
table_design_load();
setHeight_ifrmCodi();
</script>
