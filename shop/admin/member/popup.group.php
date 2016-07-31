<?
include "../_header.popup.php";

if (is_file('../../conf/config.member_group.php')) {
	include('../../conf/config.member_group.php');
}
else {
	$member_grp_ruleset = array(
	'automaticFl' => '',
	'apprSystem' => '',
	'apprPointTitle' => '',
	'apprPointLabel' => '',
	'terms_p01' => '',
	'apprPointOrderPriceUnit' => '',
	'apprPointOrderPricePoint' => '',
	'terms_p02' => '',
	'apprPointOrderRepeatPoint' => '',
	'terms_p03' => '',
	'apprPointReviewRepeatPoint' => '',
	'terms_p04' => '',
	'apprPointLoginRepeatPoint' => '',
	);
}

if (!$_GET['mode']) $_GET['mode'] = "addGrp";
$title = ($_GET['mode']!="modGrp") ? "추가" : "수정";

if ($_GET['mode']=="modGrp") $disabled = "disabled";
$query = "
	SELECT

		GRP.*,
		RULE.sno AS r_sno,
		RULE.type,
		RULE.by_score_limit,
		RULE.by_score_max,
		RULE.by_number_buy_limit,
		RULE.by_number_buy_max,
		RULE.by_number_review_require,
		RULE.by_number_order_require,
		RULE.mobile_by_number_buy_limit,
		RULE.mobile_by_number_buy_max,
		RULE.mobile_by_number_review_require,
		RULE.mobile_by_number_order_require
	FROM ".GD_MEMBER_GRP." AS GRP
	LEFT JOIN ".GD_MEMBER_GRP_RULESET." AS RULE
	ON GRP.sno = RULE.sno
	WHERE GRP.sno = '".$_GET['sno']."'
";
$data = $db->fetch($query,1);

if($_GET['mode'] == 'modGrp'){
	$query = "select count(*) from ".GD_MEMBER." where level='".$data['level']."'";
	list($memberCount) = $db->fetch($query);
}
/*
 * 패치후 신규 설정값 미적용시, 패치전 상태값(결제금액 기준) 으로 출력되도록 수정
 */
if ($data['moddt'] <= '2011-10-05 09:00:00') {
	if ($data['add_emoney'] > 0 && $data['add_emoney_type'] == 'N') $data['add_emoney_type'] = 'settle_amt';
	if ($data['dc'] > 0 && $data['dc_type'] == 'N') $data['dc_type'] = 'settle_amt';
}

$checked['free'][$data['free_deliveryfee']]		= "checked";

# 현제 사용중인 레벨값을 가지고옴
$res = $db->query("select grpnm,level from ".GD_MEMBER_GRP." order by level asc");
while($row = $db->fetch($res)){
	$except_level['lv'][$row['level']] = $row['level'];
	$except_level['nm'][$row['level']] = $row['grpnm'];
}

# 레벨값 설정
if(!$_GET['adminAuth']){
	$slevel	= 1;
	$elevel	= 79;
}else{
	$slevel	= 80;
	$elevel	= 100;
}

### 제외 카테고리 리스트
$arr_excate=array();
if ($data['excate']){
	$query = "
	select
		catnm,category
	from
		".GD_CATEGORY."
	where
		category in (".$data['excate'].")
	";
	$res = $db->query($query);
	while ($exc = $db->fetch($res)){
		 $r_category[$exc['category']] = $exc['catnm'];
	}
}

### 제외 상품 리스트
$arr_excep=array();
if ($data['excep']){
	$query = "
	select
		a.goodsno,a.goodsnm,a.img_s,b.price
	from
		".GD_GOODS." a,
		".GD_GOODS_OPTION." b
	where
		a.goodsno=b.goodsno and link and go_is_deleted <> '1'
		and a.goodsno in (".$data['excep'].")
	";
	$res = $db->query($query);
	while ($exc=$db->fetch($res)){
		 $arr_excep[] = $exc;
		 $r_excep[] = $exc['goodsno'];
	}
}

// 회원 등급 아이콘 가져오기 및 프리셋 읽기
$icon_path = '../../data/member/icon';
$ar_icons = scandir($icon_path.'/preset');

//2013/03/27 할인율혜택 결제금액 기준 제거(조건미사용)
if( $data['dc_type'] == 'settle_amt') $data['dc_type'] = 'goods';

//2011/09/06 할인율혜택, 추가적립금, 무료배송비 설정 수정 kmn
if( $data['dc_type'] ) $checked['dc_type'][$data['dc_type']] = "checked='checked'";
else $checked['dc_type']['goods'] = "checked='checked'";

$dc_std_amt[$data['dc_type']] = $data['dc_std_amt'];
$dc[$data['dc_type']] = $data['dc'];

if( $data['add_emoney_type'] ) $checked['add_emoney_type'][$data['add_emoney_type']] = "checked='checked'";
else $checked['add_emoney_type']['settle_amt'] = "checked='checked'";

$add_emoney_std_amt[$data['add_emoney_type']] = $data['add_emoney_std_amt'];
$add_emoney[$data['add_emoney_type']] = $data['add_emoney'];

$checked['free_deliveryfee_type'][$data['free_deliveryfee']] = "checked='checked'";
$free_deliveryfee_std_amt[$data['free_deliveryfee']] = $data['free_deliveryfee_std_amt'];

foreach($ar_icons as $k => $v) if ($v == '.' || $v == '..') unset($ar_icons[$k]);
?>
<script>
	function exec_add()
	{
		var ret;
		var str = new Array();
		var obj = document.forms[0]['cate[]'];
		for (i=0;i<obj.length;i++){
			if (obj[i].value){
				str[str.length] = obj[i][obj[i].selectedIndex].text;
				ret = obj[i].value;
			}
		}
		if (!ret){
			alert('카테고리를 선택해주세요');
			return;
		}
		var obj = document.getElementById('objCategory');
		oTr = obj.insertRow();
		oTd = oTr.insertCell();
		oTd.id = "currPosition";
		oTd.innerHTML = str.join(" > ");
		oTd = oTr.insertCell();
		oTd.innerHTML = "\<input type=text name=category[] value='" + ret + "' style='display:none'>";
		oTd = oTr.insertCell();
		oTd.innerHTML = "<a href='javascript:void(0)' onClick='cate_del(this.parentNode.parentNode)'><img src='../img/i_del.gif' align=absmiddle></a>";
	}

	function cate_del(el)
	{
		idx = el.rowIndex;
		var obj = document.getElementById('objCategory');
		obj.deleteRow(idx);
	}
	function fnSetMemberGroupRuleset(el) {

		var id = 'leveling_type_' + el.value;

		$$(el.tagName+'[name="'+el.name+'"]').each(function(el){

			$('leveling_type_' + el.value).setStyle({
				display : (id == 'leveling_type_' + el.value) ? 'block' : 'none'
			});

		});

		var o = document.getElementById('leveling_type_' + el.value);
		o.style.display = 'block';
	}

	function chkForm2(frm) {
		chkForm(frm);
/*
		if(frm.by_number_buy_limit.value) {
			if(!frm.by_number_buy_max.value || (frm.by_number_buy_limit.value > frm.by_number_buy_max.value)) {
				alert('구매금액이 '+frm.by_number_buy_limit.value+' 원 보다 높게 설정 되어야 합니다.');
				frm.by_number_buy_max.focus();
				return false;
			}
		}

		if(frm.by_number_buy_limit.value) {
			if(!frm.by_number_buy_max.value || (frm.by_number_buy_limit.value > frm.by_number_buy_max.value)) {
				alert('구매금액이 '+frm.by_number_buy_limit.value+' 원 보다 높게 설정 되어야 합니다.');
				frm.by_number_buy_max.focus();
				return false;
			}
		}

		if(!frm.by_number_buy_limit.value && frm.by_number_buy_max.value) {
			alert('구매금액이 '+frm.by_number_buy_limit.value+' 원 보다 높게 설정 되어야 합니다.');
			frm.by_number_buy_max.focus();
			return false;
		}
*/
	}

	function check_dormantInfo(){
		var menu = document.getElementsByName("menu[]");
		var member, dormant;

		for(var i=0; i<menu.length; i++){
			if(menu[i].value == 'member'){
				member = menu[i];
			}
			else if(menu[i].value == 'dormant'){
				dormant = menu[i];
			}
			else {

			}
		}

		if(dormant.checked === true && member.checked === false){
			alert("회원 관리 기능 권한 있는 관리자만 휴면 회원 관리 권한 부여가 가능합니다.");
			dormant.checked = false;
		}
		if(dormant.checked === true){
			if(!confirm("휴면 회원의 정보는 영업목적으로 사용 시 법적으로 처벌받을 수 있습니다.\n권한 설정에 유의하시기 바랍니다.")){
				dormant.checked = false;
			}
		}
		return;
	}
</script>
<style>
fieldset.group-icon-wrap {display:block;margin:10px 4px 5px 4px;width:100%;}
fieldset.group-icon-wrap legend {padding:0 5px 0 5px}
fieldset.group-icon-wrap ol {list-style:none;padding:0;margin:10px;}
fieldset.group-icon-wrap ol li {margin:2px;padding:5px;float:left;cursor:pointer;height:16px}
fieldset.group-icon-wrap ol li.on {background:#eeeeee;}


</style>

<div class="title title_top"><?=($_GET['adminAuth'])?"관리자그룹".$title."<span>관리자그룹을 설정하세요</span>":"회원그룹".$title."<span>회원그룹을 설정하세요</span>"?></div>

<form name="frmMemberGroup" method="post" action="indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this);" target="ifrmHidden">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>">
<input type="hidden" name="sno" value="<?=$_GET['sno']?>">
<input type="hidden" name="adminAuth" value="<?=$_GET['adminAuth']?>">


	<!--그룹정보-->
		<table cellpadding="0" cellspacing="0" border="0" bgcolor="ebebeb"><tr><td bgcolor="e8e8e8">
		<table cellpadding="2" cellspacing="1" border="0" bgcolor="e8e8e8" width="100%">
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">그룹명</td>
			<td bgcolor="ffffff" width=""><input type="text" name="grpnm" value="<?=$data['grpnm']?>" required class="line" onKeyUp="document.getElementById('grpnm_disp_type_text').innerText = this.value;"></td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">그룹 표시</td>
			<td bgcolor="ffffff" width="" class="noline">
			<div>
			<label><input type="radio" name="grpnm_disp_type" value="text" <?=$data['grpnm_disp_type'] != 'icon' ? 'checked' : ''?>>텍스트</label> : <span id="grpnm_disp_type_text"><?=$data['grpnm']?></span>
			</div>
			<div>
			<? $icon = $icon_path.'/'.$data['grpnm_icon']; ?>

			<label><input type="radio" name="grpnm_disp_type" value="icon" <?=$data['grpnm_disp_type'] == 'icon' ? 'checked' : ''?>>아이콘</label> : <img src="<?=(is_file($icon) ? $icon : '../img/ico_noimg_16.gif')?>" id="grpnm_disp_type_icon"/>
			</div>

			<fieldset class="group-icon-wrap"><legend>그룹 아이콘 선택 (기본으로 제공되는 아이콘 이미지 입니다.)</legend>
			<ol>
			<? foreach ($ar_icons as $icon) { ?>
			<li><img src="<?=$icon_path.'/preset/'.$icon?>"></li>
			<? }?>
			</ol>
			</fieldset>

			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">아이콘 등록</td>
			<td bgcolor="ffffff" width="" class="noline">
			<input type="file" name="group_icon">
			<input type="hidden" name="group_icon_preset" value="">
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">그룹레벨</td>
			<td bgcolor="ffffff">
			<select name="level" <?if($memberCount>0){?>onchange="alert('그룹레벨 변경시 그룹에 속한 회원들의 그룹레벨도 함께 변경됩니다.')"<?}?>>
		<?
			for($i = $slevel; $i <= $elevel; $i++){
				if($except_level['lv'][$i] == $i && $data['level'] != $i){
		?>
			<optgroup label="<?=$i?> - [<?=$except_level['nm'][$i]?>]"></optgroup>
		<?
				}else{
					if($data['level'] == $i){
						$strLevelNm		= " - [".$except_level['nm'][$i]."]";
						$strSelected	= "selected";
					}else{
						$strLevelNm		= "";
						$strSelected	= "";
					}
		?>
			<option value="<?=$i?>" <?=$strSelected?>><?=$i?><?=$strLevelNm?></option>
		<?
				}
			}
			echo '</select>&nbsp;['.$data['grpnm'].']에 속한 회원 수 : '.$memberCount;
		?>
		</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" width="160" align="center">실적 수치</td>
			<td bgcolor="ffffff" width="">
				<div id="leveling_type_score" style="display:<?=$member_grp_ruleset['apprSystem'] == 'point' ? 'block' : 'none' ?>;">
					실적 점수 <INPUT class="rline" value="<?=$data['by_score_limit']?>" size="4" type="text" name="by_score_limit" > 점 이상 ~ <INPUT class="rline" value="<?=$data['by_score_max']?>" size="4" type="text" name="by_score_max" > 점 미만
				</div>

				<div id="leveling_type_number" style="display:<?=$member_grp_ruleset['apprSystem'] != 'point' ? 'block' : 'none' ?>;">

				<table cellpadding="0" cellspacing="0" style="width:510px; border:solid 1px #dddddd;">
				<col class=cellC style="width:70px;" ><col class=cellL style="width:220px;"><col class=cellR style="width:220px;">
				<tr style="solid 1px #dddddd;">
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">&nbsp;</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center; border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">샵 전체</td>
					<td style="color:#333333; background:#f6f6f6; font-weight:bold; text-align:center; border-bottom:solid 1px #dddddd;">모바일샵 추가실적</td>
				</tr>
				<tr>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">구매금액</td>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_buy_limit']?>" size="4" type="text" name="by_number_buy_limit" > 원 이상 ~ <INPUT class="rline" value="<?=$data['by_number_buy_max']?>" size="4" type="text" name="by_number_buy_max" > 원 미만
					</td>
					<td style="text-align:left; border-bottom:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_buy_limit']?>" size="4" type="text" name="mobile_by_number_buy_limit" > 원 이상 ~ <INPUT class="rline" value="<?=$data['mobile_by_number_buy_max']?>" size="4" type="text" name="mobile_by_number_buy_max" > 원 미만
					</td>
				</tr>
				<tr>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">구매횟수</td>
					<td style="border-bottom:solid 1px #dddddd; border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_order_require']?>" size="4" type="text" name="by_number_order_require" > 회 이상
					</td>
					<td style="text-align:left; border-bottom:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_order_require']?>" size="4" type="text" name="mobile_by_number_order_require" > 회 이상
					</td>
				</tr>
				<tr>
					<td style="border-right:solid 1px #dddddd;">구매후기</td>
					<td style="border-right:solid 1px #dddddd;">
						<INPUT class="rline" value="<?=$data['by_number_review_require']?>" size="4" type="text" name="by_number_review_require" > 회 이상
					</td>
					<td style="text-align:left;">
						<INPUT class="rline" value="<?=$data['mobile_by_number_review_require']?>" size="4" type="text" name="mobile_by_number_review_require" > 회 이상
					</td>
				</tr>
				</table>
				<div style="height:20px; line-height:20px; color:blue; font-weight:bold;">※ 구매금액설정시 "[ ]이상" 금액과 "[ ]미만" 금액을 모두 입력해야 합니다.</div>

				</div>

			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">할인율혜택</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="dc_type" value="N" style="border:0" <?=$checked['dc_type']['N']?> />할인 혜택을 제공하지 않음</label><br/>

			<label><input type="radio" name="dc_type" value="goods" style="border:0" <?=$checked['dc_type']['goods']?> />판매금액 기준</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="dc_std_amt_goods" class="rline" value="<?= $dc_std_amt['goods']?>" size="7"/> 원 이상 구매시 상품 판매금액의 <input type="text" name="dc_goods" class="rline" value="<?= $dc['goods']?>" style="width:35px;" /> % 할인 혜택을 제공합니다.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">배송비가 제외된 상품 판매금액을 기준으로 할인혜택이 주어집니다<br/>구매금액과 상관없이 할인혜택 제공시에는 금액 입력란을 빈공란 또는 "0"으로 설정하세요</font></div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">할인예외카테고리</td>
			<td bgcolor="ffffff">
			<script src="../../lib/js/categoryBox.js"></script>
			<div style="padding-top:3px"></div>
			<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">카테고리 선정 (카테고리선택 후 오른쪽 선정버튼클릭)</font></div>
			<div style=padding-left:8><script>new categoryBox('cate[]',4,'','');</script>
			<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
			<div class="box" style="padding:10 0 0 10">
			<table  cellpadding=8 cellspacing=0 id=objCategory bgcolor=f3f3f3 border=0 bordercolor=#cccccc style="border-collapse:collapse">
			<?
			if ($r_category){ foreach ($r_category as $k=>$v){ ?>
			<tr>
				<td id=currPosition><?=strip_tags(currPosition($k))?></td>
				<td><input type=text name=category[] value="<?=$k?>" style="display:none">
				<input type=hidden name=sort[] value="<?=-$v?>" class="sortBox right" maxlength=10 <?=$hidden[sort]?>></td>
				<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
				</td>
			</tr>
			<? }} ?>
			</table>
			</div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center" height="50">할인예외상품</td>
			<td bgcolor="ffffff">
			<div id=divRefer style="position:relative;z-index:99">
				<div style="padding:5px 0px 0px 3px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 확인버튼을 누르셔야 최종 저장이 됩니다.</font></div>
				<div id="referX" style="padding-top:3px;">
					<?php foreach($arr_excep as $k => $v){ ?>
						<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
						<input type=hidden name="e_refer[]" value="<?php echo $v['goodsno']; ?>" />
					<?php } ?>
				</div>
			</div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">추가적립금</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="add_emoney_type" value="N" style="border:0" <?=$checked['add_emoney_type']['N']?> />추가적립금 혜택을 제공하지 않음</label><br/>

			<label><input type="radio" name="add_emoney_type" value="goods" style="border:0" <?=$checked['add_emoney_type']['goods']?> />판매금액 기준</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="add_emoney_std_amt_goods" class="rline" value="<?= $add_emoney_std_amt['goods']?>" size="7"/> 원 이상 구매시 상품 판매금액의 <input type="text" name="add_emoney_goods" class="rline" value="<?= $add_emoney['goods']?>" style="width:35px;" /> % 추가적립 혜택을 제공합니다.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">배송비가 제외된 상품 판매금액을 기준으로 추가적립 혜택이 주어집니다<br/>구매금액과 상관없이 적립금 혜택 제공시에는 금액 입력란을 빈공란 또는 "0"으로 설정하세요</font></div>

			<label><input type="radio" name="add_emoney_type" value="settle_amt" style="border:0" <?=$checked['add_emoney_type']['settle_amt']?> />결제금액 기준</label><br/>
			<div style="padding:3px 0px 0px 10px;"><input type="text" name="add_emoney_std_amt_settle_amt" class="rline" value="<?= $add_emoney_std_amt['settle_amt']?>" size="7"/> 원 이상 결제시 총 결제금액의 <input type="text" name="add_emoney_settle_amt" class="rline" value="<?= $add_emoney['settle_amt']?>" style="width:35px;" /> % 추가적립 혜택을 제공합니다.</div>
			<div style="padding:3px 0px 0px 10px;"><font class="extext">배송비및 쿠폰적용 등이 포함된 총 결제금액을 기준으로 추가적립 혜택이 주어집니다<br/>결제금액과 상관없이 적립금 혜택 제공시에는 금액 입력란을 빈공란 또는 "0"으로 설정하세요</font></div>
			</td>
		</tr>
		<tr>
			<td bgcolor="f6f6f6" align="center">무료배송비</td>
			<td bgcolor="ffffff">
			<label><input type="radio" name="free_deliveryfee_type" value="N" style="border:0" <?= $checked['free_deliveryfee_type']['N']?>>무료 배송비 혜택을 제공하지 않음</label><br />
			<label><input type="radio" name="free_deliveryfee_type" value="goods" style="border:0" <?= $checked['free_deliveryfee_type']['goods']?>>판매금액 기준 <input type="text" name="free_deliveryfee_std_amt_goods" class="rline" size="7" value="<?= $free_deliveryfee_std_amt['goods']?>">원 이상 상품구매시 배송비 무료</label><br/>
			<label><input type="radio" name="free_deliveryfee_type" value="settle_amt" style="border:0" <?= $checked['free_deliveryfee_type']['settle_amt']?>>결제금액 기준 <input type="text" name="free_deliveryfee_std_amt_settle_amt" class="rline" size="7" value="<?= $free_deliveryfee_std_amt['settle_amt']?>">원 이상 상품구매시 배송비 무료</label><br />
			<label><input type="radio" name="free_deliveryfee_type" value="Y" style="border:0" <?= $checked['free_deliveryfee_type']['Y']?>>조건없이 모든상품 주문시 배송비 무료</label>
			</td>
		</tr>
		<?
		if($_GET['adminAuth']){
			@include "../../conf/groupAuth.php";
			$arr = array('basic','design','goods','order','member','dormant','board','event','marketing','log','blog','todayshop','mobileShop','selly','shople','etc','hiebay');
			foreach($arr as $v)if(($rAuth[$data['level']] && in_array($v,$rAuth[$data['level']])) || $data['level'] == 100 )$checked['menu'][$v] = "checked";
		?>
		<tr><td bgcolor="f6f6f6" width="160" align="center">관리 권한설정</td>
		<td bgcolor="ffffff">
			<div style="padding-left:15;padding-top:5px"><input type="checkbox" name="menu[]" value="basic" class="null" disabled <?=$checked['menu']['basic']?> />쇼핑몰기본관리 <font class="extext" color="ea0095">(전체관리자에게만 권한이 부여됩니다)</font></div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="design" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['design']?> />디자인관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="goods" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['goods']?> />상품관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="order" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['order']?> />주문관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="member" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['member']?> onclick="javascript:check_dormantInfo();" />회원관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="dormant" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['dormant']?> onclick="javascript:check_dormantInfo();" />휴면 회원관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="board" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['board']?> />게시판관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="event" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['menu']['event']?> />프로모션</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="marketing" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['marketing']?> />마케팅관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="log" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['log']?> />통계관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="blog" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['blog']?> />블로그관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="todayshop" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['todayshop']?> />투데이샵 관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="mobileShop" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['mobileShop']?> />모바일샵관리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="selly" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['selly']?> />셀리</div>
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="shople" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['shople']?> />쇼플</div>
			<!--<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="hiebay" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['hiebay']?> />하이!ebay</div>-->
			<div style="padding-left:15;padding-top:3px;float:left;width:170px"><input type="checkbox" name="menu[]" value="etc" <?if($data['level'] == 100)echo"disabled";?> class="null" <?=$checked['menu']['etc']?> />운영지원</div>
		</td>
		</tr>
		<tr><td bgcolor="f6f6f6" width="160" align="center">통계 권한설정</td>
		<td bgcolor="ffffff">
			<? if($data['level'] == 100 || $rAuthStatistics[$data['level']] == 'y') { $checked['statistics']['y'] = 'checked';}?>
			<div style="padding-left:15;padding-top:5px"><input type="checkbox" name="statistics" value="y" class="null" <?if($data['level'] == 100)echo"disabled";?> <?=$checked['statistics']['y']?> />관리자메인 통계 테이블 보기 </div>
		</td>
		</tr>
		<?}?>
		</table>
		</td></tr></table>
	<!--//그룹정보-->

<div style="margin-bottom:10px;padding-top:10px;" class=noline align="center">
<input type="image" src="../img/btn_confirm_s.gif">
</form>

<script>
linecss(document.form);

$$('fieldset.group-icon-wrap ol li').each(function(el){
	el
	.observe("mouseover", function(event) {
		el.addClassName('on');
	})
	.observe("mouseout", function(event) {
		el.removeClassName('on');
	})
	.observe("click", function(event) {
		var img = el.firstDescendant().readAttribute('src');
		$('grpnm_disp_type_icon').writeAttribute('src', img );
		document.frmMemberGroup.group_icon_preset.value = img;
	});

});
</script>