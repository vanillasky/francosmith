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
 */

$location = "�����̼� > �Խ��Ǹ����";
include "../_header.php";

if (!$_GET['mode']) $_GET['mode'] = "register";
$returnUrl = ($_GET['returnUrl']) ? $_GET['returnUrl'] : $_SERVER['HTTP_REFERER'];
switch ($_GET['mode']){
	case "register":
		$bdId = "<input type=\"text\" name=\"id\" class=\"line\" required label=\"�Խ��� ID\" option=\"regAlpha\" />";
		break;
	case "modify":
		include "../../conf/bd_".$_GET['id'].".php";
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

$selected['bdAlign'][$bdAlign]		= "selected";

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
</script>

<body onLoad="createMenus();setDisabled('<?=$bdSkin?>');">

<form id="form" method="post" action="indb.board.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />

<div class="title title_top">�⺻����<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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
		<td style="padding-left:7"><font class="extext">gallery, photo ��Ų ���� �ϴܿ���  '���Ͼ��ε�' ����� �� üũ�ϼ���</font></td>
	</tr>
	</table>
	</td>
</tr>
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
		<input type="radio" name="bdAdminDsp" value="0" class="null" <?=$checked['bdAdminDsp'][0]?> /> �̹����� ǥ�� <font class="extext">(�̹��� ����� <a href="/shop/admin/board/board_list.php" target="_new"><font class="small1" color="0074ba">�Խ��Ǹ���Ʈ</font></a> ���� ��ϰ���)</font>
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

<div class="title">���Ѽ��� �� ���Լ���<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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
	</tr>
	<tr>
		<?
		$r_level = array("L","R","C","W");

		$res2 = $db->query("select * from ".GD_MEMBER_GRP." order by level");
		while ($data=$db->fetch($res2)) $memberGrp[$data['level']] = $data['grpnm'];

		$selected['bdLvlL'][$bdLvlL] = "selected";
		$selected['bdLvlR'][$bdLvlR] = "selected";
		$selected['bdLvlW'][$bdLvlW] = "selected";
		$selected['bdLvlC'][$bdLvlC] = "selected";

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
		<tr><td style="padding: 5 0 5 3"><font class=extext>�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><u>��ġ�ȳ�</u></a>�� �о����</font></td></tr>
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
		<div style="padding-top:3"><font class=extext>�� ���Թ�������� ���� ���׷��̵� �� ����Դϴ�. ��ɻ�� ���� �� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><font class="extext_l">[��ġ�ȳ�]</font></a> �� �о����</font></div></td></tr>
		</table>
	</td>
</tr>
</table>

<div class="title">����Ʈȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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
	������� ����<br><font class="small" color="6d6d6d">(board_list.php)</font></td>
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
		&nbsp;<font class="extext">(gallery, photo ��Ų�� ���)</font> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><u><font class="small1" color="0074ba"><b>[<u>����ȭ�麸��</u>]</b></a>
	</td>
</tr>
</table>

<div class="title">��ȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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

<div class="title">�ۼ�ȭ�鼳��<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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
		<input type="text" name="bdMaxSize" size="6" class="rline" value="<?=$bdMaxSize?>" onkeydown="onlynumber();" /> Byte
		<font class="extext">(���� ���ε�� ����ũ�⸦ �����մϴ�.)</font></font>
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

<div class="title">HTML����<span>Ŀ�´�Ƽ �޴����� �����ϴ� �Խ����� ����ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

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
<a href="board_list.php"><img src="../img/btn_list.gif" /></a>
</div><div>



</form>

<script>useSubSpeechChk();</script>

<? include "../_footer.php"; ?>