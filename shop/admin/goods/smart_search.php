<?
$location = "��ǰ���� > SMART �˻� �׸�����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.php";

if(get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$sort = ($_GET['sort']) ? $_GET['sort'] : "themenm ASC";
$qstr /* querystring*/ = "skey=".$_GET['skey']."&sword=".$_GET['sword']."&cate[]=".$_GET['cate'][0]."&cate[]=".$_GET['cate'][1]."&cate[]=".$_GET['cate'][2]."&cate[]=".$_GET['cate'][3]."&regdt[]=".$_GET['regdt'][0]."&regdt[]=".$_GET['regdt'][1]."&page=".$_GET['page']."&sort=".$_GET['sort'];

### ���� ����
$_GET['sword'] = trim($_GET['sword']);
if(!$cfg['smartSearch_useyn']) $cfg['smartSearch_useyn'] = "n";
$checked['smartSearch_useyn'][$cfg['smartSearch_useyn']] = " checked";

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_GOODS_SMART_SEARCH);

if(!$_GET['page_num']) $_GET['page_num'] = 20;
$selected['skey'][$_GET['skey']] = "selected";

if ($_GET['cate']){
	$category = array_notnull($_GET['cate']);
	$category = $category[count($category)-1];
}

$db_table = GD_GOODS_SMART_SEARCH." AS a LEFT JOIN ".GD_CATEGORY." AS b ON b.themeno = a.sno";

if ($category){
	$where[] = sprintf(" b.category like '%s%%'", $category);
}
if ($_GET['sword']) {
	if($_GET['skey'] == 'all') {
		$where[] = " (themenm LIKE '%".$_GET['sword']."%' OR catnm LIKE '%".$_GET['sword']."%') ";
	}
	else {
		$where[] = $_GET['skey']." LIKE '%".$_GET['sword']."%'";
	}
}
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].", '%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].", '%Y-%m-%d 23:59:59')";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = "DISTINCT a.sno, a.themenm, a.basic, a.updatedt, a.regdt";
$pg->setQuery($db_table, $where, "basic ASC, $sort");
$pg->exec();

$res = $db->query($pg->query);

//���� ī�װ� ����
$theme = array();
$theme_query = $db->query("SELECT themeno, COUNT(themeno) cnt FROM ".GD_CATEGORY." GROUP BY themeno;");
while($theme_cnt = $db->fetch($theme_query)) {
	if($theme_cnt['themeno'] != '') $theme[$theme_cnt['themeno']] = $theme_cnt['cnt'];
}

// �⺻ �׸� ���� ����Ʈ
$sql = "SELECT sno, themenm, basic FROM ".GD_GOODS_SMART_SEARCH." ORDER BY basic ASC, themenm ASC";
$rs = $db->query($sql);

$queryString = "sort=".$_GET['sort']."&skey=".$_GET['skey']."&sword=".$_GET['sword']."&cate[]=".$_GET['cate'][0]."&cate[]=".$_GET['cate'][1]."&cate[]=".$_GET['cate'][2]."&cate[]=".$_GET['cate'][3]."&regdt[]=".$_GET['regdt'][0]."&regdt[]=".$_GET['regdt'][1];
?>

<script language="JavaScript">
	function sort(val) {
		$('sort').value = val;
		frmList.submit();
	}

	function setBasic(basicNo) {
		if(confirm("������ �׸��� �⺻�׸��� ���� �Ͻðڽ��ϱ�?")) location.href="../goods/indb.smart_search.php?mode=changeBasic&basic=" + basicNo + "&<?=$qstr?>";
	}

	window.onload = function() {
		cssRound('MSG01');
	}
</script>

<div class="title title_top">SMART �˻� ��뼳��<span>SMART�˻� ��뿩�θ� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=38')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<form name="setForm" method="post" action="indb.smart_search.php">
<input type="hidden" name="mode" id="mode" value="setOption" />
<input type="hidden" name="qstr" id="qstr" value="<?=$qstr?>" />
<table class="tb">
<col class="cellC" style="width:100px"><col class="cellL">
<col class="cellC"><col class="cellL">
<tr>
	<td>��� ����</td>
	<td class="noline">
		<table cellspacing="0" cellpadding="0" border="0">
		<tr valign="center">
			<td width="145">
				<input type="radio" name="smartSearch_useyn" id="uy" value="y" <?=$checked['smartSearch_useyn']['y']?> /><label for="uy">���</label>
				<input type="radio" name="smartSearch_useyn" id="un" value="n" <?=$checked['smartSearch_useyn']['n']?> /><label for="un">������</label>
			</td>
			<td>
				<span class="extext">��뿩�θ� �����մϴ�.<br />'������'���� ������ '��ǰ�з�[ī�װ�]����> ��� ī�װ��� SMART�˻� ����'�� ��Ȱ��ȭ �˴ϴ�.</span>
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>�⺻�׸� ����</td>
	<td>
		<select name="basic" id="basic">
		<? while($data = $db->fetch($rs)) { ?>
			<option value="<?=$data['sno']?>"<?=$data['basic'] == 'y' ? ' selected' : ''?>><?=htmlspecialchars($data['themenm']).($data['basic'] == 'y' ? '(�⺻�׸�)' : '')?></option>
		<? } ?>
		</select>
		<span class="extext" style="margin-left:30px;">'��ǰ�з�[ī�װ�]���� > SMART�˻�����' ���� ������ �׸������� ������ ������ �⺻�׸��� ����˴ϴ�.</span>
	</td>
</tr>
</table>
<div class="button_top" style="padding-bottom:15px"><input type="image" src="../img/btn_save3.gif" /></a></div>
</form>

<div class="title title_top">SMART �˻� �׸�����<span>SMART�˻� �׸� ��� �� ���� ���� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=38')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

<form name="frmList">
<input type="hidden" name="sort" id="sort" value="<?=$_GET['sort']?>" />

<table class="tb">
<col class="cellC" style="width:100px"><col class="cellL">
<col class="cellC"><col class="cellL">
<tr>
	<td>Ű����˻�</td>
	<td colspan="3"><select name="skey">
	<option value="all"> = ���հ˻� =
	<option value="themenm" <?=$selected['skey']['themenm']?>>�׸���
	<option value="catnm" <?=$selected['skey']['catnm']?>>ī�װ���
	</select>
	<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>�з�����</td>
	<td colspan=3><script>new categoryBox('cate[]', 4, '<?=$category?>', '', 'frmList');</script></td>
</tr>
<tr>
	<td>�����</td>
	<td colspan="3">
	<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align="absmiddle"></a>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo"><font class="ver8">
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages
	</td>
	<td align="right">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="bottom"><img src="../img/sname_date.gif" /><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif" /></a><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif" /></a><img src="../img/sname_dot.gif" /><img src="../img/sname_theme.gif" /><a href="javascript:sort('themenm desc')"><img name="sort_themenm_desc" src="../img/list_up_off.gif" /></a><a href="javascript:sort('themenm')"><img name="sort_themenm" src="../img/list_down_off.gif" /></a>&nbsp;</td>
	</tr>
	</table>

	</td>
</tr>
</table>
</form>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="7"></td></tr>
<tr class="rndbg">
	<th width="60">��ȣ</th>
	<th width="">�׸���</th>
	<th width="">���� ī�װ�</th>
	<th width="">�����</th>
	<th width="60">����</th>
	<th width="60">����</th>
	<th width="60">����</th>
</tr>
<tr><td class="rnd" colspan="7"></td></tr>
<?
	while($data = $db->fetch($res)) {
		$count_category = ($theme[$data['sno']] == 0) ? '0' : $theme[$data['sno']];
		if($count_category) $count_category = "<a href=\"javascript:popupLayer('../goods/smart_search_category.php?themeno=".$data['sno']."',600,500);\">".$count_category."��</a>";
		else $count_category .= "��";
?>
<tr><td height="4" colspan="7"></td></tr>
<tr height="25">
	<td align="center"><font class="ver8" color="616161"><?=$pg->idx--?></td>
	<td align="center"><a href="../goods/smart_search_register.php?mode=modTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>"><?=htmlspecialchars($data['themenm']).($data['basic'] == 'y' ? ' (�⺻�׸�)' : '')?></a><a href="javascript:setBasic(<?=$data['sno']?>);"></a></td>
	<td align="center"><?=$count_category?></td>
	<td align="center"><font class="ver81" color="444444"><?=substr($data['regdt'], 0, 10)?></td>
	<td align="center"><a href="../goods/indb.smart_search.php?mode=copTheme&no=<?=$data['sno']?>&<?=$queryString?>" onclick="return confirm('������ �׸��� �ϳ� �� �ڵ� ��� �մϴ�.')"><img src="../img/i_copy.gif"></a></td>
	<td align="center"><a href="../goods/smart_search_register.php?mode=modTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>"><img src="../img/i_edit.gif"></a></td>
	<td align="center">
		<? if($theme[$data['sno']]) { ?>
		<a href="javascript:void(0);" onclick="alert('�������� ī�װ��� �ֽ��ϴ�.\n\n�ش� ī�װ��� Smart�˻� �׸����� ���� ��, ������ �ּ���.[<?=$theme[$data['sno']]?>]');"><img src="../img/i_del.gif"></a>
		<? } else { ?>
		<a href="../goods/indb.smart_search.php?mode=delTheme&no=<?=$data['sno']?>&<?=$queryString?>&page=<?=$_GET['page']?>" onclick="return confirm('[<?=addslashes($data['themenm'])?>] �׸��� �����Ͻðڽ��ϱ�?\n\n������ ������ �������� �ʽ��ϴ�.')"><img src="../img/i_del.gif"></a>
		<? } ?>
	</td>
</tr>
<tr><td height="4"></td></tr>
<tr><td class="rndline" colspan="7"></td></tr>
<? } ?>
</table>

<div style="padding-top:15px"></div>
<div align="center" class="pageNavi"><font class="ver8"><?=$pg->page['navi']?></font></div>
<div style="padding:5px 0px 10px 0px;" align="right"><a href="../goods/smart_search_register.php?<?=$queryString?>&page=<?=$_GET['page']?>"><img src="../img/btn_themeapply_s.gif" /></a></div>

<div style="padding-top:30px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[�׸����] ��ư�� Ŭ���Ͽ� SMART�˻� ���� ī�װ����� ������ �׸��� ����, ��� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻� �޴����Ͽ� ��ϵ� �׸������ ��ȸ�ϰ�, ����� ī�װ��� Ȯ�� �� �� �ֽ��ϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ø� �������� <a href="../design/codi.php?ifrmCodiHref=<?=urlencode("iframe.codi.php?design_file=proc/smartSearch.htm")?>" target="_blank" style="color:#FFFFFF; font-weight:bold;">[ ������������ > ��Ÿ������ > smartSearch.htm ]</a> ���������� ���� �� ������ ���� �մϴ�.</td></tr>
</table>
</div>
<? include "../_footer.php"; ?>
