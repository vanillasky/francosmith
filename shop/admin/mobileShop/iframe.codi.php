<?
if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ) $location = "�����ΰ��� > ��ü���̾ƿ� ������";
else if ( $_GET['design_file'] == 'main/index.htm' ) $location = "�����ΰ��� > ���������� ������";
else $location = "�����ΰ��� > ��Ÿ������ ������";

$scriptLoad='<script src="../mobileShop/codi/_codi.js"></script>';
include "../_header.popup.php";

?>

<? if ( $_GET['design_file'] == 'default' || $_GET['design_file'] == 'main/index.htm' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<? if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ){ ?>
	<div class="title title_top">��ü���̾ƿ� ����<span>�� ���θ��� ��ü���̾ƿ��� �����մϴ�</span></div>
	<? } else if ( $_GET['design_file'] == 'main/index.htm' ){ ?>
	<div class="title title_top">���������� ������<span>���������� �������� �����մϴ�</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=9')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<? } ?>
<? } else { ?>
<div class="title title_top">���������� ������<span>�������������� �������� �����մϴ�</span>  <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=10')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<? } ?>
<?
{ // Design Codi ����
	@include_once dirname(__FILE__) . "/codi/main.php";
}
?>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>