<?

$location = "오픈마켓 다이렉트 서비스 > 분류매칭하기";
$scriptLoad='<link rel="styleSheet" href="./js/style.css">';
$scriptLoad.='<script src="./js/common.js"></script>';
include "../_header.php";

### 상품분류 데이타 추출
$query = "select * from ".GD_CATEGORY." order by category";
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
foreach ($cate1 as $v){
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
}

?>

<div class="title title_top">분류매칭하기 <span>내 쇼핑몰 분류를 오픈마켓 표준분류로 매칭합니다.</span></div>
<div id="useMsg"><script>callUseable('useMsg');</script></div>

<div style="padding:8px 13px;background:#f7f7f7;border:3px solid #C6C6C6;margin-bottom:18px;" id="goodsInfoBox">
<div><font color="#EA0095"><b>필독! 분류매칭이란?</b></font></div>
<div style="padding-top:2px"><font color="#777777">파워오픈마켓을 신청하신 이후 상점은 분류를 오픈마켓 표준분류와 연결시켜야만 합니다.</div>
<div style="padding-top:2px"><font color="#0074BA">아래 기능은 분류을 오픈마켓 표준분류와 연결하는 기능입니다.</font></div>
<div style="padding-top:2px">물론, 상품전송할 때 한 상품씩 따로따로 분류연결을 해도 상관없습니다. 빠르게 분류연결을 하려면 아래 기능을 사용하세요.</div>
</div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="30%"><col width="60%"><col width="10%">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th bgcolor="#000000">내 쇼핑몰 분류</th>
	<th bgcolor="#6F6F6F">오픈마켓 표준분류</th>
	<th bgcolor="#000000">매칭/수정</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0" border="0" id="cateMatchList" style="border-collapse: collapse;">
<col width="30%"><col width="60%"><col width="10%">
<? foreach ($category as $k => $v){ ?>
<tr height="20" class="step<?=$v['step']?>Tr">
	<td class="step<?=$v['step']?>Icon"><?=$v['catnm']?> <catno category="<?=$v['category']?>"></catno></td>
<? if ($v['openmarket']){ ?>
	<td style="padding-left:10px;" id="catnm<?=$k?>">
	<script>callCateNm('<?=$v['openmarket']?>','catnm<?=$k?>');</script>
	</td>
	<td align="center"><img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer('../openmarket/popup.category.php?category=<?=$v['category']?>&defaultOpt=<?=$v['openmarket']?>&rowIdx=' + this.parentNode.parentNode.rowIndex + '&' + new Date().getTime(),650,550);"></td>
<? } else { ?>
	<td style="padding-left:10px;"><font color="#444444">매칭하세요</font></td>
	<td align="center"><img src="../img/btn_openmarket_catematch.gif" style="cursor:pointer;" onclick="popupLayer('../openmarket/popup.category.php?category=<?=$v['category']?>&rowIdx=' + this.parentNode.parentNode.rowIndex + '&' + new Date().getTime(),650,550);"></a></td>
<? } ?>
</tr>
<? } ?>
</table>


<div style="padding-top:10px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">오픈마켓에 상품을 등록하려면 <b>오픈마켓 표준분류와 매칭되어야 상품전송이 가능</b>합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">최대한 내 쇼핑몰 분류와 <b>가장 유사한 오픈마켓 표준분류를 찾아 매칭해야 판매에 도움</b>이 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>