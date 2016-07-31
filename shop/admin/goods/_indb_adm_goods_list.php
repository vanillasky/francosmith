<?php
include "../lib.php";

// @todo : 함수 삭제 및 모델 내부에서 처리 가능하도록 수정
$Goods = Core::loader('Goods');

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

	foreach (array('img_i','img_l','img_m','img_s','img_mobile','img_w','img_x','img_y','img_z') as $key) {
		delGoodsImg($data[$key]);
	}

	$optionData = $db->_select("select opt1img,opt1icon,opt2icon from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	foreach($optionData as $val){
		delGoodsImg($val['opt1img']);
		delGoodsImg($val['opt1icon']);
		delGoodsImg($val['opt2icon']);
	}

	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISCOUNT." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_SHOPTOUCH_GOODS." where goodsno='{$goodsno}'");

	### 네이버 지식쇼핑 상품엔진
	naver_goods_runout($goodsno);

	// 다음 지식쇼핑 상품엔진
	daum_goods_runout($goodsno);
	
	### 블로그샵 상품삭제
	include_once("../../lib/blogshop.class.php");
	$blogshop = new blogshop();
	$blogshop->delete_goods($goodsno);

	### 계정용량 계산
	setDu('goods');
}

function copyGoods($goodsno){

	global $db,$Goods;
	static $imgIdx = 0;
	$goodsSort = Core::loader('GoodsSort');

	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	### 이미지 복사
	$time = time() . sprintf("%03d", $imgIdx++);

	### 이미지 복사
	$ar_images = array(
		'i' => 'img_i',
		's' => 'img_s',
		'm' => 'img_m',
		'l' => 'img_l',
		'e' => 'img_mobile',
		'w' => 'img_w',
		'x' => 'img_x',
		'y' => 'img_y',
		'z' => 'img_z',
	);

	$image_separator = '|';
	$image_qr = array();

	foreach ($ar_images as $key => $image_field) {

		$images = explode($image_separator , $data[$image_field]);
		$images_nums = sizeof($images);
		$images_seq = 0;

		${$image_field} = array();

		if (sizeof($images) > 0) {
			foreach($images as $image_name) {
				if (empty($image_name)) continue;

				if (! preg_match('/^http(s)?:\/\/.+$/', $image_name)) {
					$image_ext = strrpos($image_name,'.') ? substr($image_name, strrpos($image_name,'.')) : '';

					$_image_name  = $time.'_'.$key.( $images_nums > 1 ? '_'.$images_seq++ : '' );
					$_image_name .= $image_ext ? $image_ext : '';

					// 파일 복사
					if (is_file($_dir .$image_name)) @copy($_dir .$image_name, $_dir .$_image_name);
					if (is_file($_dirT.$image_name)) @copy($_dirT.$image_name, $_dirT.$_image_name);

					$image_name = $_image_name;
				}

				${$image_field}[] = $image_name;
			}
		}

		$image_qr[] = "$image_field = '".mysql_real_escape_string(implode($image_separator, ${$image_field}))."'";
	}

	### 상품정보
	$except = array_merge( array("goodsno","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt","goodscd") , array_values($ar_images) );

	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			$qr[] = "$k='".addslashes($v)."'";
		}
	}
	$query = "
	INSERT INTO ".GD_GOODS." SET
		".implode(",",$qr).",
		".implode(",",$image_qr).",
		regdt	= now()
	";
	$db->query($query);
	$cGoodsno = $db->lastID();

	### 추가옵션
	$except = array("sno","goodsno");
	$res = $db->query("select * from ".GD_GOODS_ADD." where goodsno='{$goodsno}' order by sno asc ");
	while ($data=$db->fetch($res,1)){
		if ($data){ unset($qr);
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_ADD." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	### 가격/재고/옵션
	$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' order by sno asc");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			### 이미지 복사
			$time = time() . sprintf("%03d", $optionImgIdx++);

			### 이미지 복사
			$ar_optionImages = array(
				'1img' => 'opt1img',
				'1icon' => 'opt1icon',
				'2icon' => 'opt2icon'
			);

			$optionImage_qr = array();

			foreach ($ar_optionImages as $key => $optionImage_field) {

				$optionImages = explode($image_separator , $data[$optionImage_field]);
				$optionImages_nums = sizeof($optionImages);
				$optionImages_seq = 0;

				${$optionImage_field} = array();

				if (sizeof($optionImages) > 0) {
					foreach($optionImages as $optionImage_name) {
						if (empty($optionImage_name)) continue;

						if (! preg_match('/^http(s)?:\/\/.+$/', $optionImage_name)) {
							$optionImage_ext = strrpos($optionImage_name,'.') ? substr($optionImage_name, strrpos($optionImage_name,'.')) : '';

							$_optionImage_name  = $time.'_'.$key.( $optionImages_nums > 1 ? '_'.$optionImages_seq++ : '' );
							$_optionImage_name .= $optionImage_ext ? $optionImage_ext : '';

							// 파일 복사
							if (is_file($_dir .$optionImage_name)) @copy($_dir .$optionImage_name, $_dir .$_optionImage_name);
							if (is_file($_dirT.$optionImage_name)) @copy($_dirT.$optionImage_name, $_dirT.$_optionImage_name);

							$optionImage_name = $_optionImage_name;
						}

						${$optionImage_field}[] = $optionImage_name;
					}
				}

				$optionImage_qr[] = "$optionImage_field = '".mysql_real_escape_string(implode($image_separator, ${$optionImage_field}))."'";
			}

			$except_option = array_merge( array("sno", "goodsno") , array_values($ar_optionImages) );
			
			foreach ($data as $k=>$v){
				if (!in_array($k,$except_option)) $qr[] = "$k='".addslashes($v)."'";
			}

			$query = "insert into ".GD_GOODS_OPTION." set goodsno='{$cGoodsno}',".implode(",",$qr).",".implode(",",$optionImage_qr);
			$db->query($query);
		}
	}

	### 상품, 카테고리 연결정보
	$maxSortIncrease = array();
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}' order by category");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			unset($data['sort1'], $data['sort2'], $data['sort3'], $data['sort4']);
			foreach ($goodsSort->getManualSortInfoHierarchy($data['category']) as $categorySortSet) {
				if (strlen($data['category'])/3 >= $categorySortSet['depth']) {
					if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
						if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
							$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
							$linkSortIncrease[$categorySortSet['category']] = true;
						}
						$data[$categorySortSet['sort_field']] = 1;
					}
					else {
						$data[$categorySortSet['sort_field']] = ((int)$categorySortSet['sort_max']+1);
					}
					$maxSortIncrease[$categorySortSet['category']] = true;
				}
			}
			foreach ($data as $k=>$v){
				if($k=='sort')$v = -(time());
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_LINK." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}
	foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

	### 계정용량 계산
	setDu('goods');

	return $cGoodsno;
}

$return = null;

switch (Clib_Application::request()->get('action')) {
	case 'toggleOpen' :

		$goods = Clib_Application::getModelClass('goods');
		$goods->load(Clib_Application::request()->get('goodsno'));
		$goods->setData('open', Clib_Application::request()->get('value'));

		// 모바일샵 상품 노출 설정 가져오기
		$cfgMobileShop = Clib_Application::getLoadConfig('config.mobileShop');

		if($cfgMobileShop['vtype_goods'] != 1) {
			$goods->setData('open_mobile', Clib_Application::request()->get('value'));
		}

		// 다음 요약 EP
		if (Clib_Application::request()->get('value') === '1') {
			daum_goods_runout_recovery(Clib_Application::request()->get('goodsno'));
		}
		else {
			daum_goods_runout(Clib_Application::request()->get('goodsno'));
		}
		
		$goods->save();

		$return = true;
		break;

	case 'unlinkCategory':
		$query = sprintf("delete from gd_goods_link where sno = '%d'", Clib_Application::request()->get('sno'));
		if ($db->query($query)) {
			$return = true;
		}
		else {
			$return = false;
		}
		break;
	case 'setSoldout':
		$query = sprintf("update gd_goods set runout = 1 where goodsno IN (%s)", implode(',', Clib_Application::request()->get('goodsno')));
		if ($db->query($query)) {
			$return = true;
		}
		else {
			$return = false;
		}
		break;
	case 'delete':

		// transaction;
		Clib_Application::database()->begin();

		try {

			foreach((array) Clib_Application::request()->get('goodsno') as $goodsno) {
				delGoods($goodsno);
			}

			Clib_Application::database()->commit();
			$return = true;
		}
		catch (Clib_Exception $e) {
			Clib_Application::database()->rollback();
			$return = false;
		}
		break;
	case 'copy':

		// transaction;
		Clib_Application::database()->begin();

		try {

			$goods = Clib_Application::getModelClass('goods');

			foreach((array) Clib_Application::request()->get('goodsno') as $goodsno) {
				copyGoods($goodsno);
			}

			Clib_Application::database()->commit();
			$return = true;
		}
		catch (Clib_Exception $e) {
			Clib_Application::database()->rollback();
			$return = false;
		}
		break;
}

echo gd_json_encode($return);
