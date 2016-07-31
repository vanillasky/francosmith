<?
@include "../../conf/egg.usafe.php";

### 주문리스트 리퍼러
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

### 취소사유 배열생성
$r_cancel = codeitem("cancel");
$r_cancel[0] = "주문취소복원";

### 배송업체 정보
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$_delivery[] = $data;
}

### 입금계좌 정보
$query = "select * from ".GD_LIST_BANK." order by sno";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$data['name'] .= ($data['useyn'] == 'y' ? '' : ' (삭제한계좌)');
	$_bank[] = $data;
}

$ordno = $_GET[ordno];
$order = Core::loader('order');
$order->load($ordno);


$_selected[deliveryno][$order[deliveryno]] = "selected";
$_selected[bankAccount][$order[bankAccount]] = "selected";

### 카드결제로그 파싱
if ($order[settlelog]){
	$div = explode("\n",$order[settlelog]);
	foreach ($div as $v){
		$div2 = explode(":",$v);
		$r_settlelog[trim($div2[0])] = trim($div2[1]);
	}
}

### 세금계산서
$query = "select regdt, agreedt, printdt, price, step, doc_number from ".GD_TAX." where ordno='$ordno' order by sno desc limit 1";
$taxed = $db->fetch($query);
if ( $taxed['step'] != '' && $taxed['step']==0 )
	$_taxstate = "<FONT COLOR=#007FC8>발행신청</font> - 신청일 : {$taxed['regdt']}";
else if ( $taxed['step'] != '' && $taxed['step']==1 )
	$_taxstate = "<FONT COLOR=#587E06>발행승인</font> <a href=\"javascript:orderPrint('{$ordno}','tax')\">[세금계산서 인쇄]</a><br>발행액 : " . number_format($taxed['price']) . ", 승인일 : {$taxed['agreedt']}";
else if ( $taxed['step'] != '' && $taxed['step']==2 )
	$_taxstate = "<FONT COLOR=#2266BB>발행완료</font> <a href=\"javascript:orderPrint('{$ordno}','tax')\">[세금계산서 인쇄]</a><br>발행액 : " . number_format($taxed['price']) . ", 완료일 : {$taxed['printdt']}";
else if ( $taxed['step'] != '' && $taxed['step']==3 )
	$_taxstate = "<div id=\"tax1\"><FONT COLOR=#2266BB>전자발행</font></div><div id=\"tax2\">발행액 : " . number_format($taxed['price']) . ", 요청일 : {$taxed['agreedt']}</div><script>getTaxbill('{$taxed[doc_number]}');</script>";

### 인터파크
$inpk_ordno = $order['inpk_ordno'];
$inpk_regdt = $order['inpk_regdt'];

?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>
<script>
function chkCancel()
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("선택취소할 상품을 선택해주세요");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.cancel.php?ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
}
function orderPrint(ordno,type)
{
	if (!type){
		alert("인쇄할 문서 종류를 선택하세요");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600");
	orderPrint.window.print();
}
function escrow_confirm()
{
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
}
</script>
<script>
/*** Taxbill 정보 출력 ***/
function getTaxbill(doc_number)
{
	var print = function(){
		var req = ajax.transport;
		if (req.status == 200){
			var jsonData = eval( '(' + req.responseText + ')' );
			document.getElementById('tax1').innerHTML += (jsonData.status_txt != null ? ' - ' + jsonData.status_txt : '');
			if (jsonData.tax_path != null) document.getElementById('tax1').innerHTML +=" <a href=\"javascript:popup('" + jsonData.tax_path + "',750,600);\">[세금계산서 인쇄]</a>";
			document.getElementById('tax2').innerHTML += (jsonData.mtsid != null ? '<br>식별번호 : ' + jsonData.mtsid : '');
		}
		else {
			var msg = req.getResponseHeader("Status");
			document.getElementById('tax1').title = msg;
			document.getElementById('tax1').innerHTML += '<font class=small color=444444> - 로딩중에러</font>';
		}
	}
	var ajax = new Ajax.Request("../order/tax_indb.php?mode=getTaxbill&doc_number=" + doc_number + "&dummy=" + new Date().getTime(), { method: "get", onComplete: print });
}
</script>

<table width=100% cellpadding=0 cellspacing=0><tr><td style="padding:5px 10px;background:#f7f7f7;margin:10px 0;border:3px solid #C6C6C6;">
<table width=100%>
<tr>
	<td id="orderInfoBox">
	<font class=def>주문번호:</font> <span style="color:#000000;font:bold 14px verdana"><?=$ordno?></span>
	<? if ($order[inflow]!=""&&$order[inflow]!="sugi"){ ?><img src="../img/inflow_<?=$order[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$order[inflow]]?><? } ?>
	</td>
	<td align=right <?=$hiddenPrint?>>
	<select name="order_print" class="Select_Type1" style="font:8pt 돋움">
	<option value=""> - 인쇄선택 - </option>
	<option value="report"> 주문내역서  </option>
	<option value="reception"> 간이영수증  </option>
	<option value="tax"> 세금계산서  </option>
	<!--<option value="particular"> 거래명세서  </option>    -->
	</select>
	<a href="javascript:orderPrint(<?=$ordno?>, document.getElementsByName('order_print')[0].value);"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
	</td>
</tr>
</table>
</td></tr>
<tr><td height=8></td></tr>
</table>

<form name=frmOrder action="indb.php" method=post>
<input type=hidden name=mode value="modOrder">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type=hidden name=referer value="<?=$referer?>">

<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>번호</th>
	<th colspan=2><font color=white>상품명 / 제품코드</th>
	<th><font color=white>수량</th>
	<th><font color=white>판매가</th>
	<th><font color=white>할인금액</th>
	<th><font color=white>회원할인</th>
	<th><font color=white>결제금액</th>
	<!--<th><font color=white>택배사/송장번호</th>-->
	<th><font color=white>처리상태</th>
</tr>
<col align=center span=2><col>
<col align=center span=9>
<?
$idx = $goodsprice = 0;

## 정상 주문상품 갯수 구하기
$query = "select count(*) from ".GD_ORDER_ITEM." where istep < 40 and ordno='$ordno'";
list($icnt) = $db->fetch($query);

unset($goodsprice,$memberdc,$coupon,$dc);

foreach($order->getOrderItems() as $item) {

	if ($item->hasCanceled()) {
		$disabled[chk] = $item->hasCanceled() ? "disabled" : "";
		$bgcolor = "#F0F4FF";
	}
	else {
		$disabled[chk] = "";
		$bgcolor = "#ffffff";
	}

?>
<input type=hidden name=sno[] value="<?=$item[sno]?>">
<tr bgcolor="<?=$bgcolor?>">
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<td width=50 nowrap><?=goodsimg($item[img_s],40,"style='border:1 solid #cccccc'",1)?></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<font class=def><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<br><?=$item[goodscd]?><!-- 고객요청으로 코드추가 - mickey 2007-01-03  -->
	<div class=small4>제조사 : <?=$item[maker] ? $item[maker] : '―'?></div>
	<div class=small4>브랜드 : <?=$item[brandnm] ? $item[brandnm] : '―'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	</td>
	<td nowrap><input type=text name=ea[] value="<?=$item[ea]?>" size=3 class=right></td>
	<td nowrap><input type=text name=price[] value="<?=$item[price]?>" size=7 class=right></td>
	<td width=65 nowrap><?=number_format($item->getPercentCouponDiscount() + $item->getSpecialDiscount())?></td>
	<td width=65 nowrap><?=number_format($item->getMemberDiscount())?></td>
	<td width=65 nowrap><?=number_format($item->getSettleAmount())?></td>
	<!--<td nowrap>
	<select name=dvno[]>
	<option value="">==택배사==
	<? foreach ($_delivery as $v){ ?>
	<option value="<?=$v[deliveryno]?>" <?=$selected[dvno][$v[deliveryno]]?>><?=$v[deliverycomp]?>
	<? } ?>
	</select>
	<input type=text name=dvcode[] size=15 value="<?=$item[dvcode]?>">
	</td>-->
	<td width=70 nowrap>
	<font class=small4><?=$r_istep[$item[istep]]?></font>
	<? if ($item[istep]==41 || ($item[istep]==44 && $item[cyn].$item[dyn]=="nn")){ ?><div><a href="indb.php?mode=recovery&sno=<?=$item[sno]?>" onclick="return confirm(' 복원처리하시겠습니까?')"><img src="../img/btn_return.gif" border=0></a></div><? } ?>
	<!--<div><?=$item[cyn]?>|<?=$item[dyn]?></div>-->
	</td>
</tr>
<?
}
?>
</table>

<div id=layer_cancel style="display:none;padding-top:10px">
<iframe id=ifrmCancel name=ifrmCancel style="width:100%;height:0;" frameborder=0></iframe>
</div><p>

<?
$selected[step][$order[step]] = "selected";
$selected[step2][$order[step2]] = "selected";
$selected[deliveryno][$order[deliveryno]] = "selected";
?>

	</td>
</tr>
</table><p>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>결제금액정보</font></div>
<table border=2 bordercolor=627dce style="border-collapse:collapse" width=100%>
<tr><td style="padding:10px 0;">

<table width="100%" height="100%" cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<? if ($order->getCanceledCount() > 0){ ?>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_orderprice.php'; ?>
    </td>
    <td width="10" nowrap="nowrap"></td>
    <td width="50%" valign="top">
        <?
        if ($order->getCancelCompletedCount() > 0){
            include '_form.enamoo.inc_canceledprice.php';
        } else {
            include '_form.enamoo.inc_cancelingprice.php';
        }
        ?>
    </td>
</tr>
<? } else { ?>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_orderprice.php'; ?>
    </td>
</tr>
<? } ?>
<? if ($order->getCancelCompletedCount() > 0){ ?>
<tr><td height=15></td></tr>
<tr>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_settleprice.php'; ?>
    </td>
    <td width="10" nowrap="nowrap"></td>
    <td width="50%" valign="top">
        <? include '_form.enamoo.inc_cancelingprice.php'; ?>
    </td>
</tr>
<? } ?>
</table><p>
</td></tr></table><p>
<?
if($r_stepi[$order[step]][$order[step2]] == "환불완료"){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>환불금액정보</font></div>
<input type=hidden name='cancelsno' value='<?=$row2[sno]?>'>
<table class=tb cellpadding=4 cellspacing=0>
<tr>
	<td width=110 align=center bgcolor=#F6F6F6>환불수수료</td>
	<td style="padding:2px 10px">
		<?=number_format($order->getRefundedFeeAmount())?>원
	</td>
</tr>
<tr>
	<td width=110 align=center bgcolor=#F6F6F6>환불금액</td>
	<td style="padding:2px 10px">
		<?=number_format($order->getRefundedAmount())?>원
	</td>
</tr>
</table><p>
<?
}
?>
<table width=100% cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<tr>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>주문자정보</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>구분/주문자(ID)</td>
		<td>
		<?=$order[nameOrder]?>
		<? if ($order[m_id]){ ?>/ <font color=0074BA><b><?=$order[m_id]?></b></font>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td>이메일</td>
		<td><font class=ver8><?=$order[email]?></font></td>
	</tr>
	<tr>
		<td>연락처</td>
		<td><font class=ver8>
		<?=$order[phoneOrder]?> / <?=$order[mobileOrder]?>
		</td>
	</tr>
	<tr>
		<td>주문일</td>
		<td><font class=ver8><?=$order[orddt]?></td>
	</tr>
	</table>

	</td>
	<td width=10 nowrap></td>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>수령자정보</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>수령자</td>
		<td>
		<?=$order[nameReceiver]?>
		</td>
	</tr>
	<tr>
		<td>연락처</td>
		<td>
		<?=$order[phoneReceiver]?> &nbsp;/&nbsp;
		<?=$order[mobileReceiver]?>
		</td>
	</tr>
	<tr>
		<td>주소</td>
		<td><font color=444444>
		<?php echo $order[zonecode]; ?>
		<?php if(str_replace("-", "", $order[zipcode])) echo '('.substr($order[zipcode],0,3).' - '.substr($order[zipcode],4).')'; ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><?if($order['road_address']) { ?>지번 : <? } ?><?=$order[address]?><div style="padding-top:5px;" id="div_road_address"><?if($order['road_address']) { ?>도로명 : <?=$order['road_address']?><? } ?></div></td>
	</tr>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>결제정보</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>결제종류</td>
		<td><?=$r_settlekind[$order[settlekind]]?></td>
	</tr>
	<? if ($order[settlekind]=="a"){ ?>
	<tr>
		<td>입금계좌</td>
		<td>
		<select name=bankAccount>
		<? foreach ($_bank as $v){ ?>
		<option value="<?=$v[sno]?>" <?=$_selected[bankAccount][$v[sno]]?>><?=$v[bank]?> <?=$v[account]?> <?=$v[name]?>
		<? } ?>
		</select>
		</td>
	</tr>
	<tr>
		<td>입금자</td>
		<td><input type=text name=bankSender value="<?=$order[bankSender]?>"></td>
	</tr>
	<? } else if ($order[settlekind]=="v"){ ?>
	<tr>
		<td>가상계좌</td>
		<td><?=$order[vAccount]?></td>
	</tr>
	<? } ?>
	<tr>
		<td>결제확인일</td>
		<td><font class=ver8>
		<? if ($order[settlekind]=="c" && $order[settlelog]){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['결과내용']?>]</b></font><? } ?>
		<?=$order[cdt]?>
		</td>
	</tr>
	<? if ($order[cashreceipt]){ ?>
	<tr>
		<td>현금영수증번호</td>
		<td><?=$order[cashreceipt]?></td>
	</tr>
	<? } ?>
	<? if ( !empty($_taxstate) ){ ?>
	<tr>
		<td>세금계산서</td>
		<td><?=$_taxstate?></td>
	</tr>
	<? } ?>
	<? if ($order[inflow]!=""&&$order[inflow]!="sugi"){ ?>
	<tr>
		<td>제휴처주문</td>
		<td><img src="../img/inflow_<?=$order[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$order[inflow]]?></td>
	</tr>
	<? } ?>
	<? if ($order[eggyn]!="n"){ ?>
	<tr>
		<td>전자보증보험</td>
		<td>
		<? if ($order[eggno]!=""){ ?><a href="javascript:popupEgg('<?=$egg['usafeid']?>', '<?=$ordno?>')"><font class=ver71 color=0074BA><b><?=$order[eggno]?> <font class=small1>(내역서 보기)</b></font></a><? } ?>
		<? if ($order[eggno]=="" && $r_settlelog['결과메세지']){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['결과메세지']?>]</b></font><? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>배송정보</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($order[deli_title] != null){?>
	<tr>
		<td>배송방법</td>
		<td><?if($order['deli_msg'] != "개별 착불 배송비"){?><?=$order['deli_title']?><?}?> <?=( $order['deli_msg'] )?$order['deli_msg']:""?></td>
	</tr>
	<?}?>
	<tr>
		<td>송장번호</td>
		<td>
		<?
		if($order[step] >= 1 && $order[step] < 4){?>
		<select name=deliveryno>
		<option value="">==택배사==
		<?
		if ($_delivery){ foreach ($_delivery as $v){ ?>
		<option value="<?=$v[deliveryno]?>" <?=$_selected[deliveryno][$v[deliveryno]]?>><?=$v[deliverycomp]?>
		<? }} ?>
		</select>
		<input type=text name=deliverycode value="<?=$order[deliverycode]?>" class=line>
		<?}else{?>
		<font class=small1 color=444444>아래 배송상태추적 버튼을 눌러 확인하세요.</font>
		<input type=hidden name='deliveryno' value='<?=$order[deliveryno]?>'>
		<input type=hidden name='deliverycode' value='<?=$order[deliverycode]?>'>
		<?}?>
		</td>
	</tr>
	<? if ($order[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>배송추적</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>배송일(출고일)</td>
		<td><font class=ver8><?=$order[ddt]?></td>
	</tr>
	<? if ($order[confirmdt]){ ?>
	<tr>
		<td>배송완료일</td>
		<td><font class=ver8><?=$order[confirmdt]?>(<?=$order[confirm]?>)</td>
	</tr>
	<? } ?>
	<? if ($order[escrowyn]=="y"){ ?><!-- 에스크로 배송 확인 -->
	<tr>
		<td>에스크로</td>
		<td>
		<? if (!$order[escrowconfirm]){ ?><a href="javascript:escrow_confirm()">[배송확인요청]</a>
		<? } else if ($order[escrowconfirm]==1){ ?>배송요청중
		<? } else if ($order[escrowconfirm]==2){ ?>배송완료<? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>
	<span style="padding-right:10px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>요청사항/상담메모</span>
	</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr height=25>
		<td>고객요청사항</td>
		<td><?=nl2br($order[memo])?></td>
	</tr>
	<tr height=25>
		<td>고객상담메모</td>
		<td><?=nl2br($order[adminmemo])?></td>
	</tr>
	<tr height=25>
		<td>결제로그</td>
		<td><textarea style="width:100%;height:100px;overflow:visible;font:9pt 굴림체;padding:10px 0 0 8px"><?=trim(strcut($order[settlelog],134))?></textarea></td>
	</tr>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>취소요청 리스트</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?
	$query = "select * from ".GD_ORDER_CANCEL." where ordno='$ordno' order by sno desc";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
	?>
	<tr>
		<td><font class=small><?=$data[name]?></td>
		<td>
		<div style="float:left" class=ver8 color=444444><?=$data[regdt]?></div>
		<div style="float:right">[<?=$r_cancel[$data[code]]?>]</div>
		</td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=#ffffff align=left style="padding:5px">

		<table width=100%>
		<?
		$query = "select * from ".GD_LOG_CANCEL." where cancel='$data[sno]'";
		$res2 = $db->query($query);
		while ($item=$db->fetch($res2)){
		?>
		<tr bgcolor=#f7f7f7>
			<td>- <?=$item[goodsnm]?> <?=$item[ea]?>개</td>
			<td align=right><?=$r_istep[$item[prev]]?> → <?=$r_istep[$item[next]]?></td>
		</tr>
		<? } ?>
		</table>
		</div>

		<? if ($data[memo]){ ?>
		<div style="margin:5px" class=small><?=nl2br($data[memo])?></div>
		<? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr>
</table>

<div class=button <?=$hiddenPrint?>>
<input type=image src="../img/btn_modify.gif">
<a href='<?=$referer?>'><img src='../img/btn_list.gif'></a>
</div>

</form>

<?
if($order['inflow']=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // 인터파크_인클루드
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // 인터파크_인클루드
}
?>