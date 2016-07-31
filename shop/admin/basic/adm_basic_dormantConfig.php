<?php
$location = '�⺻���� > �������� ��ȿ�Ⱓ�� ����';
include '../_header.php';

$dormant = Core::loader('dormant');
//��뿩��
$dormantUse = $dormant->checkDormantAgree();
//������
$dormantAgreeDate = $dormant->getDormantAgreeDate();
//�޸�ȸ�� ��ȯ��� ��
$dormantMemberCount = $dormant->getDormantMemberCount('dormantMemberAll');
//��üȸ����
$memberTotalCount = $dormant->getDormantMemberCount('memberTotal');
?>
<style type="text/css">
.admin_dormant_config_define {
	font-family: dotum;
	font-size: 13px;
	width: 800px;
	height: 157px;
	background-image: url('../img/bg_dormant.jpg');
	background-repeat:no-repeat;
	background-size: auto;
	padding-bottom: 40px;
}
.admin_dormant_config_define .admin_dormant_config_define_subject {
	font-weight: bold;
	color: red;
	font-size: 16px;
	padding: 60px 0px 0px 100px;
	float: left;
}
.admin_dormant_config_define .admin_dormant_config_define_content {
	padding: 40px 20px 0px 0px;
	float: right;
}
.admin_dormant_config_careful {
	width: 100%;
	font-family: dotum;
	font-size: 13px;
}
.admin_dormant_config_careful ol li{
	list-style: disc;
	line-height: 150%;
}
.admin_dormant_config_button {
	width: 800px;
	margin-top: 80px;
	text-align: center;
}
.admin_dormant_config_startInformation {
	width: 800px;
	text-align: center;
	margin-top: 80px;
	font-size: 16px;
	font-weight: bold;
	color: blue;
}
</style>

<div class="title title_top">�������� ��ȿ�Ⱓ�� ����<span>�������� ��ȿ�Ⱓ���� ������ �� �ֽ��ϴ�.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=48')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<div class="admin_dormant_config_define">
	<div class="admin_dormant_config_define_subject">
		<div>��������</div>
		<div>��ȿ�Ⱓ����?</div>
	</div>
	<div class="admin_dormant_config_define_content">
		<div>������Ÿ� �̿����� �� ������ȣ� ���� ���� ��29�� 2�� ����� ��16���� ����,</div>
		<div>1�� �̻� ���� �̿� ����� ���� ���� ���������� �����ϰų� ������ �и� �����Ͽ���</div>
		<div>�մϴ�.</div>
		<div>�������� ��ȿ�Ⱓ ���� 30������ ���� ������ ������ �ݵ�� ���� �ȳ��ؾ� �մϴ�.</div>
		<div>(������ : 2015�� 8�� 18��)</div>
	</div>
</div>

<div><img src="../img/dormantInfo.png" border="0" /></div>

<table cellpadding="0" cellspacing="0" class="admin_dormant_config_careful">
<tr>
	<td>
		<ol>
			<li style="font-weight: bold; color: red;">���� ���ɿ� ���� 1�� �̻� �������� ���� ȸ���� ���������� �ݵ�� ���� Ȥ�� �и����� �ϼž� �մϴ�. </li>
			<li><strong>�������� ��ȿ�Ⱓ�� ��� ������ ���� �ϼž� ȸ�� �������� �и� �������� ����� �� �ֽ��ϴ�.</strong><br />(2015�� 10�� 29�� ���Ŀ� ������ ���θ��� ������ ��뼳�� ���� �̿��� �� �ֽ��ϴ�.)</li>
			<li>��� ��� ���� ������ <strong>1�� �̻� �������� ���� ȸ���� ���� �ȳ� ���� ��� �޸�ȸ������ �и� ����</strong>�˴ϴ�.<br />���� �ȳ� ������ �߼����� ���� ���θ������� �� �� �����Ͻþ� ��� ��� ���� �Ͻñ� �ٶ��ϴ�. </li>
			<li>���޸���� ���� �ȳ� ���ϡ� �߼� ������ �Ͻø� �޸���� ó�� 30�� �� �ȳ� ������ �ڵ����� �߼��մϴ�. <strong><br /><a href="../member/email.cfg.php?mode=40" target="_blank">[�޸� ��ȯ ���� �ȳ� ���� ���� �ٷΰ���]</a></strong></li>
			<li style="font-weight: bold; color: red;">��� ��� ���� ���������� �������������� �α��� �� �޸� ���� ��ȯ ��  �޸���� ���� �ȳ� ���� �߼��� �ڵ����� �����մϴ�. </li>
			<li style="font-weight: bold; color: red;">������ �������� �α��� ���� ������ ���μ����� ������� �����Ƿ� �����Ͻñ� �ٶ��ϴ�.<br />(�� ó���Ȱǵ��� ���� ������ ���� �� �ѹ��� ó���մϴ�.) </li>
			<li>�޸�ȸ������ ó���� ȸ���� ȸ������Ʈ ȭ�鿡 ������� ������, ���޸�ȸ�� ������ �޴����� ��ȸ �����մϴ�. <strong><br /><a href="../dormant/adm_dormant_dormantMemberList.php" target="_blank">[�޸� ȸ�� ���� �޴� �ٷΰ���]</a></strong></li>
			<li>�޸� ȸ������ �и� ������ ȸ�� ������ ������ �ִ� �����ڸ� ���� �����մϴ�. <strong><br /><a href="../basic/adminGroup.php" target="_blank">[������ ���� ���� �ٷ� ����]</a></strong></li>
			<?php if($dormantUse === false){ ?><li style="font-weight: bold; color: red;">����������ȿ�Ⱓ�� ��� ���� �� ��üȸ�� <?php echo number_format($memberTotalCount); ?>�� �� <strong><?php echo number_format($dormantMemberCount); ?></strong>���� ���� �޸�������� ��ȯ �� �����Դϴ�. </li><?php } ?>
			<li>�޸�ȸ������ �и� ����� ȸ�����Դ� �����ü� email�̳� SMS�� �߼��� �� �����ϴ�.</li>
		</ol>
	</td>
</tr>
</table>
<?php
if($dormantUse === false){
?>
<table cellpadding="0" cellspacing="0" class="admin_dormant_config_button">
<tr>
	<td><img src='../img/btn_dormantSetting.gif' id="submitButton" class="hand" border="0" /></td>
</tr>
</table>
<?php
}
else {
?>
<div class="admin_dormant_config_startInformation">�������� ��ȿ�Ⱓ�� ��� ��� ���� �� : <?php echo $dormantAgreeDate; ?></div>
<?php
}
?>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#submitButton").click( function(){
		if(confirm("�������� ��ȿ�Ⱓ�� ��� ��� �� �������� 365�� �̻� �� ȸ���� ��� �޸�������� ��ȯ�մϴ�.\n���� �ȳ������� �߼����� ���� ��� ������ ��� ������ �����Ͻñ� �ٶ��ϴ�. ���� ��� ���� �� ��Ȱ��ȭ �� �� ������, �� �۾��� �ð��� ���� �ɸ� �� �־� ���� �ð��� �۾��Ͻñ� �����մϴ�.\n����� �����Ͻðڽ��ϱ�?")){

			var nav = navigator.userAgent.toLowerCase();
			jQuery(window).bind("keydown",function(e){
				var event = e || window.event;
				if(event.keyCode == 116){
					if(nav.indexOf("chrome") != -1){
						return "�޸�ȸ�� ��ȯ ó�� ���Դϴ�. ���ΰ�ħ�� �ϰų� �������� ���� ��� ����ó�� ���� ���� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?";
						if(event.preventDefault){
							event.preventDefault();
						}
						else {
							event.returnValue = false;
						}
					}
					else {
						if(!confirm("�޸�ȸ�� ��ȯ ó�� ���Դϴ�. ���ΰ�ħ�� �ϰų� �������� ���� ��� ����ó�� ���� ���� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?")){
							if(event.preventDefault){
								event.preventDefault();
							}
							else {
								event.returnValue = false;
							}
						}
					}
				}
			});

			jQuery(window).bind("beforeunload",function(e){
				var event = e || window.event;
				if(nav.indexOf("chrome") != -1){
					return "�޸�ȸ�� ��ȯ ó�� ���Դϴ�. ���ΰ�ħ�� �ϰų� �������� ���� ��� ����ó�� ���� ���� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?";
					if(event.preventDefault){
						event.preventDefault();
					}
					else {
						event.returnValue = false;
					}
				}
				else {
					if(!confirm("�޸�ȸ�� ��ȯ ó�� ���Դϴ�. ���ΰ�ħ�� �ϰų� �������� ���� ��� ����ó�� ���� ���� �� �ֽ��ϴ�.\n����Ͻðڽ��ϱ�?")){
						if(event.preventDefault){
							event.preventDefault();
						}
						else {
							event.returnValue = false;
						}
					}
				}
			});

			showDormantProgressBar();

			var ajaxTransfer =  jQuery.ajax({
				method: "POST",
				url: "indb.php",
				data: { mode: 'dormantConfig', actionMode: 'agree'}
			});
			ajaxTransfer.done(function( data ) {
				var result = new Array();
				result = data.split("|");

				if(result[1]){
					alert(result[1]);
				}
				window.location.reload();
			});
			ajaxTransfer.fail(function() {
				alert("��ſ����� �߻��Ͽ����ϴ�.\n�ٽ��ѹ� �õ��Ͽ� �ּ���.");
			});
			ajaxTransfer.always(function() {
				jQuery(window).unbind("keydown beforeunload");
				hiddenDormantProgressBar();
			});
		}
	});

	function showDormantProgressBar(){
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);

		jQuery("body").append('<div id="dormantProgressBar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;" /><div style="color: white; font-weight: bold;">�޸�ȸ�� ��ȯ ó�����Դϴ�.<br /> ���� ~ ���ʺ��� �ɸ� �� �ֽ��ϴ�.</div></div>');
	}

	function hiddenDormantProgressBar(){
		jQuery("#dormantProgressBar").remove();
	}
});
</script>

<?php include '../_footer.php'; ?>