<?

@include "../../conf/partner.php";
include "../../lib/plusCheese.class.php";
@include "../../conf/config.plusCheeseCfg.php";

$location = "�÷���ġ�� > �÷���ġ�� �Ҽȼ��� �������";
include "../_header.php";

$requestVar = array(
	'code'=>'pluscheese_calculate',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
$plusCheese = new plusCheese($godo['sno']);
$statusCond = $plusCheese->getStatusCond();
if(empty($statusCond)){
	if($_GET['ref'] == "lm") msg("��û �� ����� �ֽñ� �ٶ��ϴ�.", "info.php");
}

?>

<div class="title title_top">�÷���ġ�� �Ҽȼ��� ������� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=30')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<iframe name='innaver' src='../proc/remote_godopage.php?<?=http_build_query($requestVar)?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>

<?include "../_footer.php"; ?>