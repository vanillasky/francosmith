<?
if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ) $location = "�����ΰ��� > ��ü���̾ƿ� ������";
else $location = "�����ΰ��� > ��Ÿ������ ������";

$scriptLoad='<script src="../todayshop/codi/_codi.js"></script>';
include "../_header.popup.php";
?>
<? if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<? if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<div class="title title_top">��ü���̾ƿ� ����<span>�� ���θ��� ��ü���̾ƿ��� �����մϴ�</span></div>
	<? } ?>
<? } else { ?>
<div class="title title_top">���������� ������<span>�������������� �������� �����մϴ�</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? } ?>
<?=$workSkinTodayStr?>
<?
	// ���̾ƿ� ���� �˸� �̹���
	$todayshop = & load_class('todayshop','todayshop');
	if ($todayshop->cfg['shopMode'] != "todayshop") {
?>
	<img src="../img/todayshop/bn_ly02.gif" style="margin-top:5px; margin-bottom:10px;" />
<?
	} //
{ // Design Codi ����
	include_once dirname(__FILE__) . "/codi/main.php";
}
?>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>