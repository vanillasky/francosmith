<?

$location = "ȸ������ > ���Ϲ߼۳�������";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_LOG_EMAIL."";

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">���Ϲ߼۳�������<span>ȸ���鿡�� ���� �̸��ϳ����� Ȯ���� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=6')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th>��ȣ</th>
	<th>����</th>
	<th>�����</th>
	<th>�߼���</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<col align=center>
<col class=cellL>
<col align=center span=2>
<tr><td height=4 colspan=10></td></tr>
<? while ($data=$db->fetch($res)){ ?>
<tr height=25>
	<td><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="javascript:popup('popup.email.php?sno=<?=$data[sno]?>',850,600)"><font class=ver8><?=$data[subject]?></font></a></td>
	<td><font class=ver8><?=number_format($data[target])?>��</td>
	<td><font class=ver8><?=$data[regdt]?></td>
</tr>
<tr><td height=4 colspan=10></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>


<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������� ȸ���鿡�� ���� ���ϸ��� ������ Ȯ���ϼ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�ڵ����� �߼۳����� ���� �ʽ��ϴ�. �������� �߼��� ������ Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������ ������ ���ϳ����� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>