<?

$location = "�⺻���� > �����뷮 ���񽺽�û";
include "../_header.php";

$rest_price = ($disk_price[$godo[diskGoods]][0] / 365) * betweenDate(date('Ymd'),$godo[diskEdate]);

### ������� üũ
list( $disk_errno, $disk_msg ) = disk();
if ( $disk_errno == '001' ) $disk_msg = "�����뷮�� �����մϴ�.<br>�Ʒ����� ���񽺺��� ��û�� ���ֽñ� �ٶ��ϴ�.";
else if ( $disk_errno == '002' ) $disk_msg = "�����뷮 ���� �Ⱓ�� ����Ǿ����ϴ�.<br>�Ʒ����� ���񽺱Ⱓ ���� �Ǵ� ���񽺺��� ��û�� ���ֽñ� �ٶ��ϴ�.";

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

<div class="title title_top">��ũ �뷮 ����<span>���θ� ��� �ʿ��� ������ ����Ǵ� ������ �����մϴ�.</span></div>



<table width="780" border="1" bordercolor="#cccccc" style="border-collapse:collapse" cellpadding="0" cellspacing="0">
	<col bgcolor="#e3e3e3" align="right" style="padding:5px 10px 5px 0px" />
	<col style="padding:5px 10px 5px 5px" />
	<?if($godo[diskEdate]+0){?>
	<tr>
		<td>��� �Ⱓ</td>
		<td>
			<div style="float:left;padding:5px 0px 0px 0px"><?=sprintf("%04d�� %02d�� %02d��",substr($godo[diskEdate],0,4),substr($godo[diskEdate],4,2),substr($godo[diskEdate],6,2))?> (<?=betweenDate(date('Ymd'),$godo[diskEdate])?>�� ����)</div>
			<div style="float:right"><a href="http://www.godo.co.kr/mygodo/rental_extension.php?pageKey=<?=$godo[sno]?>" target="_blank"><img src="http://gongji.godo.co.kr/userinterface/diskAdd/img/btn_disc01.gif"></a></div>
		</td>
	</tr>
	<?}?>
	<tr>
		<td>��ũ �ѿ뷮</td>
		<td>
			<div style="float:left;padding:5px 0px 0px 0px"><?=byte2str(mb2byte($godo[maxDisk]+$godo[diskGoods]))?> ( �⺻ <?=$godo[maxDisk]?>M<?if($godo[diskGoods]){?> + �߰� <?=byte2str(mb2byte($godo[diskGoods]))?><?}?> )</div>
			<div style="float:right"><a href="http://www.godo.co.kr/mygodo/plusDisk_rental.php?pageKey=<?=$godo[sno]?>" target="_blank"><img src="http://gongji.godo.co.kr/userinterface/diskAdd/img/btn_disc02.gif"></a></div>
			<? if(preg_match( "/^rental/i", $godo['ecCode']) && $godo['maxDisk'] < 1024){ // �Ӵ��� �⺻�뷮 1G �̸� ?>
			<div style="clear:both">
				<span style="color:#4F81BD;">"e���� ����3" ��ÿ� �Բ� �뷮������ ��û�Ͻ� �е鿡�� �⺻�뷮�� 1G�� �����Ͽ� �帳�ϴ�.</span>
				<a href="../proc/indb.php?mode=eduExtend"><img src="../img/btn_edu_extend.gif" align="absMiddle"></a>
			</div>
			<? } ?>
		</td>
	</tr>
	<tr>
		<td>��ũ ��뷮</td>
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