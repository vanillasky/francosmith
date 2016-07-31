<?

$location = "전자세금계산서 관리 > 전자세금계산서 매니저 접속";
include "../_header.php";

$compSerial	= $cfg[compSerial];	# 사업자번호
?>

<div class="title title_top">전자세금계산서 매니저 접속<span>전자세금계산서를 지원하는 LG데이콤 웹택스21에서 세금계산서의 수신된 내역을 조회할 수 있습니다.</span></div>

<? if ( $compSerial == '' ){ ?>
<div style="color:red;">쇼핑몰기본관리에서 사업자번호를 입력하신 후 전자세금계산서(WebTax21)에 가입하셔야 본 서비스를 이용하실 수 있습니다.</div>
<? } else { ?>
<iframe name="ifrmWebTax21" src="http://www.webtax21.com/webtax21/webtax?func=login&from=remote_from&saup=<?=$compSerial?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>
<? } ?>


<? include "../_footer.php"; ?>