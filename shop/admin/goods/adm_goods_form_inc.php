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
			"i"	=> "�����̹���",
			"s"	=> "����Ʈ�̹���",
			"m"	=> "���̹���",
			"l"	=> "Ȯ��(����)�̹���",
			"mobile"	=> "(��)������̹���"
			);
$str_mobile_img	= array(
			"w"	=> "�����̹���",
			"x"	=> "����Ʈ�̹���",
			"y"	=> "���̹���",
			"z"	=> "Ȯ��(����)�̹���",
			);

// ������ ����
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

// ���� ��ǰ (, �� ����� ��ǰ��ȣ)
$related_goodsnos = '';
if ($processMode=="modify") {

	// ��Ƽī�װ�
	$query = "select category,sort from ".GD_GOODS_LINK." where goodsno='$goodsno' order by category";
	$res = $db->query($query);
	while ($row=$db->fetch($res)) $r_category[$row['category']] = $row[sort];

	// ��ǰ ���� ��������
	$_extra_info = $goods['extra_info'];
	//$goods = array_map("slashes",$goods);	// @todo : �̰� �� ����� �ϴ��� üũ. �ؾ� �ϴ� ��쿡�� ���ο� �޼��带 �߰� �ؾ���
	$goods['extra_info'] = $_extra_info;	// extra_info �� json ��Ʈ���̹Ƿ� slashes �Լ��� �̿��ϸ� �ȵ�.
	$goods[launchdt] = str_replace(array('-','00000000'),'',$goods[launchdt]);
	$ex_title = explode("|",$goods[ex_title]);

	// QR ��� ���� ��������
	$goods['qrcode'] = Clib_Application::getModelClass('qrcode')->loadGoodsCode($goodsno)->hasLoaded() ? 'y' : 'n';

	for ($i=0;$i<$cnt_myicon;$i++) if ($goods[icon]&pow(2,$i)) $checked[icon][pow(2,$i)] = "checked";

	// ���û�ǰ ����Ʈ (��ġ�� �������� ���� �����ʹ� ���� �ڵ� ����)
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

if($goods[goods_deli_type] == '����' || !$goods[goods_deli_type])$goods_deli_type = 0;
if(!$goods['use_emoney']) $goods['use_emoney'] = 0;
if(!$goods['delivery_type']) $goods['delivery_type'] = 0;

else $goods_deli_type = 1;
if(!$goods['detailView']) $goods['detailView'] = 'n'; // �����Ϻ� ����

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

// �̹��� �ּҰ� url�϶� ó��
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

// �ʼ��ɼ�
$goodsOptions = $goods->getOptions();

// �߰��ɼ�
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

// ������ ���� ����
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

// ����
$colorList = array();
$CL_rs = $db->query("SELECT itemnm FROM ".GD_CODE." WHERE groupcd = 'colorList' ORDER BY sort");
while($CL_row = $db->fetch($CL_rs)) $colorList[] = $CL_row['itemnm'];

// ��ǰ�� ����
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
	// ȸ�� �� ��ȸ�� ��ü
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

// �� ������ ����

// �� ������ ����

// ��
$form = Clib_Application::form('admin_goods_register')->setData($goods);

// ȸ�� �׷�
$memberGroups = Clib_Application::getCollectionClass('member_group');
$memberGroups->load();

// �߰��ɼǹٱ���
$arDoptExtend = array();
$query = "select * from ".GD_DOPT_EXTEND." order by sno desc";
$res = $db->query($query);
while($rdopt = $db ->fetch($res)){
	$l = strlen($rdopt[title]);

	if($l > 20){
		$rdopt[title] = strcut($rdopt[title],20);
	}

	$rdopt[option] = !empty($rdopt[option]) ? unserialize($rdopt[option]) : $_tmp;
	$rdopt[option] = str_replace("\n","",gd_json_encode($rdopt[option]));	// php4 ȯ���̹Ƿ� �ӽ� �Լ� �߰� �Ͽ���.

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

<!-- S: ��ǰ �з����� -->
	<h2 class="title ">��ǰ�з�����<span>�ѻ�ǰ�� �������� �з��� ����� �� �ֽ��ϴ�&nbsp;(���ߺз��������)</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2 /></a></h2>

	<div style="width:800px;">

	<table class="admin-form-table">
	<tr>
		<th>���úз�</th>
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
				��ǰ�з� ��� �� �����з��� �ڵ� ��ϵǸ�, ��ϵ� �з��� ��ǰ�� ����˴ϴ�.<br>
				��ǰ ������ ������ �ʴ� �з��� ����������ư�� �̿��Ͽ� ������ �� �ֽ��ϴ�.
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

	<label><input type="checkbox" name="sortTop"><strong>���� ������� ����</strong> : üũ�� �̻�ǰ�� ���� ��ϵ� �ش� �� �з��������� �� ��ܿ� �������� �մϴ�.</label>
	<p class="help">
		<span class="specialchar">��</span> ����: ��ǰ�з�(ī�װ�)�� ���� ��ϵǾ� �־�� ��ǰ����� �����մϴ�. <a href="/shop/admin/goods/category.php" target="_blank">[��ǰ�з�(ī�װ�) ����ϱ�]</a>
	</p>

	<!-- ������ũ_ī�װ� -->
	<div id="interpark_category"></div>

	</div>
<!-- E: ��ǰ �з����� -->

<!-- S: ��ǰ�⺻���� -->
	<h2 class="title">��ǰ�⺻����<span>������, ������, �귣�尡 ���� ��� �Է¾��ص� �˴ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>��ǰ��ȣ</th>
		<td>
			<span id="el-generated-goodsno"><?=$goods->getId()?></span> (�ڵ�����)
		</td>
		<th>��ǰ�ڵ�</th>
		<td>
			<?=$form->getTag('goodscd'); ?> <span class="inputSize:{target:'goodscd',max:30}"></span> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.checkDuplicatedValue(document.fm.goodscd.value, 'goodscd', '<?=$goodsno?>');return false;" class="default-btn"><img src="../img/buttons/btn_repetition.gif" /></a>
		</td>
	</tr>
	<tr>
		<th>��ǰ�� <img src="../img/icons/bullet_compulsory.gif"></th>
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
			<label class="help"><?=$form->getTag('meta_title'); ?> ��ǰ���� ��ǰ ���������� Ÿ��Ʋ �±׿� �Էµ˴ϴ�.</label>
		</td>
	</tr>
	<tr>
		<th>�𵨸�</th>
		<td>
			<?=$form->getTag('model_name'); ?> <span class="inputSize:{target:'model_name',max:100}">
		</td>
		<th>��ǰ����</th>
		<td>
			<?php
			foreach ($form->getTag('goods_status') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
	</tr>
	<tr>
		<th>������</th>
		<td>
			<?=$form->getTag('maker'); ?>
			<?=$form->getTag('maker_select'); ?>
		</td>
		<th>������</th>
		<td>
			<?=$form->getTag('origin'); ?>
			<?=$form->getTag('origin_select'); ?>
		</td>
	</tr>
	<tr>
		<th>�귣��</th>
		<td>
			<?=$form->getTag('brandno')?>
			<font class=small1 color=444444><a href="brand.php" target=_blank><font class=extext_l><img src="../img/btn_brand_add.gif" /></a>
		</td>
		<th>�����</th>
		<td>
			<?=$form->getTag('launchdt'); ?> <span class="help">ex) 20080321</span>
			<p class="help">
				���̹� ���ļ��� ������ �α⵵(�������)�� �������� �߿��� ����Դϴ�
			</p>
			<div style="padding-top:3px"><font class=extext></font></div>
		</td>
	</tr>
	<tr>
		<th>������</th>
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
				�ٸ� ���������� ���� �ٲܼ� �ֽ��ϴ� <a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/buttons/btn_icon_plus.gif" align=absmiddle /></a>
			</p>

		</td>
		<th>��ǰ ��ǥ����</th>
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

			<div class="selColorText">���û��� :&nbsp;</div>
			<div id="selectedColor" title="���õ� ���� ����Ŭ������ �����Ͻ� �� �ֽ��ϴ�.">&nbsp;</div>

			<div style="padding:5px 0px 0px 0px; clear:left;"><font class=extext>��ǰ ���� �˻��ÿ� ���˴ϴ�.</font></div>
		</td>
	</tr>
	<tr>
		<th>��ǰ����<br />(����)���� <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
			<?php
			foreach ($form->getTag('open') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
		<th>����ϼ� ����(����)����</th>
		<td>
			<?php if($cfgMobileShop['vtype_goods']=='1'){?>
			<input type="checkbox" name="open_mobile" value=1 <?=$goods['open_mobile'] ? 'checked' : ''?>>���̱�
			<font class=extext>(üũ������ ����ϼ� ȭ�鿡�� �Ⱥ���)</font>
			<?php }else{?>
			<input type="hidden" name="open_mobile" value="<?php echo $goods['open'];?>" >
			<font class="red">�¶��� ���θ��� �����ϰ� ��ǰ ����(����)�� ����ǵ��� �����Ǿ� �ֽ��ϴ�.</font>
			<?php }?>
		</td>
	</tr>
	<tr>
	</tr>
	<tr>
		<th>����˻���</th>
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
				��ǰ�� �������� ��Ÿ�±׿� ��ǰ �˻��� Ű����� ����Ͻ� �� �ֽ��ϴ�.
			</p>
		</td>
	</tr>
	</table>
<!-- E: ��ǰ�⺻���� -->

<!-- S: ��ǰ�߰����� -->
	<h2 class="title">��ǰ�� �߰����� <span>��ǰƯ���� �°� �׸��� �߰��� �� �ֽ��ϴ� (��. ����, ����, ���ǻ�, �����, ��ǰ������ ��) <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span>
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
		<th><input type="text" name="title[]" class="exTitle gray" value="<?=$ex_title[$i]?>" onblur="if(!nsAdminGoodsForm.checkExtrainfoTitle())alert('�׸���� �ߺ��� �� �����ϴ�.')"></th>
		<td>
			<div class="field-wrapper">
				<input type="text" name="ex[]" value="<?=$goods[$ex]?>">
			</div>
		</td>
		<? if ($i%2){ ?></tr><tr><? } ?>
		<? } ?>
	</tr>
	</table>
<!-- E: ��ǰ�߰����� -->

<!-- S: ����ó ���� -->
	<?
	if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") {
		if($goodsno) $pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '$goodsno' ORDER BY pchsdt DESC LIMIT 1");
		$rs_pchs = $db->query("SELECT * FROM ".GD_PURCHASE." ORDER BY comnm ASC");
	?>
	<h2 class="title">����ó ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>����ó</th>
		<td>
			<select name="pchsno" id="pchsno"<?=($_GET['mode'] == "modify") ? " disabled=\"true\"" : ""?>>
				<option value="">����ó����</option>
				<? while ($row_pchs = $db->fetch($rs_pchs)) { ?>
				<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsData['pchsno']) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
				<? } ?>
			</select>
			<a href="javascript:void(0);" onclick="nsAdminGoodsForm.purchase.openSelector()"><img src="../img/purchase_find.gif" title="����ó �˻�" align="absmiddle" /></a>
		</td>
		<th>������</th>
		<td>
			<input type=text name=pchs_pchsdt id="pchs_pchsdt" size=10 value="" onclick="calendar(event);" onkeydown="onlynumber()" class="line"<?=$processMode == "modify" ? ' disabled="true"' : ''?>>
		</td>
	</tr>
	</table>

	<p class="help">
	- ����ó ���� �� ���� �Ͻø� �ش� ����ó�� ���� �̷��� ���� �˴ϴ�.<br />
	- �̹� ����ó ���� ������� ��ǰ��  "��� �� ��" ���� ���� �� �� �� �̷��� ���� ���� �ʽ��ϴ�.<br />
	* ���� : �������� ���� �Ǿ� �־�� ��ǰ����� �����մϴ�.
	</p>
	<? } ?>
<!-- E: ����ó ���� -->

<!-- S: ��ǰ ������å -->
	<h2 class="title">��ǰ ������å <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>�ǸŰ���(KRW) <img src="../img/icons/bullet_compulsory.gif"></th>
		<td colspan="3">
			<?=$form->getTag('goods_price'); ?> ��
		</td>
	</tr>
	<tr>
		<th>��������</th>
		<td>
			<? $tags = $form->getTag('tax'); ?>
			<label><?=$tags['����']?>����</label>
			<label><?=$tags['�����']?>�鼼</label>
			<p class="help">
			������ �����ÿ� ���ݰ�꼭 ��û/���� ��
			</p>
		</td>
		<th>�Һ��ڰ���</th>
		<td><?=$form->getTag('goods_consumer'); ?></td>
	</tr>
	<tr>
		<th>���԰���</th>
		<td><?=$form->getTag('goods_supply'); ?></td>
		<th>���ް���</th>
		<td><?=$form->getTag('provider_price'); ?></td>
	</tr>
	<tr>
		<th>���� ��ü����</th>
		<td colspan="3"><?=$form->getTag('strprice'); ?> <span class="inputSize:{target:'strprice',max:20}"></span> <span class="help"> ���� ��ü���� �Է�/��� �� �ش� ��ǰ �ֹ����� ����</span></td>
	</tr>
	<tr>
		<th>������ ����</th>
		<td colspan="3">
			<?php
			$tags = $form->getTag('use_emoney');
			?>
			<label><?=$tags[0]?> �⺻��å ����</label> <span class="help"><a href="../basic/emoney.php" target="_blank">[�⺻���� > �����ݼ��� > ��ǰ ���ϸ���(������) ���޿� ���� ��å]</a> ���� ���� ���ؿ� ����</span> <br />
			<label><?=$tags[1]?> ��ǰ ���� ���ϸ���(������) ����</label> : <?=$form->getTag('goods_reserve')?>��
		</td>
	</tr>
	</table>
<!-- E: ��ǰ ������å -->

<!-- S: ��ǰ ���/�ɼ� -->
	<h2 class="title">��ǰ ���/�ɼ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>���</th>
		<td>
			<?=$form->getTag('totstock'); ?>
			<p class="help">
			(������ "�������" üũ�� ����� �����˴ϴ�)
			</p>
		</td>
		<th>�������</th>
		<td>
			<label><?=$form->getTag('usestock'); ?>�ֹ��� ��� ����</label>
			<p class="help">
			(üũ���ϸ� ��� ������� ������ �Ǹ�)
			</p>
		</td>
	</tr>
	<? if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") { ?>
	<tr>
		<th>�԰�</th>
		<td colspan="3">
			<input type="text" name="pchs_stock" id="pchs_stock" value="" />
		</td>
	</tr>
	<? } ?>
	<tr>
		<th>���ż��� ����</th>
		<td colspan="3">
			�ּұ��ż��� : <?=$form->getTag('min_ea'); ?>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�ִ뱸�ż��� : <?=$form->getTag('max_ea'); ?>
			<p class="help">
			0 �̸� ������ �����ϴ�. ������ ���ż���(�ּұ��ż���, �ִ뱸�ż���)�� �� �ֹ� �Ѱǿ� ���� ���ѻ����̸�, �ɼ��� �ִ� ��� �ɼǺ��� ���� ����˴ϴ�.
			</p>
		</td>
	</tr>
	<tr>
		<th>���԰� �˸�</th>
		<td colspan="3">
			<label><?=$form->getTag('use_stocked_noti'); ?>��ǰ ���԰� �˸� ���</label>
			<span class="help">
			��ǰ/�ɼ� ǰ���� ���������� ���԰� �˸���û ��ư ����
			</span>
		</td>
	</tr>
	<tr>
		<th>�ǸűⰣ ����</th>
		<td colspan="3">
			������/������ :

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
			</select>��
			<select name="sales_range_min[]">
			<? for($i = 0; $i < 60; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeMin[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>��
			 -
			<input type=text name="sales_range_date[]" value="<?=$salesRangeDate[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
			<select name="sales_range_hour[]">
			<? for($i = 0; $i < 24; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeHour[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>��
			<select name="sales_range_min[]">
			<? for($i = 0; $i < 60; $i++) { ?>
				<option value="<? printf('%02d',$i)?>" <?=((int)$salesRangeMin[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
			<? } ?>
			</select>��

			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+7 day"))?>)"><img src="../img/sicon_week.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+15 day"))?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+1 month"))?>)"><img src="../img/sicon_month.gif" align=absmiddle /></a>
			<a href="javascript:setDate('sales_range_date[]',<?=date("Ymd")?>,<?=date("Ymd",strtotime("+2 month"))?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle /></a>

			<p class="help">
			������ �Ⱓ ���ȸ� �Ǹ� �����ϸ�, ������ ������ ���Ŀ��� ���ŵ��� �ʽ��ϴ�. <br/>
			�Ͻ� �Ǹ����� ó���Ͻ� ���, �������� ���糯¥ ������ ���� ��¥�� �־��ֽø� �˴ϴ�.
			</p>

		</td>
	</tr>
	<tr>
		<th>ǰ������ ����</th>
		<td colspan="3">
			<label><?=$form->getTag('runout'); ?>ǰ���� ǥ��</label>
			<span class="help">�ش��ǰ�� ǰ�� ó�� �մϴ�. [ǰ����ǰ ��������] ���� �������� ���� ���θ� ���� �� �� �ֽ��ϴ�.</span>
		</td>
	</tr>
	<tr>
		<th>�����ֹ� ����</th>
		<td colspan="3">
			<?=$form->getTag('sales_unit'); ?> ��
			<p class="help">
				������ ���� ������ �ֹ� �Ǹ�, ��ٱ��Ͽ� ���ϴ�. ��۴����ʹ� �ٸ��� ������ ������ ������� ���� �ʽ��ϴ�. <br/>
				�ֹ��ÿ��� ����Ǹ�, �κ���ҽÿ� ������� �ʽ��ϴ�.
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
	<span class="help" id="el-use-option-toggle-help">�� ��ǰ�� �ɼ��� �������ΰ�� ����ϼ��� (����, ������ ��)</span>
	</div>

	<?=$form->getTag('use_option'); ?>

	<table class="admin-form-table" id="el-option-form">

	<? if($purchaseSet['usePurchase'] == "Y" && $processMode == "register") { ?>
	<tr class="IF_mode_IS_register">
		<th>����ó �������</th>
		<td>
			<div>
				<label><input type="radio" name="purchaseApplyOption" value="1" checked onclick="nsAdminGoodsForm.purchase.setType(1);" /> ����ó �������� <span class="help">�߰��ɼ��� ������ ����ó���� �԰� �� ���</span></label>
			</div>
			<div>
				<label><input type="radio" name="purchaseApplyOption" value="2" onclick="nsAdminGoodsForm.purchase.setType(2);" /> ����ó �������� <span class="help">�߰��ɼ��� ���� �ٸ� ����ó���� �԰� �� ��� (��� ��, ��� �Է�)</span></label>
			</div>
		</td>
	</tr>
	<? } ?>
	<!-- if �ټ� �ɼ� -->
	<?
	// �ɼǸ� ����
	$optionNames = $goods->getOptionName();
	$optionValues = $goods->getOptionValue();
	$optionNamesSize = sizeof($optionNames);
	?>
	<? if ($processMode == 'register' || $goodsOptions->count() > 1) { ?>
	<tr>
		<th>�ɼ� ��¹��</th>
		<td>
			<?php
			$tags = $form->getTag('opttype');
			?>
			<label><?=$tags['��ü��'];?>��ü��</label> <? if ($processMode == 'modify') { ?><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.sort('<?=$goods->getId()?>', 'single'); return false;"><img src="../img/buttons/btn_option_integral.gif" /></a><? } ?>
			<label><?=$tags['�и���'];?>�и���</label> <? if ($processMode == 'modify') { ?><a href="javascript:void(0);" onclick="nsAdminGoodsForm.option.sort('<?=$goods->getId()?>', 'double'); return false;"><img src="../img/buttons/btn_option_discrete.gif" /></a><? } ?>

			<p class="help">
				�ɼ�(ǰ��) ���ε�Ͻ� �ɼ� ��¼����� �ʱ�ȭ �˴ϴ�.
			</p>
		</td>
	</tr>
	<? } ?>
	<tr class="IF_mode_IS_register">
		<th>�ɼ� ����ϱ� <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
		<table class="admin-form-table" id="el-option-table">
		<thead>
		<tr>
			<th>�ɼǸ�</th>
			<th>�ɼǰ� <span class="help">�޸�(,)�� ���� (ex: ����, �Ķ�)</span></th>
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
		<th>�ɼ�<br> ����/��� ���� <img src="../img/icons/bullet_compulsory.gif"></th>
		<td>
		<!-- // option form -->

		<? if ($goodsOptions->count() > 1) { ?>
		<div style="margin-bottom:5px;">
			<span style="border:1px solid #0070C0;background:#a7f5a1;width:20px;height:10px;display:inline-block;margin:0;"></span> ��ǥ���� �����ɼ�
			<span class="help"> ��»����� �ɼ��� �� ���� �ִ� �ɼ��� �������� ��ǥ������ �����˴ϴ�. </span>
		</div>
		<? } ?>


		<table class="admin-form-table" id="el-option-list">
		<? if ($optionNamesSize > 0) { ?>
		<thead>
		<tr>
			<? for ($i=0;$i<$optionNamesSize;$i++) { ?>
			<th><input type="text" name="optnm[]" value="<?=$optionNames[$i]?>" style="width:100%;"></th>
			<? } ?>
			<th style="width:80px;">���</th>
			<th style="width:80px;">�ɼ��Ǹűݾ�</th>
			<th style="width:80px;">����</th>
			<th style="width:80px;">���԰�</th>
			<th style="width:80px;">������</th>
			<th style="width:30px;">���</th>
			<th style="width:30px;">����</th>
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
			<td colspan="<?=$optionNamesSize?>">�ϰ�����</td>
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
			<span class="specialchar">��</span> ��ϵ� �ɼ������� ��ü ������ �Ұ��� �մϴ�. (�ɼ� 1���̻� ��� �ʼ�)<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;������忡�� [�ɼǻ��ε��]�� ���� ���� ����� �ּ���.<br />
			<span class="specialchar">��</span> �ɼ� ��¼����� ������忡�� ����/���� �����մϴ�.
		</p>

		<div class="button-container IF_mode_IS_modify">
			<? if ($optionNamesSize < 1) { ?>
			<div class="al">
			<p class="help">
			���� ��ǰ�� ���Ͽɼ� ��ǰ�Դϴ�. <br/>
			���ݿɼ� �߰��� [�ɼ�(ǰ��) ���ε��] �ϱ⸦ ���� ��� �����մϴ�.
			</p>
			</div>
			<? } else { ?>
			<div class="al">
				<a href="javascript:void(0);" class="default-btn" onclick="nsAdminGoodsForm.option.insert(<?=$goods->getId()?>);return false;"><img src="../img/buttons/btn_option_plus.gif" /></a>
			</div>
			<div class="al">
			<p class="help">
			���ݿɼ� �߰��� [�ɼ�(ǰ��) ���ε��] �ϱ⸦ ���� ��� �����մϴ�.
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
		<th>�ɼǺ� �̹���/���� ����</th>
		<td>
		<!-- // option image form -->

			<div id="el-option-image">
			<?
			// ���� ���� ���Ŀ�, �߰��� �ɼǱ��� �����;� �ϹǷ�,
			// Clib_Model_Goods_Goods::getOptions �� �̿��� ����� �̿��Ͽ� �����Ѵ�.
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
			�� <b><?=$name?> �̹���/���� ����</b>
			&nbsp;&nbsp;
			<label class="extext"><input type="radio" name="option_image_type[<?=$k?>]" value="img" class="null" <?=($kind != 'color') ? 'checked' : ''?> onclick="nsAdminForm.toggle.is(event, 'img');nsAdminForm.toggle.is(event, 'color');">�̹���</label>
			<label class="extext"><input type="radio" name="option_image_type[<?=$k?>]" value="color" class="null" <?=($kind == 'color') ? 'checked' : ''?> onclick="nsAdminForm.toggle.is(event, 'color');nsAdminForm.toggle.is(event, 'img');">����Ÿ�� ���</label>

			<table class="admin-form-table">
			<tbody>
			<?
			$icons = $goodsOptions->getNthIcons($k + 1);
			foreach ($values as $value) {
			?>
			<tr>
				<th><?=$value?> ������</th>
				<td>
					<div class="IF_option_image_type[<?=$k?>]_IS_img">
					<input type="file" name="option_icon_<?=$k?>[<?=get_js_compatible_key($value)?>]" class="opt gray">
					<?if($kind == 'img' && $icons[$value]){?>
					<div style="margin-top:3px;">
						<input type=checkbox class="null" name="del[option_icon_<?=$k?>][<?=get_js_compatible_key($value)?>]" value="delete">
						<font class=small color=#585858>���� (<?=$icons[$value]?>)</font>
						<img src='../../data/goods/<?=$icons[$value]?>' width="20" style='border:1 solid #ccc' onclick="popupImg('../data/goods/<?=$icons[$value]?>','../');" class="hand" onerror="this.style.display='none'" align="absmiddle">
					</div>
					<?}?>
					</div>
					<div class="IF_option_image_type[<?=$k?>]_IS_color">
					���� �Է� : #<input type="text" name="option_color_<?=$k?>[<?=get_js_compatible_key($value)?>]" value="<?=$icons[$value]?>" size="8" maxlength="6"><a href="javascript:nsAdminGoodsForm.openColorTable('<?=get_js_compatible_key($value)?>','option_color_<?=$k?>');"><img src="../img/codi/btn_colortable_s.gif" border="0" alt="����ǥ ����" align="absmiddle" /></a>
					</div>
				</td>
				<? if ($k == 0) { ?>
				<th>��ǰ�̹���</th>
				<td>
					<input type=file name="option_image[<?=get_js_compatible_key($value)?>]" class="opt gray">
					<?if($images[$value]){?>
					<div style="margin-top:3px;">
						<input type=checkbox class="null" name="del[option_image][<?=get_js_compatible_key($value)?>]" value="delete">
						<font class=small color=#585858>���� (<?=$images[$value]?>)</font>
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
	<!-- if �ټ� �ɼ� -->
	</table>
	<script type="text/javascript">
	nsAdminGoodsForm.option.toggle(<?=$goods['use_option'] ? 'true' : 'false'?>);
	</script>

<!-- E: ��ǰ ���/�ɼ� -->

<!-- S: �߰��ɼ�/�������Է¿ɼ� -->
	<h2 class="title">�߰��ɼ�/�������Է¿ɼ�<span>�߰��ɼ��� ������ ����� �� ������, �߰���ǰ�� �Ǹ��ϰų� ����ǰ�� ������ ���� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/icon_sample.gif" border="0" align=absmiddle /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>�߰��ɼ�</th>
		<td>
			<?
			foreach($form->getTag('use_add_option') as $label => $tag) {
				printf('<label>%s%s</label>',$tag, $label);
			}
			?>
			<p class="help">
			���� �������� �ʴ� �߰���ǰ �� ����ǰ�� ����� �� �ִ� ����Դϴ�. <br />
			�ش� ��ǰ�� ������ ���� �� ����, ���� ������ ���Ե��� ������, ����(���¸��� ��������)�� ������ũ ��ǰ������ �������� �ʽ��ϴ�.
			</p>

			<!-- // selectable form -->
			<table class="admin-form-table IF_use_add_option_IS_1" id="el-add-option" style="margin-top:10px;">
			<thead>
			<tr class="ac">
				<th>�ɼǸ�</th>
				<th>�ɼǰ�</th>
				<th>�ɼǱݾ�</th>
				<th>�ʼ�</th>
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
					<option value=''>�ɼǹٱ��� ����</option>
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
		<th>������ �Է� �ɼ�</th>
		<td>
			<?
			foreach($form->getTag('use_add_input_option') as $label => $tag) {
				printf('<label>%s%s</label>',$tag, $label);
			}
			?>
			<p class="help">
			������ ���� �Է��� ���� �� �ִ� �ɼ��Դϴ�. ex) �̴ϼ� ����� <br />
			(�ش� ���� ������ ���� DB ���ε�/�ٿ�ε� ����� �������� �ʽ��ϴ�) <br />
			�ش� ��ǰ�� ������ ���� �� ����, ���� ������ ���Ե��� ������, ����(���¸��� ��������)�� ������ũ ��ǰ������ �������� �ʽ��ϴ�.
			</p>

			<!-- // inputable form -->
			<table class="admin-form-table IF_use_add_input_option_IS_1" id="el-add-input-option" style="margin-top:10px;">
			<thead>
			<tr class="ac">
				<th>�ɼǸ�</th>
				<th>�Է¿ɼ� ���ڼ� ����</th>
				<th>�ɼǱݾ�</th>
				<th>�ʼ�</th>
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
					<input type="hidden" name="additional_option[inputable][sno][<?=$i?>][]" value="<?=$additional_option[$type]['sno'][$i][$j]?>"><input type="text" name=additional_option[inputable][value][<?=$i?>][] value="<?=$additional_option[$type]['value'][$i][$j]?>" style="width:50px"> ��
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
<!-- E: �߰��ɼ�/�������Է¿ɼ� -->

<!-- S: ��ǰ ���� ���� -->
	<h2 class="title">��ǰ ���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">

	<tr>
		<th>ȸ������ ����</td>
		<td>
			<label><?=$form->getTag('exclude_member_discount'); ?>ȸ�� �������� ���� ����</label>
			<p class="help">
			���ý�, ��ǰ�� ���ΰ� ȸ������ �ߺ� ���� �ȵ�
			</p>
		</td>
	</tr>
	<tr>
		<th>��ǰ�� ���� ����</td>
		<td>
			<?php
			foreach ($form->getTag('use_goods_discount') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<!-- // if ��ǰ�� ���� ��� -->
			<div class="IF_use_goods_discount_IS_1">
			<table class="admin-form-table">
			<tr>
				<th>�Ⱓ</th>
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
					</select>��
					<select name="goods_discount_by_term_range_min[]">
					<? for($i = 0; $i < 60; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeMin[0] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>��
					 -
					<input type=text name="goods_discount_by_term_range_date[]" value="<?=$discountRangeDate[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" style="width:80px;" class="ac">
					<select name="goods_discount_by_term_range_hour[]">
					<? for($i = 0; $i < 24; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeHour[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>��
					<select name="goods_discount_by_term_range_min[]">
					<? for($i = 0; $i < 60; $i++) { ?>
						<option value="<? printf('%02d',$i)?>" <?=((int)$discountRangeMin[1] == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
					<? } ?>
					</select>��

				</td>
			</tr>
			<tr>
				<th>��� �� �ݾ�</th>
				<td>
					<?php
					foreach ($form->getTag('goods_discount_by_term_for_specify_member_group') as $label => $tag) {
						echo sprintf('<label>%s%s</label> ',$tag, $label);
					}
					?>
					<!-- ȸ�� �� ��ȸ�� ��ü -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_2">
					<tr>
						<td>
							���αݾ� : <input type="text" name="goods_discount_by_term_amount_for_nonmember_all" value="<?=$form->getValue('goods_discount_by_term_amount_for_nonmember_all')?>">
							<select name="goods_discount_by_term_amount_type_for_nonmember_all">
								<option value="%" <?=$form->getValue('goods_discount_by_term_amount_type_for_nonmember_all') == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$form->getValue('goods_discount_by_term_amount_type_for_nonmember_all') == '=' ? 'selected' : ''?>>��</option>
							</select>
						</td>
					</tr>
					</table>

					<!-- ȸ����ü -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_0">
					<tr>
						<td>
							���αݾ� : <input type="text" name="goods_discount_by_term_amount_for_all" value="<?=$form->getValue('goods_discount_by_term_amount_for_all')?>">
							<select name="goods_discount_by_term_amount_type_for_all">
								<option value="%" <?=$form->getValue('goods_discount_by_term_amount_type_for_all') == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$form->getValue('goods_discount_by_term_amount_type_for_all') == '=' ? 'selected' : ''?>>��</option>
							</select>
						</td>
					</tr>
					</table>

					<!-- Ư�� ȸ�� �׷� -->
					<table class="nude padding-midium IF_goods_discount_by_term_for_specify_member_group_IS_1" id="el-goods-discount-by-term">
					<?
						foreach($ruleSets as $k => $ruleSet) {
							$action = $k > 0 ? 'del' : 'add';
					?>
					<tr>
						<td>
							��� :
							<select name="goods_discount_by_term_target[]">
								<? foreach ($memberGroups as $memberGroup) { ?>
								<option value="<?=$memberGroup['level']?>" <?=$ruleSet['target'] == $memberGroup['level'] ? 'selected' : ''?>><?=$memberGroup['grpnm']?></option>
								<? } ?>
							</select>
						</td>
						<td>
							���αݾ� : <input type="text" name="goods_discount_by_term_amount[]" value="<?=$ruleSet['amount']?>">
							<select name="goods_discount_by_term_amount_type[]">
								<option value="%" <?=$ruleSet['unit'] == '%' ? 'selected' : ''?>>%</option>
								<option value="=" <?=$ruleSet['unit'] == '=' ? 'selected' : ''?>>��</option>
							</select>
						</td>
						<td><a href="javascript:void(0);" onclick="nsAdminGoodsForm.discount.<?=$action?>Group(event);"><img src="../img/i_<?=$action?>.gif" /></a></td>
					</tr>
					<? } ?>
					</table>
				</td>
			</tr>
			<tr>
				<th>�������</th>
				<td>
					<?php
					foreach ($form->getTag('goods_discount_by_term_use_cutting') as $label => $tag) {
						echo sprintf('<label>%s%s</label> ',$tag, $label);
					}
					?>

					<?=$form->getTag('goods_discount_by_term_cutting_unit')?> �� ����
					<?=$form->getTag('goods_discount_by_term_cutting_method')?>
					<p class="help">
						�Ǹűݾ��� %������ ��ǰ�� ���� ������ �߻��ϴ� 1�� ���� �� 10�� ���� ���αݾ��� �����Ͽ� �����մϴ�.<br/>
						Ex) �Ǹűݾ� 1,700���� 7% ���� ? ���αݾ� 119�� �߻�<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;=> 1�� ���� ����� ���� : 110��, �ݿø� : 120��, �ø� : 120�� ���αݾ� ����<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;=> 10�� ���� ����� ���� : 100��, �ݿø� : 100��, �ø� : 200�� ���αݾ� ����<br/>
					</p>
					<p class="help" style="color: #ff0000;">
						�� ����� ���αݾ��� %�� �����ÿ��� ���� �˴ϴ�.
					</p>
				</td>
			</tr>
			</table>
			<p class="help">
			���� ���� ��ǰ���αݾ��� �������� ���� �⺻ �ǸŰ��� �������� ������ ����˴ϴ�.
			</p>
			</div>
			<!-- // if ��ǰ�� ���� ��� -->
		</td>
	</tr>
	</table>
<!-- E: ��ǰ ���� ���� -->

<!-- S: ��ǰ �߰����� ���� -->
	<h2 class="title">��ǰ �߰����� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>��������</th>
		<td><?=$form->getTag('manufacture_date'); ?></td>
		<th>��ȿ����</th>
		<td><?=$form->getTag('effective_date_start'); ?> ~ <?=$form->getTag('effective_date_end'); ?></td>
	</tr>
	<tr>
		<th>���Ű���<br /> ȸ���׷� ����</th>
		<td colspan="3">
			<?php

			foreach ($form->getTag('buyable') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<?=$form->getTag('buyable_member_group'); ?>

			: <a href="javascript:void(0);" onclick="nsAdminGoodsForm.buyable.openMemberGroupSelector();return false;"><img src="../img/buttons/btn_group_view.gif" /></a>

			<p class="help">
				Ư�� ȸ���� ���� �����ϵ��� ����/���� �� �� �ֽ��ϴ�.<br />
				Ư�� ȸ���׷� ���� �� <a href="javascript:void();" onclick="nsAdminGoodsForm.buyable.openMemberGroupSelector();">[���Ű��� ȸ���׷� ����/����]</a> ��ư�� Ŭ���Ͽ� ���� �� Ȯ���ϼ���
			</p>
		</td>
	</tr>
	<tr>
		<th>�������� <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<span class=&quot;red&quot;>�� û�ҳ� ���ظ�ü���� ������ ��ǰ�� ��� �ݵ�� �������� ����� �����ϼž� �մϴ�.</span><p>����������ȸ ��ÿ� ���� û�ҳ� ���ظ�ü���� �����Ǵ� ��ǰ�� 19�� �̸� ���ظ�ü�� ǥ�ø� �ؾ� �ϸ�, ���������� �ϱ� ������ �ش� ��ǰ�� ������ Ȯ�� �� �ֹ��� �� �� ������ �ؾ� �մϴ�.<br />�������� ��ǰ���� ������ ��ǰ�� ��� ���������� �ϱ� ������ ��ǰ �̹��� �� �������� Ȯ�� �� �� ������, �ش� ��ǰ Ŭ���� �������� Ȯ���ϱ� �������� ����˴ϴ�.</p>"></th>

		<td colspan="3">
			<?php
			foreach ($form->getTag('use_only_adult') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>

			<p class="help">
				�ش� ��ǰ�� �������� ���ٽ� ��������Ȯ�� ��Ʈ�� �������� ��µǸ�, ���� �̹����� 19�� �̹����� ��ü�Ǿ� �������ϴ�. <br />
				�������� ����� ������ ���� ���� ��û�Ϸ� �� �̿� �����մϴ�. <a href="../member/realname_info.php" target="_blank"><img src="../img/buttons/btn_confirmation.gif" /></a><br />

				<br /><br />

				<span class="specialchar">��</span> �� �Ǹ����� ���񽺴� �������� �������� ������� �ʽ��ϴ�.<br />
				����� ��������, ����������ǰ�� ��� ���������� Ȯ�ε� ȸ���� ���Ͽ� ��ǰ�� ��������, �����ϱ� ���񽺴� PC�������� ���⿡���� ���� �˴ϴ�.<br />
			</p>
			</div>

		</td>
	</tr>
	</table>
<!-- E: ��ǰ �߰����� ���� -->

<!-- S: ���û�ǰ -->
	<h2 class="title">���û�ǰ<span>�̻�ǰ�� �����ִ� ��ǰ�� ��õ�ϼ���! ���û�ǰ�� PC������ ����ϼ� ��� ����˴ϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>���û�ǰ ������</th>
		<td class="noline">
			<?php
			$tags = $form->getTag('relationis');
			?>
			<label><?=$tags['�ڵ�']?>�ڵ� <span class="help">(���� �з� ��ǰ�� �������� ������)</span></label>
			<label><?=$tags['����']?>���� <span class="help">(�Ʒ� ���� ���õ��)</span></label>
		</td>
	</tr>
	</table>

	<div id="divRefer" class="IF_relationis_IS_1" style="margin-top:10px;">
	<input type="hidden" name="relation" id="el-relation" value="">

	<p style="margin:0 0 5px 0;">
		���� ���û�ǰ : <span id="el-related-goods-count"><?=sizeof($r_relation)?></span> ��
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
		<th><a href="javascript:void(0);" onclick="nsAdminGoodsForm.relate.select();">����</a></th>
		<th>���ε��</th>
		<th></th>
		<th>��ϵ� ���û�ǰ</th>
		<th>���û�ǰ �����Ⱓ</th>
		<th>�����</th>
		<th>����</th>
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
			if (!$v[r_start] && !$v[r_end]) echo '���ӳ���';
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
		<dt>���ε�� <img src="../img/icn_1.gif" align="absmiddle" /></dt>
		<dd>�� ��ǰ�� ���ε�� ��ǰ�� ���û�ǰ���� ���ÿ� ��ϵ˴ϴ�. ������ ���ʸ�� �ڵ����� ���û�ǰ ��Ͽ��� ���ܵ˴ϴ�.</dd>

		<dt>���ε�� <img src="../img/icn_0.gif" align="absmiddle" /></dt>
		<dd>�� ��ǰ�� ���û�ǰ���� ���ε�� ���� ������, �� ��ǰ�� ���û�ǰ ��Ͽ��� ��ϵ˴ϴ�.</dd>

		<dd>���û�ǰ �������� "�ڵ�" ���� ������ ���, ���ε�ϰ� ������� ������ ���� �з��� ��ǰ�� �������� �������ϴ�.</dd>
		<dd><span class="specialchar">��</span> ���û�ǰ �������� ������ "��ǰ���� > ���û�ǰ ���� ����" ���� �Ͻ� �� �ֽ��ϴ�. <a href="../goods/related.php" target="_blank">[���û�ǰ ���� ����]</a> �ٷΰ���</dd>
	</dl>

	</div>
<!-- E: ���û�ǰ -->

<!-- S: ��ǰ �̹��� -->
	<h2 class="title">��ǰ �̹���<span>PC/����� ������ ����� ��ǰ�̹����� ����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=48');"><img src="../img/btn_img_q.gif" border="0" align="absmiddle" hspace="2" /></a> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span></h2>

	<!-- �̹��� ��Ϲ�� ���� -->
	<table class="admin-form-table">
	<tr>
		<th>�̹�����Ϲ��</th>
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
	<!-- �̹��� ���� ���ε� -->
	<div class="boxed-help">
		<p>
		ó�� ��ǰ�̹����� ����ϽŴٸ�, �ݵ�� <a href="../goods/imgsize.php" target=_blank><img src="../img/i_imgsize.gif" border=0 align=absmiddle /></a> ���� �����ϼ���!&nbsp;&nbsp;�׸��� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=16')"><img src="../img/btn_resize_knowhow.gif" border=0 align=absmiddle /></a> �� �� �ʵ��ϼ���!</font></a><br />
		<span class="specialchar">��</span> �ڵ���������� Ȯ��(����)�̹����� ����ϸ� ������ �̹������� �ڵ����� ������¡ �Ǵ� ������ ����Դϴ�. <br />
		<span class="specialchar">��</span> �̹��������� �뷮�� ��� ���ؼ� <?=ini_get('upload_max_filesize')?>B������ ����� �� �ֽ��ϴ�.
		</p>
	</div>

	<table class="admin-form-table">
	<?
		foreach ($imgs as $imageType=>$v) {
			$t = array_map("toThumb",$v);
	?>
	<tr>
		<?php if($imageType == 'l') {?>
		<th rowspan="5">PC �̹���</th>
		<?php } ?>
		<th>
			<?=$str_img[$imageType]?>

			<? if ($imageType!="l") { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" name="copy_<?=$imageType?>" onclick="return nsAdminGoodsForm.imageUpload.chkImgCopy(this.form);" title="�����̹����� �̿��� �ڵ�������¡"> <font class=extext><b>�ڵ��������� ���</b></font></div>
				<div style="padding-left:24px;"><font class=extext>(���� <?=$cfg['img_'.$imageType]?> �ȼ�)</font></div>
			<? } else { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" onclick="return nsAdminGoodsForm.imageUpload.chkImgBox(this, this.form)" title="�����̹����� �̿��� �ڵ�������¡"> <font class=extext><b>�ڵ��������� ���</b></font></div>
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
				<div style="padding:0 0" class=noline><input type="checkbox" name=del[img_<?=$imageType?>][<?=$i?>]><font class=small color=#585858>���� (<?=$v[$i]?>)</font></div>
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
		<th rowspan="5">����� �̹���</th>
		<th>
			��뿩��
			<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;red&quot;>����ϼ� v2 ���ÿ��� ����ϼ� �̹����� ������ ������ �� �ֽ��ϴ�.<br>����ϼ� v1 ����ڴ� ����ϼ� �̹��� ��� ������ ���Ͻô� ��� ����ϼ� v2�� ��ȯ �� �ֽñ� �ٶ��ϴ�.</span></p><p><strong>����ϼ� ���� �̹��� ��� : </strong><br>����ϼ��̹����� ������ ����� �� �ֽ��ϴ�. <br>�ڵ��������� ��� �� ����ϼ� Ȯ���̹��� �������� ������¡ �˴ϴ�. <br>����� ȯ�濡 ����ȭ�� �̹��� ����� �ҷ����Ƿ� �ε� �ӵ��� �����ϴ�.<br>��, �̹����� ������ ������ ����ϹǷ� �̹��� ���� ������ �ʿ��մϴ�.</p><p><strong>PC �̹��� ��� : </strong><br>PC �̹����� ����ϼ��� �״�� ����մϴ�. ����ϼ����� �̹��� ��� �� �ε� �ӵ��� ������ �� �ֽ��ϴ�.</p>">
		</th>
		<td class="noline">
			<label><input name="use_mobile_img" value="1" id="use_mobile_img_1" type="radio" <?=($goods->getData('use_mobile_img') == '1' || $_GET['mode'] != "modify") ? 'checked' : ''?> onclick="nsAdminGoodsForm.imageUpload.toggleImg();">����ϼ� ���� �̹��� ���</label>
			<label><input name="use_mobile_img" value="0" id="use_mobile_img_0" type="radio" <?=($goods->getData('use_mobile_img') == '0') ? 'checked' : ''?> onclick="nsAdminGoodsForm.imageUpload.toggleImg();">PC �̹��� ���</label>
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
				<label class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" name="copy_<?=$imageType?>" onclick="return nsAdminGoodsForm.imageUpload.chkMobileImgCopy(this.form);" title="�����̹����� �̿��� �ڵ�������¡"> <font class=extext><b>�ڵ��������� ���</b></font></label>
				<div style="padding-left:24px;"><font class=extext>(���� <?=$cfg['img_'.$imageType]?> �ȼ�)</font></div>
			<? } else { ?>
				<label class=noline style="font:11px dotum;letter-spacing:-1px;"><input type="checkbox" onclick="return nsAdminGoodsForm.imageUpload.chkMobileImgBox(this, this.form)" title="�����̹����� �̿��� �ڵ�������¡"> <font class=extext><b>�ڵ��������� ���</b></font></label>
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
					<div style="padding:0 0" class=noline><input type="checkbox" name=del[img_<?=$imageType?>][<?=$i?>]><font class=small color=#585858>���� (<?=$v[$i]?>)</font></div>
					<? } ?>
					</td>
					<td><?=goodsimg($t[$i],20,"style='border:1 solid #ccc' onclick=popupImg('../data/goods/$v[$i]','../') class=hand",2)?></td>
				</tr>
				<? } ?>
				</table>
			</div>
			<div class="use_mobile_img_0">
				<select name="img_pc_<?=$imageType?>">
					<option value="img_l"<?=($selected == 'img_l' || (!$selected && $imageType == 'z')) ? ' selected' : ''?>>PC Ȯ��(����)�̹���</option>
					<option value="img_m"<?=($selected == 'img_m' || (!$selected && $imageType == 'y')) ? ' selected' : ''?>>PC ���̹���</option>
					<option value="img_s"<?=($selected == 'img_s' || (!$selected && $imageType == 'x')) ? ' selected' : ''?>>PC ����Ʈ�̹���</option>
					<option value="img_i"<?=($selected == 'img_i' || (!$selected && $imageType == 'w')) ? ' selected' : ''?>>PC �����̹���</option>
				</select>
				<? if (in_array($imageType,array("w","x"))) { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><font class=extext>PC�̹��� �� ��� �� �̹����� �����մϴ�. Ȯ��/�� �̹��� ���� �� ù��° ��ϵ� �̹����� ������ �� �ֽ��ϴ�.</font></div>
				<? } else { ?>
				<div class=noline style="font:11px dotum;letter-spacing:-1px;"><font class=extext>PC�̹��� �� ��� �� �̹����� �����մϴ�.</font></div>
				<? } ?>
			</div>
		</td>
	</tr>
	<? } ?>
	</table>
	<!-- //�̹��� ���� ���ε� -->
	</div>

	<div id="image_attach_method_link_wrap">
	<!-- �̹��� ȣ���� URL �Է� -->
		<div class="boxed-help">
			<p>
			�̹��� ȣ���ÿ� ��ϵ� �̹����� �� �ּҸ� �����Ͽ� �ٿ� �ֱ� �Ͻø� ��ǰ �̹����� ��ϵ˴ϴ�.<br />
			ex) http://godohosting.com/img/img.jpg<br />
			�̹����� ���ϸ��� �ѱ۷� �ۼ� �� �Ϻ� ����(���̹����� ��)�� ���������� �̿��Ͻ� �� �����ϴ�.
			</p>
		</div>

		<table class="admin-form-table">
		<? foreach ($urls as $k=>$v) { ?>
		<tr>
			<?php if($k == 'l') {?>
			<th rowspan="5">PC �̹���</th>
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
			<th rowspan="5">����� �̹���</th>
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
	<!-- //�̹��� ȣ���� URL �Է� -->
	</div>
	<!--// �̹��� ��Ϲ�� ���� -->
<!-- E: ��ǰ �̹��� -->

<!-- S: ��ǰ �̹��� ������ ȿ�� -->
	<h2 class="title">��ǰ�̹��� ������ ȿ��<span>��ǰ���̹����� ���콺�� �����Ͽ� ��ǰ�̹����� Ȯ���Ͽ� �� �� �ִ� ����Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></span></h2>
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
			<div>��	<font class="small1" color="#444444">[��ǰ�̹��� ������ ȿ��] ����� ����ϱ� ���ؼ���, �Ʒ� ��ǰ�̹��� ��Ͻ� <font color="#FF0000">���̹���</font>�� ū �������� �̹����� �־�� �մϴ�.<br >
			: ���̹����� ���콺 �����ÿ� ��Ÿ���� Ȯ���̹����� �Է��ؾ� �մϴ�. 500px~800px ������ �̹����� �����մϴ�.</font></div>
			<div>�� <font class="small1" color="#444444">���̹��� �Է¶��� �̹����� ������ �ڵ����� ���̹����� ���콺 ������ ���̴� ū �̹����� ��ϵ˴ϴ�.</font></div>
			<div>�� <font class="small1" color="#444444">Ȯ��(����)�̹��� �Է¶��� �̹����� �ְ� [�ڵ��������� ���] ����� �̿��Ͽ� ���̹����� ����Ͻø�, [��ǰ�̹��� ������ ȿ��]
			����� ����� �Ұ��� �մϴ�. ��, ���̹����� ���� ����ϼž� �մϴ�.</font></div>
			</div>
		</div>
	</div>
	<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- E: ��ǰ �̹��� ������ ȿ�� -->

<!-- S: �ܺ� ������(YouTube) ����ϱ� -->
	<h2 class="title">�ܺ� ������(<img src="../img/icons/icon_youtube.gif" style="vertical-align:middle;">) ����ϱ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<table class="admin-form-table">
	<tr>
		<th>��뼳��</th>
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
		�۰��� �ҽ� ���
		</th>
		<td>
			<div class="field-wrapper">
				<?=$form->getTag('external_video_url'); ?>
			</div>
		</td>
	</tr>
	<tr class="IF_use_external_video_IS_1">
		<th>���� Size ����</th>
		<td>
			<?php
			$tags = $form->getTag('external_video_size_type');
			?>
			<table class="nude">
			<col style="width:100px;">
			<tr>
				<td><label><?=$tags['�⺻']?>�⺻</label></td>
				<td>�ʺ� (Width) : 640</td>
				<td>���� (Height) : 360</td>
			</tr>
			<tr>
				<td><label><?=$tags['�����']?>����� Size</label></td>
				<td>�ʺ� (Width) : <?=$form->getTag('external_video_width'); ?></td>
				<td>���� (Height) : <?=$form->getTag('external_video_height'); ?></td>
			</tr>
			</table>

		</td>
	</tr>
	</table>
<!-- E: �ܺ� ������(YouTube) ����ϱ� -->

<!-- S: ��ǰ �ʼ� ���� -->
	<h2 class="title">��ǰ �ʼ� ����<span>��ǰ �ʼ�(��)������ ����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<div class="boxed-help" style="width:100%;background-color: #f8f8f8">
		<p>
		<span class="specialchar">��</span> <a href="http://www.ftc.go.kr/policy/legi/legiView.jsp?lgslt_noti_no=112" target="_blank"><span class="u">�����ŷ�����ȸ���� ������ ���ڻ�ŷ��� ��ǰ�������� ��ÿ� ���� ������ �ʵ��� �ּ���!</span></a> <br />
		���ڻ�ŷ����� �ǰ��Ͽ� �ǸŻ�ǰ�� �ʼ�(��)���� ����� �ʿ��մϴ�.<br />
		<a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.overview()"><img src="../img/btn_gw_view.gif" /></a>�� �����Ͽ� ��ǰ�ʼ� ������ ����Ͽ� �ּ���.<br />
		��ϵ� ������ ���θ� ��ǰ���������� ��ǰ�⺻���� �Ʒ��� ǥ���·� ��µǾ� �������ϴ�.<br/>
		<br/><b>���̹� ���ļ���, ���������� �� ���ݺ񱳻���Ʈ�� ����Ϸ��� ��ǰ�� <span style="color:#E6008D">�Ʒ� �׸���� �����Ͽ� �����ϰ� �Է�</span>�ϼž� ���������� ��ϵ˴ϴ�.</b>
			<table class="admin-form-table" style="margin-left:10px;margin-bottom:10px;width:900px" style="background-color: #f8f8f8">
				<tr>
					<td>��� �� ��ġ��� <img src="../img/i_copy.gif" align="absmiddle" onclick="prompt('Ctrl+C�� ���� Ŭ������� �����ϼ���', '��� �� ��ġ���')" style="cursor:pointer" /></td>
					<td>����) ���� ��� ������/ ����, ��û 2���� �߰�</td>
					<td>�⺻ ��ۺ� �̿ܿ� ����, ǰ�� � ���� �߰� ��ۺ� �߻��ϴ� ��� ����
						<br/><span style="color:#E6008D">���Ϲ����� �����갣 ������ ���� �߰� ��ۺ�� �ش����� ����</span></td>
				</tr>
				<tr>
					<td>�߰���ġ��� <img src="../img/i_copy.gif" align="absmiddle" onclick="prompt('Ctrl+C�� ���� Ŭ������� �����ϼ���', '�߰���ġ���')" style="cursor:pointer" /></td>
					<td>����) ��ġ�� ���� ����</td>
					<td>�ش� ��ǰ ���Ž� �߰��� ��ġ�� �߻��ϴ� ��� ����</td>
				</tr>
			</table>
		</p>
	</div>

	<div style="margin:10px;">
	�׸��߰� : <a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.add4row();"><img src="../img/btn_ad2.gif" align="absmiddle" /></a> <a href="javascript:void(0);" onclick="nsAdminGoodsForm.information.add2row();"><img src="../img/btn_ad1.gif" align="absmiddle" /></a> �׸�� ���� ���� �ƹ� ���뵵 �Է����� ������ ������� �ʽ��ϴ�.
	</div>

	<table id="el-extra-info-table" class="admin-form-table" style="table-layout:fixed;">
	<thead>
	<tr>
		<th>�׸�</th>
		<th>����</th>
		<th>�׸�</th>
		<th>����</th>
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

			$rowidx = ($m % 2) == 0 ? $m : ++$m;	// index ����
		}

	}
	?>
	</tbody>
	</table>
<!-- E: ��ǰ �ʼ� ���� -->

<!-- S: �о��ִ� ��ǰ ���� -->
	<h2 class="title">�о��ִ� ��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a><span>����ϼ������� ��ǰ ������ �������� ������ �� �ֽ��ϴ�.</span></h2>

	<div style="padding-top:5"></div>

	<table class="admin-form-table">
	<tr>
		<th>��뼳��</th>
		<td>
			<?php
			foreach ($form->getTag('speach_description_useyn') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
			<p class="help">
				����ϼ�V2(default ��Ų ����) �� ��� �����ϸ�, ����ϼ�V1 �� PC���� ���θ����� ������� �ʽ��ϴ�.<br/>
				�Ϻ� �ȵ���̵� ����� ũ��, ���̾����� ������������ ����� ����� �� �ֽ��ϴ�.
			</p>
		</td>
	</tr>
	<tr>
		<th>
			�о��ִ� ��ǰ ����
			<div class="extext">(�ִ� 100��)</div>
		</th>
		<td>
			<?php echo $form->getTag('speach_description'); ?>
			<div style="text-align: right;">
				<span class="inputSize:{target:'speach_description',max:100}"></span>
			</div>
		</td>
	</tr>
	</table>
<!-- E: �о��ִ� ��ǰ ���� -->

<!-- S: ��ǰ ���� -->
	<h2 class="title">��ǰ ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a><span>�Ʒ� <img src="../img/up_img.gif" border=0 align=absmiddle hspace=2>�� ���� �̹����� ����ϼ���.</span> <span class="specialchar">��</span><span style="color:#E6008D">��� �̹��������� �ܺθ�ũ (����, G���� ���� ���¸��� ����)</span><span>�� �������� �ʽ��ϴ�.</span></h2>

	<div class="boxed-help">
	<p>
	<span style="color:#E6008D">�̹��� �ܺθ�ũ</span> �� <span style="color:#E6008D">���¸���</span> �ǸŸ� ���� �̹����� ����Ͻ÷��� <span style="color:#E6008D">�ݵ�� �̹���ȣ���� ����</span>�� �̿��ϼž� �մϴ�. <br />
	�̹���ȣ������ ��û�ϼ̴ٸ� <a href="javascript:popup('http://image.godo.co.kr/login/imghost_login.php',980,700)" name="navi"><img src="../img/btn_imghost_admin.gif" align=absmiddle /></a>, ���� ��û���ϼ̴ٸ� <a href="http://hosting.godo.co.kr/imghosting/service_info.php" target=_blank><img src="../img/btn_imghost_infoview.gif" align=absmiddle /></a> �� �����ϼ���!
	</p>
	</div>

	<div style="padding-top:5"></div>

	<table class="admin-form-table">
	<tr>
		<th>ª������</th>
		<td>
			<textarea name="shortdesc" style="width:100%;height:20px;overflow:visible" class=tline><?=$goods[shortdesc]?></textarea>
		</td>
	</tr>

	<tr>
		<th>�̺�Ʈ ����</th>
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
			<b><span class="specialchar">��</span> ������ ������ ���޼��� (���̹�����,  ���������Ͽ�) �̿� �� �������� ���Ǵ� �׸��Դϴ�.</b><br>
			<a href="../naver/partner.php" target="_blank">[���̹����� ���� �ٷΰ���]</a><br>
			- "������ > ���̹����� ���� > ���̹����� �̺�Ʈ ���� ���� > ��ǰ�� ���� ���" ���� �� ����ϼ���.<br>
			- �̺�Ʈ ����(���빮��+��ǰ�� ����)�� �ִ� 100�ڱ��� �Է� �����մϴ�.<br><br>

			<a href="../daumcpc/partner.php" target="_blank">[���� �����Ͽ� ���� �ٷΰ���]</a><br>
			- "������ > ���� �����Ͽ�" ��û �� ����ϼ���. �����Ͽ� ��ǰ ��Ͽ� ��ǰ ������ �Բ� ��µ˴ϴ�.<br>
			</p>
		</td>
	</tr>
	</table>

	<div id="el-tab" class="tab" style="margin-top:20px;">
		<ol class="navigation" style="margin-bottom:0;">
			<li><span class="head"></span><a href="#�Ϲݻ󼼼���"><span>�Ϲ� �󼼼���</span></a><span class="tail"></span></li>
			<li><span class="head"></span><a href="#����ϻ󼼼���"><span>����� �󼼼���</span></a><span class="tail"></span></li>
		</ol>

		<div id="container_�Ϲݻ󼼼���" class="container">
			<div class="field-wrapper"><textarea name="longdesc" style="width:100%;height:400px" type=editor><?=$goods[longdesc]?></textarea></div>
		</div>

		<div id="container_����ϻ󼼼���" class="container">
			<div class="field-wrapper"><textarea name="mlongdesc" style="width:100%;height:400px;" type=editor><?=$goods[mlongdesc]?></textarea></div>
		</div>
	</div>

<!-- E: ��ǰ ���� -->

<!-- S: ��ǰ �������/��ۺ� -->
	<h2 class="title">��ǰ �������/��ۺ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table">
	<tr>
		<th>��ǰ�� ��ۺ� ����</th>
		<td colspan="3">
			<table class="nude">
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="0" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '0') ? 'checked' : ''?>> ������(�⺻ ��� ��å�� ����)</label></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="1" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '1') ? 'checked' : ''?>> ������</label> <span class="help">�ش� ��ǰ�� ��ۺ� û������ �ʽ��ϴ�.</span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="3" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '3') ? 'checked' : ''?>> ���� ��ۺ�</label> <span style="display:none;" id="gdi3">&nbsp;<input type="text" name="goods_delivery3" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">�� <span class="help">�ش� ��ǰ�� ��ۺ� ������ û������ �ʰ�, ��ǰ ����� �������� �ϵ��� �մϴ�.</span></span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="4" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '4') ? 'checked' : ''?>> ���� ��ۺ�</label> <span style="display:none;" id="gdi4">&nbsp;<input type="text" name="goods_delivery4" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span> <span class="help">�ش� ��ǰ�� ���� �� �ֹ��ݾ��� �þ�� �ϳ��� ��ۺ�� ��� û���˴ϴ�.</span></td>
			</tr>
			<tr>
				<td><label class="noline"><input type="radio" name="delivery_type" value="5" onclick="nsAdminGoodsForm.setDeliveryType();" <?=($goods->getData('delivery_type') == '5') ? 'checked' : ''?>> ������ ��ۺ�</label> <span style="display:none;" id="gdi5">&nbsp;<input type="text" name="goods_delivery5" value="<?=$goods['goods_delivery']?>" size="8" onkeydown="onlynumber()">��</span> <span class="help">�ش� ��ǰ�� ������ ���� �� n ���� ��ۺ� �����Ͽ� û�� �˴ϴ�.</span></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<th>��۹�� �ȳ�</th>
		<td colspan="3">

			<?
			$tmp = explode('|', $goods['delivery_method']);
			$tmp2 = array(
				'�ù�',
				'����(����/���)',
				'ȭ�����',
				'��',
				'��۾���(�ʿ����)',
			);
			?>
			<table class="admin-form-table">
			<tr>
				<th>��۹��</th>
				<td>
					<? foreach($tmp2 as $name) { ?>
					<label><input type="checkbox" name="delivery_method[]" <?=(in_array($name, $tmp)) ? 'checked' : ''?> value="<?=$name?>" ><?=$name?></label>
					<? } ?>
					<input type="text" name="delivery_method[]" value="<?=array_pop(array_diff($tmp, $tmp2))?>">
				</td>
			</tr>
			<tr>
				<th>�������</th>
				<td>
					<input type="text" name="delivery_area" value="<?=$goods['delivery_area']?>" >
					<span class="help">Ư�� ��������� �Է��ϼ���. ��)����</span>
				</td>
			</tr>
			</table>

			<p class="help">
			�����å�������� ������ ������ ��ǰ���� �ȳ��� ����Դϴ�.
			</p>

		</td>
	</tr>
	</table>
<!-- E: ��ǰ �������/��ۺ� -->

<!-- S: ���̹����� 3.0 ���� -->
	<h2 class="title">���̹����� 3.0 ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

	<table class="admin-form-table" style="border:2px solid green;">
	<tr>
		<th>���� �� ���� ���� <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;blue&quot;>�ش� ��ǰ �ʼ� �׸�����, ���� �� ���̹� Ŭ�����α׷��� ����Ǿ� �г�Ƽ ó���˴ϴ�.</p>"></th>
		<td>
			<?=$form->getTag('naver_import_flag')?>
		</td>
		<th>�ǸŹ�� ���� <img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<p><span class=&quot;blue&quot;>�ش� ��ǰ �ʼ� �׸�����, ���� �� ���̹� Ŭ�����α׷��� ����Ǿ� �г�Ƽ ó���˴ϴ�.</p>"></th>
		<td>
			<?=$form->getTag('naver_product_flag')?>
		</td>
	</tr>
	<tr>
		<th>�ֿ� ��� ���ɴ�</th>
		<td>
			<?php
			foreach ($form->getTag('naver_age_group') as $label => $tag) {
				echo sprintf('<label>%s%s</label> ',$tag, $label);
			}
			?>
		</td>
		<th>�ֿ� ��� ����</th>
		<td>
			<?=$form->getTag('naver_gender')?>
		</td>
	</tr>
	<tr>
		<th>�Ӽ� ����</th>
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
			��ǰ�� �Ӽ� ������ ���Ͽ� ��^���� �����Ͽ� �Է��մϴ�.<br>
			��) ����^1��^���Ǻ�^2��^����^��������^��������^��������
			</p>
		</td>
	</tr>
	<tr>
		<th>�˻� �±�</th>
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
			��ǰ�� �˻��±׿� ���Ͽ� ���� ���� �� | �� (Vertical bar)�� �����Ͽ� �Է��մϴ�.<br>
			��) ��������Ͽ��ǽ�|2016S/S�Ż���ǽ�|��ȥ�ľ�����|��ģ��
			</p>
		</td>
	</tr>
	<tr>
		<th>���̹� ī�װ� ID</th>
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
			�ش��ϴ� ī�װ��� ��Ī�ϴµ� ������ �˴ϴ�.<br>
			���̹������� ��ü ī�װ� ����Ʈ�� <a href="https://adcenter.shopping.naver.com/main.nhn" target="_blank">[���̹����� ������Ʈ����]</a>���� �ٿ�ε��� �� �ֽ��ϴ�.
			</p>
		</td>
	</tr>
	<tr>
		<th>���� �� ������ ID</th>
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
			���̹� ���ݺ� ������ ID�� �Է��� ��� ���̹� ���ݺ� ��õ�� ������ �˴ϴ�.<br>
			</p>
			��) http://shopping.naver.com/detail/detail.nhn?nv_mid=<font color="red">8535546055</font>&cat_id=50000151
			<p class="help">
			�ڼ��� ������ �Ŵ����� �����Ͽ� �ֽñ� �ٶ��ϴ�.
			</p>
		</td>
	</tr>
	</table>
<!-- E: ���̹����� 3.0 ���� -->

<!-- S: ���� �޸� -->
	<h2 class="title">���� �޸� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>
	<div class="field-wrapper">
		<?=$form->getTag('memo'); ?>
	</div>
<!-- E: ���� �޸� -->

</div>

<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<!-- qr code ���� -->
<? if($qrCfg['useGoods'] == "y"){ ?>
<h2 class="title">QR Code ����<span>��ǰ �󼼺��⿡ QR Code �� �����ݴϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=3')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2 /></a></h2>
<div style="padding-bottom:5px" class=noline>
<label><input type="radio" name="qrcode" value=y <?=$goods['qrcode'] == 'y' ? 'checked' : ''?>>���</label>
<label><input type="radio" name="qrcode" value=n <?=$goods['qrcode'] != 'y' ? 'checked' : ''?>>������</label>
<?
	if($goods['qrcode'] == 'y'){
		require "../../lib/qrcode.class.php";
		$QRCode = Core::loader('QRCode');
		echo $QRCode->get_GoodsViewTag($goodsno, "goods_down");
	}
?>
</div>
<!-- qr code ���� -->
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

			// ����Ʈ �ڽ� �ɼ� ����
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
			// alert('���ο� �ɼ��� ���ΰ�ħ �ϼž� �ݿ��˴ϴ�.');

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

<? @include dirname(__FILE__) . "/../interpark/_goods_form.php"; // ������ũ_��Ŭ��� ?>
