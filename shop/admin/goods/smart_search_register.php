<?
	$location = "��ǰ���� > SMART �˻� �׸����� > SMART �˻� �׸� ���";
	include "../_header.php";
	require_once("../../lib/smartSearch.class.php");

	if(get_magic_quotes_gpc()) {
		stripslashes_all($_POST);
		stripslashes_all($_GET);
	}

	if(!$_GET['mode']) $_GET['mode'] = 'regTheme';
	if($_GET['mode'] == 'regTheme') $data['price'] = 'y';

	if($_GET['no']) {
		$qr = "SELECT ss.* FROM ".GD_GOODS_SMART_SEARCH." AS ss LEFT JOIN ".GD_CATEGORY." AS c ON ss.sno = c.themeno WHERE ss.sno = '".$_GET['no']."'";
		$data = $db->fetch($qr);
	}
	else $data['sno'] = rand(11111, 99999).time();

	$check['price'][$data['price']] = ' checked';
	$data['color'] = ($data['color']) ? $data['color'] : 'n';
	$check['color'][$data['color']] = ' checked';
	$data['ssOrder'] = ($data['ssOrder']) ? $data['ssOrder'] : 'beo';
	$queryString = "sort=".$_GET['sort']."&skey=".$_GET['skey']."&sword=".$_GET['sword']."&cate[]=".$_GET['cate'][0]."&cate[]=".$_GET['cate'][1]."&cate[]=".$_GET['cate'][2]."&cate[]=".$_GET['cate'][3]."&regdt[]=".$_GET['regdt'][0]."&regdt[]=".$_GET['regdt'][1]."&page=".$_GET['page'];
?>
<script language="javascript">
function menu_sync(menu) {
	if(typeof(obj['category[]'])=="undefined") {
		if(!document.getElementsByName("cate[]")[0].value) {
			alert("ī�װ��� �������ּ���");
			document.getElementsByName("cate[]")[0].focus();
			return false;
		}

		cate = "";
		for (var i=0;i<4;i++) if(document.getElementsByName("cate[]")[i].value) cate = document.getElementsByName("cate[]")[i].value;
		$('goods_' + menu + '_menu').innerHTML = "<table width=\"100%\" height=\"120\" border=\"0\"><tr><td align=\"center\" valign\"middle\"><img src=\"../img/loading20.gif\" /></td></tr></table>";
		ifrmHidden.location.href="./iframe.smart_search.php?mode="+menu+"&cate="+cate+"&themeno="+"<?=$_GET['no']?>";
	}
}

function checkThemeName(themeName, sno) {
	if(themeName) ifrmHidden.location.href="./iframe.smart_search.php?mode=checkThemeName&themeName=" + themeName + "&sno=" + sno;
}

function chkForm2() {
	var goods_add_menu = document.getElementsByName('goods_add_menu[]');
	var t_goods_add_menu = document.getElementsByName('t_goods_add_menu[]');
	var goods_option_menu = document.getElementsByName('goods_option_menu[]');
	var t_goods_option_menu = document.getElementsByName('t_goods_option_menu[]');

	if($('themenm').value=="") {
		alert("�׸����� �Է����ּ���.");
		$('themenm').focus();
		return;
	}
	else {
		if($('checkResult').value == "2") {
			alert("�Է��Ͻ� �׸����� �����մϴ�.\n\n<?=($_GET['mode'] == 'regTheme') ? '���' : '����'?>�Ͻ� �׸����� �ٽ� �Է��� �ּ���.");
			$('themenm').focus();
			return;
		}
	}

	for( var i = 0 ; i < goods_add_menu.length; i++ ) {
		if(goods_add_menu[i].checked && t_goods_add_menu[i].value == '') {
			alert("�߰��������� �� ���� �Էµ� �� �����ϴ�.");
			return;
		}
	}

	for(var i = 0 ; i < goods_option_menu.length; i++) {
		if(goods_option_menu[i].checked && t_goods_option_menu[i].value == '') {
			alert("�ɼǸ��� �� ���� �Էµ� �� �����ϴ�.");
			return;
		}
	}

	document.frmList.action = "./indb.smart_search.php";
	document.frmList.submit();
}

// ���� ���� (�⺻,�߰�,�ɼ� ����)
function ssCheckPart(partName, moveType) {
	var tmpStr = $('ssOrder').value;
	var part1 = tmpStr.substr(0, 1);
	var part2 = tmpStr.substr(1, 1);
	var part3 = tmpStr.substr(2, 1);
	var newPart = "";

	if(moveType == '1') {
		switch(partName) {
			case part1 : return false;break;
			case part2 : newPart = partName + part1 + part3; break;
			case part3 : newPart = part1 + partName + part2; break;
			default : return false; break;
		}
	}
	else {
		switch(partName) {
			case part1 : newPart = part2 + partName + part3; break;
			case part2 : newPart = part1 + part3 + partName; break;
			case part3 : return false;break;
			default : return false; break;
		}
	}

	ssMovePart(newPart);
}

// ������ ������ ���� (�⺻,�߰�,�ɼ� ����)
function ssMovePart(movedPart) {
	if(!movedPart) movedPart = "beo";

	// ������ġ ����
	var tmpStr = $('ssOrder').value;
	var part1 = tmpStr.substr(0, 1);
	var part2 = tmpStr.substr(1, 1);
	var part3 = tmpStr.substr(2, 1);

	// �̵� �� ��ġ ����
	var moved1 = movedPart.substr(0, 1);
	var moved2 = movedPart.substr(1, 1);
	var moved3 = movedPart.substr(2, 1);

	// �̵� �� ��ġ
		// [��ǰ �⺻ ����], [��ǰ �߰� ����], [���� �ɼ�] ���� �κ�
		eval("var tmpCol_" + part1 + " = $('ssCol_0').innerHTML");
		eval("var tmpCol_" + part2 + " = $('ssCol_1').innerHTML");
		eval("var tmpCol_" + part3 + " = $('ssCol_2').innerHTML");
		// [��ǰ �⺻ ����], [��ǰ �߰� ����], [���� �ɼ�] ���� �κ�
		eval("var tmpVal_" + part1 + " = $('ssVal_0').innerHTML");
		eval("var tmpVal_" + part2 + " = $('ssVal_1').innerHTML");
		eval("var tmpVal_" + part3 + " = $('ssVal_2').innerHTML");

	// �̵� �� ��ġ�� ����
		// [��ǰ �⺻ ����], [��ǰ �߰� ����], [���� �ɼ�] ���� �κ�
		eval("$('ssCol_0').innerHTML = tmpCol_" + moved1);
		eval("$('ssCol_1').innerHTML = tmpCol_" + moved2);
		eval("$('ssCol_2').innerHTML = tmpCol_" + moved3);
		// [��ǰ �⺻ ����], [��ǰ �߰� ����], [���� �ɼ�] ���� �κ�
		eval("$('ssVal_0').innerHTML = tmpVal_" + moved1);
		eval("$('ssVal_1').innerHTML = tmpVal_" + moved2);
		eval("$('ssVal_2').innerHTML = tmpVal_" + moved3);

	$('ssOrder').value = movedPart; // ���� ���� ����
}

function ssMoveItem(oItem, tItem) {
	oTD = $(oItem).getElementsByTagName("td");
	tTD = $(tItem).getElementsByTagName("td");

	var tmpOItem0 = oTD[0].innerHTML;
	var tmpTItem0 = tTD[0].innerHTML;

	tTD[0].innerHTML = tmpOItem0;
	oTD[0].innerHTML = tmpTItem0;
}

function newSaveTheme() {
	var themenm = $('themenm').value;
	$('mode').value = "newSaveTheme";

	if($('checkResult').value == "2") {
		alert("���ο� �̸����� �׸��� �����Ͻ÷��� �ٸ� �׸����� �Է����ּ���.");
		$('themenm').focus();
		return;
	}

	document.frmList.action = "./indb.smart_search.php";
	document.frmList.submit();
}

window.onload = function() {
	$('themenm').focus();
	ssMovePart('<?=$data['ssOrder']?>');
	cssRound('MSG01');
}
</script>
<style type="text/css">
	.ssColumn { color:#333333; font-weight:bold; background:#F6F6F6; }
	.ssColumn td { color:#333333; font-weight:bold; }
	.ssColumn1 { width:155px; }
	.ssColumn2 { width:250px; }
	.ssColumn3 { width:250px; }
	.mask { position:absolute; background:#000; opacity:.55; filter:alpha(opacity=55); }
	.mvArw { color:#FF0000; font-family:dotumche; font-weight:bold; }
	.ssValue { color:#333333; background:#FFFFFF; }
</style>

<div class="title title_top">SMART �˻� �׸� ���/����<span>SMART�˻� �׸��� �˻��޴� ������ �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=39')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<div style="padding: 20px 0px 5px 13px"><b><img src="../img/icon_arrow.gif"> SMART �˻� �׸� ����</b></div>

<form name="frmList" method="post">
<input type="hidden" name="mode" id="mode" value="<?=$_GET['mode']?>" />
<input type="hidden" name="sno" id="sno" value="<?=$data['sno']?>" />
<input type="hidden" name="basic" id="basic" value="<?=$data['basic']?>" />
<input type="hidden" name="checkResult" id="checkResult" value="<?=($data['themenm']) ? "1" : "2"?>" />
<input type="hidden" name="ssOrder" id="ssOrder" value="beo" />
<input type="hidden" name="queryString" id="queryString" value="<?=$queryString?>" />
<input type="hidden" name="setThemeColor" id="setThemeColor" />

<table cellspacing="1" cellpadding="4" border="0" bgcolor="#E6E6E6" width="700">
<tr>
	<td width="155" class="ssColumn">SMART �˻� �׸���</td>
	<td class="ssValue">
		<input type="text" name="themenm" id="themenm" value="<?=$data['themenm']?>" class="line" style="width:200px;" onblur="checkThemeName(this.value, $('sno').value)" onkeypress="if(event.keyCode == 13) { return false; }" />
		<span class="extext">����� �׸��� �̸��� �����մϴ�.</span>
	</td>
</tr>
<tr>
	<td class="ssColumn">�˻��� ���� ����</td>
	<td class="ssValue">
		<div><script>new categoryBox('cate[]',4,'<?=$data['category']?>');</script></div>
		<div style="margin-top:10px;" class="extext">����ȭ �ϰ��� �ϴ� ī�װ��� �����մϴ�. �ּ� 1�� ī�װ� �̻� ������ �ּž� �մϴ�.</div>
	</td>
</tr>
</table>

<div style="padding: 20px 0px 5px 13px"><b><img src="../img/icon_arrow.gif"> SMART �˻� �޴� ����</b></div>

<table cellspacing="1" cellpadding="4" border="0" bgcolor="#E6E6E6" width="700">
<tr id="ssColumn" valign="top">
	<td id="ssCol_0" class="ssColumn">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				��ǰ �⺻ ����
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('b', '1');">��</a>
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('b', '2');">��</a>
			</td>
		</tr>
		</table>
	</td>
	<td id="ssCol_1" class="ssColumn">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				��ǰ �߰� ����
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('e', '1');">��</a>
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('e', '2');">��</a>
			</td>
			<td align="right"><a href="javascript:;" onclick="javascript:menu_sync('add');"><img src="../img/icon_same.gif" align="absmiddle" /></a></td>
		</tr>
		<tr height="20">
			<td colspan="2" align="right" valign="bottom"><span class="extext">���� �߰��� ��ǰ�� �˻���� ���� �ҷ��ɴϴ�.</span></td>
		</tr>
		</table>
	</td>
	<td id="ssCol_2" class="ssColumn">
		<table width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td align="left">
				���� �ɼ�
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('o', '1');">��</a>
				<a href="javascript:;" class="mvArw" onclick="ssCheckPart('o', '2');">��</a>
			</td>
			<td align="right"><a href="javascript:;" onclick="javascript:menu_sync('option');"><img src="../img/icon_same.gif" align="absmiddle" /></a></td>
		</tr>
		<tr height="20">
			<td colspan="2" align="right" valign="bottom"><span class="extext">���� �߰��� ��ǰ�� �˻���� ���� �ҷ��ɴϴ�.</span></td>
		</tr>
		</table>
	</td>
</tr>
<tr id="ssValue" valign="top" bgcolor="#FFFFFF">
	<td id="ssVal_0" class="noline cellL">
		<input type="checkbox" name="basic_menu[]" value="price" class="rline" <?=$check['price']['y']?> />����<br />
		<input type="checkbox" name="basic_menu[]" value="maker" class="rline" <?=($data['maker']) ? 'checked' : ''?> />������<br />
		<input type="checkbox" name="basic_menu[]" value="origin" class="rline" <?=($data['origin']) ? 'checked' : ''?> />������<br />
		<input type="checkbox" name="basic_menu[]" value="brandno" class="rline" <?=($data['brandno']) ? 'checked' : ''?> />�귣��
	</td>
	<td id="ssVal_1" class="noline cellL">
		<div name="goods_add_menu" id="goods_add_menu" style="height:120px; margin:5px 0px; overflow-y:auto;"><?
	if($data['ex']) {
		$ex_list = explode("\n", $data['ex']);
		foreach($ex_list as $k => $v) {
			$tempArray = explode("|^|^", $v);
			if($tempArray[0]) $search['ex'][] = $tempArray[0];
		}

		if(is_array($search['ex'])) {
			$tmpNo = 0;
			echo "<table cellpadding='0' cellspacing='0' width='95%'>";
			foreach($search['ex'] as $k => $v) {
				$v = smartSearch::html_encode($v);
?>
				<tr id="e_Item_<?=$tmpNo?>">
					<td><input type="checkbox" name="goods_add_menu[]" value="<?=$v?>" checked /><input type="text" style="width:130px; border:0px" name="t_goods_add_menu[]" value="<?=$v?>" readonly /></td>
					<td align="right">
						<a href="javascript:;" onclick="ssMoveItem('e_Item_<?=$tmpNo?>', 'e_Item_<?=($tmpNo == 0) ? $tmpNo : ($tmpNo - 1)?>')" class="mvArw">��</a>
						<a href="javascript:;" onclick="ssMoveItem('e_Item_<?=$tmpNo?>', 'e_Item_<?=($tmpNo == (count($search['ex']) - 1)) ? $tmpNo : ($tmpNo + 1)?>')" class="mvArw">��</a>
					</td>
				</tr>
<?
				$tmpNo++;
			}
			echo "</table>";
		}
	}
		?></div>
	</td>
	<td id="ssVal_2" class="noline cellL">
		<div name="goods_option_menu" id="goods_option_menu" style="height:120px; margin:5px 0px; overflow-y:auto;"><?
	if($data['opt']) {
		$opt_list = explode("\n", $data['opt']);
		foreach($opt_list as $k => $v) {
			$tempArray = explode(_OPT_PIPE_._OPT_PIPE_, $v);
			if($tempArray[0]) $search['opt'][] = $tempArray[0];
		}

		if(is_array($search['opt'])) {
			$tmpNo = 0;
			echo "<table cellpadding='0' cellspacing='0' width='95%'>";
			foreach($search['opt'] as $k => $v) {
				$v = smartSearch::html_encode($v);
?>
				<tr id="o_Item_<?=$tmpNo?>">
					<td><input type="checkbox" name="goods_option_menu[]" value="<?=$v?>" checked /><input type="text" style="width:130px; border:0px" name="t_goods_option_menu[]" value="<?=$v?>" readonly /></td>
					<td align="right">
						<a href="javascript:;" onclick="ssMoveItem('o_Item_<?=$tmpNo?>', 'o_Item_<?=($tmpNo == 0) ? $tmpNo : ($tmpNo - 1)?>')" class="mvArw">��</a>
						<a href="javascript:;" onclick="ssMoveItem('o_Item_<?=$tmpNo?>', 'o_Item_<?=($tmpNo == (count($search['opt']) - 1)) ? $tmpNo : ($tmpNo + 1)?>')" class="mvArw">��</a>
					</td>
				</tr>
<?
				$tmpNo++;
			}
			echo "</table>";
		}
	}
		?></div>
	</td>
</tr>
</table>


<div style="padding: 20px 0px 5px 13px"><b><img src="../img/icon_arrow.gif"> �ȷ�Ʈ ����˻� ��� ����</b></div>

<table cellspacing="1" cellpadding="4" border="0" bgcolor="#E6E6E6" width="700">
<tr valign="middle">
	<td width="155" class="ssColumn">��� ����</td>
	<td class="ssValue noline" style="padding:10px 4px">
		<input type="radio" name="color" id="color_y" value="y"<?=$check['color']['y']?> /> <label for="color_y">���</label>
		<input type="radio" name="color" id="color_n" value="n"<?=$check['color']['n']?> /> <label for="color_n">������</label>
		<br /><br />
		<span class="extext">��ǰ�� ������������� ������ ��ǰ ��ǥ������ ����Ǿ� �˻��˴ϴ�.<br /><br /><br />
		�ȷ�Ʈ ���� �˻��� ��ǰ��� ������������ ��ǥ���� ���� ����� ��ǰ�� ���Ͽ� �˻��Ǿ� ���ϴ�.<br />
		<a href="../goods/goods_color.php" class="extext" style="font-weight:bold;" target="_blank">[ ��ǰ�ϰ����� > ���� ��ǥ���� ���� ]</a> �� ���Ͽ� ���� ��ǰ�� ��ǥ������ �ϰ��� ���� �Ͻ� �� �ֽ��ϴ�.<br /><br /><br />
		�������  ������ ���ݿɼ� �޴����� ������(Color)�� �� ���� ���,SMART�˻� �޴� ���� �� �ߺ� �� �� ������,<br />
		���ݿɼǿ� ���� ���õ� �޴���� ���� üũ�� �����Ͽ� �ּ���.</span>
	</td>
</tr>
</table>

<div class=button>
<a href="javascript:chkForm2();"><img src="../img/<?=($_GET['mode'] == 'regTheme') ? 'btn_register.gif' : 'btn_modify.gif'?>"></a>
<a href="../goods/smart_search.php?<?=$queryString?>"><img src="../img/btn_cancel.gif"/></a>
</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻� ���� �ϰ������� ���� ��� (��: ������  S, M, L / small, medium, large / ��, ��, �� -> small, medium, large)</td></tr>
<tr><td>&nbsp; ���� ���Ǹ� ���ؼ� ���� Ÿ������ �����ϡ� ���� �ֽô� ���� �����ϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ �߰�����, ���ݿɼ� �ϰ� ���� �� ����� <a href="../data/data_goodsxls.php" target="_blank"><font color="#FFFFFF"><strong>[ �����Ͱ��� > ��ǰDB���/��ǰDB�ٿ�ε� ]</strong></font></a> ���� �����մϴ�.</td></tr>
<tr><td>&nbsp; ��ǰ �ɼǰ��� �ϰ� ���� ��, �ݵ�� ����ȭ�� ���ּž� ������ �ɼǰ��� �ҷ��� �� �ֽ��ϴ�.</td></tr>
</table>
</div>

<? include "../_footer.php"; ?>