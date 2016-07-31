<?
@include "../../conf/merchant.php";

$location = "주문관리  > 링크프라이스 주문리스트";
include "../_header.php";
include "../../lib/page.class.php";
?>

<div class="title title_top">링크프라이스 주문리스트</div>

<form name=frmsearch method=post action="../../partner/daily_fix.php" target=myiframe>

<table class=tb>
<col class=cellC><col class=cellL>


<tr>
	<td>주문일</td>
	<td>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)"> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class="button_top">
<input type=image src="../img/btn_search2.gif">
</div>

</form>

<!-- <table class=tb> -->
<table width=100% cellspacing=0 cellpadding=0 border=0>
<tr><td><iframe name=myiframe src='../../blank.txt' width=100% height=700 frameborder=0></iframe></td></tr>
</table>


<? include "../_footer.php"; ?>