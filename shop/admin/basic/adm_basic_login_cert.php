<?

$location = '기본관리 > 관리자보안 설정';
include '../_header.php';
@include '../../conf/config.admin_login_cert.php';

### 그룹명 가져오기
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$res = $db->query("select aoc.aoc_sno, aoc.aoc_mobile, mb.m_id, mb.name, mb.level, mb.dormant_regDate from gd_admin_otp_contact as aoc left join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no order by aoc_regdt asc");
?>
<script>
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function del(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('정말로 삭제 하시겠습니까?')) return;
	fm.target = "_self";
	fm.mode.value = "delContact";
	fm.action = "indb.login_cert.php";
	fm.submit();
}
</script>

<div class="title title_top">휴대폰 인증 설정<span>관리자 페이지 로그인 시 등록된 휴대폰으로 인증 후 접근이 가능합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=40')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<!-- 안내 : Start -->
<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin-bottom:20px; color:#666666; padding:10px 0 10px 10px;">
	<div class="g9" style="color:#0074BA"><b>* 휴대폰 인증 설정이란?</b></div>
	<div style="padding-top:7px;">쇼핑몰 관리자 페이지 로그인 시 아래 <b>'인증 휴대폰 관리'</b> 에 등록해 놓은 휴대폰 번호로 인증번호를 전송 받은 후 번호 인증을 통하여 로그인 할 수 있도록 해주는 관리자 보안 강화 기능입니다.</div>
</div>
<!-- 안내 : End -->

<!-- 휴대폰 인증 설정 : Start -->
<form method="post" action="indb.login_cert.php">
<table class="tb">
<input type="hidden" name="mode" value="setAdminLoginCert">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>사용 여부</td>
	<td class="noline">
	<input type="radio" name="use" value="Y" <?if($admLoginCertCfg['use'] == 'Y')echo"checked";?>> 사용함
	<input type="radio" name="use" value="N" <?if($admLoginCertCfg['use'] != 'Y')echo"checked";?>> 사용안함
	</td>
</tr>
</table>
<div class="button" style="margin:10px;" align="center"><input type="image" src="../img/btn_save.gif"></div>
</form>
<!-- 휴대폰 인증 설정 : End -->

<!-- 목록 : Start -->
<div class="pdv10" align="right" style="padding:0 5px 5px 0"><a href="javascript:popupLayer('adm_popup_login_cert_regit.php',500,450)"><img src="../img/i_add.gif" border=0></a></div>
<form name="fmList" method="post">
<input type="hidden" name="mode" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="6"></td></tr>
<tr class="rndbg">
	<th>선택</th>
	<th>번호</th>
	<th>인증 휴대폰번호</th>
	<th>매칭관리자ID</th>
	<th>이름</th>
	<th>그룹</th>
</tr>
<tr><td class="rnd" colspan="6"></td></tr>
<col width="30" align="center">
<col width="30" align="center">
<?
while ($data=$db->fetch($res)){
	if($data['dormant_regDate'] != '0000-00-00 00:00:00'){
		$data['m_id'] = '(휴면회원)';
	}
?>
<tr height="40" align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['aoc_sno']?>" onclick="iciSelect(this);"></td>
	<td width="50" class="ver8"><?=++$idx?></td>
	<td><?=$data['aoc_mobile']?></td>
	<td><?=$data['m_id']?></td>
	<td><?=$data['name']?></td>
	<td><?=$r_grp[$data['level']]?></td>
</tr>
<tr><td colspan="6" class="rndline"></td></tr>
<? } ?>
</table>

<div style="height:35px; padding:5px 0 0 13px"><a href="javascript:del(document.fmList);"><img src="../img/btn_select_delete.gif" border="0" /></a></div>
</form>
<!-- 목록 : End -->

<!-- 잔여 SMS 포인트 : Start -->
<div style="padding-top:20px"></div>

<div style="border:solid 4px #dce1e1; border-collapse:collapse; color:#666666; padding:10px 0 10px 10px;">
	<table width="100%">
	<tr>
		<td>
		<? $sms = Core::loader('Sms');?>
		잔여 SMS 포인트 : <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> 건
		</td>
		<td>
		<div style="padding-top:7px; color:#666666" class="g9">SMS 포인트가 없는 경우 인증번호 SMS가 발송되지 않으므로 휴대폰 인증 기능이 작동하지 않습니다.</div>
		<div style="padding-top:5px; color:#666666" class="g9">SMS 포인트 충전 후 이용하시기 바랍니다.</div>
		</td>
		<td>
		<a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif" /></a>
		</td>
	</tr>
	</table>
</div>
<!-- 잔여 SMS 포인트 : End -->

<!-- 궁금증 해결 : Start -->
<div style="padding-top:30px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관리자 휴대폰 인증 기능을 사용하기 위해서는 SMS포인트가 필요하며, 인증번호 요청 시 1포인트가 소진됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">매칭관리자ID : 등록한 휴대폰 번호의 매칭된 관리자ID를 표시하며, 휴대폰 등록 시 관리자ID 매칭은 필수입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관리자ID : 관리자권한그룹으로 등록된 ID만 관리자ID로 사용할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<!-- 궁금증 해결 : End -->

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>