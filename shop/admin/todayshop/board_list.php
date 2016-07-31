<?

$location = "투데이샵 > 게시판리스트";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_BOARD."";

$pg = new Page($_GET[page],10);
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);

### 관리자 아이콘
if ( file_exists( '../../data/skin/' . $cfg['tplSkin'] . '/admin.gif' ) ) $adminicon = 'admin.gif';

?>

<div class="title title_top">게시판리스트<span>생성된 게시판을 수정하고 관리합니다 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=19')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>


<div style="padding-top:5px"></div>


<div class=pageInfo><font class=ver8>총 <b><?=$pg->recode[total]?></b> 게시판, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr class=rndbg>
	<th>번호</th>
	<th>아이디</th>
	<th>게시판이름</th>
	<th>게시글수</th>
	<th>스킨타입</th>
	<th>사용자화면</th>
	<!--<th>정리</th>-->
	<th>수정</th>
	<th>삭제</th>
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

	# 나중에 삭제를 할것
	$strSQL ="ALTER TABLE ".GD_BD_.$data['id']." CHANGE `category` `category` VARCHAR( 50 ) NULL;";
	$db->query($strSQL);
?>
<tr height=30>
	<td><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="board_register.php?mode=modify&id=<?=$data[id]?>"><font class=ver8 color=0074BA><b><?=$data[id]?></b></font></a></td>
	<td><?=$bdName?></td>
	<td><font class=ver8><?=$cnt-1?></td>
	<td><font class=ver8><?=$bdSkin?></font></td>
	<td><a href="../../todayshop/board/list.php?id=<?=$data[id]?>" target=_blank><img src="../img/btn_viewbbs.gif" border=0></a></td>
	<!--<td><a href="indb.board.php?mode=inf&id=<?=$data[id]?>" target=ifrmHidden>[정리]</a></td>-->
	<td><a href="board_register.php?mode=modify&id=<?=$data[id]?>"><img src="../img/i_edit.gif"></a></td>
	<td><?if($data[id] != "notice"){?><a href="indb.board.php?mode=drop&id=<?=$data[id]?>" onclick="return confirm('삭제된 게시판은 복구가 불가능합니다. 정말로 삭제하시겠습니까?\n\n게시글에 업로드된 이미지는 다른 곳에서도 사용하고 있을 수 있으므로 자동 삭제되지 않습니다. \n\'디자인관리 > webFTP이미지관리 > data > editor\'에서 이미지체크 후 삭제관리하세요.')" target=ifrmHidden><img src="../img/i_del.gif"></a><?}?></td>
</tr>
<tr><td height=4 colspan=9></td></tr>
<tr><td colspan=9 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div id=MSG01>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<!--<tr><td><img src="../img/icon_list.gif" align=absmiddle>각 게시판관리는 <font color=0074BA>사용자페이지에서 직접 관리자가 관리</font>할 수 있습니다.</td></tr>-->
<tr><td><img src="../img/icon_list.gif" align=absmiddle>글올리기, 질문의 답변, 수정, 삭제 등은 사용자화면 <img src="../img/btn_viewbbs.gif" align=absmiddle> 버튼을 눌러 사용자페이지의 게시판에서 직접 관리하세요.</td></tr>

</table>

</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:15px"></div>

<form method="post" action="../board/indb.board.php?mode=adminicon" name="fmAdminicon" enctype="multipart/form-data">
<div class="title title_top">관리자 아이콘 설정 <span>아이디 대신 출력할 관리자 아이콘을 설정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>관리자 아이콘</td>
	<td>
	<input type="file" name="icon_up" size="50"><input type="hidden" name="icon" value="<?=$adminicon?>">
	<a href="javascript:webftpinfo( '<?=( $adminicon != '' ? '/data/skin/' . $cfg['tplSkin'] . '/' . $adminicon : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
	<? if ( $adminicon != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="icon_del" value="Y">삭제</span><? } ?>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_save3.gif"></div>
</form>

<div style="padding-top:20px"></div>

<div id=MSG02>
<table cellpadding=2 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>상품문의, 상품후기, 생성한 게시판에 관리자 아이디(이름) 대신 출력할 관리자 아이콘을 설정하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>관리자 아이콘의 파일명은 반드시 admin.gif 로 업로드하세요.</td></tr>
</table>

</div>
<script>cssRound('MSG02')</script>

<? include "../_footer.php"; ?>