<?

### �ô�����Ʈ (AGSPay V4.0 for PHP)

include '../conf/pg.agspay.php';
@include '../conf/pg.escrow.php';

$pg['zerofee'] = ( $pg['zerofee'] == 'yes' ? '9000400002' : '9000400001' );			// ������ ���� (Y:9000400002 / N:9000400001)
if ($pg['zerofee'] != '9000400002' || empty($pg['zerofee_period']) === true) {
	$pg['zerofee_period'] = 'NONE';
}

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
//��ǰ�� Ư������ �� �±� ����
$ordnm = strcut(pg_text_replace(strip_tags($ordnm)),90); // ��ǰ�� �±� ����, ���� ����(������ü:100byte �̳�)
if($i > 1)$ordnm .= ' ��'.($i-1).'��';
?>