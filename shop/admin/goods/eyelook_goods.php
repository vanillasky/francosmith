<?
$location = "상품관리 > 아이룩 상품 이미지 관리";
include "../_header.php";
include "../../lib/page.class.php";

function eyelook_img_delete() {
	global $db;

	$res = $db->query("select goodsno, img_eyelook from ".GD_EYELOOK." where 1 order by idx asc ");
	while ($data=$db->fetch($res,1)){
		list($cnt) = $db->fetch("select count(*) cnt from ".GD_GOODS." where goodsno = '".$data[goodsno]."'");

		if(!$cnt) {

			$_dir	= "../../data/goods/";
			$_dirT	= "../../data/goods/t/";

			if (is_file($_dir.$data[img_eyelook])) @unlink($_dir.$data[img_eyelook]);
			if (is_file($_dirT.$data[img_eyelook])) @unlink($_dirT.$data[img_eyelook]);

			$db->query("delete from ".GD_EYELOOK." where goodsno = '".$data[goodsno]."'");
		}

	}

	include_once "../_mobileapp/admin_eyelook.class.php";
	$eyelook = new admin_eyelook();

	$result = $eyelook -> introduction();
}

eyelook_img_delete();

// 변수 받기 및 기본값 설정
$_GET['cate'] = isset($_GET['cate']) ? $_GET['cate'] : array();
$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : '';
$_GET['sword'] = isset($_GET['sword']) ? $_GET['sword'] : '';
$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;

// 쿼리 만들기
$db_table = GD_EYELOOK." a inner join ".GD_GOODS." b on a.goodsno = b.goodsno inner join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and c.link and go_is_deleted <> '1'";

if (!empty($_GET[cate])) {
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];

	/// 카테고리가 있는 경우 대상 테이블 재정의
	if ($category) {
		$db_table .= " left join ".GD_GOODS_LINK." d on a.goodsno=d.goodsno";

		// 상품분류 연결방식 전환 여부에 따른 처리
		$where[]	= getCategoryLinkQuery('d.category', $category, 'where');
	}
}

if ($_GET['sword']) $where[] = "b.$_GET[skey] like '%$_GET[sword]%'";

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.idx";

// 레코드 가져오기
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.goodsnm, b.img_s, b.runout, b.totstock, b.usestock, c.price";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();
$total = $pg->recode['total'];
$res = $db->query($pg->query);
//debug($pg->query);
?>
<script>
<!--

function sort(sort)
{
	var fm = document.frmFilter;
	fm.sort.value = sort;
	fm.submit();
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function form_submit() {
	obj = eval("document.fmList");

	if(isChked(document.getElementsByName('chk[]'))) {

		if(confirm("선택한 아이룩 상품 이미지 삭제 하시겠습니까?")) {
			obj.submit();
		}
	}
}
-->
</script>



<div class="title title_top">아이룩 상품 이미지 관리<span>고도 관리자 앱에서 이용가능한 ‘아이룩’ 의 상품 이미지를 등록 관리 합니다.</span><!--<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>--></div>

<!-- 상품출력조건 : start -->
<form name=frmFilter onsubmit="return chkForm(this)">
<input type=hidden name=sort value="<?=$_GET['sort']?>">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>분류선택</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td><font class=small1>검색어</td>
	<td>
		<select name=skey>
		<? foreach ( array('goodsnm'=>'상품명','goodsno'=>'고유번호','goodscd'=>'상품코드','keyword'=>'유사검색어') as $k => $v) { ?>
			<option value="<?=$k?>" <?=($k == $_GET['skey']) ? 'selected' : ''?>><?=$v?></option>
		<? } ?>
		<? unset($k,$v) ?>
		</select>
		<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td height="5px"></td></tr>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td valign=bottom>
		<img src="../img/sname_date.gif"><a href="javascript:sort('a.regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('a.regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('b.goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('b.goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a></td>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == $_GET['page_num']) ? 'selected' : ''?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
		<td width="170px" align=right><img src="../img/btn_eyelookimgadd.gif" align="absmiddle" onClick="document.location.href='eyelook_register.php';" style="cursor:pointer;"></td>
	</tr>
	</table>

	</td>
</tr>
<tr><td height="3px"></td></tr>
</table>


</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" action="./eyelook_indb.php" target="_self">
<input type=hidden name=mode value="delete">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60px><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
	<th width=60px><font class=small1><b>번호</th>
	<th width=60px><font class=small1><b></th>
	<th><font class=small1><b>상품명</th>
	<th width=120px><font class=small1><b>가격</th>
	<th width=120px><font class=small1><b>아이룩 이미지</th>
	<th width=200px><font class=small1><b>아이룩이미지등록일</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=60><col width=60><col width=60><col><col width=120><col width=120><col width=200>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	if ($data[usestock] && $data['totstock']==0) $data[runout] = 1;
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[idx]?>" onclick="iciSelect(this)"></td>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td>
		<a href="eyelook_register.php?mode=modify&idx=<?=$data[idx]?>"><font class=small1 color=0074BA><?=$data[goodsnm]?></font></a>
		<? if ($data[runout]){ ?>&nbsp; <img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif" align="absmiddle"><? } ?>
	</td>
	<td align=center><?=number_format($data[price])?></td>
	<td align=center><?=goodsimg($data['img_eyelook'],40,"style='border:1 solid #cccccc' onclick=popupImg('../data/goods/".$data['img_eyelook']."','../') class=hand align=absmiddle",2)?></td>
	<td align=center><?=$data[regdt]?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<td align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
</tr></table>

<div class=button_top><img src="../img/btn_eyelookimgdel.gif" align="absmiddle" onclick="form_submit();" style="cursor:pointer;"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이룩 이미지가 등록된 상품의 리스트입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">등록된 상품 이미지는 ‘고도몰 모바일 관리자’의 ‘아이룩’ 메뉴에서 이용할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">‘아이룩 상품 이미지 등록’ 버튼을 눌러 사용할 아이룩 이미지를 등록할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품명 클릭하시면 아이룩 상품정보 페이지로 이동합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 이미지 클릭 시 해당 상품의 상세페이지를 새창에서 보실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>