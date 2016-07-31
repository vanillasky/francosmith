<?
$location = "컴백 쿠폰/SMS > 컴백 쿠폰/SMS 등록";
include "../_header.php";

$couponyn = array('y'=>'사용','n'=>'사용안함');
$smsyn = array('y'=>'사용','n'=>'사용안함');
$step = array('orddt'=>'주문일','confirmdt'=>'배송완료일');
$msg = "(광고)\n[{shopName}]\n구매하신 상품은 마음에 드셨나요? 특별한 고객님만을 위한 컴백 할인 쿠폰 발급!지금 바로 확인하세요![모바일샵 링크]";
$checked['linkyn']['y'] = "checked";
$checked['sms_type']['lms'] = "checked";

if ($_GET['sno']) {
	$query = "SELECT * FROM ".GD_COMEBACK_COUPON." WHERE sno = '[i]'";
	$comeback_query = $db->_query_print($query,$_GET['sno']);
	$data = $db->fetch($comeback_query);

	if (!$data) {
		msg('등록된 컴백 쿠폰/SMS가 없습니다.',-1);
		exit;
	}

	$checked['type'][$data['type']] = "checked";
	$checked['couponyn'][$data['couponyn']] = "checked";
	$checked['smsyn'][$data['smsyn']] = "checked";
	$checked['linkyn'][$data['linkyn']] = "checked";
	$checked['sms_type'][$data['sms_type']] = "checked";
	$selected['step'][$data['type']][$data['step']] = "selected";
	$selected['couponcd'][$data['couponcd']] = "selected";
	$value['date'][$data['type']] = $data['date'];
	$price_arr = explode(',',$data['price']);
	$msg = $data['msg'] ? $data['msg'] : $msg;

	$loop = array();
	if ($data['goodsno']) {
		$goods_query = $db->query("SELECT goodsno, goodsnm, img_s FROM ".GD_GOODS." WHERE goodsno IN (".$data['goodsno'].")");
		while($d = $db->fetch($goods_query)) $loop[] = $d;
	}
}

// 운영자 발급 쿠폰
$query = "SELECT couponcd,coupon FROM ".GD_COUPON." WHERE coupontype = '0' AND IF((edate AND DATE_FORMAT(edate,'%Y%m%d%H%i%s') >= [i]) OR (priodtype = 1 AND edate = ''),'y','n') = 'y' ORDER BY couponcd";
$coupon_query = $db->_query_print($query,date('YmdHis'));
$res = $db->query($coupon_query);
?>
<style>
.display_none {display: none;}
.display_table_row {display: table-row;*display: inline-block;_display: inline-block;}
#sms_top {width: 146px; height: 56px; background: url('../img/sms_top.gif') no-repeat top left; text-align: right;}
#img_special {margin-right: 15px; margin-bottom: 5px;}
#td_lms_subject {background: url(../img/sms_subject_bg.gif) repeat-y; width: 146px; height: 38px; text-align: center;}
#lms_subject {font: 9pt 굴림체; overflow: hidden; border: 0; width: 98px; height: 31px; background: url(../img/long_message01.gif) repeat-y;}
.td_sms_msg {background: url(../img/sms_bg.gif) repeat-y; padding-top: 8px; width: 146px; height: 125px; text-align: center;}
.td_lms_msg {background: url(../img/sms_long_bg.gif) repeat-y; padding-top: 8px; width: 146px; height: 170px; text-align: center;}
.area_sms_msg {font: 9pt 굴림체; overflow: hidden; border: 0; width: 98px; height: 110px; background: url(../img/short_message01.gif) no-repeat;}
.area_lms_msg {font: 9pt 굴림체; overflow: hidden; border: 0; width: 98px; height: 150px; background: url(../img/long_message02.gif) no-repeat;}
#msg_byte {width: 26px; text-align: right; border: 0; font-size: 8pt; font-style: verdana;}
#td_point {text-align: center; font-size: 8pt; font-style: verdana;}
#div_cart_product {padding-top: 5px;}
.msg_sms_alert {height: 100px;}
.msg_sms_alert h4 {color: #ff2222;}
.msg_sms_alert a {color: #0000ff;}
.msg_sms_alert a:hover {text-decoration: underline;}
.msg_sms_code{height: 200px;}
</style>
<script type="text/javascript">
function coupon_submit(f){
	if (!f.title.value) {
		alert('이름을 입력해주세요.');
		f.title.focus();
		return false;
	}
	if ($$("input:checked[name='type']").length == '0') {
		alert('대상을 선택해주세요.');
		f.type.focus();
		return false;
	} else {
		if (!$$("input[name='date["+$$("input:checked[name='type']")[0].value+"]']")[0].value) {
			alert('날짜를 입력해주세요.');
			$$("input[name='date["+$$("input:checked[name='type']")[0].value+"]']")[0].focus();
			return false;
		}
		if ($$("input:checked[name='type']")[0].value == '2') {
			if ($$("input:[name='e_step[]']").length == '0') {
				alert('특정상품을 선택해주세요.');
				return false;
			}
		}
	}
	if (($$("input:checked[name='couponyn']").length == '0' && $$("input:checked[name='smsyn']").length == '0') || ($$("input:checked[name='couponyn']")[0].value == 'n' && $$("input:checked[name='smsyn']")[0].value == 'n')) {
		alert("'쿠폰발급'이나 'SMS발송'중 반드시 하나는 사용으로 체크 후 저장하시기 바랍니다.");
		return false;
	}
	if ($$("input:checked[name='couponyn']")[0].value == 'y' && !document.getElementsByName('couponcd')[0].value) {
		alert('쿠폰을 선택해주세요.');
		document.getElementsByName('couponcd')[0].focus();
		return false;
	}
	if ($$("input:checked[name='smsyn']")[0].value == 'y' && !$('msg').value) {
		alert('발송내역을 입력해주세요.');
		return false;
	}
}
</script>

<div class="title title_top">컴백 쿠폰/SMS 등록 &nbsp; &nbsp; 
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<form name="form" method="post" action="./comeback_coupon_indb.php" onsubmit="return coupon_submit(this)">
<input type="hidden" name="mode" value="insert">
<input type="hidden" name="sno" value="<?=$data['sno']?>">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>이름</td>
	<td><input type="text" name="title" size="40" value="<?=$data['title']?>" required></td>
</tr>
<tr>
	<td>대상 선택</td>
	<td>
	<input type="radio" name="type" value="1" onclick="detail_view(1)" <?=$checked['type'][1]?> required> 마지막 
	<select name="step[1]">
	<? foreach ($step as $k => $v) { ?>
	<option value="<?=$k?>" <?=$selected['step'][1][$k]?>><?=$v?></option>
	<? } ?>
	</select>
	로부터 <input type="text" name="date[1]" size="3" onkeydown="onlynumber()" class="ar" value="<?=$value['date'][1]?>">일이 지난 회원<br />
	<div id="type1_area" style="display:none; padding:3px 25px;">결제금액이 <input type="text" name="price[]" onkeydown="onlynumber()" size="15" class="ar" value="<?=$price_arr[0]?>">원 ~ <input type="text" name="price[]" onkeydown="onlynumber()" size="15" class="ar" value="<?=$price_arr[1]?>">원인 회원만 선택 <span class="extext">(공란으로 두면 결제금액에 상관없이 대상이 선택됩니다.)</span></div>

	<input type="radio" name="type" value="2" onclick="detail_view(2)" <?=$checked['type'][2]?> required> 특정 상품의  
	<select name="step[2]">
	<? foreach ($step as $k => $v) { ?>
	<option value="<?=$k?>" <?=$selected['step'][2][$k]?>><?=$v?></option>
	<? } ?>
	</select>
	로부터 <input type="text" name="date[2]" size="3" onkeydown="onlynumber()" class="ar" value="<?=$value['date'][2]?>">일이 지난 회원

	<div id="type2_area" style="display:none; padding:5px 0 0 20px;">
		<div><img src="../img/btn_goodsChoice.gif" class="hand" onclick="javascript:popupGoodschoice('e_step[]', 'stepX');" align="absmiddle" /> <font class="extext">※주의: 상품선택 후 반드시 하단 저장버튼을 누르셔야 최종 저장이 됩니다.</font></div>
		<div style="position:relative;z-index:1000;">
			<div id=stepX style="padding-top:3px">
			<? if ($loop){ foreach ($loop as $v){ ?>
			<input type=hidden name=e_step[] value="<?=$v[goodsno]?>">
			<a href="../../goods/goods_view.php?goodsno=<?=$v[goodsno]?>" target=_blank><?=goodsimg($v[img_s], '40,40', '', 1)?></a>
			<? }} ?>
			</div>
		</div>
	</div>
	</td>
</tr>
<tr>
	<td>쿠폰 발급</td>
	<td>
	<? foreach ($couponyn as $k => $v) { ?>
	<label><input type="radio" name="couponyn" value="<?=$k?>" onclick="view('coupon','<?=$k?>')" <?=$checked['couponyn'][$k]?> required><?=$v?> </label>
	<? } ?>
	</td>
</tr>
<tr class="coupon_tr" style="display:none;">
	<td>쿠폰 선택</td>
	<td>
		<select name="couponcd">
		<option value="">쿠폰 선택</option>
		<? while($coupon = $db->fetch($res)) { ?>
		<option value="<?=$coupon['couponcd']?>" <?=$selected['couponcd'][$coupon['couponcd']]?>><?=$coupon['coupon']?></option>
		<? } ?>
		</select><br />
		<span class="extext">
		등록된 운영자 발급 쿠폰 중에서 적용기간이 종료되지 않은 쿠폰만 선택할 수 있습니다.<br />
		선택할 쿠폰이 없는 경우 쿠폰만들기에서 “운영자발급” 쿠폰을 새로 만드시기 바랍니다. <a href="./coupon_register.php" target="_blank"><b>[쿠폰만들기 페이지 바로가기]</b></a>
		</span>
	</td>
</tr>
<tr>
	<td>SMS 발송</td>
	<td>
	<? foreach ($smsyn as $k => $v) { ?>
	<label><input type="radio" name="smsyn" value="<?=$k?>" onclick="view('sms','<?=$k?>')" <?=$checked['smsyn'][$k]?> required><?=$v?> </label>
	<? } ?>
	</td>
</tr>
<tr class="sms_tr" style="display:none;">
	<td>발송 내용</td>
	<td>
		<table>
		<tr>
			<td>
			<table width="146px" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td height="30px">
					<table width="146px" cellpadding="0" cellspacing="0" border="0">
					<tr class="noline">
						<td width="73px" align="right"><input type="radio" name="sms_type" value="sms" checked="checked" style="visibility: hidden;"><img id="img_sms_title" src="../img/btn_sms_on.gif" /></td>
						<td width="73px" align="left"><input type="radio" name="sms_type" value="lms" style="visibility: hidden;"><img id="img_lms_title" src="../img/btn_lms_off.gif" /></td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td id="sms_top"><a href="javascript:openLayer('special');" onfocus="blur();"><img src="../img/btn_smstext.gif" id="img_special" /></a></td>
			</tr>
			<tr id="tr_lms_subject" style="display:none;">
				<td id="td_lms_subject"><textarea name="lms_subject" id="lms_subject" onkeydown="chkLength(this,'s');" onkeyup="chkLength(this,'s');" onchange="chkLength(this,'s');" onFocus="clearBg(this);" disabled><?=$data['lms_subject']?></textarea></td>
			</tr>
			<tr>
				<td id="td_msg" class="td_sms_msg"><textarea name="sms_msg" id="msg" class="area_sms_msg" onkeydown="chkLength(this,'m');" onkeyup="chkLength(this,'m');" onchange="chkLength(this,'m');" onFocus="clearBg(this);" msgR="메세지를 입력해주세요"><?=$msg?></textarea></td>
			</tr>
			<tr><td height="31px" background="../img/sms_bottom.gif" align="center"><input name="vLength" type="text" id="msg_byte" value="0">/<font class="ver8" color="#262626" id="byte_limit">90 Bytes</font></td></tr>
			<tr>
				<td id="td_point">SMS - 1포인트 차감</td>
			</tr>
			<tr>
				<td align="center">
				<? if ($is_sms_selector === true)  { ?>
				<input type="image" onClick="fnSetSmsMessage();return false;" src="../img/btn_smsreg.gif" class="null" />

				<? } else { ?>
				<input type="image" src="../img/btn_smssend.gif" class="null" />
				<? } ?>

				</td>
			</tr>
			<tr id="tr_lms_alert" style="display:none;">
				<td style="padding: 5px 10px;width:126px; height:100px;" class="extext">※ 특수문자의 경우 제목에는 입력할 수 없으며, 내용에 입력하는 경우 통신사 정책에 의해 발송이 거절될 수 있습니다.</td>
			</tr>
			</table>
			</td>
			<td>
				<div class="msg_sms_alert">
					<h4>※ 정통망법에 따른 광고성 정보 전송 준수사항을 꼭 확인해주세요.</h4>
					<a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1237&page=2" target="_blank">[정통망법에 따른 광고성 정보 전송 관련 필수 준수사항 안내 바로가기]</a>
				</div>
				<div class="msg_sms_code">
					<h4>※ SMS 자동발송 문구에 사용되는 치환코드 안내</h4>
					{shopName} : 쇼핑몰명
				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr class="sms_tr" style="display:none;">
	<td>모바일샵 링크</td>
	<td>
	<? foreach ($smsyn as $k => $v) { ?>
	<label><input type="radio" name="linkyn" value="<?=$k?>" onclick="add_mobile_link('<?=$k?>')" <?=$checked['linkyn'][$k]?>><?=$v?> </label>
	<? } ?><br />
	<span class="extext">*모바일샵 링크는 URL의 원활한 전송을 위하여 LMS만 사용이 가능합니다.</span>
	</td>
</tr>
</table>
<div class="button">
	<?if ($data['sendyn'] != 'y') {?><input type="image" src="../img/btn_save.gif" /><? } ?>
	<a href="./comeback_coupon_list.php"><img src="../img/btn_cancel.gif"></a>
</div>
</form>

<script type="text/javascript">
function view(type,value){
	if (value == 'y') {
		$$('.'+type+'_tr').each(function(e){
			e.setStyle({display:''});
		});
	} else {
		$$('.'+type+'_tr').each(function(e){
			e.setStyle({display:'none'});
		});
	}
}

function detail_view(v){
	if (v == '1') {
		$('type1_area').setStyle({display:''});
		$('type2_area').setStyle({display:'none'});
	} else if (v == '2') {
		$('type1_area').setStyle({display:'none'});
		$('type2_area').setStyle({display:''});
	}
}

function add_mobile_link(v){
	var msg = $('msg').value.split('[모바일샵 링크]');
	if (v == 'y') {
		$('msg').value = msg[0] + "[모바일샵 링크]";
	} else {
		$('msg').value = msg[0];
	}
}

function clearBg(obj){
	var backimg = '';
	if(obj.name == 'sms_msg'){
		backimg = "../img/long_message02_none.gif";
	} else {
		backimg = "../img/long_message01_none.gif";
	}
	obj.style.backgroundImage = "url('"+backimg+"')";
}
function insChr(str)
{
	var msg = 	document.getElementById("msg");
	msg.value = msg.value + str;
	clearBg(msg);
	chkLength(msg,'m');
}
function chkLength(obj,tcode){
	str = obj.value;
	if(tcode == 's'){
		if(document.getElementsByName('sms_type')[1].checked == true){
			var specialChars = /[^\u3131-\u314e\uac00-\ud7a3a-zA-Z0-9]/g;
			if(str.match(specialChars)){
				alert("특수 문자는 사용 할 수 없습니다.");
				obj.value = str.split(specialChars).join("");
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
				return;
			}
			var strByte = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			if (strByte>2000){
				alert("제목과 내용의 메시지가 2000bytes를 넘을 수 없습니다.");
				var cutByte = 2000 - parseInt(chkByte(document.getElementById("msg").value),10);
				obj.value = strCut(str,cutByte);
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			}
			if (chkByte(str)>40){
				alert("제목은 40bytes까지 입니다.");
				obj.value = strCut(str,40);
				str = obj.value;
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
				return;
			} else {
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("msg").value),10);
			}
		}
	} else if(tcode == 'm'){
		if(document.getElementsByName('sms_type')[0].checked == true){
			document.getElementsByName('vLength')[0].value = chkByte(str);
			if (chkByte(str)>90){
				document.getElementsByName('sms_type')[1].checked = true;
				document.getElementById("img_sms_title").src = "../img/btn_sms_off.gif";
				document.getElementById("img_lms_title").src = "../img/btn_lms_on.gif";
				document.getElementById("sms_top").style.backgroundImage = "url('../img/lms_top.gif')";
				document.getElementById("tr_lms_subject").style.display = "";
				document.getElementById("td_msg").className = "td_lms_msg";
				document.getElementById("msg").className = "area_lms_msg";
				document.getElementById("msg").setAttribute("name","lms_msg");
				clearBg(document.getElementById("msg"));
				document.getElementById("tr_lms_alert").style.display = "";
				document.getElementById("lms_subject").disabled = false;
				document.getElementsByName('vLength')[0].style.color = "#f00";
				document.getElementById("byte_limit").innerHTML = "2000 Byte";
				document.getElementsByName('vLength')[0].value = chkByte(str);
				document.getElementById("td_point").innerHTML = "LMS - 3포인트 차감";

				document.getElementsByName('linkyn')[0].disabled = false;
			}
		} else {
			if (chkByte(str)<=90){
				document.getElementsByName('sms_type')[0].checked = true;
				document.getElementById("img_sms_title").src = "../img/btn_sms_on.gif";
				document.getElementById("img_lms_title").src = "../img/btn_lms_off.gif";
				document.getElementById("sms_top").style.backgroundImage = "url('../img/sms_top.gif')";
				document.getElementById("td_msg").className = "td_sms_msg";
				document.getElementById("msg").className = "area_sms_msg";
				document.getElementById("msg").setAttribute("name","sms_msg");
				clearBg(document.getElementById("msg"));
				document.getElementsByName('vLength')[0].style.color = "#000";
				document.getElementById("byte_limit").innerHTML = "90 Byte";
				document.getElementsByName('vLength')[0].value = chkByte(str);
				document.getElementById("td_point").innerHTML = "SMS - 1포인트 차감";
				document.getElementById("lms_subject").value = '';
				document.getElementById("tr_lms_subject").style.display = "none";
				document.getElementById("tr_lms_alert").style.display = "none";
				document.getElementById("lms_subject").disabled = true;

				document.getElementsByName('linkyn')[0].disabled = true;
				document.getElementsByName('linkyn')[1].checked = true;
			} else {
				var strByte = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("lms_subject").value),10);
				if (strByte>2000){
					alert("메시지가 2000bytes를 초과할 수 없습니다.");
					var cutByte = 2000 - parseInt(chkByte(document.getElementById("lms_subject").value),10);
					obj.value = strCut(str,cutByte);
					str = obj.value;
				}
				//LMS 는 제목과 메세지 포함 2000byte
				document.getElementsByName('vLength')[0].value = parseInt(chkByte(str),10) + parseInt(chkByte(document.getElementById("lms_subject").value),10);
			}
		}
	}
}

function fnSMSReserve(v) {

	if (v == 1) {
		$('reserve_date_wrap').setStyle({display:'inline'});
	}
	else {
		$('reserve_date_wrap').setStyle({display:'none'});

	}

}

var msgReload	= 'y';
var reMsgType = "<?=$data['sms_type'] ? $data['sms_type'] : 'lms'?>";

if(msgReload == 'y'){
	if(reMsgType == 'lms'){
		var lms_subject = document.getElementById("lms_subject");
		chkLength(lms_subject, 's');
		clearBg(lms_subject);
	}

	var msg = document.getElementById("msg");
	chkLength(msg, 'm');
	clearBg(msg);
}

detail_view("<?=$data['type']?>");
view('coupon',"<?=$data['couponyn']?>");
view('sms',"<?=$data['smsyn']?>");
table_design_load();
</script>
<? include "../_footer.php"; ?>