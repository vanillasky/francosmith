<?

$location = "기본관리 > 계정용량 서비스신청";
include "../_header.php";

$rest_price = ($disk_price[$godo[diskGoods]][0] / 365) * betweenDate(date('Ymd'),$godo[diskEdate]);

### 계정사용 체크
list( $disk_errno, $disk_msg ) = disk();
if ( $disk_errno == '001' ) $disk_msg = "계정용량이 부족합니다.<br>아래에서 서비스변경 신청을 해주시기 바랍니다.";
else if ( $disk_errno == '002' ) $disk_msg = "계정용량 서비스 기간이 만료되었습니다.<br>아래에서 서비스기간 연장 또는 서비스변경 신청을 해주시기 바랍니다.";

$usedisk = getDu('disk');
$useper = round($usedisk / (($godo[maxDisk]+$godo[diskGoods])*1048576) * 100);
?>

<script>

function popupPay( idx )
{
	var fm = document.frmSms;
	fm.idx.value = idx;
	window.open("","popupPay","width=500,height=450");
	fm.action = "http://www.godo.co.kr/userinterface/_godoConn/vaspay.php";
	fm.target = "popupPay";
	fm.submit();
}

</script>

<div class="title title_top">디스크 용량 서비스<span>쇼핑몰 운영에 필요한 파일이 저장되는 공각을 제공합니다.</span></div>



<table width="780" border="1" bordercolor="#cccccc" style="border-collapse:collapse" cellpadding="0" cellspacing="0">
	<col bgcolor="#e3e3e3" align="right" style="padding:5px 10px 5px 0px" />
	<col style="padding:5px 10px 5px 5px" />
	<?if($godo[diskEdate]+0){?>
	<tr>
		<td>사용 기간</td>
		<td>
			<div style="float:left;padding:5px 0px 0px 0px"><?=sprintf("%04d년 %02d월 %02d일",substr($godo[diskEdate],0,4),substr($godo[diskEdate],4,2),substr($godo[diskEdate],6,2))?> (<?=betweenDate(date('Ymd'),$godo[diskEdate])?>일 남음)</div>
			<div style="float:right"><a href="http://www.godo.co.kr/mygodo/rental_extension.php?pageKey=<?=$godo[sno]?>" target="_blank"><img src="http://gongji.godo.co.kr/userinterface/diskAdd/img/btn_disc01.gif"></a></div>
		</td>
	</tr>
	<?}?>
	<tr>
		<td>디스크 총용량</td>
		<td>
			<div style="float:left;padding:5px 0px 0px 0px"><?=byte2str(mb2byte($godo[maxDisk]+$godo[diskGoods]))?> ( 기본 <?=$godo[maxDisk]?>M<?if($godo[diskGoods]){?> + 추가 <?=byte2str(mb2byte($godo[diskGoods]))?><?}?> )</div>
			<div style="float:right"><a href="http://www.godo.co.kr/mygodo/plusDisk_rental.php?pageKey=<?=$godo[sno]?>" target="_blank"><img src="http://gongji.godo.co.kr/userinterface/diskAdd/img/btn_disc02.gif"></a></div>
			<? if(preg_match( "/^rental/i", $godo['ecCode']) && $godo['maxDisk'] < 1024){ // 임대형 기본용량 1G 미만 ?>
			<div style="clear:both">
				<span style="color:#4F81BD;">"e나무 시즌3" 출시와 함께 용량증설을 신청하신 분들에게 기본용량을 1G로 증설하여 드립니다.</span>
				<a href="../proc/indb.php?mode=eduExtend"><img src="../img/btn_edu_extend.gif" align="absMiddle"></a>
			</div>
			<? } ?>
		</td>
	</tr>
	<tr>
		<td>디스크 사용량</td>
		<td>
			<div style="padding:0px 0px 5px 0px"><?=byte2str(getDu('disk'))?> (<?=$useper?>%)</div>
			<div style="background-image:url(http://gongji.godo.co.kr/userinterface/diskAdd/img/disc_01.gif);width:390;height:27;">
				<div style="margin:8px 15px 0px 15px;background-color:#da3a8f;width:<?=$useper?>%;height:1px;font:0"></div>
			</div>
		</td>
	</tr>
</table>

<div id="diskAddInfo"><script>panel('diskAddInfo', 'basic');</script></div>

<? include "../_footer.php"; ?>