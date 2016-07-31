<?

$location = "자동입금확인 서비스 > 입금조회 / 실시간입금확인";
include "../_header.php";

### 입금일 default 7일
if ( $_GET[bkdate][0]=='' && $_GET[bkdate][1] == '' )
{
	$_GET[bkdate][0] = date("Ymd",strtotime("-7 day"));
	$_GET[bkdate][1] = date("Ymd");
}

### 변수할당
if (!$_GET[page_num]) $_GET[page_num] = 10; # 페이지 레코드수
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "bkdate desc"; # 정렬 쿼리
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$selected[gdstatus][$_GET[gdstatus]] = "selected";
$selected[bkname][$_GET[bkname]] = "selected";

$r_bank = array('기업은행','국민은행','외환은행','주택은행','농협중앙회','농협개인','우리은행','조흥은행','제일은행','서울은행','신한은행','한미은행','대구은행','부산은행','광주은행','제주은행','전북은행','경남은행','새마을금고','우체국','하나은행');

?>
<script src="../bankmatch.ajax.js"></script>

<script>

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFF0" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

</script>
<form name=frmList onsubmit="return ( accountList() ? false : false );">
<div class="title title_top">입금조회 / 실시간입금확인<span>통장에 입금된 내역을 실시간으로 조회하며, 입금된 내역을 실시간으로 입금확인처리합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=17')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=35%>
<tr>
	<td>키워드검색</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> 통합검색 </option>
	<option value="bkjukyo" <?=$selected[skey]['bkjukyo']?>> 입금자명 </option>
	<option value="bkinput" <?=$selected[skey]['bkinput']?>> 입금예정금액 </option>
	<option value="bkmemo4" <?=$selected[skey]['bkmemo4']?>> 주문번호 </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class="line">
	</td>
	<td>현재상태<font class=small color=444444>/</font>은행명</td>
	<td>
	<select name="gdstatus">
	<option value=""> 전체 </option>
	<option value="N" <?=$selected[gdstatus]['N']?>>확인전</option>
	<option value="T" <?=$selected[gdstatus]['T']?>>매칭성공(by시스템)</option>
	<option value="B" <?=$selected[gdstatus]['B']?>>매칭성공(by관리자)</option>
	<option value="F" <?=$selected[gdstatus]['F']?>>매칭실패(불일치)</option>
	<option value="S" <?=$selected[gdstatus]['S']?>>매칭실패(동명이인)</option>
	<option value="A" <?=$selected[gdstatus]['A']?>>관리자입금확인완료</option>
	<option value="U" <?=$selected[gdstatus]['U']?>>관리자미확인</option>
	</select>

	<select name="bkname">
	<option value="">↓은행검색</option>
	<? foreach ($r_bank as $v){ ?>
	<option value="<?=$v?>" <?=$selected[bkname][$v]?>><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>입금일</td>
	<td colspan="3">
	<input type=text name=bkdate[] value="<?=$_GET[bkdate][0]?>" onclick="calendar(event)" class="cline"> ~
	<input type=text name=bkdate[] value="<?=$_GET[bkdate][1]?>" onclick="calendar(event)" class="cline">
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('bkdate[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>최종 매칭일</td>
	<td colspan="3">
	<input type=text name=gddate[] value="<?=$_GET[gddate][0]?>" onclick="calendar(event)" class="cline"> ~
	<input type=text name=gddate[] value="<?=$_GET[gddate][1]?>" onclick="calendar(event)" class="cline">
	<a href="javascript:setDate('gddate[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('gddate[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td height=7 colspan=3></td></tr>
<tr>
<td width=50% align=right><A HREF="javascript:bankMatching();"><img src="../img/btn_man_banking.gif" alt="실시간입금확인 실행하기" border=0></a></td>
<td>&nbsp;&nbsp;&nbsp;</td>
<td width=50%><input type=image src="../img/btn_bank_search.gif" alt="통장입금내역 실시간조회" class=null></td></tr></table>

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	총 <b><span id="page_rtotal">0</span></b>개, 검색 <b><span id="page_recode">0</span></b>개, <b><span id="page_now">0</span></b> of <span id="page_total">0</span> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="accountList();">
	<option value="bkdate desc" <?=$selected[sort]['bkdate desc']?>>- 입금일 정렬↑</option>
	<option value="bkdate asc" <?=$selected[sort]['bkdate asc']?>>- 입금일 정렬↓</option>
	<option value="gddatetime desc" <?=$selected[sort]['gddatetime desc']?>>- 최종매칭일 정렬↑</option>
	<option value="gddatetime asc" <?=$selected[sort]['gddatetime asc']?>>- 최종매칭일 정렬↓</option>
	</select>&nbsp;
	<select name=page_num onchange="accountList();">
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

<div style="position:relative; height:150px;">
<div id="listcover" style="position:absolute; width:100%; height:100%; display:none"><!--커버--></div>
<form method="post" action="" name="fmList">
<table width=100% cellpadding=0 cellspacing=0 border=0 id="listing">
<col width="60"><col width="10%"><col width="13%"><col width="10%"><col width="12%"><col><col width="10%"><col width="10%"><col width="13%">
<tr><td class=rnd colspan=10></td></tr>
<tr class=rndbg>
	<th>번호</th>
	<th>입금완료일</th>
	<th>계좌번호</th>
	<th>은행명</th>
	<th>입금금액</th>
	<th>입금자명</th>
	<th>현재상태</th>
	<th>최종 매칭일</th>
	<th>주문번호</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
</table>

<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr><td height=5 colspan=2></td></tr>
<tr>
	<td align=center><font class=ver8><span id="page_navi"><!-- 페이징 출력--></span></font></td>
</tr>
</table>

<div class=button><a href="javascript:batchUpdate.begin();"><img src="../img/btn_editall.gif"></a></div>
<INPUT TYPE="hidden" style="width:300" NAME="nolist">
</form>
</div>
<script>document.getElementById('listcover').parentNode.style.height = ''; // 예외처리 : 모질라</script>

<div style="padding-top:15px;"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">'현재상태'  항목 설명 (매칭상태를 보여주는 항목입니다)</td></tr>
<tr><td style="padding-left: 10">
<div>- 매칭실패 (불일치) : 입금정보가 맞지않아 매칭실패된 주문건입니다. 관리자는 해당 주문고객을 찾아 처리해야 합니다.</div>
<div>- 매칭실패 (동명이인) : 입금정보가 동일한 주문이 2건 이상이 있는 주문건입니다. 관리자는 해당 주문고객을 찾아 처리해야 합니다.</div>
<div>- 관리자입금확인 : 매칭실패건이 나온 경우 관리자는 해당 주문고객을 찾아 직접 입금확인으로 처리한 후 '관리자입금확인' 상태로 변경해놓으세요.</div>
<div>- 관리자미확인 : 매칭실패건이 나온 경우 관리자가 입금자를 찾지 못하고 매칭범위에서는 제외시키려면 '관리자미확인' 상태로 변경해놓으세요.</div>
<div>- 매칭성공 (by시스템) : 시스템(자동처리/실시간처리)에 의해 입금확인처리가 완료된 주문건입니다.</div>
<div>- 매칭성공 (by관리자) : 매칭성공된 주문건중 관리자가 주문리스트에서 이미 입금확인단계로 처리한 주문건입니다.</div>
</td></tr>


<tr><td height=3></td></tr>
</table>
</div>
<script>cssRound('MSG02')</script>

<div style="clear:both; padding-top:1px;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>* 입금조회 / 실시간입금확인 메뉴는 '<b>주문관리 > 자동입금확인 서비스 신청</b>' 메뉴를 통해 서비스 신청 후 이용하실 수 있습니다. 서비스 신청 후 이용할 은행계좌를 등록해 주세요.</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>자동입금확인 서비스</b> : 서비스개시일로부터 1시간 간격으로 입금내역을 조회하여 자동으로 입금확인 처리합니다. (서비스개시일 이전 주문은 매칭안됨)
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>내 쇼핑몰 은행계좌들의 입금내역과 주문내역을 자동으로 비교하여 통장에 입금된 내역을 자동으로 입금확인 처리하는 서비스입니다.</li>
<li>비교(Matching) 범위 : 기본적으로 7일간의 입금내역과 37일간의 주문내역을 조회하여 매칭 작업합니다.</li>
<li>비교(Matching) 기준 : 은행, 계좌번호, 금액, 입금자명으로 매칭 작업합니다.</li>
<li>동일주문의 경우 : 은행, 계좌번호, 금액, 입금자명이 동일한 주문의 경우 '동명이인' 으로 처리되며, 반드시 수작업으로 입금확인 처리해야 합니다.</li>
<li>비교(Matching) 주기 : 1시간 간격으로 자동 처리합니다.</li>
</ol>
</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>실시간 입금확인 실행</b> : 자동 처리되는 1시간 간격보다 빠르게 입금확인처리가 필요한 경우 운영자가 직접 수동으로 입금확인 처리를 실행할 수 있습니다
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>입금내역과 주문내역의 비교(Matching)를 수동으로 실행 처리합니다.</li>
<li>비교(Matching) 범위 : 입금일 검색항목 기간의 입금내역과 +30일간의 주문내역을 조회하여 매칭 작업합니다. (기본범위는 7일간의 입금내역, 입금일을 조정하여 수동처리가 가능)</li>
<li>비교(Matching) 기준 : 자동입금확인 서비스와 동일</li>
</ol>
</td></tr>
<tr><td height=3></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>통장입금내역 실시간 조회</b> : 입금일을 기준으로 입금확인된 내역을 조회합니다. 단순히 조회만 하는 기능입니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>