<?php

	include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
	include dirname(__FILE__)."/../../../../conf/pg_mobile.allatbasic.php";

	//$escrow = array_merge($escrow,$escrowMobile);

	### �����ݾ� 5���� �̻�� �Һΰ���
	if ($_POST[settleprice]<50000) $pg_mobile['quota'] = "0";

	if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
		$item = $cart -> item;
	}else{
		if ($data[settleprice]<50000) $pg_mobile['quota'] = "0";
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

	$allat_card_yn		= "N";
	$allat_vbank_yn		= "N";
	$allat_hp_yn		= "N";

	$allat_cardes_yn		= "N";
	$allat_vbankes_yn		= "N";
	$allat_hpes_yn		= "N";

	switch ($_POST[settlekind]){
		case "c":	// �ſ�ī��
			$allat_card_yn		= "Y";
			$allat_cardes_yn	= $_POST['escrow']=='Y'?'Y':'N';
			break;
		case "v":	// �������
			$allat_vbank_yn		= "Y";
			$allat_vbankes_yn	= $_POST['escrow']=='Y'?'Y':'N';
			break;
		case "h":	// �ڵ���
			$allat_hp_yn		= "Y";
			$allat_hpes_yn	= $_POST['escrow']=='Y'?'Y':'N';
			break;
	}

	// ȸ��ID ����
	if ($sess['m_id'] != '') {
		$pmember_id = $sess['m_id'];
	} else if ($_POST['email'] != ''){
		$pmember_id = $_POST['email'];
	} else {
		$pmember_id = 'guest';
	}
	$pmember_id = substr($pmember_id, 0, 20);

?>
<script type="text/javascript" src="https://tx.allatpay.com/common/AllatPayM.js"></script>
<script type="text/javascript">
	// ���������� ȣ��
	function approval() {
		var sendFm = document.getElementById('sendFm');
		Allat_Mobile_Approval(sendFm,0,0); /* ������ ���� (����â ũ��, 320*360) */
	}

	// ����� ��ȯ( receive ���������� ȣ�� )
	function approval_submit(result_cd,result_msg,enc_data) {

		Allat_Mobile_Close();

		if( result_cd != '0000' ){
			alert(result_cd + " : " + result_msg);
		} else {
			sendFm.allat_enc_data.value = enc_data;

			sendFm.action = "<?=ProtocolPortDomain()?><?=$cfg['rootDir']?>/order/card/allatbasic/mobile/card_return.php";
			sendFm.method = "post";
			sendFm.target = "_self";
			sendFm.submit();
		}
	}
</script>

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>����� All@Pay ����ȭ������ �̵��մϴ�.</b></strong></div>

<form id="sendFm" name="sendFm" method="POST">
<input type="hidden" name="allat_shop_id" value="<?=$pg_mobile['id']?>">
<input type="hidden" name="allat_order_no" value="<?=$_POST['ordno']?>">
<input type="hidden" name="allat_amt" value="<?=$_POST['settleprice']?>">
<input type="hidden" name="allat_pmember_id" value="<?=$pmember_id?>">
<input type="hidden" name="allat_product_cd" value="<?=$ordgoodsno?>">
<input type="hidden" name="allat_product_nm" value="<?=$ordnm?>">
<input type="hidden" name="allat_buyer_nm" value="<?=$_POST["nameOrder"]?>">
<input type="hidden" name="allat_recp_nm" value="<?=$_POST['nameReceiver']?>">
<input type="hidden" name="allat_recp_addr" value="<?=htmlspecialchars($_POST['address'])?>">
<input type="hidden" name="shop_receive_url" value="<?=ProtocolPortDomain()?><?=$cfg['rootDir']?>/order/card/allat/mobile/allat_receive.php">
<input type="hidden" name="allat_enc_data" value="">

<input type="hidden" name="allat_card_yn" value="<?=$allat_card_yn?>">
<input type="hidden" name="allat_abank_yn" value="N">
<input type="hidden" name="allat_vbank_yn" value="<?=$allat_vbank_yn?>">
<input type="hidden" name="allat_hp_yn" value="<?=$allat_hp_yn?>">
<input type="hidden" name="allat_ticket_yn" value="N">
<input type="hidden" name="allat_cash_yn" value="<?=$pg_mobile['receipt']=='Y'?'Y':'N'?>">
<input type="hidden" name="allat_zerofee_yn" value="<?=$pg_mobile['zerofee']=="yes"?'Y':'N'?>">
<input type="hidden" name="allat_cardes_yn" value="<?=$allat_cardes_yn?>">
<input type="hidden" name="allat_abankes_yn" value="N">
<input type="hidden" name="allat_vbankes_yn" value="<?=$allat_vbankes_yn?>">
<input type="hidden" name="allat_hpes_yn" value="<?=$allat_hpes_yn?>">
<input type="hidden" name="allat_ticketes_yn" value="N">
<input type="hidden" name="allat_test_yn" value="N"><!-- �׽�Ʈ ���� -->
<input type="hidden" name="allat_email_addr" value="<?=$_POST["email"]?>">
<!--<input type="hidden" name="allat_sell_yn" value="">-->
<input type="hidden" name="allat_bonus_yn" value="N">
<!--<input type="hidden" name="allat_real_yn" value="Y">-->
</form>