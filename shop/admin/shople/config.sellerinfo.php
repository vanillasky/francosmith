<?php
$location = "쇼플 > 쇼플 제휴판매 신청";
include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg['shople'];

$origin_area = $origin_value = array();

if ($shopleCfg['origin_select'] == 1) {

	if ($shopleCfg['origin_type']) {
		$rs = $db->query("SELECT DISTINCT `area` as `name`, `area` as `value` FROM ".GD_SHOPLE_ORIGIN_CODE." WHERE `country` = '".($shopleCfg['origin_type'] == '01' ? '국내' : '해외')."'");

		while ($row = $db->fetch($rs,1)) {
			$origin_area[] = $row;
		}
	}

	if ($shopleCfg['origin_value']) {

		$query = "
			SELECT
					`area`, `name`, `value`
			FROM ".GD_SHOPLE_ORIGIN_CODE."
			WHERE
					`area` = (
								SELECT `area` FROM ".GD_SHOPLE_ORIGIN_CODE."
								WHERE `value` = '".$shopleCfg['origin_value']."'
							)

		";
		$rs = $db->query($query);

		while ($row = $db->fetch($rs,1)) {
			$origin_value[] = $row;
		}

		$shopleCfg['origin_area'] = $origin_value[0]['area'];
	}

}
?>

<form method="post" action="./indb.config.php" target="ifrmHidden" id="frmConfig" name="frmConfig">

	<div class="title title_top">쇼플 판매정보 등록<span>쇼플 서비스를 이용하기 위한 기본 판매정보를 등록합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shople&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>원산지</td>
		<td class="noline">

			<div style="float:left;">
				<? foreach($_spt_ar_origin_type as $val => $label) { ?>
				<label><input type="radio" name="origin_type" id="origin_type" value="<?=$val?>" <?=($shopleCfg['origin_type'] == $val) ? 'checked' : '' ?> onclick="nsShople.origin.change(this.value);"><?=$label?></label>
				<? } ?>
			</div>

			<div style="float:left;margin-left:10px;display:<?=($shopleCfg['origin_type'] != '03') ? 'none' : 'block'?>;" id="origin_input_wrap"><input type="text" name="origin_name" value="<?=$shopleCfg['origin_name']?>" class="line" style="border:1px solid #CCCCCC !important;width:140px" maxlength="32"></div>

			<div style="clear:both;margin-top:5px;display:<?=($shopleCfg['origin_type'] != '03') ? 'block' : 'none'?>;" id="origin_select_wrap">
				<span>상세지역 선택 </span> <input type="checkbox" name="origin_select" id="origin_select" value="1" onclick="nsShople.origin.toggle();" <?=$shopleCfg['origin_select'] ? 'checked' : '' ?>>

				<select style="width: 87px" name="origin_area" id="origin_area" class=select>
				<option value="">선택하세요</option>
				<? foreach ($origin_area as $k => $v) { ?>
				<option value="<?=$v['value']?>" <?=($v['value'] == $shopleCfg['origin_area']) ? 'selected' : ''?>><?=$v['name']?></option>
				<? } ?>
				</select>

				<select style="width: 87px" name="origin_value" id="origin_value" class=select>
				<option value="">선택하세요</option>
				<? foreach ($origin_value as $k => $v) { ?>
				<option value="<?=$v['value']?>" <?=($v['value'] == $shopleCfg['origin_value']) ? 'selected' : ''?>><?=$v['name']?></option>
				<? } ?>
				</select>
			</div>

		</td>

	</tr>
	<tr>
		<td>출고지 주소</td>
		<td>
			<input type="text" name="address_out" value="<?=$shopleCfg['address_out']?>" class="line" style="width:300px" maxlength="32">
		</td>
	</tr>
	<tr>
		<td>반품/교환지 주소</td>
		<td>
			<input type="text" name="address_in" value="<?=$shopleCfg['address_in']?>" class="line" style="width:300px" maxlength="32">
		</td>
	</tr>
	<tr>
		<td>A/S 안내</td>
		<td>
			<textarea name="as_info" class="line" style="width:300px;height:80px;"><?=$shopleCfg['as_info']?></textarea>
		</td>
	</tr>
	<tr>
		<td>반품/교환 안내</td>
		<td>
			<textarea name="rtnexch_info" class="line" style="width:300px;height:80px;"><?=$shopleCfg['rtnexch_info']?></textarea>
		</td>
	</tr>
	<tr>
		<td>반품/교환 배송비</td>
		<td>
			반품배송비 : <input type="text" name="return_dlv_price" value="<?=$shopleCfg['return_dlv_price']?>" class="line" style="width:50px" maxlength="5">원
			/
			교환배송비 (왕복) : <input type="text" name="exchange_dlv_price" value="<?=$shopleCfg['exchange_dlv_price']?>" class="line" style="width:50px" maxlength="5">원

		</td>
	</tr>
	<tr>
		<td>배송업체</td>
		<td>
			<select name="dlv_company">
			<option value="">선택</option>
			<? foreach ($_spt_ar_dlv_company as $k => $v) { ?>
			<option value="<?=$k?>" <?=($shopleCfg['dlv_company'] == $k ? 'selected' : '')?>><?=$v?></option>
			<? } ?>
			</select>

		</td>
	</tr>
	</table>

	<div style="position:relative;">
		<div class=button >
		<input type=image src="../img/btn_save.gif">
		</div>
	</div>

</form>






<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">11번가와 연동하기 위한 정보 추가 정보를 등록해야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">출고지 주소를 기준으로 상품이 묶음 배송됩니다. 정확한 출고지 정보를 입력해주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">반품/교환지 주소, A/S안내, 반품/교환안내 내용 등은 11번가 상품페이지에 노출되어 고객들에게 전달되는 내용입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">정확하게 입력해야 합니다.</td></tr>
</td></tr>
</table>
</div>

<script type="text/javascript" src="./_inc/common.js"></script>
<script type="text/javascript">
function _fnInit() {
	nsShople.origin.init();}

Event.observe(document, 'dom:loaded', _fnInit, false);
</script>
<script type="text/javascript">cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
