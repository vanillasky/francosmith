<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";


if($_GET['goodsno']) {

	$where[] = "a.goodsno = '$_GET[goodsno]'";
}
else {
	
	chkMemberShopTouch();

	$where[] = "a.m_no = '$sess[m_no]'";
}

### 상품 사용기
$pg = new Page($_GET[page],10);
$pg->field = "distinct a.sno, a.goodsno, a.subject, a.contents, a.point, a.regdt, b.m_no, b.m_id, a.attach";
$db_table = "".GD_GOODS_REVIEW." a left join ".GD_MEMBER." b on a.m_no=b.m_no";



$pg->setQuery($db_table,$where,$sort="regdt desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;
	$data[contents] = nl2br(htmlspecialchars($data[contents]));
	$data[point] = sprintf( "%0d", $data[point]);

	$query = "select b.goodsnm,b.img_s,c.price
	from
		".GD_GOODS." b
		left join ".GD_GOODS_OPTION." c on b.goodsno=c.goodsno and link
	where
		b.goodsno = '" . $data[goodsno] . "'";
	list( $data[goodsnm], $data[img_s], $data[price] ) = $db->fetch($query);

	if ($data[attach] == 1) {
		$data[image] = '<img src="../data/review/'.'RV'.sprintf("%010s", $data[sno]).'">';
	}
	else $data[image] = '';

	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

### 템플릿 출력
$tpl->print_('tpl');

?>