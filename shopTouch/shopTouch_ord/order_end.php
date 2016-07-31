<?

include dirname(__FILE__) . "/../_shopTouch_header.php";
@include $shopRootDir . "/conf/config.pay.php";
@include $shopRootDir . "/conf/egg.usafe.php";
@include $shopRootDir . "/conf/merchant.php";

### 장바구니 비우기
if ($_COOKIE[gd_isDirect]) setcookie("gd_isDirect",'',time() - 3600,'/');
else setcookie("gd_cart",'',time() - 3600,'/');

$query = "
select * from
	".GD_ORDER." a
	left join ".GD_LIST_BANK." b on a.bankAccount=b.sno
where
	a.ordno='$_GET[ordno]'
";
$data = $db->fetch($query,1);

### 현금영수증신청내역
if ($data['settlekind'] == 'a' && $set['receipt']['order'] == 'Y')
{
	$query = "select useopt from ".GD_CASHRECEIPT." where ordno='{$_GET['ordno']}' order by crno limit 1";
	list($data['cashreceipt_useopt']) = $db->fetch($query);
}

$tpl->assign($data);
$tpl->print_('tpl');
?>