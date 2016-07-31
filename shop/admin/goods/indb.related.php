<?
include "../lib.php";

$now = date('Y-m-d H:i:s');

$data = array();

switch ($_POST['mode']) {
	case 'config':

		require_once("../../lib/qfile.class.php");
		$qfile = new qfile();

		$_POST['cfg_related']['dp_image'] = 1;	// 이미지 출력은 고정

		// 장바구니 아이콘 업로드
		if ($_FILES['cart_image']['error'] === UPLOAD_ERR_OK) {	// UPLOAD_ERR_OK = 0
			$file = $_FILES['cart_image'];
			$_ext = strtolower(array_pop(explode('.',$file['name'])));
			if (strpos('png jpg gif jpeg',$_ext) !== false) {	// 허용 확장자 검사
				if ($file['size'] > 0) {
					if (@move_uploaded_file($file['tmp_name'], '../../data/goods/icon/custom/basket')) {
						echo '
						<script>
							parent.document.getElementById("el-user-cart-icon").src="../../data/goods/icon/custom/basket?'.time().'";
						</script>
						';
					}
				}
			}
		}

		$qfile->open("../../conf/config.related.goods.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfg_related = array( \n");
		foreach ($_POST['cfg_related'] as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		msg('저장되었습니다.');
		exit;
		break;
	case 'register':

		if (is_array($_POST[chk])) { foreach ($_POST[chk] as $r_goodsno) {

			if ($_POST[goodsno] == $r_goodsno) continue;	// 자기 자신을 관련상품으로 추가할 수 없음.

			$query = "
			SELECT
				G.goodsno, G.goodsnm, G.img_s, O.price, G.totstock, G.usestock, G.runout
			FROM ".GD_GOODS." AS G
			INNER JOIN ".GD_GOODS_OPTION." AS O
			ON G.goodsno = O.goodsno AND O.link = 1 and go_is_deleted <> '1'

			WHERE
				G.goodsno = $r_goodsno
			";
			$goods = $db->fetch($query,1);

			$goods['r_type'] = 'single';

			$goods['r_start'] = NULL;
			$goods['r_end'] = NULL;

			$goods['r_regdt'] = $now;

			$data[] = $goods;

		}}

		echo "
		<script>
			parent.$$('input[name=\"chk[]\"]:checked').each(function(el){
				el.writeAttribute('disabled','1')
			});

			parent.parent.nsAdminGoodsForm.relate.add(".gd_json_encode($data).");
		</script>
		";

		break;


	case 'range' :

		if (is_array($_POST[chk])) { foreach ($_POST[chk] as $r_goodsno) {

			$goods['goodsno'] = $r_goodsno;

			if ($_POST['range_type'] != 1) {	// 지속
				$goods['r_start'] = NULL;
				$goods['r_end'] = NULL;
			}
			else {
				$goods['r_start'] = date('Y-m-d', mktime(0,0,0,$_POST['r_start_m'],$_POST['r_start_d'],$_POST['r_start_y']));
				$goods['r_end'] = date('Y-m-d', mktime(0,0,0,$_POST['r_end_m'],$_POST['r_end_d'],$_POST['r_end_y']));
			}

			$data[] = $goods;
		}}

		echo "
		<script>
			parent.parent.nsAdminGoodsForm.relate.set(".gd_json_encode($data).");
			parent.parent.nsAdminForm.dialog.close();
		</script>
		";
		break;
	case 'changetype' :
		if (is_array($_POST[chk])) { foreach ($_POST[chk] as $r_goodsno) {
			$query = " UPDATE ".GD_GOODS_RELATED." SET r_type = IF(r_type = 'single', 'couple', 'single') WHERE r_goodsno = '$r_goodsno' AND goodsno = '$_POST[goodsno]' ";
			$db->query($query);
		}}
		break;
}

?>