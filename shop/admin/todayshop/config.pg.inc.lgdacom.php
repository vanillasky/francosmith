<?

### ������ �⺻ ���ð�
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "������⿬�� > LG U+ PG����";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// �����̼� pg ������ �ҷ�����
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg']!="lgdacom") $tsPG = array(); // ������� �ƴ϶�� pg ���� ����

if (!function_exists('curl_init')) {
	$msg = "LG U+ XPay�� ������ CURL Library�� ��ġ�Ǿ� �־�� �����մϴ�.\\n���� ���� �Ͻðų�, �������� ��� ȣ���þ�ü�� ���� �Ͻʽÿ�.\\nCURL Library�� ���°�� ������ Noteurl ������� ���� �˴ϴ�. ";
	echo("<script>alert('".$msg."');parent.chgifrm('dacom.php',2);</script>");
}

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="lgdacom") $tsPG['pg'] = array(); //pgŸ��üũ

if ($tsPG['cfg']['settlePg']=="lgdacom") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";
$checked['skin'][$tsPG['pg']['skin']] = "checked";
$checked['serviceType'][$tsPG['pg']['serviceType']] = "checked";

if ($tsPG['set']['use']['c']) $checked['c'] = "checked";
if ($tsPG['set']['use']['o']) $checked['o'] = "checked";
if ($tsPG['set']['use']['v']) $checked['v'] = "checked";
if ($tsPG['set']['use']['h']) $checked['h'] = "checked";
?>
<script language=javascript>

var arr=new Array('c','v','o','h');

function chkSettleKind(){
	var f = document.forms[0];

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[mertkey]','pg[quota]');

	for(var i=0;i < robj.length;i++){
		if (document.getElementsByName(robj[i]).length == 0) continue;
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
	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('LG U+ ID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('LG U+ mertkey�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_quota.value && ret){
		p_quota.focus();
		alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!chkPgid()){
		alert('LG U+ ID�� �ùٸ��� �ʽ��ϴ�.');
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

var oldId = "<?php echo $tsPG['pg']['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("�������� LG U+ ID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� LG U+ ID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=lgdacom&todayshoppg=y&pgid="+pgid,
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
	var pattern = /^(go|fp|fd|gs)[a-zA-Z0-9_]+/;
	if(pattern.test(obj.value) || (oldId == obj.value && oldId)){
		return true;
	}else if(obj.value){
		return false;
	}
	return true;
}

window.onload = function(){
	resizeFrame();
	chkPgid();
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
LG U+PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a>
</div>
<div id="dacom_banner"><script>panel('dacom_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type="hidden" name="mode" value="lgdacom">
<input type="hidden" name="cfg[settlePg]" value="lgdacom">

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td>LG U+���� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>LG U+���� <b>���Ϸ� ������ LG U+ ID�� mertkey�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� LG U+�� ����� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10px">��<u>�¶��ν�û �Ͻ���</u></td></tr>
<tr><td style="padding-left:10px">��<u>��༭���� �������� LG U+�� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[��� �󼼾ȳ�]</a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15px"></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG��</td>
	<td><b>LG U+ (XPay 1.0 - ����â2.0) <?=$spot?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class="noline">
	<label><input type="checkbox" name="set[use][c]" <?=$checked['c']?> onclick="chkSettleKind();"> �ſ�ī��<label>
	<label><input type="checkbox" name="set[use][o]" <?=$checked['o']?> onclick="chkSettleKind();"> ������ü<label>
	<!--label><input type="checkbox" name="set[use][v]" <?=$checked['v']?> onclick="chkSettleKind();"> �������<label-->
	<label><input type="checkbox" name="set[use][h]" <?=$checked['h']?> onclick="chkSettleKind();"> �޴���<label>
	&nbsp;&nbsp;&nbsp;<font class="extext"><b>(�ݵ�� LG U+PG��� ����� �������ܸ� üũ�ϼ���)</b></font></td>
</tr>
<tr>
	<td class="ver8"><b>LG U+ ID</td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext">LG U+ ID�� ��go,fp,fd,gs���� ���۵Ǵ� ���̵� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	</td>
</tr>
<tr>
	<td class="ver8"><b>LG U+ mertkey</td>
	<td>
	<input type="text" name="pg[mertkey]" class="lline" value="<?=$tsPG['pg']['mertkey']?>">
	</td>
</tr>
<tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$tsPG['pg']['quota']?>" class="lline">
	<span class="extext">ex) <?=$_pg['quota']?></span>
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
	<input type="text" name="pg[zerofee_period]" value="<?=$tsPG['pg']['zerofee_period']?>" class="lline" style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.dacom.php',500,470)" style="color:#616161;" class="ver8"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="extext" style="padding-top:4px">�����ʿ� �ִ� '�����ڱⰣ�ڵ����' ��ư�� ���� �ڵ带 �������� �����Ͽ� ����ϼ���</div>
	</td>
</tr>
<tr>
	<td>����â ����</td>
	<td class="noline">
	<input type="radio" name="pg[skin]" value="red" <?=$checked['skin']['red']?>> Red
	<input type="radio" name="pg[skin]" value="blue" <?=$checked['skin']['blue']?>> Blue
	<input type="radio" name="pg[skin]" value="cyan" <?=$checked['skin']['cyan']?>> Cyan
	<input type="radio" name="pg[skin]" value="green" <?=$checked['skin']['green']?>> Green
	<input type="radio" name="pg[skin]" value="yellow" <?=$checked['skin']['yellow']?>> Yellow
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
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� ID, Key�� �����ø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white"><b>�� LG U+ PG�� ����� ������ ���ǻ��� (�ʵ�!)</b></font></td></tr>
<tr><td height=8></td></tr>
<tr><td><font class="def1" color="white">- �̰����� LG U+ PG ������ ���ǻ��� -</b></font></td></tr>
<tr><td>�� ����� ���Ϸ� ���� 'LG U+ ID' �� 'LG U+ mertkey'�� ����Է¶��� ��Ȯ�ϰ� �Է��ϼ���.</td></tr>
<tr><td>�� LG U+PG��� ����� �� �ݵ�� ��������� ��ġ�ϵ��� �� ����� '�������ܼ���'�� ���ּž� �մϴ�.</td></tr>
<tr><td>(��, �ſ�ī��, ������ü�� ���ü���ߴٸ� �ݵ�� �ΰ����� üũ�ؾ� �մϴ�. ���� ������±��� üũ�ϸ� ���������� �߻��˴ϴ�)</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class="def1" color="white">- LG U+PG�翡�� �����ϴ� �����ڸ�� ������ ���ǻ��� -</b></font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_lgdacom_pg.html',830,680)"><img src="../img/btn_dacompg_sample.gif" align="absmiddle"></a></td></tr>
<tr><td>�� LG U+ �����ڸ�忡 ���� '���ΰ�����ۿ���'�� '����OSŸ��'�� �Ʒ��� ���� �����ϼ���.</td></tr>
<tr><td>'���ΰ�����ۿ���' ������  '����(����â2.0)' ���� �����Ͻð�,	'����OSŸ��'��  'LINUX�迭'�� ������ ������ �ֽñ� �ٶ��ϴ�.</td></tr>
<tr><td>�� �� ������ ��� �����ϰ� 1�ð� �Ŀ� ���θ����� �ſ�ī����� �׽�Ʈ�� �غ��ž� ������ ����� �ݿ��Ǿ� ���������� ������ �̷�����ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class="title">���ݿ����� <!--span>������ PG���� ���ݿ������� ����ϸ�, ���� ��� �ʿ����</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=8')"><img src="../img/btn_q.gif" border=0 align="absmiddle"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
	<input type="radio" name="pg[receipt]" value="N" <?=$checked['receipt']['N']?>> ������
	<input type="radio" name="pg[receipt]" value="Y" <?=$checked['receipt']['Y']?>> ���
	<BR><font class="extext" style="padding-left:5px">LG U+ ���ݿ����� �̿��� LG U+ ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://ecredit.lgdacom.net/renewal/html/AddiService/addser03.htm" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>

<div id="MSG03">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ�����</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ�
5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ��� üũ�� ������, ������ü, ������� ������ ���ؼ� �Һ��ڰ� ��û�� ���ݿ������� �߱� �˴ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div class="button">
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<script>chkSettleKind();</script>