<?
$location = "옥션 iPay 결제 > 옥션 iPay 결제 주문리스트";
include "../_header.php";
include "../../lib/page.class.php";

$tbIpay = 'gd_auctionipay';
$tbIpayItem = 'gd_auctionipay_item';

$selected['skey'][$_GET['skey']] = ' selected="selected" ';
$selected['paymenttype'][$_GET['paymenttype']] = ' selected="selected" ';
$selected['responsetype'][$_GET['responsetype']] = ' selected="selected" ';

?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
<!--
var arrowImg = {
	"up" : new Image(),
	"down" : new Image()
};
arrowImg.up.src = "../img/btn_up01.gif";
arrowImg.down.src = "../img/btn_down01.gif";

function expand(n, val) {
	if (n) {
		var bt = document.getElementById("expandBt"+n);
		var spans = document.getElementById("expandInfo"+n).getElementsByTagName("SPAN");

		if (val) {
			val = (val == "up")? "down" : "up";
		}
		else {
			val = (bt.src == arrowImg.up.src)? "up" : "down";
		}

		if (val == "up") {
			bt.src = arrowImg.down.src;
			for(var j = 0; j < spans.length; j++) {
				if (spans[j].tagName == 'SPAN') spans[j].style.display = "block";
			}
		}
		else {
			bt.src = arrowImg.up.src;
			for(var j = 0; j < spans.length; j++) {
				if (spans[j].tagName == 'SPAN') spans[j].style.display = "none";
			}
		}
	}
	else {
		var bt = document.getElementById("expandBt");
		var val = (bt.src == arrowImg.up.src)? "down" : "up";
		bt.src = arrowImg[val].src;
		var imgs = document.getElementById("tblList").getElementsByTagName("IMG");
		for(var i = 0; i < imgs.length; i++) {
			if (imgs[i].id.match(/expandBt[0-9]+/g)) {
				btidx = imgs[i].id.replace(/[^0-9]*/g, "");
				expand(btidx, val);
			}
		}

	}
}

//-->
</script>

<div style="width:100%">
	<div class="title title_top">옥션 iPay 결제 주문리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=22')"><img src="../img/btn_q.gif"></a></div>

	<form name="frmSearch">
	<table class="tb">
	<col class="cellC" /><col class="cellL" />
	<tr>
		<td>키워드검색</td>
		<td>
			<select name="skey">
				<option value="">통합검색</option>
				<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>상품명</option>
				<option value="auctionpayno" <?=$selected['skey']['auctionpayno']?>>옥션결제번호</option>
				<option value="buyername" <?=$selected['skey']['buyername']?>>주문자명</option>
			</select>
			<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		</td>
	</tr>
	<tr>
		<td>결제방법</td>
		<td>
			<select name="paymenttype">
				<option value="">전체</option>
				<option value="A" <?=$selected['paymenttype']['A']?>>무통장입금</option>
				<option value="C" <?=$selected['paymenttype']['C']?>>카드</option>
				<option value="M" <?=$selected['paymenttype']['M']?>>핸드폰</option>
				<option value="D" <?=$selected['paymenttype']['D']?>>실시간계좌이체</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>처리상태</td>
		<td>
			<select name="responsetype">
				<option value="">전체</option>
				<option value="orderComplete" <?=$selected['responsetype']['orderComplete']?>>주문완료</option>
				<option value="payComplete" <?=$selected['responsetype']['payComplete']?>>결제완료</option>
				<option value="orderCancel" <?=$selected['responsetype']['orderCancel']?>>주문취소</option>
				<option value="partCancel" <?=$selected['responsetype']['partCancel']?>>부분취소</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>주문일검색</td>
		<td>
			<input type=text name="start_dt" value="<?=$_GET['start_dt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" required />
			-
			<input type=text name="end_dt" value="<?=$_GET['end_dt']?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline" size="8" maxlength="8" required />
		</td>
	</tr>
	</table>
	<div style="height:20px;"></div>
	<div style="text-align:center" class="noline"><input type="image" src="../img/btn_search2.gif" /></div>
	</form>
	<div style="height:20px;"></div>

	<table width=100% cellpadding=0 cellspacing=0 id="tblList">
	<tr class=rndbg>
		<th width=100>번호 <img id="expandBt" style="cursor:pointer" onclick="expand()" src="../img/btn_up01.gif"></th>
		<th>상품</th>
		<th width=150>금액 / 배송료</th>
		<th width=130>주문일</th>
		<th width=80>주문자</th>
		<th width=80>옥션결제번호</th>
		<th width=80>결제방법</th>
		<th width=80>처리상태</th>
	</tr>
	<tr><td class=rnd colspan=8></td></tr>
	<?
		$tbl = $tbIpay . " AS a ";
		$where = array("paymenttype IS NOT NULL");

		// 키워드 검색
		if ($_GET['sword']) {
			switch($_GET['skey']) {
				case 'goodsnm': {
					$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND goodsnm LIKE '%".$_GET['sword']."%')";
					break;
				}
				case '' : {
					$tmpWhere[] = "(EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND goodsnm LIKE '%".$_GET['sword']."%'))";
					$tmpWhere[] = "(auctionpayno LIKE '%" . $_GET['sword'] . "%')";
					$tmpWhere[] = "(buyername LIKE '%" . $_GET['sword'] . "%')";
					$where[] = implode($tmpWhere, ' OR ');
					unset($tmpWhere);
					break;
				}
				default: {
					$where[] = $_GET['skey'] . " LIKE '%" . $_GET['sword'] . "%' ";
					break;
				}
			}
		}

		// 결제방법
		if ($_GET['paymenttype']) {
			$where[] = "paymenttype='".$_GET['paymenttype']."'";
		}

		// 처리상태
		switch($_GET['responsetype']) {
			case 'orderComplete' : {
				$where[] = "NOT EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='1')";
				$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='0' AND paydate IS NULL)";
				break;
			}
			case 'payComplete' : {
				$where[] = "NOT EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='1')";
				$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='0' AND paydate IS NOT NULL)";
				break;
			}
			case 'orderCancel' : {
				$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='1')";
				$where[] = "NOT EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='0')";
				break;
			}
			case 'partCancel' : {
				$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='0')";
				$where[] = "EXISTS(SELECT * FROM ".$tbIpayItem." WHERE ipaysno=a.ipaysno AND responsetype='1')";
				break;
			}
		}

		if ($_GET['start_dt']) {
			$where[] = "((orderdate IS NOT NULL AND DATE_FORMAT(orderdate, '%Y-%m-%d') >= '".substr($_GET['start_dt'], 0, 4)."-".substr($_GET['start_dt'], 4, 2)."-".substr($_GET['start_dt'], 6, 2)."') OR (orderdate IS NULL AND DATE_FORMAT(regdt, '%Y-%m-%d') >= '".substr($_GET['start_dt'], 0, 4)."-".substr($_GET['start_dt'], 4, 2)."-".substr($_GET['start_dt'], 6, 2)."'))";
		}
		if ($_GET['end_dt']) {
			$where[] = "((orderdate IS NOT NULL AND DATE_FORMAT(orderdate, '%Y-%m-%d') <= '".substr($_GET['end_dt'], 0, 4)."-".substr($_GET['end_dt'], 4, 2)."-".substr($_GET['end_dt'], 6, 2)."') OR (orderdate IS NULL AND DATE_FORMAT(regdt, '%Y-%m-%d') <= '".substr($_GET['end_dt'], 0, 4)."-".substr($_GET['end_dt'], 4, 2)."-".substr($_GET['end_dt'], 6, 2)."'))";
		}

		$pg = new Page($_GET['page'],10);
		$pg->setQuery($tbl,$where,"ipaysno desc");
		$pg->exec();

		$res = $db->query($pg->query);
		while($data = $db->fetch($res)) {
			if (!$data['orderdate']) $data['orderdate'] = $data['regdt'];
			$itemSql = " SELECT ai.*, g.img_s FROM ".$tbIpayItem." AS ai JOIN ".GD_GOODS." AS g ON ai.goodsno=g.goodsno WHERE ipaysno=".$data['ipaysno'];
			$itemRes = $db->query($itemSql);
			$itemCnt = $db->count_($itemRes);
			$itemResType = 0;
			$itemPayCnt = 0;

			$idx = $pg->idx--;

			$goodsStr = '<div id="expandInfo'.$idx.'" style="margin-bottom:0px;">';
			$cancelStyle = 'color:#BBBBBB;';

			while($itemData=$db->fetch($itemRes)) {
				$itemResType += $itemData['responsetype'];
				if ($itemData['paydate']) $itemPayCnt++;
				$goodsImg = goodsimg($itemData['img_s'],40,'',1);

				$goodsStr .= '<div style="display:inline-block; margin-top:5px; margin-bottom:5px;">';
				$goodsStr .= '<div style="float:left;"><a href="../../goods/goods_view.php?goodsno='.$itemData['goodsno'].'" target="_blank">'.$goodsImg.'</a></div>';
				$goodsStr .= '<div style="float:left;">';
				$goodsStr .= '<div><a href="javascript:popup(\'../goods/popup.register.php?mode=modify&goodsno='.$itemData['goodsno'].'\', 850, 600)" '.(($itemData['responsetype']=='1')? ' style="'.$cancelStyle.'" ':'').' >'.$itemData['goodsnm'].'</a>'.(($itemData['responsetype']=='1')? ' <label style="'.$cancelStyle.'">('.$itemData['canceldate'].'취소)</label>':'').'</div>';
				$goodsStr .= '<span style="margin-left:10px; margin-bottom:15px; display:none; '.(($itemData['responsetype']=='1')? $cancelStyle:'').' ">';
				if ($itemData['option']) $goodsStr .= '- 옵션: '.$itemData['option'].'<br/>';
				$goodsStr .= '- 수량: <font class=ver8 '.(($itemData['responsetype']=='1')? $cancelStyle:'').' >'.$itemData['ea'].'</font>개, 가격: <font class=ver8 '.(($itemData['responsetype']=='1')? $cancelStyle:'').' >'.number_format($itemData['price']).'</font>';
				$goodsStr .= '</span>';
				$goodsStr .= '</div>';
				$goodsStr .= '</div>';
			}
			$goodsStr .= '</div>';

			$strResponseType = '';
			if ($itemResType > 0) { // 주문물품중 취소내용있음.
				if ($itemResType == $itemCnt) $strResponseType = '주문취소';
				else $strResponseType = '부분취소';
			}
			else { // 주문/결제 완료
				if ($itemPayCnt == $itemCnt) $strResponseType = '결제완료';
				else $strResponseType = '주문완료';
			}

			unset($itemResType);
	?>
	<tr><td height=3 colspan=8></td></tr>
	<tr height="30">
		<td align="center"><font class=ver8 color=444444><?=$idx?> <img id="expandBt<?=$idx?>" style="cursor:pointer" onclick="expand(<?=$idx?>)" src="../img/btn_up01.gif"></font></td>
		<td><?=$goodsStr?></td>
		<td align="center">
			<font class=ver8 color=444444><?=number_format($data['payprice'])?></font><br />
			<?
				switch($data['shippingtype']) {
					case '1': { echo '무료'; break; }
					case '2': { echo '착불'; break; }
					case '3': { echo '선불 <font class=ver8 color=444444>'.number_format($data['shippingprice']).'</font>'; break; }
				}
			?>
		</td>
		<td align="center"><font class=ver8 color=444444><?=$data['orderdate']?></font></td>
		<td align="center"><?=$data['buyername']?></td>
		<td align="center"><?=$data['auctionpayno']?></td>
		<td align="center">
			<?
				switch($data['paymenttype']) {
					case 'A' : { echo '무통장입금'; break; }
					case 'C' : { echo '카드'; break; }
					case 'M' : { echo '모바일'; break; }
					case 'D' : { echo '실시간계좌이체'; break; }
				}
			?>
		</td>
		<td align="center"><?=$strResponseType?></td>
	</tr>
	<tr><td height=4 colspan=8></td></tr>
	<tr><td colspan=8 class=rndline></td></tr>
	<?
		}
	?>
	</table>

	<div class="pageNavi" align=center><font class=ver8><?=$pg->page[navi]?></div>
	<div style="padding-top:15px"></div>
</div>

<div style="clear:both;" id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
	<tr>
		<td>
			<div>
				옥션 iPay 주문 관리는 옥션의 판매관리에서 가능합니다.<br />
				금액은 선불 배송료가 포함된 금액입니다.<br />
				상품의 재고연동은 결제당시의 재고삭감단계와 해당 상품 재고량 연동 여부에 따라 적용됩니다.<br />
				무통장입금 외의 결제 수단의 경우는 자동으로 재고 가감이 반영됩니다.<br />
				- 입금확인 : 무통장입금일 때, 입금이 확인되었을 경우 클릭하시면 해당 상품의 재고가 수정됩니다.<br />
				- 주문취소 : 무통장입금일 때, 주문이 취소되었을 경우 클릭하시면 해당 상품의 재고가 복구됩니다.<br />
			</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
	cssRound('MSG01');
</script>
<? include "../_footer.php"; ?>