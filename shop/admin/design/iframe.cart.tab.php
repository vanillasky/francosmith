<?
	include "../_header.popup.php";

	if($_POST['action'] == 'ok') {

		require_once("../../lib/qfile.class.php");
		$qfile = new qfile();

		$cfg['cartTabUse']			= isset($_POST['cartTabUse']) ? $_POST['cartTabUse'] : 'n';
		$cfg['cartTabTpl']			= isset($_POST['cartTabTpl']) ? $_POST['cartTabTpl'] : '01';

		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
		$qfile->write("<?\n\n" );
		$qfile->write("\$cfg = array(\n" );

		foreach ( $cfg as $k => $v ){

			if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
			else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
			else $qfile->write("'$k'\t\t\t=> '$v',\n" );
		}

		$qfile->write(");\n\n" );
		$qfile->write("?>" );
		$qfile->close();
		@chMod( $path, 0757 );


		echo "
		<script>
		alert('����Ǿ����ϴ�');
		self.location.replace ('".$_SERVER['PHP_SELF']."');
		</script>
		";
		exit;
	}
?>
<script>


</script>
<form id="frmCartTab" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="action" value="ok">

<div class="title title_top">����īƮ�� ��� ����<span> ����īƮ�� ��뿩�θ� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>����īƮ�� ��� ����</td>
	<td>
		<input type="radio" name="cartTabUse" style="border:0px" <?=$cfg['cartTabUse'] == 'y' ? 'checked' : '' ?> value="y"> ���
		<input type="radio" name="cartTabUse" style="border:0px" <?=$cfg['cartTabUse'] != 'y' ? 'checked' : '' ?> value="n"> ������
	</td>
</tr>
</table>

<br>
<div class="title title_top">����īƮ�� ������Ÿ�� ����<span> ����īƮ�� ������Ÿ�� ������ �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>���ø� ������ ����</td>
	<td>
	<table cellpadding="5">
	<? for ($i=1;$i<=6;$i++) { ?>
	<tr><td class="noline"><input type="radio" name="cartTabTpl" value="<?=sprintf('%02s',$i)?>" <?=$cfg['cartTabTpl'] == sprintf('%02s',$i) ? 'checked' : '' ?>></td><td><img src="../img/cart_tab_preview_<?=sprintf('%02s',$i)?>.gif"></td></tr>
	<? } ?>
	</table>
	</td>
</tr>
</table>

	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>

</form>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� �ϴܿ� '���ú���ǰ' '���ɻ�ǰ' '��ٱ���' �� ��� ��ǰ����� ������ �̵����� �Ѵ��� �����ָ鼭 ������ ������ ������ �ִ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����īƮ���� ��Ʈ�� ȭ���� ������ ���θ� ��� ȭ�鿡 ����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ ���� ���� ���������� ��� ��ǰ����� Ȯ�� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����īƮ�� ��� ������ ��ü ���̾ƿ��� �ϴ� �κ��� ���� ������ ���� ũ�⸸ŭ ������ �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����īƮ���� ���������(�� ��ܺκ��� �׻� ������)�� ����Ͽ� '��ü ���̾ƿ� ���� > �ϴܵ�����' ���� �ϴ� ������ �����Ͽ� �ּ���.</td></tr>
</table>
</div>

<script>
cssRound('MSG01')
table_design_load();
setHeight_ifrmCodi();
</script>