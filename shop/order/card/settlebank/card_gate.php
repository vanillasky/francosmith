<?

### ��Ʋ��ũ

include "../conf/pg.settlebank.php";
@include "../conf/pg.escrow.php";
require_once "../lib/Pg_RingToPay.class.php";

	//ringTOpay �������� �ε� 
	$RtP = new Pg_RingToPay();
	$RtP->RtoPConfigRead();

	// ������ ���� 
	$pg['zerofee']	= ( $pg['zerofee'] == "yes" ? '1' : '0' );			// ������ ���� (Y:1 / N:0)

	// ��ǰ ����
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	//��ǰ�� Ư������ �� �±� ����
	$ordnm	= pg_text_replace(strip_tags($ordnm));
	if($i > 1)$ordnm .= " ��".($i-1)."��";

	/*
	 * 1. �⺻���� ����	 
	 */	
	
	$STT['MID']						= $pg['id'];										//�������̵�
	$STT['KEYCODE']					= $pg['key'];										//��Ʋ��ũ���� �߱޹��� Ű��
	$STT['OID']						= $_POST['ordno'];									//�ֹ���ȣ(�������� ����ũ�� �ֹ���ȣ�� �Է��ϼ���)
	$STT['AMOUNT']					= $_POST['settleprice'];							//�����ݾ�("," �� ������ �����ݾ��� �Է��ϼ���)
	$STT['PRODUCTINFO']				= $ordnm;											//��ǰ��
	$STT['SETTLEKIND']				= $_POST[settlekind];								// �ſ�ī�� : c , ������ü : o , ������� : v , �ڵ��� : h
	$STT['RPAY_YN']					= $RtP->getRpay_yn();								// ���������� ��뿩�θ� �����Ѵ�. godo������ ����

	 $tpl->assign('STT',$STT);
?>