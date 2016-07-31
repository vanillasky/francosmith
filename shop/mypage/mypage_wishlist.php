<?

include "../_header.php"; chkMember();
include "../lib/page.class.php";

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

if ($mode){
	$opt = @explode("|",implode("|",$_POST[opt]));
	$addopt = @implode("|",$_POST[addopt]);
	$addopt_inputable = @implode("|",$_POST[_addopt_inputable]);
}

switch ($mode){

	case "cart":
		chkOpenYn($_POST[goodsno],"D",-1);	//진열여부 체크
		$orderitem_mode = "cart";
		include "../lib/cart.class.php";
		$cart = new Cart;
		foreach ($_POST[sno] as $v){
			// 상품별 최소 구매수량 및 묶음 주문단위를 얻기 위해 상품 정보를 조회한다.
			list($min_ea, $sales_unit) = $db->fetch("select min_ea, sales_unit from ".GD_GOODS." where goodsno = '".$_POST[goodsno][$v]."'");

			$ea = 1;

			if ($min_ea < $sales_unit) {
				$min_ea = $sales_unit;
			}

			if ($ea < $min_ea) {
				$ea = $min_ea;
			}
			else {
				$ea = 1;
			}

			if (($remainder = $ea % $sales_unit) > 0) {
				$ea = $ea - $remainder;
			}

			$cart->addCart($_POST[goodsno][$v],array_notnull($_POST[opt][$v]),$_POST[addopt][$v],$_POST[addopt_inputable][$v],$ea,$_POST[goodsCoupon][$v]);
		}

		// ace 카운터 상품추가
		$Acecounter = new Acecounter();
		$addGoodsno = array();
		foreach ($_POST[sno] as $v){
			array_push($addGoodsno, $_POST['goodsno'][$v]);
		}
		$addEas = array_fill(0, count($addGoodsno), 1);
		if ($Acecounter->goods_cart_add($cart->item, $addGoodsno, $addEas) === true) {
			echo $Acecounter->scripts;
			exit('
			<script>
			window.onload = function() {
				location.replace("../goods/goods_cart.php");
			}
			</script>
			');
		}

		go("../goods/goods_cart.php");
		break;

	case "addItem":
		if($_POST[preview]=='y'){
			chkOpenYn($_POST[goodsno],"C",'parentClose');
		}
		else{
			chkOpenYn($_POST[goodsno],"C",-2);
		}
		// 멀티옵션
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

				$opt = @explode("|",implode("|",$_opt));
				$addopt = @implode("|",$_addopt);
				$addopt_inputable = @implode("|",$_addopt_inputable);

				$query = "
				select * from
					".GD_MEMBER_WISHLIST."
				where
					m_no = '$sess[m_no]'
					and goodsno = '$_POST[goodsno]'
					and opt1 = '$opt[0]'
					and opt2 = '$opt[1]'
					and addopt = '$addopt'
					and addopt_inputable = '$addopt_inputable'
				";
				list ($chk) = $db->fetch($query);
				if (!$chk){
					$query = "
					insert into ".GD_MEMBER_WISHLIST." set
						m_no = '$sess[m_no]',
						goodsno = '$_POST[goodsno]',
						opt1 = '$opt[0]',
						opt2 = '$opt[1]',
						addopt = '$addopt',
						addopt_inputable = '$addopt_inputable',
						regdt = now()
					";
					$db->query($query);
				}
			}
		}
		else {
			$query = "
			select * from
				".GD_MEMBER_WISHLIST."
			where
				m_no = '$sess[m_no]'
				and goodsno = '$_POST[goodsno]'
				and opt1 = '$opt[0]'
				and opt2 = '$opt[1]'
				and addopt = '$addopt'
				and addopt_inputable = '$addopt_inputable'
			";
			list ($chk) = $db->fetch($query);
			if (!$chk){
				$query = "
				insert into ".GD_MEMBER_WISHLIST." set
					m_no = '$sess[m_no]',
					goodsno = '$_POST[goodsno]',
					opt1 = '$opt[0]',
					opt2 = '$opt[1]',
					addopt = '$addopt',
					addopt_inputable = '$addopt_inputable',
					regdt = now()
				";
				$db->query($query);
			}
		}
		break;

	case "delItem":
		$sno = implode(",",$_POST[sno]);
		$db->query("delete from ".GD_MEMBER_WISHLIST." where sno in ($sno)");
		break;

	case "addItemFromCart" :
		$orderitem_mode = "cart";
		include "../lib/cart.class.php";
		$cart = new Cart;
		chkOpenYn($cart,"B",null);

		$idxs = isset($_POST[idxs]) ? $_POST[idxs] : null;

		if (is_array($idxs) && !empty($idxs)) foreach($idxs as $idx) {

			$item = $cart->item[$idx];

			$_addopt = array();

			if (is_array($item[addopt])) foreach ($item[addopt] as $addopt) {
				$_addopt[] = $addopt[sno].'^'.$addopt[optnm].'^'.$addopt[opt].'^'.$addopt[price];
			}

			$addopt = @implode("|",$_addopt);
			$opt = $item[opt];
			$query = "
			select * from
				".GD_MEMBER_WISHLIST."
			where
				m_no = '$sess[m_no]'
				and goodsno = '$item[goodsno]'
				and opt1 = '$opt[0]'
				and opt2 = '$opt[1]'
				and addopt = '$addopt'
			";
			list ($chk) = $db->fetch($query);
			if (!$chk){
				$query = "
				insert into ".GD_MEMBER_WISHLIST." set
					m_no = '$sess[m_no]',
					goodsno = '$item[goodsno]',
					opt1 = '$opt[0]',
					opt2 = '$opt[1]',
					addopt = '$addopt',
					regdt = now()
				";

				$db->query($query);
			}
		}
		break;
}
if ($mode) {
echo '
<script>
	var preview="'.$_REQUEST[preview].'";
	if (preview == "y" ) {
		if (confirm("상품보관함에 담았습니다.\n지금확인하시겠습니까?")) {
			top.opener.location.replace("./mypage_wishlist.php");
		}
		top.window.close();
	}
	else {
		window.location.replace("./mypage_wishlist.php");
	}
</script>
';
exit;
}

$db_table = "
".GD_MEMBER_WISHLIST." as w
left join ".GD_GOODS." as a on w.goodsno=a.goodsno
left join ".GD_GOODS_OPTION." as b on w.goodsno=b.goodsno and w.opt1=b.opt1 and w.opt2=b.opt2 and go_is_deleted <> '1' and go_is_display = '1'
";

$where[] = "w.m_no = $sess[m_no]";
$where[] = "a.open";

$pg = new Page($_GET[page]);
$pg->field = "w.*,a.goodsnm,a.img_s,b.price,b.reserve";
$pg->setQuery($db_table,$where,"sno desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res,1)){

	### 필수옵션
	$data[opt]	= array_notnull(array(
				$data[opt1],
				$data[opt2],
				));
	### 선택옵션
	$addopt = array_notnull(explode("|",$data[addopt]));
	if ($addopt){
		$data[r_addopt] = $addopt;
		unset($r_addopt); $addprice = 0;
		foreach ($addopt as $v){
			list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
			$r_addopt[] = $tmp;
			$addprice += $tmp[price];
		}
		$data[addopt] = $r_addopt;
		$data[addprice] = $addprice;
	}

	// 입력옵션
	$addopt_inputable = array_notnull(explode("|",$data[addopt_inputable]));
	if ($addopt_inputable){
		$data[r_addopt_inputable] = $addopt_inputable;
		unset($r_addopt_inputable); $addprice = 0;
		foreach ($addopt_inputable as $v){
			list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
			$r_addopt_inputable[] = $tmp;
			$addprice += $tmp[price];
		}
		$data[addopt_inputable] = $r_addopt_inputable;
		$data[addprice] += $addprice;
	}

	$loop[] = $data;
}

### 오픈스타일 헤더 노출
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

$tpl->assign('loop',$loop);
$tpl->assign('pg',$pg);
$tpl->print_('tpl');

?>
