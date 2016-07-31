<?
include "../_header.popup.php";
@include_once "../../conf/config.mobileShop.php";

if (empty($_GET['category']) === false) {
	$data = $db->fetch("select * from ".GD_CATEGORY." where category='".$_GET['category']."'",1);

	// 상품분류 연결방식 전환 여부에 따른 처리
	$whereArr	= getCategoryLinkQuery('category', $_GET['category']);
	list($cntGoods) = $db->fetch("select count(".$whereArr['distinct']." goodsno) from ".GD_GOODS_LINK." where ".$whereArr['where']);

	@include "../../conf/category/".$data['category'].".php";
}

$checked['tpl'][$lstcfg['tpl']] = "checked";
$checked['rtpl'][$lstcfg['rtpl']] = "checked";
$checked['hidden'][$data['hidden']] = "checked";
$checked['hidden_mobile'][$data['hidden_mobile']] = "checked";
$selected['level'][$data['level']] = "selected";
$checked['level_auth'][$data['level_auth']] = "checked";
if( $data['level'] != '0') $display['level_auth_div'] = "inline-block";
else $display['level_auth_div'] = "none";
for($i=1; $i<5; $i++){
	if($i == $data['level_auth'])  $display['level_auth_txt'][$i] = "block;";
	else $display['level_auth'][$i] = "none;";
}
$level_nm = array();
if($data['auth_step']){
	$tmp_auth_step = explode(':', $data['auth_step']);
	foreach($tmp_auth_step as $val){
		$checked['auth_step'][$val] = "checked";
	}
}
$checked['sort_type'][$data['sort_type']] = 'checked="checked"';
$selected['manual_sort_on_link_goods_position'][$data['manual_sort_on_link_goods_position']] = 'selected="selected"';

### 그룹정보 가져오기
$res = $db->query("select * from gd_member_grp order by level");
while($tmp = $db->fetch($res))$r_grp[] = $tmp;

### 기존 분류이미지
if($data[useimg])$imgName = getCategoryImg($_GET[category]);

$ar_display_type = array(1 => '갤러리형');
$ar_display_type[] = '리스트형';
$ar_display_type[] = '리스트 그룹형';
$ar_display_type[] = '';//'상품이동형';
$ar_display_type[] = '';//'롤링';
$ar_display_type[] = '';//'스크롤';
$ar_display_type[] = '';//'탭';
$ar_display_type[] = '선택강조';
$ar_display_type[] = '이미지';
$ar_display_type[] = '말풍선';
$ar_display_type[] = '장바구니';
?>

<style>
body {margin:0}
#extra-display-form-wrap {}
.display-type-config-tpl {display:none;}
.display-type-wrap {width:94px;float:left;margin:3px;}
.display-type-wrap img {border:none;width:94px;height:72px;}
.display-type-wrap div {text-align:center;}

.display-type-config {width:100%;background:#e6e6e6;border:2px dotted #f54c01;}
.display-type-config  th, .display-type-config  td {font-weight:normal;text-align:left;}
.display-type-config  th {width:100px;background:#f6f6f6;}
.display-type-config  td {background:#ffffff;}

.display-type-level {float:left; text-align:center; padding-right:5px;}
</style>
<script>
function fun_auth(level){
	if( !level.value ){
		document.getElementById("levelnm").innerHTML = '';
		document.getElementById("level_auth_div").style.display = 'none';
		for(i=0; i<document.getElementsByName("level_auth").length; i++){
			document.getElementsByName("level_auth")[i].disabled = true;
		}
		get_auto_txt(0);
	}
	else {
		document.getElementById("levelnm").innerHTML = '"'+level.options[level.selectedIndex].text+'" 미만의 그룹에게 노출 허용 설정';
		document.getElementById("level_auth_div").style.display = 'inline-block';
		for(i=0; i<document.getElementsByName("level_auth").length; i++){
			document.getElementsByName("level_auth")[i].disabled = false;
		}
	}
}
function get_auto_txt(val){
	for(var i=1; i<5; i++){
		if(val == i) document.getElementById("auth_txt_"+i).style.display = 'block';
		else  document.getElementById("auth_txt_"+i).style.display = 'none';
	}
}

// 디스플레이 유형 관련
function fnSetExtraOption(gid, tid) {	// 진열 그룹 순번, 진열 타입 번호
	if (tid == '상품이동형' || tid == '롤링' || tid == '스크롤' || tid == '탭') {
		alert('해당 디스플레이 유형은 사용할 수 없습니다.');
		return false;
	}
	var oTpl = $(tid);

	var data = <?=$lstcfg ? gd_json_encode($lstcfg) : '{}'?>;
	data.checked = {};
	data.gid = gid;

	$H(data).each(function(pair){
		if (pair.key.indexOf('dOpt') > -1 && pair.value) {
			eval('data.checked.'+ pair.key +' = ["",""];');
			eval('data.checked.'+ pair.key +'['+eval('pair.value.'+gid)+'] = "checked";');
		}
		else if (pair.key.indexOf('alphaRate') > -1 && pair.value)
		{
			data.alphaRate = eval('pair.value.'+gid);
		}
	});


	if (oTpl != null) {
		var tpl = new Template( oTpl.innerHTML.unescapeHTML() );

		var html = tpl.evaluate(data);
		$('gList_').style.display = 'block';

		$('extra-config-wrap-display-type-'+gid).update( html );
		$('extra-config-display-type-'+gid).style.display = 'block';

	}
	else {
		$('extra-config-wrap-display-type-'+gid).update('');
		$('extra-config-display-type-'+gid).style.display = 'none';
	}
}

</script>


<!-- 세부설정 소스 -->
	<textarea id="선택강조" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>효과 대상</th>
		<td class="noline">
		<label><input type="radio" name="lstcfg[dOpt8][#{gid}]" value="1" #{checked.dOpt8[1]}  />선택한 상품만 흐리게</label>
		<label><input type="radio" name="lstcfg[dOpt8][#{gid}]" value="2" #{checked.dOpt8[2]}  />선택한 나머지 상품 흐리게</label>
		</td>
	</tr>
	<tr>
		<th>투명도</th>
		<td>
		<input type="text" name="lstcfg[alphaRate][#{gid}]" value="#{alphaRate}" class="rline"> <font class="extext">0%에 가까울수록 투명해 집니다.</font>
		</td>
	</tr>
	</table></textarea>

	<textarea id="말풍선" class="display-type-config-tpl"><table class="display-type-config">
	<tr>
		<th>배경색</th>
		<td class="noline">
		<label><input type="radio" name="lstcfg[dOpt10][#{gid}]" value="1" #{checked.dOpt10[1]}  />타입1(Black)</label>
		<label><input type="radio" name="lstcfg[dOpt10][#{gid}]" value="2" #{checked.dOpt10[2]}  />타입2(white)</label>
		</td>
	</tr>
	<tr>
		<th>투명도</th>
		<td>
		<input type="text" name="lstcfg[alphaRate][#{gid}]" value="#{alphaRate}" class="rline"> <font class="extext">0%에 가까울수록 투명해 집니다.</font>
		</td>
	</tr>
	</table></textarea>
<!-- 세부설정-->


<form name=form method=post action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="mod_category">
<input type=hidden name=category value="<?=$_GET[category]?>">

<div class="title_sub" style="margin:0">분류만들기/수정/삭제<span>분류명을 생성하고 수정, 삭제합니다. <font class=extext>(입력후 반드시 아래 수정버튼을 누르세요)</font></span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<tr>
	<td>현재분류</td>
	<td>
	<?php if (empty($_GET['category']) === false) { ?>
		<?php echo currPosition($data['category'],1);?>
		<a href="../../goods/goods_list.php?category=<?=$_GET['category']?>" target="_blank"><img src="../img/i_nowview.gif" border="0" align="absmiddle" hspace="10"></a>
	<?php }	else { ?>
		1차분류만들기 (최상위분류)
	<?php } ?>
	</td>
</tr>
<?php if (empty($_GET['category']) === false) { ?>
<tr>
	<td>이 분류의 상품수</td>
	<td><b><?=number_format($cntGoods)?></b>개가 등록되어 있습니다. <font class=extext>(하위분류까지 포함)</font></td>
</tr>
<tr>
	<td>현재분류명 수정</td>
	<td>
	<input type=text name=catnm class=lline required value="<?=$data[catnm]?>" label="현재분류명" maxlen="100">
	&nbsp; 분류코드 : <b><?=$data[category]?></b>
	<div style='font:0;height:5'></div>
	<div class=extext style="font-weight:bold">분류명 노출</div>
	<div class=extext>- 텍스트 방식 : 입력된 텍스트가 노출 됩니다.</div>
	<div class=extext>- 이미지 방식 : 아래의 등록된 이미지가 노출 됩니다.(텍스트 →이미지)</div>
	</td>
</tr>
<tr>
	<td>분류이미지 등록</td>
	<td>
	<input type=file name="img[]"> <input type="checkbox" name="chkimg_0" value="1" class="null"> 삭제
	<?if($imgName[$data['category']][0]){?>
	<div><img src="../../data/category/<?=$imgName[$data['category']][0]?>"></div>
	<?}?>
	</td>
</tr>
<tr>
	<td>마우스오버이미지<br>등록</td>
	<td>
	<input type=file name="img[]"> <input type="checkbox" name="chkimg_1" value="1" class="null"> 삭제
	<?if($imgName[$data['category']][1]){?>
	<div><img src="../../data/category/<?=$imgName[$data['category']][1]?>"></div>
	<?}?>
	</td>
</tr>
<tr>
	<td>분류감추기</td>
	<td class=noline>
<? if (getCateHideCnt(substr($data[category],0,-3))){ ?>
	<input type=hidden name=hidden value='<?=$data[hidden]?>'> <font class=small1 color=E83700>상위분류가 감춤이므로 자동감춤 <font color=0074BA>(이 분류를 보이게 하려면 먼저, 상위분류를 보이는 상태로 바꾸고나서 변경하세요)</font>
<? } else { ?>
	<input type=radio name=hidden value=1 <?=$checked[hidden][1]?>> 감추기
	<input type=radio name=hidden value=0 <?=$checked[hidden][0]?>> 보이기
<? } ?>
	</td>
</tr>
<tr>
	<td>모바일샵에서 감추기</td>
	<td class=noline>
		<?php if($cfgMobileShop['vtype_category']=='1'){?>
			<? if (getCateHideCnt(substr($data[category],0,-3),'mobile')){ ?>
				<input type=hidden name=hidden_mobile value='<?=$data[hidden_mobile]?>'> <font class=small1 color=E83700>상위분류가 감춤이므로 자동감춤 <font color=0074BA>(이 분류를 보이게 하려면 먼저, 상위분류를 보이는 상태로 바꾸고나서 변경하세요)</font>
			<? } else { ?>
				<input type=radio name=hidden_mobile value=1 <?=$checked[hidden_mobile][1]?>> 감추기
				<input type=radio name=hidden_mobile value=0 <?=$checked[hidden_mobile][0]?>> 보이기
			<? } ?>
		<?php }else{?>
			<input type=hidden name=hidden_mobile value="<?php echo $data['hidden'];?>" />
		<font class="red">위의 분류감추기와 동일하게 적용되도록 설정되어있습니다.</font>
		<?php }?>
	</td>
</tr>
<? } ?>
<?php if ($_GET['category']) { ?>
<tr>
	<td>상품진열 타입</td>
	<td class="noline">
		<div style="border: dashed 2px #ff0000; padding: 5px;">
			※ 상품진열타입은 자동진열이 기본으로 설정됩니다.<br/>
			기능 배포일(2013.07.10) 기준으로 기존의 진열 순서는 그대로 유지 되며, 이후 등록한 상품은 카테고리에 등록한 순서대로(최근 등록한 상품이 맨 앞) 진열되어 출력됩니다.
		</div>
		<div>
			<input id="sort-type-auto" type="radio" name="sort_type" value="AUTO" <?php echo $checked['sort_type']['AUTO']; ?>/>
			<label for="sort-type-auto">자동진열</label>
			<span class="extext">가장 최근에 카테고리에 등록된 상품순으로(최근 등록된 상품이 맨앞) 진열되어 출력됩니다.</span>
		</div>
		<div>
			<input id="sort-type-manual" type="radio" name="sort_type" value="MANUAL" <?php echo $checked['sort_type']['MANUAL']; ?>/>
			<label for="sort-type-manual">수동진열</label>
			<span class="extext">
				<a href="adm_goods_sort.php" target="_blank">"분류페이지 상품진열"</a>에서 진열순서를 별도로 설정할 수 있습니다.<br/>
			</span>
			<div style="margin-left: 25px;">
				새로 추가된 상품
				<select name="manual_sort_on_link_goods_position">
					<option value="LAST" <?php echo $selected['manual_sort_on_link_goods_position']['LAST']; ?>>맨 뒤에 진열</option>
					<option value="FIRST" <?php echo $selected['manual_sort_on_link_goods_position']['FIRST']; ?>>맨 앞에 진열</option>
				</select>
			</div>
		</div>
	</td>
</tr>
<?php } ?>
<? if (strlen($_GET[category])<=9){ ?>
<tr>
	<td>하위분류 만들기</td>
	<td><input type=text name=sub  label="하위분류생성" maxlen="30" class="line"> <font class=extext>현재분류의 하위분류를 생성합니다</font></td>
</tr>
<? } ?>
<?if($_GET[category]){?>
<tr>
	<td>접근/구매권한</td>
	<td>
	<select name="level" onchange="fun_auth(this)">
		<option value="">제한없음</option>
		<?
		foreach($r_grp as $k => $v){
			$level_nm[$v[level]] = $v[grpnm];
		?>
		<option value="<?=$v[level]?>" <?=$selected['level'][$v['level']]?>><?=$v[grpnm]?> - lv[<?=$v[level]?>]</option>
		<?
		}
		?>
	</select> 이상의 그룹에게만 접근/구매를 허용합니다.
	<div id="levelnm" style="margin-top:15px;">
	<? if($data['level']) echo '"'.$level_nm[$data['level']].'" 미만의 그룹에게 노출 허용 설정';
	?>
	</div>
	<div id="level_auth_div" style="display:<?= $display['level_auth_div']?>; padding:2px,0,0,0;">
		<div class="display-type-level">
			<div><img src="../img/auth_01.gif" style="border:0"></div>
			<input type="radio" name="level_auth" value="1" style="border:0" onclick="get_auto_txt(this.value)" <?= $checked['level_auth'][1]?>>
		</div>
		<div class="display-type-level">
			<img src="../img/auth_arrow.gif">
		</div>
		<div class="display-type-level">
			<div><img src="../img/auth_02.gif" style="border:0"></div>
			<input type="radio" name="level_auth" value="2" style="border:0" onclick="get_auto_txt(this.value)" <?= $checked['level_auth'][2]?>>
		</div>
		<div class="display-type-level">
			<img src="../img/auth_arrow.gif">
		</div>
		<div class="display-type-level">
			<div><img src="../img/auth_03.gif" style="border:0"></div>
			<input type="radio" name="level_auth" value="3" style="border:0" onclick="get_auto_txt(this.value)" <?= $checked['level_auth'][3]?>>
		</div>
		<div class="display-type-level">
			<img src="../img/auth_arrow.gif">
		</div>
		<div class="display-type-level">
			<div><img src="../img/auth_04.gif" style="border:0"></div>
			<input type="radio" name="level_auth" value="4" style="border:0" onclick="get_auto_txt(this.value)" <?= $checked['level_auth'][4]?>>
		</div>
	</div>
	<div id="auth_txt_1" class="extext" style="margin-top:10px; display:<?= $display['level_auth'][1]?>;">
	카테고리>상세페이지 까지 모두 노출되지 않습니다<div style="padding:2px;"></div>
	<b>하위 카테고리명 까지 모두 노출되지 않습니다.</b><br/>하위 카테고리의 접근/구매 권한설정을 현 카테고리의 허용 그룹보다 낮게 설정하여도<br/>카테고리명은 모두 노출되지 않습니다. 이점 유의하여 설정하여 주세요.
	</div>
	<div id="auth_txt_2" class="extext" style="margin-top:10px; display:<?= $display['level_auth'][2]?>;">
	카테고리명 까지 노출되며 상품리스트 페이지 접근이 되지 않습니다.
	</div>
	<div id="auth_txt_3" class="extext" style="margin-top:10px; display:<?= $display['level_auth'][3]?>;">
	상품리스트 까지 노출되며 상품클릭시 상세페이지 접근이 되지 않습니다.<div style="padding:2px;"></div>
	상품노출값 선택
	<label><input type="checkbox" name="auth_step[]" value="1" style="border:0" disabled checked="checked" onclick="return(false)" <?= $checked['auth_step'][1]?>/> 상품이미지</label>
	<label><input type="checkbox" name="auth_step[]" value="2" style="border:0" <?= $checked['auth_step'][2]?>/> 상품명</label>
	<label><input type="checkbox" name="auth_step[]" value="3" style="border:0" <?= $checked['auth_step'][3]?>/> 가격</label><br/>
	상품이미지, 상품명, 가격을 제외한 아이콘 및 쿠폰할인 등의 상품정보는 노출할 수 없습니다.
	</div>
	<div id="auth_txt_4" class="extext" style="margin-top:10px; display:<?= $display['level_auth'][4]?>;">
	상세페이지 까지 노출되며, 구매/장바구니/상품보관함의 이용권한이 없습니다.<br/>
	<b>상세페이지 상품명과 가격표시는 상품리스트 상품 노출값 설정에 따라 동일하게 적용됩니다.</b>
	</div>
	</td>
</tr>
<tr>
	<td>분류삭제</td>
	<td><a href="javascript:if (document.form.category.value) parent.popupLayer('popup.delCategory.php?category='+document.form.category.value);else alert('전체분류는 삭제대상이 아닙니다');"><img src="../img/i_del.gif" border=0 align=absmiddle></a> <font class=extext>분류삭제시 하위분류도 함께 삭제됩니다. 신중히 삭제하세요.</font></td>
</tr>
<? } ?>
<?
	if($_GET['category']) {
		$ssResult = $db->query("SELECT * FROM ".GD_GOODS_SMART_SEARCH." order by if(basic='y',0,1),themenm");
?>
<script language="JavaScript">
	function goSmartSearch() {
		if(!$('themeno').value) {
			alert("SMART검색 테마를 선택해 주세요.");
			$('themeno').focus();
			return false;
		}
		else top.location.href='../goods/smart_search_register.php?mode=setTheme&no=' + $('themeno').value;
	}
</script>
<tr>
	<td>SMART검색 설정</td>
	<td>
		테마선택
		<select name="themeno" id="themeno"<?=($cfg['smartSearch_useyn'] == 'n') ? " disabled='true'" : ""?>>
			<option value="">사용안함</option>
	<? if($cfg['smartSearch_useyn'] == 'y') while($ssData = $db->fetch($ssResult)) { ?>
			<option value="<?=$ssData['sno']?>"<?=(($ssData['sno'] == $data['themeno']) || ($data['themeno'] == "0" && $ssData['basic'] == 'y')) ? " selected" : ""?>><?=htmlspecialchars($ssData['themenm']).(($ssData['basic'] == 'y') ? "(기본테마)" : "")?></option>
	<? } ?>
		</select>
		<a href="javascript:;" onclick="goSmartSearch()" target="_top"><img src="../img/btn_theme_modify.gif" align="absmiddle" title="테마보기/수정" /></a>
		<a href="../goods/smart_search_register.php" target="_top"><img src="../img/btn_theme_add.gif" align="absmiddle" title="테마추가/등록" /></a><br />
		<span class="extext">선택된 테마로 SMART검색 메뉴가 구성됩니다.</span>
	</td>
</tr>
<?
	}
?>
</table>

<? if ($_GET[category]){ ?>
<div class="title_sub">분류페이지 상단부분 꾸미기<span>분류페이지의 추천상품을 선정하고 HTML을 이용하여 꾸미기합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr id="gList_">
	<td>이 분류의<br>추천상품 선정<div style="padding-top:3px"></div>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_display.html',850,523)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2></a></td>
	<td style="padding:5px">

	<?
	$query = "
	select
		a.mode,a.goodsno,b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS_DISPLAY." a,
		".GD_GOODS." b,
		".GD_GOODS_OPTION." c
	where
		a.goodsno=b.goodsno
		and a.goodsno=c.goodsno and link and go_is_deleted <> '1'
		and a.mode = '$_GET[category]'
	";
	$res = $db->query($query);
	?>

	<div id=divRefer style="position:relative;z-index:99">
		<div style="padding-bottom:3px"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 수정버튼을 누르셔야 최종 저장이 됩니다.</font></div>
		<div>
			<div id=referX style="padding-top:3px">
			<? while ($v=$db->fetch($res)){ ?>
				<?=goodsimg($v[img_s], '40,40','',1)?>
				<input type=hidden name=e_refer[] value="<?=$v[goodsno]?>">
			<? } ?>
			</div>
		</div>
	</div>

	</td>
</tr>
<tr>
	<td>디스플레이유형</td>
	<td>

	<? for ($t=1,$m=sizeof($ar_display_type);$t<=$m;$t++) { ?>
	<? if ($ar_display_type[$t] == '') continue; ?>
	<div class="display-type-wrap">
		<img src="../img/goodalign_style_<?=sprintf('%02d',$t)?>.gif"  alt="<?=$ar_display_type[$t]?>">
		<div class="noline">
		<input type=radio name=lstcfg[rtpl] value="tpl_<?=sprintf('%02d',$t)?>" <?=$checked[rtpl]['tpl_'.sprintf('%02d',$t)]?>  onclick="fnSetExtraOption('rtpl','<?=$ar_display_type[$t]?>')">
		</div>
	</div>
	<? } ?>
	</td>
</tr>
<tr id="extra-config-display-type-rtpl" style="display:none;">
	<td>세부 설정</td>
	<td id="extra-config-wrap-display-type-rtpl"></td>
</tr>
<tr>
	<td>추천상품 출력수</td>
	<td><input type=text name=lstcfg[rpage_num] value="<?=$lstcfg[rpage_num]?>" class="rline"> 개 <font class=extext>보여질 추천상품개수를 넣으세요</td>
</tr>
<tr>
	<td>라인당 상품수</td>
	<td><input type=text name=lstcfg[rcols] value="<?=$lstcfg[rcols]?>" class="rline"> 개 <font class=extext>한줄에 보여질 상품개수를 넣으세요 (5개 이하 권장)</td>
</tr>
<tr>
	<td>상단꾸미기<br><font class=extext>(HTML 버튼을 누르면 소스수정이 가능합니다)</font></td>
	<td height=300 style="padding:5px">
	<textarea name=lstcfg[body] style="width:100%;height:300px" type=editor><?=stripslashes($lstcfg[body])?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
</table>
<? $t = (int)(array_pop(explode('_',$lstcfg[rtpl]))); ?>
<script language="JavaScript">fnSetExtraOption('rtpl','<?=$ar_display_type[$t]?>')</script>


<div class="title_sub">분류페이지 리스트부분 꾸미기<span>상품분류페이지 하단의 리스트부분을 꾸밉니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>디스플레이유형</td>
	<td>

	<? for ($t=1,$m=sizeof($ar_display_type);$t<=$m;$t++) { ?>
	<? if ($ar_display_type[$t] == '') continue; ?>
	<div class="display-type-wrap">
		<img src="../img/goodalign_style_<?=sprintf('%02d',$t)?>.gif"  alt="<?=$ar_display_type[$t]?>">
		<div class="noline">
		<input type=radio name=lstcfg[tpl] value="tpl_<?=sprintf('%02d',$t)?>" <?=$checked[tpl]['tpl_'.sprintf('%02d',$t)]?>  onclick="fnSetExtraOption('tpl','<?=$ar_display_type[$t]?>')">
		</div>
	</div>
	<? } ?>

	</td>
</tr>
<tr id="extra-config-display-type-tpl" style="display:none;">
	<td>세부 설정</td>
	<td id="extra-config-wrap-display-type-tpl"></td>
</tr>
<tr>
	<td>페이지당 상품출력수</td>
	<td><input type=text name=lstcfg[page_num] value="<?=@implode(",",$lstcfg[page_num])?>" option="regPNum" msgO="페이지당 상품출력수를 20,40,60,80 과 같이 입력해주십시요" class="rline"> 개 <font class=extext>예) 20,40,60,80 (첫번째숫자는 기본출력수, 다음숫자부터는 출력수량조정) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=6')"><img src="../img/icon_sample.gif" border=0 align=absmiddle hspace=2></a></td>
</tr>
<tr>
	<td>라인당 상품수</td>
	<td><input type=text name=lstcfg[cols] value="<?=$lstcfg[cols]?>" class="rline"> 개 <font class=extext>한줄에 보여질 상품개수를 넣으세요 (5개 이하 권장)</td>
</tr>
<tr>
	<td height=28 colspan=2 class=extext style="padding-bottom:2">상품들의 진열순서의 변경은 <a href="/shop/admin/goods/sort.php" target=_blank><font class=extext_l>[분류페이지 상품진열]</font></a> 에서 손쉽게 수정가능합니다.
</tr>
<tr>
	<td>하위분류 동일적용</td>
	<td><?if($_GET[category]){?><input type="checkbox" name="chkdesign" value="1" class="null">하위분류에도 위에서 설정한 내용들을 동일하게 적용합니다.<?}?>
	<div style="padding-top:3px" class=extext>위의 '분류페이지 상단부분 꾸미기와 '분류페이지 리스트부분 꾸미기'에서 설정한 내용을 하위분류에도 동일하게 적용시키는 기능입니다</div></td>
</tr>
</table>
<? $t = (int)(array_pop(explode('_',$lstcfg[tpl]))); ?>
<script language="JavaScript">fnSetExtraOption('tpl','<?=$ar_display_type[$t]?>')</script>

<? } ?>

<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table class="small_ex">
<tr><td>
<img src="../img/icon_list.gif" align=absmiddle>상품분류탐색기에서 1차분류만들기 (최상위분류)를 누르면 1차분류를 생성할 수 있습니다.<br>
<img src="../img/icon_list.gif" align=absmiddle>분류페이지상단에서 이벤트나 배너를 배치하여 차별화될 수 있게 디자인해보세요.<br>
<img src="../img/icon_list.gif" align=absmiddle>분류순서변경은 해당분류를 선택후 키보드의 상하이동키↓↑로 조정하고 수정을 눌러 저장합니다.
</table>
</div>

<script>cssRound('MSG01')</script>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCategory').style.height = document.body.scrollHeight;
}
<? if ($_GET[focus]=="sub"){ ?>
document.form.sub.focus();
<? } ?>
</script>