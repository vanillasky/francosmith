<?

$location = "�Խ��ǰ��� > �Խ��Ǹ���Ʈ";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_BOARD."";

$pg = new Page($_GET[page],10);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

### ������ ������
if ( file_exists( '../../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';

?>

<div class="title title_top">�Խ��Ǹ���Ʈ<span>������ �Խ����� �����ϰ� �����մϴ� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>


<div style="padding-top:5px"></div>


<div class=pageInfo><font class=ver8>�� <b><?=$pg->recode[total]?></b> �Խ���, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th>��ȣ</th>
	<th>���̵�</th>
	<th>�Խ����̸�</th>
	<th>�Խñۼ�</th>
	<th>��ŲŸ��</th>
	<th>�����ȭ��</th>
	<!--<th>����</th>-->
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=8></td></tr>
<tr><td height=3 colspan=8></td></tr>
<col width=80 align=center>
<col style="padding-left:10px">
<col width=90 align=center span=6>
<?
while ($data=$db->fetch($res)){
	list ($cnt) = $db->fetch("select count(*) from ".GD_BD_.$data['id']);
	include "../../conf/bd_$data[id].php";
?>
<tr height=30>
	<td><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="register.php?mode=modify&id=<?=$data[id]?>"><font class=ver8 color=0074BA><b><?=$data[id]?></b></font></a></td>
	<td><?=$bdName?></td>
	<td><font class=ver8><?=$cnt-1?></td>
	<td><font class=ver8><?=$bdSkin?></font></td>
	<td><a href="../../board/list.php?id=<?=$data[id]?>" target=_blank><img src="../img/btn_viewbbs.gif" border=0></a></td>
	<!--<td><a href="indb.php?mode=inf&id=<?=$data[id]?>" target=ifrmHidden>[����]</a></td>-->
	<td><a href="register.php?mode=modify&id=<?=$data[id]?>"><img src="../img/i_edit.gif"></a></td>
	<td><?if($data[id] != "notice"){?><a href="indb.php?mode=drop&id=<?=$data[id]?>" onclick="return confirm('������ �Խ����� ������ �Ұ����մϴ�. ������ �����Ͻðڽ��ϱ�?\n\n�Խñۿ� ���ε�� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�. \n\'�����ΰ��� > webFTP�̹������� > data > editor\'���� �̹���üũ �� ���������ϼ���.')" target=ifrmHidden><img src="../img/i_del.gif"></a><?}?></td>
</tr>
<tr><td height=4 colspan=9></td></tr>
<tr><td colspan=9 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<!--<tr><td><img src="../img/icon_list.gif" align=absmiddle>�� �Խ��ǰ����� <font color=0074BA>��������������� ���� �����ڰ� ����</font>�� �� �ֽ��ϴ�.</td></tr>-->
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�ۿø���, ������ �亯, �Խñ��� ����, ���� ���� [�Խñ� ����]���� �������� �����ϼ���.<br /><img src="../img/btn_viewbbs.gif" align=absmiddle> ��ư�� ���� ������������� �Խ��ǿ��� ���� ������ �����մϴ�.</td></tr>

</table>

</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:15px"></div>

<form method="post" action="../board/indb.php?mode=adminicon" name="fmAdminicon" enctype="multipart/form-data">
<div class="title title_top">������ ������ ���� <span>���̵� ��� ����� ������ �������� �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>������ ������</td>
	<td>
	<input type="file" name="icon_up" size="50"><input type="hidden" name="icon" value="<?=$adminicon?>">
	<a href="javascript:webftpinfo( '<?=( $adminicon != '' ? '/data/skin/' . $cfg['tplSkin'] . '/' . $adminicon : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
	<? if ( $adminicon != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="icon_del" value="Y">����</span><? } ?>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_save3.gif"></div>
</form>

<div style="padding-top:20px"></div>

<div id=MSG02>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>��ǰ����, ��ǰ�ı�, ������ �Խ��ǿ� ������ ���̵�(�̸�) ��� ����� ������ �������� �����ϼ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>������ �������� ���ϸ��� �ݵ�� admin.gif �� ���ε��ϼ���.</td></tr>
</table>

</div>
<script>cssRound('MSG02')</script>

<? include "../_footer.php"; ?>