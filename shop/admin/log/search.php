<?

$location = "������ > �α� �˻���м� ";
include "../_header.php";
include "../../lib/page.class.php";

$db_table = "".GD_SEARCH." as sch";

### ���� ����
$_GET[sword] = trim($_GET[sword]);

$year = ($_GET[year]) ? $_GET[year] : date("Y");
$month = ($_GET[month]) ? sprintf("%02d",$_GET[month]) : date("m");

$stype = ($_GET[stype]) ? $_GET[stype] : 'm';
$sdate_s = ($_GET[regdt][0]) ? $_GET[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_GET[regdt][1]) ? $_GET[regdt][1] : date('Ymd');

if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
	msg('��ȸ�Ⱓ ������ �ִ� 1���� ���� ���մϴ�. �Ⱓ Ȯ���� �缳�� ���ּ���.',$_SERVER['PHP_SELF']);exit;
}

$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[year][$year] = "selected";
$selected[month][$month] = "selected";

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($stype == 'm') {
	$where[] = " DATE_FORMAT(regdate, '%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = "( (regdate) >= '".($sdate_s)."' and (regdate) <= '".($sdate_e)."')";
}

if ($_GET[word]) {
	if ($_GET[skey] == 'goodsnm') {
		$where[] = "EXISTS (select goodsno from gd_goods where goodsnm like '%$_GET[word]%')";
		$where[] = "word like '%$_GET[word]%'";
	}
	else $where[] = "word like '%$_GET[word]%'";
}
$pg = new Page($_GET[page]);
$pg->field = "word,sum(cnt) cnt";
$pg->setQuery($db_table,$where,"cnt desc","group by word");
$pg->exec();

list ($total) = $db->fetch("select sum(cnt) from $db_table $pg->where");
list ($tmp,$max) = $db->fetch(substr($pg->query,0,strpos($pg->query,"limit"))."limit 1");
$res = $db->query($pg->query);

while ($data=$db->fetch($res)) $log[$data[word]] = $data[cnt];
?>

<div class="title title_top">�˻��� ���� �м�<span>���θ����� ������ �˻��� �α�ܾ ���캸�� ��� �ݿ��մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=19')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�˻��Ⱓ</td>
	<td>
	<div>
		<label class="noline"><input type="radio" name="stype" value="m" <?=$stype == 'm' ? 'checked' : ''?>>������ȸ</label>

		<select name=year>
		<? for ($i=0;$i<3;$i++){ $y = date("Y") - $i; ?>
		<option value="<?=$y?>" <?=$selected[year][$y]?>><?=$y?>
		<? } ?>
		</select>��
		<select name=month>
		<?
		for ($i=1;$i<=12;$i++){
			$tmp = sprintf("%02d",$i);
		?>
		<option value="<?=$i?>" <?=$selected[month][$tmp]?>><?=$i?>
		<? } ?>
		</select>��
	</div>

	<div style="margin-top:5px;">
		<label class="noline"><input type="radio" name="stype" value="d" <?=$stype == 'd' ? 'checked' : ''?>>�Ϻ���ȸ</label>

		<input type=text name=regdt[] value="<?=$sdate_s?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
		<input type=text name=regdt[] value="<?=$sdate_e?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
		<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
		<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	</div>

	</td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<input type=text name=word value="<?=$_GET[word]?>">
	<span style="padding-left:5px"><font class=extext>�������� �˻��ϸ� �� �Ⱓ���� ��� �˻�� �˻��մϴ�</span>
	</td>
</tr>
<tr>
	<td>�˻�����</td>
	<td class="noline">
	<label><input type=radio name=skey value="" <?=($_GET[skey] == '' ? 'checked' : '')?>>��ü</label>
	<label><input type=radio name=skey value="goodsnm" <?=($_GET[skey] == 'goodsnm' ? 'checked' : '')?>>��ǰ��</label>
	</td>
</tr>
</table>

<div class="button_top">
<input type=image src="../img/btn_search2.gif">
</div>

<div style="padding-top:15px"></div>

</form>
<font class=ver8><b>
<?

include "../../lib/graph.class.php";
$gp = new Graph;
@arsort($log);
$gp->reset();
$gp->sum = $total;
$gp->max = $max;
$gp->type = 1;
$gp->barMax = 570;
$gp->out = $log;
$gp->head = '<tr height="25" bgcolor="#CCCCCC"><td>����</td><td>�˻���</td><td>�˻�Ƚ��</td><td>����</td></tr>';
$gp->display_idx = true;

$gp->color	= array(
			"#7ef22e",
			"#64df0f",
			"#57be10",
			"#3e9800",
			"#f7f7f7",
			"#f7f7f7",
			"#f7f7f7",
			"#f7f7f7",
			"#f7f7f7",
			"#f7f7f7",
			);
$gp->drawGraph();
?>
</b></font>

<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></div>

<? include "../_footer.php"; ?>