<?php

include "../_header.popup.php";

$hpauth = Core::loader('Hpauth');

$hpauthConfig = $hpauth->loadConfig();
$mcertiConfig = $hpauth->loadServiceConfig('mcerti');
$currentHpauthConfig = $hpauth->loadCurrentServiceConfig();

if (strlen($mcertiConfig['cpid']) < 1) $disabled = true;

if (isset($mcertiConfig['useyn']) === false) $mcertiConfig['useyn'] = 'n'; 
if (isset($mcertiConfig['modyn']) === false) $mcertiConfig['modyn'] = 'y';
if (isset($mcertiConfig['moduseyn']) === false) $mcertiConfig['moduseyn'] = 'n';
if (isset($mcertiConfig['minoryn']) === false) $mcertiConfig['minoryn'] = 'n'; 

$checked = array(
    'useyn' => array($mcertiConfig['useyn'] => ' checked="checked"'),
    'modyn' => array($mcertiConfig['modyn'] => ' checked="checked"'),
	'moduseyn' => array($mcertiConfig['moduseyn'] => ' checked="checked"'),
	'minoryn' => array($mcertiConfig['minoryn'] => ' checked="checked"'),
);
?>
<script type="text/javascript">
window.onload = function()
{
	cssRound('MSG01');
	view_tb("<? echo $mcertiConfig['useyn']?>","<? echo ($mcertiConfig['useyn'] == 'y') ? '' : 'none'?>");
	resizeFrame();
};

var IntervarId;
var prevUseyn = "<?php echo $mcertiConfig['useyn']?>";
function resizeFrame()
{
    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;
    oFrame.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}
function checkForm(form)
{
	var currentServiceCode = "<?php echo $hpauthConfig['serviceCode']; ?>";
	var currentServiceName = "<?php echo $hpauthConfig['serviceName']; ?>";
	var currentServiceUseyn = "<?php echo $currentHpauthConfig['useyn']; ?>";
	if (form.useyn[0].checked && currentServiceCode !== "mcerti" && currentServiceUseyn === 'y') {
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
	padding: 8px;
	border: 1px solid #e6e6e6;
}
</style>
<div class="title title_top">
	Mcerti 설정
	<span>반드시 휴대폰본인인증 서비스 업체와 계약을 맺으신 후 설정하시기 바랍니다.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse: collapse; margin-bottom: 20px;" width="740">
	<tr>
		<td style="padding:7px 0px 10px 10px">
			<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>※ [필독] 휴대폰 본인확인서비스(Mcerti) 이용절차 안내</b></div>
			<div style="padding-top:10px; color:#666666;" class="g9">①
				<a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[본인확인 인증서비스 > 휴대폰본인확인서비스 안내]</a>의
				'Mcerti' 서비스를 온라인 신청합니다.
			</div>
			<div style="padding-top:3px; color:#666666;" class="g9">② 이메일 또는 SMS를 통해 서비스 신청 결과를 확인합니다.<span style="color: #ff0000;">(영업일 기준으로 약 3~4일 정도 소요됨)</span></div>
			<div style="padding-top:3px; color:#666666;" class="g9">③ 본 페이지의 'Mcerti'사용 탭에서 '휴대폰 본인 확인 사용 여부'를 '사용'으로 설정하세요</div>
			<div style="padding-top:3px; color:#666666;" class="g9">④ 성인 인증을 사용하는 쇼핑몰의 경우 성인 인증 사용 여부를 본 페이지에 설정하세요.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">⑤ '등록'버튼을 클릭하여 설정을 완료합니다.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">⑥ 쇼핑몰에서 본인확인이 정상 작동 하는지 확인 하세요.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">
				⑦ 이용기간 연장 및 요금결제는
				<a href="http://www.godo.co.kr/mygodo/certification_list.php" target="_blank" style="font-weight: bold; color: #627dce;">[마이고도 > 쇼핑몰관리 > 부가서비스신청관리 > 휴대폰 본인인증]</a>
				을 이용하시기 바랍니다.
			</div>
		</td>
	</tr>
</table>

<form name="frmField" action="adm_member_auth.hpauth.mcerti.indb.php" method="post" onsubmit="return checkForm(this);" <?php echo $disabled ? 'disabled="disabled"' : ''; ?>>
	<table class="tb"<?php echo $disabled; ?>>
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>회원사Code(사용승인여부)</th>
			<td id="service-id">
				<?php if (strlen($mcertiConfig['cpid']) < 1) { ?>
				<span style="color: #ff0000; font-weight: bold;">미승인</span>
				<?php } else { ?>
				<?php echo $mcertiConfig['cpid']; ?> <span style="color: #00AA00; font-weight: bold;">(승인)</span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th>휴대폰 본인 확인 사용 여부</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" value="y" <?php echo $checked['useyn']['y']; ?> onclick="view_tb('y','')"/>
				<label for="useyn-y" style="margin-right: 10px;">사용</label>
				<input id="useyn-n" type="radio" name="useyn" value="n" <?php echo $checked['useyn']['n']; ?> onclick="view_tb('n','none')"/>
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
			<th>성인 인증 사용 여부</th>
			<td class="noline">
				<input id="minoryn-y" type="radio" name="minoryn" value="y" <?php echo $checked['minoryn']['y']; ?>/>
				<label for="minoryn-y" style="margin-right: 10px;">사용 <font class="extext">(19세 미만 회원가입 불가)</font></label>
				<input id="minoryn-n" type="radio" name="minoryn" value="n" <?php echo $checked['minoryn']['n']; ?>/>
				<label for="minoryn-n">사용안함</label>
			</td>
		</tr>
	</table>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<div>
					<img src="../img/icon_list.gif" align="absmiddle"> 휴대폰 본인확인서비스가 될 수 있도록 프로그램이 기본탑재 되어 있습니다.
				</div>
				<div>
					<img src="../img/icon_list.gif" align="absmiddle"> 휴대폰 본인확인서비스를 제공하는 업체 중 적합한 업체를 선택하여 신청(또는 계약) 후 이용하실 수 있습니다
				</div>
			</td>
		</tr>
	</table>
</div>