<?

### �̴Ͻý� �⺻ ���ð�
$_pg		= array(
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '����:�Ͻú�:2����:3����:4����:5����:6����:7����:8����:9����:10����:11����:12����',
			);

$location = "������⿬�� > �̴Ͻý�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// �����̼� pg ������ �ҷ�����
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg'] != "inicis") $tsPG = array(); // ������� �ƴ϶�� pg ���� ����

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="inicis") $tsPG['pg'] = array(); //pgŸ��üũ

if ($tsPG['cfg']['settlePg']=="inicis" && $tsPG['pg']['id']) $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";

if ($tsPG['set']['use'][c]) $checked[c] = "checked";
if ($tsPG['set']['use'][o]) $checked[o] = "checked";
if ($tsPG['set']['use'][v]) $checked[v] = "checked";
if ($tsPG['set']['use'][h]) $checked[h] = "checked";

if ($tsPG['cfg']['settlePg']=="inicis"){
	$dir = "../../todayshop/card/inicis/key/";

	if (is_dir($dir.$tsPG['pg']['id'])){
		$od = opendir($dir.$tsPG['pg']['id']);
		while ($rd=readdir($od)){
			if (!ereg("\.$",$rd)) $fls['pg'][] = $rd;
		}
		closedir($od);
	}
}

// php 5�̻��� ��� inipay ���� ��ư
if (substr(phpversion(),0,1) >= 5) {
	$inipayBtn	= "<img src=\"../img/btn_inipayChange.gif\" border=\"0\" align=\"absmiddle\" class=\"hand\" alt=\"INIPay TX5 ���� ���� ��ȯ\" onclick=\"inipayChange();\">";
}
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
	var robj =  new Array('pg[id]');

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

	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
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
		parameters: "mode=getPginfo&pgtype=inicis&todayshoppg=y&pgid="+pgid,
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
	var pattern = /^(GODO|GDP|GOSO)/;
	if(pattern.test(obj.value)  || (oldId == obj.value && oldId)){
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

function inipayChange(){
	if(confirm('INIPay TX5 ���� ���� ��ȯ �Ͻðڽ��ϱ�?\r\n��ȯ�� �ϸ� Űȭ���� ���簡 �˴ϴ�.\r\n��ȯ�� Űȭ�� ���ε� ���� ������ ���� �� ������ �Ͻø� �˴ϴ�.') == true){
		var inicisKeyName	= document.getElementById('pgid').value;
		var ajax = new Ajax.Request( "./indb.config.pg.php",
		{
			method: "post",
			parameters: "mode=inipayKeyCopy&inicisKeyName="+inicisKeyName,
			onComplete: function ()
			{
				var req = ajax.transport;
				parent.chgifrm('config.pg.inc.inipay.php',3);
			}
		} );
	}
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
<div class="title title_top">�̴Ͻý�PG ����<!--span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="inicis_banner"><script>panel('inicis_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" enctype="multipart/form-data" onsubmit="return chkFormThis(this)" />
<input type=hidden name=mode value="inicis">
<input type=hidden name=cfg[settlePg] value="inicis">
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td colspan="2">
�̴Ͻý����� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�<BR>
�̴Ͻý����� <b>���Ϸ� ������ ���������� Ǯ� INIPay ID�� Key File 3���� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.<BR>
���� �̴Ͻý��� ����� ���� �����̴ٸ� ��<u>�¶��ν�û �Ͻ���</u> ��<u>��༭���� �������� �̴Ͻý��� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank"><font color='#ffffff'><b>[��� �󼼾ȳ�]<b/></font></a>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="font:0;height:5"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td class=ver8><b>PG��</b></td>
	<td><b>�̴Ͻý� (INIPay V4.119) <?=$spot?></b> <?=$inipayBtn?></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class=noline>
	<label><input type=checkbox name=set[use][c] <?=$checked[c]?> onclick="chkSettleKind()"> �ſ�ī��</label>
	<label><input type=checkbox name=set[use][o] <?=$checked[o]?> onclick="chkSettleKind()"> ������ü</label>
	<!--label><input type=checkbox name=set[use][v] <?=$checked[v]?> onclick="chkSettleKind()"> �������</label-->
	<label><input type=checkbox name=set[use][h] <?=$checked[h]?> onclick="chkSettleKind()"> �ڵ���</label>
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(�ݵ�� �̴Ͻý��� ����� �������ܸ� üũ�ϼ���)</b></font>
	</td>
</tr>
<tr>
	<td class=ver8><b>INIPay ID</b></td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext">��GODO,GDP,GOSO���� ���۵Ǵ� INIPay ID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	</td>
</tr>
<? for ($i=1;$i<=3;$i++){ ?>
<tr>
	<td class=ver8><b>INIPay Key File #<?=$i?></b></td>
	<td class=ver8><input type=file name=pg[file_0<?=$i?>] class=lline> <?=$fls[pg][$i-1]?></td>
</tr>
<? } ?>
<tr>
	<td height=50>�Ϲ��ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$tsPG['pg'][quota]?>" class=lline style="width:500px">
	<div class=extext  style="padding-top:5px">ex) <?=$_pg[quota]?></div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> �Ϲݰ���
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> �����ڰ��� <font class=extext><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b> (�Ʒ� '������ �Ⱓ' ���� üũ)</font></td>
</tr>
<tr>
	<td height=92>������ �Ⱓ</td>
	<td><input type=text name=pg[zerofee_period] value="<?=$tsPG['pg'][zerofee_period]?>" class=lline style="width:500px">
	<div style="padding-top:7px"><font class=extext >* ī����ڵ� :  01 (��ȯ), 03 (�Ե�/(��)����), 04 (����), 06 (����), 11 (BC), 12 (�Ｚ), 13 (LG), 14 (����)</div>
	<div style="padding-top:3px">ex) ��ī�� 3���� / 6���� �Һο� �Ｚī�� 3���� ������ ����� �� 11-3:6,12-3 ��� �Է�</div>
	<div style="padding-top:3px">ex) ���ī�忡 ���ؼ� 3���� / 6���� ������ ����� �� ALL-3:6 ��� �Է�</div>
	<div style="padding:3px 0 7px 0">* ������ �Ⱓ�� ����Ϸ��� �ݵ�� ���� �����ڰ����� üũ�ϼ���!</div></td>
</tr>
</table>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<BR><font class=extext style="padding-left:5px">�̴Ͻý� ���ݿ����� �̿��� �̴Ͻý� ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://www.inicis.com/ini_22.jsp" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>
</div>

<div id=MSG04>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008.7.1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ� 5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG04')</script>


<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>