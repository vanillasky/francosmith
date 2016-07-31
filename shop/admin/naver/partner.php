<?

@include "../../conf/partner.php";

$location = "네이버 쇼핑 > 네이버 쇼핑 설정";
include "../_header.php";

// 상품가격 설정
$inmemberdc = ($partner['unmemberdc'] == 'Y' ? 'N' : 'Y');
$incoupon = ($partner['uncoupon'] == 'Y' ? 'N' : 'Y');
$naver_version = $partner['naver_version'];
$useYn = $partner['useYn'];
$naver_event_common = ($partner['naver_event_common'] === 'Y' ? 'Y' : 'N');
$naver_event_goods = ($partner['naver_event_goods'] === 'Y' ? 'Y' : 'N');
$checked['cpaAgreement'][$partner['cpaAgreement']] = "checked";
$checked['inmemberdc'][$inmemberdc] = "checked";
$checked['incoupon'][$incoupon] = "checked";
$checked['naver_version'][$naver_version] = "checked";
$checked['useYn'][$useYn] = "checked";
$checked['naver_event_common'][$naver_event_common] = "checked";
$checked['naver_event_goods'][$naver_event_goods] = "checked";

if(isset($partner['cpaAgreementTime'])===false && $partner['cpaAgreement']==='true')
{
	$partner['cpaAgreementTime'] = date('Y.m.d h:i', filemtime(dirname(__FILE__).'/../../conf/partner.php'));
	require_once dirname(__FILE__).'/../../lib/qfile.class.php';
	$qfile = new qfile();
	$partner = array_map("addslashes",array_map("stripslashes",$partner));
	$qfile->open(dirname(__FILE__).'/../../conf/partner.php');
	$qfile->write("<? \n");
	$qfile->write("\$partner = array( \n");
	foreach ($partner as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
}
?>

<?php include dirname(__FILE__).'/../naverCommonInflowScript/configure.php'; ?>

<div class="title title_top">네이버 쇼핑 설정<span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse; width: 800px;">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b><font color="#bf0000">*필독*</font> 네이버 쇼핑 버전설정 안내입니다.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>네이버 쇼핑 상품DB URL 버전이 업그레이드(1.0 → 2.0) 되었습니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>업그레이드된 버전 변경관련 유의사항 입니다. 반드시 확인하신 후 변경해 주시길 바랍니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>1) 네이버 쇼핑 1.0 사용 상점이 버전 변경을 원하지 않는 경우</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 기존의 사용하시던 1.0버전으로도 네이버 쇼핑 서비스를 이용하실 수 있습니다.</font></div>
<div style="padding-top:5"></div>
<div style="padding-top:5"><font class=g9 color=666666>2) 네이버 쇼핑 1.0 고객이 2.0으로 변경하고자 하는 경우</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 네이버 쇼핑 2.0 으로 변경이 가능합니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;단, 상점의 네이버 쇼핑 버전과 네이버 쇼핑에 설정된 EP버전이 동일해야 합니다. </font><font color="#bf0000"><U>동일하게 설정되지 않은 경우 상품 Data가 모두 삭제됩니다.</U></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;*버전 변경 방법*&nbsp;</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;먼저 네이버 쇼핑파트너존 > 상품관리 > 업데이트 현황 > 쇼핑몰 상품DB(EP) URL에서 2.0 으로 직접 설정할 수 있습니다.<a href="http://adadmin.shopping.naver.com/login/login_start" target="_blank">[설정하기]</a></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;네이버에서 2.0 으로 변경한 후 고도 솔루션 관리자 페이지에서도 2.0 으로 변경을 해주셔야 합니다. 관련문의 : 고도 마케팅팀 02-567-3719</font></div>
<div style="padding-top:5"><font class=g9 color=666666>3) 네이버 쇼핑 2.0 버전 사용 시 상품 이미지 전송</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;- 전송 이미지 : 등록된 상품의 "확대(원본)이미지"를 전송함.(단, "확대(원본)이미지"가 없는 경우 "상세이미지"로 대체하여 전송.<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"상세이미지"도 없는 경우 네이버 쇼핑 상품등록이 되지 않습니다.)
</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;- 이미지 사이즈 : 최소 300 * 300 pixels 이상(권장 500 * 500 pixels 이상), 최대 1200 * 1200 pixels 이하(1MB 미만)
</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 이미지 타입 : JPEG</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 확대(원본)이미지와 상세이미지가 등록되지 않은 상품은 네이버 쇼핑에 전달되지 않습니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 추가사항 : 여백 최소화 및 이미지 중앙 정렬하여 생성</font></div>
<div style="padding-top:5"><font class=g9 color=666666>
	&nbsp;&nbsp;&nbsp;&nbsp;※ 위에서 제시한 이미지 사이즈, 용량, 타입이 맞지 않거나 주목 효과를 위해 상품의 이미지와 관련이 없는 외곽라인, 도형삽입,<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;인위적인 마크, 텍스트 등이 포함되어 있는 이미지는 허용하지 않으며, 네이버 쇼핑에서 삭제 처리 될 수 있으니 주의하여 주시기 바랍니다.
</font></div>
</td></tr>
</table>

<div style="padding-top:10"></div>

<form name=form method=post action="indb.php" style="width: 800px;" id="naver-service-configure" target="ifrmHidden">

<style type="text/css">
div#cpa-terms{
	border: solid #dce1e1 4px;
	width: 800px;
	margin-bottom: 20px;
	padding: 10px;
}
div#cpa-terms h2{
	font-size: 15px;
}
div#cpa-terms.summary .hide{
	display: none;
}
div#cpa-terms.detail .hide{
	display: block;
}
div#cpa-terms div.view{
	text-align: right;
}
div#cpa-terms div.view button{
	padding: 3px;
	margin: 0;
	background-color: #f9feef;
	border: solid 1px #cccccc;
}
div#cpa-terms.summary div.view button.detail-view{
	display: inline;
}
div#cpa-terms.summary div.view button.summary-view{
	display: none;
}
div#cpa-terms.detail div.view button.detail-view{
	display: none;
}
div#cpa-terms.detail div.view button.summary-view{
	display: inline;
}
#premium-log-analyze-info{
	border: solid #dce1e1 4px;
	width: 800px;
	margin-bottom: 20px;
	padding: 10px;
}
</style>
<div id="cpa-terms" class="summary">
	<div>
		네이버 쇼핑 CPA와 검색광고주를 위한 프리미엄 로그 분석을 사용함에 있어 공통스크립트 설치를 통해 수집되는 항목은 아래와 같습니다.<br/>
		해당 항목의 실제적인 수집은 스크립트 설치 동의와 상관없이 실제 네이버에서 해당 서비스 가입을 한 경우에만 일어납니다.
	</div>
	<h2>[네이버 쇼핑 CPA 수집 목적 및 항목]</h2>
	<ol>
		<div>CPA란, 네이버 쇼핑을 통해 입점몰 사이트에서 주문 발생시, 주문에 관련한 정보를 수집하는 방식입니다.</div>
		<div>즉, CPA에서 의미하는 ACTION은 주문발생을 의미합니다.</div></br>

		<li>
			<div>1. 데이터 수집목적:</div>
			<div>- 네이버 쇼핑을 통해 유입되는 트래픽(Traffic)을 통한 구매전환 효과측정</div></br>
		</li>

		<li>
			<div>2. 수집 데이터 항목:</div>
			<div>- 광고주 쇼핑몰에서 발생하는 이용자 주문의 일시 / 번호 / 상품 / 수량 / 금액 등</div></br>
		</li>

		<li>
			<div>3. 수집 데이터 활용범위:</div>
			<div>- 데이터 수집에 따른 결과는 네이버 쇼핑 운영자인 네이버 (주) (이하 '회사'라 함)의 내부 분석 목적의 활용</div>
			<div>- 데이터 수집에 따른 결과는 네이버 쇼핑 DB리스팅 노출순위(랭킹) 결정 요소로 활용</div></br>
		</li>

		<li class="hide">
			<div>4. 데이터 수집 관련 주요사항:</div>
			<div>- 광고주는 회사가 제공하는 스크립트 설치가이드에 따라 쇼핑몰에 스크립트를 설치합니다.</div>
			<div>- (네이버와 제휴된 호스팅사를 이용하는 경우 호스팅사에서 호스팅사 솔루션에 스크립트를 일괄 설치합니다. 따라서 수집동의시 주문정보가 회사에게 제공됩니다.)</div>
			<div>- 광고주는 회사가 제공하는 스크립트 설치가이드에서 정한 데이터 수집 운영정책을 준수하여야 하며, 광고주가 해당 운영정책 위반시 회사는 제재정책에 따라 광고주를 제재할 수 있습니다.</div>
			<div>- 광고주는 회사가 요청하는 경우 정상적인 데이터 수집의 검증을 위해 회사가 정한 기간과 양식에 따라 네이버 쇼핑 트래픽(Traffic)을 통해 쇼핑몰에서 발생한 거래내역(주문완료, 결제완료)과 취소/환불/반품내역을 회사에게 제공합니다.</div>
			<div>- 광고주는 CPA 데이터 수집 동의 이후라도 언제든 자신의 판단에 따라 회사에 사전 통지하고 쇼핑몰 내 스크립트를 삭제함으로써 본 동의를 철회할 수 있습니다.</div>
			<div>- (네이버와 제휴된 호스팅사 광고주는 동의 철회를 원할 경우 회사에 통보하여 동의 철회를 진행할 수 있으며 호스팅사에서 주문정보 전달을 중단하게 됩니다.)</div>
			<div>- 회사는 광주의 데이터 수집 동의와 스크립트 설치가 완료된 이후부터 CPA 데이터 수집을 시작하며, 광고주가 동의를 철회하거나 광고주의 운영정책 위반으로 회사가 제재조치로써 데이터 수집을 중단하기 전까지 CPA 데이터를 계속 수집할 수 있습니다.</div>
			<div>- 회사는 데이터 수집 및 광고주 스크립트 설치 지원 업무를 제3자에게 위탁하여 처리할 수 있습니다.</div></br>
		</li>

		<li class="hide">
			<div>5. 데이터 전송 검증을 위한 테스트:</div>
			<div>- CPA 수집동의 몰을 대상으로 회사는 데이터의 정상적인 전송여부를 검증하기 위해 주기적으로 모니터링 및 테스트 주문을 발생시킬 수 있습니다.</div>
			<div>- 주문 테스트는 1회 당 4~10건 정도 진행되며, 해당 주문은 테스트 후 즉시 취소 처리합니다.</div></br>
		</li>
	</ol>
	<div class="view">
		<button type="button" class="detail-view" onclick="document.getElementById('cpa-terms').className='detail';">자세히 보기</button>
		<button type="button" class="summary-view" onclick="document.getElementById('cpa-terms').className='summary';">간단히</button>
	</div>
	<div>본인은 상기 CPA 데이터 수집 동의 주요 사항에 기재된 내용을 성실히 이행할 것을 동의합니다.</div>
</div>
<div id="premium-log-analyze-info" class="red">프리미엄 로그분석 서비스 가입자도 반드시 CPA동의해주셔야 하며, 네이버 쇼핑 이용을 안 하시는 경우 아래 데이터 수집은 일어나지 않습니다.</div>

<input type=hidden name=mode value="naver">

<table class=tb border=0>
<col class=cellC><col class=cellL>
<?
@include "../../conf/fieldset.php";
list($grpnm,$grpdc) = $db->fetch("select grpnm,dc from ".GD_MEMBER_GRP." where level='".$joinset[grp]."'");
?>
<tr>
	<td>사용여부</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y'];?>/>사용</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n'];?> <?php echo $checked['useYn'][''];?> />사용안함</label>
	</td>
</tr>
<tr>
	<td>CPA 주문수집<br/>동의여부</td>
	<td class="noline">
		<label><input type="checkbox" name="cpaAgreement" value="true" required="required" <?php echo $checked['cpaAgreement']['true']; ?>/> CPA 주문수집에 동의함</label>
		<?php if(isset($partner['cpaAgreementTime'])){ ?>
		<span style="margin-left: 30px; color: #991299; font-weight: bold;">동의일시 : <?php echo $partner['cpaAgreementTime']; ?></span>
		<?php } ?>
		<br/>
		<span class="extext">
			네이버에서 CPA 주문수집에 동의하신 경우에만 주문완료시 주문정보를 네이버측으로 전송합니다.<br/>
			CPA 주문수집이 정상적으로 이루어 져야만 차후 CPA로의 과금전환이 이루어질수 있습니다.<br/>
			주문수집에 동의하신뒤에는 반드시 체크하여주시기 바라며, CPA 주문수집에대한 문의는 네이버 쇼핑파트너존으로 문의주시기 바랍니다.<br/>
			<strong>네이버 쇼핑파트너존 : 1588-3819</strong>
		</span>
	</td>
</tr>
<tr>
	<td>네이버 쇼핑<br/>버전설정</td>
	<td class="noline"><input type="radio" name="naver_version" value="1" <?=$checked['naver_version']['1']?> <?=$checked['naver_version']['']?>>기존(v1.0)&nbsp;&nbsp;<input type="radio" name="naver_version" value="2" <?=$checked['naver_version']['2']?>>신규(v2.0) &nbsp; <span class="extext" style="font-weight:bold">버전설정 안내문구를 반드시 읽어주시기 바랍니다.</span></td>
</tr>
<tr>
	<td>상품가격 설정</td>
	<td class="noline">
	<div class="extext" style="padding-bottom:5px;">네이버 쇼핑에 노출되는 가격정보를 설정합니다.<br/>
		일반적으로 네이버 쇼핑에 노출되는 가격은 적용된 쿠폰과 네이버 쇼핑 가입시 등록한 회원그룹 할인율이 적용된 가격이 노출됩니다.<br/>
		설정 사항을 체크 하지 않을 경우 쿠폰 및 할인율이 적용되지 않은 판매가로 노출됩니다.
	</div>
	<div>
		<span class="noline"><input type="checkbox" name="inmemberdc" value="Y" <?=$checked['inmemberdc']['Y']?>/></span> 회원그룹 할인율 적용
		<div style="padding:3px 0px 0px 25px;">
			<div><b><?=$grpnm?></b> 할인율은 <b><?=number_format($grpdc)?>%</b>가 상품가격에 적용되어 네이버 쇼핑에 노출 됩니다.</div>
			<div class="extext">가입시 회원그룹 설정은 <a href="../member/fieldset.php" class="extext" style="font-weight:bold">회원관리 > 회원가입관리</a>에서 변경 가능합니다.</div>
			<div class="extext">회원그룹의 할인율 변경은 <a href="../member/group.php" class="extext" style="font-weight:bold">회원관리 > 회원그룹관리 </a>에서 변경 가능합니다.</div>
		</div>
	</div>
	<div>
		<span class="noline"><input type="checkbox" name="incoupon" value="Y" <?=$checked['incoupon']['Y']?>/></span> 쿠폰 적용
		<div style="padding:3px 0px 0px 25px;">
			<div class="extext">쿠폰은 <a href="../event/coupon.php" class="extext" style="font-weight:bold">프로모션/SNS > 쿠폰리스트 </a>에서 관리 가능합니다.</div>
		</div>
	</div>
	</td>
</tr>
<tr>
	<td>네이버 쇼핑<br />무이자할부정보</td>
	<td><input type=text name=partner[nv_pcard] value="<?=$partner[nv_pcard]?>" class=lline></td>
</tr>
<tr>
	<td>네이버 쇼핑<br />상품명 머릿말 설정</td>
	<td>
	<div><input type=text name="partner[goodshead]" value="<?=$partner[goodshead]?>" class=lline></div>
	<div class="extext">* 상품명 머리말 설정을 위한 치환코드</div>
	<div class="extext">- 머리말 상품에 입력된 "제조사"를 넣고 싶을 때 : {_maker}</div>
	<div class="extext">- 머리말 상품에 입력된 "브랜드"를 넣고 싶을 때 : {_brand}</div>
	</td>
</tr>
<tr>
	<td>네이버 쇼핑<br />이벤트 문구 설정</td>
	<td>
	<div class="extext">Step1. 쇼핑몰 이벤트 문구 입력 (최대 100자)</div>
	<div style="padding:3px 0px 0px 25px;">
		<span class="noline"><input type="checkbox" name="naver_event_common" value="Y" <?=$checked['naver_event_common']['Y']?>/></span> 공통 문구 사용
		<input type=text name="partner[eventCommonText]" value="<?=$partner[eventCommonText]?>" class=line style="width:80%">
		<span class="noline"><input type="checkbox" name="naver_event_goods" value="Y" <?=$checked['naver_event_goods']['Y']?>/></span> 상품별 문구 사용
		<div style="padding:3px 0px 0px 25px;">
			<div>- "상품등록 > 상품 설명 하단 > 이벤트 문구 입력 항목"에 개별 문구를 입력해주세요. <a href="../goods/adm_goods_list.php" class="extext" style="font-weight:bold">[상품관리 바로가기]</a></div>
			<div>- 일괄 등록을 원할 경우 "데이터관리 > 상품DB 등록" 기능을 활용해주세요 <a href="../goods/data_goodscsv.php " class="extext" style="font-weight:bold">[상품DB등록 바로가기]</a></div>
		</div>
	</div></br>
	<div class="extext">Step2. 네이버 쇼핑 이벤트 문구 노출 설정</div>
	<div style="padding:3px 0px 0px 25px;">
		<div>- 네이버 쇼핑파트너존 접속 <a href="http://adcenter.shopping.naver.com" class="extext" style="font-weight:bold">adcenter.shopping.naver.com</a></div>
		<div>- 상품관리 > 상품정보수신현황 > 이벤트필드 노출상태 > 등록요청</div>
	</div>
	</td>
</tr>
</table>
<div class="noline" style="text-align: center; padding: 10px;">
	<a href="javascript:document.form.submit();"><img src="../img/btn_naver_install.gif" align=”absmiddle”></a>
</div>
</form>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>네이버 쇼핑 무이자할부정보란?: 각 카드사별 무이자정보를 입력하실 수 있습니다. 예) 삼성3/현대6/국민12</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>무이자할부정보를 입력/저장후 아래 상품DB URL의 수동 업데이트를 실행하면 상품DB URL 정보 중 무이자 정보가 필드인 pcard필드의 정보가 변경됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>변경된 무이자할부정보는 네이버 쇼핑 업데이트 주기에 따라 네이버 쇼핑에 반영되어집니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>네이버에 노출되는 상품정보는 다시 등록하시는 것이 아닙니다.</td></tr>
<tr><td style="padding-left:10">현재 운영중인 쇼핑몰의 상품정보를 네이버가 매일 새벽에 자동으로 가져갑니다.</td></tr>
</table>
<br/>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>네이버 쇼핑에서 상품검색이 많이 될 수 있도록 상품명 머리말 설정을 활용하세요!</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>예시 1) 상품명 머리말 설정 : 공란</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class=small_ex>
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>네이버 노출 상품명</td>
	</tr>
	<tr>
		<td>여자청바지</td>
		<td>스웨덴</td>
		<td>폴로</td>
		<td>여자청바지</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>예시 2) 상품명 머리말 설정 : [무료배송 / {_maker} / {_brand}]</td></tr>
<tr>
	<td style="padding-left:10">
	<table style='border:1px solid #ffffff;width:400' class=small_ex>
	<col align="center" width="60"><col align="center" width="50"><col align="center" width="50"><col>
	<tr>
		<td>상품명</td>
		<td>제조사</td>
		<td>브랜드</td>
		<td>네이버 노출 상품명</td>
	</tr>
	<tr>
		<td>여자청바지</td>
		<td>스웨덴</td>
		<td>폴로</td>
		<td>[무료배송 / 수에덴 / 폴로] 여자청바지</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script>cssRound('MSG02')</script>
</div>

<p>
<? if(in_array($naver_version,array('','1'))){	// 기존 EP(v1.0) ?>
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>업체</th>
	<th>상품 DB URL [페이지 미리보기]</th>
	<th>최근 업데이트일시</th>
	<th>업데이트</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>네이버 쇼핑<br>상품DB URL페이지</td>
	<td>
	<font color="57a300">[전체상품]</font> <?if(file_exists('../../conf/engine/naver_all.php')){?><a href="../../partner/naver.php" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}else{?>업데이트필요<?}?><br>

	<font color="57a300">[요약상품]</font> <?if(file_exists('../../conf/engine/naver_summary.php')){?><a href="../../partner/naver.php?mode=summary" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=summary</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a><?}else{?>업데이트필요<?}?><br>

	<font color="57a300">[신규상품]</font> <a href="../../partner/naver.php?mode=new" target=_blank><font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=new</font> <img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
	<td align=center><font class=ver81>
		<?if(file_exists('../../conf/engine/naver_all.php'))echo date('Y-m-d h:i:s',filectime ( '../../conf/engine/naver_all.php'));?>
	</td>
	<td align=center>
		<a href="../../partner/engine.php?mode=all" target='ifrmHidden'><img src="../img/btn_price_update.gif"></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<div class=small1 ><img src="../img/icon_list.gif" align=absmiddle><b><font color=ff6600>상품정보 변경시나 상품 DB URL의 값이 없을 시에는 반드시 업데이트버튼을 눌러주세요</font></B></div>
<div style="padding-top:2"></div>
<table align=center>
<tr><td width=500>
 <div align=center class=small1 style='padding-bottom:3'><font color=6d6d6d>업데이트가 진행되면 아래 바를 통해 진행율이 보이게 됩니다.<br>완료메시지가 출력될때까지 다른 동작을 삼가하여주십시요.</font></div>
		<div style="height:8px;font:0;background:#f7f7f7;border:2 solid #cccccc">
		<div id=progressbar style="height:8px;background:#FF4E00;width:0"></div>
 </div>
</td></tr>
</table>
<? }else{	// 신규 EP(v2.0) ?>
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>업체</th>
	<th>상품 DB URL [페이지 미리보기]</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>네이버 쇼핑<br>상품DB URL페이지</td>
	<td>
	<font color="57a300">[전체상품]</font> <a href="../../partner/naver.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br>
	<font color="57a300">[요약상품]</font> <a href="../../partner/naver.php?mode=summary" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver.php?mode=summary</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<? }?>

<br><br>
<!--
<table width=100% cellpadding=0 cellspacing=0>
<col class=cellC><col style="padding:5px 10px;line-height:140%">
<tr class=rndbg>
	<th>업체</th>
	<th style="padding-right:150px">개편 상품 DB URL [페이지 미리보기]</th>
</tr>
<tr><td class=rnd colspan=10></td></tr>
<tr>
	<td>네이버 쇼핑<br>상품DB URL페이지</td>
	<td>
	<font color="57a300">[전체상품]</font> <a href="../../partner/naver2_all.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver2_all.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br>
	<font color="57a300">[요약상품]</font> <a href="../../partner/naver2_summary.php" target=_blank>
	<font class=ver8>http://<?=$_SERVER['HTTP_HOST'].$cfg[rootDir]?>/partner/naver2_summary.php</font>
	<img src="../img/btn_naver_view.gif" align=absmiddle></a>
	</td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
</table>
<div class=small1 ><img src="../img/icon_list.gif" align=absmiddle><b><font color=ff6600>
새로 개편된 EP(Engine Page)주소입니다.
</font></B></div>
-->

<div style='padding:0 0 10 0; text-align:center;'>
<a href="http://marketing.godo.co.kr/board.php?id=notice&mode=view&postNo=178" target="_blank"><img src="../img/btn_naver_dbUrl.gif" border="0"></a>
<a href="https://adcenter.shopping.naver.com" target="_blank"><img src="../img/btn_naver_go.gif" border="0"></a>
</div>

<? include "../_footer.php"; ?>