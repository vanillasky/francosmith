<?php
$pg_name = 'agspay';

### �ô�����Ʈ �⺻ ���ð�
$_pg_mobile		= array(
			'id'		=> '',
			'zerofee'	=> 'no',
			'receipt'	=> 'N',
			'quota'		=> '0:2:3:4:5:6:7:8:9:10:11:12',
			);

$location = '������⿬�� > �ô�����ƮPG ����';
include '../_header.popup.php';
include '../../conf/config.pay.php';
if ($cfg[settlePg] == 'agspay'){
	@include '../../conf/pg.'.$cfg['settlePg'].'.php';
	$pg_mobile = $pg;
	@include "../../conf/pg.".$cfg['settlePg'].".php";
}

$pg = @array_merge($_pg_mobile,$pg);

if($cfg['settlePg']!="agspay") $pg_mobile = array(); //pgŸ��üũ

//�������
if($cfg['settlePg'] != $pg_name){
	$pgStatus = 'disable';
}
else if($pg['pg-centersetting']=='Y'){ 
	$pgStatus = 'auto';
}
else{
	$pgStatus = 'menual';
}

if ($cfg[settlePg]=="agspay" && $pg_mobile['id']) $spot = "<b style='color:#ff0000;padding-left:10px'><img src=../img/btn_on_func.gif align=absmiddle></b>";
$checked[ssl][$pg_mobile[ssl]] = $checked[zerofee][$pg_mobile[zerofee]] = $checked[cert][$pg_mobile[cert]] = $checked[bonus][$pg_mobile[bonus]] = "checked";
$checked[receipt][$pg_mobile[receipt]] = "checked";

// �����Ƚ���
$prefix = 'gda|gdfp|gdf';
?>
<script language="javascript">
<!--
var prefix = '<? echo $prefix;?>';
var arr = new Array('c','v','h');	//�ſ�ī��, �������, �޴���
var pgStatus = '<?=$pgStatus?>';
function chkSettleKind()
{
	var f = document.forms[0];

	var ret = false;
	for (var i=0; i < arr.length; i++) {
		var sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}

	if(pgStatus == 'auto' || pgStatus == 'disable'){
		return false;
	}

	var robj =  new Array('pg[id]','pg[quota]');

	for (var i=0; i < robj.length; i++) {
		var obj = document.getElementsByName(robj[i])[0];
		if (ret) {
			obj.style.background = '#ffffff';
			obj.readOnly = false;
		} else {
			obj.style.background = '#e3e3e3';
			obj.readOnly = true;
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
		sk = document.getElementsByName('set[use_mobile]['+arr[i]+']')[0].checked;
		if (sk == true) ret = true;
	}
	
	if(pgStatus == 'menual'){
		if (!p_id.value && ret) {
			p_id.focus();
			alert('�ô�����Ʈ PGID�� �ʼ��׸��Դϴ�.');
			return false;
		}

		if(!chkPgid()){
			alert('�ô�����Ʈ PGID�� �ùٸ��� �ʽ��ϴ�.');
			return false;
		}
	}

	if (!p_quota.value && ret) {
		p_quota.focus();
		alert('�Ϲ��ҺαⰣ�� �ʼ��׸��Դϴ�.');
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

var oldId = "<?php echo $pg_mobile['id'];?>";
function openPrefix(){
	if(chkPgid()){
		alert("�������� �ô�����Ʈ PGID�Դϴ�.\n���� ���� ��û�� �ʿ� �����ϴ�.\nâ�� �ݰ� �ô�����Ʈ PGID�� �Է��ϼ���!");
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
		parameters: "mode=getPginfo&pgtype=allthegate&mobilepg=y&pgid="+pgid,
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
	if (pgStatus == 'disable')
	{
		alert('��� ���� PG�� �ƴմϴ�.');
		return;
	}
	ifrmHidden.location.href = '../basic/pgSettingUpdate.php';
}

window.onload = function(){
	resizeFrame();
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
�ô�����ƮPG ����<span>�ſ�ī�� ���� �� ��Ÿ��������� �ݵ�� �������Ҽ��� ��ü�� ����� �����ñ� �ٶ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=27')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<form method="post" action="indb.pg.php" enctype="multipart/form-data" onsubmit="return chkFormThis(this)">
<input type="hidden" name="mode" value="setPg">
<input type="hidden" name="cfg[settlePg]" value="agspay">

<?if($pgStatus == 'menual'){?>
<!-- PG ���� -->
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td>�ô�����Ʈ���� �����ϴ� �ſ�ī��,������ü,�������,�ڵ����� ���������� �湮��(�Һ���)���� �����ϱ� ���ؼ�</td></tr>
	<tr><td>�ô�����Ʈ���� <b>���Ϸ� ������ �ô�����Ʈ PGID�� �Է�</b>�Ͻ��� �� ������ �ϴ��� �����ư�� Ŭ���� �ּ���.</td></tr>
	<tr><td>���� �ô�����Ʈ�� ����� ���� �����̴ٸ�</td></tr>
	<tr><td style="padding-left:10">��<u>�¶��ν�û �Ͻ���</u></td></tr>
	<tr><td style="padding-left:10">��<u>��༭���� �������� �ô�����Ʈ�� ����</u>�ּ��� <a href="../basic/pg.intro.php" target="_blank" style="color:#ffffff;font-weight:bold">[��� �󼼾ȳ�]</a></td></tr>
	</table>
</div>
<script>cssRound('MSG01')</script>
<?}?>


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
			else{
				if(empty($pg['sub_cpid']) && $key=='h'){	//�޴��������ε� sub_cpid�� ���°��
					$disabled[$key] = 'disabled';	
					$labelColor[$key] = "style='color:#cccccc'";
				}
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
	<?if($pgStatus == 'menual'){?>&nbsp;&nbsp;&nbsp;<span class="extext"><b>(�ݵ�� �ô�����Ʈ�� ����� �������ܸ� üũ�ϼ���)</b></span><?}?>
	</td>
</tr>
<tr>
	<td>�ô�����Ʈ <font color="#627dce">PG&nbsp;ID</font></td>
	<td>
		<?if($pgStatus == 'auto'){?>
			<div style="float:left"><b><?=$pg['id']?></b> <span class="extext"><b>�ڵ����� �Ϸ�</b></span>
			</div>
		<?}
		else{?>
			<div style="float:left"><input type="text" name="pg[id]" class="lline" value="<?=$pg['id']?>" onkeyup="chkPgid()" onblur="chkPgid()" id="pgid" disabled="disabled"></div>
			<div style="float:left;padding:0 0 0 5" id="btPgId"><a href="javascript:openPrefix();"><img src="../img/pginfo.gif" alt="���� ���� ��û" /></a></div>
			<div style="clear:both" class="extext"><? echo str_replace('|',',', $prefix);?>�� ���۵Ǵ� �ô�����Ʈ PGID�� ���� �Է� �����մϴ�. (��, ���� �Է°��� �����մϴ�)</div>
			<div class="extext">���� �ַ�� �̿����� ���� ������ ����ϰ� �־� ���� ���̵�� �������� �ʴ� ��쿡�� ���� ���� ��û�� �ϼž� �մϴ�.</div>
		<?}?>
	</td>
</tr>
<tr>
	<td>�Ϲ��ҺαⰣ</td>
	<td>
	<input type="text" name="pg[quota]" value="<?=$pg_mobile['quota']?>" class="lline" disabled="disabled">
	<div class="extext" style="padding-top:5px">����â�� ǥ�õǴ� �ҺαⰣ�� ���� �����Ͽ� ������ �ʴ� �Һ� �ŷ��� ������ �� �ֽ��ϴ�.<br/>ex) <?=$pg_mobile[quota]?></div>
	</td>
</tr>
<tr>
	<td>������ ����</td>
	<td class="noline">
	<label><input type="radio" name="pg[zerofee]" value="no" checked  disabled="disabled" /> �Ϲݰ���</label>
	<label><input type="radio" name="pg[zerofee]" value="yes" <?=$checked[zerofee][yes]?>  disabled="disabled" /> �����ڰ���</label> <span class="extext"><b>(�����ڰ����� �ݵ�� PG��� ���ü�� �Ŀ� ����ؾ� �մϴ�!)</b> (�Ʒ� '������ �Ⱓ' ���� üũ)</span>
	</td>
</tr>
<tr>
	<td>������ �Ⱓ</td>
	<td>
	<input type="text" name="pg[zerofee_period]" value="<?=$pg_mobile['zerofee_period']?>" class="lline" style="width:500px" disabled="disabled">
	<a href="javascript:popupLayer('../basic/popup.agspay.php',450,500)"><img src="../img/btn_carddate.gif" align="absmiddle"></a>
	<div class="small extext">ex) ��� �Һΰŷ��� �����ڷ� �ϰ� ���������� ALL�� ����<br/>ex) ����,��ȯī�� Ư���������� �����ڸ� �ϰ� ������� ����(2:3:4:5:6����) �� 200-2:3:4:5:6,300-2:3:4:5:6</div>
	</td>
</tr>
<tr>
	<td>�޴��� SUB_CPID</td>
	<td>
		<?if($pgStatus!='menual'){?>
			<? if(($pg['sub_cpid'])){ 
				echo "<b>".$pg['sub_cpid']."</b>&nbsp;<span class='extext'><b>�ڵ����� �Ϸ�</b></span><br/>";
			 }?>
			<div class="small extext">�޴��� ������ ��û�Ͽ� ���οϷ� ������ ������ ���� [�������� ���ΰ�ħ]�� Ŭ���Ͻʽÿ�. �޴��� SUB_CPID�� �ڵ����� �����˴ϴ�.</div>
		<?}
		else{?>
		<input type="text" name="pg[sub_cpid]" class="lline" value="<?=$pg['sub_cpid']?>">
		<div class="small extext">�޴��� ������ ��û�Ͽ� ���Ϸ� ������ �޴��� SUB_CPID�� �Է��մϴ�.</div>
		<?}?>
	</td>
</tr>
</table>

<div style="padding-top:5px"></div>
<div id="MSG02">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<?if($pgStatus == 'menual'){?>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG��� ����� ���� ���Ŀ��� ���Ϸ� ������ ���� �ô�����Ʈ PGID�� �����ø� �˴ϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle">PG���� �������� ������ ���Բ��� ī����� �׽�Ʈ�� �� �غ��ñ� �ٶ��ϴ�.</td></tr>
<?}else{?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڰ��� ���񽺸� ��û�ϸ� e���� �ַ�ǿ� PG ID�� �ڵ����� �����˴ϴ�. </td></tr>
<?}?>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ȥ PG�縦 ���� ī����ε� ���� �������Ͽ� �ֹ��������������� �Ա�Ȯ������ �ڵ�������� ������ �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ݵ�� �ֹ������������� �ֹ����¿� PG�翡�� �����ϴ� ������ȭ�鳻�� ī����γ����� ���ÿ� Ȯ���� �ֽʽÿ�.</td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>
<!-- //PG ���� -->

<!-- ���ݿ����� ���� -->
<div class=title>���ݿ����� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=27')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class=cellC><col class=cellL>
<tr>
	<td>���ݿ�����</td>
	<td class=noline>
	<div>
	<?if($pg['receipt']=='N'):?>
	�̻��
	<?else:?>
	���
	<?endif;?>
	<input type="hidden" name="pg[receipt]" value="<?=$pg[receipt]?>">
	</div>
	<div class="extext" style="padding-left:5px">����ϼ����� ������ ������ ���ݿ����� ���� �����Դϴ�.</div>
	<div class="extext" style="padding-left:5px">���θ� �⺻���� > ���� ���ڰ��� ���� > �ô�����Ʈ���� ������</div>
	<div class="extext" style="padding-left:5px">���ݿ����� ��뿩�� ������ �����մϴ�. </div>
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