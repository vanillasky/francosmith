<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_soldout.php?'.$_SERVER['QUERY_STRING']);
exit;
/**
	2011-01-18 by x-ta-c
	옵션을 제외한 상품의 품절 여부를 조회하여 이를 수정할 수 있다.

	상품 등록정보 中 "품절상품"에 체크된 상품만 리스팅 됨(즉, gd_goods.runout 필드의 값이 1인 레코드만)
*/

$location = "상품일괄관리 > 빠른 품절수정";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";



// 변수 받기 및 기본값 설정
	$_GET['cate'] = isset($_GET['cate']) ? $_GET['cate'] : array();
	$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : '';
	$_GET['sword'] = isset($_GET['sword']) ? $_GET['sword'] : '';
	$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;
	$_GET['runout'] = isset($_GET['runout']) ? $_GET['runout'] : 1;		// 기본값 1.


// 쿼리 만들기$where[] = '  a.runout = 1';	// 강제 품절 상품만
	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link = 1
	";


	if (!empty($_GET[cate])) {
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];

		/// 카테고리가 있는 경우 대상 테이블 재정의
		if ($category) {
			$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
			$where[] = "category like '$category%'";
			$groupby = "group by b.sno";
		}
	}

	if ($_GET['runout']) {
		$where[] = '  a.runout = 1';	// 품절 상품만
	}




	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";


// 전체 상품수 (품절건만)
	list ($total) = $db->fetch("select count(*) from ".$db_table." where a.runout = 1");


	$orderby = "a.goodsno desc";


// 레코드 가져오기
	$pg = new Page($_GET[page],$_GET[page_num]);
	$pg->field = "a.goodsno,a.img_s,a.goodsnm,a.open,a.usestock,a.runout,b.*";
	$pg->setQuery($db_table,$where,$orderby,$groupby);
	$pg->exec();
	$res = $db->query($pg->query);
?>
<script><!--

function fnToggleGoodsStatAll() {
	$$('.goods_stat > input[type="checkbox"]').each(function(o){
		o.checked = (o.checked == true) ? false : true;
		<? /* 해당 객체에 이벤트가 바인드 되어 있으므로 아래처럼 trigger 를 날려 주거나, 직접 함수를 호출해도 됨. */ ?>
		o.fireEvent('onClick');	// fnToggleGoodsStat(o);
	});
}


function fnToggleGoodsStat(o) {

	var indicator, css = 'hide';

	if (o.checked == true)
		css = 'show';

	for (indicator=o.parentNode.firstChild; indicator.nodeType !== 1; indicator=indicator.nextSibling);
	indicator.className = css;

	return;
}

function fnDeleteCheckedRow() {

	// 체크된건 확인
	var cnt=0,i,chk = document.getElementsByName('chk[]');
	for ( i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('삭제하실 상품을 선택해 주세요.');
		return;
	}


	if (confirm('선택 상품을 삭제하시겠습니까?'))
	{
		var f = document.fmList;
		f.mode.value = 'quickdelete';
		f.submit();
	}

}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
--></script>

<style>
div.goods_stat {}
div.goods_stat input {border:none;}
div.goods_stat span {display:block;width:30px;height:12px;}
div.goods_stat span.show {background:url(../img/icn_1.gif) no-repeat 50% 50%;}
div.goods_stat span.hide {background:url(../img/icn_0.gif) no-repeat 50% 50%;}

</style>

<div class="title title_top">빠른 품절수정<span>등록된 상품의 품절여부를 일괄적으로 수정할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- 상품출력조건 : start -->
<form name=frmFilter onsubmit="return chkForm(this)">

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
	<? foreach ( array('goodsnm'=>'상품명','a.goodsno'=>'고유번호','goodscd'=>'상품코드','keyword'=>'유사검색어') as $k => $v) { ?>
		<option value="<?=$k?>" <?=($k == $_GET['skey']) ? 'selected' : ''?>><?=$v?></option>
	<? } ?>
	<? unset($k,$v) ?>
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class=small1>상품출력여부</td>
	<td class="noline">
	<label><input type=radio name=runout value="1" <?=($_GET['runout'] == 1) ? 'checked' : '' ?>>품절상품</label>
	<label><input type=radio name=runout value="0" <?=($_GET['runout'] == 0) ? 'checked' : '' ?>>전체상품</label>
	</td>
</tr>
</table>



<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
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
	</tr>
	</table>

	</td>
</tr>
</table>


</form>
<!-- 상품출력조건 : end -->

<form name="fmList" method="post" action="./indb.php" target="_self">
<input type=hidden name=mode value="quickrunout">


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
	<th><font class=small1><b>번호</th>
	<th><font class=small1><b></th>
	<th><font class=small1><b>상품명</th>
	<th><font class=small1><b><a href="javascript:fnToggleGoodsStatAll()" class="white">품절여부</a></th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col width=50><col><col width=70 span=5><col width=60><col width=35>
<?
while (is_resource($res) && $data=$db->fetch($res)){
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>

	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></font></a></td>

	<td align=center>
		<div class="goods_stat">
			<span class="<?=($data['runout'] == 1) ? 'show' : 'hide'?>"></span>
			<input type="checkbox" name=runout[<?=$data['goodsno']?>] value="1" <?=($data['runout'] == 1) ? 'checked' : ''?> onClick="fnToggleGoodsStat(this);">
			<input type="hidden" name=target[] value="<?=$data['goodsno']?>">
		</div>
	</td>

</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=120 style="padding-left:12">
	<a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	<a href="javascript:fnDeleteCheckedRow();"><img src="../img/btn_all_delet.gif"></a>

</td>
<td width=80% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=120></td>
</tr></table>






<div class=button_top><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">등록된 상품의 상태를 일괄적으로 품절처리 또는 해지 할 수 있습니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품수정 페이지에서 사용자가 직접 품절로 체크한 상품을 검색한 후 상태를 일괄 변경할 수 있습니다.</td></tr>
<tr><tr><td style="padding:4px 0 4px 0;font-weight:bold;"><img src="../img/icon_list.gif" align="absmiddle">YES: 품절처리상태 NO: 판매상태</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">체크박스를 이용하여 상태를 변경한 후 수정 버튼을 클릭하면 상태가 변경 되어 쇼핑몰 페이지에 반영됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>






<? include "../_footer.php"; ?>
