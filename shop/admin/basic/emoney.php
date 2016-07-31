<?

$location = "기본관리 > 적립금 설정";
include "../_header.php";
include "../../conf/config.pay.php";

$set = $set['emoney'];

if(!$set[limit])$set[limit] = 0;

$k_max = (strpos($set['max'],'%')!==false) ? 1 : 0;

$checked['useyn'][$set[useyn]] = "checked";
$checked['k_max'][$k_max] = "checked";
$checked['limit'][$set[limit]] = "checked";
$max[$k_max] = $set['max'];

if(!$set['emoney_delivery']) $set['emoney_delivery'] = 0;
$checked['emoney_delivery'][$set['emoney_delivery']] = "checked";

if(!$set['chk_goods_emoney']) $set['chk_goods_emoney'] = 0;
$checked['chk_goods_emoney'][$set['chk_goods_emoney']] = "checked";

if(!$set['emoney_use_range'])$set['emoney_use_range'] = 0;
$selected['emoney_use_range'][$set['emoney_use_range']] = "selected";

if(!$set['emoney_standard']) $set['emoney_standard'] = 0;
$checked['emoney_standard'][$set['emoney_standard']] = "checked";

if(!$set['useduplicate']) $set['useduplicate'] = 0;
$checked['useduplicate'][$set['useduplicate']] = "checked";

if($set['chk_goods_emoney'] == 0){
	$emoney_standard_display = " style='display:block' ";
}else {
	$emoney_standard_display = " style='display:none' ";
}

if (trim($set['cut']) === '') $set['cut'] = '2';
$selected['cut'][$set['cut']] = 'selected';
?>
<script language=javascript src="/shop/admin/common.js"></script>
<script language="javascript">
function chkGoodsEmoney(){
	var obj = document.getElementsByName('chk_goods_emoney');
	var txt = document.getElementsByName('goods_emoney[]');
	for(var i=0;i<obj.length;i++){
		if(obj[i].checked == true){
			txt[i].style.background = "#ffffff";
			txt[i].readOnly = false;
		}else{
			txt[i].style.background = "#e3e3e3";
			txt[i].readOnly = true;
			txt[i].value = '';
		}
	}
	var es_div = document.getElementById('es_div');
	if(obj[0].checked == true)es_div.style.display = "block";
	if(obj[1].checked == true)es_div.style.display = "none";
}
</script>
<form method=post action="indb.php" onsubmit="return chkForm(this);">
<input type=hidden name=mode value="emoney">

<div class="title title_top">적립금 지급에 대한 정책<span>회원에게 지급되는 적립금에 대한 정책입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>적립금 지급여부</td>
	<td class=noline>
	<input type=radio name=useyn value='y' <?=$checked['useyn']['y']?>> 사용
	<input type=radio name=useyn value='n' <?=$checked['useyn']['n']?>> 사용안함
	</td>
</tr>
<tr>
	<td>적립금 사용시<br>상품적립금 지급여부</td>
	<td class=noline height=50>
		<div><input type=radio name='limit' value='0' <?=$checked['limit'][0]?>> 적립금을 사용해도 구매하려는 상품의 적립금을 그대로 지급합니다.</div>
		<div><input type=radio name='limit' value='1' <?=$checked['limit'][1]?>> 적립금을 사용하면 구매하려는 상품의 적립금을 지급하지 않습니다.</div>
		<div class="extext_t">* 회원이 적립금으로 결제하려 할 때 구매할 상품의 적립금도 적립해 줄 것인지를 선택하는 항목입니다. <br>
예를 들어, 가격이 10,000원인 상품(구매하면 100원 적립)을 어떤 회원이 적립금 5,000원을 이용해서 이 상품을 구매하려 한다면, 그 회원에게 100원의 적립을 해줄것인가 정하는 정책입니다. <br>
* 참고로, 회원의 적립금은 구매 또는 기타 혜택으로 받은 현금성 포인트이므로, 현금과 동일하게 대우하는 것이 좋습니다.
</div>

	</td>
</tr>
</table>
<br>
<br>
<div class="title title_top">상품 적립금 지급에 대한 정책<span>회원에게 지급되는 적립금에 대한 정책입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>상품적립금 기본설정</td>
	<td>
	<div style='height:25;padding-top:2'><input type="radio" name="chk_goods_emoney" value="0" class="null" onclick="chkGoodsEmoney()" <?=$checked[chk_goods_emoney][0]?>>주문 상품금액의 <input type="text" name="goods_emoney[]" style="text-align:right" value="<?=$set['goods_emoney']?>" size=2 label="상품적립금" onkeydown="onlynumber()" readonly class=rline> %를 배송완료 시 적립합니다.</div>
	<div style='height:25;padding-top:2'><input type="radio" name="chk_goods_emoney" value="1" class="null" onclick="chkGoodsEmoney()" <?=$checked[chk_goods_emoney][1]?>>주문 상품 당 <input type="text" name="goods_emoney[]" style="text-align:right" value="<?=$set['goods_emoney']?>" size=7 label="상품적립금" onkeydown="onlynumber()" readonly class=rline> 원을  배송완료 시 적립합니다.</div>
	<div style="padding-top:3"><font class=extext>* 상품등록시 상품마다 각각 개별적인 적립금을 입력할 수도 있습니다.</font>
	</td>
</tr>
</table>
<br>
<div id="es_div" <?=$emoney_standard_display?>>
<table class=tb>
<col class=cellC><col class=cellL>
<tr height=50>
	<td>적립금 적립기준</td>
	<td>
	<div><input type=radio name="emoney_standard" value="0" class=null <?=$checked['emoney_standard'][0]?>> 상품 판매금액
	<input type=radio name="emoney_standard" value="1" class=null <?=$checked['emoney_standard'][1]?>> 총 결제금액</div>
<div class="extext_t">* 적립금 적립기준을 "상품 판매금액"으로 선택 시 적립금/쿠폰/배송비 적용 전 상품 판매금액를 기준으로 적립금이 적립됩니다.<br>
* 적립금 적립기준을 "총 결제금액"으로 선택 시 적립금/쿠폰/배송비 적용 후 구매자가 실제 결제한 총 금액을 기준으로 적립금이 적립됩니다.</div>
	</td>
</tr>
</table>
</div>
<br>
<div class="title title_top">적립금 사용에 대한 정책<span>회원에게 지급되는 적립금에 대한 정책입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>상품 주문합계액 기준</td>
	<td>
	상품 주문 합계액이 <input type=text name="totallimit" value="<?=$set['totallimit']?>" size=10 option="regNum" label="상품주문합계액" onkeydown="onlynumber()" class=rline> 원 이상일때 사용 가능합니다. <span class=extext>(상품 주문액을 기준으로 적립금 사용 제한을 설정합니다.)</span></td>
</tr>
<tr height=30>
	<td>사용가능한 적립금<br />보유금액</td>
	<td>
	보유 적립금이 <input type=text name="hold" value="<?=$set['hold']?>" size=10 option="regNum" label="적립금사용가능액" onkeydown="onlynumber()" class=rline> 원 이상일때 사용 가능합니다.</div></td>
</tr>
<tr height=50>
	<td>적립금 최소 사용금액</td>
	<td>
	적립금은 최소  <input type=text name=min value="<?=$set['min']?>" size=10 option="regNum" label="최소한도액" onkeydown="onlynumber()" class=rline> 원 이상부터 사용할 수 있습니다. <span class=extext>(숫자입력)</span></td>
</tr>

<tr height=50>
	<td>적립금 최대 사용금액</td>
	<td>
	<input type=radio name=k_max value=0 class=null <?=$checked['k_max'][0]?>> 결제시  최대 <input type=text name=max[] size=10 value="<?=$max[0]?>" option="regNum" label="적립금사용한도" onkeydown="onlynumber()" class=rline> 원까지 적립금을 사용할 수 있습니다.<span class=extext> (숫자입력)</span><br>
	<input type=radio name=k_max value=1 class=null <?=$checked['k_max'][1]?>> 결제시 최대 <select name="emoney_use_range"><option value="0" <?=$selected['emoney_use_range'][0]?>>상  품  합  계</option><option value="1" <?=$selected['emoney_use_range'][1]?>>상품합계+배송비</option></select>의 <input type=text name=max[] size=3 value="<?=substr($max[1],0,-1)?>" option="regNum" label="적립금사용한도" onkeydown="onlynumber()" class=rline> % 까지 적립금을 사용할 수 있습니다. <span class=extext>(숫자입력)</span>
<div class="extext_t">* 적립금 결제시 최소 사용금액과 최대 사용금액을 정합니다. 적립금으로 전액을 결제하게 하려면 100%로 입력하세요. <br>
* 최대한도액을 %로 할 경우 최소한도액과의 상관관계를 고려하여 신중하게 설정하세요. <br>
예) 적립금 결제 최소한도액을 10,000원으로 하고 최대한도액을 상품가격의 40%로 설정했을 때, 구매할 상품이 20,000원이라면 적립금으로 결제할 수 있는 최대한도액(40%)은 8,000원이 됩니다. <br>
이 경우 최소한도액(10,000원)보다 최대한도액(8,000원)이 적게 되므로 고객은 적립금을 사용할 수 없는 상황이 발생됩니다. <br>
따라서 고객에게 오해의 소지가 없도록 최소한도액과 최대한도액의 상관관계를 고려하여 설정하시기 바랍니다. </div>
	</td>
</tr>

<tr height=50>
	<td>적립금 사용기준</td>
	<td>
	<div><input type=radio name="emoney_delivery" value="0" class=null <?=$checked['emoney_delivery'][0]?>> 적립금으로 주문시 결제금액에 적립금 주문금액 포함</div>
	<div><input type=radio name="emoney_delivery" value="1" class=null <?=$checked['emoney_delivery'][1]?>> 적립금으로 주문시 결제금액에 적립금 주문금액 비포함</div>
<div class="extext_t">* 적립금사용기준을 "적립금으로 주문시 결제금액에 적립금 주문금액 비포함"으로 선택 시 배송비 계산시 적립금은 결제금액에 포함되지 않습니다. <br>
예) 배송비 정책을 결제금액 50,000원이상 무료, 미만일 경우에는 2,500원의 배송비부과로 설정하였고, 적립금사용기준을 적립금으로 주문시 결제금액에 적립금 주문금액 비포함으로 설정하였고, 주문 총구매금액이 51,000원이고 적립금 2,000원을 사용하였을 경우
실결제금액은 49,000원이며 결제금액에 적립금이 비포함되므로 배송비가 부과됩니다.</div>
	</td>
</tr>
</table>

<div class=title>적립금/쿠폰 중복사용 설정<span>상품 주문시에 적립금과 쿠폰 중복사용 여부를 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>쿠폰 적용시<br>적립금 중복 사용</td>
	<td class=noline>
	<div><label><input type="radio" name="useduplicate" value="1" <?=$checked['useduplicate'][1]?>> 쿠폰 적용시 적립금 중복사용 가능</label></div>
	<div><label><input type="radio" name="useduplicate" value="0" <?=$checked['useduplicate'][0]?>> 쿠폰 적용시 적립금 사용 불가</label></div>
	</td>
</tr>
</table>

<div class=title>금액절사관리<span>금액절사관리는 적립금, 할인쿠폰의 적용으로 발생되는 결제금액 끝자리 단위를 관리하기 위함입니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=4')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>절사기준 설정</td>
	<td>
		<select name="cut">
			<option value="0">절사안함</option>
			<option value="1" <?php echo $selected['cut']['1']; ?>>1원 단위 절사</option>
			<option value="2" <?php echo $selected['cut']['2']; ?>>10원 단위 절사</option>
			<option value="3" <?php echo $selected['cut']['3']; ?>>100원 단위 절사</option>
			<option value="4" <?php echo $selected['cut']['4']; ?>>1000원 단위 절사</option>
		</select>
		<p class="extext">
			판매금액의 %단위로 적립금 적립시 발생하는 1원단위 및 10원단위 금액을 절사하여 적용합니다.<br/>
			Ex) 판매금액 1,700원의 7% 적립 ? 적립금 119원 발생<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;=> 1원 단위 절사시110원 적립금 적용 / 10원 단위 절사시 100원 적립금 적용<br/>
		</p>
		<p class="extext" style="color: #ff0000;">
			※ 절사는 적립금과 할인쿠폰 금액을 %로 설정시에만 적용 됩니다.
		</p>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">적립금정책의 <font class=small_ex_point>사용가능한 금액, 사용한도설정</font>은 <font class=small_ex_point>'이용안내페이지'</font>에 고지하시기 바랍니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=small_ex_point>금액절삭관리</font>는 적립금, 할인쿠폰의 적용으로 발생되는 결제금액 끝자리 단위를 관리하기 위함입니다.</td></tr></table>
</div>
<script>cssRound('MSG03');chkGoodsEmoney();</script>


<? include "../_footer.php"; ?>