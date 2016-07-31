<?
$couponcd = $_GET['couponcd'];
$query = "select category from ".GD_COUPON_CATEGORY." where couponcd='$_GET[couponcd]' order by category";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$i++;
	$r_category[$data[category]] = $i;
}

$query = "select * from ".GD_COUPON." where couponcd = '$couponcd'";
$data = $db->fetch($query);

$perc = (substr($data[price],-1) == '%') ? "%" :  "원";
$data[price] = str_replace('%','',$data[price]);
$data[coupontype] = (!$data[coupontype]) ? "0" :  $data[coupontype];
$data[ability] = (!$data[ability]) ? "0" :  $data[ability];
$data[goodstype] = (!$data[goodstype]) ? "0" :  $data[goodstype];
$data[priodtype] = (!$data[priodtype]) ? "0" :  $data[priodtype];
$data[coupon_img] = (!$data[coupon_img]) ? "0" :  $data[coupon_img];
$data[payMethod] = (!$data[payMethod]) ? "0" :  $data[payMethod];

if($data[sdate]){
	$sdate = date("Y-m-d",strtotime($data[sdate]));
	$shour = date("H",strtotime($data[sdate]));
	$smin = date("i",strtotime($data[sdate]));
}
if($data[edate]){
	$edate = date("Y-m-d",strtotime($data[edate]));
	$ehour = date("H",strtotime($data[edate]));
	$emin = date("i",strtotime($data[edate]));
}

if( $data[edncnt]==null ) $data[edncnt]=0;
if( $data[dncnt] == null ) $data[dncnt]=1;
$checked[coupon_img][$data[coupon_img]] = "checked";
$checked[coupontype][$data[coupontype]] = "checked";
$checked[ability][$data[ability]] = "checked";
$checked[goodstype][$data[goodstype]] = "checked";
$checked[priodtype][$data[priodtype]] = "checked";
$checked[eactl][$data[eactl]] = "checked";
$checked[duplctl][$data[duplctl]] = "checked";
$checked[payMethod][$data[payMethod]] = "checked";

$selected['perc'][$perc] = "selected";

if($data[priodtype] == 1)$data[priod] = $data[sdate];
?>
<script language=javascript>
function checkform(form){
	if( form.priodtype['0'].checked && (!form.sdate.value || !form.edate.value )){
		alert('시작일 , 종료일을 입력하세요');
		form.sdate.focus();
		return false;
	}
	if( form.priodtype['1'].checked && (!form.priod.value || form.priod.value < 1 )){
		alert('쿠폰발급일을 1일 이상으로 입력하세요');
		form.focus();
		return false;
	}
	if(!form.price.value){
		alert('할인금액을 입력하세요');
		form.price.focus();
		return false;
	}
	var fieldname = eval("form.elements['goodstype']");
	if (fieldname[1].checked) {
		var fieldname1 = eval("form.elements['category[]']");
		var fieldname2 = eval("form.elements['e_refer[]']");
		if(!fieldname1 && !fieldname2){
			alert('선택상품을 고르세요!');
			return false;
		}
	}
	if(!chkForm(form)) return false;
	return true;
}

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

function displayLayer(layerid){
	document.getElementById('priodid0').style.display='none';
	document.getElementById('priodid1').style.display='none';
	document.getElementById(layerid).style.display='block';
}

function chk_msg(val){
	if(val == 1) document.getElementById('actid').innerHTML='적립합니다';
	else document.getElementById('actid').innerHTML='할인합니다';
}

function chk_coupontype(){
	var f = document.forms[0];
	document.getElementById('goodsallid').style.display = 'none';
	document.getElementById('goodsallid2').style.display = 'none';
	document.getElementById('tgt').innerHTML = '총 상품판매금액을';
	document.getElementById('applyMsg').innerHTML = '쿠폰사용조건';
	document.getElementById('goodstypeMsg1').innerHTML = '모든 상품을 구매할 때 쿠폰을 사용할 수 있습니다';
	document.getElementById('goodstypeMsg2').innerHTML = '특정상품 및 특정카테고리의 상품을 구매할 때 쿠폰을 사용할 수 있습니다';
	if(f.coupontype[1].checked){
		document.getElementById('tgt').innerHTML = '쿠폰이 발급(적용)되는 각각의 상품 판매금액을';
		document.getElementById('applyMsg').innerHTML = '쿠폰발급상품<br/><font class=extext>(쿠폰이 적용되는 상품)</font>';
		document.getElementById('goodstypeMsg1').innerHTML = '전체상품에 발급합니다';
		document.getElementById('goodstypeMsg2').innerHTML = '특정 상품 및 특정 카테고리에 발급합니다';
		document.getElementById('goodsallid').style.display = 'block';
		if(document.getElementsByName('duplctl')[0].checked == true)document.getElementById('goodsallid2').style.display = 'block';
		return;
	}
}

function coupon_img_upload(){
	if(document.getElementsByName('coupon_img')[4].checked == true){
		document.getElementById('cp_img_upload').style.display = '';
	}else{
		document.getElementById('cp_img_upload').style.display = 'none';
	}
}
</script>

<table class=tb style="margin-bottom:10px;">
<col class=cellC>
<tr>
	<td>생성된 쿠폰은 PC버전 쇼핑몰과 모바일샵에서 공통으로 사용됩니다.</br>
		모바일샵에서만 사용할 수 있는 쿠폰은 "모바일샵전용 쿠폰만들기"에서 별도로 생성 가능합니다.
	</td>
</tr>
</table>

<form method=post action="indb.coupon.php" onsubmit="return checkform(this)" enctype="multipart/form-data">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=couponcd  value="<?=$_GET[couponcd]?>">

<table class=tb>
<col class=cellC><col class=cellL style='padding:5,0,5,5'>

<tr>
	<td>쿠폰이름</td>
	<td><input type=text name='name' size=40 maxlength=30 value="<?=$data[coupon]?>" required class=line> <font class=extext>ex) 오픈기념쿠폰, 추석할인쿠폰</td>
</tr>

<tr>
	<td>쿠폰설명</td>
	<td><input type=text name='summa' size=40 maxlength=70 value="<?=$data[summa]?>" class=line> <font class=extext> ex) 특가이벤트! 여름상품 10% 할인쿠폰</td>
</tr>

<tr>
	<td>쿠폰발급방식</td>
	<td>
		<div><input type=radio name=coupontype value='0' class=null <?=$checked[coupontype][0]?> onclick="chk_coupontype();"> 운영자발급 <font class=extext>(쿠폰등록 후 쿠폰리스트에서 운영자가 특정회원에게 발급합니다)</font></div>
		<div><input type=radio name=coupontype value='1' class=null <?=$checked[coupontype][1]?> onclick="chk_coupontype();"> 회원직접다운로드 <font class=extext>(상품상세정보에서 회원이 직접 쿠폰을 다운로드받습니다)</font></div>

		<div id='goodsallid' style="padding:3 0 10 11">
	    <table border=1 bordercolor=#cccccc style="border-collapse:collapse" width=635>
		<tr><td bgcolor=white style="padding:5 0 7 2">
		<div style='padding-left:10'>이 쿠폰의 총 다운로드 횟수를 <input type='text' style='text-align:right' name='dncnt' size=3 value='<?=$data[dncnt]?>' onkeydown='onlynumber()' maxlength='9'>회로 제한합니다 <font class=extext>(공란으로 두면  무제한)</font></div>
		<div style='padding-left:5'><input type=checkbox name='eactl' value='1' <?=$checked[eactl][1]?> class=null> 쿠폰이 적용된 하나의 상품을 한번에 여러개 주문할 때 쿠폰혜택을 모두 제공합니다</div>
		<div style='padding: 1 0 5 28'><font class=extext>(체크안하면 같은 상품을 한번에 여러개 주문시 한개만 쿠폰혜택 제공)&nbsp;</font></div>
		<div style='padding-left:5'><input type=checkbox name='duplctl' value='1' <?=$checked[duplctl][1]?> class=null onclick="chk_coupontype();"> 쿠폰을 사용한 후 다음번 주문시에도 같은 상품의 쿠폰다운로드를 허용합니다</div>
		<div style='padding: 1 0 2 28'><font class=extext>(체크안하면 다음번 주문시 같은 상품의 쿠폰다운로드 허용안함)&nbsp;</font></div>
		</div>
		<div id='goodsallid2'><input type='hidden' style='text-align:right' name='edncnt' size=3 maxlength=9 value='<?=$data[edncnt]?>'></td></tr></table>
		</div>

		<div><input type=radio name=coupontype value='2' class=null <?=$checked[coupontype][2]?> onclick="chk_coupontype();"> 회원가입자동발급 <font class=extext>(회원가입시 자동발급됩니다)</font></div>
		<div><input type=radio name=coupontype value='3' class=null <?=$checked[coupontype][3]?> onclick="chk_coupontype();"> 구매후 자동발급 <font class=extext>(구매후 배송완료시에 자동발급됩니다)</font></div>

		<div style="padding-top:4"></div>
	</td>
</tr>

<tr>
	<td>쿠폰기능</td>
	<td>
		<input type=radio name=ability value='0' class=null <?=$checked[ability][0]?> onclick='chk_msg(this.value);'> 할인쿠폰을 발행합니다 <font class=extext>(구매시 바로 할인되는 쿠폰)</font>&nbsp;&nbsp;
		<input type=radio name=ability value='1' class=null <?=$checked[ability][1]?> onclick='chk_msg(this.value);'> 적립쿠폰을 발행합니다 <font class=extext>(구매 후(배송완료) 적립되는 쿠폰)</font>
	</td>
</tr>

<tr>
	<td>쿠폰금액</td>
	<td>
		<div>
		<span id='tgt'></span>&nbsp;<input type=text class=line name='price' size=10 style="text-align:right" maxlength=15 value="<?=$data[price]?>" required onkeydown='onlynumber();'>&nbsp;<select name='perc'><option value='원' <?=$selected['perc']['원']?>>원</option><option value='%' <?=$selected['perc']['%']?>>%</option></select>&nbsp;<span id=actid>할인/적립해주는 쿠폰을 발행합니다</span></div>
		</td>
</tr>

<tr>
	<td id="applyMsg">쿠폰발급상품</td>
	<td>
	<table width=100% cellpadding=0 cellspacing=0>
		<tr>
			<td><input type=radio name=goodstype value='0' class=null <?=$checked[goodstype][0]?>> <span id="goodstypeMsg1"></span></td>
		</tr>
		<tr>
			<td height=10></td>
		</tr>

		<tr>
			<td><input type=radio name=goodstype value='1' class=null <?=$checked[goodstype][1]?>> <span id="goodstypeMsg2"></span>&nbsp;<font class=extext>(아래에서 검색후 선정)</font></td>
		</tr>
		<tr>
			<td height=5></td>
		</tr>
		<tr>
			<td>
			<?
			$query = "
			select
				a.goodsno,b.goodsnm,b.img_s,c.price
			from
				".GD_COUPON_GOODSNO." a,
				".GD_GOODS." b,
				".GD_GOODS_OPTION." c
			where
				a.goodsno=b.goodsno
				and a.goodsno=c.goodsno and c.link and go_is_deleted <> '1' and go_is_display = '1'
				and a.couponcd = '$_GET[couponcd]'

			";
			$res = $db->query($query);
			?>
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
			<div style="padding-top:13px"></div>
			<div style=padding-left:8><font class=small1 color=FF0066><img src="../img/icon_list.gif" align="absmiddle">상품 선정 (상품검색 후 선정)</font></div>
			<div id=divRefer style="position:relative;z-index:99;padding-left:8">
				<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_refer[]', 'referX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
				<div id="referX" style="padding-top:3px;">
					<?php while ($v = $db->fetch($res)){ ?>
						<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
						<input type=hidden name="e_refer[]" value="<?php echo $v['goodsno']; ?>" />
					<?php } ?>
				</div>
			</div>
			</td>
		</tr>
	</table>
	</td>
</tr>



<tr>
	<td>쿠폰이미지</td>
	<td>
	<table cellpadding=0 cellspacing=0>
		<tr>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon01.gif"><div><input type=radio class=null name=coupon_img value=0 <?=$checked[coupon_img][0]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon02.gif"><div><input type=radio class=null name=coupon_img value=1 <?=$checked[coupon_img][1]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon03.gif"><div><input type=radio class=null name=coupon_img value=2 <?=$checked[coupon_img][2]?> onclick ="coupon_img_upload();"></div></td>
			<td width=5></td>
			<td align=center><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/common/coupon04.gif"><div><input type=radio class=null name=coupon_img value=3 <?=$checked[coupon_img][3]?> onclick ="coupon_img_upload();"></div></td>
    	</tr>
		</table>
	</td>
</tr>
<tr>
	<td>쿠폰이미지 직접 등록</td>
	<td>
	<table cellpadding=0 cellspacing=0>
		<tr>
			<td>
					<?
					if(!empty($data['coupon_img_file'])){
						$coupon_img_path = "../../data/skin/".$cfg['tplSkin']."/img/common/".$data['coupon_img_file'];
					?>
					<img src="<?=$coupon_img_path?>" border="0" align ="absbottom"/>
					<? } ?>
					<div style="padding:2 0 0 0;"> <input type=radio class=null name=coupon_img value=4 <?=$checked[coupon_img][4]?> onclick ="coupon_img_upload();"> 등록
					<span id="cp_img_upload" <? if($data['coupon_img'] != "4"){ ?>style="display:none"<?}?>>
					<input type="file" name="coupon_img_file"/> <span class="small1 extext">(권장사이즈 140 x 50)</span>
				</span>
				</div>
			</td>
    	</tr>
		</table>
	</td>
</tr>
<tr>
	<td>적용기간</td>
	<td>
	<input type=radio class=null name=priodtype value=0 <?=$checked[priodtype][0]?> onclick="javascript:displayLayer('priodid0')"> 시작일, 종료일 선택
	&nbsp;&nbsp;<input type=radio class=null name=priodtype value=1 <?=$checked[priodtype][1]?> onclick="javascript:displayLayer('priodid1')"> 발급일로부터 기간 제한
	<div id=priodid0 style="display:none;">
		<input type=text name=sdate size=10 maxlength=10 value="<?=$sdate?>" onclick="calendar(event,'-')" onkeydown="onlynumber()" class=line>
		<select name="shour">
		<? for($i = 0; $i < 24; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($shour == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>시
		<select name="smin">
		<? for($i = 0; $i <= 59; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($smin == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>분 ~
		<input type=text name=edate size=10 maxlength=10 value="<?=$edate?>" onclick="calendar(event,'-')" onkeydown="onlynumber()"  class=line>
		<select name="ehour">
		<? for($i = 0; $i < 24; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($ehour == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>시
		<select name="emin">
		<? for($i = 0; $i <= 59; $i++) { ?>
			<option value="<? printf('%02d',$i)?>" <?=($emin == $i) ? 'selected' : ''?>><? printf('%02d',$i)?></option>
		<? } ?>
		</select>분
	</div>
	<div id=priodid1 style="display:none;">
		&nbsp; 쿠폰발급일로부터 <input type=text name=priod value="<?=$data[priod]?>" size=5 maxlength=3 onkeydown='onlynumber()'> 일까지 사용기간을 제한합니다.<br>
		&nbsp; 사용 가능일을 <input type=text name=priod_edate size=10 maxlength=10 value="<?=$edate?>" onclick="calendar(event,'-')"  class=line> 로 제한합니다. 입력하지 않을 경우 제한이 없습니다.
	</div>
	</td>
</tr>
<tr>
	<td>쿠폰사용제한</td>
	<td>
		<input type=text name=excPrice size=10 style="text-align:right" maxlength=10 value="<?=$data[excPrice]?>" class=line> 원 이상 구매시에만 사용가능 <font class=extext>(공란으로 두면 구매금액에 상관없이 사용이 가능합니다)</font></td>
</tr>
<tr>
	<td>결제수단사용제한</td>
	<td><input type=radio name="payMethod" value="0" class=null <?=$checked['payMethod'][0]?>> 결제수단 상관 없음
	<input type=radio name="payMethod" value="1" class=null <?=$checked['payMethod'][1]?>> 무통장 입금에서만 사용가능
	<div><font class="extext">무통장 입금에서만 쿠폰 사용 가능하도록 제한하는것은 여신전문금융업법에 저촉 될 수 있습니다.</font> &nbsp;<a href="javascript:popupLayer('../event/popup.credit_financial_law.php',750,430);"><font class="extext_l">[자세히 보기]</font></a></div>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="coupon.php"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<script>chk_msg(0);chk_coupontype();displayLayer('priodid<?=$data[priodtype]?>');</script>