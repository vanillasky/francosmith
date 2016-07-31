<?

$location = "전자세금계산서 관리 > 전자발행내역리스트";
include "../_header.php";

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no where step=3"); # 총 레코드수

### 변수할당
$tax_step = array( '발행신청', '발행승인', '발행완료', '전자발행' );
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "agreedt desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";

### 목록
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.m_no, b.m_id, b.name as m_name, b.dormant_regDate";
$db_table = "".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
$where[] = "step=3";

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
if ($_GET[sagreedt][0] && $_GET[sagreedt][1]) $where[] = "date_format(agreedt,'%Y-%m-%d') between date_format({$_GET[sagreedt][0]},'%Y-%m-%d') and date_format({$_GET[sagreedt][1]},'%Y-%m-%d')";

$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>
<script src="../tax.ajax.js"></script>

<form name=frmList>
<div class="title title_top">전자발행내역리스트<span>발행신청리스트에서 전자발행요청한 세금계산서를 관리합니다.</span></div>
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
	<td>발행요청일</td>
	<td colspan="3">
	<input type=text name=sagreedt[] value="<?=$_GET[sagreedt][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=sagreedt[] value="<?=$_GET[sagreedt][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('sagreedt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sagreedt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sagreedt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sagreedt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sagreedt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sagreedt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
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
	<option value="agreedt desc" <?=$selected[sort]['agreedt desc']?>>- 요청일 정렬↑</option>
	<option value="agreedt asc" <?=$selected[sort]['agreedt asc']?>>- 요청일 정렬↓</option>
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
<col width=35><col width=35><col width=120><col><col width=10%><col width=15%><col width=75><col width=75><col width=75><col width=86>
<tr class=rndbg>
	<th rowspan=2>선택</th>
	<th rowspan=2>번호</th>
	<th>주문번호</th>
	<th colspan=3>사업자정보</th>
	<th>발행일</th>
	<th>문서번호</th>
	<th>식별번호</th>
	<th rowspan=2>발행상태<br>인쇄하기</th>
</tr>
<tr class=rndbg>
	<th>신청자명</th>
	<th>상품명</th>
	<th>결제금액</th>
	<th>발행금액</th>
	<th>신청일</th>
	<th>요청일</th>
	<th>승인/반려일</th>
</tr>

<?
$k = 0;
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

	?>
<tr height=25 align="center" id="taxtd<?=++$k?>">
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
	<td><font class=small color=444444><?=$data[doc_number]?></td>
	<td><font class=small color=444444>데이타로딩중</font></td>
	<td rowspan=2 style="line-height:15pt;"><font class=small color=444444>데이타로딩중</font></td>
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
	<td><font class=small color=444444>데이타로딩중</font><script>getTaxbill('<?=$data[doc_number]?>','taxtd<?=$k?>');</script></td>
</tr>
<? } ?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="전체선택"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="선택반전"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="선택해제"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="선택삭제" border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:TIM.act_delete();"<?}else{?>onclick="javascript:alert( '데이타가 존재하지 않습니다.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:TIM.dnXls();"><img src="../img/btn_order_data.gif" alt="엑셀저장" border=0 align=absmiddle></A>
</div>

<div style="clear:both; padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<dl style="margin:0;">
<dt style="padding-bottom:3"><img src="../img/icon_list.gif" align="absmiddle">발행상태 설명</font></dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li style="padding-bottom:3">① 발행준비 : 세금계산서 발행을 준비중입니다.</li>
	<li style="padding-bottom:3">② 발행 : 공인인증서를 기반으로 발행자의 전자서명이 된 디지털 파일 형태로 발행(전달)되었습니다.</li>
	<li style="padding-bottom:3">③ 수신 : 수신자가 세금계산서 내용을 확인하였습니다.</li>
	<li style="padding-bottom:3">④ 승인 : 공급받는자가 세금계산서 발행을 승인하였습니다.</li>
	<li style="padding-bottom:3">⑤ 반려 : 공급받는자가 세금계산서 발행을 반려하였습니다.</li>
	<li style="padding-bottom:3">⑥ 취소 : 발행, 수신 또는 승인 상태에서 발행을 취소하였습니다.</li>
	<li style="padding-bottom:6">⑦ 에러 : 발행준비 -> 발행으로 전환중에 에러가 발행하였습니다.</li>
	</ol>
</dd>
</dl>
</td></tr>
<tr><td style="padding-bottom:3"><img src="../img/icon_list.gif" align="absmiddle">반려/취소/에러일 경우 구매고객은 [쇼핑몰화면 > 마이페이지 > 주문/배송조회 > 주문내역상세보기] 에서 세금계산서 재신청이 가능합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">공급자용 세금계산서를 확인하시려면 왼쪽측면 메뉴에 있는 전자세금계산서 매니저'에 접속하셔서 확인하셔야 합니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<form name=frmDnXls method=post>
<input type=hidden name=mode value="etax">
<input type=hidden name=query value="<?=$pg->query?>">
</form>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>