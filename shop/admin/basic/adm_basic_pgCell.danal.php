<?php

include "../_header.popup.php";
@include "../../conf/pg_cell.danal.cfg.php";

$config = Core::loader('config');
$danal = Core::loader('Danal');

$shopConfig = $config->load('config');
if (empty($danalCfg)) {
	$danalCfg = $config->load('danal');
}
$checked = array(
	'serviceType' => array($danalCfg['serviceType'] => ' checked="checked"'),
);
?>
<script type="text/javascript">
window.onload = function()
{
	resizeFrame();
};

var IntervarId;
function resizeFrame()
{

	var oBody = document.body;
	var oFrame = parent.document.getElementById("pgifrm");
	var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;
	oFrame.height = i_height;

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>
<div class="title title_top">
�ٳ� ���� <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=46')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<form action="<?php echo $shopConfig['rootDir']; ?>/admin/basic/adm_basic_pgCell.danal.indb.php" method="post">
	<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td height="40">���� ��� ����</td>
			<td class="noline">
				<div style="margin: 8px 0; padding-left: 10px;">
					<input id="svc-type-10" type="radio" name="serviceType" value="10"<?php echo $checked['serviceType']['10']; ?> required label="����ȯ��"/>
					<label for="svc-type-10">�����</label>
					<input id="svc-type-no" type="radio" name="serviceType" value="no"<?php echo $checked['serviceType']['no']; ?>/>
					<label for="svc-type-no">������</label>
				</div>

				<div style="margin: 8px 0; padding: 10px 10px 0 0;" class="red">
					<ul style="padding: 0 0 0 20px; margin: 5px 0 0 0;">
						<li style="margin-bottom: 5px;">
							����� : ���θ��� ������ ��� ����ڵ��� ���񽺸� ����� �� ������, ������ PC�� ����Ͽ��� ������ �̷�����ϴ�.
						</li>
						<li>
							������ : ���񽺸� ��Ȱ��ȭ���� ������ ������� �ʽ��ϴ�.
						</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td height="40">
				�ٳ� <span class="blue">PG ID</span>
			</td>
			<td style="padding-left:10px;">
				<? if ($danalCfg['S_CPID']) { 
					echo $danalCfg['S_CPID']; ?> &nbsp; <? if ($danalCfg['pg-centersetting'] === 'Y') { ?><span class="extext">�ڵ����� �Ϸ� <?php } ?></span>
				<? } else {?>
					<span class="extext">�ٳ� ���� ��û�� �߱޵Ǵ� ���̵��Դϴ�. <a href='http://www.godo.co.kr/echost/power/add/payment/mobile-pg-intro.gd' target="_blank">[�ٳ� ��û �ٷΰ���]</a> <? } ?></span>
			</td>
		</tr>
	</table>

	<div class="button">
		<input type="image" src="../img/btn_save.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>
<!-- �ñ��� �ذ� : Start -->
<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<ol>
					<li style="margin-bottom: 5px;">
						2015�� 08�� 27�� ���� ��û�� ��� '�����' ���� �� ����ϼ�(v1/v2) ��Ų��ġ�� �Ͻñ� �ٶ��ϴ�. (PC�� ��Ų�������� ��� ����) ��Ų��ġ ����� �Ŵ����� �����Ͻñ� �ٶ��ϴ�. <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=46')"><img src="../img/btn_q.gif" border="0" align="absmiddle"/></a>
					</li>
					<li>
						������� ���ɽð� : ���� �� ���ϱ����� ��� ó�� �����ϹǷ� ���� �� ���Ŀ��� ���θ�(������)�� �����ڿ��� �ٸ� ����(������, ������ ��)����<br/>
						ȯ�� ó���ؾ� �մϴ�.<br/>
						Ex1) 2015�� 8�� 01�� ���� > 8�� 31�� 24�ñ����� ���� ��� ����<br/>
						Ex2) 2015�� 8�� 31�� ���� > 9�� 1�� ���� ��� ��û �� ��� ���� ��� ó�� �Ұ� > �����ڿ��� �ٸ�����(������,������ ��)���� ȯ��ó���� ����
					</li>
				</ol>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">cssRound('MSG01');</script>