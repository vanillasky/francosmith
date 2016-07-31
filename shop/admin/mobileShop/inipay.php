<?php
$pg_name = 'inipay';
// INIpay �⺻ ���ð�
$_pg_mobile		= array(
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '����:�Ͻú�:2����:3����:4����:5����:6����:7����:8����:9����:10����:11����:12����',
			);

$location = "������⿬�� > �̴Ͻý�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// INIpay ������ ó��
if ($cfg['settlePg'] == "inipay"){
	@include "../../conf/pg.".$cfg['settlePg'].".php";
	$pg_mobile	= $pg;
}

// PG Ÿ��üũ
if($cfg['settlePg'] != "inipay") {
	$pg_mobile = array();
}

// ����� üũ
if ($cfg['settlePg'] == "inipay" && $pg_mobile['id']) {
	$spot	= "<b style=\"color:#ff0000;padding-left:10px\"><img src=\"../img/btn_on_func.gif\" align=\"absmiddle\" alt=\"�����\" /></b>";
}

// �⺻ ������ üũ
$checked['zerofee'][$pg_mobile['zerofee']]		= "checked";
$checked['receipt'][$pg_mobile['receipt']]		= "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

// �����Ƚ���(��Ű�� or ����ü��)
$prefix = 'GODO|GDP|GDFP|GDF';

// INIpay Űȭ�� ����
if ($cfg['settlePg'] == "inipay"){
	$dir = "../../order/card/inipay/key/";

	if (is_dir($dir.$pg_mobile['id'])){
		$od = opendir($dir.$pg_mobile['id']);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls['pg'][] = $rd;
		}
	}
}
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','h');

function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		var sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
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

function chkFormThis(f){

	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('INIPay ID�� �ʼ��׸��Դϴ�.');
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

var oldId = "<?php echo $pg_mobile['id'];?>";
function openPrefix(){
	return;
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
		parameters: "mode=getPginfo&pgtype=inipay&mobilepg=y&pgid="+pgid,
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
<?if($pgStatus == 'menual') {?>
<div id="MSG01">
	<div class="small_ex">�̴Ͻý� ����ϰ��������� �����ڿ��� �����ϱ� ���ؼ��� ��û������ ����� �������� �̿��� üũ�Ͻø� �˴ϴ�.</div>
	<div class="small_ex">�ű� ������ : ��ű԰�� ���� ���û������ ����� �������� ��� üũ �� ����</div>
	<div class="small_ex">���� ������ : ���û������ ����� �������� ��� üũ �� ����</div>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="font:0;height:5px"></div>

<div class="title title_top">
	�̴Ͻý� ����ϰ��� ����<span>���ڰ���(PG)�� ��û���� ����� �������� �̿��� üũ�Ͽ� �����ڿ��� �ſ�ī�� ���� ����� ���������� ������ �� �ֽ��ϴ�.</span>
</div>
<form method="post" action="indb.pg.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="setPg" />
<input type="hidden" name="cfg[settlePg]" value="inipay" />
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td><b>PG�� ��� ����</b></td>
	<td><b>INIpayMobile Web (V 2.4 - 20110725) <?php echo $spot;?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class="noline">
	<? 
	$mobileMethodList = array('c'=>'�ſ�ī��', 'v'=>'�������', 'h'=>'�޴��� ����');
		foreach($mobileMethodList as $key=>$val) {
			unset($disabled[$key]);
			unset($labelColor[$key]);
			unset($checked[$key]);
			if ($set['use_mobile'][$key] == 'on') $checked[$key] = 'checked';
	
			if ($set['use_mobile_ck'][$key]!='on'){
				$disabled[$key] = 'disabled';	
				$labelColor[$key] = "style='color:#cccccc'";
			}

			if($pgStatus != 'auto') {
				unset($disabled);
				unset($labelColor);
			}
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='set[use_mobile][".$key."]' ".$checked[$key]." ".$disabled[$key]." onclick='chkSettleKind()' /> ".$val."</label>";
		}
	?>
	<?if($pgStatus != 'menual'){?>
	<button class="default-btn" type="button" style="padding-top:5px" onclick="methodUpdate()">�������� ���ΰ�ħ</button>
	<br/><span class="extext">����� �������� �߿��� �����Ͽ� ����� �� �ֽ��ϴ�. ���������� �߰��Ϸ��� PG�� �����ͷ� ��û�Ͻʽÿ�.</span>
	<?}?>

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(�ݵ�� �̴Ͻý��� ����� �������ܸ� üũ�ϼ���)</b></font><?}?>
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
		<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?php echo $pg_mobile['id'];?>" onkeyup="chkPgid();" onblur="chkPgid();" id="pgid" disabled="disabled" /></div>
		<div style="float:left;padding:0 0 0 5px" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
		<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� INIPay ID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
		<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
		<?}?>
	</td>
</tr>
<?php for ($i=1; $i<=3; $i++){ ?>
<tr>
	<td><b>�̴Ͻý� <font color="#627dce">Key <?php echo $i;?></b></font></td>
	<td class="ver8"><?if($pgStatus == 'menual'){?><input type="file" name="pg[file_0<?php echo $i;?>]" class="lline" disabled /><?}?> <?php echo $fls['pg'][$i-1];?>
	<?if($pgStatus == 'auto'){?><span class="extext"><b>�ڵ����� �Ϸ�</b><?}?>
	</td>
</tr>
<?php } ?>
<tr>
	<td height=50>�Ϲ��ҺαⰣ</td>
	<td>
		<input type="text" name="pg[quota]" value="<?php echo $pg_mobile['quota'];?>" class="lline" style="width:500px" disabled="disabled" />
		<div class="extext" style="padding-top:5px">ex) <?php echo $_pg_mobile['quota'];?></div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
		<input type="radio" name="pg[zerofee]" value="no" checked disabled="disabled" /> �Ϲݰ���
		<input type="radio" name="pg[zerofee]" value="yes" <?php echo $checked['zerofee']['yes'];?> disabled="disabled" /> �����ڰ���
		<font class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b> (�Ʒ� '������ �Ⱓ' ���� üũ)</font>
	</td>
</tr>
<tr>
	<td height=92>������ �Ⱓ</td>
	<td>
		<input type="text" name=pg[zerofee_period] value="<?php echo $pg_mobile['zerofee_period'];?>" class="lline" style="width:500px" disabled="disabled" />
		<div style="padding-top:7px"><font class="extext" >* ī����ڵ� :  01 (��ȯ), 03 (�Ե�/(��)����), 04 (����), 06 (����), 11 (BC), 12 (�Ｚ), 13 (LG), 14 (����)</div>
		<div style="padding-top:3px">ex) ��ī�� 3���� / 6���� �Һο� �Ｚī�� 3���� ������ ����� �� 11-3:6,12-3 ��� �Է�</div>
		<div style="padding-top:3px">ex) ���ī�忡 ���ؼ� 3���� / 6���� ������ ����� �� ALL-3:6 ��� �Է�</div>
		<div style="padding:3px 0 7px 0">* ������ �Ⱓ�� ����Ϸ��� �ݵ�� ���� �����ڰ����� üũ�ϼ���!</div>
	</td>
</tr>
</table>
<div id="MSG02">
	<div class="small_ex">�̴Ͻý��� ������ �߱� ������ ���� ������ ����ϰ��������� �״�� �̿��ϰ� �˴ϴ�.</div>
	<div class="small_ex">�׷��Ƿ� �̴Ͻý� ��û���� ����ϰ������� �̿��� üũ�Ͻ� ���θ���</div>
	<div class="small_ex">�ݵ�� ����ϼ����� �ſ�ī�� ���� ������������ ���������� ������ �̷������ �׽�Ʈ �� �ּ���</div>
</div>
<script>cssRound('MSG02')</script>

<div class=title>
	���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
		<div>
		<input type="radio" name="pg[receipt]" value="N" <?php echo $checked['receipt']['N']?> /> ������
		<input type="radio" name="pg[receipt]" value="Y" <?php echo $checked['receipt']['Y']?> /> ���(���� �̴Ͻý��� ���ݿ����� ����� ��û�� �� �����ϼ���)
		</div>
		<div class="extext">����ϼ����� ������ ������ ���ݿ������� �����Ͻð��� �ϸ�</div>
		<div class="extext">�� �̴Ͻý� �������������������� ���� ��û�� ��</div>
		<div class="extext">�� �������������� ���ݿ����� ����� �����Ͻð� �����ϼ���.</div>
	</td>
</tr>
</table>
</div>

<div class="button">
	<input type="image" src="../img/btn_save.gif" />
	<a href="javascript:history.back();"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>