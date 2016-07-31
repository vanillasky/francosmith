<?
$location = '구매안전 서비스 > 쇼핑몰 보증보험 설정';
include '../_header.php';
include '../../lib/lib.func.egg.php';

$egg = getEggConf();
$checked['displayEgg'][$cfg['displayEgg']+0] = 'checked';
if ($egg['use'] != 'Y') $disabled['displayEgg'] = 'disabled';
if ($egg['use'] != 'Y' || $egg['scope'] != 'P') $disabled['min'] = 'disabled';
?>

<div class="title title_top">쇼핑몰 보증보험 설정 <span>쇼핑몰 보증보험 신청 및 진행상황을 확인합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<script id=script name=script src="http://www.godo.co.kr/userinterface/_usafe/progress.js.php?godosno=<?=$godo[sno]?>&hashdata=<?=md5($godo[sno])?>&u_id=<?=md5($egg[usafeid])?>"></script>

<div id="request" style="display:none;"><img src="../img/btn_usafe.gif" onclick="popup('http://www.godo.co.kr/service/surety_insurance_regist.php?mode=remoteGodoPage&godosno=<?=$godo[sno]?>&hashdata=<?=md5($godo[sno])?>',770,800);" border="0" style="cursor:pointer;"></div>
<script language="javascript"><!--
if (typeof(usafeStep) != "undefined"){
	if (usafeStep == '' || (usafeStep != '0' && usafeStep != '1' && usafeStep != '3' && usafeStep != '4')){
		document.getElementById('request').style.display = 'block';
		document.getElementById('request').style.margin = '20px 20px 0 320px';
	}
}
--></script>


<!-- 쇼핑몰 보증보험 설정 : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">쇼핑몰 보증보험 설정 &nbsp; <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12',890,800)"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<form method=post action="egg.indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="displayEgg">
<input type="hidden" name="min" value="<?=$egg['min']?>" <?=$disabled['min']?>>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=30>
	<td>구매 안전 표시 설정</td>
	<td class=noline>
	<input type=radio name=cfg[displayEgg] value=0 <?=$checked['displayEgg'][0]?> <?=$disabled['displayEgg']?>> 메인하단과 결제수단 선택페이지에만 표시
	<input type=radio name=cfg[displayEgg] value=1 <?=$checked['displayEgg'][1]?> <?=$disabled['displayEgg']?>> 전체페이지에 표시
	<input type=radio name=cfg[displayEgg] value=2 <?=$checked['displayEgg'][2]?> <?=$disabled['displayEgg']?>> 표시하지 않음
	</td>
</tr>
</table>
<div class=button>
<input type=image src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</form>
<!-- 쇼핑몰 보증보험 설정 : End -->


<!-- 구매안전 서비스 표기 적용 방법 안내 : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">구매안전 서비스 표기 적용 방법 안내 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table border=1 bordercolor=#e1e1e1 style="border-collapse:collapse" width=100%>
<tr><td>
<table cellpadding=15 cellspacing=0 border=0 bgcolor=white width=100%>
<tr><td>
<div style="padding:0 0 5 0">* 구매안전서비스 표기 적용방법 (소비자피해보상보험 사용시 위에서 구매안전표시를 체크하고, 아래 표기방법에 따라 반영하세요)</font></div>
<table width=100% height=100 class=tb style='border:1px solid #cccccc;' bgcolor=white>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>① [메인페이지 하단] 표기방법</td>
<td align=center rowspan=2 style='border:1px solid #cccccc;padding:0 10 0 10'><a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=10')"><img src="../img/icon_sample.gif" align=absmiddle></a></td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'><font class=extext><a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext><b>[디자인관리 > 전체레이아웃 디자인 > 하단디자인 > html소스 직접수정]</b></font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displayEggBanner()}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=outline/footer/standard.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></td>
</tr>
<tr>
<td width=30% style='border:1px solid #cccccc;padding-left:20'>② [결제수단 선택페이지] 표기방법</td>
<td width=70% style='border:1px solid #cccccc;padding-left:40'>
<a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext><font class=extext_l>[디자인관리 > 기타페이지 디자인 > 주문하기 > order.htm]</font></a> 을 눌러<br> 치환코드 <font class=ver8 color=000000><b>{=displayEggBanner(1)}</b></font> 를 삽입하세요. <a href='../design/codi.php?design_file=order/order.htm' target=_blank><font class=extext_l>[바로가기]</font></a></font></td>
</tr>
</table>
</td></tr>
</table>

<div style="padding-top:15"></div>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>구매안전서비스 가입 표기 의무화 안내 (2007년 9월 1일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>① 표시·광고 또는 고지의 위치를 사이버몰 초기화면과 소비자의 결제수단 선택화면 두 곳으로 정함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 사이버몰 초기화면 상법 제10조제1항의 사업자의 신원 등 표기사항 게재부분의 바로 좌측 또는 우측에 구매안전서비스 관련 사항을 표시하도록 함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 소비자가 정확한 이해를 바탕으로 구매안전서비스 이용을 선택할 수 있도록, 결제수단 선택부분의 바로 위에 구매안전서비스 관련사항을 알기 쉽게 고지하여야  함.</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>② 표시·광고 또는 고지 사항으로 다음의 세 가지를 규정함.</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 현금 등으로 전자보증 최소 금액 이상 결제시 소비자가 구매안전서비스의 이용을 선택할 수 있다는 사항</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 통신판매업자 자신이 가입한 구매안전서비스의 제공사업자명 또는 상호</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>- 소비자가 구매안전서비스 가입사실의 진위를 확인 또는 조회할 수 있다는 사항</font></td></tr>
<tr><td height=10></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font class=extext><b>구매안전서비스 의무 적용 확대 (2013년 11월 29일 시행)</b></font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>① 개정 내용</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>5만원 이하 거래에 대해서도 소비자의 권익을 보호하기 위하여 구매안전서비스 의무 적용 대상 확대 <br/>1회 결제 기준, 5만원 이상 → 5만원 이하의 소액 거래(모든 금액)
</font></td></tr>
<tr><td style="padding-top:4px">&nbsp;&nbsp;<font class=extext>② 관련 법률</font></td></tr>
<tr><td style="padding-left:16"><font class=extext>전자상거래 등에서의 소비자보호에 관한 법률 <br/>[ 법률 제11841호, 공포일: 2013.5.28, 일부 개정 ]</font></td></tr>
<tr><td height=10></td></tr>
</table>
</td></tr></table>
<!-- 구매안전 서비스 표기 적용 방법 안내 : End -->


<!-- 제3자 정보제공 설정 : Start -->
<div style="padding-top:20px"></div>

<div class="title title_top">제3자 정보제공 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=36')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="padding:0 10px 20px 10px;">
	2012년 8월 18일부로 시행되는 정보통신망법 제23조 2의 "주민등록번호 수집제한 및 보유금지"에 따라 향 후 구매자의 보증보험증권<br>
	발급시 사용되던 '주민등록번호'가 구매자의 '생년월일'과 '성별'로 대체됩니다.<br>
	따라서 전자보증을 신청하셨거나 신청해 주시는 운영자께서는 반드시 운영중인 쇼핑몰에 "개인정보 수집/동의 및 제3자 정보제공 동의"<br>
	항목을 게재하여 이용고객으로 하여금 '동의함'을 받아야 합니다.
</div>
<!-- 제3자 정보제공 설정 : End -->


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<dl style="margin:0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">보증보험 ID :  서비스 연동을 위한 유세이프 가입 ID입니다. 서비스 승인후 자동으로 기재됩니다.</dt>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">보증범위 :</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>① 전체보증(무조건 발금) - 결제수단(카드, 현금) 및 금액 상관없이 무조건 보증서 발행(소비자 선택없음)</li>
	<li>② 부분보증(법 의무화 범위) - 전자보증 최소 금액 이상으로 현금 주문한 경우 소비자 선택 보증서 발행</li>
	</ul>
</dd>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">보증보험 발급을 통한 결제시 수수료 안내</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>① 수수료 판매자 부담 - 총 결제금액의 0.535%를 판매자가 부담하게 됩니다.</li>
	<li>② 수수료 소비자 부담 - 총 결제금액의 0.535%를 구매자가 부담하게 됩니다.</li>
	</ul>
</dd>
</dl>
<dl style="margin:5px 0 0 0;">
<dt><img src="../img/icon_list.gif" align="absmiddle">업무처리 안내</dt>
<dd style="margin-left:8px;">
	<ol style="list-style-type:none; margin:0; padding:0;">
	<li>① 보증서발급
		<ul>
		<li>무통장입금, 신용카드, 계좌이체, 가상계좌 모두 발급 가능합니다. (핸드폰결제 제외)</li>
		<li>PG사 결제(신용카드/계좌이체/가상계좌) 경우에는 정상적으로 승인되면 발급됩니다.</li>
		<li>PG사 결제는 승인인데, 보증서발급이 실패했을 경우에는 '주문배송조회'에서 재발급 신청할 수 있습니다.</li>
		</ul>
	</li>
	<li>② 보증서취소/입금확인 - 유세이프 사이트만 지원합니다.</li>
	</ol>
</dd>
</dl>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>