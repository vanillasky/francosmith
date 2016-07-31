<?
/* 두 날짜 간 차이 일수 계산 */
function getGapDay($sdate, $edate) {
	$tmp_sdate = explode(' ', $sdate);
	$tmp_edate = explode(' ', $edate);

	$arr_sdate = explode('-', $tmp_sdate[0]);
	$arr_edate = explode('-', $tmp_edate[0]);

	$ts_sdate = mktime(0,0,0, $arr_sdate[1], $arr_sdate[2], $arr_sdate[0]);
	$ts_edate = mktime(0,0,0, $arr_edate[1], $arr_edate[2], $arr_edate[0]);

	$gap_day = floor(($ts_edate - $ts_sdate +1)/60/60/24);

	return $gap_day;
}

$past_date = getGapDay($expire_dt, $now_date);
?>
<script type="text/javascript">
function noMsgToday(cKey, cValue, cPeriod) {

	var date = new Date();
	date.setDate(date.getDate() + cPeriod);
	setCookie(cKey, cValue, date);
	

	document.getElementById("appsExpireMSG").style.display = "none";

}

window.onload = function(){ 
	if(!getCookie('appsExpireAlert')) document.getElementById("appsExpireMSG").style.display = "";
}

</script>

<style type="text/css">
.ST_codeInsertBorder {position:absolute;border:3px #FFFFFF solid;background-color:#78B300;padding:5px;display:none;visibility:hidden;}
.ST_codeInsertBox {background-color:#FFFFFF;padding:3px;}
.ST_codeInsertBox .ST_title {font-family:Dotum;font-size:8pt;color:#1D8E0D;}
.ST_codeInsertBox .ST_button img {margin:0px 0px 0px 5px;}
</style>


<div id="appsExpireMSG" style="position:absolute;left:550px;top:300px;display:none;background-color:#FFFFFF;z-index:99">
<table width="500px" border="0" cellspacing="0" cellpadding="0" style="border:3px solid #000000;">
	<tr>
		<td style="padding:18px">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="22"><img src="../img/pop_bu.gif" /></td>
					<td style="color: #000000; font-weight: bold;">서비스 사용기간 만료</td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td></td>
					<td>쇼핑몰 App 서비스 사용기간이 만료 되었습니다.<br />
						사용기간 만료 후 30일이 지나면 서비스가 삭제됩니다.<br />
						서비스 사용기간을 연장 해 주시기 바랍니다.<br />
						<br />
						<b>서비스 사용기간 만료 <?=$past_date?>일 차</b></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td bgcolor="#000000">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="padding-left:10px;"><a href="javascript:;" onclick="noMsgToday('appsExpireAlert', 'off', 1);" style="font-size:11px; color: #ffffff;">오늘 하루 열지 않기</a></td>
					<td align="right" style="padding-right:10px"><a href="javascript:;" onclick="document.getElementById('appsExpireMSG').style.display='none'"><img src="../img/btn_close.gif" border="0" /></a></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>