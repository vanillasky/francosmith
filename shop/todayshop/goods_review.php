<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";
include "../lib/page.class.php";
$query = "";
$rs = $db->query($query);

### 리스트 템플릿 기본 환경변수
$lstcfg[size]	= 50;
$lstcfg[page_num] = array(10,20,30,40);
$lstcfg[sort] = array( 1 => '최근등록순', 2 => '평점높은순' );

### 변수할당
if ($cfg['todayshopReviewListCnt'] > 0) {
	array_unshift($lstcfg[page_num] ,$cfg['todayshopReviewListCnt']);
	$lstcfg[page_num] = array_unique($lstcfg[page_num]);
	sort($lstcfg[page_num]);
}
if (!$_GET[page_num]) $_GET[page_num] = $lstcfg[page_num][0];
$selected[page_num][$_GET[page_num]] = "selected";
if (!$_GET[sort]) $_GET[sort] = 1;
$selected[sort][$_GET[sort]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
if ( file_exists( dirname(__FILE__) . '/../data/skin_today/' . $cfg['tplSkinToday'] . '/admin.gif' ) ) $adminicon = '../../../data/skin_today/'.$cfg['tplSkinToday'].'/admin.gif';


### 상품 사용기
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
	RV.*,

	MB.m_no, MB.m_id, MB.name AS m_name, MB.level,


	TG.tgsno, TG.goodsno, TG.goodsnm, TG.img_s, GO.price,

	IF (RV.sno = RV.parent ,'Q','A') AS type,
	IFNULL(SUB.replecnt,0) AS replecnt
";

$db_table = "
	".GD_TODAYSHOP_GOODS_REVIEW." AS RV
	LEFT JOIN (
		SELECT RV.parent, COUNT( IF( RV.sno <> RV.parent, RV.parent, NULL ) ) AS replecnt FROM ".GD_TODAYSHOP_GOODS_REVIEW." AS RV  GROUP BY RV.parent
	) AS SUB
	ON SUB.parent = RV.sno
	LEFT JOIN ".GD_MEMBER." AS MB
	ON RV.m_no = MB.m_no
	LEFT JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
	ON RV.goodsno = TG.goodsno
	LEFT JOIN ".GD_GOODS_OPTION." AS GO
	ON TG.goodsno = GO.goodsno AND GO.link and go_is_deleted <> '1' and go_is_display = '1'
";



if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	if ($category){
		$db_table .= " LEFT JOIN ".GD_GOODS_LINK." LNK ON RV.goodsno=LNK.goodsno";
		$where[] = "LNK.category LIKE '$category%'";
	}
}

if ($_GET[skey] && $_GET[sword]) {
	if ( $_GET[skey]== 'all' )			$where[] = "( CONCAT( RV.subject, RV.contents, IFNULL(MB.m_id, ''), IFNULL(RV.name, '') ) LIKE '%$_GET[sword]%' OR TG.goodsnm LIKE '%$_GET[sword]%' )";
	else if ( $_GET[skey]== 'goodnm' )	$where[] = "TG.goodsnm LIKE '%$_GET[sword]%'";
	else if ( $_GET[skey]== 'm_id' )	$where[] = "CONCAT( IFNULL(MB.m_id, ''), IFNULL(RV.name, '') ) LIKE '%$_GET[sword]%'";
	else								$where[] = "{$_GET[skey]} LIKE '%$_GET[sword]%'";
}

switch ($_GET[sort]){

	case "1":
		$sort = "RV.parent DESC, ( CASE WHEN RV.parent=RV.sno THEN 0 ELSE 1 END ) ASC, RV.regdt DESC";
		break;
	case "2":
		$sort = "RV.point DESC";
		break;
}

$pg->setQuery($db_table,$where,$sort,'GROUP BY RV.sno');
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;

	$data[authmodify] = $data[authdelete] = $data[authreply] = 'Y'; # 권한초기값

	if ( empty($cfg['todayshopReviewWriteAuth']) || isset($sess) || !empty($data[m_no]) ){ // 회원전용 or 회원 or 작성자==회원
		$data[authmodify] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
		$data[authdelete] = ( isset($sess) && $sess[m_no] == $data[m_no] ? 'Y' : 'N' );
	}


	$data[authdelete] = ( $data[replecnt] > 0 ? 'N' : $data[authdelete] ); # 답글 있는 경우 삭제 불가

	if ( $data[sno] == $data[parent] ){

		if ( empty($cfg['todayshopReviewWriteAuth']) ){ // 회원전용
			$data[authreply] = ( isset($sess) ? 'Y' : 'N' );
		}
	}else $data[authreply] = 'N';

	if ( $data[level] == '100' && $adminicon ) $data[m_id] = $data[name] = "<img src='../data/skin/{$cfg['tplSkin'] }/{$adminicon}' border=0>";
	if ( empty($data[m_no]) ) $data[m_id] = $data[name]; // 비회원명

	$data[contents] = nl2br(htmlchars_ech($data[contents]));
	$data[point] = sprintf( "%0d", $data[point]);

	if ($data[attach] == 1) $data[image] = '<img src="../data/review/'.'TSRV'.sprintf("%010s", $data[sno]).'">';
	else $data[image] = '';

	$loop[] = $data;
}

// 쓰기 권한
if ($cfg['todayshopReviewWriteAuth'] != 'free' && !$sess) $authwrite = 'N';
else $authwrite = 'Y';

// 구독 관련
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

$tpl->assign( 'pg', $pg );
$tpl->assign( 'lstcfg', $lstcfg );
$tpl->assign( 'authwrite', $authwrite );
### 템플릿 출력
$_html = $tpl->fetch('tpl');

// 캐시
$cache->setCache($_html);
?>