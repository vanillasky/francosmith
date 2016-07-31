<?

$location = "회원관리 > 메일발송내역보기";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_LOG_EMAIL."";

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">메일발송내역보기<span>회원들에게 보낸 이메일내역을 확인할 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=6')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th>번호</th>
	<th>제목</th>
	<th>대상자</th>
	<th>발송일</th>
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
	<td><font class=ver8><?=number_format($data[target])?>명</td>
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
<tr><td><img src="../img/icon_list.gif" align=absmiddle>현재까지 회원들에게 보낸 메일링의 내역을 확인하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>자동메일 발송내역은 남지 않습니다. 수동으로 발송한 내역만 확인하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>제목을 누르면 메일내용을 보실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>