<?php

if(isset($cfg)===false) @include dirname(__FILE__).'/../../conf/config.php';
include dirname(__FILE__).'/../../lib/naverCommonInflowScript.class.php';

$naverCommonInflowScript = new NaverCommonInflowScript();

?>
<link rel="stylesheet" href="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/configure.css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/configure.js"></script>
<div class="title title_top">�������� ��ũ��Ʈ ����</div>
<form id="common-inflow-script-configure-form" action="<?php echo $cfg['rootDir']; ?>/admin/naverCommonInflowScript/indb.php">
	<input type="hidden" name="mode" value=""/>
	<div class="common-inflow-script-description">
		<strong>*�ʵ�* </strong><br/>
		<p><strong>�������Խ�ũ��Ʈ</strong>��, ���̹� ����, ���̹� ����, �˻������� �����̾��α׺м� ������ ����Ʈ��ŷ�� ���� �����Ǵ� ���� �����Ͽ� �����ϱ����� �����Դϴ�.</p>
		<p>
			�������Խ�ũ��Ʈ�� ���� �ϼž߸� ���̹��ΰ����񽺸� ����� �� �ֱ� ������, �������Խ�ũ��Ʈ�� �������� ����	��������<br/>
			���̹� ����, ���̹� ���� ���� ��뿡 ������ ���� �� �ֽ��ϴ�.<br/>
			��, ���̹� ���μ��񽺸� ����ϴ� �������� ��� <u>CPC</u><sup>1)</sup>���� <u>CPA</u><sup>2)</sup>���� ������ȯ �������� �������� �����ŵ� ������, CPA������ȯ ���Ŀ���<br/>
			�������Խ�ũ��Ʈ�� �����Ǿ����� ������ ���񽺻�뿡 ������ ���� �� �ֻ���� �ǵ��� �̸� �����Ͽ��νñ� �ٶ��ϴ�.
		</p>
		<p>
			1) CPC(Cost per click) : ���̹� ���ο� ����� ��ǰ�� Ŭ���Ͽ����� ���ݵǴ� ����.<br/>
			2) CPA(Cost per action) : ���̹��κ��� ���ԵǾ� �ֹ��� �Ͼ���� ���ݵǴ� ����.
		</p>
		<br/>
		<p>
		<strong>���̹���������Ű Ȯ�ι��</strong><br/>
		���̹���������Ű�� "���̹� ������Ʈ���� > �������� > ��������"���� Ȯ���ϽǼ� �ֽ��ϴ�. ���� ���̹���������Ű�� Ȯ���ϽǼ� �����ôٸ� ���̹� ������Ʈ���������� ���̹���������Ű�� Ȯ�ι���� ���Ͽ� �����ֽñ� �ٶ��ϴ�.<br/>
		[���α��� ������] 1588-3819&nbsp;&nbsp;&nbsp;[�˻����� ������] 1588-5896
		</p>
	</div>

	<table class="tb" border="0">
		<colgroup>
			<col class="cellC"><col class="cellL">
		</colgroup>
		<tbody>
			<tr>
				<td>���̹���������Ű</td>
				<td>
					<div>
						<span class="red" style="font-weight: bold;">�����ǡ�</span><br/>
						<span class="extext">
							�ѹ� �Է��Ͻ� "���̹���������Ű"�� �����Ͻ� �� �����ϴ�.<br/>
							�����Է½� �����Ͽ��ֽñ� �ٶ��ϴ�.<br/>
							���� �߸��Է��Ͽ��ų� ������ �ʿ��� �ÿ��� �� �����ͷ� �����ֽñ� �ٶ��ϴ�.
						</span>
					</div>
<?php if($naverCommonInflowScript->isEnabled){ ?>
					<div id="set-account-id" class="confirmed-account-id"><?php echo $naverCommonInflowScript->accountId; ?></div>
					<input type="hidden" name="accountId" value="<?php echo $naverCommonInflowScript->accountId; ?>" class="line" required="required"/>
<?php }else{ ?>
					<input type="text" name="accountId" value="<?php echo $naverCommonInflowScript->accountId; ?>" class="line" required="required"/>
					<input type="button" id="account-id-check-duplicate" value="�ߺ�Ȯ��"/>
<?php } ?>
				</td>
			</tr>
			<tr>
				<td>White List</td>
				<td>
					<div id="white-list-container" data-initialize="<?php echo is_array($naverCommonInflowScript->whiteList)?implode('|', $naverCommonInflowScript->whiteList):''; ?>"></div>
					<div class="extext">
						���̹� ���̼����� ���԰�κ� ������ ���̹� ������Ʈ������ ����Ͻ� �����ο� ���ؼ��� ����˴ϴ�.<br/>
						���θ��� �������� ���������� ��Ǵ� ��� White List�� �ش� �����ε��� �߰��Ͽ��ֽñ� �ٶ��ϴ�.
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="noline" style="text-align: center; padding: 10px;" id="common-inflow-script-configure-form-submit">
		<input type="image" src="<?php echo $cfg['rootDir']; ?>/admin/img/btn_naver_install.gif"/>
	</div>
</form>