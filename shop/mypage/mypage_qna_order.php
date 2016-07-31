<?

include "../_header.php"; chkMember();
include "../lib/page.class.php";

### 주문
$pg = new Page($_GET[page],5);
$pg->field = "a.ordno, a.orddt, a.settleprice";
$db_table = "".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no";

$where[] = "a.m_no='$sess[m_no]'";

$pg->setQuery($db_table,$where,$sort="ordno desc");
$pg->exec();

$res = $db->query($pg->query);
while ($data=$db->fetch($res)){

	$data['idx'] = $pg->idx--;

	list( $data[cnt], $data[ea] ) = $db->fetch( "select count(ea), sum(ea) from ".GD_ORDER_ITEM." where ordno = '$data[ordno]' limit 1" );

	list( $data[goodsnm] ) = $db->fetch( "select goodsnm from ".GD_ORDER_ITEM." where ordno = '$data[ordno]' limit 1" );
	if ( $data[cnt] > 1 ) $data[goodsnm] = strcut( $data[goodsnm], 22 ) . ' 외 ' . ( $data[cnt] - 1 ) . '건';
	else $data[goodsnm] = strcut( $data[goodsnm], 28 );

	$data['orddt'] = substr($data['orddt'],2,8);
	$loop[] = $data;
}

$tpl->assign( 'pg', $pg );

$tpl->print_('tpl');

?>