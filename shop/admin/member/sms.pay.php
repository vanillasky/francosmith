<?

$location = "SMS���� > SMS ����Ʈ����";
include "../_header.php";

$checked[idx][0] = "checked";

### SMS ���ݵ���Ÿ ��������
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
	// LMS �� SMS �� 3��!!!
	$unit2 = $unit*3;

	$smsPrice[$key] = array('useFee' => $useFee, 'unit' => $unit, 'bonus' => $bonus, 'unit2' => $unit2);

	if ($minUnit == 0 || $minUnit > $unit) $minUnit = $unit;
}

### �ִ�Ǽ�
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

<div class="title title_top"><font  face=���� color=black><b>SMS</b></font> ����Ʈ����<span>SMS ����� ���� ����Ʈ�� �����մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border=1 bordercolor=cccccc style="border-collapse:collapse" cellpadding=4 cellspacing=0>
<tr><td>
<table border=3 bordercolor=#cccccc style="border-collapse:collapse">
	<tr>
		<td width=762 height=50 align=center bgcolor=ADFFFE>�ܿ�����Ʈ : ���� <font face=���� size=5 color=#04062F><b><u><?=number_format(getSmsPoint())?></u></b></font></span> Point <a href="javascript:sms_sync();"><img src="../img/btn_point_synchronization.gif" border=0 align=absmiddle hspace=2></a></td>
	</tr>
</table>
</td></tr></table>

<div style="padding-top:5px"></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS �߼ۼ��񽺴� ����Ʈ���������� ����Ʈ�� �־�߸� �߼��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ݾ��� �߼۰Ǽ��� ���� �Ǵ� ���� <?=$minUnit;?>���Դϴ�. (�����ݾ��� �ΰ��� �����Դϴ�)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ SMS ����Ʈ�� ȯ�ҵ��� �ʽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<div style="padding-top:5px"></div>



<form name=frmSms method=post onsubmit="popupPay()">
<input type=hidden name=sno value="<?=$godo[sno]?>">
<input type=hidden name=mode value="sms">

<table width=780 border=1 bordercolor="#cccccc" style="border-collapse:collapse" cellpadding=0 cellspacing=0>
<caption align=right>�� �Ʒ� ����ݰ� �ܰ��� <font color=red><b>�ΰ��� ����</b></font> �����Դϴ�.</caption>
<tr bgcolor=#f7f7f7 height=27 align=center>
	<th width=100>��������</th>
	<th>�߼� ��/����Ʈ</th>
	<th>�����</th>
	<th>SMS(�Ǵ� 1����Ʈ)</th>
	<th>LMS(�Ǵ� 3����Ʈ)</th>
</tr>
<? $idx=0; foreach ($smsPrice as $k=>$v){ ?>
<tr height=25 align=center>
	<td class=noline><input type=radio name=idx value="<?=$idx?>" <?=$checked[idx][$idx++]?>>
	<td><font class=ver8><b><?=number_format($k)?></b> ��/����Ʈ</td>
	<td><font class=ver8><b><?=number_format($v['useFee'])?></b>��</td>
	<td><font class=ver8><?=$v['unit']?>��/1��</td>
	<td><font class=ver8><?=$v['unit2']?>��/1��</td>
</tr>
<? } ?>
<!-- �������� : Start -->
<tr height=25 align=center>
	<td></td>
	<td><font class=ver8><b><?=number_format($maxSms)?></b> �� �̻�</td>
	<td style="background-color:#E2F5FA;"><font class="ver8"><b>-</b></font></td>
	<td><font class=ver8><b>��������</b></font></td>
	<td><font class=ver8>-</font></td>
</tr>
<!-- �������� : End -->
</table>

<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1"><?=number_format($maxSms)?> �� �̻� �뷮���� �����Ͻ� ��� ������ �����ּ���.</font> <a href="mailto:service2@godo.co.kr"><img src="../img/btn_inquiry.gif" align="absmiddle"></a></div>
<div style="margin-top:5px; color:#5A5A5A;">&#149; <font class="small1">SMS ����Ʈ�� ������ ������ �� �� �ֽ��ϴ�</font> <a href="javascript:popupLayer('http://www.godo.co.kr/userinterface/_godoConn/Mysmslog.php?sno=<?=$godo['sno']?>',700,350)"><img src="../img/btn_sattlelog.gif" align="absmiddle"></a></div>
<div style="padding-top:10px"></div>

<table width=780 border=0>
<tr><td align=center class=noline>
<input type=image src="../img/btn_point_pay.gif">
</td></tr></table>
</form>


<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�߼ۿϷ�� �Ǽ��� ����Ʈ�����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">SMS(�ܹ�)�� �ִ� 80bytes, LMS(�幮)�� �ִ� 2000byte ���� ���� �˴ϴ�.</td></tr>
<!--<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ <font color=0074BA>SMS ����Ʈ�� ȯ�ҵ��� �ʽ��ϴ�.</font></td></tr>-->
</table>
</div>
<script>cssRound('MSG02')</script>


<? include "../_footer.php"; ?>