<?

$location = "이벤트관리 > 이벤트리스트";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_EVENT."";

$pg = new Page($_GET[page]);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">이벤트리스트<span>이벤트페이지를 직접 디자인하고 이벤트상품들을 선정하실 수 있습니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th width=100>번호</th>
	<th>이벤트제목</th>
	<th width=100>미리보기</th>
	<th width=100>내용수정</th>
	<th width=100>이벤트시작일</th>
	<th width=100>이벤트만료일</th>
	<th width=50>삭제</th>
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
	<td align=center style="padding-bottom:4"><a href="indb.php?mode=delEvent&sno=<?=$data[sno]?>" onclick="return confirm('정말로 삭제하시겠습니까?\n\n이벤트내용에 쓰인 이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다. \n\'디자인관리 > webFTP이미지관리 > data > editor\'에서 이미지체크 후 삭제관리하세요.')"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>

<div style="padding-top:15px"></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>이벤트제목을 클릭하면 이벤트 내용을 수정</font>할 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>미리보기를 클릭하면 새창과 함께 이벤트페이지로 이동</font>합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>




<? include "../_footer.php"; ?>
