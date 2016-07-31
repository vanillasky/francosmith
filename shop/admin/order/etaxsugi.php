<?

$location = "전자세금계산서 관리 > 전자세금계산서 수기발행";
$scriptLoad='<script src="../tax.sugi.js"></script>';
include "../_header.php";

### 요청일 default 7일
if ( $_GET[sbm_tm][0]=='' && $_GET[sbm_tm][1] == '' )
{
	$_GET[sbm_tm][0] = date("Ymd",strtotime("-7 day"));
	$_GET[sbm_tm][1] = date("Ymd");
}

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "SBM_TM desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[tax_type][$_GET[tax_type]] = "selected";
$selected[bill_type][$_GET[bill_type]] = "selected";
$selected[status][$_GET[status]] = "selected";

?>

<form name=frmList onsubmit="return ( TLM.list() ? false : false );">
<div class="title title_top">전자세금계산서 수기발행<span>전자세금계산서를 수기로 작성하여 발행요청을 할 수 있습니다.</span></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="DOC_NUMBER" <?=$selected[skey]['DOC_NUMBER']?>> 문서번호</option>
	<option value="BUY_REGNUM" <?=$selected[skey]['BUY_REGNUM']?>> 수요업체사업자번호 </option>
	<option value="BUY_COMPANY" <?=$selected[skey]['BUY_COMPANY']?>> 수요업체회사명 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>과세종류</td>
	<td>
	<select name="tax_type">
	<option value=""> 전체 </option>
	<option value="VAT" <?=$selected[tax_type]['VAT']?>>과세(세금계산서)</option>
	<option value="FRE" <?=$selected[tax_type]['FRE']?>>면세(계산서)</option>
	<option value="RCP" <?=$selected[tax_type]['RCP']?>>영수증</option>
	</select>
	</td>
</tr>
<tr>
	<td>청구서종류</td>
	<td>
	<select name="bill_type">
	<option value=""> 전체 </option>
	<option value="T01" <?=$selected[bill_type]['T01']?>>영수함</option>
	<option value="T02" <?=$selected[bill_type]['T02']?>>청구함</option>
	</select>
	</td>
	<td>발행상태</td>
	<td>
	<select name="status">
	<option value=""> 전체 </option>
	<option value="RDY" <?=$selected[status]['RDY']?>>발행준비</option>
	<option value="SND" <?=$selected[status]['SND']?>>발행</option>
	<option value="RCV" <?=$selected[status]['RCV']?>>수신</option>
	<option value="ACK" <?=$selected[status]['ACK']?>>승인</option>
	<option value="CAN" <?=$selected[status]['CAN']?>>반려</option>
	<option value="CCR" <?=$selected[status]['CCR']?>>취소</option>
	<option value="ERR" <?=$selected[status]['ERR']?>>에러</option>
	<option value="DEL" <?=$selected[status]['DEL']?>>삭제</option>
	</select>
	</td>
</tr>
<tr>
	<td>요청일</td>
	<td colspan="3">
	<input type=text name=sbm_tm[] value="<?=$_GET[sbm_tm][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=sbm_tm[] value="<?=$_GET[sbm_tm][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sbm_tm[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=15 colspan=3></td></tr>
<tr>
<td width=50% align=right><A HREF="javascript:popupLayer('../order/etaxsugi.register.php',700,650);"><img src="../img/btn_tax_hand_apply.gif" alt="전자세금계산서 수기로 작성하기" border=0></a></td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td width=50%><input type=image src="../img/btn_tax_hand_search.gif" alt="수기로 작성된 전자세금계산서 조회" class=null></td></tr></table>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><span id="page_rtotal">0</span></b>개, 검색 <b><span id="page_recode">0</span></b>개, <b><span id="page_now">0</span></b> of <span id="page_total">0</span> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="TLM.list();">
	<option value="GEN_TM desc" <?=$selected[sort]['issuedate desc']?>>- 발행일 정렬↑</option>
	<option value="GEN_TM asc" <?=$selected[sort]['issuedate asc']?>>- 발행일 정렬↓</option>
    <optgroup label="------------"></optgroup>
	<option value="SBM_TM desc" <?=$selected[sort]['SBM_TM desc']?>>- 요청일 정렬↑</option>
	<option value="SBM_TM asc" <?=$selected[sort]['SBM_TM asc']?>>- 요청일 정렬↓</option>
	</select>&nbsp;
	</td>
</tr>
</table>
</form>

<div style="position:relative; height:0;">
<div id="listcover" style="position:absolute; width:100%; height:100%; display:none"><!--커버--></div>
<form method="post" action="" name="fmList">
<table width=100% cellspacing=0 cellpadding=0 border=1 bordercolor="#D9D9D9" style="border-collapse: collapse; word-break:break-all;" id="listing">
<col width=35><col><col width=15%><col width=10%><col width=75><col width=75><col width=75><col width=86>
<tr class=rndbg>
	<th rowspan=2>번호</th>
	<th colspan=3>사업자정보</th>
	<th>발행일</th>
	<th>문서번호</th>
	<th>식별번호</th>
	<th rowspan=2>발행상태<br>인쇄하기</th>
</tr>
<tr class=rndbg>
	<th>상품명</th>
	<th>발행금액</th>
	<th>과세종류</th>
	<th>청구서종류</th>
	<th>요청일</th>
	<th>승인/반려일</th>
</tr>
</table>

<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr><td height=5 colspan=2></td></tr>
<tr>
	<td align=center><font class=ver8><span id="page_navi"><!-- 페이징 출력--></span></font></td>
</tr>
</table>

</form>
</div>
<script>if ( !document.all ) document.getElementById('listcover').parentNode.style.height = ''; // 예외처리 : 모질라</script>

<div style="padding-top:15px;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<dl style="margin:0;">
<dt style="padding-bottom:3"><img src="../img/icon_list.gif" align="absmiddle">발행상태 설명</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li style="padding-bottom:3">① 발행준비 : 세금계산서 발행을 준비중입니다.</li>
	<li style="padding-bottom:3">② 발행 : 공인인증서를 기반으로 발행자의 전자서명이 된 디지털 파일 형태로 발행(전달)되었습니다.</li>
	<li style="padding-bottom:3">③ 수신 : 수신자가 세금계산서 내용을 확인하였습니다.</li>
	<li style="padding-bottom:3">④ 승인 : 공급받는자가 세금계산서 발행을 승인하였습니다.</li>
	<li style="padding-bottom:3">⑤ 반려 : 공급받는자가 세금계산서 발행을 반려하였습니다.</li>
	<li style="padding-bottom:3">⑥ 취소 : 발행, 수신 또는 승인 상태에서 발행을 취소하였습니다.</li>
	<li style="padding-bottom:6">⑦ 에러 : 발행준비 ⇒ 발행으로 전환중에 에러가 발행하였습니다.</li>
	</ol>
</dd>
</dl>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">공급자용 세금계산서를 확인하시려면 왼쪽측면 메뉴에 있는 전자세금계산서 매니저'에 접속하셔서 확인하셔야 합니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">전자세금계산서 수기발행요청
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>전자세금계산서를 수기로 작성합니다.</li>
<li>포인트가 있어야만 발행요청이 가능하며 내역당 1point 차감됩니다.</li>
<li>공급받는자의 세금계산서는 수기작성시에 입력한 이메일과 휴대폰으로 각각 발송되며 안내되어집니다.</li>
<li>[주의] 수기발행을 위해 수동으로 입력되는 작성일자가 수기작성일을 기준으로 <b>30일 이내어야만</b> 발행되어집니다.</li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>