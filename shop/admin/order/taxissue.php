<?

$location = "�Ϲݼ��ݰ�꼭 ���� > �Ϲݹ��೻������Ʈ";
include "../_header.php";

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no where step between 1 and 2"); # �� ���ڵ��

### �����Ҵ�
$tax_step = array( '�����û', '�������', '����Ϸ�', '���ڹ���' );
if (!$_GET[page_num]) $_GET[page_num] = 10; # ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "issuedate desc"; # ���� ����
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";
$checked[sstep][$_GET[sstep]] = "checked";

### ���
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.m_no, b.m_id, b.name as m_name, b.dormant_regDate";
$db_table = "".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
if ($_GET[sstep]) $where[] = "step = '$_GET[sstep]'";
else {
	$where[] = "step=1";
	$checked[sstep][1] = "checked";
}

if ($_GET[skey] && $_GET[sword]){
	$sordno = array();
	if ( $_GET[skey]== 'all' || $_GET[skey]== 'm_name' ){
		$res = $db->query("select a.ordno from ".GD_TAX." a left join ".GD_ORDER." b on a.ordno=b.ordno where b.nameOrder like '%$_GET[sword]%'");
		while( $row = $db->fetch($res)) $sordno[] = $row[ordno];
	}

	if ( $_GET[skey]== 'all' ){
		$where[] = "(concat( a.company, a.name, ifnull(b.name, ''), ifnull(m_id, ''), ordno ) like '%$_GET[sword]%'" .
		(count($sordno) ? " or find_in_set(ordno, '" . implode(",", $sordno) . "')" : "")
		. ")";
	}
	else if ( $_GET[skey]== 'm_id' ) $where[] = "b.m_id like '%$_GET[sword]%'";
	else if ( $_GET[skey]== 'm_name' ) $where[] = "(b.name like '%$_GET[sword]%'" . (count($sordno) ? " or find_in_set(ordno, '" . implode(",", $sordno) . "')" : "") . ")";
	else $where[] = "a.$_GET[skey] like '%$_GET[sword]%'";
}

if ( $_GET[sbusino] <> '' ) $where[] = "a.busino='" . $_GET[sbusino] . "'"; # �з��˻�

if ($_GET[sregdt][0] && $_GET[sregdt][1]) $where[] = "issuedate between date_format({$_GET[sregdt][0]},'%Y-%m-%d') and date_format({$_GET[sregdt][1]},'%Y-%m-%d')";

$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>
<script src="../tax.ajax.js"></script>

<form name=frmList>
<div class="title title_top">�Ϲݹ��೻������Ʈ<span>�����û����Ʈ���� ���ε� ���ݰ�꼭�� ����Ʈ�ϰ� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=9')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=40%>
<tr>
	<td>Ű����˻�����</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected[skey]['all']?>> ���հ˻� </option>
	<option value="company" <?=$selected[skey]['company']?>> ��ȣ </option>
	<option value="name" <?=$selected[skey]['name']?>> ��ǥ�� </option>
	<option value="m_name" <?=$selected[skey]['m_name']?>> ��û�� </option>
	<option value="m_id" <?=$selected[skey]['m_id']?>> ���̵� </option>
	<option value="ordno" <?=$selected[skey]['ordno']?>> �ֹ���ȣ </option>
	</select> <input type="text" NAME="sword" value="<?=$_GET['sword']?>" class=line>
	</td>
	<td>����ڹ�ȣ</td>
	<td><input type=text name=sbusino value="<?=$_GET[sbusino]?>" size=15 maxlength=10 class=line> <span class=small><font color=#5B5B5B>���ڸ� ����</font><span></td>
</tr>
<tr>
	<td>������</td>
	<td colspan="3">
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][0]?>" onclick="calendar(event)" class=cline> -
	<input type=text name=sregdt[] value="<?=$_GET[sregdt][1]?>" onclick="calendar(event)" class=cline>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>�������</td>
	<td colspan="3" class=noline>
	<input type=radio name=sstep value="1" <?=$checked[sstep]['1']?>> �������(�μ� �����)
	<input type=radio name=sstep value="2" <?=$checked[sstep]['2']?>> ����Ϸ�(���޹޴��ڿ� �μ���)
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
	<option value="issuedate desc" <?=$selected[sort]['issuedate desc']?>>- ������ ���ġ�</option>
	<option value="issuedate asc" <?=$selected[sort]['issuedate asc']?>>- ������ ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="printdt desc" <?=$selected[sort]['printdt desc']?>>- �μ��� ���ġ�</option>
	<option value="printdt asc" <?=$selected[sort]['printdt asc']?>>- �μ��� ���ġ�</option>
	<option value="agreedt desc" <?=$selected[sort]['agreedt desc']?>>- ������ ���ġ�</option>
	<option value="agreedt asc" <?=$selected[sort]['agreedt asc']?>>- ������ ���ġ�</option>
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- ��û�� ���ġ�</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- ��û�� ���ġ�</option>
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

<form method="post" action="" name="fmList">
<table width=100% cellspacing=0 cellpadding=0 border=1 bordercolor="#D9D9D9" style="border-collapse: collapse; word-break:break-all;">
<col width=35><col width=35><col width=120><col><col width=10%><col width=15%><col width=75><col width=75><col width=86>
<tr class=rndbg>
	<th rowspan=2>����</th>
	<th rowspan=2>��ȣ</th>
	<th>�ֹ���ȣ</th>
	<th colspan=3>���������</th>
	<th>������</th>
	<th>�μ���</th>
	<th rowspan=2>�������<br>�μ��ϱ�</th>
</tr>
<tr class=rndbg>
	<th>��û�ڸ�</th>
	<th>��ǰ��</th>
	<th>�����ݾ�</th>
	<th>����ݾ�</th>
	<th>��û��</th>
	<th>������</th>
</tr>

<?
while ($data=$db->fetch($res)){

	### �ֹ�����Ÿ
	$query = "select step, step2, prn_settleprice, nameOrder from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);
	$step = $r_stepi[$o_data[step]][$o_data[step2]];

	### ��û��
	if ( !$data[m_no] ) {
		$namestr = $o_data[nameOrder];
	}
	else {
		if($data[m_id] && $data['dormant_regDate'] != '0000-00-00 00:00:00'){
			$namestr = "�޸�ȸ��";
		}
		else {
			$namestr = "{$data[m_name]}/<span id=\"navig\" name=\"navig\" m_id=\"{$data[m_id]}\" m_no=\"{$data[m_no]}\"><font color=0074BA><b>{$data[m_id]}</b></font></span>";
		}
	}

	### �������
	$state = $tax_step[ $data[step] ];
	$state .= " <nobr><a href=\"javascript:;\" onclick=\"var w=popup_return( '../order/_paper.php?type=tax&taxarea=blue&ordno={$data[ordno]}', 'orderPrint', 750, 600 ); w.focus();\"><img src='../img/btn_tax_buyer.gif' border=0></a>";
	$state .= " <nobr><a href=\"javascript:;\" onclick=\"var w=popup_return( '../order/_paper.php?type=tax&taxarea=red&ordno={$data[ordno]}', 'orderPrint', 750, 600 ); w.focus();\"><img src='../img/btn_tax_seller.gif' border=0></a>";

	### �μ���
	if ( str_replace(array("0","-",":"," "), "", $data[printdt]) == '' ) $data[printdt] = "�μ� �����";

	?>
<tr height=25 align="center">
	<td rowspan=2 class=noline><input type=checkbox name=chk[] value="<?=$data[sno]?>" ordno="<?=$data[ordno]?>" onclick="TIM.iciSelect(this)"></td>
	<td rowspan=2><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a> <font class=small color=EA0095><nobr><b><?=$step?></b></font></td>
	<td colspan=3 align=left style="padding:5 0 5 7"><font class=small color=444444>
	����ڹ�ȣ : <?=$data[busino]?>&nbsp;&nbsp;
	ȸ��� : <?=$data[company]?><br>
	��ǥ�ڸ� : <?=$data[name]?>&nbsp;&nbsp;
	���� : <?=$data[service]?>&nbsp;&nbsp;
	���� : <?=$data[item]?><br>
	������ּ� : <?=$data[address]?>
	</td>
	<td><font class=ver8 color=444444><?=$data[issuedate]?></td>
	<td><font class=ver8 color=444444><?=$data[printdt]?></td>
	<td rowspan=2 style="line-height:15pt;"><font color=EA0095><b><?=$state?></b></font></td>
</tr>
<tr height=25 align="center">
	<td><font class=small color=444444><?=$namestr?></td>
	<td align=left style="padding:5 0 5 7"><font class=small color=444444><?=$data[goodsnm]?></td>
	<td><?=number_format($o_data[prn_settleprice])?></td>
	<td style="padding:5 0 5 0">
	<table width=92% border=0 cellspacing=0 cellpadding=0 style="line-height:15pt;">
	<col width=44%>
	<tr><td><font class=small color=444444>����� :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[price])?></td></tr>
	<tr><td><font class=small color=444444>���޾� :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[supply])?></td></tr>
	<tr><td><font class=small color=444444>�ΰ��� :</td><td style="text-align:right;"><font class=ver8 color=444444><?=number_format($data[surtax])?></td></tr>
	</table>
	</td>
	<td><font class=ver8 color=444444><?=$data[regdt]?></td>
	<td><font class=ver8 color=444444><?=$data[agreedt]?></td>
</tr>
<? } ?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:hand" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:TIM.act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:TIM.dnXls();"><img src="../img/btn_order_data.gif" alt="��������" border=0 align=absmiddle></A>
</div>

<div style="clear:both; padding-top:35;"></div>

<!-- �ֹ����� ����Ʈ : Start -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td background="../img/etc_print3.gif"><img src="../img/etc_print1.gif" border="0"></td>
	<td width="19"><img src="../img/etc_print2.gif" border="0"></td>
</tr>
</table>

<div style="border-left:6px #e6e6e6 solid;border-right:6px #e6e6e6 solid;border-bottom:6px #e6e6e6 solid;padding:6 12 10 18;margin-bottom:20pt;">
<form method="get" name="frmPrint">
<input type="hidden" name="type" value="tax">
<input type="hidden" name="ordnos">
<div style="float:left;">
<select NAME="taxarea" style="margin-right:10px;">
<option value="">���ݰ�꼭</option>
<option value="blue">���޹޴��ں�����</option>
<option value="red">�����ں�����</option>
</select>
<strong class=noline><label for="r1"><input class="no_line" type="radio" name="list_type" value="list" id="r1" onclick="openLayer('psrch','none')" checked>��ϼ���</label>&nbsp;&nbsp;&nbsp;<label for="r2"><input class="no_line" type="radio" name="list_type" value="tax_term" id="r2" onclick="openLayer('psrch','block')">�Ⱓ����</label></strong>
</div>

<div style="float:left; margin-left:5px; display:none;" id="psrch">
<input type=text name=regdt[] onclick="calendar(event)" size=12 class=cline> -
<input type=text name=regdt[] onclick="calendar(event)" size=12 class=cline>
</div>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:order_print('frmPrint', 'fmList');"><img src="../img/btn_print.gif" border="0" align="absmiddle"></a>
</form>
</div>
<!-- �ֹ����� ����Ʈ : End -->

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���޹޴��ڿ� ���ݰ�꼭�� �μ��ϸ� ����Ϸ�� ��ȯ�Ǹ�, �μ����� ǥ�� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>



<form name=frmDnXls method=post>
<input type=hidden name=mode value="tax">
<input type=hidden name=query value="<?=$pg->query?>">
</form>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>