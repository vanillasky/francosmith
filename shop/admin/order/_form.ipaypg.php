<?
//------- 주문내역서 출력시 _form.php 를 그대로 출력하기 때문에 페이지가 늘어나고 주소가 짤리는 문제로 인해
//------- 출력용으로 _reportForm.php 파일 생성함, 현페이지 메뉴및 쿼리수정시 _reportForm.php 도 함께 수정해주어야 함.
@include "../../conf/egg.usafe.php";
@include "../../conf/config.pay.php";
@include "../../conf/phone.php";

### 주문리스트 리퍼러
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

### 은행 배열생성
$r_bank = codeitem("bank");

### 취소사유 배열생성
$r_cancel = codeitem("cancel");
$r_cancel[0] = "주문취소복원";

### 배송업체 정보
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$_delivery[] = $data;
	$r_delivery[$data[deliveryno]] = $data[deliverycomp];
}

### 입금계좌 정보
$query = "select * from ".GD_LIST_BANK." order by useyn asc, sno";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$data['name'] .= ($data['useyn'] == 'y' ? '' : ' (삭제한계좌)');
	$_bank[] = $data;
}

$ordno = $_GET[ordno];

$query = "select b.m_id,a.* from ".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no where ordno='$ordno'";
$data = $db->fetch($query);

$inflow = $data[inflow];

if(!$data[deliveryno] && $_delivery[0][deliveryno]) $data[deliveryno] = $_delivery[0][deliveryno];
$_selected[deliveryno][$data[deliveryno]] = "selected";
$_selected[bankAccount][$data[bankAccount]] = "selected";

if(!$data[confirm])$data[confirm] = "admin";

### 카드결제로그 파싱
if ($data[settlelog]){
	$div = explode("\n",$data[settlelog]);
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
$inpk_ordno = $data['inpk_ordno'];
$inpk_regdt = $data['inpk_regdt'];

### 굿스플로
if ((int)$data['step'] >= 2) {

	// 굿스플로를 통해 발급 받았는가.
	$query = "
	SELECT GF.*
	FROM ".GD_ORDER." AS O

	INNER JOIN ".GD_GOODSFLOW_ORDER_MAP." AS OM
	ON O.ordno = OM.ordno

	INNER JOIN ".GD_GOODSFLOW." AS GF
	ON GF.sno = OM.goodsflow_sno

	WHERE
		GF.status > '' AND O.ordno = '$ordno'

	LIMIT 1
	";

	$GF = $db->fetch($query,1);

}
else {
	$GF = false;
}
?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>
<script>
function cal_repay(repay,price,i){
	var tmp = price - repay;
	document.getElementsByName('repay')[0].value=tmp;
}

function chkCancel(m)
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("선택취소할 상품을 선택해주세요");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.cancel.php?m="+m+"&ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
}
function cardSettleCancel(ordno){
	var obj = document.ifrmHidden;
	if(confirm('카드결제를 취소하시겠습니까?'))	obj.location.href = "cardCancel.php?ordno="+ordno;
}
function cardCancel(pg,ordno){
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
}
function orderPrint(ordno,type)
{
	if (!type){
		alert("인쇄할 문서 종류를 선택하세요");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600,scrollbars=1");
	orderPrint.window.print();
}
function escrow_confirm()
{
	var obj = document.ifrmHidden;
	obj.location.href = "../../order/card/<?=$cfg[settlePg]?>/escrow_gate.php?ordno=<?=$ordno?>";
}
function escrow_cancel()
{
	var win = window.open("../../order/card/<?=$cfg[settlePg]?>/escrow_cancel.php?ordno=<?=$ordno?>","","width=520,height=250,scrollbars=0");
}
function registerDelivery()
{
	var sno = new Array();
	var el = document.getElementsByName('chk[]');
	for (i=0;i<el.length;i++) if (el[i].checked) sno[sno.length] = el[i].value;
	if (sno.length==0){
		alert("배송정보를 입력할 상품을 선택해주세요");
		return;
	}
	_ID('layer_cancel').style.display = "block";
	ifrmCancel.location.href = "ifrm.delivery.php?ifrmScroll=1&ordno=<?=$ordno?>&chk=" + sno.join();
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

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFF0" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

document.observe("dom:loaded", function() {
	var selDeliveryNo=document.frmOrder.deliveryno;
	var iptDeliveryCode=document.frmOrder.deliverycode;
	Element.extend(iptDeliveryCode);
	Element.extend(selDeliveryNo);
	if(selDeliveryNo.value=="100") {
		iptDeliveryCode.readOnly=true;
	}
	else {
		iptDeliveryCode.readOnly=false;
	}

	selDeliveryNo.observe("change",function(evt){
		var element = evt.element();
		if(element.value=="100") {
			iptDeliveryCode.readOnly=true;
		}
		else {
			iptDeliveryCode.readOnly=false;
		}
	});


});

function couponDelPop(){
	if(confirm("결제시도/실패 등으로 정상적인 결제가 이루어 지지 않았을 경우 \n사용되어진 쿠폰을 미사용 상태로 복원하는 기능입니다. \n복원하시겠습니까?")){
		document.frmOrder.mode.value = "restoreDiscount";
		document.frmOrder.submit();
	}
}
</script>
<?getjskPc080();?>

<div class="title title_top">주문상세내역<span>이 주문에 대한 상세한 내역을 조회하고 수정하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr><td style="padding:5px 10px;background:#f7f7f7;margin:10px 0;border:3px solid #627dce;">
<table width=100%>
<tr>
	<td id="orderInfoBox">
	<font class=def>주문번호:</font> <span style="color:<?=($data['inflow']!="sugi") ? "#4f67af" : "#ED6C0A"?>;font:bold 11px verdana"><?=$ordno.(($data['inflow']=="sugi") ? "(수기주문)" : "")?></span>
	<? if ($data[inflow]!=""&&$data[inflow]!="sugi"){ ?><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$data[inflow]]?><? } ?>
	<? if ($data[pCheeseOrdNo]!=""){ ?><img src="../img/icon_plus_cheese.gif" align=absmiddle> 플러스 치즈 주문<? } ?>
	<font class=def>iPay 카트번호:</font> <span style="color:#4f67af;font:11px verdana"><?=$data['ipay_cartno']?></span>
	<font class=def>iPay 결제번호:</font> <span style="color:#4f67af;font:11px verdana"><?=$data['ipay_payno']?></span>
	</td>
	<td align=right <?=$hiddenPrint?>>
	<select name="order_print" class="Select_Type1" style="font:8pt 돋움">
	<option value=""> - 인쇄선택 - </option>
	<option value="report"> 주문내역서  </option>
	<option value="reception"> 간이영수증  </option>
	<option value="tax"> 세금계산서  </option>
	<option value="particular"> 거래명세서  </option>
	</select>
	<a href="javascript:orderPrint(<?=$ordno?>, document.getElementsByName('order_print')[0].value);"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
	</td>
</tr>
</table>
</td></tr>
<tr><td height=8></td></tr>
</table>

<?
$query = "
select b.*,a.*, tg.tgsno from
	".GD_ORDER_ITEM." a
	left join ".GD_GOODS." b on a.goodsno=b.goodsno
	left join ".GD_TODAYSHOP_GOODS." tg on a.goodsno=tg.goodsno
where
	a.ordno='$ordno'
order by a.sno
";
$sub = $db->query($query);

?>
<form name=frmOrder action="indb.php" method=post>
<input type=hidden name=mode value="modOrder">
<input type=hidden name=ordno value="<?=$ordno?>">
<input type=hidden name=referer value="<?=$referer?>">
<input type=hidden name=step2 value="<?=$data[step2]?>">

<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>선택</a></th>
	<th><font color=white>번호</th>
	<th colspan=2><font color=white>상품명</th>
	<th><font color=white>수량</th>
	<th><font color=white>상품가격</th>
	<th><font color=white>총 할인</th>
	<th><font color=white>소계</th>
	<th><font color=white>매입가</th>
	<!--<th><font color=white>택배사/송장번호</th>-->
	<th><font color=white>처리상태</th>
	<?if($set[delivery][basis]){?>
	<th nowrap><font color=white >택배사/송장번호</th>
	<?}?>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$idx = $goodsprice = 0;
$icancel = 0;

## 정상 주문상품 갯수 구하기
$query = "select count(*) from ".GD_ORDER_ITEM." where istep < 40 and ordno='$ordno'";
list($icnt) = $db->fetch($query);

while ($item=$db->fetch($sub)){
	unset($selected);
	$supply += $item[supply] * $item[ea];
	$selected[dvno][$item[dvno]] = "selected";
	$selected[istep][$item[istep]] = "selected";
	$disabled[chk] = ($item[istep]>40) ? "disabled" : "";

	if($icnt == 0){ //모든 주문상품이 취소,환불일 경우
		$goodsprice += $item[price] * $item[ea];
		$memberdc += $item[memberdc] * $item[ea];
		$coupon += $item[coupon] * $item[ea];
	}else if ($item[istep]<40){
		$goodsprice += $item[price] * $item[ea];
		$memberdc += $item[memberdc] * $item[ea];
		$coupon += $item[coupon] * $item[ea];
	}

	## 주문취소 건 카운트
	if($item[istep] > 40) $icancel++;

	$bgcolor = ($item[istep]>40) ? "#F0F4FF" : "#ffffff";

	if($item[dvcode])$cntDv++;
?>
<input type=hidden name=sno[] value="<?=$item[sno]?>">
<tr bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>">
	<td width=35 nowrap class=noline><input type=checkbox name=chk[] value="<?=$item[sno]?>" onclick="iciSelect(this)" <?=$disabled[chk]?>></td>
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<? if ($item['todaygoods'] != 'y') { ?>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<? } else { ?>
	<td width=50 nowrap><a href="../../todayshop/today_goods.php?tgsno=<?=$item[tgsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="../todayshop/goods_reg.php?mode=modify&tgsno=<?=$item[tgsno]?>"  target=_blank>
	<? } ?>
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<투데이샵상품>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>제조사 : <?=$item[maker] ? $item[maker] : '없음'?></div>
	<div><font class=small1 color=6d6d6d>브랜드 : <?=$item[brandnm] ? $item[brandnm] : '없음'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>iPay 주문번호/상품번호 : <span style="font:10px verdana"><?=$item['ipay_ordno']?> / <?=$item['ipay_itemno']?></span></font></div>
	</td>
	<td nowrap><input type=text name=ea[] value="<?=$item[ea]?>" size=3 class=right readonly style="background-color:#e3e3e3;"></td>
	<td nowrap><input type=text name=price[] value="<?=$item[price]?>" size=7 class=right readonly style="background-color:#e3e3e3;"></td>
	<td width=55 nowrap><?php echo number_format($item['ipay_dcprice']); ?></td>
	<td width=55 nowrap><?php echo number_format(($item['price']*$item['ea'])-$item['ipay_dcprice']); ?></td>
	<td nowrap><input type=text name=supply[] value="<?=$item[supply]?>" size=7 class=right></td>
	<td width=70 nowrap>
	<font class=small4><?=$r_istep[$item[istep]]?></font>
	</td>
	<?if($set[delivery][basis]){?>
	<td><div nowrap><font class=small color=555555><b><?=((!$item[dvcode]||!$item[dvno])&&$data[step])? "-":$r_delivery[$item[dvno]] . "</div><div nowrap>" . $item[dvcode]?></font></div></td>
	<?}?>
</tr>
<?
}

### 할인액 계산
$discount = $memberdc + $data[emoney] + $data[coupon] + $data[enuri] + $data[ncash_emoney] + $data[ncash_cash];

### 실데이타 계산으로 결제금액 산출
$settleprice = $goodsprice + $data[delivery] - $discount + $data[eggFee];
?>
</table>

<table cellpadding=0 cellspacing=0 width=100%>
<tr><td width=60% style="padding:5px 0 0 12px"><a href="javascript:chkCancel(0)"><img src="../img/btn_cancelorder.gif" border=0></a></td>
<td width=40% align=right style="padding-right:5px"><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0></a>
</td></tr></table>



<!-- 개별송장입력 시작 -->
<?if($set[delivery][basis]){?>
<div style="padding:5px 0 0 12px">
<?if($data[step]){?><a href="javascript:registerDelivery()"><img src="../img/btn_input_delinumber.gif" border=0></a><?}?>
</div>
<?}?>
<!-- 개별송장입력 끝 -->
<div id=layer_cancel style="display:none;padding-top:10px">
<iframe id=ifrmCancel name=ifrmCancel style="width:100%;height:0;" frameborder=0></iframe>
</div><p>
<?
$selected[step][$data[step]] = "selected";
$selected[step2][$data[step2]] = "selected";
$selected[deliveryno][$data[deliveryno]] = "selected";

if ($memberdc) $dc[memberdc] = "회원할인 (<font color=0074BA class=ver81>".number_format($memberdc)."</font>원)";
if ($data[coupon]) $dc[coupon] = "쿠폰할인 (<font color=0074BA class=ver81>".number_format($data[coupon]-$data[about_dc_sum])."</font>원)";
if ($data[about_coupon_flag]) $dc[aboutcoupon] = "어바웃쿠폰 (<font color=0074BA class=ver81>".number_format($data[about_dc_sum])."</font>원)";
if ($data[emoney]) $dc[emoney] = "적립금사용 (<font color=0074BA class=ver81>".number_format($data[emoney])."</font>원)";
if ($data[ncash_emoney]) $dc[ncash_emoney] = "네이버마일리지사용 (<font color=0074BA class=ver81>".number_format($data[ncash_emoney])."</font>원)";
if ($data['ncash_cash']) $dc['ncash_cash'] = "네이버캐쉬사용 (<font color=0074BA class=ver81>".number_format($data['ncash_cash'])."</font>원)";
$dc[enuri] = "에누리 <input type=text name=enuri value='$data[enuri]' size=6 class='ver81 right' style='color:#0074BA'> 원";
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>현주문상태</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>주문상태</td>
	<td style="padding:2px 10px">
	<table width=100%>
	<tr>
		<td><font class="small1">
		<? if (!$data['step2']){ ?>
			<select name="step">
			<? foreach ($r_step as $k=>$v){ if ($k>9) break; ?>
			<option value="<?=$k?>" <?=$selected[step][$k]?>><?=$v?>
			<? } ?>
			</select>
			<span class="extext" style="margin-top: 10px;">iPay PG 주문건의 경우 배송중으로 전환 시 반드시 송장번호를 입력해야 합니다.</span>
		<?
			if($icancel) echo "주문취소 $icancel 건입니다.";
		} else {
			echo getStepMsg($data[step],$data[step2],$data[ordno]);
		}
		?>
		<?
		list($cnt) = $db->fetch("select count(*) from ".GD_ORDER." where oldordno='$ordno'");
		if($cnt){
			$query = "select ordno from ".GD_ORDER." where oldordno='$ordno'";
			$nres = $db->query($query);
			while($ndata = $db->fetch($nres))	$newordno[] = "<a href=\"javascript:popup('popup.order.php?ordno=".$ndata[ordno]."',800,600)\"><font color=0074BA class=ver81><b><u>".$ndata[ordno]."</u></b></font></a>";
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>교환으로 인해 자동생성된 <font color=ED00A2>맞교환주문건</font> (<?=implode(',',$newordno)?>) 이 있습니다.</font>
		<?
		}
		?>
		<?
		if($data[oldordno]){
		?>
		&nbsp;<img src="../img/arrow_gray.gif" align=absmiddle><font class=small1 color=444444>이 주문은 <font color=ED00A2>교환요청건</font> (<a href="javascript:popup('popup.order.php?ordno=<?=$data[oldordno]?>',800,600)"><font color=0074BA class=ver81><b><u><?=$data[oldordno]?></u></b></font></a>) 으로 자동생성된 재주문 입니다.</font>
		<?
		}
		?>
		</td>
		<td align="right">
		<?if($data[step2] >= 50){?>
		<span><a href="indb.php?mode=faileRcy&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="return confirm('주문삭제는 현상태에서 단순히 데이타를 입금확인 상태로 변경하는 기능입니다.\n\n복원된주문은 다시 시도 실패로 변경이 불가합니다.\n\n선택하신 주문[<?=$ordno?>]을 정말로 입금확인으로 변경하시겠습니까?')"><img src="../img/btn_order_try_return.gif"></a></span>
		<?}?>
		<span style='width:80'><a href="indb.php?mode=delOrder&ordno=<?=$ordno?>&returnUrl=<?=urlencode($referer)?>&popup=<?=$popup?>" onclick="return confirm('주문삭제는 이 주문데이타를 단순히 삭제만 하는 기능입니다.\n\n따라서 바로 삭제를 하면 이 주문에 따른 재고, 적립금, 쿠폰은 환원이 안됩니다.\n\n\재고, 적립금, 쿠폰을 환원하려면 반드시 주문취소(선택한 상품취소)를 먼저 해주세요.\n\n주문취소가 되면 원래대로 재고, 적립금, 쿠폰이 환원됩니다.\n\n그리고 주문삭제를 하시기 바랍니다.\n\n한번 삭제된 주문은 복구가 불가능합니다. 신중히 삭제하세요.\n\n선택하신 주문[<?=$ordno?>]을 정말로 삭제하시겠습니까?')"><img src="../img/btn_delete_order.gif"></a><span>
		</td>

	</tr>
	</table>

	</td>
</tr>
</table><p>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>결제금액정보</font></div>

<table class=tb>
<col class=cellC><col class=cellL>
<!--<col class=cellC><col class=cellL width=120><col class=cellL>-->
<tr>
	<td>주문금액</td>
	<td width=110 align=right><font class=ver8><?=number_format($goodsprice+$data[delivery]+$data[eggFee])?></font>원</td>
	<td><img src="../img/arrow_gray.gif" align=absmiddle><font class=small color=444444>상품가격 (<font color=0074BA class=ver81><?=number_format($goodsprice)?></font>원)
	<? if ($data[delivery]){ ?>
	+ 배송비 (<font color=0074BA class=ver81><?=number_format($data[delivery])?></font>원)
	<? } ?>
	<? if ($data[eggFee]){ ?>
	+ 보증보험수수료 (<font color=0074BA class=ver81><?=number_format($data[eggFee])?></font>원)
	<? } ?>
	</td>
</tr>
<tr>
	<td>할인액</td>
	<td align=right><font class=ver8>- <?=number_format($discount)?></font>원</td>
	<td><img src="../img/arrow_gray.gif" align=absmiddle><font class=small color=444444><?=implode(" + ",$dc)?>
	<? if($data[step2] > 40){ ?>&nbsp;<a href="javascript:couponDelPop();"><img src="../img/btn_savedmoney.gif" align=absmiddle></a>
	<? }else{ ?>&nbsp;<img src="../img/btn_savedmoney_off.gif" align=absmiddle></a>
	<? } ?>
	</td>
</tr>
<tr>
	<td>결제금액</td>
	<td align=right><font color=0074BA class=ver8><b><?=number_format($settleprice)?></b></font>원</td>
	<td><font class=small color=444444><? if ($settleprice!=$data[settleprice]){ ?><img src="../img/arrow_gray.gif" align=absmiddle>최초주문금액  (<font color=0074BA class=ver81><?=number_format($data[settleprice])?></font>원)
	 + 취소금액 합계 (<font color=0074BA class=ver81><?=number_format($settleprice-$data[settleprice])?></font>원)
	<? } ?>
	</td>
</tr>
</table><p>
<?
$res = $db->query("select a.*,c.couponcd,c.goodsnm from ".GD_COUPON_ORDER." a left join ".GD_ORDER_ITEM." b on a.goodsno=b.goodsno and a.ordno=b.ordno left join ".GD_COUPON_APPLY." c on a.applysno=c.sno where a.ordno='$ordno'");
if($db->count_($res)){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>쿠폰사용정보</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29>
	<td bgcolor="#F6F6F6" align=center>쿠폰번호</td>
	<td bgcolor="#F6F6F6" align=center>쿠폰명</td>
	<td bgcolor="#F6F6F6" align=center>할인/적립</td>
	<td bgcolor="#F6F6F6" align=center>사용일시</td>
</tr>
<col align=center><col align=center><col align=center><col align=center>
<?
	while($coupon = $db->fetch($res)){
		if($coupon[downloadsno]){
			$res_cp = $db->query("select p.number from gd_offline_download d left outer join gd_offline_paper p on d.paper_sno = p.sno where  d.sno = '$coupon[downloadsno]'");
			$rst_cp = $db->fetch($res_cp);
			$coupon[couponcd] = $rst_cp[number];
		}
?>
<tr>
	<td nowrap><?=$coupon[couponcd]?></td>
	<td nowrap><?=$coupon[coupon]?></td>
	<td nowrap><font class=ver8 color=444444>
		<?
		if($coupon[dc]){
			if(substr($coupon[dc],-1,1) == '%')	echo "할인 ".$coupon[dc];
			else echo "할인 ".number_format($coupon[dc])."원";
		}
		if($coupon[emoney]){
			if(substr($coupon[emoney],-1,1) == '%')	echo "적립 ".$coupon[emoney];
			else echo "적립 ".number_format($coupon[emoney])."원";
		}
		?>
	</td>
	<td nowrap><?=$coupon[regdt]?></td>
</tr>
<?}?>
</table><p>
<?}?>

<!-- 인터파크_클레임 -->
<div id="interpark_claim"></div>

<?
## okcashbag 적립금 표시
if($data[cbyn] == "Y"){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=494949>OKCashBag적립</font> <font class=small1 color=6d6d6d>아래는 캐쉬백 적립내역입니다</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr>
	<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>번호</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>거래번호</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>적립금액</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>적립일</td>
	<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>적립취소</td>
</tr>
<?
	$query = "select * from gd_order_okcashbag where ordno='$ordno'";
	$cashbag_res = $db ->query($query);
	while($r_cashbag = $db->fetch($cashbag_res)){
		$ci++;
?>
<tr>
	<td  style="padding:2px 10px" rowspan=2 align=center><font class=ver7 color=444444><?=$ci?></td>
	<td align=center><font class=ver8><?=$r_cashbag[tno]?></td>
	<td align=center><font class=ver8><?=number_format($r_cashbag[add_pnt])?>원</td>
	<td align=center><font class=ver8><?=substr($r_cashbag[pnt_app_time],0,4)?>-<?=substr($r_cashbag[pnt_app_time],4,2)?>-<?=substr($r_cashbag[pnt_app_time],6,2)?> <?=substr($r_cashbag[pnt_app_time],8,2)?>:<?=substr($r_cashbag[pnt_app_time],10,2)?></td>
	<td align=center><font class=ver8><a href="javascript:popup('../../order/card/kcp/cancel_okcashbag.php?tno=<?=$r_cashbag[tno]?>',600,300)">[적립취소]</a></td>
</tr>
<?
	}
}

## 환불완료 상품
unset($rcancel);
$query = "select distinct a.cancel,b.* from ".GD_ORDER_ITEM." a left join ".GD_ORDER_CANCEL." b on a.cancel=b.sno where a.istep = 44 and a.cyn in ('r','y') and a.ordno='$ordno' and (b.rprice OR b.remoney OR b.rfee)";
$rres = $db->query($query);



if($db->count_($rres)){
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>환불내역정보</font> <font class=small1 color=6d6d6d>아래는 이미 환불완료된 내역입니다</font></div>

<table border=2 bordercolor=#F43400 style="border-collapse:collapse" width=100%>
<tr><td>

<table class=tb cellpadding=4 cellspacing=0>
	<tr>
		<td width=5% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>번호</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>환불수수료</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>환불금액</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>적립금환불금액</td>
		<td width=20% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>환불완료 처리일</td>
		<td width=15% align=center bgcolor=#F6F6F6><font class=small1 color=444444><b>처리상태</td>
	</tr>
<?
	while($row2 = $db->fetch($rres)){


		$i++;
		$row2[bankcode] = sprintf('%02d',$row2[bankcode]);

		$query = "select * from ".GD_ORDER_ITEM." where cancel='$row2[cancel]'";
		$res3 = $db->query($query);
		$body3 = "<table>";
		while($row3 = $db->fetch($res3)){
			$body3 .= "<tr><td width=200><div style='text-overflow:ellipsis;overflow:hidden;width:200px' nowrap><font class=small1 color=444444>".$row3[goodsnm]."</div></td>";
			$body3 .= "<td width=50 style=padding-left:10><font class=small1 color=444444>".$row3[ea]."개</td></tr>";
		}
		$body3 .= "</table>";
?>
	<tr>
		<td  style="padding:2px 10px" rowspan=2 align=center><font class=ver7 color=444444><?=$i?></td>
		<td align=center><font class=ver8><?=number_format($row2[rfee])?>원</td>

		<td align=center><font class=ver8 color=EA0095><b><?=number_format($row2[rprice])?></b>원</td>

		<td align=center><font class=ver8><?=number_format($row2[remoney])?>원</td>
		<td align=center><font class=ver81><?=$row2[ccdt]?>	</td>
		<td align=center><font class=small1 color=0074BA><b>환불완료</td>
	</tr>

	<tr>
		<td colspan=3>
			<div style='float:left'><?=$body3?></div>
		</div>
		<td colspan=2 align=center>
			<font class=small1 color=444444><b>환불계좌</b>: <?=$r_bank[$row2[bankcode]]?>&nbsp;<?=$row2[bankaccount]?>&nbsp;&nbsp;<b>예금주</b>: <?=$row2[bankuser]?>

		</td>
	</tr>
<?
	}
?>
	</table>
	</td></tr></table>
	<p>
<?
}
?>




<table width=100% cellpadding=0 cellspacing=0>
<col span=3 valign=top>
<tr>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>주문자정보</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>이름/ID</td>
		<td><? if ($data[m_id]) { ?><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>" popup="<?=$popup?>"><? } ?><font color=0074BA><b>
		<?=$data[nameOrder]?>
		<? if ($data[m_id]){ ?>/ <?=$data[m_id]?></b></font></span>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td>이메일</td>
		<td><font class=ver8><?=$data[email]?></font> <a href="javascript:popup('../member/email.php?type=direct&email=<?=$data['email']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_smsmailsend.gif" align=absmiddle></font></a></td>
	</tr>
	<tr>
		<td>연락처</td>
		<td class=ver8>
		<?=$data[phoneOrder]?><?getlinkPc080($data['phoneOrder'],'phone')?> / <?=$data[mobileOrder]?><?getlinkPc080($data['mobileOrder'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$data['mobileOrder']?>',780,600)"><img src="../img/btn_sms.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td>주문일</td>
		<td><font class=ver8><?=$data[orddt]?></td>
	</tr>
	</table>

	</td>
	<td width=10 nowrap></td>
	<td width=50%>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>수령자정보</font></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>수령자</td>
		<td>
		<input type=text name=nameReceiver value="<?=$data[nameReceiver]?>" style="width:115px" class=line>
		</td>
	</tr>
	<tr>
		<td>연락처</td>
		<td>
		<input type=text name="phoneReceiver" value="<?=$data[phoneReceiver]?>" style="width:95px" class=line><?getlinkPc080($data['phoneReceiver'],'phone')?><?if($popup != 1){?> /<?}?> <input type=text name="mobileReceiver" value="<?=$data[mobileReceiver]?>" style="width:95px" class=line><?getlinkPc080($data['mobileReceiver'],'mobile')?> <a href="javascript:popup('../member/popup.sms.php?mobile=<?=$data['mobileReceiver']?>',780,600)"><font color="#FF6C4B"><img src="../img/btn_sms.gif" align=absmiddle></font></a>
		</td>
	</tr>
	<tr>
		<td>주소</td>
		<td><font color=444444>
		<input type=text name=zipcode[] size=3 readonly value="<?=substr($data[zipcode],0,3)?>" class=line> - <input type=text name=zipcode[] size=3 readonly value="<?=substr($data[zipcode],4)?>" class=line>
		<a href="javascript:popup('../proc/popup_zipcode.php?form=opener.document.frmOrder',400,500)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>
		</td>
	</tr>
	<tr>
		<td></td>
		<td colspan=3><input type=text name=address style="width:100%" value="<?=$data[address]?>" class=line></td>
	</tr>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>결제정보</div>
	<table class=tb>
	<col class=cellC><col class=cellL>

	<tr>
		<td>결제종류</td>
		<td><?=$r_settlekind[$data[settlekind]]?><!--<?if($data[settlekind]=='c'){?>&nbsp;<a href='javascript:cardCancel();'>[신용카드취소]</a><?}?>--></td>
	</tr>
	<? if ($data[settlekind]=="a"){ ?>
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
		<td><input type=text name=bankSender value="<?=$data[bankSender]?>"></td>
	</tr>
	<? } else if ($data[settlekind]=="v"){ ?>
	<tr>
		<td>가상계좌</td>
		<td><?=$data[vAccount]?></td>
	</tr>
	<? } ?>
	<tr>
		<td>결제확인일</td>
		<td><font class=ver8>
		<? if ($data[settlekind]=="c" && $data[settlelog]){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['결과내용']?>]</b></font><? } ?>
		<?=$data[cdt]?>
		</td>
	</tr>
	<? if ($data['settlekind'] != 'c'){ ?>
	<tr>
		<td>현금영수증</td>
		<td>
		<?
		@include dirname(__FILE__).'/../../lib/cashreceipt.class.php';
		if (class_exists('cashreceipt'))
		{
			$cashreceipt = new cashreceipt();
			echo $cashreceipt->prnAdminReceipt($ordno);
		}
		else if ($data['cashreceipt']){
			echo $data['cashreceipt'];
		}
		?>
		<div><input type="checkbox" name="cashreceipt_ectway" value="Y" class="null" <?=($data['cashreceipt_ectway'] == 'Y' ? 'checked' : '');?>> 현금영수증 개별발급 및 별도발행 되었다면 체크하세요.(중복발행 방지위함)</div>
		</td>
	</tr>
	<? } ?>
	<? if ( !empty($_taxstate) ){ ?>
	<tr>
		<td>세금계산서</td>
		<td><?=$_taxstate?></td>
	</tr>
	<? } ?>
	<? if ($data[inflow]!="" && $data[inflow]!="sugi"){ ?>
	<tr>
		<td>제휴처주문</td>
		<td><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle> <?=$r_inflow[$data[inflow]]?></td>
	</tr>
	<? } ?>
	<? if ($data[eggyn]!="n"){ ?>
	<tr>
		<td>전자보증보험</td>
		<td>
		<? if ($data[eggno]!=""){ ?><a href="javascript:popupEgg('<?=$egg['usafeid']?>', '<?=$ordno?>')"><font class=ver71 color=0074BA><b><?=$data[eggno]?> <font class=small1>(내역서 보기)</b></font></a><? } ?>
		<? if ($data[eggno]=="" && $r_settlelog['결과메세지']){ ?><font class=small1 color=FD4700><b>[<?=$r_settlelog['결과메세지']?>]</b></font><? } ?>
		</td>
	</tr>
	<? } ?>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>배송정보</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($data[deli_title] != null){?>
	<tr>
		<td>배송방법</td>
		<td><?if($data['deli_msg'] != "개별 착불 배송비"){?><?=$data['deli_title']?><?}?> <?=( $data['deli_msg'] )?$data['deli_msg']:""?></td>
	</tr>
	<?}?>

	<?
	// 굿스플로를 이용한 택배 사용일때 송장번호 등 수정할 수 없음 (배송준비중 이상일 경우에만)
	if ($GF['type'] == 'casebyorder' || $GF['type'] == 'package') {
	?>
	<tr>
		<td>송장번호</td>
		<td>
			<? if ($GF['status'] == 'print_invoice') { ?>
			굿스플로 택배 연동서비스를 통해 발급 받은 송장번호는 직접 수정하실 수 없습니다.
			<div style="margin-top:3px;">
			<a href="../order/goodsflow.standby.php"class="extext">[굿스플로 상품수집 대기리스트 바로가기]</a>
			</div>

			<? } else { ?>
			굿스플로 택배 연동서비스를 통해 발급 받은 송장번호는 수정하실 수 없습니다.
			<? } ?>
		</td>
	</tr>
	<? } else { ?>
	<tr>
		<td>송장번호</td>
		<td>
		<? if($data['step'] >= 1 && $data['step'] < 4 && !$set['delivery']['basis']): ?>
			<select name="deliveryno">
			<option value="">==택배사==</option>
			<? foreach((array)$_delivery as $v): ?>
				<option value="<?=$v['deliveryno']?>" <?=$_selected['deliveryno'][$v['deliveryno']]?>><?=$v['deliverycomp']?>
			<? endforeach; ?>
			</select>
			<input type='text' name='deliverycode' value="<?=$data['deliverycode']?>" class=line>
		<? else: ?>
			<? if($data['deliverycode']) : ?>
				<?=$r_delivery[$data['deliveryno']]?> <?=$data['deliverycode']?>
				<div class=small1 color=444444>아래 배송상태추적 버튼을 눌러 확인하세요.</div>
			<? endif; ?>
			<input type='hidden' name='deliveryno' value='<?=$data['deliveryno']?>'>
			<input type='hidden' name='deliverycode' value='<?=$data['deliverycode']?>'>
		<? endif; ?>
		</td>
	</tr>
	<? } ?>

	<? if ($data[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>배송추적</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>배송일(출고일)</td>
		<td><font class=ver8><?=$data[ddt]?></td>
	</tr>
	<? if ($data[confirmdt]){ ?>
	<tr>
		<td>배송완료일</td>
		<td><font class=ver8><?=$data[confirmdt]?>(<?=$data[confirm]?>)</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr><tr><td height=15></td></tr>
<tr>
	<td>

	<div class=title2>
	<span style="padding-right:10px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>요청사항/상담메모</span>
	<a href="javascript:popupLayer('popup.log.php?ordno=<?=$ordno?>')"><img src="../img/btn_orderlog.gif" align=absmiddle border=0></a>
	</div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr height=25>
		<td>고객요청사항</td>
		<td><textarea name=memo style="width:100%;height:100px"><?=$data[memo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>고객상담메모</td>
		<td><textarea name=adminmemo style="width:100%;height:100px"><?=$data[adminmemo]?></textarea></td>
	</tr>
	<tr height=25>
		<td>결제로그</td>
		<td><textarea style="width:100%;height:100px;overflow:visible;font:9pt 굴림체;padding:10px 0 0 8px"><?=trim($data[settlelog])?></textarea></td>
	</tr>
	</table>

	</td>
	<td></td>
	<td>

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>취소내역리스트 <font class=small1 color=6d6d6d>(주문취소를 요청한 내역을 볼 수 있습니다)</font></div>
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
		<div style="float:left" class=ver81><font color=555555><?=$data[regdt]?></div>
		<div style="float:right"><font class=small1><?=$r_cancel[$data[code]]?></div>
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
		<tr >
			<td><font class=small1 color=444444>- <?=$item[goodsnm]?> <?=$item[ea]?>개</td>
			<td align=right><font class=small1 color=EA0095><?=$r_stepi[$item[prev]][$item[next]]?></td>
		</tr>
		<? } ?>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan=2 bgcolor=#ffffff align=left style="padding:5px">
		<? if ($data[memo]){ ?>
		<div style="margin:5px" class=small><font color=0074BA>처리메모:</font> <font color=555555><?=nl2br($data[memo])?></div>
		<? } ?>
		</td>
	</tr>

	<? } ?>

	</table>

	</td>
</tr>

</table>

<?
$res = $db->query("select * from ".GD_ORDER_ITEM_LOG." where ordno='$ordno'");
if($db->count_($res)){
?>
<p />
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color="#508900">주문상품 변경사항</font></div>
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29>
	<td bgcolor="#F6F6F6" align=center>순번</td>
	<td bgcolor="#F6F6F6" align=center>상품명</td>
	<td bgcolor="#F6F6F6" align=center>변경사항</td>
	<td bgcolor="#F6F6F6" align=center>일자</td>
</tr>
<col align="center" width="50"><col align=center><col align=left><col align=center width="100">
<?
$cnum = 0;
while($clog = $db->fetch($res)){
	$cnum++;
?>
<tr height=60>
	<td><?=$cnum?></td>
	<td><?=$clog['goodsnm']?></td>
	<td><?=nl2br($clog['log'])?></td>
	<td><?=substr($clog['regdt'],0,10)?><br><?=substr($clog['regdt'],10)?></td>
</tr>
<?}?>
</table><p />
<?}?>

<div class=button <?=$hiddenPrint?>>
<input type=image src="../img/btn_modify.gif">
<?
if(!preg_match('/popup.order.php/',$_SERVER[SCRIPT_FILENAME])){
?>
<a href='<?=$referer?>'><img src='../img/btn_list.gif'></a>
<?
}
?>
</div>

</form>

<script>window.onload = function(){ UNM.inner();};</script>
<?
if($inflow=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // 인터파크_인클루드
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // 인터파크_인클루드
}

?>