<?
/*********************************************************
* ���ϸ�     :  orderScrap.php
* ���α׷��� :  �ֹ�����
* �ۼ���     :  dn
* ������     :  2012.05.12
**********************************************************/
$location = "���� > �ֹ�����";
include "../_header.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.");
	go("./setting.php");
}

$sAPI = new sAPI();

$code_arr = array();
$code_arr['grp_cd'] = 'MALL_CD';

$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

$scrap_data = $sAPI->getMallLoginId();

?>
<script type="text/javascript">
var popup_no = 0;

function scrapOrderPop() {

	var chk = document.getElementsByName("chk[]");
	var bool_chk = false;
	
	for (var i=0; i< chk.length; i++) {	
		if(chk[i].checked == true) {
			bool_chk = true;
		}
	}	

	if(bool_chk == false) {
		alert('�ֹ������� ���̵� ������ �ּ���');
		return;
	}
	
	popup_return('_blank.php', 'scrap_pop' + popup_no, 800, 700, '', '', 1);
	var frm = document.frmOrderScrap;
		
	frm.target = 'scrap_pop' + popup_no;
	frm.action = 'orderScrapPop.php';
	frm.submit();

	popup_no ++;
}
</script>

<div class="title title_top">���� �ֹ� ���� <span>���Ͽ� ��ũ�Ǿ� �ִ� ��ǰ�� �ֹ��� �ϰ� ������ �� �ֽ��ϴ�.</span><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<form name="frmOrderScrap" method="post" action="">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=4></td></tr>
<tr class="rndbg">
	<th width="50" align="center">����</th>
	<th width="30%" align="center">����</th>
	<th width="30%" align="center">�α��� ID</th>
	<th width="30%" align="center">���� �ֹ� ������</th>
</tr>
<tr><td class="rnd" colspan="4"></td></tr>
<tr><td height=4 colspan=4></td></tr>
<?
if(!empty($scrap_data)) {
	foreach($scrap_data as $row_scrap) {
		if($row_scrap['mall_cd'] == 'mall0005') continue;
?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25>
	<td width="50" align="center" class="noline">
		<input type="checkbox" name="chk[]" value="<?=$row_scrap['minfo_idx']?>" />
	</td>
	<td width="30%" align="center"><?=$mall_cd[$row_scrap['mall_cd']]?></td>
	<td width="30%" align="center"><?=$row_scrap['mall_login_id']?></td>
	<td width="30%" align="center"><?=$row_scrap['last_order_date']?></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<?
	}
}
else { ?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25>
	<td colspan=4 align="center">�⺻�������� ������ ����� �ּ���</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<? } ?>
</table>
<div class="button">
	<input type="image" src="../img/btn_orderscrap.gif" alt="�ֹ�����" onclick="javascript:scrapOrderPop();return false;" />
</div>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
<a href="../selly/mallList.php"><font color=white><u>[���ϰ���]</u></font></a>���� ��ϵ� ������ �ֹ��� �����Ͻ� �� �ֽ��ϴ�.<br/><br/><br/>

�ֹ������� e������ ��ϵ� �ֹ��� �ƴѰ�� �ű��ֹ�(����Ȯ��)�� �����Ǿ� e������ ��ϵ˴ϴ�.<br/>
e������ ��ϵ� �ֹ��� ��� ���/��ǰ/��ȯ��û ������ ����Ȯ��, ����Ϸ� ������ �����մϴ�.<br/>
�ֹ������� ���Ͽ� ���� ������ ��� �ش� ���·� �������� �ʽ��ϴ�.<br/>
������ �ֹ��� <a href="../selly/marketOrderList.php"><font color=white><u>[�����ֹ�����]</u></font></a>���� ���¸� ó���Ͻ� �� �ֽ��ϴ�.<br/>
������ ������ ������ �ٸ� ��� <a href="../selly/mallList.php"><font color=white><u>[���ϰ���]</u></font></a>���� ���������� �ϼž� �ֹ������� �˴ϴ�.<br/>
���������� �ֹ������� ���ڸ� ���� �ֹ� �����Ͽ��� Ȯ���Ͻ� �� �ֽ��ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>