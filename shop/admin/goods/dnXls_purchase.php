<?
	set_time_limit(0);
	include '../lib.php';
	@include "../../conf/config.purchase.php";

	header('Content-Type: application/vnd.ms-excel; charset=euc-kr');
	header('Content-Disposition: attachment; filename=GDPurchaseList_'.date('YmdHi').'.xls');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0,pre-check=0');
	header('Pragma: public');

	$pchsno			= isset($_GET['pchsno'])		? $_GET['pchsno']		: "";
	$cate			= isset($_GET['cate'])			? $_GET['cate']			: array();
	$skey			= isset($_GET['skey'])			? $_GET['skey']			: "";
	$sword			= isset($_GET['sword'])			? $_GET['sword']		: "";
	$sort			= isset($_GET['sort'])			? $_GET['sort']			: "O.stock ASC";

	// ������ ����� ���� ����
	$field = " O.sno, O.opt1, O.opt2, O.price, O.stock, G.goodsno, G.goodsnm, G.img_s, P.pchsno, P.comnm, P.phone1, P.phone2"; // �ʵ�
	$table = "gd_goods_option AS O LEFT JOIN gd_goods AS G ON O.goodsno = G.goodsno LEFT JOIN ".GD_PURCHASE." AS P ON O.pchsno = P.pchsno"; // ���̺�
	$where = " O.stock <= '".$purchaseSet['popStock']."' and go_is_deleted <> '1'"; // �˻�

	if($pchsno) {
		list($thisCode) = $db->fetch("SELECT comcd FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
		$where .= " AND O.pchsno = '$pchsno'";
	}
	if($sword) $where .= " AND $skey LIKE '%$sword%'";
	if(!empty($cate)) {
		$category = array_notnull($cate);
		$category = $category[count($category) - 1];

		/// ī�װ��� �ִ� ��� ��� ���̺� ������
		if($category) {
			$table .= " LEFT JOIN ".GD_GOODS_LINK." AS L ON O.goodsno = L.goodsno";
			$where .= sprintf(" AND L.category like '%s%%'", $category);
			if($group) $group .= ",";
			$group .= " O.sno ";
		}
	}

	$sql = "SELECT ".$field." FROM ".$table." WHERE ".$where.(($group) ? " GROUP BY ".$group : "").(($sort) ? " ORDER BY ".$sort : "");
?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>td {mso-number-format:"@"}</style>
<table border="1">
<tr bgcolor="#F7F7F7" align="center">
	<td><b>��ȣ</b></td>
	<td><b>��ǰ��</b></td>
	<td><b>�ɼ�1</b></td>
	<td><b>�ɼ�2</b></td>
	<td><b>�ǸŰ�</b></td>
	<td><b>�ֱٸ��԰�</b></td>
	<td><b>���</b></td>
	<td><b>����ó</b></td>
	<td><b>����� ��ȭ��ȣ</b></td>
</tr>
<?
	$rs = $db->query($sql);
	for($i = 0; $data = $db->fetch($rs); $i++) {
		list($p_price) = $db->fetch("SELECT p_price FROM ".GD_PURCHASE_GOODS." WHERE pchsno = '".$data['pchsno']."' AND goodsno = '".$data['goodsno']."' AND opt1 = '".$data['opt1']."' AND opt2 = '".$data['opt2']."' ORDER BY pchsdt DESC LIMIT 1");
?>
<tr align="center">
	<td><?=$i + 1?></td>
	<td align="left"><?=$data['goodsnm']?></td>
	<td><?=$data['opt1']?></td>
	<td><?=$data['opt2']?></td>
	<td><?=number_format($data['price'])?> ��</td>
	<td><?=number_format($p_price)?> ��</td>
	<td><?=$data['stock']?></td>
	<td><?=$data['comnm']?></td>
	<td><?=$data['phone1'].(($data['phone2'] && $data['phone2'] != "--") ? " / ".$data['phone2'] : "")?></td>
</tr>
<?
	}
?>
</table>
