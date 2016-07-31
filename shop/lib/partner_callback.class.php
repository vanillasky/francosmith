<?
class Partner_callback
{
	function about_callback($ordno,$settlekind){

		if($_COOKIE['ref'] == "about_ad"){			// 상품광고/박스광고/키워드광고
			$about_url = "http://sba.about.co.kr/ClickCallBack.aspx";
		}else if( $_COOKIE['ref'] == "about_open"){	// 서비스/디스플레이광고
			$about_url = "http://www.about.co.kr/ClickCallBack.aspx";
			$type = "&cpcType=".$_COOKIE['cpcType'];
		}else {
			return;
		}

		$tmp = $this->getOrderItem($ordno);

		switch ($settlekind)
		{
			case "c" : $paytype="CARD";
			break;
			case "h" : $paytype="MOBI";
			break;
			case "p" : $paytype="PONT";
			break;
			default : $paytype="CASH";
			break;
		}

		$about_url .= "?clickDate=".$_COOKIE['clickDate']."&clickNo=".$_COOKIE['clickNo'].$type."&MItemNos=".@implode(',',$tmp['goodsno'])."&MAmounts=".@implode(',',$tmp[price])."&PayType=".$paytype."&MOrderN=".$ordno;
		$ret = @readurl($about_url);
	}

	function getOrderItem($ordno){

		global $db;

		$query = "select goodsno, ea, price from ".GD_ORDER_ITEM." where ordno='".$ordno."'";
		$res = $db->query($query);
		while($row = $db->fetch($res)){
			/*if($row[ea]){
				for($i=0;$i< $row['ea'];$i++){
					$tmp['goodsno'][] = $row['goodsno'];
					$tmp['price'][] = $row['price'];
				}
			}*/
			$tmp['goodsno'][] = $row['goodsno'];
			$tmp['price'][] = $row['price'] * $row['ea'];
		}

		return $tmp;
	}
}
?>