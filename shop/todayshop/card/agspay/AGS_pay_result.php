<?php
/**********************************************************************************************
*
* ���ϸ� : AGS_pay_result.php
* �ۼ����� : 2006/08/03
*
* ���ϰ�������� ó���մϴ�.
*
* Copyright 2005-2006 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

//������
$AuthTy 		= trim( $_POST["AuthTy"] );				//��������
$SubTy 			= trim( $_POST["SubTy"] );				//�����������
$rStoreId 		= trim( $_POST["rStoreId"] );			//��üID
$rAmt 			= trim( $_POST["rAmt"] );				//�ŷ��ݾ�
$rOrdNo 		= trim( $_POST["rOrdNo"] );				//�ֹ���ȣ
$rProdNm 		= trim( $_POST["rProdNm"] );			//��ǰ��
$rOrdNm			= trim( $_POST["rOrdNm"] );				//�ֹ��ڸ�

//������Ű���(�ſ�ī��,�ڵ���,�Ϲݰ������)�� ���
$rSuccYn 		= trim( $_POST["rSuccYn"] );			//��������
$rResMsg 		= trim( $_POST["rResMsg"] );			//���л���
$rApprTm 		= trim( $_POST["rApprTm"] );			//���νð�

//�ſ�ī�����
$rBusiCd 		= trim( $_POST["rBusiCd"] );			//�����ڵ�
$rApprNo 		= trim( $_POST["rApprNo"] );			//���ι�ȣ
$rCardCd 		= trim( $_POST["rCardCd"] );			//ī����ڵ�

//�ſ�ī��(�Ƚ�,�Ϲ�)
$rCardNm 		= trim( $_POST["rCardNm"] );			//ī����
$rMembNo 		= trim( $_POST["rMembNo"] );			//��������ȣ
$rAquiCd 		= trim( $_POST["rAquiCd"] );			//���Ի��ڵ�
$rAquiNm 		= trim( $_POST["rAquiNm"] );			//���Ի��
$rBillNo 		= trim( $_POST["rBillNo"] );			//��ǥ��ȣ

//�ſ�ī��(ISP)
$rDealNo 		= trim( $_POST["rDealNo"] );			//�ŷ�������ȣ

//������ü
$ICHE_OUTBANKNAME	= trim( $_POST["ICHE_OUTBANKNAME"] );	//��ü���������
$ICHE_OUTACCTNO 	= trim( $_POST["ICHE_OUTACCTNO"] );		//��ü���¹�ȣ
$ICHE_OUTBANKMASTER = trim( $_POST["ICHE_OUTBANKMASTER"] );	//��ü���¼�����
$ICHE_AMOUNT 		= trim( $_POST["ICHE_AMOUNT"] );		//��ü�ݾ�

//�ڵ���
$rHP_TID 		= trim( $_POST["rHP_TID"] );			//�ڵ�������TID
$rHP_DATE 		= trim( $_POST["rHP_DATE"] );			//�ڵ���������¥
$rHP_HANDPHONE 	= trim( $_POST["rHP_HANDPHONE"] );		//�ڵ��������ڵ�����ȣ
$rHP_COMPANY 	= trim( $_POST["rHP_COMPANY"] );		//�ڵ���������Ż��(SKT,KTF,LGT)

//�������
$rVirNo 		= trim( $_POST["rVirNo"] );				//������¹�ȣ ��������߰�
$VIRTUAL_CENTERCD = trim( $_POST["VIRTUAL_CENTERCD"] );	//������� �Ա������ڵ�

//�츮����ũ��
$mTId 		= trim( $_POST["mTId"] );					//����ũ�� �ֹ���ȣ
?>
<html>
<head>
<title>�ô�����Ʈ</title>
<style type="text/css">
<!--
body { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsright { padding-right:10px; text-align:right; }
.clsleft { padding-left:10px; text-align:left; }
-->
</style>
<script language=javascript> // "����ó����" �˾�â �ݱ�
<!--
var openwin = window.open("AGS_progress.html","popup","width=300,height=160");
openwin.close();
-->
</script>
<script language=javascript>
<!--
/***********************************************************************************
* �� ������ ����� ���� �ڹٽ�ũ��Ʈ
*		
*	������ ����� [ī�����]�ÿ��� ����Ͻ� �� �ֽ��ϴ�.
*  
*   �ش��� �����ǿ� ���ؼ� ������ ����� �����մϴ�.
*     ���� ���Ŀ��� �Ʒ��� �ּҸ� �˾�(630X510)���� ��� ���� ��ȸ �� ����Ͻñ� �ٶ��ϴ�.
*	  �� �˾��� ����������ȸ ������ �ּ� : 
*	     	 http://www.allthegate.com/support/card_search.html
*		�� (�ݵ�� ��ũ�ѹٸ� 'yes' ���·� �Ͽ� �˾��� ���ñ� �ٶ��ϴ�.) ��
*
***********************************************************************************/
function show_receipt() 
{
	if("<?=$rSuccYn?>"== "y" && "<?=$AuthTy?>"=="card")
	{
		url="http://www.allthegate.com/customer/receiptLast3.jsp"
		url=url+"?sRetailer_id="+sRetailer_id.value;
		url=url+"&approve="+approve.value;
		url=url+"&send_no="+send_no.value;
		
		window.open(url, "window","toolbar=no,location=no,directories=no,status=,menubar=no,scrollbars=no,resizable=no,width=420,height=700,top=0,left=150");
	}
	else
	{
		alert("�ش��ϴ� ���������� �����ϴ�");
	}
}
-->
</script>
</head>
<body topmargin=0 leftmargin=0 rightmargin=0 bottommargin=0>
<table border=0 width=100% height=100% cellpadding=0 cellspacing=0>
	<tr>
		<td align=center>
		<table width=400 border=0 cellpadding=0 cellspacing=0>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>���� ���</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td>
				<table width=400 border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td class=clsright width=150>�������� : </td>
						<td class=clsleft width=250>
							<?php

							if($AuthTy == "card")
							{
								if($SubTy == "isp")
								{
									echo "�ſ�ī�����-��������(ISP)";
								}	
								else if($SubTy == "visa3d")
								{
									echo "�ſ�ī�����-�Ƚ�Ŭ��";
								}
								else if($SubTy == "normal")
								{
									echo "�ſ�ī�����-�Ϲݰ���";
								}
								
							}
							else if($AuthTy == "iche")
							{
								echo "������ü";
							}
							else if($AuthTy == "hp")
							{
								echo "�ڵ�������";
							}
							else if($AuthTy == "virtual")
							{
								echo "������°���";
							}
							else if($AuthTy == "eiche")
							{
								echo "����ũ��-������ü";
							}
							else if($AuthTy == "evirtual")
							{
								echo "����ũ��-������°���";
							}
							?>
						</td>
					</tr>
					<tr>
						<td class=clsright>�������̵� : </td>
						<td class=clsleft><?=$rStoreId?></td>
					</tr>
					<tr>
						<td class=clsright>�ֹ���ȣ : </td>
						<td class=clsleft><?=$rOrdNo?></td>
					</tr>
					<tr>
						<td class=clsright>�ֹ��ڸ� : </td>
						<td class=clsleft><?=$rOrdNm?></td>
					</tr>
					<tr>
						<td class=clsright>��ǰ�� : </td>
						<td class=clsleft><?=$rProdNm?></td>
					</tr>
					<tr>
						<td class=clsright>�����ݾ� : </td>
						<td class=clsleft><?=$rAmt?></td>
					</tr>
<?				if($AuthTy == "card" || $AuthTy == "hp" || $AuthTy == "virtual" ) { ?>
					<tr>
						<td class=clsright>�������� : </td>
						<td class=clsleft><?=$rSuccYn?></td>
					</tr>
					<tr>
						<td class=clsright>ó���޼��� : </td>
						<td class=clsleft><?=$rResMsg?></td>
					</tr>
<?				}
				if($AuthTy == "card" || $AuthTy == "virtual") { ?>
					<tr>
						<td class=clsright>���νð� : </td>
						<td class=clsleft><?=$rApprTm?></td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>�����ڵ� : </td>
						<td class=clsleft><?=$rBusiCd?></td>
					</tr>
					<tr>
						<td class=clsright>���ι�ȣ : </td>
						<td class=clsleft><?=$rApprNo?></td>
					</tr>
					<tr>
						<td class=clsright>ī����ڵ� : </td>
						<td class=clsleft><?=$rCardCd?></td>
					</tr>
<?				}
				if($AuthTy == "card" && ($SubTy == "visa3d" || $SubTy == "normal") ) {?>
					<tr>
						<td class=clsright>ī���� : </td>
						<td class=clsleft><?=$rCardNm?></td>
					</tr>
					<tr>
						<td class=clsright>���Ի��ڵ� : </td>
						<td class=clsleft><?=$rAquiCd?></td>
					</tr>
					<tr>
						<td class=clsright>���Ի�� : </td>
						<td class=clsleft><?=$rAquiNm?></td>
					</tr>
					<tr>
						<td class=clsright>��������ȣ : </td>
						<td class=clsleft><?=$rMembNo?></td>
					</tr>
					<tr>
						<td class=clsright>��ǥ��ȣ : </td>
						<td class=clsleft><?=$rBillNo?></td>
					</tr>
<?				}
				if($AuthTy == "card" && $SubTy == "isp" ) {?>
					<tr>
						<td class=clsright>�ŷ�������ȣ : </td>
						<td class=clsleft><?=$rDealNo?></td>
					</tr>
<?				}
				if($AuthTy == "iche" || $AuthTy == "eiche" ) {?>
					<tr>
						<td class=clsright>��ü��������� : </td>
						<td class=clsleft><?=$ICHE_OUTBANKNAME?></td>
					</tr>
					<tr>
						<td class=clsright>��ü�ݾ� : </td>
						<td class=clsleft><?=$ICHE_AMOUNT?></td>
					</tr>
<?				}
				if($AuthTy == "iche" ) {?>
					<tr>
						<td class=clsright>��ü���¼����� : </td>
						<td class=clsleft><?=$ICHE_OUTBANKMASTER?></td>
					</tr>
<?				}
				if($AuthTy == "hp" ) {?>
					<tr>
						<td class=clsright>�ڵ�������TID : </td>
						<td class=clsleft><?=$rHP_TID?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ���������¥ : </td>
						<td class=clsleft><?=$rHP_DATE?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ��������ڵ�����ȣ : </td>
						<td class=clsleft><?=$rHP_HANDPHONE?></td>
					</tr>
					<tr>
						<td class=clsright>�ڵ���������Ż�� : </td>
						<td class=clsleft><?=$rHP_COMPANY?></td>
					</tr>
<?				}
				if($AuthTy == "eiche" || $AuthTy == "evirtual" ) {?>
					<tr>
						<td class=clsright>����ũ���ֹ���ȣ : </td>
						<td class=clsleft><?=$mTId?></td>
					</tr>
<?				}
				if($AuthTy == "virtual" || $AuthTy == "evirtual" ) {?>
					<tr>
						<td class=clsright>�Աݰ��¹�ȣ : </td>
						<td class=clsleft><?=$rVirNo?></td>
					</tr>
                    <tr><!-- �����ڵ�(20) : �츮���� -->
						<td class=clsright>�Ա����� : </td>
						<td class=clsleft><?if($VIRTUAL_CENTERCD == "20"){echo "�츮����";}else{?><?=$VIRTUAL_CENTERCD?><?}?></td>
					</tr>
                    <tr>
						<td class=clsright>�����ָ� : </td>
						<td class=clsleft>(��)������ȿ��</td>
					</tr>
<?				}
				if($AuthTy == "card" ) {?>
					<tr>
						<td class=clsright>������ :</td>
						<!--��������������ؼ������ִ°�-------------------->
						<input type=hidden name=sRetailer_id value="<?=$rStoreId?>"><!--�������̵�-->
						<input type=hidden name=approve value="<?=$rApprNo?>"><!---���ι�ȣ-->
						<input type=hidden name=send_no value="<?=$rOrdNo?>"><!--�ֹ���ȣ-->
						<!--��������������ؼ������ִ°�-------------------->
						<td class=clsleft><input type="button" value="������" onclick="javascript:show_receipt();"></td>
					</tr>
<?				}	?>
					<tr>
						<td colspan=2>&nbsp;</td>
					</tr>
					<tr>
						<td align=center colspan=2>ī�� �̿������ ����ó�� <font color=red>������ȿ��(��)</font>�� ǥ��˴ϴ�.</td>
					</tr>
					
				</table>
				</td>
			</tr>
			<tr>
				<td><hr></td>
			</tr>
			<tr>
				<td class=clsleft>Copyright 2005-2006 AEGISHYOSUNG.Co.,Ltd. All rights reserved.</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
</body>
</html>
