<?
/**
	2011-01-13 by x-ta-c

	������ �ŷ��� �� ���Ա� �ֹ����� ��ȸ�Ͽ�, ������ �Աݿ�û SMS�� �߼� �Ǵ� �Ա�Ȯ�� ó���� �� �� �ִ�.
 */

$location = "�ֹ����� > �ֹ�����Ʈ";
include "../_header.php";
@include "../../conf/config.pay.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

// ���� ��¥ -> �ֹ��Ϸ� ���� ����� ���� ����ϱ� ����.
$today = mktime(0,0,0,date('m'), date('d'), date('Y'));

$_GET[dtkind] = 'orddt'; # ó���� => �ֹ����ڷ� ����
$checked[dtkind][$_GET[dtkind]] = $checked[settlekind][$_GET[settlekind]] = $checked[escrowyn][$_GET[escrowyn]] = $checked[eggyn][$_GET[eggyn]] = $checked[mobilepay][$_GET[mobilepay]] = $checked[sugi][$_GET[sugi]] = "checked";

$selected[skey][$_GET[skey]] = "selected";
$selected[sgkey][$_GET[sgkey]] = "selected";

$db_table = "".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
$orderby = "a.ordno desc";


/**
	���Աݳ����� ������ŷ��ÿ��� �����ϹǷ� ����.
 */
$where[] = "settlekind = 'a'";

if ($_GET[sword]){
	$_GET[sword] = trim($_GET[sword]);
	$t_skey = ($_GET[skey]=="all") ? "concat( a.ordno, nameOrder, nameReceiver, bankSender, ifnull(m_id,'') )" : $_GET[skey];
	$where[] = "$t_skey like '%$_GET[sword]%'";
}
if ($_GET[sgword]){
	$_GET[sgword] = trim($_GET[sgword]);
	$where[] = "{$_GET[sgkey]} like '%$_GET[sgword]%'";
	$db_table .= " left join ".GD_ORDER_ITEM." c on a.ordno=c.ordno";
	$tmp_query = "group by a.ordno";
}
if ($_GET[regdt][0]){
	if (!$_GET[regdt][1]) $_GET[regdt][1] = date("Ymd");
	$where[] = "{$_GET[dtkind]} between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
}
if ($_GET[sugi] == "online") $where[] = "a.inflow != 'sugi'";
elseif ($_GET[sugi] == "sugi") $where[] = "a.inflow = 'sugi'";

/**
	����ó���� �ܿ��� �����õ� �ʿ䰡 ����.
 */
$where2[] = "(step=0 and step2 = 0)";


if ($_GET[cbyn] == 'Y'){
	$checked[cbyn] = "checked";
	$where[] = "cbyn = 'Y'";
}

if ($_GET['aboutcoupon'] == '1'){
	$checked['aboutcoupon'] = "checked";
	$where[] = "a.about_coupon_flag = '1'";
}

if($_GET[chk_inflow]){
	foreach ($_GET[chk_inflow] as $v){
		$checked[chk_inflow][$v] = "checked";
		if ( $v == 'naver_price' ) $where3[] = "inflow in ('naver_elec', 'naver_bea', 'naver_milk')";
		else $where3[] = "inflow='$v'";
	}
}

if ($where2) $where[] = "(".implode(" or ",$where2).")";
if ($where3) $where[] = "(".implode(" or ",$where3).")";

if ($_GET[escrowyn]) $where[] = "escrowyn='$_GET[escrowyn]'";
if ($_GET[eggyn]) $where[] = "eggyn='$_GET[eggyn]'";
if ($_GET[mobilepay]) $where[] = "mobilepay='$_GET[mobilepay]'";

if(!$cfg['orderPageNum'])$cfg['orderPageNum'] = 15;
$pg = new Page($_GET[page],$cfg['orderPageNum']);

$pg->field = "b.*,a.*";
$pg->cntQuery = sprintf("select count(distinct a.ordno) from %s where %s", $db_table, implode(' and ', $where));
$pg->setQuery($db_table,$where,$orderby,$tmp_query);
$pg->exec();
$res = $db->query($pg->query);
?>

<script>
function fnRequestBanking() {

	var f = document.frmList;

	if (f.processType.value == 'sms')
	{
		f.mode.value = 'requestSMS';
	}
	else if (f.processType.value == 'confirm') {
		f.mode.value = 'chgAllBanking';
	}
	else {
		alert('ó�� ����� ������ �ּ���.');
		return;
	}

	// ó���� �ֹ��� üũ.
	var cnt = 0, chk = document.getElementsByName('chk[]');

	for (var i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	}

	f.submit();
}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;
	for (i=0;i<El.length;i++){
		if (El[i].disabled) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}
</script>

<div class="title title_top" style="position:relative;padding-bottom:15px">�Աݴ�� ����Ʈ<span>������ �ŷ����� �Աݴ���� ����� Ȯ���ϰ� �ֹ����¸� �����մϴ�</span>
<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=14')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a>
</div>
<form>
<input type=hidden name=mode value="<?=$_GET[mode]?>">

<table class=tb>
<col class=cellC><col class=cellL style="width:250">
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�ֹ��˻� (����)</td>
	<td>
	<select name=skey>
	<option value="all"> = ���հ˻� =
	<option value="a.ordno" <?=$selected[skey][a.ordno]?>> �ֹ���ȣ
	<option value="nameOrder" <?=$selected[skey][nameOrder]?>> �ֹ��ڸ�
	<option value="nameReceiver" <?=$selected[skey][nameReceiver]?>> �����ڸ�
	<option value="bankSender" <?=$selected[skey][bankSender]?>> �Ա��ڸ�
	<option value="m_id" <?=$selected[skey][m_id]?>> ���̵�
	</select>
	<input type=text name=sword value="<?=$_GET[sword]?>" class=line>
	</td>
	<td><font class=small1>��ǰ�˻� (����)</td>
	<td>
	<select name=sgkey>
	<option value="goodsnm" <?=$selected[sgkey][goodsnm]?>> ��ǰ��
	<option value="brandnm" <?=$selected[sgkey][brandnm]?>> �귣��
	<option value="maker" <?=$selected[sgkey][maker]?>> ������
	</select>
	<input type=text name=sgword value="<?=$_GET[sgword]?>" class=line>
	</td>
</tr>
<tr>
	<td><font class=small1>��������</td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=sugi value="" <?=$checked[sugi]['']?>>��ü
	<input type=radio name=sugi value="online" <?=$checked[sugi]['online']?>>�¶�������
	<input type=radio name=sugi value="sugi" <?=$checked[sugi]['sugi']?>>��������
	</td>
</tr>
<tr>
	<td><font class=small1>�ֹ���</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" size=12 class=line> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" size=12 class=line>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td><font class=small1>����ϼ�</td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=mobilepay value="" <?=$checked[mobilepay]['']?>>��ü
	<input type=radio name=mobilepay value="n" <?=$checked[mobilepay]['n']?>>�Ϲݰ���
	<input type=radio name=mobilepay value="y" <?=$checked[mobilepay]['y']?>>����ϼ�����
	</td>
</tr>
<tr>
	<td><font class=small1>����ũ��</td>
	<td class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=escrowyn value="" <?=$checked[escrowyn]['']?>>��ü
	<input type=radio name=escrowyn value="n" <?=$checked[escrowyn]['n']?>>�Ϲݰ���
	<input type=radio name=escrowyn value="y" <?=$checked[escrowyn]['y']?>>����ũ�� <img src="../img/btn_escrow.gif" align=absmiddle>
	</td>
	<td><font class=small1>���ں������� <a href="../basic/egg.intro.php"><img src="../img/btn_question.gif"></a></td>
	<td class=noline><font class=small1 color=5C5C5C>
	<input type=radio name=eggyn value="" <?=$checked[eggyn]['']?>>��ü
	<input type=radio name=eggyn value="n" <?=$checked[eggyn]['n']?>>�̹߱�
	<input type=radio name=eggyn value="f" <?=$checked[eggyn]['f']?>>�߱޽���
	<input type=radio name=eggyn value="y" <?=$checked[eggyn]['y']?>>�߱޿Ϸ� <img src="../img/icon_guar_order.gif">
	</td>
</tr>
<tr>
	<td><font class=small1>����ó�ֹ� <a href="../naver/naver.php"><img src="../img/btn_question.gif"></a></td>
	<td colspan=3 class=noline><font class=small1 color=5C5C5C>
	<input type=checkbox name=chk_inflow[] value="naver" <?=$checked[chk_inflow][naver]?>><img src="../img/inflow_naver.gif" align=absmiddle> ���̹� ����&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahoo_fss" <?=$checked[chk_inflow][yahoo_fss]?>><img src="../img/inflow_yahoo_fss.gif" align=absmiddle> �����мǼ�ȣ&nbsp;
	<input type=checkbox name=chk_inflow[] value="interpark" <?=$checked[chk_inflow][interpark]?>><img src="../img/inflow_interpark.gif" align=absmiddle> ������ũ���÷���&nbsp;
	<input type=checkbox name=chk_inflow[] value="openstyle" <?=$checked[chk_inflow][openstyle]?>><img src="../img/inflow_interpark.gif" align=absmiddle> ������ũ���½�Ÿ��&nbsp;
	<input type=checkbox name=chk_inflow[] value="openstyleOutlink" <?=$checked[chk_inflow][openstyleOutlink]?>><img src="../img/inflow_interpark.gif" align=absmiddle> ������ũ���½�Ÿ�Ͼƿ���ũ<br>
	<input type=checkbox name=chk_inflow[] value="naver_price" <?=$checked[chk_inflow][naver_price]?>><img src="../img/inflow_naver_price.gif" align=absmiddle> ���̹����ݺ�&nbsp;
	<input type=checkbox name=chk_inflow[] value="danawa" <?=$checked[chk_inflow][danawa]?>><img src="../img/inflow_danawa.gif" align=absmiddle> �ٳ���&nbsp;
	<input type=checkbox name=chk_inflow[] value="mm" <?=$checked[chk_inflow][mm]?>><img src="../img/inflow_mm.gif" align=absmiddle> ���̸���&nbsp;
	<input type=checkbox name=chk_inflow[] value="bb" <?=$checked[chk_inflow][bb]?>><img src="../img/inflow_bb.gif" align=absmiddle> ����Ʈ���̾�&nbsp;
	<input type=checkbox name=chk_inflow[] value="omi" <?=$checked[chk_inflow][omi]?>><img src="../img/inflow_omi.gif" align=absmiddle> ����&nbsp;
	<input type=checkbox name=chk_inflow[] value="enuri" <?=$checked[chk_inflow][enuri]?>><img src="../img/inflow_enuri.gif" align=absmiddle> ������&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahoo" <?=$checked[chk_inflow][yahoo]?>><img src="../img/inflow_yahoo.gif" align=absmiddle> ���İ��ݺ�&nbsp;
	<input type=checkbox name=chk_inflow[] value="yahooysp" <?=$checked[chk_inflow][yahooysp]?>><img src="../img/inflow_yahooysp.gif" align=absmiddle> ����������<br />
	<input type=checkbox name=chk_inflow[] value="auctionos" <?=$checked[chk_inflow][auctionos]?>><img src="../img/inflow_auctionos.gif" align=absmiddle> ���Ǿ�ٿ�&nbsp;
	<input type=checkbox name=chk_inflow[] value="daumCpc" <?=$checked['chk_inflow']['daumCpc']?>><img src="../img/inflow_daumCpc.gif" align="absmiddle"> ���������Ͽ�&nbsp;
	<input type=checkbox name=chk_inflow[] value="cywordScrap" <?=$checked['chk_inflow']['cywordScrap']?>><img src="../img/inflow_cywordScrap.gif" align="absmiddle"> ���̿��彺ũ��&nbsp;
	<input type=checkbox name=chk_inflow[] value="naverCheckout" <?=$checked['chk_inflow']['naverCheckout']?>><img src="../img/inflow_naverCheckout.gif" align="absmiddle"> ���̹�üũ�ƿ�
	</td>
</tr>
</table>
<div class="button_top">
<input type=image src="../img/btn_search2.gif">
</div>
</form>

<div style="padding-top:15px"></div>

<form name=frmList method=post action="indb.php">
<input type=hidden name=mode value="chgAllBanking">
<input type=hidden name=case value="1"><!-- �Ա�Ȯ�� -->

<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width=25><col width=30><col width=100><col width=100><col width=150><col><col width=95><col width=50><col width=50><col><col width=55>
<tr><td class=rnd colspan=20></td></tr>
<tr class=rndbg>
	<th><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th>��ȣ</th>
	<th>�ֹ��Ͻ�</th>
	<th>�������</th>
	<th colspan=2>�ֹ���ȣ (�ֹ���ǰ)</th>
	<th>�ֹ���</th>
	<th>�޴º�</th>
	<th>����</th>
	<th>�ݾ�</th>
	<th colspan=6>ó������</th>
</tr>
<tr><td class=rnd colspan=20></td></tr>
<?
$idx_grp = 0;
$idx = $pg->idx; $pr = 1;
while ($data=$db->fetch($res)){
	unset($supply); unset($selected);
	$bgcolor = ($data[step2]) ? "#F0F4FF" : "#ffffff";
	$disabled = ($data[step2]) ? "disabled" : "";

	$stepMsg = $step = getStepMsg($data[step],$data[step2],$data[ordno]);

	if(strlen($step) > 10) $step = substr($step,10);

	list($cntDv) = $db->fetch("SELECT count(*) cntDv FROM gd_order_item WHERE ordno='$data[ordno]' and dvcode != '' and dvno != ''");

	if ( $data[deliverycode] || $cntDv ){
		$step = "<a href=\"javascript:popup('popup.delivery.php?ordno=$data[ordno]',650,500)\"><font color=0074BA><b><u>$step</u></b></font></a>";
	}

	if ($_GET[sgword]) {
        $_res = $db->query("select goodsnm, if({$_GET[sgkey]} LIKE '%{$_GET[sgword]}%', 0, 1) as resort from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by resort, sno");
        list($goodsnm) = $db->fetch($_res);
    }
	else {
        $_res = $db->query("select goodsnm from ".GD_ORDER_ITEM." where ordno='$data[ordno]' order by sno");
        list($goodsnm) = $db->fetch($_res);
    }

	$grp[settleprice][''] += $data[prn_settleprice];

	$passed = Core::helper('Date')->diff($data['orddt'],$today);	// xm, xy, xd, -xm, -xy, -xd
?>
<tr height=25 bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align=center>
	<td class=noline><input type=checkbox name=chk[] value="<?=$data[ordno]?>" onclick="iciSelect(this)" required label=">���û����� �����ϴ�" <?=$disabled?>></td>
	<td><font class=ver8 color=616161><?=$pr*$idx--?></font></td>
	<td><font class=ver81 color=616161><?=substr($data[orddt],0,-3)?></font></td>
	<td><font class=ver81 color=616161><?=$passed?></font></td>
	<td align=left>
	<? if ($data['inflow'] == "sugi"){ ?>
	<a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 style="color:#ED6C0A"><b><?=$data[ordno]?></b><span class="small1">(����)</span></font></a>
	<? } else { ?>
	<a href="view.php?ordno=<?=$data[ordno]?>"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a>
	<? } ?>
	<a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td align=left>
	<div style="height:13px; overflow-y:hidden;">
		<? if ($data[oldordno]!=""){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
		<? if ($data[escrowyn]=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
		<? if ($data[eggyn]=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
		<? if ($data[inflow]!="" && $data[inflow]!="sugi"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/inflow_<?=$data[inflow]?>.gif" align=absmiddle></a><? } ?>
		<? if ($data[cashreceipt]!=""){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
		<? if ($data[cbyn]=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
		<font class=small1 color=444444><?=$goodsnm?>
			<? if (($_cnt = $db->count_($_res))>1){ ?>�� <?=$_cnt-1?>��<? } ?>
		</font>
	</div>
	</td>
	<td>
		<?php if($data[m_id]){ ?>
			<?php if($data['dormant_regDate'] == '0000-00-00 00:00:00'){ ?>
				<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><span class="small1" style="color:#0074BA"><strong><?=$data['nameOrder']?></strong> (<?=$data[m_id]?>)</span></span>
			<?php } else { ?>
				<span class="small1" style="color:#0074BA"><strong><?=$data['nameOrder']?></strong>(<?=$data[m_id]?> / �޸�ȸ��)</span>
			<?php } ?>
		<?php } else { ?>
			<span class="small1"><?=$data['nameOrder']?></span>
		<?php } ?>
	</td>
	<td><font class=small1 color=444444><?=$data[nameReceiver]?></td>
	<td class=small4><?=$r_settlekind[$data[settlekind]]?></td>
	<td class=ver81><b><?=number_format($data[prn_settleprice])?></b></td>
	<td class=small4 width=60><?=$step?></td>
</tr>
<tr><td colspan=20 bgcolor=E4E4E4></td></tr>
<?
	}
	$cnt = $pr * ($idx+1);
?>
<tr>
	<td>

	<a href="javascript:chkBoxAll(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif" border=0></a>

	</td>
	<td align=right height=30 colspan=9 style=padding-right:8>�հ�: <!--(<?=$cnt?>��)--> <font class=ver9><b><?=number_format($grp[settleprice][$preStepMsg])?></font>��</b></td>
	<td colspan=3></td>
</tr>
<tr bgcolor=#f7f7f7 height=30>
	<td colspan=10 align=right style=padding-right:8>��ü�հ� : <span class=ver9><b><?=number_format(@array_sum($grp[settleprice]))?>��</b></span></td>
	<td colspan=3></td>
</tr>

<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>

</table>

<div class=pageNavi align=center><font class=ver8><?=$pg->page[navi]?></font></div>


������ �ֹ��� : <select name="processType"><option value="">--------- ���� ---------</option><option value="sms">�Աݿ�û SMS �߼�</option><option value="confirm">�Ա�Ȯ�� ó��</option></select>
<img src="../img/btn_confirm_mini.gif" border="0" onclick="fnRequestBanking();" class="hand" alt="Ȯ��" align="absmiddle">


</form>


<p>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���������� �ֹ��� ���� �� �Աݴ�� ������ �ֹ��ǿ� ���� ����Ʈ�Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��� ���ڸ� Ȯ�� �Ͻ� �� �Աݿ�û SMS(����)�� �߼��ϰ��� �ϴ� �ֹ����� ������ �ּ���.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����Ͻ� �� �Աݿ�û SMS�߼��� �Ͻø� �ش� ������ �Աݿ�û ������ ���۵˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�߼۵Ǵ� SMS�� ������ ������������ > ȸ��/SMS EMAIL > SMS���� > SMS�ڵ��߼�/�������� "�Աݿ�û �߼�" �������� �����Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��,SMS�ڵ��߼�/���� �޴��� �ִ� �Աݿ�û �߼۳��� �ϴܿ� ������ �ڵ��߼��� üũ���� ���� ��쿡�� ������ �ֹ��ǿ� SMS�� �߼۵��� �ʽ��ϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ա��� Ȯ�� �� �ֹ����� ���� �Ͻ� �� �Ա�Ȯ�� ���·� �����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ա� Ȯ�ε� �ֹ����� �ֹ�����Ʈ���� Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>


</table>
</div>
<script>cssRound('MSG01')</script>

<script>window.onload = function(){ UNM.inner();};</script>
<? @include dirname(__FILE__) . "/../interpark/_order_list.php"; // ������ũ_��Ŭ��� ?>

<? include "../_footer.php"; ?>
