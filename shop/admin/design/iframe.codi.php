<?
if ( $_GET['design_file'] == 'default' || substr( $_GET['design_file'], 0, 8 ) == 'outline/' ) $location = "�����ΰ��� > ��ü���̾ƿ� ������";
else if ( $_GET['design_file'] == 'main/index.htm' ) $location = "�����ΰ��� > ���������� ������";
else $location = "�����ΰ��� > ��Ÿ������ ������";

$scriptLoad='<script src="../design/codi/_codi.js"></script>';
include "../_header.popup.php";

## ����������޾�� ����
if($_GET[design_file] == "service/_private.txt" && !file_exists("../../data/skin/".$cfg['tplSkinWork']."/service/_private.txt")) $_GET[design_file] = "service/private.htm";
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
<?=$workSkinStr?>

<?php
	// ���/�������� ���� �ȳ�
	$termsTxtFileArray = array(
		'service/_private.txt'		, 
		'service/_private1.txt'		, 
		'service/_private2.txt'		, 
		'service/_private3.txt'		, 
		'service/_private_non.txt'	,
		'proc/_agreement.txt'
	);
	
	if(in_array($_GET[design_file], $termsTxtFileArray)){
?>
	<table cellpadding="0" cellspacing="0" border="0" style="border: 2px solid #dddddd;" width="100%">
	<tr>
		<td style="font-size:15px; padding: 5px 5px 15px 5px; color: #0080FF; font-weight: bold;">* ���ο� �̿���, ����������޹�ħ �� ���� ��� �ȳ�</td>
	</tr>
	<tr>
		<td style="padding: 5px 5px 5px 5px;">�̿���, ����������޹�ħ �� ���θ� � ��å�� ���õ� �ȳ� ������ �����ϰ� ����� �� �ִ� ����� �����Ǿ����ϴ�. ����� ���� �����ΰ����� �� �������� HTML ���·� ������ �Է��Ͽ� ����Ͽ��� ���������� ���ο� ����� �̿��ϸ� ���� ��Ų ��ü�� ������� �Էµ� ������ �״�� ����� �� �����Ƿ� ������ ���ο� ����� �̿��� �ֽñ� �ٶ��ϴ�. <a href="javascript:;" onclick="javascript:parent.document.location.href='../basic/terms.php';" style="color: #0080FF;"><u>[�⺻���� > ���/�������� ���� �ٷΰ���]</u></a>
		</td>
	</tr>
	<tr>
		<td style="padding: 5px 5px 5px 5px;">�� <span style="color: red; font-weight: bold;">2014�� 07�� 31�� ���� ���� ���� ��Ų</span>�� ����Ͻô� ��� <span style="font-weight: bold; text-decoration: underline;">�ݵ�� ��Ų��ġ</span>�� �����ؾ� ��� ����� �����մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2064" target="_blank" style="color: #0080FF;"><u>[��ġ �ٷΰ���]</u></a></td>
	</tr>
	</table>
<?php
	}
?>

<?
	// ���̾ƿ� ���� �˸� �̹���
	$todayshop = Core::loader('todayshop');
	if ($todayshop->cfg['shopMode'] == "todayshop") {
?>
	<img src="../img/todayshop/bn_ly01.gif" style="margin-top:5px; margin-bottom:10px;" />
<?
	} //
{ // Design Codi ����
	@include_once dirname(__FILE__) . "/codi/main.php";
}
?>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>
