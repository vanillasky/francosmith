<?php
$location = "�������� > ���� iPay PG���� ����";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/auctionIpay.class.php";

$auctionIpayConfigPath = "../../conf/auctionIpay.cfg.php";
if(file_exists($auctionIpayConfigPath)) require $auctionIpayConfigPath;

$auctionIpayPgConfigPath = "../../conf/auctionIpay.pg.cfg.php";
if(file_exists($auctionIpayPgConfigPath)) require $auctionIpayPgConfigPath;

if(!isset($auctionIpayCfg['logoType'])) $auctionIpayCfg['logoType'] = '../admin/img/logo_ipay01.gif';
$checked['logoType'][$auctionIpayCfg['logoType']] = 'checked="checked"';

if(!isset($auctionIpayPgCfg['testYn'])) $auctionIpayPgCfg['testYn']='n';
if(!isset($auctionIpayPgCfg['useYn'])) $auctionIpayPgCfg['useYn']='n';
if(!isset($auctionIpayPgCfg['paymentrule'])) $auctionIpayPgCfg['paymentrule']=0;
$checked['testYn'][$auctionIpayPgCfg['testYn']] = 'checked="checked"';
$checked['useYn'][$auctionIpayPgCfg['useYn']] = 'checked="checked"';
$checked['paymentrule'][$auctionIpayPgCfg['paymentrule']] = 'checked="checked"';
?>
<style type="text/css">
img{
	border:none;
}
</style>
<script type="text/javascript">
function copy_txt(val)
{
	window.clipboardData.setData('Text', val);
}
</script>

<div style="width:800px;">
<form method="post" action="indb.iPayPg.php" onsubmit="return checkForm(this)" target="ifrmHidden"/>
<div class="title title_top">���� iPay ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr>
	<td height="50">���� seller id</td>
	<td>
		<input type="text" name="sellerid" value="<?php echo $auctionIpayCfg['sellerid']; ?>" required msgR="���� seller id�� �ʼ��Դϴ�."/>
	</td>
</tr>
<tr>
	<td height="50">����Ű</td>
	<td>
		<div style="margin:0px; padding:0px;" class="small1 extext"><a href="javascript:popup('http://ipay.auction.co.kr/ipay/SellerRegister.aspx?url=<?=urlencode("http://ipaymall.godo.co.kr/auction_ipaymall_ticket.php");?>&return_url=<?php echo urlencode($_SERVER['HTTP_HOST']); ?>',493,672);"><img src="../img/btn_id_return.gif" align="absmiddle" /></a> ���� �α��� �� iPay �����ϱ� â���� �������� ��ư�� �����ֽñ� �ٶ��ϴ�.</div>
		<div><textarea name="ticket" style="width:600px; height:50px;" required msgR="����Ű�� �ʼ��Դϴ�." ><?php echo $auctionIpayCfg['ticket']; ?></textarea></div>
	</td>
</tr>
<tr>
	<td height="50">���� iPay �ΰ� ����</td>
	<td class="noline">
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay01.gif" <?php echo $checked['logoType']['../admin/img/logo_ipay01.gif']; ?> /><img src="../../admin/img/logo_ipay01.gif" align="absmiddle" /></label>
		<label><input type="radio" name="logoType" value="../admin/img/logo_ipay02.gif" <?php echo $checked['logoType']['../admin/img/logo_ipay02.gif']; ?> /><img src="../../admin/img/logo_ipay02.gif" align="absmiddle" /></label>
		<div style="padding-top:5;">{=auctionIpayLogo()} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{auctionIpayLogo()}')" alt="�����ϱ�" align="absmiddle"/></div>
		<div style="padding-top:10;" class="small1 extext">
			<div>�����Ͻ� <b>ġȯ�ڵ�</b>�� �������� �����Ͻø� ���� iPay �ΰ� ��µ˴ϴ�.</div>
		</div>
	</td>
</tr>
</table>
<p/>
<div class="title title_top">���� iPay PG���� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=16')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
<col class="cellC"><col class="cellL">
<tr height="30">
	<td>��뿩��</td>
	<td class="noline">
	<label><input type="radio" name="useYn" value="y" <?php echo $checked['useYn']['y']; ?>/>���</label><label><input type="radio" name="useYn" value="n" <?php echo $checked['useYn']['n']; ?>/>������</label>
	</td>
</tr>
<tr height="30">
	<td>�׽�Ʈ�ϱ�</td>
	<td class="noline">
	<label><input type="radio" name="testYn" value="y" <?php echo $checked['testYn']['y']; ?>/>���</label><label><input type="radio" name="testYn" value="n" <?php echo $checked['testYn']['n']; ?> />������</label>
	<div style="padding-top:5;" class="small1 extext">
	<div>�׽�Ʈ�� ��뿡 �����Ͻø� �����ڷ� �α����� ���¿����� �������������� ����iPay ���� üũ�ڽ��� ǥ�õ˴ϴ�.</div>
	</div>
	</td>
</tr>
<tr>
	<td height="50">��������</td>
	<td class="noline">
		<label><input type="radio" name="paymentrule" value="0" <?php echo $checked['paymentrule'][0]; ?> required msgR="���������� �ʼ��Դϴ�." />��� ����</label>
		<label><input type="radio" name="paymentrule" value="1" <?php echo $checked['paymentrule'][1]; ?> required msgR="���������� �ʼ��Դϴ�." />ī�� �Ұ�</label>
		<label><input type="radio" name="paymentrule" value="2" <?php echo $checked['paymentrule'][2]; ?> required msgR="���������� �ʼ��Դϴ�." />�������Ա� �Ұ�</label>
		<label><input type="radio" name="paymentrule" value="3" <?php echo $checked['paymentrule'][3]; ?> required msgR="���������� �ʼ��Դϴ�." />ī�� �޴��� �Ұ�</label>
		<div style="padding-top:5;" class="small1 extext">
		<div>�����ݾ� 1000�� ���ϴ� �ſ�ī�� ������ �Ұ����մϴ�.</div>
		</div>
	</td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>
</div>

</form>
</div>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr>
<td>
	<div>�ݵ�� ���� iPay �ɻ簡 �Ϸ� �Ǿ� ���񽺸� ����Ͻ� �� ������ �� ��뿩�θ� ������� �����Ͻʽÿ�.</div>
	<div>iPay PG������ ���� ���ܿ� ����Ǹ�, ������ ���� ������ �״�� ����Ͻ� �� �ֽ��ϴ�.</div>
	<div>
		iPay PG�� ������ �ֹ��� e���� �����ڿ� ���� ������, ��� Ȯ���� �����ϸ� ���񽺽�û�� �⺻�������������� ��û�� �� �ֽ��ϴ�.
		<a href="<?php echo $cfg['rootDir']; ?>/admin/basic/iPayPg.intro.php" style="margin-left: 5px; font-weight: bold;" class="blue">[iPay PG���� ���� ��û]</button>
	</div>
</td>
</tr>
</table>
</div>

<script type="text/javascript">cssRound('MSG01');</script>
<? include "../_footer.php"; ?>