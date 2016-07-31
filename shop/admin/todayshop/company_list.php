<?
//$hiddenLeft = 1;
$location = "투데이샵 > 공급업체리스트";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

// 변수 받기
$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;	// 페이지별 리스팅 갯수
$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : 'name';	// 키워드 검색 대상 필드 (기본값: 업체명)
$_GET['sword'] = isset($_GET['sword']) ? $_GET['sword'] : '';	// 키워드

// where 절
if ($_GET['sword']) $where[] = "cp_".$_GET['skey']." LIKE '%".$_GET['sword']."%'";
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59')";



// 전체 레코드수
list ($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_COMPANY);



// 페이징
$db_table = GD_TODAYSHOP_COMPANY;
$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where);
$pg->exec();
$res = $db->query($pg->query);


?>

<script type="text/javascript">
<!--

function fnCompanyDelete(sno) {

	if (confirm("삭제할꺼유?"))
	{
		var f = document.frmCompany;
		f.sno.value = sno;
		f.action = './indb.company.php';
		f.submit();

	}

}

function fnCompanyForm(sno) {

	if( typeof(sno) == 'number') {
		parent.location.href = './company_form.php?sno='+sno;
	}

}
//-->
</script>

<form name="frmCompany" method="post" action="" target="_self">
<input type="hidden" name="sno">
<input type="hidden" name="mode" value="delete">
<input type="hidden" name="page" value="<?=$_GET['page']?>">
<input type="hidden" name="skey" value="<?=$_GET['skey']?>">
<input type="hidden" name="sword" value="<?=$_GET['sword']?>">
</form>

<form name=frmList>
	<div class="title title_top">공급업체리스트<span>입점 되어 있는 공급업체의 정보를 조회하고 관리할 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=11')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>검색어</td>
		<td>
			<?
			$_arSelect = array('업체명'=>'name','담당자명'=>'man');
			?>
			<select name="skey">
				<? foreach($_arSelect as $k=>$v) { ?>
				<option value="<?=$v?>" <?=($_GET['skey'] == $v) ? 'selected' : ''?>><?=$k?></option>
				<? } ?>
			</select>
			<input type=text name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>
	<tr>
		<td>등록기간</td>
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
		<td align=right>
			<table cellpadding=0 cellspacing=0 border=0>
			<tr>
				<td valign=bottom></td>
					<td style="padding-left:20px">
					<img src="../img/sname_output.gif" align=absmiddle>
					<select name=page_num onchange="this.form.submit()">
					<?
					$r_pagenum = array(10,20,40,60,100);
					foreach ($r_pagenum as $v){
					?>
					<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>개 출력
					<? } ?>
					</select>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</form>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<colgroup>
	<col width="40" align="center">
	<col width="">
	<col width="">
	<col width="">
	<col width="150">
	<col width="100">
	<col width="100">
</colgroup>
<tr><td class=rnd colspan=7></td></tr>
<tr class=rndbg>
	<th width=60>번호</th>
	<th>업체명</th>
	<th>담당자명</th>
	<th>연락처</th>
	<th>등록일</th>
	<th>수정</th>
	<th>삭제</th>
</tr>
<tr><td class=rnd colspan=7></td></tr>
<?
while ($data=$db->fetch($res)){
?>
<tr><td height=4 colspan=7></td></tr>
<tr height=25  align="center">
	<td><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td><?=$data['cp_name']?></td>
	<td><?=$data['cp_man']?></td>
	<td><?=$data['cp_phone']?></td>
	<td><?=$data['regdt']?></td>
	<td align=center>
		<a href="javascript:fnCompanyForm(<?=$data['cp_sno']?>);"><img src="../img/i_edit.gif"></a>
	</td>
	<td align=center>
		<a href="javascript:fnCompanyDelete(<?=$data['cp_sno']?>);"><img src="../img/i_del.gif"></a>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=7 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div>

<? include "../_footer.php"; ?>