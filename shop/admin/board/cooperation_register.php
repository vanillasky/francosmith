<?

$location = "���ǰ��� > �������޹���";
include "../_header.php";

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];

$parseUrl = parse_url( $returnUrl );
$listUrl = ( $returnUrl ? $parseUrl[query] : $_SERVER['QUERY_STRING'] );
$listUrl = 'cooperation.php?' . preg_replace( "'(mode|sno)=[^&]*(&|)'is", '', $listUrl );

if (!$_GET[mode]) $_GET[mode] = "register";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_COOPERATION." where sno='" . $_GET['sno'] . "'",1);
	$data['reply'] = htmlspecialchars( $data['reply'] );
}
?>

<div id=goods_form>

<form method=post action="cooperation_indb.php" enctype="multipart/form-data" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name=returnUrl value="<?=$returnUrl?>">

<div class="title title_top">����<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>���Ǻо� ����</td>
	<td nowrap>
	<SELECT NAME="itemcd" required label="���Ǻо�">
	<option value="">�� ���Ǻо߸� �����ϼ���.</option>
	<?foreach ( codeitem('cooperation') as $k => $v ){?>
	<option value='<?=$k?>' <?=( $k == $data['itemcd'] ? 'selected' : '' )?>><?=$v?></option>
	<?}?>
	</SELECT>
	</td>
</tr>
<TR>
	<td nowrap>������</td>
	<td nowrap><font class=ver8><?=$data['regdt']?></td>
</tr>
<TR>
	<td nowrap>����</td>
	<td nowrap><input name="name" type="text" size="20"  value="<?=$data['name']?>" required label="����" class=line></td>
</tr>
<TR>
	<td nowrap>E-mail</td>
	<td nowrap>
	<input name="email" type="text" size="50"  value="<?=$data['email']?>" class=line>
	<a href="mailto:<?=$data['email']?>"><font class=ver8><?=$data['email']?></a>
	</td>
</tr>
<TR>
	<td nowrap>��������</td>
	<td nowrap><?=$data['title']?></td>
</tr>
<TR>
	<td nowrap>���ǳ���</td>
	<td nowrap><?=nl2br( $data['content'] )?></td>
</tr>
</table>

<div class="title">�亯<span></span></div>
<table class=tb>
<col class=cellC><col class=cellL>

<tr>
	<td nowrap>�亯����</td>
	<td nowrap><textarea name="reply" cols="100" rows="12" style="width:90%;" class=tline><?=$data['reply']?></textarea></td>
</tr>
<TR>
	<td nowrap>�亯��</td>
	<td nowrap><input name="replydt" type="text" size="20" value="<?=$data['replydt']?>" onclick="calendar(event)" class=line></td>
</tr>
<TR>
	<td nowrap>��������</td>
	<td nowrap>
	<div style="padding-top:10px"></div>
	<font class=ver8>
	<b><?echo( str_replace( ".", "", $data[maildt] ) > 0 ? '<span class="txt_Color2">' . $data[maildt] . '</span>' : '<span class="txt_Color3">�̹߼�</span>' )?></b>
	<?=str_repeat('&nbsp;',2);?>
	<img onclick="document.ifrmHidden.location.href='cooperation_indb.php?mode=mailsend&sno=<?=$data[sno]?>';" alt="�������� �����ϱ�" src="../img/btn_mailing.gif" align="absmiddle" style="cursor:pointer;">
	<div style="padding-top:10px"></div>
	<span class=noline><input type="checkbox" name="mailyn" value="Y"> �亯�� ����� �� ������ �亯������ ���ÿ� �����Ͻðڽ��ϱ�?</span><br><br>
	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_<?=$_GET[mode]?>.gif">
<a href="<?=$listUrl?>"><img src='../img/btn_list.gif'></a>
</div>

</form>
</div>

<? include "../_footer.php"; ?>