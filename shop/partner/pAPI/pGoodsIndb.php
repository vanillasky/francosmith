<?
/*********************************************************
* ���ϸ�     :  pGoodsIndb.php
* ���α׷��� :	pad ��ǰ ó�� API (���,����,����)
* �ۼ���     :  dn
* ������     :  2011.10.22
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/upload.lib.php";
require_once "../../lib/load.class.php";
require_once "../../lib/qrcode.class.php";
require_once "../../lib/json.class.php";

$Goods = Core::loader('Goods');
$upload = new upload_file;
$pAPI = new pAPI();
$json = new Services_JSON(16);

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!($user_name = $pAPI->keyCheck($_POST['authentic']))) {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### ����Ű Check �� ###

$_POST = $json->decode(str_replace("\\\"", "'", stripslashes($_POST['content'])));

$mode = $_POST['mode'];

unset($_POST['mode']);
if(!$mode) {
	$res_data['result']['code'] = '301';
	$res_data['result']['msg'] = 'ó�� mode �� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

foreach($_POST as $key=>$val) {
	if(strstr($key, 'arr_')) {
		$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
	}
	else  {
		$tmp_arr[$key] = $val;
	}
}
unset($_POST);
$_POST = $tmp_arr;

$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
if (!is_file($file)) {
	$res_data['result']['code'] = '300';
	$res_data['result']['msg'] = '�� ���������� �����ϴ�. ���������� ����ϼ���';
	echo ($json->encode($res_data));
	exit;
}

$file	= file($file);
$godo	= decode($file[1],1);

function delGoodsImg($str)
{
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		if ($v == '') continue;

		if (is_file($_dir.$v)) @unlink($_dir.$v);
		if (is_file($_dirT.$v)) @unlink($_dirT.$v);
	}
}

function delGoods($goodsno){

	global $db;
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
	delGoodsImg($data[img_i]);
	delGoodsImg($data[img_l]);
	delGoodsImg($data[img_m]);
	delGoodsImg($data[img_s]);

	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");

	### ���̹� ���ļ��� ��ǰ����
	naver_goods_runout($goodsno);

	### ��α׼� ��ǰ����
	include_once("../../lib/blogshop.class.php");
	$blogshop = new blogshop();
	$blogshop->delete_goods($goodsno);

	### �����뷮 ���
	setDu('goods');
}


switch($mode) {
	case 'register' :
	case 'modify' :

		$goodsno = $_POST['goodsno'];

		### �ʼ� �� check START ###
		$chk_required = true;
		$arr_required = array();

		if(empty($_POST['category'])) {
			$chk_required = false;
			$arr_required[] = 'ī�װ�';
		}

		if(trim($_POST['goodsnm']) == '') {
			$chk_required = false;
			$arr_required[] = '��ǰ��';
		}

		if($_POST['tax'] == '') {
			$chk_required = false;
			$arr_required[] = '��������';
		}

		if(!$chk_required) {
			$res_data['result']['code'] = '301';
			$str_required = implode(', ', $arr_required);
			$res_data['result']['msg'] = '�ʼ� �Է°��� �����ϴ�. '.$str_required;
			echo ($json->encode($res_data));
			exit;
		}

		### �ʼ� �� check END ###

		### �⺻�� setting ###
		if(empty($_POST['optnm'])) {
			$_POST['optnm'][0] = '';
			$_POST['optnm'][1] = '';
		}

		if(empty($_POST['opttype'])) {
			$_POST['opttype'] = 'single';
		}

		if(empty($_POST['option'])) {
			$tmp_option['opt1'][0] = '';
			$tmp_option['opt2'][0] = '';
			$tmp_option['price'][0] = '';
			$tmp_option['consumer'][0] = '';
			$tmp_option['supply'][0] = '';
			$tmp_option['reserve'][0] = '';
			$tmp_option['stock'][0] = '';
			$tmp_option['optno'][0] = '';

			$_POST['option'] = $tmp_option;
			unset($tmp_option);
		}

		if($mode == 'register') {
			### ��ϼ� ���� üũ
			$chk_query = $db->_query_print('SELECT count(*) cnt_goods FROM '.GD_GOODS.' WHERE 1=1');
			$res_chk = $db->_select($chk_query);
			$cnt_goods = $res_chk[0]['cnt_goods'];

			if ($godo['maxGoods']!='unlimited' && $godo['maxGoods']<=$cnt_goods){
				$res_data['result']['code'] = '309';
				$res_data['result']['msg'] = '��ǰ�� ����� ���ѵǾ����ϴ�.';
				echo ($json->encode($res_data));
				exit;
			}

			$ins_query = $db->_query_print('INSERT INTO '.GD_GOODS.' SET regdt=now()');
			$db->query($ins_query);
			$goodsno = $db->_last_insert_id();

			### shoptouch ��ǰ ���� insert ###
			$ins_query_shoptouch = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_GOODS.' SET goodsno=[i], sregdt=now()', $goodsno);
			$db->query($ins_query_shoptouch);


			if ($_POST['category']) {
				foreach ($_POST['category'] as $category){
					$hidden = getCateHideCnt($category) > 0 ? 1 : 0;

					$arr_cate_ins = array();
					$arr_cate_ins['goodsno'] = $goodsno;
					$arr_cate_ins['category'] = $category;
					$arr_cate_ins['hidden'] = $hidden;

					$cate_ins_query = $db->_query_print('INSERT INTO '.GD_GOODS_LINK.' SET [cv], sort=unix_timestamp()', $arr_cate_ins);
					$db->query($cate_ins_query);
					unset($arr_cate_ins);
				}
			}
		}
		else if($mode == 'modify') {

			### ī�װ� ����
			$p_category = $n_category = array();

			$n_category = $_POST['category'];
			$cate_query = $db->_query_print('SELECT category FROM '.GD_GOODS_LINK.' WHERE goodsno=[i]', $goodsno);
			$res_cate = $db->_select($cate_query);

			if(!empty($res_cate) && is_array($res_cate)) {
				foreach($res_cate as $row_cate) {
					$p_category[] = $row_cate['category'];
				}
			}

			$add = @array_diff($n_category,$p_category);
			$del = @array_diff($p_category,$n_category);
			$mod = @array_diff($n_category,$add);

			if ($add) {
				foreach ($add as $k=>$v){
					$hidden = getCateHideCnt($v) > 0 ? 1 : 0;

					$arr_cate_ins = array();
					$arr_cate_ins['goodsno'] = $goodsno;
					$arr_cate_ins['category'] = $v;
					$arr_cate_ins['hidden'] = $hidden;
					$arr_cate_ins['sort'] = -(int)$_POST['sort'][$k];

					$cate_ins_query = $db->_query_print('INSERT INTO '.GD_GOODS_LINK.' SET [cv]', $arr_cate_ins);

					$db->query($cate_ins_query);
					unset($arr_cate_ins);
				}
			}

			if($del) {
				foreach ($del as $k=>$v){
					$cate_del_query = $db->_query_print('DELETE FROM '.GD_GOODS_LINK.' WHERE goodsno=[i] AND category=[s]', $goodsno, $v);
					$db->query($cate_del_query);
				}
			}

			if($mod) {
				foreach ($mod as $k=>$v){
					$cate_mod_query = $db->_query_print('UPDATE '.GD_GOODS_LINK.' SET sort=[i], WHERE goodsno=[i] AND category=[s]', -(int)$_POST['sort']['$k'], $goodsno, $v);
					$db->query($cate_mod_query);
				}
			}

			$goods_query = $db->_query_print('SELECT * FROM '.GD_GOODS.' WHERE goodsno=[i]', $goodsno);
			$res_goods = $db->_select($goods_query);
			$data = $res_goods[0];

		}

		### QR �ڵ� ���� ###
		$del_qr_query = $db->_query_print('DELETE FROM '.GD_QRCODE.' WHERE qr_type=[s] AND contsNo=[i]', 'goods', $goodsno);
		if($_POST['qrcode'] == 'y') {

			$arr_ins_qr = array();
			$arr_ins_qr['qr_type'] = 'goods';
			$arr_ins_qr['contsNo'] = $goodsno;
			$arr_ins_qr['qr_string'] = '';
			$arr_ins_qr['qr_name'] = 'event qr code';
			$arr_ins_qr['qr_size'] = '';
			$arr_ins_qr['useLogo'] = '';

			$ins_qr_query = $db->_query_print('INSERT INTO '.GD_QRCODE.' [cv], regdt=now()', $arr_ins_qr);
			$db->query($ins_qr_query);
		}

		if(!empty($_POST['img_shoptouch']) && is_array($_POST['img_shoptouch'])) {
			foreach($_POST['img_shoptouch'] as $img_shoptouch) {

				$img_chk = getimagesize($img_shoptouch);

				if($img_chk[2]) {

					### �̹��� ���� ###
					$url_stuff = parse_url($img_shoptouch);
					$port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;

					$fp = fsockopen($url_stuff['host'], $port);

					$query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
					$query .= 'Host: ' . $url_stuff['host'];
					$query .= "\n\n";

					@fwrite($fp, $query);

					while ($tmp = fread($fp, 1024))
					{
						$buffer .= $tmp;
					}

					preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);

					$img_content = substr($buffer, - $parts[1]);
					@fclose($fp);

					$ext_idx = strrpos($img_shoptouch, '.');
					$ext = '';
					if ($ext_idx !== false) $ext = '.'.substr($img_url, $ext_idx + 1);

					### �̹��� Ȯ���ڰ� ���� ��� �̹��� Ÿ������ Ȯ���� �Է�
					if(strtoLower($ext) != '.gif' || strtoLower($ext) != '.jpg' || strtoLower($ext) != '.jpeg' || strtoLower($ext) != '.png' || strtoLower($ext) != '.bmp') {
						$arr_img_type = getimagesize($img_shoptouch);

						switch ($arr_img_type[2]) {
							case 1 :
								$ext = '.gif';
								break;
							case 2 :
								$ext = '.jpg';
								break;
							case 3 :
								$ext = '.png';
								break;
							case 6 :
								$ext = '.bmp';
								break;
							default :
								$ext = '';
								break;
						}
					}

					### �̹��� Ȯ���ڰ� ���� ��� �̹��� ������ ó��
					if(!$ext) {
						$img_yn='N';
					}

					$img_path = '../../data/goods/';
					if (!is_dir($img_path)) {
						@mkdir($img_path);
						@chmod($img_path, 0707);
					}

					$_POST['img_i'] = time().'i0'.$ext;
					$img_i = $img_path.$_POST['img_i'];

					$w_fh = @fopen($img_i, 'w');
					@fwrite($w_fh, $img_content);
					@chmod($img_i, 0707);
					@fclose($w_fh);

					thumbnail($img_i, $img_path.'t/'.$_POST['img_i'], 45);

					### �̹��� ������¡ ###
					$arr_img_key = Array('img_l', 'img_m', 'img_s', 'img_i');
					foreach($arr_img_key as $val_img_key) {
						if($val_img_key != 'img_i') {
							$_POST[$val_img_key] = time()."_".substr($val_img_key,-1,1)."_0".$ext;
							thumbnail($img_i, $img_path.$_POST[$val_img_key], $cfg[$val_img_key]);

							if($val_img_key != 'img_s') {
								copy($img_path.'t/'.$_POST['img_i'], $img_path.'t/'.$_POST[$val_img_key]);
							}
						}
					}

					break;
				}
			}
		}

		### ���̹� ���ļ��� ��ǰ����
		naver_goods_diff_check();

		$ar_naver_diff=array();

		$ar_update=array(
			'goodsnm'=>$_POST[goodsnm],
			'brandno'=>$_POST[brandno],
			'origin'=>$_POST[origin],
			'maker'=>$_POST[maker],
			'launchdt'=>date("Y-m-d",strtotime($_POST[launchdt])),
			'delivery_type'=>$_POST[delivery_type],
			'goods_delivery'=>$_POST[goods_delivery],
			'use_emoney'=>$_POST[use_emoney],
			'price'=>$_POST[option][price][0],
			'reserve'=>$_POST[option][reserve][0],
			'img_m'=> ($_POST['image_attach_method'] == 'url') ? $_POST[url_m][0] : $file[img_m][name][0],	// url ���� �Է��϶� ó�� �߰�.
		);
		if($p_category[0]!=$n_category[0]) $ar_update['category']=$n_category[0];

		if ($_POST[mode]=="modify"){
			naver_goods_diff($goodsno,$ar_update);
		}

		### useEx
		if($_POST[useEx] == 0) unset($_POST[ex]);

		### useAdd
		if($_POST[useAdd] == 0) unset($_POST[addoptnm]);

		### qrcode defalut
		if(!$_POST[qrcode]) $_POST[qrcode] = "n";

		### �ʼ��ɼ�

		//$optnm = @implode("|",array_notnull($_POST[optnm]));
		$optnm = $_POST['opt1kind'];
		if($_POST['opt2kind']) $optnm .= '|'.$_POST['opt2kind'];

		if($_POST['opt1kind']) $_POST['opt1kind'] = 'img';
		if($_POST['opt2kind']) $_POST['opt2kind'] = 'img';

		$idx = -1; $link[0] = 1;
		$db->query("delete from ".GD_GOODS_OPTION." where goodsno=$goodsno");
		$cnt = count($_POST[option][opt2]);

		$totstock = 0;

		$chk_cnt = 0;

		$tmp_option1 = Array();

		$tmp_option1 = $_POST[option];


		$tmp_option2 = Array();
		$tmp_opt1 = Array();
		$tmp_opt2 = Array();

		$tmp_opt1 = @array_values(@array_unique($_POST[option][opt1]));
		$tmp_opt2 = @array_values(@array_unique($_POST[option][opt2]));

		unset($_POST[option]);

		$tmp_idx = 0;
		for($t1_i = 0; $t1_i < count($tmp_opt1); $t1_i++) {

			for($t2_i = 0; $t2_i<count($tmp_opt2); $t2_i++) {

				$tmp_option2[opt1][] = $tmp_opt1[$t1_i];
				$tmp_option2[opt2][] = $tmp_opt2[$t2_i];
			}
		}

		if(empty($tmp_option2)) {
			$_POST[option] = $tmp_option1;
		}
		else {

			for($to2_i=0; $to2_i < count($tmp_option2[opt1]); $to2_i++) {

				for($to1_i=0; $to1_i < count($tmp_option1[opt1]); $to1_i++) {

					if(($tmp_option2[opt1][$to2_i] == $tmp_option1[opt1][$to1_i]) && ($tmp_option2[opt2][$to2_i] == $tmp_option1[opt2][$to1_i])) {

						$tmp_option2[stock][$to2_i] = $tmp_option1[stock][$to1_i];

						$tmp_option2[price][$to2_i] =$tmp_option1[price][$to1_i];
						$tmp_option2[consumer][$to2_i] = $tmp_option1[consumer][$to1_i];
						$tmp_option2[supply][$to2_i] = $tmp_option1[supply][$to1_i];
						$tmp_option2[reserve][$to2_i] = $tmp_option1[reserve][$to1_i];
					}
				}

				if(empty($tmp_option2[stock][$to2_i]) && empty($tmp_option2[price][$to2_i]) && empty($tmp_option2[consumer][$to2_i]) && empty($tmp_option2[supply][$to2_i]) && empty($tmp_option2[reserve][$to2_i])) {
					for($to1_i=0; $to1_i < count($tmp_option1[opt1]); $to1_i++) {

						if(($tmp_option2[opt1][$to2_i] == $tmp_option1[opt1][$to1_i])) {

							$tmp_option2[stock][$to2_i] = 0;

							$tmp_option2[price][$to2_i] =$tmp_option1[price][$to1_i];
							$tmp_option2[consumer][$to2_i] = $tmp_option1[consumer][$to1_i];
							$tmp_option2[supply][$to2_i] = $tmp_option1[supply][$to1_i];
							$tmp_option2[reserve][$to2_i] = $tmp_option1[reserve][$to1_i];

						}
					}
				}
			}

			$_POST[option] = $tmp_option2;
		}

		if(empty($_POST[option])) $_POST[option] = $tmp_option1;

		foreach ($_POST[option][stock] as $k=>$v){
			$idx++;
			if($_POST[option][opt1][$idx] || count($_POST[option][stock]) == 1) {
				$key = (int)($idx/$cnt);
				$opt1 = str_replace("'","��",$_POST[option][opt1][$idx]);
				$opt2 = str_replace("'","��",$_POST[option][opt2][$idx]);

				if(trim($opt1) == '�ɼǸ�1') $opt1='';
				if(trim($opt2) == '�ɼǸ�2') $opt2='';
				if(trim($v) == '���' || trim($v) == '��� �� ��� �Է�') $v='';

				$price = trim(str_replace(",","",$_POST[option][price][$idx]));
				$consumer = trim(str_replace(",","",$_POST[option][consumer][$idx]));
				$supply = trim(str_replace(",","",$_POST[option][supply][$idx]));
				$reserve = trim(str_replace(",","",$_POST[option][reserve][$idx]));

				if($_POST['opt1kind'] == 'img') $icon1 = $file['opticon_a']['name'];
				else	$icon1 = $_POST['opt1icon'];
				if($_POST['opt2kind'] == 'img') $icon2 = $file['opticon_b']['name'];
				else	$icon2 = $_POST['opt2icon'];

				$query = "
				insert into ".GD_GOODS_OPTION." set
					goodsno = '$goodsno',
					opt1	= '$opt1',
					opt2	= '$opt2',
					price	= '$price',
					consumer= '$consumer',
					supply	= '$supply',
					reserve	= '$reserve',
					stock	= '$v',
					opt1img	= '".$file['opt1img']['name'][$idx]."',
					opt1icon = '".$icon1[$idx]."',
					opt2icon = '".$icon2[$idx]."',
					link	= '$link[$idx]',
					pchsno	= '".$_POST['pchsno']."'
				";
				$db->query($query);
				$totstock += $v;

				# �ɼ��ڵ�
				ob_start();
				$sno = $db->lastID();
				if ($_POST[option][optno][$k] == '') $optno = $sno;
				else $optno = $_POST[option][optno][$k];
				$db->query("update ".GD_GOODS_OPTION." set optno='{$optno}' where sno='{$sno}'");
				ob_end_clean();

				$chk_cnt ++;
			}
		}

		### �߰��ɼ�
		$idx = 0;
		$addoptnm = array_notnull($_POST[addoptnm]);
		$db->query("UPDATE ".GD_GOODS_ADD." SET stats = 0 WHERE goodsno = $goodsno");
		if ($addoptnm){
			foreach ($addoptnm as $k=>$v){
				$isInsert = 0;
				$_addopt_keys = array_keys($_POST[addopt][opt][$k]);

				for ($i=0,$m=sizeof($_addopt_keys);$i<$m;$i++) {

					$_opt		= isset($_POST[addopt][opt][$k][$i]) ? $_POST[addopt][opt][$k][$i] : '';
					$_sno		= isset($_POST[addopt][sno][$k][$i]) ? $_POST[addopt][sno][$k][$i] : '';
					$_addprice	= isset($_POST[addopt][addprice][$k][$i]) ? $_POST[addopt][addprice][$k][$i] : '';

					if ($_opt == '') continue;

					// ����
					if ($_sno) {
						$query = "
						UPDATE ".GD_GOODS_ADD." SET
							step	= '$idx', opt		= '$_opt', addprice= '$_addprice', stats	= 1
						WHERE sno = $_sno
						";
					}
					// ���
					else {
						$query = "
						INSERT INTO ".GD_GOODS_ADD." SET
							goodsno	= '$goodsno', step	= '$idx', opt		= '$_opt', addprice= '$_addprice', stats	= 1
						";
					}
					$rs = $db->query($query);
					if ($rs) $isInsert = 1;
				}

				if ($isInsert){
					$tmp[] = $v."^".$_POST[addoptreq][$k];
					$idx++;
				}

			}
		}
		$db->query("DELETE FROM ".GD_GOODS_ADD." WHERE stats = 0 AND goodsno = $goodsno");
		$addoptnm = @implode("|",$tmp);

		$icon = @array_sum($_POST[icon]);

		### �߰��ʵ�
		if($_POST['useEx']) $ex_title = preg_replace("/^(\|)+$/i","",@implode("|",$_POST[title]));

		### ��������
		$_POST[goodsnm] = trim($_POST[goodsnm]);

		### mysql 5.0 �Է°����� 0000-00-00���θ� ���¹���
		if($_POST[launchdt]) $_POST[launchdt] = "'$_POST[launchdt]'";
		else $_POST[launchdt] = "null";

		###
		if($_POST[delivery_type] == '3') $_POST[goods_delivery];

		### �ɼǾ����� �� �ɼǻ�ǰ�̹��� ����(���̾����������)
		//deloptimg();

		if($cfgMobileShop['vtype_goods']=='0') $_POST[open_mobile] = $_POST[open];

		$_POST['longdesc'] = urldecode($_POST['longdesc']);
		### ��ǰ ����Ÿ ����
		$query = "
		update ".GD_GOODS." set
			goodsnm			= '$_POST[goodsnm]',
			meta_title		= '$_POST[meta_title]',
			goodscd			= '$_POST[goodscd]',
			maker			= '$_POST[maker]',
			origin			= '$_POST[origin]',
			brandno			= '$_POST[brandno]',
			icon			= '$icon',
			open			= '$_POST[open]',
			open_mobile		= '$_POST[open_mobile]',
			runout			= '$_POST[runout]',
			delivery_type	= '$_POST[delivery_type]',
			goods_delivery	= '$_POST[goods_delivery]',
			keyword			= '$_POST[keyword]',
			strprice		= '$_POST[strprice]',
			tax				= '$_POST[tax]',
			shortdesc		= '$_POST[shortdesc]',
			longdesc		= '$_POST[longdesc]',
			mlongdesc		= '$_POST[mlongdesc]',
			img_i			= '$_POST[img_i]',
			img_s			= '$_POST[img_s]',
			img_m			= '$_POST[img_m]',
			img_l			= '$_POST[img_l]',
			img_mobile		= '',
			optnm			= '$optnm',
			opttype			= '$_POST[opttype]',
			opt1kind			= '$_POST[opt1kind]',
			opt2kind			= '$_POST[opt2kind]',
			use_emoney		= '$_POST[use_emoney]',
			addoptnm		= '$addoptnm',
			memo			= '$_POST[memo]',
			ex_title		= '$ex_title',
			ex1				= '{$_POST[ex][0]}',
			ex2				= '{$_POST[ex][1]}',
			ex3				= '{$_POST[ex][2]}',
			ex4				= '{$_POST[ex][3]}',
			ex5				= '{$_POST[ex][4]}',
			ex6				= '{$_POST[ex][5]}',
			relationis		= '$_POST[relationis]',
			relation		= '$relation',
			usestock		= '$_POST[usestock]',
			totstock		= '$totstock',
			launchdt		= $_POST[launchdt],
			min_ea			= '$_POST[min_ea]',
			max_ea			= '$_POST[max_ea]',
			useblog			= '$_POST[useblog]',
			detailView		= '$_POST[detailView]',
			use_stocked_noti= '$_POST[use_stocked_noti]'
		where
			goodsno = '$goodsno'
		";


		$db->query($query);


		### ������Ʈ �Ͻ�
		$Goods -> update_date($goodsno);

		### ����ġ ����Ÿ ����
		$query = "
		update ".GD_SHOPTOUCH_GOODS." set
			open_shoptouch	= '$_POST[open_shoptouch]',
			img_shoptouch	= '".@implode("|",$_POST[img_shoptouch])."',
			slongdesc		= '$_POST[longdesc]',
			supddt = now()
		where
			goodsno = '$goodsno'
		";
		$db->query($query);

		### ���̹� ���ļ��� ��ǰ����
		if($_POST[mode]=="register")
		{
			naver_goods_diff($goodsno,array(),"I");
		}

		### �����뷮 ���
		setDu('goods');

		### �̺�Ʈ ī�װ� ����
		$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='$goodsno'");
		$i=0;
		while($tmp = $db->fetch($res)){
			$mode = "e".$tmp['sno'];
			list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'");
			if($cnt == 0){
				list($sort) = $db->fetch("select max(sort) from ".GD_GOODS_DISPLAY." where mode = '$mode'");
				$sort++;
				$query = "
				insert into ".GD_GOODS_DISPLAY." set
					goodsno		= '".$goodsno."',
					mode		= '$mode',
					sort		= '$sort'
				";
				$db->query($query);
			}
		}

		$res_data['result']['code'] = '000';
		$res_data['result']['msg'] = '����:��ǰ�ڵ�'.$goodsno;
		$res_data['goodsno'] = $goodsno;
		break;

	case 'delete' :
		if(!$_POST['goodsno']) {
			$res_data['result']['code'] = '301';
			$res_data['result']['msg'] = 'goodsno���� �����ϴ�';
			break;
		}

		delGoods($_POST['goodsno']);
		$res_data['result']['code'] = '000';
		$res_data['result']['msg'] = '����';
		break;
}

echo ($json->encode($res_data));
exit;

?>
