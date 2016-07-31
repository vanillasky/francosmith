<?
/*********************************************************
* ���ϸ�     :  pGoodsList.php
* ���α׷��� :	pad ��ǰ����Ʈ API
* �ۼ���     :  dn
* ������     :  2011.10.12
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);


if($_GET['debug']) {
	$_POST['category'] = '004001';
	$_POST['skey'] = '';
	$_POST['sword'] = '';
	$_POST['open'] = '';
	$_POST['list'] = '';
	$_POST['page'] = '';
	$_POST['authentic'] = 'JDC28qadwgSUcKzlyqtglO112W4hQCBt';
}

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### ����Ű Check �� ###


if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		if(strstr($key, 'arr_')) {
			${str_replace('arr_', '', $key)} = explode('|', $val);
		}
		else  {
			${$key} = $val;
		}
	}
}

if(!$list) $list = 10;
if(!$page) $page = 1;

$arr_where = Array();

$arr_where[] = $db->_query_print('g.todaygoods=[s]', 'n');	//�����̼� ��ǰ�� ����
if($category) $arr_where[] = sprintf("l.category like '%s%%'", $val_category);

if(!empty($category) && is_array($category)) {
	$tmp_where = Array();
	
	foreach($category as $val_category) {
		if($val_category) {
			$tmp_where[] = sprintf("l.category like '%s%%'", $val_category);
		}
	}

	if(!empty($tmp_where)) {
		$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	}
}

if($skey && !empty($sword) && is_array($sword)) {
	$tmp_where = Array();
	
	foreach($sword as $val_sword) {
		if($val_sword) {
			if($skey == 'goodsnm') {
				$tmp_where[] = $db->_query_print('g.'.$skey.' like [s]', '%'.$val_sword.'%');
			}
			else if($skey =='goodsno'){
				$tmp_where[] = $db->_query_print('g.'.$skey.'=[i]', $val_sword);
			}
			else if($skey =='goodscd') {
				$tmp_where[] = $db->_query_print('g.'.$skey.'=[s]', $val_sword);
			}
		}
	}

	if(!empty($tmp_where)) {
		$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	}
}

$open = 1;
if($open != '') $arr_where[] = $db->_query_print('sg.open_shoptouch=[i]', $open);

if(!empty($arr_where)) {
	$where = ' WHERE '.implode(' AND ', $arr_where);
}
else {
	$where = ' WHERE 1=1';
}

$table = '
'.GD_GOODS.' g
JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno
LEFT JOIN '.GD_GOODS_OPTION.' o ON g.goodsno=o.goodsno AND o.link
';
if(!empty($category)) {
	$table .= '
	LEFT JOIN '.GD_GOODS_LINK.' l ON g.goodsno=l.goodsno';
}

$goods_query = $db->_query_print('SELECT DISTINCT g.goodsno, g.goodsnm, g.regdt, g.open, o.price, g.totstock, g.img_s, g.img_l, sg.open_shoptouch, g.brandno, g.delivery_type, g.goods_delivery, sg.img_shoptouch FROM '.$table.$where.' ORDER BY regdt DESC');

$res_goods = $db->_select_page($list, $page, $goods_query);

if(!empty($res_goods['record'])) {
	$i = 0;
	foreach($res_goods['record'] as $row_goods) {
		$tmp_goods[$i]['goodsno'] = $row_goods['goodsno'];	//��ǰ��ȣ
		
		$img_path = 'http://'.$_SERVER['HTTP_HOST'].$cfg[rootDir].'/data/goods/';
		
		$tmp_goods[$i]['thumbnail'] = '';

		if($row_goods['img_shoptouch']) {
			
			$arr_img_shopTouch = @explode('|', $row_goods['img_shoptouch']);
			
			if($arr_img_shopTouch[0]) $tmp_goods[$i]['thumbnail'] = $arr_img_shopTouch[0];

		}else if($row_goods['img_s']) {	//����Ʈ �̹����� ���� ���
			$tmp_goods[$i]['thumbnail'] = $img_path.$row_goods['img_s']; //�����
		}
		else if($row_goods['img_l']) {
			$tmp_goods[$i]['thumbnail'] = $img_path.$row_goods['img_l']; //�����
		}

		//$tmp_goods[$i]['thumbnail'] = 'http://darknulbo.selly.co.kr/data/goods/201107/AAAE00000047_1.jpg';//����� �ϵ��ڵ�
		
		$tmp_goods[$i]['goodsnm'] = $row_goods['goodsnm'];	//��ǰ��
		$tmp_goods[$i]['regdt'] = substr($row_goods['regdt'], 0, 10);	//�����
		$tmp_goods[$i]['open'] = $row_goods['open_shoptouch'];	//��������
		$tmp_goods[$i]['price'] = $row_goods['price'];	//����
		$tmp_goods[$i]['totstock'] = $row_goods['totstock'];	//���

		if($row_goods['delivery_type'] == '0') {
			$tmp_goods[$i]['delivery_str'] = '�⺻�����å�� ����';
		}
		else if($row_goods['delivery_type'] == '1'){
			$tmp_goods[$i]['delivery_str'] = '������';
		}
		else if($row_goods['delivery_type'] == '2'){
			if($row_goods['goods_delivery'] == 0) {
				$tmp_goods[$i]['delivery_str'] = '������';
			}
			else {
				$tmp_goods[$i]['delivery_str'] = '��ǰ�� ��ۺ�('.$row_goods['goods_delivery'].')';
			}
		}
		else if($row_goods['delivery_type'] == '3'){
			if($row_goods['goods_delivery'] == 0) {
				$tmp_goods[$i]['delivery_str'] = '���ҹ�ۺ�';
			}
			else {
				$tmp_goods[$i]['delivery_str'] = '���ҹ�ۺ�('.$row_goods['goods_delivery'].')';
			}

		}

		### �귣��� �˻� ###
		$row_goods['brandnm'] = '';
		if($row_goods['brandno']) {
			$brand_query = $db->_query_print('SELECT * FROM '.GD_GOODS_BRAND.' WHERE sno=[i]', $row_goods['brandno']);
			$brand_res = $db->_select($brand_query);

			$row_goods['brandnm'] = $brand_res[0]['brandnm'];
		}

		$tmp_goods[$i]['brandnm'] = $row_goods['brandnm'];	//�귣���


		$i++;
	}
}


$res_data['result']['code'] = '000';
$res_data['result']['msg'] = '����';


if(!empty($tmp_goods)) {
	$res_data['data'] = $tmp_goods;
}


echo ($json->encode($res_data));

?>
