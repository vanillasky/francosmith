<?

$location = "QR 코드 관리 > QR 코드 리스트";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_QRCODE."";
$where = array("qr_type='etc'");
$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">QR 코드 리스트<span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=15')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th width=100>번호</th>
	<th>QR 코드 제목</th>
	<th width=100>내용수정</th>
	<th width=100>등록일</th>
	<th width=50>삭제</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<? while ($data=$db->fetch($res)){ ?>
<tr><td height=10 colspan=15></td></tr>
<tr height=25>
	<td align=center style="padding-bottom:9"><font class=ver81><?=$pg->idx--?></td>
	<td class=cellL>
	<a href="qr_edit.php?sno=<?=$data[sno]?>"><?=$data[qr_name]?></a>
	</td>
	<td align=center>
	<a href="qr_edit.php?sno=<?=$data[sno]?>"><img src="../img/i_edit.gif" border=0></a>
	</td>
	<td align=center style="padding-bottom:5"><font class=ver8 color=EB4200><?=substr($data[regdt], 0,10)?></td>
	<td align=center style="padding-bottom:4"><a href="indb.php?mode=del&sno=<?=$data[sno]?>" onclick="return confirm('정말로 삭제하시겠습니까?')"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품페이지와 이벤트 페이지 외에 영역에 사용할 QR코드리스트 입니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>등록한 QR코드를 수정/삭제 할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>