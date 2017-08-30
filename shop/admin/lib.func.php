<?
//ȸ�� ��ü where��
define("MEMBER_DEFAULT_WHERE", "dormant_regDate='0000-00-00 00:00:00'");

$combine_menu = array(
	'basic' => array('basic'),
	'design' => array('design'),
	'goods' => array('goods'),
	'order' => array('order'),
	'member' => array('member', 'dormant'),
	'board' => array('board'),
	'promotion' => array('event','sns','qrcode'),
	'marketing' => array('marketing','payco','naver','naverCheckout','naverNcash','daumcpc','natebasket','keyword','criteo','advertise','interpark','auctionIpay','nateClipping','engine'),
	'log' => array('acecounter','log','data'),
	'mobileShop' => array('mobileShop'),
	'shoptouch' => array('shoptouch'),
	'blog' => array('blog'),
	'todayshop' => array('todayshop'),
	'shople' => array('shople'),
	'selly' => array('selly'),
	'overseas' => array('overseas'),
	'mobileShop2' => array('mobileShop2'),
);

if ($cfg['om_id'] == '' && !preg_match("/\/open\//", $_SERVER['PHP_SELF'])){
	$combine_menu['marketing'] = array_ereg( '/[^(open)]/', $combine_menu['marketing'] );
}

if (!$cfg[img_c]) $cfg[img_c] = 70;
if (!$cfg[img_i]) $cfg[img_i] = 100;
if (!$cfg[img_s]) $cfg[img_s] = 130;
if (!$cfg[img_m]) $cfg[img_m] = 300;
if (!$cfg[img_l]) $cfg[img_l] = 500;
if (!$cfg[img_mobile]) $cfg[img_mobile] = 74;
// ������̹��� ȯ�漳�� ����Ʈ ������ ������ ��ġ��
// �����ϰ� �����ؾ� �����۵� (/shop/lib/Clib/Controller/Admin/Clib_Controller_Admin_Goods.php::444)
if (!$cfg[img_w]) $cfg[img_w] = 200;
if (!$cfg[img_x]) $cfg[img_x] = 200;
if (!$cfg[img_y]) $cfg[img_y] = 300;
if (!$cfg[img_z]) $cfg[img_z] = 500;

### �Ŵ��� URL
$guideUrl = 'http://guide.godo.co.kr/season4/';

function popupReload()
{
	echo "<script>parent.location.reload();</script>";
	exit;
}

function sortTop(){ return time(); }
function multiUpload($key, $detailView = 'n')
{
	global $file, $data, $cfg;
	static $now = null;

	if ($now === null) $now = time();

	if(!$cfg){
		include "../../conf/config.php";
	}

	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	if ($data[$key]) $file[$key][name] = explode("|",$data[$key]);

	$keys = array_keys($_FILES[$key]['tmp_name']);

	for ($i=0,$m=sizeof($keys);$i<$m;$i++){
		$kk = $keys[$i];

		if ($_POST[del][$key][$kk]){
			@unlink($_dir.$file[$key][name][$kk]);
			@unlink($_dirT.$file[$key][name][$kk]);
			if ($key=='img_m') @unlink($_dirT.str_replace('.', '_sc.', $file[$key][name][$kk]));
			$file[$key][name][$kk] = "";
		}

		if (is_uploaded_file($_FILES[$key][tmp_name][$kk])){

			$_ext = array_pop(explode(".",$_FILES[$key][name][$kk]));
			$_key = substr($key,-1,1).$i;
			$_rnd = mt_rand(0,999);

			while (is_file($_dir.$now.$_rnd.$_key.".".$_ext)) {
				$now++;
				$_rnd = mt_rand(0,999);
			}

			$file[$key][name][$kk] = $now.$_rnd.$_key.".".$_ext;

			if (move_uploaded_file($_FILES[$key][tmp_name][$kk],$_dir.$file[$key][name][$kk])) {
				@chmod($_dir.$file[$key][name][$kk],0707); // ���ε�� ���� ���� ����

				if ($key!="img_s" && $key!="img_i" && $key!="img_x" && $key!="img_w" && $key!="img_c" && $key!="opticon_a" && $key!="opticon_b" && $key!="img_mobile") thumbnail($_dir.$file[$key][name][$kk],$_dirT.$file[$key][name][$kk],45);
				if ($key=='img_m' && $detailView=='y') {
					thumbnail($_dir.$file[$key][name][$kk],$_dirT.str_replace('.', '_sc.', $file[$key][name][$kk]),$cfg['img_m'], 0, 0, 80);
				}
			}
		}
	}
	$file[$key][name] = array_notnull($file[$key][name]);
	return $file[$key][name];
}

function copyImg($key)
{
	global $file,$cfg;
	static $now = null;

	if ($now === null) $now = time();

	if(!$cfg){
		include "../../conf/config.php";
	}

	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$len = ($key=="img_i" || $key=="img_s" || $key=="img_mobile") ? 1 : count($file[img_l][name]);
	if($key == "opt1img" || $key == "opticon_a" || $key == "opticon_b")$len = count($file[$key][name]);

	### ���� �̹��� ���� ����
	if ($key != "img_l" && $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b"){
		for ($i=0;$i<count($file[$key][name]);$i++){
			@unlink($_dir.$file[$key][name][$i]);
			@unlink($_dirT.$file[$key][name][$i]);
			$file[$key][name][$i] = "";
		}
	}
	$file[$key][name] = array_notnull($file[$key][name]);

	for ($i=0;$i<$len;$i++){
		if( $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b") $src = $file[img_l][name][$i];
		else{
			 $src = $file[$key]['name'][$i];
			if($key == "opt1img")  $cfg[$key] = $cfg['img_l'];
			else $cfg[$key] = 40;
		}

		if($src && !preg_match('/^http(s)?:\/\//',$src)) {

			$_ext = array_pop(explode(".",$src));
			$_key = substr($key,-1,1).$i;
			$_rnd = mt_rand(0,999);

			while (is_file($_dir.$now.$_rnd.$_key.".".$_ext)) {
				$now++;
				$_rnd = mt_rand(0,999);
			}

			$file[$key][name][$i] = $now.$_rnd.$_key.".".$_ext;
			thumbnail($_dir.$src,$_dir.$file[$key][name][$i],$cfg[$key]);
			if ($key!="img_i" && $key!="img_s" && $key!="opticon_a" && $key!="opticon_b" && $key!="img_mobile") copy($_dirT.$src,$_dirT.$file[$key][name][$i]);

		}
	}
}

function copyMobileImg($key)
{
	global $file,$cfg;
	static $now = null;

	if ($now === null) $now = time();

	if(!$cfg){
		include "../../conf/config.php";
	}

	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$len = ($key=="img_w" || $key=="img_x") ? 1 : count($file[img_z][name]);
	if($key == "opt1img" || $key == "opticon_a" || $key == "opticon_b")$len = count($file[$key][name]);

	### ���� �̹��� ���� ����
	if ($key != "img_z" && $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b"){
		for ($i=0;$i<count($file[$key][name]);$i++){
			@unlink($_dir.$file[$key][name][$i]);
			@unlink($_dirT.$file[$key][name][$i]);
			$file[$key][name][$i] = "";
		}
	}
	$file[$key][name] = array_notnull($file[$key][name]);

	for ($i=0;$i<$len;$i++){
		if( $key != "opt1img" && $key != "opticon_a" && $key != "opticon_b") $src = $file[img_z][name][$i];
		else{
			 $src = $file[$key]['name'][$i];
			if($key == "opt1img")  $cfg[$key] = $cfg['img_z'];
			else $cfg[$key] = 40;
		}

		if($src && !preg_match('/^http(s)?:\/\//',$src)) {

			$_ext = array_pop(explode(".",$src));
			$_key = substr($key,-1,1).$i;
			$_rnd = mt_rand(0,999);

			while (is_file($_dir.$now.$_rnd.$_key.".".$_ext)) {
				$now++;
				$_rnd = mt_rand(0,999);
			}

			$file[$key][name][$i] = $now.$_rnd.$_key.".".$_ext;
			thumbnail($_dir.$src,$_dir.$file[$key][name][$i],$cfg[$key]);
			if ($key!="img_w" && $key!="img_x" && $key!="opticon_a" && $key!="opticon_b") copy($_dirT.$src,$_dirT.$file[$key][name][$i]);

		}
	}
}

function chkAdmin(){
	global $db,$sess,$rAuth,$combine_menu;
	$data = $db->fetch("select * from ".GD_MEMBER." where m_id='$sess[m_id]' and m_no='$sess[m_no]' and level >= '80' limit 1");
	if(!$data[m_no]) msg('�������� ������� �����ڿ� �α����Ͽ� �ּ���!!',-1);
	if(!$rAuth)	@include dirname(__FILE__)."/../conf/groupAuth.php";
	$arr = $rAuth[$data[level]]; $res = (empty($arr) && $data[level] < 100) ? false : true;

	$except = array(
		'basic/index.php',
		'basic/adm_basic_index.php',
		'basic/adm_basic_widget_service_execute.php',
		'basic/main.state.php',
		'proc/indb.php',
		'proc/popup_zipcode.php',
		'proc/popup_zipcode.custom.php',
		'webftp/3DBar_conf.php',
		'proc/popup.autoCancel.php',
		'proc/popup.goodsChoice.php',
		'proc/_ajaxGoodsChoiceList.php',
		'proc/_goodsChoiceList.php'
	);
	$extra = array(
		array('goods' => 'board/goods_qna_register.php'),
		array('goods' => 'board/goods_qna_indb.php'),
		array('goods' => 'board/goods_review_register.php'),
		array('goods' => 'board/goods_review_indb.php'),
		array('goods' => 'data/data_goodsxls.php'),
		array('goods' => 'data/data_goodscsv.php'),
		array('goods' => 'data/data_goodscsv_indb.php'),
		array('goods' => 'data/data_goodsxls_indb.php'),
		array('order' => 'data/popup.orderxls.php'),
		array('order' => 'data/indb.php'),
		array('order' => 'board/member_qna_register.php'),
		array('order' => 'board/member_qna_indb.php'),
		array('member' => 'proc/indb.email.php'),
		array('board' => 'order/checkout.view.php'),
	);

	if($data[level] != 100 && count($arr)>=1){
		$tmp = explode('/',$_SERVER[PHP_SELF]);
		$section = $tmp[count($tmp)-2];
		$link = $tmp[count($tmp)-1];

		$t_arr = $arr;
		$keys = array_keys($combine_menu);
		foreach ($t_arr as $dirnm){
			if (in_array($dirnm, $keys)) {
				$mergeCombineMenu = array();
				$mergeCombineMenu = $combine_menu[$dirnm];

				//dormant �� ���� �����̹Ƿ� ����
				if($dirnm == 'member'){
					foreach($mergeCombineMenu as $key => $name){
						if($name == 'dormant') unset($mergeCombineMenu[$key]);
					}
				}
				$arr = array_merge($arr, $mergeCombineMenu);
			}
		}

		if(in_array('design',$arr))$arr[] = 'webftp';
		if(in_array('design',$arr))$arr[] = 'codi';
		if(in_array('todayshop',$arr))$arr[] = "proc";
		if(in_array('selly',$arr))$arr[] = "proc";
		if(in_array('shople',$arr))$arr[] = "proc";
		if(in_array('hiebay',$arr))$arr[] = "proc";
		if(in_array('event',$arr)) {
			$arr[] = "sns";	$arr[] = "qrcode";
			$arr[] = "proc";
		}
		if(in_array('mobileShop', $arr)) {
			$arr[] = 'mobileShop2';
		}
		if(!in_array($section,$arr))$res = false;



		if(in_array($section.'/'.$link,$except))$res = true;

		foreach($extra as $tmp)
			foreach($tmp as $k => $v)
				if($section.'/'.$link  == $v && in_array($k,$arr))$res = true;

		// ������ �߰������� ��� �н�
		if ($section === 'ExtraFile') $res = true;
	}

	if(!$res) msg('���� ������ �����ϴ�.',-1);
}

function getSmsPoint(){
	if(file_exists(dirname(__FILE__)."/../conf/sms.cfg.php")){
		$file = file(dirname(__FILE__)."/../conf/sms.cfg.php");
		$sms = trim($file[1]);
	}else{
		@include_once dirname(__FILE__)."/../lib/sms.class.php";
		$smscl = new Sms();
		$smscl -> update();
		$sms = $smscl -> smsPt;
	}
	return (int)$sms;
}

function getlinkPc080($phone,$mode='phone'){
	global $set,$cfg;
	$ablePc080 = false;

	if($mode == 'phone')$img="<img src='../img/icon_phon.gif'>";
	else $img="<img src='../img/icon_mobile.gif'>";


	if($set['phone']['pc080_id'] && $set['phone']['user_id'] && $set['phone']['coop_id'])$ablePc080 = true;
	$phone = str_replace('-','',$phone);
	if($ablePc080) echo("&nbsp;<A HREF=\"pc080:".$phone."\" onClick =\"return check_obj();\">".$img."</A>");
}

function getjskPc080(){
	global $set,$cfg;
	if($set['phone']['pc080_id'] && $set['phone']['user_id'] && $set['phone']['coop_id']){
		echo("<script language=\"javascript\" src=\"".$cfg[rootDir]."/partner/pc080/pc080Check.js\"></script>");
	}
}

$r_sms_category = array(
				'�ֹ�����',
				'��۰���',
				'ȸ������',
				'�Ż�ǰ�ҽ�',
				'�̺�Ʈ�ҽ�',
				'��������',
				'���λ�',
				'��������',
				'�����',
				'��Ÿ����',
				'��������',
				'ũ��������',
				'��Ÿ'
				);

$r_sms_chr = array(
	'��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
	'��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
	'��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��',
	'��','��','��','��','��','��','��','��','��','��','��','��','?','��','��'
);

### ������ũ ȯ������ ###
@include_once dirname(__FILE__) . "/../conf/interpark.php";

### ���½�Ÿ�� ȯ������ ###
@include_once dirname(__FILE__) . "/../conf/interparkOpenStyle.php";

### ������ üũ ###
if($sess) chkAdmin();

// ������ IP �������� ó��
$IPAccessRestriction	= Core::loader('IPAccessRestriction');
$IPAccessRestriction->setAdminAccessIP();

### ���� ���� üũ
if(file_exists(dirname(__FILE__).'/../_godoConn/versionConvert.php')){
	$tmp	= @file(dirname(__FILE__).'/../_godoConn/versionConvert.php');
	$tmp1	= explode(" -> Season2 Convert. ",$tmp[0]);
	$godo['convertVer']		= $tmp1[0];		// Rental , Free , Self
	$godo['convertDate']	= $tmp1[1];
	unset($tmp);
	unset($tmp1);
}

### �ֹ������׸�������
function getdefault($mode){
	global $default,${$mode};
	if(!${$mode}) ${$mode} = array();
	foreach($default[$mode] as $v){
		$res = false;
		foreach(${$mode} as $v1){
			if($v[1] == $v1[1]){
				$res = true;
			}
		}
		if(!$res) ${$mode}[] = $v;
	}
	return ${$mode};
}

### ���ڼ��ݰ�꼭����Ʈ ###
$file = dirname(__FILE__).'/../conf/tax.cfg.php';
if ((isset($godo['tax']) === true && file_exists($file) === false) || (isset($godo['tax']) === false && file_exists($file) === true))
{
	@include_once dirname(__FILE__)."/../lib/tax.class.php";
	$etax = new eTax();
}
unset($file);

function naver_goods_diff($goodsno,$ar_update,$class="U") {
	include("../../conf/config.pay.php");
	include("../../conf/config.php");

	global $db;

	$allupdate=false;

	$ar_result = array();
	$ar_goods_field=array('goodsnm','img_l','brandno','origin','maker','delivery_type','goods_delivery','use_emoney','usestock','runout','open','naver_event');
	$ar_opt_field=array('price','reserve');

	// �űԶ�� ������Ʈ�� ���� �ǹ̰�����
	if($class=="I") {
		$allupdate=true;
		$ar_update=array();
		$ar_result['pgurl']="http://".$_SERVER['HTTP_HOST'].$cfg[rootDir]."/goods/goods_view.php?goodsno=$goodsno&inflow=naver";
	}

	// ���������͸� ��� ���Ѵ�.
	$query = "select ".implode(",",$ar_goods_field)." from ".GD_GOODS." where goodsno='$goodsno'";
	$ar_goods = $db->fetch($query);

	$query = "select ".implode(",",$ar_opt_field)." from ".GD_GOODS_OPTION." where goodsno='$goodsno' and link=1 and go_is_deleted <> '1' and go_is_display = '1'";
	$ar_opt = $db->fetch($query);

	$query = "select sum(stock) from ".GD_GOODS_OPTION." where goodsno='$goodsno' and go_is_deleted <> '1' and go_is_display = '1'";
	list($ar_goods['stock']) = $db->fetch($query);

	$query = "select category,hidden from ".GD_GOODS_LINK." where goodsno='$goodsno' order by length(category) DESC, sno ASC limit 1";
	list($ar_goods['category'],$ar_goods['hidden']) = $db->fetch($query);

	// ���� ǰ�� ���� üũ
	$before_stock=true;
	if($ar_goods['runout']=='1' || $ar_goods['open']=='0' || $ar_goods['hidden']=='1')
	{
		$before_stock=false;
	}
	if($ar_goods['usestock']=='o' && $ar_goods['stock']==0)
	{
		$before_stock=false;
	}

	// ������Ʈ �� ǰ�� ���� üũ
	$tmp_runout = (isset($ar_update['runout']) ? $ar_update['runout'] : $ar_goods['runout']);
	$tmp_open = (isset($ar_update['open']) ? $ar_update['open'] : $ar_goods['open']);
	$tmp_hidden = (isset($ar_update['hidden']) ? $ar_update['hidden'] : $ar_goods['hidden']);
	$tmp_usestock = (isset($ar_update['usestock']) ? $ar_update['usestock'] : $ar_goods['usestock']);
	$tmp_stock = (isset($ar_update['stock']) ? $ar_update['stock'] : $ar_goods['stock']);

	if($tmp_usestock == 'on') $tmp_usestock = 'o';

	$after_stock=true;
	if($tmp_runout=='1' || $tmp_open=='0' || $tmp_hidden=='1')
	{
		$after_stock=false;
	}
	if($tmp_usestock=='o' && $tmp_stock==0)
	{
		$after_stock=false;
	}

	// �ű԰� �ƴ� ������Ʈ ��Ȳ������ ����ǰ�����¿� ������Ʈ�� ǰ�����¿� ���� ���������Ǵ�
	if($class!="I") {
		// ǰ���̾��µ� ��� ����ٸ� ��� �����͸� ������Ʈ
		if($before_stock==false && $after_stock==true)
		{
			$allupdate=true;
		}

		// ��� �־��µ� ǰ���� �Ǹ� ǰ����������� ����
		if($before_stock==true && $after_stock==false)
		{
			naver_goods_runout($goodsno);
			return;
		}
	}

	// ��絥���Ͱ� ������Ʈ �ȴٸ� ������Ʈ�� �����Ϳ� ���������͸� ���ľ� �Ѵ�
	if($allupdate)
	{
		foreach($ar_goods as $key=>$value)
		{
			if(!isset($ar_update[$key])) $ar_update[$key]=$value;
		}
		foreach($ar_opt as $key=>$value)
		{
			if(!isset($ar_update[$key])) $ar_update[$key]=$value;
		}

	}


	// ���������Ϳ� ������Ʈ �� �����͸� ���� naver���ʵ带 �����.

	if($allupdate || (isset($ar_update['goodsnm']) && $ar_update['goodsnm']!=$ar_goods['goodsnm'])) {
		$ar_result['pname']=strip_tags($ar_update['goodsnm']);
	}

	if ($allupdate || (isset($ar_update['img_l']) && $ar_update['img_l'] != $ar_goods['img_l'])) {
		if (preg_match('/^http(s)?:\/\//', $ar_update['img_l'])) {
			$ar_result['igurl'] = $ar_update['img_l'];
		}
		else {
			$ar_result['igurl'] = 'http://'.$cfg['shopUrl'].$cfg['rootDir'].'/data/goods/'.$ar_update['img_l'];
		}
	}

	if($allupdate || (isset($ar_update['brandno']) && $ar_update['brandno']!=$ar_goods['brandno'])) {
		list($ar_result['brand'])=$db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$ar_update['brandno']}'");
		$ar_result['brand'] = strip_tags($ar_result['brand']);
	}

	if($allupdate || (isset($ar_update['origin']) && $ar_update['origin']!=$ar_goods['origin'])) {
		$ar_result['origi']=strip_tags($ar_update['origin']);
	}

	if($allupdate || (isset($ar_update['maker']) && $ar_update['maker']!=$ar_goods['maker'])) {
		$ar_result['maker']=strip_tags($ar_update['maker']);
	}

	if($allupdate || (isset($ar_update['goodscd']) && $ar_update['goodscd']!=$ar_goods['goodscd'])) {
		$ar_result['model']=strip_tags($ar_update['goodscd']);
	}

	if($allupdate || (isset($ar_update['naver_event']) && $ar_update['naver_event']!=$ar_goods['naver_event'])) {
		$ar_result['event']=strip_tags($ar_update['naver_event']);
	}

	if(
		$allupdate
		||
		(
			(isset($ar_update['delivery_type']) && $ar_update['delivery_type']!==$ar_goods['delivery_type'])
			||
			(isset($ar_update['goods_delivery']) && $ar_update['goods_delivery']!==$ar_goods['goods_delivery'])
			||
			(isset($ar_update['price']) && $ar_update['price']!=$ar_opt['price'])
		)
	) {


		if($allupdate || (isset($ar_update['price']) && $ar_update['price']!=$ar_opt['price']))
		{
			$ar_result['price']=$ar_update['price'];
		}
		/*
			[0] : �⺻ ��� ��å�� ����
			[1] : ������
			[2] : ��ǰ�� ��ۺ� (���̻� ������� ����)
			[4] : ���� ��ۺ�
			[5] : ������ ��ۺ�
			[3] : ���� ��ۺ�
		*/
		switch($ar_update['delivery_type']) {
			case "0":
				$tmp_price =  (isset($ar_update['price']) ? $ar_update['price'] : $ar_opt['price']);
				if($set['delivery']['free'] <= $tmp_price) $ar_result['deliv']=0;
				else $ar_result['deliv']=$set['delivery']['default'];
				break;
			case "1":
				$ar_result['deliv']=0;
				break;
			case "4":	// ���� ��ۺ�
			case "5":	// ������ ��ۺ�
			case "2":
				$ar_result['deliv'] = (isset($ar_update['goods_delivery']) ? $ar_update['goods_delivery'] : $ar_goods['goods_delivery']);
				break;
			case "3":
				$ar_result['deliv'] = -1;
				break;
		}
	}

	if(
		$allupdate
		||
		(
			(isset($ar_update['reserve']) && $ar_update['reserve']!=$ar_opt['reserve'])
			||
			(isset($ar_update['use_emoney']) && $ar_update['use_emoney']!=$ar_goods['use_emoney'])
			||
			(isset($ar_update['price']) && $ar_update['price']!=$ar_opt['price'])
		)
	) {
		if($ar_goods['use_emoney']=='0')
		{
			$tmp_price =  (isset($ar_update['price']) ? $ar_update['price'] : $ar_opt['price']);
			if( !$set['emoney']['chk_goods_emoney'] ){
				if( $set['emoney']['goods_emoney'] ) $ar_result['point'] = getDcprice($tmp_price,$set['emoney']['goods_emoney'].'%');
			}else{
				$ar_result['point']	= $set['emoney']['goods_emoney'];
			}
		}
		else
		{
			$ar_result['point']=$ar_update['reserve'];
		}
	}

	if($ar_update['category'])
	{
		for($i=1;$i<=4;$i++)
		{
			$tmp_nm="";
			$tmp_code = substr($ar_update['category'],0,3*$i);
			if(strlen($tmp_code)==$i*3)
			{
				list($tmp_nm) = $db->fetch("select catnm from ".GD_CATEGORY." where category='$tmp_code'");

				$ar_result['caid'.$i]=strip_tags($tmp_code);
				$ar_result['cate'.$i]=strip_tags($tmp_nm);
			}
			else
			{
				$ar_result['caid'.$i]="";
				$ar_result['cate'.$i]="";
			}
		}
	}

	// �����Ͱ� �ִٸ� �����͸� �ִ´�

	if(count($ar_result))
	{
		$ar_result['class']=$class;
		$ar_result['mapid']=$goodsno;
		$ar_result['utime']=date("Y-m-d H:i:s", G_CONST_NOW);
		$ar_str=array();
		foreach($ar_result as $key=>$value)
		{
			$ar_str[]="$key = '$value'";
		}
		$query = "insert into ".GD_GOODS_UPDATE_NAVER." set ".implode(" , ",$ar_str);
		$db->query($query);
	}

	// �ű��ε� ǰ���̸� ǰ�������� �� ������
	if($class=="I" && $before_stock==false)
	{
		naver_goods_runout($goodsno);
	}
}

// Form Helper �Լ�
function frmSelected($var1,$var2) {
	if($var1==$var2) return 'selected';
}
function frmChecked($var1,$var2) {
	if($var1==$var2) return 'checked';
}

function getPurePhoneNumber($number) {

	$number = preg_replace('/[^0-9\-]/','',$number);	// ����,-(������) �� ����

	return (preg_match('/^([0-9]{3,4})-?([0-9]{3,4})-?([0-9]{4})$/',$number)) ? $number : '';	// ��ȭ��ȣ�� �ƴ϶�� ���ڿ� ����.

}
/*
	�� ���� ��ǰ ������ ����
*/
function fixRelationGoods($goodsno) {

	global $db;

	$_sort = 0;

	$query = "SELECT relation FROM ".GD_GOODS." WHERE goodsno = '".$goodsno."'";
	list($_relation) = $db->fetch($query);

	if ($_relation == 'new_type') {
		// ���� ����.

	}
	else if (!empty($_relation)) {

		$query = "select goodsno from ".GD_GOODS." where goodsno in ($_relation)";
		$res = $db->query($query);
		$arr_relation=array();
		while ($rel=$db->fetch($res)) $arr_relation[] = $rel;
		$arr  = explode(',',$_relation);
		foreach($arr as $k2 => $v2) foreach($arr_relation as $k => $v)if($v2 == $v[goodsno]) {
			$query = "
			INSERT INTO ".GD_GOODS_RELATED." SET
				goodsno		= '".$goodsno."',
				sort		= '".$_sort."',
				r_type		= 'single',
				r_goodsno		= '".$v['goodsno']."',
				r_start		= NULL,
				r_end		= NULL,
				regdt		= NOW()
			";
			$db->query($query);
			$_sort++;
		}
		$db->query("UPDATE ".GD_GOODS." SET relation = 'new_type' WHERE goodsno = '".$goodsno."'");
	}
	else {
		list($_cnt) = $db->fetch("SELECT COUNT(goodsno) FROM ".GD_GOODS_RELATED." WHERE goodsno = '".$goodsno."'");
		if ($_cnt > 0) $db->query("UPDATE ".GD_GOODS." SET relation = 'new_type' WHERE goodsno = '".$goodsno."'");
		else return false;
	}

	return true;
}


/*
 * 2011-11-25 by x.ta.c
 * ������ �޴��� ���ٱ����� ����.
 */
function getPermission($section='') {

	static $_rAuth = null;
	global $sess;

	if ((int)$sess['level'] < 80 || $section == '') return false;
	elseif((int)$sess['level'] >= 100) return true;

	if ($_rAuth === null) {

		if (isset($GLOBALS['rAuth'])) $_rAuth = $GLOBALS['rAuth'];
		else {
			@include dirname(__FILE__)."/../conf/groupAuth.php";
			$_rAuth = $rAuth;
		}

		settype($_rAuth,'array');
	}

	if (!isset($_rAuth[$sess['level']])) return true;
	elseif (!in_array($section, $_rAuth[$sess['level']])) return false;

	return true;

}

/*
 * ���ڵ� (arr)�� �� Ű�� ���� total Ű�� �ջ��Ͽ� ����
 */
function get_total($total=array(),$arr = array()) {
	foreach ($arr as $k => $v) {
		if (!is_numeric($v)) continue;
		if (isset($total[$k])) $total[$k] = $total[$k] + $v;
		else $total[$k] = $v;
	}
	return $total;
}

/*
 * �� ��¥���� �ϼ��� ����Ͽ� ���� (mysql ����� �����ؾ� ��)
 */
function checkStatisticsDateRange($d1=null, $d2=null) {

	if (!is_object($GLOBALS['db'])) return false;

	$d1 = Core::helper('Date')->min($d1);
	$d2 = Core::helper('Date')->max($d2);

	if (preg_match('/^[1-9]{1}[0-9]{3}(.)?[0-1]{1}[0-9]{1}(.)?[0-3]{1}[0-9]{1}((.)?[0-2]{1}[0-9]{1}(.)?[0-5]{1}[0-9]{1}(.)?[0-5]{1}[0-9]{1})?$/',$d1) &&
		preg_match('/^[1-9]{1}[0-9]{3}(.)?[0-1]{1}[0-9]{1}(.)?[0-3]{1}[0-9]{1}((.)?[0-2]{1}[0-9]{1}(.)?[0-5]{1}[0-9]{1}(.)?[0-5]{1}[0-9]{1})?$/',$d2)) {

		$d1 = strtotime( preg_replace('/[^0-9]/','',$d1) );
		$d2 = strtotime( preg_replace('/[^0-9]/','',$d2) );

	}
	else if (is_numeric($d1) && is_numeric($d2)) {
		// �Ҳ� ����..
	}
	else {
		// �̵� ���� �ƴ�
		return false;
	}

	$d1 = date('Y-m-d H:i:s',$d1);
	$d2 = date('Y-m-d H:i:s',$d2);

	list($diff) = $GLOBALS['db']->fetch("SELECT DATEDIFF('$d1','$d2')");

	return abs($diff);
}

/**
 * �迭�� Ư�� �ʵ� ���� ����.
 * @param object $array
 * @param object $specify_field [optional]
 * @return
 */
function gd_array_sum($array, $specify_field = null) {

	$total = 0;

	foreach($array as $v) {
		$total = $total + ($specify_field === null ? $v : $v[$specify_field]);
	}

	return $total;

}

// �����ΰ��� �����丮 �������� ��������
// skinmode : skin / skin_mobile / skin_mobileV2
// tplSkin : �۾���Ų
// design_file : ���ϸ�
function get_design_history_file($skinmode, $tplSkin, $design_file) {
	// �ֱ� ���� ���� ��������(5��)
	$saved_dir = dirname($design_file);
	$saved_name = basename($design_file);
	$df_dir = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME']).$GLOBALS['cfg']['rootDir'].'/data/_skin_history/'.$skinmode.'/'.$tplSkin.'/'.$saved_dir;

	$file_hx = array();
	if (is_dir($df_dir)) {
		if ($dh = opendir($df_dir)) {
			while (($file = readdir($dh)) !== false) {
				if (preg_replace('/^Hx[0-9]*_/', '', $file) == $saved_name) $file_hx[$file] = '../../_skin_history/'.$skinmode.'/'.$tplSkin.'/'.$saved_dir.'/'.$file;
			}
			closedir($dh);
		}
	}

	$old_file_hx = array();
	if (count($file_hx) < 5) {
		$df_dir = str_replace($_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME']).$GLOBALS['cfg']['rootDir'].'/data/'.$skinmode.'/'.$tplSkin.'/'.$saved_dir.'/__gd__history';
		if (is_dir($df_dir)) {
			if ($dh = opendir($df_dir)) {
				while (($file = readdir($dh)) !== false) {
					if (preg_replace('/^Hx[0-9]*_/', '', $file) == $saved_name) $old_file_hx[$file] = '../../'.$skinmode.'/'.$tplSkin.'/'.$saved_dir.'/__gd__history/'.$file;
				}
				closedir($dh);
			}
		}
	}

	$file_hx = array_merge($file_hx, $old_file_hx);
	krsort($file_hx);
	$file_hx = array_splice($file_hx, 0, 5);
	return $file_hx;
}

// �����ΰ��� �����丮 ���� �±� ����
// skinmode : skin / skin_mobile / skin_mobileV2
// tplSkin : �۾���Ų
// design_file : ���ϸ�
function gen_design_history_tag($skinmode, $tplSkin, $design_file) {
	$file_hx = get_design_history_file($skinmode, $tplSkin, $design_file);

	$html = array();
	$html[] = '<div style="height:30px;">';
	$html[] = '	- �ֱ� ���� ����(�ִ� 5��) ����';
	$html[] = '	<select id="slt_history">';

	if (empty($file_hx) === false) {
		foreach($file_hx as $file => $df_name) {
			preg_match('/Hx([^_]*)_/', $file, $hx);
			$html[] = '		<option value="/'.$df_name.'">'.date('Y-m-d H:i:s', $hx[1]).' ���峻��</option>';
		}
	}
	else {
		$html[] = '		<option value="">�ֱ� ���峻���� �����ϴ�.</option>';
	}

	$html[] = '	</select>';
	$html[] = '	<a onclick="get_design_history()" style="cursor:pointer"><img src="../img/btn_confirm_mini.gif" /></a><a href="javascript:manual(\'http://guide.godo.co.kr/season4/board/view.php?id=design&no=21\')" style="margin-left:5px;"><img src="../img/codi/btn_designeditor_info.gif" /></a>';
	$html[] = '</div>';

	return implode(PHP_EOL, $html);
}

// �����ΰ��� �����丮 ���� �±� ����
// skinmode : skin / skin_mobile / skin_mobileV2
// tplSkin : �۾���Ų
// design_file : ���ϸ�
function save_design_history_file($skinmode, $tplSkin, $design_file) {
	$saved_dir = dirname($design_file);
	$saved_fname = basename($design_file);
	$new_design_file = 'Hx'.time().'_'.$saved_fname;

	$root_dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $GLOBALS['cfg']['rootDir'];
	$src_dir = $root_dir . '/data/' . $skinmode . '/' . $tplSkin;
	$dest_dir = '/data/_skin_history/' . $skinmode . '/' . $tplSkin. '/' . $saved_dir;

	$tmp = explode('/', $dest_dir);
	$dest_dir = $root_dir;
	for ( $i = 0; $i < count($tmp); $i++ )
	{
		$dest_dir .= $tmp[$i] . '/';
		if (!@file_exists($dest_dir)) @mkdir($dest_dir, 0757, true);
		@chMod($dest_dir, 0757);
	}

	$new_design_file = $dest_dir . '/' . $new_design_file;
	copy($src_dir . '/' . $design_file, $new_design_file);
	@chmod($new_design_file, 0757);

	if (is_dir($dest_dir)) {
		if ($dh = opendir($dest_dir)) {
			$file_hx = array();
			while (($file = readdir($dh)) !== false) {
				if (preg_replace('/^Hx[0-9]*_/', '', $file) == $saved_fname) $file_hx[] = $file;
			}
			sort($file_hx);

			for($i = 0; $i < count($file_hx) - 5; ++$i) {
				@unlink($new_dir.'/'.$file_hx[$i]);
			}
			closedir($dh);
		}
	}

	// �̸����� ���� ����.
	$preview_file = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $GLOBALS['cfg']['rootDir'] . '/data/_skin_history/'.$skinmode.'/'. $tplSkin.'/'.$design_file;
	@unlink($preview_file);
}

/*
* sms ���й�ȣ Ȯ��
* type - single, array
* single - return boolean
* array - return array - sno
*/
function smsFailCheck($type, $_phoneNumberArr)
{
	global $db;

	if($type == 'array'){
		$phoneNumberArr = $_resultSnoArr = $resultSnoArr = array();
		$phoneNumberArr = array_chunk($_phoneNumberArr, 3000);
		foreach($phoneNumberArr as $v){
			$where = implode("','", $v);
			$result = $db->query("SELECT sno FROM " . GD_SMS_FAILLIST . " WHERE phoneNumber IN( '" . $where . "') ");
			while($row = $db->fetch($result, 1)){
				$_resultSnoArr[] = $row['sno'];
			}
		}
		$resultSnoArr = array_unique($_resultSnoArr);
		sort($resultSnoArr);
		return (array)$resultSnoArr;
	}
	else {
		$result = $db->query("SELECT COUNT(*) as cnt FROM " . GD_SMS_FAILLIST . " WHERE phoneNumber = '" . $_phoneNumberArr . "'");
		$row = $db->fetch($result, 1);
		if($row['cnt'] > 0){
			return true;
		}
		return false;
	}
}

// ���� �����Ͽ� ��� EP
function daum_goods_diff($goodsno,$ar_update,$class="U") {
	include("../../conf/config.pay.php");
	include("../../conf/config.php");

	global $db;

	$allupdate=false;

	$ar_result = array();
	$ar_goods_field=array('goodsno','goodsnm','img_l','brandno','origin','maker','delivery_type','goods_delivery','use_emoney','usestock','runout','open','naver_event','use_only_adult','model_name','goods_price','sales_range_start','sales_range_end');
	$ar_opt_field=array('price','reserve');

	// �ű� ������ ����
	if ($class=="I") {
		$allupdate=true;
		$ar_update['launchdt'] = date("Y-m-d",strtotime($ar_update['launchdt']));
		$ar_update['price'] = $ar_update['goods_price'];
		$ar_update['stock'] = $ar_update['totstock'];
		$ar_update['reserve'] = $ar_update['goods_reserve'];
		$ar_update['img_l'] = array_shift(explode('|',$ar_update['img_l']));

		$ar_result['pgurl']="http://".$_SERVER['HTTP_HOST'].$cfg[rootDir]."/goods/goods_view.php?goodsno=$goodsno&inflow=daum";
	}

	// ���������͸� ��� ���Ѵ�.
	$query = "select ".implode(",",$ar_goods_field)." from ".GD_GOODS." where goodsno='$goodsno'";
	$ar_goods = $db->fetch($query);

	$query = "select ".implode(",",$ar_opt_field)." from ".GD_GOODS_OPTION." where goodsno='$goodsno' and link=1 and go_is_deleted <> '1' and go_is_display = '1'";
	$ar_opt = $db->fetch($query);

	$query = "select sum(stock) from ".GD_GOODS_OPTION." where goodsno='$goodsno' and go_is_deleted <> '1' and go_is_display = '1'";
	list($ar_goods['stock']) = $db->fetch($query);

	$query = "select category,hidden from ".GD_GOODS_LINK." where goodsno='$goodsno' order by length(category) DESC, sno ASC limit 1";
	list($ar_goods['category'],$ar_goods['hidden']) = $db->fetch($query);

	// ���� ǰ�� ���� üũ
	$before_stock=true;
	if ($ar_goods['runout']=='1' || $ar_goods['open']=='0' || $ar_goods['hidden']=='1') {
		$before_stock=false;
	}
	if ($ar_goods['usestock']=='o' && $ar_goods['stock']==0) {
		$before_stock=false;
	}
	$current = time();
	if (($ar_goods['sales_range_start'] || $ar_goods['sales_range_end']) && ($ar_goods['sales_range_start'] > $current || $current > $ar_goods['sales_range_end'])) {
		$before_stock=false;
	}

	// ������Ʈ �� ǰ�� ���� üũ
	$tmp_usestock = $ar_update['usestock'];
	$tmp_stock = (isset($ar_update['stock']) ? $ar_update['stock'] : $ar_goods['stock']);

	if ($tmp_usestock == 'on') $ar_update['usestock'] = 'o';

	$after_stock=true;
	if ($ar_update['runout'] == '1' || $ar_update['open'] == '0' || $ar_update['hidden'] == '1') {
		$after_stock=false;
	}
	if ($tmp_usestock=='o' && $tmp_stock==0) {
		$after_stock=false;
	}
	if (($ar_update['sales_range_start'] || $ar_update['sales_range_end']) && ($ar_update['sales_range_start'] > $current || $current > $ar_update['sales_range_end'])) {
		$after_stock=false;
	}

	// �ǸźҰ� > �ǸŰ���
	if ($allupdate == false && $before_stock == false && $after_stock == true) {
		daum_goods_runout_recovery($goodsno);
		return;
	}

	// �ǸźҰ� > �ǸźҰ�
	if ($allupdate == false && $before_stock == false && $after_stock == false) {
		return;
	}

	// �ű԰� �ƴ� ������Ʈ ��Ȳ������ ����ǰ�����¿� ������Ʈ�� ǰ�����¿� ���� ���������Ǵ�
	if ($class!="I") {
		// ��� �־��µ� ǰ���� �Ǹ� ǰ����������� ����
		if ($before_stock==true && $after_stock==false)
		{
			daum_goods_runout($goodsno);
			return;
		}
	}

	// ���������Ϳ� ������Ʈ �� �����͸� ���� ���ʵ带 �����.
	if ($allupdate || (isset($ar_update['goodsnm']) && $ar_update['goodsnm']!=$ar_goods['goodsnm'])) {
		$ar_result['pname']=strip_tags($ar_update['goodsnm']);
	}

	if ($ar_update['img_l'] && $ar_update['img_l'] != $ar_goods['img_l']) {
		if (preg_match('/^http(s)?:\/\//', $ar_update['img_l'])) {
			$ar_result['igurl'] = $ar_update['img_l'];
		}
		else {
			$ar_result['igurl'] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$ar_update['img_l'];
		}
	}

	if ($allupdate || ($ar_update['brandno'] && $ar_update['brandno']!=$ar_goods['brandno'])) {
		list($ar_result['brand'])=$db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='{$ar_update['brandno']}'");
		$ar_result['brand'] = strip_tags($ar_result['brand']);
	}

	if ($allupdate || (isset($ar_update['maker']) && $ar_update['maker']!=$ar_goods['maker'])) {
		$ar_result['maker'] = strip_tags($ar_update['maker']);
	}

	if ($allupdate || (isset($ar_update['naver_event']) && $ar_update['naver_event']!=$ar_goods['naver_event'])) {
		$ar_result['event'] = strip_tags($ar_update['naver_event']);
	}

	if ($allupdate || (isset($ar_update['use_only_adult']) && $ar_update['use_only_adult']!=$ar_goods['use_only_adult'])) {
		$ar_result['adult'] = $ar_update['use_only_adult'];
	}

	if ($allupdate || (isset($ar_update['model_name']) && $ar_update['model_name']!=$ar_goods['model_name'])) {
		$ar_result['model'] = strip_tags($ar_update['model_name']);
	}

	if ($allupdate || (isset($ar_update['price']) && $ar_update['price']!=$ar_opt['price']))
	{
		$ar_result['price'] = $ar_update['price'];
	}
		/*
			[0] : �⺻ ��� ��å�� ����
			[1] : ������
			[3] : ���� ��ۺ�
			[4] : ���� ��ۺ�
			[5] : ������ ��ۺ�
		*/
	if ($allupdate || $ar_update['delivery_type'] != $ar_goods['delivery_type'] || $ar_update['goods_delivery'] != $ar_goods['goods_delivery']) {
		switch ($ar_update['delivery_type']) {
			case "0":
				$tmp_price = (isset($ar_update['price']) ? $ar_update['price'] : $ar_opt['price']);
				if($set['delivery']['free'] <= $tmp_price) $ar_result['deliv']=0;
				else $ar_result['deliv']=$set['delivery']['default'];

				if ($set['delivery']['deliveryType'] != "�ĺ�") {
					if ($tmp_price >= $set['delivery']['free'])
						$ar_result['deliv'] = 0;
					else
						$ar_result['deliv'] = $set['delivery']['default'];
				}
				else
					$ar_result['deliv'] = -1;
				break;
			case "1":
				$ar_result['deliv']=0;
				break;
			case "3":
				$ar_result['deliv'] = -1;
				break;
			case "4":
				$ar_result['deliv'] = $ar_update['goods_delivery'];
				break;
			case "5":
				$ar_result['deliv'] = $ar_update['goods_delivery'];
				break;
		}
	}

	if(
		$allupdate
		||
		(
			($ar_update['reserve'] && $ar_update['reserve']!=$ar_opt['reserve'])
			||
			($ar_update['use_emoney'] && $ar_update['use_emoney']!=$ar_goods['use_emoney'])
			||
			(isset($ar_update['price']) && $ar_update['price']!=$ar_opt['price'])
		)
	) {
		if ($ar_update['use_emoney']=='0') {
			$tmp_price = (isset($ar_update['price']) ? $ar_update['price'] : $ar_opt['price']);
			if( !$set['emoney']['chk_goods_emoney'] ){
				if( $set['emoney']['goods_emoney'] ) $ar_result['point'] = getDcprice($tmp_price,$set['emoney']['goods_emoney'].'%');
			}else{
				$ar_result['point'] = $set['emoney']['goods_emoney'];
			}
		}
		else {
			$ar_result['point']=$ar_update['reserve'];
		}
	}

	if ($ar_update['category'] && $ar_update['category'] != $ar_goods['category']) {
		for ($i=1;$i<=4;$i++) {
			$tmp_nm="";
			$tmp_code = substr($ar_update['category'],0,3*$i);
			if (strlen($tmp_code)==$i*3) {
				list($tmp_nm) = $db->fetch("select catnm from ".GD_CATEGORY." where category='$tmp_code'");

				$ar_result['caid'.$i]=strip_tags($tmp_code);
				$ar_result['cate'.$i]=strip_tags($tmp_nm);
			}
		}
	}

	$discount = $ar_update['discount'];
	if ($discount) {
		$query = "select gd_amount,gd_unit,gd_cutting from ".GD_GOODS_DISCOUNT." where gd_goodsno='$goodsno'";
		list($gd_amount,$gd_unit,$gd_cutting) = $db->fetch($query);

		if ($discount['gd_amount'] != $gd_amount || $discount['gd_unit'] != $gd_unit || $discount['gd_cutting'] != $gd_cutting) {
			$ar_result['discount'] = 'Y';
		}
	}

	// �����Ͱ� �ִٸ� �����͸� �ִ´�
	if (count($ar_result)) {
		$ar_result['class']=$class;
		$ar_result['mapid']=$goodsno;
		$ar_result['utime']=date("Y-m-d H:i:s", G_CONST_NOW);
		if (!$ar_result['pname'] && !$ar_result['price']) {
			$ar_result['pname'] = strip_tags($ar_goods['goodsnm']);
			$ar_result['price'] = $ar_goods['goods_price'];
		}
		else if (!$ar_result['pname']) $ar_result['pname'] = strip_tags($ar_goods['goodsnm']);
		else if (!$ar_result['price']) $ar_result['price'] = $ar_goods['goods_price'];
		$ar_str=array();
		foreach($ar_result as $key=>$value)
		{
			$ar_str[]="$key = '$value'";
		}
		$query = "insert into ".GD_GOODS_UPDATE_DAUM." set ".implode(" , ",$ar_str);
		$db->query($query);
	}

	// �ű��ε� ǰ���̸� ǰ�������� �� ������
	if ($class=="I" && $after_stock==false) {
		daum_goods_runout($goodsno);
	}
}

// ����ϼ� ������� Ȯ��
function isMobileV2() {
	// �������� ���� ���� Ȯ��
	$version2_apply_file_name = ".htaccess";
	$version2_apply_file_path = dirname(__FILE__)."/../../m/".$version2_apply_file_name;

	$bCurrent_V2_htaccess = file_exists($version2_apply_file_path);
	$bCurrent_V2_applied = false;

	## ������� Ȯ��
	if ( $bCurrent_V2_htaccess ) {
		$aFileContent = file(dirname(__FILE__)."/../../m/".$version2_apply_file_name);
		for ($i=0; $i<count($aFileContent); $i++) {
			if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
				break;
			}
		}
		if ($i == count($aFileContent)) {
			$bCurrent_V2_applied = false;
		} else {
			$bCurrent_V2_applied = true;
		}
	} else {
		$bCurrent_V2_applied = false;
	}
	return $bCurrent_V2_applied;
}
?>