<?php
$pg_name = 'inipay';

// INIpay �⺻ ���ð�
$_pg		= array(
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '����:�Ͻú�:2����:3����:4����:5����:6����:7����:8����:9����:10����:11����:12����',
			'skin'		=> 'ORIGINAL',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 0,
			);

$location	= "������⿬�� > �̴Ͻý�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// PHP ���� üũ
if (substr(phpversion(),0,1) < 5) {
	$msg = "INIPay TX5 ����� PHP ���� 5 �̻󿡼� ������ �մϴ�.\\n���� ���� �Ͻðų�, �������� ��� ȣ���þ�ü�� ���� �Ͻʽÿ�.\\nPHP ������ 4�� ��� INIPay TX4 ��� ������� ���� �˴ϴ�.";
	echo("<script>alert('".$msg."');parent.chgifrm('inicis.php',3);</script>");
}

if ($_GET['changePg'] == $pg_name){
	include "../../conf/pg.$pg_name.php";	
	include "../../conf/pg.escrow.php";
}
else if($cfg['settlePg'] == $pg_name){
	include "../../conf/pg.inipay.php";
	include "../../conf/pg.escrow.php";
}


// ������ ����
$pg		= array_merge($_pg, (array)$pg);
$escrow	= array_merge($_escrow, (array)$escrow);

// ����� üũ
if ($cfg['settlePg']=="inipay" && $pg['id']) {
	$spot	= "<b style=\"color:#ff0000;padding-left:10px\"><img src=\"../img/btn_on_func.gif\" align=\"absmiddle\" alt=\"�����\" /></b>";
}

// �⺻ ������ üũ
$checked[escrow]['use'][$escrow['use']] = $checked['type'][$escrow[type]] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked['zerofee'][$pg['zerofee']]				= "checked";
$checked['receipt'][$pg['receipt']]				= "checked";
$checked['skin'][$pg['skin']]					= "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

$checked['displayEgg'][$cfg['displayEgg']+0]	= "checked";

// �����Ƚ���(��Ű�� or ����ü��)
$prefix = 'GODO|GDP|GDFP|GDF';

// INIpay Űȭ�� ����
if ($cfg['settlePg']=="inipay" || is_file("../../conf/pg.inipay.php")){
	$dir = "../../order/card/inipay/key/";

	if (is_dir($dir.$pg['id']) && empty($pg['id']) === false){
		$od = opendir($dir.$pg['id']);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls['pg'][] = $rd;
		}
	}
	if (is_dir($dir.$escrow['id']) && empty($escrow['id']) === false){
		$od = opendir($dir.$escrow['id']);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls['escrow'][] = $rd;
		}
	}
}

// ����ũ�� ������ũ ó��
$escrow['eggDisplayLogo']	= stripslashes(html_entity_decode($escrow['eggDisplayLogo'], ENT_QUOTES));
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','o','h');

function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]');

	for(var i=0;i < robj.length;i++){
		var obj = document.getElementsByName(robj[i])[0];
		if(ret){
			obj.style.background = "#ffffff";
			obj.readOnly = false;
		}else{
			obj.style.background = "#e3e3e3";
			obj.readOnly = true;
		}
	}
}

function chkEscrow(){

	var obj = document.getElementsByName('escrow[id]')[0];

	if(document.getElementsByName('escrow[use]')[0].checked){
		obj.style.background = "#ffffff";
		obj.readOnly = false;
		return true;
	}else{
		obj.style.background = "#e3e3e3";
		obj.readOnly = true;
		return false;
	}

}

function chkFormThis(f){

	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	var s_id =  document.getElementsByName('escrow[id]')[0];
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('INIPay ID�� �ʼ��׸��Դϴ�.');
		return false;
	}

	if( chkEscrow() && !s_id.value ){
		s_id.focus();
		alert('Escrow ID�� �ʼ��׸��Դϴ�.');
		return false;
	}

	if(!chkPgid()){
		alert('INIPay ID�� �ùٸ��� �ʽ��ϴ�.');
		return false;
	}
	<?}?>
	return chkForm(f);
}

var IntervarId;

function resizeFrame()
{

    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}

var oldId = "<?php echo $pg['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("�������� INIPay ID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� INIPay ID�� �Է��ϼ���!");
		return;
	}
	var obj = document.getElementById('prefix');
	var pgid = document.getElementById('pgid').value;
	var ifrm = document.getElementById('pgifrm');
	get_pginfo(pgid);
	obj.className = 'show';
}
function closePrefix(){
	var obj = document.getElementById('prefix');
	document.getElementById('pgid').value='';
	obj.className = 'hide';
}
function get_pginfo(pgid){
	var ajax = new Ajax.Request( "../../proc/pginfo.indb.php",
	{
		method: "post",
		parameters: "mode=getPginfo&pgtype=inipay&pgid="+pgid,
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status != 200) return;
			if (req.responseText =='') return;
			var ifrm = document.getElementById('pgifrm');
			ifrm.src = req.responseText;
		}
	} );
}
function chkPgid(){
	var obj = document.getElementById('pgid');
	var pattern = new RegExp('^('+prefix+')');
	if(pattern.test(obj.value) || (oldId == obj.value && oldId)){
		return true;
	}else if(obj.value){
		return false;
	}
	return true;
}

function methodUpdate(){
	<?if ($pgStatus == 'disable'){?>
	alert('��� ���� PG�� �ƴմϴ�.');
	return;
	<?}
	else{?>
	ifrmHidden.location.href = '../basic/pgSettingUpdate.php';
	<?}?>
}

window.onload = function(){
	resizeFrame();
	<?if($pgStatus == 'menual'){?>
		chkPgid();
	<?}?>
}
</script>
<style>
.show {display:block}
.hide {display:none}
</style>
<div style="postion:relative">
	<div id="prefix" style="position:absolute;" class="hide">
	<iframe id="pgifrm" frameborder="0" width="554" height="366"></iframe>
	</div>
</div>

<div class="title title_top">
	�̴Ͻý�PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
</div>
<div id="inicis_banner"><script>panel('inicis_banner', 'pg');</script></div>

<form method="post" action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type="hidden" name="mode" value="inipay" />
<input type="hidden" name="cfg[settlePg]" value="inipay" />
<?if($pgStatus == 'menual') {?>
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr>
		<td colspan="2">
		�̴Ͻý����� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�<br />
		�̴Ͻý����� <b>���Ϸ� ������ ���������� Ǯ� INIPay ID�� Key File 3���� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.<br />
		���� �̴Ͻý��� ����� ���� �����̴ٸ� ��<u>�¶��ν�û �Ͻ���</u> ��<u>��༭���� �������� �̴Ͻý��� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff">[<u>��� �󼼾ȳ�</u>]</font></a>
		</td>
	</tr>
	</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="font:0;height:5"></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td><b>PG�� ��� ����</b></td>
	<td><b>INIpay V5.0 - ������ (V 0.1.1 - 20120302) <?php echo $spot;?></b></td>
</tr>
<tr>
	<td><b>�������� ����</b></td>
	<td class="noline">
	<? 
	$methodList = array('c'=>'�ſ�ī��', 'o'=>'������ü', 'v'=>'�������', 'h'=>'�޴��� ����','y'=>'��������');
		foreach($methodList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($set['use'][$key] == 'on') $checked[$key] = 'checked';
	
			if ($set['use_ck'][$key]!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='set[use][".$key."]' ".$checked[$key]." ".$disabled[$key]." onclick='chkSettleKind()' /> ".$val."</label>";
		}
	?>
	<?if($pgStatus != 'menual'){?>
	<button class="default-btn" type="button" style="padding-top:5px" onclick="methodUpdate()">�������� ���ΰ�ħ</button>
	<br/><span class="extext">*����� �������� �߿��� �����Ͽ� ����� �� �ֽ��ϴ�. ���������� �߰��Ϸ��� PG�� �����ͷ� ��û�Ͻʽÿ�.</span>
	<?}?>
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� �̴Ͻý��� ����� �������ܸ� üũ�ϼ���)</b></span><?}?>
		<div class="extext">
			*���� ��û ����ȭ�� ���� �������� �������� ��� ���� �� KG�̴Ͻý����� ���Ҽ��� �߰���û�� ������ ������ ȿ���� �߻��ϸ�, 
			�������� ���Ҽ��� �̿뿡 ���� �Ͻ� ������ ���ֵ˴ϴ�. [��, �������� ���� ��û���� �� ������ 2013�� 5�� 8�� ���� ���� ��ü�� ����.]<br />
			<a href="https://www.inicis.com/ini_19_4.jsp" class="extext" target="_blank"><strong>[ KG�̴Ͻý� �������̶�? ]</strong></a>
		</div>
	</td>
</tr>
<tr>
	<td><b>�̴Ͻý� <font color="#627dce">PG&nbsp;ID</font></b></td>
	<td>
		<?
		if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
			</div>
		<?}
		else if($pgStatus == 'disable'){?>
			<span class="extext"><b>���񽺸� ��û�ϸ�  �ڵ������˴ϴ�.</b></span>
		<?}
		else{?>
		<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?php echo $pg['id'];?>" onkeyup="chkPgid();" onblur="chkPgid();" id="pgid" /></div>
		<div style="float:left;padding:0 0 0 5px" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
		<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� INIPay ID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
		<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
		<?}?>
	</td>
</tr>
<?php for ($i=1; $i<=3; $i++){ ?>
<tr>
	<td class="ver8"><b>�̴Ͻý� <font color="#627dce">Key <?php echo $i;?></b></b><br/> (Key File #<?=$i?>)</font></td>
	<td class="ver8"><?if($pgStatus == 'menual'){?><input type="file" name="pg[file_0<?php echo $i;?>]" class="lline" /><?}?> <?php echo $fls['pg'][$i-1];?>
	<?if($pgStatus == 'auto'){?><span class="extext"><b>�ڵ����� �Ϸ�</b><?}?>
	</td>
</tr>
<?php } ?>
<tr>
	<td height="50">�Ϲ��ҺαⰣ</td>
	<td>
		<input type="text" name="pg[quota]" value="<?php echo $pg['quota'];?>" class="lline" style="width:500px" />
		<div class="extext" style="padding-top:5px">ex) <?php echo $_pg['quota'];?></div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
		<input type="radio" name="pg[zerofee]" value="no" <?php echo $checked['zerofee']['no'];?> /> �Ϲݰ���
		<input type="radio" name="pg[zerofee]" value="yes" <?php echo $checked['zerofee']['yes'];?> /> �����ڰ���
		<font class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b> (�Ʒ� '������ �Ⱓ' ���� üũ)</font>
	</td>
</tr>
<tr>
	<td height="92">������ �Ⱓ</td>
	<td>
		<input type="text" name=pg[zerofee_period] value="<?php echo $pg[zerofee_period];?>" class="lline" style="width:500px" />
		<div style="padding-top:7px"><font class="extext" >* ī����ڵ� :  01 (��ȯ), 03 (�Ե�), 04 (����), 06 (����), 11 (BC), 12 (�Ｚ), 14 (����), 34 (�ϳ� SK), 41 (NH(����))</div>
		<div style="padding-top:3px">ex) ��ī�� 3���� / 6���� �Һο� �Ｚī�� 3���� ������ ����� �� 11-3:6,12-3 ��� �Է�</div>
		<div style="padding-top:3px">ex) ���ī�忡 ���ؼ� 3���� / 6���� ������ ����� �� ALL-3:6 ��� �Է�</div>
		<div style="padding:3px 0 7px 0">* ������ �Ⱓ�� ����Ϸ��� �ݵ�� ���� �����ڰ����� üũ�ϼ���!</div>
	</td>
</tr>
<tr>
	<td>����â ��Ų</td>
	<td class="noline">
		<input type="radio" name="pg[skin]" value="ORIGINAL" <?php echo $checked['skin']['ORIGINAL'];?> /> �⺻
		<input type="radio" name="pg[skin]" value="GREEN" <?php echo $checked['skin']['GREEN'];?> /> ���
		<input type="radio" name="pg[skin]" value="ORANGE" <?php echo $checked['skin']['ORANGE'];?> /> ������
		<input type="radio" name="pg[skin]" value="BLUE" <?php echo $checked['skin']['BLUE'];?> /> �Ķ�
		<input type="radio" name="pg[skin]" value="KAKKI" <?php echo $checked['skin']['KAKKI'];?> /> īŰ
		<input type="radio" name="pg[skin]" value="GRAY" <?php echo $checked['skin']['GRAY'];?> /> ȸ��
	</td>
</tr>
</table>
<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<? if($pgStatus == 'auto'){?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�. ���ڰ��� ��û �� ��༭���� �������� �̴Ͻý��� �����ּ���. <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a>
</td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�Һ� , ������ ���� �ɼ��� ���θ� ��å�� ���� �����Ͽ� ����Ͻʽÿ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle" />PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
<tr><td height="8"></td></tr>
<tr><td><font class="def1" color="white"><b>- �̴Ͻý� PG��� "�������" ���������� ���Ǿ� �ִ� ��� (�ʵ�!) -</b></font></td></tr>
<tr><td>�� �̴Ͻý�PG��� "�������" ���������� ���Ǿ� �ִ� �����̶�� "��������Աݳ��� �ǽð� �뺸" ���񽺸� ���� ���ϰ� �Աݳ����� Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td>�� "��������Աݳ��� �ǽð��뺸" ��  ���� ������·� �Ա��� �ϰ� �Ǿ� ������ �� ���  �Աݳ������ΰ������ e���� �������������� ������  �ش��ֹ��ǿ� ���Ͽ� �ڵ����� "�Ա�Ȯ��" ó���� �ǵ��� �� �� �ִ� ���Դϴ�. </td></tr>
<tr><td>�� ���� "��������Աݳ��� �ǽð� �뺸"�� �����Ͽ� �̴Ͻý� PG�翡 ��û�� �Ͻ� �������� Ȯ���� �غ��ñ� �ٶ��ϴ�. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_inisis_pg.html#01',1113,420)"><img src="../img/btn_dacompg_sample.gif" align=absmiddle></a></td></tr>
<tr><td>�� ��û�� �Ͻ� ���¶�� �׸����� ����Ǿ� �ִ� ����,   "�Աݳ����뺸URL"�� http://������/shop/order/card/inipay/vacctinput.php ���� �Է��� ���ֽñ� �ٶ��ϴ�. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_inisis_pg.html#02',1113,420)"><img src="../img/btn_dacompg_sample.gif" align=absmiddle></a></td></tr>
<tr><td>�� ������¿� �����Ͽ� "�Աݳ��� �뺸 ���" / "�뺸��ļ���" / "�Աݳ����뺸URL" ���������� ��� ��ġ�� ���¶�� ������� �ֹ� �׽�Ʈ �� ������·� �Ա��� �Ŀ�</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�̴Ͻý� PG�翡���� ���ο��ο� e���� ������������������ �ֹ�ó�����°� "�Ա�Ȯ��"���� ����Ǿ����� Ȯ���� �ֽø� �˴ϴ�.</td></tr>
<tr><td>�� ���������� �Ա��뺸 ������� ���� ���� e���� �������������� �ֹ�����Ʈ���� �ش��ֹ����� (������¿� ����)   �ֹ�ó�����°� �Ա�Ȯ���� ���� �ʾ��� �� �� ����������� Ȯ���� �ֽñ� �ٶ��ϴ�. <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_inisis_pg.html#03',1113,420)"><img src="../img/btn_dacompg_sample.gif" align=absmiddle></a></td></tr>
<tr><td height="8"></td></tr>
<tr><td><font class="def1" color="white"><b>- �������� �̿�ȳ�  <a href="https://www.yelopay.com/home/home_policy.jsf" target="_blank">&nbsp;&nbsp;<font color="white">[ �ٷΰ��� ]</font></a></b></font></td></tr>
<tr><td>�� �������̴� �̴Ͻý�PG�� �̿��Ͽ��� ���񽺰� ���� �մϴ�. ŸPG�̿�� ���� �Ұ�.</td></tr>
<tr><td>�� 2013. 5. 8 ���� �̴Ͻý�PG�� ��û�Ͻø� �⺻������ �������� ���񽺰� ���ԵǾ� ��û�˴ϴ�. ��� ��, ����� ���Ͻô� ��� �������ܿ��� �������̸� üũ ���ּ���.</td></tr>
<tr><td>�� �ֹ��ϱ� ���������� �������� ���������� ������� ������ ��Ų��ġ�� üũ�Ͽ� �����ϼ���. <b><a href="http://www.godo.co.kr/customer_center/patch.php?sno=1620" target="_blank">&nbsp;&nbsp;<font color="white">[�������� ���׷��̵� �ٷΰ���]</font></a></b></td></tr>
<tr><td>�� �������̴� ����ϼ������� ���񽺸� �������� �ʽ��ϴ�.</td></tr>
<tr><td>�� �������̴� �޴����� ������ ����ϰ� �޴��� ��ȣ������ ���θ����� ������ �����ϵ��� �����ϴ� ���� �Դϴ�.</td></tr>
<tr><td>�� �����ڰ� �������̷� ������ �� ��� �������̿� ���� �����ϰ� �ܰ� �����ؾ� ������ �����ϸ� �Ϻ� ������ ���� ���� �ٷ� ������ �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02');</script>

<div style="display:block">
<div class="title">
	����ũ�� ���� <span>���ݼ� ������ �ǹ������� ����ũ�ΰ����� ����ؾ� �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
</div>

<div id="MSG03">
<table border=0 cellpadding=1 cellspacing=0 border=0 class="small_ex">
<tr><td>�̴Ͻý����� �����ϴ� �̴� ����ũ�� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>�̴Ͻý����� <b>���Ϸ� ������ ���������� Ǯ� Escrow ID�� Escrow Key File 3���� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� �̴Ͻý� �̴� ����ũ�θ� ��� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10">���༭���� �������� �̴Ͻý��� �����Ͻø� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div style="padding-top:5px"></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>��뿩��</td>
	<td class="noline">
		<input type="radio" name="escrow[use]" value="Y" <?php echo $checked['escrow']['use']['Y'];?> onclick="chkEscrow();" /> ���
		<input type="radio" name="escrow[use]" value="N" <?php echo $checked['escrow']['use']['N'];?> onclick="chkEscrow();" /> ������
		<span style="padding-left:15"><font class="extext"><b>(�̴Ͻý��� �̴� ����ũ�θ� ����ϼ̴ٸ� ������� üũ�ϼ���.)</b></font></span><br />
		<span style="padding-left:15"><font class="extext"><b>�� '�ϳ�����ũ��'�� �ƴմϴ�. �ݵ�� "�̴Ͽ���ũ��"�� ����ϼž� �մϴ�.</b></font></span>
	</td>
</tr>
<input type="hidden" name="escrow[comp]" value="PG" />	<!-- ����ũ�� ������� -->
<tr>
	<td>���� ����</td>
	<td class="noline">
<?
		$methodEscrowList = array('c'=>'�ſ�ī��', 'o'=>'������ü', 'v'=>'�������');
		foreach($methodEscrowList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($escrow[$key] == 'on') $checked[$key] = 'checked';
			if ($escrow[$key.'_ck']!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='escrow[".$key."]' ".$checked[$key]." ".$disabled[$key]."   /> ".$val."</label>";
		}
	?>
	</td>
</tr>
<tr>
	<td>��� �ݾ�</td>
	<td>
		<input type="text" name="escrow[min]" value="<?php echo $escrow['min'];?>" class="lline" onkeydown="onlynumber();" style="width:100px;" />
		<div class="extext" style="padding-top:4px">PG�縶�� ����ũ�� ������ ��� �ݾ׿� ������ �ȵɼ��� �����Ƿ�, �ݵ�� ������ PG���� ����ũ�� ��೻���� �� Ȯ���ϼ���.</div>
	</td>
</tr>
<!--
<tr>
	<td>�� ������ �δ�</td>
	<td>
		<input type="text" name="escrow[fee]" value="<?php echo $escrow['fee']+0;?>" size="5" class="right" /> %
	</td>
</tr>
-->
<tr>
	<td>Escrow <font color="#627dce">ID</font></td>
	<td>
		<?
		if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$escrow['id']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
			</div>
		<?}
		else if($pgStatus == 'disable'){?>
			<span class="extext"><b>���񽺸� ��û�ϸ�  �ڵ������˴ϴ�.</b></span>
		<?}
		else{?>
		<input type="text" name=escrow[id] class="lline" value="<?php echo $escrow['id'];?>" />
		<?}?>
	</td>
</tr>
<?
for ($i=1;$i<=3;$i++){ ?>
<tr>
	<td class="ver8"><b>Escrow <font color="#627dce">Key <?php echo $i;?></b><br/>
	(Key File #<?=$i?>)</font>
	</td>
	<td class="ver8"><?if($pgStatus == 'menual'){?><input type="file" name="escrow[file_0<?php echo $i;?>]" class="lline" /><?}?><?php echo $fls['escrow'][$i-1];?>
	<?if($pgStatus == 'auto'){?><span class="extext"><b>�ڵ����� �Ϸ�</b><?}?>
	</td>
</tr>
<?} ?>
<tr>
	<td>���� ���� ǥ��<div style="padding-top:3"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><font class="extext_l">[ǥ���̹��� ����]</font></a></div></td>
	<td class="noline">
		<input type="radio" name="cfg[displayEgg]" value="0" <?php echo $checked['displayEgg'][0];?> /> �����ϴܰ� �������� �������������� ǥ��
		<input type="radio" name="cfg[displayEgg]" value="1" <?php echo $checked['displayEgg'][1];?> /> ��ü�������� ǥ��
		<input type="radio" name="cfg[displayEgg]" value="2" <?php echo $checked['displayEgg'][2];?> /> ǥ������ ����
	</td>
</tr>
<tr>
	<td>����ũ�� ���� ��ũ</td>
	<td class="noline">
		<textarea name="escrow[eggDisplayLogo]" style="width:100%;height:80px" class="tline"><?php echo $escrow['eggDisplayLogo'];?></textarea><br />
		<font class="extext"><b>�� <a href="http://mark.inicis.com/certi2/certi_escrow.php" class="extext_l" target="_blank">[KG �̴Ͻý� ��������]</a>���� ���� ���� ������ ��������.</b></font>
	</td>
</tr>
</table>

<div style="padding-top:10"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<tr>
	<td>
		<table cellpadding="15" cellspacing="0" border="0" bgcolor="white" width="100%">
		<tr>
			<td>
				<div style="padding:0 0 5px 0">* ���ž������� ǥ�� ������ (����ũ�� ���� ������ ���ž���ǥ�ø� üũ�ϰ�, �Ʒ� ǥ������ ���� �ݿ��ϼ���)</div>
				<table width="100%" height="100" class="tb" style='border:1px solid #cccccc;' bgcolor="white">
				<tr>
					<td width="30%" style='border:1px solid #cccccc;padding-left:20'>�� [���������� �ϴ�] ǥ����</td>
					<td align="center" rowspan="2" style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
					<td width="70%" style='border:1px solid #cccccc;padding-left:40'><font class="extext"><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class="extext"><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > html�ҽ� ��������]</b></font></a> �� ����<br /> ġȯ�ڵ� <font class="ver8" color=000000><b>{=displayEggBanner()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class="extext_l">[�ٷΰ���]</font></a></font></td>
				</tr>
				<tr>
					<td width="30%" style='border:1px solid #cccccc;padding-left:20'>�� [�������� ����������] ǥ����</td>
					<td width="70%" style='border:1px solid #cccccc;padding-left:40'>
						<a href='../design/codi.php?design_file=order/order.htm' target="_blank"><font class="extext"><font class="extext_l">[�����ΰ��� > ��Ÿ������ ������ > �ֹ��ϱ� > order.htm]</font></a> �� ����<br /> ġȯ�ڵ� <font class="ver8" color=000000><b>{=displayEggBanner(1)}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class="extext_l">[�ٷΰ���]</font></a></font>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>

		<div style="padding-top:15"></div>

		<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext"><b>���ž������� ���� ǥ�� �ǹ�ȭ �ȳ� (2007�� 9�� 1�� ����)</b></font></td></tr>
		<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">�� ǥ�á����� �Ǵ� ������ ��ġ�� ���̹��� �ʱ�ȭ��� �Һ����� �������� ����ȭ�� �� ������ ����.</font></td></tr>
		<tr><td style="padding-left:16"><font class="extext">- ���̹��� �ʱ�ȭ�� ��� ��10����1���� ������� �ſ� �� ǥ����� ����κ��� �ٷ� ���� �Ǵ� ������ ���ž������� ���� ������ ǥ���ϵ��� ��.</font></td></tr>
		<tr><td style="padding-left:16"><font class="extext">- �Һ��ڰ� ��Ȯ�� ���ظ� �������� ���ž������� �̿��� ������ �� �ֵ���, �������� ���úκ��� �ٷ� ���� ���ž������� ���û����� �˱� ���� �����Ͽ���  ��.</font></td></tr>
		<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">�� ǥ�á����� �Ǵ� ���� �������� ������ �� ������ ������.</font></td></tr>
		<tr><td style="padding-left:16"><font class="extext">- ���� ������ 5���� �̻� ������ �Һ��ڰ� ���ž��������� �̿��� ������ �� �ִٴ� ����</font></td></tr>
		<tr><td style="padding-left:16"><font class="extext">- ����Ǹž��� �ڽ��� ������ ���ž��������� ��������ڸ� �Ǵ� ��ȣ</font></td></tr>
		<tr><td style="padding-left:16"><font class="extext">- �Һ��ڰ� ���ž������� ���Ի���� ������ Ȯ�� �Ǵ� ��ȸ�� �� �ִٴ� ����</font></td></tr>
		<tr><td height=10></td></tr>
		</table>

		<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>���ž������� �ǹ� ���� Ȯ�� (2013�� 11�� 29�� ����)</b></font></td></tr>
		<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
		<tr><td style="padding-left:16"><font class=extext>5���� ���� �ŷ��� ���ؼ��� �Һ����� ������ ��ȣ�ϱ� ���Ͽ� ���ž������� �ǹ� ���� ��� Ȯ�� <br/>1ȸ ���� ����, 5���� �̻� �� 5���� ������ �Ҿ� �ŷ�(��� �ݾ�)</font></td></tr>
		<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
		<tr><td style="padding-left:16"><font class=extext>���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ���� <br/>[ ���� ��11841ȣ, ������: 2013.5.28, �Ϻ� ���� ]</font></td></tr>
		<tr><td height=10></td></tr>
		</table>
	</td>
</tr>
</table>

<div class="title">
	���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
		<input type="radio" name="pg[receipt]" value="N" <?php echo $checked['receipt']['N'];?> /> ������
		<input type="radio" name="pg[receipt]" value="Y" <?php echo $checked['receipt']['Y'];?> /> ���
		<br /><font class="extext" style="padding-left:5px">�̴Ͻý� ���ݿ����� �̿��� �̴Ͻý� ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="https://www.inicis.com/ini_21_1.jsp" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>
</div>

<div id="MSG04">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���ž�������(����ũ�� �Ǵ� ���ں���)�� ���ڻ�ŷ��Һ��ں�ȣ�� �� ����� ������ ���� 2011�� 7�� 29�Ϻ��� 5���� �̻� ���ݼ� ������ �ǹ� ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />����ũ�� ������ �� ���ݾ׿� ���Ѱ��� ��û�� PG�糪 ���࿡ ���� �ٸ� �� �����Ƿ� ���Ǹ� �ϼž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�Һ��ڴ� 2008.7.1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ� 5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG04')</script>

<div class="button">
	<input type="image" src="../img/btn_save.gif" />
	<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>

</form>
<script>chkSettleKind();chkEscrow();</script>