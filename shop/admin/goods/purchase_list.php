<?
	$location = "����ó ���� > ����ó ����Ʈ";
	include "../_header.php";
	include "../../lib/page.class.php";
	@include "../../conf/config.purchase.php";
	if($purchaseSet['usePurchase'] != "Y") msg("[����ó ���� ��� ����] > [��ǰ ����ó ����]�� ���� �ϼ���.", -1);

	list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_PURCHASE); # �� ���ڵ��

	if( !$_GET['page_num'] ) $_GET['page_num'] = 10;
	$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����

	### �����Ҵ�
	$selected['skey'][$_GET['skey']]			= "selected";
	$selected['page_num'][$_GET['page_num']]	= "selected";
	$selected['sort'][$orderby]					= "selected";

	### ���
	$db_table = GD_PURCHASE;

	if( $_GET['sword'] ) {
		if($_GET['skey'] == "all") {
			$where[] = "CONCAT(comnm, ceonm, phone1, phone2) LIKE '%".$_GET['sword']."%'";
		}
		else if($_GET['skey'] == "phone") {
			$where[] = "CONCAT(phone1, phone2) LIKE '%".$_GET['sword']."%'";
		}
		else {
			$where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
		}
	}

	$pg = new Page($_GET['page'],$_GET['page_num']);
	$pg->setQuery($db_table,$where,"ordgrade DESC, ".$orderby);
	$pg->exec();
	$res = $db->query($pg->query);

	$qstr = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&sort=".$_GET['sort']."&page_num=".$_GET['page_num'];
?>
<script>
	function iciSelect( obj ) {
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
	}

	function delPurchase( f ) {
		if( !isChked( document.getElementsByName( 'chk[]' ) ) ) return;
		if( !confirm( '������ �Ͻðڽ��ϱ�?' ) ) return;
		f.target = "_self";
		f.mode.value = "pchs_del";
		f.action = "indb.purchase.php";
		f.submit();
	}
</script>

<div><form>
<div class="title title_top">����ó ����Ʈ<span>����Ͻ� ����ó ����Ʈ�� ��ȸ�ϰ� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=28')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>�˻���</td>
	<td>
		<select name="skey">
			<option value="comnm" <?=$selected['skey']['comnm']?>> ����ó�� </option>
			<option value="ceonm" <?=$selected['skey']['ceonm']?>> ��ǥ�� </option>
			<option value="phone" <?=$selected['skey']['phone']?>> ����ó </option>
		</select>
		<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
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
		<select name="sort" onchange="this.form.submit();">
			<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>����� ���ġ�</option>
			<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>����� ���ġ�</option>
			<option value="comnm desc" <?=$selected['sort']['comnm desc']?>>����ü�� ���ġ�</option>
			<option value="comnm asc" <?=$selected['sort']['comnm asc']?>>����ü�� ���ġ�</option>
			<option value="ceonm desc" <?=$selected['sort']['ceonm desc']?>>��ǥ�ڸ� ���ġ�</option>
			<option value="ceonm asc" <?=$selected['sort']['ceonm asc']?>>��ǥ�ڸ� ���ġ�</option>
		</select>&nbsp;
		<select name="page_num" onchange="this.form.submit();">
<?
	$r_pagenum = array( 10, 20, 40, 60, 100 );
	foreach( $r_pagenum as $v ) {
?>
			<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
<? } ?>
		</select>
	</td>
</tr>
</table>
</form></div>

<div><form name="pList" method="post">
<input type="hidden" name="mode" />
<input type="hidden" name="qstr" value="<?=$qstr?>" />
<input type="hidden" name="query" value="<?=substr( $pg->query, 0, strpos( $pg->query, "limit" ) )?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="9"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>����ó</th>
	<th>��ǥ��</th>
	<th>����ó</th>
	<th>�ֱ� �����</th>
	<th>��� ��ǰ</th>
	<th width="80">���� ���</th>
	<th width="80">���� �̷�</th>
</tr>
<tr><td class="rnd" colspan="9"></td></tr>
<?
	while($data=$db->fetch($res)) {
		$last_login = (substr($data['last_login'], 0, 10) != date("Y-m-d")) ? substr($data['last_login'], 0, 10) : "<font color=#7070B8>".substr($data['last_login'], 11)."</font>";

		if($data['comcd'] == "0000") { // �̵��
			list($data['count']) = $db->fetch("SELECT COUNT(G.goodsno) FROM gd_goods AS G LEFT JOIN ".GD_PURCHASE_GOODS." AS PG ON G.goodsno = PG.goodsno WHERE PG.pchsno IS NULL OR PG.pchsno = '".$data['pchsno']."'");
?>
<tr height="40" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><font class="small" color="#616161"><?=$data['comnm']?></font></td>
	<td>-</td>
	<td>-</td>
	<td>-</td>
	<td><font class="ver81" color="#616161"><?=$data['count']?></font></td>
	<td><a href="../goods/purchase_goods.php?pchsno=<?=$data['pchsno']?>"><img src="../img/i_add.gif" title="���� ����ϱ�" /></a></td>
	<td><a href="../goods/purchase_log.php?pchsno=<?=$data['pchsno']?>"><img src="../img/btn_viewbbs.gif" title="'<?=$data['comnm']?>' ���� �̷� ����" /></a></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<?
		}
		else { // �Ϲ� ����ó
			$data['count'] = $db->count_($db->query("SELECT sno FROM gd_goods_option WHERE pchsno = '".$data['pchsno']."' and go_is_deleted <> '1' GROUP BY goodsno, opt1, opt2"));
?>
<tr height="40" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><a href="purchase_info.php?mode=pchs_mod&pchsno=<?=$data['pchsno']?>&<?=$qstr?>&page=<?=$_GET['page']?>"><font class="small" color="#616161"><?=$data['comnm']?></font></a></td>
	<td><font class="small" color="#616161"><?=$data['ceonm']?></font></td>
	<td><font class="ver81" color="#616161"><?=$data['phone1']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'], 0, 10)?></font></td>
	<td><font class="ver81" color="#616161"><?=number_format($data['count'])?></font></td>
	<td><a href="../goods/purchase_goods.php?pchsno=<?=$data['pchsno']?>"><img src="../img/i_add.gif" title="'<?=$data['comnm']?>' ���� ����ϱ�" /></a></td>
	<td><a href="../goods/purchase_log.php?pchsno=<?=$data['pchsno']?>"><img src="../img/btn_viewbbs.gif" title="'<?=$data['comnm']?>' ���� �̷� ����" /></a></td>
</tr>
<tr><td colspan="9" class="rndline"></td></tr>
<?
		}
	}
?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td height="35" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>�� �� ����� ����ó�� ��ǰ ������ ���� �Ǳ� ������ ���� �Ͻ� �� �����ϴ�.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form></div>
<script>window.onload = function() { UNM.inner(); }</script>
<? include "../_footer.php"; ?>