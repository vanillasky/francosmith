<?php
/*
 * IP 접속 제한 설정 (관리자 IP 접속 제한, 쇼핑몰 IP 접속 제한 설정)
 * @author artherot @ godosoft development team.
 */

// 네비게이션
$location	= '기본관리 > IP접속제한 설정';

// 상단 호출
include '../_header.php';

// IP 접속 제한 설정값
$adminAccessIP			= $IPAccessRestriction->getAdminAccessIP();
$userAccessIP			= $IPAccessRestriction->getUserAccessIP();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function()
{
	// 관리자 접속 IP 모두 허용 체크시
	jQuery("#admin-ip-access-none").click(function(){
		ipAccessConfigToggle('admin', 'none');
	});

	// 관리자 접속 IP 제한 (등록된 IP만 접속 가능) 체크시
	jQuery("#admin-ip-access-default").click(function(){
		ipAccessConfigToggle('admin', 'default');
	});

	// 대역지정하기 체크시 (관리자)
	jQuery("#admin-ip-access-band-check").click(function(){
		ipAccessBandToggle('admin');
	});

	// 현재접속 IP 적용 클릭시 (관리자)
	jQuery("#admin-ip-apply").click(function(){
		var admin_ip_address	= jQuery("#admin-ip-address").text().split('.');
		for (var i = 0; i < 4; i++) {
			jQuery("#admin_access_ip_class"+i).val(admin_ip_address[i]);
		}
	});

	// 추가 버튼을 누른 경우 (관리자)
	jQuery("#admin-ip-add").click(function(){
		addIPAddress('admin');
	});

	// 쇼핑몰 접속 IP 모두 허용 체크시
	jQuery("#user-ip-access-none").click(function(){
		ipAccessConfigToggle('user', 'none');
	});

	// 쇼핑몰 접속 IP 제한 (등록된 IP만 접속 가능) 체크시
	jQuery("#user-ip-access-default").click(function(){
		ipAccessConfigToggle('user', 'default');
	});

	// 대역지정하기 체크시 (쇼핑몰)
	jQuery("#user-ip-access-band-check").click(function(){
		ipAccessBandToggle('user');
	});

	// 추가 버튼을 누른 경우 (쇼핑몰)
	jQuery("#user-ip-add").click(function(){
		addIPAddress('user');
	});

	// 관리자 IP접속제한 설정 체크
	if (jQuery('input[name=\'set_ip_permit\']:checked').val() == '0') {
		ipAccessConfigToggle('admin', 'none');
	}

	// 쇼핑몰 IP접속제한 설정 체크
	if (jQuery('input[name=\'user_ip_access_restriction\']:checked').val() == 'N') {
		ipAccessConfigToggle('user', 'none');
	}

	restrictionPageToggle('<?php echo $userAccessIP['restriction_page'];?>');
});

/**
 * 허용 / 지정 토글
 * @param string thisMode 모드 (admin , user)
 * @param string thisSwitch 처리 (none , default)
 */
function ipAccessConfigToggle(thisMode, thisSwitch)
{
	if (thisSwitch == 'none') {
		jQuery("#"+thisMode+"-ip-access-form").addClass("none").removeClass("default");
	} else {
		jQuery("#"+thisMode+"-ip-access-form").removeClass("none").addClass("default");
	}
}

/**
 * ip 대역 지정하기 토글
 * @param string thisMode 모드 (admin , user)
 */
function ipAccessBandToggle(thisMode)
{
	var fieldID		= thisMode + "-ip-access-band";
	if (jQuery("input[id='"+fieldID+"-check']:checked").length == 1) {
		jQuery("#"+fieldID).show();
	} else {
		jQuery("#"+fieldID).hide();
	}
}

/**
 * ip 추가하기
 * @param string thisMode 모드 (admin , user)
 */
function addIPAddress(thisMode)
{
	var fieldID		= thisMode + "_access_ip_class";
	var Aclass		= jQuery("#"+fieldID+"0").val();
	var Bclass		= jQuery("#"+fieldID+"1").val();
	var Cclass		= jQuery("#"+fieldID+"2").val();
	var Dclass		= jQuery("#"+fieldID+"3").val();
	var Dclass_band	= jQuery("#"+fieldID+"_band").val();

	// C class 까지 입력하지 않을경우 경고
	if(!Aclass || !Bclass || !Cclass) {
		alert("정확한 IP를 입력해주세요");
		if (!Aclass) {
			jQuery("#"+fieldID+"0").focus();
		} else if (!Bclass) {
			jQuery("#"+fieldID+"1").focus();
		} else if (!Cclass) {
			jQuery("#"+fieldID+"2").focus();
		}
		return false;
	}

	// D class 입력하지 않은경우 대역으로 변경
	if (!Dclass && !Dclass_band) {
		// 대역 지정하기 체크
		jQuery("input[id='"+thisMode+"-ip-access-band-check']").attr("checked", "checked");

		// ip 대역 지정하기 토글 처리
		ipAccessBandToggle(thisMode);

		// D class 와 대역에 입력하기
		jQuery("#"+fieldID+"3").val('0');
		Dclass		= '0';
		jQuery("#"+fieldID+"_band").val('255');
		Dclass_band	= '255';
	}

	// D class 입력하지 않고 대역만 입력한경우 D class 를 0으로 처리
	if (!Dclass && Dclass_band != '') {
		jQuery("#"+fieldID+"3").val('0');
		Dclass	= '0';
	}

	// D class 가 대역보타 큰경우 경고
	if (Dclass !='' && Dclass_band != '') {
		if (Dclass >= Dclass_band) {
			alert("정확한 IP 대역을 입력해주세요");
			return false;
		}
	}

	// 추가할 IP 주소
	var ipAddress	= Aclass+'.'+Bclass+'.'+Cclass+'.'+Dclass;
	if (Dclass_band != '') {
		ipAddress	= ipAddress+'~'+Dclass_band;
	}

	// 최대 등록 개수
	if (thisMode == 'admin') {
		var maxLimit	= 10;
	} else {
		var maxLimit	= 100;
	}

	// ID 체크 및 등록된 개수 추출
	var addListID	= thisMode+'_ip_address_list';
	var addFieldID	= thisMode+'_ip_no_';
	var addTextID	= thisMode+'_ip_text_';
	var addInputID	= thisMode+'_ip_address';
	var fieldNoChk	= 0;
	if (typeof jQuery('#'+addListID).find('div:last').get(0) != 'undefined') {
		fieldNoChk	= jQuery('#'+addListID).find('div:last').get(0).id.replace(addFieldID,'');
	}
	var fieldNo		= parseInt(fieldNoChk) + 1;

	// 등록개수 체크
	if (maxLimit < fieldNo) {
		alert('최대 '+maxLimit+'개 까지만 등록이 가능 합니다.');
		return false;
	}

	// 추가할 HTML 내용
	var addHtml		= '';
	addHtml	+= '<div id="'+addFieldID+fieldNo+'" style="padding:4px 0px;">';
	addHtml	+= '<span id="'+addTextID+fieldNo+'">'+fieldNo+' : '+ipAddress+'</span>&nbsp;&nbsp;&nbsp;';
	addHtml	+= '<input type="hidden" id="'+addInputID+fieldNo+'" name="'+addInputID+'[]" value="'+ipAddress+'" />';
	addHtml	+= '<img src="../img/i_del.gif" onClick="javascript:delIPAddress(\''+thisMode+'\', '+fieldNo+');" style="border:0;vertical-align:top;cursor:pointer;" alert="삭제" />';
	addHtml	+= '</div>';

	// 추가
	jQuery('#'+addListID).append(addHtml);
}

/**
 * ip 삭제하기
 * @param string thisMode 모드 (admin , user)
 * @param string fieldNo 삭제할 번호
 */
function delIPAddress(thisMode, fieldNo)
{
	// ID 체크 및 등록된 개수 추출
	var addTextID	= thisMode+'_ip_text_'+fieldNo;
	var addInputID	= thisMode+'_ip_address'+fieldNo;;

	// 텍스트에는 중간줄을, hidden input 은 삭제
	jQuery('#'+addTextID).attr('style', 'text-decoration:line-through');
	jQuery('#'+addInputID).remove();
}

/**
 * 저장하기
 * @param object obj object
 */
function validateForm(obj)
{
	// 관리자 IP접속제한 설정 체크
	if (jQuery('input[name=\'set_ip_permit\']:checked').val() == '1') {
		if(jQuery('input[name=\'admin_ip_address[]\']').length < 1) {
			alert("[관리자 IP접속제한 설정]\n최소 1개 이상의 접속가능 IP 가 등록 되어 있어야 합니다.\n접속가능 IP를 등록해 주세요.");
			return false;
		}
	}

	// 쇼핑몰 IP접속제한 설정 체크
	if (jQuery('input[name=\'user_ip_access_restriction\']:checked').val() == 'Y') {
		if(jQuery('input[name=\'user_ip_address[]\']').length < 1) {
			alert("[쇼핑몰 IP접속제한 설정]\n최소 1개 이상의 접속불가 IP 가 등록 되어 있어야 합니다.\n접속불가 IP를 등록해 주세요.");
			return false;
		}
	}
	return true;
}

/**
 * 접속제한시 페이지 선택에 따른 토글
 * @param string thisCode 여부
 */
function restrictionPageToggle(thisCode)
{
	if (thisCode == '404') {
		jQuery('#rp_404').show();
		jQuery('#rp_url').hide();
		jQuery('#rp_skin').hide();
	} else if (thisCode == 'url') {
		jQuery('#rp_404').hide();
		jQuery('#rp_url').show();
		jQuery('#rp_skin').hide();
	} else if (thisCode == 'skin') {
		jQuery('#rp_404').hide();
		jQuery('#rp_url').hide();
		jQuery('#rp_skin').show();
	}
}
//-->
</script>

<style type="text/css">
#admin-ip-access-form.none tr.enable {
	display: none;
}
#admin-ip-access-form.default tr.enable {
}
#user-ip-access-form.none tr.enable {
	display: none;
}
#user-ip-access-form.default tr.enable {
}
</style>

<form name="frmAdmin" id="el-ip-access-form" class="admin-form" method="post" target="ifrmHidden" action="adm_basic_ip_access.indb.php" onsubmit="return validateForm(this);">

<h2 class="title">관리자 IP접속제한 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=35')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<table class="admin-form-table" id="admin-ip-access-form">
<tr>
	<th style="width: 150px;">접속제한 설정</th>
	<td>
		<label>
			<input type="radio" id="admin-ip-access-none" name="set_ip_permit" value="0" <?php if($adminAccessIP['set_ip_permit']=='0'){ echo 'checked="checked"'; }?> />
			관리자 접속 IP 모두 허용
		</label>
		&nbsp;
		<label>
			<input type="radio" id="admin-ip-access-default" name="set_ip_permit" value="1" <?php if($adminAccessIP['set_ip_permit']=='1'){ echo 'checked="checked"'; }?> />
			관리자 접속 IP 제한 (등록된 IP만 접속 가능)
		</label>
		<div style="padding:5px 0px 0px 5px; color:#627DCE;">※ 관리자페이지에 접근 가능한 IP를 등록 / 관리함으로써 외부 해킹 등 보안을 강화할 수 있습니다.</div>
	</td>
</tr>
<tr class="enable">
	<th>접속가능 IP 등록</th>
	<td>
		<div style="margin-bottom:5px;">
			<div style="margin-bottom:10px;float:left;">
				<input type="text" id="admin_access_ip_class0" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="admin_access_ip_class1" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="admin_access_ip_class2" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="admin_access_ip_class3" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />
			</div>
			<div id="admin-ip-access-band" style="display:none;float:left;">
				~ <input type="text" id="admin_access_ip_class_band" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />
			</div>
			<div style="float:left;" class="noline">
				<img src="../img/i_add.gif" id="admin-ip-add" style="border:0;margin:2px 5px;vertical-align:top;cursor:pointer;" />
				<label><input type="checkbox" id="admin-ip-access-band-check" value="1" /> 대역지정하기</label>
			</div>
			<div style="float:left;padding-left:20px;margin:2px 5px;">
				<span style="color:#627DCE;" >
					[ 현재접속IP : <span id="admin-ip-address"><?php echo $IPAccessRestriction->_thisIP;?></span>
					<img src="../img/btn_s_apply.gif" id="admin-ip-apply" style="border:0;vertical-align:middle;cursor:pointer;" />
					]
				</span>
			</div>
		</div>

		<div id="admin_ip_address_list" style="clear:both;margin:5px 0 0 3px;line-height:150%">
			<?php for ($i = 1; $i <= count($adminAccessIP['set_regist_ip']); $i++) { ?>
			<div id="admin_ip_no_<?php echo $i;?>" style="padding:4px 0px;">
				<span id="admin_ip_text_<?php echo $i;?>"><?php echo $i;?> : <?php echo $adminAccessIP['set_regist_ip'][($i-1)];?></span>&nbsp;&nbsp;&nbsp;
				<input type="hidden" id="admin_ip_address<?php echo $i;?>" name="admin_ip_address[]" value="<?php echo $adminAccessIP['set_regist_ip'][($i-1)];?>" />
				<img src="../img/i_del.gif" onClick="javascript:delIPAddress('admin', <?php echo $i;?>);" style="border:0;vertical-align:top;cursor:pointer;" alert="삭제" />
			</div>
			<?php } ?>
		</div>
	</td>
</tr>
</table>


<h2 class="title">쇼핑몰 IP접속제한 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=35')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<table class="admin-form-table" id="user-ip-access-form">
<tr>
	<th style="width: 150px;">접속제한 설정</th>
	<td>
		<label>
			<input type="radio" id="user-ip-access-none" name="user_ip_access_restriction" value="N" <?php if($userAccessIP['user_ip_access_restriction']=='N'){ echo 'checked="checked"'; }?> />
			쇼핑몰 접속 IP 모두 허용
		</label>
		&nbsp;
		<label>
			<input type="radio" id="user-ip-access-default" name="user_ip_access_restriction" value="Y" <?php if($userAccessIP['user_ip_access_restriction']=='Y'){ echo 'checked="checked"'; }?> />
			쇼핑몰 접속 IP 제한 (등록된 IP는 접속 불가)
		</label>
		<div style="padding:5px 0px 0px 5px; color:#627DCE;">※ 쇼핑몰페이지에 접근 불가능한 IP를 등록 / 관리함으로써 불법 상품 정보 갈취나 프로그램에 의한 비정상적인 접근을 차단할 수 있습니다.</div>
	</td>
</tr>
<tr class="enable">
	<th style="width: 150px;">접속제한시 페이지</th>
	<td>
		<div>
			<label><input type="radio" name="restriction_page" value="404" onclick="restrictionPageToggle('404')" <?php if($userAccessIP['restriction_page']=='404'){ echo 'checked="checked"'; }?> /> 시스템 404</label>
			<span id="rp_404" class="ver8 blue">* "웹 페이지를 찾을 수 없습니다." 라는 브라우저의 기본적인 404 페이지를 보여줍니다.</span>
		</div>
		<div>
			<label><input type="radio" name="restriction_page" value="url" onclick="restrictionPageToggle('url')" <?php if($userAccessIP['restriction_page']=='url'){ echo 'checked="checked"'; }?> /> 특정 페이지</label>
			<div id="rp_url" style="margin:5px 0px 5px 20px">
				<input type="text" name="restriction_page_url" value="" size="60" class="line" />
				<div style="margin:5px 0px 5px 0px" class="ver8 red">* http:// 를 포함한 외부 사이트의 주소를 넣으세요. 쇼핑몰내 페이지로 설정시 사이트에 문제가 생겨 운영이 불가능 할 수 있습니다.</div>
			</div>
		</div>
		<div>
			<label><input type="radio" name="restriction_page" value="skin" onclick="restrictionPageToggle('skin')" <?php if($userAccessIP['restriction_page']=='skin'){ echo 'checked="checked"'; }?> /> 스킨 페이지</label>
			<div id="rp_skin" style="margin:5px 0px 5px 20px">
				<div style="margin:0px 0px 5px 0px">
				<select name="restriction_page_skin">
					<option value="">"<?php echo $cfg['tplSkin'];?>"스킨의 service 폴더내 파일 선택</option>
<?php
	// 현재 사용 스킨의 service 폴더의 파일
	$getFiles	= $IPAccessRestriction->getSkinFolderFile($cfg['tplSkin'], 'service');
	foreach ($getFiles as $skinFile) {
		if ($userAccessIP['restriction_page_skin'] == $skinFile){
			$selected	= 'selected="selected"';
		} else {
			$selected	= '';
		}
		echo '<option value="'.$cfg['rootDir'].'/data/skin/'.$cfg['tplSkin'].'/service/'.$skinFile.'" '.$selected.'>'.$skinFile.'</option>';
	}
?>
				</select>
				<div style="margin:5px 0px 5px 0px" class="ver8 blue">* "<?php echo $cfg['tplSkin'];?>"스킨의 service 폴더내 파일을 선택하세요. 디자인 관리에서 service 폴더에다 미리 생성을 해서 선택하시면 됩니다.</div>
			</div>
		</div>
	</td>
</tr>
<tr class="enable">
	<th>접속불가 IP 등록</th>
	<td>
		<div style="margin-bottom:5px;">
			<div style="margin:5px 0px 5px 0px; color:#FF0000;">※ 현재 접속하고 있는 IP는 <b><?php echo $IPAccessRestriction->_thisIP;?></b> 입니다. 해당 IP를 등록시 사이트 접속이 불가능하므로 주의 하세요.</div>
			<div style="margin-bottom:10px;float:left;">
				<input type="text" id="user_access_ip_class0" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="user_access_ip_class1" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="user_access_ip_class2" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />.
				<input type="text" id="user_access_ip_class3" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />
			</div>
			<div id="user-ip-access-band" style="display:none;float:left;">
				~ <input type="text" id="user_access_ip_class_band" maxlength="3" size="3" onkeyup="onlyNumber(this);" onkeypress="onlyNumber(this);" style="ime-mode:disabled;text-align:center;" />
			</div>
			<div style="float:left;" class="noline">
				<img src="../img/i_add.gif" id="user-ip-add" style="border:0;margin:2px 5px;vertical-align:top;cursor:pointer;" />
				<label><input type="checkbox" id="user-ip-access-band-check" value="1" /> 대역지정하기</label>
			</div>
		</div>

		<div id="user_ip_address_list" style="clear:both;margin:5px 0 0 3px;line-height:150%">
			<?php for ($i = 1; $i <= count($userAccessIP['user_ip_address']); $i++) { ?>
			<div id="user_ip_no_<?php echo $i;?>" style="padding:4px 0px;">
				<span id="user_ip_text_<?php echo $i;?>"><?php echo $i;?> : <?php echo $userAccessIP['user_ip_address'][($i-1)];?></span>&nbsp;&nbsp;&nbsp;
				<input type="hidden" id="user_ip_address<?php echo $i;?>" name="user_ip_address[]" value="<?php echo $userAccessIP['user_ip_address'][($i-1)];?>" />
				<img src="../img/i_del.gif" onClick="javascript:delIPAddress('user', <?php echo $i;?>);" style="border:0;vertical-align:top;cursor:pointer;" alert="삭제" />
			</div>
			<?php } ?>
		</div>
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
</div>

<ul class="admin-simple-faq" style="margin-top: 15px;">
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		관리자 IP접속제한 설정 : 관리자페이지에 접근 가능한 IP를 등록 / 관리함으로써 외부 해킹 등 보안을 강화할 수 있습니다.(최대 10개까지 지정 가능)
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		쇼핑몰 IP접속제한 설정 : 쇼핑몰페이지에 접근 불가능한 IP를 등록 / 관리함으로써 불법 상품 정보 갈취나 프로그램에 의한 비정상적인 접근을 차단할 수 있습니다.(최대 100개까지 지정 가능)
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		대역지정하기 : IP 대역의 4번째 입력범위를 지정할 수 있습니다. 빈 칸으로 둘 경우 0~255로 자동 등록됩니다.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		공인 IP에 대해서만 작동하며, 사설 IP 등록 시 작동하지 않습니다.<br />
		<span class="ver8">(사설 IP 대역 : 10.0.0.0 ~ 10.255.255.255, 172.16.0.0 ~ 172.31.255.255, 192.168.0.0 ~ 192.168.255.255)</span>
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		유동적으로 변경되는 IP를 등록시 접속이 제한되실 수 있으니 주의바랍니다.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none; font-weight:bold;">
		잘못된 IP 등록으로 사이트 접속 및 운영에 문제가 생길수 있으므로 "IP접속제한 설정"은 주의 하셔야 합니다.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none; font-weight:bold;">
		잘못된 IP 등록으로 인해 생기는 문제에 대해서는 고객님께서 직접 책임을 지셔야 합니다.
	</li>
</ul>
<? include "../_footer.php"; ?>