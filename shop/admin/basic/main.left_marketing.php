<?php

## ���� �ʱ�ȭ
$naver_in = $auction_about = $daum_how = $naver_keyword = $overture_keyword = $natebasket = $interpark_style = $naver_checkout = $naver_mileage = "";

### ���̹� ���ļ���
@include_once "../../conf/partner.php";

$naver_useYn = $partner['useYn'];

if($naver_useYn == 'y'){
	$naver_in  = "_on";
}else{
	$naver_in  = "";
}

### ���� �����Ͽ�
@include_once "../../conf/daumCpc.cfg.php";

$daum_how_useYn = $daumCpc['useYN'];

if($daum_how_useYn == 'Y'){
	$daum_how  = "_on";
}else{
	$daum_how  = "";
}

### ���̹� Ű���層��
list($naver_keyword_useYn) = $db->fetch("SELECT value FROM gd_env WHERE category = 'keywordad' AND name='naverad'"); // ���� ������ üũ

if($naver_keyword_useYn == 'Y'){
	$naver_keyword  = "_on";
}else{
	$naver_keyword  = "";
}

### �����߾� Ű���層��
list($overture_keyword_useYn) = $db->fetch("SELECT value FROM gd_env WHERE category = 'keywordad' AND name='overture'"); // ���� ������ üũ

if($overture_keyword_useYn == 'Y'){
	$overture_keyword  = "_on";
}else{
	$overture_keyword  = "";
}

### ����Ʈ �ٽ���
@include_once "../../conf/natebasket.php";

$natebasket_useYn = $natebasket['useYn'];

if($natebasket_useYn == 'y'){
	$natebasket  = "_on";
}else{
	$natebasket  = "";
}


### ���̹� üũ�ƿ�
@include_once "../../conf/naverCheckout.cfg.php";

$naver_checkout_useYn = $checkoutCfg['useYn'];

if($naver_checkout_useYn == 'y'){
	$naver_checkout  = "_on";
}else{
	$naver_checkout  = "";
}

### ���̹� ���ϸ���

$load_config_ncash = $config->load('ncash');

$naver_mileage_useYn = $load_config_ncash['status'];

if($naver_mileage_useYn == 'real'){
	$naver_mileage  = "_on";
}else{
	$naver_mileage  = "";
}

### ������ũ ���½�Ÿ��
@include_once "../../conf/interparkOpenStyle.php";

$interpark_style_useYn = $inpkOSCfg['use'];

if($interpark_style_useYn == 'Y'){
	$interpark_style  = "_on";
}else{
	$interpark_style  = "";
}

### ���� ��������
/*@include_once "../../conf/auctionIpay.cfg.php";

$auction_ipay_useYn = $auctionIpayCfg['useYn'];

if($auction_ipay_useYn == 'y'){
	$auction_ipay  = "_on";
}else{
	$auction_ipay  = "";
}*/


/*
������ ��� ��Ȳ ����

���̹� ���ļ���
���Ǿ�ٿ�
���������Ͽ�
���̹�Ű���層��
�����߾�Ű���層��
����Ʈ�ٽ���
���̹�üũ�ƿ�
���̹����ϸ���
������ũ���½�Ÿ��
*/
?>

<div id='market_21' style='position:relative;width:0;height:0;display:none;'>
<div style='position:absolute;filter:alpha(opacity=100);left:0;top:-100' onMouseOver="javascript:SubMenu2(21,'over')" onMouseOut="javascript:SubMenu2(21,'out')">
	<table border='0' cellpadding='3' cellspacing='2' bgcolor='#5d644a' width=200 height=65>
	<tr><td bgcolor='#f5ff9f' class=small1 style="text-align: justify;padding:5 5 5 5"><font color=444444>������Ȩ �ٷΰ���!&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://gongji.godo.co.kr/userinterface/pingAndLocation.php?url=<?php echo urlencode('/shop/admin/marketing/main.php');?>&ex=<?php echo urlencode('http://marketing.godo.co.kr');?>"><img src="http://gongji.godo.co.kr/userinterface/img/btn_sub_service_go.gif" align=absmiddle></a></td></tr>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">�����ִ� �����÷��̽�<br/>���ļ���<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">������ ��� ��� ������<br/>������ȿ��<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">���� ã�� ���<br/>Ű������ �߽�<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">���ѹα� ��ǥ���� �˻�<br/>�ֻ�� ����<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">����Ʈ+���� ���ó���!!<br/>�ʼ� ������<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">�Ʊ��� ��ģ ��!<br/>üũ�ƿ��� ã�Ƶ帳�ϴ�.<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">��𼭳� ����/��밡����<br/>����������<br/>
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
				<tr><td style="text-align:center;color:#ffffff;font:11px Dotum;padding-top:10px;">������ũ�� �����θ��� ��~<br/>
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