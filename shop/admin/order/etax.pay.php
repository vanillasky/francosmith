<?

$location = "세금계산서관리 > 서비스가입 및 포인트충전";
include "../_header.php";

$checked[idx][0] = "checked";

### TAX 가격데이타 가져오기
$out = readurl("http://www.godo.co.kr/userinterface/_godoConn/conf/tax.cfg");
$div = explode(chr(10),$out);
foreach ($div as $v){
	$div2 = explode("|",$v);
	$tax_price[$div2[0]] = $div2[1];
}
?>
<script src="../tax.ajax.js"></script>

<div class="title title_top"><font  face=굴림 color=black><b>서비스가입 및 포인트충전<span>전자세금계산서 사용을 위한 포인트를 충전합니다</span></div>

<table border=1 bordercolor=cccccc style="border-collapse:collapse" cellpadding=4 cellspacing=0>
<tr><td>
<table border=3 bordercolor=#cccccc style="border-collapse:collapse">
	<tr>
		<td width=762 height=50 align=center bgcolor=ADFFFE>잔여포인트 : 현재 <font face=굴림 size=5 color=#04062F><b><u><?=number_format($godo[tax])?></u></b></font></span> Point</td>
	</tr>
</table>
</td></tr></table>

<div style="padding-top:5px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자세금계산서 발행서비스는 포인트충전식으로 포인트가 있어야만 발행이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">포인트를 충전하시려면 전자세금계산서를 지원하는 LG데이콤 웹택스21에 먼저 가입하셔야 합니다. <a href="./etax.request.php"><img src="../img/btn_taxservice_apply_s.gif" align=absmiddle></a></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">충전금액은 발행건수에 따라 건당 최저 200원입니다. (아래 결제금액은 부가세별도입니다)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">충전한 포인트는 환불되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발행된 전자세금계산서 건당 1포인트가 차감됩니다. (공인인증서 승인절차에 따라 발행된 전자세금계산서가 반려된 경우에도 발행건수에 포함됩니다)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(주)플라이폭스와 (주)LG데이콤 양사가 전자 세금계산서 서비스를 제공합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:5px"></div>



<form name=frmTax method=post onsubmit="return TPR.popupPay(this);">
<input type=hidden name=sno value="<?=$godo[sno]?>">
<input type=hidden name=mode value="tax">

<table width=780 border=1 bordercolor="#cccccc" style="border-collapse:collapse" cellpadding=0 cellspacing=0>
<tr bgcolor=#f7f7f7 height=27 align=center>
	<th width=100>결제선택</th>
	<th>발행 건수/포인트</th>
	<th>결제금액</th>
	<th>단가</th>
</tr>
<? $idx=0; foreach ($tax_price as $k=>$v){ $v = $v * 10 / 11; ?>
<tr height=25 align=center>
	<td class=noline><input type=radio name=idx value="<?=$idx?>" <?=$checked[idx][$idx++]?>>
	<td><font class=ver8><b><?=number_format($k)?></b> 건/포인트</td>
	<td><font class=ver8><b><?=number_format($v)?></b>원 <font color=6d6d6d>(부가세별도)</font></td>
	<td><font class=ver8><?=$v/$k?>원/1건 <font color=6d6d6d>(부가세별도)</font></td>
</tr>
<? } ?>
</table>

<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1">포인트를 충전한 내역을 볼 수 있습니다</font> <a href="javascript:popupLayer('http://www.godo.co.kr/userinterface/_godoConn/Mytaxlog.php?sno=<?=$godo['sno']?>',700,350)"><img src="../img/btn_sattlelog.gif" align="absmiddle"></a></div>
<div style="padding-top:10px"></div>

<div class="button" id="avoidSubmit" style="width:780px;">
<input type="image" src="../img/btn_taxpoint_pay.gif">
<a href="./etax.request.php"><img src="../img/btn_taxservice_apply.gif"></a>
</div>
</form>


<? include "../_footer.php"; ?>