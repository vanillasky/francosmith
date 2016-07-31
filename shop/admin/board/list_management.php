<?

$location = "�Խ��ǰ��� > �Խñ� ���հ���";
include "../_header.php";
include "../../lib/page.class.php";

if( !$_GET['page']) $_GET['page'] = 1;
if( !$_GET['skey']) $_GET['skey'] = "all";
if( !$_GET['board']) $_GET['board'] = "all";
if( !$_GET['page_num']) $_GET['page_num'] = "20";
if( !$_GET['sort']) $_GET['sort'] = "regdt desc";

$selected['skey'][$_GET['skey']] = "selected='selected'";
$selected['board'][$_GET['board']] = "selected='selected'";
$selected['page_num'][$_GET['page_num']] = "selected='selected'";
$selected['sort'][$_GET['sort']] = "selected='selected'";

$res = $db->_select("select id from gd_board");
for($i=0; $i<count($res); $i++) {
	include "../../conf/bd_".$res[$i]['id'].".php";
	$boardDB[$res[$i]['id']] = $bdName;
	$tmp = $db->fetch("SELECT COUNT(*) AS cnt FROM gd_bd_".$res[$i]['id']." WHERE main <> 0 ");
	$total += $tmp['cnt'];
}
$tables = array();
$where = array();
$tmp = array();

$where[] = " main <> 0 ";

if ($_GET['skey'] && $_GET['sword']) {
	switch ($_GET['skey']) {
		case "all": $key = "CONCAT( subject, contents, name, m_no )"; break;
		default: $key = $_GET['skey'];
	}

	$r_word = array_notnull(array_unique(explode(" ",$_GET['sword'])));
	for ($i=0;$i<count($r_word);$i++) {
		$tmp[] = "$key LIKE '%$r_word[$i]%'";
		if (strlen($r_word[$i])>2) $log_word[] = $r_word[$i];
	}
	if (is_array($tmp)) $where[] = "(".implode(" AND ",$tmp).")";
}

if( $_GET['sregdt'][0] && $_GET['sregdt'][1] ) $where[] = " DATE_FORMAT(regdt, '%Y%m%d') BETWEEN '".$_GET['sregdt'][0]."' AND '".$_GET['sregdt'][1]."'";

if( $_GET['board'] != 'all') {
	if($_GET['sort'] == 'regdt desc'){
		$_GET['sort'] = "idx,main,sub";
	}

	$tables[] = "( SELECT '".$boardDB[$_GET['board']]."' AS boardnm, '".$_GET['board']."' AS board, no, titleStyle, subject, name, m_no, main, comment, regdt, hit, idx, HEX(sub) AS sub FROM gd_bd_".$_GET['board']." AS b ";
	if($where) $tables[] .= " WHERE ".implode(' AND ', $where);
	$tables[] = ") AS a ";

}
else {
	if($_GET['sort'] == "idx,main,sub") $_GET['sort'] = "regdt desc";
	$t_cnt = 0;
	$tables[] = " ( ";
	foreach($boardDB as $key => $val) {
		$t_cnt++;
		$tables[] = " SELECT idx,'".$val."' AS boardnm, '".$key."' AS board, no, titleStyle, subject, name, m_no, main, comment, regdt, hit, HEX(sub) AS sub ";
		$tables[] .= " FROM gd_bd_".$key." AS b";
		if($where) $tables[] .= " WHERE ".implode(' AND ', $where);
		if($tmpWhere) $tables[] .= " AND ".implode(' AND ', $tmpWhere);
		if($t_cnt < count($boardDB)) $tables[] = " UNION ALL ";
		unset($tmpWhere);
	}
	$tables[] = ") AS a ";
}
unset($t_cnt);

$db_table = implode(" ", $tables);
$db_where[] = "1=1";

$pg = new Page($_GET['page'], $_GET['page_num']);
$pg->vars['page'] = getVars('page,log,x,y');
$pg->field = " * ";
$pg->setQuery($db_table, $db_where, $_GET['sort']);
$pg->exec();

$res = $db->query($pg->query);
### ������ ������
if(file_exists( '../../data/skin/' . $cfg['tplSkin'] . '/admin.gif' )) $adminicon = 'admin.gif';
?>
<script type="text/javascript">
function go_excel(bnm) {
	if(confirm("�˻��� ����Ʈ�� ���� �Խù��� �ٿ�ε� �Ͻðڽ��ϱ�?")) location.href = "list_excel_management.php?<?=$_SERVER['QUERY_STRING']?>&bnm=" + bnm;
}

function chkBoxTitle() {
	var cnt = document.getElementsByName("chk_no[]").length;
	var chk = document.getElementsByName("chk_no[]")[0].checked;
	if(chk) chk = false;
	else chk = true;
	for(i = 0; i < cnt; i++) document.getElementsByName("chk_no[]")[i].checked = chk;
}

function chkBox(chk) {
	var cnt = document.getElementsByName("chk_no[]").length;
	for(i = 0; i < cnt; i++) document.getElementsByName("chk_no[]")[i].checked = chk;
}

function chkDelete(idx) {
	if(confirm("�������� �����Ͻø� �亯�۵� ���� �����˴ϴ�.\n������ ������ �������� �ʽ��ϴ�")) {
		if(!no) {
			var cnt = document.getElementsByName("chk_no[]").length;
			var chkcnt = 0;
			for(i = 0; i < cnt; i++) if(document.getElementsByName("chk_no[]")[i].checked) chkcnt++;
			if(chkcnt == 0) {
				alert("������ �Խñ��� �����ϼ���");
				return;
			}
		}

		if(idx || idx == 0){	//���� �Խñ� �����ΰ��
			var cnt = document.getElementsByName("chk_no[]").length;
			for(i = 0; i < cnt; i++) document.getElementsByName("chk_no[]")[i].checked = false;
			$("chk_no_" + idx).checked = true;
		}
		document.getElementById("mode").value="list_delete";
		document.frm.action = "indb.php";
		document.frm.method = "post";
		document.frm.submit();
	}
}
</script>

<div class="title title_top">�Խñ� ���հ���<span>������ �Խ����� �Խñ۵��� �����Ͽ� �����մϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=board&no=10', 870, 800)"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"></a></div>

<form name="frm">
<input type="hidden" name="mode" id="mode" value="">
<table class="tb">
<col class="cellC" /><col class="cellL" />
<tr>
	<td>Ű���� �˻�</td>
	<td colspan="3">
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="subject" <?=$selected['skey']['subject']?>> ���� </option>
	<option value="contents" <?=$selected['skey']['contents']?>> ���� </option>
	<option value="name" <?=$selected['skey']['name']?>> �ۼ��� </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
</tr>
<tr>
	<td>�����</td>
	<td colspan="3">
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" onclick="calendar(event)" class="line"> -
		<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" onclick="calendar(event)" class="line">
		<a href="javascript:setDate('sregdt[]', <?=date("Ymd")?>, <?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle"></a>
		<a href="javascript:setDate('sregdt[]', <?=date("Ymd", strtotime("-7 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('sregdt[]', <?=date("Ymd", strtotime("-15 day"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('sregdt[]', <?=date("Ymd", strtotime("-1 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('sregdt[]', <?=date("Ymd", strtotime("-2 month"))?>, <?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
		<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>�Խ���</td>
	<td>
		<select name="board" style="width:252px;">
		<option value="all" <?=$selected['board']['all']?>>��ü</option>
		<? foreach( $boardDB as $key=>$val ) { ?>
		<option value="<?=$key?>" <?=$selected['board'][$key]?>><?=$val?></option>
		<? } ?>
		</select>
	</td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>


<div style="height:15px"></div>
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td class="pageInfo"><font class="ver8">
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages
	</td>
	<td align="right">
		<input type="hidden" name="sort" id="sort" value="<?=$_GET['sort']?>" />
		<? if($_GET['board'] != "all") { ?>
		<a href="../board/list_management.php?skey=<?=$_GET['skey']?>&sword=<?=$_GET['sword']?>&sregdt[]=<?=$_GET['sregdt'][0]?>&sregdt[]=<?=$_GET['sregdt'][1]?>&board=<?=$_GET['board']?>&sort=idx,main,sub"><img src="../img/btn_boardlist_array.gif" align="absmiddle" /></a>
		<? } ?>
		<select name="sortSelect" onchange="if(this.value) { $('sort').value=this.value;this.form.submit(); }">
		<? if($_GET['board'] != "all") { ?>
			<option value="">- ���� ���� -</option>
			<optgroup label="---------------"></optgroup>
		<? } ?>
			<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- �ۼ��� ���ġ�</option>
			<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- �ۼ��� ���ġ�</option>
			<optgroup label="---------------"></optgroup>
			<option value="TRIM(subject) asc" <?=$selected['sort']['TRIM(subject) asc']?>>- ���� ���ġ�</option>
			<option value="TRIM(subject) desc" <?=$selected['sort']['TRIM(subject) desc']?>>- ���� ���ġ�</option>
		</select>
		<select name="page_num" onchange="this.form.submit()">
			<?
			$r_pagenum = array(10, 20, 40, 60, 100);
			foreach($r_pagenum as $v) {
			?>
			<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
			<? } ?>
		</select>
	</td>
</tr>
</table>

<?
$t_cnt = 0;
foreach($boardDB as $key=>$val) {
	if($t_cnt++ == 0) $inc = $key;
	$tmp[] = $key."^".$val;
}
?>
<table width="100%" cellpadding="0" cellspacing="0">
<col width="40"><col width="40"><col width="100"><col width=""><col width="120"><col width="120"><col width="40"><col width="50"><col width="50"><col width="50">
<tr><td class="rnd" colspan="10"></td></tr>
<tr class="rndbg">
	<th onclick="chkBoxTitle()" style="cursor:pointer;">����</th>
	<th>��ȣ</th>
	<th>�Խ����̸�</th>
	<th>�� ����</th>
	<th>�ۼ���</th>
	<th>�ۼ���</th>
	<th>��ȸ��</th>
	<th>�亯</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="10"></td></tr>
<tr><td height="3" colspan="10"></td></tr>
<?
$i = 0;
$navigCount = 0;

while($data=$db->fetch($res)) {
	list($data['m_id'], $data['sex'], $data['dormant_regDate']) = $db->fetch("SELECT m_id, sex, dormant_regDate FROM ".GD_MEMBER." WHERE m_no = '".$data['m_no']."'");
	if($data['titleStyle']) {
		$data['titleStyle'] = str_replace(array("|", "^C", "^S", "^B"), array(";", "color", "font-size", "font-weight"), $data['titleStyle']);
		$data['titleStyle'] = "style=\"".$data['titleStyle']."\"";
	}
?>
<tr height="32" align="center">
<input type="hidden" name="proc_board[<?=$data["no"]?>]" value="<?=$data['board']?>">
	<td><input type="checkbox" style="border:0" name="chk_no[]" id="chk_no_<?=$i?>" value="<?=$data['board']."|^".$data['no']?>"></td>
	<td><?=$pg->idx--?></td>
	<td><a href="../board/register.php?mode=modify&id=<?=$data['board']?>"><?=$data['boardnm']?></a></td>
	<td align="left">
		<? if($data['sub']) { ?><span style="margin-left:<?=(strlen($data['sub']) / 2) * 20?>px" /><? for($j = 0, $jmax = (strlen($data['sub']) / 2); $j < $jmax; $j++) { ?>Re:<? } ?></span><? } ?>
		<a href="../../board/view.php?id=<?=$data["board"]?>&no=<?=$data["no"]?>" target="_blank" <?=$data['titleStyle']?>><?=$data['subject']?></a>
		<?=($data['comment']) ? "<span style=\"font-size:10px; font-weight:bold; color:#FF1E1E;\">[".$data['comment']."]</span> " : ""?>
	</td>
	<td>
		<?php if($data['m_id']){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?php echo $data['m_id']; ?>" m_no="<?php echo $data['m_no']; ?>"><font class="small1" color="#0074ba"><strong><?php echo $data['name']; ?></strong></font></span> <a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?php echo $data['m_id']; ?>',780,600);"><img src="../img/icon_crmlist<?php echo $data['sex']; ?>.gif" /></a>
			<?php } else { ?>
				<font class="small1" color="#0074ba"><strong><?php echo $data['name']; ?></strong>(�޸�ȸ��)</font>
			<?php } ?>
		<?php } else { ?>
			<font class="small1" color="#0074ba"><strong><?php echo $data['name']; ?></strong></font>
		<?php } ?>
	</td>
	<td><?=$data['regdt']?></td>
	<td><?=$data['hit']?></td>
	<td><a href="javascript:popup2('../board/admin_reply.php?mode=reply&inc=<?=$data["board"]?>&no=<?=$data["no"]?>',800,800,1)"><img src="../img/i_reply.gif"></a></td>
	<td><a href="javascript:popup2('../board/admin_register.php?mode=modify&inc=<?=$data["board"]?>&no=<?=$data["no"]?>',800,800,1)"><img src="../img/i_edit.gif"></a></td>
	<td><a href="javascript:chkDelete(<?=$i?>)"><img src="../img/i_del.gif"></a></td>
</tr>
<tr><td height="4" colspan="10"></td></tr>
<tr><td colspan="10" class="rndline"></td></tr>
<?
	$i++;
}
?>
</table>

<table style="width:100%">
<tr>
	<td><a href="javascript:chkBox(true)"><img src="../img/btn_allselect_s.gif" border="0"/></a>
		<a href="javascript:chkBox(false)"><img src="../img/btn_alldeselect_s.gif" border="0"></a>
		<a href="javascript:chkDelete()"><img src="../img/btn_alldelet_s.gif" border="0"></a>
	</td>
	<td style="width:50%"></td>
	<td style="text-align:right">
		<a href="javascript:popup2('../board/admin_register.php?mode=register&inc=<?=$_GET["board"]?>', 800, 800, 1)"><img src="../img/btn_boardapply_s.gif" align='absmiddle' /></a>
		<a href="javascript:go_excel()"><img src="../img/btn_download_s.gif" align='absmiddle' /></a>
	</td>
</tr>
</table>

<div align="center" class="pageNavi"><font class="ver9"><?=$pg->page['navi']?></font></div>

<div id="MSG01">
<table cellpadding="2" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ �Խ��Ǻ� �Խñ۵��� Ȯ�� �� �亯, �Խñ� �ۼ��� ���� ���� �� ȭ�鿡�� ó��, ���� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������� Ŭ���Ͻø� �ش� �Խñ��� ���θ� �������� �� â���� �����ϴ�. ���θ� ����� �������� �Խ��ǿ����� �Խñ� Ȯ�� �� ��� ����� ���� ������ �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Խ��� �̸��� Ŭ���Ͻø� �ش� �Խ��� ���� �������� �̵��մϴ�. �Խ��� ���� ������ �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ȸ�� �Խñ� ����Ʈ�� [�������Ϸ� �ٿ�ε�] �Ͽ� ������ �������Ϸ� ���� �� Ȱ���� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Խñ� ����Ʈ �������� �ٿ�ε� �� '���ø�� �ٿ�ε�' ����� �������� �ʽ��ϴ�.</td></tr>
</table>
</div>

</form>
<script>window.onload = function() { UNM.inner(); }</script>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
