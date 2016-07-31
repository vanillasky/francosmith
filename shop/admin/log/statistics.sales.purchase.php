<?
$location = "������ > ����м� > ����ó(����ó)�� �������";
include "../_header.php";
include '../../lib/ofc/php-ofc-library/open-flash-chart.php';

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
		PCH.comnm, PCH.pchsno,
		O.ordno,					/* �ֹ��Ǽ� */
		O.emoney,			/* ������ ��� �ݾ� */
		(O.coupon) AS coupon_dc,	/* �������� �ݾ� */
		(O.memberdc) AS member_dc,	/* ȸ������ �ݾ� */
		(O.enuri) AS enuri_dc,	/* ���������� �ݾ� */
		(O.o_special_discount_amount) AS goods_dc,	/* ��ǰ���� �ݾ� */
		SUM(OI.price * OI.ea) AS goodsprice,			/* ��ǰ���� */
		O.delivery,		/* �����ݾ��� ��ۺ� */
		SUM(OI.supply * OI.ea) AS sub_supply	/* ���Աݾ� */

	FROM ".GD_ORDER." AS O

	INNER JOIN ".GD_ORDER_ITEM." AS OI
	ON O.ordno = OI.ordno

	INNER JOIN ".GD_GOODS." AS G
	ON OI.goodsno = G.goodsno

	LEFT JOIN ".GD_PURCHASE_GOODS." AS PCHG
	ON G.goodsno = PCHG.goodsno

	LEFT JOIN ".GD_PURCHASE." AS PCH
	ON PCHG.pchsno = PCH.pchsno
	";

	if ($category){
		$sub_query .= "
		INNER JOIN ".GD_GOODS_LINK." AS LNK
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
	//$where[] = "PCH.pchsno IS NOT NULL ";

	$sub_query .= ' WHERE '.implode(' AND ', $where);
	$sub_query .= ' GROUP BY O.ordno, PCH.pchsno ';

	$query = "
	SELECT
		IFNULL(SUB.comnm, '�̸�����') AS comnm,
		IFNULL(SUB.pchsno, 0) AS pchsno,
		COUNT(SUB.ordno) AS cnt,
		SUM(SUB.emoney) AS tot_emoney,			/* ������ ��� �ݾ� */
		SUM(SUB.coupon_dc) AS tot_coupon_dc,	/* �������� �ݾ� */
		SUM(SUB.member_dc) AS tot_member_dc,	/* ȸ������ �ݾ� */
		SUM(SUB.enuri_dc) AS tot_enuri_dc,	/* ���������� �ݾ� */
		SUM(SUB.goods_dc) AS tot_goods_dc,	/* ��ǰ���� �ݾ� */
		SUM(SUB.goodsprice) AS tot_price,			/* ��ǰ���� */
		SUM(SUB.delivery) AS tot_delivery,		/* �����ݾ��� ��ۺ� */
		SUM(SUB.sub_supply) AS tot_supply	/* ���Աݾ� */

	FROM
	(
		".$sub_query."

	) AS SUB
	";
	$query .= ' GROUP BY SUB.pchsno ';
	$query .= ' ORDER BY SUB.comnm ';

// ����
$rs = $db->query($query);
$rs_max = $db->count_($rs);
$total = $arRow = $chart = array();

while ($_row = $db->fetch($rs,1)) {

	$row['comnm']		= $_row['comnm'];
	$row['pchsno']	= $_row['pchsno'];
	$row['payment_cnt']	= $_row['cnt'];
	$row['tot_emoney']	= $_row['tot_emoney'];
	$row['tot_coupon_dc']		= $_row['tot_coupon_dc'];
	$row['tot_member_dc']		= $_row['tot_member_dc'];
	$row['tot_enuri_dc']		= $_row['tot_enuri_dc'];
	$row['tot_goods_dc']		= $_row['tot_goods_dc'];
	$row['tot_price']	= $_row['tot_price'];
	$row['tot_delivery']		= $_row['tot_delivery'];
	$row['tot_supply']	= $_row['tot_supply'];

	$row['tot_sales']	= $row['tot_price'] - $row['tot_dc'] - $row['tot_emoney'];
	$row['tot_earn']	= $row['tot_sales'] - $row['tot_supply'];

	$total = get_total($total, $row);

	$arRow[] = $row;

	// ��Ʈ ������
	$chart['data'][1][] = (int)$row['tot_sales'];
	$chart['data'][2][] = (int)$row['tot_supply'];
	$chart['data'][3][] = (int)$row['tot_earn'];

	$_m = max( array($row['tot_sales'],$row['tot_supply'],$row['tot_earn']));
	$chart['y_max'] = ($chart['y_max'] > $_m) ? (int)$chart['y_max'] : $_m;

	$chart['x_label'][] = iconv('EUC-KR','UTF-8',$row['comnm']);

}
$db->free($rs);
//  �׷���

if (!empty($chart)) {

	$chart['color'][1] = '#A6A6A6';
	$chart['color'][2] = '#92D050';
	$chart['color'][3] = '#FF0000';

	$chart['Key'][1] = '����ݾ�';
	$chart['Key'][2] = '���Աݾ�';
	$chart['Key'][3] = '�Ǹ�����';

	$ofc = new open_flash_chart();

	foreach($chart['data'] as $k => $data) {
		${'data'} = $data;
		${'bar'.$k} = new bar();
		${'bar'.$k}->colour( $chart['color'][$k] );
		${'bar'.$k}->set_values( $data );
		${'bar'.$k}->key( iconv('euc-kr','utf-8',$chart['Key'][$k]) , 12);

		$ofc->add_element( ${'bar'.$k} );
	}

	$tmp = pow(10,strlen($chart['y_max']) - 1);
	$chart['y_max'] =  ceil($chart['y_max'] / $tmp) * $tmp;

	$y = new y_axis();
	$y->set_range(0, $chart['y_max']);
	$y->set_colours( '#595D63', '#DEDEDE');

	$x = new x_axis();
	$x->set_colours( '#595D63', '#DEDEDE');

	$x_labels = new x_axis_labels();
	$x_labels->set_colour( '#595D63' );
	$x_labels->set_steps( ceil($rs_max / 11) );
	$x_labels->set_labels( $chart['x_label'] );
	$x_labels->set_size( 12 );

	$x->set_labels( $x_labels );

	$ofc->set_x_axis( $x );
	$ofc->set_y_axis( $y );
	$ofc->set_bg_colour( '#FFFFFF' );

	$chart_data_1 = $ofc->toPrettyString();
}
else {
	$chart_data_1 = 'false';
}
?>
<script type="text/javascript" src="common.js"></script>
<script type="text/javascript">

var chart_data_1 = <?=$chart_data_1?>;

function open_flash_chart_data() {
		if (chart_data_1 === false) {
			$('_chart').setStyle({'display':'none'});
			chart_data_1 = {};
		}

		return Object.toJSON(chart_data_1);
}

function fnDisplayChart(type, name) {

	var tab = $('el-chart-tab').childElements()[1];

	// �� �ؽ�Ʈ ����
	if (type === false) {
		$('chart-tab-button-overview').src = "../img/teb_graph.gif";
		tab.setStyle({display:'none'});
		var ofc = findSWF("my_chart");
		ofc.load( Object.toJSON(chart_data_1) );
	}
	else {
		$('chart-tab-button-overview').src = "../img/teb_graph_off.gif";
		tab.setStyle({display:'block'});
		tab.update(name);
	}

	var param = '?';
	var el, pair;


	$A( $('frmStatistics').serialize().split('&') ).each(function(el){

		el = decodeURIComponent(el);
		pair = el.split('=');
		param += '&'+ pair[0] +'='+ pair[1];

	});

	if (type) param += '&pchno='+type;

	var ajax = new Ajax.Request( "./indb.statistics.sales.purchase.chart.data.php",
	{
		method: "post",
		parameters: param,
		onComplete: function (response) {

			var json = response.responseText.evalJSON(true);
			var ofc = findSWF("my_chart");
			ofc.load( Object.toJSON(json) );
			$('_chart_loading').setStyle({display : 'none'});
		},
		onCreate : function(){
			$('_chart_loading').setStyle({display : 'block'});
		}
	});

}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window[movieName];
  } else {
    return document[movieName];
  }
}

function fnToggleChart() {
	var el = $('_chart').toggle();
	$('chart-toggle-button').src = el.getStyle('display') == 'none' ? '../img/btn_up.gif' : '../img/btn_down.gif';
}
</script>

<script type="text/javascript">
function fnToggleSettleKind() {
	var i=0;
	var _check = true;

	$$('input[name^=settlekind]').each(function(chk){

		if (i === 0) {
			_check = (chk.checked == true) ? false : true;
		}
		chk.checked = _check;
		i++;
	});
}

function fnDetailStatistic(d) {

	var param = '?';
	var el, pair;
	$A( $('frmStatistics').serialize().split('&') ).each(function(el){
		el = decodeURIComponent(el);
		pair = el.split('=');
		param += '&'+ pair[0] +'='+ pair[1];

	});
	param += '&pchno='+d;

	popupLayer('./statistics.sales.purchase.detail.php'+param,750,450);

}
</script>

<div class="title title_top">����ó(����ó)�� ������� <span>����ó(����ó)�� ������踦 ��ȸ/�м� �� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=26')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

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
	<tr>
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
	</tr>
	</table>

	<div class=button_top><input type=image src="../img/btn_search_s.gif"></div>

</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="height:26px;background:url(../img/teb_bg.gif) repeat-x top left;">
<tr>
	<td id="el-chart-tab">
		<div style="float:left;"><a href="javascript:void(0);" onClick="fnDisplayChart(false)"><img src="../img/teb_graph.gif" id="chart-tab-button-overview"></a></div>
		<div style="display:none;float:left;background:#fff;height:26px;border-top:1px solid #DBDBDB;border-right:1px solid #DBDBDB;line-height:26px;padding:0 10px 0 10px;font-weight:bold;color:#167BBB;">�ƽιٸ�</div>
	</td>
	<td align="right"><a href="javascript:void(0);" onClick="fnToggleChart();"><img src="../img/btn_down.gif" id="chart-toggle-button"></a></td>
</tr>
</table>


<!-- �׷��� -->
<div id="_chart" style="margin-top:20px;">
<div id="_chart_loading" style="display:none;position:absolute;background:#fff;width:100%;height:250px;text-align:center;"><img src="../img/loading.gif" style="vertical-align:middle;"><img src="../img/blank.gif" width="1" height="250" style="vertical-align:middle;"></div>
<script type="text/javascript">
var param = {
	'id':'my_chart',
	'width':'100%',
	'height':'250'
};
flash_chart(param);
</script>
</div>
<!-- �׷��� -->

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0 class="statistics-list">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th>�׷���</th>
	<th>����ó</th>
	<th>�Ǽ�</th>
	<th>����������</th>
	<th>ȸ������</th>
	<th>��������</th>
	<th>��ǰ����</th>
	<th>������</th>
	<th>�ֹ��ݾ�</th>
	<!--th>�����ݾ� (��ۺ�����)</th-->
	<th>����ݾ�</th>
	<th>���Աݾ�</th>
	<th>�Ǹ�����</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>

<?
for ($i=0,$m=sizeof($arRow);$i<$m;$i++) {
	$row = $arRow[$i];
?>
<tr height=25>
	<td class="cell1"><a href="javascript:void(0);" onClick="fnDisplayChart('<?=$row['pchsno']?>','<?=$row['comnm']?>');"><img src="../img/btn_graph.gif"></td>
	<td class="cell1"><a href="javascript:void(0);" onClick="fnDetailStatistic('<?=$row['pchsno']?>');"><?=$row['comnm']?></a></td>
	<td class="numeric ar"><?=number_format($row['payment_cnt'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_enuri_dc'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_price'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($row['tot_supply'])?></td>
	<td class="cell1 numeric highlight  ar"><?=number_format($row['tot_earn'])?></td>
</tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
<tr><td colspan=12 bgcolor=A3A3A3></td></tr>
<tfoot>
<tr>
	<th colspan="2">�հ�</th>
	<td class="numeric ar"><?=number_format($total['payment_cnt'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_emoney'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_member_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_coupon_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_goods_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_enuri_dc'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_price'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_sales'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_supply'])?></td>
	<td class="numeric highlight  ar"><?=number_format($total['tot_earn'])?></td>
</tr>
</tfoot>
<tr><td colspan=12 class=rndline></td></tr>
</table>

<table width="100%" style="margin-top:10px;">
<tr>
	<td width="" align="right"><a href="javascript:void(0);" onClick="fnDownloadStatistics()"><img src="../img/btn_download_s.gif"></a></td>
</tr>
</table>

<p />
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ���, �Ա���, �����, ��ۿϷ��� ���غ��� �Ⱓ������ �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����ó(����ó)���� Ŭ���ϸ� �ش� ����ó(����ó)�� ��ǰ�� �����̷��� Ȯ�� �Ͻ� �� �ֽ��ϴ�</td></tr>
<tr><td height="8"></td></tr>
<tr><td><span class="def1">&nbsp;&nbsp;<b>��� ����Ʈ</span></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ ���� : �ֹ� �����ÿ� ���� ������ ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���� : �ֹ� �����ÿ� ����� ȸ������, �������� ���� ���Ե� ���αݾ� ����</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�ֹ��ݾ� : ������, ����, ��ۺ� ������� ���� ��ǰ�� �ֹ��ݾ� ����</td></tr>
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

<form name="frmExcelQuery" id="frmExcelQuery" method="post" action="indb.excel.statistics.sales.purchase.php" target="ifrmHidden">
<input type="hidden" name="query" value="<?=base64_encode($query)?>">
</form>

<? include "../_footer.php"; ?>
