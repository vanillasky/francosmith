<?

$location = "전자세금계산서 관리 > 전자발행요청리스트";
include "../_header.php";

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no where step=0"); # 총 레코드수

### 변수할당
$tax_step = array( '발행신청', '발행승인', '발행완료', '전자발행' );
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "issuedate desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";

### 목록
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.m_no, b.m_id, b.name as m_name";
$db_table = "".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
$where[] = "step=0";

if ($_GET[skey] && $_GET[sword]){
	$sordno = array();
	if ( $_GET[skey]== 'all' || $_GET[skey]== 'm_name' ){
		$res = $db->query("select a.ordno from ".GD_TAX." a left join ".GD_ORDER." b on a.ordno=b.ordno where b.nameOrder like '%$_GET[sword]%'");
		while( $row = $db->fetch($res)) $sordno[] = $row[ordno];
	}

	if ( $_GET[skey]== 'all' ){
		$where[] = "(concat( a.company, a.name, ifnull(b.name, ''), ifnull(m_id, ''), ordno ) like '%$_GET[sword]%'" .
		(count($sordno) ? " or find_in_set(ordno, '" . implode(",", $sordno) . "')" : "")
		. ")";
	}
	else if ( $_GET[skey]== 'm_id' ) $where[] = "b.m_id like '%$_GET[sword]%'";
	else if ( $_GET[skey]== 'm_name' ) $where[] = "(b.name like '%$_GET[sword]%'" . (count($sordno) ? " or find_in_set(ordno, '" . implode(",", $sordno) . "')" : "") . ")";
	else $where[] = "a.$_GET[skey] like '%$_GET[sword]%'";
}

if ( $_GET[sbusino] <> '' ) $where[] = "a.busino='" . $_GET[sbusino] . "'"; # 분류검색

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $where[] = "issuedate between date_format({$_GET[sregdt][0]},'%Y-%m-%d') and date_format({$_GET[sregdt][1]},'%Y-%m-%d')";

$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>

<script src="../tax.ajax.js"></script>

<div align="center"><a href="http://www.godobill.com/" target="_blank"><img src="../img/etaxServiceEnd.jpg"></a></div>

<form name=frmList>
<div class="title title_top">전자발행요청리스트<span>구매고객이 신청한 세금계산서를 조회하고 승인처리합니다</span></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=40%>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="company" <?=$selected[skey]['company']?>> 상호 </option>
	<option value="name" <?=$selected[skey]['name']?>> 대표자 </option>
	<option value="m_name" <?=$selected[skey]['m_name']?>> 신청자 </option>
	<option value="m_id" <?=$selected[skey]['m_id']?>> 아이디 </option>
	<option value="ordno" <?=$selected[skey]['ordno']?>> 주문번호 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>사업자번호</td>
	<td><input type=text name=sbusino value="<?=$_GET[sbusino]?>" size=15 maxlength=10 class=line> <span class=small><font color=#5B5B5B>숫자만 기입</font><span></td>
</tr>
<tr>
	<td>발행일</td>
	<td colspan="3">
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><?=number_format($total)?></b>개, 검색 <b><?=number_format($pg->recode[total])?></b>개, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="issuedate desc" <?=$selected[sort]['issuedate desc']?>>- 발행일 정렬↑</option>
	<option value="issuedate asc" <?=$selected[sort]['issuedate asc']?>>- 발행일 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- 등록일 정렬↑</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- 등록일 정렬↓</option>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- 주문번호 정렬↑</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- 주문번호 정렬↓</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>개 출력
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form method="post" action="" name="fmList">
<table width=100% cellspacing=0 cellpadding=0 border=1 bordercolor="#D9D9D9" style="border-collapse: collapse; word-break:break-all;">
<col width=35><col width=35><col width=120><col><col width=12%><col width=15%><col width=74><col width=100>
<tr class=rndbg>
	<th rowspan=2>선택</th>
	<th rowspan=2>번호</th>
	<th>주문번호</th>
	<th colspan=3>사업자정보</th>
	<th>발행일</th>
	<th rowspan=2>발행상태</th>
</tr>
<tr class=rndbg>
	<th>신청자명</th>
	<th>상품명</th>
	<th>결제금액</th>
	<th>발행금액</th>
	<th>신청일</th>
</tr>

<?
while ($data=$db->fetch($res)){

	### 주문데이타
	$query = "select step, step2, prn_settleprice, nameOrder,cashreceipt from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);
	$step = $r_stepi[$o_data[step]][$o_data[step2]];
	$cashMsg = ($o_data[cashreceipt])?"현금영수증발행":"";

	### 신청자
	if ( !$data[m_no] ) $namestr = $o_data[nameOrder];
	else $namestr = "{$data[m_name]}/<span id=\"navig\" name=\"navig\" m_id=\"{$data[m_id]}\" m_no=\"{$data[m_no]}\"><font color=0074BA><b>{$data[m_id]}</b></font></span>";

	### 발행상태
	$state = $tax_step[ $data[step] ];

	?>
<tr height=25 align="center">
	<td rowspan=2 class=noline><input type=checkbox name=chk[] value="<?=$data[sno]?>" subject="주문번호 <?=$data[ordno]?>" onclick="TAM.iciSelect(this)"></td>
	<td rowspan=2><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a> <font class=small color=EA0095><nobr><b><?=$step?></b></font></td>
	<td colspan=3 align=left style="padding:5 0 5 7"><font class=small color=444444>
	사업자번호 : <input type=text name="busino[<?=$data[sno]?>]" value="<?=$data[busino]?>" style="width:85" maxlength=10 class=line>&nbsp;&nbsp;
	회사명 : <input type=text name="company[<?=$data[sno]?>]" value="<?=$data[company]?>" style="width:25%;" class=line><br>
	대표자성명 : <input type=text name="name[<?=$data[sno]?>]" value="<?=$data[name]?>" style="width:85;" class=line>&nbsp;&nbsp;
	업태<font color=white>명</font> : <input type=text name="service[<?=$data[sno]?>]" value="<?=$data[service]?>" style="width:17%;" class=line>&nbsp;&nbsp;
	종목 : <input type=text name="item[<?=$data[sno]?>]" value="<?=$data[item]?>" style="width:17%;" class=line><br>
	사업장주소 : <input type=text name="address[<?=$data[sno]?>]" value="<?=$data[address]?>" style="width:422;" class=line>
	</td>
	<td><input type=text name="issuedate[<?=$data[sno]?>]" value="<?=$data[issuedate]?>" size=10 maxlength=10 style="text-align:center;" class=cline></td>
	<td rowspan=2><font color=EA0095><b><?=$state?></b></font><div style='padding-top:5'><font class=ver81 color=0074BA><b><?=$cashMsg?></b></font></div></td>
</tr>
<tr height=25 align="center">
	<td><font class=small color=444444><?=$namestr?></td>
	<td><input type=text name="goodsnm[<?=$data[sno]?>]" value="<?=$data[goodsnm]?>" style="width:96%;" class=line></td>
	<td><?=number_format($o_data[prn_settleprice])?></td>
	<td><font class=small color=444444>
	발행액 <input type=text name="price[<?=$data[sno]?>]" value="<?=$data[price]?>" size=8 maxlength=11 style="text-align:right;" onKeyDown="onlynumber();" onkeyup="TAM.putTax( '<?=$data[sno]?>' );" class=rline><br>
	공급액 <input type=text name="supply[<?=$data[sno]?>]" value="<?=$data[supply]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline><br>
	부가세 <input type=text name="surtax[<?=$data[sno]?>]" value="<?=$data[surtax]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline>
	</td>
	<td><font class=ver8 color=444444><?=$data[regdt]?></td>
</tr>
<? } ?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:TAM.act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:TAM.act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="일괄수정" border=0 align=absmiddle></A>
</div>

<div style="clear:both; text-align:center;">
<a href="javascript:WTS.begin();"><img src="../img/btn_webtax_app.gif" alt="전자발행요청" border=0 align=absmiddle></a>
</div>

<div style="clear:both; padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">세금계산서 신청은 쇼핑몰의 주문/배송조회를 통해 받습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">세금계산서 조건
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>비과세 상품은 제외됩니다.</li>
<li>배송비는 발행금액에 포함되지 않습니다.</li>
<li>발행금액은 할인액(회원할인+쿠폰할인+적립금사용+에누리) 만큼 차감됩니다.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자세금계산서 발행요청
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>전자세금계산서 발행을 요청합니다.</li>
<li>포인트가 있어야만 발행요청이 가능하며 내역당 1point 차감됩니다.</li>
<li>공급받는자의 세금계산서는 주문시에 입력되었던 이메일과 휴대폰으로 각각 발송되며 안내되어집니다.</li>
<li>발행일자 기준으로 <b>30일 전까지</b> 문서에 대해서만 발행되므로 <b>발행요청전에 발행일자를 확인</b>하시기 바랍니다.</font></li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>