<?
$location = "�����̼� > ���ⱸ�� ����/��û�ڰ���";
include "../_header.php";
include "../../lib/page.class.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}
$tsCfg = $todayShop->cfg;
$tsCategory = $todayShop->getCategory(true);

list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_SUBSCRIBE);
$pg = new Page($_GET['page'],$_GET['page_num']);

$db_table = "
	".GD_TODAYSHOP_SUBSCRIBE." AS SC
	LEFT JOIN ".GD_MEMBER." AS MB
	ON SC.m_id = MB.m_id
	LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS TC
	ON SC.category = TC.category
";

$where = array();
$where[] = "SC.phone <> ''";
if ($_GET['sword']) $where[] = " SC.phone like '%".$_GET['sword']."%' ";
if ($_GET['regdt'][0] && $_GET['regdt'][1]) $where[] = "SC.regdt BETWEEN DATE_FORMAT(".$_GET['regdt'][0].",'%Y-%m-%d 00:00:00') AND DATE_FORMAT(".$_GET['regdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['category'] != '') $where[] = " SC.category = '".$_GET['category']."'";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = " SC.*, MB.name, TC.catnm";
$pg->setQuery($db_table,$where,'');
$pg->exec();
$res = $db->query($pg->query);
?>
<form>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">SMS(���ⱸ��) �߼� <span>���ⱸ�� ��û�ڵ鿡�� SMS�� �߼��մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=17')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>





<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="700">
<tr><td style="padding:7 0 10 10">

	<table width="100%">
	<tr>
		<td>
		<? $sms = & load_class('Sms','Sms');?>
		�ܿ� SMS ����Ʈ : <?=number_format($sms->smsPt)?>��
		</td>
		<td>
		<div style="padding-top:7px; color:#666666" class="g9">SMS ����Ʈ�� ���� ��� SMS�� �߼۵��� �ʽ��ϴ�.</div>
		<div style="padding-top:5px; color:#666666" class="g9">SMS����Ʈ�� �����Ͽ� �߼��Ͻñ� �ٶ��ϴ�.</div>
		</td>
		<td>
		<a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif" /></a>
		</td>
	</tr>

	</table>


</td></tr>
</table>

	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>�޴��� �˻�</td>
		<td>
			<input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
		</td>
	</tr>
	<tr>
		<td>���ɺз�</td>
		<td class="noline">
			<select name="category">
				<option value="">-���ɺз��� ������ �ּ���-</option>
				<? foreach ($tsCategory as $v ) { ?>
				<option value="<?=$v['category']?>" <?=$_GET['category'] == $v['category'] ? 'selected' : ''?>><?=$v['catnm']?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>��û��</td>
		<td>
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
			<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
			<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
			<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
		</td>
	</tr>
	</table>
	<div class=button_top><input type=image src="../img/btn_search2.gif"></div>
	<div style="padding-top:15px"></div>
	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td class=pageInfo>
			<font class=ver8>�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages</font>
		</td>
	</tr>
	</table>
</form>


<form name="fmList" method="post" onsubmit="return chkFuncForm(this)">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>">

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colSpan=16></td></tr>
<tr class=rndbg>
	<th width="60">����</th>
	<th width="60">��ȣ</th>
	<th>ȸ����</th>
	<th>���ɺз�</th>
	<th>�̸���</th>
	<th>�޴���</th>
	<th>��û��</th>
	<th>����</th>
</tr>
<tr><td class=rnd colSpan=16></td></tr>
<col width=40 span=2 align=center>
<? while ($data=$db->fetch($res)) { ?>
<tr><td height=4 colSpan=16></td></tr>
<tr height=25 align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['sno']?>"></td>
	<td><font class=ver8 color=616161><?=$pg->idx--?></font></td>
	<td><?=$data['name']?></td>
	<td><?=$data['catnm']?></td>
	<td><?=$data['email']?></td>
	<td><?=$data['phone']?></td>
	<td><?=$data['regdt']?></td>
	<td>
	<A onclick="return confirm('������ �����Ͻðڽ��ϱ�?');" href="./indb.subscribe.php?mode=delete&sno=<?=$data['sno']?>"><IMG src="../img/i_del.gif"></A>
	</td>

</tr>
<tr><td height=4></td></tr>
<tr><td colSpan=16 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=6% style="padding-left:7"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=6%></td>
</tr></table>

<script>
function sendSMS(fm)
{
	if (fm.type.value=="ts_select" && !isChked(document.getElementsByName('chk[]'))) return false;
	if (fm.type.value=="ts_query" && !fm.query.value){
		alert('�˻� ����� �����ϴ�.!');
		return false;
	}
	openLayer('objEmail','block');
	fm.target = "ifrmSMS";
	fm.action = "./sms.php?ifrmScroll=1";
	fm.submit();
}
</script>

<div style='font:0;height:10'></div>
<div align=center>
<table bgcolor=F7F7F7 width=100%>
<tr>
	<td class=noline width=57% align=right>
	<select name=type>
	<option value="ts_select">������ ��󿡰�
	<option value="ts_query">���� ���� ��û�� ��ü
	</select>
	</td>
	<td width=43% style="padding-left:10px">
	<a href="javascript:void(0)" onClick="sendSMS(document.fmList)"><img src="../img/btn_today_email_sm.gif"></a>
	</td>
</tr>
</table>
</div><p>


<table width="100%" id="objEmail" style="display:none" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td align="center">
	<iframe name="ifrmSMS" style="width:100%;height:900px" frameborder=0></iframe>
	</td>
</tr>
</table>

</form>

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>
