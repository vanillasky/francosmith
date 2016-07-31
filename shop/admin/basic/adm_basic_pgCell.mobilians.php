<?php

include "../_header.popup.php";

$config = Core::loader('config');
$mobilians = Core::loader('Mobilians');

$shopConfig = $config->load('config');
$mobiliansCfg = $config->load('mobilians');
$mobiliansPrefix = $mobilians->lookupPrefix();

$checked = array(
    'serviceType' => array($mobiliansCfg['serviceType'] => ' checked="checked"'),
);

//������� PG �߾�ȭ üũ
if($mobiliansCfg['pg-centersetting']=='Y'){	
	$pgStatus = 'auto';
} else{
	$pgStatus = 'menual';
}
?>
<script type="text/javascript">
window.onload = function()
{
	resizeFrame();
};

function mobiliansConfigFormSubmit(frm)
{
	var
	originEnv = "<?php echo $mobiliansCfg['serviceType']; ?>",
	selectRealEnv = null,
	warningMsg = "����ȯ���� �ǰŷ� ȯ������ �����ϼ̽��ϴ�.\r\n"
		   + "������� ����ڿ� ���Ǿ��� �ǰŷ� ȯ������ ��ȯ ��\r\n"
		   + "������ �����ʾ� Ŭ������ �߻��� �� �ֽ��ϴ�.\r\n"
		   + "����Ͻðڽ��ϱ�?";
	for (var serviceTypeIndex = 0, selectedServiceType = frm.serviceType[0]; selectedServiceType; selectedServiceType = frm.serviceType[++serviceTypeIndex]) {
		if(frm.serviceType[serviceTypeIndex].value === "10" && frm.serviceType[serviceTypeIndex].checked === true) selectRealEnv = true;
	}
	// �ǰŷ��� �����ߴµ� Ȯ��â�� ���� ���� �������� false
	if (originEnv !== "10" && selectRealEnv === true && confirm(warningMsg) === false) {
		return false;
	}
	else {
		return chkForm(frm);
	}
}

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
������� ���� <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=38')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a>
</div>
<form action="<?php echo $shopConfig['rootDir']; ?>/admin/basic/adm_basic_pgCell.mobilians.indb.php" method="post" onsubmit="return mobiliansConfigFormSubmit(this);">
	<table border="1" bordercolor="#e1e1e1" style="border-collapse:collapse" width="100%">
		<col class="cellC"><col class="cellL">
		<tr>
			<td height="40">���� ��� ����</td>
			<td class="noline">
				<div style="margin: 8px 0; padding-left: 10px;">
					<input id="svc-type-10" type="radio" name="serviceType" value="10"<?php echo $checked['serviceType']['10']; ?> required label="����ȯ��"/>
					<label for="svc-type-10">�����</label>
					<input id="svc-type-00" type="radio" name="serviceType" value="00"<?php echo $checked['serviceType']['00']; ?>/>
					<label for="svc-type-00">�׽�Ʈ</label>
					<input id="svc-type-no" type="radio" name="serviceType" value="no"<?php echo $checked['serviceType']['no']; ?>/>
					<label for="svc-type-no">������</label>
				</div>

				<div style="margin: 8px 0; padding: 10px 10px 0 0;" class="red">
					<ul style="padding: 0 0 0 20px; margin: 5px 0 0 0;">
						<li style="margin-bottom: 5px;">
							����� : ���θ��� ������ ��� ����ڵ��� ���񽺸� ����� �� ������, ������ PC�� ����Ͽ��� ������ �̷�����ϴ�.
						</li>
						<li style="margin-bottom: 5px;">
							�׽�Ʈ : �����ڸ� ���񽺸� ����� �� ������, ���������� �����ϵ� ������ ������ �̷�������� �ʽ��ϴ�.
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
				������� <span class="blue">PG ID <br>(����ID)</span>
			</td>
			<td style="padding-left:10px;">
				<? if ( $pgStatus == "auto" ) { ?>
				<input type="hidden" name="serviceId" value="<?php echo $mobiliansCfg['serviceId']; ?>"/>
				<input type="hidden" name="pg_centersetting" value="<?php echo $mobiliansCfg['pg-centersetting']; ?>"/>
				<?=$mobiliansCfg['serviceId']?>
				&nbsp;<span class="blue">�ڵ����� �Ϸ�</span>
				<? } else { ?>
				<input type="text" name="serviceId" value="<?php echo $mobiliansCfg['serviceId']; ?>" required label="����ID"/>
				<span class="extext">������𽺿��� �������� �߱޵Ǵ� ���̵� �Դϴ�.(���� 12�ڸ��̸�, <?php echo implode(',', $mobiliansPrefix); ?>�� ���۵Ǿ�� ��)</span>
				<? } ?>
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
						2013�� 06�� 07�� ���� ��û�� ��� '�׽�Ʈ', '�����' ���� �� ��Ų��ġ�� �Ͻñ� �ٶ��ϴ�. ��Ų��ġ ����� �Ŵ����� �����Ͻñ� �ٶ��ϴ�. <a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=basic&no=38')"><img src="../img/btn_q.gif" border="0" align="absmiddle"/></a>
					</li>
					<li>
						������� ���ɽð� : ���� �� ���ϱ����� ��� ó�� �����ϹǷ� ���� �� ���Ŀ��� ���θ�(������)�� �����ڿ��� �ٸ� ����(������, ������ ��)����<br/>
						ȯ�� ó���ؾ� �մϴ�.<br/>
						Ex1) 2013�� 5�� 01�� ���� > 5�� 31�� 24�ñ����� ���� ��� ����<br/>
						Ex2) 2013�� 5�� 31�� ���� > 6�� 1�� ���� ��� ��û �� ��� ���� ��� ó�� �Ұ� > �����ڿ��� �ٸ�����(������,������ ��)���� ȯ��ó���� ����
					</li>
				</ol>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">cssRound('MSG01');</script>