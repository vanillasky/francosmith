<?
$location = "투데이샵 > 정기구독 설정/신청자관리";
include "../_header.php";
include "../../lib/page.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}
$tsCfg = $todayShop->cfg;
$tsCategory = $todayShop->getCategory(true);

$tsCfg['subscribe'] = unserialize(stripslashes($tsCfg['subscribe']));
$tsCfg['interest'] = unserialize(stripslashes($tsCfg['interest']));

if(!$tsCfg['subscribe']['use']) $tsCfg['subscribe']['use'] = 'n';
$checked['subscribe']['use'][$tsCfg['subscribe']['use']] = 'checked';

if(!$tsCfg['subscribe']['email']) $tsCfg['subscribe']['email'] = '0';
$checked['subscribe']['email'][$tsCfg['subscribe']['email']] = 'checked';

if(!$tsCfg['subscribe']['sms']) $tsCfg['subscribe']['sms'] = '0';
$checked['subscribe']['sms'][$tsCfg['subscribe']['sms']] = 'checked';


if(!$tsCfg['interest']['use']) $tsCfg['interest']['use'] = 'n';
$checked['interest']['use'][$tsCfg['interest']['use']] = 'checked';

if(!$tsCfg['interest']['member']) $tsCfg['interest']['member'] = '0';
$checked['interest']['member'][$tsCfg['interest']['member']] = 'checked';

if(!$tsCfg['interest']['subscribe']) $tsCfg['interest']['subscribe'] = '0';
$checked['interest']['subscribe'][$tsCfg['interest']['subscribe']] = 'checked';



$where = array();

if ($_GET['stype'] != '') $where[] = " SC.".$_GET['stype']." <> ''";
if ($_GET['sword'] != '') $where[] = " SC.".$_GET['skey']." like '%".$_GET['sword']."%'";
if ($_GET['category'] != '') $where[] = " SC.category = '".$_GET['category']."'";



list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_SUBSCRIBE);


$pg = new Page($_GET['page'],$_GET['page_num']);


$db_table = "
	".GD_TODAYSHOP_SUBSCRIBE." AS SC
	LEFT JOIN ".GD_MEMBER." AS MB
	ON SC.m_id = MB.m_id
	LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS TC
	ON SC.category = TC.category

";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = " SC.*, MB.name, TC.catnm";
$pg->setQuery($db_table,$where,'');
$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript">
function fnCheckForm(f) {

	// 관심분류
	$$('input[name="interest[use]"]:checked').each(function(item){

		if (item.value == 'y') {
			// 회원, 정기구동 1개 이상 체크
			if (($$('input[name="interest[member]"]:checked').size() + $$('input[name="interest[subscribe]"]:checked').size()) < 1) {
				alert('회원/정기구독 분류에 체크해 주세요.');
				return false;
			}
		}
		else {


		}

	});


	// 정기구독
	$$('input[name="subscribe[use]"]:checked').each(function(item){

		if (item.value == 'y') {
			// 발송 수단 1개 이상 체크
			if (($$('input[name="subscribe[email]"]:checked').size() + $$('input[name="subscribe[sms]"]:checked').size()) < 1) {
				alert('발송수단에 체크해 주세요.');
				return false;
			}
		}
		else {


		}

	});

	return true;

}

</script>

<form name="frmConfig" method="post" action="indb.config.php" target="ifrmHidden" onSubmit="return fnCheckForm(this);"/>

	<div class="title title_top">정기구독 신청 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>정기구독 사용</td>
		<td class="noline">
			<label><input type="radio" name="subscribe[use]" value="y" <?=$checked['subscribe']['use']['y']?> />사용</label>
			<label><input type="radio" name="subscribe[use]" value="n" <?=$checked['subscribe']['use']['n']?> />미사용</label>
			<span class="small"><font class="extext">정기구독 기능 사용 여부를 설정합니다.</font></span>
		</td>
	</tr>
	<tr>
		<td>발송 수단 선택</td>
		<td class="noline">
			<label><input type="checkbox" name="subscribe[email]" value="1" <?=$checked['subscribe']['email']['1']?> />이메일</label>
			<label><input type="checkbox" name="subscribe[sms]" value="1" <?=$checked['subscribe']['sms']['1']?> />SMS</label>
		</td>
	</tr>
	</table>

	<div style="margin-top:10px;"></div>


	<div class="title title_top">관심분류지역 사용설정<span>고객이 원하는 관심분류를 선택할 수 있도록 하는 기능입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td rowspan="2">사용설정</td>
		<td class="noline">
			<label><input type="radio" name="interest[use]" value="y" <?=$checked['interest']['use']['y']?> /> 사용</label>
			<label><input type="radio" name="interest[use]" value="n" <?=$checked['interest']['use']['n']?> /> 미사용</label>
			<font class="extext">정기구독 신청 및 회원관심 관심 분류를 함께 선택할 수 있습니다.</font>
		</td>
	</tr>
	<tr>
		<!--td>사용설정</td-->
		<td class="noline">
			<label><input type="checkbox" name="interest[member]" value="1"  <?=$checked['interest']['member']['1']?> /> 회원</label>
			<label><input type="checkbox" name="interest[subscribe]" value="1"  <?=$checked['interest']['subscribe']['1']?> /> 정기구독</label>
			<font class="extext">회원관심분류 설정 및 정기구독 신청자가 원하는 분류를 선택할 수 있습니다.</font>
		</td>
	</tr>
	</table>

	<p style="margin:3px;line-height:150%;">
	회원관심 분류(지역)설정은  로그인 후 회원이 선택한 분류의 상품을 메인으로 노출됩니다. <br>
	정기구독신청시 관심분류로 선택한 경우 해당 분류에 대한 정보만을 발송할 수 있습니다.
	</p>







	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>

<form name=frmList>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">정기구독 신청자 리스트 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td rowspan="2">고객검색</td>
		<td class="noline">
			<label><input type="radio" name="stype" value="" <?=$_GET['stype'] == '' ? 'checked' : '' ?>>전체</label>
			<label><input type="radio" name="stype" value="email" <?=$_GET['stype'] == 'email' ? 'checked' : '' ?>>이메일</label>
			<label><input type="radio" name="stype" value="phone" <?=$_GET['stype'] == 'phone' ? 'checked' : '' ?>>SMS</label>
		</td>
	</tr>
	<tr>
		<!--td rowspan="2">고객검색</td-->
		<td>
			<select name="skey">
				<option value="email">이메일</option>
				<option value="phone">휴대폰</option>
			</select>
			<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>
	<tr>
		<td>관심분류</td>
		<td class="noline">
			<select name="category">
				<option value="">-관심분류를 선택해 주세요-</option>
				<? foreach ($tsCategory as $v ) { ?>
				<option value="<?=$v['category']?>" <?=$_GET['category'] == $v['category'] ? 'selected' : ''?>><?=$v['catnm']?></option>
				<? } ?>
			</select>
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

	<th>관심분류(지역)</th>

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
	<td><?=$data['catnm']?></td>
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

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=6% style="padding-left:7"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div></td>
<td width=6%></td>
</tr></table>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">멘트..</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>