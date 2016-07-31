<?
$pg_name = 'lgdacom';
### ������ �⺻ ���ð�
$_pg_mobile		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "������⿬�� > LG U+PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="lgdacom"){
	@include "../../conf/pg_mobile.".$cfg['settlePg'].".php";
	@include "../../conf/pg.".$cfg['settlePg'].".php";
	@include "../../conf/pg_mobile.escrow.php";
}

if (!function_exists('curl_init')) {
	$msg = "LG U+ XPay�� ������ CURL Library�� ��ġ�Ǿ� �־�� �����մϴ�.\\n���� ���� �Ͻðų�, �������� ��� ȣ���þ�ü�� ���� �Ͻʽÿ�.\\nCURL Library�� ���°�� ������ Noteurl ������� ���� �˴ϴ�. ";
	echo("<script>alert('".$msg."');</script>");
	exit;
}

$pg_mobile = @array_merge($_pg_mobile,$pg_mobile);

if($cfg['settlePg']!="lgdacom") $pg_mobile = array(); //pgŸ��üũ

if ($cfg['settlePg']=="lgdacom") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked['receipt'][$pg_mobile['receipt']] = $checked['zerofee'][$pg_mobile['zerofee']] = $checked['serviceType'][$pg_mobile['serviceType']] = "checked";
$checked['use'][$escrow['use']] = "checked";


if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'menual';
}
else if($pg['pg-centersetting']=='Y'){
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

?>
<script language=javascript>

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
	var robj =  new Array('pg[id]','pg[mertkey]','pg[quota]');

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
	var p_key =  document.getElementsByName('pg[mertkey]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('LG U+ PGID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('LG U+ KEY�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!chkPgid()){
		alert('LG U+ PGID�� �ùٸ��� �ʽ��ϴ�.');
		return false;
	}
	<?}?>
	if(!p_quota.value && ret){
		p_quota.focus();
		alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
		return false;
	}

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
	if(chkPgid()){
		alert("�������� LG U+ PGID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� LG U+ PGID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=lgdacom&mobilepg=y&pgid="+pgid,
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
	var pattern = /^(go_|fp_|fd_)/;
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
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td>LG U+ ����ϰ��������� �����ڿ��� �����ϱ� ���ؼ��� ���θ�(����)�� LG U+ ���̵� �߰��� �߱� �����ž� �մϴ�.</td></tr>
<tr><td>����ϰ����� ���̵� �߱� ������ �Ʒ��� �����ϴ�.  [����ϰ��� ���̵� ��û �󼼾ȳ�]</td></tr>
<tr><td>�ű� ������ : ��ű԰�� ���� �����ϰ����� ���̵� �߰� �¶��� ��û �����ϰ����� ���̵� �߰� ��û�� ����</td></tr>
<tr><td>���� ������ : �����ϰ����� ���̵� �߰� �¶��� ��û �����ϰ����� ���̵� �߰� ��û�� ����</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="padding-top:15px"></div>

<div class="title title_top">
LG U+ ����ϰ��� ����<span>���ڰ���(PG)��κ��� �������� ����ϰ��� ������ �����Ͽ� �����ڿ��� �ſ�ī�� ���� ����� ���������� ������ �� �ֽ��ϴ�.</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a>
</div>

<form method="post" action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this);">
<input type="hidden" name="mode" value="setPg">
<input type="hidden" name="cfg[settlePg]" value="lgdacom">

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG��</td>
	<td><b>LG U+ ǥ�ذ���â 2.5 (Smart XPay Ver.1.2 - 20141212) <?=$spot?></b></td>
</tr>
<tr>
	<td>����ϼ���<br/>�������� ����</td>
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

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(�ݵ�� LG U+PG�� ����� �������ܸ� üũ�ϼ���)</b></font><?}?>
	</td>
</tr>
<tr>
	<td>LG U+ <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
	<?
	if($pgStatus == 'auto'){?>
		<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
		</div>
	<?}
	else if($pgStatus == 'disable'){?>
	<span class="extext"><b>���񽺸� ��û�ϸ�  �ڵ������˴ϴ�.</b></span>
	<?}else{?>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg_mobile[id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext">LG U+ PGID�� ��go, fp, fd���� ���۵Ǵ� ���̵� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	<?}?>
	</td>
</tr>
<tr>
	<td><b>LG U+ <font color="#627dce">KEY</font></td>
	<td>
		<?
		if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$pg['mertkey']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
			</div>
		<?}
		else if($pgStatus == 'disable'){?>
			<span class="extext"><b>���񽺸� ��û�ϸ�  �ڵ������˴ϴ�.</b></span>
		<?}
		else{?>
	<input type="text" name="pg[mertkey]" class="lline" value="<?=$pg_mobile['mertkey']?>">
	<font class="extext">mertkey�� ��������</font>
		<?}?>
	</td>
</tr>
<tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$pg_mobile['quota']?>" class="lline">
	<span class="extext">ex) <?=$_pg_mobile['quota']?></span>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
	<input type="radio" name="pg[zerofee]" value="no" checked> �Ϲݰ���
	<input type="radio" name="pg[zerofee]" value="yes" <?=$checked['zerofee']['yes']?>> �����ڰ��� <font class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font>
	</td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>
	<input type="text" name="pg[zerofee_period]" value="<?=$pg_mobile['zerofee_period']?>" class="lline" style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.dacom.php',500,470)" style="color:#616161;" class="ver8"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="extext" style="padding-top:4px">�����ʿ� �ִ� '�����ڱⰣ�ڵ����' ��ư�� ���� �ڵ带 �������� �����Ͽ� ����ϼ���</div>
	</td>
</tr>
<input type="hidden" name="pg[serviceType]" value="service">
<!--<tr>
	<td>���� Ÿ��</td>
	<td class="noline">
	<input type="radio" name="pg[serviceType]" value="service" <?=$checked['serviceType']['service']?>> service
	<input type="radio" name="pg[serviceType]" value="test" <?=$checked['serviceType']['test']?>> test
	</td>
</tr>-->
</table>

<div style="padding-top:15px"></div>

<div id="MSG02">
<?if($pgStatus == 'menual'){?>
<div class="small_ex">LG U+�� ��û�� ����ϰ������� �� ����ϼ����� �̿��ϰ����ϴ� ����ϰ��������� üũ �� ��</div>
<div class="small_ex">LG U+�κ��� �������� ����ϰ����� ID�� KEY ������ �Է��ϰ� �����ϼ���.</div>
<div class="small_ex">���� �� �ݵ�� ����ϼ����� �ſ�ī�� ���� ������������ ���������� ������ �̷������ �׽�Ʈ �� �ּ���.</div>
<?}else{?>
<div class="small_ex">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�.</div>
<?}?>
<div style="font:0;height:5px;"></div>
<div class="small_ex">�� LG U+ PG�翡�� �����ϴ� ���������� ���� ���ǻ��� - <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_dacom_pg.html',830,680)"><img src="../img/btn_dacompg_sample.gif" align="absmiddle"></a></div>
<div class="small_ex">�� LG U+ ���������ڿ��� '���ΰ�����ۿ���'�� '����OSŸ��'�� �Ʒ��� ���� �����ϼ���. </div>
<div class="small_ex">'���ΰ�����ۿ���' ������ '����(����â2.0)' ���� �����Ͻð�, '����OSŸ��'�� 'LINUX�迭'�� ������ ������ �ֽñ� �ٶ��ϴ�. </div>
<div class="small_ex">�� �� ������ ��� �����ϰ� 1�ð� �Ŀ� ���θ����� �ſ�ī����� �׽�Ʈ�� �غ��ž� ������ ����� �ݿ��Ǿ� ���������� ������ �̷�����ϴ�.</div>
</div>
<script>cssRound('MSG02')</script>

<!-- LG ����ũ�� -->
<div class="title">����ũ�� ���� <span>���ݼ� ������ �ǹ������� ����ũ�ΰ����� ����ؾ� �մϴ�. ����ũ�ζ�?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a></div>
<input type="hidden" name="mobile_escrow[pg]" value="lgdacom">	<!-- PG�� -->
<input type="hidden" name="mobile_escrow[comp]" value="PG">	<!-- ����ũ�� ������� -->

<div class="extext">���� LG U+ ����ũ�θ� ��û���� �����̴ٸ� <a href="http://pgweb.dacom.net" target="_blank" class="extext" style="font-weight:bold">LG U+ ����������(http://pgweb.dacom.net)���� ��û</a>�� �ּ���.</div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>��뿩��</td>
	<td class="noline">
	<input type="radio" name="mobile_escrow[use]" value="Y" <?=$checked['use']['Y']?>> ���
	<input type="radio" name="mobile_escrow[use]" value="N" <?=$checked['use']['N']?>> ������
	&nbsp;&nbsp;&nbsp;<font class="extext"><b>(LG U+ ����ũ�θ� ��û�ϼ̴ٸ� ������� üũ�ϼ���)</b></font>
	</td>
</tr>
<tr>
	<td>���� ����</td>
	<td class="noline">
	<?
		$methodEscrowList = array('c'=>'�ſ�ī��'/*, 'o'=>'������ü'*/, 'v'=>'�������');
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
			echo "<label ".$labelColor[$key]."><input type='checkbox' name='mobile_escrow[".$key."]' ".$checked[$key]." ".$disabled[$key]."   /> ".$val."</label>";
		}
	?>
	</td>
</tr>
<tr>
	<td>��� �ݾ�</td>
	<td>
	<input type="text" name="mobile_escrow[min]" value="<?=$escrow[min]?>" class="lline" onkeydown="onlynumber()" style="width:100px;">
	<div class="extext"  style="padding-top:4px">PG�縶�� ����ũ�� ������ ��� �ݾ׿� ������ �ȵɼ��� �����Ƿ�, �ݵ�� ������ PG���� ����ũ�� ��೻���� �� Ȯ���ϼ���.</div>
	</td>
</tr>
</table>

<div id="MSG04">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td style="padding-bottom:10px;"><img src="../img/icon_list.gif" align="absmiddle">���ž������� �ǹ� ���� Ȯ�� (2013�� 11�� 29�� ����)</font></td></tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">�� ���� ����
		<div style="padding-left:20px;">
		5���� ���� �ŷ��� ���ؼ��� �Һ����� ������ ��ȣ�ϱ� ���Ͽ� ���ž������� �ǹ� ���� ��� Ȯ��<br />
		1ȸ ���� ����, 5���� �̻� �� 5���� ������ �Ҿ� �ŷ�(��� �ݾ�)
		</div>
	</td>
</tr>
<tr>
	<td>
		<img src="../img/icon_list.gif" align="absmiddle">�� ���� ����
		<div style="padding-left:20px;">
		���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ����<br />
		[ ���� ��11841ȣ, ������: 2013.5.28, �Ϻ� ���� ]
		</div>
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG04')</script>
<!-- LG ����ũ�� -->


<div class="title">���ݿ����� <!--span>������ PG���� ���ݿ������� ����ϸ�, ���� ��� �ʿ����</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
	<div>
	<?if($pg['receipt']=='N'):?>
	�̻��
	<?else:?>
	���
	<?endif;?>
	<input type="hidden" name="pg[receipt]" value="<?=$pg[receipt]?>">
	</div>
	<div class="extext">����ϼ����� ������ ������ ���ݿ����� ���� �����Դϴ�.</div>
	<div class="extext">���θ� �⺻���� > ���� ���ڰ��� ���� > LG U+���� ������</div>
	<div class="extext">���ݿ����� ��뿩�� ������ �����մϴ�. <a class="extext" style="font-weight:bold" href="http://ecredit.uplus.co.kr/renewal/html/AddiService/addser03.htm" target="_blank">[�ٷΰ���]</a></font></div>
	</td>
</tr>
</table><p>


<div id="MSG03">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ�����</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ�
5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ��� üũ�� ������, ������� ������ ���ؼ� �Һ��ڰ� ��û�� ���ݿ������� �߱� �˴ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div class="button">
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<script>chkSettleKind();</script>