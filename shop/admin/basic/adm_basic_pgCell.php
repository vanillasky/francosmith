<?php

$location = "�������� > �޴��� ���� ����";
include "../_header.php";

$config = Core::loader('config');

@include dirname(__FILE__).'/../../conf/config.mobileShop.php';
$shopConfig = $config->load('config');
$paymentConfig = $config->load('configpay');

if (!$shopConfig['settleCellPg']) $shopConfig['settleCellPg'] = 'mobilians';

$containsError = array();
if ($paymentConfig['use']['h'] === 'on') {
	$containsError[] = 'E0001';
}
if ($paymentConfig['use_mobile']['h'] === 'on') {
	if ($cfgMobileShop['mobileShopRootDir'] !== '/m2') $containsError[] = 'E0002';
	else $containsError[] = 'E0003';
}

?>

<script type="text/javascript">
function chgifrm(src, key)
{
	var pgTab = document.getElementById("pgtab"), pgIfrm = document.getElementById("pgifrm");
	if (pgIfrm) {
		pgIfrm.src = src;
		for (var index = 0; index < pgTab.cells.length; index++) {
			if (index == key) {
				pgTab.cells[index].style.background = "#627dce";
				pgTab.cells[index].style.color = "#ffffff";
			}
			else {
				pgTab.cells[index].style.background = "#ffffff";
				pgTab.cells[index].style.color = "#627dce";
			}
		}
	}
}
</script>

<style type="text/css">
#pgtab td{
	cursor: pointer; font-weight: bold;
}
</style>

<div class="title title_top">
�޴��� ���� ���� <img src="../img/btn_q.gif" border="0" align="absmiddle" style="cursor: pointer;" onclick="manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=37');"/>
</div>

<?php if (count($containsError) > 0) { ?>
<div class="red" style="margin-bottom: 8px;">
	�� ���ڰ����� �޴��� ������ ������ ���¿����� ����� �� �����ϴ�.
	<span style="color: #000000; cursor: pointer;" onclick="popup('http://guide.godo.co.kr/guide/php/ex_cell_pg.html', 850, 680);"><img src="<?php echo $shopConfig['rootDir']; ?>/admin/img/btn_help_cell.gif"/></span><br/>
	�޴��� �������񽺸� �̿��Ͻ÷��� ����
	<?php if (in_array('E0001', $containsError)) { ?>
	<a href="<?php echo $shopConfig['rootDir']; ?>/admin/basic/pg.php">[���� ���ڰ��� ����]</a><?php echo count($containsError) > 1 ? ',' : ''; ?>
	<?php } ?>
	<?php if (in_array('E0002', $containsError)) { ?>
	<a href="<?php echo $shopConfig['rootDir']; ?>/admin/mobileShop/mobile_pg.php">[����ϼ� ���ڰ��� ����]</a>
	<?php } ?>
	<?php if (in_array('E0003', $containsError)) { ?>
	<a href="<?php echo $shopConfig['rootDir']; ?>/admin/mobileShop2/mobile_pg.php">[����ϼ� ���ڰ��� ����]</a>
	<?php } ?>
	���������� �޴��� ������ ������� �ʵ��� �����Ͽ��ֽñ� �ٶ��ϴ�.
</div>
<?php } else { ?>
<table border="5" bordercolor="#627dce" style="border-collapse:collapse" width="100%">
	<tr>
		<td colspan="10" align="center" style="padding: 10px 0px 10px 12px; color: #627dce">����Ͻ� �޴��� ���� ���񽺻� �� ���� Ŭ���� �� �޴��� ���� ���� ������ �Է��ϼ���.</td>
	</tr>
	<tr align="center" height="40" id="pgtab">
		<td width="665" onclick="chgifrm('adm_basic_pgCell.mobilians.php', 0);">�������</td>
		<td width="665" onclick="chgifrm('adm_basic_pgCell.danal.php', 1);">�ٳ�</td>
	</tr>
</table>

<div style="padding-top: 20px"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<iframe id="pgifrm" width="100%" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="10" scrolling="no"></iframe>
		</td>
	</tr>
</table>
<script type="text/javascript">chgifrm("adm_basic_pgCell.<?php echo $shopConfig['settleCellPg']; ?>.php", 0);</script>
<?php
	if($shopConfig['settleCellPg']){
		switch ($shopConfig['settleCellPg']){
			
			case "mobilians" :
				echo("<script>chgifrm('adm_basic_pgCell.mobilians.php',0);</script>");
			break;
		
			case "danal" :
				echo("<script>chgifrm('adm_basic_pgCell.danal.php',1);</script>");
			break;
		}
	}
}
include "../_footer.php";
?>