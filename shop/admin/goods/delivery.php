<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_delivery.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "��ǰ���� > ���� ��ۺ� ����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[delivery_type][$_GET[delivery_type]] = "checked";

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link
";
$where[] = "a.todaygoods='n'";
if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
if ($_GET[delivery_type] != '') $where[] = "a.delivery_type=".$_GET[delivery_type];

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,a.delivery_type,a.goods_delivery,
b.price,b.reserve,a.use_emoney

";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);

$_ar_delivery_type = array(
0=>'�⺻���',
1=>'������',
2=>'��ǰ�� ��ۺ�',
3=>'���� ��ۺ�',
4=>'���� ��ۺ�',
5=>'������ ��ۺ�',
);
?>
<script>

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

</script>

<form name=frmList>
<div class="title title_top">���� ��ۺ����<span>����Ͻ� ��ǰ�� ��ۺ� �ϰ������� �����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=35')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
	<td>�귣��</td>
	<td>
	<select name=brandno>
	<option value="">-- �귣�� ���� --
	<?
	$bRes = $db->query("select * from gd_goods_brand order by sort");
	while ($tmp=$db->fetch($bRes)){
	?>
	<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
	</td>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
</tr>
<tr>
	<td>��ǰ�����</td>
	<td colspan=3>
	<input type=text name=regdt[] value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
	<input type=text name=regdt[] value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>��ǰ�� ��ۺ�</td>
	<td colspan="3" class="noline">
	<label><input type="radio" name="delivery_type" value="" checked>��ü</label>
	<label><input type="radio" name="delivery_type" value="0" <?=$checked[delivery_type][0]?>>�⺻���</label>
	<label><input type="radio" name="delivery_type" value="1" <?=$checked[delivery_type][1]?>>������</label>
	<label><input type="radio" name="delivery_type" value="2" <?=$checked[delivery_type][2]?>>��ǰ�� ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="4" <?=$checked[delivery_type][4]?>>���� ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="5" <?=$checked[delivery_type][5]?>>������ ��ۺ�</label>
	<label><input type="radio" name="delivery_type" value="3" <?=$checked[delivery_type][3]?>>���� ��ۺ�</label>
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div style="padding-top:15px"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td valign=bottom>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
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

	</td>
</tr>
</table>
</form>

<form name="fmList" method="post" action="./indb.php" target="_self">
<input type=hidden name=mode value="quickdelivery">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<col width="60" align=center>
<col width="60" align=center>
<col width="40">
<col width="10">
<col>
<col width="90">
<col width="90">
<col width="80">
<col width="60">
<col width="100">
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
	<th>��ȣ</th>
	<th></th>
	<th></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����</th>
	<th>���</th>
	<th>����</th>
	<th>��ۺ�</th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=40 span=2 align=center>
<?
while ($data=$db->fetch($res)){
	$stock = $data['totstock'];

	### ������
	if(!$data['use_emoney']){
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) $data['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
		}else{
			$data['reserve']	= $set['emoney']['goods_emoney'];
		}
	}
	$icon = setIcon($data[icon],$data[regdt],"../");

	### ����� ���� �ڵ� ǰ�� ó��
	if ($data[usestock] && $stock==0) $data[runout] = 1;
?>
<tr><td height=4 colspan=12></td></tr>
<tr height=25>
	<td class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<td><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<!--<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',800,600)"><img src="../img/icon_popup.gif" hspace=2 align=absmiddle></a>-->
	<a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><!--<a href="register.php?mode=modify&goodsno=<?=$data[goodsno]?>">--><?=$data[goodsnm]?></font></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td align=center><font class=ver81 color=444444><?=substr($data[regdt],0,10)?></td>
	<td align=center>
	<font color=4B4B4B><font class=ver81 color=444444><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class=ver81 color=444444><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center>
	<?=$_ar_delivery_type[ $data['delivery_type'] ]?>
	<?=($data['delivery_type'] > 1 ? '<br>'.number_format($data['goods_delivery']).'��' : '')?>
	</td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>
<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>



<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��ǰ�� ��ۺ�<br>�ϰ� ����/����</td>
	<td>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="0">������ ��ǰ�� '�⺻�����å�� ����' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="1">������ ��ǰ�� '������' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0;text-decoration: line-through;"><label><input class="null" type="radio" name="set_delivery_type" value="2" disabled>������ ��ǰ�� '��ǰ�� ��ۺ�' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="4">������ ��ǰ�� '���� ��ۺ� : <input type="text" class="line" name="set_goods_delivery4" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[3].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="5">������ ��ǰ�� '������ ��ۺ� : <input type="text" class="line" name="set_goods_delivery5" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[4].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
		<div style="margin:5px 0"><label><input class="null" type="radio" name="set_delivery_type" value="3">������ ��ǰ�� '���� ��ۺ� : <input type="text" class="line" name="set_goods_delivery3" value="" size="8" onFocus="document.getElementsByName('set_delivery_type')[5].checked = true;" onkeydown="onlynumber()">��' ���� �ϰ� ����</label></div>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_modify.gif"></div>

</form>



<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ���� ������ ��ۺ񺰷� ��ǰ�� Ȯ���ϰ� �ϰ������� ��ǰ�� ��ۺ� ����/���� �մϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�⺻�����å�� ��ǰ�� ��ۺ� ��å�� <a href="../basic/delivery.php"><font color="#ffffff"><b>[�⺻���� > ���/�ù�� ����]</b></font></a> ���� ���� �Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�� ��ǰ�� ��ۺ� ��å�� ����Ͽ� �����ϰ� �����Ͽ� �ּ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
