<?
$location = "투데이샵 > SMS 관리";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}


$tsCfg = $todayShop->cfg;

$arTitle['orderc'] = array('title'=>'쿠폰상품 주문완료시 자동발송', 'desc'=>'(결제 완료시 발송되는 메시지입니다.)');
$arTitle['salec'] = array('title'=>'쿠폰상품 판매성공시 자동발송', 'desc'=>'(판매가 결정되면 발송됩니다.)');
$arTitle['giftc'] = array('title'=>'쿠폰상품 판매성공시 자동발송(선물하기)', 'desc'=>'(선물을 받는 사람에게 발송됩니다.)');
$arTitle['orderg'] = array('title'=>'실물상품 주문완료시 자동발송', 'desc'=>'(결제 완료시 발송되는 메시지입니다.)');
$arTitle['deliveryg'] = array('title'=>'실물상품 배송시 자동발송', 'desc'=>'(판매가 결정되고 상태가 배송중으로 바뀔 때 발송되는 메세지입니다.)');
$arTitle['cancel'] = array('title'=>'판매실패시 자동발송', 'desc'=>'(목표구매량에 도달하지 못한 경우 구매 취소 메시지 입니다.)');

$spChr = array('＃','＆','＊','＠','§','※','☆','★','○','●','◎','◇','◆','□','■','△','▲','▽','▼','→','←','↑','↓','↔','〓','◁','◀','▷','▶','♤','♠','♡','♥','♧','♣','◈','▣','◐','◑','▒','▤','▥','▨','▧','▦','▩','♨','☏','☎','☜','☞','¶','†','‡','↕','↗','↙','↖','↘','♭','♩','♪','♬','㉿','㈜','№','㏇','™','㏂','㏘','℡','ª','º');

// SMS 포인트 가져오기
$sms = &load_class('sms', 'sms');
$smsPt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
unset($sms);
?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
SMS = {
	insSpchr: function(n, str) {
		var obj = document.getElementById("smsMsg_" + n);
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength(n);
	},
	chkLength: function(n) {
		var obj = document.getElementById('smsMsg_'+n);
		var obj2 = document.getElementById('vLength_'+n);
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>80) {
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

<div style="width:100%">
	<form name="frmSMS" method="post" action="indb.sms_config.php" onsubmit="return SMS.chkForm(this);" target="ifrmHidden" />
		<div class="title title_top">SMS 관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=7')"><img src="../img/btn_q.gif"></a></div>
		<div>현재 SMS 잔여포인트는 <b><?=number_format($smsPt)?>Point</b>입니다. <a href="../member/sms.pay.php" target="_blank"><font class=extext_l>[SMS 포인트 충전 바로가기]</font></a></div>
		<?
		foreach($arTitle as $key => $val) {
		?>
		<div style="padding:10px;">
			<div style="color:#0074ba; font-weight:bold;"><?=$val['title']?> <font class="small1"><?=$val['desc']?></font></div>
			<div style="width:90%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
				<? foreach($spChr as $chr) { ?>
				<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr('<?=$key?>', this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
				<? } ?>
			</div>
			<div><textarea id="smsMsg_<?=$key?>" name="smsMsg_<?=$key?>" style="font:9pt 굴림체;height:74px; width:90%;" onkeydown="SMS.chkLength('<?=$key?>');" onkeyup="SMS.chkLength('<?=$key?>');" onchange="SMS.chkLength('<?=$key?>');"><?=$tsCfg['smsMsg_'.$key]?></textarea></div>
			<div class="noline">
				<input type="checkbox" name="smsUse_<?=$key?>" value="y" <? if ($tsCfg['smsUse_'.$key]=='y') {?>checked="checked"<?}?> /> 고객에게 자동발송
				<font class="ver8" color="262626"><input id="vLength_<?=$key?>" type="text" style="width:40px;text-align:right;font-size:8pt;font-style:verdana;border:solid 1px;" value="0"> Bytes
			</div>
			<script type="text/javascript">SMS.chkLength('<?=$key?>');</script>
		</div>
		<? } ?>
		<div class="button">
			<input type=image src="../img/btn_register.gif">
			<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
		</div>
		<div style="padding-top:15px"></div>
	</form>

	<div style="padding:10px;">
		<div style="color:#0074ba; font-weight:bold;">치환코드</div>
		<table width=500 cellpadding=0 cellspacing=0 border=0>
		<tr><td class=rnd colSpan=4></td></tr>
		<tr class=rndbg>
			<th>치환코드명</th>
			<th>설명</th>
			<th>치환코드명</th>
			<th>설명</th>
		</tr>
		<tr><td class=rnd colSpan=4></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=shopName}</td>
			<td>쇼핑몰 명</td>
			<td>{=memo}</td>
			<td>메모</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=goodsnm}</td>
			<td>상품명</td>
			<td>{=nameOrder}</td>
			<td>주문자명</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=couponNo}</td>
			<td>쿠폰번호</td>
			<td>{=deliverycomp}</td>
			<td>택배사</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=option}</td>
			<td>옵션정보</td>
			<td>{=deliverycode}</td>
			<td>송장번호</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=usedt}</td>
			<td>유효기간</td>
			<td></td>
			<td></td>
		</tr>
		</table>
	</div>
</div>

<div style="margin-top:20px"></div>

<div style="clear:both;" id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
	<tr>
		<td>
			<div>쿠폰상품 결제시 발송되는 자동 SMS입니다.</div>
			<div>주문완료시 쿠폰번호정보가 발송되며, 목표량을 달성하지 못한 경우 취소 SMS를 발송합니다.
			<div>일반쇼핑몰과 중복되는 내용의 SMS 내용은 <a href="../member/sms.auto.php" target="_blank" style="color:#0074ba;">회원/SMS/EMAIL>SMS설정>SMS자동발송/설정</a> 메뉴에서 관리합니다.</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
	cssRound('MSG01');
</script>
<? include "../_footer.php"; ?>