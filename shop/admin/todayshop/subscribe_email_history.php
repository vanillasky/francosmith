<?
$location = "투데이샵 > 정기구독 설정/신청자관리";
include "../_header.php";
include "../../lib/page.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsCfg = $todayShop->cfg;



list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_SUBSCRIBE);


$pg = new Page($_GET['page'],$_GET['page_num']);


$db_table = "
	".GD_TODAYSHOP_SUBSCRIBE." AS SC
	LEFT JOIN ".GD_MEMBER." AS MB
	ON SC.m_id = MB.m_id
";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = " SC.*, MB.name";
$pg->setQuery($db_table,$where,'');
$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript">

</script>



<form name=frmList>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">이메일(정기구독) 발송 내역 <span>정기구독 신청자들에게 발송한 이메일 내역을 확인할 수 있습니다.</span></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td rowspan="2">고객검색</td>
		<td class="noline">
			<label><input type="radio" name="dddd" value="">전체</label>
			<label><input type="radio" name="dddd" value="e">이메일</label>
			<label><input type="radio" name="dddd" value="s">SMS</label>
		</td>
	</tr>
	<tr>
		<!--td rowspan="2">고객검색</td-->
		<td>
			<select name="skey">
				<option name="email">이메일</option>
				<option name="phone">휴대폰</option>
			</select>
			<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>

	<tr>
		<td>신청일</td>
		<td>
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	</table>
	<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
	<div style="padding-top:15px"></div>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td class=pageInfo>
			<font class=ver8>총 <b><?=$total?></b>개, 검색 <b><?=$pg->recode[total]?></b>개, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font>
		</td>
	</tr>
	</table>
</form>




<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colSpan=16></td></tr>
<tr class=rndbg>
	<th width="60">선택</th>
	<th width="60">번호</th>
	<th>회원명</th>
	<th>이메일</th>
	<th>휴대폰</th>
	<th>신청일</th>
	<th>관리</th>
</tr>
<tr><td class=rnd colSpan=16></td></tr>
<col width=40 span=2 align=center>
<? while ($data=$db->fetch($res)) { ?>
<tr><td height=4 colSpan=16></td></tr>
<tr height=25 align="center">
	<td><input type="checkbox" name="chk[]" value="<?=$data['sno']?>"></td>
	<td><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td><?=$data['name']?></td>
	<td><?=$data['email']?></td>
	<td><?=$data['phone']?></td>
	<td><?=$data['regdt']?></td>
	<td>
	<A onclick="return confirm('정말로 삭제하시겠습니까?');" href="./indb.subscribe.php?mode=delete&sno=<?=$data['sno']?>"><IMG src="../img/i_del.gif"></A>
	</td>

</tr>
<tr><td height=4></td></tr>
<tr><td colSpan=16 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">멘트..</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>