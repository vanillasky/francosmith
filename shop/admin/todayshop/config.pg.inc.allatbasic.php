<?

### �þ����� �⺻ ���ð�
$_pg		= array(
			'ssl'		=> 'NOSSL',
			'cert'		=> 'Y',
			//'bonus'		=> 'N',
			'sell'		=> 'N',
			'zerofee'	=> 'N',
			'receipt'	=> 'N',
			//'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "������⿬�� > �þ�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";

// �����̼� pg ������ �ҷ�����
$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);
if ($tsPG['cfg']['settlePg'] != "allatbasic") $tsPG = array(); // ������� �ƴ϶�� pg ���� ����

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if($tsPG['cfg']['settlePg']!="allatbasic") $tsPG['pg'] = array(); //pgŸ��üũ

if ($tsPG['cfg'][settlePg]=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['sell'][$tsPG['pg']['sell']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = "checked";
$checked['receipt'][$tsPG['pg']['receipt']] = "checked";

if ($tsPG['set']['use'][c]) $checked[c] = "checked";
if ($tsPG['set']['use'][o]) $checked[o] = "checked";
if ($tsPG['set']['use'][v]) $checked[v] = "checked";
if ($tsPG['set']['use'][h]) $checked[h] = "checked";

// �����Ƚ���
$prefix = 'GC|GM|GP|GF';
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
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
	var robj =  new Array('pg[id]','pg[crosskey]');

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
	var p_crosskey = document.getElementsByName('pg[crosskey]')[0];
	//var p_quota = document.getElementsByName('pg[quota]')[0];

	for(var i=0;i < arr.length;i++)
	{
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('All@Pay ID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_crosskey.value && ret){
		p_crosskey.focus();
		alert('All@Pay CrossKey�� �ʼ��׸��Դϴ�.');
		return false;
	}
	/*if(!p_quota.value && ret){
		p_quota.focus();
		alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
		return false;
	}*/
	if(!chkPgid()){
		alert('All@Pay ID�� �ùٸ��� �ʽ��ϴ�.');
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
		alert("�������� All@Pay ID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� All@Pay ID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=allatbasic&todayshoppg=y&pgid="+pgid,
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
�þ�PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="allatbasic_banner"><script>panel('allatbasic_banner', 'pg');</script></div>
<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type=hidden name=mode value="allatbasic">
<input type=hidden name=cfg[settlePg] value="allatbasic">

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>�þܿ��� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>�þܿ��� <b>���Ϸ� ������ All@Pay ID�� Form Key, CrossKey�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� �þܿ� ����� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ���</u></td></tr>
<tr><td style="padding-left:10">��<u>��༭���� �������� �þܿ� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[��� �󼼾ȳ�]</a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15"></div>

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG��</td>
	<td class=noline><b>�þ� (All@Pay�� BASIC) <?=$spot?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class=noline>
	<label><input type=checkbox name=set[use][c] <?=$checked[c]?> onclick="chkSettleKind()"> �ſ�ī��</label>
	<label><input type=checkbox name=set[use][o] <?=$checked[o]?> onclick="chkSettleKind()"> ������ü</label>
	<!--label><input type=checkbox name=set[use][v] <?=$checked[v]?> onclick="chkSettleKind()"> �������</label-->
	<label><input type=checkbox name=set[use][h] <?=$checked[h]?> onclick="chkSettleKind()"> �ڵ���</label>
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(�ݵ�� �þܰ� ����� �������ܸ� üũ�ϼ���)</b></font>
	</td>
</tr>
<tr class=ver8>
	<td><b>All@Pay ID</td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg'][id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� All@Pay ID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	</td>
</tr>
<tr class=ver8>
	<td><b>All@Pay CrossKey</td>
	<td><input type=text name=pg[crosskey] class=lline value="<?=$tsPG['pg'][crosskey]?>"> <font class=extext>CrossKey�� ��������</td>
</tr>
<?
$pg_ssl = $sitelink->old_get_type();
?>
<input type=hidden name=pg[ssl] value="<?=$pg_ssl?>">
<!--tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$tsPG['pg'][quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg[quota]?></span>
	</td>
</tr-->
<tr>
	<td>�Һ� ��� ����</td>
	<td class=noline>
	<input type=radio name=pg[sell] value="Y" <?=$checked[sell][Y]?>> �Һλ��
	<input type=radio name=pg[sell] value="N" <?=$checked[sell][N]?>> �Һλ�����	
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="N" <?=$checked[zerofee][N]?>> �Ϲݰ���
	<input type=radio name=pg[zerofee] value="Y" <?=$checked[zerofee][Y]?>> �����ڰ��� <font class=extext><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font></td>
</tr>
<tr>
	<td>ī����� ���� ����</td>
	<td class=noline>
	<input type=radio name=pg[cert] value="Y" <?=$checked[cert][Y]?>> �����
	<input type=radio name=pg[cert] value="N" <?=$checked[cert][N]?>> ������
	</td>
</tr>
<!--tr>
	<td>���ʽ� ����Ʈ</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> ���
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> ������
	</td>
</tr-->
</table>
<div style="padding-top:5"></div>
<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� ID, Key�� �����ø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>���ݿ����� <!--span>������ PG���� ���ݿ������� ����ϸ�, ���� ����� �ؾ� ��</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<BR><font class=extext style="padding-left:5px">�Ｚ�þ� ���ݿ����� �̿��� �Ｚ�þ� ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://www.allatpay.com/servlet/AllatBiz/svcinfo/si_receipt_apply.jsp?menu_id=idS16" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>

<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ�����</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ�
5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ��� üũ�� ������, ������ü, ������� ������ ���ؼ� �Һ��ڰ� ��û�� ���ݿ������� �߱� �˴ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>