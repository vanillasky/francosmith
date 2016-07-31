<?php
$location = 'ȸ������ > �޸� ��ȯ ���� ȸ�� ����Ʈ';
include '../_header.php';
include '../../lib/page.class.php';

$dormant = Core::loader('dormant');

//�������� ��ȿ�Ⱓ�� ���� üũ
if($dormant->checkDormantAgree() === false){
	msg("�������� ��ȿ�Ⱓ�� ���� �� �̿밡���մϴ�.", "../basic/adm_basic_dormantConfig.php");
	exit;
}

### �׷�� ��������
$group = array();
$res = $db->query("SELECT * FROM ".GD_MEMBER_GRP);
while ($data=$db->fetch($res)) {
	$group[$data['level']] = $data['grpnm'];
}

//�� �޸鿹�� ȸ�� ��
$total = $dormant->getDormantMemberCount('dormantMemberAll');

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = 'last_login desc';

// �����Ҵ�
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$selected['sstatus'][$_GET['sstatus']]		= "selected";
$selected['slevel'][$_GET['slevel']]		= "selected";
$selected['sunder14'][$_GET['sunder14']]	= "selected";
$selected['sage'][$_GET['sage']]			= "selected";
$selected['birthtype'][$_GET['birthtype']]	= "selected";
$selected['marriyn'][$_GET['marriyn']]		= "selected";
$checked['sex'][$_GET['sex']]				= "checked";
$checked['mailing'][$_GET['mailing']]		= "checked";
$checked['smsyn'][$_GET['smsyn']]			= "checked";
if(is_array($_GET['inflow'])) foreach($_GET['inflow'] as $v) {
	$checked['inflow'][$v]					= "checked";
}

//���
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name, nickname, email, phone, mobile, recommid, company ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if ($_GET['sstatus']!='') $where[] = "status='".$_GET['sstatus']."'";
if($_GET['slevel'] == '__null__'){
	$where[] = 'level not in ('.implode(',',array_keys($r_grp)).')';
}
else{
	if ($_GET['slevel']!='') $where[] = "level='".$_GET['slevel']."'";
}

if ($_GET['sunder14']!='') $where[] = "under14='".$_GET['sunder14']."'";

if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale between ".$_GET['ssum_sale'][0]." and ".$_GET['ssum_sale'][1];
else if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] == '') $where[] = "sum_sale >= ".$_GET['ssum_sale'][0];
else if ($_GET['ssum_sale'][0] == '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale <= ".$_GET['ssum_sale'][1];

if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] != '') $where[] = "emoney between ".$_GET['semoney'][0]." and ".$_GET['semoney'][1];
else if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] == '') $where[] = "emoney >= ".$_GET['semoney'][0];
else if ($_GET['semoney'][0] == '' && $_GET['semoney'][1] != '') $where[] = "emoney <= ".$_GET['semoney'][1];

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['slastdt'][0] && $_GET['slastdt'][1]) $where[] = "last_login between date_format(".$_GET['slastdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['slastdt'][1].",'%Y-%m-%d 23:59:59')";

if ($_GET['sex']) $where[] = "sex = '".$_GET['sex']."'";
if ($_GET['sage']!=''){
	$age[] = date('Y') + 1 - $_GET['sage'];
	$age[] = $age[0] - 9;
	foreach ($age as $k => $v) $age[$k] = substr($v,2,2);
	if ($_GET['sage'] == '60') $where[] = "right(birth_year,2) <= ".$age[1];
	else $where[] = "right(birth_year,2) between ".$age[1]." and ".$age[0];
}

if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login between ".$_GET['scnt_login'][0]." and ".$_GET['scnt_login'][1];
else if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] == '') $where[] = "cnt_login >= ".$_GET['scnt_login'][0];
else if ($_GET['scnt_login'][0] == '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login <= ".$_GET['scnt_login'][1];

if ($_GET['dormancy']){
	$dormancyDate	= date("Ymd",strtotime("-{$_GET['dormancy']} day"));
	$where[] = " date_format(last_login,'%Y%m%d') <= '".$dormancyDate."'";
}

if ($_GET['mailing']) $where[] = "mailling = '".$_GET['mailing']."'";
if ($_GET['smsyn']) $where[] = "sms = '".$_GET['smsyn']."'";

if( $_GET['birthtype'] ) $where[] = "calendar = '".$_GET['birthtype']."'";
if( $_GET['birthdate'][0] ){
	if( $_GET['birthdate'][1] ){
		if(strlen($_GET['birthdate'][0]) > 4 && strlen($_GET['birthdate'][1]) > 4) $where[] = "concat(birth_year, birth) between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
		else $where[] = "birth between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
	}else{
		$where[] = "birth = '".$_GET['birthdate'][0]."'";
	}
}

if( $_GET['marriyn'] ) $where[] = "marriyn = '".$_GET['marriyn']."'";
if( $_GET['marridate'][0] ){
	if( $_GET['marridate'][1] ){
		if(strlen($_GET['marridate'][0]) > 4 && strlen($_GET['marridate'][1]) > 4) $where[] = "marridate between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
		else $where[] = "substring(marridate,5,4) between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
	}else{
		$where[] = "substring(marridate,5,4) = '".$_GET['marridate'][0]."'";
	}
}

// ȸ������ ���� ���
if(is_array($_GET['inflow'])) foreach($_GET['inflow'] as $v) {
	if($inflow_where) $inflow_where .= " OR ";
	if($v) $inflow_where .= "inflow = '$v'";
}
if($inflow_where) $where[] = $inflow_where;

//���ο��� ������ SMS Ȯ�ο�
if ($_GET['mobileYN'] == "y") $where[] = "mobile != ''";

$where[] = "m_id != 'godomall'";
$where[] = $dormant->getToBeMemberWhere();

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table, $where, $orderby);
$pg->exec();

$res = $db->query($pg->query);
?>
<div class="title title_top">�޸� ��ȯ ���� ȸ�� ����Ʈ<span>�޸�ȸ������ ��ȯ ������ ȸ���� Ȯ���Ͻ� �� �ֽ��ϴ�.<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=26');"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<form>
<?php
//ȸ���˻���
include "../member/_listForm.php";
?>

<div class="button_top" style="margin-bottom:30px;"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo ver8">
	<strong> �� �޸� ��ȯ ���� ȸ�� ����Ʈ (�˻���� <?php echo number_format($pg->recode['total']);?>�� / ��ü <?php echo number_format($total); ?>��)</strong>
	<br />
	�޸�ȸ�� ��ȯ  ���(���� �α������κ��� 335���� ����) ȸ�� ����Ʈ�Դϴ�.
	</td>
	<td align="right">
		<select name="page_num" onchange="this.form.submit();">
		<?php
		$r_pagenum = array(10, 20, 40, 60, 100);
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
<input type="hidden" id="mode" name="mode" value="" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<colgroup>
	<col width="5%" align="center" />
	<col width="11%" align="center" />
	<col width="11%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
	<col width="11%" align="center" />
	<col width="12%" align="center" />
	<col width="10%" align="center" />
	<col width="10%" align="center" />
</colgroup>
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">����</a></th>
	<th>�޸�ȸ�� ��ȯ ������</th>
	<th>�����α���</th>
	<th>���̵�</th>
	<th>�̸�</th>
	<th>�׷�</th>
	<th>������</th>
	<th>�̸���</th>
	<th>�޴�����ȣ</th>
	<th>��ȭ��ȣ</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<?
while ($data=$db->fetch($res)){
	$toDormantDate = '';
	if($data['last_login'] != '0000-00-00 00:00:00'){
		$toDormantDate = date("Y-m-d H:i:s", strtotime($data['last_login'] . " +1year"));
	}
?>
<tr height="40" align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?php echo $data['m_no']; ?>"></td>
	<td><?php echo $toDormantDate; ?></td>
	<td><?php echo $data['last_login']; ?></td>
	<td><span id="navig" name="navig" m_id="<?php echo $data['m_id']; ?>" m_no="<?php echo $data['m_no']; ?>"><font class="small1" style="color:#0074ba; font-weight: bold;"><?php echo $data['m_id']; ?></font></span></td>
	<td><span id="navig" name="navig" m_id="<?php echo $data['m_id']; ?>" m_no="<?php echo $data['m_no']; ?>"><font class="small1" style="color:#0074ba; font-weight: bold;"><?php echo $data['name']; ?></font></span></td>
	<td><?php echo $group[$data['level']]; ?></td>
	<td><?php echo number_format($data['emoney']); ?> ��</td>
	<td><?php echo $data['email']; ?></td>
	<td><?php echo $data['mobile']; ?></td>
	<td><?php echo $data['phone']; ?></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%" height="35" style="padding-left:13px">
		<div style="width: 225px;">������ <img src="../img/btn_dormantChange.gif" border="0" id="dormantAdmin" class="hand" style="vertical-align: bottom;" />&nbsp;<img src="../img/btn_dormantDelete.gif" border="0" id="dormantMemberToBeDelete" class="hand" style="vertical-align: bottom;" /></div>
	</td>
	<td width="60%" align="center"><font class="ver8"><?php echo $pg->page['navi']; ?></font></td>
	<td width="20%" align="right"></td>
</tr>
</table>

</form>

<?php include '../_footer.php'; ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	UNM.inner();

	var form = $("#dormantForm");

	//�޸�ȸ�� ��ȯ
	$("#dormantAdmin").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("ȸ���� �����Ͽ� �ּ���.");
			return;
		}
		if(confirm("������ ȸ���� �޸� ���¸� ��ȯ �Ͻðڽ��ϱ�?")){
			$("#mode").val("dormantAdmin");
			form.submit();
		}
		return;
	});
	//ȸ�� ����
	$("#dormantMemberToBeDelete").click(function(){
		if($("input[name='chk[]']:checkbox:checked").length < 1){
			alert("ȸ���� �����Ͽ� �ּ���.");
			return;
		}
		if(confirm("������ �޸�ȸ�������� �����Ͻðڽ��ϱ�?\n������ ȸ���� Ż��ó���˴ϴ�.")){
			$("#mode").val("dormantMemberToBeDelete");
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