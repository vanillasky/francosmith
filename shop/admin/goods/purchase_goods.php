<?
	$location = "����ó ���� > �԰� ��ǰ ���";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[����ó ���� ��� ����] > [��ǰ ����ó ����]�� ���� �ϼ���.", -1);

	$pchsno			= isset($_GET['pchsno'])		? $_GET['pchsno']		: "";		// ����ó ��ȣ
	$minQuantity	= isset($_GET['minQuantity'])	? $_GET['minQuantity']	: "";		// ��� �˻� (����)
	$cate			= isset($_GET['cate'])			? $_GET['cate']			: array();	// ī�װ�
	$price			= isset($_GET['price'])			? $_GET['price']		: array();	// ���԰�
	$skey			= isset($_GET['skey'])			? $_GET['skey']			: "";		// Ű���� �˻� �ʵ�
	$sword			= isset($_GET['sword'])			? $_GET['sword']		: "";		// Ű���� �˻� Ű����
	$page_num		= isset($_GET['page_num'])		? $_GET['page_num']		: 10;		// �� ������ �� ��µ� ��� ��
	$pchsdt			= isset($_GET['pchsdt'])		? $_GET['pchsdt']		: array();	// ������
	$sort			= isset($_GET['sort'])			? $_GET['sort']			: "";		// ���Ĺ��
	$page			= isset($_GET['page'])			? $_GET['page']			: 1;		// ���� ������
	$pchsDefType	= isset($_GET['pchsDefType'])	? $_GET['pchsDefType']	: "";		// �� ����ó �κп� �⺻���� �Է��� ���� ����ó �˻� �ʵ�
	$pchsDefVal		= isset($_GET['pchsDefVal'])	? $_GET['pchsDefVal']	: "";		// �� ����ó �κп� �⺻���� �Է��� ���� ����ó �˻� Ű����

	list($total) = $db->fetch("SELECT COUNT(*) FROM gd_goods_option WHERE go_is_deleted <> '1'");

	### �����Ҵ�
		$selected['page_num'][$page_num] = "selected";

	### ���
		$db_table = " gd_goods_option AS O
		LEFT JOIN gd_goods AS G
		ON O.goodsno = G.goodsno
		LEFT JOIN ".GD_PURCHASE." AS P
		ON O.pchsno = P.pchsno";

	### �˻�
	if($pchsno) {
		list($thisCode) = $db->fetch("SELECT comcd FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");

		if($thisCode == "0000") $where[] = "(O.pchsno = '$pchsno' OR O.pchsno = '')";
		else $where[] = "O.pchsno = '$pchsno'";
	}
	if($minQuantity) $where[] = "O.stock <= $minQuantity";
	if($sword) $where[] = "$skey LIKE '%$sword%'";
	if(!empty($cate)) {
		$category = array_notnull($cate);
		$category = $category[count($category) - 1];

		/// ī�װ��� �ִ� ��� ��� ���̺� ������
		if($category) {
			$addField .= ", L.category ";
			$db_table .= " LEFT JOIN ".GD_GOODS_LINK." AS L ON O.goodsno = L.goodsno";

			// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
			$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'O.sno');
			$where[]	= $whereArr['where'];
			$groupby	= $whereArr['group'];
		}
	}
	if($price[0] && $price[1]) {
		$where[] = "(O.goodsno, O.opt1, O.opt2) IN ( SELECT goodsno, opt1, opt2 FROM ".GD_PURCHASE_GOODS." WHERE p_price >= '".$price[0]."' AND p_price <= '".$price[1]."' GROUP BY goodsno, opt1, opt2 )";
	}
	if($pchsdt[0] && $pchsdt[1]) { // �԰���
		$where[] = "(O.goodsno, O.opt1, O.opt2) IN ( SELECT goodsno, opt1, opt2 FROM ".GD_PURCHASE_GOODS." WHERE pchsdt BETWEEN DATE_FORMAT( ".$pchsdt[0].", '%Y-%m-%d 00:00:00' ) AND DATE_FORMAT( ".$pchsdt[1].", '%Y-%m-%d 23:59:59' ) AND pchsdt != '' AND pchsdt != '0000-00-00 00:00:00' GROUP BY goodsno, opt1, opt2 )";
	}

	$where[] = "go_is_deleted <> '1'";

	$pg = new Page($page, $page_num);
	$pg->field = " O.sno, O.opt1, O.opt2, O.price, O.stock,
	G.goodsno, G.goodsnm, G.open,
	P.pchsno, P.comnm ".$addField;
	$pg->setQuery($db_table, $where, "O.goodsno DESC", $groupby);

	$pg->setTotal();
	$res = $db->query($pg->query);
	$pg->exec();

	$qstr = "pchsno=".$pchsno."&sort=".$sort."&page_num=".$page_num."&skey=".$skey."&sword=".$sword."&minQuantity=".$minQuantity."&page=".$page;
	if(count($price)) foreach($price as $k => $v) $qstr .= "&price[]=".$v;
	if(count($cate)) foreach($cate as $k => $v) $qstr .= "&cate[]=".$v;
	if(count($pchsdt)) foreach($pchsdt as $k => $v) $qstr .= "&pchsdt[]=".$v;

	$pchs = $db->fetch("SELECT * FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
?>

<script>
	function iciSelect(obj) {
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
	}

	function pchsInfoToggle() {
		if($('pchsInfo').style.display == "none") {
			$('pchsInfo').style.display = '';
			$('btnCode').src = '../img/ico_arrow_up.gif';
			$('codeLink').title = '����ó �� ���� �ݱ�';
		}
		else {
			$('pchsInfo').style.display = 'none';
			$('btnCode').src = '../img/ico_arrow_down.gif';
			$('codeLink').title = '����ó �� ���� ����';
		}
	}
</script>

<form>
<div class="title title_top">�԰� ��ǰ ���<span>������ ��ǰ�� �̷��� ��� �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=26')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb">
<col class="cellC" /><col style="padding-left:10px;" />
<tr>
	<td>����ó �˻�</td>
	<td>
		<select name="pchsno" id="pchsno" onchange="this.form.submit();">
			<option value="">����ó����</option>
<?
	$sql_pchs = "SELECT * FROM ".GD_PURCHASE." ORDER BY ordgrade DESC, comnm ASC";
	$rs_pchs = $db->query($sql_pchs);
	for($i = 0; $row_pchs = $db->fetch($rs_pchs); $i++) {
?>
			<option value="<?=$row_pchs['pchsno']?>"<?=($row_pchs['pchsno'] == $pchsno) ? "selected" : ""?>><?=$row_pchs['comnm']?></option>
<?
	}
?>
		</select>
		<a href="javascript:;" onclick="window.open('../goods/popup.purchase_find.php?ctrlType=url', 'purchaseSearchPop', 'width=640,height=450');"><img src="../img/purchase_find.gif" title="����ó �˻�" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>�з�����</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name="skey">
<? foreach ( array('G.goodsnm'=>'��ǰ��','G.goodsno'=>'������ȣ','G.goodscd'=>'��ǰ�ڵ�','G.keyword'=>'����˻���') as $k => $v) { ?>
		<option value="<?=$k?>" <?=($k == $skey) ? 'selected' : ''?>><?=$v?></option>
<? } ?>
	</select>
	<input type=text name="sword" class="lline" value="<?=$sword?>" class="line">
	</td>
</tr>
<tr>
	<td>���԰�</td>
	<td><font class="small" color="#444444">
		<input type=text name="price[]" value="<?=$price[0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
		<input type=text name="price[]" value="<?=$price[1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
	</td>
</tr>
<tr>
	<td>���</td>
	<td>
	<input type="text" name="minQuantity" class="line" value="<?=$minQuantity?>" style="width:50px;"> �� ���� (�Է°��� ������ ��ü ���ڵ带 ��ȸ�մϴ�)
	</td>
</tr>
<tr>
	<td>�ֱٻ�����</td>
	<td colspan="3">
	<input type=text name="pchsdt[]" value="<?=$pchsdt[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name="pchsdt[]" value="<?=$pchsdt[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
	<a href="javascript:setDate('pchsdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
	<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
	<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
	<a href="javascript:setDate('pchsdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
	<a href="javascript:setDate('pchsdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
	</td>
</tr>
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
<table width="100%">
<tr>
	<td class="pageInfo">
		�� <font class="ver8"><b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
		<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array(10, 20, 40, 60, 100);
	foreach ($r_pagenum as $v) {
?>
			<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
<? } ?>
		</select>
	</td>
</tr>
</table>
</form>

<form name="pList" method="post" action="../goods/indb.purchase.php">
<input type="hidden" name="mode" value="pchs_manager" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="page" value="<?=$page?>" />
<input type="hidden" name="query" value="<?=substr($pg->query, 0, strpos($pg->query, "limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>��ǰ��</th>
	<th>�ɼ�1</th>
	<th>�ɼ�2</th>
	<th>����</th>
	<th>����</th>
	<th>�ֱ� ������</th>
	<th>�ֱ� ���԰�</th>
	<th>���</th>
	<th width="100">������</th>
	<th width="100">���԰�</th>
	<th width="80">�԰�</th>
	<th>����ó</th>
	<th width="35"></th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<?
	// �� ����ó �κп� �⺻���� �� ����ó �˻�
	if($pchsDefType && $pchsDefVal) {
		list($pchsDefault['pchsno'], $pchsDefault['comnm']) = $db->fetch("SELECT pchsno, comnm FROM ".GD_PURCHASE." WHERE $pchsDefType = '$pchsDefVal'");
	}

	while($data=$db->fetch($res)) {
		$pchsData = $db->fetch("SELECT * FROM ".GD_PURCHASE_GOODS." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '".$data['opt1']."' AND opt2 = '".$data['opt2']."' ORDER BY pchsdt DESC LIMIT 0, 1");

		if(!$data['comnm']) {
			$data['pchsno'] = $pchsDefault['pchsno'];
			$data['comnm'] = $pchsDefault['comnm'];
		}
?>
<tr height="40" align="center" bgcolor="">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td align="left" title="<?=$data['goodsnm']?>"><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=strcut($data['goodsnm'], 30)?></a></td>
	<td><?=$data['opt1']?></td>
	<td><?=$data['opt2']?></td>
	<td><?=($data['price']) ? number_format($data['price']) : ""?></td>
	<td><img src="../img/icn_<?=$data['open']?>.gif" /></td>
	<td><?=$pchsData['pchsdt']?></td>
	<td><?=($pchsData['p_price']) ? number_format($pchsData['p_price']) : ""?></td>
	<td><?=$data['stock']?></td>
	<td><input type="text" name="pchsdt[]" id="pchsdt_<?=$data['pchsno']?>" size="8" onclick="calendar()" onkeydown="onlynumber()" class="line" readonly /></td>
	<td><input type="text" name="p_price[]" size="8" onkeydown="onlynumber()" class="line" /></td>
	<td><input type="text" name="p_stock[]" size="6" class="line" /></td>
	<td id="comnm_<?=$data['sno']?>"><?=$data['comnm']?></td>
	<td><a href="javascript:;" onclick="window.open('../goods/popup.purchase_find.php?ctrlType=goods&targetNo=<?=$data['sno']?>', 'purchaseSearchPop', 'width=640,height=450');"><img src="../img/<?=($data['comnm']) ? "i_change" : "i_regist"?>.gif" align="absmiddle" /></a></td>
</tr>
<input type="hidden" name="pchsno[]" id="pgno_<?=$data['sno']?>" value="<?=$data['pchsno']?>" />
<input type="hidden" name="pgno[]" value="<?=$pchsData['pgno']?>" />
<input type="hidden" name="sno[]" value="<?=$data['sno']?>" />
<tr><td colspan="14" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
<tr>
	<td height="35" align="center"><input type="image" src="../img/btn_regist.gif" style="border:0px;" /></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>
		�԰��� �����ϸ� ����� �ش� ������ �ݿ� �˴ϴ�.<br />
		������ ��ǰ�� ����ó�� ��ǰ�� ��� (-)���̳ʽ� �԰��� ��� �Ͻø� �˴ϴ�.<br />
		���� ����� ��0�� ���ϰ� �Ǵ� ��� (-)���̳ʽ� �԰��� �Է� �Ͻ� �� �����ϴ�.
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>
<script>window.onload = function(){ UNM.inner();};</script>
<? include "../_footer.php"; ?>
