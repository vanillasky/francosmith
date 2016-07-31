<?

$location = "일반세금계산서 관리 > 일반발행내역리스트";
include "../_header.php";

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no where step between 1 and 2"); # 총 레코드수

### 변수할당
$tax_step = array( '발행신청', '발행승인', '발행완료', '전자발행' );
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "issuedate desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$checked[sstep][$_GET[sstep]] = "checked";

### 목록
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.m_no, b.m_id, b.name as m_name, b.dormant_regDate";
$db_table = "".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
if ($_GET[sstep]) $where[] = "step = '$_GET[sstep]'";
else {
	$where[] = "step=1";
	$checked[sstep][1] = "checked";
}

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

<form name=frmList>
<div class="title title_top">일반발행내역리스트<span>발행신청리스트에서 승인된 세금계산서를 프린트하고 관리합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=9')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=40%>
<tr>
	<td>키워드검색전송</td>
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
<tr>
	<td>발행상태</td>
	<td colspan="3" class=noline>
	<input type=radio name=sstep value="1" <?=$checked[sstep]['1']?>> 발행승인(인쇄 대기중)
	<input type=radio name=sstep value="2" <?=$checked[sstep]['2']?>> 발행완료(공급받는자용 인쇄후)
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
	<option value="printdt desc" <?=$selected[sort]['printdt desc']?>>- 인쇄일 정렬↑</option>
	<option value="printdt asc" <?=$selected[sort]['printdt asc']?>>- 인쇄일 정렬↓</option>
	<option value="agreedt desc" <?=$selected[sort]['agreedt desc']?>>- 승인일 정렬↑</option>
	<option value="agreedt asc" <?=$selected[sort]['agreedt asc']?>>- 승인일 정렬↓</option>
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- 신청일 정렬↑</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- 신청일 정렬↓</option>
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
<col width=35><col width=35><col width=120><col><col width=10%><col width=15%><col width=75><col width=75><col width=86>
<tr class=rndbg>
	<th rowspan=2>선택</th>
	<th rowspan=2>번호</th>
	<th>주문번호</th>
	<th colspan=3>사업자정보</th>
	<th>발행일</th>
	<th>인쇄일</th>
	<th rowspan=2>발행상태<br>인쇄하기</th>
</tr>
<tr class=rndbg>
	<th>신청자명</th>
	<th>상품명</th>
	<th>결제금액</th>
	<th>발행금액</th>
	<th>신청일</th>
	<th>승인일</th>
</tr>

<?
while ($data=$db->fetch($res)){

	### 주문데이타
	$query = "select step, step2, prn_settleprice, nameOrder from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);
	$step = $r_stepi[$o_data[step]][$o_data[step2]];

	### 신청자
	if ( !$data[m_no] ) {
		$namestr = $o_data[nameOrder];
	}
	else {
		if($data[m_id] && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
			$namestr = "휴면회원";
		}
		else {
			$namestr = "{$data[m_name]}/<span id=\"navig\" name=\"navig\" m_id=\"{$data[m_id]}\" m_no=\"{$data[m_no]}\"><font color=0074BA><b>{$data[m_id]}</b></font></span>";
		}
	}

	### 발행상태
	$state = $tax_step[ $data[step] ];
	$state .= " <nobr><a href=\"javascript:;\" onclick=\"var w=popup_return( '../order/_paper.php?type=tax&taxarea=blue&ordno={$data[ordno]}', 'orderPrint', 750, 600 ); w.focus();\"><img src='../img/btn_tax_buyer.gif' border=0></a>";
	$state .= " <nobr><a href=\"javascript:;\" onclick=\"var w=popup_return( '../order/_paper.php?type=tax&taxarea=red&ordno={$data[ordno]}', 'orderPrint', 750, 600 ); w.focus();\"><img src='../img/btn_tax_seller.gif' border=0></a>";

	### 인쇄일
	if ( str_replace(array("0","-",":"," "), "", $data[printdt]) == '' ) $data[printdt] = "인쇄 대기중";

	?>
<tr height=25 align="center">
	<td rowspan=2 class=noline><input type=checkbox name=chk[] value="<?=$data[sno]?>" ordno="<?=$data[ordno]?>" onclick="TIM.iciSelect(this)"></td>
	<td rowspan=2><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a> <font class=small color=EA0095><nobr><b><?=$step?></b></font></td>
	<td colspan=3 align=left style="padding:5 0 5 7"><font class=small color=444444>
	사업자번호 : <?=$data[busino]?>&nbsp;&nbsp;
	회사명 : <?=$data[company]?><br>
	대표자명 : <?=$data[name]?>&nbsp;&nbsp;
	업태 : <?=$data[service]?>&nbsp;&nbsp;
	종목 : <?=$data[item]?><br>
	사업장주소 : <?=$data[address]?>
	</td>
	<td><font class=ver8 color=444444><?=$data[issuedate]?></td>
	<td><font class=ver8 color=444444><?=$data[printdt]?></td>
	<td rowspan=2 style="line-height:15pt;"><font color=EA0095><b><?=$state?></b></font></td>
</tr>
<tr height=25 align="center">
	<td><font class=small color=444444><?=$namestr?></td>
	<td align=left style="padding:5 0 5 7"><font class=small color=444444><?=$data[goodsnm]?></td>
	<td><?=number_format($o_data[prn_settleprice])?></td>
	<td style="padding:5 0 5 0">
	<table width=92% border=0 cellspacing=0 cellpadding=0 style="line-height:15pt;">
	<col width=44%>
	<tr><td><font class=small color=444444>발행액 :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[price])?></td></tr>
	<tr><td><font class=small color=444444>공급액 :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[supply])?></td></tr>
	<tr><td><font class=small color=444444>부가세 :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[surtax])?></td></tr>
	</table>
	</td>
	<td><font class=ver8 color=444444><?=$data[regdt]?></td>
	<td><font class=ver8 color=444444><?=$data[agreedt]?></td>
</tr>
<? } ?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:TIM.act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:TIM.dnXls();"><img src="../img/btn_order_data.gif" alt="엑셀저장" border=0 align=absmiddle></A>
</div>

<div style="clear:both; padding-top:35;"></div>

<!-- 주문내역 프린트 : Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td background="../img/etc_print3.gif"><img src="../img/etc_print1.gif" border="0"></td>
	<td width="19"><img src="../img/etc_print2.gif" border="0"></td>
</tr>
</table>

<div style="border-left:6px #e6e6e6 solid;border-right:6px #e6e6e6 solid;border-bottom:6px #e6e6e6 solid;padding:6 12 10 18;margin-bottom:20pt;">
<form method="get" name="frmPrint">
<input type="hidden" name="type" value="tax">
<input type="hidden" name="ordnos">
<div style="float:left;">
<select NAME="taxarea" style="margin-right:10px;">
<option value="">세금계산서</option>
<option value="blue">공급받는자보관용</option>
<option value="red">공급자보관용</option>
</select>
<strong class=noline><label for="r1"><input class="no_line" type="radio" name="list_type" value="list" id="r1" onclick="openLayer('psrch','none')" checked>목록선택</label>&nbsp;&nbsp;&nbsp;<label for="r2"><input class="no_line" type="radio" name="list_type" value="tax_term" id="r2" onclick="openLayer('psrch','block')">기간선택</label></strong>
</div>

<div style="float:left; margin-left:5px; display:none;" id="psrch">
<input type=text name=regdt[] onclick="calendar(event)" size=12 class=cline> -
<input type=text name=regdt[] onclick="calendar(event)" size=12 class=cline>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:order_print('frmPrint', 'fmList');"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
</form>
</div>
<!-- 주문내역 프린트 : End -->

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">공급받는자용 세금계산서를 인쇄하면 발행완료로 전환되며, 인쇄일이 표기 됩니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<form name=frmDnXls method=post>
<input type=hidden name=mode value="tax">
<input type=hidden name=query value="<?=$pg->query?>">
</form>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>