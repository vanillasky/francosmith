<?
$pg_name = 'allatbasic';

### �þ����� �⺻ ���ð�
$_pg_mobile		= array(
			'ssl'		=> 'NOSSL',
			'cert'		=> 'Y',
			'bonus'		=> 'N',
			'zerofee'	=> 'N',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = "������⿬�� > �þ�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="allatbasic"){
	@include "../../conf/pg_mobile.$cfg[settlePg].php";
	@include "../../conf/pg.".$cfg['settlePg'].".php";
}

$pg_mobile = @array_merge($_pg_mobile,$pg_mobile);

if($cfg['settlePg']!="allatbasic") $pg_mobile = array(); //pgŸ��üũ

if ($cfg[settlePg]=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked[ssl][$pg_mobile[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg_mobile[cert]] = $checked[bonus][$pg_mobile[bonus]] = "checked";
$checked[receipt][$pg_mobile[receipt]] = "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

// �����Ƚ���
$prefix = 'GM|GP|GF';
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
	var robj =  new Array('pg[id]','pg[crosskey]','pg[quota]');

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
	var p_crosskey = document.getElementsByName('pg[crosskey]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];

	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	<?if($pgStatus == 'menual'){?>
		if(!p_id.value && ret){
			p_id.focus();
			alert('�þ� PGID�� �ʼ��׸��Դϴ�.');
			return false;
		}
		if(!p_crosskey.value && ret){
			p_crosskey.focus();
			alert('�þ� KEY�� �ʼ��׸��Դϴ�.');
			return false;
		}
		if(!p_quota.value && ret){
			p_quota.focus();
			alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
			return false;
		}
		if(!chkPgid()){
			alert('�þ� PGID�� �ùٸ��� �ʽ��ϴ�.');
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
	if(chkPgid()){
		alert("�������� �þ� PGID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� �þ� PGID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=allatbasic&mobilepg=y&pgid="+pgid,
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
	<div id=MSG01>
	<div class="small_ex">�Ｚ�þ� ����ϰ��������� �����ڿ��� �����ϱ� ���ؼ��� ���θ�(����)�� �Ｚ�þ� ���̵� �߰��� �߱� �����ž� �մϴ�.</div>
	<div class="small_ex">����ϰ����� ���̵� �߱� ������ �Ʒ��� �����ϴ�.</div>
	<div class="small_ex">�ű� ������ : ��ű԰�� ���� �����ϰ����� ���̵� �߰� ��û�� ���� (�ѽ�: 02-3783-9833)</div>
	<div class="small_ex">���� ������ : �����ϰ����� ���̵� �߰� ��û�� ���� (�ѽ�: 02-3783-9833)</div>
	</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="padding-top:15"></div>

<div class="title title_top">
�Ｚ�þ� ����ϰ��� ����<span>���ڰ���(PG)��κ��� �������� ����ϰ��� ������ �����Ͽ� �����ڿ��� �ſ�ī�� ���� ����� ���������� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>

<form method=post action="indb.pg.php" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="setPg">
<input type=hidden name=cfg[settlePg] value="allatbasic">

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG��</td>
	<td class=noline><b>�Ｚ�þ�<!--(All@Pay�� Plus 2.0)--><?=$spot?></b></td>
</tr>
<tr>
	<td>����ϼ���<br/>�������� ����</td>
	<td class=noline>
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

	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<font class=extext><b>(�ݵ�� �þܰ� ����� �������ܸ� üũ�ϼ���)</b></font><?}?>
	</td>
</tr>

<tr>
	<td>�þ� <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
	<?if($pgStatus == 'auto'){?>
		<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
		</div>
	<?}
	else if($pgStatus == 'disable'){?>
		<span class="extext"><b>���񽺸� ��û�ϸ�  �ڵ������˴ϴ�.</b></span>
	<?}
	else{?>
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg_mobile[id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� �þ� PGID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	<?}?>
	</td>
</tr>
<tr>
	<td>�þ� <font color="#627dce">KEY</font></td>
	<td>
	<?if($pgStatus!='menual'){?>
	<?if(($pg['crosskey'])){ 
		echo "<b>".$pg['crosskey']."</b>&nbsp;<span class='extext'><b>�ڵ����� �Ϸ�</b></span><br/>";
	 }?>
	<?}
	else{?>	
	<input type=text name=pg[crosskey] class=lline value="<?=$pg_mobile[crosskey]?>"> <font class=extext>CrossKey�� ��������
	<?}?>
	</td>
</tr>
<?
$pg_mobile_ssl = $sitelink->old_get_type();
?>
<input type=hidden name=pg[ssl] value="<?=$pg_mobile_ssl?>">
<tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg_mobile[quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg_mobile[quota]?></span>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="N" <?=$checked[zerofee][N]?>> �Ϲݰ���
	<input type=radio name=pg[zerofee] value="Y" <?=$checked[zerofee][Y]?>> �����ڰ��� <font class=extext><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font></td>
</tr>
<tr>
	<td>���� ����</td>
	<td class=noline>
	<input type=radio name=pg[cert] value="Y" <?=$checked[cert][Y]?>> ����
	<input type=radio name=pg[cert] value="N" <?=$checked[cert][N]?>> ���� ������
	</td>
</tr>
<tr>
	<td>���ʽ� ����Ʈ</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> ���
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> ������
	</td>
</tr>
</table>
<div style="padding-top:5"></div>
<div id="MSG02">
<?if($pgStatus == 'menual') {?>
<div class="small_ex">�Ｚ�þܿ� ��û�� ����ϰ������� �� ����ϼ����� �̿��ϰ����ϴ� ����ϰ��������� üũ �� ��</div>
<div class="small_ex">�Ｚ�þ����κ��� �������� ����ϰ����� ID�� CrossKey ������ �Է��ϰ� �����ϼ���.</div>
<div class="small_ex">���� �� �ݵ�� ����ϼ����� �ſ�ī�� ���� ������������ ���������� ������ �̷������ �׽�Ʈ �� �ּ���.</div>
<?}
else{?>
<div class="small_ex">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�� PG ID�� �ڵ����� �����˴ϴ�.</div>
<?}?>
<div style="padding-top:10px"></div>
<div class="small_ex"><strong> * �þ� ������� �ڵ��Ա�Ȯ�� ����</strong> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<div class="small_ex">�þܰ� ��������¡� ���������� ���Ǿ� �ִ� �����̶�� ����������Ա� Ȯ�� �������� ���� ���ϰ� �Աݳ����� Ȯ���Ͻ� �� �ֽ��ϴ�.</div>
<div class="small_ex" style="padding-bottom:7px;">��������� �Ա�Ȯ�� �������̶� ���� ������·� �Ա��� �ϰ� �Ǿ� ������ �� ��� �Աݳ������ΰ������ e���� ������ �������� ������ �ش��ֹ��ǿ� ���Ͽ� �ڵ����� ���Ա�Ȯ�Ρ� ó���� �ǵ��� �� �� �ִ� ���Դϴ�.</div>
<div class="small_ex">������� �Ա�Ȯ���� �þ� �����ڿ��� Ȯ���� �� ������ e���� �����ڿ��� �Ա�Ȯ�� ������ �ڵ����� ���۹����� � �� ���ϹǷ� ���� ������  �����մϴ�.</div>
<div class="small_ex" style="padding-bottom:7px;">�ڼ��� ����� �Ŵ����� ���� ������ �ּ���.</div>
<div class="small_ex">�� �þ��� ��������¡� ���������� ����ϴ� ���θ����Ը� �����ǹǷ� ���� �þܿ� ������� ��û�� �Ǿ� �ִ��� Ȯ���Ͻñ� �ٶ��ϴ�.</div>
<div class="small_ex">�� �þ� ������ �α����� ��, ���������� >URL ����  �޴����� ����ID(PG ID)�� ����, [������� �Ա�Ȯ�� NOTI URL ��û]�� Ŭ���Ͽ� ����� ������ �ּҸ� �Է��ϰ� �����մϴ�.</div>
<div class="small_ex small_ex_padding" style="font-weight: bold; color: #0174DF;">�þ� �����ڿ� �Է��� �ּ� : http://���θ�������/shop/order/card/allatbasic/allat_notiurl.php</div>
<div class="small_ex">�� ������ ��ġ�� ���θ� ������ �ֹ� ���������� �ڵ� �Ա�Ȯ���� �׽�Ʈ�� ���ñ� �ٶ��ϴ�.</div>
<div class="small_ex small_ex_padding" style="font-weight: bold; color: #0174DF;">����ϼ��� PG ID �߰���� �� �Է��� �ּ� :  http://���θ�������/shop/order/card/allatbasic/mobile/allat_mobile_notiurl.php</div>
<div class="small_ex">�� ������ ��ġ�� ���θ� ������ �ֹ� ���������� �ڵ� �Ա�Ȯ���� �׽�Ʈ�� ���ñ� �ٶ��ϴ�.</div>
<div class="small_ex small_ex_padding">�׽�Ʈ ����� ������·� �ֹ��� �� �� �ش� ���·� �Ա��� �� �ڿ� �þ� ������ ������������ �Աݿ��ο� e���� ������ ������������ �ֹ�ó�� ���°� ���Ա�Ȯ�Ρ����� ����Ǿ����� Ȯ���ϸ� �˴ϴ�.</div>
</div>
<script>cssRound('MSG02')</script>

<div class=title>���ݿ����� <!--span>������ PG���� ���ݿ������� ����ϸ�, ���� ����� �ؾ� ��</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
	<div>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?> /> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?> /> ���(���� �Ｚ�þܿ� ���ݿ����� ����� ��û�� �� �����ϼ���)
	</div>
	<div class="extext">����ϼ����� ������ ������ ���ݿ������� �����Ͻð��� �ϸ�</div>
	<div class="extext">�� �Ｚ�þ� ����� ���̵� �߰� ��û�� �ۼ� �� ���ݿ����� ��û�� ��û�� ��</div>
	<div class="extext">�� �������������� ���ݿ����� ����� �����Ͻð� �����ϼ���.</div>
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