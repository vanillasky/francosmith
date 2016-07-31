<?php
$location = 'ȸ������ > �޸� ȸ�� ����Ʈ';
include '../_header.php';
include '../../lib/page.class.php';

$dormant = Core::loader('dormant');

//�������� ��ȿ�Ⱓ�� ���� üũ
if($dormant->checkDormantAgree() === false){
	msg("�������� ��ȿ�Ⱓ�� ���� �� �̿밡���մϴ�.", "../basic/adm_basic_dormantConfig.php");
	exit;
}

//ȸ���׷�
$group = array();
$res = $db->query("SELECT * FROM ".GD_MEMBER_GRP);
while ($data=$db->fetch($res)) {
	$group[$data['level']] = $data['grpnm'];
}

//�� �޸�ȸ�� ��
$total = $dormant->getDormantMemberCount('dormantCount');

if (!$_GET['page_num']) $_GET['page_num'] = 10;
if (!$_GET['skey']) $_GET['skey'] = 'm_id';
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['skey'][$_GET['skey']]			= "selected";

$where[] = "m_id != 'godomall'";
if ($_GET['skey'] && trim($_GET['sword'])){
	$where[] = $dormant->getListWhere($_GET['skey'], trim($_GET['sword']));
}
if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "dormant_regDate between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";

$orderby = 'dormant_regDate desc, regdt desc';

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = $dormant->getSecretField();
$pg->setQuery($dormant->getDormantTableName(), $where, $orderby);
$pg->exec();
$res = $db->query($pg->query);
?>
<div class="title title_top">�޸� ȸ�� ����Ʈ<span>�޸� ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=27');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<form>
<table class="tb">
<colgroup>
	<col class="cellC" style="width: 150px;"/>
	<col class="cellL" />
</colgroup>
<tr>
	<td>�޸� ��� ȸ�� ����</td>
	<td>
		<select name="skey">
			<option value="m_id" <?=$selected['skey']['m_id']?>> ���̵� </option>
			<option value="name" <?=$selected['skey']['name']?>> �̸� </option>
			<option value="email" <?=$selected['skey']['email']?>> �̸��� </option>
			<option value="mobile" <?=$selected['skey']['mobile']?>> �޴�����ȣ </option>
			<option value="phone" <?=$selected['skey']['phone']?>> ��ȭ��ȣ </option>
		</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
		<div class="ver81" style="color: #627dce; margin-top: 5px;"> * ���̵�, �̸� �� ȸ������ �˻� �� ��Ȯ�� ������ �Է��ؾ� �˻������մϴ�.<br /> ��) �˻��Ϸ��� ȸ�� ID�� godomall �� ��� : go(X), godo(X), godomall(O)</div>
	</td>
</tr>
<tr>
	<td>�޸�ȸ�� ��ȯ��</td>
	<td>
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
		<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>

<div class="button_top" style="margin-bottom:30px;"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo ver8">
	<strong> �� �޸�ȸ�� ����Ʈ (�˻���� <?php echo number_format($pg->recode['total']);?>�� / ��ü <?php echo number_format($total); ?>��)</strong>
	<br />
	������ �����Ϸκ��� 1���� ���� ȸ���� �޸�ȸ������ �и� ����˴ϴ�
	</td>
	<td align="right">
		<select name="page_num" onchange="this.form.submit();">
		<?php
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?php echo $v; ?>" <?php echo $selected['page_num'][$v]; ?>><?php echo $v; ?>�� ���</option>
		<? } ?>
		</select>
	</td>
</tr>
</table>
</form>

<form name="dormantForm" id="dormantForm" action="./adm_dormant_indb.php" method="post" target="ifrmHidden" />
<input type="hidden" name="mode" id="mode" value="" />
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<colgroup>
	<col width="5%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="right" />
	<col width="12%" align="right" />
	<col width="13%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
</colgroup>
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">����</a></th>
	<th>�޸�ȸ�� ��ȯ��</th>
	<th>���̵�</th>
	<th>�̸�</th>
	<th>�׷�</th>
	<th>������</th>
	<th>�����α���</th>
	<th>�̸���</th>
	<th>�޴�����ȣ</th>
	<th>��ȭ��ȣ</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<?php
while ($data = $db->fetch($res)){
?>
<tr height=40 align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?php echo $data['m_no']; ?>"></td>
	<td><?php echo $data['dormant_regDate']; ?></td>
	<td><?php echo $data['m_id']; ?></td>
	<td><?php echo $data['name']; ?></td>
	<td><?php echo $group[$data['level']]; ?></td>
	<td><?php echo number_format($data['emoney']); ?> ��</td>
	<td><?php echo $data['last_login']; ?></td>
	<td><?php echo $data['email']; ?></td>
	<td><?php echo $data['mobile']; ?></td>
	<td><?php echo $data['phone']; ?></td>
</tr>
<tr><td colspan="40" class="rndline"></td></tr>
<?php } ?>
</table>
</form>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%" height="35" style="padding-left:13px">
		<div style="width: 225px;">������ <img src="../img/btn_dormantClear.gif" border="0" id="dormantRestoreAdmin" class="hand" style="vertical-align: bottom;" />&nbsp;<img src="../img/btn_dormantDelete.gif" border="0" id="dormantMemberDelete" class="hand" style="vertical-align: bottom;" /></div>
	</td>
	<td width="60%" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></font></td>
	<td width="20%" align="right"><img src="../img/btn_dormantAllDelete.gif" border="0" id="dormantMemberDeleteAll" class="hand" style="vertical-align: bottom;" /></td>
</tr>
</table>

<?php include "../_footer.php"; ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	var form = $("#dormantForm");

	//�޸�ȸ�� ��ȯ
	$("#dormantRestoreAdmin").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("ȸ���� �����Ͽ� �ּ���.");
			return;
		}
		if(confirm("������ ȸ���� �޸� ���¸� ���� �Ͻðڽ��ϱ�?")){
			$("#mode").val("dormantRestoreAdmin");
			form.submit();
		}
		return;
	});
	//ȸ�� ����
	$("#dormantMemberDelete").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("ȸ���� �����Ͽ� �ּ���.");
			return;
		}
		if(confirm("������ �޸�ȸ�������� �����Ͻðڽ��ϱ�?\n������ ȸ���� Ż��ó���˴ϴ�.")){
			$("#mode").val("dormantMemberDelete");
			form.submit();
		}
		return;
	});
	//�޸�ȸ������ ��ü����
	$("#dormantMemberDeleteAll").click(function(){
		var total = '<?php echo $total; ?>';
		if(confirm("��ü " + total + " ���� �޸�ȸ�������� �����Ͻðڽ��ϱ�?\n���� ���� �� �ش� ȸ���� Ż��ó���˴ϴ�.")){
			$("#mode").val("dormantMemberDeleteAll");
			form.submit();
		}
		return;
	});
	$("iframe[name='ifrmHidden']").load(function(){
		hiddenDormantProgressBar();
	});
	form.submit(function(){
		showDormantProgressBar();
	});

	function showDormantProgressBar(msg){
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);

		jQuery("body").append('<div id="dormantProgressBar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;" /></div>');
	}

	function hiddenDormantProgressBar(){
		jQuery("#dormantProgressBar").remove();
	}
});
</script>