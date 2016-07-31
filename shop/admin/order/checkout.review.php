<?php
$location = '���̹�üũ�ƿ� 4.0 > ��ǰ������';
include '../_header.php';
include "../../lib/page.class.php";

// üũ�ƿ� 4.0 ����
	$checkout_message_schema = include "./_cfg.checkout.php";

// �⺻ ��
	$now = time();

	if (empty($_GET)) {
		$loc = $sess['m_id'].preg_replace('/[^a-zA-Z0-9]/','_',($_SERVER['PHP_SELF']));
		list($_data) = $db->fetch("SELECT `value` FROM gd_env WHERE `category` = 'form_helper' AND `name` = 'searchCondition'");
		$data = $_data ? unserialize($_data) : array();

		if (!empty($data[$loc])) {
			parse_str( iconv('utf-8','euc-kr',urldecode($data[$loc])),$_GET );
		}
		// �Ⱓ ����
		if (!empty($_GET['regdt'][0]) && !empty($_GET['regdt'][1])) {
			$gap = abs( strtotime($_GET['regdt'][1]) - strtotime($_GET['regdt'][0]) );
			$_GET['regdt'][0] = date('Ymd',$now - $gap);
			$_GET['regdt'][1] = date('Ymd',$now);
		}
		unset($loc,$data,$_data);
	}

// get �Ķ���� ó�� �� �⺻�� ����
	unset($_GET['x'],$_GET['y']);

	$_GET['sort']			= !empty($_GET['sort']) ? $_GET['sort'] : 'PR.PurchaseReviewId desc';		// ����
	$_GET['ProductOrderStatus']		= isset($_GET['ProductOrderStatus']) ? $_GET['ProductOrderStatus'] : -1;	// ó������
	$_GET['PaymentMeans']	= !empty($_GET['PaymentMeans']) ? $_GET['PaymentMeans'] : '';		// ��������
	$_GET['skey']			= !empty($_GET['skey']) ? $_GET['skey'] : '';					// �ֹ��˻� ����
	$_GET['sword']			= !empty($_GET['sword']) ? trim($_GET['sword']) : '';					// �ֹ��˻� Ű����
	$_GET['dtkind']			= !empty($_GET['dtkind']) ? $_GET['dtkind'] : 'OrderDate';				// ��¥ ����
	$_GET['regdt']			= !empty($_GET['regdt']) ? $_GET['regdt'] : array(date('Ymd',strtotime('-'.(int)$cfg['orderPeriod'].' day',$now)), date('Ymd',$now));					// ��¥
	$_GET['regdt_time']		= !empty($_GET['regdt_time']) ? $_GET['regdt_time'] : array(-1,-1);		// �ð�
	$_GET['sgkey']			= !empty($_GET['sgkey']) ? $_GET['sgkey'] : '';					// ��ǰ�˻� ����
	$_GET['sgword']			= !empty($_GET['sgword']) ? trim($_GET['sgword']) : '';				// ��ǰ�˻� Ű����

	$_GET['page']			= !empty($_GET['page']) ? $_GET['page'] : 1;						// ������
	$_GET['page_num']		= !empty($_GET['page_num']) ? $_GET['page_num'] : ($cfg['orderPageNum'] ? $cfg['orderPageNum'] : 20);	// �������� ���ڵ��

// �˻��� ����
	#0. �ʱ�ȭ
		$arWhere = array();

	#xx. ����¡ query ����
		$_paging_query = http_build_query($_GET);	// php5 �����Լ�. but! lib.func.php �ȿ� php4�� ����.

// ���� ����
	$db_table = "
	".GD_NAVERCHECKOUT_PURCHASEREVIEW." AS PR

	INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
	ON PR.ProductOrderID = PO.ProductOrderID
	";

	$orderby = $_GET['sort'];

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->vars['page']= $_paging_query;

	$pg->field = "
		PR.*,
		PO.OrderID
	";
	$pg->setQuery($db_table,$arWhere,$orderby);
	$pg->exec();
	$rs = $db->query($pg->query);
?>
<script type="text/javascript" src="./checkout.js"></script>

<div class="title title_top">��ǰ������ <span>�� ��ǰ�� �������� Ȯ��/���� �ϴ� ������ �Դϴ�.</span></div>

<form name="frmSearch" id="frmSearch" method="get" action="">

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><span class="small1">�ֹ��˻�</span></td>
		<td>
			<select name="ProductOrderStatus">
				<option value="-1"> = �ֹ����� = </option>
				<? foreach ($checkout_message_schema['extra_productOrderStatusType'] as $k=>$v) { ?>
				<option value="<?=$k?>" <?=$_GET['ProductOrderStatus'] == $k ? 'selected' : ''?>><?=$k?></option>
				<? } ?>
			</select>

			<select name="payMeansClassType">
				<option value=""> = �������� = </option>
				<? foreach ($checkout_message_schema['payMeansClassType'] as $k=>$v) { ?>
				<? if ($k == 'p') continue; ?>
				<option value="<?=$k?>" <?=$_GET['payMeansClassType'] == $k ? 'selected' : ''?>><?=$v?></option>
				<? } ?>
			</select>

			<select name="skey">
				<option value="all"> = ���հ˻� = </option>
				<option value="OrderID"			<?=($_GET['skey'] == 'OrderID') ? 'selected' : ''?>			>�ֹ���ȣ</option>
				<option value="ProductOrderID"		<?=($_GET['skey'] == 'ProductOrderID') ? 'selected' : ''?>		>��ǰ�ֹ���ȣ</option>
				<option value="MallMemberID"			<?=($_GET['skey'] == 'MallMemberID') ? 'selected' : ''?>			>�ֹ���ID</option>
				<option value="OrdererName"			<?=($_GET['skey'] == 'OrdererName') ? 'selected' : ''?>			>�ֹ��ڸ�</option>
				<option value="OrdererTel"		<?=($_GET['skey'] == 'OrdererTel') ? 'selected' : ''?>		>�ֹ��ڿ���ó</option>	OrdererTel1	OrdererTel2
			</select>

			<input type="text" name="sword" value="<?=htmlspecialchars($_GET['sword'])?>" class="line" />

		</td>
	</tr>
	<tr>
		<td><span class="small1">ó������</span></td>
		<td>
			<select name="dtkind">
				<option value="OrderDate"		<?=($_GET['dtkind'] == 'OrderDate' ? 'selected' : '')?>		>�ֹ���</option>
				<option value="PaymentDate"			<?=($_GET['dtkind'] == 'PaymentDate' ? 'selected' : '')?>			>�Ա���</option>
				<option value="SendDate"			<?=($_GET['dtkind'] == 'SendDate' ? 'selected' : '')?>			>�����</option>
				<option value="DeliveredDate"	<?=($_GET['dtkind'] == 'DeliveredDate' ? 'selected' : '')?>	>��ۿϷ���</option>
			</select>

			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" size="12" class="line"/>

			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][0] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>��</option>
			<? } ?>
			</select>
			-
			<input type="text" name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" size="12" class="line"/>
			<select name="regdt_time[]">
			<option value="-1">---</option>
			<? for ($i=0;$i<24;$i++) {?>
			<option value="<?=$i?>" <?=($_GET['regdt_time'][1] === $i ? 'selected' : '')?>><?=sprintf('%02d',$i)?>��</option>
			<? } ?>
			</select>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>);"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"/></a>

		</td>
	</tr>
	<tr>
		<td><span class="small1">��ǰ�˻�</span></td>
		<td>
			<select name="sgkey">
				<option value="ProductName"	<?=($_GET['sgkey'] == 'ProductName') ? 'selected' : ''?>	>��ǰ��</option>
			</select>
			<input type="text" name="sgword" value="<?=htmlspecialchars($_GET['sgword'])?>" class="line"/>
		</td>
	</tr>

	</table>

	<div class="button_top">
	<table width="100%">
	<tr>
		<td width="35%" align="left">&nbsp;</td>
		<td width="30%" align="center"><input type="image" src="../img/btn_search2.gif"/></td>
		<td width="35%" align="right">

		<a href="javascript:void(0);" onClick="nsGodoFormHelper.save();"><img src="../img/btn_search_form_save.gif"></a>
		<a href="javascript:void(0);" onClick="nsGodoFormHelper.reset();"><img src="../img/btn_search_form_reset.gif"></a>

		</td>
	</tr>
	</table>
	</div>

</form>

<div style="padding-top:15px"></div>

<form name="frmNaverCheckout" method="post" target="processLayerForm">
<input type="hidden" name="mode" value="">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col width="40"><col width="100"><col width="140"><col><col width="50">
<tr><td class="rnd" colspan="20"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�ۼ�(����)�Ͻ�</th>
	<th>��ǰ�ֹ���ȣ</th>
	<th>������</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="20"></td></tr>
<?
while ($row = $db->fetch($rs,1)) {
		$view_url = 'checkout.view.php?OrderID='.$row['OrderID'].'&ProductOrderIDList='.$row['ProductOrderID'];
?>
<tr height="25" bgcolor="#ffffff" bg="#ffffff" align="center">
	<td><span class="ver8" style="color:#616161"><?=$pg->idx--?></span></td>
	<td><span class="ver81" style="color:#616161"><?=substr($row['CreateYmdt'],0,-3)?></span></td>
	<td align="left">
		<a href="<?=$view_url?>"><span class="ver81" style="color:#0074BA"><b><?=$row['ProductOrderID']?></b></span></a>
		<a href="javascript:popup('<?=$view_url?>&win=1',800,600)"><img src="../img/btn_newwindow.gif" border=0 align="absmiddle"/></a>
	</td>
	<td align="left">
		<span class="small1" style="color:#444444"><?=$row['Title']?></span>
	</td>
	<td><span class="small1" style="color:#616161"><?=$checkout_message_schema['PurchaseReviewScore'][$row['PurchaseReviewScore']]?></span></td>
</tr>
<tr><td colspan="20" bgcolor="#E4E4E4"></td></tr>
<? } ?>
</table>

</form>

<div class=pageNavi align=center>
	<font class=ver8><?=$pg->page[navi]?></font>
</div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>�ֹ���ȣ�� Ŭ���Ͻø� �ش� �ֹ��� �������� Ȯ���Ͻ� �� ������, �ֹ� ��ǰ�� �κ�ó���� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"/>���ſϷ�� �ֹ����� �� ��ǰ���� ���� ������ ������ Ȯ��, �����մϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
