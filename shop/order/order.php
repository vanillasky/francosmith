<?
include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
@include "../conf/pg.$cfg[settlePg].php";
include "../conf/pg.escrow.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();
@include "../conf/auctionIpay.pg.cfg.php";

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY => 'text',
	));
}

// getordno �Լ��� /shop/lib/lib.func.php ���Ϸ� �̵�

// ������8 Metro IE�� ���������� ���ӽ� Desktop IE�� ��ȯ ���� �޽��� ���
if (isset($pg) === true) {
	header( 'X-UA-Compatible: requiresActiveX=true');
}

### okĳ�������
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] ) $set['use']['p'] = "on";

### �ܺ� ������ ���(�����мǼ�ȣ) ����ũ�� �ڵ�����
if ($_COOKIE[cc_inflow] == 'yahoo_fss'){
	$escrow['use'] = 'Y';
	$escrow['min'] = '0';
}

### ȸ������ ��������
if ($sess){
	$query = "
	select * from
		".GD_MEMBER." a
		left join ".GD_MEMBER_GRP." b on a.level=b.level
	where
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
	$style_member = "readonly style='border:0'";
}

### ��ٱ��� ��Ű ����
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = Core::loader('Cart',$isDirect);
$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');

if ($_REQUEST[preview]=='y' && $_REQUEST[cody]!='y' ) {	//�̸������˾� ������ǰó��
	chkOpenYn($_POST[goodsno],"D",'parentClose');
		echo "
			<script>
				parent.frmView.preview.value='n';
				parent.act('../order/order','opener');
			</script>
		";
		exit;
}

if(is_array($_POST[goodsno])){	//���ڿ��� �Ѿ�� ��Ʈ��ǰ
	chkOpenYn($_POST[goodsno],"D",-2);	//�������� üũ
	for($i=0;$i<sizeof($_POST[goodsno]);$i++){
		if($_POST[goodsno][$i]){
			$cart->addCart($_POST[goodsno][$i],array_notnull($_POST[opt][$i]),$_POST[addopt][$i],$_POST[addopt_inputable][$i],$_POST[ea][$i],$_POST[goodsCoupon][$i]);
		}
	}
}
else{
	if ($_POST[mode]=="addItem"){
		chkOpenYn($_POST[goodsno],"D",-1);	//�������� üũ
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_ea = $_POST[multi_ea][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

				$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
			}
		}
		else {
			$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
		}
	}
}

if (!isset($_POST[idxs])) $_POST[idxs] = $_COOKIE['_posted_idxs'] ? unserialize( get_magic_quotes_gpc() ? stripslashes($_COOKIE['_posted_idxs']) : $_COOKIE['_posted_idxs']) : 'all';
//�������� ��ǰ û�ҳ⺸ȣ�� ���� (���������� ���� ���¿��� �������� ��ǰ �ֹ��� �������� ����)
if($_POST[idxs] == 'all'){
	if ($cart->item) {
		foreach ($cart->item as $v){
			if ($v['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
				msg("���������� �ʿ��� ��ǰ/������ �� ���ԵǾ� �ֽ��ϴ�.\\n\\n����(����) ���� �� �ֹ��ϱ⸦ ������ �ּ���.","../main/intro_adult.php?returnUrl=../goods/goods_cart.php");
				exit;
			}
		}
	}
} else {
	if (is_array($_POST[idxs]) && !empty($_POST[idxs])) foreach($_POST[idxs] as $idx) {
		$item = $cart->item[$idx];
		if ($item['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			msg("���������� �ʿ��� ��ǰ/������ �� ���ԵǾ� �ֽ��ϴ�.\\n\\n����(����) ���� �� �ֹ��ϱ⸦ ������ �ּ���.","../main/intro_adult.php?returnUrl=../goods/goods_cart.php");
			exit;
		}
	}
}
$cart->setOrder($_POST[idxs]);	// $_POST[idxs] �� , �� ���е� 0 �̻��� ���� �Ǵ� 'all'

if($_POST['mode'] != 'addItem' && is_array($_POST['goodsno']) == false){	//��ٱ��� �ֹ��ϱ⿡�� ��������,�Ǹ����� üũ
	chkOpenYn($cart,"D",-1);
}

if($member){
	$cart->excep = $member['excep'];
	$cart->excate = $member['excate'];
	$cart->dc = $member[dc]."%";
}
$cart -> coupon = $_POST['coupon'];
$cart -> coupon_emoney = $_POST['coupon_emoney'];
$cart->calcu();
$orderitem_rowspan = get_items_rowspan($cart->item);

### s1��Ų���� ���� �⺻ ��ۺ� ��������
$param = array(
	'mode' => '0',
	'zipcode' => $member[zipcode],
	'emoney' => 0,
	'deliPoli' => 0,
	'coupon' => 0,
	'road_address' => $member['road_address'],
	'address' => $member['address'],
	'address_sub' => $member['address_sub'],
);

$delivery = getDeliveryMode($param);
$cart -> delivery = $delivery['price'];
$cart -> totalprice += $delivery['price'];

### �ܿ� ��� üũ........2007-07-18 modify
if ($cart->item) {
	foreach ($cart->item as $v){
		$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	}
}

### ��ȸ���� ��� �α���â���� �̵�
if ($_GET[guest]) setCookie('guest',1,0,'/');
if (!$sess && !$_GET[guest] && !$_COOKIE[guest]){
	setCookie('_posted_idxs', serialize($_POST[idxs]) ,0,'/');
	go("../member/login.php?guest=1&returnUrl=$_SERVER[PHP_SELF]");
}
else {
	setCookie('_posted_idxs', false , time() - 86400 ,'/');
}

### �ֹ���ȣ ����
$ordno = getordno();

$set['emoney']['base'] = pow(10,$set['emoney']['cut']);

### ������ ������
if(!$set['emoney']['emoney_use_range'])$tmp = $cart->goodsprice;
else $tmp = $cart->totalprice;
$tmp = $tmp - getDcPrice($cart->goodsprice,$cart->dc) - getDcPrice($cart->goodsprice,$cart->special_discount_amount);
$emoney_max = getDcprice($tmp,$set[emoney][max])+0;

$r_deli = explode('|',$set['r_delivery']['title']);

if ($member){
	$member[zipcode] = explode("-",$member[zipcode]);
	$member[phone] = explode("-",$member[phone]);
	$member[mobile] = explode("-",$member[mobile]);
	$tpl->assign($member);
}

### ���½�Ÿ�� ��� ����
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

### ��ٿ� ����
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

### ���̹� ���ϸ���
$load_config_ncash = $config->load('ncash');
$naverNcash = Core::loader('naverNcash');
if(!$naverNcash->realyn()){ $load_config_ncash['useyn'] = "N"; $naverNcash->useyn = "N"; }
if($load_config_ncash['api_id'] == '' || $load_config_ncash['api_key'] == '') $load_config_ncash['useyn'] = "N";
if($load_config_ncash['useyn'] == 'Y'){

	$exceptionYN = "";

	$naverNcash = Core::loader('naverNcash');

	if($naverNcash->useyn == 'Y'){
		$tpl->assign('ncash_yn','Y');
		if($naverNcash->baseAccumRate)
		{
			$load_config_ncash['N_ba'] = preg_replace('/\.0$/', '', $naverNcash->get_base_accum_rate());
			$load_config_ncash['N_aa'] = preg_replace('/\.0$/', '', $naverNcash->get_add_accum_rate());
		}
	}
	else {
		$load_config_ncash['useyn'] = 'N';
	}

	### ���ܻ�ǰ üũ
	$exceptionYN = $naverNcash->exception_goods($cart->item);
	$load_config_ncash['exception_price'] = $naverNcash->exception_price($cart->item);

	if( $exceptionYN == "N" ){
		$load_config_ncash['doneUrl'] = urlencode("http://".$_SERVER['HTTP_HOST']."/shop/proc/naverNcash_bridge.php");
		$tpl->assign('ncash',$load_config_ncash);
	}

	foreach($cart->item as $k => $v){
		$exception_goods = $exception_category = "N";

		// ���ܻ�ǰ üũ
		if(@in_array($v['goodsno'],$naverNcash->e_exceptions))	$exception_goods = "Y";

		// ����ī�װ� üũ
		$res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$v['goodsno']);
		while ($data=$db->fetch($res)){
			if(@in_array($data['category'],$naverNcash->e_category))	$exception_category = "Y";
		}

		// ���� ���� �� ����
		if(!($exception_goods == 'N' && $exception_category == 'N')){
			$cart->item[$k]['exceptionYN'] = 'Y';
		}else{
			$cart->item[$k]['exceptionYN'] = 'N';
		}
	}
}

// �������� �������
if(isset($auctionIpayPgCfg))
{
	if($auctionIpayPgCfg['testYn']==='y')
	{
		if(isset($sess) && (int)$sess['level']===100) $useIpayPg = true;
		else $useIpayPg = false;
	}
	else
	{
		if($auctionIpayPgCfg['useYn']==='y') $useIpayPg = true;
		else $useIpayPg = false;
	}
}

//������
if(is_file('../lib/payco.class.php')){
	$Payco = Core::loader('payco')->getButtonHtmlCode('EASYPAY', false, '');
	if($Payco) $tpl->assign('Payco', $Payco);
}

$tpl->assign('useIpayPg', $useIpayPg);

$tpl->assign('orderitem_rowspan',$orderitem_rowspan);
$tpl->assign('cart',$cart);
$tpl->assign('ordno',$ordno);

### �ֹ�ó��url
if($cfg['ssl_type'] == "free") { //����
	$tpl->assign('orderActionUrl',$sitelink->link('order/settle.php','regular'));
} else { //���� Ȥ�� ���ȼ����Ⱦ�
	$tpl->assign('orderActionUrl',$sitelink->link('order/settle.php','ssl'));
}

//nscreen �����϶�, �������� ���� ���� ó�� 2013-10-31 dn
@include '../conf/config.nscreenPayment.php';
$nScreenPayment = Core::Loader('nScreenPayment');

if($config_nscreen_payment['use'] && ($nScreenPayment->getScreenType() == 'MOBILE' && $cfg[settlePg] != 'lgdacom')) {
	$set['use']['o'] = ''; //������ü
	$set['use']['u'] = ''; //�߱�ī��
	$set['use']['y'] = ''; //��������
	$escrow['use'] = 'N';  //����ũ��
	$tpl->assign('useIpayPg', false); //��������
}

// ������� ���񽺰� Ȱ��ȭ �Ǿ����� �� ����� ���� �߰� ����
if ($cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled()) {
	$set['use']['h'] = 'on';
}
// �ٳ� ���񽺰� Ȱ��ȭ �Ǿ����� �� ����� ���� �߰� ����
else if ($cfg['settleCellPg'] === 'danal' && $danal->isEnabled()) {
	$set['use']['h'] = 'on';
}

$termsPolicyCollection3 = getTermsGuideContents('terms', 'termsPolicyCollection3');
$tpl->assign('termsPolicyCollection3', $termsPolicyCollection3);

$tpl->print_('tpl');

?>
