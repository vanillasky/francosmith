<?

### settlebank �⺻ ���ð�
$_pg		= array(
			'id'	=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '00,02,03,04,05,06,07,08,09,10,11,12',
			);
$_escrow	= array(
			'use'		=> 'N',
			'min'		=> 50000,
			);

$location = "������⿬�� > ��Ʋ��ũ PG����";
include "../_header.popup.php";
include "../../conf/config.pay.php";
include "../../conf/pg.$cfg[settlePg].php";
@include "../../conf/pg.escrow.php";


$pg = @array_merge($_pg,$pg);
$escrow = @array_merge($_escrow,$escrow);

if ($cfg[settlePg]=="settlebank") $spot = "<b style='color:#ff0000;padding-left:10px'>[�����]</b>";
$checked[ssl][$pg[ssl]] = $checked[zerofee][$pg[zerofee]] = $checked[cert][$pg[cert]] = $checked[bonus][$pg[bonus]] = "checked";
$checked[escrow]['use'][$escrow['use']] = $checked[escrow][comp][$escrow[comp]] = $checked[escrow]['min'][$escrow['min']] = "checked";
$checked[receipt][$pg[receipt]] = "checked";

if ($set['use'][c]) $checked[c] = "checked";
if ($set['use'][o]) $checked[o] = "checked";
if ($set['use'][v]) $checked[v] = "checked";
if ($set['use'][h]) $checked[h] = "checked";

if ($escrow[c]) $checked[method][c] = "checked";
if ($escrow[o]) $checked[method][o] = "checked";
if ($escrow[v]) $checked[method][v] = "checked";
$checked[displayEgg][$cfg[displayEgg]+0] = "checked";
?>
<script language=javascript>
var arr=new Array('c','v','o','h');
function chkSettleKind(){
	var f = document.forms[0];

	var ret = false; var sk = false;
	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0];
		
		if(sk.checked == true){
			ret=true;
		}
		
	}
}
function chkFormThis(f){

	var ret = false;
	var sk = false;

	for(var i=0;i < arr.length;i++)
	{
		sk = document.getElementsByName('set[use]['+arr[i]+']')[0].checked;
		if(sk == true)ret=true;
	}

	return chkForm(f);
}
var IntervarId;

function resizeFrame()
{

    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height+"px";

    if ( IntervarId ) clearInterval( IntervarId );
}

function notChange()
{
	alert('PG�߾�ȭ��� PG�� ���� ���� ������ �����Ҽ������ϴ�.');	
}

window.onload = function(){
	resizeFrame()
}
</script>
<div class="title title_top">
��Ʋ��ũ PG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� ���ڰ������� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=39')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<div id="settlebank_banner"><script>panel('settlebank_banner', 'pg');</script></div>
<form method=post action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type=hidden name=mode value="settlebank">
<input type=hidden name=cfg[settlePg] value="settlebank">
<input type=hidden name=cfg[settlePgPopup] value="On">
<input type=hidden name=pg[quota] value="">
<input type=hidden name=pg[zerofee] value="no">
<input type=hidden name=pg[zerofee_period] value="">
<input type=hidden name=pg[receipt] value="">

<div style="padding-top:15px"></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>PG��</td>
	<td><b>��Ʋ��ũ(S'Pay) <?=$spot?></b></td>
</tr>
<tr>
	<td>�������� ����</td>
	<td class=noline>
<?	$use_name = array('c'=>'�ſ�ī��','o'=>'������ü','v'=>'�������','h'=>'�޴���');
	foreach( $use_name as $k => $v) {
		if($set[use_ck][$k] == 'on'){
			echo "<input type=checkbox name='set[use][$k]' ".$checked[$k]." >"; 
			echo $use_name[$k];
		} else {
			echo "<input type=checkbox name='set[use][$k]' ".$checked[$k]." style='background:#e3e3e3;' onclick='notChange();return false;'>";
			echo '<font style="background-color:#e3e3e3;margin:20px 0;">'.$use_name[$k].'</font>';
		}
	}
?> 
		<span style="margin-left:15px">�� ��Ʋ��ũ�� ��û�� �������ܸ� �����Ͽ� ����� �� �ֽ��ϴ�.</span>

	</td>
</tr>
<tr>
	<td class=ver8><b>��Ʋ��ũ Mid</td>
	<td><?=$pg[id]?></td>
</tr>
<tr>
	<td class=ver8><b>��Ʋ��ũ Key Code</b></td>
	<td><?=$pg[key]?></td>
</tr>
<tr>
	<td>�ҺαⰣ</td>
	<td>�� ��༭ �ۼ��� ��û�� ��� �����Ǹ�, ����ÿ��� ��Ʋ��ũ �����ͷ� ��û�Ͽ� �ֽʽÿ�.</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class=noline>�� ��༭ �ۼ��� ��û�� ��� �����Ǹ�, ����ÿ��� ��Ʋ��ũ �����ͷ� ��û�Ͽ� �ֽʽÿ�.</td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>�� ��༭ �ۼ��� ��û�� ��� �����Ǹ�, ����ÿ��� ��Ʋ��ũ �����ͷ� ��û�Ͽ� �ֽʽÿ�.</td>
</tr>
</table>
</div>

<div style="padding-top:5px"></div>

<div class=title>���ݿ����� </div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>�� ��༭ �ۼ��� ��û�� ��� �����Ǹ�, ����ÿ��� ��Ʋ��ũ �����ͷ� ��û�Ͽ� �ֽʽÿ�.</font>
	</td>
</tr>
</table><p>

<div id=MSG03>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ʋ��ũ�� PG�� ID�� Ű��(Key Code)�� �ڵ����� �����˴ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ��û�� ���񼭷��� �ѽ��� �߼��Ͻð� �ַ�� ������ ��ٷ��ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG03','#F7F7F7')</script>



<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>
<script>chkSettleKind();</script>
