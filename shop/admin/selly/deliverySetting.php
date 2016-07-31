<?
/*********************************************************
* 파일명     :  deliverySetting.php
* 프로그램명 :  연동상품 배송비 설정
* 작성자     :  이훈
* 생성일     :  2012.05.31
**********************************************************/
/*********************************************************
* 수정일     :  
* 수정내용   :  
**********************************************************/
$location = "셀리 > 연동상품 배송비 설정";
include "../_header.php";

$deilvery_type = Array(
	'1' => '무료',
	'2' => '선결제가능착불',
	'3' => '착불만가능',
	'4' => '선결제만가능'
);

$query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s]', 'selly');
$delivery_data = $db->_select($query);

if($delivery_data) {
	foreach($delivery_data as $data) {
		if($data['name'] == 'basic_payment_delivery_price') {
			$arr_data[$data['name']] = $data['value'];
		}
		else {
			$selected[$data['name']][$data['value']] = 'selected';
		}
	}
}
?>

<script>

function submitForm() {
	var check = formCheck();
	if(check) {
		alert(check);
		return;
	}

	var fm = document.frmList;
	fm.method = "POST";
	fm.action = "../selly/indb.php";
	fm.submit();
}

function formCheck() {
	if(!document.getElementsByName('fixe_delivery')[0].value) return '고정배송비값이 없습니다.';//고정배송비
	if(!document.getElementsByName('cnt_delivery')[0].value) return '수량별배송비값이 없습니다.';//수량별배송비
	if(!document.getElementsByName('payment_delivery')[0].value) return '착불배송비값이 없습니다.';//착불배송비
	if(!document.getElementsByName('basic_advence_delivery')[0].value) return '기본배송정책(선불) 값이 없습니다.';//기본배송정책_선불
	if(!document.getElementsByName('basic_payment_delivery')[0].value) return '기본배송정책(착불) 값이 없습니다.';//기본배송정책_착불 타입
	if(!document.getElementsByName('basic_payment_delivery_price')[0].value) return '기본배송정책(착불 배송비) 값이 없습니다.';//기본배송정책_착불 배송비
}

</script>

<div class="title title_top">연동상품 배송비 설정<span>SELLY에 연동하기 위한 배송비를 설정하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=5')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<div style="padding-top:15px"></div>

<form name="frmList" action="../selly/indb.php">
	<input type="hidden" name="mode" value="basic_delivery">
	<table class="tb">
		<col class="cellC" style="width:130px;"><col class="cellC" style="width:120px;"><col class="cellL">
		<tr>
			<td rowspan="4">상품별 배송비</td>
			<td style="height=40px;">무료배송</td>
			<td>SELLY에 상품등록시 배송비가 무료로 설정됩니다.</td>
		</tr>
		<tr>
			<td style="height=40px;">고정배송비</td>
			<td>
				<span>
					배송타입 : 
					<select name="fixe_delivery">
						<option value="">선택하세요</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['fixe_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					배송비 : 상품에 입력된 배송비로 등록됩니다.
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=40px;">수량별배송비</td>
			<td>
				<span>
					배송타입 : 
					<select name="cnt_delivery">
						<option value="">선택하세요</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['cnt_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					배송비 : 상품에 입력된 배송비로 등록됩니다.
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=40px;">착불배송비</td>
			<td>
				<span>
					배송타입 : 
					<select name="payment_delivery">
						<option value="">선택하세요</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '4' || $key == '1') continue; ?>
						<option value="<?=$key?>" <?=$selected['payment_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					배송비 : 상품에 입력된 배송비로 등록됩니다.
				 </span>
			</td>
		</tr>
		<tr>
			<td rowspan="3">기본배송비정책</td>
		</tr>
		<tr>
			<td style="height=65px;">선불</td>
			<td>
				<span>
					배송타입 : 
					<select name="basic_advence_delivery">
						<option value="">선택하세요</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1' || $key == '3') continue;?>
						<option value="<?=$key?>" <?=$selected['basic_advence_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					배송비 : 기본배송정책에 설정됨 배송비로 등록됩니다.
					<div class="extext" style="margin-top:8px;">* 무료조건 이상인 판매가를 가진 상품은 무료로 등록됩니다.</div>
				 </span>
			</td>
		</tr>
		<tr>
			<td style="height=65px;">착불</td>
			<td>
				<span>
					배송타입 : 
					<select name="basic_payment_delivery">
						<option value="">선택하세요</option>
					<? foreach($deilvery_type as $key => $val) { ?>
						<? if($key == '1' || $key == '4') continue;?>
						<option value="<?=$key?>" <?=$selected['basic_payment_delivery'][$key]?>><?=$val?></option>
					<? } ?>
					</select>
				</span>
				<span style="margin-left:10px;">
					배송비 : <input type="text" name="basic_payment_delivery_price" value="<?=$arr_data['basic_payment_delivery_price']?>" class="line" style="height:22px" onkeydown="onlynumber();" />
					<div class="extext" style="margin-top:8px;">* 무료조건 이상인 판매가를 가진 상품은 무료로 등록됩니다.</div>
				</span>
			</td>
		</tr>
	</table>
	<div class="button_top">
		<input type="image" src="../img/btn_register.gif" alt="등록" onclick="submitForm();return false;" />
	</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
SELLY에 연동되는 상품의 배송비를 설정하실 수 있습니다.<br/>
SELLY에서 사용되는 배송비는 무료, 선결제가능착불, 착불만가능, 선결제만가능이 있으며<br/>
e나무 배송비 종류에 따라 선택할 수 있는 값이 달라지게 됩니다.<br/><br/><br/>

연동상품 배송비 설정을 안하실 경우 e나무 배송비가 무료가 아닌 상품은 마켓으로 링크하실 수 없습니다.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>