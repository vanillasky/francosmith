<?

$location = "�̺�Ʈ���� > �̺�Ʈ����Ʈ";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_EVENT."";

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">�̺�Ʈ����Ʈ<span>�̺�Ʈ�������� ���� �������ϰ� �̺�Ʈ��ǰ���� �����Ͻ� �� �ֽ��ϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th width=100>��ȣ</th>
	<th>�̺�Ʈ����</th>
	<th width=100>�̸�����</th>
	<th width=100>�������</th>
	<th width=100>�̺�Ʈ������</th>
	<th width=100>�̺�Ʈ������</th>
	<th width=50>����</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<? while ($data=$db->fetch($res)){ ?>
<tr><td height=10 colspan=15></td></tr>
<tr height=25>
	<td align=center style="padding-bottom:9"><font class=ver81><?=$pg->idx--?></td>
	<td class=cellL>
	<a href="register.php?mode=modEvent&sno=<?=$data[sno]?>"><?=$data[subject]?></a>
	</td>
	<td align=center style="padding-bottom:4"><a href="../../goods/goods_event.php?sno=<?=$data[sno]?>" target=_blank><img src="../img/btn_viewbbs.gif" border=0></a></td>
	<td align=center>
	<a href="register.php?mode=modEvent&sno=<?=$data[sno]?>"><img src="../img/i_edit.gif" border=0></a>
	</td>
	<td align=center style="padding-bottom:5"><font class=ver8 color=EB4200><?=$data[sdate]?></td>
	<td align=center style="padding-bottom:5"><font class=ver8 color=EB4200><?=$data[edate]?></td>
	<td align=center style="padding-bottom:4"><a href="indb.php?mode=delEvent&sno=<?=$data[sno]?>" onclick="return confirm('������ �����Ͻðڽ��ϱ�?\n\n�̺�Ʈ���뿡 ���� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�. \n\'�����ΰ��� > webFTP�̹������� > data > editor\'���� �̹���üũ �� ���������ϼ���.')"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�̺�Ʈ������ Ŭ���ϸ� �̺�Ʈ ������ ����</font>�� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�̸����⸦ Ŭ���ϸ� ��â�� �Բ� �̺�Ʈ�������� �̵�</font>�մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<? include "../_footer.php"; ?>
