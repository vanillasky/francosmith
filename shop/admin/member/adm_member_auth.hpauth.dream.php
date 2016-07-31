<?

include '../_header.popup.php';

$dreamsecurity = Core::loader('Dreamsecurity');
$hpauth = Core::loader('Hpauth');

$hpauthConfig = $hpauth->loadConfig();
$hpauthDreamCfg = $hpauth->loadServiceConfig('dream');
$currentHpauthConfig = $hpauth->loadCurrentServiceConfig();
$dreamsecurityPrefix = $dreamsecurity->lookupPrefix();

if (isset($hpauthDreamCfg['useyn']) === false) $hpauthDreamCfg['useyn'] = 'n'; 
if (isset($hpauthDreamCfg['modyn']) === false) $hpauthDreamCfg['modyn'] = 'y';
if (isset($hpauthDreamCfg['moduseyn']) === false) $hpauthDreamCfg['moduseyn'] = 'n';
if (isset($hpauthDreamCfg['minoryn']) === false) $hpauthDreamCfg['minoryn'] = 'n'; 

$checked = array(
    'useyn' => array($hpauthDreamCfg['useyn'] => ' checked="checked"'),
	'modyn' => array($hpauthDreamCfg['modyn'] => ' checked="checked"'),
	'moduseyn' => array($hpauthDreamCfg['moduseyn'] => ' checked="checked"'),
    'minoryn' => array($hpauthDreamCfg['minoryn'] => ' checked="checked"'),
);
?>

<script type="text/javascript">
window.onload = function()
{
	cssRound('MSG01');
	view_tb("<? echo $hpauthDreamCfg['useyn']?>","<? echo ($hpauthDreamCfg['useyn'] == 'y') ? '' : 'none'?>");
	resizeFrame();
};

var IntervarId;
var prevUseyn = "<?php echo $hpauthDreamCfg['useyn']?>";
function resizeFrame()
{

    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;
    oFrame.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}
function checkForm(form) {
	if($('useyn-y').checked) {
		if(!$('cpid').value) {
			alert("회원사 Id를 입력하셔야 사용 가능한 서비스입니다.");
			return false;
		}
	}
	var currentServiceCode = "<?php echo $hpauthConfig['serviceCode']; ?>";
	var currentServiceName = "<?php echo $hpauthConfig['serviceName']; ?>";
	var currentServiceUseyn = "<?php echo $currentHpauthConfig['useyn']; ?>";
	if (form.useyn[0].checked && currentServiceCode !== "dream" && currentServiceUseyn === 'y') {
		return confirm("현재 " + currentServiceName + "에 '사용'으로 설정되어있습니다.\r\n정말 변경하시겠습니까?");
	}
	else {
		return true;
	}
}
function view_tb(val,disp){
	if(!disp && val != prevUseyn){
		document.frmField.modyn[0].checked = true;
		document.frmField.moduseyn[1].checked = true;
	}
	document.getElementById('useyn_tbl').style.display = disp;
	prevUseyn = val;
	resizeFrame();
}
</script>
<style type="text/css">
table.tb {
	width: 100%;
	border-collapse: collapse;
	border-color: #e6e6e6;
	
}
table.tb th {
	width: 160px;
	text-align: left;
	color: #333333;
}
table.tb th, table.tb td {
	padding: 5px;
	border: 1px solid #e6e6e6;
}
</style>

<div class="title title_top">
	드림시큐리티 설정<span>반드시 휴대폰본인인증 서비스 업체와 계약을 맺으신 후 설정하시기 바랍니다.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"/></a>
</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse: collapse; margin-bottom: 20px;" width="730">
	<tr>
		<td style="padding:7px 0px 10px 10px">
			<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>※ [필독] 휴대폰 본인확인서비스(드림시큐리티) 이용절차 안내</b></div>
			<div style="padding-top:10px; color:#666666;" class="g9">
				① <a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[본인확인 인증서비스 > 휴대폰본인확인서비스 안내]</a>
				의 '드림시큐리티'의 신청서를 작성하여 발송합니다.<br/>
				(주)드림시큐리티의 주소 "서울시 송파구 문정동 150-28 서경빌딩 5층 (주)드림시큐리티 고도몰 서비스 담당자 앞"
			</div>
			<div style="padding-top:3px; color:#666666;" class="g9">② 접수 및 승인 후 상담원이 가입 및 상품결제에 대한 안내 전화를 드립니다.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">③ 발급 받으신 회원사Code를 본 페이지 아래 기입란에 입력합니다.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">④ 성인 인증을 사용하는 쇼핑몰의 경우 성인 인증 사용 여부를 본 페이지에 설정하세요.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">⑤ '등록'버튼을 클릭하여 설정을 완료합니다.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">⑥ 쇼핑몰에서 본인확인이 정상 작동 하는지 확인 하세요.</div>
			<div style="padding-top:3px; color:#666666; margin-top: 10px;" class="g9">
				※ 가입 및 이용 문의 : 1899-4134 (주)드림시큐리티<br/>
				신청서는 <a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[본인확인 인증서비스 > 휴대폰본인확인서비스 안내]</a>
				에서  신청서  다운로드 해주시면 됩니다.
			</div>
		</td>
	</tr>
</table>

<form name="frmField" method="post" action="adm_member_auth.hpauth.dream.indb.php" onsubmit="return checkForm(this);" style="margin-top: 10px;">
	<table class="tb">
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>회원사 Code</th>
			<td>
				<input type="text" name="cpid" id="cpid" class="line" value="<?php echo $hpauthDreamCfg['cpid']; ?>"/> 
				<span class="extext">드림시큐리티에서 상점별로 발급되는 아이디 입니다.(<?php echo implode(',', $dreamsecurityPrefix); ?>로 시작되어야 함)</span>
			</td>
		</tr>
		<tr>
			<th style="width:150;">휴대폰 본인 확인 사용 여부</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" id="use_y" value="y" <?php echo $checked['useyn']['y']; ?> onclick="view_tb('y','')">
				<label for="useyn-y" style="margin-right: 10px;">사용</label>
				<input id="useyn-n" type="radio" name="useyn" id="use_n" value="n" <?php echo $checked['useyn']['n']; ?> onclick="view_tb('n','none')">
				<label for="useyn-n">사용안함</label>
			</td>
		</tr>
	</table>
	<table id="useyn_tbl" class="tb">
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th style="border-top:none;">회원 가입 시 휴대폰 번호 수정 여부</th>
			<td class="noline" style="border-top:none;">
				<input id="modyn-y" type="radio" name="modyn" value="y" <?php echo $checked['modyn']['y']; ?>/>
				<label for="modyn-y" style="margin-right: 10px;">가능</label>
				<input id="modyn-n" type="radio" name="modyn" value="n" <?php echo $checked['modyn']['n']; ?>/>
				<label for="modyn-n">불가능</label>
				<div class="extext">회원 가입 시 휴대폰 본인 확인 사용 시에만 휴대폰 번호를 수정 할 수 없도록 설정할 수 있습니다.</div>
			</td>
		</tr>
		<tr>
			<th>회원 휴대폰번호 수정 시 휴대폰본인확인 사용 여부</th>
			<td class="noline">
				<input id="moduseyn-y" type="radio" name="moduseyn" value="y" <?php echo $checked['moduseyn']['y']; ?>/>
				<label for="moduseyn-y" style="margin-right: 10px;">사용</label>
				<input id="moduseyn-n" type="radio" name="moduseyn" value="n" <?php echo $checked['moduseyn']['n']; ?>/>
				<label for="moduseyn-n">사용안함</label>
				<div class="extext">쇼핑몰 > 마이페이지> 회원정보수정 > 휴대폰번호 수정 시 휴대폰 본인확인 사용 여부를 설정합니다.</div>
			</td>
		</tr>
		<tr>
			<th>성인 인증 여부</th>
			<td class="noline">
				<input id="minoryn-y" type="radio" name="minoryn" value="y" <?php echo $checked['minoryn']['y']; ?>>
				<label for="minoryn-y" style="margin-right: 10px;">사용 <font class="extext">(19세 미만 회원가입 불가)</font></label>
				<input id="minoryn-n" type="radio" name="minoryn" value="n" <?php echo $checked['minoryn']['n']; ?>>
				<label for="minoryn-n">사용안함</label>
			</td>
		</tr>
	</table>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">휴대폰 본인확인서비스가 될 수 있도록 프로그램이 기본탑재 되어 있습니다. </td></tr>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">휴대폰 본인확인서비스를 하기 위해서는 (주)드림시큐리티와 계약만 진행하시면 됩니다. </td></tr>
		<tr>
			<td>
				<img src="../img/icon_list.gif" align="absmiddle">휴대폰 본인확인서비스 제공업체:
				<a href="http://www.dreamsecurity.com" target="_new" style="color: #ffffff;">(주)드림시큐리티 <span class="ver7">(http://www.dreamsecurity.com)</span></a>
			</td>
		</tr>
	</table>
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr><td height="20"></td></tr>
		<tr><td><font class="def1" color="white">[필독] 휴대폰 본인확인서비스 절차 </b></font></td></tr>
		<tr><td><font class="def1" color="white">①</font> 휴대폰 본인확인 서비스를 제공하는 (주)드림시큐리티의 신청서를 작성하여 발송합니다.</td></tr>
		<tr><td>(주)드림시큐리티의 주소 "서울시 송파구 문정동 150-28 서경빌딩 5층 (주)드림시큐리티 고도몰 서비스 담당자 앞"</td></tr>
		<tr><td><font class="def1" color="white">②</font> (주)드림시큐리티의 담당자로부터 회원사Code를 발급 받으시게 됩니다.</td></tr>
		<tr><td><font class="def1" color="white">③</font> 발급 받으신 회원사Code를 본 페이지에 입력하세요.</td></tr>
		<tr><td><font class="def1" color="white">④</font> 쇼핑몰에서 본인확인이 정상 작동 하는지 확인 하세요.</td></tr>
	</table>
</div>