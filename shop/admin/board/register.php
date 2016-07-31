<?

/**
 * @�Խ��� ȯ�� ���� ����
 *
 * bdName			�Խ��� �̸�
 * bdSkin			��Ų
 * bdAlign			���̺� ����
 * bdWidth			���̺� ũ��
 * bdUseSubSpeech	���Ӹ� ��� ����
 * bdSubSpeechTitle	���Ӹ� Ÿ��Ʋ
 * bdSubSpeech		���Ӹ�
 * bdStrlen			���� �ڸ���
 * bdPageNum		�� ������ �ۼ�
 * bdNew			���� ���� �ð�
 * bdHot			�α�� ��ȸ��
 * bdNoticeList		������ ����
 * bdLvlL			���� (����Ʈ)
 * bdLvlR			���� (�б�)
 * bdLvlW			���� (����)
 * bdLvlP			���� (���)
 * bdIp				������ ��� ����
 * bdIpAsterisk		������ ��ǥ ����
 * bdTypeView		�� ���� Ÿ��
 * bdUseLink		��ũ ��� ����
 * bdUseFile		���ε� ��� ����
 * bdMaxSize		���ε� �ִ� ���� ������
 * bdTypeMail		���� Ÿ��
 * bdHeader			�ش�
 * bdFooter			Ǫ��
 * bdUseComment		�ڸ�Ʈ ��� ����
 * bdSearchMode		�˻� ���
 * bdPrintMode		��� ����
 * bdField			���� �ʵ�
 * bdImg			��Ų (�̹��� ����)
 * bdColor			��Ų �����ڵ尪
 * bdPrnType		����Ʈ�������
 * bdListImgCntW	����Ʈ�̹�������
 * bdListImgCntH	����Ʈ�̹�������
 * bdListImgSizeW	����Ʈ�̹���ũ��
 * bdListImgSizeH	����Ʈ�̹���ũ��
 * bdListImg		����Ʈ�̹�����ũ
 * bdUserDsp		�ۼ���ǥ��
 * bdAdminDsp		������ǥ��
 * bdSpamComment	�ڸ�Ʈ ���Թ���
 * bdSpamBoard		�Խñ� ���Թ���
 * bdSecretChk		��б� ����
 * bdTitleCChk		���� ���ڻ� ���
 * bdTitleSChk		���� ����ũ�� ���
 * bdTitleBChk		���� ���ڱ��� ���
 * bdEmailNo		�̸��� �ۼ�
 * bdHomepage		Ȩ������ �ۼ�
 * bdUseMobile		����ϼ� ��뿩��
 */

$location = "�Խ��ǰ��� > �Խ��Ǹ����";
include "../_header.php";

if (!$_GET['mode']) $_GET['mode'] = "register";
$returnUrl = ($_GET['returnUrl']) ? $_GET['returnUrl'] : $_SERVER['HTTP_REFERER'];
switch ($_GET['mode']){
	case "register":
		$bdId = "<input type=\"text\" name=\"id\" class=\"line\" required label=\"�Խ��� ID\" option=\"regAlpha\" />";
		break;
	case "modify":
		include "../../conf/bd_".$_GET['id'].".php";
		include "../../conf/config.mobileShop.php";
		$bdId = "<b>$_GET[id]</b><input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\" />";
		break;
}

if(!$bdPrnType) $bdPrnType = 1;
if(!$bdListImg) $bdListImg = 1;
if(!$bdUserDsp) $bdUserDsp = 0;
if(!$bdAdminDsp) $bdAdminDsp = 0;
if(!$bdSecretChk) $bdSecretChk = 0;
if(!$bdSkin) $bdSkin = "default";
if(!$bdListImgCntW) $bdListImgCntW = 5;
if(!$bdListImgCntH) $bdListImgCntH = 4;
if(!$bdListImgSizeW) $bdListImgSizeW = 100;
if(!$bdListImgSizeH) $bdListImgSizeH = 100;
if( $_GET['mode'] == "register" ){
	if(!$bdSpamComment) $bdSpamComment="3";
	if(!$bdSpamBoard) $bdSpamBoard="3";
}
if(!$bdUseXss) $bdUseXss = 'html';
$checked['bdUseXss'][$bdUseXss]	= "checked";

if(isset($bdAllowPluginTag)){
	$bdAllowPluginTagArray = explode('|',$bdAllowPluginTag);
	foreach($bdAllowPluginTagArray as $val) {
		$checked['bdAllowPluginTag'][$val] = 'checked';
	}
}
else{
	$bdAllowPluginTagArray = explode('|',validation::$_allowPluginTag);
	foreach($bdAllowPluginTagArray as $val) {
		$checked['bdAllowPluginTag'][$val] = 'checked';
	}
}

if(isset($bdAllowPluginDomain)) {
	$bdAllowPluginDomain = explode('|',$bdAllowPluginDomain);
}
else{
	$bdAllowPluginDomain = validation::$_allowPluginDomain;
}

$rowCount =  ceil(count($bdAllowPluginDomain)/2);

$selected['bdAlign'][$bdAlign]		= "selected";
if(!$bdmaxsize_select) $bdmaxsize_select = 'o';
$selected['bdmaxsize_select'][$bdmaxsize_select] = "selected";

$checked['bdPrnType'][$bdPrnType]	= "checked";
$checked['bdListImg'][$bdListImg]	= "checked";
$checked['bdAdminDsp'][$bdAdminDsp]	= "checked";
$checked['bdUserDsp'][$bdUserDsp]	= "checked";
$checked['bdSecretChk'][$bdSecretChk]	= "checked";
if($bdEditorChk!= 0||$bdEditorChk == null) $checked['bdEditorChk']="checked";

$disabled['bdListImg']		= (in_array($bdSkin, array('gallery', 'photo')) ? "" : "disabled");
$disabled['bdIpAsterisk']	= ($bdIp ? "" : "disabled");

if(!$bdWidth) $bdWidth = "95%";
if(!$bdPageNum) $bdPageNum = "20";

if(!$bdUseMobile) $bdUseMobile = 'N';
$checked['bdUseMobile'][$bdUseMobile] = "checked";

$od	= opendir("../../data/skin/".$cfg['tplSkin']."/board");
$i	= 0;
while ($rd=readdir($od)){
	if (!ereg("\.$",$rd))$rdir[]= $rd;
}
asort($rdir);
?>
<script>
var skin = new Array();
<?
$i=0;
foreach($rdir as $v){
	echo "skin[$i] = \"$v\"; \n";
	$i++;
}
?>

function createMenus()
{
	var idx	= 0;
	var tmp	= new Array();
	for (i=0;i<skin.length;i++){
		tmp[i] = "<option value='" + skin[i] + "'>" + skin[i] + "</option>";
		if (skin[i]=="<?=$bdSkin?>") var idx = i;
	}
	SKIN.innerHTML = "<select name=\"bdSkin\" onChange=\"setDisabled(this.value);\">" + tmp.join() + "</select>"; // onChange='setSub(this.value)'
	document.forms[0].bdSkin.options[idx].selected = 1;
	//setSub(document.forms[0].bdSkin.value);
}

function setSub(skin)
{
	exec_script("sub.js.php?time=<?=time()?>&skin=" + skin + "&tplSkin=<?=$cfg[tplSkin]?>&bdImg=<?=$bdImg?>");
}

function setDisabled(skin)
{
	var disabled1	= (inArray(skin, new Array('gallery', 'photo')) ? false : true);
	var disabled2	= (inArray(skin, new Array('gallery')) ? false : true);
	var enabled1	= (inArray(skin, new Array('gallery', 'default')) ? true : false);

	if(enabled1 == true){
		document.getElementsByName('bdUseMobile')[0].disabled = false;
	}
	else{
		document.getElementsByName('bdUseMobile')[0].disabled = true;
		document.getElementsByName('bdUseMobile')[1].checked = true;
	}

	if(disabled1 == true){
		document.getElementById('ListImg').style.display = 'none';
		document.getElementById('ListImgSize').style.display = 'none';
	}else{
		document.getElementById('ListImg').style.display = 'block';
		document.getElementById('ListImgSize').style.display = 'block';
	}
	if(disabled2 == true){
		document.getElementById('ListImgCnt').style.display = 'none';
	}else{
		document.getElementById('ListImgCnt').style.display = 'block';
	}
}

function useSubSpeechChk(){
	if( document.getElementById("UseSubSpeech").checked == true ){
		document.getElementById('subSpeechWrite').style.display = 'block';
	}else{
		document.getElementById('subSpeechWrite').style.display = 'none';
	}
}
function add(){
	var table = document.getElementById('domainTable');
	if (table.rows.length>11){
		alert("���� ���ε�� �ִ� 12���� �����մϴ�");
		return;
	}
	tblRowsLength = table.rows.length;

	tmpInputBox = "<input type=\"text\" name=\"bdAllowPluginDomain[]\" style=\"border:1px solid #cccccc;height:20px\" />";
	tmpDelBtn = "<a href=\"javascript:del('tr"+(tblRowsLength-1)+"')\"><img src=\"../img/i_del.gif\" /></a>";
	oTr		= table.insertRow( tblRowsLength );
	oTr.id	= 'tr'+(tblRowsLength-1);
	oTd = oTr.insertCell(0);
	oTd.innerHTML = tmpInputBox;
	oTd		= oTr.insertCell(1);
	oTd.innerHTML = tmpInputBox;
	oTd = oTr.insertCell(2);
	oTd.innerHTML = tmpDelBtn;
	oTd = oTr.insertCell(3);
	oTd.innerHTML = tmpHTML;
}

function del(index)
{
	var table = document.getElementById('domainTable');
    for (i=0;i<table.rows.length;i++) if (index==table.rows[i].id) table.deleteRow(i);

}

// ���Ͼ��ε������
function uploadSizeSelect()
{
	if (document.forms[0].bdmaxsize_select.value == 'o') {
		document.forms[0].bdMaxSize.readOnly = false;
		document.forms[0].bdMaxSize.focus();
		document.getElementById("maxsizeMsg").style.display = "";
	}
	else {
		document.forms[0].bdMaxSize.readOnly = true;
		document.forms[0].bdMaxSize.value = document.forms[0].bdmaxsize_select.value;
		document.getElementById("maxsizeMsg").style.display = "none";
	}
}
</script>

<body onLoad="createMenus();setDisabled('<?=$bdSkin?>');uploadSizeSelect();">

<form id="form" method="post" action="indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">�⺻����<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>�Խ��� ID</td>
	<td><?=$bdId?> <font class="extext">(�����Է� / �ٸ� �Խ��� ID�� �ߺ��Ұ�)</font></td>
</tr>
<tr>
	<td>�Խ��� �̸�</td>
	<td><input type="text" name="bdName" value="<?=$bdName?>" class="line" /> <font class="extext">�ѱ��Է�</font></td>
</tr>
<tr>
	<td>��Ų ����<br><font class="small" color="6d6d6d">(�Խ��ǽ�Ÿ��)</font></td>
	<td>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td><div style="position:relative;width:80px;" id="SKIN"></div></td>
		<td><div style="position:relative;" id="IMG"></div></td>
		<td style="padding-left:7"><font class="extext">gallery, photo ��Ų ���� �ϴܿ���  '���Ͼ��ε�' ����� �� üũ�ϼ���.<br/>�� ����ϼ��� default ��Ų�� gallery ��Ų�� ��� �����մϴ�.
</font></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td>����ϼ� ��뿩��</td>
	<td>
		<label><input type="radio" name="bdUseMobile" value="Y" class="null" <?=$checked['bdUseMobile']['Y']?> /> ���</label>
		<label><input type="radio" name="bdUseMobile" value="N" class="null" <?=$checked['bdUseMobile']['N']?> /> �̻��</label>
		<font class="extext">����ϼ�V2 (default ��Ų ����) �� ��� �����ϸ�, ����ϼ�V1���� ������� �ʽ��ϴ�.</font>
	</td>
</tr>
<? if($_GET['mode'] == 'modify'){?>
<tr>
	<td>PC�Խ��� �ּ�</td>
	<td>(���θ��ּ�) <?=$cfg['rootDir'].'/board/list.php?id='.$_GET['id']?>&nbsp;<img src="../img/i_copy.gif" align="absmiddle" onclick="prompt('Ctrl+C�� ���� Ŭ������� �����ϼ���', '<?=$cfg['rootDir'].'/board/list.php?id='.$_GET['id']?>')" style="cursor:pointer" /></td>
</tr>
<tr>
	<td>����ϰԽ��� �ּ�</td>
	<td>(���θ��ּ�) <?='/m2/board/list.php?id='.$_GET['id']?>&nbsp;<img src="../img/i_copy.gif" onclick="prompt('Ctrl+C�� ���� Ŭ������� �����ϼ���', '<?=$cfgMobileShop['mobileShopRootDir'].'/board/list.php?id='.$_GET['id']?>')" style="cursor:pointer" /></td>
</tr>
<?}?>
<tr>
	<td>�Խ��� ��ġ</td>
	<td>
		<select name="bdAlign">
		<option value="center" <?=$selected['bdAlign']['center']?>>�������
		<option value="left" <?=$selected['bdAlign']['left']?>>��������
		<option value="right" <?=$selected['bdAlign']['right']?>>����������
		</select> <font class="extext">������� ����</font>
	</td>
</tr>
<tr>
	<td>�Խ��� ����</td>
	<td>
		<input type="text" name="bdWidth" size="6" class="rline" value="<?=$bdWidth?>" /> <font class="extext">% ���� ������ �� % �� �־��ּ���. �ȼ� ���� ������ ���ڸ� �Է��ϼ���</font>
	</td>
</tr>
<tr>
	<td>�ۼ��� ǥ�ù��</td>
	<td>
		<input type="radio" name="bdUserDsp" value="0" class="null" <?=$checked['bdUserDsp'][0]?> /> �̸�ǥ��
		<input type="radio" name="bdUserDsp" value="1" class="null" <?=$checked['bdUserDsp'][1]?> /> ���̵�ǥ��
		<input type="radio" name="bdUserDsp" value="2" class="null" <?=$checked['bdUserDsp'][2]?> /> �г���ǥ�� <font class="extext">(�г����� ���� ��쿡�� �̸��� ǥ�õ˴ϴ�)</font>
	</td>
</tr>
<tr>
	<td>������ ǥ�ù��</td>
	<td>
		<input type="radio" name="bdAdminDsp" value="0" class="null" <?=$checked['bdAdminDsp'][0]?> /> �̹����� ǥ�� <font class="extext">(�̹��� ����� <a href="/shop/admin/board/list.php" target="_new"><font class="small1" color="0074ba">�Խ��Ǹ���Ʈ</font></a> ���� ��ϰ���)</font>
		<input type="radio" name="bdAdminDsp" value="1" class="null" <?=$checked['bdAdminDsp'][1]?> /> �� �ۼ��� ǥ�ù���� �����ϰ� ǥ��
	</td>
</tr>
<tr>
	<td>���Ӹ� ���</td>
	<td>
		<div class="noline"><input type="checkbox" name="bdUseSubSpeech" id="UseSubSpeech" onclick="useSubSpeechChk();"; <? if ($bdUseSubSpeech=="on") echo"checked" ?> /> ���Ӹ� ��� <font class="extext">(���ۼ��� ����տ� Ư���ܾ �ִ� ����Դϴ�)</font></div>
		<div id="subSpeechWrite" style="display:none">
		<table align="left">
		<tr>
			<td>���Ӹ� Ÿ��Ʋ</td>
			<td><input type="text" name="bdSubSpeechTitle" size="30" class="line" value="<?=$bdSubSpeechTitle?>" /></td>
			<td></td>
		</tr>
		<tr>
			<td>���Ӹ� �Է�</td>
			<td><textarea name="bdSubSpeech" style="width:200px" rows=8><?=str_replace("|",chr(10),$bdSubSpeech)?></textarea></td>
			<td>
			<font class="extext">- �������� ���Ӹ��� ����� �� �ֽ��ϴ�<br />
			<div style="padding-top:1">- ���ۼ��� ���Ӹ��� ������ �� �ֽ��ϴ�</div>
			<div style="padding-top:1">- ���ͷ� ������ ���ּ���</div>
			<div style="padding-top:1">- ���Ӹ����� ���� �Ǵ� ������ �����Խ��ǿ��� ������ ���� �ʽ��ϴ�</font></div>
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>

<div class="title">���Ѽ��� �� ���Լ���<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>���� ����</td>
	<td>
	<table align="left" border=0>
	<tr>
		<td align="center">����Ʈ����</td>
		<td align="center">�۳��뺸��</td>
		<td align="center">�ڸ�Ʈ�ޱ�</td>
		<td align="center">�۾���</td>
		<td align="center">��۾���</td>
	</tr>
	<tr>
		<?
		$r_level = array("L","R","C","W","P");

		$res2 = $db->query("select * from ".GD_MEMBER_GRP." order by level");
		while ($data=$db->fetch($res2)) $memberGrp[$data['level']] = $data['grpnm'];

		$selected['bdLvlL'][$bdLvlL] = "selected";
		$selected['bdLvlR'][$bdLvlR] = "selected";
		$selected['bdLvlW'][$bdLvlW] = "selected";
		$selected['bdLvlC'][$bdLvlC] = "selected";
		$selected['bdLvlP'][$bdLvlP] = "selected";

		for ($i=0;$i<count($r_level);$i++){
		?>
		<td>
			<select name="bdLvl<?=$r_level[$i]?>">
			<option value=''>���Ѿ���</option>
			<? foreach ($memberGrp as $k => $v){ ?>
			<option value="<?=$k?>" <?=$selected["bdLvl$r_level[$i]"][$k]?> style="background-color:#E9FFE9"><?=$v?> - lv[<?=$k?>]</option>
			<? } ?>
			</select>
		</td>
		<? } ?>
	</tr>
	<tr>
		<td colspan="4">
		<div style="padding:3 0 6 0"><font class=extext><a href="/shop/admin/member/group.php" target="_new"><font class="extext_l">[�׷����]</font></a> ���� �׷��� ���弼��</div>
	<div>�׷���ѽ� ���� ���� ���� �׷� ������ ���� ����� ���� ������ �ֽ��ϴ�.</font></div>

		</td>
	</tr>
	</table>




	</td>
</tr>
<tr>
	<td>�ڸ�Ʈ ���Թ���</td>
	<td class="noline">
		<input type="checkbox" name="bdSpamComment[]" value="1" <? if ($bdSpamComment&1) echo"checked" ?> /> �ܺ��������� &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdSpamComment[]" value="2" <? if ($bdSpamComment&2) echo"checked" ?> /> �ڵ���Ϲ�������

		<table cellpadding="0" cellspacing="0">
		<tr><td style="padding: 5 0 5 3"><font class=extext>�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=9')"><u>��ġ�ȳ�</u></a>�� �о����</font></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td>�Խñ� ���Թ���</td>
	<td class="noline">
		<input type="checkbox" name="bdSpamBoard[]" value="1" <? if ($bdSpamBoard&1) echo"checked" ?> /> �ܺ��������� &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdSpamBoard[]" value="2" <? if ($bdSpamBoard&2) echo"checked" ?> /> �ڵ���Ϲ������� <font class="extext"><a href="javascript:popupLayer('../board/popup.captcha.php')"><font class="extext_l">[�̹�������]</font></a>

		<table cellpadding="0" cellspacing="0">
		<tr><td style="padding: 5 0 5 3">
		<font class="extext">���Թ����� ���� �ڼ��� �����Ͻ÷��� <a href="http://www.godo.co.kr/edu/edu_board_list.html?cate=adminen&in_view=y&sno=408#Go_view" target=_blank><font class="extext_l">[�����ڷ�]</font></a> �� Ȯ���ϼ���</font></font><br>
		<div style="padding-top:3"><font class=extext>�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=9')"><font class="extext_l">[��ġ�ȳ�]</font></a> �� �о����</font></div></td></tr>
		</table>
	</td>
</tr>
<tr>
	<td>�����±� ������</td>
	<td class="noline">
		<input type="radio" name="bdUseXss" value="html" <?=$checked['bdUseXss']['html']?> onclick="document.getElementById('tr_allow_domain').style.display=''" /> ����� &nbsp; &nbsp; &nbsp;
		<input type="radio" name="bdUseXss" value="disable" <?=$checked['bdUseXss']['disable']?> onclick="document.getElementById('tr_allow_domain').style.display='none'" /> ������ <font class="extext">

		<table cellpadding="0" cellspacing="0">
			<tr><td style="padding: 5 0 5 3">
				<font class="extext">�Խñ� ��� �� �Ǽ� �ڵ��� ������ �����ϱ� ���� ����Դϴ�</font><br>
			</td></tr>
		</table>
	</td>
</tr>
<tr id="tr_allow_domain">
	<td>��� �±� ����
	<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" title="�� �ܺ� �������� ���θ��� �����ϱ� ���� ���� ���Ǵ� �±׿� ���� �����Դϴ�.
- iframe : �Խ��� ������ ���� ������(����Ʈ)�� ���� �κ��� ������ �� �ֽ��ϴ�.
- embed : �Խ��� ������ ���� ������(����Ʈ)���� �����ϴ� ������, ����, �÷��� �� �̵�� ������ �����ų �� �ֽ��ϴ�" />
	<br/>
	<input type="checkbox" name="bdAllowPluginTag[]"  value="iframe" <?=$checked['bdAllowPluginTag']['iframe']?> style="border:none"/>iframe<br/>
	<input type="checkbox" name="bdAllowPluginTag[]"  value="embed" <?=$checked['bdAllowPluginTag']['embed']?> style="border:none"/>embed
	</td>
	<td class="noline">
		<table cellpadding="6" cellspacing="1" class="tb" id="domainTable">
		<colgroup>
			<col width="20%">
			<col width="20%">
			<col width="*">
		</colgroup>
		<tr class="cellC">
			<td colspan="2" >��� ������</td>
			<td> - </td>
		</tr>
		<?
		$y=0;
		for($i=0 ; $i<$rowCount ; $i++){?>
		<tr id="tr<?=$i?>">
			<td><input type="text" name="bdAllowPluginDomain[]" style="border:1px solid #cccccc;height:20px" value="<?=$bdAllowPluginDomain[$y]?>" /></td><?$y++;?>
			<td><input type="text" name="bdAllowPluginDomain[]" style="border:1px solid #cccccc;height:20px" value="<?=$bdAllowPluginDomain[$y]?>" /></td><?$y++;?>
			<td>
			<?if($i==0){?>
				<a href="javascript:add()"><img src="../img/i_add.gif" /></a>
			<?}
			else{?>
				<a href="javascript:del('tr<?=$i?>')"><img src="../img/i_del.gif" /></a>
			<?}?>
			</td>
		</tr>
		<?}?>
		</table>
		 <font class="extext">
		 ���� �������� �������� ���� iframe �±� ����� �����մϴ�. ��) youtube.com <br/>
������ ����� �±� ������� ���Ͽ� Ȥ�� �� ��� ������ �����ϱ� ���Ͽ� ���� ������ �̿��� �������� ���ѵ˴ϴ�.</font>
	</td>
</tr>
</table>

<div class="title">����Ʈȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>����ۼ� ����</td>
	<td>
		<input type="text" name="bdStrlen" size="6" class="rline" value="<?=$bdStrlen?>" onkeydown="onlynumber();" /> ���ڼ� ����
		&nbsp;<font class="extext">(���ۼ��� ���� ����, ���� ���ڼ� �̻��� ���� ���� �۾��� ������ ����)</font>
	</td>
</tr>
<tr>
	<td>�������� �Խù���</td>
	<td>
		<input type="text" name="bdPageNum" size="6" class="rline" value="<?=$bdPageNum?>" onkeydown="onlynumber();" /> ��
		&nbsp;<font class="extext">(�⺻ 20�� ���, gallery ��Ų�� ���� �Ʒ� �̹��� �������� ���� ���)</font>
	</td>
</tr>
<tr>
	<td>NEW������ ȿ��</td>
	<td>
		<input type="text" name="bdNew" size="6" class="rline" value="<?=$bdNew?>" onkeydown="onlynumber();" /> �ð�
		&nbsp;<font class="extext">(���ۼ��� ������)</font>
	</td>
</tr>
<tr>
	<td>HOT������ ����</td>
	<td>
		��ȸ�� <input type="text" name="bdHot" class="rline" size="6" value="<?=$bdHot?>" onkeydown="onlynumber();" /> ȸ �̻� �Խñ�
		&nbsp;<font class="extext">(���ۼ��� ������)</font>
	</td>
</tr>
<tr>
	<td>������ ��� ����</td>
	<td class="noline">
		<input type="radio" name="bdNoticeList" value="" <? if (!$bdNoticeList) echo "checked" ?> /> 1���������� ���
		<input type="radio" name="bdNoticeList" value="o" <? if ($bdNoticeList) echo "checked" ?> /> ��������� ���
	</td>
</tr>
<tr>
	<td>�׸��߱�</td>
	<td class="noline">

	<input type="checkbox" name="bdField[]" value="1" <? if ($bdField&1) echo"checked" ?> /> üũ
	<input type="checkbox" name="bdField[]" value="2" <? if ($bdField&2) echo"checked" ?> /> ��ȣ
	<input type="checkbox" name="bdField[]" value="4" <? if ($bdField&4) echo"checked" ?> /> ����
	<input type="checkbox" name="bdField[]" value="8" <? if ($bdField&8) echo"checked" ?> /> �̸�
	<input type="checkbox" name="bdField[]" value="16" <? if ($bdField&16) echo"checked" ?> /> ��¥
	<input type="checkbox" name="bdField[]" value="32" <? if ($bdField&32) echo"checked" ?> /> ��ȸ��

	</td>
</tr>
<tr>
	<td>�˻� ���</td>
	<td class="noline">
		<input type="radio" name="bdSearchMode" value="0" <? if (!$bdSearchMode) echo "checked" ?> /> �Ϲ� �˻� (�˻��� Ǯ��ĵ)
		<input type="radio" name="bdSearchMode" value="1" <? if ($bdSearchMode) echo "checked" ?> /> ���� �˻� (�˻��� ���ϸ� ���̱� ���� ����¡ ����)
	</td>
</tr>
<tr>
	<td>����Ʈ<br>
	������� ����<br><font class="small" color="6d6d6d">(list.php)</font></td>
	<td class="noline">
		<div><input type="radio" name="bdPrnType" value="1" <?=$checked['bdPrnType'][1] ?> /> �⺻���� (����,�ۼ���,��ȸ��,�ڸ�Ʈ��,���ε�����1��)&nbsp;<font class="extext">(default, gallery, photo ��Ų�� �⺻������ �����ϼ���)</font></div>
		<div><input type="radio" name="bdPrnType" value="2" <?=$checked['bdPrnType'][2] ?> /> ������ (�⺻������ ������ ����Ʈ��¿� �ʿ��� ��� ����Ÿ)&nbsp;<font class="extext">(webzine ��Ų�� �������� �����ϼ���)</font></div>
	</td>
</tr>
<tr id="ListImgCnt">
	<td>����� �̹��� ����</td>
	<td>
		<input type="text" name="bdListImgCntW" size="6" class="rline" value="<?=$bdListImgCntW?>" onkeydown="onlynumber();" /> X
		<input type="text" name="bdListImgCntH" size="6" class="rline" value="<?=$bdListImgCntH?>" onkeydown="onlynumber();" />
		&nbsp;<font class="extext">(gallery ��Ų�� ���. ������Ÿ�� �Խ��� ����Ʈ�� ������ ������̹��� ����)</font>
	</td>
</tr>
<tr id="ListImgSize">
	<td>����� �̹��� ũ��</td>
	<td>
		<input type="text" name="bdListImgSizeW" id="ListImgSizeW" size="6" class="rline" value="<?=$bdListImgSizeW?>" onkeydown="onlynumber();" /> Pixel X
		<input type="text" name="bdListImgSizeH" id="ListImgSizeH" size="6" class="rline" value="<?=$bdListImgSizeH?>" onkeydown="onlynumber();" /> Pixel
		&nbsp;<font class="extext">(gallery, photo ��Ų�� ���)</font>
	</td>
</tr>
<tr id="ListImg">
	<td>�̹��� Ŭ������</td>
	<td class="noline">
		<input type="radio" name="bdListImg" value="1" <?=$checked['bdListImg'][1] ?> <?=$disabled['bdListImg'] ?> /> �̹��� Ŭ���� �˾�â�� ��ϴ�&nbsp;
		<input type="radio" name="bdListImg" value="2" <?=$checked['bdListImg'][2] ?> <?=$disabled['bdListImg'] ?> /> �̹��� Ŭ���� �۳������� �̵�&nbsp;
		&nbsp;<font class="extext">(gallery, photo ��Ų�� ���)</font> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><u><font class="small1" color="0074ba"><b>[<u>����ȭ�麸��</u>]</b></a>
	</td>
</tr>
</table>

<div class="title">��ȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>View Ÿ��</td>
	<td class="noline">
		<?
		$r_tmp = array("���븸","���ñ�","����Ʈ");
		for ($i=0;$i<count($r_tmp);$i++){
			$chk = ($bdTypeView==$i) ? "checked" : "";
			echo "<input type=\"radio\" name=\"bdTypeView\" value=\"$i\" $chk class=\"noline\" /> $r_tmp[$i] ";
		}
		?>
	</td>
</tr>
<tr>
	<td>IP ���</td>
	<td class="noline">
		<input type="checkbox" name="bdIp" <? if ($bdIp=="on") echo"checked" ?> onclick="this.form['bdIpAsterisk'].disabled = !this.checked" /> �۾����� IP�� �����ݴϴ�
		<div style="padding: 2px 0 3px 0"><input type="checkbox" name="bdIpAsterisk" <? if ($bdIpAsterisk=="on") echo"checked" ?> <?=$disabled['bdIpAsterisk'] ?> /> IP ���ڸ� ��ȣȭǥ�� <font class=extext>��)</font> <font class="ver71" color="#627dce">123.213.139.***</font></div>
	</td>
</tr>
<tr>
	<td>��ũ/���ε�</td>
	<td class="noline">
		<input type="checkbox" name="bdUseLink" <? if ($bdUseLink=="on") echo"checked" ?> /> ��ũ &nbsp; &nbsp; &nbsp;
		<input type="checkbox" name="bdUseFile" <? if ($bdUseFile=="on") echo"checked" ?> /> ���Ͼ��ε� <font class="extext">(Gallery, Photo ��Ų ���� �� üũ�ϼ���!)</font>
	</td>
</tr>
<tr>
	<td>�ڸ�Ʈ(���)���</td>
	<td class="noline"><input type="checkbox" name="bdUseComment" <? if ($bdUseComment=="on") echo"checked" ?> /> ���</td>
</tr>
</table>

<div class="title">�ۼ�ȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>��б� ����</td>
	<td>
		<input type="radio" name="bdSecretChk" value="0" class="null" <?=$checked['bdSecretChk'][0]?> /> �ۼ��� �⺻ �Ϲݱ�
		<input type="radio" name="bdSecretChk" value="1" class="null" <?=$checked['bdSecretChk'][1]?> /> �ۼ��� �⺻ ��б�
		<input type="radio" name="bdSecretChk" value="2" class="null" <?=$checked['bdSecretChk'][2]?> /> ������ �Ϲݱ�
		<input type="radio" name="bdSecretChk" value="3" class="null" <?=$checked['bdSecretChk'][3]?> /> ������ ��б�
	</td>
</tr>
<tr>
	<td>�����ۼ� ����</td>
	<td>
		<input type="checkbox" name="bdTitleCChk" class="null" <? if ($bdTitleCChk=="on") echo"checked" ?> /> ���ڻ� ���
		<input type="checkbox" name="bdTitleSChk" class="null" <? if ($bdTitleSChk=="on") echo"checked" ?> /> ����ũ�� ���
		<input type="checkbox" name="bdTitleBChk" class="null" <? if ($bdTitleBChk=="on") echo"checked" ?> /> ���ڱ��� ���
	</td>
</tr>
<tr>
	<td>������ ���ε� ���</td>
	<td><input type="checkbox" name="bdEditorChk" class="null" value="1" <?=$checked['bdEditorChk']?> /> ������ ���ε� ���</td>
</tr>
<tr>
	<td>���ε����� Size</td>
	<td>
		�뷮����
		<select name="bdmaxsize_select" id="bdmaxsize_select" onchange="uploadSizeSelect()" >
			<option value="">�����ϼ���</option>
			<?php
				$min = str_to_byte("1MB");
				$max = str_to_byte(ini_get("upload_max_filesize"));
				for($i=$min; $i<=$max; $i=$i+$min) {
			?>
			<option value="<?=$i?>" <?=$selected['bdmaxsize_select'][$i]?>> <?=byte2str($i)?></option>
			<?php } ?>
		    <option value="o" <?=$selected['bdmaxsize_select']['o']?>>��������</option>
		</select>
		<span style="display:none;" id="maxsizeMsg">
			<input type="text" name="bdMaxSize" size="9" class="rline" value="<?=$bdMaxSize?>" onkeydown="javascript:onlynumber();" /> Byte
			<font class="extext">���� ������ �ִ� �뷮�� <?=number_format(str_to_byte(ini_get("upload_max_filesize")))?> byte (<?=ini_get("upload_max_filesize")?>) �Դϴ�.</font>
		</span>
	</td>
</tr>
<tr>
	<td>�̸��� �ۼ�</td>
	<td>
		<input type="checkbox" name="bdEmailNo" class="null" <? if ($bdEmailNo=="on") echo"checked" ?> /> �̸��� �ۼ� �̻��
	</td>
</tr>
<tr>
	<td>Ȩ������ �ۼ�</td>
	<td>
		<input type="checkbox" name="bdHomepageNo" class="null" <? if ($bdHomepageNo=="on") echo"checked" ?> /> Ȩ������ �ۼ� �̻��
	</td>
</tr>

<!--
<tr>
	<td>����ȯ�漳��</td>
	<td class="noline">
		<input type="radio" name="bdTypeMail" value="0" <? if (!$bdTypeMail) echo "checked" ?> /> Outlook
		<input type="radio" name="bdTypeMail" value="1" <? if ($bdTypeMail) echo "checked" ?> /> ���� ���ϸ�
	</td>
</tr>
-->

<!--
<tr>
	<td>��Ų ����</td>
	<td>
		<div style="padding:5">���ѻ� �� ���ѻ� (�ٹٲ����� ����)</div>
		<textarea name="bdColor" style="width:100%" rows="5"><?=$bdColor?></textarea>
	</td>
</tr>
-->
</table>

<div class="title">HTML����<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=3');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:27">
<tr>
	<td>��ܵ�����<br>(Header)</td>
	<td>
		<textarea name="bdHeader" style="width:100%" rows=8 class=tline><?=stripslashes($bdHeader)?></textarea>
	</td>
</tr>
<tr>
	<td>�ϴܵ�����<br>(Footer)</td>
	<td>
		<textarea name="bdFooter" style="width:100%" rows=8 class=tline><?=stripslashes($bdFooter)?></textarea>
	</td>
</tr>
</table>


<div style="padding:20px" align="center" class="noline">
<div class="button">
<input type="image" src="../img/btn_<?=$_GET['mode']?>.gif" />
<a href="list.php"><img src="../img/btn_list.gif" /></a>
</div><div>



</form>

<script>useSubSpeechChk();</script>
<? if($checked['bdUseXss']['disable']){?>
<script>
	document.getElementById('tr_allow_domain').style.display = 'none';
</script>
<?}?>

<? include "../_footer.php"; ?>