<?

### �ô�����Ʈ �⺻ ���ð�
$_pg		= array(
			'id'		=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = '������⿬�� > �ô�����ƮPG ����';
include "../_header.popup.php";
include "../../conf/config.pay.php";

// �����̼� pg ������ �ҷ�����
$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsPG = $todayShop->getPginfo();
unset($todayShop);

if ($tsPG['cfg']['settlePg'] != 'agspay') $tsPG = array(); // ������� �ƴ϶�� pg ���� ����

$tsPG['pg'] = @array_merge($_pg,$tsPG['pg']);

if ($tsPG['cfg']['settlePg'] == 'agspay') $spot = '<b style="color:#ff0000;padding-left:10px">[�����]</b>';
$checked['ssl'][$tsPG['pg']['ssl']] = $checked['zerofee'][$tsPG['pg']['zerofee']] = $checked['cert'][$tsPG['pg']['cert']] = $checked['bonus'][$tsPG['pg']['bonus']] = 'checked';
$checked['receipt'][$tsPG['pg']['receipt']] = 'checked';

if ($tsPG['set']['use']['c']) $checked['c'] = 'checked';
if ($tsPG['set']['use']['o']) $checked['o'] = 'checked';
if ($tsPG['set']['use']['v']) $checked['v'] = 'checked';
if ($tsPG['set']['use']['h']) $checked['h'] = 'checked';

// �����Ƚ���
$prefix = 'gdso|gda|gdfp|gdf';
?>
<script language="javascript">
<!--
var prefix = '<? echo $prefix;?>';
var arr = new Array('c','v','o','h');

function chkSettleKind()
{
	var f = document.forms[0];

	var ret = false;
	for (var i=0; i < arr.length; i++) {
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}
	var robj =  new Array('pg[id]','pg[quota]');

	for (var i=0; i < robj.length; i++) {
		if (document.getElementsByName(robj[i]).length == 0) continue;
		var obj = document.getElementsByName(robj[i])[0];
		if (ret) {
			obj.style.background = '#ffffff';
			obj.readOnly = false;
		} else {
			obj.style.background = '#e3e3e3';
			obj.readOnly = true;
			obj.value = '';
		}
	}
}

function chkFormThis(f)
{
	var ret = false;
	var sk = false;
	var p_id = document.getElementsByName('pg[id]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];

	for (var i=0; i < arr.length; i++) {
		if (document.getElementsByName('set[use]['+arr[i]+']').length == 0) continue;
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}

	if (!p_id.value && ret) {
		p_id.focus();
		alert('AGSPay ID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if (!p_quota.value && ret) {
		p_quota.focus();
		alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!chkPgid()){
		alert('AGSPay ID�� �ùٸ��� �ʽ��ϴ�.');
		return false;
	}
	return chkForm(f);
}
var IntervarId;

function resizeFrame()
{
	var oBody = document.body;
	var oFrame = parent.document.getElementById('pgifrm');
	var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;

	if ( IntervarId ) clearInterval( IntervarId );
}

var oldId = "<?php echo $tsPG['pg']['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("�������� AGSPay ID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� AGSPay ID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=allthegate&todayshoppg=y&pgid="+pgid,
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
//-->
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
�ô�����ƮPG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� �������Ҽ��� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/manual_basic.php#acount',870,800)"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>


<form name="frmPGConfig" method="post" action="indb.config.pg.php" target="ifrmHidden" onsubmit="return chkFormThis(this)" />
<input type="hidden" name="mode" value="agspay">
<input type="hidden" name="cfg[settlePg]" value="agspay">

<!-- PG ���� -->
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td>�ô�����Ʈ���� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>�ô�����Ʈ���� <b>���Ϸ� ������ AGSPay ID�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� �ô�����Ʈ�� ����� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ���</u></td></tr>
<tr><td style="padding-left:10">��<u>��༭���� �������� �ô�����Ʈ�� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[��� �󼼾ȳ�]</a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:15"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG��</td>
	<td>�ô�����Ʈ (AGSPay V4.0 for PHP) <?=$spot?></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class="noline">
	<label><input type="checkbox" name="set[use][c]" <?=$checked[c]?> onclick="chkSettleKind()" /> �ſ�ī��</label>
	<label><input type="checkbox" name="set[use][o]" <?=$checked[o]?> onclick="chkSettleKind()" /> ������ü</label>
	<!--label><input type="checkbox" name="set[use][v]" <?=$checked[v]?> onclick="chkSettleKind()" /> �������</label-->
	<label><input type="checkbox" name="set[use][h]" <?=$checked[h]?> onclick="chkSettleKind()" /> �ڵ���</label>
	&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� �ô�����Ʈ�� ����� �������ܸ� üũ�ϼ���)</b></span>
	</td>
</tr>
<tr>
	<td>AGSPay ID</td>
	<td>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$tsPG['pg']['id']?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� AGSPay ID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	</td>
</tr>
<tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$tsPG['pg']['quota']?>" class="lline">
	<div class="extext" style="padding-top:5px">����â�� ǥ�õǴ� �ҺαⰣ�� ���� �����Ͽ� ������ �ʴ� �Һ� �ŷ��� ������ �� �ֽ��ϴ�.<br/>ex) <?=$_pg[quota]?></div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
	<label><input type="radio" name="pg[zerofee]" value="no" checked /> �Ϲݰ���</label>
	<label><input type="radio" name="pg[zerofee]" value="yes" <?=$checked[zerofee][yes]?> /> �����ڰ���</label> <span class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b> (�Ʒ� '������ �Ⱓ' ���� üũ)</span>
	</td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>
	<input type="text" name="pg[zerofee_period]" value="<?=$tsPG['pg']['zerofee_period']?>" class="lline" style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.agspay.php',450,500)"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="small extext">ex) ��� �Һΰŷ��� �����ڷ� �ϰ� ���������� ALL�� ����<br/>ex) ����,��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����) �� 200-2:3:4:5:6,300-2:3:4:5:6</div>
	</td>
</tr>
<tr>
	<td>�ڵ��� SUB_CPID</td>
	<td>
	<input type="text" name="pg[sub_cpid]" class="lline" value="<?=$tsPG['pg']['sub_cpid']?>">
	<div class="small extext">�ڵ��������� ��û�Ͽ� ���Ϸ� ������ SUB_CPID�� �Է��մϴ�.</div>
	</td>
</tr>
</table>

<div style="padding-top:5px"></div>
<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� AGSPay ID�� �����ø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>
<!-- //PG ���� -->

<!-- ���ݿ����� ���� -->
<div class=title>���ݿ�����</div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<BR><span class=extext style="padding-left:5px">�ô�����Ʈ ���ݿ����� �̿��� �ô�����Ʈ ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://www.allthegate.com/ags/add/add_08.jsp" target="_blank">[�ٷΰ���]</a></span>
	</td>
</tr>
</table>

<div style="padding-top:5px"></div>
<div id="MSG04">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ� 5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� ��� üũ�� ������, ������ü, ������� ������ ���ؼ� �Һ��ڰ� ��û�� ���ݿ������� �߱� �˴ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG04')</script>
<!-- //���ݿ����� ���� -->

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<script>chkSettleKind();</script>