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

// ��ǰ�з� ������ Class
$categoryNewMethod	= Core::loader('categoryNewMethod');

/**
 * ��ǰ �̹��� ����
 * @param  sting ������ �̹����� ��� (������ �ΰ�� '|' �� ����)
 */
function delGoodsImg($str)
{
	// ��ǰ �̹��� ���� ���
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		if ($v == '') continue;

		// �̹����� �ִ� ��� ����
		if (is_file($_dir.$v)) @unlink($_dir.$v);
		if (is_file($_dirT.$v)) @unlink($_dirT.$v);
	}
}

/**
 * ��ǰ ����
 * @param  integer ��ǰ ��ȣ
 */
function delGoods($goodsno)
{
	global $db;

	// ��ǰ �̹��� ����
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");
	foreach (array('img_i','img_l','img_m','img_s','img_mobile') as $key) {
		delGoodsImg($data[$key]);
	}

	// �ɼǺ� �̹��� ����
	$optionData = $db->_select("select opt1img,opt1icon,opt2icon from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	foreach($optionData as $val){
		delGoodsImg($val['opt1img']);
		delGoodsImg($val['opt1icon']);
		delGoodsImg($val['opt2icon']);
	}

	// ��ǰ ���� ���̺� ����
	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISCOUNT." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_SHOPTOUCH_GOODS." where goodsno='{$goodsno}'");

	// ���̹� ���ļ��� ��ǰ����
	naver_goods_runout($goodsno);

	// �����뷮 ���
	setDu('goods');
}

/**
 * ��ǰ ����
 * @param  integer ��ǰ ��ȣ
 */
function copyGoods($goodsno)
{
	global $db, $Goods, $goodsSort;
	static $imgIdx = 0;

	// ��ǰ���� Ŭ����
	if (!is_object($goodsSort)) {
		$goodsSort	= Core::loader('GoodsSort');
	}

	// ��ǰ �̹��� ���� ���
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data	= $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	// �̹����� prefix
	$time	= time() . sprintf("%03d", $imgIdx++);

	// ������ �̹���
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

					// ���� ����
					if (is_file($_dir .$image_name)) @copy($_dir .$image_name, $_dir .$_image_name);
					if (is_file($_dirT.$image_name)) @copy($_dirT.$image_name, $_dirT.$_image_name);

					$image_name	= $_image_name;
				}

				${$image_field}[]	= $image_name;
			}
		}

		$image_qr[]	= "$image_field = '".mysql_real_escape_string(implode($image_separator, ${$image_field}))."'";
	}

	// ��ǰ����
	$except	= array_merge( array("goodsno","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt","goodscd") , array_values($ar_images) );

	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			if ($k == 'open') $v = 0;
			$qr[]	= "$k='".addslashes($v)."'";
		}
	}

	// ��ǰ ���� ����
	$query = "
	INSERT INTO ".GD_GOODS." SET
		".implode(",",$qr).",
		".implode(",",$image_qr).",
		regdt	= now()
	";
	$db->query($query);

	// goodsno ���� ����
	$cGoodsno	= $db->lastID();

	// ������Ʈ �Ͻ�
	$Goods->update_date($cGoodsno);

	// �߰��ɼ� ����
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

	// ��ǰ �ɼ� ����
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

	// ��ǰ ī�װ� �������� ����
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	while ($data=$db->fetch($res,1)){
		setCategoryLink($cGoodsno, $data['category'], $data['hidden'], $data['hidden_mobile']);
	}

	// �����뷮 ���
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
 * ��ǰ�� �з� ����
 * @param1  integer ��ǰ ��ȣ
 * @param2  string  ī�װ� ��ȣ
 * @param3  integer PC �з����㿩�� (0, 1)
 * @param4  integer ����ϼ� �з����㿩�� (0, 1)

 * @return  boolean ó������
 */
function setCategoryLink($goodsno, $sCategory, $hidden = 0, $hidden_mobile = 0)
{
	global $db, $goodsSort, $categoryNewMethod;

	// üũ
	if (empty($goodsno) || empty($sCategory)) {
		return false;
	}

	// ��ǰ���� Class
	if (!is_object($goodsSort)) {
		$goodsSort	= Core::loader('GoodsSort');
	}

	// ��ǰ�з� ������ Class
	if (!is_object($goodsSort)) {
		$categoryNewMethod	= Core::loader('categoryNewMethod');
	}

	// ������ �з� ���� �迭ȭ
	$arrCategoryLink	= $categoryNewMethod->getHighCategoryLink($goodsno, $sCategory);

	// �з� ������ ���� ��� ����
	if (is_array($arrCategoryLink) === false || empty($arrCategoryLink) === true) {
		return false;
	}

	// ������ �з� ������ ���� ����
	foreach ($arrCategoryLink as $categoryLink) {

		$linkSortIncrease	= array();
		$sortList			= array();
		$goodsLinkSort		= array();
		$maxSortIncrease	= array();

		// ������ �з� ������ ����� ��ǰ�� �ִ����� üũ
		$lookupGoodsLink	= $db->query('SELECT * FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($categoryLink, 0, 3).'%" AND goodsno='.$goodsno);

		// �з� ������ ��Ʈ ��ȣ�� �迭ȭ
		while ($goodsLink	= $db->fetch($lookupGoodsLink, true)) {
			for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
				$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
			}
		}

		// ������ ī�װ��� �������� ���İ��� �ִ�ġ�� ��ȯ
		foreach ($goodsSort->getManualSortInfoHierarchy($categoryLink) as $categorySortSet) {
			// �ش� �ִ밪�� ������ ������ �з� ������ ��ũ���� �ִ밪���� ó��
			if ($goodsLinkSort[$categorySortSet['category']]) {
				$sortList[] = $categorySortSet['sort_field'].'='.$goodsLinkSort[$categorySortSet['category']];
			}
			// ������� ���� ���
			else {
				// ī�װ��� ��ǰ ���� ���� �� "�Ǿտ� ������ ���"
				if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
					if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
						// ���� �������� 1 �� ��������
						$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
						$linkSortIncrease[$categorySortSet['category']] = true;
					}
					// ���� ������ �з��� 1�� ó�� (�Ǿ�)
					$sortList[] = $categorySortSet['sort_field'].'=1';
				}
				// ī�װ��� ��ǰ ���� ���� �� "�ǵڿ� ������ ���"
				else {
					// �ִ밪�� 1�� ����
					$sortList[] = $categorySortSet['sort_field'].'='.((int)$categorySortSet['sort_max']+1);
				}
				$maxSortIncrease[$categorySortSet['category']] = true;
			}
		}

		// �ش� ī�װ��� �ִ밪�� ������
		foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

		// ������ �з��� ������
		$strSQL	= "insert into ".GD_GOODS_LINK." set goodsno='".$goodsno."',category='".$categoryLink."',hidden='".$hidden."',hidden_mobile='".$hidden_mobile."',sort=-unix_timestamp()".(count($sortList) ? ', '.implode(', ', $sortList) : '');
		$db->query($strSQL);
	}
	return true;
}

/**
 * ��ǰ�� �з� ����
 * @param1  integer ��ǰ ��ȣ
 * @param2  string  ī�װ� ��ȣ
 * @param3  string	ó����� (1 => ���� �з� ����, 2 => ���� �з� ����, 3 => �ش� �з���, 4 => ��� �з� ����)
 * @return  boolean ó������
 */
function setCategoryUnlink($goodsno, $category, $unlinkType = 1)
{
	global $db, $categoryNewMethod;

	// üũ
	if (empty($goodsno) || empty($category)) {
		return false;
	}

	// ��ǰ�з� ������ Class
	if (!is_object($goodsSort)) {
		$categoryNewMethod	= Core::loader('categoryNewMethod');
	}

	// ���� �з� ����
	if ($unlinkType == '1') {
		$arrCategoryUnlink		= $categoryNewMethod->getHighCategoryUnlink($goodsno, $category);
	}

	// ���� �з� ����
	else if ($unlinkType == '2') {
		$strSQL	= "DELETE FROM ".GD_GOODS_LINK." WHERE goodsno='".$goodsno."' and category LIKE '".$category."%'";
		$db->query($strSQL);

		return true;
	}

	// �ش� �з���
	else if ($unlinkType == '3') {
		$arrCategoryUnlink[]	= $category;
	}

	// ��� �з� ����
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
 * �̺�Ʈ ī�װ� ó��
 * @param1  integer ��ǰ ��ȣ
 * @param2  string  ���� (link => ����, del => ����)
 * @return  boolean ó������
 */
function setEventCategory($goodsno, $method = 'link')
{
	global $db;

	// üũ
	if (empty($goodsno)) {
		return false;
	}

	// �̺�Ʈ ī�װ� ����
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

	// �з� ����
	case "link":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// ����Ÿ ��ȿ�� �˻�
		$sCategory = array_notnull($_POST['sCate']);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// ����ϼ� ���߱�
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// ����ϼ� ī�װ� ���� ������ '�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����'�� ���
			$hidden_mobile = $hidden;
		}
		else {
			// ����ϼ� ī�װ� ���� ������ '����ϼ� ���� ���⼳�� ����'�� ���
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// ���� ��ǰ�� �з� ����
		foreach ($_POST['chk'] as $goodsno){

			// ������ ����� �з� ������ �ִ��� üũ�� ��
			list($cnt) = $db->fetch("SELECT COUNT(0) FROM ".GD_GOODS_LINK." WHERE goodsno='{$goodsno}' AND category='{$sCategory}'");

			// ����� ������ ���°�� �з� ���� ó��
			if (!$cnt) {
				setCategoryLink($goodsno, $sCategory, $hidden, $hidden_mobile);
			}

			// �ش� ��ǰ�� ������� ���� ��Ͻð����� ����
			if ($_POST['isToday'] == 'Y') $db->query("UPDATE ".GD_GOODS." SET regdt=now() WHERE goodsno='{$goodsno}'");

			// �̺�Ʈ ī�װ� ����
			setEventCategory($goodsno, 'link');
		}

		break;

	// �з� �̵�
	case "move":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// ����Ÿ ��ȿ�� �˻�
		$mCategory	= array_notnull($_POST['mCate']);
		$mCategory	= $mCategory[count($mCategory)-1];
		if ($mCategory == '') break;
		if ($_POST['category'] == '') break;
		//if ($_POST['unlinkTypeMove'] == '') break;
		$_POST['unlinkTypeMove']	= 4;

		$hidden		= getCateHideCnt($mCategory) > 0 ? 1 : 0;

		// ����ϼ� ���߱�
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// ����ϼ� ī�װ� ���� ������ '�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����'�� ���
			$hidden_mobile = $hidden;
		}
		else {
			// ����ϼ� ī�װ� ���� ������ '����ϼ� ���� ���⼳�� ����'�� ���
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// ���� ��ǰ�� �з� �̵�
		foreach ($_POST['chk'] as $goodsno){

			// �з� ���� ����
			setCategoryUnlink($goodsno, $_POST['category'], $_POST['unlinkTypeMove']);

			// �з� ���� ó��
			setCategoryLink($goodsno, $mCategory, $hidden, $hidden_mobile);
		}
		break;

	// �з� ����
	case "unlink":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// ����Ÿ ��ȿ�� �˻�
		if ($_POST['category'] == '') break;
		//if ($_POST['unlinkTypeUnlink'] == '') break;
		$_POST['unlinkTypeUnlink']	= 4;

		// ���� ��ǰ�� �з� ���� ����
		foreach ($_POST['chk'] as $goodsno){

			// �̺�Ʈ ī�װ� ���� ����
			setEventCategory($goodsno, 'del');

			// �з� ���� ����
			setCategoryUnlink($goodsno, $_POST['category'], $_POST['unlinkTypeUnlink']);
		}

		break;

	// ��ǰ ����
	case "copyGoodses":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);

		// ����Ÿ ��ȿ�� �˻�
		$sCategory	= array_notnull($_POST['ssCate']);
		$sCategory	= $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		if ($_POST['category'] == '') break;
		$hidden		= getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// ����ϼ� ���߱�
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// ����ϼ� ī�װ� ���� ������ '�¶��� ���θ�(PC����)�� ���⼳�� �����ϰ� ����'�� ���
			$hidden_mobile = $hidden;
		}
		else {
			// ����ϼ� ī�װ� ���� ������ '����ϼ� ���� ���⼳�� ����'�� ���
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		// ��ǰ����
		foreach ($_POST['chk'] as $goodsno){

			// ��ǰ ����
			$cGoodsno = copyGoods($goodsno);

			// �з� ���� ó��
			setCategoryLink($cGoodsno, $sCategory, $hidden, $hidden_mobile);

			// �̺�Ʈ ī�װ� ����
			setEventCategory($cGoodsno, 'link');
		}
		break;

	// ��ǰ ����
	case "delGoodses":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno) delGoods($goodsno);
		break;

	// �귣�� ����
	case "linkBrand":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='{$_POST['brandno']}' where goodsno='{$goodsno}'");

			// ������Ʈ �Ͻ�
			$Goods -> update_date($goodsno);
		}
		break;

	// �귣�� ����
	case "unlinkBrand":

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='0' where goodsno='{$goodsno}'");

			// ������Ʈ �Ͻ�
			$Goods -> update_date($goodsno);
		}
		break;

}
?>
<script>
alert("����Ǿ����ϴ�.");
parent.location.reload();
</script>