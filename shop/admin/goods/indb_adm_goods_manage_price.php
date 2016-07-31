<?
include "../lib.php";

### ���̹� ���ļ��� ��ǰ����
naver_goods_diff_check();
// ���� �����Ͽ� ��ǰ����
daum_goods_diff_check();
include("../../conf/config.pay.php");

//if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('query,chk', $_POST);

### ����Ÿ ��ȿ�� �˻�
if (in_array($_POST['dtprice'], array('price', 'consumer', 'supply')) === false) break;
if (is_numeric($_POST['pmnum']) === false || $_POST['pmnum'] < 0) break;
if (in_array($_POST['pmmark'], array('%', '��')) === false) break;
if (in_array($_POST['pmtype'], array('down', 'up')) === false) break;
if (in_array($_POST['tgprice'], array('price', 'consumer')) === false) break;
if (in_array($_POST['roundunit'], array(1, 10, 100, 1000)) === false) break;
if (in_array($_POST['roundtype'], array('down', 'halfup', 'up')) === false) break;

### ������ �籸��(�˻���� ��ü)
if ($_POST['isall'] == 'Y' && $_POST['query']){
	$_POST['chk'] = array();
	$res = $db->query(base64_decode($_POST['query']));
	while ($row=$db->fetch($res)) $_POST['chk'][] = $row['sno'];
}

### ���� �ϰ�����
$option = Clib_Application::getModelClass('goods_option');

foreach ($_POST['chk'] as $sno) {

	// ��ǰ
	$option->resetData();
	$option->load($sno);

	$price = $option[$_POST['dtprice']];
	$pmtype = ($_POST['pmtype'] == 'down' ? '-' : '+');
	if ($_POST['pmtype'] == 'down' && $_POST['pmmark'] == '��' && $price < $_POST['pmnum']) continue;

	if ($_POST['pmmark'] == '%') eval("\$price = {$price} * (1 {$pmtype} ({$_POST['pmnum']} / 100));");
	else eval("\$price = {$price} {$pmtype} {$_POST['pmnum']};");

	if ($_POST['roundtype'] == 'down') $price = floor($price / $_POST['roundunit']) * $_POST['roundunit'];
	else if ($_POST['roundtype'] == 'halfup') $price = round($price, -(strlen($_POST['roundunit']) - 1));
	else $price = ceil($price / $_POST['roundunit']) * $_POST['roundunit'];

	### ���̹� ���ļ��� ��ǰ���� �� ��ǰ ��ǥ ���� ����
	if ($option['link']==1) {

		// ��ǥ���� ����
		$option->goods->setData('goods_'.$_POST['tgprice'], $price);
		
		// ���� ��� EP
		$ar_update['price'] = $price;
		daum_goods_diff($option['goodsno'],$ar_update);
		$option->goods->save();
		
		// ���̹� ���ļ��� ��ǰ ����
		if($_POST['tgprice']=='price' && $option['price']!=$price)
		{
			$query = "
			SELECT sum( stock ) as stock , OPEN , runout, delivery_type, use_emoney, usestock, ( SELECT hidden FROM ".GD_GOODS_LINK." WHERE goodsno = b.goodsno LIMIT 1 )  AS hidden
			FROM ".GD_GOODS_OPTION." AS a
			LEFT JOIN ".GD_GOODS." AS b ON a.goodsno = b.goodsno
			WHERE b.goodsno = '{$option['goodsno']}' and go_is_deleted <> '1' and go_is_display = '1'
			GROUP BY a.goodsno
			";
			$sub_data = $db->fetch($query);

			$check=true;
			if($sub_data['runout']=='1' || $sub_data['OPEN']=='0' || $sub_data['hidden']=='1')
			{
				$check=false;
			}
			if($sub_data['usestock']=='o' && $sub_data['stock']==0)
			{
				$check=false;
			}
			if($check)
			{
				$ar_update=array();
				$ar_update['price']=$price;
				$ar_update['mapid']=$option[goodsno];
				$ar_update['class']="U";
				$ar_update['utime']=date("Y-m-d H:i:s");
				if($sub_data[delivery_type]=='0')
				{
					if($set['delivery']['free'] <= $price) $ar_update['deliv']=0;
					else $ar_update['deliv']=$set['delivery']['default'];
				}
				if($sub_data[use_emoney]=='0' && !$set['emoney']['chk_goods_emoney'] && $set['emoney']['goods_emoney'])
				{
					$ar_update['point'] = getDcprice($tmp_price,$set['emoney']['goods_emoney'].'%');
				}

				$ar_str=array();
				foreach($ar_update as $key=>$value)
				{
					$ar_str[]="$key = '$value'";
				}
				$query = "insert into ".GD_GOODS_UPDATE_NAVER." set ".implode(" , ",$ar_str);
				$db->query($query);
			}
		}

	}

	$option->setData($_POST['tgprice'], $price);
	$option->save();

	$goodsno[] = $option[goodsno];

}

if (is_array($goodsnos)){
	$goodsnos = array_unique($goodsnos);
}

### ������Ʈ �Ͻ�
$Goods = new Goods();
foreach($goodsnos as $v){
	$Goods -> update_date($v);
}

### ������ũ ����
if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
	$element = array();
	$element['returnUrl'] = $_POST[returnUrl];
	foreach($goodsnos as $k => $v) $element['goodsno['.$k.']'] = $v;
	goPost('../interpark/transmit_action.php', $element, 'parent');
}

echo '
<script>
alert("����Ǿ����ϴ�.");
parent.location.reload();
</script>
';
