<?
/*********************************************************
* ���ϸ�     :  sellyApiProc.php
* ���α׷��� :  Ajax �̿� SELLY Api ���� ������
* �ۼ���     :  dn
* ������     :  2012.05.13
**********************************************************/
include "../lib.php";
include "../../lib/sAPI.class.php";
include "../../lib/json.class.php";
include "../../conf/config.pay.php";

$mode = $_POST['mode'];
unset($_POST['mode']);
$sAPI = new sAPI();

### ��ǰ��ũ/������ũ colum START ###
$column = Array(//�⺻��ǰ
	"goodsnm" => "goods_nm",
	"origin" => "origin",
	"maker" => "maker",
	"brandnm" => "brand_nm",//
	"tax" => "tax",
	"delivery_type" => "delivery_type",//
	"keyword" => "keyword",//
	"shortdesc" => "pr_text",//
	"supply" => "buy_price",//���԰�
	"longdesc" => "desc",
	"runout" => "goods_status",
	"goods_delivery" => "delivery_price",
	"manufacture_date" => "make_date",
	"model_name" => "model_nm",
);

$cate_column = Array(//ī�װ�
	"catnm" => "category_nm",
	"category" => "shop_category_cd",
	"sort" => "sort",
	"hidden" => "use_yn"
);

$cate_hidden = Array(//ī�װ� ��뿩��
	"0" => "Y",
	"1" => "N"
);

$opt_column = Array(//��ǰ�ɼ�
	"opt1" => "opt_value1",
	"opt2" => "opt_value2",
	"price" => "add_price",
	"stock" => "stock",
);


### ��ۺ� ������ �˻� START ###
$query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s]', 'selly');
$tmp_data = $db->_select($query);

if($tmp_data) {
	foreach($tmp_data as $data) {
			$delivery_data[$data['name']] = $data['value'];
	}
}

### ��ۺ� ������ �˻� END ###

$arr_delivery_type = Array(//���Ÿ��
	"0" => Array(//�⺻�����å(��/�ĺҷ� ����)
		"����" => $delivery_data['basic_advence_delivery'],//(�������� ����)
		"�ĺ�" => $delivery_data['basic_payment_delivery'],//(���Ҹ� ����)
		),
	"1" => "1",//����(����)
	"2" => "",//��ǰ�� ��ۺ�(�̻��)
	"3" => $delivery_data['payment_delivery'],//���� ��ۺ�(���Ҹ� ����)
	"4" => $delivery_data['fixe_delivery'],//���� ��ۺ�(�������� ����)
	"5" => $delivery_data['cnt_delivery'],//������ ��ۺ�(���̽��� �����)
);

$arr_tax_data = Array(//����/�����
	"0" => "2",//�����
	"1" => "1"//����
);

$arr_runout_data = Array(//��ǰ����
	"0" => "0001",//�Ǹ���
	"1" => "0003"//ǰ��
);
### ��ǰ��ũ/������ũ colum END ###

switch($mode) {
	case 'scraporder' :

		$arr = array();

		if(is_array($_POST) && !empty($_POST)) {
			foreach($_POST as $key=>$val) {
				$arr[$key] = $val;
			}
		}

		$res = $sAPI->scrapOrder($arr);

		$res_arr = array();
		$res_arr['minfo_idx'] = $arr['minfo_idx'];
		$res_arr['scrap_order_status'] = $arr['scrap_order_status'];
		$res_arr['total_cnt'] = 0;
		$res_arr['new_cnt'] = 0;
		$res_arr['old_cnt'] = 0;
		$res_arr['err_cnt'] = 0;

		unset($arr);

		if($res['code']) {
			$res_arr['code'] = $res['code'];
			$res_arr['msg'] = $res['msg'];
			$json = new Services_JSON();
			echo $json->encode($res_arr);
			exit;
		}

		switch($res_arr['scrap_order_status']) {
			case 'new' :
				### �ֹ�Ȯ�� ###
				if(!empty($res) && is_array($res)) {
					foreach($res as $row_order) {
						$chk_query = $db->_query_print('SELECT count(morder_no) ord_cnt FROM '.GD_MARKET_ORDER.' WHERE order_idx=[i]', $row_order['order_idx']);
						$res_chk = $db->_select($chk_query);
						$ord_chk_cnt = $res_chk[0]['ord_cnt'];

						unset($chk_query, $res_chk);

						if($ord_chk_cnt < 1) {

							### �ֹ��� �Է� ###
							$ord_arr = array();

							/* �ֹ������� �Է����� �ʰ� gd_market_order�� ��� �ֹ� ������ ���� */
							/*
							# �ֹ����� �Է�
							$ord_arr['ordno'] = getordno();
							$ord_arr['nameOrder'] = $row_order['order_nm'];
							$ord_arr['email'] = '';
							$ord_arr['phoneOrder'] = $row_order['order_tel'];
							$ord_arr['mobileOrder'] = $row_order['order_cel'];

							$ord_arr['nameReceiver'] = $row_order['receive_nm'];
							$ord_arr['phoneReceiver'] = $row_order[''];
							$ord_arr['mobileReceiver'] = $row_order[''];
							$ord_arr['zipcode'] = $row_order['receive_zip'];
							$ord_arr['address'] = $row_order['receive_addr'];

							$ord_arr['settlekind'] = 'a';	//�������Ա�
							// ����ݾ� / �����ݾ� ?? ���߿� � �ݾ��� �־�� ���� ����
							$ord_arr['settleprice'] = $row_order['adjust_price'];
							$ord_arr['prn_settleprice'] = $row_order['adjust_price'];
							$ord_arr['goodsprice'] = $row_order['sale_price'];
							$ord_arr['deli_title'] = '�⺻���';

							$ord_arr['delivery'] = $row_order['settle_delivery_price'];
							$delivery_type = '';
							if($row_order['delivery_type_order'] == 1 || $row_order['delivery_type_order'] == 3) {
								$delivery_type = '����';
							}
							else {
								$delivery_type = '�ĺ�';
							}
							$ord_arr['deli_type'] = $delivery_type;
							$ord_arr['deli_msg'] = $row_order['delivery_msg'];
							$ord_arr['coupon'] = '';
							$ord_arr['emoney'] = 0;

							$ord_arr['memberdc'] = 0;
							$ord_arr['reserve'] = 0;
							$ord_arr['bankAccount'] = '';
							$ord_arr['bankSender'] = '';
							$ord_arr['m_no'] = '0';

							$ord_arr['ip'] = $_SERVER['REMOTE_ADDR'];
							$ord_arr['referer'] = '';
							$ord_arr['memo'] = '';
							$ord_arr['inflow'] = 'sugi'; //'selly'; ��� �־�� ���� ����
							$ord_arr['orddt'] = date('Y-m-d H:i:s');

							$ord_arr['coupon_emoney'] = 0;
							$ord_arr['cashbagcard'] = '';
							$ord_arr['cbyn'] = 'N';
							$ord_arr['settlekind'] = 'a';	//�������Ա�
							$ord_arr['m_no'] = 0;

							$ord_arr['phoneOrder'] = $row_order['order_tel'];
							$ord_arr['mobileOrder'] = $row_order['order_cel'];
							$ord_arr['phoneReceiver'] = $row_order['receive_tel'];
							$ord_arr['mobileReceiver'] = $row_order['receive_cel'];
							$ord_arr['zipcode'] = $row_order['receive_zip'];

							$ord_arr['nameOrder'] = $row_order['order_nm'];

							## �ٷ� �Ա�Ȯ�� ó��
							$ord_arr['step'] = '1';
							$ord_arr['cyn'] = 'y';
							$ord_arr['cdt'] = date('Y-m-d H:i:s');


							$ord_query = $db->_query_print('INSERT INTO '.GD_ORDER.' SET [cv]', $ord_arr);
							$ord_res = $db->query($ord_query);

							if(!$ord_res) {
								$res_arr['err_cnt'] = $res_arr['err_cnt'] + 1;
								continue;
							}
							*/

							### ���� �ֹ� ���� �Է� ##

							unset($row_order['goods_cd'], $row_order['goods_opt_cd'], $row_order['category'], $row_order['category_cust'], $row_order['temp4'], $row_order['temp5'], $row_order['temp6'],$row_order['temp7'],$row_order['temp8'],$row_order['temp9'],$row_order['temp10'], $row_order['buy_price']);
							$mord_arr = array();

							$mord_arr = $row_order;
							/*
							$mord_arr['ordno'] = $ord_arr['ordno'];
							$mord_arr['order_idx'] = $row_order['order_idx'];
							$mord_arr['mall_cd'] = $row_order['mall_cd'];
							$mord_arr['mall_order_no'] = $row_order['mall_order_no'];
							$mord_arr['mall_order_seq'] = $row_order['mall_order_seq'];

							$mord_arr['order_id'] = $row_order['order_id'];
							$mord_arr['order_nm'] = $row_order['order_nm'];
							$mord_arr['order_tel'] = $row_order['order_tel'];
							$mord_arr['order_cel'] = $row_order['order_cel'];
							$mord_arr['receive_nm'] = $row_order['receive_nm'];
							$mord_arr['receive_tel'] = $row_order['receive_tel'];
							$mord_arr['receive_cel'] = $row_order['receive_cel'];
							$mord_arr['receive_zip'] = $row_order['receive_zip'];
							$mord_arr['receive_addr'] = $row_order['receive_addr'];

							$mord_arr['mall_goods_cd'] = $row_order['mall_goods_cd'];
							$mord_arr['mall_goods_nm'] = $row_order['mall_goods_nm'];
							$mord_arr['mall_goods_opt'] = $row_order['mall_goods_opt'];
							$mord_arr['mall_goods_add'] = $row_order['mall_goods_add'];

							$mord_arr['susu_price'] = $row_order['susu_price'];

							$mord_arr['delivery_price'] = $row_order['delivery_price'];
							$mord_arr['settle_delivery_price'] = $row_order['settle_delivery_price'];
							$mord_arr['adjust_price'] = $row_order['adjust_price'];
							$mord_arr['settle_type'] = $row_order['settle_type'];
							$mord_arr['delivery_st'] = $row_order['delivery_st'];

							$mord_arr['status'] = $row_order['status'];
							$mord_arr['order_date'] = $row_order['order_date'];
							$mord_arr['settle_price'] = $row_order['settle_price'];
							$mord_arr['mall_login_id'] = $row_order['mall_login_id'];
							$mord_arr['sale_price'] = $row_order['sale_price'];

							$mord_arr['discount_price'] = $row_order['discount_price'];
							$mord_arr['delivery_type_order'] = $row_order['delivery_type_order'];
							*/
							$mord_query = $db->_query_print('INSERT INTO '.GD_MARKET_ORDER.' SET [cv]', $mord_arr);
							$mord_res = $db->query($mord_query);

							if(!$mord_res) {
								$res_arr['err_cnt'] = $res_arr['err_cnt'] + 1;
								continue;
							}
							### �ֹ���ǰ �Է� ###

							$ord_item_arr = array();

							$ord_item_arr['order_no'] = $mord_arr['order_no'];

							### ��ǰ���� �������� ###
							$chk_query = $db->_query_print('SELECT goodsno FROM '.GD_MARKET_GOODS.' WHERE mall_cd=[s] AND mall_goods_cd=[s]', $row_order['mall_cd'], $row_order['mall_goods_cd']);
							$res_chk = $db->_select($chk_query);
							$ord_item_arr['goodsno'] = $res_chk[0]['goodsno'];

							//90 / ���� (1,500��) +0�� / 1����100 / ��� (1,500��) +0�� / 1��
							if($row_order['mall_goods_opt']) {
								### opt �� ���� ###
								$tmp_row_opt = explode('��', $row_order['mall_goods_opt']);

								foreach($tmp_row_opt as $row_opt) {

									$ord_item_arr['opt1'] = '';
									$ord_item_arr['opt2'] = '';

									preg_match('/\((.*?)��\)/smi', str_replace(' ', '', $row_opt), $tmp_price);
									preg_match('/\+(.*?)��/smi', str_replace(' ', '', $row_opt), $tmp_addprice);
									preg_match('/��\/(.*?)��/smi', str_replace(' ', '', $row_opt), $tmp_cnt);

									$r_price = str_replace(',', '', $tmp_price[1]);
									$add_price = str_replace(',', '', $tmp_addprice[1]);
									$r_cnt = str_replace(',', '', $tmp_cnt[1]);

									if(!$r_cnt) {
										$re_check_cnt = explode('/', $row_opt);
										$r_cnt = trim(str_replace('��', '', $re_check_cnt[count($re_check_cnt)-1]));
									}

									$tmp_opt = explode('/', $row_opt);

									if(count($tmp_opt) > 2) {
										$ord_item_arr['opt1'] = trim($tmp_opt[0]);

										$ttmp_opt = explode('(', $tmp_opt[1]);
										$ord_item_arr['opt2'] = trim($ttmp_opt[0]);
									}
									else {
										$ttmp_opt = explode('(', $tmp_opt[0]);
										$ord_item_arr['opt1'] = trim($ttmp_opt[0]);
									}

									$ord_item_arr['goodsopt'] = $row_opt;

									if($ord_item_arr['goodsno']) {

										### ��ǰ ���̺��� ���ް� ��������
										$supply_query = $db->_query_print('SELECT supply FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i] AND opt1=[s] AND opt2=[s] and go_is_deleted <> \'1\'', $ord_item_arr['goodsno'], $ord_item_arr['opt1'], $ord_item_arr['opt2']);
										$res_supply = $db->_select($supply_query);
										$ord_item_arr['supply'] = $res_supply[0]['supply'];
										if(!$ord_item_arr['supply']) $ord_item_arr['supply'] = 0;

										$ord_item_arr['goodsnm'] = $row_order['mall_goods_nm'];

										$brand_query = $db->_query_print('SELECT g.maker, gb.brandnm, g.tax, g.delivery_type, g.goods_delivery, g.usestock FROM '.GD_GOODS.' g LEFT JOIN '.GD_GOODS_BRAND.' gb ON g.brandno=gb.sno WHERE g.goodsno=[i]', $ord_item_arr['goodsno']);
										$res_brand = $db->_select($brand_query);

										$ord_item_arr['maker'] = addslashes($res_brand[0]['maker']);
										$ord_item_arr['brandnm'] = addslashes($res_brand[0]['brandnm']);
										$ord_item_arr['tax'] = $res_brand[0]['tax'];
										$delivery_type = $res_brand[0]['delivery_type'];
										$goods_delivery = $res_brand[0]['goods_delivery'];

										if($res_brand[0]['usestock'] == 'o') {
											$ord_item_arr['stockable'] = 'y';
										}
										else {
											$ord_item_arr['stockable'] = 'n';
										}

										$ord_item_arr['deli_msg'] = '';
										if($row_order['delivery_type_order'] == 2 || $row_order['delivery_type_order'] == 4){
											$ord_item_arr['deli_msg'] = '����';
											if($goods_delivery) $ord_item_arr['deli_msg'] .= ' '.number_format($goods_delivery).' ��';
										}

										$ord_item_arr['price'] = $r_price + $add_price;
										$ord_item_arr['ea'] = $r_cnt;

										## �ٷ� �Ա�Ȯ�� ó��
										$ord_item_arr['cyn'] = 'y';
										$ord_item_arr['istep'] = '1';

										$ord_item_query = $db->_query_print('INSERT INTO '.GD_MARKET_ORDER_ITEM.' SET [cv]', $ord_item_arr);


										$db->query($ord_item_query);
									}
									else {

										if(!$ord_item_arr['goodsno']) $ord_item_arr['goodsno'] = 0;

										$ord_item_arr['goodsnm'] = $row_order['mall_goods_nm'];

										$ord_item_arr['price'] = $r_price + $add_price;
										$ord_item_arr['ea'] = $r_cnt;

										## �ٷ� �Ա�Ȯ�� ó��
										$ord_item_arr['cyn'] = 'y';
										$ord_item_arr['istep'] = '1';

										$ord_item_query = $db->_query_print('INSERT INTO '.GD_MARKET_ORDER_ITEM.' SET [cv]', $ord_item_arr);

										$db->query($ord_item_query);

									}

								}

							}
							else {

								$ord_item_arr['opt1'] = '';
								$ord_item_arr['opt2'] = '';

								if($ord_item_arr['goodsno']) {

									### ��ǰ ���̺��� ���ް� ��������
									$supply_query = $db->_query_print('SELECT supply FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i] AND opt1=[s] AND opt2=[s] and go_is_deleted <> \'1\' ', $ord_item_arr['goodsno'], $ord_item_arr['opt1'], $ord_item_arr['opt2']);
									$res_supply = $db->_select($supply_query);
									$ord_item_arr['supply'] = $res_supply[0]['supply'];
									if(!$ord_item_arr['supply']) $ord_item_arr['supply'] = 0;

									$ord_item_arr['goodsnm'] = $row_order['mall_goods_nm'];

									$brand_query = $db->_query_print('SELECT g.maker, gb.brandnm, g.tax, g.delivery_type, g.goods_delivery, g.usestock FROM '.GD_GOODS.' g LEFT JOIN '.GD_GOODS_BRAND.' gb ON g.brandno=gb.sno WHERE g.goodsno=[i]', $ord_item_arr['goodsno']);
									$res_brand = $db->_select($brand_query);

									$ord_item_arr['maker'] = addslashes($res_brand[0]['maker']);
									$ord_item_arr['brandnm'] = addslashes($res_brand[0]['brandnm']);
									$ord_item_arr['tax'] = $res_brand[0]['tax'];
									$delivery_type = $res_brand[0]['delivery_type'];
									$goods_delivery = $res_brand[0]['goods_delivery'];

									if($res_brand[0]['usestock'] == 'o') {
										$ord_item_arr['stockable'] = 'y';
									}
									else {
										$ord_item_arr['stockable'] = 'n';
									}

									$ord_item_arr['deli_msg'] = '';
									if($row_order['delivery_type_order'] == 2 || $row_order['delivery_type_order'] == 4){
										$ord_item_arr['deli_msg'] = '����';
										if($goods_delivery) $ord_item_arr['deli_msg'] .= ' '.number_format($goods_delivery).' ��';
									}

									$ord_item_arr['price'] = $row_order['settle_price'];
									$ord_item_arr['ea'] = $row_order['order_cnt'];

									## �ٷ� �Ա�Ȯ�� ó��
									$ord_item_arr['cyn'] = 'y';
									$ord_item_arr['istep'] = '1';

									$ord_item_query = $db->_query_print('INSERT INTO '.GD_MARKET_ORDER_ITEM.' SET [cv]', $ord_item_arr);

									$db->query($ord_item_query);
								}
								else {

									if(!$ord_item_arr['goodsno']) $ord_item_arr['goodsno'] = 0;

									$ord_item_arr['goodsnm'] = $row_order['mall_goods_nm'];

									$ord_item_arr['price'] = $row_order['settle_price'];
									$ord_item_arr['ea'] = $row_order['order_cnt'];

									## �ٷ� �Ա�Ȯ�� ó��
									$ord_item_arr['cyn'] = 'y';
									$ord_item_arr['istep'] = '1';

									$ord_item_query = $db->_query_print('INSERT INTO '.GD_MARKET_ORDER_ITEM.' SET [cv]', $ord_item_arr);
									$db->query($ord_item_query);
								}

							}

							## �ű� ������ count ++;
							$res_arr['new_cnt'] = $res_arr['new_cnt'] + 1;
						}
						else {
							## ������� count ++;
							$res_arr['old_cnt'] = $res_arr['old_cnt'] + 1;

						}
						## total count ++;
						$res_arr['total_cnt'] = $res_arr['total_cnt'] + 1;
					}
				}

				$json = new Services_JSON();
				echo $json->encode($res_arr);

				break;

			default :

				if(!empty($res) && is_array($res)) {
					foreach($res as $row_order) {
						$chk_query = $db->_query_print('SELECT order_idx, status FROM '.GD_MARKET_ORDER.' WHERE order_idx=[i]', $row_order['order_idx']);
						$res_chk = $db->_select($chk_query);
						$result = $res_chk[0];

						if(is_array($result) && !empty($result)) {

							if (($row_order['status'] != '0031' && $row_order['status'] > $result['status'])
							|| ($row_order['status'] == '0031' && $row_order['status'] != $result['status'])) {

								$upd_data['status'] = $row_order['status'];
								$upd_data['return_msg'] = $row_order['return_msg'];
								$upd_data['sync_'] = 0;

								switch($res_arr['scrap_order_status']) {
									case 'cancel': {
										//��ҿ�û START
										$req_date_type = 'cancel_date';
										break;
									}
									case 'return': {
										//��ǰ��û START
										$req_date_type = 'return_date';
										break;
									}
									case 'exchange': {
										//��ȯ��û START
										$req_date_type = 'exchange_date';
										break;
									}
									case 'buyconfirm':{
										//����Ȯ�� START
										$req_date_type = 'buyconfirm_date';
										break;
									}
									case 'adjust': {
										//����Ϸ� START
										$req_date_type = 'adjust_date';
										break;
									}
								}

								$upd_data[$req_date_type] = $row_order[$req_date_type];

								$upd_query = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_idx=[i]', $upd_data, $result['order_idx']);

								$upd_result = $db->query($upd_query);

								## �ű� ������ count ++;
								$res_arr['new_cnt'] = $res_arr['new_cnt'] + 1;
							}
							else {
								## ������� count ++;
								$res_arr['old_cnt'] = $res_arr['old_cnt'] + 1;
							}
						}

						$res_arr['total_cnt'] = $res_arr['total_cnt'] + 1;
					}
				}

				$json = new Services_JSON();
				echo $json->encode($res_arr);

				break;
		}
		break;
	case 'sendorder' :
		$arr = array();

		if(is_array($_POST) && !empty($_POST)) {
			foreach($_POST as $key=>$val) {
				$arr[$key] = $val;
			}
		}
		$res = $sAPI->sendOrder($arr);
		$res_arr = array();
		$json = new Services_JSON();
		if($res['code']) {
			if($res['status']) {
				$upd_arr['status'] = $res['status'];
				switch($res['status']) {
					case '0021' :
						$upd_arr['cancel_date'] = date('Y-m-d');
						break;
					case '0022' :
						$upd_arr['cancel_date'] = date('Y-m-d');
						$upd_arr['cancel_confirm_date'] = date('Y-m-d');
						break;
				}
				$db->query($db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_idx=[i]', $upd_arr, $arr['order_idx']));
				unset($res['status']);
			}
			$res_arr = $res;
			$res_arr['order_idx'] = $arr['order_idx'];
			echo $json->encode($res_arr);
			exit;
		}

		$upd_query = array();
		if(is_array($res) && !empty($res)) {
			foreach($res as $row) {
				$req_date_type = array();
				switch($arr['send_status']) {
					case '0020':
						$req_date_type[] = 'check_date';
						break;
					case '0030':
						$req_date_type[] = 'delivery_date';
						$req_date_type[] = 'delivery_end_date';
						break;
					case '0022':
						$req_date_type[] = 'cancel_confirm_date';
						break;
					case '0032':
						$req_date_type[] = 'return_confirm_date';
						break;
					case '0042':
						$req_date_type[] = 'exchange_return_date';
						break;
					case '0043':
						$req_date_type[] = 'exchange_delivery_date';
						$req_date_type[] = 'exchange_confirm_date';
						break;
				}

				$upd_arr = array();
				$upd_arr['status'] = $row['status'];
				$upd_arr['sync_'] = 0;

				if(is_array($req_date_type) && !empty($req_date_type)) {
					foreach($req_date_type as $val_date_type) {
						if($row[$val_date_type]) $upd_arr[$val_date_type] = $row[$val_date_type];
					}
				}

				$upd_query[] = $db->_query_print('UPDATE '.GD_MARKET_ORDER.' SET [cv] WHERE order_idx=[i]', $upd_arr, $arr['order_idx']);

			}
		}

		if(!empty($upd_query) && is_array($upd_query)) {
			foreach($upd_query as $row_query) {
				$res_upd = $db->query($row_query);
			}
		}

		if($res_upd) {
			$res_arr['order_idx'] = $arr['order_idx'];
			$res_arr['code'] = '000';
			$res_arr['msg'] = 'ó������';

		}
		else {
			$res_arr['order_idx'] = $arr['order_idx'];
			$res_arr['code'] = '899';
			$res_arr['msg'] = 'DB �Է½���';
		}
		echo $json->encode($res_arr);
		exit;

		break;

	case 'linkgoods' :
		$goods_no = $_POST['goods_no'];

		### ���� �������ڵ� START ###
		$grp_cd = Array("grp_cd"=>"ORIGIN");
		$arr_origin = $sAPI->getCode($grp_cd, 'hash');
		### ���� �������ڵ� END ###

		### �⺻��ǰ�˻� START ###
		$table = ''.GD_GOODS.' g ';
		$table .= 'left join '.GD_GOODS_BRAND.' b on g.brandno=b.sno';

		$field = 'g.goodsnm, g.origin, g.maker, g.tax, g.delivery_type, g.goods_delivery, g.keyword, g.shortdesc, g.longdesc, g.img_l, g.runout, g.optnm, g.goods_delivery, g.launchdt, g.model_name, g.manufacture_date';
		$field .= ', b.brandnm';

		$query = $db->_query_print('SELECT '.$field.' FROM '.$table.' WHERE g.goodsno=[s]', $goods_no);
		$result = $db->_select($query);
		$goods_data = $result[0];
		unset($query, $result);
		### �⺻��ǰ�˻� END ###

		### �⺻�ǸŰ� ���渵ũ üũ START ###
		$query = $db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] AND link=[s] and go_is_deleted <> \'1\' ', $goods_no, '1');
		$result = $db->_select($query);
		$tmp_price = $result[0]['price'];

		$link_date = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y'))).' '.date('H:i:s');//������ũ��
		if(!$_POST['price']) {
			$res['code'] = '905';
			$res['msg'] = '�Էµ� �ǸŰ��� �����ϴ�.';
			$res['goods_no'] = $goods_no;
			### ��ũ���и޼��� ###
			$json = new Services_JSON();
			echo $json->encode($res);
			exit;
		}
		if($tmp_price != $_POST['price']) {
			$option_price_update['price'] = $_POST['price'];
			$option_price_query = $db->_query_print('UPDATE '.GD_GOODS_OPTION.' SET [cv] WHERE goodsno=[s] AND link=[s]', $option_price_update, $goods_no, '1');
			$db->query($option_price_query);

			//��ǰ ���������� �������� �Ϻ� ����
			$goods_update_update['updatedt'] = $link_date;
		}
		### �⺻�ǸŰ� ���渵ũ üũ END ###

		### ��ǰ�ɼ� �˻� START ###
		$query = $db->_query_print('SELECT * FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\' AND go_is_display = \'1\' ORDER BY link DESC, go_sort ASC, sno ASC', $goods_no);
		$result = $db->_select($query);
		$arr_opt = $result;
		unset($query, $result);
		### ��ǰ�ɼ� �˻� END ###

		### �ʼ��Է°� üũ START ###
		//�ɼ��� ������ �ɼǸ��� ���� ��� ����ó��
		if($goods_data['optnm'] == '' && $arr_opt[0]['opt1'] != '') {
			$res['code'] = '905';
			$res['msg'] = '�ɼǸ��� �Է��Ͻ� �� �ٽ� �õ��� �ּ���.';
			$res['goods_no'] = $goods_no;
			### ��ũ���и޼��� ###
			$json = new Services_JSON();
			echo $json->encode($res);
			exit;
		}
		### �ʼ��Է°� üũ END ###

		$buy_price = $arr_opt[0]['supply'];//���԰�
		### ��ǰ�ɼ� ������ ���� START ###
		for($i = 0; $i < count($arr_opt); $i++) {
			foreach($opt_column as $key => $field) {
				if($key == 'price' && $arr_opt[$i]['opt1']) {
					$arr_opt[$i][$key] = $arr_opt[$i][$key] - $_POST['price'];
				}
				if($arr_opt[$i]['opt1'] == '' && $key != 'stock') unset($arr_opt[$i][$key]);
				$arr['option']['opt'.$i][$field] = $arr_opt[$i][$key];
			}
		}
		### ��ǰ�ɼ� ������ ���� END ###

		### ��ǰī�װ� �˻� START ###
		$cate_query = $db->_query_print('SELECT * FROM '.GD_GOODS_LINK.' WHERE goodsno=[s] order by length(category) DESC, sort DESC, sno ASC', $goods_no);
		$result = $db->_select($cate_query);
		$tmp_category = $result[0]['category'];
		unset($cate_query, $result);
		### ��ǰī�װ� �˻� END ###

		### ��ǰī�װ� ������ ���� START ###
		$cnt = strlen($tmp_category)/3;

		if($cnt > 0) {
			for($i = 0; $i < $cnt; $i++) {//�з��� ī�װ� �ڵ� ������
				$cate_query = $db->_query_print('SELECT catnm,category,sort,hidden FROM '.GD_CATEGORY.' WHERE category=[s]', substr($tmp_category, 0, 3+($i*3)));
				$result = $db->_select($cate_query);
				if(empty($result[0])) break;
				$arr_cate_info[] = $result[0];
			}

			if(empty($arr_cate_info)) {
				$res['code'] = '905';
				$res['msg'] = '��ǰ�з��� Ȯ���� �ּ���.';
				$res['goods_no'] = $goods_no;
				### ��ũ���и޼��� ###
				$json = new Services_JSON();
				echo $json->encode($res);
				exit;
			}

			for($j = 0; $j < count($arr_cate_info); $j++) {
				foreach($cate_column as $key => $field) {//����ī�װ�������
					if($key == 'hidden') {
						$arr_cate_info[$j][$key] = $cate_hidden[$arr_cate_info[$j][$key]];
					}
					$arr['goods_category']['cate'.$j][$field] = $arr_cate_info[$j][$key];
				}
				$arr['goods_category']['cate'.$j]['shop_cd'] = $godo['sno'];
			}
		}
		### ��ǰī�װ� ������ ���� END ###

		### �⺻ ��ǰ���� ������ ���� START ###
		foreach($column as $key=>$field) {//�⺻ ��ǰ����
			if($key == 'delivery_type') {//���Ÿ��
				if($goods_data[$key] == 0) {//�⺻�����å(����/�ĺ�)
					if(!$arr_delivery_type[$goods_data[$key]][$set['delivery']['deliveryType']]) {//��ۺ� ������ �ȵ����� ��� ����ó��
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '������ǰ ��ۺ� ���� �� �ٽ� �õ��� �ּ���.';
						$fail_msg['goods_no'] = $goods_no;
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					else if($set['delivery']['deliveryType'] == '����' && !$set['delivery']['default']) {
						//��ۺ��� ������� ���������� ��ϵ�(11���� ����)
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '�⺻���� > ���/�ù�� �������� ���� ��ۺ� ������ �ּ���.';
						$fail_msg['goods_no'] = $goods_no;
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					if($set['delivery']['deliveryType'] == '����') {
						$goods_data['goods_delivery'] = $set['delivery']['default'];
					}
					else {
						$goods_data['goods_delivery'] = $delivery_data['basic_payment_delivery_price'];
					}
					$goods_data[$key] = $arr_delivery_type[$goods_data[$key]][$set['delivery']['deliveryType']];
				}
				else {//������ۺ�, ���ҹ�ۺ�, ����
					if(!$arr_delivery_type[$goods_data[$key]]) {//��ۺ� ������ �ȵ����� ��� ����ó��
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '������ǰ ��ۺ� ���� �� �ٽ� �õ��� �ּ���.';
						$fail_msg['goods_no'] = $goods_no;
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					else if(($arr_delivery_type[$goods_data[$key]] != '1') &&!$_POST['delivery_price']) {
						//��ۺ��� ������� ���������� ��ϵ�(11���� ����)
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '��ۺ��� �����ϴ�.';
						$fail_msg['goods_no'] = $goods_no;
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					$goods_data[$key] = $arr_delivery_type[$goods_data[$key]];
				}
			}

			if($key == 'runout') $goods_data[$key] = $arr_runout_data[$goods_data[$key]];//�ǸŻ���

			if($key == 'origin') {//���� �������ڵ� ����
				$goods_origin = trim($goods_data[$key]);
				if($goods_origin == '�ѱ�' || $goods_origin == '����' || $goods_origin == '����' || $goods_origin == '������') $goods_origin = '���ѹα�';

				$origin_cd = array_search($goods_origin, $arr_origin);
				if(!$origin_cd) {
					$res['code'] = '905';
					$res['msg'] = '�������� ���ų� �´� �ڵ尡 �����ϴ�.';
					$res['goods_no'] = $goods_no;
					### ��ũ���и޼��� ###
					$json = new Services_JSON();
					echo $json->encode($res);
					exit;
				}
				$goods_data[$key] = $origin_cd;
			}

			if($key == 'longdesc') {//�󼼼��� �̹���url ����� => ������ ����
				$link_pregOld = array('/(href=.|src=.)(\/)/i');
				$link_pregNew = array('$1http://'.$_SERVER['HTTP_HOST'].'/');
				$goods_data[$key] = str_replace("\r\n", "", preg_replace($link_pregOld, $link_pregNew, $goods_data[$key] ));
			}

			if($key == 'supply') {//���԰�
				$goods_data[$key] = $arr_opt[0]['supply'];
			}

			if($key == 'tax') $goods_data[$key] = $arr_tax_data[$goods_data[$key]];//����/�����

			$arr['goods'][$field] = $goods_data[$key];
		}

		$tmp_img = explode('|', $goods_data['img_l']);//�̹���1~4
		for($i = 0; $i < 4; $i++) {
			if($tmp_img[$i] && !strstr($tmp_img[$i], 'http://')) {
				$tmp_img[$i] = 'http://'.$_SERVER['HTTP_HOST'].'/shop/data/goods/'.$tmp_img[$i];
			}
			$arr['goods']['img'.($i+1)] = $tmp_img[$i];
		}

		$tmp_opt = explode('|', $goods_data['optnm']);//�ɼǸ�
		$arr['goods']['opt_nm1'] = $tmp_opt[0];//�ɼǸ�1
		$arr['goods']['opt_nm2'] = $tmp_opt[1];//�ɼǸ�2

		$arr['goods']['delivery_price'] = $_POST['delivery_price'];//��ۺ�
		$arr['goods']['sale_price'] = $_POST['price'];//�ǸŰ�
		$arr['goods']['goods_cd_cust'] = $_POST['goods_no'];//�����ǰ�ڵ�(e���� ��ǰ�ڵ�)
		### �⺻ ��ǰ���� ������ ���� END ###

		### ��ũ���� ������ ���� START ###
		$arr['link']['mall_cd'] = $_POST['mall_cd'];//�����ڵ�
		$arr['link']['set_cd'] = $_POST['set_cd'];//��Ʈidx
		$arr['link']['mall_login_id'] = $_POST['mall_login_id'];//���Ͼ��̵�
		$arr['link']['mall_category_cd'] = $_POST['mall_category_cd'];//��ũī�װ��ڵ�
		### ��ũ���� ������ ���� END ###

		$len_arr = $sAPI->convertEncodeArr($arr, 'euc-kr', 'utf-8');
		$len_arr['link']['mall_category_nm'] = $_POST['mall_category_nm'];//��ũī�װ���
		$res = $sAPI->linkGoods($len_arr);

		if(count($res) < 2) {
			$res = $res[0];
		}
		$res['goods_no'] = $goods_no;

		### e���� ���̺� ������ ���� START ###
		if($res['code'] == '000') {
			$link_data['goodsno'] = $goods_no;//e���� ��ǰ ������ȣ
			$link_data['glink_idx'] = $res['glink_idx'];//���� ��ũidx
			$link_data['mall_cd'] = $_POST['mall_cd'];//��ũ �����ڵ�
			$link_data['mall_goods_cd'] = $res['mall_goods_cd'];//��ũ ���� ��ǰ�ڵ�
			$link_data['set_cd'] = $_POST['set_cd'];//��ũ ��Ʈ�ڵ�
			$link_data['sale_start_date'] = $res['sale_start_date'];//�ǸŽ�����
			$link_data['sale_end_date'] = $res['sale_end_date'];//�Ǹ�������
			$link_data['sale_status'] = '0001';//��ǰ�ǸŻ���
			$link_data['link_yn'] = 'Y';//��ũ��������
			$link_data['link_date'] = $link_date;

			$link_data_query = $db->_query_print('INSERT INTO '.GD_MARKET_GOODS.' SET [cv]', $link_data);
			$link_data_result = $db->query($link_data_query);

			### ��ۺ� �����Ͽ� ��ũ�� e���� ��ۺ� ���� START ###
			if($arr['goods']['delivery_price'] != $goods_data['goods_delivery']) {
				$arr_delivery_data['goods_delivery'] = $arr['goods']['delivery_price'];
				$delivery_update_query = $db->_query_print('UPDATE '.GD_GOODS.' SET [cv] WHERE goodsno=[s]', $arr_delivery_data, $goods_no);
				$db->query($delivery_update_query);
				$goods_update_update['updatedt'] = $link_date;
			}
			### ��ۺ� �����Ͽ� ��ũ�� e���� ��ۺ� ���� END ###

			if(!empty($goods_update_update['updatedt'])) {
				$goods_update_query = $db->_query_print('UPDATE '.GD_GOODS.' SET [cv] WHERE goodsno=[s]', $goods_update_update, $goods_no);
				$db->query($goods_update_query);
			}

			### �ǸŻ�ǰ ����ϱ⿡ ����ó�� START ###
			$domain_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'domain');
			$domain_res = $db->_select($domain_query);
			$selly_domain = $domain_res[0]['value'];

			$arr_data['goodsno'] = $goods_no;
			if($res['code'] === '000') $res['msg'] = '����';
			$arr_data['code'] = $res['code'];
			$arr_data['msg'] = $res['msg'];
			$arr_data['requrl'] = $selly_domain;
			$arr_data['regdt'] = date('Y-m-d H:i:s');
			$query = $db->_query_print('INSERT INTO '.GD_GOODS_STLOG.' SET [cv]', $arr_data);
			$db->query($query);
			### �ǸŻ�ǰ ����ϱ⿡ ����ó�� END ###

			if($link_data_result !== true) {
				$res['code'] = '399';
				$res['msg'] = '�����Ǿ����� e���� �����ڿ� ��ϵ��� �ʾҽ��ϴ�.';
				### ��ũ���и޼��� ###
				$json = new Services_JSON();
				echo $json->encode($res);
				exit;
			}
		### e���� ���̺� ������ ���� END ###
		}
		else if(!$res['code'] && !$res['msg']) {
			$res['code'] = '905';
			$res['msg'] = '������� �����ϴ�.';
			$res['goods_no'] = $goods_no;
			### ��ũ���и޼��� ###
			$json = new Services_JSON();
			echo $json->encode($res);
			exit;
		}

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'getcategory' :
		$search_data = $_POST;

		$get_category_data['mall_cd'] = $search_data['mall_cd'];
		$get_category_data['mall_login_id'] = $search_data['mall_login_id'];
		$get_category_data['category_type'] = $search_data['category_type'];
		$get_category_data['category_cd'] = $search_data['category_cd'];
		$xml_set_data = $sAPI->getMallCategory($get_category_data);
		$arr_category_data = $xml_set_data['data'][0]['child']['mallcategory_data'][0]['child']['item'];
		### ���� ī�װ�api END ###

		$arr_category[0]['category_type'] = $get_category_data['category_type'];
		for($i = 0; $i < count($arr_category_data); $i++) {
			$arr_category[$i]['category_cd'] = $arr_category_data[$i]['child']['category_cd'][0]['data'];
			$arr_category[$i]['category_nm'] = $arr_category_data[$i]['child']['category_nm'][0]['data'];
		}
		$json = new Services_JSON();
		echo $json->encode($arr_category);
		break;

	case 'getloginid' :
		$search_data = $_POST;
		break;

	case 'linkmidifygoods' :
		$link_data = $_POST;

		### ��ǰ������ �˻� START ###
		$table = GD_MARKET_GOODS.' m ';
		$table .= ' left join '.GD_GOODS.' g on m.goodsno=g.goodsno';
		$table .= ' left join '.GD_GOODS_BRAND.' b on g.brandno=b.sno';

		$field = 'g.goodsno, g.goodsnm, g.origin, g.maker, g.tax, g.delivery_type, g.keyword, g.shortdesc, g.longdesc, g.img_l, g.runout, g.optnm, g.goods_delivery, g.launchdt';
		$field .= ', b.brandnm';

		$query = $db->_query_print('SELECT '.$field.' FROM '.$table.' WHERE m.glink_idx=[s] AND m.link_yn=[s]', $link_data['glink_idx'], 'y');
		$result = $db->_select($query);
		$goods_data = $result[0];
		$goods_no = $goods_data['goodsno'];
		unset($goods_data['goodsno']);

		### ��ǰ������ �˻� END ###

		### ���� �������ڵ� START ###
		$grp_cd = Array("grp_cd"=>"ORIGIN");
		$arr_origin = $sAPI->getCode($grp_cd, 'hash');
		### ���� �������ڵ� END ###

		### �⺻�ǸŰ� ���渵ũ üũ START ###
		$query = $db->_query_print('SELECT price FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] AND link=[s] and go_is_deleted <> \'1\' ', $goods_no, '1');
		$result = $db->_select($query);
		$tmp_price = $result[0]['price'];

		$link_date = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y'))).' '.date('H:i:s');//������ũ��
		if($tmp_price != $_POST['price']) {
			$option_price_update['price'] = $_POST['price'];
			$option_price_query = $db->_query_print('UPDATE '.GD_GOODS_OPTION.' SET [cv] WHERE goodsno=[s] AND link=[s]', $option_price_update, $goods_no, '1');
			$db->query($option_price_query);

			//��ǰ ���������� �������� �Ϻλ���
			$goods_update_update['updatedt'] = $link_date;
		}
		### �⺻�ǸŰ� ���渵ũ üũ END ###

		### ��ǰ�ɼ� �˻� START ###
		$query = $db->_query_print('SELECT * FROM '.GD_GOODS_OPTION.' WHERE goodsno=[s] and go_is_deleted <> \'1\'  AND go_is_display = \'1\' ORDER BY link DESC, go_sort ASC, sno ASC', $goods_no);
		$result = $db->_select($query);
		$arr_opt = $result;
		unset($query, $result);
		### ��ǰ�ɼ� �˻� END ###

		### ��ǰ�ɼ� ������ ���� START ###
		for($i = 0; $i < count($arr_opt); $i++) {
			foreach($opt_column as $key => $field) {
				if($key == 'price') {
					$arr_opt[$i][$key] = $arr_opt[$i][$key] - $_POST['price'];
				}
				$arr['option']['opt'.$i][$field] = $arr_opt[$i][$key];
			}
		}
		### ��ǰ�ɼ� ������ ���� END ###

		### ��ǰī�װ� �˻� START ###
		$cate_query = $db->_query_print('SELECT * FROM '.GD_GOODS_LINK.' WHERE goodsno=[s] order by length(category) DESC, sort DESC, sno ASC', $goods_no);
		$result = $db->_select($cate_query);
		$tmp_category = $result[0]['category'];
		unset($cate_query, $result);
		### ��ǰī�װ� �˻� END ###

		### ��ǰī�װ� ������ ���� START ###
		$cnt = strlen($tmp_category)/3;

		if($cnt > 0) {
			for($i = 0; $i < $cnt; $i++) {//�з��� ī�װ� �ڵ� ������
				$cate_query = $db->_query_print('SELECT catnm,category,sort,hidden FROM '.GD_CATEGORY.' WHERE category=[s]', substr($tmp_category, 0, 3+($i*3)));
				$result = $db->_select($cate_query);
				$arr_cate_info[] = $result[0];
			}

			for($j = 0; $j < count($arr_cate_info); $j++) {
				foreach($cate_column as $key => $field) {//����ī�װ�������
					if($key == 'hidden') {
						$arr_cate_info[$j][$key] = $cate_hidden[$arr_cate_info[$j][$key]];
					}
					$arr['goods_category']['cate'.$j][$field] = $arr_cate_info[$j][$key];
				}
				$arr['goods_category']['cate'.$j]['shop_cd'] = $godo['sno'];
			}
		}
		### ��ǰī�װ� ������ ���� END ###

		### �⺻ ��ǰ���� ������ ���� START ###
		foreach($column as $key=>$field) {//�⺻ ��ǰ����
			if($key == 'delivery_type') {//���Ÿ��
				if($goods_data[$key] == 0) {//�⺻�����å(����/�ĺ�)
					if(!$arr_delivery_type[$goods_data[$key]][$set['delivery']['deliveryType']]) {//��ۺ� ������ �ȵ����� ��� ����ó��
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '������ǰ ��ۺ� ���� �� �ٽ� �õ��� �ּ���.';
						$fail_msg['glink_idx'] = $_POST['glink_idx'];
						$fail_msg['mode'] = 'modify';
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					else if($set['delivery']['deliveryType'] == '����' && !$set['delivery']['default']) {
						//��ۺ��� ������� ���������� ��ϵ�(11���� ����)
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '�⺻���� > ���/�ù�� �������� ���� ��ۺ� ������ �ּ���.';
						$fail_msg['glink_idx'] = $_POST['glink_idx'];
						$fail_msg['mode'] = 'modify';
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					if($set['delivery']['deliveryType'] == '����') {
						$goods_data['goods_delivery'] = $set['delivery']['default'];
					}
					else {
						$goods_data['goods_delivery'] = $delivery_data['basic_payment_delivery_price'];
					}
					$goods_data[$key] = $arr_delivery_type[$goods_data[$key]][$set['delivery']['deliveryType']];
				}
				else {//������ۺ�, ���ҹ�ۺ�, ����
					if(!$arr_delivery_type[$goods_data[$key]]) {//��ۺ� ������ �ȵ����� ��� ����ó��
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '������ǰ ��ۺ� ���� �� �ٽ� �õ��� �ּ���.';
						$fail_msg['glink_idx'] = $_POST['glink_idx'];
						$fail_msg['mode'] = 'modify';
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					else if(($arr_delivery_type[$goods_data[$key]] != '1') &&!$_POST['delivery_price']) {
						//��ۺ��� ������� ���������� ��ϵ�(11���� ����)
						$fail_msg['code'] = '905';
						$fail_msg['msg'] = '��ۺ��� �����ϴ�.';
						$fail_msg['glink_idx'] = $_POST['glink_idx'];
						$fail_msg['mode'] = 'modify';
						$json = new Services_JSON();
						echo $json->encode($fail_msg);
						exit;
					}
					$goods_data[$key] = $arr_delivery_type[$goods_data[$key]];
				}
			}

			if($key == 'runout') $goods_data[$key] = $arr_runout_data[$goods_data[$key]];//�ǸŻ���

			if($key == 'origin') {//���� �������ڵ� ����
				$goods_origin = trim($goods_data[$key]);
				if($goods_origin == '�ѱ�' || $goods_origin == '����' || $goods_origin == '����' || $goods_origin == '������') $goods_origin = '���ѹα�';
				$origin_cd = array_search($goods_origin, $arr_origin);
				if(!$origin_cd) {
					$res['code'] = '905';
					$res['msg'] = '�������� ���ų� �´� �ڵ尡 �����ϴ�.';
					$res['glink_idx'] = $_POST['glink_idx'];
					$res['mode'] = 'modify';
					### ��ũ���и޼��� ###
					$json = new Services_JSON();
					echo $json->encode($res);
					exit;
				}
				$goods_data[$key] = $origin_cd;
			}

			if($key == 'tax') $goods_data[$key] = $arr_tax_data[$goods_data[$key]];//����/�����

			if($key == 'longdesc') {//�󼼼��� �̹���url ����� => ������ ����
				$link_pregOld = array('/(href=.|src=.)(\/)/i');
				$link_pregNew = array('$1http://'.$_SERVER['HTTP_HOST'].'/');
				$goods_data[$key] = str_replace("\r\n", "", preg_replace($link_pregOld, $link_pregNew, $goods_data[$key] ));
			}

			$arr['goods'][$field] = $goods_data[$key];
		}

		$tmp_img = explode('|', $goods_data['img_l']);//�̹���1~4
		for($i = 0; $i < 4; $i++) {
			$arr['goods']['img'.($i+1)] = $tmp_img[$i];
		}

		$tmp_opt = explode('|', $goods_data['optnm']);//�ɼǸ�
		$arr['goods']['opt_nm1'] = $tmp_opt[0];//�ɼǸ�1
		$arr['goods']['opt_nm2'] = $tmp_opt[1];//�ɼǸ�2

		$arr['goods']['delivery_price'] = $_POST['delivery_price'];//��ۺ�
		$arr['goods']['sale_price'] = $_POST['price'];//�ǸŰ�
		$arr['goods']['goods_cd_cust'] = $goods_no;//�����ǰ�ڵ�(e���� ��ǰ�ڵ�)
		### �⺻ ��ǰ���� ������ ���� END ###

		### ��ũ���� ������ ���� START ###
		$arr['link']['glink_idx'] = $_POST['glink_idx'];//��ũ idx
		### ��ũ���� ������ ���� END ###

		$len_arr = $sAPI->convertEncodeArr($arr, 'euc-kr', 'utf-8');

		$res = $sAPI->linkModifyGoods($len_arr);

		if(count($res) < 2) {
			$res = $res[0];
		}

		if(!$res['code']) {
			$res['code'] = '905';
			$res['msg'] = '������� �����ϴ�.';
		}

		$res['mode'] = 'modify';
		### e���� ���̺� ������ ���� START ###
		if($res['code'] == '000') {
			$res['msg'] = '������ũ����';
			$link_result_data['link_date'] = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y'))).' '.date('H:i:s');//������ũ��
			$link_update_query = $db->_query_print('UPDATE '.GD_MARKET_GOODS.' SET [cv] WHERE glink_idx=[s]', $link_result_data, $res['glink_idx']);
			$link_update_result = $db->query($link_update_query);
			if(!$link_update_result) {
				$res['msg'] .= ' : e���� DB������Ʈ�� �����Ͽ����ϴ�.';
			}

			### ��ۺ� �����Ͽ� ��ũ�� e���� ��ۺ� ���� START ###
			if($arr['goods']['delivery_price'] != $goods_data['goods_delivery']) {
				$arr_delivery_data['goods_delivery'] = $arr['goods']['delivery_price'];
				$delivery_update_query = $db->_query_print('UPDATE '.GD_GOODS.' SET [cv] WHERE goodsno=[s]', $arr_delivery_data, $goods_no);
				$db->query($delivery_update_query);
				$goods_update_update['updatedt'] = $link_date;
			}
			### ��ۺ� �����Ͽ� ��ũ�� e���� ��ۺ� ���� END ###

			### ��ǰ ���������� ���� START ###
			if(!empty($goods_update_update['updatedt'])) {
				$goods_update_query = $db->_query_print('UPDATE '.GD_GOODS.' SET [cv] WHERE goodsno=[s]', $goods_update_update, $goods_no);
				$db->query($goods_update_query);
			}
			### ��ǰ ���������� ���� END ###
		}
		else {
			$res['glink_idx'] = $arr['link']['glink_idx'];
		}
		### e���� ���̺� ������ ���� END ###

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'linkgoodsstatus' :
		$status_data = $_POST;
		$res = $sAPI->linkGoodsStatus($status_data);

		if(count($res) < 2) {
			$res = $res[0];
		}

		if($res['code'] == '000') {
			$res['msg'] = '���º��漺��';
			$status_result_data['sale_status'] = $res['sale_status'];
			$status_update_query = $db->_query_print('UPDATE '.GD_MARKET_GOODS.' SET [cv] WHERE glink_idx=[s]', $status_result_data, $res['glink_idx']);
			$status_update_result = $db->query($status_update_query);
			if(!$status_update_result) {
				$res['msg'] .= ' : e���� DB������Ʈ�� �����Ͽ����ϴ�.';
			}
		}
		else {
			$res['glink_idx'] = $status_data['glink_idx'];
		}
		$res['mode'] = 'status';

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'linkgoodsextend' :
		$extend_data = $_POST;
		$res = $sAPI->linkGoodsExtend($extend_data);

		if(count($res) < 2) {
			$res = $res[0];
		}

		$glink_idx = $extend_data['glink_idx'];
		if($res['code'] == '000') {
			//�ǸűⰣ����
			$extend_result_data['sale_start_date'] = $res['sale_start_date'];
			$extend_result_data['sale_end_date'] = $res['sale_end_date'];
			if($res['sale_status']) $extend_result_data['sale_status'] = $res['sale_status'];

			$status_result_data['sale_status'] = $res['sale_status'];
			$status_update_query = $db->_query_print('UPDATE '.GD_MARKET_GOODS.' SET [cv] WHERE glink_idx=[s]', $extend_result_data, $glink_idx);
			$status_update_result = $db->query($status_update_query);
			if(!$status_update_result) {
				$res['msg'] .= ' : e���� DB������Ʈ�� �����Ͽ����ϴ�.';
			}
		}
		else {
			$res['glink_idx'] = $glink_idx;
		}
		$res['mode'] = 'extend';

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'checkmalllogin' :
		$check_mall_data = $_POST;

		$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
		$res_cust_cd = $db->_select($cust_cd_query);
		$cust_cd = $res_cust_cd[0]['value'];

		$sAPI = new sAPI();
		$check_mall_data['mall_login_pwd'] = base64_encode($sAPI->xcryptare($check_mall_data['mall_login_pwd'], $cust_cd, true));

		$res = $sAPI->checkMallLogin($check_mall_data);
		$res['reload'] = 'N';//���ΰ�ħ

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'insmall' :
		$mall_info_data = $_POST;

		$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
		$res_cust_cd = $db->_select($cust_cd_query);
		$cust_cd = $res_cust_cd[0]['value'];

		$mall_pwd = base64_encode($sAPI->xcryptare($mall_info_data['mall_login_pwd'], $cust_cd, true));
		$mall_info_data['mall_login_pwd'] = $mall_pwd;

		$res = $sAPI->insMall($mall_info_data);
		$res['reload'] = 'Y';//���ΰ�ħ

		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'deletemall' :
		$mall_delete_data = $_POST;

		$res = $sAPI->deleteMall($mall_delete_data);
		$json = new Services_JSON();
		echo $json->encode($res);
		break;

	case 'setdelete' :
		$set_cd = $_POST['set_cd'];

		$arr_data = Array('set_cd' => $set_cd);

		$res = $sAPI->delSetInfo($arr_data);
		$json = new Services_JSON();
		echo $json->encode($res);
		break;
}
?>
