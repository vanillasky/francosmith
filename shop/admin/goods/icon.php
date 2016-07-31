<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_icon.php?'.$_SERVER['QUERY_STRING']);
exit;
//$hiddenLeft = 1;
$location = "상품관리 > 빠른 아이콘 수정";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
@include "../../conf/my_icon.php";

// 아이콘 갯수
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

// 아이콘쓰
$ar_icon = array('good_icon_new.gif','good_icon_recomm.gif','good_icon_special.gif','good_icon_popular.gif','good_icon_event.gif','good_icon_reserve.gif','good_icon_best.gif','good_icon_sale.gif');


### 공백 제거
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[blog][$_GET[blog]] = "checked";

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "▲" : "▼";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link
";
$where[] = "a.todaygoods='n'";
if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
if ($_GET['blog']) $where[] = "useblog='y'";

// 아이콘
if (sizeof($_GET[sicon])) {
	if ($_GET[sicon][custom] == 1) {
		unset($_GET[sicon][custom]);
		$_max = sizeof($r_myicon);

		$checked[sicon][custom] = "checked";
	}
	else {
		unset($_GET[sicon][custom]);
		$_max = 8;
	}

	$tmp = array();

	for ($i=0;$i<$_max;$i++) {
		if ($_GET[sicon][$i] > 0) {
			$checked[sicon][$i] = "checked";
			$_bit = pow(2,$i);
			$tmp[] = "(a.icon & $_bit) > 0";
		}
	}

	if (!empty($tmp)) $where[] = implode(' or ',$tmp);

}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,
b.price,b.reserve,a.use_emoney
";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);

?>

<script>

function eSort(obj,fld)
{
	var form = document.frmList;
	if (obj.innerText.charAt(1)=="▲") fld += " desc";
	form.sort.value = fld;
	form.submit();
}

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

var nsMultiIconSet = function() {
	return {
		ar_icons : <?=gd_json_encode($r_myicon)?>
		,


		// 되돌리기
		restore : function() {

			var self = this;

			// 기본 세트
			for (var i=0;i<8 ;i++ )
			{
				$$('.el-checkbox-icon-' + i).each(function(el) {
					el.checked = (el.readAttribute('o_checked') == 'checked') ? true : false;
				});
			}

			// 추가 아이콘
			var oval,img,div,fld,del,icon,goodsno,_id;
			var cnt_myicon = self.ar_icons.length;

			var btn_x = new Element('img', {src:'../img/btn_x.gif'});

			$$('input[name="chk[]"]').each(function(el){
				goodsno = el.value;

				fld = $$('input[name="customicon['+goodsno+']"]')[0];
				oval = parseInt(fld.getAttribute('o_value'));

				if (parseInt(fld.value) != oval && oval > 0) {

					div = $$('.el-custom-icon-' + goodsno)[0];
					div.update('');
					fld.value = oval;

					for (i=8;i<cnt_myicon;i++) {

						_bit = Math.pow(2,i);

						if (self.ar_icons[i] && (oval & _bit) > 0) {
							// 아이콘 출력

							_id = "el-custom-icon-" + goodsno + "-" + _bit;

							del = Element.clone(btn_x);

							del.writeAttribute('goodsno',goodsno);
							del.writeAttribute('icon',_bit);
							del.writeAttribute('p_id',_id);

							del.observe('click', nsMultiIconSet.del);

							img = new Element('img',{src:'../../data/my_icon/'+self.ar_icons[i]});
							icon = new Element('p', {id:_id,style:'padding:0;margin:5px'});

							icon.insert({ bottom:img });
							icon.insert({ bottom:'&nbsp;' });
							icon.insert({ bottom:del });

							div.insert({bottom:icon});

						}
					}
				}
			});
		}
		,
		// 추가 아이콘 설정
		set : function() {

			var img,div,fld,del,icon,goodsno,_id;
			var val = 0;
			var icons = new Array;

			var custom_icons = $$('input[name="custom_icon[]"]:checked');
			if (!custom_icons.length)
			{
				alert('추가할 아이콘을 선택하세요.');
				return false;
			}

			var chks = $$('input[name="chk[]"]:checked');
			if (!chks.length)
			{
				alert('아이콘을 추가할 상품을 선택하세요.');
				return false;
			}

			var btn_x = new Element('img', {src:'../img/btn_x.gif'});

			chks.each(function(el){

				goodsno = el.value;

				div = $$('.el-custom-icon-' + goodsno)[0];
				div.update('');

				fld = $$('input[name="customicon['+goodsno+']"]')[0];

				val = 0;

				custom_icons.each(function(el) {

					val = parseInt(val) + parseInt(el.value);

					_id = "el-custom-icon-" + goodsno + "-" + el.value;

					del = Element.clone(btn_x);

					del.writeAttribute('goodsno',goodsno);
					del.writeAttribute('icon',el.value);
					del.writeAttribute('p_id',_id);

					del.observe('click', nsMultiIconSet.del);

					img = Element.clone(el.next('img'));

					icon = new Element('p', {id:_id,style:'padding:0;margin:5px'});

					icon.insert({ bottom:img });
					icon.insert({ bottom:'&nbsp;' });
					icon.insert({ bottom:del });
					div.insert({bottom:icon});


				});

				fld.value = val;

				el.checked = false;
				iciSelect(el);

			});
		}
		,
		// 추가 아이콘 삭제
		del : function(e) {

			var el = typeof e.type != 'undefined' ? e.srcElement : e;
			fld = $$('input[name="customicon['+el.getAttribute('goodsno')+']"]')[0];
			fld.value = parseInt(fld.value) - parseInt(el.getAttribute('icon'));
			$(el.getAttribute('p_id')).remove();

		}
		,
		// 사용자 아이콘 일괄 삭제
		cs_del : function() {

			var self = this;

			var chks = $$('input[name="chk[]"]:checked');
			if (!chks.length)
			{
				alert('삭제할 상품을 선택하세요.');
				return false;
			}


			if (confirm('선택된 상품의 사용자 아이콘을 삭제하시겠습니까?'))
			{
				// 추가 아이콘
				var div,fld,goodsno;

				chks.each(function(el) {

					goodsno = el.value;

					fld = $$('input[name="customicon['+goodsno+']"]')[0];
					fld.value = 0;

					div = $$('.el-custom-icon-' + goodsno)[0];
					div.update('');

				});

			}

		}
		,
		// 페이지내 모든 상품 아이콘 지정 (기본 세트만)
		multiset : function (id) {
			var idx = 0;
			var bool = true;
			$$('.el-checkbox-icon-' + id).each(function(el){

				if (idx == 0)
				{
					if (el.checked == false) bool = true;
					else bool = false;
				}

				el.checked = bool;

				idx++;
			});
		}

	}
}();




function fnToggleCustomIconSearchForm(c) {

	if (c.checked == true)
		$('el-customicon-search-form').setStyle({display:'block'});
	else
		$('el-customicon-search-form').setStyle({display:'none'});

}



function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>


<form name=frmList>
<input type=hidden name=sort value="<?=$_GET['sort']?>">

<div class="title title_top">빠른 아이콘 수정<span>등록한 상품의 아이콘을 빠르고 편리하게 수정, 관리 하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=34')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>분류선택</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>검색어</td>
	<td colspan=3>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>상품명
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>고유번호
	<option value="goodscd" <?=$selected[skey][goodscd]?>>상품코드
	<option value="keyword" <?=$selected[skey][keyword]?>>유사검색어
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>상품가격</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> 원 -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> 원
	</td>
	<td>브랜드</td>
	<td>
	<select name=brandno>
	<option value="">-- 브랜드 선택 --
	<?
	$bRes = $db->query("select * from gd_goods_brand order by sort");
	while ($tmp=$db->fetch($bRes)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>아이콘</td>
	<td colspan="3" class="noline">
	<?
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $icon = "../../data/my_icon/".$r_myicon[$i];
		else $icon = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];

	?>
	<input type="checkbox" name="sicon[<?=$i?>]" value="<?=(pow(2,$i))?>" <?=$checked[sicon][$i]?>>
	<img src="<?=$icon?>">
	<? } ?>
	<input type="checkbox" name="sicon[custom]" value="1" onClick="fnToggleCustomIconSearchForm(this)" <?=$checked[sicon][custom]?>><font class=extext>사용자 아이콘</font>

	<div id="el-customicon-search-form" style="display:<?=$checked[sicon][custom] ? 'block' : 'none'?>;padding:0;">
		<ul style="margin:0;padding:0;">
			<? for ($i=8;$i<$cnt_myicon;$i++) { ?><? if($r_myicon[$i]) { ?>
			<li class="noline" style="float:left;padding:0 3px 0 0;"><input type="checkbox" name="sicon[<?=$i?>]" value="<?=(pow(2,$i))?>" <?=$checked[sicon][$i]?>><img src="../../data/my_icon/<?=$r_myicon[$i]?>"></li>
			<? } } ?>
		</ul>
		<div style="clear:both;"></div>
	</div>

	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td valign=bottom>
		<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno desc')"><img name=sort_brandno_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno')"><img name=sort_brandno src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="../img/list_down_off.gif"></a></td>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>

<form method="post" action="./indb.php" target="ifrmHidden">
<input type="hidden" name="mode" value="quickicon">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=30></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>전체선택</a></th>
	<th></th>
	<th width=10></th>
	<th>상품명</th>
	<th>등록일</th>
	<th>가격</th>
	<th>재고</th>
	<th>진열</th>
	<?
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $img = "../../data/my_icon/".$r_myicon[$i];
		else $img = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];
	?>
	<th><a href="javascript:void(0);" onClick="nsMultiIconSet.multiset(<?=$i?>)"><img src="<?=$img?>"></a></th>
	<? } ?>
	<th>사용자아이콘</th>
</tr>
<tr><td class=rnd colspan=30></td></tr>
<col width=40 span=2 align=center>
<?
while ($data=$db->fetch($res)){

	$stock = $data['totstock'];

	//$icon = setIcon($data[icon],$data[regdt],"../");

	### 실재고에 따른 자동 품절 처리
	if ($data[usestock] && $stock==0) $data[runout] = 1;
?>
<tr><td height=4 colspan=30></td></tr>
<tr height=25>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><?=$data[goodsnm]?></font></a></td>
	<td align=center><font class=ver81 color=444444><?=substr($data[regdt],0,10)?></td>
	<td align=center>
	<font color=4B4B4B><font class=ver81 color=444444><b><?=number_format($data[price])?></b></font>
	</td>
	<td align=center><font class=ver81 color=444444><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<?
	$o_icon = 0;
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $icon = "../../data/my_icon/".$r_myicon[$i];
		else $icon = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];
//
		$icon_use = ($data[icon] & pow(2,$i)) > 0 ? true : false;
		$o_icon += $icon_use ? pow(2,$i) : 0;
	?>
	<td align=center class="noline"><!--img src="<?=$icon?>"--><input type="checkbox" name="icon[<?=$data[goodsno]?>][<?=$i?>]" class="el-checkbox-icon-<?=$i?>" value="<?=(pow(2,$i))?>" o_checked="<?=$icon_use ? 'checked' : ''?>" <?=$icon_use ? 'checked' : ''?>></td>
	<? } ?>
	<td>
	<input type="hidden" name="customicon[<?=$data[goodsno]?>]" value="<?=($data[icon] - $o_icon)?>" o_value="<?=($data[icon] - $o_icon)?>">
	<div class="el-custom-icon-<?=$data[goodsno]?>">
	<? for ($i=8;$i<$cnt_myicon;$i++) {
		$_bit = pow(2,$i);
		if($r_myicon[$i] && ($data[icon] & $_bit) > 0) { ?>
	<p style="padding:0;margin:5px" id="el-custom-icon-<?=$data[goodsno]?>-<?=$_bit?>">
	<img src="../../data/my_icon/<?=$r_myicon[$i]?>"> <img src="../img/btn_x.gif" p_id="el-custom-icon-<?=$data[goodsno]?>-<?=$_bit?>" goodsno="<?=$data[goodsno]?>" icon="<?=$_bit?>" onClick="nsMultiIconSet.del(this);">
	</p>
	<?
		}
	}
	?>
	</div>
	</td>
</tr>
<tr><td height=4 colspan="30"></td></tr>
<tr><td colspan=30 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=15%></td>
<td width=70% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=15% align="right"><a href="javascript:void(0);" onClick="nsMultiIconSet.cs_del();"><img src="../img/admin_btn_user_delet.gif"></a></td>
</tr></table>

<fieldset style="padding:10px;"><legend> 사용자 아이콘 </legend>
	<ul style="margin:0;padding:0;">
		<? for ($i=8;$i<$cnt_myicon;$i++) { ?><? if($r_myicon[$i]) { ?>
		<li class="noline" style="float:left;padding:3px;"><input type="checkbox" name="custom_icon[]" value="<?=(pow(2,$i))?>"><img src="../../data/my_icon/<?=$r_myicon[$i]?>"></li>
		<? } } ?>
	</ul>

	<div style="clear:both;"></div>

	<div style="text-align:center;border-top:1px solid #DCD8D6;padding-top:10px;">

		<div style="display:inline;padding:5px;"><a href="javascript:void(0);" onClick="nsMultiIconSet.set();"><img src="../img/admin_btn_user_icon.gif" align=absmiddle></a></div>
		<div style="display:inline;padding:5px;"><a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/admin_btn_user_icon01.gif" align=absmiddle></a></font>
	</div>
	</div>

</fieldset>

<div class=button_top>
<a href="javascript:void(0);" onClick="nsMultiIconSet.restore();"><img src="../img/admin_btn_refresh.gif"></a>
<input type=image src="../img/admin_btn_re01.gif">
</div>
</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">진열페이지에 노출되는 아이콘 개수는 최대 7개입니다. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">등록 가능한 아이콘 개수는 최대 30개 입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">8개의 아이콘을 제외한 추가 아이콘은 사용자 아이콘 목록에서 선택, 적용하세요. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">추가 적용된 사용자 아이콘은 list의 사용자 아이콘에 표시됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[원래대로] 클릭후, 반드시 [수정] 버튼 클릭하여 설정을 완료 하여 주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
