<?php
include "../lib.php";
require_once("../../lib/qfile.class.php");
$qfile = new qfile();


// 쿠폰번호 자동생성
function create_coupon_number($sno,$num){
	$key = rand(11,55).sprintf('%010d',$sno).rand(55,99).sprintf('%010d',$num);
	$key = str_split($key,12);
	$key = strtoupper(base_convert($key[0], 10, 34).base_convert($key[1], 10, 34));
	$key = str_split($key,4);
	return $key;
}

// 쿠폰번호 저장
function create_paper($number_type,$limit_paper,$publish_limit,$coupon_sno,$number,$arFile){
	$db = Core::loader('db');
	$values_query = '';
	$db_name = $db->db_name;

	$data = $db->_select("SHOW TABLE STATUS FROM $db_name LIKE 'gd_offline_paper'");
	$paperSno = $data[0]['Auto_increment'];
	if($number_type == 'duplication'){
		$paperNum = create_coupon_number($coupon_sno,$paperSno);
		$paperNum = implode('-',$paperNum);
		$query = "insert into gd_offline_paper (coupon_sno,number) values ('$coupon_sno','$paperNum')";
		if(!$db->query($query)) return false;
	}else if($number_type == 'auto' && $limit_paper > 0){
		for($i=1;$i<=$limit_paper;$i++){
			$paperNum = create_coupon_number($coupon_sno,$paperSno);
			$paperNum = implode('-',$paperNum);
			$values_query .= ",('$coupon_sno','$paperNum')";
			if($i%100 == 0 || $i == $limit_paper){
				$query = "insert into gd_offline_paper (coupon_sno,number) values ".substr($values_query,1);
				if(!$db->query($query)) return false;
				$values_query='';
			}
			$paperSno++;
		}
	}
	return true;
}

// 쿠폰적용상품등록
function create_goods_coupon($arInsertGoods,$arInsertCategory,$coupon_sno){
	$db = Core::loader('db');
	if(count($arInsertGoods)){
		foreach($arInsertGoods as $v){
			$v['coupon_sno'] = $coupon_sno;
			$query = $db->_query_print("insert into gd_offline_goods set [cv]", $v);
			if(!$db->query($query))return false;
		}
	}
	if(count($arInsertCategory)){
		foreach($arInsertCategory as $v){
			$v['coupon_sno'] = $coupon_sno;
			$query = $db->_query_print("insert into gd_offline_goods set [cv]", $v);
			if(!$db->query($query))return false;
		}
	}
	return true;
}

if (get_magic_quotes_gpc()) {
    stripslashes_all($_POST);
    stripslashes_all($_GET);
}

$mode = ($_POST['mode'])?$_POST['mode']:$_GET['mode'];
if($mode=='register'){
	if($limit_paper > 10000){
		msg('쿠폰생성은 10,000개 까지 가능합니다.',-1);
	}

	if($publish_limit == 'limited' && $limit_paper < 1){
		msg('생성할 쿠폰 수량이 없습니다.',-1);
	}

	$limit_paper = 0;
    $publish_limit = $_POST['publish_limit'];

    if( $_POST['number_type'] == 'duplication' && $_POST['duplication_limit_paper'] > 0 && $publish_limit=='limited'){
		$limit_paper = (int) $_POST['duplication_limit_paper'];
    }else if($_POST['number_type'] == 'auto' && $_POST['auto_limit_paper'] > 0){
		$limit_paper = (int) $_POST['auto_limit_paper'];
		$publish_limit = "limited";
    }
}

if(in_array($mode,array('register','modify'))){

	// 파라미터 검증
	$arInsertCoupon = array(
		'coupon_name'=>$_POST['coupon_name'],
		'start_year'=>$_POST['start_year'],
		'start_mon'=>$_POST['start_mon'],
		'start_day'=>$_POST['start_day'],
		'start_time'=>$_POST['start_time'],
		'end_year'=>$_POST['end_year'],
		'end_mon'=>$_POST['end_mon'],
		'end_day'=>$_POST['end_day'],
		'end_time'=>$_POST['end_time'],
		'coupon_type'=>$_POST['coupon_type'],
		'coupon_price'=>$_POST['coupon_price'],
		'currency'=>$_POST['currency'],
		'pay_method'=>$_POST['pay_method'],
		'pay_limit'=>$_POST['pay_limit'],
		'limit_amount'=>$_POST['limit_amount'],
		'goods_apply'=>$_POST['goods_apply']
	);

	if($mode=='register'){
		$arInsertCoupon['number_type'] = $_POST['number_type'];
		$arInsertCoupon['publish_limit'] = $publish_limit;
		$arInsertCoupon['limit_paper'] = $limit_paper;
	}

	$validationCheck = array(
		'coupon_name'=>array('require'=>true,'max_length'=>30),
		'start_year'=>array('require'=>true,'pattern'=>'/^[0-9]{4}$/'),
		'start_mon'=>array('require'=>true,'pattern'=>'/^[0-9]{2}$/'),
		'start_day'=>array('require'=>true,'pattern'=>'/^[0-9]{2}$/'),
		'start_time'=>array('require'=>true,'pattern'=>'/^[0-9]{2}$/'),
		'end_year'=>array('require'=>true,'pattern'=>'/^[0-9]{4}$/'),
		'end_day'=>array('require'=>true,'pattern'=>'/^[0-9]{2}$/'),
		'end_time'=>array('require'=>true,'pattern'=>'/^[0-9]{2}$/'),
		'coupon_type'=>array('require'=>true,'array'=>array('sale', 'save')),
		'coupon_price'=>array('require'=>true,'type'=>'int','max_byte'=>10),
		'currency'=>array('require'=>true,'array'=>array('원', '%')),
		'pay_method'=>array('require'=>true,'array'=>array('unlimited', 'cash')),
		'limit_amount'=>array('type'=>'int','max_byte'=>10),
		'goods_apply'=>array('require'=>true,'array'=>array('all', 'limited'))
	);

	$chkResult = array_value_cheking($validationCheck,$arInsertCoupon);
	if(count($chkResult)) msg('입력값이 올바르지 않습니다.',-1);

	if($arInsertCoupon['goods_apply'] == 'limited'){
		if(count($_POST['e_goods'])){
			foreach($_POST['e_goods'] as $k => $goods){
				$arInsertGoods[$k] = array('goodsno'=>$goods,'coupon_sno'=>$coupon_sno);
				$validationCheck = array('goodsno'=>array('require'=>true,'type'=>'int','max_byte'=>10));
				$chkResult = array_value_cheking($validationCheck,$arInsertGoods[$k]);
				if(count($chkResult)) msg('적용상품이 올바르지 않습니다.',-1);
			}
		}
		if(count($_POST['e_category'])){
			foreach($_POST['e_category'] as $k => $category){
				$arInsertCategory[$k] = array('category'=>$category,'coupon_sno'=>$coupon_sno);
				$validationCheck = array('category'=>array('require'=>true,'type'=>'int','max_byte'=>12));
				$chkResult = array_value_cheking($validationCheck,$arInsertCategory[$k]);
				if(count($chkResult)) msg('적용 카테고리가 올바르지 않습니다.',-1);
			}
		}
	}

}

switch($mode){
    case "register" : //쿠폰 생성

		$query = $db->_query_print("insert into gd_offline_coupon set [cv],status='pre',updatedt=now(),regdt=now()", $arInsertCoupon);
		$res = $db->query($query);
		$coupon_sno = $db->lastID();

		if(!create_goods_coupon($arInsertGoods,$arInsertCategory,$coupon_sno)){
			msg('쿠폰 적용상품 등록이 실패 하였습니다.',-1);
		}

		if(!create_paper($_POST['number_type'],$limit_paper,$_POST['publish_limit'],$coupon_sno,$_POST['number'],$arFile)){
			msg('쿠폰 번호 등록이 실패 하였습니다.',-1);
		}

		if(!$res){
			msg('쿠폰 등록이 실패 하였습니다.',-1);
		}

		msg('쿠폰이 등록 되었습니다.');
		echo("<script>parent.location.href=\"paper_list.php\";</script>");
	break;
    case "modify" :
    	$sno = (int) $_POST['sno'];

    	$query = "SELECT count(*) cnt FROM gd_offline_download WHERE coupon_sno='$sno'";
		list($downloadCnt) = $db->fetch($query);
		if($downloadCnt) msg('사용된 쿠폰이 있어서 수정이 불가능 합니다.');

    	$query = $db->_query_print("UPDATE gd_offline_coupon SET [cv],status='pre',updatedt=now() WHERE sno='$sno'", $arInsertCoupon);
    	$res = $db->query($query);

    	$query = "DELETE FROM gd_offline_goods WHERE coupon_sno='$sno'";
    	$db->query($query);
		if(!create_goods_coupon($arInsertGoods,$arInsertCategory,$sno)){
			msg('쿠폰 적용상품 등록이 실패 하였습니다.',-1);
		}

		msg('쿠폰이 수정 되었습니다.');
		echo("<script>parent.location.reload();</script>");
	break;
	case "disuse":
		$sno = (int) $_GET['sno'];

    	if($sno){
			$query = "SELECT `status` FROM gd_offline_coupon WHERE sno='$sno' limit 1";
			list($status) = $db->fetch($query);
			if($status == 'disuse' ){
				$query = "SELECT count(*) cnt FROM gd_offline_download WHERE coupon_sno='$sno'";
    			list($tmp) = $db->fetch($query);
    			$status = "done";
    			if($tmp == 0)$status = "pre";
    		}else{
    			$status="disuse";
    		}
    		$query = "UPDATE gd_offline_coupon SET `status`='$status' WHERE sno='$sno'";
    		$db->query($query);
    	}
    	msg('상태가 변경되었습니다.');
    	echo("<script>parent.location.reload();</script>");
	break;
    case "delete":
    	$sno = (int) $_GET['sno'];

    	if($sno){
    		$query = "SELECT count(*) cnt FROM gd_offline_download WHERE coupon_sno='$sno'";
			list($downloadCnt) = $db->fetch($query);
			if($downloadCnt) msg('사용된 쿠폰이 있어서 삭제가 불가능 합니다.',-1);

    		$query = "DELETE FROM gd_offline_coupon WHERE sno='$sno'";
    		$db->query($query);

    		$query = "DELETE FROM gd_offline_goods WHERE coupon_sno='$sno'";
    		$db->query($query);

    		$query = "DELETE FROM gd_offline_paper WHERE coupon_sno='$sno'";
    		$db->query($query);
    	}

    	msg('쿠폰이 삭제 되었습니다.');
    	echo("<script>parent.location.reload();</script>");
    break;
}

?>
