<?php
    @include_once dirname(__FILE__)."/allatutil.php";
	include dirname(__FILE__)."/../../../../conf/pg_mobile.allatbasic.php";

    // Set CrossKey 
    // -------------------------------------------------------------------
    $at_cross_key= $pg_mobile['crosskey'];  //������ CrossKey�� (ġȯ�ʿ�)

    // Set Value
    // -------------------------------------------------------------------
    $at_shop_id=$data['allat_shop_id'];         //ShopId��     (�ִ�  20�ڸ�)
    $at_order_no=$data['allat_order_no'];    //�ֹ���ȣ     (�ִ�  80�ڸ�)
    $at_amt=$data['allat_amt'];                  //��ұݾ�     (�ִ�  10�ڸ�)
    $at_pay_type=$data['allat_pay_type'];          //���ŷ����� �������[ī��:CARD,������ü:ABANK]
    $at_seq_no=$data['allat_seq_no'];           //�ŷ��Ϸù�ȣ (�ִ�  10�ڸ�) : �ɼ��ʵ���
    $at_test_yn="N";              //�׽�Ʈ ����
    $at_opt_pin="NOUSE";
    $at_opt_mod="APP";

    // set Enc Data
    // -------------------------------------------------------------------
    $at_enc=setValue($at_enc,"allat_shop_id",$at_shop_id);
    $at_enc=setValue($at_enc,"allat_order_no",$at_order_no);
    $at_enc=setValue($at_enc,"allat_amt",$at_amt);
    $at_enc=setValue($at_enc,"allat_pay_type",$at_pay_type);
    $at_enc=setValue($at_enc,"allat_seq_no",$at_seq_no);
    $at_enc=setValue($at_enc,"allat_test_yn",$at_test_yn);
    $at_enc=setValue($at_enc,"allat_opt_pin",$at_opt_pin);
    $at_enc=setValue($at_enc,"allat_opt_mod",$at_opt_mod);

    // Set Request Data
    //--------------------------------------------------------------------
    $at_data   = "allat_shop_id=".$at_shop_id.
                 "&allat_enc_data=".$at_enc.
                 "&allat_cross_key=".$at_cross_key;

    // �þܰ� ��� �� ����� �ޱ� : CancelReq->����Լ�
    //-----------------------------------------------------------------
    $at_txt=CancelReq($at_data,"SSL");

    // �����
    //----------------------------------------------------------------
    $REPLYCD     = getValue("reply_cd",$at_txt);       //����ڵ�
    $REPLYMSG    = getValue("reply_msg",$at_txt);      //��� �޼���

    // ����� ó��
    //------------------------------------------------------------------
    if( !strcmp($REPLYCD,"0000") || !strcmp($REPLYCD,"0001") ){
		// reply_cd "0000" �϶��� ����
		$CANCEL_YMDHMS=getValue("cancel_ymdhms",$at_txt);
		$PART_CANCEL_FLAG=getValue("part_cancel_flag",$at_txt);
		$REMAIN_AMT=getValue("remain_amt",$at_txt);
		$PAY_TYPE=getValue("pay_type",$at_txt);

		$settlelog = '\n----------------------------------------\n';
		$settlelog .= 'All@Pay Mobile ī�� ��� ���'."\n";
		$settlelog .= "����ڵ�: ".$REPLYCD."\n";
		$settlelog .= "����޼���: ".$REPLYMSG."\n";
		$settlelog .= "��ҳ�¥: ".$CANCEL_YMDHMS."\n";
		$settlelog .= "��ұ���: ".$PART_CANCEL_FLAG."\n";
		$settlelog .= '----------------------------------------\n';
		$cardCancelResult	= true;

    } else {
		// reply_cd �� "0000" �ƴҶ��� ���� (�ڼ��� ������ �Ŵ�������)
		// reply_msg �� ���п� ���� �޼���
		$settlelog = '\n----------------------------------------\n';
		$settlelog .= 'All@Pay Mobile ī�� ��� ���\n';
		$settlelog .= "$ordno (".date('Y:m:d H:i:s').")\n";
		$settlelog .= "����ڵ�: ".$REPLYCD."\n";
		$settlelog .= "����޼���: ".$REPLYMSG."\n";
		$settlelog .= '----------------------------------------\n';
		$cardCancelResult	= false;
    }
?>