<?

$location = "������ũ ���½�Ÿ�� ���� > Ŭ����ó������";
include "../_header.php";
include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".INPK_CLAIM_ITEM." a left join ".INPK_CLAIM." b on a.clmsno=b.clmsno where b.step='c'");

### �����Ҵ�
$clm_tpnms = array( '�Ա���������ֹ����', '��ǰ', '����ȯ' );
$clm_statnms = array( 'Ŭ��������', 'Ŭ�������', '��ǰ/��ȯ��������','��ǰ/��ȯ�԰�Ȯ������',  '��ǰ/��ȯ�԰�Ϸ�', '��ȯ/�����������', '��ȯȮ������', '��ȯ/�������Ϸ�', 'Ŭ����Ȯ��_ȯ�ҿϷ�' );

if (!$_GET['page_num']) $_GET['page_num'] = 20; # ������ ���ڵ��
$selected['page_num'][$_GET['page_num']] = "selected";

$orderby = ($_GET['sort']) ? $_GET['sort'] : "a.latedt desc"; # ���� ����
$selected['sort'][$orderby] = "selected";

$selected['skey'][$_GET['skey']] = "selected";
$selected['sgkey'][$_GET['sgkey']] = "selected";

if (!$_GET['dtkind']) $_GET['dtkind'] = 'latedt'; # ó����
$checked['dtkind'][$_GET['dtkind']] = "checked";

### ���
$db_table = "
".INPK_CLAIM_ITEM." a
left join ".INPK_CLAIM." b on a.clmsno=b.clmsno
";

$where[] = "b.step='c'";
if ($_GET['sword']){
	$_GET['sword'] = trim($_GET['sword']);
	$t_skey = ($_GET['skey']=="all") ? "concat(b.ordno, clm_rsn_tpnm, clm_rsn_dtl)" : $_GET['skey'];
	$t_skey = ($_GET['skey']=="clm_rsn_tpnm") ? "concat(clm_rsn_tpnm, clm_rsn_dtl)" : $t_skey;
	$where[] = "$t_skey like '%{$_GET['sword']}%'";
}
if ($_GET['sgword']){
	$_GET['sgword'] = trim($_GET['sgword']);
	$where[] = "{$_GET['sgkey']} like '%{$_GET['sgword']}%'";
	$db_table .= " left join ".GD_ORDER_ITEM." c on a.item_sno=c.sno";
}
if ($_GET['clm_tpnm']){
	$where[] = "clm_tpnm in ('".implode("','",$_GET['clm_tpnm'])."')";
	foreach ($_GET['clm_tpnm'] as $v) $checked['clm_tpnm'][$v] = "checked";
}
if ($_GET['clm_statnm']){
	$where[] = "clm_statnm in ('".implode("','",$_GET['clm_statnm'])."')";
	foreach ($_GET['clm_statnm'] as $v) $checked['clm_statnm'][$v] = "checked";
}
if ($_GET['regdt'][0]){
	if (!$_GET['regdt'][1]) $_GET['regdt'][1] = date("Ymd");
	$where[] = "{$_GET[dtkind]} between date_format({$_GET['regdt'][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET['regdt'][1]},'%Y-%m-%d 23:59:59')";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "*";
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

?>

<div class="title title_top">Ŭ����ó������<span>������ũ�κ��� ������ Ŭ����(�Ա�����������/��ǰ/����ȯ) �����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=25')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>


<!-- �˻����� : start -->
<form name=frmList onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL style="width:250">
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�˻� (����)</td>
	<td>
	<select name=skey>
	<option value="all"> = ���հ˻� =
	<option value="b.ordno" <?=$selected['skey']['b.ordno']?>> �ֹ���ȣ
	<option value="clm_rsn_tpnm" <?=$selected['skey']['clm_rsn_tpnm']?>>Ŭ���ӻ���
	</select>
	<input type=text name=sword value="<?=$_GET['sword']?>">
	</td>
	<td><font class=small1>��ǰ�˻� (����)</td>
	<td>
	<select name=sgkey>
	<option value="goodsnm" <?=$selected['sgkey']['goodsnm']?>> ��ǰ��
	<option value="brandnm" <?=$selected['sgkey']['brandnm']?>> �귣��
	<option value="maker" <?=$selected['sgkey']['maker']?>> ������
	<option value="a.goodsno" <?=$selected['sgkey']['a.goodsno']?>>������ȣ
	</select>
	<input type=text name=sgword value="<?=$_GET['sgword']?>">
	</td>
</tr>
<tr>
	<td>Ŭ��������</td>
	<td class=noline colspan="3">
	<? foreach ($clm_tpnms as $v){ ?>
	<input type=checkbox name="clm_tpnm[]" value="<?=$v?>" <?=$checked['clm_tpnm'][$v]?>><?=$v?>
	<? } ?>
	</td>
</tr>
<tr>
	<td>ó������</td>
	<td class=noline colspan="3">
	<? foreach ($clm_statnms as $k => $v){ echo ($k == 5 ? '<br>' : '');?>
	<input type=checkbox name="clm_statnm[]" value="<?=$v?>" <?=$checked['clm_statnm'][$v]?>><?=$v?>
	<? } ?>
	</td>
</tr>
<tr>
	<td><font class=small1>ó������</td>
	<td colspan=3>
	<span class="noline small1" style="color:5C5C5C; margin-right:20px;">
	<input type=radio name=dtkind value="latedt" <?=$checked['dtkind']['latedt']?>>ó����
	<input type=radio name=dtkind value="clm_dt" <?=$checked['dtkind']['clm_dt']?>>������
	</span>
	<input type=text name=regdt[] value="<?=$_GET['regdt'][0]?>" onclick="calendar()" size=12> -
	<input type=text name=regdt[] value="<?=$_GET['regdt'][1]?>" onclick="calendar()" size=12>
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

<table width=100%>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode[total])?></b>��, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
	<select name="sort" onchange="this.form.submit();">
	<option value="latedt desc" <?=$selected[sort]['latedt desc']?>>- ó���� ���ġ�</option>
	<option value="latedt asc" <?=$selected[sort]['latedt asc']?>>- ó���� ���ġ�</option>
	<option value="clm_dt desc" <?=$selected[sort]['clm_dt desc']?>>- ������ ���ġ�</option>
	<option value="clm_dt asc" <?=$selected[sort]['clm_dt asc']?>>- ������ ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="ordno desc" <?=$selected[sort]['ordno desc']?>>- �ֹ���ȣ ���ġ�</option>
	<option value="ordno asc" <?=$selected[sort]['ordno asc']?>>- �ֹ���ȣ ���ġ�</option>
	</select>&nbsp;
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?>><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>

</form>
<!-- �˻����� : end -->


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b>Ŭ��������</th>
	<th><font class=small1><b>�ֹ���ȣ</th>
	<th><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b>Ŭ���Ӽ���</th>
	<th><font class=small1><b>Ŭ���ӻ���</th>
	<th><font class=small1><b>������</th>
	<th><font class=small1><b>ó����</th>
	<th><font class=small1><b>ó������</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=120><col width=120><col><col width=60><col width=100><col width=65 span=2><col width=70>
<?
while (is_resource($res) && $data=$db->fetch($res))
{
	$gItem = $db->fetch("select goodsnm, opt1, opt2, addopt from ".GD_ORDER_ITEM." where sno='{$data['item_sno']}'");
	$goodsnm = $gItem['goodsnm'];
	if ($gItem['opt1']) $goodsnm .= "[{$gItem['opt1']}" . ($gItem['opt2'] ? "/{$gItem['opt2']}" : "") . "]";
	if ($gItem['addopt']) $goodsnm .= "<div>[" . str_replace("^","] [",$gItem[addopt]) . "]</div>";
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=18>
	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></font></td>
	<td align=center><font class="small"><?=$data['clm_tpnm']?></font></td>
	<td>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=ver81 color=0074BA><b><?=$data['ordno']?></b></font></a>
	<a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td><font class=small><?=$goodsnm?></font></td>
	<td align=center><font class="ver81" color="#444444"><b><?=number_format($data['clm_qty'])?></b></font></td>

<? if ($data['clm_rsn_dtl'] == ''){ ?>
	<td align=center><font class="small" color="#444444"><?=$data['clm_rsn_tpnm']?></font></td>
<? } else { ?>
	<td align=center style="cursor:default" onmouseover="this.getElementsByTagName('div')[0].style.display='block';" onmouseout="this.getElementsByTagName('div')[0].style.display='none';">
	<font class="small" color="#0074BA"><?=$data['clm_rsn_tpnm']?></font>
	<div style="position:relative; display:none;">
		<div style="position:absolute; background-color:#eeeeee; border:solid 1px #dddddd; filter:Alpha(Opacity=90); opacity:0.9; padding:5; top:0px; width:200px; text-align:left;">
			<?=$data['clm_rsn_dtl']?>
		</div>
	</div>
	</td>
<? } ?>

	<td align=center><font class="small" color="#444444"><?=substr($data['clm_dt'],2,8)?></font></td>
	<td align=center><a href="javascript:popupLayer('popup.log.php?itmsno=<?=$data['itmsno']?>')"><font class="small" color="#0074BA"><u><?=($data['clm_statnm'] == 'Ŭ��������' ? '' : substr($data['latedt'],2,8))?></u></font></a></td>
	<td align=center>
<? if (in_array($data['clm_statnm'], array('��ǰ/��ȯ��������', '��ȯ/�����������'))){ ?>
	<a href="../order/view.php?ordno=<?=$data['ordno']?>"><font class=small color=#E97D00><u><b><?=$data['clm_statnm']?></b></u></font></a>
<? } else { ?>
	<font class=small color=0074BA><b><?=$data['clm_statnm']?></b></font>
<? } ?>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">Ŭ�������� �ȳ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>�Ա���������ֹ���� : ���(�����) ���� �����ڰ� �ֹ���Ҹ� ��û�Ͽ� �Ǹ��ڰ� ��û�� ������ Ŭ�����Դϴ�.</li>
<li>��ǰ : ���(�����) �Ŀ� �����ڰ� ��ǰ�� ��û�Ͽ� �Ǹ��ڰ� ��û�� ������ Ŭ�����Դϴ�.</li>
<li>����ȯ : ���(�����) �Ŀ� �����ڰ� ��ȯ�� ��û�Ͽ� �Ǹ��ڰ� ��û�� ������ Ŭ�����Դϴ�.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó������ �ȳ�
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>Ŭ�������� : ������ Ŭ����(���/��ǰ/��ȯ)�� ������ �����Դϴ�.</li>
<li>Ŭ������� : ������ Ŭ����(���/��ǰ/��ȯ)�� ��ҵ� �����Դϴ�.</li>
<li>��ǰ/��ȯ�������� : ��ǰ/��ȯ �������� ��û��ǰ�� ����(ȸ��)�϶�� ������ũ�� ���÷�, ����(ȸ��)�Ǹ� <b>[��ǰ/��ȯ�԰�Ȯ��]</b>�� �����մϴ�.</li>
<li>��ǰ/��ȯ�԰�Ȯ������ : ��ǰ/��ȯ �������� <b>[��ǰ/��ȯ�԰�Ȯ��]</b>�� ������ũ�� ������ �����Դϴ�.</li>
<li>��ǰ/��ȯ�԰�Ϸ� : ��ǰ/��ȯ �������� <b>[��ǰ/��ȯ�԰�Ȯ��]</b>�� ������ũ�� ���޵� �����Դϴ�.</li>
<li>��ȯ/����������� : ��ȯ �������� ��û��ǰ�� �����϶�� ������ũ�� ���÷�, ���۽� <b>[��ȯȮ��]</b>�� �����մϴ�.</li>
<li>��ȯȮ������ : ��ȯ �������� <b>[��ȯȮ��]</b>�� ������ũ�� ������ �����Դϴ�.</li>
<li>��ȯ/�������Ϸ� : ��ȯ �������� <b>[��ȯȮ��]</b>�� ������ũ�� ���޵� �����Դϴ�.</li>
<li>Ŭ����Ȯ��_ȯ�ҿϷ� : ���/��ǰ �������� ȯ�ҵǾ� Ŭ������ Ȯ���� �����Դϴ�.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">Ŭ���ӻ����� ���콺�� �ø��ø� �󼼻����� Ȯ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó������ Ŭ���Ͻø� ������ũ ó���α׸� Ȯ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>