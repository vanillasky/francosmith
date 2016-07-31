<?
$pg_name = 'kcp';
### KCP �⺻ ���ð�
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '00,02,03,04,05,06,07,08,09,10,11,12',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 0,
			);

$location = "������⿬�� > KCP PG����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="kcp"){
	include "../../conf/pg.$cfg[settlePg].php";
	include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);

if ($cfg[settlePg]=="kcp") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use'][$escrow['use']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if($pg['pg-centersetting']=='Y'){ 
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

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[key]','pg[quota]');

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
	var p_key = document.getElementsByName('pg[key]')[0];
	var p_quota = document.getElementsByName('pg[quota]')[0];
	<?if($pgStatus == 'menual'){?>
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	if(!p_id.value && ret){
		p_id.focus();
		alert('KCP PGID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('KCP Key�� �ʼ��׸��Դϴ�.');
		return false;
	}
	<?}?>

	if(!p_quota.value && ret){
		p_quota.focus();
		alert('�ҺαⰣ�� �ʼ��׸��Դϴ�.');
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
}
</script>
<div class="title title_top">
KCP PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="kcp_banner"><script>panel('kcp_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="kcp">
<input type=hidden name=cfg[settlePg] value="kcp">
<?if($pgStatus == 'menual') {?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>KCP���� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>KCP���� <b>���Ϸ� ������ KCP PGID�� KCP Key�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
<tr><td>���� KCP�� ����� ���� �����̴ٸ�</td></tr>
<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ� ��</u></td></tr>
<tr><td style="padding-left:10">��<u>��༭���� �������� KCP�� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank"><font color="#ffffff">[<u>��� �󼼾ȳ�</u>]</font></a></td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>

<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG��</td>
	<td><b>KCP (ESCROW AX-HUB V6) <?=$spot?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class=noline>
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
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� KCP�� ����� �������ܸ� üũ�ϼ���)</b></span><?}?>
	
	</td>
</tr>
<tr>
	<td>KCP <font color="#627dce">PG&nbsp;ID</font></td>
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
	<input type=text name=pg[id] class=lline value="<?=$pg[id]?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>KCP <font color="#627dce">KEY</font></td>
	<td>
	<?if($pgStatus!='menual'){?>
			<? if(($pg['key'])){ 
				echo "<b>".$pg['key']."</b>&nbsp;<span class='extext'><b>�ڵ����� �Ϸ�</b></span><br/>";
			 }?>
			
		<?}
		else{?>	
	<input type=text name=pg[key] class=lline value="<?=$pg[key]?>">
	<?}?>
	</td>
</tr>
<tr>
	<td>�ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline>
	<div class=extext style="padding-top:4">�����ڰ� �Һ� ������ ������ �Һΰ��� �� �Դϴ�. 00 ���� 12 �� ���� �����ϴ�.(��: 3������ ��� : 03 , �Ͻú��� ��� : 00) </div>
	<div class=extext style="padding-top:3">�ִ� �Һΰ��������� �Է��� �ֽñ� �ٶ��ϴ� (��: 6�����Һα������� ����� 06 ���� �Է� => �Ͻú�~6�����Һθ��� ����) </div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>
	<input type=radio name=pg[zerofee] value="no" checked> �Ϲݰ���
	<input type=radio name=pg[zerofee] value="yes" <?=$checked[zerofee][yes]?>> �����ڰ��� (�Ʒ� �Ⱓ �Է�)
	<input type=radio name=pg[zerofee] value="admin" <?=$checked[zerofee][admin]?>> �����ڰ��� (KCP ���� ������ ��忡�� ����) <font class=extext><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font></td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>
	<input type=text name=pg[zerofee_period] value="<?=$pg[zerofee_period]?>" class=lline style="width:500px">
	<a href="javascript:popupLayer('../basic/popup.kcp.php',500,470)" style="color:#616161;" class=ver8><img src="../img/btn_carddate.gif" align=absmiddle></a>
	<div class=extext  style="padding-top:4px">���� �ִ� '�����ڱⰣ�ڵ����' ��ư�� ���� �ڵ带 �������� �����Ͽ� ����ϼ���</div>
	</td>
</tr>
</table>
</div>

<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<?if($pgStatus == 'menual') {?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� ID, Key�� �����ø� �˴ϴ�.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�. ���ڰ��� ��û �� ��༭���� �������� KCP�� �����ּ���. 
<a href="pg.intro.php" target="_blank" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a></td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ� , ������ ���� �ɼ��� ���θ� ��å�� ���� �����Ͽ� ����Ͻʽÿ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white"><b>�� KCP PG�� ����� ������ ���ǻ��� (�ʵ�!)</b></font></td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=white>- KCP PG��� '�������' ���������� ���Ǿ� �ִ� ��� -</font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_kcp_pg_url.html',720,510)"><img src="../img/btn_dacompg_sample.gif" border=0 align=absmiddle></a></td></tr>
<tr><td>�� ������� ��� �� �ڵ��Ա��뺸�� ���θ��� �ޱ� ���ؼ��� KCP�����ڿ��� ���� URL�� ����� �ּž� �մϴ�.<br>
<b>���� URL�� "http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/order/card/kcp/common_return.php" �Դϴ�.</b>
</td></tr>
<tr><td height=8></td></tr>
<tr><td><font class=def1 color=white>- ������ ���� üũ �� ������ ����(KCP ���� ������ ��忡�� ����)�� �����Ͻ� ��� -</font> <a href="javascript:popup('http://guide.godo.co.kr/guide/php/ex_kcp_pg.html',900,680)"><img src="../img/btn_dacompg_sample.gif" border=0 align=absmiddle></a></td></tr>
<tr><td>�� KCP���� �����ϴ� �ҽ� ������ ������������ > ������ > �ֹ� > ī�� > kcp.htm �� 201 line �κ�<br>
&lt;input type='text' name='kcp_noint' value=&quot;{? _pg.zerofee == 'yes'}Y{:}N{/}&quot;&gt; �ҽ���<br>
&lt;input type='text' name='kcp_noint' value='{_pg.zerofeeFl}'&gt; �� ������ �ּž� �մϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>



<div class=title>����ũ�� ���� <span>���ݼ� ������ �ǹ������� ����ũ�ΰ����� ����ؾ� �մϴ�. ����ũ�ζ�?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<input type=hidden name=escrow[comp] value="PG">	<!-- ����ũ�� ������� -->
<div class=extext>���� KCP ����ũ�θ� ��û���� �����̴ٸ� <a href="http://admin.kcp.co.kr" target="_blank" class=extext><b>KCP ����������(http://admin.kcp.co.kr)���� ��û</b></a>�� �ּ���.</div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>��뿩��</td>
	<td class=noline>
	<input type=radio name=escrow[use] value="Y" <?=$checked[escrow]['use'][Y]?>> ���
	<input type=radio name=escrow[use] value="N" <?=$checked[escrow]['use'][N]?>> ������
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(KCP ����ũ�θ� ��û�ϼ̴ٸ� ������� üũ�ϼ���)</b></font>
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
	<td>���� ���� ǥ��<div style="padding-top:3"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=14')"><font class=extext_l>[ǥ���̹��� ����]</font></a></div></td>
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
<div style="padding:0 0 5 0">* ���ž������� ǥ�� ������ (����ũ�� ���� ������ ���ž���ǥ�ø� üũ�ϰ�, �Ʒ� ǥ������ ���� �ݿ��ϼ���)</font></div>
<table width=100% height=100 class=tb style='border:1px solid #cccccc;' bgcolor=white>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>�� [���������� �ϴ�] ǥ����</td>
<td align=center rowspan=2 style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=14')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
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


<div class=title>���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<div class=extext style="padding-left:5px">���� KCP���ݿ������� ��û���� �����̴ٸ� <a href="http://admin.kcp.co.kr" target="_blank" class=extext><b>KCP ����������(http://admin.kcp.co.kr)���� ��û</b></a>�� �ּ���.</div>
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
