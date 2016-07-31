<?
$location = "페이퍼쿠폰관리 > 페이퍼쿠폰 만들기";
include "../_header.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$sno = (int)$_GET['sno'];

// 초기값 세팅
$arCoupon['coupon_type'] = 'sale';
$arCoupon['currency'] = '원';
$arCoupon['pay_method'] = 'unlimited';
$arCoupon['pay_limit'] = 'unlimited';
$arCoupon['publish_limit'] = 'unlimited';
$arCoupon['goods_apply'] = 'all';
$arCoupon['number_type'] = 'duplication';

if($sno){
	$mode = "modify";
	$mode_title = "페이퍼쿠폰 수정<span>페이퍼쿠폰을 수정하실 수 있습니다.</span>";

	$query = "SELECT count(*) cnt FROM gd_offline_download WHERE coupon_sno='$sno'";
	list($downloadCnt) = $db->fetch($query);

	$query = "
			SELECT coupon.*,paper.number
			FROM gd_offline_coupon coupon, gd_offline_paper paper
			WHERE coupon.sno='$sno' AND coupon.sno=paper.coupon_sno
			LIMIT 1";
	list($arCoupon) = $db->_select($query);

	if($arCoupon['limit_paper']>0) $arCoupon['publish_limit']="limited";
	if($arCoupon['goods_apply'] == 'limited'){
		$query = "
				SELECT coupon.*,goods.goodsnm,options.price,goods.img_s
				FROM gd_offline_goods coupon
					LEFT JOIN gd_goods goods ON coupon.goodsno=goods.goodsno AND coupon.goodsno
					LEFT JOIN gd_goods_option options ON goods.goodsno=options.goodsno AND options.link and go_is_deleted <> '1' and go_is_display = '1'
				WHERE coupon.coupon_sno='$sno'";
		$arGoods = $db->_select($query);
	}
}else{
	$mode = "register";
	$mode_title = "페이퍼쿠폰 만들기<span>페이퍼쿠폰을 생성하실 수 있습니다.</span>";

	$arCoupon['start_year'] = date("Y");
	$arCoupon['start_mon'] = date("m");
	$arCoupon['start_day'] = date("d");
	$arCoupon['start_time'] = "00";
	$arCoupon['end_year'] = date("Y");
	$arCoupon['end_mon'] = date("m");
	$arCoupon['end_day'] = date("d");
	$arCoupon['end_time'] = "23";
	$arCoupon['coupon_type'] = "sale";
	$arCoupon['currency'] = '원';
	$arCoupon['number_type'] = "duplication";
}


$arrNumberType = array('auto'=>'다른 번호로 자동 생성','duplication'=>'동일한 번호로 자동 생성');

?>
<script type="text/javascript" src="../../lib/js/goodsBox.js"></script>
<script type="text/javascript">
function chk_number(){
	var chk = $('chk_number');
	var obj = $('make_number');
	if(chk.checked == true){
		obj.disabled = false;
	}else{
		obj.disabled = true;
	}
}
function chk_number_type(){
	var obj = $$('.number_type');
	var ly =  $$('.lp_layer');

	for(var i=0;i<obj.length;i++){
		ly[i].style.display="none";
		if(obj[i].checked == true){
			ly[i].style.display="block";
		}
	}
}
function chk_goods_apply(){
	$$('.goods_apply').each(function(chk,idx){
		$('goods_apply_id').hide();
		if(chk.checked == true && idx == 1)$('goods_apply_id').show();
	});
}
function chk_limit_amount(){
	var obj = $('limit_amount');
	$$('.pay_limit').each(function(chk,idx){
		if(chk.checked == true){
			if(idx == 0) obj.disabled=true;
			if(idx == 1) obj.disabled=false;
		}
	});
}
function fillzero(obj, len) {
	if(!obj)return '';
	var tmp = '';
	for(var i=0;i<=len;i++) tmp += '0';
	obj= tmp+obj;
	return obj.substring(obj.length-len);
}
function checkform(){
	if(!chkForm( $('coupon_frm'))) return false;
	var ret = new Array('','');
	ret.toString();
	var tmp = new Array('.startdate','.enddate');
	for(var i=0;i<2;i++){
		var obj = $$(tmp[i]+' input');
		for(var j=1;j<4;j++){
			if(obj[j].value>12 && j==1){
				alert('월 형식에 맞지 않습니다.');
				return false;
			}
			obj[j].value = fillzero(obj[j].value,2);
		}
		for(var j=0;j<4;j++) ret[i] += obj[j].value;
	}
	if(ret[0] > ret[1]){
		obj[0].focus();
		alert('유효기간이 옳바르지 않습니다.');
		return false;
	}
	return true;
}
document.observe("dom:loaded", function() {
	var frm = $('coupon_frm');
	<?if($sno):?>
		frm.setValue('mode',"modify");
		frm.setValue('sno',"<?=$sno?>");
		frm.setValue('coupon_name',"<?=$arCoupon['coupon_name']?>");
		frm.setValue('start_year',"<?=$arCoupon['start_year']?>");
		frm.setValue('start_mon',"<?=$arCoupon['start_mon']?>");
		frm.setValue('start_day',"<?=$arCoupon['start_day']?>");
		frm.setValue('start_time',"<?=$arCoupon['start_time']?>");
		frm.setValue('end_year',"<?=$arCoupon['end_year']?>");
		frm.setValue('end_mon',"<?=$arCoupon['end_mon']?>");
		frm.setValue('end_day',"<?=$arCoupon['end_day']?>");
		frm.setValue('end_time',"<?=$arCoupon['end_time']?>");
		frm.setValue('coupon_type',"<?=$arCoupon['coupon_type']?>");
		frm.setValue('coupon_price',"<?=$arCoupon['coupon_price']?>");
		frm.setValue('currency',"<?=$arCoupon['currency']?>");
		frm.setValue('pay_method',"<?=$arCoupon['pay_method']?>");
		frm.setValue('pay_limit',"<?=$arCoupon['pay_limit']?>");
		frm.setValue('limit_amount',"<?=$arCoupon['limit_amount']?>");
		frm.setValue('goods_apply', "<?=$arCoupon['goods_apply']?>");
	<?else:?>
		frm.setValue('start_year',"<?=$arCoupon['start_year']?>");
		frm.setValue('start_mon',"<?=$arCoupon['start_mon']?>");
		frm.setValue('start_day',"<?=$arCoupon['start_day']?>");
		frm.setValue('start_time',"<?=$arCoupon['start_time']?>");
		frm.setValue('end_year',"<?=$arCoupon['end_year']?>");
		frm.setValue('end_mon',"<?=$arCoupon['end_mon']?>");
		frm.setValue('end_day',"<?=$arCoupon['end_day']?>");
		frm.setValue('end_time',"<?=$arCoupon['end_time']?>");
		frm.setValue('coupon_type',"<?=$arCoupon['coupon_type']?>");
		frm.setValue('currency',"<?=$arCoupon['currency']?>");
		frm.setValue('pay_method',"<?=$arCoupon['pay_method']?>");
		frm.setValue('pay_limit',"<?=$arCoupon['pay_limit']?>");
		frm.setValue('limit_amount',"<?=$arCoupon['limit_amount']?>");
		frm.setValue('number_type', "<?=$arCoupon['number_type']?>");
		frm.setValue('goods_apply', "<?=$arCoupon['goods_apply']?>");
		frm.setValue('publish_limit', "<?=$arCoupon['publish_limit']?>");
	<?endif;?>

	<?if(!$sno):?>
	$$('.number_type').each(function(chk){chk.observe('click',chk_number_type)});
	chk_number_type();
	<?endif;?>
	$$('.pay_limit').each(function(chk){chk.observe('click',chk_limit_amount)});
	$$('.goods_apply').each(function(chk){chk.observe('click',chk_goods_apply)});
	$('coupon_frm').onsubmit = function(){return checkform();}
	chk_limit_amount();
	chk_goods_apply();


});
</script>
<style>
.pay_limit {border:0px}
.price {text-align:right}
</style>
<div class='title title_top'><?=$mode_title?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<form id="coupon_frm" method="post" action="indb.paper.php" enctype="multipart/form-data" target="ifrmHidden">

<input type=hidden name=mode value="register" />
<input type=hidden name=sno value="" />

<table class=tb>
<col class=cellC><col class=cellL style='padding:5,0,5,5'>

<tr>
	<td>페이퍼쿠폰 이름</td>
	<td><input type="text" name="coupon_name" size="40" maxlength="30" value="" required /> <span class="extext">ex) 봄상품 할인쿠폰</span></td>
</tr>

<tr>
	<td>유효기간</td>
	<td>
	<div class="startdate">
	<input type="text" name="start_year" size="5" maxlength="4" value="" option="regNum" required />년
	<input type="text" name="start_mon" size="2" maxlength="2" value="" option="regNum" required />월
	<input type="text" name="start_day" size="2" maxlength="2" value="" option="regNum" required />일&nbsp;&nbsp;
	<input type="text" name="start_time" size="2" maxlength="2" value="" option="regNum" required>시 00분 부터
	</div>
	<div class="enddate">
	<input type="text" name="end_year" size="5" maxlength="4" value="" option="regNum" required />년
	<input type="text" name="end_mon" size="2" maxlength="2" value="" option="regNum" required />월
	<input type="text" name="end_day" size="2" maxlength="2" value="" option="regNum" required />일&nbsp;&nbsp;
	<input type="text" name="end_time" size="2" maxlength="2" value="" option="regNum" required>시 59분 까지
	</div>
	</td>
</tr>

<tr>
	<td>쿠폰종류</td>
	<td class="noline">
	<label><input type="radio" name="coupon_type" value="sale" />할인쿠폰</label> <span class="extext">(구매시 바로 할인되는 쿠폰)</span>
	<label><input type="radio" name="coupon_type" value="save" />적립쿠폰</label> <span class="extext">(구매/배송완료 후에 적립되는 쿠폰)</span>
	</td>
</tr>

<tr>
	<td>할인/적립</td>
	<td>
	<input type="text" name="coupon_price" class="price" value="" option="regNum" required />
	<select name="currency">
	<option value="원" <?=$selected['currency']['원']?>>원</option>
	<option value="%" <?=$selected['currency']['%']?>>%</option>
	</select>
	을 할인/적립합니다.
	</td>
</tr>

<tr>
	<td>결제수단</td>
	<td  class="noline">
	<label><input type="radio" name="pay_method" value="unlimited" />무제한</label>
	<label><input type="radio" name="pay_method" value="cash" />현금결제(무통장입금)</label>
	<div><font class="extext">무통장 입금에서만 쿠폰 사용 가능하도록 제한하는것은 여신전문금융업법에 저촉 될 수 있습니다.</font> &nbsp;<a href="javascript:popupLayer('../event/popup.credit_financial_law.php',750,430);"><font class="extext_l">[자세히 보기]</font></a></div>
	</td>
</tr>

<tr>
	<td>결제금액</td>
	<td>
	<label><input type="radio" name="pay_limit" class="pay_limit" value="unlimited" />무제한</label>
	<label><input type="radio" name="pay_limit" class="pay_limit" value="limited" /><input type="text" name="limit_amount" id="limit_amount" class="price" size="9" maxlength="8" value="<?=($arCoupon['limit_amount']>0?$arCoupon['limit_amount']:'')?>" />원 이상 결제시 가능</label>
	</td>
</tr>

<tr>
	<td>쿠폰번호설정</td>
	<td>
	<?if($sno):?>
	<div style="padding:5px 0px 3px 0px"><?=$arrNumberType[$arCoupon['number_type']]?></div>
	<div style="padding:0px 0px 5px 0px;font-size:14pt">
	<?=($arCoupon['number_type']!='auto')?$arCoupon['number']:""?>
	</div>
	<?else:?>
	<div id="make_number">
	<label class="noline"><input type="radio" name="number_type" class="number_type" value="duplication" />동일한 번호로 자동 생성</label>
	<label class="noline"><input type="radio" name="number_type" class="number_type" value="auto" />다른 번호로 자동 생성</label>
	</div>
	<?endif;?>
	<div class="extext">발행된 쿠폰번호는 수정이 불가능 합니다.</div>
	</td>
</tr>

<tr>
	<td>발행수량</td>
	<td>
	<?if($sno):?>
	<?if($arCoupon['publish_limit']=='unlimited'):?>
	무한대
	<?else:?>
	<?=number_format($arCoupon['limit_paper'])?> 매 발행 <span style="padding-top:3px">
	<?if($arCoupon['number_type']=='auto'):?>
	<a href="paper_print.php?sno=<?=$sno?>" class="extext">[발행내역 다운로드]</a></span>
	<?endif;?>
	<?endif;?>
	<?else:?>
	<div class="lp_layer">
	<label class="noline"><input type="radio" name="publish_limit" value="unlimited" />무한대</label>
	<label class="noline"><input type="radio" name="publish_limit" value="limited" /></label><input type="text" name="duplication_limit_paper" size="9" maxlength="8" value="<?=($arCoupon['limit_paper']>0?$arCoupon['limit_paper']:'')?>" option="regNum" />매 발행
	</div>
	<div class="lp_layer" style="display:none;">
	<div><input type="text" name="auto_limit_paper" size="9" maxlength="8" value="" option="regNum" />매 발행</div>
	<?endif;?>
	</div>
	</td>
</tr>

<tr>
	<td>적용상품</td>
	<td>
	<label class="noline"><input type="radio" name="goods_apply" class="goods_apply"  value="all" />전체상품에 적용</label><br>
	<label class="noline"><input type="radio" name="goods_apply" class="goods_apply" value="limited" />특정 카테고리 및 상품선정</label>
	<div style="margin-left:20px;"><span class="extext">※ 특정상품 및 특정카테고리의 상품을 구매할 때 쿠폰을 사용할 수 있습니다.</span></div>
	<div style="margin:0 0 10px 20px;"><span class="extext">※ 해당 상품이 포함된 주문건에 대하여 적용 됩니다.</span></div>

	<div id="goods_apply_id">ㆍ카테고리 선정 (카테고리선택 후 오른쪽 선정버튼클릭)
	<div style="padding:5px 0px 0px 0px"><script>new categoryBox('cate[]',4,'','');</script>
	<a href="javascript:exec_add()"><img src="../img/btn_coupon_cate.gif"></a></div>
	<div class="box" style="padding:10 0 10 10">
	<table cellpadding="8" cellspacing=0 id="objCategory" bgcolor="f3f3f3" border="0" bordercolor="#cccccc" style="border-collapse:collapse">
	<?
	if ($arGoods) foreach ($arGoods as $v):
		if($v['category']):
	?>
	<tr>
		<td id="currPosition"><?=strip_tags(currPosition($v[category]))?></td>
		<td><input type="text" name="e_category[]" value="<?=$v[category]?>" style="display:none" />
		<td><a href="javascript:void(0)" onClick="cate_del(this.parentNode.parentNode)"><img src="../img/i_del.gif" border=0 align=absmiddle></a>
		</td>
	</tr>
	<?
		endif;
	endforeach;
	?>
	</table>
	</div>

	<div style="padding:5px 0px 0px 0px">ㆍ상품 선정 (상품검색 후 선정)
		<div id="divgoods" style="position:relative;z-index:99;padding-left:8">
			<div style="padding:5px 0px 0px 0px;"><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_goods[]', 'goodsX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 등록(수정)버튼을 누르셔야 최종 저장이 됩니다.</font></div>
			<div id="goodsX" style="padding-top:3px;">
				<?php
					if ($arGoods) {
						foreach ($arGoods as $v){
							if($v['goodsno']){
				?>
					<a href="../../goods/goods_view.php?goodsno=<?php echo $v['goodsno']; ?>" target="_blank"><?php echo goodsimg($v['img_s'], '40,40', '', 1); ?></a>
					<input type=hidden name="e_goods[]" value="<?php echo $v['goodsno']; ?>" />
				<?php
							}
						}
					}
				?>
			</div>
		</div>
	</div>

	</div>
	</td>
</tr>
</table>

<div class="button">
<?if($downloadCnt==0):?>
<input type="image" src="../img/btn_<?=$mode?>.gif">
<?else:?>
<div class="red" align="center">이미 사용된 쿠폰이 있는 경우에는 쿠폰을 수정하실 수 없습니다.</div>
<?endif;?>
</div>
</form>


<div style="padding-top:10px"></div>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">페이퍼쿠폰을 지급받은 회원의 쿠폰사용 방법</td></tr>
<tr><td style="padding-left:14px">① 쇼핑몰內 마이페이지에서 페이퍼쿠폰번호를 입력하여 쿠폰인증을 받습니다.</td></tr>
<tr><td style="padding-left:14px">② 주문페이지에서 페이퍼쿠폰은 사용합니다.</td></tr>
<tr><td style="padding-top:10px"><img src="../img/icon_list.gif" align="absmiddle">페이퍼쿠폰은 1인 1회에 한하여 사용 가능합니다.</td></tr>
<tr><td style="padding-left:14px">예) 페이퍼쿠폰 1,000장(A그룹) 인쇄 a 1,000장(A그룹)중 2장을 받은 ‘홍길동’ 회원은 1장만 사용 가능.</td></tr>
<tr><td style="padding-left:14px">예) 페이퍼쿠폰 2,000장(B그룹) 인쇄 a 2,000장(B그룹)중 2장을 받은 ‘홍길동’ 회원은 1장만 사용 가능.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">페이퍼쿠폰을 “다른 쿠폰번호”형식으로 만들 경우 최대 10,000장까지 생성할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<? include "../_footer.php"; ?>