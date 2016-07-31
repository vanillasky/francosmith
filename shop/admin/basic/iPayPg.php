<?php
$location = "결제관리 > 옥션 iPay PG결제 설정";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/auctionIpay.class.php";

$auctionIpayConfigPath = "../../conf/auctionIpay.cfg.php";
if(file_exists($auctionIpayConfigPath)) require $auctionIpayConfigPath;

$auctionIpayPgConfigPath = "../../conf/auctionIpay.pg.cfg.php";
if(file_exists($auctionIpayPgConfigPath)) require $auctionIpayPgConfigPath;

if(!isset($auctionIpayCfg['logoType'])) $auctionIpayCfg['logoType'] = '../admin/img/logo_ipay01.gif';
$checked['logoType'][$auctionIpayCfg['logoType']] = 'checked="checked"';

if(!isset($auctionIpayPgCfg['testYn'])) $auctionIpayPgCfg['testYn']='n';
if(!isset($auctionIpayPgCfg['useYn'])) $auctionIpayPgCfg['useYn']='n';
if(!isset($auctionIpayPgCfg['paymentrule'])) $auctionIpayPgCfg['paymentrule']=0;
$checked['testYn'][$auctionIpayPgCfg['testYn']] = 'checked="checked"';
$checked['useYn'][$auctionIpayPgCfg['useYn']] = 'checked="checked"';
$checked['paymentrule'][$auctionIpayPgCfg['paymentrule']] = 'checked="checked"';
?>
<style type="text/css">
img{
	border:none;
}
</style>
<script type="text/javascript">
function copy_txt(val)
{
	window.clipboardData.setData('Text', val);
}
</script>

<div style="width:800px;">
<form method="post" action="indb.iPayPg.php" onsubmit="return checkForm(this)" target="ifrmHidden"/>
<div class="title title_top">옥션 iPay 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">옥션 seller id</td>
	<td>
		<input type="text" name="sellerid" value="<?php echo $auctionIpayCfg['sellerid']; ?>" required msgR="옥션 seller id는 필수입니다."/>
	</td>
</tr>
<tr>
	<td height="50">인증키</td>
	<td>
		<div style="margin:0px; padding:0px;" class="small1 extext"><a href="javascript:popup('http://ipay.auction.co.kr/ipay/SellerRegister.aspx?url=<?=urlencode("http://ipaymall.godo.co.kr/auction_ipaymall_ticket.php");?>&return_url=<?php echo urlencode($_SERVER['HTTP_HOST']); ?>',493,672);"><img src="../img/btn_id_return.gif" align="absmiddle" /></a> 옥션 로그인 후 iPay 수정하기 창에서 정보수정 버튼을 눌러주시기 바랍니다.</div>
		<div><textarea name="ticket" style="width:600px; height:50px;" required msgR="인증키는 필수입니다." ><?php echo $auctionIpayCfg['ticket']; ?></textarea></div>
	</td>
</tr>
<tr>
	<td height="50">옥션 iPay 로고 삽입</td>
	<td class="noline">
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay01.gif" <?php echo $checked['logoType']['../admin/img/logo_ipay01.gif']; ?> /><img src="../../admin/img/logo_ipay01.gif" align="absmiddle" /></label>
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay02.gif" <?php echo $checked['logoType']['../admin/img/logo_ipay02.gif']; ?> /><img src="../../admin/img/logo_ipay02.gif" align="absmiddle" /></label>
		<div style="padding-top:5;">{=auctionIpayLogo()} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{auctionIpayLogo()}')" alt="복사하기" align="absmiddle"/></div>
		<div style="padding-top:10;" class="small1 extext">
			<div>복사하신 <b>치환코드</b>를 페이지에 삽입하시면 옥션 iPay 로고가 출력됩니다.</div>
		</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">옥션 iPay PG결제 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>사용여부</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y']; ?>/>사용</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n']; ?>/>사용안함</label>
	</td>
</tr>
<tr height="30">
	<td>테스트하기</td>
	<td class="noline">
	<label><input type="radio" name="testYn" value="y" <?php echo $checked['testYn']['y']; ?>/>사용</label><label><input type="radio" name="testYn" value="n" <?php echo $checked['testYn']['n']; ?> />사용안함</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>테스트를 사용에 설정하시면 관리자로 로그인한 상태에서만 결제페이지에서 옥션iPay 결제 체크박스가 표시됩니다.</div>
	</div>
	</td>
</tr>
<tr>
	<td height="50">결제수단</td>
	<td class="noline">
		<label><input type="radio" name="paymentrule" value="0" <?php echo $checked['paymentrule'][0]; ?> required msgR="결제수단은 필수입니다." />모두 가능</label>
		<label><input type="radio" name="paymentrule" value="1" <?php echo $checked['paymentrule'][1]; ?> required msgR="결제수단은 필수입니다." />카드 불가</label>
		<label><input type="radio" name="paymentrule" value="2" <?php echo $checked['paymentrule'][2]; ?> required msgR="결제수단은 필수입니다." />무통장입금 불가</label>
		<label><input type="radio" name="paymentrule" value="3" <?php echo $checked['paymentrule'][3]; ?> required msgR="결제수단은 필수입니다." />카드 휴대폰 불가</label>
		<div style="padding-top:5;" class="small1 extext">
		<div>결제금액 1000원 이하는 신용카드 결제가 불가능합니다.</div>
		</div>
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</div>

</form>
</div>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr>
<td>
	<div>반드시 옥션 iPay 심사가 완료 되어 서비스를 사용하실 수 있으실 때 사용여부를 사용으로 설정하십시요.</div>
	<div>iPay PG결제는 결제 수단에 노출되며, 옥션의 결제 수단을 그대로 사용하실 수 있습니다.</div>
	<div>
		iPay PG로 결제한 주문은 e나무 관리자와 옥션 관리자, 모두 확인이 가능하며 서비스신청은 기본설정페이지에서 신청할 수 있습니다.
		<a href="<?php echo $cfg['rootDir']; ?>/admin/basic/iPayPg.intro.php" style="margin-left: 5px; font-weight: bold;" class="blue">[iPay PG결제 서비스 신청]</button>
	</div>
</td>
</tr>
</table>
</div>

<script type="text/javascript">cssRound('MSG01');</script>
<? include "../_footer.php"; ?>