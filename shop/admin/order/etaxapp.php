<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڹ����û����Ʈ";
include "../_header.php";

include "../../lib/page.class.php";

list ($total) = $db->fetch("select count(*) from ".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no where step=0"); # �� ���ڵ��

### �����Ҵ�
$tax_step = array( '�����û', '�������', '����Ϸ�', '���ڹ���' );
if (!$_GET[page_num]) $_GET[page_num] = 10; # ������ ���ڵ��
$selected[page_num][$_GET[page_num]] = "selected";

$orderby = ($_GET[sort]) ? $_GET[sort] : "issuedate desc"; # ���� ����
$selected[sort][$orderby] = "selected";

$selected[skey][$_GET[skey]] = "selected";

### ���
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "a.*, b.m_no, b.m_id, b.name as m_name";
$db_table = "".GD_TAX." a left join ".GD_MEMBER." b on a.m_no=b.m_no";
$where[] = "step=0";

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

<div align="center"><a href="http://www.godobill.com/" target="_blank"><img src="../img/etaxServiceEnd.jpg"></a></div>

<form name=frmList>
<div class="title title_top">���ڹ����û����Ʈ<span>���Ű��� ��û�� ���ݰ�꼭�� ��ȸ�ϰ� ����ó���մϴ�</span></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL width=40%>
<tr>
	<td>Ű����˻�</td>
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
	<option value="regdt desc" <?=$selected[sort]['regdt desc']?>>- ����� ���ġ�</option>
	<option value="regdt asc" <?=$selected[sort]['regdt asc']?>>- ����� ���ġ�</option>
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
<col width=35><col width=35><col width=120><col><col width=12%><col width=15%><col width=74><col width=100>
<tr class=rndbg>
	<th rowspan=2>����</th>
	<th rowspan=2>��ȣ</th>
	<th>�ֹ���ȣ</th>
	<th colspan=3>���������</th>
	<th>������</th>
	<th rowspan=2>�������</th>
</tr>
<tr class=rndbg>
	<th>��û�ڸ�</th>
	<th>��ǰ��</th>
	<th>�����ݾ�</th>
	<th>����ݾ�</th>
	<th>��û��</th>
</tr>

<?
while ($data=$db->fetch($res)){

	### �ֹ�����Ÿ
	$query = "select step, step2, prn_settleprice, nameOrder,cashreceipt from ".GD_ORDER." where ordno='$data[ordno]'";
	$o_data = $db->fetch($query);
	$step = $r_stepi[$o_data[step]][$o_data[step2]];
	$cashMsg = ($o_data[cashreceipt])?"���ݿ���������":"";

	### ��û��
	if ( !$data[m_no] ) $namestr = $o_data[nameOrder];
	else $namestr = "{$data[m_name]}/<span id=\"navig\" name=\"navig\" m_id=\"{$data[m_id]}\" m_no=\"{$data[m_no]}\"><font color=0074BA><b>{$data[m_id]}</b></font></span>";

	### �������
	$state = $tax_step[ $data[step] ];

	?>
<tr height=25 align="center">
	<td rowspan=2 class=noline><input type=checkbox name=chk[] value="<?=$data[sno]?>" subject="�ֹ���ȣ <?=$data[ordno]?>" onclick="TAM.iciSelect(this)"></td>
	<td rowspan=2><font class=ver8 color=444444><?=$pg->idx--?></td>
	<td><a href="javascript:popup('popup.order.php?ordno=<?=$data[ordno]?>',800,600)"><font class=ver81 color=0074BA><b><?=$data[ordno]?></b></font></a> <font class=small color=EA0095><nobr><b><?=$step?></b></font></td>
	<td colspan=3 align=left style="padding:5 0 5 7"><font class=small color=444444>
	����ڹ�ȣ : <input type=text name="busino[<?=$data[sno]?>]" value="<?=$data[busino]?>" style="width:85" maxlength=10 class=line>&nbsp;&nbsp;
	ȸ��� : <input type=text name="company[<?=$data[sno]?>]" value="<?=$data[company]?>" style="width:25%;" class=line><br>
	��ǥ�ڼ��� : <input type=text name="name[<?=$data[sno]?>]" value="<?=$data[name]?>" style="width:85;" class=line>&nbsp;&nbsp;
	����<font color=white>��</font> : <input type=text name="service[<?=$data[sno]?>]" value="<?=$data[service]?>" style="width:17%;" class=line>&nbsp;&nbsp;
	���� : <input type=text name="item[<?=$data[sno]?>]" value="<?=$data[item]?>" style="width:17%;" class=line><br>
	������ּ� : <input type=text name="address[<?=$data[sno]?>]" value="<?=$data[address]?>" style="width:422;" class=line>
	</td>
	<td><input type=text name="issuedate[<?=$data[sno]?>]" value="<?=$data[issuedate]?>" size=10 maxlength=10 style="text-align:center;" class=cline></td>
	<td rowspan=2><font color=EA0095><b><?=$state?></b></font><div style='padding-top:5'><font class=ver81 color=0074BA><b><?=$cashMsg?></b></font></div></td>
</tr>
<tr height=25 align="center">
	<td><font class=small color=444444><?=$namestr?></td>
	<td><input type=text name="goodsnm[<?=$data[sno]?>]" value="<?=$data[goodsnm]?>" style="width:96%;" class=line></td>
	<td><?=number_format($o_data[prn_settleprice])?></td>
	<td><font class=small color=444444>
	����� <input type=text name="price[<?=$data[sno]?>]" value="<?=$data[price]?>" size=8 maxlength=11 style="text-align:right;" onKeyDown="onlynumber();" onkeyup="TAM.putTax( '<?=$data[sno]?>' );" class=rline><br>
	���޾� <input type=text name="supply[<?=$data[sno]?>]" value="<?=$data[supply]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline><br>
	�ΰ��� <input type=text name="surtax[<?=$data[sno]?>]" value="<?=$data[surtax]?>" size=8 maxlength=11 style="text-align:right;" readonly class=rline>
	</td>
	<td><font class=ver8 color=444444><?=$data[regdt]?></td>
</tr>
<? } ?>
</table>
</form>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="float:left;">
<img src="../img/btn_allselect_s.gif" alt="��ü����"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'select', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_allreselect_s.gif" alt="���ù���"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'reflect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldeselect_s.gif" alt="��������"  border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javascript:PubAllSordes( 'deselect', fmList['chk[]'] );"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
<img src="../img/btn_alldelet_s.gif" alt="���û���" border="0" align='absmiddle' style="cursor:pointer" <?if ( $pg->recode[total] != 0 ){?>onclick="javaScript:TAM.act_delete();"<?}else{?>onclick="javascript:alert( '����Ÿ�� �������� �ʽ��ϴ�.' );"<?}?>>
</div>

<div style="float:right;">
<A HREF="javascript:TAM.act_allmodify();"><img src="../img/btn_allmodify_s.gif" alt="�ϰ�����" border=0 align=absmiddle></A>
</div>

<div style="clear:both; text-align:center;">
<a href="javascript:WTS.begin();"><img src="../img/btn_webtax_app.gif" alt="���ڹ����û" border=0 align=absmiddle></a>
</div>

<div style="clear:both; padding-top:35;"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݰ�꼭 ��û�� ���θ��� �ֹ�/�����ȸ�� ���� �޽��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݰ�꼭 ����
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>����� ��ǰ�� ���ܵ˴ϴ�.</li>
<li>��ۺ�� ����ݾ׿� ���Ե��� �ʽ��ϴ�.</li>
<li>����ݾ��� ���ξ�(ȸ������+��������+�����ݻ��+������) ��ŭ �����˴ϴ�.</li>
</ol>
</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ڼ��ݰ�꼭 �����û
<ol type="a" style="margin:0px 0px 0px 40px;">
<li>���ڼ��ݰ�꼭 ������ ��û�մϴ�.</li>
<li>����Ʈ�� �־�߸� �����û�� �����ϸ� ������ 1point �����˴ϴ�.</li>
<li>���޹޴����� ���ݰ�꼭�� �ֹ��ÿ� �ԷµǾ��� �̸��ϰ� �޴������� ���� �߼۵Ǹ� �ȳ��Ǿ����ϴ�.</li>
<li>�������� �������� <b>30�� ������</b> ������ ���ؼ��� ����ǹǷ� <b>�����û���� �������ڸ� Ȯ��</b>�Ͻñ� �ٶ��ϴ�.</font></li>
</ol>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>