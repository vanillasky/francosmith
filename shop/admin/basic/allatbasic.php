<?
$pg_name = 'allatbasic';

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
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 1,
			);

$location = "������⿬�� > �þ�PG ����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
if ($_GET['changePg'] == $pg_name){	//�þ�->������ ��ȯ�̸� ī�ǵ� �þܹ����� ���� ������.
	include "../../conf/pg.$pg_name.php";	
	include "../../conf/pg.escrow.php";
}
else if($cfg['settlePg'] == $pg_name){
	include "../../conf/pg.$cfg[settlePg].php";
	include "../../conf/pg.escrow.php";
}

$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,(array)$escrow);

if($cfg['settlePg']!= $pg_name && $_GET['changePg'] != $pg_name) $pg = array(); //pgŸ��üũ

if ($cfg['settlePg']=="allatbasic") $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked['ssl'][$pg['ssl']] = $checked['sell'][$pg['sell']] = $checked['zerofee'][$pg['zerofee']] = $checked['cert'][$pg['cert']] = $checked['bonus'][$pg['bonus']] = "checked";
$checked['escrow']['use'][$escrow['use']] = $checked['escrow']['comp'][$escrow['comp']] = $checked['escrow']['min'][$escrow['min']] = "checked";
$checked['receipt'][$pg['receipt']] = "checked";

if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

$checked['displayEgg'][$cfg['displayEgg']+0] = "checked";

// �����Ƚ���
$prefix = 'GM|GP|GF';
?>
<script language=javascript>
var prefix = '<? echo $prefix;?>';
var arr=new Array('c','v','o','h');

function chkSettleKind(){
	var f = document.forms[0];

	var ret = false;
	for(var i=0;i < arr.length;i++)
	{
		var sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}
	var robj =  new Array('pg[id]','pg[crosskey]');

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
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
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

var oldId = "<?php echo $pg['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("�������� �þ� PGID �Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� �þ� PGID �� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=allatbasic&pgid="+pgid,
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
<div class="title title_top">
�þ�PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="allatbasic_banner"><script>panel('allatbasic_banner', 'pg');</script></div>
<form method="post" action="indb.pg.php" onsubmit="return chkFormThis(this)">
<input type="hidden" name="mode" value="allatbasic" />
<input type="hidden" name="cfg[settlePg]" value="allatbasic" />

<?if($pgStatus == 'menual') {?>
	<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td>�þܿ��� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
	<tr><td>�þܿ��� <b>���Ϸ� ������ �þ�PGID�� �þ� KEY�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
	<tr><td>���� �þܿ� ����� ���� �����̴ٸ�</td></tr>
	<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ���</u></td></tr>
	<tr><td style="padding-left:10">��<u>��༭���� �������� �þܿ� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a></td></tr>
	</table>
	</div>
	<script>cssRound('MSG01')</script>
<?}?>

<div style="padding-top:15"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>PG��</td>
	<td class="noline"><b>�þ� (All@Pay�� Basic) <?=$spot?></b></td>
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
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� �þܰ� ����� �������ܸ� üũ�ϼ���)</b></span><?}?>
	</td>
</tr>

<tr>
	<td>�þ� <font color="#627dce">PG&nbsp;ID</font></td>
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
			
	<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg['id']?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid" /></div>
	<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
	<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� �þ� PGID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
	<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
	<?}?>
	</td>
</tr>
<tr >
	<td>�þ� <font color="#627dce">KEY</font></td>
	<td>
		<?if($pgStatus!='menual'){?>
			<? if(($pg['crosskey'])){ 
				echo "<b>".$pg['crosskey']."</b>&nbsp;<span class='extext'><b>�ڵ����� �Ϸ�</b></span><br/>";
			 }?>
			
		<?}
		else{?>	
		<input type="text" name="pg[crosskey]" class="lline" value="<?=$pg['crosskey']?>" /> <font class="extext">CrossKey�� ��������</font>
		<?}?>
	</td>
</tr>
<?
$pg_ssl = $sitelink->old_get_type();
?>
<input type="hidden" name="pg[ssl]" value="<?=$pg_ssl?>" />
<!--tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type=text name=pg[quota] value="<?=$pg[quota]?>" class=lline>
	<span class=extext>ex) <?=$_pg[quota]?></span>
	</td>
</tr-->
<tr>
	<td>�Һ� ��� ����</td>
	<td class="noline">
	<input type="radio" name="pg[sell]" value="Y" <?=$checked['sell']['Y']?> /> �Һλ��
	<input type="radio" name="pg[sell]" value="N" <?=$checked['sell']['N']?> /> �Һλ�����	
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
	<input type="radio" name="pg[zerofee]" value="N" <?=$checked['zerofee']['N']?> /> �Ϲݰ���
	<input type="radio" name="pg[zerofee]" value="Y" <?=$checked['zerofee']['Y']?> /> �����ڰ��� <font class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b></font></td>
</tr>
<tr>
	<td>ī����� ���� ����</td>
	<td class="noline">
	<input type="radio" name="pg[cert]" value="Y" <?=$checked['cert']['Y']?> /> �����
	<input type="radio" name="pg[cert]" value="N" <?=$checked['cert']['N']?> /> ������
	</td>
</tr>
<!--tr>
	<td>���ʽ� ����Ʈ</td>
	<td class=noline>
	<input type=radio name=pg[bonus] value="Y" <?=$checked[bonus][Y]?>> ���
	<input type=radio name=pg[bonus] value="N" <?=$checked[bonus][N]?>> ������
	</td>
</tr-->
<!--tr>
	<td>���������ڸ��</td>
	<td class=noline>
	<span><a href="https://www.allatpay.com/servlet/AllatBiz/login/login.jsp" target="_blank"><?php echo $pgNm;?> ���� �����ڸ�� �ٷΰ���</a></span>
	<span><a href="http://www.allatpay.com/" target="_blank"><?php echo $pgNm;?> ����Ʈ �ٷΰ���</a></span>
	</td>
</tr-->
</table>
<div style="padding-top:5"></div>

<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<?if($pgStatus == 'menual') {?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� ID, Key�� �����ø� �˴ϴ�..</td></tr>
	<?}else{?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�. ���ڰ��� ��û �� ��༭���� �������� �þܿ� �����ּ���. <a href="pg.intro.php" target="_blank" style="color:#ffffff;">[<u>��� �󼼾ȳ�</u>]</a>
	<?}?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Һ�, �����ڵ��� �ɼ��� ���θ� ��å�� ���� �����Ͽ� ����Ͻʽÿ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�. ��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.<br/>�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</td></tr>
	<tr><td class="pdv10"><strong> * �þ� ������� �ڵ��Ա�Ȯ�� ����</strong> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></td></tr>
	<tr><td>�þܰ� ��������¡� ���������� ���Ǿ� �ִ� �����̶�� ����������Ա� Ȯ�� �������� ���� ���ϰ� �Աݳ����� Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
	<tr><td style="padding-bottom:7px;">��������� �Ա�Ȯ�� �������̶� ���� ������·� �Ա��� �ϰ� �Ǿ� ������ �� ��� �Աݳ������ΰ������ e���� ������ �������� ������ �ش��ֹ��ǿ� ���Ͽ� �ڵ����� ���Ա�Ȯ�Ρ� ó���� �ǵ��� �� �� �ִ� ���Դϴ�.</td></tr>
	<tr><td>������� �Ա�Ȯ���� �þ� �����ڿ��� Ȯ���� �� ������ e���� �����ڿ��� �Ա�Ȯ�� ������ �ڵ����� ���۹����� � �� ���ϹǷ� ���� ������  �����մϴ�.</td></tr>
	<tr><td style="padding-bottom:7px;">�ڼ��� ����� �Ŵ����� ���� ������ �ּ���.</td></tr>
	<tr><td>�� �þ��� ��������¡� ���������� ����ϴ� ���θ����Ը� �����ǹǷ� ���� �þܿ� ������� ��û�� �Ǿ� �ִ��� Ȯ���Ͻñ� �ٶ��ϴ�.</td></tr>
	<tr><td>�� �þ� ������ �α����� ��, ���������� >URL ����  �޴����� ����ID(PG ID)�� ����, [������� �Ա�Ȯ�� NOTI URL ��û]�� Ŭ���Ͽ� ����� ������ �ּҸ� �Է��ϰ� �����մϴ�.</td></tr>
	<tr><td class="small_ex_padding extext" style="font-weight: bold; color: #0174DF;">�þ� �����ڿ� �Է��� �ּ� : http://���θ�������/shop/order/card/allatbasic/allat_notiurl.php</td></tr>
	<tr><td>�� ����ϼ��� PG ID(����ID)�� �߰��� ����ϴ� ���,  �ش�ID�� �����ϰ� �ּҸ� �Է��Ͻʽÿ�(��� ��Ϲ�İ� ����)</td></tr>
	<tr><td class="small_ex_padding extext" style="font-weight: bold; color: #0174DF;">����ϼ��� PG ID �߰���� �� �Է��� �ּ� :  http://���θ�������/shop/order/card/allatbasic/mobile/allat_mobile_notiurl.php</td></tr>
	<tr><td>�� ������ ��ġ�� ���θ� ������ �ֹ� ���������� �ڵ� �Ա�Ȯ���� �׽�Ʈ�� ���ñ� �ٶ��ϴ�.</td></tr>
	<tr><td class="small_ex_padding">�׽�Ʈ ����� ������·� �ֹ��� �� �� �ش� ���·� �Ա��� �� �ڿ� �þ� ������ ������������ �Աݿ��ο� e���� ������ ������������ �ֹ�ó�� ���°� ���Ա�Ȯ�Ρ����� ����Ǿ����� Ȯ���ϸ� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div class=title>����ũ�� ���� <span>���ݼ� ������ �ǹ������� ����ũ�ΰ����� ����ؾ� �մϴ�. ����ũ�ζ�?</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<input type="hidden" name="escrow[comp]" value="PG" />	<!-- ����ũ�� ������� -->
<input type="hidden" name="escrow[min]" value="<?=$escrow[min]?>">

<div class="extext">���� �þ� ����ũ�θ� ��û���� �����̴ٸ� �þ� ����ũ�� Ư�༭�� �þ����� ����߼��� �ּ���.</div>


<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>��뿩��</td>
	<td class="noline">
	<input type="radio" name="escrow[use]" value="Y" <?=$checked['escrow']['use']['Y']?> /> ���
	<input type="radio" name="escrow[use]" value="N" <?=$checked['escrow']['use']['N']?> /> ������
	&nbsp;&nbsp;&nbsp;<font class=extext><b>(�þ� ����ũ�θ� ��û�ϼ̴ٸ� ������� üũ�ϼ���)</b></font>
	</td>
</tr>
<tr>
	<td>���� ����</td>
	<td class="noline">
<?
		$methodEscrowList = array('o'=>'������ü', 'v'=>'�������');
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
	<td>���� ���� ǥ��<div style="padding-top:3"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=9')"><font class="extext_l">[ǥ���̹��� ����]</font></a></div></td>
	<td class="noline">
	<input type="radio" name="cfg[displayEgg]" value="0" <?=$checked['displayEgg']['0']?> /> �����ϴܰ� �������� �������������� ǥ��
	<input type="radio" name="cfg[displayEgg]" value="1" <?=$checked['displayEgg']['1']?> /> ��ü�������� ǥ��
	<input type="radio" name="cfg[displayEgg]" value="2" <?=$checked['displayEgg']['2']?> /> ǥ������ ����
	</td>
</tr>
</table>

<div style="padding-top:10"></div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<tr><td>
<table cellpadding="15" cellspacing="0" border="0" bgcolor="white" width="100%">
<tr><td>
<div style="padding:0 0 5 0">* ���ž������� ǥ�� ������ (����ũ�� ���� ������ ���ž���ǥ�ø� üũ�ϰ�, �Ʒ� ǥ������ ���� �ݿ��ϼ���)</font></div>
<table width="100%" height="100" class="tb" style="border:1px solid #cccccc;" bgcolor="white">
<tr>
<td width="30%" style="border:1px solid #cccccc;padding-left:20">�� [���������� �ϴ�] ǥ����</td>
<td align="center" rowspan="2" style="border:1px solid #cccccc;padding:0 10 0 10"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><img src="../img/icon_sample.gif" align="absmiddle"></a></td>
<td width="70%" style="border:1px solid #cccccc;padding-left:40"><font class="extext"><a href="../design/codi.php?design_file=outline/footer/standard.htm" target="_blank"><font class="extext"><b>[�����ΰ��� > ��ü���̾ƿ� ������ > �ϴܵ����� > html�ҽ� ��������]</b></font></a> �� ����<br> ġȯ�ڵ� <font class="ver8" color="000000"><b>{=displayEggBanner()}</b></font> �� �����ϼ���. <a href="../design/codi.php?design_file=outline/footer/standard.htm" target="_blank"><font class="extext_l">[�ٷΰ���]</font></a></font></td>
</tr>
<tr>
<td width="30%" style="border:1px solid #cccccc;padding-left:20">�� [�������� ����������] ǥ����</td>
<td width="70%" style="border:1px solid #cccccc;padding-left:40">
<a href="../design/codi.php?design_file=order/order.htm" target="_blank"><font class="extext"><font class="extext_l">[�����ΰ��� > ��Ÿ������ ������ > �ֹ��ϱ� > order.htm]</font></a> �� ����<br> ġȯ�ڵ� <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> �� �����ϼ���. <a href="../design/codi.php?design_file=order/order.htm" target="_blank"><font class="extext_l">[�ٷΰ���]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext"><b>���ž������� ���� ǥ�� �ǹ�ȭ �ȳ� (2007�� 9�� 1�� ����)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">�� ǥ�á����� �Ǵ� ������ ��ġ�� ���̹��� �ʱ�ȭ��� �Һ����� �������� ����ȭ�� �� ������ ����.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- ���̹��� �ʱ�ȭ�� ��� ��10����1���� ������� �ſ� �� ǥ����� ����κ��� �ٷ� ���� �Ǵ� ������ ���ž������� ���� ������ ǥ���ϵ��� ��.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- �Һ��ڰ� ��Ȯ�� ���ظ� �������� ���ž������� �̿��� ������ �� �ֵ���, �������� ���úκ��� �ٷ� ���� ���ž������� ���û����� �˱� ���� �����Ͽ���  ��.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class="extext">�� ǥ�á����� �Ǵ� ���� �������� ������ �� ������ ������.</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- ���� ������ ������ �Һ��ڰ� ���ž��������� �̿��� ������ �� �ִٴ� ����</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- ����Ǹž��� �ڽ��� ������ ���ž��������� ��������ڸ� �Ǵ� ��ȣ</font></td></tr>
<tr><td style="padding-left:16"><font class="extext">- �Һ��ڰ� ���ž������� ���Ի���� ������ Ȯ�� �Ǵ� ��ȸ�� �� �ִٴ� ����</font></td></tr>
<tr><td height="10"></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_tip">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>���ž������� �ǹ� ���� Ȯ�� (2013�� 11�� 29�� ����)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>5���� ���� �ŷ��� ���ؼ��� �Һ����� ������ ��ȣ�ϱ� ���Ͽ� ���ž������� �ǹ� ���� ��� Ȯ�� <br/>1ȸ ���� ����, 5���� �̻� �� 5���� ������ �Ҿ� �ŷ�(��� �ݾ�)</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>�� ���� ����</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ���� <br/>[ ���� ��11841ȣ, ������: 2013.5.28, �Ϻ� ���� ]</font></td></tr>
<tr><td height="10"></td></tr>
</table>
</td></tr></table>

<div class=title>���ݿ����� <!--span>������ PG���� ���ݿ������� ����ϸ�, ���� ����� �ؾ� ��</span--> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=6')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>���ݿ�����</td>
	<td class="noline">
	<input type="radio" name="pg[receipt]" value="N" <?=$checked['receipt']['N']?> /> ������
	<input type="radio" name="pg[receipt]" value="Y" <?=$checked['receipt']['Y']?> /> ���
	<BR><font class="extext" style="padding-left:5px">�Ｚ�þ� ���ݿ����� �̿��� �Ｚ�þ� ���ݿ����� �ȳ��� Ȯ���Ͻñ� �ٶ��ϴ�. <a class="extext" style="font-weight:bold" href="http://www.allatpay.com/servlet/AllatBiz/svcinfo/si_receipt_apply.jsp?menu_id=idS16" target="_blank">[�ٷΰ���]</a></font>
	</td>
</tr>
</table><p>

<div id="MSG03">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />����ũ��</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ž�������(����ũ�� �Ǵ� ���ں���)�� ���ڻ�ŷ� ����� �Һ��ں�ȣ�� ���� ���� [ ���� ��11841ȣ, ������: 2013.5.28, �Ϻ� ���� ] �� ���� 
<br> &nbsp;&nbsp; 2013�� 11�� 29�� ���� ��5���� �̻��� �����ݾס� ���� ����� �����ݾס����� �ǹ� ������ Ȯ�� �˴ϴ�.</td></tr>

<tr><td height="8"></td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���ݿ�����</font>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�Һ��ڴ� 2008. 7. 1�Ϻ��� ���ݿ����� �߱޴��ݾ��� 5õ���̻󿡼� 1���̻����� ����Ǿ�
5õ�� �̸��� ���ݰŷ��� ���ݿ������� ��û�Ͽ� �߱� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���ݿ����� ��� üũ�� ������, ������ü, ������� ������ ���ؼ� �Һ��ڰ� ��û�� ���ݿ������� �߱� �˴ϴ�</td></tr>
</table>
</div>
<script>cssRound('MSG03')</script>



<div class="button">
<input type="image" src="../img/btn_save.gif" />
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif" /></a>
</div>

</form>
<script>chkSettleKind();</script>