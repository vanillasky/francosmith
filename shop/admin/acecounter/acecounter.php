<?
@include "../../conf/partner.php";

$location = "���/�����Ͱ��� > ���̽�ī���� ������";
include "../_header.php";
@include "../../conf/config.acecounter.php";


if (!$acecounter['status_apply']) $acecounter['status_apply'] = 'N';
if (!$acecounter['status_use']) $acecounter['status_use'] = 'N';
if (!$acecounter['use']) $acecounter['use'] = 'N';


if ($acecounter['status_use'] == 'Y') {
	$acecounter_status = "���: ����(";
	#
	if ($acecounter['ver_use']=='m') $version_msg = "������";
	else if ($acecounter['ver_use']=='c') $version_msg = "��Ŀ�ӽ�";
	else $version_msg = $acecounter['ver_apply'];
	#
	$acecounter_status .= $version_msg.")";
} else {
	$acecounter_status = "�̵��";
}

$acecounter_status .= " ";
if ($acecounter['status_apply'] == 'Y') {
	$acecounter_status .= " ==> ��û��: ����(";
	#
	if ($acecounter['ver_apply']=='m') $version_msg = "������";
	else if ($acecounter['ver_apply']=='c') $version_msg = "��Ŀ�ӽ�";
	else $version_msg = $acecounter['ver_apply'];
	#
	$acecounter_status .= $version_msg.")";
}

?>
<? if ( strlen($acecounter['id']) == 0 || strlen($acecounter['pass']) == 0 || $acecounter[status_use] != 'Y') { ?>
<div class="title title_top">���̽�ī���� ������<span>���̽�ī���� ���� ��û�� �����ϼ���.</span> </div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�������</td>
	<td><?=$acecounter_status?></td>
</tr>
</table>
<? } else { ?>
<div class="title title_top">���̽�ī���� ������<span></span> </div>
<table class=tb>
<col class=cellL>
<tr>
	<td>���̽�ī���� �������������� <b><font color="#0000ff">��â</font></b>���� ����˴ϴ�. �˾� ���� ������ �Ǿ��ִٸ� ���� ���� �� �ٽ� Ȯ�����ּ���.</td>
</tr>
</table>
<script language="javascript">
window.open("http://godomall.acecounter.com/login.amz?id=<?=$acecounter['id']?>&pw=<?=$acecounter['pass']?>","_blank");
</script>
<? } ?>

<?include "../_footer.php"; ?>