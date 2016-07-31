<?

$location = "자동입금확인 서비스 > 자동입금확인 서비스 신청";
include "../_header.php";

$MID		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디

$ceoName	= $cfg[ceoName];	# 대표자명
$resDomain	= urlencode("{$_SERVER[HTTP_HOST]}" . str_replace("admin/order/bankda.php", "", $_SERVER[PHP_SELF]) . "lib/bank.sock.php"); # 자동입금확인 처리결과 수신 URL
?>

<div class="title title_top">자동입금확인 서비스 신청<span>거래은행계좌들의 입출금내역을 통합적으로 조회관리할 수 있는 서비스입니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=10')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<div style="padding-left:23">자동입금확인 서비스는 (주)뱅크다에서 제공하는 서비스이며, 서비스 가입은 (주)뱅크다 회원으로 가입됨을 알려드립니다.</div>


<? if ( $ceoName == '' ){ ?>
<div style="color:red;">쇼핑몰기본관리에서 대표자명을 입력하셔야 본 서비스를 이용하실 수 있습니다.</div>
<? } else { ?>
<iframe name="ifrmBankda" src="http://bankda.godomall.co.kr/index.asp?Upid=<?=$MID?>&Upname=<?=$ceoName?>&Updomain=<?=$resDomain?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>
<? } ?>


<? include "../_footer.php"; ?>