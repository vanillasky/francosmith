<?
/*********************************************************
* 파일명     :  goodsLink.php
* 프로그램명 :  상품링크 관리
* 작성자     :  이훈
* 생성일     :  2012.05.08
**********************************************************/
/*********************************************************
* 수정일     :
* 수정내용   :
**********************************************************/
$location = "셀리 > 상품링크 관리";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.");
	go("./setting.php");
}

$base_delivery = $set['delivery']['default'];
$base_delivery_type = $set['delivery']['deliveryType'];

//선/착불|기본배송비|~원이상무료|착불배송메세지
$sAPI = new sAPI();

$grp_cd = Array("grp_cd"=>"MALL_CD");
$arr_mall_cd = $sAPI->getCode($grp_cd, 'hash');

$tmp_mall_set = $sAPI->getSetList();
$arr_mall_set = array();
if(is_array($tmp_mall_set) && !empty($tmp_mall_set)) {
	foreach($tmp_mall_set as $row_mall_set) {
		$arr_mall_set[$row_mall_set['mall_cd']][$row_mall_set['mall_login_in']][] = $row_mall_set;
	}
}

### 기본설정 배송비(착불) 가져오기 START ###
$query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'basic_payment_delivery_price');
$tmp_data = $db->_select($query);
$base_payment_price = $tmp_data[0]['value'];
### 기본설정 배송비(착불) 가져오기 END ###

$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";

$mall_cd = $_GET['mall'];
if($mall_cd) {
	foreach($mall_cd as $mall) {
		$checked['mall'][$mall] = 'checked';
	}
}

$selected['link_yn'][$_GET['link_yn']] = 'selected';

$order_by = ($_GET['sort']) ? $_GET['sort'] : "-a.goodsno";
$div = explode(" ",$order_by);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$order_by)) ? "▲" : "▼";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}
$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and b.link=1 and go_is_deleted <> '1'
";

if ($category){//분류선택
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

	// 상품분류 연결방식 전환 여부에 따른 처리
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}

if($_GET['brandno']) $where[] = $db->_query_print('a.brandno = [i]', $_GET['brandno']);//브랜드
if($_GET['open']) $where[] = $db->_query_print('a.open = [i]', substr($_GET[open],-1));//상품출력여부
if($_GET['sword']) $where[] = $db->_query_print('a.'.$_GET['skey'].' like [s]', '%'.$_GET['sword'].'%');//검색어

if($_GET['regdt'][0] && $_GET['regdt'][1]) {//상품등록일
	$tmp_sdate = substr($_GET['regdt'][0], 0, 4).'-'.substr($_GET['regdt'][0], 4, 2).'-'.substr($_GET['regdt'][0], 6, 2);
	$tmp_edate = substr($_GET['regdt'][1], 0, 4).'-'.substr($_GET['regdt'][1], 4, 2).'-'.substr($_GET['regdt'][1], 6, 2);
	$where[] = $db->_query_print('a.regdt >= [s] AND a.regdt <= [s]', $tmp_sdate.' 00:00:00', $tmp_edate.' 23:59:59');
}

if($_GET['mall'] && $_GET['link_yn']) {
	if($_GET['link_yn'] == 'n') $not = ' NOT ';
	$tmp_query = $db->_query_print('SELECT * FROM '.GD_MARKET_GOODS.' AS m WHERE  m.mall_cd in [v] AND m.goodsno=a.goodsno', $_GET['mall']);
	$where[] = $not.'EXISTS ('.$tmp_query.')';
}

$where[] = "a.todaygoods='n'";
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = 'DISTINCT a.goodsno,a.goodsnm,a.regdt,a.delivery_type,a.goods_delivery,a.totstock,a.open,a.brandno,a.img_l,b.price';//검색필드
$pg->setQuery($db_table,$where,$order_by);
$pg->exec();
$res = $db->query($pg->query);

$arr_delivery_type = array(
	0 => '기본배송정책',
	1 => '무료배송',
	2 => '상품별 배송비',
	3 => '착불 배송비',
	4 => '고정 배송비',
	5 => '수량별 배송비',
);
?>

<script>

function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}

function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

function all_check() {//마켓 링크여부 전체선택
	var obj = document.getElementsByName('mall[]');
	chkBox(document.getElementsByName('mall[]'),obj[0].checked);
}

var popup_no = 0;

function frm_check() {
	var ch_set = document.getElementsByName('set_cd')[0].value;
	if(!ch_set) {
		alert('세트를 선택하셔야 합니다.');
		return;
	}
//	popupLayer('goodsLinkPop.php',800,700);
//	popup_return( theURL, winName, Width, Height, left, top, scrollbars )
	popup_return('_blank.php', 'link_pop' + popup_no, 800, 700, '', '', 1);//, left, top, scrollbars )


	var fm = document.frmGoodsList;
		fm.target = "link_pop" + popup_no;
		fm.action = "goodsLinkPop.php";
		fm.submit();
	popup_no ++;
}


window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>

<form name="frmList">
	<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">상품 링크<span>이나무의 상품을 오픈마켓에 일괄 링크 하실 수 있는 기능입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

	<table class="tb">
		<col class="cellC"><col class="cellL" style="width:500px">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>분류선택</td>
			<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
		</tr>
		<tr>
			<td>검색어</td>
			<td colspan="3">
			<select name="skey">
				<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명</option>
				<option value="goodsno" <?=$selected['skey']['goodsno']?>>고유번호</option>
				<option value="goodscd" <?=$selected['skey']['goodscd']?>>상품코드</option>
				<option value="keyword" <?=$selected['skey']['keyword']?>>유사검색어</option>
			</select>
			<input type=text name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
			</td>
		</tr>
		<tr>
			<td>상품등록일</td>
			<td>
				<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
				<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
				<a href="javascript:setDate('regdt[]',<?=date('Ymd')?>,<?=date('Ymd')?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-7 day'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-15 day'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-1 month'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-2 month'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
			</td>
			<td>브랜드</td>
			<td>
				<select name="brandno">
					<option value="">-- 브랜드 선택 --</option>
					<?
					$bRes = $db->query("select * from gd_goods_brand order by sort");
					while ($tmp=$db->fetch($bRes)){ ?>
						<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>상품출력여부</td>
			<td class=noline colspan="3">
				<input type="radio" name="open" value="" <?=$checked['open']['']?>>전체
				<input type="radio" name="open" value="11" <?=$checked['open'][11]?>>출력상품
				<input type="radio" name="open" value="10" <?=$checked['open'][10]?>>미출력상품
			</td>
		</tr>
		<tr>
			<td>마켓 링크여부</td>
			<td class=noline colspan="3">
				<label><input type="checkbox" name="mall[]" value="all" <?=$checked['mall']['all']?> onclick="all_check()">전체</label>
				<? if(is_array($arr_mall_cd) && !empty($arr_mall_cd)) { ?>
				<? foreach($arr_mall_cd as $key => $val) {?>
					<? if($key == 'mall0005') continue; ?>
					<label><input type="checkbox" name="mall[]" value="<?=$key?>" <?=$checked['mall'][$key]?>><?=$val?></label>
				<? } ?>
				<? } ?>
				<select name="link_yn">
					<option value="y" <?=$selected['link_yn']['y']?>>링크된 상품</option>
					<option value="n" <?=$selected['link_yn']['n']?>>링크되지 않은 상품</option>
				</select>
			</td>
		</tr>
	</table>

	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="pageInfo"><font class="ver8">
			총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode['total']?></b>개, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages
			</td>
			<td align="right">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="bottom">
				<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name="sort_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name="sort_price" src="../img/list_down_off.gif"></a></td>
				<td style="padding-left:20px">
				<img src="../img/sname_output.gif" align="absmiddle">
				<select name="page_num" onchange="this.form.submit()">
				<?
				$r_pagenum = array(10,20,40,60,100);
				foreach ($r_pagenum as $v){
				?>
				<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
				<? } ?>
				</select>
				</td>
			</tr>
			</table>
			</td>
		</tr>
	</table>
</form>

<form name="frmGoodsList" action="" method="POST">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr><td class="rnd" colspan="12"></td></tr>
		<tr class="rndbg">
			<th width="60"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
			<th>이미지</th>
			<th>상품명</th>
			<th>등록일</th>
			<th>판매가</th>
			<th>배송구분</th>
			<th>재고</th>
			<th>진열</th>
		</tr>
		<tr><td class="rnd" colspan="12"></td></tr>

		<?
		while ($data=$db->fetch($res)) {
		?>
		<tr><td height="4" colspan="12"></td></tr>
		<tr>
			<td align="center" class="noline">
				<input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" />
			</td>
			<td>
			<? if(!$data['img_l']) { ?>
					<input type="image" src="../../data/skin/season3/img/common/noimg_100.gif" style="width:30px;height:30px;" onclick="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600); return false;">
			<? }
				else {
					$arr_img = explode('|', $data['img_l']);
					if(strstr($arr_img[0], 'http://')) {
						$img_url = $arr_img[0];
					}
					else {
						$img_url = '../../data/goods/'.$arr_img[0];
					}
					?>
					<input type="image" src="<?=$img_url?>" style="width:30px;height:30px;" onclick="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600); return false;">
					<?
				}
			?>
			</td>
			<td>
				<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600)"><font color="303030"><?=$data['goodsnm']?></font></a>
				<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
				<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
				<input type="hidden" name="goodsnm[<?=$data['goodsno']?>]" value="<?=$data['goodsnm']?>" class="line" style="height:22px;width:60px;" />
			</td>
			<td align="center">
				<font class=ver81 color=444444><?=substr($data[regdt],0,10)?>
			</td>
			<td align="center">
				<input type="text" name="price[<?=$data['goodsno']?>]" value="<?=$data['price']?>" class="line" style="height:22px;width:60px;" />
			</td>
			<td align="center">
				<?=$arr_delivery_type[$data['delivery_type']]?>
				<?
					$text_type = 'text';
					unset($type_text);
					switch($data['delivery_type']) {
						case '1' ://무료배송
							$text_type = 'hidden';
							$goods_delivery = '0';
							break;
						case '0' ://기본배송정책
							if($base_delivery_type == '후불') {//착불
								$goods_delivery = $base_payment_price;
								$type_text = '착불 ';
							}
							else {//선불
								$goods_delivery = $base_delivery;
								$type_text = '선불 ';
							}
							$read_only = 'readonly';
							break;
						default :
							$goods_delivery = $data['goods_delivery'];
					}
				?>

				<div><?=$type_text?></div><div><input type="<?=$text_type?>" name="goods_delivery[<?=$data['goodsno']?>]" value="<?=$goods_delivery?>" class="line" style="height:22px;width:60px;" <?=$read_only?> /></div>
				<?unset($read_only);?>
			</td>
			<td align=center>
				<font class=ver81 color=444444><?=number_format($data['totstock'])?>
			</td>
			<td align=center>
				<img src="../img/icn_<?=$data[open]?>.gif">
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=12 class=rndline></td></tr>
		<? } ?>
	</table>

	<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

	<div style="margin:10px 0"><font class=extext>※ 셀리에 상품등록시, 연동에 필요한 상품정보 항목을 확인해 주세요.<br />
	셀리에서 필요로하는 필수정보가 e나무 상품정보에 등록되어 있어야 셀리에 정상적으로 상품이 등록됩니다.<br />
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=3')">[상품정보 필수항목 확인하기]</a></font></div>

	<table class=tb>
		<col class=cellC style="width:150px"><col class=cellL>
		<tr>
			<td>세트 선택(링크하기)</td>
			<td>
				<select name="set_cd">
					<option value="">세트를 선택해 주세요.</option>
					<?
					if(is_array($arr_mall_cd) && !empty($arr_mall_cd)) {
						foreach($arr_mall_cd as $key => $val) {
							if($key == 'mall0005') continue;
							if(is_array($arr_mall_set[$key]) && !empty($arr_mall_set[$key])) {
						?>
						<option value="">=====================</option>
						<?
								foreach($arr_mall_set[$key] as $arr_login_id) {
									foreach($arr_login_id as $data) {
						?>
						<option value="<?=$data['set_cd']?>"><?=$val?>(<?=$data['mall_login_id']?>) : <?=$data['set_nm']?></option>
						<?
									}
								}
							}
						}
					}
					 ?>
				</select>
				선택한 세트 정보를 이용하여
				<span class="noline"><input type="image" src="../img/btn_linkmarket.gif" onclick="frm_check();return false;" alt="마켓에 링크하기" align="absbottom"></span>
			</td>
		</tr>
	</table>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
e나무에 등록된 상품과 세트정보를 선택하여 상품링크를 하실 수 있습니다.<br/>
상품링크 시도시 SELLY에 e나무 상품이 자동으로 등록이 되며 이미 등록이 되어 있는 경우 수정됩니다.<br/>
SELLY에 상품이 등록/수정이 완료되면 마켓에 링크를 시도하며 링크성공시 마켓에 실제로 상품이 등록됩니다.<br/><br/><br/>

리스트에서 판매가를 수정하여 상품링크를 하시면 e나무 상품과 다른 판매가를 가진 상품을 SELLY와 마켓에 등록이 가능합니다..<br/>
리스트에서 배송구분이 무료배송이 아닌 경우 판매가와 동일하게 배송비를 수정 하여 상품링크가 가능합니다.<br/>
상품의 배송정책이 기본배송정책일 경우 리스트에서 배송비를 수정하여 상품링크를 하실 수 없습니다.<br/>

무료배송이 아닌 상품을 링크하기 위해서는 <a href="../selly/deliverySetting.php"><font color=white><u>[연동상품 배송비 설정]</u></font></a>에서 e나무 배송값과 SELLY 배송값을 매핑시켜 주셔야 합니다.<br/><br/><br/>

마켓에 링크할 상품을 선택하신 다음 하단에서 링크시 사용될 세트를 선택합니다.<br/>
상품과 세트를 선택하셨다면 마켓에 링크하기 버튼을 클릭하시면 팝업이 띄워지게 됩니다.<br/>
띄워진 팝업에서는 실제로 등록될 카테고리를 선택하실 수 있으며<br/>
카테고리 선택 후 링크하기 버튼을 클릭하면 마켓으로 상품링크를 시도합니다.<br/>
링크성공시 <a href="../selly/linkGoodsList.php"><font color=white><u>[링크상품 관리]</u></font></a>에서 확인/수정링크를 하실 수 있습니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
