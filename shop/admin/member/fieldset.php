<?

$location = "회원관리 > 회원가입관리";
include "../_header.php";
include "../../conf/fieldset.php";
@include "../../conf/mobile_fieldset.php";

$fld	= array(
	'def'	=> array(
		"m_id"			=> "아이디",
		"password"		=> "비밀번호",
		"name"			=> "이름",
	),
	'per'	=> array(
		"nickname"		=> "닉네임",
		"email"			=> "이메일",
		"resno"			=> "주민등록번호",
		"sex"			=> "성별",
		"birth"			=> "생년월일",
		"calendar"		=> "양/음력",
		"address"		=> "주소",
		"phone"			=> "전화번호",
		"mobile"		=> "핸드폰",
		"fax"			=> "팩스번호",
		"company"		=> "회사명",
		"service"		=> "업태",
		"item"			=> "종목",
		"busino"		=> "사업자번호",
		"mailling"		=> "메일링",
		"sms"			=> "SMS 수신",
		"marriyn"		=> "결혼여부",
		"marridate"		=> "결혼기념일",
		"job"			=> "직업",
		"interest"		=> "관심분야",
		"memo"			=> "남기는말씀",
		"recommid"		=> "추천인",
		"ex1"			=> "추가1",
		"ex2"			=> "추가2",
		"ex3"			=> "추가3",
		"ex4"			=> "추가4",
		"ex5"			=> "추가5",
		"ex6"			=> "추가6",
	),
);

// 20130508 후 주민등록번호 출력제한
if (date('Ymd') >= 20130508 && $checked['useField']['resno'] == '' && $checked_mobile['useField']['resno'] == '') {
	unset($fld['per']['resno']);
}
?>

<script>

function chkBox2(El,mode,mode2)
{
	if (!El) return;
	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		chk(El[i].key,mode2);
	}
}

function chk(obj,mode)
{
	var objUse = document.frmField["useField["+obj+"]"];
	var objReq = document.frmField["reqField["+obj+"]"];
	if (objReq.checked && mode=='req') objUse.checked = true;
	else if (objUse.checked==false && mode=='use') objReq.checked = false;
}

function chkBox2_mobile(El,mode,mode2)
{
	if (!El) return;
	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		chk_mobile(El[i].key,mode2);
	}
}

function chk_mobile(obj,mode)
{
	var objUse = document.frmField["useMobileField["+obj+"]"];
	var objReq = document.frmField["reqMobileField["+obj+"]"];
	if (objReq.checked && mode=='req') objUse.checked = true;
	else if (objUse.checked==false && mode=='use') objReq.checked = false;
}
</script>

<form name="frmField" method="post" action="indb.php">
<input type="hidden" name="mode" value="fieldset" />

<div class="title title_top">회원가입 정책관리<span>회원가입에 필요한 각종 정책을 정합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=3')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>회원인증절차</td>
	<td class="noline">

	<div style="margin:5px 0px">
	<input type="radio" name="status" value="1" <?=( $joinset['status'] == '1' ? 'checked' : '' )?> />인증절차없음&nbsp;
	<input type="radio" name="status" value="0" <?=( $joinset['status'] != '1' ? 'checked' : '' )?> />관리자 인증 후 가입	<font class="extext">(관리자 승인 후 가입처리할 수 있습니다)</font>
	</div>

	<div style="margin:5px 2px">
	<span class="extext">* 네이버 체크아웃 부가서비스를 이용중일 경우 회원인증절차를 사용하실 수 없습니다.</span>
	</div>

	</td>
</tr>
<tr>
	<td>회원재가입기간</td>
	<td>
	<div style="padding-top:5"></div>
	회원탈퇴 및 회원삭제 후 <input type="text" name="rejoin" value="<?=$joinset['rejoin']?>" size="4" class="rline" /> 일 동안 재가입할 수 없습니다

	<div style="padding-top:5"></div>

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td height="5"></td></tr>
	<tr><td><font class="extext">회원 재가입 기간에 제한이 필요한 경우 반드시 본인확인인증 수단을 적용하여야 합니다.
  </font></td></tr>
	<tr><td style="padding: 2px 0px 0px 0px"><a href="realname_info.php"><font class="extext">[<u><b>아이핀 바로가기</b></u>]</a><br/><a href="adm_member_auth.hpauthDream.info.php"><font class="extext">[<u><b>휴대폰본인인증 바로가기</b></u>]</font></a></td></tr>
	<tr><td height="5"></td></tr>
	</table>

	<div style="padding-top:5"></div>
	</td>
</tr>
<tr>
	<td>가입불가 ID</td>
	<td>
	<textarea name="unableid" style="width:100%;height:60px" class="tline"><?=$joinset['unableid']?></textarea>

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td height="5"></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext">회원가입을 제한할 ID를 입력하세요. 컴마로 구분합니다</font></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext">주요 제한 ID : </font><font class=ver7 color=627dce>admin,administration,administrator,master,webmaster,manage,manager</font></td></tr>
	<tr><td height="5"></td></tr>
	</table>

	</td>
</tr>
<tr>
	<td width=125>회원가입시 적립금지급</td>
	<td><input type="text" name="emoney" value="<?=$joinset['emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> 원 <font class="extext">(미적용시 0 원을 입력)</font></td>
</tr>
<tr>
	<td>회원가입시 쿠폰지급</td>
	<td>회원가입쿠폰을 제공하고 싶다면 <a href="../event/coupon_register.php" target=_blank><font class=extext_l>[쿠폰만들기]</font></a> 에서 쿠폰을 발행하세요. '회원가입자동발급' 방식으로 발급하세요</td>
</tr>
<tr>
	<td>가입시 회원그룹</td>
	<td>회원가입 후 바로 <select name="grp">
<?
foreach( member_grp() as $v ){
	echo '<option value="' . $v['level'] . '"' . ( $joinset['grp'] == $v['level'] ? 'selected' : '' ) . '>' . $v['grpnm'] . ' - lv[' . $v['level'] . ']</option>' . "\n";
}
?>
	</select> 그룹에 속하도록 합니다. <font class="extext">('회원그룹관리'에서 그룹을 만드세요) &nbsp;<a href="../member/group.php" target="_new"><font class="extext_l">[그룹관리바로가기]</font></a></td>
</tr>
<tr>
	<td>추천인 설정</td>
	<td>
	  <div>신규가입고객이 기입한 추천인에게 적립금 <input type="text" name="recomm_emoney" value="<?=$joinset['recomm_emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> 원 지급 <font class="extext">(미적용시 0 원을 입력)</font></div>
	  <div>신규가입시 추천인을 기입하면 적립금 <input type="text" name="recomm_add_emoney" value="<?=$joinset['recomm_add_emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> 원 추가 지급 <font class="extext">(미적용시 0 원을 입력)</font></div>

	</td>
</tr>
<tr>
	<td>만14세 미만 가입 설정</td>
	<td class="noline">

	<div style="margin:5px 0px">
	<input type="radio" name="under14status" value="1" <?=( $joinset['under14status'] == '1' ? 'checked' : '' )?> />관리자 승인 후 가입&nbsp;
	<input type="radio" name="under14status" value="0" <?=( in_array($joinset['under14status'], array(1,2)) !== true ? 'checked' : '' )?> />승인없이 가입&nbsp;
	<input type="radio" name="under14status" value="2" <?=( $joinset['under14status'] == '2' ? 'checked' : '' )?> />가입불가
	</div>

	<div style="margin:5px 2px" class="extext">
		<div><img src="../img/icon_list.gif" align="absmiddle" />정보통신망법 제31조 제1항에 따라 만14세 미만의 아동은 법정대리인의 동의를 확인 후 회원가입 할 수 있습니다.
			<a href="http://www.law.go.kr/lsInfoP.do?lsiSeq=111970#0000" target="_blank" class="extext_l">[관련법규 전문 보기]</a>
		</div>
		<div><img src="../img/icon_list.gif" align="absmiddle" />'관리자 승인 후 가입'으로 설정 후 만14세 미만의 아동이 회원가입 할 경우 '미승인'상태로 가입되므로 법정대리인 동의를 확인 후
			<a href="../member/list.php" class="extext_l">[회원리스트]</a> 혹은 <a href="../member/batch.php?func=status" class="extext_l">[회원승인상태 일괄변경]</a> 메뉴를 통해 <br>
			&nbsp;&nbsp;&nbsp; 승인 상태를 변경해 주시기 바랍니다. <a href="http://guide.godo.co.kr/guide/doc/만14세미만회원가입동의서(샘플).docx" target="_blank" class="extext_l">[법정대리인 동의서 샘플 다운받기]</a>
		</div>
		<div><img src="../img/icon_list.gif" align="absmiddle" />'가입불가'로 설정하면 만14세 미만은 회원가입을 할 수 없습니다.</div>
		<div class="extext">
		※ '관리자 승인 후 가입' 및 '가입불가'로 설정 시 <u>본인인증수단이 적용</u>되어 있어야 하며, 본인인증수단 미 사용 시에는 <u>'생년월일'을 필수로 입력</u> 받으셔야 합니다.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp; 본인인증수단 또는 생년월일 필수 설정이 없는 경우 만14세 미만 회원을 판단할 수 없으므로 <u class="red">'미승인'상태로 가입되거나(관리자 승인 후 가입 선택 시), 가입이 안되오니(가입불가 선택 시) 주의</u>해주시기 바랍니다.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp; 본인인증수단설정 바로가기 : <a href="../member/adm_member_auth.hpauth.php" class="extext_l">[휴대폰본인확인관리]</a> <a href="../member/ipin_new.php" class="extext_l">[아이핀관리]</a>
		</div>
	</div>
	</td>
</tr>
</table>




<div class="title">회원가입 항목관리<span>회원가입에 필요한 각종 항목 및 옵션을 정합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=3')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>※ 주민등록번호 수집 금지 안내</b></font></div>
<div style="padding-top:7px;"><b>정보통신망법 개정에 따라서 신규 주민등록번호 수집이 금지됩니다.(시행일자 : 2012년 8월 18일)</b></div>
<ol style="margin:7px 0 0 25px;">
<li>회원가입 항목 중 ‘주민등록번호(resno)’ 사용여부가 사용으로 체크되어 있으실 경우,  체크를 해지하시고 미사용으로 등록/저장하여 주셔야 합니다.</li>
<li>아이디/비밀번호 찾기시 주민등록번호 대체확인 수단인 ‘이메일(email)’ 항목은 반드시 사용 및 필수사항으로 선택하여 등록/저장하여 주세요!!</li>
</ol>
<div style="padding-top:10px;"><a href="http://www.godo.co.kr/news/notice_view.php?board_idx=725" target="_blank"><font class="small1" color="#0074BA"><b><u>[주민등록번호 미수집관련 안내 및 조치사항 자세히 보기]</u></b></font></a></div>
</table>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr>
	<td style="padding:7px 0 10px 10px; color:#666666;">
		<div style="padding-top:5px;"><font class="g9" color="#0074BA"><strong>※ 회원 비밀번호 작성 규칙 안내</strong></font></div>
		<div style="padding-top:7px;">
			<strong>정보통신망법의 기준에 따라 회원 비밀번호의 작성 규칙을 아래와 같이 보완합니다.</strong> (<a href="http://guide.godo.co.kr/guide/doc/(방통위고시_제2012-50호)_개인정보의_기술적.관리적_보호조치_기준_전문.hwp" target="_blank"><font color="#0074BA"><strong>방통위고시 제2012-50호 전문 다운로드</strong></font></a>)
		</div>
		<div>
			정보통신서비스 제공자 등은 이용자가 안전한 비밀번호를 이용할 수 있도록 비밀번호 작성규칙을 수립하고 이행해야 합니다. 이에 고도몰에서는 영문대문자(26개), 영문소문자(26개), 숫자(10개), 특수문자(32개) 중 <font color="red"><u>2종류 이상을 조합하여 최소 10자리 이상 16자리 이하로 비밀번호를 설정하도록 적용</u></font>합니다.
		</div>

		<div style="padding-top:10px;"><strong>2014년 7월 3일 이전</strong>부터 운영중인 쇼핑몰의 경우,</div>
		<div>관련 <strong>스킨 패치를 반드시 적용</strong>하셔야 합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2037" target="_blank"><font color="#0074BA"><strong>[바로가기]</strong></font></a></div>
		<div><font color="#0074BA">※ 패치 적용 이전에 가입한 회원의 경우 안전한 개인정보보호를 위하여 비밀번호 작성 규칙에 따라 비밀번호를 변경할 수 있도록 적절한 조치를 취하여 주시기 바랍니다.</font> (비밀번호 변경 안내 전체 메일(SMS) 보내기, 비밀번호 변경 안내 설정 기능 사용 등)</div>
	</td>
</tr>
</table>

<table width="100%" border="1" bordercolor="#efefef" style="border-collapse:collapse">
<col width=200px><col width=150px><col width=150px><col width=150px><col width=150px>
<tr height="25" bgcolor="#f7f7f7">
	<th rowspan=2>필드명</th>
	<th colspan=2>PC용</th>
	<th colspan=2>모바일샵용<div><font class="g9" color="#0074BA"><b>모바일샵 회원가입시에는 회원인증을 제공하지 않습니다.</b></div></th>
</tr>
<tr height="25" bgcolor="#f7f7f7">

	<th width=130px><a href="javascript:void(0)" onclick="chkBox2(document.frmField.elements['chkUse[]'],'rev','use');">사용여부</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2(document.frmField.elements['chkReq[]'],'rev','req');">필수사항</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2_mobile(document.frmField.elements['chkMobileUse[]'],'rev','use');">사용여부</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2_mobile(document.frmField.elements['chkMobileReq[]'],'rev','req');">필수사항</a></th>
</tr>

<col align="center" width="20%" bgcolor="#f7f7f7"><col align="center" width="15%" span="2">

<tbody style="height:25">

<? while (list($key,$value)=each($fld['def'])){ ?>
<tr class=noline>
<!-- 	<? if ($key=="m_id"){ ?><td rowspan=<?=count($fld['def'])?> valign="top" style="padding-top:4px;">필수사항</td><? } ?> -->
	<td align=left style="padding-left:10px"><?=$value?></td>
	<td><input type="checkbox" name="useField[<?=$key?>]" checked disabled /> 사용</td>
	<td><input type="checkbox" name="reqField[<?=$key?>]" checked disabled /> 필수</td>
	<td><input type="checkbox" name="useMobileField[<?=$key?>]" checked disabled /> 사용</td>
	<td><input type="checkbox" name="reqMobileField[<?=$key?>]" checked disabled /> 필수</td>
</tr>
<? } ?>

<tr>
	<? $idx=0; while (list($key,$value)=each($fld['per'])){ ?>
	<? if (in_array( $key, array( 'ex1', 'ex2', 'ex3', 'ex4', 'ex5', 'ex6' ) ) ){?>
	<td><?=$value?> <input type="text" name="<?=$key?>" value="<?=$joinset[ $key ]?>" size="10" style="cline" /> <font class=ver7 color='3853a5'>(<?=$key?>)</font></td>
	<? } else { ?>
	<td align=left style="padding-left:10px"><?=$value?> (<font class=ver7 color='3853a5'><?=$key?></font>)</td>
	<? } ?>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkUse[]" name="useField[<?=$key?>]" <?=$checked['useField'][$key]?> key="<?=$key?>" onClick="chk('<?=$key?>','use');" /> 사용</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkReq[]" name="reqField[<?=$key?>]" <?=$checked['reqField'][$key]?> key="<?=$key?>" onClick="chk('<?=$key?>','req');" /> 필수</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkMobileUse[]" name="useMobileField[<?=$key?>]" <?=$checked_mobile['useField'][$key]?> key="<?=$key?>" onClick="chk_mobile('<?=$key?>','use');" /> 사용</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkMobileReq[]" name="reqMobileField[<?=$key?>]" <?=$checked_mobile['reqField'][$key]?> key="<?=$key?>" onClick="chk_mobile('<?=$key?>','req');" /> 필수</td>
	</tr><tr>
	<? } ?>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>

</form>

	<div id="MSG02">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />회원가입시 입력하는 항목들을 정하는 곳입니다.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />원하는 필드항목을 체크하고, 필수사항인지의 여부를 체크하시면 됩니다. 추가로 항목을 만드실 수도 있습니다.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG02');</script>

<? include "../_footer.php"; ?>