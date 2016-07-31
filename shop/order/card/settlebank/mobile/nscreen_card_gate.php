<?
### 세틀뱅크
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.settlebank.php";
@include dirname(__FILE__)."/../../../../conf/conf/pg.escrow.php";

$page_type = $_GET['page_type'];

	// 상품 정보
	if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
		$item = $cart -> item;
	}
	foreach($item as $v){
		$i++;
		if($i == 1) $ordnm = $v['goodsnm'];
	}
	//상품명에 특수문자 및 태그 제거
	$ordnm	= pg_text_replace(strip_tags($ordnm));
	if($i > 1)$ordnm .= " 외".($i-1)."건";

//ssl 보안서버 관련 추가
if($_SERVER['SERVER_PORT'] == 80) {
	$Port = "";
} elseif($_SERVER['SERVER_PORT'] == 443) {
	$Port = "";
} else {
	$Port = $_SERVER['SERVER_PORT'];
}

if (strlen($Port)>0) $Port = ":".$Port;

$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';
$host = parse_url($_SERVER['HTTP_HOST']);

if ($host['path']) {
	$Host = $host['path'];
} else {
	$Host = $host['host'];
}

?>
<script>
webbrowser=navigator.appVersion;

var isIPHONE = (navigator.userAgent.match('iPhone') != null ||
		navigator.userAgent.match('iPod') != null);
var isIPAD = (navigator.userAgent.match('iPad') != null);
var isANDROID = (navigator.userAgent.match('Android') != null);

function submitForm()
{
	window.name = "STPG_CLIENT";
	var settle_payinfo = document.SETTLE_PAYINFO;

	<?if ($_POST[settlekind] == 'c') { //카드?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/card/MbCardAction.do";
	<?}else if ($_POST[settlekind] == 'o') { //계좌이체?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/bank/MbBankAction.do";
	<?}else if ($_POST[settlekind] == 'h') { //휴대폰?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/mobile/MbMobileAction.do";
	<?}else if ($_POST[settlekind] == 'v') { //가상계좌?>
		settle_payinfo.action = "https://pg.settlebank.co.kr/vbank/MbVBankAction.do?equ_gb=MB";
	<?}else {?>
		alert("결제 수단이 선택되지 않았습니다.");
	<?}?>

	strEncode();//한글인코딩

	settle_payinfo.submit();
}

//파라미터 값이 한글인 경우 여기서 인코딩을 해준다.
function strEncode()
{
	var settle_payinfo = document.SETTLE_PAYINFO;
	settle_payinfo.PGoods.value = encodeURI(settle_payinfo.t_PGoods.value);
//	settle_payinfo.PNoti.value = encodeURI(settle_payinfo.t_PNoti.value);
	settle_payinfo.PMname.value = encodeURI(settle_payinfo.t_PMname.value);
	settle_payinfo.PUname.value = encodeURI(settle_payinfo.t_PUname.value);

	<?if ($_POST[settlekind] == 'v' || $_POST[settlekind] == 'o') {?>
		settle_payinfo.PBname.value = encodeURI(settle_payinfo.t_PBname.value);
	<?}?>

}
</script>

<form id="SETTLE_PAYINFO" name="SETTLE_PAYINFO" method="POST">
<!-- 결과처리를 위한 파라미터 -->
<input type="hidden" name="PNoteUrl" value="<?=$Protocol.$Host.$Port?><?=$cfg['rootDir']?>/order/card/settlebank/mobile/nscreen_card_return.php?page_type=<?=$page_type?>"><!--결제성공시 노티전송받을 url -->
<input type="hidden" name="PNextPUrl" value="<?=$Protocol.$Host.$Port?><?=$cfg['rootDir']?>/order/card/settlebank/mobile/nscreen_pay_rcv.php?page_type=<?=$page_type?>"><!--결제성공/오류시 호출될 url-->
<input type="hidden" name="PCancPUrl" value="" ><!--결제취소시 호출될 url-->
<input type="hidden" name="PMid" value="<?=$pg['id']?>"><br> <!--<?=$pg['id']?>가맹점id(필수)-->
<input type="hidden" name="PAmt" value="<?=$_POST['settleprice']?>"><br><!--금액(필수)-->
<input type="hidden" name="PPhone" value="<?if (implode('-',$_POST['mobileOrder'])){echo implode('-',$_POST['mobileOrder']); }else{ echo implode('-',$_POST['phoneOrder']); }?>"><br><!--휴대폰번호(필수)-->
<input type="hidden" name="PMobile" value=""><!--통신사 skt,kt,lgt (필수)-->
<input type="hidden" name="POid" value="<?=$_POST['ordno']?>"><!--주문번호(필수)-->
<input type="hidden" name="PEname" value="<?if ($_POST['shopEng']){ echo $_POST['shopEng'];}else{echo "SETTLEBANK";}?>"><!--가맹점영문명(필수)-->
<input type="hidden" name="PVtransDt" value="<?=date('Ymd', strtotime('+5 day'))?>"><!--가상계좌입금대기유효기간(가상계좌채번시 필수)-->
<input type="hidden" name="PEmail" value="<?=$_POST['email']?>"><!--고객 email-->
<input type="hidden" name="t_PUname" value="<?=$_POST['nameOrder']?>"> <!-- 결제자 이름-->
<input type="hidden" name="t_PGoods" value="<?=$ordnm?>"> <!-- 상품명 -->
<input type="hidden" name="t_PMname" value="<?=$cfg['compName']?>"> <!-- 회원사 한글명 -->
<input type="hidden" name="t_PBname" value="<?if ($cfg['compName']){ echo $cfg['compName'];}else{echo "SETTLEBANK";}?>"> <!--가맹점명(계좌이체사용시필수)-->


<input type="hidden" name="PGoods">
<input type="hidden" name="PNoti">
<input type="hidden" name="PMname">
<input type="hidden" name="PUname">
<input type="hidden" name="PBname">
</form>