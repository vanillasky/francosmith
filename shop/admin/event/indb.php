<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/qrcode.class.php");
$qfile = new qfile();

$qr = "
subject		= '$_POST[subject]',
sdate		= '$_POST[sdate]',
edate		= '$_POST[edate]',
body		= '$_POST[body]',
tpl			= '$_POST[tpl]',
size		= '$_POST[size]',
page_num	= '$_POST[page_num]',
cols		= '$_POST[cols]'
";

### 카테고리 연결
if( $_POST[catnm] && ($_POST[mode] == 'addEvent' || $_POST[mode] == 'modEvent') ){
	$cate_ins = false;
	list($next_category) = $db->fetch("select max(category) from ".GD_CATEGORY." where length(category)='3'");
	$next_category += 1;
	$next_category = sprintf('%03d',$next_category);
	if( !$_POST[category] ){
		$db->query("insert into ".GD_CATEGORY." set category='".$next_category."',catnm='".$_POST[catnm]."',hidden='1',hidden_mobile='1',sort=unix_timestamp()");
		$cate_ins = true;
	}else{
		$next_category = $_POST[category];
		$db->query("update ".GD_CATEGORY." set catnm='".$_POST[catnm]."' where category='".$next_category."'");
	}
	if($cate_ins){
		$arr = array(
			'rtpl' => 'tpl_01',
			'rpage_num' => '4',
			'rcols' => '4',
			'body' => '',
			'tpl' => 'tpl_01',
			'page_num' => '20',
			'cols' => '4'
		);

		$qfile->open("../../conf/category/$_POST[category].php");
		$qfile->write("<? \n");
		$qfile->write("\$lstcfg = array( \n");
		foreach ($arr as $k=>$v){
			$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
			$qfile->write("'$k' => $v, \n");
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/category/$_POST[category].php",0707);
	}

	$hidden = getCateHideCnt($next_category) > 0 ? 1 : 0;
	// 모바일샵 감추기
	@include "../../conf/config.mobileShop.php";
	if ($cfgMobileShop['vtype_category'] == 0) {
		// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
		$hidden_mobile = $hidden;
	}
	else {
		// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
		$hidden_mobile = getCateHideCnt($next_category, 'mobile') > 0 ? 1 : 0;
	}
}else{
	$next_category = "";
}

switch ($_GET[mode]){

	case "delEvent":

		$mode = "e".$_GET[sno];
		$db->query("delete from ".GD_EVENT." where sno='$_GET[sno]'");
		$db->query("delete from ".GD_GOODS_DISPLAY." where mode='$mode'");
		break;

}

switch ($_POST[mode]){

	case "addEvent":

		$qr .= ",category = '$next_category'";

		### 이벤트 정보 등록
		$query = "insert into ".GD_EVENT." set $qr";
		$db->query($query);
		$mode = "e".$db->lastID();

		if( $next_category ){
			$db->query("delete from ".GD_GOODS_LINK." where category='$next_category'");
		}

		### 연결상품
		if ($_POST[e_refer]){ foreach ($_POST[e_refer] as $k=>$goodsno){
			$query = "
			insert into ".GD_GOODS_DISPLAY." set
				goodsno		= '$goodsno',
				mode		= '$mode',
				sort		= '$k'
			";
			$db->query($query);
		}}



		break;

	case "modEvent":

		$qr .= ",category = '$next_category'";
		$chkbrand = $chkcate = '';

		if($_POST['chkcate']) $chkcate = implode('|',$_POST['chkcate']);
		if($_POST['chkbrand']) $chkbrand = implode('|',$_POST['chkbrand']);
		$qr .= ", r_category = '$chkcate', r_brand = '$chkbrand'";

		### 이벤트 정보 등록
		$query = "update ".GD_EVENT." set $qr where sno='$_POST[sno]'";
		$db->query($query);
		$mode = "e".$_POST[sno];

		### 연결상품
		$db->query("delete from ".GD_GOODS_DISPLAY." where mode='$mode'");
		if ($_POST[e_refer]){ foreach ($_POST[e_refer] as $k=>$goodsno){
			$query = "
			insert into ".GD_GOODS_DISPLAY." set
				goodsno		= '$goodsno',
				mode		= '$mode',
				sort		= '$k'
			";
			$db->query($query);
		}}

		break;
}

if( $_POST[catnm] && ($_POST[mode] == 'addEvent' || $_POST[mode] == 'modEvent') ){
	### 이벤트카테고리와 연결상품 싱크
	$db->query("delete from ".GD_GOODS_LINK." where category='".$next_category."'");
	$res = $db->query("select * from ".GD_GOODS_DISPLAY." where mode='".$mode."' order by sort");
	$i=0;
	while($tmp = $db->fetch($res)){
		$timestamp = time()+$i;
		$db->query("insert into ".GD_GOODS_LINK." set goodsno='$tmp[goodsno]',category='$next_category',hidden='$hidden',hidden_mobile='$hidden_mobile',sort=-'".$timestamp."'");
		$i=$i+10;
	}
}

$eventsno = str_replace("e","",$mode);

$db->query("delete from ".GD_QRCODE." where qr_type='event' and contsNo=$eventsno");
if($_POST['qrcode'] == 'y'){
	$db->query("insert into ".GD_QRCODE." set  qr_type='event' ,contsNo=".$eventsno." ,qr_string = '', qr_name = 'event qr code', qr_size='', useLogo = '', regdt	= now()");
}

go($_SERVER[HTTP_REFERER]);

?>