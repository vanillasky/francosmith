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

	// 쿼리에 사용할 변수 정의
	$field = " O.sno, O.opt1, O.opt2, O.price, O.stock, G.goodsno, G.goodsnm, G.img_s, P.pchsno, P.comnm, P.phone1, P.phone2"; // 필드
	$table = "gd_goods_option AS O LEFT JOIN gd_goods AS G ON O.goodsno = G.goodsno LEFT JOIN ".GD_PURCHASE." AS P ON O.pchsno = P.pchsno"; // 테이블
	$where = " O.stock <= '".$purchaseSet['popStock']."' and go_is_deleted <> '1'"; // 검색

	if($pchsno) {
		list($thisCode) = $db->fetch("SELECT comcd FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
		$where .= " AND O.pchsno = '$pchsno'";
	}
	if($sword) $where .= " AND $skey LIKE '%$sword%'";
	if(!empty($cate)) {
		$category = array_notnull($cate);
		$category = $category[count($category) - 1];

		/// 카테고리가 있는 경우 대상 테이블 재정의
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
	<td><b>번호</b></td>
	<td><b>상품명</b></td>
	<td><b>옵션1</b></td>
	<td><b>옵션2</b></td>
	<td><b>판매가</b></td>
	<td><b>최근매입가</b></td>
	<td><b>재고량</b></td>
	<td><b>사입처</b></td>
	<td><b>담당자 전화번호</b></td>
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
	<td><?=number_format($data['price'])?> 원</td>
	<td><?=number_format($p_price)?> 원</td>
	<td><?=$data['stock']?></td>
	<td><?=$data['comnm']?></td>
	<td><?=$data['phone1'].(($data['phone2'] && $data['phone2'] != "--") ? " / ".$data['phone2'] : "")?></td>
</tr>
<?
	}
?>
</table>
