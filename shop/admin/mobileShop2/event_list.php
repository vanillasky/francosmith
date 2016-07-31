<?
/*********************************************************
* 파일명     :  event_list.php
* 프로그램명 :	모바일샵 이벤트리스트
* 작성자     :  dn
* 생성일     :  2012.05.11
**********************************************************/	

$location = "모바일샵 > 이벤트 관리";
include "../_header.php";
include "../../conf/design.main.php";

$select_event_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_EVENT.' WHERE 1=1 ORDER BY mevent_no DESC');
$res_event = $db->_select($select_event_query);
?>
<script type="text/javascript">
function delEvent(mevent_no) {
	if(confirm('정말 삭제 하시겠습니까?')) {
		var frm = document.frm_del;
		$('mevent_no').value = mevent_no;
		frm.submit();
	}
}
</script>
<div class="title title_top">이벤트 관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=11')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="10%" align="center" />
<col width="40%" />
<col width="10%" align="center" />
<col width="10%" align="center" />
<col width="10%" align="center" />
<col width="10%" align="center" />
<tr><td class="rnd" colspan="6"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>이벤트제목</th>
	<th>미리보기</th>
	<th>이벤트시작일</th>
	<th>이벤트만료일</th>
	<th>삭제</th>
</tr>
<tr><td class="rnd" colspan="6"></td></tr>
<? 
if(is_array($res_event) && !empty($res_event)) {
	
	$no = 0;
	foreach($res_event as $row_event) { 
		$no++;	
?>
<tr><td height=4 colspan="6"></td></tr>
<tr height=25>
	<td><?=$no?></td>
	<td><a href="event_register.php?mevent_no=<?=$row_event['mevent_no']?>"><?=$row_event['event_title']?></a></td>
	<td><a href="../../../m/goods/event.php?mevent_no=<?=$row_event['mevent_no']?>" target=_blank><img src="../img/btn_viewbbs.gif" border=0></a></td>
	<td><?=substr($row_event['start_date'], 0, 10)?></td>
	<td><?=substr($row_event['end_date'], 0, 10)?></td>
	<td><a href="javascript:delEvent('<?=$row_event['mevent_no']?>');"><img src="../img/i_del.gif" align="absmiddle" /></a></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan="6" class=rndline></td></tr>
<?	} 
}
else { ?>
	
<tr><td height=4 colspan="6"></td></tr>
<tr height=25>
	<td colspan="6">등록된 이벤트가 없습니다</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan="6" class=rndline></td></tr>
<? } ?>
</table>
<form name="frm_del" action="indb.php" method="post">
	<input type="hidden" name="mode" value="del_event" />
	<input type="hidden" name="mevent_no" id="mevent_no" />
</form>

<? include "../_footer.php"; ?>