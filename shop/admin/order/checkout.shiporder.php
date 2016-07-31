<?php
/**
 * 네이버체크아웃 주문 > 발송처리
 * @author sunny, oneorzero
 */
$location = '네이버체크아웃 주문 > 발송처리';
include '../_header.php';

$page = $_GET['page'] ? $_GET['page'] : 1;
$sk = $_GET['sk'];
$sv = $_GET['sv'];
$code = (array)$_GET['code'];
$orddt = $_GET['orddt'];

$ORDER_OrderDateTimeStart = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[0]);
$ORDER_OrderDateTimeEnd = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[1]);

$arWhere=array();
$arWhere[] = "ORDER_OrderStatusCode in ('OD0007','OD0008')";

if($sv) {
	$sv=$db->_escape($sv);
	switch($sk) {
		case 'ORDER_OrderID':
			$arWhere[] = "ORDER_OrderID = '{$sv}'";
		break;
		case 'ORDER_OrdererName':
			$arWhere[] = "ORDER_OrdererName = '{$sv}'";
		break;
		case 'ORDER_OrdererID':
			$arWhere[] = "ORDER_OrdererID = '{$sv}'";
		break;
		case 'SHIPPING_Recipient':
			$arWhere[] = "SHIPPING_Recipient = '{$sv}'";
		break;
	}
}

if($ORDER_OrderDateTimeStart && $ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime between [s] and [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}
elseif($ORDER_OrderDateTimeStart) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime >= [s]',$ORDER_OrderDateTimeStart);
}
elseif($ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('ORDER_OrderDateTime <= [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}

$strWhere = 'where '.implode(' and ',$arWhere);

$query = "
	select
		orderNo,
		ORDER_OrderID,
		ORDER_OrderDateTime,
		ORDER_OrderStatusCode,
		ORDER_OrderStatus,
		ORDER_OrdererName,
		ORDER_OrdererID,
		ORDER_MallOrderAmount,
		SHIPPING_Recipient
	from
		gd_navercheckout_order
	{$strWhere}
	order by
		ORDER_OrderDateTime desc
";
$orderList = $db->_select_page(10,$page,$query);

$orderNoList =array();
foreach($orderList['record'] as $eachOrder) {
	$orderNoList[]='"'.$eachOrder['orderNo'].'"';
}
if(count($orderNoList)) {
	$strWhere = implode(',',$orderNoList);
	$query = "select * from gd_navercheckout_order_product where orderNo in ($strWhere) order by orderNo asc,seq asc";
	$result = $db->_select($query);
	$orderProductList=array();
	foreach($result as $eachResult) {
		$orderProductList[$eachResult['orderNo']][]=$eachResult;
	}
}
?>
<? include('checkout.common.php'); ?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	$$(".selShippingCompany").each(function(item){
		Event.observe(item, 'change', function(event) {
			var element = $(Event.element(event));
			if(element.value=='z_etc' || element.value=='z_quick' || element.value=='z_direct' || element.value=='z_visit' || element.value=='z_delegation') {
				element.up(1).select(".iptTrackingNumber")[0].value='';
				element.up(1).select(".iptTrackingNumber")[0].disabled=true;
				element.up(1).select(".iptTrackingNumber")[0].style.backgroundColor="#cccccc";
			}
			else {
				element.up(1).select(".iptTrackingNumber")[0].disabled=false;
				element.up(1).select(".iptTrackingNumber")[0].style.backgroundColor="#ffffff";
			}
		});
	});
});


function apiCall() {
	var test=false;
	var testValid=true;
	var requestSeq=0;
	$('processForm').childElements().invoke('remove');
	$$('input[name="orderNo[]"]').each(function(item){
		if(item.checked && testValid==true) {
			test=true;
			var eleShippingCompleteDate = $$('input[name="ShippingCompleteDate['+item.value+']"]')[0];
			var eleShippingCompany = $$('select[name="ShippingCompany['+item.value+']"]')[0];
			var eleTrackingNumber = $$('input[name="TrackingNumber['+item.value+']"]')[0];

			if(eleShippingCompleteDate.value.length==0) {
				testValid=false;
				alert('선택하신 주문을 발송처리하기 위해서는 배송일을 입력하셔야 합니다');
				return;
			}
			if(eleShippingCompany.selectedIndex==0) {
				testValid=false;
				alert('선택하신 주문을 발송처리하기 위해서는 배송방법을 선택하셔야 합니다');
				return;
			}
			var tmp = eleShippingCompany.options[eleShippingCompany.selectedIndex].value;
			if(!(tmp=='z_etc' || tmp=='z_quick' || tmp=='z_direct' || tmp=='z_visit' || tmp=='z_delegation')) {
				if(eleTrackingNumber.value.length==0) {
					testValid=false;
					alert('선택하신 주문을 발송처리하기 위해서는 송장번호를 입력하셔야 합니다');
					return;
				}
			}
			$('processForm').insert(new Element('input',
				{'type':'hidden','name':'request['+requestSeq+'][orderNo]','value':item.value}
			));
			$('processForm').insert(new Element('input',
				{'type':'hidden','name':'request['+requestSeq+'][ShippingCompleteDate]','value':eleShippingCompleteDate.value}
			));
			$('processForm').insert(new Element('input',
				{'type':'hidden','name':'request['+requestSeq+'][ShippingCompany]','value':eleShippingCompany.options[eleShippingCompany.selectedIndex].value}
			));
			$('processForm').insert(new Element('input',
				{'type':'hidden','name':'request['+requestSeq+'][TrackingNumber]','value':eleTrackingNumber.value}
			));
			requestSeq++;
		}
	});

	if(testValid==false) {
		return;
	}

	if(test==false) {
		alert("주문을 선택해주세요");
		return;
	}

	customPopupLayer('about:blank',780,500);
	$('processForm').submit();
}
</script>
<div class="title title_top">발송처리 <span>주문에 대한 확인이 완료된 주문들을 발송처리하는 단계입니다.</span></div>
<form name="frmSearch" method="get">

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td><span class="small1">키워드검색</span></td>
	<td>
		<select name="sk">
			<option value="ORDER_OrderID" <?=frmSelected($_GET['sk'],'ORDER_OrderID')?>>주문번호</option>
			<option value="ORDER_OrdererName" <?=frmSelected($_GET['sk'],'ORDER_OrdererName')?>>주문자이름</option>
			<option value="ORDER_OrdererID" <?=frmSelected($_GET['sk'],'ORDER_OrdererID')?>>주문자아이디(네이버)</option>
			<option value="SHIPPING_Recipient" <?=frmSelected($_GET['sk'],'SHIPPING_Recipient')?>>수취인 이름</option>
		</select>
		<input type="text" name="sv" value="<?=htmlspecialchars($_GET['sv'])?>" class="line">
	</td>
</tr>
<tr>
	<td><span class="small1">주문일시</span></td>
	<td>
		<input type=text name=orddt[] value="<?=$_GET[orddt][0]?>" onclick="calendar(event)" size=12 class=line> -
		<input type=text name=orddt[] value="<?=$_GET[orddt][1]?>" onclick="calendar(event)" size=12 class=line>
	</td>
</tr>
</table>

<table width="100%">
<tr>
	<td align="center">
	<input type="image" src="../img/btn_search2.gif" border="0" style="border:0px">
	</td>
</tr>
</table>
</form>

<form id="processForm" action="checkout.api.ShipOrder.php" target="processLayerForm" method="post">
</form>

<form id="chkFrm">
<br><br>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th><span onclick="chkAllOrderNO()" style="cursor:pointer">선택</a></th>
	<th>주문번호</th>
	<th>상태</th>
	<th>주문일</th>
	<th>주문자</th>
	<th>상품명</th>
	<th>옵션</th>
	<th>수량</th>
	<th>결제금액</th>
	<th>배송일</th>
	<th>배송방법</th>
	<th>송장번호</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col align="center" width="40"/>
<col align="center" width="120"/>
<col align="center" width="90"/>
<col align="center" width="110" />
<col align="center" width="100" />
<col align="center" />
<col align="center" width="90" />
<col align="center" width="30" />
<col align="center" width="80" />
<col align="center" width="70" />
<col align="center" width="75" />
<col align="center" width="90" />

<? foreach($orderList['record'] as $eachOrder): ?>
<?
	$eachOrder['ORDER_OrderDateTime'] = preg_replace('/^\d{2}(\d{2})-(\d+)-(\d+) (\d+):(\d+):(\d+)$/','$1.$2.$3 $4:$5',$eachOrder['ORDER_OrderDateTime']);
	$orderProduct = $orderProductList[$eachOrder['orderNo']];
	$firstProduct = array_shift($orderProduct);
?>
<tr>
	<td class="noline" ><input type="checkbox" name="orderNo[]" value="<?=$eachOrder['orderNo']?>"></td>
	<td height="23"><a href="checkout.orderdetail.php?orderNo=<?=$eachOrder['orderNo']?>"><?=$eachOrder['ORDER_OrderID']?></a></td>
	<td><?=$eachOrder['ORDER_OrderStatus']?></td>
	<td><?=$eachOrder['ORDER_OrderDateTime']?></td>
	<td><?=$eachOrder['ORDER_OrdererName']?>(<?=$eachOrder['ORDER_OrdererID']?>)</td>
	<td style="font-size:8pt"><?=$firstProduct['ProductName']?></td>
	<td><?=$firstProduct['ProductOption']?></td>
	<td><?=$firstProduct['Quantity']?></td>
	<td><?=number_format($eachOrder['ORDER_MallOrderAmount'])?></td>
	<td><input type="text" name="ShippingCompleteDate[<?=$eachOrder['orderNo']?>]" value="" onclick="calendar(event)" readonly style="width:95%"></td>
	<td>
		<select name="ShippingCompany[<?=$eachOrder['orderNo']?>]" style="width:95%;font-size:7pt;" class="selShippingCompany">
		<option value="">(선택)</option>
		<option value="korex">대한통운</option>
		<option value="cjgls">CJGLS</option>
		<option value="sagawa">SC 로지스</option>
		<option value="yellow">옐로우캡</option>
		<option value="kgb">로젠택배</option>
		<option value="dongbu">동부익스프레스택배</option>
		<option value="EPOST">우체국택배</option>
		<option value="hanjin">한진택배</option>
		<option value="hyundai">현대택배</option>
		<option value="kgbls">KGB 택배</option>
		<option value="z_etc">기타 택배</option>
		<option value="z_quick">퀵서비스</option>
		<option value="z_direct">직배송</option>
		<option value="z_visit">방문 수령</option>
		<option value="z_post">우편 등기</option>
		<option value="z_delegation">업체별 배송</option>
		<option value="kdexp">경동택배</option>
		</select>
	</td>
	<td><input type="text" name="TrackingNumber[<?=$eachOrder['orderNo']?>]" value="" style="width:95%" class="iptTrackingNumber"></td>
</tr>
<? foreach($orderProduct as $eachProduct): ?>
<tr>
	<td height="23"></td>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
	<td><?=$eachProduct['ProductName']?></td>
	<td><?=$eachProduct['ProductOption']?></td>
	<td><?=$eachProduct['Quantity']?></td>
	<td></td>
	<td></td>
	</tr>
<? endforeach; ?>
<tr><td height="4"></td></tr>
<tr><td colspan="16" class="rndline"></td></tr>
</tr>
<? endforeach; ?>

</table>


<? $pageNavi = &$orderList['page']; ?>
<div align="center" class="pageNavi ver8">
	<? if($pageNavi['prev']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['prev'])?>">◀ </a>
	<? endif; ?>
	<? foreach($pageNavi['page'] as $v): ?>
		<? if($v==$pageNavi['nowpage']): ?>
			<a href="?<?=getvalue_chg('page',$v)?>"><?=$v?></a>
		<? else: ?>
			<a href="?<?=getvalue_chg('page',$v)?>">[<?=$v?>]</a>
		<? endif; ?>
	<? endforeach; ?>
	<? if($pageNavi['next']): ?>
		<a href="?<?=getvalue_chg('page',$pageNavi['next'])?>">▶</a>
	<? endif; ?>
</div>

<input type="button" value=" 선택한 주문 발송처리하기 " onclick="apiCall()">
</form>


<div style="margin-top:30px;"></div>
<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문에 대한 확인(발주)이 완료된 주문입니다. 발송처리해 주세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>주문을 선택하고 배송정보(배송일, 배송방법, 송장번호)를 입력한 후 주문발송 버튼을 눌러주세요.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include '../_footer.php'; ?>