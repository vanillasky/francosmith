<?
$location = "기본설정 > 회원정보 변경 관리 > 비밀번호 찾기 설정";
include "../_header.php";

$info_cfg = $config->load('member_info');

$info_cfg['finder_use_email'] = 1;	// 무조건 사용
if(!$info_cfg['finder_use_mobile']) $info_cfg['finder_use_mobile'] = 0;
if(!$info_cfg['finder_mobile_auth_message']) $info_cfg['finder_mobile_auth_message'] = '[{shopName}]'.PHP_EOL.'회원님의 인증번호는 {authNum} 입니다. 정확히 입력해주세요.';

$checked['finder_use_email'][$info_cfg['finder_use_email']] = " checked";
$checked['finder_use_mobile'][$info_cfg['finder_use_mobile']] = " checked";

$spChr = array('＃','＆','＊','＠','§','※','☆','★','○','●','◎','◇','◆','□','■','△','▲','▽','▼','→','←','↑','↓','↔','〓','◁','◀','▷','▶','♤','♠','♡','♥','♧','♣','◈','▣','◐','◑','▒','▤','▥','▨','▧','▦','▩','♨','☏','☎','☜','☞','¶','†','‡','↕','↗','↙','↖','↘','♭','♩','♪','♬','㉿','㈜','№','㏇','™','㏂','㏘','℡','ª','º');

?>

<script type="text/javascript">
SMS = {
	insSpchr: function(str) {
		var obj = document.getElementById("el-auth-message");
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength();
	},
	chkLength: function() {
		var obj = document.getElementById('el-auth-message');
		var obj2 = document.getElementById('el-auth-message-length');
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>90) {
			obj2.style.color = "#FF0000";
	//		SMS.chkLength(obj);
		}
		else {
			obj2.style.color = "";
		}
	}
}
</script>


<form method="post" action="indb.info.php">
<input type="hidden" name="mode" value="finder_pwd">

<!-- e-mail 주소로 인증 후 재발급 -->
<div class="title title_top">
	e-mail 주소로 인증 후 재발급
	<span>회원정보상에 등록되어 있는 e-mail 주소로 인증 후 비밀번호를 재발급 합니다.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=31')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용 설정</td>
	<td class="noline">
	<input type="radio" name="finder_use_email" value="1" checked /> <label for="info_email1">사용</label>
	<!--input type="radio" name="finder_use_email" value="0" <?=$checked['finder_use_email'][0]?> /> <label for="info_email0">사용 안함</label-->
	<br />
	<div class="extext_t">e-mail 주소로 비밀번호 재발급 서비스는 기본으로 제공 됩니다.</div>
	</td>
</tr>
</table>
<div class="extext_t">* <a href="../member/email.cfg.php?mode=11" style="font-weight:bold;" class="extext">[ 회원관리>자동메일설정>비빌번호찾기 인증메일,  비빌번호변경 안내메일 ]</a> 에서 메일 내용을 관리하실 수 있습니다.</div>

<!-- 휴대폰 번호로 인증 후 재발급 -->
<div class="title">
	휴대폰 번호로 인증 후 재발급
	<span>회원정보상에 등록되어 있는 휴대폰 번호로 인증 후 비밀번호를 재발급 합니다.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=31')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>사용 설정</td>
	<td class="noline">
	<input type="radio" name="finder_use_mobile" value="1" <?=$checked['finder_use_mobile'][1]?> /> <label for="info_mobile1">사용</label>
	<input type="radio" name="finder_use_mobile" value="0" <?=$checked['finder_use_mobile'][0]?> /> <label for="info_mobile0">사용 안함</label>
	<br />
	<div class="extext_t">서비스 사용 여부를 설정합니다. 사용으로 설정시 비밀번호 찾기 화면에서 서비스 선택 메뉴로 추가 제공됩니다.</div>
	</td>
</tr>
<tr>
	<td>잔여 SMS 포인트</td>
	<td class="noline">
		<div>
			<span style="font-weight:bold"><font class="ver9" color="0074ba"><b><?=number_format((int) getSmsPoint())?></b></span><font color="262626">건</font>
			<a href="javascript:location.href='../member/sms.pay.php';"><img src="../img/btn_smspoint.gif" align="absmiddle"></a>
		</div>
		<div class="extext_t">SMS포인트가 없는 경우 '휴대폰 번호로 인증 후 재발급’ 서비스를 사용으로 설정하셔도 서비스가 제공되지 않습니다. (비밀번호 찾기 화면에서 선택 메뉴에 추가되지 않습니다.)</div>
	</td>
</tr>
<tr>
	<td>인증번호 발송<br>메세지
</td>
	<td class="noline">

		<table border="0" width="100%">
		<tr>
			<td>
			<table width="146" cellpadding="0" cellspacing="0" border="0">
			<tr><td><img src="../img/sms_top.gif" /></td></tr>
			<tr>
				<td background="../img/sms_bg.gif" align="center" height="81"><textarea name="finder_mobile_auth_message" id="el-auth-message" style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" onkeydown="SMS.chkLength();" onkeyup="SMS.chkLength();" onchange="SMS.chkLength();" required msgR="메세지를 입력해주세요"><?=$info_cfg['finder_mobile_auth_message']?></textarea></td>
			</tr>
			<tr><td height="31" background="../img/sms_bottom.gif" align="center"><font class="ver8" color="262626"><input id="el-auth-message-length" type="text" style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value="0">/90 Bytes</td></tr>
			</table>

			</td>
			<td style="vertical-align:top;padding-top:20px;">
			특수문자
			<div style="width:100%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
				<? foreach($spChr as $chr) { ?>
				<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr(this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
				<? } ?>
			</div>

			<div class="extext_t">
			기존의 비밀번호찾기시 발송 메시지 [회원관리>SMS설정> 자동발송/설정] 는 사용이 중지되며 더 이상 발송되지 않습니다. <br>
			{shopName} 은 상점 이름의 치환코드<br>
			{authNum} 은 인증번호의 치환코드<br>
			{shopName}과 {authNum}를 포함하여 변환된 문자의 길이가 90 Bytes를 넘으면 받는 사람 문자 메세지 화면에서 일부 내용이 잘려보일 수 있습니다.
			</div>

			</td>
		</tr>
		</table>
		<script type="text/javascript">SMS.chkLength();</script>

	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">비밀번호 찾기에 필요한 다양한 서비스의 제공여부를 설정 할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">휴대폰 번호로 재발급하기 서비스를 사용으로 설정 하실 경우</td></tr>
<tr><td>&nbsp;&nbsp;SMS 잔여 포인트를 확인하여 주세요. 포인트 잔여가 없는 경우, 서비스가 제공되지 않습니다.</td></tr>
<tr><td>&nbsp;&nbsp;기존의 비밀번호찾기시 발송 메시지 <a href="../member/sms.auto.php" style="font-weight:bold;"><font color="#ffffff">[회원관리>SMS설정> 자동발송/설정]</font></a> 는 사용이 중지되며 더 이상 발송되지 않습니다.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
	}
</script>
<? include "../_footer.php"; ?>