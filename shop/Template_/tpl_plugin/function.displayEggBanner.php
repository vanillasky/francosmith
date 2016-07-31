<?php

/* Return EggBanner Function */

function displayEggBanner($mode=0)
{
	global $cfg,$egg,$pg,$escrow;

	if(!$cfg) @include dirname(__FILE__)."/../../conf/config.php";
	if(!$egg) @include dirname(__FILE__)."/../../conf/egg.usafe.php";
	if(!$escrow) @include dirname(__FILE__)."/../../conf/pg.escrow.php";
	if((!is_array($pg) && $cfg[settlePg]) || is_object($pg)) {
		$pg = null;
		@include dirname(__FILE__)."/../../conf/pg.".$cfg[settlePg].".php";
	}
	$compSerial = str_replace('-','',$cfg['compSerial']);

	$tags[0][usafe] = "<script>function usafe(){var win=window.open('http://www.usafe.co.kr/usafeShopCheck.asp?com_no=".$compSerial."','', 'width=500, height=370, scrollbars=no, location=yes,status=yes,left=0, top=0');}</script><a href=\"javascript:usafe()\"><img src='../skin/".$cfg['tplSkin']."/img/banner/usafe.gif' border=0></a>";

	if ($escrow[type] == 'INI') {
		$tags[0][inicis] = stripslashes(html_entity_decode($escrow['eggDisplayLogo'], ENT_QUOTES));
	} else {
		$tags[0][inicis] = "<a href='http://www.hanaescrow.com/hnbecc/serviceJoinSrchManagement/escser003l.jsp?shopBsnNo=".$compSerial."&mid=".$escrow[id]."' target=_blank><img src='../skin/".$cfg['tplSkin']."/img/banner/inicis.gif' border=0></a>";
	}

	$tags[0][inipay] = stripslashes(html_entity_decode($escrow['eggDisplayLogo'], ENT_QUOTES));

	$tags[0][dacom] = "<script>function dacom(){var win=window.open('https://pgweb.dacom.net/pg/wmp/mertadmin/jsp/mertservice/s_escrowYn.jsp?mertid=".$pg[id]."','check','width=339, height=263, scrollbars=no, left = 200, top = 50');}</script><a href=\"javascript:dacom()\"><img src='../skin/".$cfg['tplSkin']."/img/banner/dacom.gif' border=0></a>";

	$tags[0][allat] = "<script>function f_escrowAllat(){var win=window.open('https://www.allatpay.com/servlet/AllatBiz/svcinfo/si_escrowview.jsp?menu_id=idS16&shop_id=".$pg[id]."&business_no=".$compSerial."','allat_escrow','top=0,left=0,width=980,height=600,scrollbars,menubar=no,resizable,status,location=yes,toolbar=yes');}</script><a href=\"javascript:f_escrowAllat()\"><img src='../skin/".$cfg['tplSkin']."/img/banner/allat.gif' border=0></a>";

	$tags[0][allatbasic] = "<script>function f_escrowAllat(){var win=window.open('https://www.allatpay.com/servlet/AllatBiz/svcinfo/si_escrowview.jsp?menu_id=idS16&shop_id=".$pg[id]."&business_no=".$compSerial."','allat_escrow','top=0,left=0,width=980,height=600,scrollbars,menubar=no,resizable,status,location=yes,toolbar=yes');}</script><a href=\"javascript:f_escrowAllat()\"><img src='../skin/".$cfg['tplSkin']."/img/banner/allat.gif' border=0></a>";

	$tags[0][kcp] = "<script language=\"JavaScript\">function go_check(){var status  = \"width=500 height=450 menubar=no,scrollbars=no,resizable=no,status=no\"; var obj = window.open('', 'kcp_pop', status);document.shop_check.method = \"post\";document.shop_check.target = \"kcp_pop\";document.shop_check.action = \"http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp\";document.shop_check.submit();}</script><form name=\"shop_check\" method=\"post\" action=\"http://admin.kcp.co.kr/Modules/escrow/kcp_pop.jsp\"><input type=\"hidden\" name=\"site_cd\" value=\"".$pg[id]."\"><a href='javascript:go_check()'><img src='../skin/".$cfg['tplSkin']."/img/banner/kcp.gif' border=0></a></form>";

	$tags[0][agspay] = "<a href=\"http://www.allthegate.com/hyosung/support/escrow.jsp\"><img src='../skin/".$cfg['tplSkin']."/img/banner/agspay.gif' border=0></a>";
	$tags[0][easypay] = "<a href='http://www.easypay.co.kr/escrow/escrow_memb_auth.jsp?memb_id=".$pg[id]."' target=\"_blank\"><img src='../skin/".$cfg['tplSkin']."/img/banner/easypay.gif' border=0></a>";
	$tags[1][usafe]  = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=130 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>쇼핑몰보증보험 구매안전서비스</b></span>를</div>
							<div style='height:20px;padding-left:10px;'>이용하실 수 있습니다.</div>
							<div style='height:20px;'>* 보상대상 : <span class='red'><b>미배송,반품/환불거부, 쇼핑몰부도</b></span></div>
							<div style='height:20px;'>* 구매안전서비스를 통하여 주문하시고 <span class='red'><b>서울보증보험에서 발행하는 보험계약 체결내역서를 반드시 확인</b></span></div>
							<div style='height:20px;padding-left:10px;'>하시기 바랍니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	if ($escrow[type] == 'INI') {
		$tags[1][inicis] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>이니시스의 구매안전(에스크로)</b></span></div>
							<div style='height:20;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	} else {
		$tags[1][inicis] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>하나은행의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	}

	$tags[1][inipay] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>이니시스의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";

	$tags[1][dacom] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위해 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>LG데이콤의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	$tags[1][allat] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>올엣의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	$tags[1][allatbasic] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>올엣의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	$tags[1][kcp] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>KCP의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	$tags[1][agspay] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>올더게이트의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	$tags[1][easypay] = "<table  width=100% style='border:1px solid #DEDEDE' cellpadding=0 cellspacing=0>
						<tr><td style='padding-left:25px;padding-top:10px;' height=70 valign=middle>
							<div style='height:20px;'>* 고객님은 안전거래를 위하여 현금 등으로 결제시 저희 쇼핑몰에서 가입한 <span class='red'><b>이지페이의 구매안전(에스크로)</b></span></div>
							<div style='height:20px;padding-left:10px;'><span class='red'><b>서비스</b></span>를 이용하실 수 있습니다.</div>
						</td></tr>
						</table><div style='font-size:0;height:5px'></div>";
	if($egg['use'] == 'Y'){
		$img = $tags[$mode][usafe];
	}else if($escrow['use'] == 'Y'){
		$img = $tags[$mode][$cfg[settlePg]];
		if($cfg['settlePg']=='lgdacom')$img = $tags[$mode]['dacom'];
	}


	//return $tags[0][usafe];
	if(!$img && !$mode)	$img = "";
	if( $cfg[displayEgg] == 1 || ($cfg[displayEgg] == 0 && ( preg_match('/index.php/',$_SERVER[PHP_SELF]) || preg_match('/order.php/',$_SERVER[PHP_SELF]) )) ){
		return $img;
	}else{
		return '';
	}
}
?>