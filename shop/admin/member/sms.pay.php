<?

$location = "SMS설정 > SMS 포인트충전";
include "../_header.php";

$checked[idx][0] = "checked";

### SMS 가격데이타 가져오기
$minUnit = 0;
$smsPrice = array();
$out = readurl('http://www.godo.co.kr/userinterface/_godoConn/conf/sms.cfg');
$div = explode(chr(10),$out);
foreach ($div as $v)
{
	$div2 = explode('|',$v);

	$key = $div2[0];
	$useFee = $div2[1] * 10 / 11;
	$bonus = $div2[2];
	if ($bonus) $key -= $bonus;
	$unit = round($useFee / $key, 1);
	// LMS 는 SMS 의 3배!!!
	$unit2 = $unit*3;

	$smsPrice[$key] = array('useFee' => $useFee, 'unit' => $unit, 'bonus' => $bonus, 'unit2' => $unit2);

	if ($minUnit == 0 || $minUnit > $unit) $minUnit = $unit;
}

### 최대건수
$maxSms = end(array_keys($smsPrice));
?>

<script>

function popupPay()
{
	var fm = document.frmSms;
	window.open("","popupPay","width=500,height=450");
	fm.action = "http://www.godo.co.kr/userinterface/_godoConn/vaspay.php";
	fm.target = "popupPay";
}

function sms_sync(){
	var obj = document.ifrmHidden;
	obj.location.href = "../sms.sync.php";
}

</script>

<div class="title title_top"><font  face=굴림 color=black><b>SMS</b></font> 포인트충전<span>SMS 사용을 위한 포인트를 충전합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border=1 bordercolor=cccccc style="border-collapse:collapse" cellpadding=4 cellspacing=0>
<tr><td>
<table border=3 bordercolor=#cccccc style="border-collapse:collapse">
	<tr>
		<td width=762 height=50 align=center bgcolor=ADFFFE>잔여포인트 : 현재 <font face=굴림 size=5 color=#04062F><b><u><?=number_format(getSmsPoint())?></u></b></font></span> Point <a href="javascript:sms_sync();"><img src="../img/btn_point_synchronization.gif" border=0 align=absmiddle hspace=2></a></td>
	</tr>
</table>
</td></tr></table>

<div style="padding-top:5px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS 발송서비스는 포인트충전식으로 포인트가 있어야만 발송이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">충전금액은 발송건수에 따라 건당 최저 <?=$minUnit;?>원입니다. (충전금액은 부가세 별도입니다)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">충전한 SMS 포인트는 환불되지 않습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:5px"></div>



<form name=frmSms method=post onsubmit="popupPay()">
<input type=hidden name=sno value="<?=$godo[sno]?>">
<input type=hidden name=mode value="sms">

<table width=780 border=1 bordercolor="#cccccc" style="border-collapse:collapse" cellpadding=0 cellspacing=0>
<caption align=right>※ 아래 사용요금과 단가는 <font color=red><b>부가세 별도</b></font> 가격입니다.</caption>
<tr bgcolor=#f7f7f7 height=27 align=center>
	<th width=100>결제선택</th>
	<th>발송 건/포인트</th>
	<th>사용요금</th>
	<th>SMS(건당 1포인트)</th>
	<th>LMS(건당 3포인트)</th>
</tr>
<? $idx=0; foreach ($smsPrice as $k=>$v){ ?>
<tr height=25 align=center>
	<td class=noline><input type=radio name=idx value="<?=$idx?>" <?=$checked[idx][$idx++]?>>
	<td><font class=ver8><b><?=number_format($k)?></b> 건/포인트</td>
	<td><font class=ver8><b><?=number_format($v['useFee'])?></b>원</td>
	<td><font class=ver8><?=$v['unit']?>원/1건</td>
	<td><font class=ver8><?=$v['unit2']?>원/1건</td>
</tr>
<? } ?>
<!-- 별도문의 : Start -->
<tr height=25 align=center>
	<td></td>
	<td><font class=ver8><b><?=number_format($maxSms)?></b> 건 이상</td>
	<td style="background-color:#E2F5FA;"><font class="ver8"><b>-</b></font></td>
	<td><font class=ver8><b>별도협의</b></font></td>
	<td><font class=ver8>-</font></td>
</tr>
<!-- 별도문의 : End -->
</table>

<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1"><?=number_format($maxSms)?> 건 이상 대량으로 충전하실 경우 별도로 문의주세요.</font> <a href="mailto:service2@godo.co.kr"><img src="../img/btn_inquiry.gif" align="absmiddle"></a></div>
<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1">SMS 포인트를 충전한 내역을 볼 수 있습니다</font> <a href="javascript:popupLayer('http://www.godo.co.kr/userinterface/_godoConn/Mysmslog.php?sno=<?=$godo['sno']?>',700,350)"><img src="../img/btn_sattlelog.gif" align="absmiddle"></a></div>
<div style="padding-top:10px"></div>

<table width=780 border=0>
<tr><td align=center class=noline>
<input type=image src="../img/btn_point_pay.gif">
</td></tr></table>
</form>


<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">발송완료된 건수만 포인트차감됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS(단문)은 최대 80bytes, LMS(장문)은 최대 2000byte 까지 전송 됩니다.</td></tr>
<!--<tr><td><img src="../img/icon_list.gif" align="absmiddle">충전한 <font color=0074BA>SMS 포인트는 환불되지 않습니다.</font></td></tr>-->
</table>
</div>
<script>cssRound('MSG02')</script>


<? include "../_footer.php"; ?>