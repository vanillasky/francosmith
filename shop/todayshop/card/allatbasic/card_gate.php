<?

### All@Pay�� Basic

//include "../conf/pg.allatbasic.php";
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
if($i > 1)$ordnm .= " ��".($i-1)."��";

### ������� ����
if ($_POST[settlekind] == "c") { $pg[CARD]	= "Y"; } else { $pg[CARD]	= "N";} //�ſ�ī��
if ($_POST[settlekind] == "o") { $pg[ABANK]	= "Y"; } else { $pg[ABANK]	= "N";} //������ü
if ($_POST[settlekind] == "v") { $pg[VBANK]	= "Y"; } else { $pg[VBANK]	= "N";} //�������
if ($_POST[settlekind] == "h") { $pg[HP] = "Y"; }    else { $pg[HP]		= "N";} //�ڵ���
//if ($_POST[settlekind] == "a") {	$paymentCode[VBANK] = "Y"; } else { $paymentCode[VBANK] = "N";} //�������Ա�
z?>