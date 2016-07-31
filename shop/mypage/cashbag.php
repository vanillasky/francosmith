<?
include "../_header.php";
@require_once dirname(__FILE__)."/../conf/pg.cashbag.php";
$ordno = $_POST['ordno'];

$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno' and step='4' and step2='0'");
$res = $db->query("select * from ".GD_ORDER_ITEM." where ordno='$ordno'");

$cashbagprice = $minus = 0;
$r_exc = $r_kind = array();
if($cashbag['e_refer']) $r_exc = unserialize($cashbag['e_refer']);
if($cashbag['paykind']) $r_kind = unserialize($cashbag['paykind']);

while($data = $db->fetch($res)){
	$item[] = $data;
	if($data['istep'] < 40){
		$cashbagprice += $data['price'] * $data['ea'];
		if(in_array($data['goodsno'],$r_exc)) $minus += $data['price'] * $data['ea'];
	}
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
	$good_info .= "seq=".$i.chr(31);
	$good_info .= "ordr_numb=".$ordno.$i.chr(31);
	$good_info .= "good_name=".substr($v[goodsnm],0,30).chr(31);
	$good_info .= "good_cntx=".$v[ea].chr(31);
	$good_info .= "good_amtx=".$v[price].chr(30);
}
if($i > 1)$ordnm .= " ¿Ü".($i-1)."°Ç";

if( $pre['cbyn'] == 'N' && $pre[step] == '4' && $pre[step2] == '0' && in_array($pre[settlekind],$r_kind) ){

	$pg['quota'] = "0";
	$pg['id'] = $cashbag['code'];
	$pg['key'] = $cashbag['key'];
	$pg['zerofee'] = '';
	$pg['zerofee_period'] = '';
	$pg['receipt'] = 'Y';
	$pg['saveok'] = 'Y';

	$_POST['escrow'] = 'N';
	$_POST['nameReceiver'] = $pre['nameReceiver'];
	$_POST['phoneReceiver'] = @explode('-',$pre['phoneReceiver']);
	$_POST['mobileReceiver'] = @explode('-',$pre['mobileReceiver']);
	$_POST['zipcode'] = @explode('-',$pre['zipcode']);
	$_POST['address_sub'] = $pre['address_sub'];
	$_POST['email'] = $pre['email'];

	$arr['settlekind'] =  $pre['settlekind'];
	$arr['settleprice'] = $cashbagprice - $minus;
	$arr['nameOrder'] = $pre['nameOrder'];
	$arr['email'] = $pre['email'];
	$arr['phoneOrder'] = @explode('-',$pre['phoneOrder']);
	$arr['mobileOrder'] = @explode('-',$pre['mobileOrder']);
	$arr['ordno'] = $ordno;
	$pay_date =  str_replace('-','',substr($pre['cdt'],0,10));
	if(!$pay_date)$pay_date = date("Ymd");
	$arr['pay_date'] = $pay_date;
	$tpl = new Template_;
	$tpl -> template_dir = dirname(__FILE__)."/../data/skin/".$cfg[tplSkin]."/order/card";
	$tpl -> compile_dir	= dirname(__FILE__)."/../Template_/_compiles/$cfg[tplSkin]/order/card/kcp";
	$tpl -> define('tpl',"kcp.htm");
	$tpl -> assign($arr);
	$msg = $tpl->fetch('tpl');

	echo $msg;
	echo "<script>var fm=document.order_info; if(jsf__pay(fm))fm.submit();</script>";

}
?>