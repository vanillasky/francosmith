<?

### All@Pay�� Plus 2.0

include "../conf/pg.allat.php";
@include "../conf/pg.escrow.php";

$pg['tax']		= "N";		// �������� (Y/N) - ���ݿ��������� �ʿ� (N:�̻���)
$pg['test']		= "N";		// �׽�Ʈ ���� (Y:�׽�Ʈ,N:�Ǽ���)
$pg['real']		= "Y";		// ��ǰ �ǹ����� (Y:�ǹ�,N:�ǹ��ƴ�)

### �����ݾ� 5���� �̻�� �Һΰ���
if ($_POST[settleprice]<50000) $pg['quota'] = "0";

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}else{
	if ($data[settleprice]<50000) $pg['quota'] = "0";
}
$i=0;

foreach($item as $v){
	$i++;
	if($i == 1){
		$ordnm = $v[goodsnm];
		$ordgoodsno = $v[goodsno];
	}
}
//��ǰ�� Ư������ �� �±� ����
$ordnm = pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " ��".($i-1)."��";
?>