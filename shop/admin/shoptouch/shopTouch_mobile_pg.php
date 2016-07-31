<?
$location = "쇼핑몰 App관리 > 모바일 전자결제 설정";
include "../_header.php";
include "../../conf/config.pay.php";
include "../../conf/config.mobileShop.php";

if ($cfg['settlePg']){
	include "../../conf/pg.".$cfg['settlePg'].".php";
}

if($cfg['settlePg']=='inicis' && $pg['id']!='') list($nowPg,$ifrmSrc) = array("이니시스","../mobileShop/inicis.php");
elseif($cfg['settlePg']=='inipay' && $pg['id']!='') list($nowPg,$ifrmSrc) = array("이니시스","../mobileShop/inipay.php");
elseif($cfg['settlePg']=='lgdacom' && $pg['id']!='') list($nowPg,$ifrmSrc) = array("LG U+","../mobileShop/lgdacom.php");
elseif($cfg['settlePg']=='allat' && $pg['id']!='') list($nowPg,$ifrmSrc) = array("올앳","../mobileShop/allat.php");
elseif($cfg['settlePg']=='kcp' && $pg['id']!='') list($nowPg,$ifrmSrc) = array("KCP","../mobileShop/kcp.php");
else $ifrmSrc = '';

?>
<div class="title title_top">
모바일 전자결제 설정 안내<span>모바일결제는 이용중인(또는 계약된) 전자결제(PG) 서비스사의 모바일 결제를 이용하시게 됩니다.
</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshop&no=7')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td>이용중인 PG사</td>
	<td><b><?if($nowPg){?><?=$nowPg?><?}else{?><font class=extext>쇼핑몰기본관리 &gt; 통합전자결제설정에서 전자결제정보를 설정해주세요.</font> <a href="../basic/pg.php"><font class=extext_l>[통합전자결제설정]</font></a><?}?>
	</b></td>
</tr>
</table>

<?php if($ifrmSrc){?>
<div style="padding-top: 20px"></div>
<table width="100%" cellpadding=0 cellspacing=0 border=0>
<tr>
	<td>
	<iframe id="pgifrm" src="<?php echo $ifrmSrc;?>" width="100%" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="10" scrolling="no"></iframe>
	</td>
</tr>
</table>
<?php }?>

<? include "../_footer.php"; ?>