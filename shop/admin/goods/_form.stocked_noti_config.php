<?
include_once "../lib.php";

if (is_file("../../conf/config.stocked_noti.php")) include "../../conf/config.stocked_noti.php";
else {
	// 기본 설정 값
	$stocked_noti_cfg = array(
		'msg' => '[{shopName}]
{goodsnm}- {goodsopt} 재입고 되었습니다',
		'short_name' => false
		);
	$stocked_noti_cfg['msgOpt'] = "fix";
}


$spChr = array('＃','＆','＊','＠','§','※','☆','★','○','●','◎','◇','◆','□','■','△','▲','▽','▼','→','←','↑','↓','↔','〓','◁','◀','▷','▶','♤','♠','♡','♥','♧','♣','◈','▣','◐','◑','▒','▤','▥','▨','▧','▦','▩','♨','☏','☎','☜','☞','¶','†','‡','↕','↗','↙','↖','↘','♭','♩','♪','♬','㉿','㈜','№','㏇','™','㏂','㏘','℡','ª','º');
?>
<script type="text/javascript">
SMS = {
	insSpchr: function(str) {
		var obj = document.getElementById("stockedSMS");
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength();
	},
	chkLength: function() {
		var obj = document.getElementById('stockedSMS');
		var obj2 = document.getElementById('stockedSMSLen');
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>90) {
			obj2.style.color = "#FF0000";
	//		SMS.chkLength(obj);
		}
		else {
			obj2.style.color = "";
		}
	},
	chkForm: function(fobj) {
		if (!fobj.smsMsg.value) {
			alert("메세지를 입력하세요.");
			fobj.smsMsg.focus();
			return false;
		}
		if (!fobj.smsCallback.value) {
			alert("메세지를 입력하세요.");
			fobj.smsCallback.focus();
			return false;
		}
	}
}
</script>
<? if(!$popup){ ?>
<form name="frmStockedNotiConfig" method="post" action="./indb.stocked_noti_config.php" target="ifrmHidden">
<? } ?>
	<table border="0" width="100%">
	<tr>
		<td>
		<table width="146" cellpadding="0" cellspacing="0" border="0">
		<tr><td><img src="../img/sms_top.gif" /></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align="center" height="81"><textarea name="msg" id="stockedSMS" style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" onkeydown="SMS.chkLength();" onkeyup="SMS.chkLength();" onchange="SMS.chkLength();" required msgR="메세지를 입력해주세요"><?=$stocked_noti_cfg['msg']?></textarea></td>
		</tr>
		<tr><td height="31" background="../img/sms_bottom.gif" align="center"><font class="ver8" color="262626"><input name="stockedSMSLen" id="stockedSMSLen" type="text" style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value="0">/90 Bytes</td></tr>
		</table>

		</td>
		<td style="vertical-align:top;padding-top:20px;">
		특수문자
		<div style="width:100%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
			<? foreach($spChr as $chr) { ?>
			<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr(this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
			<? } ?>
		</div>
		<div style="clear:both">재입고알림 SMS발송시, 등록하신 메시지 내용이 자동으로 입력되어 발송됩니다.</div>
		<strong>치환코드 안내</strong>
		<table class="tb">
			<tr>
				<td>샵네임 : {shopName}</td>
				<td>회원명 : {name}</td>
				<td>상품명 : {goodsnm}</td>
				<td>상품옵션명 : {goodsopt}</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
	<table class="tb">
		<col class="cellC" style="width:150px"><col class="cellL">
		<col class="cellC"><col class="cellL">
		<tr>
			<td rowspan="2">메시지 전송 옵션</td>
			<td class="noline">
				<p><input type="radio" name="msgOpt" value="fix" id="fix" <? if($stocked_noti_cfg['msgOpt'] == "fix"){ echo "checked"; } ?> /><label for="fix">단문(90byte) 고정</label><br /><span style="color:#6D6D6D; margin-left: 20px">전송되는 문자의 총 길이가 90Bytes를 초과시, 상품명과 옵션명이 각각 10Bytes로 축소되며</span><br /><span style="color:#6D6D6D; margin-left: 20px">축소된 이후 에도 90Bytes 초과 내용은 잘려서 전송 됩니다.</span></p>
				<p style="margin-top:-10px"><input type="checkbox" name="shortGoodsNm" value="y" id="shortGoodsNm" style="margin-left:50px;" <? if($stocked_noti_cfg['shortGoodsNm'] == "y"){ echo "checked"; } ?> /><label for="shortGoodsNm">상품정보 짧게 표시</label><br /><span style="color:#6D6D6D; margin-left: 70px">90Bytes 기준에 맞추어 상품명과 옵션명이 최소 한 글자까지 축소되어 전송될 수 있습니다.</span></p>
			</td>
		</tr>
		<tr>
			<td class="noline">
				<p><input type="radio" name="msgOpt" value="separate" id="separate" <? if($stocked_noti_cfg['msgOpt'] == "separate"){ echo "checked"; } ?> /><label for="separate">장문(90byte 이상) 분할전송</label><br /><span style="color:#6D6D6D; margin-left: 20px">90Bytes 이상일 경우 SMS 발송건수는 2건 이상으로 나누어 전송됩니다.</span><br /><span style="color:#6D6D6D; margin-left: 20px">장문(90Bytes 이상) 분할전송으로 설정하여도 90Bytes를 넘지 않을 경우, 1건 으로 발송됩니다.</span></p>
			</td>
		</tr>
	</table>
	<script type="text/javascript">SMS.chkLength();</script>
<? if(!$popup){ ?>
	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>
<? } ?>
