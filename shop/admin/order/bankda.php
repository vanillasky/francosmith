<?

$location = "�ڵ��Ա�Ȯ�� ���� > �ڵ��Ա�Ȯ�� ���� ��û";
include "../_header.php";

$MID		= sprintf("GODO%05d",$godo[sno]);	# �������̵�

$ceoName	= $cfg[ceoName];	# ��ǥ�ڸ�
$resDomain	= urlencode("{$_SERVER[HTTP_HOST]}" . str_replace("admin/order/bankda.php", "", $_SERVER[PHP_SELF]) . "lib/bank.sock.php"); # �ڵ��Ա�Ȯ�� ó����� ���� URL
?>

<div class="title title_top">�ڵ��Ա�Ȯ�� ���� ��û<span>�ŷ�������µ��� ����ݳ����� ���������� ��ȸ������ �� �ִ� �����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=10')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<div style="padding-left:23">�ڵ��Ա�Ȯ�� ���񽺴� (��)��ũ�ٿ��� �����ϴ� �����̸�, ���� ������ (��)��ũ�� ȸ������ ���Ե��� �˷��帳�ϴ�.</div>


<? if ( $ceoName == '' ){ ?>
<div style="color:red;">���θ��⺻�������� ��ǥ�ڸ��� �Է��ϼž� �� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�.</div>
<? } else { ?>
<iframe name="ifrmBankda" src="http://bankda.godomall.co.kr/index.asp?Upid=<?=$MID?>&Upname=<?=$ceoName?>&Updomain=<?=$resDomain?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>
<? } ?>


<? include "../_footer.php"; ?>