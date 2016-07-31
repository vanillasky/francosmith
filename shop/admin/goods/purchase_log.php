<?
	// �⺻ ���� �� ��Ŭ���
	$location = "����ó ���� > ���� �̷� ��ȸ";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[����ó ���� ��� ����] > [��ǰ ����ó ����]�� ���� �ϼ���.", -1);

	// �Ķ���� ����
	$pchsno			= isset($_GET['pchsno'])		? $_GET['pchsno']		: "";				// ����ó ��ȣ
	$minQuantity	= isset($_GET['minQuantity'])	? $_GET['minQuantity']	: "";				// ��� (����)
	$cate			= isset($_GET['cate'])			? $_GET['cate']			: array();			// ī�װ�
	$price			= isset($_GET['price'])			? $_GET['price']		: array();			// ���԰�
	$skey			= isset($_GET['skey'])			? $_GET['skey']			: "";				// �˻� �ʵ�
	$sword			= isset($_GET['sword'])			? $_GET['sword']		: "";				// �˻� Ű����
	$pchsdt			= isset($_GET['pchsdt'])		? $_GET['pchsdt']		: array();			// ������
	$page_num		= isset($_GET['page_num'])		? $_GET['page_num']		: 10;				// �� �������� ��µ� �Խù� ��
	$sort			= isset($_GET['sort'])			? $_GET['sort']			: "pchsdt DESC";	// ���� ����
	$page			= isset($_GET['page'])			? $_GET['page']			: 1;				// ���� ������

	// �� ���ڵ��
	if($pchsno) list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE_GOODS." WHERE pchsno = '$pchsno'");
	else list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE_GOODS."");

	// �����Ҵ�
	$selected['page_num'][$page_num]	= "selected";
	$selected['sort'][$sort]			= "selected";

	// ���� ����
		// ���̺�
		$db_table = "".GD_PURCHASE_GOODS." AS PG
			LEFT JOIN gd_goods AS G
				ON PG.goodsno = G.goodsno
			LEFT JOIN ".GD_PURCHASE." AS P
				ON PG.pchsno = P.pchsno";

		// �˻�
		$where[] = "G.goodsnm != ''";
		$where[] = "G.goodsnm IS NOT NULL";
		if($pchsno)			$where[] = "PG.pchsno = '$pchsno'"; // ����ó
		if($sword)			$where[] = "$skey LIKE '%$sword%'"; // Ű����
		if($minQuantity) { // ��� : ����� �ִ� ��� ���̺� �߰�
			$db_table .= " LEFT JOIN ".GD_GOODS_OPTION." AS O ON PG.goodsno = O.goodsno AND PG.opt1 = O.opt1 AND PG.opt2 = O.opt2 and go_is_deleted <> '1'";
			$where[] = "O.stock <= $minQuantity";
		}
		if(!empty($cate)) { // ī�װ� : ī�װ��� �ִ� ��� ���̺� �߰�
			$category = array_notnull($cate);
			$category = $category[count($category) - 1];

			if($category) {
				$db_table .= " LEFT JOIN ".GD_GOODS_LINK." AS L ON PG.goodsno = L.goodsno";

				// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
				$whereArr	= getCategoryLinkQuery('L.category', $category, null, 'PG.pgno');
				$where[]	= $whereArr['where'];
				$groupby	= $whereArr['group'];
			}
		}
		if($price[0] && $price[1]) $where[] = "PG.p_price >= '".$price[0]."' AND PG.p_price <= '".$price[1]."'";
		if($pchsdt[0] && $pchsdt[1]) { // �԰���
			$where[] = "PG.pchsdt BETWEEN DATE_FORMAT(".$pchsdt[0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$pchsdt[1].",'%Y-%m-%d 23:59:59')";
		}

		// ������ �� ���� ����
		$pg = new Page($page, $page_num);
		$pg->field = " PG.pgno, PG.pchsno, PG.goodsno, PG.opt1, PG.opt2, PG.pchsdt, PG.p_price, PG.p_stock, G.goodsno, G.goodsnm, G.img_s, P.comnm, PG.goodsnm AS p_goodsnm, PG.img_s AS p_img_s ".$addField; // �ʵ� ����
		$pg->setQuery($db_table, $where, $sort, $groupby);

		$pg->setTotal();
		$res = $db->query($pg->query);
		$pg->exec();

		// ���ڰ� ����
		$qstr = "pchsno=".$pchsno."&sort=".$sort."&page_num=".$page_num."&skey=".$skey."&sword=".$sword."&minQuantity=".$minQuantity."&page=".$page;
		if(count($price)) foreach($price as $k => $v) $qstr .= "&price[]=".$v;
		if(count($cate)) foreach($cate as $k => $v) $qstr .= "&cate[]=".$v;
		if(count($pchsdt)) foreach($pchsdt as $k => $v) $qstr .= "&pchsdt[]=".$v;


		// ����ó�� ���õǾ� ������ ����ó ���� �о����
		$pchs = $db->fetch("SELECT * FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno'");
?>

<script>
	// ����ó ���ý� ����ó ���� â ���
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

	// �ٲ� ���� ������ �� ���
	function chkChangeVal(targetObj, stateObjID) {
		if(targetObj.value != targetObj.oVal) $(stateObjID	).value = "1";
		else document.getElementById('stateObjID').value = "2";
	}

	// ������ �� üũ
	function chkForm() {
		var ar_pchsdt = document.getElementsByName('p_pchsdt[]');
		var ar_price = document.getElementsByName('p_price[]');
		var ar_stock = document.getElementsByName('p_stock[]');
		var ar_checkChange = document.getElementsByName('checkChange[]');

		for(i = 0; i < ar_pchsdt.length; i++) {
			if((ar_pchsdt[i].value != ar_pchsdt[i].oVal) || (ar_price[i].value != ar_price[i].oVal) || (ar_stock[i].value != ar_stock[i].oVal)) {
				ar_checkChange[i].value = "1";
			}
			else ar_checkChange[i].value = "0";

			if(!ar_pchsdt[i].value) {
				alert("�԰����� �Է����ּ���.");
				ar_pchsdt[i].focus();
				return false;
			}

			if(!ar_price[i].value) {
				alert("���԰��� �Է����ּ���.");
				ar_price[i].focus();
				return false;
			}

			if(!ar_stock[i].value) {
				alert("�԰��� �Է����ּ���.");
				ar_stock[i].focus();
				return false;
			}
		}

		return true;
	}

	function chkMoveURL() {
		var ar_pchsdt = document.getElementsByName('p_pchsdt[]');
		var ar_price = document.getElementsByName('p_price[]');
		var ar_stock = document.getElementsByName('p_stock[]');
		var ar_checkChange = document.getElementsByName('checkChange[]');
		var chkCnt = 0;

		for(i = 0; i < ar_pchsdt.length; i++) {
			if((ar_pchsdt[i].value != ar_pchsdt[i].oVal) || (ar_price[i].value != ar_price[i].oVal) || (ar_stock[i].value != ar_stock[i].oVal)) {
				chkCnt = chkCnt + 1;
			}
		}

		if(chkCnt > 0) {
			if(!confirm('������ �����ϰ� [�԰� ��ǰ ���] �������� �̵��Ͻðڽ��ϱ�?')) return false;
		}
		else return true;
	}
</script>

<!-- Ÿ��Ʋ --><div class="title title_top">���� �̷� ��ȸ<span>����� ���� �̷��� ��ȸ �մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=25')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<!-- �˻� �� ���� S -->
<div><form method="get" action="<?=$_SERVER['PHP_SELF']?>">

	<!-- �˻� S -->
	<table class="tb">
	<col class="cellC" /><col style="padding-left:10px;<?=($pchsno) ? "width:250;" : ""?>" />
	<? if($pchsno) { ?><col class="cellC" /><col style="padding-left:10px;" /><? } ?>
	<tr>
		<td>����ó �˻�</td>
		<td>
			<select name="pchsno" id="pchsno" onchange="location.href='./purchase_log.php?pchsno=' + this.value;">
				<option value="">����ó����</option>
<?
	$sql_pchs = "SELECT * FROM ".GD_PURCHASE." ORDER BY comnm ASC";
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
<? if($pchsno) { ?>
		<td>��ü�ڵ�</td>
		<td><? if($pchs['comcd'] != "0000") { ?><a href="javascript:;" id="codeLink" title="����ó �� ���� ����" onclick="pchsInfoToggle()"><?=$pchs['comcd']; ?> <img src="../img/ico_arrow_down.gif" id="btnCode" align="absmiddle" /></a><? } else { echo $pchs['comcd']; } ?></td>
<? } ?>
	</tr>
	<tr>
		<td>�з�����</td>
		<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
	</tr>
	<tr>
		<td>�˻���</td>
		<td colspan="3">
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
		<td colspan="3">
		<input type="text" name="minQuantity" class="line" value="<?=$minQuantity?>" style="width:50px;"> �� ���� (�Է°��� ������ ��ü ���ڵ带 ��ȸ�մϴ�)
		</td>
	</tr>
	<tr>
		<td>�԰���</td>
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
	<!-- �˻� E -->

	<!-- ����ó ���� S -->
<? if($pchsno) { ?>
	<table class="tb" id="pchsInfo" style="margin-top:5px; display:none;">
		<col class="cellC" /><col style="padding-left:10px; width:250;" />
		<col class="cellC" /><col style="padding-left:10px; width:170;" />
		<col class="cellC" /><col style="padding-left:10px;" />
		<tr>
			<td>��ǥ�ڸ�</td>
			<td><?=$pchs['ceonm']?></td>
			<td>����ڹ�ȣ</td>
			<td colspan="3"><?=str_replace("-", " - ", $pchs['comno'])?></td>
		</tr>
		<tr>
			<td>�ּ�</td>
			<td colspan="5"><?=str_replace("-", " - ", $pchs['zipcode'])?> <?=$pchs['address']?> <?=$pchs['address_sub']?></td>
		</tr>
		<tr>
			<td>���¹�ȣ</td>
			<td><?=$pchs['accountno']?></td>
			<td>�����</td>
			<td><?=$pchs['banknm']?></td>
			<td>������</td>
			<td><?=$pchs['accountnm']?></td>
		</tr>
		<tr>
			<td>����ó1</td>
			<td><?=str_replace("-", " - ", $pchs['phone1'])?></td>
			<td>����ó2</td>
			<td colspan="3"><?=str_replace("-", " - ", $pchs['phone2'])?></td>
		</tr>
		<tr>
			<td>�޸�</td>
			<td colspan="5"><?=nl2br($pchs['memo'])?></td>
		</tr>
		<tr height="35">
			<td>�����</td>
			<td colspan="5"><?=$pchs['regdt']?></td>
		</tr>
	</table>
<? } ?>
	<!-- ����ó ���� E -->

	<!-- ��� ����, ����/��ϼ� ���� S -->
	<table width="100%">
	<tr>
		<td class="pageInfo">
			�� <font class="ver8"><b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
		</td>
		<td align="right">
			<select name="sort" onchange="this.form.submit();">
<?
	$r_pagenum = array("�԰��� ��" => "PG.pchsdt ASC", "�԰��� ��" => "PG.pchsdt DESC", "��ǰ�� ��" => "G.goodsnm ASC", "��ǰ�� ��" => "G.goodsnm DESC", "�԰��� ��" => "PG.pchsdt ASC", "�԰��� ��" => "PG.pchsdt DESC", "���԰� ��" => "PG.p_price ASC", "���԰� ��" => "PG.p_price DESC", "�԰� ��" => "PG.p_stock ASC", "�԰� ��" => "PG.p_stock DESC");
	foreach ($r_pagenum as $k => $v){
?>
				<option value="<?=$v?>" <?=$selected['sort'][$v]?>><?=$k?></option>
<?
	}
?>
			</select>
			<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array(10, 20, 40, 60, 100);
	foreach ($r_pagenum as $v){
?>
				<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
<?
	}
?>
			</select>
		</td>
	</tr>
	</table>
	<!-- ��� ����, ����/��ϼ� ���� E -->
</form></div>
<!-- �˻� �� ���� E -->

<!-- ��� ���� S -->
<div><form name="pList" method="post" action="../goods/indb.purchase.php" onsubmit="return chkForm()">
<input type="hidden" name="mode" value="pchs_log_modify" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="page" value="<?=$page?>" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th width="50">��ȣ</th>
	<th width="120">����ó</th>
	<th width="50"></th>
	<th>��ǰ��</th>
	<th>�ɼ�1</th>
	<th>�ɼ�2</th>
	<th width="70">�԰���</th>
	<th width="90">���԰�</th>
	<th width="60">�԰�</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>

<!-- ��� S -->
<?
	while($data = $db->fetch($res)) {
		if(!$data['img_s']) $data['img_s'] = $data['p_img_s'];
		if(!$data['goodsnm']) $data['goodsnm'] = $data['p_goodsnm'];
?>
<tr height="50" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><a href="purchase_info.php?mode=pchs_mod&pchsno=<?=$data['pchsno']?>"><font class="small" color="#616161"><?=$data['comnm']?></font></a></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=goodsimg($data['img_s'], 40, '', 1)?></a></td>
	<td align="left"><a href="../../goods/goods_view.php?goodsno=<?=$data['goodsno']?>" target="_blank"><?=$data['goodsnm']?></a></td>
	<td><?=$data['opt1']?></td>
	<td><?=$data['opt2']?></td>
	<td><input type="text" name="p_pchsdt[]" id="pchsdt_<?=$data['pgno']?>" size="8" class="line" oVal="<?=str_replace("-", "", $data['pchsdt'])?>" value="<?=str_replace("-", "", $data['pchsdt'])?>" onclick="calendar()" onkeydown="onlynumber()" /></td>
	<td><input type="text" name="p_price[]" size="8" class="line" onkeydown="onlynumber()" oVal="<?=$data['p_price']?>" value="<?=$data['p_price']?>" /> ��</td>
	<td><input type="text" name="p_stock[]" size="6" class="line" onkeydown="onlynumber()" oVal="<?=$data['p_stock']?>" value="<?=$data['p_stock']?>" /></td>
</tr>
<tr><td colspan="10" class="rndline"></td></tr>
<input type="hidden" name="pgno[]" value="<?=$data['pgno']?>" />
<input type="hidden" name="checkChange[]" value="0" />
<? } ?>
<!-- ��� E -->

</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
<tr>
	<td height="35" align="center"><input type="image" src="../img/btn_editall.gif" align="absmiddle" style="border:0px;" title="���� �̷� �ϰ�����" /></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>
		<b>[���� �̷� ��ȸ]</b>�� ������ �����̷��� ��ȸ�ϰų� ���� �Ͻ� �� �ֽ��ϴ�.<br />
		�԰��� �����ϸ� ����� �ش� ������ �ݿ� �˴ϴ�.<br />
		���� ����� ��0�� ���ϰ� �Ǵ� ��� (-)���̳ʽ� �԰��� �Է� �Ͻ� �� �����ϴ�.
	</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form></div>
<!-- ��� ���� E -->

<script>window.onload = function(){ UNM.inner();};</script>
<? include "../_footer.php"; ?>
