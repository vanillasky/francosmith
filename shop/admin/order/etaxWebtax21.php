<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڼ��ݰ�꼭 �Ŵ��� ����";
include "../_header.php";

$compSerial	= $cfg[compSerial];	# ����ڹ�ȣ
?>

<div class="title title_top">���ڼ��ݰ�꼭 �Ŵ��� ����<span>���ڼ��ݰ�꼭�� �����ϴ� LG������ ���ý�21���� ���ݰ�꼭�� ���ŵ� ������ ��ȸ�� �� �ֽ��ϴ�.</span></div>

<? if ( $compSerial == '' ){ ?>
<div style="color:red;">���θ��⺻�������� ����ڹ�ȣ�� �Է��Ͻ� �� ���ڼ��ݰ�꼭(WebTax21)�� �����ϼž� �� ���񽺸� �̿��Ͻ� �� �ֽ��ϴ�.</div>
<? } else { ?>
<iframe name="ifrmWebTax21" src="http://www.webtax21.com/webtax21/webtax?func=login&from=remote_from&saup=<?=$compSerial?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>
<? } ?>


<? include "../_footer.php"; ?>