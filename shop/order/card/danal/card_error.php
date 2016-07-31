<?	
	// from card_check.php
	$btn_error = "btn_cancel_error.gif";

	if( $AbleBack )
	{
		$btn_error = "btn_retry.gif";
	}

	/*
	 * Get CIURL
	 */
	$URL = GetCIURL( $IsUseCI,$CIURL );

	/*
	 * Get BgColor
	 */
	$BgColor = GetBgColor( $BgColor );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ko" lang="ko">
<head>
<title>�ٳ� �޴��� ����</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<link href="./css/style.css" type="text/css" rel="stylesheet"  media="screen" />
<script language="JavaScript" src="./js/Common.js"></script>
<script language="JavaScript">
function orderFail()
{
	opener.parent.location.href = "<?php echo $shopConfig['rootDir'] ?>/order/order_fail.php?ordno=<?php echo $ordno ?>";
	window.close();
}
</script>
</head>
<body>
	<!-- popup size 500x680 -->
	<div class="paymentPop cType<?=$BgColor?>">
		<p class="tit">
			<img src="./images/img_tit.gif" width="494" height="48" alt="�ٳ��޴�������" />
			<span class="logo"><img src="<?=$URL?>" width="119" height="47" alt="" /></span>
		</p>
		<div class="tabArea">
			<ul class="tab">
				<li class="tab01">�������񽺿���</li>
			</ul>
			<p class="btnSet">
				<a href="JavaScript:OpenHelp();"><img src="./images/btn_useInfo.gif" width="55" height="20" alt="�̿�ȳ�" /></a>
				<a href="JavaScript:OpenCallCenter();"><img src="./images/btn_customer.gif" width="55" height="20" alt="������" /></a>
			</p>
		</div>
		<div class="content">
			<div class="alertBox">
				<p class="type01"><strong>���� ����(<?=$Result?>)</strong><br/><?=$ErrMsg?></p>
			</div>
			<div class="infoText">
				<p class="t02">�ٳ� ������ : <strong>1566-3355</strong> (��������)</p>
			</div> 
			<div class="grayBox" style="margin-top:11px;">
				<p class="type02">���� ��ȭ���ɽð� : <br/>
				���� : 9�� ~ 19��<br/>
				<strong>�����, �Ͽ���, ������ �޹�</strong></p>
			</div>
			<p class="btnRetry"><a href="JavaScript:orderFail();"><img src="./images/<?=$btn_error?>" width="110" height="32" alt="���� ��õ�" /></a></p>
		</div>
		<div class="footer">
			<dl class="noti">
				<dt>��������</dt>
				<dd>�ٳ� �޴��� ������ �̿����ּż� �����մϴ�.</dd>
			</dl>
		</div>
	</div>
</body>
</html>