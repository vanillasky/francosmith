<?php
$pg_name = 'easypay';

### Easypay �⺻ ���ð�
$_pg		= array(
			'id'	=> '',
			'receipt'	=> 'N',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 0,
			);

$location = "������⿬�� > �������� PG����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
include "../../conf/pg.escrow.php";

if (is_file("../../conf/pg.easypay.php")){
	include "../../conf/pg.easypay.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);

if ($cfg[settlePg]=="easypay") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use'][$escrow['use']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'menual';
}
else if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

$checked[displayEgg][$cfg[displayEgg]+0] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','o','h');
function chkSettleKind(){
	var f = document.forms[0];

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]');

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	for(var i=0;i < robj.length;i++){
		var obj = document.getElementsByName(robj[i])[0];
		if(ret){
			obj.style.background = "#ffffff";
			obj.readOnly = false;
		}else{
			obj.style.background = "#e3e3e3";
			obj.readOnly = true;
			obj.value = '';
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
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('�������� PG ID�� �ʼ��׸��Դϴ�.');
		return false;
	}

	if(!chkPgid()){
		alert('�������� PG ID�� �ùٸ��� �ʽ��ϴ�.');
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
		alert("�������� �������� PG ID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� �������� PG ID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=easypay&pgid="+pgid,
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
	var pattern = /^(GD)/;
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
�������� PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=29')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="easypay_banner"><script>panel('easypay_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="easypay">
<input type=hidden name=cfg[settlePg] value="easypay">
<?if($pgStatus == 'menual') {?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>�������̿��� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�<br/>
�������̿��� <b>���Ϸ� ������ �������� PG ID�� �Է�</b> �Ͻ� �� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���. <br/>
���� �������̿� ����� ���� �����̴ٸ�, <br/>
��¶��ν�û �Ͻ��� ���༭���� �������� �������̿� �����ּ���. <a href="/shop/admin/basic/pg.intro.php" target="_blank" class="extext" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a><br/>
</td></tr>
<!--<tr><td>KCP���� <b>���Ϸ� ������ KCP Code�� KCP Key�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� KCP�� ����� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ� ��</u></td></tr>
<tr><td style="padding-left:10">��<u>��༭���� �������� KCP�� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff"><b>[��� �󼼾ȳ�]</b></font></a></td></tr>//-->
</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>
<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG��</td>
	<td><b>�������� (Easypay V7.0 �÷�����) <?=$spot?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class="noline">
	<? 
	$methodList = array('c'=>'�ſ�ī��', 'o'=>'������ü', 'v'=>'�������', 'h'=>'�޴��� ����');
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
	<br/><span class="extext">����� �������� �߿��� �����Ͽ� ����� �� �ֽ��ϴ�. ���������� �߰��Ϸ��� PG�� �����ͷ� ��û�Ͻʽÿ�.</span>
	<?}?>
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� ������� ����� �������ܸ� üũ�ϼ���)</b></span><?}?>
	</td>
</tr>
<tr>
	<td>�������� <font color="#627dce">PG&nbsp;ID</font></td>
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
	<div style="float:left"><input type=text name=pg[id] class=lline value="<?=$pg[id]?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid"></div>
	<div style="clear:both" class="extext"><b>GD</b>�� ���۵Ǵ� �������� PG ID�� ���� �Է� �����մϴ�. </div>
	<?}?>
	</td>
</tr>


<tr>
	<td>�ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline>
	<div class=extext style="padding-top:4">�����ڰ� �Һ� ������ ������ �Һΰ��� �� �Դϴ�. 00 ���� 12 �� ���� �����ϴ�.ex) 00:02:03:04:05:06:07:08:09:10:11:12   </div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> �Ϲݰ���
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> �����ڰ��� (�Ʒ� �Ⱓ �Է�)
	 <font class=extext><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font></td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>
	<input type=text name=pg[zerofee_period] value="<?=$pg[zerofee_period]?>" class=lline style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.easypay.php',500,470)" style="color:#616161;" class=ver8><img src="../img/btn_carddate.gif" align=absmiddle></a>
	<div class=extext  style="padding-top:4px">���� �ִ� '�����ڱⰣ�ڵ����' ��ư�� ���� �ڵ带 �������� �����Ͽ� ����ϼ���</div>
	</td>
</tr>



</table>
</div>

<div style="padding-top:15px"></div>

<div id=MSG02>
	<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
		<?if($pgStatus == 'menual') {?>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� ID�� �����ø� �˴ϴ�.</td></tr>
		<?}else{?>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�. ���ڰ��� ��û �� ��༭���� �������� �������̿� �����ּ���. <a href="pg.intro.php" target="_blank" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a>
		<?}?>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ�, �����ڵ��� �ɼ��� ���θ� ��å�� ���� �����Ͽ� ����Ͻʽÿ�.</td></tr>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�. ��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.<br/>�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</td></tr>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������� PG��� '�������' ���������� ���Ǿ� �ִ� ��� , �������� PG�� ������� �������� ��� �� ������ ���� ���� �ڵ����� �Ա��뺸�� ���θ��� ���� �� �ֽ��ϴ�.</font></td></tr>
	</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>����ũ�� ���� <span>���ݼ� ������ �ǹ������� ����ũ�ΰ����� ����ؾ� �մϴ�. ����ũ�ζ�?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=29')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<input type=hidden name=escrow[comp] value="PG">	<!-- ����ũ�� ������� -->
<div class=extext> - ���� �������� ����ũ�θ� ��û���� �����̴ٸ� ��û���� �ۼ��Ͽ�, �������̷� �߼����ּ���. &nbsp;&nbsp; <b><a href="http://godo.co.kr/service/EasyPay_godo.zip">[�ٿ�ε�]</a></b> <br/>
- �ۼ����� : �������� ���ڰ��� ���� �̿��༭ 2��, �������� ���ڰ������� �̿��û�� 1�� &nbsp;&nbsp;<b><a href="http://www.godo.co.kr/service/pg_service_kicc_info.php" target="_blank" >[�ڼ�������]</a></b>
</div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>��뿩��</td>
	<td class=noline>
	<input type=radio name=escrow[use] value="Y" <?=$checked[escrow]['use'][Y]?>> ���
	<input type=radio name=escrow[use] value="N" <?=$checked[escrow]['use'][N]?>> ������
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(�������� ����ũ�θ� ��û�ϼ̴ٸ� ������� üũ�ϼ���)</b></font>
	</td>
</tr>
<tr>
	<td>���� ����</td>
	<td class=noline>
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
	<input type="text" name="escrow[min]" value="<?=$escrow[min]?>" class="lline" onkeydown="onlynumber()" style="width:100px;">
	<div class="extext"  style="padding-top:4px">PG�縶�� ����ũ�� ������ ��� �ݾ׿� ������ �ȵɼ��� �����Ƿ�, �ݵ�� ������ PG���� ����ũ�� ��೻���� �� Ȯ���ϼ���.</div>
	</td>
</tr>
<tr>
	<td>���� ���� ǥ��</td>
	<td class=noline>
	<input type=radio name=cfg[displayEgg] value=0 <?=$checked[displayEgg][0]?>> �����ϴܰ� �������� �������������� ǥ��
	<input type=radio name=cfg[displayEgg] value=1 <?=$checked[displayEgg][1]?>> ��ü�������� ǥ��
	<input type=radio name=cfg[displayEgg] value=2 <?=$checked[displayEgg][2]?>> ǥ������ ����
	</td>
</tr>
</table>


<div style="padding-top:10"></div>

<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<tr><td>
<table cellpadding=15 cellspacing=0 border=0 bgcolor=white width=100%>
<tr><td>
<div style="padding:0 0 5 0">
<b>*��Ų ������ ���� �� ���濡 ���� �������� ���ž��� ���� ǥ�� ������</b><br/>
- ��Ų �ҽ��� �����Ͽ��ų�, ��Ų�� �������� ���, �Ǵ� ���� ��Ų�� ���� ��츦 ���� ǥ�����Դϴ�.<br/>
- ��Ų�� ���� �ϴܼҽ��� Table������ �ٸ���, �̺κ� �����ؼ� ���ϴ� ��ġ�� ġȯ�ڵ带 �־��ּ���.<br/>
- ������ ���ž���ǥ�ø� üũ�ϰ�, �Ʒ� ǥ������ ���� �ݿ��ϼ���.
</div>
<table width=100% height=100 class=tb style='border:1px solid #cccccc;' bgcolor=white>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>�� [���������� �ϴ�] ǥ����</td>
<td align=center rowspan=2 style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=30')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displayEggBanner()}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></td>
</tr>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>�� [�������� ����������] ǥ����</td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'>
<a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext><font class=extext_l>[�����ΰ��� > ��Ÿ������ ������ > �ֹ��ϱ� > order.htm]</font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> �� �����ϼ���. <a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext_l>[�ٷΰ���]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>���ž������� ���� ǥ�� �ǹ�ȭ �ȳ� (2007�� 9�� 1�� ����)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ǥ�á����� �Ǵ� ������ ��ġ�� ���̹��� �ʱ�ȭ��� �Һ����� �������� ����ȭ�� �� ������ ����.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ���̹��� �ʱ�ȭ�� ��� ��10����1���� ������� �ſ� �� ǥ����� ����κ��� �ٷ� ���� �Ǵ� ������ ���ž������� ���� ������ ǥ���ϵ��� ��.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- �Һ��ڰ� ��Ȯ�� ���ظ� �������� ���ž������� �̿��� ������ �� �ֵ���, �������� ���úκ��� �ٷ� ���� ���ž������� ���û����� �˱� ���� �����Ͽ���  ��.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ǥ�á����� �Ǵ� ���� �������� ������ �� ������ ������.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ���� ������ 5���� �̻� ������ �Һ��ڰ� ���ž��������� �̿��� ������ �� �ִٴ� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- ����Ǹž��� �ڽ��� ������ ���ž��������� ��������ڸ� �Ǵ� ��ȣ</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- �Һ��ڰ� ���ž������� ���Ի���� ������ Ȯ�� �Ǵ� ��ȸ�� �� �ִٴ� ����</font></td></tr>
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
</td></tr></table>


<div class=title>���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=29')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<BR><font class=extext style="padding-left:5px">�������� ���ݿ����� �̿��� �������� ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://www.easypay.co.kr/service_receipt.jsp" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>


<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ž�������(����ũ�� �Ǵ� ���ں���)�� ���ڻ�ŷ��Һ��ں�ȣ�� �� ����� ������ ���� 2011�� 7�� 29�Ϻ��� 5���� �̻� ���ݼ� ������ �ǹ� ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����ũ�� ������ �� ���ݾ׿� ���Ѱ��� ��û�� PG�糪 ���࿡ ���� �ٸ� �� �����Ƿ� ���Ǹ� �ϼž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ�
5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG03','#F7F7F7')</script>



<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>