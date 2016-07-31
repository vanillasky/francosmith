<?php
include "../lib/library.php";
include "../lib/load.class.php";
require "../lib/auctionIpay.class.php";
require "../conf/config.php";

/**
 * xml node value �̸����� ��������.
 *
 */
function getXmlValueByName($index, $vals, $nodeName) {
	$nodeName = strtoupper($nodeName);
	if (empty($index[$nodeName]) === false) {
		foreach($index[$nodeName] as $idx) {
			if ($vals[$idx]['tag'] ==$nodeName && isset($vals[$idx]['value'])) {
				return iconv('UTF-8', 'EUC-KR', $vals[$idx]['value']);
			}
		}
	}
	return null;
}

/*
$receive_xml :
// ���� :
<IpayResponse>
<ResponseType>0</ResponseType>					--0 ����, 1 ���
<AuctionOrderNo>584812713</AuctionOrderNo>		--���� �ֹ���ȣ
<IpayCartNo>41359</IpayCartNo>					--īƮ��ȣ
<OrderDate>2010-11-18 09:03:00</OrderDate>		--�ֹ��Ͻ�
<BankCode>1520</BankCode>						--������ �Ա� �����ڵ�(�� 2�ڸ��� Ȱ���Ͻø� �˴ϴ�.), �ٸ� ���� ������ �����Ͱ� �����ϴ�.
<BankName>�츮����</BankName>                   --������ �Ա� �����, �ٸ� ���� ������ �����Ͱ� �����ϴ�.
<AccountNumb>00004834818456</AccountNumb>		--���¹�ȣ, �ٸ� ���� ������ �����Ͱ� �����ϴ�.
<PayDate></PayDate>								--�����Ͻ� ������ �Ա��� �ƴ� ���������� �����Ͻð� ���ϴ�. ��) 2010-10-20 10:59:00
<PayPrice>22500</PayPrice>						--�����ݾ�
<PaymentType>A</PaymentType>					--����Ÿ�� (A=�������Ա� ,C=ī��, M=�����, D=�ǽð�������ü)
<ExpireDate>2010-11-25</ExpireDate>				--������ �Ա��� ����������. ��)2010-11-25 �� 2010-11-25 23:59:59 �� �ǹ��մϴ�.
<Emoney>0</Emoney>								--���� �̸Ӵ� ���ݾ�
<CardName></CardName>							--������ ����� ī����
<CardMonth></CardMonth>							--ī�� �Һΰ�� �� ��)00=�Ͻú�, 03=3���� �Һ�
<NoInterestYN>N</NoInterestYN>					--ī�� ������ �Һ� ���� (Y=������ �Һ�, N=������ �Һ�)
<BuyerName>ȫ�浿</BuyerName>					--�������� ���� ȸ���̸��Դϴ�. (���� �Ա��� �� �Ǵ� ��ǰ ������ ��� �ٸ� �� �ֽ��ϴ�.)
<AuctionPayNo>507331315</AuctionPayNo>			--���� ������ȣ
</IpayResponse>

OR

<IpayResponse>
<ResponseType>0</ResponseType>					--0 ����, 1 ���
<IpayItemNo>1_12</IpayItemNo>
<IpayCartNo>625440</IpayCartNo>
<PaymentType>A</PaymentType>
<PayDate>2012-05-25 11:11:00</PayDate>
<AuctionOrderNo>722353796</AuctionOrderNo>
</IpayResponse>

// ��� :
<IpayResponse>
<ResponseType>1</ResponseType>					--0 ����, 1 ���
<AuctionOrderNo>123456789</AuctionOrderNo>		--���� �ֹ���ȣ
<IpayCartNo>12345</IpayCartNo>					--īƮ��ȣ
<AuctionItemNo>I000016033</AuctionItemNo>
<IpayItemNo>54</IpayItemNo>						--��ǰ��ȣ
<CancelDate>2010-10-02 ���� 5:37:00 </CancelDate>
</IpayResponse>
*/

ini_set("always_populate_raw_post_data", "true");
$receive_xml = $GLOBALS['HTTP_RAW_POST_DATA'];

if (empty($receive_xml)) exit;

$CURRENT_DATETIME = Core::helper('Date')->format(G_CONST_NOW);

$parser = xml_parser_create("UTF-8");
xml_parse_into_struct($parser, trim($receive_xml), $vals, $index);
xml_parser_free($parser);

$result['IpayCartNo'] = getXmlValueByName($index, $vals, 'IpayCartNo');
if (!$result['IpayCartNo']) exit;

$tblIpay = 'gd_auctionipay';
$tblIpayItem = 'gd_auctionipay_item';

// XML Ÿ��.
$result['ResponseType'] = getXmlValueByName($index, $vals, 'ResponseType');

list($pgType) = $db->fetch("SELECT `pg` FROM `gd_order` WHERE `ipay_cartno`=".$result['IpayCartNo']);

if($pgType=='ipay')
{
	$result['PaymentType'] = getXmlValueByName($index, $vals, 'PaymentType');
	if($result['ResponseType']=='0' && $result['PaymentType']=='A')
	{
		$result['AuctionOrderNo'] = getXmlValueByName($index, $vals, 'AuctionOrderNo');
		$result['AuctionPayNo'] = getXmlValueByName($index, $vals, 'AuctionPayNo');
		$result['PaymentType'] = getXmlValueByName($index, $vals, 'PaymentType');
		$result['ExpireDate'] = getXmlValueByName($index, $vals, 'ExpireDate');
		$result['BuyerName'] = getXmlValueByName($index, $vals, 'BuyerName');
		$result['OrderDate'] = getXmlValueByName($index, $vals, 'OrderDate');
		$payDate = getXmlValueByName($index, $vals, 'PayDate');

		// ȯ����� ��� �谨.
		$stockStatus = 'n';
		if ($cfg['stepStock'] == '0' && ($result['OrderDate'] !== null && strlen($result['OrderDate']) > 0)) $stockStatus = 'y'; // ���谨�ܰ谡 �ֹ���, �ֹ����ڰ� �ִ°�� ���谨
		if ($cfg['stepStock'] == '1' && ($payDate !== null && strlen($payDate) > 0)) $stockStatus = 'y'; // ���谨�ܰ谡 �Աݽ�, �������ڰ� �ִ°�� ���谨

		$ordno = null;
		$preStep = null;
		$goodsSql = "
		SELECT `o`.`ordno`, `o`.`step`, `o`.`ipay_payno` AS `ipaysno`, `oi`.`goodsno`, `oi`.`optno` AS `optionsno`, `oi`.`ipay_ordno`, `go`.`stock`, `oi`.`ea`, `oi`.`stockable`, `oi`.`stockyn` AS `stockStatus`
		FROM `gd_order` AS `o`
		RIGHT JOIN `gd_order_item` AS `oi`
		ON `o`.`ordno`=`oi`.`ordno`
		LEFT JOIN `gd_goods_option` AS `go`
		ON `go`.`optno`=`oi`.`optno` and go_is_deleted <> '1' and go_is_display = '1'
		WHERE `o`.`ipay_cartno`=".$result['IpayCartNo']." AND `oi`.`ipay_ordno`=".$result['AuctionOrderNo']." AND `o`.`step`<1 AND `o`.`step2`<1";
		$goodsRes = $db->query($goodsSql);
		while($data = $db->fetch($goodsRes))
		{
			if($ordno===null) $ordno = $data['ordno'];
			if($preStep===null) $preStep = $data['step'];
			if($data['stockable']=='n') $stockStatus = 'n'; // ��ǰ�� ������� �Ұ����� ���
			if($stockStatus=='y' && $data['stockStatus']=='n')
			{
				$newStock = $data['stock'] - $data['ea'];
				$newStockSql = " UPDATE ".GD_GOODS_OPTION." SET stock=".$newStock." WHERE goodsno='".$data['goodsno']."' AND optno='".$data['optionsno']."'";
				$db->query($newStockSql);

				$totStockSql = " SELECT SUM(stock) AS totStock FROM ".GD_GOODS_OPTION." WHERE goodsno='".$data['goodsno']."'  and go_is_deleted <> '1' and go_is_display = '1' ";
				$totRes = $db->fetch($totStockSql);

				$newStockSql = " UPDATE ".GD_GOODS." SET totStock=".$totRes['totStock'];
				if ($totRes['totStock'] == 0) $newStockSql .= ", runout=1 ";
				else $newStockSql .= ", runout=0 ";
				$newStockSql .= " WHERE goodsno='".$data['goodsno']."'";
				$db->query($newStockSql);

				unset($totRes);
			}
			// ��� �谨�ܰ谡 �ֹ����̰� �������ڰ�������(�̹� �ֹ��ܰ谡 �������) ���谨�� �̷������ �ʵ� DB�󿡴� ���谨�� �Ͼ �ֹ������� �˷���
			if($cfg['stepStock']=='0' && ($payDate !== null && strlen($payDate) > 0)) $stockStatus = 'y';
			$orderItemSql = "UPDATE `gd_order_item` SET `stockyn`='".$stockStatus."', `istep`=1, `cyn`='y' WHERE `ipay_ordno`=".$data['ipay_ordno']." AND `istep`<1";
			$db->query($orderItemSql);
		}
		if($ordno!==null)
		{
			$orderSql = "
			UPDATE `gd_order` AS `o`
			LEFT JOIN (SELECT `soi`.`ordno`, COUNT(`soi`.`sno`) AS `count_all`, COUNT(IF(`soi`.`cyn`='y',1,NULL)) AS `count_paid` FROM `gd_order_item` AS `soi` GROUP BY `soi`.`ordno`) AS `oi`
			ON `o`.`ordno`=`oi`.`ordno`
			SET `o`.`step`=1, `o`.`cdt`='".$CURRENT_DATETIME."', `o`.`cyn`='y'
			WHERE `o`.`ordno`=".$ordno." AND `o`.`step`<1 AND `o`.`step2`<1 AND `oi`.`count_all`=`oi`.`count_paid`";
			$db->query($orderSql);
			if((int)$db->affected()>0) orderLog($ordno,$r_step[$preStep]." > ".$r_step[1]);
		}
		unset($data);
	}
}
else
{
	switch($result['ResponseType']) {
		case '0' : {
			$subSql[] = " stepstock='".$cfg['stepStock']."' "; // ����� �ñ�.

			$result['AuctionOrderNo'] = getXmlValueByName($index, $vals, 'AuctionOrderNo');
			$result['AuctionPayNo'] = getXmlValueByName($index, $vals, 'AuctionPayNo');
			$result['PaymentType'] = getXmlValueByName($index, $vals, 'PaymentType');
			$result['ExpireDate'] = getXmlValueByName($index, $vals, 'ExpireDate');
			$result['BuyerName'] = getXmlValueByName($index, $vals, 'BuyerName');
			$result['OrderDate'] = getXmlValueByName($index, $vals, 'OrderDate');
			$payDate = getXmlValueByName($index, $vals, 'PayDate');

			if ($result['AuctionOrderNo'] !== null && strlen($result['AuctionOrderNo']) > 0) $subSql[] = " auctionordno='".$result['AuctionOrderNo']."' ";
			if ($result['AuctionPayNo'] !== null && strlen($result['AuctionPayNo']) > 0) $subSql[] = " auctionpayno='".$result['AuctionPayNo']."' ";
			if ($result['PaymentType'] !== null && strlen($result['PaymentType']) > 0) $subSql[] = " paymenttype='".$result['PaymentType']."' ";
			if ($result['ExpireDate'] !== null && strlen($result['ExpireDate']) > 0) $subSql[] = " expiredate='".$result['ExpireDate']."' ";
			if ($result['BuyerName'] !== null && strlen($result['BuyerName']) > 0) $subSql[] = " buyername='".$result['BuyerName']."' ";
			if ($result['OrderDate'] !== null && strlen($result['OrderDate']) > 0) $subSql[] = " orderdate='".$result['OrderDate']."' ";
			if ($payDate !== null && strlen($payDate) > 0) $payDateSql = ", paydate='".$payDate."' ";
			if (is_array($subSql) && count($subSql) > 0) $sql .= implode(',', $subSql);

			// ȯ����� ��� �谨.
			$stockStatus = 'n';
			if ($cfg['stepStock'] == '0' && ($result['OrderDate'] !== null && strlen($result['OrderDate']) > 0)) $stockStatus = 'y'; // ���谨�ܰ谡 �ֹ���, �ֹ����ڰ� �ִ°�� ���谨
			if ($cfg['stepStock'] == '1' && ($payDate !== null && strlen($payDate) > 0)) $stockStatus = 'y'; // ���谨�ܰ谡 �Աݽ�, �������ڰ� �ִ°�� ���谨

			$goodsSql =		" SELECT a.ipaysno, ai.goodsno, ai.optionsno, go.stock, ai.ea, ai.stockable, ai.stockStatus ";
			$goodsSql .=	" FROM ".$tblIpay." AS a ";
			$goodsSql .=	"	JOIN ".$tblIpayItem." AS ai ON a.ipaysno=ai.ipaysno";
			$goodsSql .=	"	JOIN ".GD_GOODS_OPTION." AS go ON ai.goodsno=go.goodsno AND ai.optionsno=go.optno and go_is_deleted <> '1' and go_is_display = '1' ";
			$goodsSql .=	" WHERE a.ipaycartnos='".$result['IpayCartNo']."'";

			$goodsRes = $db->query($goodsSql);
			while($data = $db->fetch($goodsRes)) {
				if ($data['stockable'] == 'n') $stockStatus = 'n'; // ��ǰ�� ������� �Ұ����� ���
				if ($stockStatus == 'y' && $data['stockStatus'] == 'n') {
					$newStock = $data['stock'] - $data['ea'];
					$newStockSql = " UPDATE ".GD_GOODS_OPTION." SET stock=".$newStock." WHERE goodsno='".$data['goodsno']."' AND optno='".$data['optionsno']."'";
					$db->query($newStockSql);

					$totStockSql = " SELECT SUM(stock) AS totStock FROM ".GD_GOODS_OPTION." WHERE goodsno='".$data['goodsno']."'  and go_is_deleted <> '1' and go_is_display = '1' ";
					$totRes = $db->fetch($totStockSql);

					$newStockSql = " UPDATE ".GD_GOODS." SET totStock=".$totRes['totStock'];
					if ($totRes['totStock'] == 0) $newStockSql .= ", runout=1 ";
					else $newStockSql .= ", runout=0 ";
					$newStockSql .= " WHERE goodsno='".$data['goodsno']."'";
					$db->query($newStockSql);

					unset($totRes);
				}
				$restypeSql = " UPDATE ".$tblIpayItem." SET responsetype='".$result['ResponseType']."', stockstatus='".$stockStatus."' ".$payDateSql." WHERE ipaysno='".$data['ipaysno']."'";
				$db->query($restypeSql);
			}
			unset($data);
			break;
		}
		case '1' : {
			$result['CancelDate'] = getXmlValueByName($index, $vals, 'CancelDate'); // �������
			$result['IpayItemNo'] = getXmlValueByName($index, $vals, 'IpayItemNo');	// ��ǰ��ȣ
			$ipayitemno = explode('_', $result['IpayItemNo']);

			$sqlcart = " SELECT ipaysno, paymenttype, stepstock FROM ".$tblIpay." WHERE ipaycartnos='".$result['IpayCartNo']."'";
			$cart = $db->fetch($sqlcart);

			// ȯ����� ��� ����.
			$goodsSql =		" SELECT ai.goodsno, ai.optionsno, go.stock, ai.ea, ai.stockable, ai.stockstatus ";
			$goodsSql .=	" FROM ".$tblIpayItem." AS ai ";
			$goodsSql .=	"	JOIN ".GD_GOODS_OPTION." AS go ON ai.goodsno=go.goodsno AND ai.optionsno=go.optno and go_is_deleted <> '1' and go_is_display = '1' ";
			$goodsSql .=	" WHERE ai.ipaysno='".$cart['ipaysno']."' AND ai.goodsno='".$ipayitemno[0]."' AND ai.optionsno='".$ipayitemno[1]."'";

			$goodsRes = $db->query($goodsSql);
			while($data = $db->fetch($goodsRes)) {
				if ($data['stockstatus'] == 'y') {
					$newStock = $data['stock'] + $data['ea'];
					$newStockSql = " UPDATE ".GD_GOODS_OPTION." SET stock=".$newStock." WHERE goodsno='".$data['goodsno']."' AND optno='".$data['optionsno']."'";
					$db->query($newStockSql);

					$totStockSql = " SELECT SUM(stock) AS totStock FROM ".GD_GOODS_OPTION." WHERE goodsno='".$data['goodsno']."'  and go_is_deleted <> '1' and go_is_display = '1' ";
					$totRes = $db->fetch($totStockSql);

					$newStockSql = " UPDATE ".GD_GOODS." SET totStock=".$totRes['totStock'];
					if ($totRes['totStock'] == 0) $newStockSql .= ", runout=1 ";
					else $newStockSql .= ", runout=0 ";
					$newStockSql .= " WHERE goodsno='".$data['goodsno']."'";
					$db->query($newStockSql);

					unset($totRes);
				}

				$restypeSql = " UPDATE ".$tblIpayItem." SET responsetype='".$result['ResponseType']."'";
				if ($result['CancelDate'] !== null && strlen($result['CancelDate']) > 0) $restypeSql .= ", canceldate='".$result['CancelDate']."' ";
				$restypeSql .= " WHERE ipaysno='".$cart['ipaysno']."' AND goodsno='".$data['goodsno']."' AND optionsno='".$data['optionsno']."'";
				$db->query($restypeSql);
			}
			unset($data);
			break;
		}
		default : exit;
	}

	if (is_array($subSql) && count($subSql) > 0) {
		$sql = " UPDATE ".$tblIpay." SET ";
		$sql .= implode(',', $subSql);
		$sql .= " WHERE ipaycartnos='".$result['IpayCartNo']."'";
		$db->query($sql);
	}
}
?>
