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

$tsCfg['subscribe'] = unserialize(stripslashes($tsCfg['subscribe']));
$tsCfg['interest'] = unserialize(stripslashes($tsCfg['interest']));

if(!$tsCfg['subscribe']['use']) $tsCfg['subscribe']['use'] = 'n';
$checked['subscribe']['use'][$tsCfg['subscribe']['use']] = 'checked';

if(!$tsCfg['subscribe']['email']) $tsCfg['subscribe']['email'] = '0';
$checked['subscribe']['email'][$tsCfg['subscribe']['email']] = 'checked';

if(!$tsCfg['subscribe']['sms']) $tsCfg['subscribe']['sms'] = '0';
$checked['subscribe']['sms'][$tsCfg['subscribe']['sms']] = 'checked';


if(!$tsCfg['interest']['use']) $tsCfg['interest']['use'] = 'n';
$checked['interest']['use'][$tsCfg['interest']['use']] = 'checked';

if(!$tsCfg['interest']['member']) $tsCfg['interest']['member'] = '0';
$checked['interest']['member'][$tsCfg['interest']['member']] = 'checked';

if(!$tsCfg['interest']['subscribe']) $tsCfg['interest']['subscribe'] = '0';
$checked['interest']['subscribe'][$tsCfg['interest']['subscribe']] = 'checked';



$where = array();

if ($_GET['stype'] != '') $where[] = " SC.".$_GET['stype']." <> ''";
if ($_GET['sword'] != '') $where[] = " SC.".$_GET['skey']." like '%".$_GET['sword']."%'";
if ($_GET['category'] != '') $where[] = " SC.category = '".$_GET['category']."'";



list($total) = $db->fetch("SELECT COUNT(*) FROM ".GD_TODAYSHOP_SUBSCRIBE);


$pg = new Page($_GET['page'],$_GET['page_num']);


$db_table = "
	".GD_TODAYSHOP_SUBSCRIBE." AS SC
	LEFT JOIN ".GD_MEMBER." AS MB
	ON SC.m_id = MB.m_id
	LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS TC
	ON SC.category = TC.category

";

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->field = " SC.*, MB.name, TC.catnm";
$pg->setQuery($db_table,$where,'');
$pg->exec();
$res = $db->query($pg->query);
?>
<script type="text/javascript">
function fnCheckForm(f) {

	// ���ɺз�
	$$('input[name="interest[use]"]:checked').each(function(item){

		if (item.value == 'y') {
			// ȸ��, ���ⱸ�� 1�� �̻� üũ
			if (($$('input[name="interest[member]"]:checked').size() + $$('input[name="interest[subscribe]"]:checked').size()) < 1) {
				alert('ȸ��/���ⱸ�� �з��� üũ�� �ּ���.');
				return false;
			}
		}
		else {


		}

	});


	// ���ⱸ��
	$$('input[name="subscribe[use]"]:checked').each(function(item){

		if (item.value == 'y') {
			// �߼� ���� 1�� �̻� üũ
			if (($$('input[name="subscribe[email]"]:checked').size() + $$('input[name="subscribe[sms]"]:checked').size()) < 1) {
				alert('�߼ۼ��ܿ� üũ�� �ּ���.');
				return false;
			}
		}
		else {


		}

	});

	return true;

}

</script>

<form name="frmConfig" method="post" action="indb.config.php" target="ifrmHidden" onSubmit="return fnCheckForm(this);"/>

	<div class="title title_top">���ⱸ�� ��û ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>���ⱸ�� ���</td>
		<td class="noline">
			<label><input type="radio" name="subscribe[use]" value="y" <?=$checked['subscribe']['use']['y']?> />���</label>
			<label><input type="radio" name="subscribe[use]" value="n" <?=$checked['subscribe']['use']['n']?> />�̻��</label>
			<span class="small"><font class="extext">���ⱸ�� ��� ��� ���θ� �����մϴ�.</font></span>
		</td>
	</tr>
	<tr>
		<td>�߼� ���� ����</td>
		<td class="noline">
			<label><input type="checkbox" name="subscribe[email]" value="1" <?=$checked['subscribe']['email']['1']?> />�̸���</label>
			<label><input type="checkbox" name="subscribe[sms]" value="1" <?=$checked['subscribe']['sms']['1']?> />SMS</label>
		</td>
	</tr>
	</table>

	<div style="margin-top:10px;"></div>


	<div class="title title_top">���ɺз����� ��뼳��<span>���� ���ϴ� ���ɺз��� ������ �� �ֵ��� �ϴ� ����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td rowspan="2">��뼳��</td>
		<td class="noline">
			<label><input type="radio" name="interest[use]" value="y" <?=$checked['interest']['use']['y']?> /> ���</label>
			<label><input type="radio" name="interest[use]" value="n" <?=$checked['interest']['use']['n']?> /> �̻��</label>
			<font class="extext">���ⱸ�� ��û �� ȸ������ ���� �з��� �Բ� ������ �� �ֽ��ϴ�.</font>
		</td>
	</tr>
	<tr>
		<!--td>��뼳��</td-->
		<td class="noline">
			<label><input type="checkbox" name="interest[member]" value="1"  <?=$checked['interest']['member']['1']?> /> ȸ��</label>
			<label><input type="checkbox" name="interest[subscribe]" value="1"  <?=$checked['interest']['subscribe']['1']?> /> ���ⱸ��</label>
			<font class="extext">ȸ�����ɺз� ���� �� ���ⱸ�� ��û�ڰ� ���ϴ� �з��� ������ �� �ֽ��ϴ�.</font>
		</td>
	</tr>
	</table>

	<p style="margin:3px;line-height:150%;">
	ȸ������ �з�(����)������  �α��� �� ȸ���� ������ �з��� ��ǰ�� �������� ����˴ϴ�. <br>
	���ⱸ����û�� ���ɺз��� ������ ��� �ش� �з��� ���� �������� �߼��� �� �ֽ��ϴ�.
	</p>







	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>

<form name=frmList>
<input type=hidden name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">���ⱸ�� ��û�� ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=15')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td rowspan="2">���˻�</td>
		<td class="noline">
			<label><input type="radio" name="stype" value="" <?=$_GET['stype'] == '' ? 'checked' : '' ?>>��ü</label>
			<label><input type="radio" name="stype" value="email" <?=$_GET['stype'] == 'email' ? 'checked' : '' ?>>�̸���</label>
			<label><input type="radio" name="stype" value="phone" <?=$_GET['stype'] == 'phone' ? 'checked' : '' ?>>SMS</label>
		</td>
	</tr>
	<tr>
		<!--td rowspan="2">���˻�</td-->
		<td>
			<select name="skey">
				<option value="email">�̸���</option>
				<option value="phone">�޴���</option>
			</select>
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



<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colSpan=16></td></tr>
<tr class=rndbg>
	<th width="60">����</th>
	<th width="60">��ȣ</th>
	<th>ȸ����</th>

	<th>���ɺз�(����)</th>

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
	<td><input type="checkbox" name="chk[]" value="<?=$data['sno']?>"></td>
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
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page['navi']?></font></div></td>
<td width=6%></td>
</tr></table>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��Ʈ..</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>