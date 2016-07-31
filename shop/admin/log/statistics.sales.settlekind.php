<?
$location = "������ > ����м� > �������ܺ� �������";
include "../_header.php";

$where = array();


// �˻� ����
	$_GET['regdt'][0] = $sdate_s = ($_GET['regdt'][0]) ? $_GET['regdt'][0] : date('Ymd',strtotime('-7 day'));
	$_GET['regdt'][1] = $sdate_e = ($_GET['regdt'][1]) ? $_GET['regdt'][1] : date('Ymd');

	if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
		msg('��ȸ�Ⱓ ������ �ִ� 1���� ���� ���մϴ�. �Ⱓ Ȯ���� �缳�� ���ּ���.',$_SERVER['PHP_SELF']);exit;
	}

	$sword = isset($_GET['sword']) ? $_GET['sword'] : '';
	if ($sword) {
		$where[] = $_GET['skey']." like '%$sword%'";
	}

	$brandnm = isset($_GET['brandnm']) ? $_GET['brandnm'] : '';
	if ($brandnm) {
		$where[] = " OI.brandnm = '$brandnm'";
	}
	$category = false;
	if ($_GET['cate']){
		$category = array_notnull($_GET['cate']);
		$category = $category[count($category)-1];
	}

	if (sizeof($_GET['settlekind']) < 1 || $_GET['settlekind']['all']) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	elseif (sizeof($_GET['settlekind']) === 6) {
		$_GET['settlekind'] = array();
		$_GET['settlekind']['all'] = 1;
	}
	else {
		$_tmp = array();
		foreach($_GET['settlekind'] as $k => $v) {
			if (!$v || $k == 'all') continue;

			$_tmp[] = " O.settlekind = '".$k."'";
		}

		if (!empty($_tmp)) $where[] = ' ('.implode(' OR ',$_tmp).') ';
	}

// sql
	if (empty($_GET['dtkind'])) $_GET['dtkind'] = $dtkind = 'cdt';
	else $dtkind = $_GET['dtkind'];


	$sub_query = "
	SELECT
		O.settlekind,
		O.ordno,					/* �ֹ��Ǽ� */
		O.emoney,			/* ������ ��� �ݾ� */
		(O.coupon) AS coupon_dc,	/* �������� �ݾ� */
		(O.memberdc) AS member_dc,	/* ȸ������ �ݾ� */
		(O.enuri) AS enuri_dc,	/* ���������� �ݾ� */
		(O.o_special_discount_amount) AS goods_dc,	/* ��ǰ���� �ݾ� */
		SUM(OI.price * OI.ea) AS goodsprice,			/* ��ǰ���� */
		O.prn_settleprice,		/* �����ݾ� */
		O.delivery,		/* �����ݾ��� ��ۺ� */
		SUM(OI.supply * OI.ea) AS sub_supply	/* ���Աݾ� */

	FROM ".GD_ORDER." AS O
	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno
	LEFT JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno
	";

	if ($category){
		$sub_query .= "
		LEFT JOIN ".GD_GOODS_LINK." AS LNK
		ON OI.goodsno=LNK.goodsno
		";

		// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
		$where[]	= getCategoryLinkQuery('LNK.category', $category, 'where');
	}

	$_param = array(
		$dtkind,
		Core::helper('Date')->min($sdate_s),
		Core::helper('Date')->max($sdate_e)
	);

	$where[] = vsprintf("O.%s between '%s' and '%s'", $_param);
	$where[] = "O.step2 < 40 AND O.step > 0 AND OI.istep < 40";

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno ';
	$sub_query .= ' ORDER BY NULL ';

	$query = "
	SELECT
		SUB.settlekind,
		COUNT(SUB.ordno) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* ������ ��� �ݾ� */
		SUM(SUB.coupon_dc) AS tot_coupon_dc,	/* �������� �ݾ� */
		SUM(SUB.member_dc) AS tot_member_dc,	/* ȸ������ �ݾ� */
		SUM(SUB.enuri_dc) AS tot_enuri_dc,	/* ���������� �ݾ� */
		SUM(SUB.goods_dc) AS tot_goods_dc,	/* ��ǰ���� �ݾ� */
		SUM(SUB.goodsprice) AS tot_price,			/* ��ǰ���� */
		SUM(SUB.prn_settleprice) AS tot_settle,		/* �����ݾ� */
		SUM(SUB.delivery) AS tot_delivery,		/* �����ݾ��� ��ۺ� */
		SUM(SUB.sub_supply) AS tot_supply	/* ���Աݾ� */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY SUB.settlekind ';


// ����
$rs = $db->query($query);
$rs_max = $db->count_($rs);
$total = $arRow = array();

while ($_row = $db->fetch($rs,1)) {

	$row['settlekind']	= $r_settlekind[$_row['settlekind']];
	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_coupon_dc'] = $_row['tot_coupon_dc'];
	$row['tot_member_dc'] = $_row['tot_member_dc'];
	$row['tot_enuri_dc'] = $_row['tot_enuri_dc'];
	$row['tot_goods_dc'] = $_row['tot_goods_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_settle']	= $_row['tot_settle'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];
	$row['tot_sales']	= $row['tot_settle'] - $row['tot_delivery'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;

}
$db->free($rs);
?>

<div class="title title_top">�������ܺ� ������� <span>�������ܺ� ������踦 ��ȸ/�м� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=27')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<script type="text/javascript">
function fnDownloadStatistics() {
	if (confirm('�˻��� ��� ������ �ٿ�ε� �Ͻðڽ��ϱ�?')) {
		var f = document.frmExcelQuery;
		if (f.query.value != '') f.submit();
	}
}
</script>

<form name="frmStatistics" id="frmStatistics" method=get>

	<table class=tb>
	<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
	<tr>
		<td>�Ⱓ����</td>
		<td colspan="3">

			<select name="dtkind">
				<option value="orddt" <?=$_GET['dtkind'] == 'orddt' ? 'selected' : ''?>>�ֹ���</option>
				<option value="cdt" <?=$_GET['dtkind'] == 'cdt' ? 'selected' : ''?>>�Ա���</option>
				<option value="ddt" <?=$_GET['dtkind'] == 'ddt' ? 'selected' : ''?>>�����</option>
				<option value="confirmdt" <?=$_GET['dtkind'] == 'confirmdt' ? 'selected' : ''?>>��ۿϷ���</option>
			</select>

			<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][0]?>" /> -
			<input type="text" name="regdt[]" onclick="calendar(event)" size="12" class="line" value="<?=$_GET['regdt'][1]?>"/>

			<a href="javascript:setDate('regdt[]',<?=date("Ymd",G_CONST_NOW)?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_today.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_week.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_month.gif" align="absmiddle"/></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month",G_CONST_NOW))?>,<?=date("Ymd",G_CONST_NOW)?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"/></a>
		</td>
	</tr>
	<tr>
		<td>�з�����</td>
		<td colspan="3">
			<script>new categoryBox('cate[]',4,'<?=$category?>');</script>
		</td>
	</tr>
	<tr>
		<td>��ǰ</td>
		<td>
			<select name=skey>
			<option value="OI.goodsnm" <?=$_GET['skey'] == 'OI.goodsnm' ? 'selected' : ''?>>��ǰ��
			<option value="OI.goodsno" <?=$_GET['skey'] == 'OI.goodsno' ? 'selected' : ''?>>������ȣ
			<option value="G.goodscd" <?=$_GET['skey'] == 'G.goodscd' ? 'selected' : ''?>>��ǰ�ڵ�
			</select>
			<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
		</td>
		<td>�귣��</td>
		<td>
			<select name=brandnm>
			<option value="">-- �귣�� ���� --
			<?
			$bRes = $db->query("select * from ".GD_GOODS_BRAND." order by sort");
			while ($tmp=$db->fetch($bRes)){
			?>
			<option value="<?=$tmp[brandnm]?>" <?=$_GET['brandnm'] == $tmp[brandnm] ? 'selected' : ''?> ><?=$tmp[brandnm]?>
			<? } ?>
			</select>	</td>
	</tr>
	<!--tr>
		<td>��������</td>
		<td colspan="3" class="noline">
			<label><input type="checkbox" name="settlekind[all]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['all'] ? 'checked' : ''?>>��ü</label>
			<label><input type="checkbox" name="settlekind[a]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['a'] ? 'checked' : ''?>>������</label>
			<label><input type="checkbox" name="settlekind[c]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['c'] ? 'checked' : ''?>>�ſ�ī��</label>
			<label><input type="checkbox" name="settlekind[o]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['o'] ? 'checked' : ''?>>������ü</label>
			<label><input type="checkbox" name="settlekind[v]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['v'] ? 'checked' : ''?>>�������</label>
			<label><input type="checkbox" name="settlekind[h]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['h'] ? 'checked' : ''?>>�ڵ���</label>
			<label><input type="checkbox" name="settlekind[d]" value=1 onClick="nsGodoFormHelper.magic_check(this);" <?=$_GET['settlekind']['d'] ? 'checked' : ''?>>��������</label>
		</td>
	</tr-->
	</table>

	<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>

</form>



<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=13></td></tr>
<tr class=rndbg>
	<th>��������</th>
	<th>����</th>
	<th>�Ǽ�</th>
	<th>����������</th>
	<th>ȸ������</th>
	<th>��������</th>
	<th>��ǰ����</th>
	<th>������</th>
	<th>�ֹ��ݾ�</th>
	<th>�����ݾ� (��ۺ�����)</th>
	<th>����ݾ� (��ۺ�����)</th>
	<th>���Աݾ�</th>
	<th>�Ǹ�����</th>
</tr>
<tr><td class=rnd colspan=13></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="cell1"><?=$row['settlekind']?></td>
	<td class="cell1 numeric ar"><?=round($row['payment_cnt'] / $total['payment_cnt'] * 100 * 100) / 100?>%</td>
	<td class="numeric ar"><?=number_format($row['payment_cnt'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_enuri_dc'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_settle'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_supply'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_earn'])?></td>
</tr>
<tr><td colspan=13 class=rndline></td></tr>
<? } ?>
<tr><td colspan=13 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th>�հ�</th>
	<th>100%</th>
	<td class="numeric ar"><?=number_format($total['payment_cnt'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_enuri_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_settle'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_earn'])?></td>
</tr>
</tfoot>
<tr><td colspan=13 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>
	<td width="" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>

<p />
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻��� ��賻���� ���� �ٿ�ε� �Ͻø� �������� ���ں� �� ������� ������ Ȯ�� �Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ���, �Ա���, �����, ��ۿϷ��� ���غ��� �Ⱓ������ �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����ó(����ó)���� Ŭ���ϸ� �ش� ����ó(����ó)�� ��ǰ���� �̷��� Ȯ�� �Ͻ� �� �ֽ��ϴ�</td></tr>
<tr><td height="8"></td></tr>
<tr><td><span class="def1">&nbsp;&nbsp;<b>��� ����Ʈ</span></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��¥�� Ŭ���ϸ� �ش� ������ �ð��뺰 ������踦 Ȯ�� �Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ ���� : �ֹ� �����ÿ� ���� ������ ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� : �ֹ� �����ÿ� ����� ȸ������, �������� ���� ���Ե� ���αݾ� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ��ݾ� : ������, ����, ��ۺ� ������� ���� ��ǰ�� �ֹ��ݾ� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ݾ� : ������, ����, ��ۺ� ����� ���� �����ݾ� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����ݾ� : ��ǰ �ֹ��ݾ׿��� ������, ����, ��ۺ� ���ܵ� �� ����ݾ� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���Աݾ� : ��ǰ�� ���Աݾ�(��ǰ��Ͻ� ���Աݾ��� ��Ȯ�� �Է��Ͽ��� �Ǹ������� Ȯ���� �� �ֽ��ϴ�.)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ����� : ����ݾ׿��� ���Աݾ��� ������ �ݾ� ����</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ�������  ī�������, ��۷�, VAT �� ��Ÿ ������ ���Ե��� ���� ��ü���⿡�� ��ǰ���� �ݾ��� ������ ��� �ݾ�����, ������(������)�ݾװ��� ���̰� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ý��� ����ȭ�� ����Ͽ� �Ϻ� �������� �ִ� 1�� ������ ������ �˻��Ͻð�, ������ ���Ϸ� �ٿ�ε� �Ͽ� Ȱ���Ͻñ⸦ ���� �帳�ϴ�.</td></tr>
<tr><td height="8"></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ��� �����ʹ� �ֹ���� �ݾװ� ���μ��θ�(e����)�� �ٸ� �Ǹ�ä���� �ֹ����� �ݾ��� ���ܵ� ����ڷ� �Դϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>

<form name="frmExcelQuery" id="frmExcelQuery" method="post" action="indb.excel.statistics.sales.settlekind.php" target="ifrmHidden">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">
</form>

<? include "../_footer.php"; ?>
