<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
require_once("../../lib/todayshop_cache.class.php");

$qfile = new qfile();
$upload = new upload_file;

$_POST[sub] = trim($_POST[sub]);

function delGoodsImg($str)
{
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		@unlink($_dir.$v);
		@unlink($_dirT.$v);
	}
}

function delGoods($tgsno){
	global $db;
	$data = $db->fetch("select g.* from ".GD_TODAYSHOP_GOODS." AS tg LEFT JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno  where tg.tgsno='{$tgsno}'");
	$goodsno = $data['goodsno'];
	delGoodsImg($data[img_i]);
	delGoodsImg($data[img_l]);
	delGoodsImg($data[img_m]);
	delGoodsImg($data[img_s]);

	$db->query("delete from ".GD_TODAYSHOP_GOODS_MERGED." where tgsno='{$tgsno}'");
	$db->query("delete from ".GD_TODAYSHOP_GOODS." where tgsno='{$tgsno}'");
	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");

	### 네이버 지식쇼핑 상품엔진
	naver_goods_runout($goodsno);

	### 블로그샵 상품삭제
	include_once("../../lib/blogshop.class.php");
	$blogshop = new blogshop();
	$blogshop->delete_goods($goodsno);

	### 계정용량 계산
	setDu('goods');
}

function copyGoods($tgsno){
	global $db;

	// 상품정보 가져오기 (GD_TODAYSHOP_GOODS)
	$query = $db->_query_print('SELECT * FROM '.GD_TODAYSHOP_GOODS.' WHERE tgsno=[i]',$tgsno);
	$res_tg = $db->_select($query);
	$data_tg = $res_tg[0];
	$goodsno = $data_tg['goodsno'];
	unset($res_tg, $data_tg['tgsno'], $data_tg['regdt']);

	// 상품정보 복사(GD_GOODS)
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	### 이미지 복사
	$time = time() . sprintf("%03d", $GLOBALS[imgIdx]++);
	$div = explode("|",$data[img_l]);
	if ($div){ foreach ($div as $k=>$v){
		if ($v == '') continue;
		if (preg_match('/^http(s)?:\/\/.+$/',$v)) {
			$img_l[] = $v;
		}
		else {
			$img_l[] = $time."_l_".$k.(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$v)) copy($_dir.$v,$_dir.$img_l[$k]);
			if (is_file($_dirT.$v)) copy($_dirT.$v,$_dirT.$img_l[$k]);
		}
	}}

	$div = explode("|",$data[img_m]);
	if ($div){ foreach ($div as $k=>$v){
		if ($v == '') continue;
		if (preg_match('/^http(s)?:\/\/.+$/',$v)) {
			$img_m[] = $v;
		}
		else {
			$img_m[] = $time."_m_".$k.(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$v)) copy($_dir.$v,$_dir.$img_m[$k]);
			if (is_file($_dirT.$v)) copy($_dirT.$v,$_dirT.$img_m[$k]);
		}
	}}

	if ($data[img_s]){
		if (preg_match('/^http(s)?:\/\/.+$/',$data[img_s])) {
			$img_s = $data[img_s];
		}
		else {
			$img_s = $time."_s".(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$data[img_s])) copy($_dir.$data[img_s],$_dir.$img_s);
		}
	}

	if ($data[img_i]){
		if (preg_match('/^http(s)?:\/\/.+$/',$data[img_i])) {
			$img_i = $data[img_i];
		}
		else {
			$img_i = $time."_i".(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$data[img_i])) copy($_dir.$data[img_i],$_dir.$img_i);
		}
	}

	$merged = array();

	// 상품 재고량 연동 취소 (품절시 수정이 안되기 때문에)
	$data['usestock'] = '';
	$data['runout'] = '0';

	### 상품정보
	$except = array("goodsno","img_i","img_s","img_m","img_l","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt","updatedt");
	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			$qr[] = "$k='".addslashes($v)."'";
		}
	}
	$query = "
	insert into ".GD_GOODS." set ".implode(",",$qr).",
		img_i	= '$img_i',
		img_s	= '$img_s',
		img_m	= '".@implode("|",$img_m)."',
		img_l	= '".@implode("|",$img_l)."',
		regdt	= now(),
		updatedt = now()
	";
	$db->query($query);
	$cGoodsno = $db->lastID();

	$merged_query = "
	insert into ".GD_TODAYSHOP_GOODS_MERGED." set ".implode(",",$qr).",
		img_i	= '$img_i',
		img_s	= '$img_s',
		img_m	= '".@implode("|",$img_m)."',
		img_l	= '".@implode("|",$img_l)."',
		regdt	= now(),
		updatedt = now()
	";


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
	$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1'");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_OPTION." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	### 상품, 카테고리 연결정보
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			foreach ($data as $k=>$v){
				if($k=='sort')$v = -(time());
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_LINK." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	// 상품정보 복사 (GD_TODAYSHOP_GOODS)
	$data_tg['goodsno'] = $cGoodsno;
	$data_tg['visible'] = 'n';
	$data_tg['buyercnt'] = 0;
	unset($data_tg['startdt'], $data_tg['enddt']);
	$query = $db->_query_print('INSERT INTO '.GD_TODAYSHOP_GOODS.' SET [cv], regdt=now()', $data_tg);
	$db->query($query);
	$data_tg['tgsno'] = $db->lastID();

	// 병합 테이블 기록
	$merged_query .=  $db->_query_print(' , [cv]', $data_tg);
	$db->query($merged_query);

	// 분류 추가
	$db->query("insert into ".GD_TODAYSHOP_LINK." (tgsno, category, hidden) SELECT $data_tg[tgsno], category, hidden FROM ".GD_TODAYSHOP_LINK." WHERE tgsno = $tgsno");

	### 계정용량 계산
	setDu('goods');

	return $cGoodsno;
}

function reReferer($except, $request){
	return preg_replace("/(&mode=.*)(&page=[0-9]*$)*/", "\${2}" ,$_SERVER[HTTP_REFERER]) . '&' . getVars($except, $request);
}


switch ($_GET[mode]){
	case "delGoods":
		delGoods($_GET[tgsno]);
		todayshop_cache::remove($_GET[tgsno],'*');
		todayshop_cache::remove('*','todaythumb');
		todayshop_cache::remove('*','todaylist');

		break;

	case "copyGoods":
		copyGoods($_GET[tgsno]);
		break;
	case "cacheRemove":
		todayshop_cache::remove($_GET[tgsno],'*');
		todayshop_cache::remove('*','todaythumb');
		todayshop_cache::remove('*','todaylist');
		break;
}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl]);

?>
