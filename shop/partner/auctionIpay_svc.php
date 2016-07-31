<?php
include "../lib/library.php";
include "../lib/load.class.php";
require "../lib/auctionIpay.class.php";
require "../conf/config.php";

/**
 * xml node value 이름으로 가져오기.
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
// 결제 :
<IpayResponse>
<ResponseType>0</ResponseType>					--0 결제, 1 취소
<AuctionOrderNo>584812713</AuctionOrderNo>		--옥션 주문번호
<IpayCartNo>41359</IpayCartNo>					--카트번호
<OrderDate>2010-11-18 09:03:00</OrderDate>		--주문일시
<BankCode>1520</BankCode>						--무통장 입금 은행코드(뒤 2자리를 활용하시면 됩니다.), 다른 결제 수단은 데이터가 없습니다.
<BankName>우리은행</BankName>                   --무통장 입금 은행명, 다른 결제 수단은 데이터가 없습니다.
<AccountNumb>00004834818456</AccountNumb>		--계좌번호, 다른 결제 수단은 데이터가 없습니다.
<PayDate></PayDate>								--결제일시 무통장 입금이 아닌 결제수단은 결제일시가 들어갑니다. 예) 2010-10-20 10:59:00
<PayPrice>22500</PayPrice>						--결제금액
<PaymentType>A</PaymentType>					--결제타입 (A=무통장입금 ,C=카드, M=모바일, D=실시간계좌이체)
<ExpireDate>2010-11-25</ExpireDate>				--무통장 입금의 결제마감일. 예)2010-11-25 는 2010-11-25 23:59:59 를 의미합니다.
<Emoney>0</Emoney>								--옥션 이머니 사용금액
<CardName></CardName>							--결제시 사용한 카드사명
<CardMonth></CardMonth>							--카드 할부계월 수 예)00=일시불, 03=3개월 할부
<NoInterestYN>N</NoInterestYN>					--카드 무이자 할부 여부 (Y=무이자 할부, N=유이자 할부)
<BuyerName>홍길동</BuyerName>					--구매자의 옥션 회원이름입니다. (은행 입금자 명 또는 물품 수령자 명과 다를 수 있습니다.)
<AuctionPayNo>507331315</AuctionPayNo>			--옥션 결제번호
</IpayResponse>

OR

<IpayResponse>
<ResponseType>0</ResponseType>					--0 결제, 1 취소
<IpayItemNo>1_12</IpayItemNo>
<IpayCartNo>625440</IpayCartNo>
<PaymentType>A</PaymentType>
<PayDate>2012-05-25 11:11:00</PayDate>
<AuctionOrderNo>722353796</AuctionOrderNo>
</IpayResponse>

// 취소 :
<IpayResponse>
<ResponseType>1</ResponseType>					--0 결제, 1 취소
<AuctionOrderNo>123456789</AuctionOrderNo>		--옥션 주문번호
<IpayCartNo>12345</IpayCartNo>					--카트번호
<AuctionItemNo>I000016033</AuctionItemNo>
<IpayItemNo>54</IpayItemNo>						--상품번호
<CancelDate>2010-10-02 오후 5:37:00 </CancelDate>
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

// XML 타입.
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

		// 환경따라 재고 삭감.
		$stockStatus = 'n';
		if ($cfg['stepStock'] == '0' && ($result['OrderDate'] !== null && strlen($result['OrderDate']) > 0)) $stockStatus = 'y'; // 재고삭감단계가 주문시, 주문일자가 있는경우 재고삭감
		if ($cfg['stepStock'] == '1' && ($payDate !== null && strlen($payDate) > 0)) $stockStatus = 'y'; // 재고삭감단계가 입금시, 결제일자가 있는경우 재고삭감

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
			if($data['stockable']=='n') $stockStatus = 'n'; // 상품의 재고연동이 불가능할 경우
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
			// 재고 삭감단계가 주문시이고 결제일자가있을때(이미 주문단계가 지난경우) 재고삭감은 이루어지지 않되 DB상에는 재고삭감이 일어난 주문건임을 알려줌
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
			$subSql[] = " stepstock='".$cfg['stepStock']."' "; // 재고연동 시기.

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

			// 환경따라 재고 삭감.
			$stockStatus = 'n';
			if ($cfg['stepStock'] == '0' && ($result['OrderDate'] !== null && strlen($result['OrderDate']) > 0)) $stockStatus = 'y'; // 재고삭감단계가 주문시, 주문일자가 있는경우 재고삭감
			if ($cfg['stepStock'] == '1' && ($payDate !== null && strlen($payDate) > 0)) $stockStatus = 'y'; // 재고삭감단계가 입금시, 결제일자가 있는경우 재고삭감

			$goodsSql =		" SELECT a.ipaysno, ai.goodsno, ai.optionsno, go.stock, ai.ea, ai.stockable, ai.stockStatus ";
			$goodsSql .=	" FROM ".$tblIpay." AS a ";
			$goodsSql .=	"	JOIN ".$tblIpayItem." AS ai ON a.ipaysno=ai.ipaysno";
			$goodsSql .=	"	JOIN ".GD_GOODS_OPTION." AS go ON ai.goodsno=go.goodsno AND ai.optionsno=go.optno and go_is_deleted <> '1' and go_is_display = '1' ";
			$goodsSql .=	" WHERE a.ipaycartnos='".$result['IpayCartNo']."'";

			$goodsRes = $db->query($goodsSql);
			while($data = $db->fetch($goodsRes)) {
				if ($data['stockable'] == 'n') $stockStatus = 'n'; // 상품의 재고연동이 불가능할 경우
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
			$result['CancelDate'] = getXmlValueByName($index, $vals, 'CancelDate'); // 취소일자
			$result['IpayItemNo'] = getXmlValueByName($index, $vals, 'IpayItemNo');	// 상품번호
			$ipayitemno = explode('_', $result['IpayItemNo']);

			$sqlcart = " SELECT ipaysno, paymenttype, stepstock FROM ".$tblIpay." WHERE ipaycartnos='".$result['IpayCartNo']."'";
			$cart = $db->fetch($sqlcart);

			// 환경따라 재고 복원.
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
