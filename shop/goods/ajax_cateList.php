<?
// 필요파일 로드
	include "../lib/library.php";
	@include dirname(__FILE__) . "/../lib/tplSkinView.php";

// 파라메터 가공 및 정의
	$goodsno	= ($_GET['goodsno'])	? $_GET['goodsno']		: "";	// 현재 상품 번호
	$category	= ($_GET['category'])	? $_GET['category']		: "";	// 카테고리 코드
	$page_num	= ($_GET['page_num'])	? $_GET['page_num']		: 5;	// 페이지당 출력 상품 수
	$page		= $_GET['page'];										// 페이지

// 상품 번호가 없으면 작동하지 않음
	if(!$goodsno) exit();

// 카테고리가 없다면 카테고리 읽기
	if(!$category) list($category) = $db->fetch("SELECT category FROM ".GD_GOODS_LINK." WHERE goodsno = '$goodsno' ORDER BY LENGTH(category) DESC, sno ASC LIMIT 1");

// 상품분류 연결방식 전환 여부에 따른 처리
	$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'L.goodsno');

// 쿼리 생성 및 실행
	$query = "
SELECT
	".$whereArr['distinct']." L.goodsno, L.category, G.img_l, G.goodsnm, O.price, G.use_only_adult
FROM
	".GD_GOODS_LINK." AS L
	LEFT JOIN ".GD_GOODS." AS G
		ON L.goodsno = G.goodsno
	LEFT JOIN ".GD_GOODS_OPTION." AS O
		ON L.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1' and go_is_display = '1'
WHERE
	".$whereArr['where']."
	AND G.open = '1'
".$whereArr['group']."
ORDER BY L.sort";
	$total = $db->count_($db->query($query)); // 전체 레코드

	// 페이지가 지정되지 않은 경우 현재 상품이 있는 페이지를 표시
	if(!$page) {
		list($this_sort) = $db->fetch("SELECT sort FROM ".GD_GOODS_LINK." WHERE goodsno = '$goodsno'");
		list($temp_cnt) = $db->fetch("SELECT COUNT(sort) AS cnt FROM ".GD_GOODS_LINK." WHERE sort < $this_sort AND ".getCategoryLinkQuery('category', $category, 'where'));
		$page = ceil(($temp_cnt + 1) / $page_num);
	}

	$query .= "
LIMIT ".($page_num * ($page - 1)).", $page_num";
	$result = $db->query($query);

	$count = $db->count_($result);
	if(!$count) exit;

// 하단 페이지 부분
	// 현재 리스트 번호
		$first_num = ($page_num * ($page - 1)) + 1;
		$last_num = ($page_num * ($page - 1)) + $count;
		$now_num = ($first_num == $last_num) ? $first_num : $first_num."-".$last_num;

// 하단 버튼
	$total_page = ceil($total / $page_num); // 총 페이지
	$prev_page = ($page == 1) ? "1" : $page - 1; // 이전 페이지
	$next_page = ($page == $total_page) ? $total_page : $page + 1; // 다음 페이지
	$qStr = "goodsno={$goodsno}&category={$category}";
?>
<div style="width:90;background-image:url('../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_bg_list.gif'); background-repeat:repeat-y;">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr><td><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_h_list.gif" style="cursor:pointer;" onclick="toggle('leftCateList');toggle('leftCatePage');" /></td></tr>
	<tr>
		<td align="center"><div id="leftCateList" style="">
<?
	while($data=$db->fetch($result)) {
		$ar_temp = explode("|", $data['img_l']);
		$data['img'] = $ar_temp[0];
		//성인인증 상품 청소년 보호법 적용
		if($data['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			$data['img'] = "../skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
		}
?>
		<div><a href="../goods/goods_view.php?goodsno=<?=$data['goodsno']?>&category=<?=$category?>" onmouseover="scrollTooltipShow(this)" onmousemove="scrollTooltipShow(this)" onmouseout="scrollTooltipHide(this)" tooltip="<span style='color:#333232;'><?=htmlspecialchars($data['goodsnm'], ENT_QUOTES)?></span><br><span style='color:#EF1C21;font-weight:bold;'><?=number_format($data['price'])?>원</span>"><?=goodsimg($data['img'], 70)?></a></div>
		<div style="height:3px;font-size:0"></div>
<?
	}
?>
		</div></td>
	</tr>
	<tr>
		<td align="center" id="leftCatePage"><div style="width:88px;margin:0px 1px;padding:5px 0px;font-weight:bold;color:#333232;"><font color="#EF1C21"><?=$now_num?></font> of <?=$total?></div></td>
	</tr>
	<tr><td><a href="javascript:scrollCateList_ajax('<?=$qStr?>', <?=$prev_page?>, <?=$page_num?>)" onfocus="blur()"><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_off.gif" onmousemove="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_on.gif';" onmouseout="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_left_list_off.gif';" border="0"></a><a href="javascript:scrollCateList_ajax('<?=$qStr?>', <?=$next_page?>, <?=$page_num?>)" onfocus="blur()"><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_off.gif" onmousemove="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_on.gif';" onmouseout="this.src='../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_btn_right_list_off.gif';" border='0'></a></td></tr>
	<tr><td><img src="../data/skin/<?=$cfg['tplSkin']?>/img/common/skin_bn_foot_list.gif" border='0'></td></tr>
</table>
</div>
