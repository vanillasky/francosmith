<?php
$location = '네이버체크아웃 주문 > 4.0 마이그레이션';
include '../_header.php';

$page = $_GET['page'] ? $_GET['page'] : 1;
$sk = $_GET['sk'];
$sv = $_GET['sv'];
$code = (array)$_GET['code'];
$orddt = $_GET['orddt'];

$ORDER_OrderDateTimeStart = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[0]);
$ORDER_OrderDateTimeEnd = preg_replace('/^(\d{4})(\d{2})(\d{2})$/','$1-$2-$3',$orddt[1]);

$arWhere=array();

$arWhere[] = "O.migrated = '0'";

if($sv) {
	$sv=$db->_escape($sv);
	switch($sk) {
		case 'ORDER_OrderID':
			$arWhere[] = "O.ORDER_OrderID = '{$sv}'";
		break;
		case 'ORDER_OrdererName':
			$arWhere[] = "O.ORDER_OrdererName = '{$sv}'";
		break;
		case 'ORDER_OrdererID':
			$arWhere[] = "O.ORDER_OrdererID = '{$sv}'";
		break;
		case 'ORDER_MallMemberID':
			$arWhere[] = "O.ORDER_MallMemberID = '{$sv}'";
		break;
		case 'SHIPPING_Recipient':
			$arWhere[] = "O.SHIPPING_Recipient = '{$sv}'";
		break;
	}
}
if(count($code)) {
	$tmp=array();
	foreach($code as $eachCode) {
		$tmp[] = '"'.$eachCode.'"';
	}
	$tmp = implode(',',$tmp);
	$arWhere[] = "O.ORDER_OrderStatusCode in ({$tmp})";
}

if($ORDER_OrderDateTimeStart && $ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime between [s] and [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}
elseif($ORDER_OrderDateTimeStart) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime >= [s]',$ORDER_OrderDateTimeStart);
}
elseif($ORDER_OrderDateTimeEnd) {
	$arWhere[] = $db->_query_print('O.ORDER_OrderDateTime <= [s]',$ORDER_OrderDateTimeStart,$ORDER_OrderDateTimeEnd);
}

if(count($arWhere)) {
	$strWhere = 'where '.implode(' and ',$arWhere);
}

$query = "
	select
		O.orderNo,
		O.ORDER_OrderID,
		O.ORDER_OrderDateTime,
		O.ORDER_OrderStatusCode,
		O.ORDER_OrderStatus,
		O.ORDER_OrdererName,
		O.ORDER_OrdererID,
		O.ORDER_MallOrderAmount,
		O.SHIPPING_Recipient,

		COUNT(OP.seq) AS PO_cnt,
		OP.ProductName,
		OP.ProductOption

	FROM
		gd_navercheckout_order AS O

	INNER JOIN gd_navercheckout_order_product AS OP
	ON O.orderNo = OP.orderNo

	{$strWhere}

	GROUP BY O.orderNo

	order by
		O.orderNo desc
";

$orderList = $db->_select_page(20,$page,$query);
?>
<script type="text/javascript" src="./checkout.js"></script>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		var flagCancel=true;
		var flagReturn=true;
		var flagExchange=true;
		Event.observe($("btnAllCancel"), 'click', function(event) {
			var element = Event.element(event);
			var checkValue = ['OD0003','OD0004','OD0005','OD0006'];
			var tmp;
			$$("input[name='code[]']").each(function(item){
				tmp=false;
				checkValue.each(function(v){
					if(item.value==v) {
						tmp=true;
					}
				});
				if(tmp) item.checked=flagCancel;
			});
			flagCancel = !flagCancel;
		});
		Event.observe($("btnAllExchange"), 'click', function(event) {
			var element = Event.element(event);
			var checkValue = ['OD0014','OD0015','OD0016','OD0017','OD0018','OD0019','OD0020','OD0021','OD0022','OD0023','OD0024','OD0025'];
			var tmp;
			$$("input[name='code[]']").each(function(item){
				tmp=false;
				checkValue.each(function(v){
					if(item.value==v) {
						tmp=true;
					}
				});
				if(tmp) item.checked=flagExchange;
			});
			flagExchange = !flagExchange;

		});
		Event.observe($("btnAllReturn"), 'click', function(event) {
			var element = Event.element(event);
			var checkValue = ['OD0026','OD0027','OD0028','OD0029','OD0030','OD0031','OD0032','OD0033','OD0034','OD0035'];
			var tmp;
			$$("input[name='code[]']").each(function(item){
				tmp=false;
				checkValue.each(function(v){
					if(item.value==v) {
						tmp=true;
					}
				});
				if(tmp) item.checked=flagReturn;
			});
			flagReturn = !flagReturn;

		});
	});

function fnGetMigratedProductOrderList() {

	if ($$('input[name="OldOrderID[]"]:checked').size() < 1) {
		alert("데이터 변환할 주문건을 선택해 주세요.");
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;
	f.action = './checkout.api.migration.process.php';
	f.submit();

}

</script>
<style>
	.el-navercheckout-gogosing {border:4px solid #dce1e1;padding:10px;margin:0 0 10px 0;}
	.el-navercheckout-gogosing h3 {font-size:12px;font-weight:bold;color:#bf0000;margin:0 0 10px 0;}
	.el-navercheckout-gogosing p {margin:0;color:#666666;}
	.el-navercheckout-gogosing dl {}
	.el-navercheckout-gogosing dl dt,
	.el-navercheckout-gogosing dl dd {font-size:12px;margin:0;color:#666666;}
	.el-navercheckout-gogosing dl dt {font-weight:bold;}
	.el-navercheckout-gogosing dl dd {margin:0 0 0 3px;}


</style>
<div class="title title_top">4.0 마이그레이션</div>

<div class="el-navercheckout-gogosing">
	<h3>네이버체크아웃 4.0 버전 업그레이드 안내</h3>
	<p>
		네이버체크아웃 주문 서비스가 4.0으로 버전이 업그레이드 되었습니다. <br/>
		관련 유의사항을 반드시 확인하여 주세요.
	</p>
	<dl>
		<dt>[ 4.0 주문 데이터 관련 ]</dt>
		<dd>네이버체크아웃 4.0 오픈(3월 15일)과 동시에 기존의 3.0버전 서비스가 더 이상 제공되지 않습니다.</dd>
		<dd>4.0 오픈(3월 15일) 이후 발생된 주문건은 "네이버체크아웃 4.0" 주문관리에서 확인 및 관리가 가능하십니다.</dd>
	</dl>
	<dl>
		<dt>[ (구)3.0 기존 주문 데이터 관련 ]</dt>
		<dd>4.0 오픈(3월 15일) 이전의 3.0 주문건은 자동으로 연동되지 않습니다.</dd>
		<dd>기존 주문데이터를 4.0 관리페이지에서 조회 및 관리 하시기 위해서는 별도의 변환 작업이 필요합니다.</dd>
	</dl>
	<dl>
		<dt>[ 데이터 변환 작업 ]</dt>
		<dd>아래의 3.0 주문 리스트에서 4.0으로 주문데이터 변환하고자 하시는 주문건을 선택하신후, [주문데이터변환] 버튼을 클릭하여 데이터를 변환하여 주세요.</dd>
		<dd>이후 변환된 주문건은 "네이버체크아웃 4.0" 주문관리에서 확인 및 관리가 가능하십니다.</dd>
	</dl>
</div>



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
				<option value="ORDER_MallMemberID" <?=frmSelected($_GET['sk'],'ORDER_MallMemberID')?>>주문자아이디(쇼핑몰)</option>
				<option value="SHIPPING_Recipient" <?=frmSelected($_GET['sk'],'SHIPPING_Recipient')?>>수취인 이름</option>
			</select>
			<input type="text" name="sv" value="<?=htmlspecialchars($_GET['sv'])?>" class="line">
		</td>
	</tr>
	<tr>
		<td valign="top"><span class="small1">주문상태</span></td>
		<td class="noline">
		<table >
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0001" <?=(in_array('OD0001',$code)?'checked':'')?>>입금 대기</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0002" <?=(in_array('OD0002',$code)?'checked':'')?>>결제 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0003" <?=(in_array('OD0003',$code)?'checked':'')?>>주문 취소(입금 기한 만료)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0004" <?=(in_array('OD0004',$code)?'checked':'')?>>주문 취소(이용자 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0005" <?=(in_array('OD0005',$code)?'checked':'')?>>주문 취소(직권 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0006" <?=(in_array('OD0006',$code)?'checked':'')?>>판매 취소</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0007" <?=(in_array('OD0007',$code)?'checked':'')?>>발송 처리 요청</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0008" <?=(in_array('OD0008',$code)?'checked':'')?>>미발송</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0009" <?=(in_array('OD0009',$code)?'checked':'')?>>집화 예정</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0010" <?=(in_array('OD0010',$code)?'checked':'')?>>배송 정보 승인 대기</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0011" <?=(in_array('OD0011',$code)?'checked':'')?>>배송 정보 오류</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0012" <?=(in_array('OD0012',$code)?'checked':'')?>>배송 중</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0013" <?=(in_array('OD0013',$code)?'checked':'')?>>배송 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0014" <?=(in_array('OD0014',$code)?'checked':'')?>>교환 신청 중</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0015" <?=(in_array('OD0015',$code)?'checked':'')?>>교환 신청 승인</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0016" <?=(in_array('OD0016',$code)?'checked':'')?>>교환 신청 거절</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0017" <?=(in_array('OD0017',$code)?'checked':'')?>>이의 제기(교환 신청 거절)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0018" <?=(in_array('OD0018',$code)?'checked':'')?>>반송 중(교환)</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0019" <?=(in_array('OD0019',$code)?'checked':'')?>>교환 심사 중</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0020" <?=(in_array('OD0020',$code)?'checked':'')?>>교환 승인</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0021" <?=(in_array('OD0021',$code)?'checked':'')?>>교환 거절</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0022" <?=(in_array('OD0022',$code)?'checked':'')?>>이의 제기(교환 거절)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0023" <?=(in_array('OD0023',$code)?'checked':'')?>>재배송 중</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0024" <?=(in_array('OD0024',$code)?'checked':'')?>>교환 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0025" <?=(in_array('OD0025',$code)?'checked':'')?>>교환 완료(관리자 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0026" <?=(in_array('OD0026',$code)?'checked':'')?>>반품 신청 중</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0027" <?=(in_array('OD0027',$code)?'checked':'')?>>반품 신청 승인</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0028" <?=(in_array('OD0028',$code)?'checked':'')?>>반품 신청 거절</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0029" <?=(in_array('OD0029',$code)?'checked':'')?>>이의 제기(반품 신청 거절)</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0030" <?=(in_array('OD0030',$code)?'checked':'')?>>반송 중(반품)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0031" <?=(in_array('OD0031',$code)?'checked':'')?>>반품 심사 중</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0032" <?=(in_array('OD0032',$code)?'checked':'')?>>반품 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0033" <?=(in_array('OD0033',$code)?'checked':'')?>>반품 완료(관리자 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0034" <?=(in_array('OD0034',$code)?'checked':'')?>>반품 거절</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0035" <?=(in_array('OD0035',$code)?'checked':'')?>>이의 제기(반품 거절)</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0036" <?=(in_array('OD0036',$code)?'checked':'')?>>판매 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0037" <?=(in_array('OD0037',$code)?'checked':'')?>>배송 처리 완료</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0038" <?=(in_array('OD0038',$code)?'checked':'')?>>환불 대기(판매 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0039" <?=(in_array('OD0039',$code)?'checked':'')?>>환불 대기(주문 직권 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0040" <?=(in_array('OD0040',$code)?'checked':'')?>>환불 대기(교환 관리자 취소)</td>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0041" <?=(in_array('OD0041',$code)?'checked':'')?>>환불 대기(반품 직권 취소)</td>
		</tr>
		<tr>
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0042" <?=(in_array('OD0042',$code)?'checked':'')?>>환불 대기(주문 취소)
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0043" <?=(in_array('OD0043',$code)?'checked':'')?>>재결제 중
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0044" <?=(in_array('OD0044',$code)?'checked':'')?>>이의 제기(재결제)
			<td style="font-size:7pt;letter-spacing:-1px"><input type="checkbox" name="code[]" value="OD0045" <?=(in_array('OD0045',$code)?'checked':'')?>>입금 대기(재결제)
		</tr>
		</table>
		<br>
		<input type="button" value="취소관련 모두체크" style="border:1px solid #cccccc;font-size:8pt" id="btnAllCancel">
		<input type="button" value="교환관련 모두체크" style="border:1px solid #cccccc;font-size:8pt" id="btnAllExchange">
		<input type="button" value="반품관련 모두체크" style="border:1px solid #cccccc;font-size:8pt" id="btnAllReturn">
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

<br><br>

<form name="frmNaverCheckout" method="post" target="processLayerForm">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col align="center" width="30"/>
<col align="center" width="35"/>
<col align="center" width="90"/>
<col align="center" width="100"/>
<col align="center" />
<col align="center" width="100" />
<col align="center" width="100" />
<col align="center" width="75" />
<col align="center" width="75" />
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:void(0)" onClick="chkBoxAll()" class=white>선택</a></th>
	<th>번호</th>
	<th>주문일시</th>
	<th>주문번호</th>
	<th>주문상품</th>
	<th>주문자</th>
	<th>받는분</th>
	<th>금액</th>
	<th>처리상태</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
foreach($orderList['record'] as $eachOrder) {

	$eachOrder['ORDER_OrderDateTime'] = preg_replace('/^\d{2}(\d{2})-(\d+)-(\d+) (\d+):(\d+):(\d+)$/','$1.$2.$3 $4:$5',$eachOrder['ORDER_OrderDateTime']);
	$eachOrder['ProductName'] = $eachOrder['PO_cnt'] > 1 ? $eachOrder['ProductName'].' 외 '.($eachOrder['PO_cnt']-1).'건' : $eachOrder['ProductName'];
?>
<tr>
	<td class="noline"><input type="checkbox" name="OldOrderID[]" value="<?=$eachOrder['ORDER_OrderID']?>" class="el-OrderID"/></td>
	<td><font class=ver8 color=616161><?=$eachOrder['_rno']?></font></td>
	<td><font class=ver81 color=616161><?=$eachOrder['ORDER_OrderDateTime']?></font></td>
	<td height="23"><a href="checkout.orderdetail.php?orderNo=<?=$eachOrder['orderNo']?>"><font class=ver81 color=0074BA><b><?=$eachOrder['ORDER_OrderID']?></b></font></a></td>
	<td align="left"><font class=small1 color=444444><?=$eachOrder['ProductName']?></font></td>
	<td><font class=small1 color=444444><?=$eachOrder['ORDER_OrdererName']?><?=$eachOrder['ORDER_OrdererID'] ? '<br>('.$eachOrder['ORDER_OrdererID'].')' : ''?></font></td>
	<td><font class=small1 color=444444><?=$eachOrder['SHIPPING_Recipient']?></font></td>
	<td class=ver81><?=number_format($eachOrder['ORDER_MallOrderAmount'])?></td>
	<td class="small4"><?=$eachOrder['ORDER_OrderStatus']?></td>
</tr>
<tr><td colspan="9" bgcolor="#E4E4E4"></td></tr>
<? } ?>
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

<button class="default-btn" onClick="fnGetMigratedProductOrderList();return false;">주문데이터변환</button>

</form>

<? include '../_footer.php'; ?>