<?php
@include_once "../../conf/config.mobileShop.php";
@include_once "../../conf/qr.cfg.php";
@include_once "../../conf/config.purchase.php";
@include "../../conf/my_icon.php";

$processMode = Clib_Application::request()->get('mode', 'register');
$goodsno = Clib_Application::request()->get('goodsno');
$goods = Clib_Application::getModelClass('goods');

if ($goodsno) {
	$goods->load($goodsno);
}
else {
	$goods->setDefaultData();
}

$str_img	= array(
			"i"	=> "메인이미지",
			"s"	=> "리스트이미지",
			"m"	=> "상세이미지",
			"l"	=> "확대(원본)이미지",
			"mobile"	=> "(구)모바일이미지"
			);
$str_mobile_img	= array(
			"w"	=> "메인이미지",
			"x"	=> "리스트이미지",
			"y"	=> "상세이미지",
			"z"	=> "확대(원본)이미지",
			);

// 아이콘 갯수
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

// 관련 상품 (, 로 연결된 상품번호)
$related_goodsnos = '';
if ($processMode=="modify") {

	// 멀티카테고리
	$query = "select category,sort from ".GD_GOODS_LINK." where goodsno='$goodsno' order by category";
	$res = $db->query($query);
	while ($row=$db->fetch($res)) $r_category[$row['category']] = $row[sort];

	// 상품 정보 가져오기
	$_extra_info = $goods['extra_info'];
	//$goods = array_map("slashes",$goods);	// @todo : 이걸 꼭 해줘야 하는지 체크. 해야 하는 경우에는 내부에 메서드를 추가 해야함
	$goods['extra_info'] = $_extra_info;	// extra_info 는 json 스트링이므로 slashes 함수를 이용하면 안됨.
	$goods[launchdt] = str_replace(array('-','00000000'),'',$goods[launchdt]);
	$ex_title = explode("|",$goods[ex_title]);

	// QR 사용 정보 가져오기
	$goods['qrcode'] = Clib_Application::getModelClass('qrcode')->loadGoodsCode($goodsno)->hasLoaded() ? 'y' : 'n';

	for ($i=0;$i<$cnt_myicon;$i++) if ($goods[icon]&pow(2,$i)) $checked[icon][pow(2,$i)] = "checked";

	// 관련상품 리스트 (패치후 수정하지 않은 데이터는 수정 코드 실행)
	if (fixRelationGoods($goods['goodsno'])) $goods[relation] = 'new_type';

	if ($goods[relation]){

		$r_relation = array();

		$query = "
		SELECT
			G.goodsno, G.goodsnm, G.img_s, O.price, G.totstock, G.usestock, G.runout,
			R.r_type, R.r_start, R.r_end, R.regdt AS r_regdt

		FROM ".GD_GOODS_RELATED." AS R

		INNER JOIN ".GD_GOODS." AS G
		ON R.r_goodsno = G.goodsno
		INNER JOIN ".GD_GOODS_OPTION." AS O
		ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1' and go_is_display = '1'

		WHERE
			R.goodsno = $goods[goodsno]

		ORDER BY sort ASC
		";

		$rs = $db->query($query);
		while ($v = $db->fetch($rs,1)) {
			if ($v[usestock] && $v[totstock] < 1) $v[runout] = 1;
			$r_relation[] = $v;
		}
	}

}

if($goods[goods_deli_type] == '선불' || !$goods[goods_deli_type])$goods_deli_type = 0;
if(!$goods['use_emoney']) $goods['use_emoney'] = 0;
if(!$goods['delivery_type']) $goods['delivery_type'] = 0;

else $goods_deli_type = 1;
if(!$goods['detailView']) $goods['detailView'] = 'n'; // 디테일뷰 설정

$goods['use_extra_field'] = ($goods[ex_title]) ? 1 : 0;

$img_i = explode("|",$goods[img_i]);
$img_s = explode("|",$goods[img_s]);
$img_m = explode("|",$goods[img_m]);
$img_l = explode("|",$goods[img_l]);
$img_mobile = explode("|",$goods[img_mobile]);
$img_w = explode("|",$goods[img_w]);
$img_x = explode("|",$goods[img_x]);
$img_y = explode("|",$goods[img_y]);
$img_z = explode("|",$goods[img_z]);

$imgs = $urls = array(
		'l'	=> $img_l,
		'm'	=> $img_m,
		's'	=> $img_s,
		'i'	=> $img_i,
		'mobile'	=> $img_mobile
		);
$mobile_imgs = $mobile_urls = array(
		'z'	=> $img_z,
		'y'	=> $img_y,
		'x'	=> $img_x,
		'w'	=> $img_w
		);

// 이미지 주소가 url일때 처리
if (preg_match('/^http(s)?:\/\//',$img_l[0])) {
	$goods['image_attach_method'] = 'url';
	$imgs	= array(
			'l'	=> array(''),
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			'mobile' => array('')
			);
	$mobile_imgs = array(
			'z'	=> array(''),
			'y'	=> array(''),
			'x'	=> array(''),
			'w'	=> array(''),
			);
}
else {
	$urls	= array(
			'l'	=> array(''),
			'm'	=> array(''),
			's'	=> array(''),
			'i'	=> array(''),
			'mobile' => array('')
			);
	$mobile_urls	= array(
			'z'	=> array(''),
			'y'	=> array(''),
			'x'	=> array(''),
			'w'	=> array(''),
			);

}
// eof 2011-01-21

// 필수옵션
$goodsOptions = $goods->getOptions();

// 추가옵션
$r_addoptnm = explode("|",$goods[addoptnm]);

for ($i=0,$m=sizeof($r_addoptnm);$i<$m;$i++){

	list($name, $require, $type) = explode("^",$r_addoptnm[$i]);

	if (!$name) continue;

	$type = ($type == 'I') ? 'inputable' : 'selectable';

	$additional_option[$type]['name'][] = $name;
	$additional_option[$type]['require'][] = $require;

}

$query = "select * from ".GD_GOODS_ADD." where goodsno > 0 and goodsno='$goodsno' order by type,step,sno";
$res = $db->query($query);
while ($tmp=$db->fetch($res,1)){

	$type = ($tmp['type'] == 'I') ? 'inputable' : 'selectable';

	$additional_option[$type]['sno'][$tmp['step']][] = $tmp['sno'];
	$additional_option[$type]['addno'][$tmp['step']][] = $tmp['addno'];
	$additional_option[$type]['value'][$tmp['step']][] = $tmp['opt'];
	$additional_option[$type]['addprice'][$tmp['step']][] = abs($tmp['addprice']);
	$additional_option[$type]['addprice_operator'][$tmp['step']][] = (int)$tmp['addprice'] > -1 ? '+' : '-';

}

$goods['use_add_option'] = ($additional_option['selectable']) ? 1 : 0;
$goods['use_add_input_option'] = ($additional_option['inputable']) ? 1 : 0;

if (!$additional_option['selectable']) {
	$additional_option['selectable']['name'] = array('');
	$additional_option['selectable']['value'] = array('');
}
if (!$additional_option['inputable']) {
	$additional_option['inputable']['name'] = array('');
	$additional_option['inputable']['value'] = array('');
}

// 아이콘 설정 적용
$arr = array('good_icon_new.gif',
		'good_icon_recomm.gif',
		'good_icon_special.gif',
		'good_icon_popular.gif',
		'good_icon_event.gif',
		'good_icon_reserve.gif',
		'good_icon_best.gif',
		'good_icon_sale.gif');

for($i=0;$i<$cnt_myicon;$i++){
	if($r_myicon[$i])$img = "<img src='../../data/my_icon/".$r_myicon[$i]."'";
	else $img = "<img src='../../data/skin/".$cfg[tplSkin]."/img/icon/".$arr[$i]."'";

	$ti_date = substr($goods[regdt],0,10);
	$r_date = explode('-',$ti_date);

	if($r_myicondt[$i]){
		$date = date('Ymd',mktime(0, 0, 0, $r_date[1], $r_date[2]+$r_myicondt[$i], (int)$r_date[0]));
		if($date < date('Ymd',time())){
			$img .= " style='filter:alpha(opacity=15)'";
		}
	}
	$img .= ">";
	$r_icon[] = $img;
}

// 색상
$colorList = array();
$CL_rs = $db->query("SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort");
while($CL_row = $db->fetch($CL_rs)) $colorList[] = $CL_row['itemnm'];

// 상품별 할인
$discount = $goods->getDiscount();

//
$ruleSets = array();

$_gd_cutting = explode(':', $discount['gd_cutting']);

$goods['goods_discount_by_term_use_cutting'] = $_gd_cutting[0];
$goods['goods_discount_by_term_cutting_unit'] = pow(10, $_gd_cutting[1]-1);
$goods['goods_discount_by_term_cutting_method'] = $_gd_cutting[2];
if ($discount->hasLoaded()) {
	if ($discount['gd_level'] == '*') {
		$goods['goods_discount_by_term_for_specify_member_group'] = '0';
		$goods['goods_discount_by_term_amount_for_all'] = $discount['gd_amount'];
		$goods['goods_discount_by_term_amount_type_for_all'] = $discount['gd_unit'];
	}
	// 회원 및 비회원 전체
	else if ($discount['gd_level'] == '0') {
		$goods['goods_discount_by_term_for_specify_member_group'] = '2';
		$goods['goods_discount_by_term_amount_for_nonmember_all'] = $discount['gd_amount'];
		$goods['goods_discount_by_term_amount_type_for_nonmember_all'] = $discount['gd_unit'];
	}
	else {
		$goods['goods_discount_by_term_for_specify_member_group'] = '1';

		$_gd_level = explode(',', $discount['gd_level']);
		$_gd_amount = explode(',', $discount['gd_amount']);
		$_gd_unit = explode(',', $discount['gd_unit']);

		foreach ($_gd_level as $k => $v) {
			$ruleSets[] = array(
				'target' => $_gd_level[$k],
				'unit' => $_gd_unit[$k],
				'amount' => $_gd_amount[$k],
			);

		}
	}
}


if (empty($ruleSets)) {
	$ruleSets[] = array();
}

// 구 데이터 보정

// 구 데이터 보정

// 폼
$form = Clib_Application::form('admin_goods_register')->setData($goods);

// 회원 그룹
$memberGroups = Clib_Application::getCollectionClass('member_group');
$memberGroups->load();

// 추가옵션바구니
$arDoptExtend = array();
$query = "select * from ".GD_DOPT_EXTEND." order by sno desc";
$res = $db->query($query);
while($rdopt = $db ->fetch($res)){
	$l = strlen($rdopt[title]);

	if($l > 20){
		$rdopt[title] = strcut($rdopt[title],20);
	}

	$rdopt[option] = !empty($rdopt[option]) ? unserialize($rdopt[option]) : $_tmp;
	$rdopt[option] = str_replace("\n","",gd_json_encode($rdopt[option]));	// php4 환경이므로 임시 함수 추가 하였음.

	$arDoptExtend[] = $rdopt;
}
?>

<link rel="stylesheet" type="text/css" href="./css/css.css?ts=<?=date('Ym')?>">
<script type="text/javascript" src="./js/goods_register.js?ts=<?=date('Ym')?>"></script>
<script type="text/javascript" src="../godo_ui.js?ts=<?=date('Ym')?>"></script>
<script type="text/javascript" src="../godo.loading.indicator.js?ts=<?=date('Ym')?>"></script>
<script type="text/javascript" src="../js/adm_form.js?ts=<?=date('Ym')?>"></script>
<script type="text/javascript" src="../js/adm_tab.js?ts=<?=date('Ym')?>"></script>
<script type="text/javascript" src="../proc/warning_disk_js.php?ts=<?=date('Ym')?>"></script><!-- don't delete -->
<script type="text/javascript" src="../../lib/meditor/mini_editor.js?ts=<?=date('Ym')?>"></script>

<div id="enamoo-anchor-helper" style="display:none;"></div>

<table width=800 cellpadding=0 cellspacing=0>
<tr><td align=center><div id=goods_form><? include "../proc/warning_disk_msg.php"; // not_delete ?></td></tr></table>

<form name="fm" id="goods-form" class="admin-form" method="post" target="ifrmHidden" action="indb.goods.php" enctype="multipart/form-data" onsubmit="return nsAdminGoodsForm.validate(this)">
<input type="hidden" name="version" value="2.0">
<input type="hidden" name="mode" value="<?=$processMode?>">
<input type="hidden" name="goodsno" value="<?=$goodsno?>">
<input type="hidden" name="returnUrl" value="<?=$returnUrl?>">
<input type="hidden" name="popup" value="<?=Clib_Application::request()->get('popup')?>">

<!-- S: 상품 분류정보 -->
	<h2 class="title ">상품분류정보<span>한상품에 여러개의 분류를 등록할 수 있습니다&nbsp;(다중분류기능지원)</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2 /></a></h2>

	<div style="width:800px;">

	<table class="admin-form-table">
	<tr>
		<th>선택분류</th>
		<td>
			<table id="objCategory" class="nude padding-midium" style="width:100%;">
			<col><col width="50" style="padding-right:10"><col width="52" align="right">
			<tbody>
			<? if ($r_category){ foreach ($r_category as $k=>$v){ ?>
			<tr>
				<td><?=strip_tags(currPosition($k))?></td>
				<td>
					<input type="text" name="category[]" value="<?=$k?>" style="display:none">
					<input type="hidden" name="sort[]" value="<?=-$v?>" class="sortBox right" maxlength="10">
				</td>
				<td>
					<a href="javascript:void(0);" onclick="nsAdminGoodsForm.category.del(event);"><img src="../img/i_del.gif" border=0 align=absmiddle /></a>
				</td>
			</tr>
			<? }} ?>
			</tbody>
			</table>
			<? if (_CATEGORY_NEW_METHOD_ === true) {?>
			<p class="help">
				<input type="hidden" id="_CATEGORY_NEW_METHOD_" />
				상품분류 등록 시 상위분류는 자동 등록되며, 등록된 분류에 상품이 노출됩니다.<br>
				상품 노출을 원하지 않는 분류는 ‘삭제’버튼을 이용하여 삭제할 수 있습니다.
			</p>
			<?}?>
		</td>
	</tr>
	</table>

	<div style="padding:10px;;border:1px solid #ccc;margin:10px 0 10px 0;">
		<table class="nude">
		<tr>
			<td valign="top"><script type="text/javascript">new categoryBox('cate[]',4,'','multiple');</script></td>
			<td valign="top">
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.category.add();"><img src="../img/i_regist_l.gif" vspace="4" /></a>
			</td>
		</tr>
		</table>
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.category.openCategorySelector();return false;"><img src="../img/buttons/btn_bloc_select.gif" /></a>
	</div>

	<label><input type="checkbox" name="sortTop"><strong>고정 상단진열 적용</strong> : 체크시 이상품을 위에 등록된 해당 각 분류페이지의 최 상단에 보여지게 합니다.</label>
	<p class="help">
		<span class="specialchar">※</span> 주의: 상품분류(카테고리)가 먼저 등록되어 있어야 상품등록이 가능합니다. <a href="/shop/admin/goods/category.php" target="_blank">[상품분류(카테고리) 등록하기]</a>
	</p>

	<!-- 인터파크_카테고리 -->
	<div id="interpark_category"></div>

	</div>
<!-- E: 상품 분류정보 -->

<!-- S: 상품기본정보 -->
	<h2 class="title">상품기본정보<span>제조사, 원산지, 브랜드가 없는 경우 입력안해도 됩니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>상품번호</th>
		<td>
			<span id="el-generated-goodsno"><?=$goods->getId()?></span> (자동생성)
		</td>
		<th>상품코드</th>
		<td>
			<?=$form->getTag('goodscd'); ?> <span class="inputSize:{target:'goodscd',max:30}"></span> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.checkDuplicatedValue(document.fm.goodscd.value, 'goodscd', '<?=$goodsno?>');return false;" class="default-btn"><img src="../img/buttons/btn_repetition.gif" /></a>
		</td>
	</tr>
	<tr>
		<th>상품명 <img src="../img/icons/bullet_compulsory.gif"></th>
		<td colspan="3">
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('goodsnm'); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'goodsnm',max:250}"></span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<label class="help"><?=$form->getTag('meta_title'); ?> 상품명을 상품 상세페이지의 타이틀 태그에 입력됩니다.</label>
		</td>
	</tr>
	<tr>
		<th>모델명</th>
		<td>
			<?=$form->getTag('model_name'); ?> <span class="inputSize:{target:'model_name',max:100}">
		</td>
		<th>상품상태</th>
		<td>
			<?php
			foreach ($form->getTag('goods_status') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
	</tr>
	<tr>
		<th>제조사</th>
		<td>
			<?=$form->getTag('maker'); ?>
			<?=$form->getTag('maker_select'); ?>
		</td>
		<th>원산지</th>
		<td>
			<?=$form->getTag('origin'); ?>
			<?=$form->getTag('origin_select'); ?>
		</td>
	</tr>
	<tr>
		<th>브랜드</th>
		<td>
			<?=$form->getTag('brandno')?>
			<font class=small1 color=444444><a href="brand.php" target=_blank><font class=extext_l><img src="../img/btn_brand_add.gif" /></a>
		</td>
		<th>출시일</th>
		<td>
			<?=$form->getTag('launchdt'); ?> <span class="help">ex) 20080321</span>
			<p class="help">
				네이버 지식쇼핑 입점시 인기도(노출순위)를 결정짓는 중요한 요소입니다
			</p>
			<div style="padding-top:3px"><font class=extext></font></div>
		</td>
	</tr>
	<tr>
		<th>아이콘</th>
		<td>
			<table class="nude">
			<tr>
			<?
				for($j=0;$j<$cnt_myicon;$j++){
					if( $j && $j % 4 == 0 ){ echo "</tr><tr>";}
					echo '<td><label><input type="checkbox" name="icon[]" value="'.pow(2,$j).'" '.$checked[icon][pow(2,$j)].'>'.$r_icon[$j].'</label></td>';
				}
			?>
			</tr>
			</table>
			<p class="help">
				다른 아이콘으로 쉽게 바꿀수 있습니다 <a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/buttons/btn_icon_plus.gif" align=absmiddle /></a>
			</p>

		</td>
		<th>상품 대표색상</th>
		<td>
			<?=$form->getTag('color'); ?>

			<table class="nude">
			<tr>
			<? for($i = 0, $imax = count($colorList); $i < $imax; $i++) {
			echo "<td><div class=\"paletteColor\" style=\"background-color:#".$colorList[$i].";\" onclick=\"nsAdminGoodsForm.color.select(this.style.backgroundColor)\"></div></td>";
			if($imax / 2 == $i + 1) echo "</tr><tr>";
			}
			?>
			</tr>
			</table>

			<div class="selColorText">선택색상 :&nbsp;</div>
			<div id="selectedColor" title="선택된 색은 더블클릭으로 삭제하실 수 있습니다.">&nbsp;</div>

			<div style="padding:5px 0px 0px 0px; clear:left;"><font class=extext>상품 색상 검색시에 사용됩니다.</font></div>
		</td>
	</tr>
	<tr>
		<th>상품진열<br />(노출)여부 <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
			<?php
			foreach ($form->getTag('open') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
		<th>모바일샵 진열(노출)여부</th>
		<td>
			<?php if($cfgMobileShop['vtype_goods']=='1'){?>
			<input type="checkbox" name="open_mobile" value=1 <?=$goods['open_mobile'] ? 'checked' : ''?>>보이기
			<font class=extext>(체크해제시 모바일샵 화면에서 안보임)</font>
			<?php }else{?>
			<input type="hidden" name="open_mobile" value="<?php echo $goods['open'];?>" >
			<font class="red">온라인 쇼핑몰과 동일하게 상품 진열(노출)이 적용되도록 설정되어 있습니다.</font>
			<?php }?>
		</td>
	</tr>
	<tr>
	</tr>
	<tr>
		<th>유사검색어</th>
		<td colspan="3">
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('keyword', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'keyword',max:250}"></span>
				</div>
				<div style="clear:both;"></div>
			</div>

			<p class="help">
				상품상세 페이지의 메타태그와 상품 검색시 키워드로 사용하실 수 있습니다.
			</p>
		</td>
	</tr>
	</table>
<!-- E: 상품기본정보 -->

<!-- S: 상품추가정보 -->
	<h2 class="title">상품별 추가정보 <span>상품특성에 맞게 항목을 추가할 수 있습니다 (예. 감독, 저자, 출판사, 유통사, 상품영문명 등) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span>
	<a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_infoadd.html',650,610)"><img src="../img/icon_sample.gif" border="0" align=absmiddle hspace=2 /></a></h2>

	<div class=noline style="padding-bottom:5px">
	<?php
	foreach ($form->getTag('use_extra_field') as $label => $tag) {
		echo sprintf('<label>%s%s</label> ',$tag, $label);
	}
	?>
	</div>

	<table id=tbEx class="admin-form-table IF_use_extra_field_IS_1">
	<tr>
		<? for ($i=0;$i<6;$i++){ $ex = "ex".($i+1); ?>
		<th><input type="text" name="title[]" class="exTitle gray" value="<?=$ex_title[$i]?>" onblur="if(!nsAdminGoodsForm.checkExtrainfoTitle())alert('항목명은 중복될 수 없습니다.')"></th>
		<td>
			<div class="field-wrapper">
				<input type="text" name="ex[]" value="<?=$goods[$ex]?>">
			</div>
		</td>
		<? if ($i%2){ ?></tr><tr><? } ?>
		<? } ?>
	</tr>
	</table>
<!-- E: 상품추가정보 -->

<!-- S: 사입처 정보 -->
	<?
	if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") {
		if($goodsno) $pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '$goodsno' ORDER BY pchsdt DESC LIMIT 1");
		$rs_pchs = $db->query("SELECT * FROM ".GD_PURCHASE." ORDER BY comnm ASC");
	?>
	<h2 class="title">사입처 정보 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>사입처</th>
		<td>
			<select name="pchsno" id="pchsno"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>>
				<option value="">사입처선택</option>
				<? while ($row_pchs = $db->fetch($rs_pchs)) { ?>
				<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsData['pchsno']) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
				<? } ?>
			</select>
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.purchase.openSelector()"><img src="../img/purchase_find.gif" title="사입처 검색" align="absmiddle" /></a>
		</td>
		<th>사입일</th>
		<td>
			<input type=text name=pchs_pchsdt id="pchs_pchsdt" size=10 value="" onclick="calendar(event);" onkeydown="onlynumber()" class="line"<?=$processMode == "modify" ? ' disabled="true"' : ''?>>
		</td>
	</tr>
	</table>

	<p class="help">
	- 사입처 변경 후 저장 하시면 해당 사입처로 사입 이력이 저장 됩니다.<br />
	- 이미 사입처 연동 사용중인 상품을  "사용 안 함" 으로 변경 시 이 후 이력이 저장 되지 않습니다.<br />
	* 주의 : 사입일이 지정 되어 있어야 상품등록이 가능합니다.
	</p>
	<? } ?>
<!-- E: 사입처 정보 -->

<!-- S: 상품 가격정책 -->
	<h2 class="title">상품 가격정책 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>판매가격(KRW) <img src="../img/icons/bullet_compulsory.gif"></th>
		<td colspan="3">
			<?=$form->getTag('goods_price'); ?> 원
		</td>
	</tr>
	<tr>
		<th>과세설정</th>
		<td>
			<? $tags = $form->getTag('tax'); ?>
			<label><?=$tags['과세']?>과세</label>
			<label><?=$tags['비과세']?>면세</label>
			<p class="help">
			과세로 설정시에 세금계산서 신청/발행 됨
			</p>
		</td>
		<th>소비자가격</th>
		<td><?=$form->getTag('goods_consumer'); ?></td>
	</tr>
	<tr>
		<th>매입가격</th>
		<td><?=$form->getTag('goods_supply'); ?></td>
		<th>공급가격</th>
		<td><?=$form->getTag('provider_price'); ?></td>
	</tr>
	<tr>
		<th>가격 대체문구</th>
		<td colspan="3"><?=$form->getTag('strprice'); ?> <span class="inputSize:{target:'strprice',max:20}"></span> <span class="help"> 가격 대체문구 입력/등록 시 해당 상품 주문되지 않음</span></td>
	</tr>
	<tr>
		<th>적립금 설정</th>
		<td colspan="3">
			<?php
			$tags = $form->getTag('use_emoney');
			?>
			<label><?=$tags[0]?> 기본정책 적용</label> <span class="help"><a href="../basic/emoney.php" target="_blank">[기본관리 > 적립금설정 > 상품 마일리지(적립금) 지급에 대한 정책]</a> 에서 설정 기준에 따름</span> <br />
			<label><?=$tags[1]?> 상품 개별 마일리지(적립금) 설정</label> : <?=$form->getTag('goods_reserve')?>원
		</td>
	</tr>
	</table>
<!-- E: 상품 가격정책 -->

<!-- S: 상품 재고/옵션 -->
	<h2 class="title">상품 재고/옵션 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>재고량</th>
		<td>
			<?=$form->getTag('totstock'); ?>
			<p class="help">
			(오른쪽 "재고량연동" 체크시 재고량이 차감됩니다)
			</p>
		</td>
		<th>재고량연동</th>
		<td>
			<label><?=$form->getTag('usestock'); ?>주문시 재고량 빠짐</label>
			<p class="help">
			(체크안하면 재고량 상관없이 무한정 판매)
			</p>
		</td>
	</tr>
	<? if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") { ?>
	<tr>
		<th>입고량</th>
		<td colspan="3">
			<input type="text" name="pchs_stock" id="pchs_stock" value="" />
		</td>
	</tr>
	<? } ?>
	<tr>
		<th>구매수량 설정</th>
		<td colspan="3">
			최소구매수량 : <?=$form->getTag('min_ea'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;최대구매수량 : <?=$form->getTag('max_ea'); ?>
			<p class="help">
			0 이면 제한이 없습니다. 설정된 구매수량(최소구매수량, 최대구매수량)은 각 주문 한건에 대한 제한사항이며, 옵션이 있는 경우 옵션별로 각각 적용됩니다.
			</p>
		</td>
	</tr>
	<tr>
		<th>재입고 알림</th>
		<td colspan="3">
			<label><?=$form->getTag('use_stocked_noti'); ?>상품 재입고 알림 사용</label>
			<span class="help">
			상품/옵션 품절시 상세페이지에 재입고 알림신청 버튼 노출
			</span>
		</td>
	</tr>
	<tr>
		<th>판매기간 설정</th>
		<td colspan="3">
			시작일/종료일 :

			<?
			$salesRange = $goods->getSalesRange();

			$salesRangeDate = array();
			$salesRangeHour = array();
			$salesRangeMin = array();

			foreach($salesRange as $k => $time) {
				$salesRangeDate[$k] = $time ? date('Ymd', $time) : '';
				$salesRangeHour[$k] = $time ? date('H', $time) : '';
				$salesRangeMin[$k] = $time ? date('i', $time) : '';
			}
			?>
			<input type=text name="sales_range_date[]" value="<?=$salesRangeDate[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
			<select name="sales_range_hour[]">
			<? for($i = 0; $i < 24; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeHour[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>시
			<select name="sales_range_min[]">
			<? for($i = 0; $i < 60; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeMin[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>분
			 -
			<input type=text name="sales_range_date[]" value="<?=$salesRangeDate[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
			<select name="sales_range_hour[]">
			<? for($i = 0; $i < 24; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeHour[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>시
			<select name="sales_range_min[]">
			<? for($i = 0; $i < 60; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeMin[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>분

			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+7 day"))?>)"><img src="../img/sicon_week.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+15 day"))?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+1 month"))?>)"><img src="../img/sicon_month.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+2 month"))?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle /></a>

			<p class="help">
			설정된 기간 동안만 판매 가능하며, 설정된 종료일 이후에는 구매되지 않습니다. <br/>
			일시 판매중지 처리하실 경우, 종료일을 현재날짜 이전의 과거 날짜를 넣어주시면 됩니다.
			</p>

		</td>
	</tr>
	<tr>
		<th>품절여부 설정</th>
		<td colspan="3">
			<label><?=$form->getTag('runout'); ?>품절로 표시</label>
			<span class="help">해당상품을 품절 처리 합니다. [품절상품 진열설정] 에서 페이지별 노출 여부를 설정 할 수 있습니다.</span>
		</td>
	</tr>
	<tr>
		<th>묶음주문 단위</th>
		<td colspan="3">
			<?=$form->getTag('sales_unit'); ?> 개
			<p class="help">
				설정된 개수 단위로 주문 되며, 장바구니에 담깁니다. 배송단위와는 다르며 설정된 단위로 묶음배송 되지 않습니다. <br/>
				주문시에만 적용되며, 부분취소시엔 적용되지 않습니다.
			</p>
		</td>
	</tr>
	</table>

	<?
	if ($goodsOptions->count() === 1) {
		echo '<input type="hidden" name="sno" value="'.($goodsOptions->getIterator()->current()->getId()).'">';
	}
	?>

	<div class="button-container al">
	<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.toggle();"><img src="../img/btn_priceopt_add.gif" id="el-use-option-toggle-button"></a>
	<span class="help" id="el-use-option-toggle-help">이 상품의 옵션이 여러개인경우 등록하세요 (색상, 사이즈 등)</span>
	</div>

	<?=$form->getTag('use_option'); ?>

	<table class="admin-form-table" id="el-option-form">

	<? if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") { ?>
	<tr class="IF_mode_IS_register">
		<th>사입처 적용기준</th>
		<td>
			<div>
				<label><input type="radio" name="purchaseApplyOption" value="1" checked onclick="nsAdminGoodsForm.purchase.setType(1);" /> 사입처 동일적용 <span class="help">추가옵션이 동일한 사입처에서 입고 된 경우</span></label>
			</div>
			<div>
				<label><input type="radio" name="purchaseApplyOption" value="2" onclick="nsAdminGoodsForm.purchase.setType(2);" /> 사입처 개별적용 <span class="help">추가옵션이 각각 다른 사입처에서 입고 된 경우 (등록 후, 재고 입력)</span></label>
			</div>
		</td>
	</tr>
	<? } ?>
	<!-- if 다수 옵션 -->
	<?
	// 옵션명 길이
	$optionNames = $goods->getOptionName();
	$optionValues = $goods->getOptionValue();
	$optionNamesSize = sizeof($optionNames);
	?>
	<? if ($processMode == 'register' || $goodsOptions->count() > 1) { ?>
	<tr>
		<th>옵션 출력방식</th>
		<td>
			<?php
			$tags = $form->getTag('opttype');
			?>
			<label><?=$tags['일체형'];?>일체형</label> <? if ($processMode == 'modify') { ?><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.sort('<?=$goods->getId()?>', 'single'); return false;"><img src="../img/buttons/btn_option_integral.gif" /></a><? } ?>
			<label><?=$tags['분리형'];?>분리형</label> <? if ($processMode == 'modify') { ?><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.sort('<?=$goods->getId()?>', 'double'); return false;"><img src="../img/buttons/btn_option_discrete.gif" /></a><? } ?>

			<p class="help">
				옵션(품목) 새로등록시 옵션 출력순서가 초기화 됩니다.
			</p>
		</td>
	</tr>
	<? } ?>
	<tr class="IF_mode_IS_register">
		<th>옵션 등록하기 <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
		<table class="admin-form-table" id="el-option-table">
		<thead>
		<tr>
			<th>옵션명</th>
			<th>옵션값 <span class="help">콤마(,)로 구분 (ex: 빨강, 파랑)</span></th>
		<tr>
		</thead>
		<tbody>
		<tr>
			<th><input type="text" name="option_name[]" value="<?=$optionNames[0]?>"></th>
			<td>
				<!-- css3 -->
				<div style="width:100%;padding-right:100px;box-sizing:border-box;">
					<div class="field-wrapper" style="float:left;">
						<input type="text" name="option_value[]" value="<?=$optionValues[0]?>">
					</div>
					<div style="float:left;margin:2px -100px 0 5px;">
						<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.add();"><img src="../img/i_add.gif" /></a>
					</div>
				</div>
			</td>
		</tr>
		<? for ($i=1;$i<$optionNamesSize;$i++) { ?>
		<tr>
			<th><input type="text" name="option_name[]" value="<?=$optionNames[$i]?>"></th>
			<td>
				<div style="width:100%;padding-right:100px;box-sizing:border-box;">
					<div class="field-wrapper" style="float:left;">
						<input type="text" name="option_value[]" value="<?=$optionValues[$i]?>">
					</div>
					<div style="float:left;margin:2px -100px 0 5px;">
						<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.del(event);"><img src="../img/i_del.gif" /></a>
					</div>
				</div>
			</td>
		</tr>
		<? } ?>
		</tbody>
		</table>

		<div class="button-container">
			<div class="al">
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.generate();return false;"><img src="../img/buttons/btn_form_confirm.gif" /></a>
			</div>

			<div class="ar">
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.save();return false;"><img src="../img/buttons/btn_option_save.gif" /></a>
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.preset.load();return false;"><img src="../img/buttons/btn_option_select.gif" /></a>
			</div>

			<div class="clear"></div>
		</div>

		</td>
	</tr>
	<tr>
		<th>옵션<br> 가격/재고 설정 <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
		<!-- // option form -->

		<? if ($goodsOptions->count() > 1) { ?>
		<div style="margin-bottom:5px;">
			<span style="border:1px solid #0070C0;background:#a7f5a1;width:20px;height:10px;display:inline-block;margin:0;"></span> 대표가격 지정옵션
			<span class="help"> 출력상태의 옵션중 맨 위에 있는 옵션을 기준으로 대표가격이 설정됩니다. </span>
		</div>
		<? } ?>


		<table class="admin-form-table" id="el-option-list">
		<? if ($optionNamesSize > 0) { ?>
		<thead>
		<tr>
			<? for ($i=0;$i<$optionNamesSize;$i++) { ?>
			<th><input type="text" name="optnm[]" value="<?=$optionNames[$i]?>" style="width:100%;"></th>
			<? } ?>
			<th style="width:80px;">재고</th>
			<th style="width:80px;">옵션판매금액</th>
			<th style="width:80px;">정가</th>
			<th style="width:80px;">매입가</th>
			<th style="width:80px;">적립금</th>
			<th style="width:30px;">출력</th>
			<th style="width:30px;">삭제</th>
		</tr>
		</thead>
		<tbody>
		<? foreach($goodsOptions as $k => $option) { ?>
		<tr class="ac" style="display:;">

			<input type="hidden" name="option_sno[<?=$k?>]" value="<?=$option[sno]?>">

			<? for ($i=0;$i<$optionNamesSize;$i++) {
				if ($i < 2) {
					$_optionName = $option['opt'.($i+1)];
				}
				else {
					$_optionName = $option['optn'];
				}
			?>
			<td>
				<input type="text" name="opt[<?=$i?>][<?=$k?>]" value="<?=$_optionName?>" style="width:100%;">
			</td>
			<? } ?>
			<td><input type="text" name="option_stock[<?=$k?>]" value="<?=$option['stock']?>" style="width:60px;"></td>
			<td><input type="text" name="option_price[<?=$k?>]" value="<?=$option['price']?>" style="width:60px;"></td>
			<td><input type="text" name="option_consumer[<?=$k?>]" value="<?=$option['consumer']?>" style="width:60px;"></td>
			<td><input type="text" name="option_supply[<?=$k?>]" value="<?=$option['supply']?>" style="width:60px;"></td>
			<td><input type="text" name="option_reserve[<?=$k?>]" value="<?=$option['reserve']?>" style="width:60px;"></td>
			<td><input type="checkbox" name="option_is_display[<?=$k?>]" value="1" <?=$option['go_is_display'] == '1' ? 'checked' : ''?>></td>
			<td><input type="checkbox" name="option_is_deleted[<?=$k?>]" value="1"></td>
		</tr>
		<? } ?>
		</tbody>
		<tfoot>
		<tr class="ac">
			<td colspan="<?=$optionNamesSize?>">일괄적용</td>
			<td>
				<input type="text" name="all_option_stock" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_stock');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_price" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_price');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_consumer" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_consumer');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_supply" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_supply');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="text" name="all_option_reserve" style="width:60px;" >
				<a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.allChange('option_reserve');"><img src="../img/buttons/btn_seting.gif"></a>
			</td>
			<td>
				<input type="checkbox" value="1" checked name="all_option_is_display" onclick="nsAdminGoodsForm.option.allChange('option_is_display');">
			</td>
			<td>
				<input type="checkbox" value="1" name="all_option_is_deleted" onclick="nsAdminGoodsForm.option.allChange('option_is_deleted');">
			</td>
		</tr>
		</tfoot>
		<? } else { ?>
		<thead></thead>
		<tbody></tbody>
		<tfoot></tfoot>
		<? } ?>
		</table>

		<div id="el-option-list-paging">
		</div>

		<p class="help IF_mode_IS_register">
			<span class="specialchar">※</span> 등록된 옵션정보는 전체 삭제가 불가능 합니다. (옵션 1개이상 출력 필수)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;수정모드에서 [옵션새로등록]을 통해 새로 등록해 주세요.<br />
			<span class="specialchar">※</span> 옵션 출력순서는 수정모드에서 설정/관리 가능합니다.
		</p>

		<div class="button-container IF_mode_IS_modify">
			<? if ($optionNamesSize < 1) { ?>
			<div class="al">
			<p class="help">
			현재 상품은 단일옵션 상품입니다. <br/>
			가격옵션 추가는 [옵션(품목) 새로등록] 하기를 통해 등록 가능합니다.
			</p>
			</div>
			<? } else { ?>
			<div class="al">
				<a href="javascript:void(0);" class="default-btn" onclick="nsAdminGoodsForm.option.insert(<?=$goods->getId()?>);return false;"><img src="../img/buttons/btn_option_plus.gif" /></a>
			</div>
			<div class="al">
			<p class="help">
			가격옵션 추가는 [옵션(품목) 새로등록] 하기를 통해 등록 가능합니다.
			</p>
			</div>
			<? } ?>

			<div class="ar">
				<a href="javascript:void(0);" class="default-btn" onclick="nsAdminGoodsForm.option.reset(<?=$goods->getId()?>);return false;"><img src="../img/buttons/btn_option_new.gif" /></a>
			</div>

			<div class="clear"></div>
		</div>

		<!-- // option form -->
		</td>
	</tr>
	<tr>
		<th>옵션별 이미지/색상 설정</th>
		<td>
		<!-- // option image form -->

			<div id="el-option-image">
			<?
			// 최초 설정 이후에, 추가한 옵션까지 가져와야 하므로,
			// Clib_Model_Goods_Goods::getOptions 을 이용한 결과를 이용하여 구성한다.
			$optionValues = array();
			foreach($goodsOptions as $option) {

				for ($i=0;$i<$optionNamesSize;$i++) {
					$name = $option->getNthName($i+1);

					if (!in_array($name, $optionValues[$i])) {
						$optionValues[$i][] = $name;
					}
				}
			}

			$images = $goodsOptions->getImages();

			foreach($optionNames as $k => $name) {
				$values = $optionValues[$k];
				$kind = $goods['opt'.($k+1).'kind'];
			?>
			▼ <b><?=$name?> 이미지/색상 설정</b>
			&nbsp;&nbsp;
			<label class="extext"><input type="radio" name="option_image_type[<?=$k?>]" value="img" class="null" <?=($kind != 'color') ? 'checked' : ''?> onclick="nsAdminForm.toggle.is(event, 'img');nsAdminForm.toggle.is(event, 'color');">이미지</label>
			<label class="extext"><input type="radio" name="option_image_type[<?=$k?>]" value="color" class="null" <?=($kind == 'color') ? 'checked' : ''?> onclick="nsAdminForm.toggle.is(event, 'color');nsAdminForm.toggle.is(event, 'img');">색상타입 사용</label>

			<table class="admin-form-table">
			<tbody>
			<?
			$icons = $goodsOptions->getNthIcons($k + 1);
			foreach ($values as $value) {
			?>
			<tr>
				<th><?=$value?> 아이콘</th>
				<td>
					<div class="IF_option_image_type[<?=$k?>]_IS_img">
					<input type="file" name="option_icon_<?=$k?>[<?=get_js_compatible_key($value)?>]" class="opt gray">
					<?if($kind == 'img' && $icons[$value]){?>
					<div style="margin-top:3px;">
						<input type=checkbox class="null" name="del[option_icon_<?=$k?>][<?=get_js_compatible_key($value)?>]" value="delete">
						<font class=small color=#585858>삭제 (<?=$icons[$value]?>)</font>
						<img src='../../data/goods/<?=$icons[$value]?>' width="20" style='border:1 solid #ccc' onclick="popupImg('../data/goods/<?=$icons[$value]?>','../');" class="hand" onerror="this.style.display='none'" align="absmiddle">
					</div>
					<?}?>
					</div>
					<div class="IF_option_image_type[<?=$k?>]_IS_color">
					색상값 입력 : #<input type="text" name="option_color_<?=$k?>[<?=get_js_compatible_key($value)?>]" value="<?=$icons[$value]?>" size="8" maxlength="6"><a href="javascript:nsAdminGoodsForm.openColorTable('<?=get_js_compatible_key($value)?>','option_color_<?=$k?>');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="색상표 보기" align="absmiddle" /></a>
					</div>
				</td>
				<? if ($k == 0) { ?>
				<th>상품이미지</th>
				<td>
					<input type=file name="option_image[<?=get_js_compatible_key($value)?>]" class="opt gray">
					<?if($images[$value]){?>
					<div style="margin-top:3px;">
						<input type=checkbox class="null" name="del[option_image][<?=get_js_compatible_key($value)?>]" value="delete">
						<font class=small color=#585858>삭제 (<?=$images[$value]?>)</font>
						<img src='../../data/goods/<?=$images[$value]?>' width="20" style='border:1 solid #ccc' onclick="popupImg('../data/goods/<?=$images[$value]?>','../');" class="hand" onerror="this.style.display='none'" align="absmiddle">
					</div>
					<?}?>
				</td>
				<? } ?>
			</tr>
			<?}?>
			</tbody>
			</table>
			<? } ?>
			</div>

		<!-- // option image form -->
		</td>
	</tr>
	<!-- if 다수 옵션 -->
	</table>
	<script type="text/javascript">
	nsAdminGoodsForm.option.toggle(<?=$goods['use_option'] ? 'true' : 'false'?>);
	</script>

<!-- E: 상품 재고/옵션 -->

<!-- S: 추가옵션/구매자입력옵션 -->
	<h2 class="title">추가옵션/구매자입력옵션<span>추가옵션을 무제한 등록할 수 있으며, 추가상품을 판매하거나 사은품을 제공할 수도 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/icon_sample.gif" border="0" align=absmiddle /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>추가옵션</th>
		<td>
			<?
			foreach($form->getTag('use_add_option') as $label => $tag) {
				printf('<label>%s%s</label>',$tag, $label);
			}
			?>
			<p class="help">
			재고와 연동되지 않는 추가상품 및 사은품을 등록할 수 있는 기능입니다. <br />
			해당 상품은 적립금 지급 및 할인, 쿠폰 적용대상에 포함되지 않으며, 셀리(오픈마켓 연동서비스)와 인터파크 상품정보에 연동되지 않습니다.
			</p>

			<!-- // selectable form -->
			<table class="admin-form-table IF_use_add_option_IS_1" id="el-add-option" style="margin-top:10px;">
			<thead>
			<tr class="ac">
				<th>옵션명</th>
				<th>옵션값</th>
				<th>옵션금액</th>
				<th>필수</th>
			</tr>
			</thead>
			<tbody>
			<?
			$type = 'selectable';
			for ($i=0,$m=sizeof($additional_option[$type]['name']);$i<$m;$i++) {
				$action = ($i > 0) ? 'del' : 'add';
			?>
			<tr>
				<td>
					<input type="hidden" name="additional_option[selectable][_idx][]" value="<?=$i?>">
					<input type="text" name="additional_option[selectable][name][<?=$i?>]" value="<?=$additional_option[$type]['name'][$i]?>" maxlength="20">
					<a href="javascript:void(0);" onclick="nsAdminGoodsForm.addOption.selectable.<?=$action?>(event);"><img src="../img/i_<?=$action?>.gif" align=absmiddle /></a>
				</td>
				<td colspan="2">
					<table class="nude padding-midium">
					<?
					for ($j=0,$n=sizeof($additional_option[$type]['value'][$i]);$j<$n;$j++) {
						$action = ($j > 0) ? 'del' : 'add';
					?>
					<tr>
						<td>
							<input type="hidden" name="additional_option[selectable][sno][<?=$i?>][]" value="<?=$additional_option[$type]['sno'][$i][$j]?>"><input type="text" name="additional_option[selectable][value][<?=$i?>][]" value="<?=$additional_option[$type]['value'][$i][$j]?>" style="width:270px">
							<a href="javascript:void(0)" onclick="nsAdminGoodsForm.addOption.selectable.<?=$action?>Sub(event);"><img src="../img/i_<?=$action?>.gif" align=absmiddle border=0 /></a>
						</td>
						<td>
							<select name="additional_option[selectable][addprice_operator][<?=$i?>][]">
								<option value="+" <?=$additional_option[$type]['addprice_operator'][$i][$j] != '-' ? 'selected' : ''?>>+</option>
								<option value="-" <?=$additional_option[$type]['addprice_operator'][$i][$j] == '-' ? 'selected' : ''?>>-</option>
							</select>
							<input type="text" name="additional_option[selectable][addprice][<?=$i?>][]" size=9 value="<?=$additional_option[$type]['addprice'][$i][$j]?>"><input type="hidden" name="additional_option[selectable][addno][<?=$i?>][]" value="<?=$additional_option[$type]['addno'][$i][$j]?>">
						</td>
					</tr>
					<? } ?>
					</table>
				</td>
				<td class="ac"><input type="checkbox" name="additional_option[selectable][require][<?=$i?>]" value="o" <?=$additional_option[$type]['require'][$i] ? 'checked' : ''?>></td>
			</tr>
			<? } ?>
			</tbody>
			</table>
			<div style="padding-top:10px" class="IF_use_add_option_IS_1">
				<select name="dopt_extend" style="width:125">
					<option value=''>옵션바구니 선택</option>
					<? foreach ($arDoptExtend as $k => $val) { ?>
					<option value='<?=$val[sno]?>'><?=$val[title]?></option>
					<? } ?>
				</select>&nbsp;&nbsp;<a href="javascript:fnApplyDoptExtendData();"><img src="../img/btn_optionbasket_add.gif" border="0" align="absmiddle"></a>
				<a href="javascript:nsAdminGoodsForm.presetExtend.load();"><img src="../img/btn_optionbasket_admin_add.gif" border="0" align="absmiddle"></a>
			</div>
			<!-- // selectable form -->
		</td>
	</tr>
	<tr>
		<th>구매자 입력 옵션</th>
		<td>
			<?
			foreach($form->getTag('use_add_input_option') as $label => $tag) {
				printf('<label>%s%s</label>',$tag, $label);
			}
			?>
			<p class="help">
			고객에게 직접 입력을 받을 수 있는 옵션입니다. ex) 이니셜 새기기 <br />
			(해당 설정 정보는 엑셀 DB 업로드/다운로드 기능을 지원하지 않습니다) <br />
			해당 상품은 적립금 지급 및 할인, 쿠폰 적용대상에 포함되지 않으며, 셀리(오픈마켓 연동서비스)와 인터파크 상품정보에 연동되지 않습니다.
			</p>

			<!-- // inputable form -->
			<table class="admin-form-table IF_use_add_input_option_IS_1" id="el-add-input-option" style="margin-top:10px;">
			<thead>
			<tr class="ac">
				<th>옵션명</th>
				<th>입력옵션 글자수 제한</th>
				<th>옵션금액</th>
				<th>필수</th>
			</tr>
			</thead>
			<tbody>
			<?
			$type = 'inputable';
			for ($i=0,$m=sizeof($additional_option[$type]['name']);$i<$m;$i++) {
				$action = ($i > 0) ? 'del' : 'add';
				$j=0;
			?>
			<tr>
				<td>
					<input type="hidden" name="additional_option[inputable][_idx][]" value="<?=$i?>">
					<input type="text" name="additional_option[inputable][name][<?=$i?>]" value="<?=$additional_option[$type]['name'][$i]?>" maxlength="20">
					<a href="javascript:void(0);" onclick="nsAdminGoodsForm.addOption.inputable.<?=$action?>(event);"><img src="../img/i_<?=$action?>.gif" align=absmiddle /></a>
				</td>
				<td>
					<input type="hidden" name="additional_option[inputable][sno][<?=$i?>][]" value="<?=$additional_option[$type]['sno'][$i][$j]?>"><input type="text" name=additional_option[inputable][value][<?=$i?>][] value="<?=$additional_option[$type]['value'][$i][$j]?>" style="width:50px"> 자
				</td>
				<td>
					<select name="additional_option[inputable][addprice_operator][<?=$i?>][]">
							<option value="+" <?=$additional_option[$type]['addprice_operator'][$i][$j] != '-' ? 'selected' : ''?>>+</option>
							<option value="-" <?=$additional_option[$type]['addprice_operator'][$i][$j] == '-' ? 'selected' : ''?>>-</option>
					</select>
					<input type="text" name="additional_option[inputable][addprice][<?=$i?>][]" size=9 value="<?=$additional_option[$type]['addprice'][$i][$j]?>"><input type="hidden" name=additional_option[inputable][addno][<?=$i?>][] value="<?=$additional_option[$type]['addno'][$i][$j]?>">
				</td>
				<td class="ac"><input type="checkbox" name=additional_option[inputable][require][<?=$i?>] value="o" <?=$additional_option[$type]['require'][$i][$j] ? 'checked' : ''?>></td>
			</tr>
			<? } ?>
			</tbody>
			</table>
			<!-- // inputable form -->

		</td>
	</tr>
	</table>
<!-- E: 추가옵션/구매자입력옵션 -->

<!-- S: 상품 할인 설정 -->
	<h2 class="title">상품 할인 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">

	<tr>
		<th>회원혜택 제외</td>
		<td>
			<label><?=$form->getTag('exclude_member_discount'); ?>회원 할인혜택 적용 제외</label>
			<p class="help">
			선택시, 상품별 할인과 회원할인 중복 적용 안됨
			</p>
		</td>
	</tr>
	<tr>
		<th>상품별 할인 설정</td>
		<td>
			<?php
			foreach ($form->getTag('use_goods_discount') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<!-- // if 상품별 할인 사용 -->
			<div class="IF_use_goods_discount_IS_1">
			<table class="admin-form-table">
			<tr>
				<th>기간</th>
				<td>
					<?
					$discountRange = array(
						$discount['gd_start_date'],
						$discount['gd_end_date'],
					);

					$discountRangeDate = array();
					$discountRangeHour = array();
					$discountRangeMin = array();

					foreach($discountRange as $k => $time) {
						$discountRangeDate[$k] = $time ? date('Ymd', $time) : '';
						$discountRangeHour[$k] = $time ? date('H', $time) : '';
						$discountRangeMin[$k] = $time ? date('i', $time) : '';
					}
					?>
					<input type=text name="goods_discount_by_term_range_date[]" value="<?=$discountRangeDate[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
					<select name="goods_discount_by_term_range_hour[]">
					<? for($i = 0; $i < 24; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeHour[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>시
					<select name="goods_discount_by_term_range_min[]">
					<? for($i = 0; $i < 60; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeMin[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>분
					 -
					<input type=text name="goods_discount_by_term_range_date[]" value="<?=$discountRangeDate[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
					<select name="goods_discount_by_term_range_hour[]">
					<? for($i = 0; $i < 24; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeHour[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>시
					<select name="goods_discount_by_term_range_min[]">
					<? for($i = 0; $i < 60; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeMin[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>분

				</td>
			</tr>
			<tr>
				<th>대상 및 금액</th>
				<td>
					<?php
					foreach ($form->getTag('goods_discount_by_term_for_specify_member_group') as $label => $tag) {
						echo sprintf('<label>%s%s</label> ',$tag, $label);
					}
					?>
					<!-- 회원 및 비회원 전체 -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_2">
					<tr>
						<td>
							할인금액 : <input type="text" name="goods_discount_by_term_amount_for_nonmember_all" value="<?=$form->getValue('goods_discount_by_term_amount_for_nonmember_all')?>">
							<select name="goods_discount_by_term_amount_type_for_nonmember_all">
								<option value="%" <?=$form->getValue('goods_discount_by_term_amount_type_for_nonmember_all') == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$form->getValue('goods_discount_by_term_amount_type_for_nonmember_all') == '=' ? 'selected' : ''?>>원</option>
							</select>
						</td>
					</tr>
					</table>

					<!-- 회원전체 -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_0">
					<tr>
						<td>
							할인금액 : <input type="text" name="goods_discount_by_term_amount_for_all" value="<?=$form->getValue('goods_discount_by_term_amount_for_all')?>">
							<select name="goods_discount_by_term_amount_type_for_all">
								<option value="%" <?=$form->getValue('goods_discount_by_term_amount_type_for_all') == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$form->getValue('goods_discount_by_term_amount_type_for_all') == '=' ? 'selected' : ''?>>원</option>
							</select>
						</td>
					</tr>
					</table>

					<!-- 특정 회원 그룹 -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_1" id="el-goods-discount-by-term">
					<?
						foreach($ruleSets as $k => $ruleSet) {
							$action = $k > 0 ? 'del' : 'add';
					?>
					<tr>
						<td>
							대상 :
							<select name="goods_discount_by_term_target[]">
								<? foreach ($memberGroups as $memberGroup) { ?>
								<option value="<?=$memberGroup['level']?>" <?=$ruleSet['target'] == $memberGroup['level'] ? 'selected' : ''?>><?=$memberGroup['grpnm']?></option>
								<? } ?>
							</select>
						</td>
						<td>
							할인금액 : <input type="text" name="goods_discount_by_term_amount[]" value="<?=$ruleSet['amount']?>">
							<select name="goods_discount_by_term_amount_type[]">
								<option value="%" <?=$ruleSet['unit'] == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$ruleSet['unit'] == '=' ? 'selected' : ''?>>원</option>
							</select>
						</td>
						<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.discount.<?=$action?>Group(event);"><img src="../img/i_<?=$action?>.gif" /></a></td>
					</tr>
					<? } ?>
					</table>
				</td>
			</tr>
			<tr>
				<th>절사기준</th>
				<td>
					<?php
					foreach ($form->getTag('goods_discount_by_term_use_cutting') as $label => $tag) {
						echo sprintf('<label>%s%s</label> ',$tag, $label);
					}
					?>

					<?=$form->getTag('goods_discount_by_term_cutting_unit')?> 원 단위
					<?=$form->getTag('goods_discount_by_term_cutting_method')?>
					<p class="help">
						판매금액의 %단위로 상품별 할인 설정시 발생하는 1원 단위 및 10원 단위 할인금액을 절사하여 적용합니다.<br/>
						Ex) 판매금액 1,700원의 7% 할인 ? 할인금액 119원 발생<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;=> 1원 단위 적용시 내림 : 110원, 반올림 : 120원, 올림 : 120원 할인금액 적용<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;=> 10원 단위 적용시 내림 : 100원, 반올림 : 100원, 올림 : 200원 할인금액 적용<br/>
					</p>
					<p class="help" style="color: #ff0000;">
						※ 절사는 할인금액을 %로 설정시에만 적용 됩니다.
					</p>
				</td>
			</tr>
			</table>
			<p class="help">
			쿠폰 사용시 상품할인금액이 차감되지 않은 기본 판매가격 기준으로 쿠폰이 적용됩니다.
			</p>
			</div>
			<!-- // if 상품별 할인 사용 -->
		</td>
	</tr>
	</table>
<!-- E: 상품 할인 설정 -->

<!-- S: 상품 추가관리 설정 -->
	<h2 class="title">상품 추가관리 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>제조일자</th>
		<td><?=$form->getTag('manufacture_date'); ?></td>
		<th>유효일자</th>
		<td><?=$form->getTag('effective_date_start'); ?> ~ <?=$form->getTag('effective_date_end'); ?></td>
	</tr>
	<tr>
		<th>구매가능<br /> 회원그룹 설정</th>
		<td colspan="3">
			<?php

			foreach ($form->getTag('buyable') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<?=$form->getTag('buyable_member_group'); ?>

			: <a href="javascript:void(0);" onclick="nsAdminGoodsForm.buyable.openMemberGroupSelector();return false;"><img src="../img/buttons/btn_group_view.gif" /></a>

			<p class="help">
				특정 회원만 구매 가능하도록 설정/지정 할 수 있습니다.<br />
				특정 회원그룹 선택 시 <a href="javascript:void();" onclick="nsAdminGoodsForm.buyable.openMemberGroupSelector();">[구매가능 회원그룹 선택/보기]</a> 버튼을 클릭하여 선택 및 확인하세요
			</p>
		</td>
	</tr>
	<tr>
		<th>성인인증 <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<span class=&quot;red&quot;>※ 청소년 유해매체물로 지정된 상품의 경우 반드시 성인인증 사용을 설정하셔야 합니다.</span><p>방송통신위원회 고시에 따라서 청소년 유해매체물로 지정되는 상품은 19세 미만 유해매체물 표시를 해야 하며, 성인인증을 하기 전에는 해당 상품의 상세정보 확인 및 주문을 할 수 없도록 해야 합니다.<br />성인인증 상품으로 설정된 상품의 경우 성인인증을 하기 전에는 상품 이미지 및 상세정보를 확인 할 수 없으며, 해당 상품 클릭시 성인인증 확인하기 페이지로 연결됩니다.</p>"></th>

		<td colspan="3">
			<?php
			foreach ($form->getTag('use_only_adult') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<p class="help">
				해당 상품의 상세페이지 접근시 성인인증확인 인트로 페이지가 출력되며, 진열 이미지는 19금 이미지로 대체되어 보여집니다. <br />
				성인인증 기능은 별도의 인증 서비스 신청완료 후 이용 가능합니다. <a href="../member/realname_info.php" target="_blank"><img src="../img/buttons/btn_confirmation.gif" /></a><br />

				<br /><br />

				<span class="specialchar">※</span> 구 실명인증 서비스는 성인인증 수단으로 연결되지 않습니다.<br />
				모바일 샵에서는, 성인인증상품의 경우 성인인증이 확인된 회원에 한하여 상품이 보여지며, 인증하기 서비스는 PC버전으로 보기에서만 지원 됩니다.<br />
			</p>
			</div>

		</td>
	</tr>
	</table>
<!-- E: 상품 추가관리 설정 -->

<!-- S: 관련상품 -->
	<h2 class="title">관련상품<span>이상품과 관련있는 상품을 추천하세요! 관련상품은 PC버전과 모바일샵 모두 노출됩니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>관련상품 노출방식</th>
		<td class="noline">
			<?php
			$tags = $form->getTag('relationis');
			?>
			<label><?=$tags['자동']?>자동 <span class="help">(같은 분류 상품이 무작위로 보여짐)</span></label>
			<label><?=$tags['수동']?>수동 <span class="help">(아래 직접 선택등록)</span></label>
		</td>
	</tr>
	</table>

	<div id="divRefer" class="IF_relationis_IS_1" style="margin-top:10px;">
	<input type="hidden" name="relation" id="el-relation" value="">

	<p style="margin:0 0 5px 0;">
		현재 관련상품 : <span id="el-related-goods-count"><?=sizeof($r_relation)?></span> 개
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.register();"><img src="../img/btn_goods_check.gif" align="absmiddle" /></a>
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.undo();"><img src="../img/btn_reset.gif" align="absmiddle" /></a>
	</p>

	<table id="el-related-goodslist" class="admin-form-table" style="width:750px;">
	<colgroup>
		<col width="40">
		<col width="70">
		<col width="40">
		<col width="">
		<col width="130">
		<col width="130">
		<col width="40">
	<colgroup>
	<thead>
	<tr>
		<th><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.select();">선택</a></th>
		<th>서로등록</th>
		<th></th>
		<th>등록된 관련상품</th>
		<th>관련상품 설정기간</th>
		<th>등록일</th>
		<th>삭제</th>
	</tr>
	</thead>
	<tbody>
	<? if ($r_relation){ foreach ($r_relation as $v){ ?>
	<tr align="center">
		<td class="noline"><input type="checkbox" name="related_chk[]" value="<?=$v[goodsno]?>"></td>
		<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.changetype(event);"><img src="../img/icn_<?=$v[r_type] == 'couple' ? '1' : '0'?>.gif" /></a></td>
		<td><a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s],40,'',1)?></a></td>
		<td align="left">
			<?=$v[goodsnm]?>
			<p style="margin:0;"><b><?=number_format($v[price])?></b></p>
			<? if ($v[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
		</td>
		<td>
			<?
			if (!$v[r_start] && !$v[r_end]) echo '지속노출';
			else {
				if ($v[r_start]) echo $v[r_start];
				echo ' ~ ';
				if ($v[r_end]) echo $v[r_end];
			}
			?>
		</td>
		<td><?=$v[r_regdt]?></td>
		<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.del(event);"><img src="../img/btn_delete_new.gif" /></a></td>
	</tr>
	<? }} ?>
	</tbody>
	</table>

	<table border="0" width="750">
	<tr>
		<td align="left">
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.changetype(event, 'multi','couple');"><img src="../img/btn_yes.gif" /></a>
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.changetype(event, 'multi','single');"><img src="../img/btn_no.gif" /></a>
		</td>
		<td align="right">
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.del(event, 'multi');"><img src="../img/btn_select_delete.gif" /></a>
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.range();"><img src="../img/btn_dayset.gif" /></a>
		</td>
	</tr>
	</table>

	<dl class="help">
		<dt>서로등록 <img src="../img/icn_1.gif" align="absmiddle" /></dt>
		<dd>본 상품이 서로등록 상품과 관련상품으로 동시에 등록됩니다. 삭제시 양쪽모두 자동으로 관련상품 목록에서 제외됩니다.</dd>

		<dt>서로등록 <img src="../img/icn_0.gif" align="absmiddle" /></dt>
		<dd>본 상품이 관련상품으로 서로등록 되지 않으며, 본 상품의 관련상품 목록에만 등록됩니다.</dd>

		<dd>관련상품 노출방식을 "자동" 으로 설정할 경우, 서로등록과 상관없이 무조건 같은 분류의 상품이 랜덤으로 보여집니다.</dd>
		<dd><span class="specialchar">※</span> 관련상품 노출형태 설정은 "상품관리 > 관련상품 노출 설정" 에서 하실 수 있습니다. <a href="../goods/related.php" target="_blank">[관련상품 노출 설정]</a> 바로가기</dd>
	</dl>

	</div>
<!-- E: 관련상품 -->

<!-- S: 상품 이미지 -->
	<h2 class="title">상품 이미지<span>PC/모바일 상점에 노출될 상품이미지를 등록합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=48');"><img src="../img/btn_img_q.gif" border="0" align="absmiddle" hspace="2" /></a> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span></h2>

	<!-- 이미지 등록방식 선택 -->
	<table class="admin-form-table">
	<tr>
		<th>이미지등록방식</th>
		<td class="noline">
		<?php
		foreach ($form->getTag('image_attach_method') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
		</td>
	</tr>
	</table>

	<div id="image_attach_method_upload_wrap">
	<!-- 이미지 직접 업로드 -->
	<div class="boxed-help">
		<p>
		처음 상품이미지를 등록하신다면, 반드시 <a href="../goods/imgsize.php" target=_blank><img src="../img/i_imgsize.gif" border=0 align=absmiddle /></a> 먼저 설정하세요!&nbsp;&nbsp;그리고 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=16')"><img src="../img/btn_resize_knowhow.gif" border=0 align=absmiddle /></a> 을 꼭 필독하세요!</font></a><br />
		<span class="specialchar">※</span> 자동리사이즈는 확대(원본)이미지만 등록하면 나머지 이미지들은 자동으로 리사이징 되는 간편한 기능입니다. <br />
		<span class="specialchar">※</span> 이미지파일의 용량은 모두 합해서 <?=ini_get('upload_max_filesize')?>B까지만 등록할 수 있습니다.
		</p>
	</div>

	<table class="admin-form-table">
	<?
		foreach ($imgs as $imageType=>$v) {
			$t = array_map("toThumb",$v);
	?>
	<tr>
		<?php if($imageType == 'l') {?>
		<th rowspan="5">PC 이미지</th>
		<?php } ?>
		<th>
			<?=$str_img[$imageType]?>

			<? if ($imageType!="l") { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" name="copy_<?=$imageType?>" onclick="return nsAdminGoodsForm.imageUpload.chkImgCopy(this.form);" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></div>
				<div style="padding-left:24px;"><font class=extext>(가로 <?=$cfg['img_'.$imageType]?> 픽셀)</font></div>
			<? } else { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" onclick="return nsAdminGoodsForm.imageUpload.chkImgBox(this, this.form)" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></div>
			<? } ?>
		</th>
		<td>
			<table class="nude" id="tb_<?=$imageType?>">
			<col valign=top span=2>
			<? for ($i=0;$i<count($v);$i++) { ?>
			<tr>
				<td>
				<? if (!in_array($imageType,array("i","s","mobile"))){ if (!$i){ ?>
				<a href="javascript:nsAdminGoodsForm.imageUpload.addfld('tb_<?=$imageType?>')"><img src="../img/i_add.gif" align=absmiddle /></a>
				<? } else { ?><font color=white>.........</font>
				<? }} else { ?><font color=white>.........</font>
				<? } ?>
				<span><input type=file name=img_<?=$imageType?>[] style="width:300px" onChange="nsAdminGoodsForm.imageUpload.preview(this)"></span>
				</td>
				<td>
				<? if ($v[$i]){ ?>
				<div style="padding:0 0" class=noline><input type="checkbox" name=del[img_<?=$imageType?>][<?=$i?>]><font class=small color=#585858>삭제 (<?=$v[$i]?>)</font></div>
				<? } ?>
				</td>
				<td><?=goodsimg($t[$i],20,"style='border:1 solid #ccc' onclick=popupImg('../data/goods/$v[$i]','../') class=hand",2)?></td>
			</tr>
			<? } ?>
			</table>
		</td>
	</tr>
	<? } ?>
	</table>

	<table class="admin-form-table" style="margin-top:5px">
	<tr>
		<th rowspan="5">모바일 이미지</th>
		<th>
			사용여부
			<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;red&quot;>모바일샵 v2 사용시에만 모바일샵 이미지를 별도로 설정할 수 있습니다.<br>모바일샵 v1 사용자는 모바일샵 이미지 사용 설정을 원하시는 경우 모바일샵 v2로 전환 해 주시기 바랍니다.</span></p><p><strong>모바일샵 전용 이미지 사용 : </strong><br>모바일샵이미지를 별도로 등록할 수 있습니다. <br>자동리사이즈 사용 시 모바일샵 확대이미지 기준으로 리사이징 됩니다. <br>모바일 환경에 최적화된 이미지 사이즈를 불러오므로 로딩 속도가 빠릅니다.<br>단, 이미지를 별도로 서버에 등록하므로 이미지 저장 공간이 필요합니다.</p><p><strong>PC 이미지 사용 : </strong><br>PC 이미지를 모바일샵에 그대로 사용합니다. 모바일샵에서 이미지 출력 시 로딩 속도가 느려질 수 있습니다.</p>">
		</th>
		<td class="noline">
			<label><input name="use_mobile_img" value="1" id="use_mobile_img_1" type="radio" <?=($goods->getData('use_mobile_img') == '1' || $_GET['mode'] != "modify") ? 'checked' : ''?> onclick="nsAdminGoodsForm.imageUpload.toggleImg();">모바일샵 전용 이미지 사용</label>
			<label><input name="use_mobile_img" value="0" id="use_mobile_img_0" type="radio" <?=($goods->getData('use_mobile_img') == '0') ? 'checked' : ''?> onclick="nsAdminGoodsForm.imageUpload.toggleImg();">PC 이미지 사용</label>
		</td>
	</tr>
	<?
		foreach ($mobile_imgs as $imageType=>$v) {
			$t = array_map("toThumb",$v);
			$selected = $goods->getData('img_pc_' . $imageType);
	?>
	<tr>
		<th>
			<?=$str_mobile_img[$imageType]?>

			<div class="use_mobile_img_1">
			<? if ($imageType!="z") { ?>
				<label class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" name="copy_<?=$imageType?>" onclick="return nsAdminGoodsForm.imageUpload.chkMobileImgCopy(this.form);" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></label>
				<div style="padding-left:24px;"><font class=extext>(가로 <?=$cfg['img_'.$imageType]?> 픽셀)</font></div>
			<? } else { ?>
				<label class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" onclick="return nsAdminGoodsForm.imageUpload.chkMobileImgBox(this, this.form)" title="원본이미지를 이용한 자동리사이징"> <font class=extext><b>자동리사이즈 사용</b></font></label>
			<? } ?>
			</div>
		</th>
		<td>
			<div class="use_mobile_img_1">
				<table class="nude" id="tb_mobile_<?=$imageType?>">
				<col valign=top span=2>
				<? for ($i=0;$i<count($v);$i++) { ?>
				<tr>
					<td>
					<? if (!in_array($imageType,array("w","x"))){ if (!$i){ ?>
					<a href="javascript:nsAdminGoodsForm.imageUpload.addfld('tb_mobile_<?=$imageType?>')"><img src="../img/i_add.gif" align=absmiddle /></a>
					<? } else { ?><font color=white>.........</font>
					<? }} else { ?><font color=white>.........</font>
					<? } ?>
					<span><input type=file name=img_<?=$imageType?>[] style="width:300px" onChange="nsAdminGoodsForm.imageUpload.preview(this)"></span>
					</td>
					<td>
					<? if ($v[$i]){ ?>
					<div style="padding:0 0" class=noline><input type="checkbox" name=del[img_<?=$imageType?>][<?=$i?>]><font class=small color=#585858>삭제 (<?=$v[$i]?>)</font></div>
					<? } ?>
					</td>
					<td><?=goodsimg($t[$i],20,"style='border:1 solid #ccc' onclick=popupImg('../data/goods/$v[$i]','../') class=hand",2)?></td>
				</tr>
				<? } ?>
				</table>
			</div>
			<div class="use_mobile_img_0">
				<select name="img_pc_<?=$imageType?>">
					<option value="img_l"<?=($selected == 'img_l' || (!$selected && $imageType == 'z')) ? ' selected' : ''?>>PC 확대(원본)이미지</option>
					<option value="img_m"<?=($selected == 'img_m' || (!$selected && $imageType == 'y')) ? ' selected' : ''?>>PC 상세이미지</option>
					<option value="img_s"<?=($selected == 'img_s' || (!$selected && $imageType == 'x')) ? ' selected' : ''?>>PC 리스트이미지</option>
					<option value="img_i"<?=($selected == 'img_i' || (!$selected && $imageType == 'w')) ? ' selected' : ''?>>PC 메인이미지</option>
				</select>
				<? if (in_array($imageType,array("w","x"))) { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><font class=extext>PC이미지 중 사용 할 이미지를 선택합니다. 확대/상세 이미지 선택 시 첫번째 등록된 이미지만 가져올 수 있습니다.</font></div>
				<? } else { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><font class=extext>PC이미지 중 사용 할 이미지를 선택합니다.</font></div>
				<? } ?>
			</div>
		</td>
	</tr>
	<? } ?>
	</table>
	<!-- //이미지 직접 업로드 -->
	</div>

	<div id="image_attach_method_link_wrap">
	<!-- 이미지 호스팅 URL 입력 -->
		<div class="boxed-help">
			<p>
			이미지 호스팅에 등록된 이미지의 웹 주소를 복사하여 붙여 넣기 하시면 상품 이미지가 등록됩니다.<br />
			ex) http://godohosting.com/img/img.jpg<br />
			이미지의 파일명을 한글로 작성 시 일부 서비스(네이버쇼핑 등)를 정상적으로 이용하실 수 없습니다.
			</p>
		</div>

		<table class="admin-form-table">
		<? foreach ($urls as $k=>$v) { ?>
		<tr>
			<?php if($k == 'l') {?>
			<th rowspan="5">PC 이미지</th>
			<?php } ?>
			<th>
			<?=$str_img[$k]?>
			</th>
			<td>

			<table id="tbl_<?=$k?>" class="nude">
			<col valign=top span=2>
			<? for ($i=0;$i<count($v);$i++){ ?>
			<?
				if ($v[$i] && ! preg_match('/^http:\/\//',$v[$i])) $v[$i] = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$v[$i];
				?>
			<tr>
				<td>
				<? if (!in_array($k,array("i","s","mobile"))){ if (!$i){ ?>
				<a href="javascript:nsAdminGoodsForm.imageUpload.addfld('tbl_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle /></a>
				<? } else { ?><font color=white>.........</font>
				<? }} else { ?><font color=white>.........</font>
				<? } ?>
				<span><input type="text" name=url_<?=$k?>[] style="width:430px" onChange="nsAdminGoodsForm.imageUpload.preview(this)" value="<?=$v[$i]?>"></span>
				</td>
				<td>
				<?=goodsimg($v[$i],20,"style='border:1 solid #ccc' onclick=popupImg('$v[$i]','../') class=hand",2)?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>

		<? } ?>
		</table>
	
		<table class="admin-form-table" style="margin-top:5px;">
		<? foreach ($mobile_urls as $k=>$v) { ?>
		<tr>
			<?php if($k == 'z') {?>
			<th rowspan="5">모바일 이미지</th>
			<?php } ?>
			<th>
			<?=$str_mobile_img[$k]?>
			</th>
			<td>

			<table id="tbl_mobile_<?=$k?>" class="nude">
			<col valign=top span=2>
			<? for ($i=0;$i<count($v);$i++){ ?>
			<?
				if ($v[$i] && ! preg_match('/^http:\/\//',$v[$i])) $v[$i] = 'http://'.$_SERVER['SERVER_NAME'].'/shop/data/goods/'.$v[$i];
				?>
			<tr>
				<td>
				<? if (!in_array($k,array("w","x"))){ if (!$i){ ?>
				<a href="javascript:nsAdminGoodsForm.imageUpload.addfld('tbl_mobile_<?=$k?>')"><img src="../img/i_add.gif" align=absmiddle /></a>
				<? } else { ?><font color=white>.........</font>
				<? }} else { ?><font color=white>.........</font>
				<? } ?>
				<span><input type="text" name=url_<?=$k?>[] style="width:430px" onChange="nsAdminGoodsForm.imageUpload.preview(this)" value="<?=$v[$i]?>"></span>
				</td>
				<td>
				<?=goodsimg($v[$i],20,"style='border:1 solid #ccc' onclick=popupImg('$v[$i]','../') class=hand",2)?>
				</td>
			</tr>
			<? } ?>
			</table>

			</td>
		</tr>
		<? } ?>
		</table>
	<!-- //이미지 호스팅 URL 입력 -->
	</div>
	<!--// 이미지 등록방식 선택 -->
<!-- E: 상품 이미지 -->

<!-- S: 상품 이미지 돋보기 효과 -->
	<h2 class="title">상품이미지 돋보기 효과<span>상품상세이미지에 마우스를 오버하여 상품이미지를 확대하여 볼 수 있는 기능입니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span></h2>
	<div style="padding-bottom:5px" class=noline>
		<?php
		foreach ($form->getTag('detailView') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>

	</div>
	<div id='detailViewCmt' class="IF_detailView_IS_y" style="width:660px;border:solid 1px #ccc; margin-bottom:5px;">
		<div style="margin:1px; background-color:#f8f8f8; padding:7px 10px; line-height:1.3em;">
			<div style="margin-bottom:2px">
			<div>※	<font class="small1" color="#444444">[상품이미지 돋보기 효과] 기능을 사용하기 위해서는, 아래 상품이미지 등록시 <font color="#FF0000">상세이미지</font>에 큰 사이즈의 이미지를 넣어야 합니다.<br >
			: 상세이미지에 마우스 오버시에 나타나는 확대이미지를 입력해야 합니다. 500px~800px 정도의 이미지를 권장합니다.</font></div>
			<div>※ <font class="small1" color="#444444">상세이미지 입력란에 이미지를 넣으면 자동으로 상세이미지와 마우스 오버시 보이는 큰 이미지가 등록됩니다.</font></div>
			<div>※ <font class="small1" color="#444444">확대(원본)이미지 입력란에 이미지를 넣고 [자동리사이즈 사용] 기능을 이용하여 상세이미지를 등록하시면, [상품이미지 돋보기 효과]
			기능은 사용이 불가능 합니다. 꼭, 상세이미지에 직접 등록하셔야 합니다.</font></div>
			</div>
		</div>
	</div>
	<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- E: 상품 이미지 돋보기 효과 -->

<!-- S: 외부 동영상(YouTube) 등록하기 -->
	<h2 class="title">외부 동영상(<img src="../img/icons/icon_youtube.gif" style="vertical-align:middle;">) 등록하기 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>사용설정</th>
		<td>
			<?php
			foreach ($form->getTag('use_external_video') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
	</tr>
	<tr class="IF_use_external_video_IS_1">
		<th>
		퍼가기 소스 등록
		</th>
		<td>
			<div class="field-wrapper">
				<?=$form->getTag('external_video_url'); ?>
			</div>
		</td>
	</tr>
	<tr class="IF_use_external_video_IS_1">
		<th>영상 Size 설정</th>
		<td>
			<?php
			$tags = $form->getTag('external_video_size_type');
			?>
			<table class="nude">
			<col style="width:100px;">
			<tr>
				<td><label><?=$tags['기본']?>기본</label></td>
				<td>너비 (Width) : 640</td>
				<td>높이 (Height) : 360</td>
			</tr>
			<tr>
				<td><label><?=$tags['사용자']?>사용자 Size</label></td>
				<td>너비 (Width) : <?=$form->getTag('external_video_width'); ?></td>
				<td>높이 (Height) : <?=$form->getTag('external_video_height'); ?></td>
			</tr>
			</table>

		</td>
	</tr>
	</table>
<!-- E: 외부 동영상(YouTube) 등록하기 -->

<!-- S: 상품 필수 정보 -->
	<h2 class="title">상품 필수 정보<span>상품 필수(상세)정보를 등록합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<div class="boxed-help" style="width:100%;background-color: #f8f8f8">
		<p>
		<span class="specialchar">※</span> <a href="http://www.ftc.go.kr/policy/legi/legiView.jsp?lgslt_noti_no=112" target="_blank"><span class="u">공정거래위원회에서 공고한 전자상거래법 상품정보제공 고시에 관한 내용을 필독해 주세요!</span></a> <br />
		전자상거래법에 의거하여 판매상품의 필수(상세)정보 등록이 필요합니다.<br />
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.overview()"><img src="../img/btn_gw_view.gif" /></a>를 참고하여 상품필수 정보를 등록하여 주세요.<br />
		등록된 정보는 쇼핑몰 상품상세페이지에 상품기본정보 아래에 표형태로 출력되어 보여집니다.<br/>
		<br/><b>네이버 지식쇼핑, 에누리닷컴 등 가격비교사이트에 등록하려는 상품은 <span style="color:#E6008D">아래 항목명을 참조하여 동일하게 입력</span>하셔야 정상적으로 등록됩니다.</b>
			<table class="admin-form-table" style="margin-left:10px;margin-bottom:10px;width:900px" style="background-color: #f8f8f8">
				<tr>
					<td>배송 · 설치비용 <img src="../img/i_copy.gif" align="absmiddle" onclick="prompt('Ctrl+C를 눌러 클립보드로 복사하세요', '배송 · 설치비용')" style="cursor:pointer" /></td>
					<td>예시) 서울 경기 무료배송/ 강원, 충청 2만원 추가</td>
					<td>기본 배송비 이외에 지역, 품목 등에 따라 추가 배송비가 발생하는 경우 기재
						<br/><span style="color:#E6008D">※일반적인 도서산간 지역에 대한 추가 배송비는 해당하지 않음</span></td>
				</tr>
				<tr>
					<td>추가설치비용 <img src="../img/i_copy.gif" align="absmiddle" onclick="prompt('Ctrl+C를 눌러 클립보드로 복사하세요', '추가설치비용')" style="cursor:pointer" /></td>
					<td>예시) 설치비 현장 지불</td>
					<td>해당 상품 구매시 추가로 설치비가 발생하는 경우 기재</td>
				</tr>
			</table>
		</p>
	</div>

	<div style="margin:10px;">
	항목추가 : <a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.add4row();"><img src="../img/btn_ad2.gif" align="absmiddle" /></a> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.add2row();"><img src="../img/btn_ad1.gif" align="absmiddle" /></a> 항목과 내용 란에 아무 내용도 입력하지 않으면 저장되지 않습니다.
	</div>

	<table id="el-extra-info-table" class="admin-form-table" style="table-layout:fixed;">
	<thead>
	<tr>
		<th>항목</th>
		<th>내용</th>
		<th>항목</th>
		<th>내용</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	<?
	$rowidx = 0;

	if ($goods['extra_info']) {

		$extra_info = gd_json_decode($goods['extra_info']);
		$keys = array_keys($extra_info);

		if (!empty($keys)) {
			for ($i=min($keys),$m=max($keys);$i<=$m;$i++) {

				$next_key = $i + 1 <= $m ? $i + 1 : null;

				if (!isset($extra_info[$i])) continue;

				if ($i % 2 == 1 && !isset($extra_info[$next_key])) {
					$colspan = 3;
				}
				else {
					$colspan = 1;
				}

				$extra_info[$i]['title'] = htmlspecialchars(stripslashes($extra_info[$i]['title']));
				$extra_info[$i]['desc'] = htmlspecialchars(stripslashes($extra_info[$i]['desc']));

				if($i % 2 != 0) echo '<tr>';
				echo '
					<td><input type="text" name="extra_info_title['.$i.']" style="width:100%" value="'.$extra_info[$i]['title'].'"></td>
					<td '.($colspan > 1 ? 'colspan="'.$colspan.'"' : '').'><input type="text" name="extra_info_desc['.$i.']" style="width:100%" value="'.$extra_info[$i]['desc'].'"></td>
				';

				if ((!isset($extra_info[$next_key]) || $i % 2 == 0)) echo '<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.delrow(event);"><img src="../img/i_del.gif" /></a></td></tr>'.PHP_EOL.PHP_EOL;

			}

			$rowidx = ($m % 2) == 0 ? $m : ++$m;	// index 보정
		}

	}
	?>
	</tbody>
	</table>
<!-- E: 상품 필수 정보 -->

<!-- S: 읽어주는 상품 설명 -->
	<h2 class="title">읽어주는 상품 설명 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a><span>모바일샵에서는 상품 설명을 음성으로 제공할 수 있습니다.</span></h2>

	<div style="padding-top:5"></div>

	<table class="admin-form-table">
	<tr>
		<th>사용설정</th>
		<td>
			<?php
			foreach ($form->getTag('speach_description_useyn') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
			<p class="help">
				모바일샵V2(default 스킨 제외) 만 사용 가능하며, 모바일샵V1 과 PC버전 쇼핑몰에는 적용되지 않습니다.<br/>
				일부 안드로이드 모바일 크롬, 파이어폭스 브라우저에서는 재생이 어려울 수 있습니다.
			</p>
		</td>
	</tr>
	<tr>
		<th>
			읽어주는 상품 설명
			<div class="extext">(최대 100자)</div>
		</th>
		<td>
			<?php echo $form->getTag('speach_description'); ?>
			<div style="text-align: right;">
				<span class="inputSize:{target:'speach_description',max:100}"></span>
			</div>
		</td>
	</tr>
	</table>
<!-- E: 읽어주는 상품 설명 -->

<!-- S: 상품 설명 -->
	<h2 class="title">상품 설명 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a><span>아래 <img src="../img/up_img.gif" border=0 align=absmiddle hspace=2>를 눌러 이미지를 등록하세요.</span> <span class="specialchar">※</span><span style="color:#E6008D">모든 이미지파일의 외부링크 (옥션, G마켓 등의 오픈마켓 포함)</span><span>는 지원되지 않습니다.</span></h2>

	<div class="boxed-help">
	<p>
	<span style="color:#E6008D">이미지 외부링크</span> 및 <span style="color:#E6008D">오픈마켓</span> 판매를 위한 이미지를 등록하시려면 <span style="color:#E6008D">반드시 이미지호스팅 서비스</span>를 이용하셔야 합니다. <br />
	이미지호스팅을 신청하셨다면 <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name="navi"><img src="../img/btn_imghost_admin.gif" align=absmiddle /></a>, 아직 신청안하셨다면 <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle /></a> 를 참조하세요!
	</p>
	</div>

	<div style="padding-top:5"></div>

	<table class="admin-form-table">
	<tr>
		<th>짧은설명</th>
		<td>
			<textarea name="shortdesc" style="width:100%;height:20px;overflow:visible" class=tline><?=$goods[shortdesc]?></textarea>
		</td>
	</tr>

	<tr>
		<th>이벤트 문구</th>
		<td colspan="3">
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('naver_event', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'naver_event',max:100}"</span>
				</div>
				<div style="clear:both;"></div>
			</div>

			<p class="help">
			<b><span class="specialchar">※</span> 마케팅 관리의 제휴서비스 (네이버쇼핑,  다음쇼핑하우) 이용 시 공통으로 사용되는 항목입니다.</b><br>
			<a href="../naver/partner.php" target="_blank">[네이버쇼핑 설정 바로가기]</a><br>
			- "마케팅 > 네이버쇼핑 설정 > 네이버쇼핑 이벤트 문구 설정 > 상품별 문구 사용" 설정 후 사용하세요.<br>
			- 이벤트 문구(공통문구+상품별 문구)는 최대 100자까지 입력 가능합니다.<br><br>

			<a href="../daumcpc/partner.php" target="_blank">[다음 쇼핑하우 설정 바로가기]</a><br>
			- "마케팅 > 다음 쇼핑하우" 신청 후 사용하세요. 쇼핑하우 상품 목록에 상품 정보와 함께 출력됩니다.<br>
			</p>
		</td>
	</tr>
	</table>

	<div id="el-tab" class="tab" style="margin-top:20px;">
		<ol class="navigation" style="margin-bottom:0;">
			<li><span class="head"></span><a href="#일반상세설명"><span>일반 상세설명</span></a><span class="tail"></span></li>
			<li><span class="head"></span><a href="#모바일상세설명"><span>모바일 상세설명</span></a><span class="tail"></span></li>
		</ol>

		<div id="container_일반상세설명" class="container">
			<div class="field-wrapper"><textarea name="longdesc" style="width:100%;height:400px" type=editor><?=$goods[longdesc]?></textarea></div>
		</div>

		<div id="container_모바일상세설명" class="container">
			<div class="field-wrapper"><textarea name="mlongdesc" style="width:100%;height:400px;" type=editor><?=$goods[mlongdesc]?></textarea></div>
		</div>
	</div>

<!-- E: 상품 설명 -->

<!-- S: 상품 배송정보/배송비 -->
	<h2 class="title">상품 배송정보/배송비 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>상품별 배송비 설정</th>
		<td colspan="3">
			<table class="nude">
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="0" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '0') ? 'checked' : ''?>> 사용안함(기본 배송 정책에 따름)</label></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="1" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '1') ? 'checked' : ''?>> 무료배송</label> <span class="help">해당 상품의 배송비를 청구하지 않습니다.</span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="3" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '3') ? 'checked' : ''?>> 착불 배송비</label> <span style="display:none;" id="gdi3">&nbsp;<input type="text" name="goods_delivery3" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">원 <span class="help">해당 상품의 배송비를 결제시 청구하지 않고, 상품 수취시 별도지급 하도록 합니다.</span></span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="4" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '4') ? 'checked' : ''?>> 고정 배송비</label> <span style="display:none;" id="gdi4">&nbsp;<input type="text" name="goods_delivery4" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">원</span> <span class="help">해당 상품의 수량 및 주문금액이 늘어나도 하나의 배송비로 묶어서 청구됩니다.</span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="5" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '5') ? 'checked' : ''?>> 수량별 배송비</label> <span style="display:none;" id="gdi5">&nbsp;<input type="text" name="goods_delivery5" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">원</span> <span class="help">해당 상품의 수량에 따라 × n 으로 배송비가 증가하여 청구 됩니다.</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>배송방법 안내</th>
		<td colspan="3">

			<?
			$tmp = explode('|', $goods['delivery_method']);
			$tmp2 = array(
				'택배',
				'우편(소포/등기)',
				'화물배달',
				'퀵',
				'배송안함(필요없음)',
			);
			?>
			<table class="admin-form-table">
			<tr>
				<th>배송방법</th>
				<td>
					<? foreach($tmp2 as $name) { ?>
					<label><input type="checkbox" name="delivery_method[]" <?=(in_array($name, $tmp)) ? 'checked' : ''?> value="<?=$name?>" ><?=$name?></label>
					<? } ?>
					<input type="text" name="delivery_method[]" value="<?=array_pop(array_diff($tmp, $tmp2))?>">
				</td>
			</tr>
			<tr>
				<th>배송지역</th>
				<td>
					<input type="text" name="delivery_area" value="<?=$goods['delivery_area']?>" >
					<span class="help">특정 배송지역을 입력하세요. 예)전국</span>
				</td>
			</tr>
			</table>

			<p class="help">
			배송정책설정과는 관련이 없으며 상품정보 안내용 기능입니다.
			</p>

		</td>
	</tr>
	</table>
<!-- E: 상품 배송정보/배송비 -->

<!-- S: 네이버쇼핑 3.0 설정 -->
	<h2 class="title">네이버쇼핑 3.0 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table" style="border:2px solid green;">
	<tr>
		<th>수입 및 제작 여부 <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;blue&quot;>해당 상품 필수 항목으로, 누락 시 네이버 클린프로그램이 적용되어 패널티 처리됩니다.</p>"></th>
		<td>
			<?=$form->getTag('naver_import_flag')?>
		</td>
		<th>판매방식 구분 <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;blue&quot;>해당 상품 필수 항목으로, 누락 시 네이버 클린프로그램이 적용되어 패널티 처리됩니다.</p>"></th>
		<td>
			<?=$form->getTag('naver_product_flag')?>
		</td>
	</tr>
	<tr>
		<th>주요 사용 연령대</th>
		<td>
			<?php
			foreach ($form->getTag('naver_age_group') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
		<th>주요 사용 성별</th>
		<td>
			<?=$form->getTag('naver_gender')?>
		</td>
	</tr>
	<tr>
		<th>속성 정보</th>
		<td colspan="3">
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('naver_attribute', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'naver_attribute',max:500}"</span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<p class="help">
			상품의 속성 정보에 대하여 ‘^’로 구분하여 입력합니다.<br>
			예) 서울^1개^오션뷰^2명^주중^조식포함^무료주차^와이파이
			</p>
		</td>
	</tr>
	<tr>
		<th>검색 태그</th>
		<td colspan="3">
			<div style="width:100%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('naver_search_tag', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'naver_search_tag',max:100}"</span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<p class="help">
			상품의 검색태그에 대하여 띄어쓰기 없이 ‘ | ’ (Vertical bar)로 구분하여 입력합니다.<br>
			예) 물방울패턴원피스|2016S/S신상원피스|결혼식아이템|여친룩
			</p>
		</td>
	</tr>
	<tr>
		<th>네이버 카테고리 ID</th>
		<td colspan="3">
			<div style="width:30%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('naver_category', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'naver_category',max:8}"</span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<p class="help">
			해당하는 카테고리에 매칭하는데 도움이 됩니다.<br>
			네이버쇼핑의 전체 카테고리 리스트는 <a href="https://adcenter.shopping.naver.com/main.nhn" target="_blank">[네이버쇼핑 쇼핑파트너존]</a>에서 다운로드할 수 있습니다.
			</p>
		</td>
	</tr>
	<tr>
		<th>가격 비교 페이지 ID</th>
		<td colspan="3">
			<div style="width:30%;padding-right:100px;box-sizing:border-box;">
				<div class="field-wrapper" style="float:left;">
					<?=$form->getTag('naver_product_id', array('style' => 'width:100%;')); ?>
				</div>
				<div style="float:left;margin:2px -100px 0 5px;">
					<span class="inputSize:{target:'naver_product_id',max:50}"</span>
				</div>
				<div style="clear:both;"></div>
			</div>
			<p class="help">
			네이버 가격비교 페이지 ID를 입력할 경우 네이버 가격비교 추천에 도움이 됩니다.<br>
			</p>
			예) http://shopping.naver.com/detail/detail.nhn?nv_mid=<font color="red">8535546055</font>&cat_id=50000151
			<p class="help">
			자세한 내용은 매뉴얼을 참고하여 주시기 바랍니다.
			</p>
		</td>
	</tr>
	</table>
<!-- E: 네이버쇼핑 3.0 설정 -->

<!-- S: 관리 메모 -->
	<h2 class="title">관리 메모 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<div class="field-wrapper">
		<?=$form->getTag('memo'); ?>
	</div>
<!-- E: 관리 메모 -->

</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- qr code 설정 -->
<? if($qrCfg['useGoods'] == "y"){ ?>
<h2 class="title">QR Code 노출<span>상품 상세보기에 QR Code 를 보여줍니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2 /></a></h2>
<div style="padding-bottom:5px" class=noline>
<label><input type="radio" name="qrcode" value=y <?=$goods['qrcode'] == 'y' ? 'checked' : ''?>>사용</label>
<label><input type="radio" name="qrcode" value=n <?=$goods['qrcode'] != 'y' ? 'checked' : ''?>>사용안함</label>
<?
	if($goods['qrcode'] == 'y'){
		require "../../lib/qrcode.class.php";
		$QRCode = Core::loader('QRCode');
		echo $QRCode->get_GoodsViewTag($goodsno, "goods_down");
	}
?>
</div>
<!-- qr code 설정 -->
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<? } ?>

<div class=button>
	<input type=image src="../img/btn_<?=$processMode?>.gif" id="formBtn" >
	<?=$btn_list?>
	<?if($goodsno){?>&nbsp;<a href="../../goods/goods_view.php?goodsno=<?=$goodsno?>" target="_blank"><img src="../img/btn_goods_view.gif" /></a><?}?>
</div>
</form>
</div>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){

	nsAdminForm.init($('goods-form'));

	<? if ($_GET['call']=='tabLongdescShow'){?>
		nsAdminGoodsForm.tabLongdescShow(_ID('btn_longdesc_mobile'));
	<? } ?>

	mini_editor("../../lib/meditor/");
	nsAdminGoodsForm.setDeliveryType();
	nsAdminGoodsForm.color.toHtml('selectedColor');

	nsAdminGoodsForm.imageUpload.toggleForm();
	nsAdminGoodsForm.imageUpload.toggleImg();

	nsAdminGoodsForm.relate.init('<?=$goods[goodsno]?>', <?=!empty($r_relation) ? gd_json_encode($r_relation) : '[]'?>);
	nsAdminGoodsForm.information.init(<?=$rowidx?>);
	nsAdminForm.inputSizeIndicator.init();

	nsAdminGoodsForm.option.init({
		mode : '<?=$processMode?>',
		pageSize : 50
	});

	nsAdminTab.init('el-tab');

	nsAdminForm.anchorHelper.init({
		// anchor option.
		id : 'enamoo-anchor-helper',
		save : 'nsAdminGoodsForm.validate(document.fm)',
		cancel : 'window.location.replace(\'<?=$returnUrl?>\')'
	});

});

var json_dopt_extend_data = new Array;
<? foreach ($arDoptExtend as $sno => $val) {?>
json_dopt_extend_data[<?=$val[sno]?>] = <?=$val[option]?>;
<? } ?>

function fnReloadDoptExtendData() {

	new Ajax.Request('./ax_dopt_extend_loader.php', {
		method:'post',
		onSuccess: function(transport){

			json_dopt_extend_data = new Array;

			// 셀렉트 박스 옵션 삭제
			var opt, sel = document.fm.dopt_extend;

			while (sel.length > 1)
			{
				sel.remove( sel.length - 1 );
			}

			var data = eval(transport.responseText);

			for (i=0;i<data.length ;i++ )
			{
				json_dopt_extend_data[ data[i].sno ] = eval(data[i].option);
				opt = document.createElement('option');
				opt.text = data[i].title;
				opt.value =  data[i].sno;

				sel.options.add(opt, sel.length + 1 );
			}

		},
		onFailure: function(){
			// alert('새로운 옵션은 새로고침 하셔야 반영됩니다.');

		}
	});
}

function fnApplyDoptExtendData() {

	var key = document.fm.dopt_extend.value;

	if (key) {
		nsAdminGoodsForm.presetExtend.set(json_dopt_extend_data[key]);
	}

}
</script>

<? @include dirname(__FILE__) . "/../interpark/_goods_form.php"; // 인터파크_인클루드 ?>
