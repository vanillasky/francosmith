<?
include "../_header.popup.php";
include "../../lib/godopost.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$godopost = new godopost();

$ordno=$_REQUEST['ordno'];
$sel_sno = (array)$_POST['sno'];
$paytype=$_POST['paytype'];

// �����ȣ �Ҵ� ó���κ�
if(count($sel_sno)) {
	$needs_count = count($sel_sno);
	if($paytype=='prepay') {
		$result = $godopost->get_regino($needs_count,0);
		$result = $result['prepay'];
	} else {
		$result = $godopost->get_regino(0,$needs_count);
		$result = $result['collect'];
	}
	
	
	for($i=0;$i<$needs_count;$i++) {
		$query = $db->_query_print(
			'update gd_order_item set dvno=[i] , dvcode=[s] where sno=[s]'
			,100,$result[$i],$sel_sno[$i]
		);
		$db->query($query);
	}

}

// �ֹ���ȣ�� �ֹ���ǰ��� �ڱ�� ����
$query = $db->_query_print("select * from gd_order_item where ordno=[s]",$ordno);
$result = $db->_select($query);

// ��۾�ü ����
$query = "select deliveryno,deliverycomp from gd_list_delivery where useyn='y' order by deliverycomp asc";
$delivery_list = $db->_select($query);
?>
<script type="text/javascript">

</script>

<div class="title title_top">��ǰ�� ��ü���ù� �����ȣ �Ҵ�</div>

<form name="fmList" action="popup.godopost.itemassign.php" method="post">
<input type="hidden" name="ordno" value="<?=$ordno?>">
<table cellpadding="3" cellspacing="0" border="1" style="border-collapse:collapse" bordercolor="#cccccc" width="100%">
<tr bgcolor="#2E2B29">
	<th><font color="white">����</font></th>
	<th><font color="white">��ǰ��</font></th>
	<th><font color="white">����</font></th>
	<th><font color="white">ó������</font></th>
	<th><font color="white">�ù�����</font></th>
</tr>
<col align="center" width="40">
<col align="center">
<col align="center" width="50">
<col align="center" width="70">
<col align="center" width="200">

<? foreach($result as $k=>$data): ?>
<tr>	
	<td class="noline"><input type="checkbox" name="sno[]" value="<?=$data['sno']?>"></td>
	<td><?=$data['goodsnm']?></td>
	<td><?=$data['ea']?></td>
	<td><?=$r_istep[$data['istep']]?></td>
	<td>
		<? foreach($delivery_list as $each_delivery): ?>
			<? if($each_delivery['deliveryno']==$data['dvno']):?>
				<?=$each_delivery['deliverycomp']?>
			<? endif;?>
		<? endforeach; ?>
		<?=$data['dvcode']?>	
	</td>
</tr>
<? endforeach;?>
</table>

<div style="padding:20px 0 0 12px" >
<input type="radio" name="paytype" value="prepay" style="border:0px" checked>���� �����ȣ�� �Ҵ�
<input type="radio" name="paytype" value="collect" style="border:0px">���� �����ȣ�� �Ҵ�<br>
<input type="submit" value=" ������ ��ǰ�� ���ο� ��ü���ù� �����ȣ �Ҵ��ϱ� ">
</div>
</form>
