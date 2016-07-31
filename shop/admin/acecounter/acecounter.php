<?
@include "../../conf/partner.php";

$location = "통계/데이터관리 > 에이스카운터 관리자";
include "../_header.php";
@include "../../conf/config.acecounter.php";


if (!$acecounter['status_apply']) $acecounter['status_apply'] = 'N';
if (!$acecounter['status_use']) $acecounter['status_use'] = 'N';
if (!$acecounter['use']) $acecounter['use'] = 'N';


if ($acecounter['status_use'] == 'Y') {
	$acecounter_status = "등록: 버전(";
	#
	if ($acecounter['ver_use']=='m') $version_msg = "몰버전";
	else if ($acecounter['ver_use']=='c') $version_msg = "이커머스";
	else $version_msg = $acecounter['ver_apply'];
	#
	$acecounter_status .= $version_msg.")";
} else {
	$acecounter_status = "미등록";
}

$acecounter_status .= " ";
if ($acecounter['status_apply'] == 'Y') {
	$acecounter_status .= " ==> 신청중: 버전(";
	#
	if ($acecounter['ver_apply']=='m') $version_msg = "몰버전";
	else if ($acecounter['ver_apply']=='c') $version_msg = "이커머스";
	else $version_msg = $acecounter['ver_apply'];
	#
	$acecounter_status .= $version_msg.")";
}

?>
<? if ( strlen($acecounter['id']) == 0 || strlen($acecounter['pass']) == 0 || $acecounter[status_use] != 'Y') { ?>
<div class="title title_top">에이스카운터 관리자<span>에이스카운터 서비스 신청을 먼저하세요.</span> </div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>현재상태</td>
	<td><?=$acecounter_status?></td>
</tr>
</table>
<? } else { ?>
<div class="title title_top">에이스카운터 관리자<span></span> </div>
<table class=tb>
<col class=cellL>
<tr>
	<td>에이스카운터 관리자페이지가 <b><font color="#0000ff">새창</font></b>에서 연결됩니다. 팝업 차단 설정이 되어있다면 설정 해제 후 다시 확인해주세요.</td>
</tr>
</table>
<script language="javascript">
window.open("http://godomall.acecounter.com/login.amz?id=<?=$acecounter['id']?>&pw=<?=$acecounter['pass']?>","_blank");
</script>
<? } ?>

<?include "../_footer.php"; ?>