<?

include "../lib.php";
include "../../lib/openmarket.class.php";

### �����Ѱ���
$oSend = new openmarketSend();
$out = $oSend->isExists();
if ($out[1] != 'true') msg($oSend->isExistsMsg, 0);


### ��ǰ����
$goodsno = $_POST['goodsno'];
if ($_POST['mode']=="register") $data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'");
else $data = $db->fetch("select * from ".GD_OPENMARKET_GOODS." where goodsno='$goodsno'");


### �ʼ������� ����
$vData = $_POST;
$img_m = explode("|",$data['img_m']);
foreach ($_FILES['img_m']['name'] as $k => $v)
{
	if ($_FILES['img_m']['name'][$k] && !isset($vData['img_m'])) $vData['img_m'] = $_FILES['img_m']['name'][$k];
	else if ($_POST['del']['img_m'][$k]);
	else if ($img_m[$k] && !isset($vData['img_m'])) $vData['img_m'] = $img_m[$k];
}
$needs = $oSend->verifyData($vData);
if (count($needs)){
	msg("������ ���� �ʼ������ʹ� �Է��ؾ� �մϴ�.\\n\\n--------------- üũ�׸� ---------------\\n- ". implode("\\n- ", $needs), 0);
}

### ���ʵ����� ����
if ($_POST['mode']=="register") $db->query("insert into ".GD_OPENMARKET_GOODS." set goodsno = '$goodsno', regdt = now()");
else $db->query("update ".GD_OPENMARKET_GOODS." set moddt = now() where goodsno = '$goodsno'");

### �ʼ��ɼ� ����
$optnm = @implode("|",array_notnull($_POST['optnm']));
$idx = -1;
$db->query("delete from ".GD_OPENMARKET_GOODS_OPTION." where goodsno=$goodsno");
$cnt = count($_POST['opt2']);
foreach ($_POST['option']['stock'] as $k=>$v){
	$idx++;
	$key = (int)($idx/$cnt);
	$opt1 = str_replace("'","��",$_POST['opt1'][$key]);
	$opt2 = str_replace("'","��",$_POST['opt2'][$idx%$cnt]);

	if(trim($opt1) == '�ɼǸ�1') $opt1='';
	if(trim($opt2) == '�ɼǸ�2') $opt2='';
	if(trim($v) == '���') $v='';

	$query = "
	insert into ".GD_OPENMARKET_GOODS_OPTION." set
		goodsno = '$goodsno',
		opt1	= '$opt1',
		opt2	= '$opt2',
		stock	= '$v'
	";
	$db->query($query);
}

### ������ǰ�� ���Ǵ� �̹��� ��������
$img_m = explode("|",$data['img_m']);
foreach ($_FILES['img_m']['name'] as $k => $v)
{
	if ($_POST['del']['img_m'][$k] && preg_match('/^openmarket_/', $img_m[$k]) == 0){
		$img_m[$k] = '';
		unset($_POST['del']['img_m'][$k]);
	}
}
$data['img_m'] = implode("|",$img_m);

### �̹��� ���ε� & ���ε��� �̹����� ����
multiUpload("img_m");
foreach ($file['img_m']['name'] as $k => $v){
	if ($file['img_m']['name'][$k] != $img_m[$k])
	{
		$oldnm = $file['img_m']['name'][$k];
		$file['img_m']['name'][$k] = 'openmarket_'. $oldnm;
		$_dir	= "../../data/goods/";
		@rename($_dir.$oldnm, $_dir.$file['img_m']['name'][$k]);
	}
}

### ��������
$_POST['goodsnm'] = trim($_POST['goodsnm']);

### ��ǰ ����Ÿ ����
$query = "
update ".GD_OPENMARKET_GOODS." set
	goodsnm			= '{$_POST['goodsnm']}',
	goodscd			= '{$_POST['goodscd']}',
	maker			= '{$_POST['maker']}',
	origin_kind		= '{$_POST['origin_kind']}',
	origin_name		= '{$_POST['origin_name']}',
	brandnm			= '{$_POST['brandnm']}',
	tax				= '{$_POST['tax']}',
	shortdesc		= '{$_POST['shortdesc']}',
	longdesc		= '{$_POST['longdesc']}',
	img_m			= '".@implode("|",$file[img_m][name])."',
	category		= '{$_POST['category']}',
	max_count		= '{$_POST['max_count']}',
	optnm			= '{$optnm}',
	price			= '{$_POST['price']}',
	consumer		= '{$_POST['consumer']}',
	runout			= '{$_POST['runout']}',
	usestock		= '{$_POST['usestock']}',
	age_flag		= '{$_POST['age_flag']}',
	noSameShipAS	= '{$_POST['noSameShipAS']}',
	as_info			= '{$_POST['as_info']}',
	ship_price		= '{$_POST['ship_price']}',
	ship_pay		= '{$_POST['ship_pay']}',
	ship_type		= '{$_POST['ship_type']}',
	ship_base		= '{$_POST['ship_base']}'
where
	goodsno = '$goodsno'
";
$db->query($query);

### ������ ����
ob_start();
$res = $oSend->putGoods($goodsno, $_POST['mode']);
ob_end_clean();

### �����뷮 ���
setDu('goods');

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl], "top");

?>