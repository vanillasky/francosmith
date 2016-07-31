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
			'use_mobile'		=> 'N',
			'min'		=> 0,
			);

$location = "������⿬�� > KCP PG����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($cfg[settlePg]=="kcp"){
	@include "../../conf/pg.$cfg[settlePg].php";
	@include "../../conf/pg_mobile.$cfg[settlePg].php";
	@include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);
if ($cfg[settlePg]=="kcp") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use_mobile'][$escrow['use_mobile']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
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
var arr=new Array('c','v','h');
function chkSettleKind(){
	var f = document.forms[0];

	<?if($pgStatus == 'auto' || $pgStatus == 'disable'){?>
		return false;
	<?}?>

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[key]','pg[quota]');
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
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}


	if(!p_id.value && ret){
		p_id.focus();
		alert('KCP PGID�� �ʼ��׸��Դϴ�.');
		return false;
	}
	if(!p_key.value && ret){
		p_key.focus();
		alert('KCP KEY�� �ʼ��׸��Դϴ�.');
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
	resizeFrame()
}
</script>
<div class="title title_top">
KCP PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="kcp_banner"><script>panel('kcp_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="setPg">
<input type=hidden name=cfg[settlePg] value="kcp">
<?if($pgStatus == 'menual') {?>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>KCP���� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
<tr><td>KCP���� <b>���Ϸ� ������ KCP PGID�� KCP KEY�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
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
	<input type=text name="pg[id]" class="lline" value="<?=$pg[id]?>"  disabled="disabled">
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
	<input type=text name=pg[key] class=lline value="<?=$pg[key]?>"  disabled="disabled">
	<?}?>
	</td>
</tr>
<tr>
	<td>�ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg_mobile[quota]?>" class=lline>
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
	<input type=text name=pg[zerofee_period] value="<?=$pg_mobile[zerofee_period]?>" class=lline style="width:500px">
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�.  </td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������� ���� �Ա� �뺸�� ���θ��� �ޱ� ���ؼ��� KCP�����ڿ��� ����URL�� ������ּž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����URL�� "http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/order/card/kcp/common_return.php" �Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=21')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<input type=radio name=pg[receipt] value="N" <?=$checked[receipt][N]?>> ������
	<input type=radio name=pg[receipt] value="Y" <?=$checked[receipt][Y]?>> ���
	<BR><font class=extext style="padding-left:5px">KCP ���ݿ����� �̿��� KCP ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://kcp.co.kr/html/cash01.jsp" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>

<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>
