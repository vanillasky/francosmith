<?
/*********************************************************
* ���ϸ�     :  popup_list.php
* ���α׷��� :	����ϼ� �˾�����Ʈ
* �ۼ���     :  dn
* ������     :  2012.05.08
**********************************************************/	

$location = "����ϼ� > �˾�â ����";
include "../_header.php";
include "../../conf/design.main.php";

$select_popup_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_POPUP.' WHERE 1=1 ORDER BY mpopup_no DESC');
$res_popup = $db->_select($select_popup_query);

?>
<script type="text/javascript">
function delPopup(mpopup_no) {
	if(confirm('���� ���� �Ͻðڽ��ϱ�?')) {
		var frm = document.frm_del;
		$('mpopup_no').value = mpopup_no;
		frm.submit();
	}
}
</script>
<div class="title title_top">�˾�â ���� </div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="5%" align="center" />
<col width="25%" />
<col width="30%" />
<col width="10%" align="center" />
<col width="10%" align="center" />
<col width="10%" align="center" />
<col width="5%" align="center" />
<col width="5%" align="center" />
<tr><td class="rnd" colspan="8"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�˾�����</th>
	<th>�˾��̹���</th>
	<th>��뿩��</th>
	<th>������</th>
	<th>������</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="8"></td></tr>
<? 
if(is_array($res_popup) && !empty($res_popup)) {

	$no = 0;
	foreach($res_popup as $row_popup) { 
		$no++;	
?>
<tr><td height=4 colspan=8></td></tr>
<tr height=25>
	<td><?=$no?></td>
	<td><?=$row_popup['popup_title']?></td>
	<td>
	<? if($row_popup['popup_img']) { ?>
		<img src="../../data/m/upload_img/<?=$row_popup['popup_img']?>" width=100px height=100px/>
	<? } else { ?>
		�˾� �̹����� ����� �ּ���
	<? } ?>
	</td>
	<td><img src="../img/icn_<?=$row_popup['open']?>.gif"></td>
	<td><?=substr($row_popup['start_date'], 0, 10)?></td>
	<td><?=substr($row_popup['end_date'], 0, 10)?></td>
	<td><a href="popup_register.php?mpopup_no=<?=$row_popup['mpopup_no']?>"><img src="../img/i_edit.gif" align="absmiddle" /></a></td>
	<td><a href="javascript:delPopup('<?=$row_popup['mpopup_no']?>');"><img src="../img/i_del.gif" align="absmiddle" /></a></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=8 class=rndline></td></tr>
<?	} 
}
else { ?>
	
<tr><td height=4 colspan="6"></td></tr>
<tr height=25>
	<td colspan="8">��ϵ� �˾��� �����ϴ�</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan="8" class=rndline></td></tr>
<? } ?>
</table>
<form name="frm_del" action="indb.php" method="post">
	<input type="hidden" name="mode" value="del_popup" />
	<input type="hidden" name="mpopup_no" id="mpopup_no" />
</form>

<? include "../_footer.php"; ?>