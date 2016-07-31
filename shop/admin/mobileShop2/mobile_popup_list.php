<?
/*********************************************************
* 파일명     :  popup_list.php
* 프로그램명 :	모바일샵 팝업리스트
* 작성자     :  kth.
* 생성일     :  2013.05.09
**********************************************************/

$location = "모바일샵 > 모바일샵 팝업창 관리";
include "../_header.php";

$checked['open'][$_GET['open']]	= "checked";

$sub_query = "";
if ($_GET[sword]) $sub_query .= " and popup_title like '%$_GET[sword]%'";
if ($_GET[open] != "") $sub_query .= " and open=".$_GET[open];

$select_popup_query = $db->_query_print('SELECT * FROM '.GD_MOBILEV2_POPUP.' WHERE mpopup_no > 0  '.$sub_query.' ORDER BY mpopup_no DESC');
$res_popup = $db->_select($select_popup_query);

?>
<script type="text/javascript">

function open_change(mpopup_no, before, after) {

	var frm = document.frm_open;

	if(before == '0' && after == '1') {
		var open_cnt = frm.open_cnt.value;
		var obj = eval("document.getElementById('sel_open_"+mpopup_no+"')");

		if(open_cnt > 0) {
			alert("출력중인 팝업창이 존재합니다. \r\n먼저, 현재 출력중인 팝업창을 미출력으로 변경해 주세요.");
			obj.value = before;
			return false;
		}
	}

	$('mpopup_no').value = mpopup_no;
	$('change_open').value = after;
	frm.submit();
}

function delPopup(mpopup_no) {
	if(confirm('삭제된 파일은 복구되지 않습니다.\r\n정말 삭제 하시겠습니까?')) {
		var frm = document.frm_del;
		$('mpopup_no1').value = mpopup_no;
		frm.submit();
	}
}
</script>

<form name=frmSearch>

<div class="title title_top">모바일샵 팝업창 관리 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=14')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class="tb">
<col class="cellC" style="width:150px"><col class="cellL">
<tr>
	<td>팝업제목</td>
	<td>
		<input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line style="width:500px;">
	</td>
</tr>
<tr>
	<td>출력여부</td>
	<td class="noline">
		<label class="noline"><input type="radio" name="open" value="" <?=$checked['open']['']?> />전체 </label>
		<label class="noline"><input type="radio" name="open" value="1" <?=$checked['open']['1']?> />출력 </label>
		<label class="noline"><input type="radio" name="open" value="0" <?=$checked['open']['0']?> />미출력 </label>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

</form>

<div style="height:20px;"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="5%" align="center" />
<col width="40%" />
<col width="15%" align="center" />
<col width="25%" align="center" />
<col width="5%" align="center" />
<col width="5%" align="center" />
<col width="5%" align="center" />
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th>번호</th>
	<th>팝업제목</th>
	<th>출력여부</th>
	<th>출력기간</th>
	<th>보기</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>
<?
if(is_array($res_popup) && !empty($res_popup)) {

	$no = 0;
	$cnt = 0;
	foreach($res_popup as $row_popup) {
		$no++;
		$selected = array();
		$selected[$row_popup['open']] = "selected";
		if($row_popup['open'] == '1')	$cnt++;
?>
<tr><td height=4 colspan=8></td></tr>
<tr height=25>
	<td><?=$no?></td>
	<td align="center"><?=$row_popup['popup_title']?></td>
	<td>
		<select name="sel_open_<?=$row_popup['mpopup_no']?>" id="sel_open_<?=$row_popup['mpopup_no']?>" onChange="open_change('<?=$row_popup['mpopup_no']?>','<?=$row_popup[open]?>',this.value)">
			<option value="1" style="color:blue;" <?=$selected[1]?>>출력</option>
			<option value="0"  style="color:red;" <?=$selected[0]?>>미출력</option>
		</select>
	</td>
	<td>
	<?
		$row_popup['start_time'] = (strlen($row_popup['start_time']) == 1 && !is_null($row_popup['start_time'])) ? "0".$row_popup['start_time'] : $row_popup['start_time'];
		$row_popup['end_time'] = (strlen($row_popup['end_time']) == 1 && !is_null($row_popup['end_time'])) ? "0".$row_popup['end_time'] : $row_popup['end_time'];
		if($row_popup['open_type'] == '0')	echo "계속";
		else echo $row_popup['start_date']." ".$row_popup['start_time'].":00 ~ ".$row_popup['end_date']." ".$row_popup['end_time'].":59";
	?>
	<td><a href="javascript:popup2('./mobile_popup_view.php?mpopup_no=<?=$row_popup['mpopup_no']?>','360', '400', 'yes')"><img src="../img/i_view_popup.gif"></a></td>
	<td><a href="mobile_popup_register.php?mpopup_no=<?=$row_popup['mpopup_no']?>"><img src="../img/i_edit.gif" align="absmiddle" /></a></td>
	<td><a href="javascript:delPopup('<?=$row_popup['mpopup_no']?>');"><img src="../img/i_del.gif" align="absmiddle" /></a></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=8 class=rndline></td></tr>
<?		}
	} else {
?>
<tr><td height=4 colspan="6"></td></tr>
<tr height=25>
	<td colspan="8">등록된 팝업이 없습니다</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan="8" class=rndline></td></tr>
<? } ?>
</table>

<table width="100%">
<tr><td height=10></td></tr>
<tr>
	<td align=center><a href="mobile_popup_register.php"><img src="../img/btn_popup_make.gif"><a/></td>
</tr>
</table>


<div style="padding-top:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'팝업창만들기'를 클릭하면 팝업창을 새로 만들수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />'화면보기'를 클릭하면 팝업창 화면을 볼 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />모바일샵 팝업창은 중복 출력이 되지 않고 1개만 출력이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />팝업창마다 노출출력기간을 설정 할 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<form name="frm_open" action="indb.php" method="post">
	<input type="hidden" name="open_cnt" value="<?=$cnt?>" />
	<input type="hidden" name="mode" value="open_popup" />
	<input type="hidden" name="mpopup_no" id="mpopup_no" />
	<input type="hidden" name="change_open" id="change_open" />
</form>

<form name="frm_del" action="indb.php" method="post">
	<input type="hidden" name="mode" value="del_popup" />
	<input type="hidden" name="mpopup_no" id="mpopup_no1" />
</form>

<? include "../_footer.php"; ?>