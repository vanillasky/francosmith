<?
set_time_limit(0);
include "../lib.php";
@include "../../conf/config.pay.php";
@include "../../conf/orderXls.php";

header("Content-Type: application/vnd.ms-excel; charset=euc-kr");
header("Content-Disposition: attachment; filename=GDorder_".$_POST[mode]."_".date("YmdHi").".xls");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
header("Pragma: public");

// 주문상태 (다중 선택이 가능하므로 OR 연산)
$_SQL['WHERE']['OR'] = array();
if ($_POST['step']){
	$_SQL['WHERE']['OR'][] = "
			(step IN (".implode(",",$_POST['step']).") AND step2 = '')
			";
	foreach ($_POST['step'] as $v) $checked['step'][$v] = "checked";
}

if ($_POST['step2']) {
	foreach ($_POST['step2'] as $v) {
		switch ($v){
			case "1": $_SQL['WHERE']['OR'][] = "(O.step=0 and O.step2 between 1 and 49)"; break;
			case "2": $_SQL['WHERE']['OR'][] = "(O.step in (1,2) and O.step2!=0) OR (O.cyn='r' and O.step2='44' and O.dyn!='e')"; break;
			case "3": $_SQL['WHERE']['OR'][] = "(O.step in (3,4) and O.step2!=0)"; break;
			case "60" :
				$_SQL['WHERE']['OR'][] = "(OI.dyn='e' and OI.cyn='e')";
			break; //교환완료
			case "61" : $_SQL['WHERE']['OR'][] = "oldordno != ''";break; //재주문
			default:
				$_SQL['WHERE']['OR'][] = "O.step2=$v";
			break;
		}
		$checked['step2'][$v] = "checked";
	}
}

if (!empty($_SQL['WHERE']['OR'])) $_SQL['WHERE'][] = "(".implode(" OR ",$_SQL['WHERE']['OR']).")";
unset($_SQL['WHERE']['OR']);

// 주문 정보
if ($_POST['mode']=='goods'){
	if(!$orderTodayGoodsXls) $orderXls = $default['orderTodayGoodsXls'];
	else $orderXls = getdefault('orderTodayGoodsXls');
}
else {
	if(!$orderTodayCouponXls) $orderXls = $default['orderTodayCouponXls'];
	else $orderXls = getdefault('orderTodayCouponXls');
}

if (is_array($orderXls) && empty($orderXls) === false) {
	foreach($orderXls as $key=>$value) {
		if ($value[3] == '') unset($orderXls[$key]);
	}
}

$query = "SELECT 
				O.*, MB.m_id,
				G.goodsnm, 
				OI.ea, OI.istep,
				CP.cp_num, CP.cp_publish, 
				O.deliverycode,
				TG.startdt, TG.enddt
			FROM ".GD_ORDER." AS O
			INNER JOIN ".GD_ORDER_ITEM." AS OI
			ON OI.ordno = O.ordno
			INNER JOIN ".GD_GOODS." AS G
			ON G.goodsno = OI.goodsno AND G.todaygoods = 'y'
			INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
			ON G.goodsno = TG.goodsno
			LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS CP
			ON O.ordno = CP.ordno
			LEFT JOIN ".GD_MEMBER." AS MB
			ON O.m_no=MB.m_no
			WHERE
				G.goodsno = '".$_POST['goodsno']."'";

if (empty($_SQL['WHERE'])===false) {
	$query .= ' AND '.implode(' AND ', $_SQL['WHERE']);
}

$res = $db->query($query);
?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<table border=1>
<tr bgcolor=#f7f7f7>
<?
	if (is_array($orderXls) && empty($orderXls) === false) {
		foreach($orderXls as $k => $v)	echo("<th>$v[0]</th>");
	}
?>
</tr>
<? while ($data=$db->fetch($res)){ ?>
<tr>
	<?
	//$data['no'] = $data['opt'] = $data['sprice'] = $data['deliveryno'] = $data['deliverycode'] = "";
	if(!$data['dvno']) $data['dvno'] = "";
	$data['no'] = ++$idx;
	if ($data['opt1']) $data['opt'] .= "[".$data[opt1];
	if ($data['opt2']) $data['opt'] .= "/".$data[opt2];
	if ($data['opt']) $data['opt'] .= "]";
	if ($data['addopt']) $data['opt'] .= "<div>[".str_replace("^","] [",$data['addopt'])."]</div>";
	$data['settlekind'] = $r_settlekind[$data['settlekind']];
	$data['step'] = getStepMsg($data['step'],$data['step2'],$data['ordno']);
	if(strlen($data['step']) > 10) $data['step'] = substr($data['step'],10);
	$data['deliveryno'] = $data['dvno'];
	$data['deliverycode'] = $data['dvcode'];
	$data['sprice'] = $data['prn_settleprice'];
	if($data['deli_msg']) $data['deli_type'] = $data['deli_msg'];
	$data['deli_type'] = str_replace('후불','착불',$data['deli_type']);
	$data['usedt'] = $data['usestartdt'].'~'.$data['useenddt'];
	$data['order_memo'] = $data['memo'];

	if (is_array($orderXls) && empty($orderXls) === false) {
		foreach($orderXls as $k => $v)  echo("<td>".strip_tags($data[$v[1]])."</td>");
	}
	?>
</tr>
<? } ?>
</table>