<?

@include "../../conf/partner.php";

$location = "네이버 쇼핑 > 네이버 쇼핑 설정";
include "../_header.php";
include "../../lib/naverPartner.class.php";

$naver = new naverPartner();

// 상품가격 설정
$inmemberdc = ($partner['unmemberdc'] == 'Y' ? 'N' : 'Y');
$incoupon = ($partner['uncoupon'] == 'Y' ? 'N' : 'Y');
$naver_version = $partner['naver_version'];
$useYn = $partner['useYn'];
$naver_event_common = ($partner['naver_event_common'] === 'Y' ? 'Y' : 'N');
$naver_event_goods = ($partner['naver_event_goods'] === 'Y' ? 'Y' : 'N');
$auto_create_use = ($partner['auto_create_use'] === 'Y' ? 'Y' : 'N');
$checked['cpaAgreement'][$partner['cpaAgreement']] = "checked";
$checked['inmemberdc'][$inmemberdc] = "checked";
$checked['incoupon'][$incoupon] = "checked";
$checked['naver_version'][$naver_version] = "checked";
$checked['useYn'][$useYn] = "checked";
$checked['naver_event_common'][$naver_event_common] = "checked";
$checked['naver_event_goods'][$naver_event_goods] = "checked";
$checked['auto_create_use'][$auto_create_use] = "checked";

//서버호스팅, 외부호스팅
$outsideServer = false;
if($godo['webCode'] == 'webhost_outside' || $godo['webCode'] == 'webhost_server'){
	$outsideServer = true;
}

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
<div style="padding-top:7"><font class=g9 color=666666>네이버 쇼핑 상품DB URL 버전이 업그레이드(2.0 → 3.0) 되었습니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>업그레이드된 버전 변경관련 유의사항 입니다. 반드시 확인하신 후 변경해 주시길 바랍니다.</font></div><br>
<div style="padding-top:5"><b>1) 기존 버전(v1.0, v2.0) 이용 고객의 경우 2017년 8월 18일까지 정상적으로 이용하실 수 있습니다.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;- 2017년 8월 18일까지는 기존의 사용하시던 1.0, 2.0 버전으로도 네이버 쇼핑 서비스를 정상적으로 이용하실 수 있습니다.</font></div><br>
<div style="padding-top:5"><b>2) 단, 2017년 8월 18일 이후에는 기존 버전 (v1.0, v2.0)의 서비스가 종료되므로 해당 일자 이전에 v3.0으로 버전을 변경하여야 합니다.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;*버전 변경 방법*&nbsp;</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;먼저 네이버 쇼핑파트너존 > 상품관리 > 업데이트 현황 > 쇼핑몰 상품DB(EP) URL에서 3.0 으로 직접 설정합니다.<a href="https://adcenter.shopping.naver.com/member/login/form.nhn" target="_blank"><font color=blue>[설정하기]</font></a></font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;네이버에서 3.0 으로 변경한 후 고도 솔루션 관리자 페이지에서도 3.0 으로 변경을 해주셔야 합니다. 관련문의 : 고도 마케팅팀 02-567-3719</font></div>
<div style="padding-top:5"><font class=g9 color=666666>&nbsp;&nbsp;&nbsp;&nbsp;※ 상점의 네이버 쇼핑 버전과 네이버 쇼핑에 설정된 EP버전이 동일해야 합니다. <font color=red><u>동일하게 설정되지 않은 경우 상품 Data 수신에 오류가 발생합니다.</u></font></font></div><br>
<div style="padding-top:5"><b>3) 신규 가입자는 v3.0(신규)로 설정하여 주시기 바랍니다.</b></font></div>
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
	<td class="noline"><input type="radio" name="naver_version" value="1" <?=$checked['naver_version']['1']?> onclick="version();">v1.0(기존)&nbsp;&nbsp;<input type="radio" name="naver_version" value="2" <?=$checked['naver_version']['2']?> onclick="version();">v2.0(기존)&nbsp;&nbsp;<input type="radio" name="naver_version" value="3" <?=$checked['naver_version']['3']?> <?=$checked['naver_version']['']?> onclick="version();">v3.0(신규) &nbsp; <span class="extext" style="font-weight:bold">버전설정 안내문구를 반드시 읽어주시기 바랍니다.</span></td>
</tr>
<? if ($outsideServer === false) { ?>
<tr id="auto_create">
	<td>상품 EP<br/>자동 생성 설정</td>
	<td class="noline">
		● 자동 생성 기능 사용 여부 :
		<label><input type="radio" name="auto_create_use" value="Y" <?php echo $checked['auto_create_use']['Y'];?>/>사용</label><label><input type="radio" name="auto_create_use" value="N" <?php echo $checked['auto_create_use']['N'];?> />사용안함</label><br/>
		<div style="padding:3px 0px 5px 25px;">
			<span class="extext">네이버 쇼핑에서 스크랩하는 정보를 1일 1회, 자동으로 생성합니다.</span><br>
			<span class="extext" style="font-weight:bold"> - 상품이 매우 많을 경우 사용으로 설정 시 더 안정적으로 전송할 수 있습니다.</span>
		</div>
		● 실행 시간대 설정 :
		<select name="auto_excute_time" style="width:80px;">
			<option value="00" <?=($partner['auto_excute_time'] === '00') ? 'selected' : ''?> <?=(!$partner['auto_excute_time']) ? 'selected' : ''?>>00시</option>
			<option value="01" <?=($partner['auto_excute_time'] === '01') ? 'selected' : ''?>>01시</option>
			<option value="02" <?=($partner['auto_excute_time'] === '02') ? 'selected' : ''?>>02시</option>
			<option value="03" <?=($partner['auto_excute_time'] === '03') ? 'selected' : ''?>>03시</option>
			<option value="04" <?=($partner['auto_excute_time'] === '04') ? 'selected' : ''?>>04시</option>
			<option value="05" <?=($partner['auto_excute_time'] === '05') ? 'selected' : ''?>>05시</option>
		</select><br/>
		<div style="padding:3px 0px 5px 25px;">
			<span class="extext">선택한 시간에 네이버 쇼핑에 보낼 상품 DB 정보를 자동으로 생성합니다.</span><br>
			<span class="extext">전체상품 DB 업데이트 주기를 확인하여 업데이트 시간대를 제외하고 선택하시기 바랍니다.</span>
		</div>
	</td>
</tr>
<?}?>
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
	<td><input type=text name=partner[nv_pcard] value="<?=$partner[nv_pcard]?>" class=lline>
	<div class="extext">예) 기존 버전(v1.0, 2.0) : 삼성3/현대3/국민6<br/>&nbsp;&nbsp;&nbsp;신규 버전(v3 . 0) : 삼성카드^2~3|현대카드^2~3|KB국민카드^2~6</div></td>
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

<div id="shoppingGoodsDiv" style="width:800px;">
<div class="title title_top">네이버 쇼핑 상품 노출 설정<span> 네이버 쇼핑에 노출할 상품을 설정합니다. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=35')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></span></div>
<? if ($naver->migrationCheck() == false) { ?>
<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse; width: 800px;">
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b><font color="#bf0000">*필독*</div>
<div style="padding-top:7"><font class=g9 color=666666>네이버 쇼핑 기준에 따라, 노출이 가능한 상품은 최대 50만개 입니다.</font></div>
<div style="padding-top:5"><font class=g9 color=666666>따라서, 아래에 설정에 따라 <b>네이버 쇼핑 상품 DB를 499,000개 이하로 생성하는 기능을 제공하고 있습니다.</b></font></div>
<div style="padding-top:5"><font class=g9 color=666666>(50만개가 초과하면 네이버 쇼핑 서비스가 중지되어 안전한 사용을 위하여 499,000개 까지 등록하실 수 있습니다.)</font></div>
<div style="padding-top:5"><font size=2 color=#627dce><b><br>※ 총 상품수가 499,000개를 넘지 않는 경우에는 별도 설정 없이도 정상적으로 네이버 쇼핑을 이용하실 수 있습니다.</b></font></div>
</td></tr>
</table>

<div style="padding-top:10"></div>

<form name=frm method=post action="indb.php" target="ifrmHidden">
<input type=hidden name=mode value="naverShopingGoods">
<table style="border:1px solid #d5d5d5; border-collapse:collapse; width: 800px;">
<col class=cellC><col class=cellL>
<tr>
	<td style="border:1px solid #d5d5d5; width:130px;">노출 카테고리 설정</td>
	<td>
		<div style="padding:5 5 5 5">● 노출 카테고리 선택</div>
		<table style="border:1px solid #d5d5d5; margin-left:5px; margin-right: px; width:650px;">
			<tr>
				<td>
					<div class="extext" style="padding-top:5">2차 분류까지 선택할 수 있으며 선택한 카테고리에 속한 상품이 499,000개를 초과할 수 없습니다.</div>
					<div class="extext">선택한 카테고리의 상품이 499,000만개 초과 시 <b>최근 상품 등록일자순으로</b> 499,000개 이하로 노출 상품이 조정됩니다.</div>
					<div class="extext">노출 카테고리를 설정하지 않은 경우, 전체 상품에서 <b>최근 상품 등록일자를 기준</b>으로 상품을 노출합니다.</div>
				</td>
			</tr>
		</table>
		<div style="padding:5 5 5 5"><font size="4">총 상품수 : <span id="goodsAllCount"><font color=red><b>로딩중...</font></b></span> 개</font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:red;">** 총 상품수는 품절상품과 미진열상품을 제외한 개수입니다.</span></div>
		<div style="margin-left: 5px; margin-right: 5px; margin-bottom: 5px;">
		<div valign="top"><script type="text/javascript">new categoryBox('cate[]',2,'','naver','frm');</script></div>
		</div>
		<div class="extext" style="text-align: right; padding:5 5 5 5;"> - 상품별로 다수의 카테고리를 등록할 수 있기 때문에, 카테고리별 상품수의 총합이 총 상품수보다 많을 수 있습니다.</div>
		<div style="text-align: center;"><a href="javascript:categoryAdd();"><img src='../img/btn_naver_category.gif' align=absmiddle></a></div>
		<div style="text-align: right;"><a href="javascript:deleteAll();"><img src='../img/btn_naver_delete.gif' align=absmiddle></a></div>
		<div id="selectCategory" style="border:1px solid #d5d5d5; padding:5; height: auto; min-height: 100px; margin-top:10px; margin-left:5px; margin-right:5px; margin-bottom:5px;"><font size=4 color=red><b>로딩중...</font></b></div>
		<div style="text-align: center;"><a href="javascript:categoryCalc();"><img src='../img/btn_naver_count.gif' align=absmiddle></a></div>
		<div style="padding:5 5 5 5">● 선택한 카테고리 ( 노출될 상품수 : <span id="goodsCount">-</span> / 499,000 선택된 카테고리 중 중복된 상품수 : <span id="duplicateGoodsCount">-</span> 개)</div>
		<div style="padding:5 5 5 5; color:red;">** 선택한 카테고리 중 중복된 상품은 제외합니다. </div>
		<div style="padding:0 5 5 5; color:red;">** 초과 시 최근 상품등록일자순으로 499,000개 이하로 조정합니다.</div>
		<div style="padding:0 5 5 5;"><font size=2 color=#627dce>※ 노출될 상품수 확인 후 설정정보를 저장해 주세요.</font></div>
	</td>
</tr>

</table>
<div class="noline" style="text-align: center; padding: 10px; width: 800px;">
	<a href="javascript:check();"><img src="../img/btn_naver_install.gif" align=”absmiddle”></a>
</div>
</form>
<div id="overlayDiv" style="filter:alpha(opacity=80); opacity:0.95; background:#44515b; position:absolute; text-align:center; display:table;">
<span style="display:table-cell; vertical-align:middle; color:white; font-size:12pt;"><b>네이버 쇼핑 EP파일 생성 방식을 개선하였습니다.<br>마이그레이션을 하시면 기존에 비해 더욱 안정적으로 네이버 쇼핑 EP파일을 생성할 수 있습니다.<br>아래 마이그레이션 버튼을 클릭하시어 마이그레이션을 실행해주시기 바랍니다.<br>※ 마이그레이션 작업에는 일정 시간이 소요됩니다.<br>※ 마이그레이션 후에는 네이버 쇼핑 상품 설정 메뉴에서 해당 기능을 사용하실 수 있습니다.<br></b>
<a href="javascript:migration();"><img style="margin-top:20px;" src="../img/btn_naver_shopping_migration.png"></a>
</span>
</div>
</div>
<?}else{?>
<div class="extext" style="margin-bottom:50px;">해당 설정은 <a href="naver_shopping_setting.php" style="color:#627dce"><b><u>[네이버 쇼핑 상품 설정]</u></b></a> 메뉴에서 가능합니다.</div>
<?}?>
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
	<img src="../img/btn_naver_view.gif" align=absmiddle></a><br><br>
	<div class="extext">[요약상품]의 경우 기존 EP 버전(v1.0,v2.0)에서만 사용하는 기능으로 ‘v3.0(신규)’ 설정 시 사용하지 않습니다.</span>
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
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script>
var selectedGoodsCount = 0;		// 노출될 상품 수
var duplicateGoodsCount = 0;	// 중복된 상품 수 계산용
var goodsCountCheck = 0;		// 노출될 상품수 확인 체크
var cateValues = new Array();	// 선택 되어있는 카테고리 번호
var outsideServer = '<?=$outsideServer?>';
window.onload = function(){
	if (outsideServer == false) {
		version();
	}
	else {}

	<? if ($naver->migrationCheck() == false) { ?>overlay(); <?}?>
}

function version() {
	if (outsideServer == true) {
		return;
	}
	var f = document.form;
	if (f.naver_version[0].checked) {
		document.getElementById('auto_create').style.display = "none";
	}
	else {
		document.getElementById('auto_create').style.display = "";
	}
}

// 노출될 상품수 확인 여부 체크
function check() {
	if (goodsCountCheck == 0) {
		alert('노출될 상품수 확인 후 해당 설정으로 저장이 가능합니다.');
		return;
	}
	else {
		document.frm.submit();
	}
}

// 등록된 상품수 출력
function goodsCalc() {
	var ajax = new Ajax.Request('../naver/naver_category_calc.php',
	{
		method: 'POST',
		parameters: 'mode=goods',
		onComplete: function () {
			var req = ajax.transport;
			if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
				alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
				return;
			}

			document.getElementById('goodsAllCount').innerHTML = comma(req.responseText);
			document.getElementById('goodsAllCount').style.color = 'red';
		},
		onFailure : function() {
			alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			return;
		}
	});
}

// 저장된 카테고리 목록 출력
function viewCategory() {
	var str = Array();
	var temp = Array();
	var categoryList = Array();

	var ajax = new Ajax.Request('../naver/naver_category_calc.php',
	{
		method: 'POST',
		datatype: 'array',
		onComplete: function () {
			var req = ajax.transport;
			if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
				alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
				return;
			}
			var parent = document.getElementById("selectCategory");
			parent.removeChild(parent.firstChild);
			categoryList = JSON.parse(req.responseText);

			for (i=0; i<categoryList.length; i++) {
				temp[i] = categoryList[i].split(',');
				str[i] = temp[i][1] + ' (' + comma(temp[i][2]) + '개)';

				duplicateGoodsCount += Number(temp[i][2]);
				cateValues[cateValues.length] = temp[i][0];
			}

			// 노출될 상품수 계산 및 선택한 카테고리 노출
			for (i=0; i<temp.length; i++) {
				categoryText = "<span id='" + temp[i][0] + "' style='display:inline-block; background-color:d5d5d5; padding:5 5 5 5; margin-top:3px; margin-bottom:3px; margin-right:5px;'>" + str[i];
				categoryText += "<input type=hidden name=category[] value='" + temp[i][0] + "' style='display:none'> ";
				categoryText += "<input type=hidden name=category_" + temp[i][0] + " value='" + temp[i][2] + "' style='display:none'> ";
				categoryText += "<a href='javascript:void(0)' onClick='categoryDelete(\"" + temp[i][0] + "\",\"" + temp[i][2] + "\")'><img src='../img/i_del.gif' align=absmiddle></a></span>";

				var selected = document.getElementById('selectCategory');
				selected.innerHTML += categoryText;
			}
		},
		onFailure : function() {
			alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			return;
		}
	});
}

// 노출될 카테고리 추가
function categoryAdd() {
	var selCate;						// 선택한 카테고리 번호
	var str = new Array();				// 선택한 카테고리 이름
	var obj = document.frm['cate[]'];	// 선택한 카테고리
	var valueTemp = new Array();
	var cnt;

	goodsCountCheck = 0;
	for (i=0;i<obj.length;i++) {
		if (obj[i].value) {
			valueTemp = obj[i].value.split(',');
			str[str.length] = valueTemp[2];
			selCate = valueTemp[0];
			cnt = valueTemp[1];
		}
	}

	if (!selCate) {
		alert('카테고리를 선택해주세요');
		return;
	}

	// 중복 카테고리 체크
	for (i=0;i<cateValues.length;i++) {
		if (cateValues[i]) {
			var cateValue = cateValues[i];

			// 같은 카테고리를 선택했거나 선택되어 있는 카테고리의 하위 카테고리를 선택 했을경우
			if (selCate == cateValue) {
				alert('해당 카테고리는 이미 추가되어 있습니다.');
				return;
			}
			else if (selCate.length > 3 && selCate.substr(0,selCate.length-3) == cateValue) {
				alert('해당 카테고리보다 상위 카테고리가 이미 추가되어 있습니다.');
				return;
			}
			// 선택된 카테고리보다 상위 카테고리를 선택 했을경우
			else if (cateValue.substr(0,selCate.length) == selCate) {
				if (confirm("현재 추가된 카테고리보다 상위 카테고리를 선택하셨습니다.\n하위 카테고리를 삭제하고 상위 카테고리를 추가하시겠습니까?") == true) {
					// 하위 카테고리가 여러개 일수 있으니 찾아서 삭제
					var tempCate = cateValues.slice();	// 배열 복사
					for (j=0; j<tempCate.length; j++) {
						if (tempCate[j].substr(0,selCate.length) == selCate) {
							duplicateGoodsCount -= Number(document.getElementsByName("category_"+tempCate[j])[0].value);
							var parent = document.getElementById("selectCategory");
							var delCate = document.getElementById(tempCate[j]);
							parent.removeChild(delCate);
							cateValues.splice(cateValues.indexOf(tempCate[j]), 1);
						}
					}
					break;
				}
				else {
					return;
				}
			}
		}
	}

	// 카테고리명 (00개) 처럼 구성
	str = str.join(" > ");
	str += ' ('+comma(cnt)+'개)';

	// 노출될 상품수 계산 및 선택한 카테고리 노출
	cateValues[cateValues.length] = selCate;
	duplicateGoodsCount += Number(cnt);

	categoryText = "<span id='" + selCate + "' style='display:inline-block; background-color:d5d5d5; padding:5 5 5 5; margin-top:3px; margin-bottom:3px; margin-right:5px;'>" + str;
	categoryText += "<input type=hidden name=category[] value='" + selCate + "' style='display:none'> ";
	categoryText += "<input type=hidden name=category_" + selCate + " value='" + cnt + "' style='display:none'> ";
	categoryText += "<a href='javascript:void(0)' onClick='categoryDelete(\"" + selCate + "\",\"" + cnt + "\")'><img src='../img/i_del.gif' align=absmiddle></a></span>";

	var selected = document.getElementById('selectCategory');
	selected.innerHTML += categoryText;
}

// 선택한 카테고리 삭제
function categoryDelete(selCate,cnt) {
	goodsCountCheck = 0;
	cateValues.splice(cateValues.indexOf(selCate), 1);
	duplicateGoodsCount -= cnt;

	// 선택 상품 수 계산
	var parent = document.getElementById("selectCategory");
	var delCate = document.getElementById(selCate);
	parent.removeChild(delCate);
}

//노출될 상품수 계산
function categoryCalc() {
	if (cateValues.length > 0) {
		// 로딩 처리
		nsGodoLoadingIndicator.init({});
		nsGodoLoadingIndicator.show();
		var ajax = new Ajax.Request('../naver/naver_category_calc.php',
		{
			method: 'POST',
			parameters: 'mode=category&category='+cateValues,
			onComplete: function () {
				nsGodoLoadingIndicator.hide();	// 로딩끝
				var req = ajax.transport;
				if (req.status !== 200 || req.responseText === '' || req.responseText === 'fail') {
					alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
					return;
				}

				selectedGoodsCount = req.responseText;
				document.getElementById('goodsCount').innerHTML = comma(selectedGoodsCount);

				if (selectedGoodsCount > 499000) {
					document.getElementById('goodsCount').style.color = 'red';
					document.getElementById('goodsCount').style.fontWeight = 'bold';
					alert("선택한 상품수가 제한 개수를 넘었습니다.\n(선택된 상품 수 : "+selectedGoodsCount+" 개)\n수정하여 주시기 바랍니다.");
				}

				// 중복된 상품 개수 계산
				document.getElementById("duplicateGoodsCount").innerHTML = comma(duplicateGoodsCount - selectedGoodsCount);
				goodsCountCheck = 1;
			},
			onFailure : function() {
				nsGodoLoadingIndicator.hide();	// 로딩끝
				alert("통신을 실패하였습니다.\n고객센터에 문의하여 주세요.");
				return;
			}
		});
	}
	// 선택된 카테고리를 모두 삭제 했을시 초기화
	else {
		goodsCountCheck = 1;
		selectedGoodsCount = duplicateGoodsCount = 0;
		document.getElementById("goodsCount").innerHTML = selectedGoodsCount;
		document.getElementById("duplicateGoodsCount").innerHTML = duplicateGoodsCount;

		alert("노출 카테고리를 선택해 주세요.");
		return;
	}
}

//선택한 카테고리 전체 삭제
function deleteAll() {
	if (confirm('선택한 카테고리를 초기화 하시겠습니까?') != true) {
		return;
	}

	goodsCountCheck = 1;
	var parent = document.getElementById("selectCategory");
	while(parent.firstChild) {
		parent.removeChild(parent.firstChild);
	}
	cateValues = Array();
	selectedGoodsCount = duplicateGoodsCount = 0;
	document.getElementById("goodsCount").innerHTML = selectedGoodsCount;
	document.getElementById("duplicateGoodsCount").innerHTML = duplicateGoodsCount;
}

function overlay() {
	var left = document.getElementById("shoppingGoodsDiv").offsetleft;
	var top = document.getElementById("shoppingGoodsDiv").offsetTop;
	var width = document.getElementById("shoppingGoodsDiv").offsetWidth;
	var height = document.getElementById("shoppingGoodsDiv").offsetHeight;

	document.getElementById("overlayDiv").style.left = left+200;
	document.getElementById("overlayDiv").style.top = top+130;
	document.getElementById("overlayDiv").style.width = width
	document.getElementById("overlayDiv").style.height = height;
}

function migration() {
	if (confirm('마이그레이션은 작업 시간이 다소 소요됩니다. 계속하시겠습니까?')) {
		popupLayer('naver_shopping_migration.php',1000,800);
	}
	else {
		return false;
	}
}
</script>
<? include "../_footer.php"; ?>