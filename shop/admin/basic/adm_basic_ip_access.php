<?php
/*
 * IP ���� ���� ���� (������ IP ���� ����, ���θ� IP ���� ���� ����)
 * @author artherot @ godosoft development team.
 */

// �׺���̼�
$location	= '�⺻���� > IP�������� ����';

// ��� ȣ��
include '../_header.php';

// IP ���� ���� ������
$adminAccessIP			= $IPAccessRestriction->getAdminAccessIP();
$userAccessIP			= $IPAccessRestriction->getUserAccessIP();
?>
<script type="text/javascript">
<!--
jQuery(document).ready(function()
{
	// ������ ���� IP ��� ��� üũ��
	jQuery("#admin-ip-access-none").click(function(){
		ipAccessConfigToggle('admin', 'none');
	});

	// ������ ���� IP ���� (��ϵ� IP�� ���� ����) üũ��
	jQuery("#admin-ip-access-default").click(function(){
		ipAccessConfigToggle('admin', 'default');
	});

	// �뿪�����ϱ� üũ�� (������)
	jQuery("#admin-ip-access-band-check").click(function(){
		ipAccessBandToggle('admin');
	});

	// �������� IP ���� Ŭ���� (������)
	jQuery("#admin-ip-apply").click(function(){
		var admin_ip_address	= jQuery("#admin-ip-address").text().split('.');
		for (var i = 0; i < 4; i++) {
			jQuery("#admin_access_ip_class"+i).val(admin_ip_address[i]);
		}
	});

	// �߰� ��ư�� ���� ��� (������)
	jQuery("#admin-ip-add").click(function(){
		addIPAddress('admin');
	});

	// ���θ� ���� IP ��� ��� üũ��
	jQuery("#user-ip-access-none").click(function(){
		ipAccessConfigToggle('user', 'none');
	});

	// ���θ� ���� IP ���� (��ϵ� IP�� ���� ����) üũ��
	jQuery("#user-ip-access-default").click(function(){
		ipAccessConfigToggle('user', 'default');
	});

	// �뿪�����ϱ� üũ�� (���θ�)
	jQuery("#user-ip-access-band-check").click(function(){
		ipAccessBandToggle('user');
	});

	// �߰� ��ư�� ���� ��� (���θ�)
	jQuery("#user-ip-add").click(function(){
		addIPAddress('user');
	});

	// ������ IP�������� ���� üũ
	if (jQuery('input[name=\'set_ip_permit\']:checked').val() == '0') {
		ipAccessConfigToggle('admin', 'none');
	}

	// ���θ� IP�������� ���� üũ
	if (jQuery('input[name=\'user_ip_access_restriction\']:checked').val() == 'N') {
		ipAccessConfigToggle('user', 'none');
	}

	restrictionPageToggle('<?php echo $userAccessIP['restriction_page'];?>');
});

/**
 * ��� / ���� ���
 * @param string thisMode ��� (admin , user)
 * @param string thisSwitch ó�� (none , default)
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
 * ip �뿪 �����ϱ� ���
 * @param string thisMode ��� (admin , user)
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
 * ip �߰��ϱ�
 * @param string thisMode ��� (admin , user)
 */
function addIPAddress(thisMode)
{
	var fieldID		= thisMode + "_access_ip_class";
	var Aclass		= jQuery("#"+fieldID+"0").val();
	var Bclass		= jQuery("#"+fieldID+"1").val();
	var Cclass		= jQuery("#"+fieldID+"2").val();
	var Dclass		= jQuery("#"+fieldID+"3").val();
	var Dclass_band	= jQuery("#"+fieldID+"_band").val();

	// C class ���� �Է����� ������� ���
	if(!Aclass || !Bclass || !Cclass) {
		alert("��Ȯ�� IP�� �Է����ּ���");
		if (!Aclass) {
			jQuery("#"+fieldID+"0").focus();
		} else if (!Bclass) {
			jQuery("#"+fieldID+"1").focus();
		} else if (!Cclass) {
			jQuery("#"+fieldID+"2").focus();
		}
		return false;
	}

	// D class �Է����� ������� �뿪���� ����
	if (!Dclass && !Dclass_band) {
		// �뿪 �����ϱ� üũ
		jQuery("input[id='"+thisMode+"-ip-access-band-check']").attr("checked", "checked");

		// ip �뿪 �����ϱ� ��� ó��
		ipAccessBandToggle(thisMode);

		// D class �� �뿪�� �Է��ϱ�
		jQuery("#"+fieldID+"3").val('0');
		Dclass		= '0';
		jQuery("#"+fieldID+"_band").val('255');
		Dclass_band	= '255';
	}

	// D class �Է����� �ʰ� �뿪�� �Է��Ѱ�� D class �� 0���� ó��
	if (!Dclass && Dclass_band != '') {
		jQuery("#"+fieldID+"3").val('0');
		Dclass	= '0';
	}

	// D class �� �뿪��Ÿ ū��� ���
	if (Dclass !='' && Dclass_band != '') {
		if (Dclass >= Dclass_band) {
			alert("��Ȯ�� IP �뿪�� �Է����ּ���");
			return false;
		}
	}

	// �߰��� IP �ּ�
	var ipAddress	= Aclass+'.'+Bclass+'.'+Cclass+'.'+Dclass;
	if (Dclass_band != '') {
		ipAddress	= ipAddress+'~'+Dclass_band;
	}

	// �ִ� ��� ����
	if (thisMode == 'admin') {
		var maxLimit	= 10;
	} else {
		var maxLimit	= 100;
	}

	// ID üũ �� ��ϵ� ���� ����
	var addListID	= thisMode+'_ip_address_list';
	var addFieldID	= thisMode+'_ip_no_';
	var addTextID	= thisMode+'_ip_text_';
	var addInputID	= thisMode+'_ip_address';
	var fieldNoChk	= 0;
	if (typeof jQuery('#'+addListID).find('div:last').get(0) != 'undefined') {
		fieldNoChk	= jQuery('#'+addListID).find('div:last').get(0).id.replace(addFieldID,'');
	}
	var fieldNo		= parseInt(fieldNoChk) + 1;

	// ��ϰ��� üũ
	if (maxLimit < fieldNo) {
		alert('�ִ� '+maxLimit+'�� ������ ����� ���� �մϴ�.');
		return false;
	}

	// �߰��� HTML ����
	var addHtml		= '';
	addHtml	+= '<div id="'+addFieldID+fieldNo+'" style="padding:4px 0px;">';
	addHtml	+= '<span id="'+addTextID+fieldNo+'">'+fieldNo+' : '+ipAddress+'</span>&nbsp;&nbsp;&nbsp;';
	addHtml	+= '<input type="hidden" id="'+addInputID+fieldNo+'" name="'+addInputID+'[]" value="'+ipAddress+'" />';
	addHtml	+= '<img src="../img/i_del.gif" onClick="javascript:delIPAddress(\''+thisMode+'\', '+fieldNo+');" style="border:0;vertical-align:top;cursor:pointer;" alert="����" />';
	addHtml	+= '</div>';

	// �߰�
	jQuery('#'+addListID).append(addHtml);
}

/**
 * ip �����ϱ�
 * @param string thisMode ��� (admin , user)
 * @param string fieldNo ������ ��ȣ
 */
function delIPAddress(thisMode, fieldNo)
{
	// ID üũ �� ��ϵ� ���� ����
	var addTextID	= thisMode+'_ip_text_'+fieldNo;
	var addInputID	= thisMode+'_ip_address'+fieldNo;;

	// �ؽ�Ʈ���� �߰�����, hidden input �� ����
	jQuery('#'+addTextID).attr('style', 'text-decoration:line-through');
	jQuery('#'+addInputID).remove();
}

/**
 * �����ϱ�
 * @param object obj object
 */
function validateForm(obj)
{
	// ������ IP�������� ���� üũ
	if (jQuery('input[name=\'set_ip_permit\']:checked').val() == '1') {
		if(jQuery('input[name=\'admin_ip_address[]\']').length < 1) {
			alert("[������ IP�������� ����]\n�ּ� 1�� �̻��� ���Ӱ��� IP �� ��� �Ǿ� �־�� �մϴ�.\n���Ӱ��� IP�� ����� �ּ���.");
			return false;
		}
	}

	// ���θ� IP�������� ���� üũ
	if (jQuery('input[name=\'user_ip_access_restriction\']:checked').val() == 'Y') {
		if(jQuery('input[name=\'user_ip_address[]\']').length < 1) {
			alert("[���θ� IP�������� ����]\n�ּ� 1�� �̻��� ���ӺҰ� IP �� ��� �Ǿ� �־�� �մϴ�.\n���ӺҰ� IP�� ����� �ּ���.");
			return false;
		}
	}
	return true;
}

/**
 * �������ѽ� ������ ���ÿ� ���� ���
 * @param string thisCode ����
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

<h2 class="title">������ IP�������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=35')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<table class="admin-form-table" id="admin-ip-access-form">
<tr>
	<th style="width: 150px;">�������� ����</th>
	<td>
		<label>
			<input type="radio" id="admin-ip-access-none" name="set_ip_permit" value="0" <?php if($adminAccessIP['set_ip_permit']=='0'){ echo 'checked="checked"'; }?> />
			������ ���� IP ��� ���
		</label>
		&nbsp;
		<label>
			<input type="radio" id="admin-ip-access-default" name="set_ip_permit" value="1" <?php if($adminAccessIP['set_ip_permit']=='1'){ echo 'checked="checked"'; }?> />
			������ ���� IP ���� (��ϵ� IP�� ���� ����)
		</label>
		<div style="padding:5px 0px 0px 5px; color:#627DCE;">�� �������������� ���� ������ IP�� ��� / ���������ν� �ܺ� ��ŷ �� ������ ��ȭ�� �� �ֽ��ϴ�.</div>
	</td>
</tr>
<tr class="enable">
	<th>���Ӱ��� IP ���</th>
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
				<label><input type="checkbox" id="admin-ip-access-band-check" value="1" /> �뿪�����ϱ�</label>
			</div>
			<div style="float:left;padding-left:20px;margin:2px 5px;">
				<span style="color:#627DCE;" >
					[ ��������IP : <span id="admin-ip-address"><?php echo $IPAccessRestriction->_thisIP;?></span>
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
				<img src="../img/i_del.gif" onClick="javascript:delIPAddress('admin', <?php echo $i;?>);" style="border:0;vertical-align:top;cursor:pointer;" alert="����" />
			</div>
			<?php } ?>
		</div>
	</td>
</tr>
</table>


<h2 class="title">���θ� IP�������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=35')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<table class="admin-form-table" id="user-ip-access-form">
<tr>
	<th style="width: 150px;">�������� ����</th>
	<td>
		<label>
			<input type="radio" id="user-ip-access-none" name="user_ip_access_restriction" value="N" <?php if($userAccessIP['user_ip_access_restriction']=='N'){ echo 'checked="checked"'; }?> />
			���θ� ���� IP ��� ���
		</label>
		&nbsp;
		<label>
			<input type="radio" id="user-ip-access-default" name="user_ip_access_restriction" value="Y" <?php if($userAccessIP['user_ip_access_restriction']=='Y'){ echo 'checked="checked"'; }?> />
			���θ� ���� IP ���� (��ϵ� IP�� ���� �Ұ�)
		</label>
		<div style="padding:5px 0px 0px 5px; color:#627DCE;">�� ���θ��������� ���� �Ұ����� IP�� ��� / ���������ν� �ҹ� ��ǰ ���� ���볪 ���α׷��� ���� ���������� ������ ������ �� �ֽ��ϴ�.</div>
	</td>
</tr>
<tr class="enable">
	<th style="width: 150px;">�������ѽ� ������</th>
	<td>
		<div>
			<label><input type="radio" name="restriction_page" value="404" onclick="restrictionPageToggle('404')" <?php if($userAccessIP['restriction_page']=='404'){ echo 'checked="checked"'; }?> /> �ý��� 404</label>
			<span id="rp_404" class="ver8 blue">* "�� �������� ã�� �� �����ϴ�." ��� �������� �⺻���� 404 �������� �����ݴϴ�.</span>
		</div>
		<div>
			<label><input type="radio" name="restriction_page" value="url" onclick="restrictionPageToggle('url')" <?php if($userAccessIP['restriction_page']=='url'){ echo 'checked="checked"'; }?> /> Ư�� ������</label>
			<div id="rp_url" style="margin:5px 0px 5px 20px">
				<input type="text" name="restriction_page_url" value="" size="60" class="line" />
				<div style="margin:5px 0px 5px 0px" class="ver8 red">* http:// �� ������ �ܺ� ����Ʈ�� �ּҸ� ��������. ���θ��� �������� ������ ����Ʈ�� ������ ���� ��� �Ұ��� �� �� �ֽ��ϴ�.</div>
			</div>
		</div>
		<div>
			<label><input type="radio" name="restriction_page" value="skin" onclick="restrictionPageToggle('skin')" <?php if($userAccessIP['restriction_page']=='skin'){ echo 'checked="checked"'; }?> /> ��Ų ������</label>
			<div id="rp_skin" style="margin:5px 0px 5px 20px">
				<div style="margin:0px 0px 5px 0px">
				<select name="restriction_page_skin">
					<option value="">"<?php echo $cfg['tplSkin'];?>"��Ų�� service ������ ���� ����</option>
<?php
	// ���� ��� ��Ų�� service ������ ����
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
				<div style="margin:5px 0px 5px 0px" class="ver8 blue">* "<?php echo $cfg['tplSkin'];?>"��Ų�� service ������ ������ �����ϼ���. ������ �������� service �������� �̸� ������ �ؼ� �����Ͻø� �˴ϴ�.</div>
			</div>
		</div>
	</td>
</tr>
<tr class="enable">
	<th>���ӺҰ� IP ���</th>
	<td>
		<div style="margin-bottom:5px;">
			<div style="margin:5px 0px 5px 0px; color:#FF0000;">�� ���� �����ϰ� �ִ� IP�� <b><?php echo $IPAccessRestriction->_thisIP;?></b> �Դϴ�. �ش� IP�� ��Ͻ� ����Ʈ ������ �Ұ����ϹǷ� ���� �ϼ���.</div>
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
				<label><input type="checkbox" id="user-ip-access-band-check" value="1" /> �뿪�����ϱ�</label>
			</div>
		</div>

		<div id="user_ip_address_list" style="clear:both;margin:5px 0 0 3px;line-height:150%">
			<?php for ($i = 1; $i <= count($userAccessIP['user_ip_address']); $i++) { ?>
			<div id="user_ip_no_<?php echo $i;?>" style="padding:4px 0px;">
				<span id="user_ip_text_<?php echo $i;?>"><?php echo $i;?> : <?php echo $userAccessIP['user_ip_address'][($i-1)];?></span>&nbsp;&nbsp;&nbsp;
				<input type="hidden" id="user_ip_address<?php echo $i;?>" name="user_ip_address[]" value="<?php echo $userAccessIP['user_ip_address'][($i-1)];?>" />
				<img src="../img/i_del.gif" onClick="javascript:delIPAddress('user', <?php echo $i;?>);" style="border:0;vertical-align:top;cursor:pointer;" alert="����" />
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
		������ IP�������� ���� : �������������� ���� ������ IP�� ��� / ���������ν� �ܺ� ��ŷ �� ������ ��ȭ�� �� �ֽ��ϴ�.(�ִ� 10������ ���� ����)
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		���θ� IP�������� ���� : ���θ��������� ���� �Ұ����� IP�� ��� / ���������ν� �ҹ� ��ǰ ���� ���볪 ���α׷��� ���� ���������� ������ ������ �� �ֽ��ϴ�.(�ִ� 100������ ���� ����)
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		�뿪�����ϱ� : IP �뿪�� 4��° �Է¹����� ������ �� �ֽ��ϴ�. �� ĭ���� �� ��� 0~255�� �ڵ� ��ϵ˴ϴ�.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		���� IP�� ���ؼ��� �۵��ϸ�, �缳 IP ��� �� �۵����� �ʽ��ϴ�.<br />
		<span class="ver8">(�缳 IP �뿪 : 10.0.0.0 ~ 10.255.255.255, 172.16.0.0 ~ 172.31.255.255, 192.168.0.0 ~ 192.168.255.255)</span>
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none;">
		���������� ����Ǵ� IP�� ��Ͻ� ������ ���ѵǽ� �� ������ ���ǹٶ��ϴ�.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none; font-weight:bold;">
		�߸��� IP ������� ����Ʈ ���� �� ��� ������ ����� �����Ƿ� "IP�������� ����"�� ���� �ϼž� �մϴ�.
	</li>
	<li style="margin:5px 5px 5px 15px; list-style-type: disc; background: none; font-weight:bold;">
		�߸��� IP ������� ���� ����� ������ ���ؼ��� ���Բ��� ���� å���� ���ž� �մϴ�.
	</li>
</ul>
<? include "../_footer.php"; ?>