<?

$location = "���ǰ��� > FAQ����";
include "../_header.php";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];

$parseUrl = parse_url( $returnUrl );
$listUrl = ( $returnUrl ? $parseUrl[query] : $_SERVER['QUERY_STRING'] );
$listUrl = 'faq.php?' . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_FAQ." where sno='" . $_GET['sno'] . "'",1);
	$data['question'] = htmlspecialchars( $data['question'] );
	$data['descant'] = htmlspecialchars( $data['descant'] );
	$data['answer'] = htmlspecialchars( $data['answer'] );
}
?>

<div id=goods_form>

<form method=post action="faq_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class="title title_top">FAQ ����<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>�з� ����</td>
	<td nowrap>
	<SELECT NAME="itemcd" required label="�з�">
	<option value="">�� FAQ�з��� �����ϼ���.</option>
	<?foreach ( codeitem('faq') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</SELECT>
	</td>
</tr>
<TR>
	<td nowrap>���� (�ܹ�)</td>
	<td nowrap><input type="text" name="question" size="55" value="<?=$data['question']?>" required label="����" class=line></td>
</tr>
<TR>
	<td nowrap>���� (�幮)</td>
	<td nowrap>

	<table width="90%" border="0" cellpadding="0" cellspacing="0" style="margin:5 0 10 0;">
	<tr>
		<td width="200" height="100%" valign="top" style="border:3px #f6f6f6 solid" bgcolor="#ffffff">
		<div style="border:1px #e6e6e6 solid; width:100%; height:100%;">
			<div align="center" style="padding:5;"><font color="298CC7"><b>���� ���� ��Ī</b></font></div>
			<div align="center" style="padding-bottom:5;">
			<SELECT NAME="autorr" size=6 onchange="javascript:document.getElementsByName('getppwordz')[0].value=this.options[this.options.selectedIndex].value;">
			<optGroup label="------ ���� ------">
			<option value="__shopname__"> ���θ��̸� </option>
			<option value="__shopdomain__"> ���θ��ּ� </option>
			<option value="__shopcpaddr__"> ������ּ� </option>
			<option value="__shopcoprnum__"> ����ڵ�Ϲ�ȣ </option>
			<option value="__shopcpmallceo__"> ���θ� ��ǥ </option>
			<option value="__shopcpmanager__"> �������������� </option>
			<option value="__shoptel__"> ���θ� ��ȭ </option>
			<option value="__shopfax__"> ���θ� �ѽ� </option>
			<option value="__shopmail__"> ���θ� �̸��� </option>
			</SELECT>
			</div>
		</div>
		</td>
		<td height="100%" valign="top" style="border:3px #74BBF5 solid" bgcolor="#ffffff">
		<div style="border:1px #298CC7 solid; width:100%; height:100%;">
			<div align="center" style="padding:5;"><b><font color="298CC7">�ڵ��</font></b></div>
			<div align="center" style="padding-bottom:5;"><textarea NAME="getppwordz" readonly style="width:90%;height:100" class=tline></textarea></div>
		</div>
		</td>
	</tr>
	</table>

	<div><font class=extext>* ���� ���� ��Ī�� Ŭ���ϸ� �����ʿ� �ڵ���� ���Դϴ�. �ڵ���� ����ؼ� ������ �亯�� Ȱ���ϼ���.</div>
    <div style="padding-top:2px">* �ش� �ڵ����� '���θ� �⺻����' ���� �̹� �ԷµǾ� �ִ� �������� �ҷ��ɴϴ�.</div> 
	<div style="padding-top:2px">* ���忡�� �ڵ���� Ȱ���ϸ� ���߿� ������ �����ؾ��ϴ� ���ŷο��� ���� �˴ϴ�.</font></div>

	<TEXTAREA NAME="descant" ROWS="10" COLS="100" style="width:90%;" class=tline><?=$data['descant']?></TEXTAREA>
	</td>
</tr>
<tr>
	<td nowrap>����Ʈ ���</td>
	<td nowrap><span class=noline>
	<input name="best" type="radio" value="Y" <?if ( $data['best'] == "Y" )echo"checked";?>> �߰�
	<input name="best" type="radio" value="N" <?if ( $data['best'] != "Y" )echo"checked";?>> �߰�����&nbsp;&nbsp;&nbsp;&nbsp;</span>
	���� : <input name="bestsort" type="text" size="5" value="<?=$data['bestsort']?>" style="width:30;text-align:center" class=line>
	</td>
</tr>
</table>

<div class="title">FAQ �亯<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<TR>
	<td nowrap>�亯</td>
	<td nowrap><TEXTAREA NAME="answer" ROWS="18" COLS="100" style="width:90%;" required label="�亯" class=tline><?=$data['answer']?></TEXTAREA></td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="<?=$listUrl?>"><img src='../img/btn_list.gif'></a>
</div>

</form>
</div>

<? include "../_footer.php"; ?>