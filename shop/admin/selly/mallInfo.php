<?
/*********************************************************
* ���ϸ�     :  mallInfo.php
* ���α׷��� :  ���� ���/����
* �ۼ���     :  ����
* ������     :  2012.05.24
**********************************************************/
/*********************************************************
* ������     :  
* ��������   :  
**********************************************************/
$location = "���� > ���� ���";
$minfo_idx = $_GET['minfo_idx'];

if($minfo_idx) {
	include "../_header.popup.php";
	$reload = 'self.close();';
}
else {
	include "../_header.php";
	$reload = 'location.reload()';
}
include "../../lib/sAPI.class.php";

$sAPI = new sAPI();
$grp_cd = Array("grp_cd"=>"MALL_CD");
$arr_mall_cd = $sAPI->getCode($grp_cd, 'hash');

$checked['status']['Y'] = 'checked';
$login_info_encrypt = 'display:none;';

$cust_cd_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'cust_cd');
$res_cust_cd = $db->_select($cust_cd_query);
$cust_cd = $res_cust_cd[0]['value'];

if($minfo_idx) {//���� ����
	$arr_mall_info_data = Array('minfo_idx' => $minfo_idx);
	$arr_mall_info = $sAPI->getMallInfo($arr_mall_info_data);
	$mall_info = $arr_mall_info[0];

	$selected['mall_cd'][$mall_info['mall_cd']] = 'selected';
	if($mall_info['mall_cd'] != 'mall0001') $display['mall0001'] = 'style="display:none"';
	if($mall_info['mall_cd'] != 'mall0007')  $display['mall0007'] = 'style="display:none"';
	$checked['status'][$mall_info['status']] = 'checked';
	$mode = 'modify';
	if($mall_info['mall_cd'] == 'mall0003') $login_info_encrypt = '';

	### ���� ��й�ȣ ��ȣȭ ###
	$mall_pwd = rawurldecode($mall_info['mall_login_pwd']);
	$mall_login_pwd = $sAPI->xcryptare($mall_pwd, $cust_cd, false);
}



if($_POST['ticket']) {
	$ticket = $_POST['ticket'];
	$data['mall_cd'] = 'mall0001';
}
else {
	$ticket = $mall_info['etc2'];
}

if(!$mode) $mode = 'register';

?>

<script src="js/selly.js"></script>

<script>

function settingForm(mall_cd) {
	if(mall_cd == 'mall0001') {
		document.getElementById('security_ticket').style.display = '';
	}
	else {
		document.getElementById('security_ticket').style.display = 'none';
	}

	if(mall_cd == 'mall0007') {
		document.getElementById('api_id').style.display = '';
		document.getElementById('store_address').style.display = '';
	}
	else {
		document.getElementById('api_id').style.display = 'none';
		document.getElementById('store_address').style.display = 'none';
	}

	if(mall_cd == 'mall0003') document.getElementById('loginEncrypt').style.display = "";
	else document.getElementById("loginEncrypt").style.display = "none";
}

function auctionTicket() {
	var fm = document.frm_ticket;
		fm.target = "_self";
		fm.action = "https://memberssl.auction.co.kr/API/Login/WebServiceLogin2.aspx";
		fm.submit();
		return;
}

function scmLoginTest() {
	var mall_cd = document.getElementsByName('mall_cd')[0].value;
	var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
	var mall_login_pwd = document.getElementsByName('mall_login_pwd')[0].value;
	var security_ticket = document.getElementsByName('etc2')[0].value;
	var api_id = document.getElementsByName('etc5')[0].value;

	if(!mall_cd || !mall_login_id || !mall_login_pwd) {
		alert('�α��������� �Է��� �ּ���.');
		return;
	}

	if(mall_cd == 'mall0001' && !security_ticket) {
		alert('����Ƽ���� ������ �� �ٽ� �õ��� �ּ���.');
		return;
	}

	if(mall_cd == 'mall0007' && !api_id) {
		alert('API ID�� �Է��� �ּ���.');
		return;
	}

	if(mall_cd == 'mall0003' && document.getElementById("loginEncryptYn").value != "Y") {
		alert("�α������� ��ȣȭ �� ������ �ּ���.");
		return;
	}

	if(mall_cd != 'mall0007') api_id = '';


	if(mall_cd != 'mall0001') {
		security_ticket = '';
	}

	sellyLink.checkMallLogin(mall_cd, mall_login_id, mall_login_pwd, security_ticket, api_id);
}

function loginInfoEncrypt() {
	var mall_cd = document.getElementsByName('mall_cd')[0].value;
	var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
	var mall_login_pwd = document.getElementsByName('mall_login_pwd')[0].value;

	if(mall_cd != 'mall0003') {
		alert('�߸��� �����Դϴ�.');
		return;
	}

	if(!mall_cd || !mall_login_id || !mall_login_pwd) {
		alert('�α��������� �Է��� �ּ���.');
		return;
	}

	var obj1 = document.createElement("iframe");
	var obj2 = document.createElement("iframe");
	var url1 = 'http://steng19.godo.co.kr/mallScrap/login/STEncryptMall0003.gm?mall_login_id=' + mall_login_id + '&mall_login_pwd=' + mall_login_pwd + '&cust_cd=<?=$cust_cd?>&mall_cd=' + mall_cd + '&type=X"';
	obj1.src = url1;
	obj1.style.display = 'none';
	var url2 = 'http://stdev24.godo.co.kr/mallScrap/login/STEncryptMall0003.gm?mall_login_id=' + mall_login_id + '&mall_login_pwd=' + mall_login_pwd + '&cust_cd=<?=$cust_cd?>&mall_cd=' + mall_cd + '&type=X"';
	obj2.src = url2;
	obj2.style.display = 'none';
	document.body.appendChild(obj1);
	document.body.appendChild(obj2);
	document.getElementById("loginEncryptYn").value = "Y";
	alert("����Ǿ����ϴ�.");
}

function successAjax(data) {//���ó��
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//��ũ���º� ó������
		alert(json_data['msg']);
		document.getElementsByName('check_mall_login')[0].value = 'Y';
		if(json_data['reload'] == 'Y') <?=$reload?>;
		return;
	}
	else {
		alert(json_data['msg']);
		document.getElementsByName('check_mall_login')[0].value = 'N';
		return;
	}
}

function submitForm() {
	var check_mall_login = document.getElementsByName('check_mall_login')[0].value;
	if(check_mall_login != 'Y') {
		alert('SCM �α��� �׽�Ʈ�� �������� �ʾҽ��ϴ�.');
		return;
	}

	var mall_cd = document.getElementsByName('mall_cd')[0].value;
	var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
	var mall_login_pwd = document.getElementsByName('mall_login_pwd')[0].value;
	var status = document.getElementsByName('status')[0].value;
	var security_ticket = document.getElementsByName('etc2')[0].value;
	var type = document.getElementsByName('type')[0].value;
	var minfo_idx = document.getElementsByName('minfo_idx')[0].value;
	var api_id = document.getElementsByName('etc5')[0].value;
	var store_address = document.getElementsByName('etc4')[0].value;

	if(!mall_cd || !mall_login_id || !mall_login_pwd) {
		alert('�α��������� �Է��� �ּ���.');
		return;
	}

	if(mall_cd != 'mall0001') {
		security_ticket = '';
	}

	if(mall_cd != 'mall0007') {
		api_id = '';
		store_address = '';
	}

	sellyLink.insMall(mall_cd, mall_login_id, mall_login_pwd, security_ticket, status, type, minfo_idx, api_id, store_address);
}

function mallList() {
	location.replace("mallList.php");
}

window.onload = function(){
	table_design_load();
	if(document.getElementsByName('mall_cd')[0].value != 'mall0007') document.getElementById('api_id').style.display = 'none';
}

</script>

<input type="hidden" name="loginEncryptYn" id="loginEncryptYn" value="N" />
<form name="frmList">
	<input type="hidden" name="check_mall_login" value="N">
	<input type="hidden" name="type" value="<?=$mode?>">
	<input type="hidden" name="minfo_idx" value="<?=$minfo_idx?>">
	<div class="title title_top">���� ���<span>SELLY���� ��ũ ���񽺰� ������ ���� �α��� ������ ����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

	<table class="tb">
		<col class="cellC" style="width:100px;"><col class="cellL">
		<tr>
			<td>����</td>
			<td>
				<select name="mall_cd" onchange="settingForm(this.value);">
				<? foreach($arr_mall_cd as $key => $val) { ?>
					<? if($key == 'mall0005') continue; ?>
					<option value="<?=$key?>" <?=$selected['mall_cd'][$key]?>><?=$val?></option>
				<? } ?>
				</select>
			</td>
		</tr>
		<tr id="security_ticket" <?=$display['mall0001']?>>
			<td>����Ƽ��</td>
			<td>
				<input type="text" id="etc2" name="etc2" value="<?=$ticket?>" class="line" readonly style="height:22px; width:300px;">
				<img src="../img/btn_ticket.gif" align="absbottom" onclick="auctionTicket();" style="cursor:pointer;" alt="����Ƽ�� �ޱ�">
			</td>
		</tr>
		<tr id="api_id" <?=$display['mall0007']?>>
			<td>API ID</td>
			<td>
				<input type="text" id="etc5" name="etc5" value="<?=$mall_info['etc5']?>" class="line" style="height:22px; width:300px;">
			</td>
		</tr>
		<tr id="store_address" <?=$display['mall0007']?>>
			<td>������� �ּ�</td>
			<td>
				<input type="text" id="etc4" name="etc4" value="<?=$mall_info['etc4']?>" class="line" style="height:22px; width:300px;">
			</td>
		</tr>
		<tr>
			<td>�α���ID</td>
			<td>
				<input type="text" name="mall_login_id" value="<?=$mall_info['mall_login_id']?>" class="line" style="height:22px">
			</td>
		</tr>
		<tr>
			<td>�α��� ��й�ȣ</td>
			<td>
				<input type="password" name="mall_login_pwd" value="<?=$mall_login_pwd?>" class="line" style="height:22px">
				<img id="loginEncrypt" src="../img/btn_logininfo_encryption.gif" align="absbottom" onclick="loginInfoEncrypt();" style="cursor:pointer;<?=$login_info_encrypt?>" alt="�α������� ��ȣȭ">
				<img src="../img/btn_scmlogin_test.gif" align="absbottom" onclick="scmLoginTest();" style="cursor:pointer;" alt="SCM �α��� �׽�Ʈ">
			</td>
		</tr>
		<tr>
			<td>��뿩��</td>
			<td class=noline>
				<label><input type="radio" name="status" value="Y" <?=$checked['status']['Y']?>>���</label>
				<label><input type="radio" name="status" value="N" <?=$checked['status']['N']?>>�̻��</label>
			</td>
		</tr>
	</table>
	<div class="button_top">
		<input type="image" src="../img/btn_<?=$mode?>.gif" alt="���/����" onclick="submitForm();return false;" />
		<? if(!$minfo_idx) { ?>
			<input type="image" src="../img/btn_list.gif" alt="���" onclick="mallList();return false;" />
		<? } ?>
	</div>
</form>

<?
	$return_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

	if($minfo_idx) $return_url = $return_url.'?minfo_idx='.$minfo_idx;
?>
<form name="frm_ticket" action="https://memberssl.auction.co.kr/API/Login/WebServiceLogin2.aspx" method="post" target="_self">
	<input type="hidden" value="godomall" name="DevID" />
	<input type="hidden" value="godoselly" name="AppID" />
	<input type="hidden" value="rhehahf" name="AppPassword" />
	<input type="hidden" value="<?=$return_url?>" name="ReturnUrl" size=100>
</form>
<? if(!$minfo_idx) {?>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
SELLY���� ��ũ ���񽺰� ������ ���� �α��� ������ ����� �ּ���.<br/>
SCM �α��� �׽�Ʈ�� ���� �Ǿ�� ����� �����մϴ�.<br/>
�α��� ��й�ȣ�� SELLY�� ��ü ��ȣȭ�� ���� ���� �ǿ��� �Ƚ��Ͻð� ����Ͻñ� �ٶ��ϴ�.<br/><br/><br/>

���Ͽ� �Ǹ��ڷ� �����α��ν� ��й�ȣ ���� �� �޴������� �������� ����Ǵ� ��� SCM�α��� �׽�Ʈ�� ���е˴ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? } ?>
<? if(!$minfo_idx) include "../_footer.php"; ?>