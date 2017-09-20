<?
$data = $db->fetch("select MB.*, SC.category from ".GD_MEMBER." AS MB LEFT JOIN ".GD_TODAYSHOP_SUBSCRIBE." AS SC ON MB.m_id = SC.m_id where MB.m_id='".$_GET['m_id']."'");

$zipcode	= explode("-",$data['zipcode']);
$phone		= explode("-",$data['phone']);
$mobile		= explode("-",$data['mobile']);
$fax		= explode("-",$data['fax']);

foreach( codeitem('like') as $k => $v ){
	if ($data['interest']&pow(2,$k)) $checked['interest'][$k] = "checked";
}

include "../../conf/fieldset.php";
foreach ($checked['reqField'] as $k=>$v) $required[$k] = "required";

// 회원가입 유입 경로 아이콘
$memIcon_inflow = ($data['inflow']) ? " <img src=\"../img/memIcon_".$data['inflow'].".gif\" align=\"absmiddle\" />" : "";

//SMS 발송 실패 여부
$smsFailCheck = smsFailCheck('single', $data['mobile']);
?>

<script language="javascript"><!--
function chkFormMember( fobj ){

	if ( fobj['mod_pass'].checked ){

		if ( fobj['password'].value == '' ){
			alert( "[비밀번호] 필수입력사항" );
			fobj['password'].focus();
			return false;
		}

		if ( fobj['password'].value != fobj['password2'].value ){
			alert( "[새비밀번호]와 [비밀번호확인] 데이타가 다릅니다." );
			fobj['password'].value = fobj['password2'].value = '';
			fobj['password'].focus();
			return false;
		}
	}

	if ( !chkForm(fobj) ) return false;
}
--></script>

<form name="frmMember" method="post" action="<?=$sitelink->link('admin/member/indb.php','ssl');?>" onsubmit="return chkFormMember(this);">
<input type="hidden" name="mode" value="modify" />
<?if ($crm_view == true){?><input type="hidden" name="crmyn" value="Y"><?}?>
<input type="hidden" name="m_id" value="<?=$_GET['m_id']?>" />
<input type="hidden" name="returnUrl" value="<?=$returnUrl?>" />

<div class="title title_top">회원정보</div>

<table class="tb">
<col class="cellC" /><col style="padding-left:10px;width:250;" />
<col class="cellC" /><col style="padding-left:10px" />
<tr>
	<td>아이디</td>
	<td>
		<b><?=$data['m_id']?></b>
		<?=$memIcon_inflow?>
		<?php if (strlen($data['connected_sns']) > 0) { foreach(explode(',', $data['connected_sns']) as $socialCode) { ?>
		<img src="../img/ico_member_<?php echo strtolower($socialCode); ?>.gif" style="vertical-align: middle; margin: 0;"/>
		<?php }} ?>
	</td>
	<td>승인</td>
	<td class="noline">
	<input type="radio" name="status" value="1" <?=( "1" == $data[status] ? 'checked' : '' )?> /> 승인
	<input type="radio" name="status" value="0" <?=( "1" != $data[status] ? 'checked' : '' )?> /> 미승인
	</td>
</tr>
<tr>
	<td>이름</td>
	<td><input type="text" name="name" value="<?=$data[name]?>" required label="이름" class="line" /></td>
	<td>그룹</td>
	<td>
<?
$garr = member_grp();
if($data['level']  > 79 && $sess['level'] < 100){?>
	<input type="hidden" name="level" value="<?=$data['level']?>" />
	<?foreach( $garr as $v )if($v['level'] == $data['level'])echo $v['grpnm'];?>
<?}else{?>
	<select name="level" required>
	<option value="">↓그룹선택</option>
	<?
	foreach( $garr as $v ){
		if($sess['level'] == 100 || $v['level'] < 80) echo '<option value="' . $v['level'] . '"' . ( $v['level'] == $data['level'] ? 'selected' : '' ) . '>' . $v['grpnm'] . '</option>' . "\n";
	}
	?>
	</select>
<?}?>
<?
$inGroup = false;
foreach($garr as $val){ if($val['level'] == $data['level']) $inGroup = true;}
if($inGroup === false) echo "<span style='color:#ff0000;font-weight:bold'>※ 속한 그룹이 없음!</span>";
?>
	</td>
</tr>
<tr>
	<td>닉네임</td>
	<td colspan="3"><input type="text" name="nickname" value="<?=$data[nickname]?>" <?=$required['nickname']?> label="닉네임" class="line" /></td>
</tr>
<tr>
	<td>비밀번호</td>
	<td colspan="3">
	<div style="float:left;" class="noline"><input type="checkbox" name="mod_pass" value="Y" onclick="openLayer('pass');" class="line" /> 변경</div>
	<div style="float:left;margin-left:10;display:none;" id="pass">
	새비밀번호 : <input type="password" name="password" class="line" /> &nbsp;&nbsp;
	비밀번호확인 : <input type="password" name="password2" class="line" />
	</div>
	</td>
</tr>
<tr>
	<td>성별</td>
	<td>
	<div style="float:left;" class="noline"><input type="checkbox" name="mod_sex" value="Y" onclick="openLayer('sex');" /> 변경</div>
	<div style="float:left;margin-left:10;padding-top:3px;"><?=$data['sex'] == 'm' ? '남자' : '여자'?></div>
	<div style="float:left;margin-left:10;display:none;" class="noline" id="sex">
	<input type="radio" name="sex" value="m" /> 남자
	<input type="radio" name="sex" value="w" /> 여자
	</div>
	</td>
	<td>생년월일</td>
	<td>
	<input type="text" name="birth_year" value="<?=$data[birth_year]?>" size="4" maxlength="4" <?=$required['birth']?> class="line" />년
	<input type="text" name="birth[]" value="<?=substr($data['birth'],0,2)?>" size="2" maxlength="2" <?=$required['birth']?> class="line" />월
	<input type="text" name="birth[]" value="<?=substr($data['birth'],2)?>" size="2" maxlength="2" <?=$required['birth']?> class="line" />일

	<span class="noline">( <input type="radio" name="calendar" value="s" <?=( "s" == $data['calendar'] ? 'checked' : '' )?> /> 양
	<input type="radio" name="calendar" value="l" <?=( "l" == $data['calendar'] ? 'checked' : '' )?> /> 음 )</span>
	</td>
</tr>
<tr>
	<td>이메일</td>
	<td colspan="3"><input type="text" name="email" value="<?=$data[email]?>" size=50 <?=$required['email']?> label="이메일" class="line" />
	<span class="noline">( <input type="radio" name="mailling" value="y" <?=( "y" == $data['mailling'] ? 'checked' : '' )?> /> 메일링 받음
	<input type="radio" name="mailling" value="n" <?=( "n" == $data['mailling'] ? 'checked' : '' )?> /> 메일링 거부 )</span>
	<div style="color: #0074BA; margin-top: 3px;"> *수신동의설정 안내메일의 자동발송여부에 따라 회원정보의 수신동의설정 변경 시 해당 회원에게 안내메일이 자동 발송됩니다.</div>
	</td>
</tr>
<tr>
	<td>주소</td>
	<td colspan="3">

	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<input type="text" name="zonecode" id="zonecode" size="5" readonly value="<?=$data['zonecode']?>" class="line" />
		( <input type="text" name="zipcode[]" id="zipcode0" size="3" readonly value="<?=$zipcode[0]?>" class="line" /> -
		<input type="text" name="zipcode[]" id="zipcode1" size="3" readonly value="<?=$zipcode[1]?>" class="line" /> )
		<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a>
		</td>
	</tr>
	<tr>
		<td>
		<input type="text" name="address" id="address" value="<?=$data['address']?>" readonly size="50" <?=$required['address']?> label="주소" class="line" />
		<input type="text" name="address_sub" id="address_sub" value="<?=$data['address_sub']?>" size="30" label="주소" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" class="line" /><br/>
		<input type="hidden" name="road_address" id="road_address" value="<?=$data['road_address']?>">
		<div style="padding:5px 5px 0 5px;font:12px dotum;color:#999;float:left;" id="div_road_address"><?=$data['road_address']?></div>
		<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><? if ($data['road_address']) { echo $data['address_sub']; } ?></div>
		</td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<td>핸드폰</td>
	<td colspan="3">
	<input type="text" name="mobile[]" value="<?=$mobile[0]?>" size="4" maxlength="4" <?=$required['mobile']?> label="핸드폰" class="line" /> -
	<input type="text" name="mobile[]" value="<?=$mobile[1]?>" size="4" maxlength="4" <?=$required['mobile']?> label="핸드폰" class="line" /> -
	<input type="text" name="mobile[]" value="<?=$mobile[2]?>" size="4" maxlength="4" <?=$required['mobile']?> label="핸드폰" class="line" />
	<span class="noline">( <input type="radio" name="sms" value="y" <?=( "y" == $data['sms'] ? 'checked' : '' )?> /> SMS 받음
	<input type="radio" name="sms" value="n" <?=( "n" == $data['sms'] ? 'checked' : '' )?> /> SMS 거부 )</span>
	<?php if($smsFailCheck === true){ ?>
	<script type="text/javascript" src="../godo_ui.js"></script>
	<style>div.tooltip {width:260px;padding:0;margin:0;}</style>
	<br /><br /><font color="red"> SMS 발송실패 번호</font> <img src="../img/icons/icon_qmark.gif" style="vertical-align:bottom; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="<span style=&quot;color: red;&quot;>SMS 발송실패번호</span>는 &quot;잘못된 전화번호&quot; 등의 사유로 SMS 발송실패 이력이 있는 번호입니다."> &nbsp;
	<?php } ?>
	<img src="../img/btn_sms_sendinfo.gif" style="vertical-align:middle; cursor:pointer; border: 0px;" onclick="javascript:popup('./popup.sms.sendView.php?sms_phoneNumber=<?php echo $data['mobile']; ?>', '700', '500');" />
	<div style="color: #0074BA; margin-top: 3px;"> *수신동의설정 안내메일의 자동발송여부에 따라 회원정보의 수신동의설정 변경 시 해당 회원에게 안내메일이 자동 발송됩니다.</div>
	</td>
</tr>
<tr>
	<td>전화번호</td>
	<td>
	<input type="text" name="phone[]" value="<?=$phone[0]?>" size="4" maxlength="4" <?=$required['phone']?> label="전화번호" class="line" /> -
	<input type="text" name="phone[]" value="<?=$phone[1]?>" size="4" maxlength="4" <?=$required['phone']?> label="전화번호" class="line" /> -
	<input type="text" name="phone[]" value="<?=$phone[2]?>" size="4" maxlength="4" <?=$required['phone']?> label="전화번호" class="line" />
	</td>
	<td>팩스번호</td>
	<td>
	<input type="text" name="fax[]" value="<?=$fax[0]?>" size="4" maxlength="4" <?=$required['fax']?> label="팩스" class="line" /> -
	<input type="text" name="fax[]" value="<?=$fax[1]?>" size="4" maxlength="4" <?=$required['fax']?> label="팩스" class="line" /> -
	<input type="text" name="fax[]" value="<?=$fax[2]?>" size="4" maxlength="4" <?=$required['fax']?> label="팩스" class="line" />
	</td>
</tr>
<tr>
	<td>회사명</td>
	<td><input type="text" name="company" value="<?=$data[company]?>" class="line" /></td>
	<td>업태</td>
	<td><input type="text" name="service" value="<?=$data[service]?>" class="line" /></td>
</tr>
<tr>
	<td>사업자번호</td>
	<td><input type="text" name="busino" value="<?=$data[busino]?>" class="line" /></td>
	<td>종목</td>
	<td><input type="text" name="item" value="<?=$data[item]?>" class="line" /></td>
</tr>
<tr>
	<td>직업</td>
	<td>
	<select name="job">
	<option value="">↓직업선택</option>
	<?
	foreach( codeitem('job') as $k => $v ){
		echo '<option value="' . $k . '"' . ( $k == $data['job'] ? 'selected' : '' ) . '>' . $v . '</option>' . "\n";
	}
	?>
	</select>
	</td>
	<td>결혼기념일</td>
	<td>
	<div style="float:left;" class="noline">
	<input type="radio" name="marriyn" value="n" <?=( "n" == $data['marriyn'] ? 'checked' : '' )?> onclick="openLayer('marri','none')" /> 미혼
	<input type="radio" name="marriyn" value="y" <?=( "y" == $data['marriyn'] ? 'checked' : '' )?> onclick="openLayer('marri','block')" /> 기혼
	</div>
	<div style="float:left;margin-left:5;display:none;" id="marri">
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],0,4)?>" size="4" maxlength="4" />년
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],4,2)?>" size="2" maxlength="2" />월
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],6,2)?>" size="2" maxlength="2" />일
	</div>
	<script>if( frmMember.marriyn[1].checked ) openLayer('marri','block');</script>
	</td>
</tr>
<tr>
	<td>관심분야<br><a href="../data/data_code.php?sgroupcd=like" target="_new"><img src="../img/btn_editinterest.gif" vspace="2" /></a></td>
	<td colspan="3" class="noline">
	<table><tr>
	<? $idx=0; foreach( codeitem('like') as $k => $v ){ ?>
	<td nowrap><input type="checkbox" name="interest[]" value="<?=$k?>" <?=$checked['interest'][$k]?>> <?=$v?></td>
	<? if (++$idx%4==0){ ?></tr><tr><? } ?>
	<? } ?>
	</tr></table>

	</td>
</tr>
<tr>
	<td>관심분류</td>
	<td colspan="3" class="noline">
	<?
	$todayshop = Core::loader('todayshop');
	$ts_category_all = $todayshop->getCategory(true);
	?>
	<select name="interest_category">
	<option value=""> - 관심분류를 선택해 주세요 - </option>
	<? foreach ($ts_category_all as $v) { ?>
	<option value="<?=$v['category']?>" <?=$v['category'] == $data['category'] ? 'selected' : ''?>><?=$v['catnm']?></option>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>남기는 말씀</td>
	<td colspan="3"><textarea name="memo" style="width:100%;height:80px" class="tline"><?=$data['memo']?></textarea></td>
</tr>
<tr>
	<td title="추가정보1"><?=( $joinset['ex1'] ? $joinset['ex1'] : '추가정보1' )?></td>
	<td><input type="text" name="ex1" value="<?=$data['ex1']?>" style="width:90%" class="line" /></td>
	<td title="추가정보4"><?=( $joinset['ex4'] ? $joinset['ex4'] : '추가정보4' )?></td>
	<td><input type="text" name="ex4" value="<?=$data['ex4']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td title="추가정보2"><?=( $joinset['ex2'] ? $joinset['ex2'] : '추가정보2' )?></td>
	<td><input type="text" name="ex2" value="<?=$data['ex2']?>" style="width:90%" class="line" /></td>
	<td title="추가정보5"><?=( $joinset['ex5'] ? $joinset['ex5'] : '추가정보5' )?></td>
	<td><input type="text" name="ex5" value="<?=$data['ex5']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td title="추가정보3"><?=( $joinset['ex3'] ? $joinset['ex3'] : '추가정보3' )?></td>
	<td><input type="text" name="ex3" value="<?=$data['ex3']?>" style="width:90%" class="line" /></td>
	<td title="추가정보6"><?=( $joinset['ex6'] ? $joinset['ex6'] : '추가정보6' )?></td>
	<td><input type="text" name="ex6" value="<?=$data['ex6']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td>추천인</td>
	<td colspan="3"><input type="text" name="recommid" value="<?=$data['recommid']?>" class="line" /> <?if( $data['recommid'] ){?>&nbsp;&nbsp; <a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$data['recommid']?>',780,600)" style="color:#616161;" class="ver8">[정보보기]</a><?}?></td>
</tr>
<tr>
	<td>개인정보취급방침</td>
	<td colspan="3">
	<font class="ver8">이용자동의:<?=( $data['private1'] == "y" ? '<font color=0074BA>동의함</font>' : '<font color=EA0095>동의안함</font>' )?></font>
	<?if($cfg['private2YN'] == "Y"){?> , <font class="ver8">제3자제공:<?=( $data['private2'] == "y" ? '<font color=0074BA>동의함</font>' : '<font color=EA0095>동의안함</font>' )?></font><?}?>
	<?if($cfg['private3YN'] == "Y"){?> , <font class="ver8">위탁관련:<?=( $data['private3'] == "y" ? '<font color=0074BA>동의함</font>' : '<font color=EA0095>동의안함</font>' )?></font><?}?>
	(※ 해당부분은 관리자가 임의 수정이 불가능합니다.)
	</td>
</tr>
<?
// 추가동의항목내용
$result = $db->query("SELECT * FROM ".GD_CONSENT." WHERE useyn = 'y' ORDER BY sno");
$consentCnt = $db->count_($result);

if ($consentCnt > 0){
?>
<tr>
	<td>추가항목 동의여부</td>
	<td colspan="3">
	<?
	while($consent = $db->fetch($result)){
		list($consentyn) = $db->fetch("SELECT consentyn FROM ".GD_MEMBER_CONSENT." WHERE m_no = '".$data['m_no']."' AND consent_sno = '".$consent['sno']."'");
	?>
		<div><font class="ver8"><?echo $consent['title']?>: <?=( $consentyn == "y" ? '<font color=0074BA class=ver811>동의함</font>' : '<font color=EA0095>동의안함</font>' )?></font></div>
	<? } ?>
	</td>
</tr>
<? } ?>
<tr>
	<td>회원가입일</td>
	<td><font class="ver8"><?=$data['regdt']?></font></td>
	<td>적립금</td>
	<td><?=number_format($data['emoney'])?> 원<?if ($crm_view != true){?> &nbsp;&nbsp; <a href="javascript:popupLayer('../member/popup.emoney.php?m_no=<?=$data['m_no']?>',600,500)"><img src="../img/btn_detailview.gif" align="absmiddle" /></a><?}?></td>
</tr>
<tr>
	<td>최종로그인</td>
	<td><font class="ver8"><?=$data['last_login']?> &nbsp;&nbsp; 방문 <?=number_format($data['cnt_login'])?> 회</font></td>
	<td>최종로그인IP</td>
	<td><?=$data['last_login_ip']?></td>
</tr>
<tr>
	<td>최종구매일</td>
	<td><font class="ver8"><?=$data['last_sale']?></font></td>
	<td>구입금액</td>
	<td><?=number_format( $data['sum_sale'] )?> 원 &nbsp;&nbsp; 주문 <?=number_format($data['cnt_sale'])?> 건<?if ($crm_view != true){?> &nbsp;&nbsp; <a href="javascript:popup('../member/orderlist.php?m_no=<?=$data['m_no']?>',500,600)"><img src="../img/btn_detailview.gif" align="absmiddle" /></a><?}?></td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_modify.gif" />
<?if ($crm_view != true){?><a href='<?=$listUrl?>'><img src="../img/btn_list.gif" /></a><?}?>
</div>

</form>