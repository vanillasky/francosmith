<?
	// from card_check.php
	if( $AbleBack ) $btn_error = "���� ��õ�";
	else		$btn_error = "�� ��";

	/*
	 * Get CIURL
	 */
	$URL = GetCIURL( $IsUseCI,$CIURL );

	/*
	 * Get BgColor
	 */
	$BgColor = GetBgColor( $BgColor );
	
	// ��ư Ŭ���� �̵��� ���
	if ($isMobile && !$isPc) {
		$BackURL = $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php?ordno='.$ordno;
	}
	else {
		$BackURL = $shopConfig['rootDir'].'/order/order_fail.php?ordno='.$ordno;
	}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, target-densitydpi=medium-dpi;" />
<link href="./css/mStyle.css" type="text/css" rel="stylesheet"  media="screen" />
<script language="javascript" src="./js/jquery-latest.js" type="text/javascript"></script>
<script language="javascript" src="./js/jquery.mobile-1.2.0.js" type="text/javascript"></script>
<script language="JavaScript" src="./js/Common.js" type="text/javascript"></script>
<script language="javascript">
function orderFail()
{
	location.replace('<?php echo $BackURL ?>');
}
//<![CDATA[
// Run the script on DOM ready:
$(document).ready(function(){
	OrtChange();
});
//]]>
</script>
</head>
<!-- ���θ���϶� horizontal �߰� -->
<body class="">
	<!-- ������ type01 ~ type10 ������ -->
	<div class="wrap type<?=$BgColor?>">
		<div class="header">
			<p class="tit">���� ����</p>
			<a href="JavaScript:orderFail();" class="closeBtn"><img src="./images/btn_close.png" width="37" alt="�ݱ�" /></a>
		</div>
		<div class="content">
			<div class="error">
				<dl class="info">
					<dt>���� ����(<?=$Result?>)</dt>
					<dd><?=str_replace(".","<br>",$ErrMsg)?></dd>
				</dl>
				<p class="customer">���� ��ȭ���ɽð� : <br />
				���� : 9�� ~ 19��<br />
				<span>�����, �Ͽ���, ������ �޹�</span></p>
			</div>
			
			<p class="btn st02">
				<a href="JavaScript:orderFail();" class="on"><?=$btn_error?></a>
			</p>
			<div class="cs">
				<p class="text">�ٳ� ������ : 1566-3355</p>
				<span class="logo"><img src="<?=$URL?>" width="77" alt="�������ΰ�" /></span>
			</div>
		</div>
	</div>
</body>
</html>
