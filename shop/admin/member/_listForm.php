<?
	# 팝업창에서의 검색이 있는경우 상세검색을 넣음
	if($popupSearch == "Y"){
?>
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<col class="cellC" /><col class="cellL" />
<tr>
	<td class="noline" colspan="4">
	<input type="checkbox" name="popupDetail" value="Y" onClick="javascript:popupDetailDiv();" <?=$checked['popupDetail']['Y']?>> 상세 검색시 체크를 해주세요
	</td>
</tr>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
	<option value="name" <?=$selected['skey']['name']?>> 회원명 </option>
	<option value="nickname" <?=$selected['skey']['nickname']?>> 닉네임 </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> 아이디 </option>
	<option value="email" <?=$selected['skey']['email']?>> 이메일 </option>
	<option value="phone" <?=$selected['skey']['phone']?>> 전화번호 </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> 핸폰번호 </option>
	<option value="recommid" <?=$selected['skey']['recommid']?>> 추천인 </option>
	<option value="company" <?=$selected['skey']['company']?>> 회사명 </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
	<td>승인여부/그룹</td>
	<td>
	<select name="sstatus">
	<option value="" <?=$selected['sstatus']['']?>> 전체 </option>
	<option value="1" <?=$selected['sstatus']['1']?>> 승인 </option>
	<option value="0" <?=$selected['sstatus']['0']?>> 미승인 </option>
	</select>
	<select name="slevel">
	<option value="">==그룹선택==</option>
	<option value="__null__" <? if($_GET['slevel']=='__null__')echo 'selected';?>>그룹없음</option>
	<? foreach( member_grp() as $v ){ ?>
	<option value="<?=$v[level]?>" <?=$selected['slevel'][$v['level']]?>><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
	<? } ?>
	</select>
	</td>
</tr>
</table>
<div style="padding-top:3;display:none;" id="searchDetail">
<?	}	?>
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<col class="cellC" /><col class="cellL" />
<?if(!$popupSearch){?>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> 통합검색 </option>
	<option value="name" <?=$selected['skey']['name']?>> 회원명 </option>
	<option value="nickname" <?=$selected['skey']['nickname']?>> 닉네임 </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> 아이디 </option>
	<option value="email" <?=$selected['skey']['email']?>> 이메일 </option>
	<option value="phone" <?=$selected['skey']['phone']?>> 전화번호 </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> 핸폰번호 </option>
	<option value="recommid" <?=$selected['skey']['recommid']?>> 추천인 </option>
	<option value="company" <?=$selected['skey']['company']?>> 회사명 </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
	<td>승인여부/그룹</td>
	<td>
	<select name="sstatus">
	<option value="" <?=$selected['sstatus']['']?>> 전체 </option>
	<option value="1" <?=$selected['sstatus']['1']?>> 승인 </option>
	<option value="0" <?=$selected['sstatus']['0']?>> 미승인 </option>
	</select>
	<select name="slevel">
	<option value="">==그룹선택==</option>
	<option value="__null__" <? if($_GET['slevel']=='__null__')echo 'selected';?>>그룹없음</option>
	<? foreach( member_grp() as $v ){ ?>
	<option value="<?=$v[level]?>" <?=$selected['slevel'][$v['level']]?>><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
	<? } ?>
	</select>
	</td>
</tr>
<?	}	?>
<tr>
	<td>구매액</td>
	<td>
	<input type="text" name="ssum_sale[]" value="<?=$_GET['ssum_sale'][0]?>" size="10" onkeydown="onlynumber();" class="rline" />원 ~
	<input type="text" name="ssum_sale[]" value="<?=$_GET['ssum_sale'][1]?>" size="10" onkeydown="onlynumber();" class="rline" />원
	</td>
	<td>적립금</td>
	<td>
	<input type="text" name="semoney[]" value="<?=$_GET['semoney'][0]?>" size="10" onkeydown="onlynumber();" class="rline" />원 ~
	<input type="text" name="semoney[]" value="<?=$_GET['semoney'][1]?>" size="10" onkeydown="onlynumber();" class="rline" />원
	</td>
</tr>
<tr>
	<td>가입일</td>
	<td colspan="3">
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>최종로그인</td>
	<td colspan="3">
	<input type="text" name="slastdt[]" value="<?=$_GET['slastdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
	<input type="text" name="slastdt[]" value="<?=$_GET['slastdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>성별</td>
	<td class="noline">
	<input type="radio" name="sex" value="" <?=$checked['sex']['']?> />전체
	<input type="radio" name="sex" value="m" <?=$checked['sex']['m']?> />남자
	<input type="radio" name="sex" value="w" <?=$checked['sex']['w']?> />여자
	</td>
	<td>연령층</td>
	<td>
	<select name="sage">
	<option value="" <?=$selected['sage']['']?>> 전체 </option>
	<option value="10" <?=$selected['sage']['10']?>> 10대 </option>
	<option value="20" <?=$selected['sage']['20']?>> 20대 </option>
	<option value="30" <?=$selected['sage']['30']?>> 30대 </option>
	<option value="40" <?=$selected['sage']['40']?>> 40대 </option>
	<option value="50" <?=$selected['sage']['50']?>> 50대 </option>
	<option value="60" <?=$selected['sage']['60']?>> 60대이상 </option>
	</select>
	</td>
</tr>
<tr>
	<td>방문횟수</td>
	<td>
	<input type="text" name="scnt_login[]" value="<?=$_GET[scnt_login][0]?>" size="10" onkeydown="onlynumber();" class="rline" />회 ~
	<input type="text" name="scnt_login[]" value="<?=$_GET[scnt_login][1]?>" size="10" onkeydown="onlynumber();" class="rline" />회
	</td>
	<td>휴면회원검색</td>
	<td>
	<input type="text" name="dormancy" value="<?=$_GET['dormancy']?>" size="8" maxlength="8" onkeydown="onlynumber();" class="rline" /> 일 이상 미접속 회원검색
	</td>
</tr>
<tr>
	<td>메일수신여부</td>
	<td class="noline">
	<input type="radio" name="mailing" value="" <?=$checked['mailing']['']?> />전체
	<input type="radio" name="mailing" value="y" <?=$checked['mailing']['y']?> />수신
	<input type="radio" name="mailing" value="n" <?=$checked['mailing']['n']?> />수신거부
	</td>
	<td>SMS수신여부</td>
	<td class="noline">
	<input type="radio" name="smsyn" value="" <?=$checked['smsyn']['']?> />전체
	<input type="radio" name="smsyn" value="y" <?=$checked['smsyn']['y']?> />수신
	<input type="radio" name="smsyn" value="n" <?=$checked['smsyn']['n']?> />수신거부
	</td>
</tr>
<tr>
	<td>생년월일</td>
	<td>
	<select name="birthtype">
	<option value="" <?=$selected['birthtype']['']?>> 전체 </option>
	<option value="s" <?=$selected['birthtype']['s']?>> 양력 </option>
	<option value="l" <?=$selected['birthtype']['l']?>> 음력 </option>
	</select>
	<input type="text" name="birthdate[]" value="<?=$_GET['birthdate'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="birthdate[]" value="<?=$_GET['birthdate'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<div style="padding-left:53px"><font class="ver71" color="627dce">ex) 20080321 <font class="extext">또는</font> 0321</font></div>
	</td>
	<td>결혼여부/결혼기념일</td>
	<td>
	<select name="marriyn">
	<option value="" <?=$selected['marriyn']['']?>> 전체 </option>
	<option value="n" <?=$selected['marriyn']['n']?>> 미혼 </option>
	<option value="y" <?=$selected['marriyn']['y']?>> 기혼 </option>
	</select>
	<input type="text" name="marridate[]" value="<?=$_GET['marridate'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="marridate[]" value="<?=$_GET['marridate'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<div style="padding-left:53px"><font class="ver71" color="627dce">ex) 20080321 <font class="extext">또는</font> 0321</font></div>
	</td>
</tr>
<tr>
	<? if (strpos($_SERVER['PHP_SELF'],'member/list.php') || strpos($_SERVER['PHP_SELF'],'member/batch.php')) { // 회원리스트 / 회원승인상태 일괄변경 에서만 노출?>
	<td>회원가입 유입 경로</td>
	<td class="noline" style="font-size:11px;">
	<input type="checkbox" name="inflow[naverCheckout]" id="inflow[naverCheckout]" value="naverCheckout" <?=$checked['inflow']['naverCheckout']?> /><label for="inflow[naverCheckout]"><img src="../img/memIcon_naverCheckout.gif" align="absmiddle" alt="네이버 체크아웃" title="네이버 체크아웃" />네이버 체크아웃</label>
	<input type="checkbox" name="inflow[mobileshop]" id="inflow[mobileshop]" value="mobileshop" <?=$checked['inflow']['mobileshop']?> /><label for="inflow[mobileshop]"><img src="../img/memIcon_mobileshop.gif" align="absmiddle" alt="모바일샵" title="모바일샵" />모바일샵</label>
	</td>
	<td>회원가입구분</td>
	<td class="noline" style="font-size:11px;">
	<select name="sunder14">
	<option value="">==회원구분선택==</option>
	<option value="" <?=$selected['sunder14']['']?>> 전체 </option>
	<option value="1" <?=$selected['sunder14']['1']?>> 만14세 미만 </option>
	</select>
	</td>
	<? } else { ?>
	<td>회원가입 유입 경로</td>
	<td colspan="3" class="noline" style="font-size:11px;">
	<input type="checkbox" name="inflow[naverCheckout]" id="inflow[naverCheckout]" value="naverCheckout" <?=$checked['inflow']['naverCheckout']?> /><label for="inflow[naverCheckout]"><img src="../img/memIcon_naverCheckout.gif" align="absmiddle" alt="네이버 체크아웃" title="네이버 체크아웃" />네이버 체크아웃</label>
	<input type="checkbox" name="inflow[mobileshop]" id="inflow[mobileshop]" value="mobileshop" <?=$checked['inflow']['mobileshop']?> /><label for="inflow[mobileshop]"><img src="../img/memIcon_mobileshop.gif" align="absmiddle" alt="모바일샵" title="모바일샵" />모바일샵</label>
	</td>
	<? } ?>
</tr>
<?if ($_GET['mobileYN'] == "y"){?>
<tr>
	<td colspan="4">생일자 SMS 전송은 금일생일자, SMS 수신, 양력생일, 핸드폰번호 등록고객에 한해서만 전송이 됩니다.</td>
</tr>
<?}?>
</table>

<div style="margin: 3px 0px 0px 3px; color: red;">*정보통신망법에 따라 수신거부한 회원에게는 <strong>광고성 정보</strong>를 발송할 수 없으며, 위반 시 과태료가 부과됩니다.</div>

<? if($popupSearch == "Y"){	# 팝업창에서의 검색이 있는경우 ?>
</div>
<script language="JavaScript" type="text/JavaScript">
function popupDetailDiv(){
	if(document.getElementsByName('popupDetail')[0].checked == true){
		document.getElementById('searchDetail').style.display = 'block';
	}else{
		document.getElementById('searchDetail').style.display = 'none';
	}
}
popupDetailDiv();
</script>
<? } ?>