<?

$location = "쇼플 > 표준 카테고리 연결/관리";
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';

include "../_header.php";
require_once ('./_inc/config.inc.php');

// 카테고리 갱신
$shople = Core::loader('shople');

$time_start = microtime(true);

### 상품분류 데이타 (mysql 5 이상 쿼리임)
$query = "
	SELECT
		GC.sno,
		GC.catnm,
		GC.category,
		GC.sort,

		SUB.full_dispno, SUB.full_name

	FROM ".GD_CATEGORY." AS GC

	LEFT JOIN (

			SELECT
				SCM.category,
				SUB2.full_dispno,
				SUB2.full_name

			FROM ".GD_SHOPLE_CATEGORY_MAP." AS SCM

			INNER JOIN (
						SELECT

							CONCAT_WS('|', SC1.dispno, SC2.dispno, SC3.dispno, SC4.dispno ) as full_dispno,
							CONCAT_WS(' > ', SC1.name, SC2.name, SC3.name, SC4.name ) as full_name

						FROM	 ".GD_SHOPLE_CATEGORY." AS SC1

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC2
						ON SC1.dispno = SC2.p_dispno

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC3
						ON SC2.dispno = SC3.p_dispno

						LEFT JOIN ".GD_SHOPLE_CATEGORY." AS SC4
						ON SC3.dispno = SC4.p_dispno

						WHERE SC1.depth = 1
			) AS SUB2
			ON SCM.11st = SUB2.full_dispno

	) AS SUB

	ON GC.category = SUB.category

	ORDER BY GC.category
";

$res = $db->query($query);

while ($data=$db->fetch($res,1)){

	$data['catnm'] = strip_tags( $data['catnm'] );
	if (!$data['catnm']) $data['catnm'] = "_deleted_";

	switch (strlen($data['category'])){
		case 3:
			$cate1[$data['sort']][] = $data;
			$spot = $data['category'];
			break;
		case 6:
			$cate2[$spot][$data['sort']][] = $data;
			$spot2 = $data['category'];
			break;
		case 9:
			$cate3[$spot2][$data['sort']][] = $data;
			$spot3 = $data['category'];
			break;
		case 12:
			$cate4[$spot3][$data['sort']][] = $data;
			break;
	}
}

### 배열 순서 재정의
$cate1 = resort($cate1);
if ($cate2) foreach ($cate2 as $k=>$v) $cate2[$k] = resort($v);
if ($cate3) foreach ($cate3 as $k=>$v) $cate3[$k] = resort($v);
if ($cate4) foreach ($cate4 as $k=>$v) $cate4[$k] = resort($v);

### 배열 하나로 통합
$category = array();

if (is_array($cate1)) { foreach ($cate1 as $v){
	$category[] = array_merge(array('step'=>'1'), $v);
	if ($cate2[$v['category']]){ foreach ($cate2[$v['category']] as $v2){
		$category[] = array_merge(array('step'=>'2'), $v2);
		if ($cate3[$v2['category']]){ foreach ($cate3[$v2['category']] as $v3){
			$category[] = array_merge(array('step'=>'3'), $v3);
			if ($cate4[$v3['category']]){ foreach ($cate4[$v3['category']] as $v4){
				$category[] = array_merge(array('step'=>'4'), $v4);
			}}
		}}
	}}
}}

?>
<script type="text/javascript" src="./_inc/common.js"></script>

<div class="title title_top">표준 카테고리 연결/관리 <span>내 쇼핑몰의 카테고리와 쇼피통 표준카테고리를 연결처리 합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 분류매칭이란?</b></font></div>
<div style="padding-top:2px"><font color="#777777">쇼플 제휴판매를 신청하신 이후 상점은 분류를 11번가 카테고리와 연결시켜야만 합니다.</div>
<div style="padding-top:2px"><font color="#0074BA">아래 기능은 분류을 11번가 카테고리와 연결하는 기능입니다.</font></div>
<div style="padding-top:2px">물론, 상품전송할 때 한 상품씩 따로따로 분류연결을 해도 상관없습니다. 빠르게 분류연결을 하려면 아래 기능을 사용하세요.</div>
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="30%"><col width="60%"><col width="10%">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th bgcolor="#000000">내 쇼핑몰 카테고리</th>
	<th bgcolor="#6F6F6F">11번가 카테고리</th>
	<th bgcolor="#000000">매칭/수정</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" id="cateMatchList" style="border-collapse: collapse;">
<col width="30%"><col width="60%"><col width="10%">
<? foreach ($category as $k => $v){ ?>
<tr height="20" class="step<?=$v['step']?>Tr">
	<td class="step<?=$v['step']?>Icon"><?=$v['catnm']?> <catno category="<?=$v['category']?>"></catno></td>

	<? if ($v['full_name']){ ?>
		<td style="padding-left:10px;" id="catnm<?=$k?>"><?=$v['full_name']?></td>
		<td align="center"><img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer('../shople/popup.config.category.php?category=<?=$v['category']?>&full_dispno=<?=$v['full_dispno']?>&rowIdx=' + this.parentNode.parentNode.rowIndex,750,550);"></td>
	<? } else { ?>
		<td style="padding-left:10px;"><font color="#444444">매칭하세요</font></td>
		<td align="center"><img src="../img/btn_openmarket_catematch.gif" style="cursor:pointer;" onclick="popupLayer('../shople/popup.config.category.php?category=<?=$v['category']?>&rowIdx=' + this.parentNode.parentNode.rowIndex,750,550);"></a></td>
	<? } ?>

</tr>
<? } ?>
</table>

<div style="padding-top:10px"></div>






<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가에 상품을 등록하려면 11번가 카테고리와 매칭되어야 상품전송이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">최대한 내 쇼핑몰 분류와 가장 유사한 11번가 카테고리를 찾아 매칭해야 판매에 도움이 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">각각의 카테고리를  ‘수정작업’버튼을 클릭하여 매칭하여 주시길 바랍니다.</td></tr>
</table>
</div>
<script type="text/javascript">cssRound('MSG01')</script>

<script type="text/javascript">

</script>
<? include "../_footer.php"; ?>
