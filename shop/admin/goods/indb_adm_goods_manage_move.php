<?
include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
include "../../lib/categoryNewMethod.class.php";

$qfile		= new qfile();
$upload		= new upload_file;
$Goods		= Core::loader('Goods');
$goodsSort	= Core::loader('GoodsSort');

// 상품분류 연결방식 Class
$categoryNewMethod	= Core::loader('categoryNewMethod');

/**
 * 상품 이미지 삭제
 * @param  sting 삭제할 이미지의 경로 (여러개 인경우 '|' 로 구분)
 */
function delGoodsImg($str)
{
	// 상품 이미지 저장 경로
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		if ($v == '') continue;

		// 이미지가 있는 경우 삭제
		if (is_file($_dir.$v)) @unlink($_dir.$v);
		if (is_file($_dirT.$v)) @unlink($_dirT.$v);
	}
}

/**
 * 상품 삭제
 * @param  integer 상품 번호
 */
function delGoods($goodsno)
{
	global $db;

	// 상품 이미지 삭제
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
	foreach (array('img_i','img_l','img_m','img_s','img_mobile') as $key) {
		delGoodsImg($data[$key]);
	}

	// 옵션별 이미지 삭제
	$optionData = $db->_select("select opt1img,opt1icon,opt2icon from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	foreach($optionData as $val){
		delGoodsImg($val['opt1img']);
		delGoodsImg($val['opt1icon']);
		delGoodsImg($val['opt2icon']);
	}

	// 상품 관련 테이블 삭제
	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISCOUNT." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_SHOPTOUCH_GOODS." where goodsno='{$goodsno}'");

	// 네이버 지식쇼핑 상품엔진
	naver_goods_runout($goodsno);

	// 계정용량 계산
	setDu('goods');
}

/**
 * 상품 복사
 * @param  integer 상품 번호
 */
function copyGoods($goodsno)
{
	global $db, $Goods, $goodsSort;
	static $imgIdx = 0;

	// 상품진열 클래스
	if (!is_object($goodsSort)) {
		$goodsSort	= Core::loader('GoodsSort');
	}

	// 상품 이미지 저장 경로
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data	= $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	// 이미지명 prefix
	$time	= time() . sprintf("%03d", $imgIdx++);

	// 복사할 이미지
	$ar_images	= array(
		'i'	=> 'img_i',
		's'	=> 'img_s',
		'm'	=> 'img_m',
		'l'	=> 'img_l',
		'e'	=> 'img_mobile',
	);

	$image_separator = '|';
	$image_qr = array();

	foreach ($ar_images as $key => $image_field) {

		$images			= explode($image_separator , $data[$image_field]);
		$images_nums	= sizeof($images);
		$images_seq		= 0;

		${$image_field}	= array();

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

					$image_name	= $_image_name;
				}

				${$image_field}[]	= $image_name;
			}
		}

		$image_qr[]	= "$image_field = '".mysql_real_escape_string(implode($image_separator, ${$image_field}))."'";
	}

	// 상품정보
	$except	= array_merge( array("goodsno","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt","goodscd") , array_values($ar_images) );

	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			if ($k == 'open') $v = 0;
			$qr[]	= "$k='".addslashes($v)."'";
		}
	}

	// 상품 정보 저장
	$query = "
	INSERT INTO ".GD_GOODS." SET
		".implode(",",$qr).",
		".implode(",",$image_qr).",
		regdt	= now()
	";
	$db->query($query);

	// goodsno 값을 추출
	$cGoodsno	= $db->lastID();

	// 업데이트 일시
	$Goods->update_date($cGoodsno);

	// 추가옵션 저장
	$except	= array("sno","goodsno","optno");
	$res	= $db->query("select * from ".GD_GOODS_ADD." where goodsno='{$goodsno}' order by sno asc ");
	while ($data=$db->fetch($res,1)){
		if ($data){ unset($qr);
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query	= "insert into ".GD_GOODS_ADD." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	// 상품 옵션 저장
	$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' and go_is_deleted <> '1' order by sno asc");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_OPTION." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	// 상품 카테고리 연결정보 저장
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	while ($data=$db->fetch($res,1)){
		setCategoryLink($cGoodsno, $data['category'], $data['hidden'], $data['hidden_mobile']);
	}

	// 계정용량 계산
	setDu('goods');

	return $cGoodsno;
}

function reReferer($except, $request)
{
	return preg_replace("/(&mode=.*)(&page=[0-9]*$)*/", "\${2}" ,$_SERVER[HTTP_REFERER]) . '&' . getVars($except, $request);
}

function __trim(&$var)
{
    if(is_array($var)) {
        array_walk($var, '__trim');
    }
	else if ($var != '') {
		$var = trim($var);
    }
}

/**
 * 상품에 분류 연결
 * @param1  integer 상품 번호
 * @param2  string  카테고리 번호
 * @param3  integer PC 분류감춤여부 (0, 1)
 * @param4  integer 모바일샵 분류감춤여부 (0, 1)

 * @return  boolean 처리여부
 */
function setCategoryLink($goodsno, $sCategory, $hidden = 0, $hidden_mobile = 0)
{
	global $db, $goodsSort, $categoryNewMethod;

	// 체크
	if (empty($goodsno) || empty($sCategory)) {
		return false;
	}

	// 상품진열 Class
	if (!is_object($goodsSort)) {
		$goodsSort	= Core::loader('GoodsSort');
	}

	// 상품분류 연결방식 Class
	if (!is_object($goodsSort)) {
		$categoryNewMethod	= Core::loader('categoryNewMethod');
	}

	// 연결할 분류 정보 배열화
	$arrCategoryLink	= $categoryNewMethod->getHighCategoryLink($goodsno, $sCategory);

	// 분류 정보가 없는 경우 제외
	if (is_array($arrCategoryLink) === false || empty($arrCategoryLink) === true) {
		return false;
	}

	// 연결할 분류 정보를 토대로 루프
	foreach ($arrCategoryLink as $categoryLink) {

		$linkSortIncrease	= array();
		$sortList			= array();
		$goodsLinkSort		= array();
		$maxSortIncrease	= array();

		// 계층별 분류 정보에 연결된 상품이 있는지를 체크
		$lookupGoodsLink	= $db->query('SELECT * FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($categoryLink, 0, 3).'%" AND goodsno='.$goodsno);

		// 분류 정보의 소트 번호를 배열화
		while ($goodsLink	= $db->fetch($lookupGoodsLink, true)) {
			for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
				$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
			}
		}

		// 지정된 카테고리의 수동진열 정렬값의 최대치를 반환
		foreach ($goodsSort->getManualSortInfoHierarchy($categoryLink) as $categorySortSet) {
			// 해당 최대값이 있으며 저장할 분류 정보의 소크값을 최대값으로 처리
			if ($goodsLinkSort[$categorySortSet['category']]) {
				$sortList[] = $categorySortSet['sort_field'].'='.$goodsLinkSort[$categorySortSet['category']];
			}
			// 뷴류값이 없는 경우
			else {
				// 카테고리에 상품 새로 연결 시 "맨앞에 진열일 경우"
				if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
					if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
						// 기존 진열값은 1 씩 증가를함
						$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
						$linkSortIncrease[$categorySortSet['category']] = true;
					}
					// 현재 연결할 분류는 1로 처리 (맨앞)
					$sortList[] = $categorySortSet['sort_field'].'=1';
				}
				// 카테고리에 상품 새로 연결 시 "맨뒤에 진열일 경우"
				else {
					// 최대값에 1을 더함
					$sortList[] = $categorySortSet['sort_field'].'='.((int)$categorySortSet['sort_max']+1);
				}
				$maxSortIncrease[$categorySortSet['category']] = true;
			}
		}

		// 해당 카테고리에 최대값을 저장함
		foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

		// 연결할 분류를 저장함
		$strSQL	= "insert into ".GD_GOODS_LINK." set goodsno='".$goodsno."',category='".$categoryLink."',hidden='".$hidden."',hidden_mobile='".$hidden_mobile."',sort=-unix_timestamp()".(count($sortList) ? ', '.implode(', ', $sortList) : '');
		$db->query($strSQL);
	}
	return true;
}

/**
 * 상품에 분류 해제
 * @param1  integer 상품 번호
 * @param2  string  카테고리 번호
 * @param3  string	처리방법 (1 => 연관 분류 포함, 2 => 하위 분류 포함, 3 => 해당 분류만, 4 => 모든 분류 해제)
 * @return  boolean 처리여부
 */
function setCategoryUnlink($goodsno, $category, $unlinkType = 1)
{
	global $db, $categoryNewMethod;

	// 체크
	if (empty($goodsno) || empty($category)) {
		return false;
	}

	// 상품분류 연결방식 Class
	if (!is_object($goodsSort)) {
		$categoryNewMethod	= Core::loader('categoryNewMethod');
	}

	// 연관 분류 포함
	if ($unlinkType == '1') {
		$arrCategoryUnlink		= $categoryNewMethod->getHighCategoryUnlink($goodsno, $category);
	}

	// 하위 분류 포함
	else if ($unlinkType == '2') {
		$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE goodsno='".$goodsno."' and category LIKE '".$category."%'";
		$db->query($strSQL);

		return true;
	}

	// 해당 분류만
	else if ($unlinkType == '3') {
		$arrCategoryUnlink[]	= $category;
	}

	// 모든 분류 해제
	else if ($unlinkType == '4') {
		$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE goodsno='".$goodsno."'";
		$db->query($strSQL);

		return true;
	}

	foreach ($arrCategoryUnlink as $categoryUnlink) {
		$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE goodsno='".$goodsno."' and category='".$categoryUnlink."'";
		$db->query($strSQL);
	}

	return true;
}

/**
 * 이벤트 카테고리 처리
 * @param1  integer 상품 번호
 * @param2  string  구분 (link => 연결, del => 삭제)
 * @return  boolean 처리여부
 */
function setEventCategory($goodsno, $method = 'link')
{
	global $db;

	// 체크
	if (empty($goodsno)) {
		return false;
	}

	// 이벤트 카테고리 연결
	$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='$goodsno'");
	$i=0;
	while($tmp = $db->fetch($res)){
		$mode = "e".$tmp['sno'];
		list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'");

		if ($method == 'link') {
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
		else if ($method == 'del') {
			if( $cnt > 0 ){
				$query = "delete from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'";
				$db->query($query);
			}
		}
	}

	return true;
}

array_walk($_POST,	'__trim');

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

switch ($mode){

	// 분류 연결
	case "link":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// 데이타 유효성 검사
		$sCategory = array_notnull($_POST['sCate']);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// 선택 상품의 분류 연결
		foreach ($_POST['chk'] as $goodsno){

			// 기존에 연결된 분류 정보가 있는지 체크를 함
			list($cnt) = $db->fetch("SELECT COUNT(0) FROM ".GD_GOODS_LINK." WHERE goodsno='{$goodsno}' AND category='{$sCategory}'");

			// 연결된 정보가 없는경우 분류 연결 처리
			if (!$cnt) {
				setCategoryLink($goodsno, $sCategory, $hidden, $hidden_mobile);
			}

			// 해당 상품의 등록일을 현재 등록시간으로 변경
			if ($_POST['isToday'] == 'Y') $db->query("UPDATE ".GD_GOODS." SET regdt=now() WHERE goodsno='{$goodsno}'");

			// 이벤트 카테고리 연결
			setEventCategory($goodsno, 'link');
		}

		break;

	// 분류 이동
	case "move":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// 데이타 유효성 검사
		$mCategory	= array_notnull($_POST['mCate']);
		$mCategory	= $mCategory[count($mCategory)-1];
		if ($mCategory == '') break;
		if ($_POST['category'] == '') break;
		//if ($_POST['unlinkTypeMove'] == '') break;
		$_POST['unlinkTypeMove']	= 4;

		$hidden		= getCateHideCnt($mCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// 선택 상품의 분류 이동
		foreach ($_POST['chk'] as $goodsno){

			// 분류 연결 해제
			setCategoryUnlink($goodsno, $_POST['category'], $_POST['unlinkTypeMove']);

			// 분류 연결 처리
			setCategoryLink($goodsno, $mCategory, $hidden, $hidden_mobile);
		}
		break;

	// 분류 해제
	case "unlink":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// 데이타 유효성 검사
		if ($_POST['category'] == '') break;
		//if ($_POST['unlinkTypeUnlink'] == '') break;
		$_POST['unlinkTypeUnlink']	= 4;

		// 선택 상품의 분류 연결 해제
		foreach ($_POST['chk'] as $goodsno){

			// 이벤트 카테고리 연결 삭제
			setEventCategory($goodsno, 'del');

			// 분류 연결 해제
			setCategoryUnlink($goodsno, $_POST['category'], $_POST['unlinkTypeUnlink']);
		}

		break;

	// 상품 복사
	case "copyGoodses":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// 데이타 유효성 검사
		$sCategory	= array_notnull($_POST['ssCate']);
		$sCategory	= $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		if ($_POST['category'] == '') break;
		$hidden		= getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// 상품복사
		foreach ($_POST['chk'] as $goodsno){

			// 상품 복사
			$cGoodsno = copyGoods($goodsno);

			// 분류 연결 처리
			setCategoryLink($cGoodsno, $sCategory, $hidden, $hidden_mobile);

			// 이벤트 카테고리 연결
			setEventCategory($cGoodsno, 'link');
		}
		break;

	// 상품 삭제
	case "delGoodses":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno) delGoods($goodsno);
		break;

	// 브랜드 연결
	case "linkBrand":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='{$_POST['brandno']}' where goodsno='{$goodsno}'");

			// 업데이트 일시
			$Goods -> update_date($goodsno);
		}
		break;

	// 브랜드 해제
	case "unlinkBrand":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='0' where goodsno='{$goodsno}'");

			// 업데이트 일시
			$Goods -> update_date($goodsno);
		}
		break;

}
?>
<script>
alert("저장되었습니다.");
parent.location.reload();
</script>