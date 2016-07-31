<?php

## 변수 초기화
$naver_in = $auction_about = $daum_how = $naver_keyword = $overture_keyword = $natebasket = $interpark_style = $naver_checkout = $naver_mileage = "";

### 네이버 지식쇼핑
@include_once "../../conf/partner.php";

$naver_useYn = $partner['useYn'];

if($naver_useYn == 'y'){
	$naver_in  = "_on";
}else{
	$naver_in  = "";
}

### 다음 쇼핑하우
@include_once "../../conf/daumCpc.cfg.php";

$daum_how_useYn = $daumCpc['useYN'];

if($daum_how_useYn == 'Y'){
	$daum_how  = "_on";
}else{
	$daum_how  = "";
}

### 네이버 키워드광고
list($naver_keyword_useYn) = $db->fetch("SELECT value FROM gd_env WHERE category = 'keywordad' AND name='naverad'"); // 기존 데이터 체크

if($naver_keyword_useYn == 'Y'){
	$naver_keyword  = "_on";
}else{
	$naver_keyword  = "";
}

### 오버추어 키워드광고
list($overture_keyword_useYn) = $db->fetch("SELECT value FROM gd_env WHERE category = 'keywordad' AND name='overture'"); // 기존 데이터 체크

if($overture_keyword_useYn == 'Y'){
	$overture_keyword  = "_on";
}else{
	$overture_keyword  = "";
}

### 네이트 바스켓
@include_once "../../conf/natebasket.php";

$natebasket_useYn = $natebasket['useYn'];

if($natebasket_useYn == 'y'){
	$natebasket  = "_on";
}else{
	$natebasket  = "";
}


### 네이버 체크아웃
@include_once "../../conf/naverCheckout.cfg.php";

$naver_checkout_useYn = $checkoutCfg['useYn'];

if($naver_checkout_useYn == 'y'){
	$naver_checkout  = "_on";
}else{
	$naver_checkout  = "";
}

### 네이버 마일리지

$load_config_ncash = $config->load('ncash');

$naver_mileage_useYn = $load_config_ncash['status'];

if($naver_mileage_useYn == 'real'){
	$naver_mileage  = "_on";
}else{
	$naver_mileage  = "";
}

### 인터파크 오픈스타일
@include_once "../../conf/interparkOpenStyle.php";

$interpark_style_useYn = $inpkOSCfg['use'];

if($interpark_style_useYn == 'Y'){
	$interpark_style  = "_on";
}else{
	$interpark_style  = "";
}

### 옥션 아이페이
/*@include_once "../../conf/auctionIpay.cfg.php";

$auction_ipay_useYn = $auctionIpayCfg['useYn'];

if($auction_ipay_useYn == 'y'){
	$auction_ipay  = "_on";
}else{
	$auction_ipay  = "";
}*/


/*
마케팅 사용 현황 순서

네이버 지식쇼핑
옥션어바웃
다음쇼핑하우
네이버키워드광고
오버추어키워드광고
네이트바스켓
네이버체크아웃
네이버마일리지
인터파크오픈스타일
*/
?>

<div id='market_21' style='position:relative;width:0;height:0;display:none;'>
<div style='position:absolute;filter:alpha(opacity=100);left:0;top:-100' onMouseOver="javascript:SubMenu2(21,'over')" onMouseOut="javascript:SubMenu2(21,'out')">
	<table border='0' cellpadding='3' cellspacing='2' bgcolor='#5d644a' width=200 height=65>
	<tr><td bgcolor='#f5ff9f' class=small1 style="text-align: justify;padding:5 5 5 5"><font color=444444>마케팅홈 바로가기!&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://gongji.godo.co.kr/userinterface/pingAndLocation.php?url=<?php echo urlencode('/shop/admin/marketing/main.php');?>&ex=<?php echo urlencode('http://marketing.godo.co.kr');?>"><img src="http://gongji.godo.co.kr/userinterface/img/btn_sub_service_go.gif" align=absmiddle></a></td></tr>
	</table>
</div>
</div>

<script>
function SubMenu2(mode,type) {
	var dnm = 'market_' + mode;
	var div = document.getElementById( dnm );
	if(type=='open'){
		div.style.display = "block";
	}else if (type == 'over') {
		div.style.display = "block";
	}else if (type == 'out') {
		div.style.display = "none";
	}
}
</script>

<div style="background-image:url(../img/t_marketing_topbg.gif);width:190px;height:24px;display:table;">
<div style="padding-left:28px;font:11px Dotum;color:#ffffff;display:table-cell;"><?php echo substr($cfg['shopName'],0,12);?> <img src="../img/t_marketing_tit.gif" style='vertical-align:middle;'></div>
</div>
<div class="main-basic-left-marketing">
<table cellpadding=0 cellspacing=0 align="center">
	<tr>
		<td style="background:url('../img/m_naver_in<?php echo $naver_in;?>.gif') no-repeat; width:55px; height:72px;" onMouseOver="javascript:SubMenu2(1,'open')" onMouseOut="javascript:SubMenu2(1,'out')"><div class="best"></div>
			<div id='market_1' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(1,'over')" onMouseOut="javascript:SubMenu2(1,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">국내최대 마켓플레이스<br/>지식쇼핑<br/>
				<a href="../naver/naver_pass.php" target="_blank"><? if($naver_in != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>

		<td style="background:url('../img/m_daum_how<?php echo $daum_how;?>.gif') no-repeat; width:50px; height:72px;" onMouseOver="javascript:SubMenu2(3,'open')" onMouseOut="javascript:SubMenu2(3,'out')"><div class="best"></div>
			<div id='market_3' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(3,'over')" onMouseOut="javascript:SubMenu2(3,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">저럼한 비용 대비 폭발적<br/>매출상승효과<br/>
				<a href="../daumcpc/info.php" target="_blank"><? if($daum_how != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="background:url('../img/m_naver_keyword<?php echo $naver_keyword;?>.gif') no-repeat; width:55px; height:72px;" onMouseOver="javascript:SubMenu2(4,'open')" onMouseOut="javascript:SubMenu2(4,'out')"><div class="non"></div>
			<div id='market_4' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(4,'over')" onMouseOut="javascript:SubMenu2(4,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">고객이 찾는 모든<br/>키워드의 중심<br/>
				<a href="../keyword/register.php" target="_blank"><? if($naver_keyword != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
		<td style="background:url('../img/m_overture_keyword<?php echo $overture_keyword;?>.gif') no-repeat; width:55px; height:72px;" onMouseOver="javascript:SubMenu2(5,'open')" onMouseOut="javascript:SubMenu2(5,'out')"><div class="non"></div>
			<div id='market_5' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(5,'over')" onMouseOut="javascript:SubMenu2(5,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">대한민국 대표포털 검색<br/>최상단 노출<br/>
				<a href="../keyword/register.php" target="_blank"><? if($overture_keyword != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
		<td style="background:url('../img/m_nate_basket<?php echo $natebasket;?>.gif') no-repeat; width:50px; height:72px;" onMouseOver="javascript:SubMenu2(6,'open')" onMouseOut="javascript:SubMenu2(6,'out')"><div class="new"></div>
			<div id='market_6' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(6,'over')" onMouseOut="javascript:SubMenu2(6,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">네이트+야후 동시노출!!<br/>필수 마케팅<br/>
				<a href="../natebasket/natebasket.php" target="_blank"><? if($natebasket != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td style="background:url('../img/m_naver_checkout<?php echo $naver_checkout;?>.gif') no-repeat; width:55px; height:72px;" onMouseOver="javascript:SubMenu2(7,'open')" onMouseOut="javascript:SubMenu2(7,'out')"><div class="hot"></div>
			<div id='market_7' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(7,'over')" onMouseOut="javascript:SubMenu2(7,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">아깝게 놓친 고객!<br/>체크아웃이 찾아드립니다.<br/>
				<a href="../naverCheckout/info.php" target="_blank"><? if($naver_checkout != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
		<td style="background:url('../img/m_naver_mileage<?php echo $naver_mileage;?>.gif') no-repeat; width:55px; height:72px;" onMouseOver="javascript:SubMenu2(8,'open')" onMouseOut="javascript:SubMenu2(8,'out')"><div class="new"></div>
			<div id='market_8' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(8,'over')" onMouseOut="javascript:SubMenu2(8,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">어디서나 적립/사용가능한<br/>통합적립금<br/>
				<a href="../naverNcash/index.php" target="_blank"><? if($naver_mileage != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
		<td style="background:url('../img/m_interpark_style<?php echo $interpark_style;?>.gif') no-repeat; width:50px; height:72px;" onMouseOver="javascript:SubMenu2(9,'open')" onMouseOut="javascript:SubMenu2(9,'out')"><div class="non"></div>
			<div id='market_9' style='position:relative;width:0;height:0;display:none;'>
			<div style='position:absolute;filter:alpha(opacity=100);left:0;' onMouseOver="javascript:SubMenu2(9,'over')" onMouseOut="javascript:SubMenu2(9,'out')">
				<table border='0' cellpadding='0' cellspacing='0' width=160 height=62 background="../img/over_layerbox.gif">
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">인터파크에 내쇼핑몰을 쏙~<br/>
				<a href="../interpark/intro.php" target="_blank"><? if($interpark_style != "_on"){?><img src="../img/btn_ad_apply.gif"><?}else{?><img src="../img/marketing_ading.gif"><?}?></a>
				</td></tr>
				</table>
			</div>
			</div>
		</td>
	</tr>
</table>
</div>
<div style="background-image:url(../img/t_marketing_bottom.gif);width:190px;height:48px;"></div>
<? unset($service); ?>