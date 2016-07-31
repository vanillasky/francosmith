<?php

$location = "����ϼ����� > ����ϼ� ��ǰ�̹��� ����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include SHOPROOT."/lib/qfile.class.php";
include SHOPROOT."/conf/config.mobileShop.php";

if(!$_GET[m_mode]) $_GET[m_mode] = 'depend';

$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";

$checked[open][$_GET[open]] = "checked";
$checked[open_mobile][$_GET[open_mobile]] = "checked";
$checked[mlongdesc][$_GET[mlongdesc]] = "checked";
$checked[m_mode][$_GET[m_mode]] = "checked";
$checked[vtype_mlongdesc][$cfgMobileShop[vtype_mlongdesc]] = "checked";

if($_GET['searchYn']=='y'){

### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS."");

if (!$_GET[page_num]) $_GET[page_num] = 10;

$orderby = ($_GET[sort]) ? $_GET[sort] : "-a.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link and go_is_deleted <> '1'
";

if ($category){
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
	$where[] = "category like '$category%'";
}
if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $where[] = "price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $where[] = "brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $where[] = "regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $where[] = "open=".substr($_GET[open],-1);
if (strlen($_GET[open_mobile])>0) $where[] = "open_mobile=".$_GET[open_mobile];
if ($_GET[mlongdesc]) {
	if ($_GET[mlongdesc] == 'Y') $where[] = "length(mlongdesc)>0";
	else if ($_GET[mlongdesc] == 'N') $where[] = "( mlongdesc is null or length(mlongdesc)=0 )";
}

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
distinct a.goodsno,a.goodsnm,a.img_s,a.icon,a.open,a.regdt,a.runout,a.usestock,a.inpk_prdno,a.totstock,a.open_mobile,a.mlongdesc,
b.price,b.reserve,a.use_emoney
";
$pg->setQuery($db_table,$where,$orderby);

$pg->exec();
$res = $db->query($pg->query);

}
?>
<script src="../prototype.js"></script>
<script>

function eSort(obj,fld)
{
	var form = document.frmList;
	if (obj.innerText.charAt(1)=="��") fld += " desc";
	form.sort.value = fld;
	form.submit();
}

function sort(sort)
{
	var fm = document.frmList;
	fm.sort.value = sort;
	fm.submit();
}
function sort_chk(sort)
{
	if (!sort) return;
	sort = sort.replace(" ","_");
	var obj = document.getElementsByName('sort_'+sort);
	if (obj.length){
		div = obj[0].src.split('list_');
		for (i=0;i<obj.length;i++){
			chg = (div[1]=="up_off.gif") ? "up_on.gif" : "down_on.gif";
			obj[i].src = div[0] + "list_" + chg;
		}
	}
}

function iciSelect(obj)
{
	//var row = obj.parentNode.parentNode;
	//row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
}

function chkBoxAll(El,mode)
{
	if (!El || !El.length) return;

	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		iciSelect(El[i]);
	}
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

// ������ ��ǰ�� ����� �󼼼��� ��ȯó���� �����Ѵ�.
function onclickMobileDesc(actionType)
{
	var frm = document.frmList;
	var org_action;
	var org_method;

	org_action = frm.action ;
	org_method = frm.action ;

	frm.action = "mobile_img_indb.php";
	frm.method = "post";

	frm.submit();

	frm.action = org_action;
	frm.method = org_method;
}

// ��ǰ ���� ������ �̹��� ����
function onclickVtypeMlongdesc(no){

	var urlStr = "./indb.php?mode=setVtypeMlongdesc&vtype_mlongdesc="+no+"&dummy+" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get"
	});

	_ID('setWrap').style.display=no=='0'?'none':'block';
}

</script>

<form name=frmList>
<input type='hidden' name='searchYn' value='y' />
<input type='hidden' name='sort' value="<?=$_GET['sort']?>">
<input type='hidden' name='m_cate' value='<?=$category?>' />
<input type='hidden' name='m_skey' value='<?=$_GET[skey]?>' />
<input type='hidden' name='m_sword' value='<?=$_GET[sword]?>' />
<input type='hidden' name='m_open' value='<?=substr($_GET[open],-1)?>' />
<input type='hidden' name='m_price_0' value='<?=$_GET[price][0]?>' />
<input type='hidden' name='m_price_1' value='<?=$_GET[price][1]?>' />
<input type='hidden' name='m_brandno' value='<?=$_GET[brandno]?>' />
<input type='hidden' name='m_regdt_0' value='<?=$_GET[regdt][0]?>' />
<input type='hidden' name='m_regdt_1' value='<?=$_GET[regdt][1]?>' />
<input type='hidden' name='m_open' value='<?=$_GET[open]?>' />
<input type='hidden' name='m_open_mobile' value='<?=$_GET[open_mobile]?>' />


<div class="title title_top">����ϼ� ��ǰ�̹��� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=mobileshopV2&no=6')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="text-align:center;" rowspan="2">
		��ǰ ���� ������<br />�̹��� ����
	</td>
	<td class="noline">
		<div><label><input type='radio' name='vtype_mlongdesc' value='0' <?=$checked['vtype_mlongdesc']['0']?> onclick="onclickVtypeMlongdesc(0)" />�¶��μ� �̹����� �״�� ���</label></div>
		<div style="padding-left:20px;"><font class=small color=6d6d6d>����ϼ����� �̹����� ����� ��, �ε��ӵ��� ������ �� �ֽ��ϴ�.</font></div>
	</td>
</tr>
<tr>
	<td class="noline">
		<div><label><input type='radio' name='vtype_mlongdesc' value='1' <?=$checked['vtype_mlongdesc']['1']?> onclick="onclickVtypeMlongdesc(1)" />����ϼ��� �°� �̹���ũ�⸦ ��ȯ�Ͽ� ���</label></div>
		<div style="padding-left:20px;"><font class=small color=6d6d6d>��ȯ�� �̹����� ������ �� �ִ� ������ ���θ��� �־�� �մϴ�. ������ ��� ����� �Ұ����մϴ�.</font></div>
	</td>
</tr>
</table>

<div id="setWrap" <?php if(!$cfgMobileShop['vtype_mlongdesc']){?>style="display:none;"<?}?>>

<div style="padding-top:15px"></div>

<div class="title title_top">����ϼ� ��ǰ �����̹��� ��ȯ <span>�¶��μ��θ� ��ǰ�� �˻��Ͽ�, ����ϼ� ȯ�濡 ����ȭ�� ��ǰ �����̹����� ��ȯ�� �����մϴ�.</span></div>
<table class=tb>
<col class=cellC><col class=cellL style="width:250px">
<col class=cellC><col class=cellL>
<tr>
	<td>�з�����</td>
	<td colspan=3><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td>�˻���</td>
	<td colspan=3>
	<select name=skey>
	<option value="goodsnm" <?=$selected[skey][goodsnm]?>>��ǰ��
	<option value="a.goodsno" <?=$selected[skey][a.goodsno]?>>������ȣ
	<option value="goodscd" <?=$selected[skey][goodscd]?>>��ǰ�ڵ�
	<option value="keyword" <?=$selected[skey][keyword]?>>����˻���
	</select>
	<input type=text name="sword" value="<?=$_GET[sword]?>" class="line" style="height:22px">
	</td>
</tr>
<tr>
	<td>��ǰ����</td>
	<td><font class=small color=444444>
	<input type=text name=price[] value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="rline"> �� -
	<input type=text name=price[] value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="rline"> ��
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
	<td>����ϻ󼼼���</td>
	<td class=noline colspan=3>
	<input type=radio name=mlongdesc value="" <?=$checked[mlongdesc]['']?>>��ü
	<input type=radio name=mlongdesc value="Y" <?=$checked[mlongdesc][11]?>>����
	<input type=radio name=mlongdesc value="N" <?=$checked[mlongdesc][10]?>>������
	</td>
</tr>
<tr>
	<td>��ǰ��¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
	<td>�������¿���</td>
	<td class=noline>
	<input type=radio name=open_mobile value="" <?=$checked[open_mobile]['']?>>��ü
	<input type=radio name=open_mobile value="1" <?=$checked[open_mobile][1]?>>��»�ǰ
	<input type=radio name=open_mobile value="0" <?=$checked[open_mobile][0]?>>����»�ǰ
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
		<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name=sort_price src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('brandno desc')"><img name=sort_brandno_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('brandno')"><img name=sort_brandno src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('maker desc')"><img name=sort_maker_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('maker')"><img name=sort_maker src="../img/list_down_off.gif"></a></td>
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

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=10></td></tr>
<tr class="rndbg">
	<th width="30"><a href="javascript:void(0)" onClick="chkBoxAll(document.getElementsByName('chk[]'),'rev')" class=white>����</a></th>
	<th></th>
	<th width=10></th>
	<th>��ǰ��</th>
	<th width="60">�����</th>
	<th width="100">������� �󼼼���</th>
	<th width="90">����</th>
	<th width="60">���</th>
	<th width="60">PC����</th>
	<th width="60">���������</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<?
while ($data=$db->fetch($res)){
	$stock = $data['totstock'];

?>
<tr><td height=4 colspan=10></td></tr>
<tr height=25>
	<td class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)" required label=">���û����� �����ϴ�" <?=$disabled?>></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><!--<a href="register.php?mode=modify&goodsno=<?=$data[goodsno]?>">--><?=$data[goodsnm]?></a>
	<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
	<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
	</td>
	<td align=center><font class=ver81 color=444444><?=substr($data[regdt],0,10)?></td>
	<td align=center><? if (strlen($data['mlongdesc'])>0) { echo "<a href=\"javascript:popup('../goods/popup.register.php?mode=modify&goodsno={$data[goodsno]}&call=tabLongdescShow#tabLongdesc',825,600)\"><img src='../img/btn_viewbbs.gif' border=0></a>"; } else { echo "����"; } ?></td>
	<td align=center>
	<font color=4B4B4B><font class=ver81 color=444444><b><?=number_format($data[price])?></b></font>
	<div style="padding-top:2px"></div>
	<img src="../img/good_icon_point.gif" align=absmiddle><font class=ver8><?=number_format($data[reserve])?></font>
	</td>
	<td align=center><font class=ver81 color=444444><?=number_format($stock)?></td>
	<td align=center><img src="../img/icn_<?=$data[open]?>.gif"></td>
	<td align=center><img src="../img/icn_<?=$data[open_mobile]?>.gif"></td>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<?
}
?>
</table>

<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

<div style="padding-top:15px"></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="text-align:center;">
	��ǰ �����̹��� ��ȯ<br />
	<select name='range_type2' style='margin-top:5px;width:150px'>
		<option value='query_select'>���õ� ��ǰ�� </option>
		<option value='query_all'>�˻��� ��� ��ǰ��</option>
	</select>
	<td class="noline">
		<div><label><input type='radio' name='m_mode' value='depend' <?=$checked['m_mode']['depend']?> />����/�˻��� ��ǰ�� ����ϼ� �̹����� ��ȯ �մϴ�. (��ó�� ��ǰ�� ��ȯ)</label></div>
		<div style="padding-left:20px;"><font class=small color=6d6d6d>������ ��ȯ�� ������ ���� ��ǰ �����̹����� ��ȯ ó���մϴ�.</font></div>
		<div><label><input type='radio' name='m_mode' value='force' <?=$checked['m_mode']['force']?> />����/�˻��� ��ǰ�� ����ϼ� �̹����� ��ȯ �մϴ�. (��ó�� ��ǰ�� �����Ͽ� ��ȯ)</label></div>
		<div style="padding-left:20px;"><font class=small color=6d6d6d>������ ��ȯ�� ������ �ִ� ��ǰ �����̹����� �����Ͽ�, ���õ� ��� �̹����� ��ȯ ó���մϴ�.</font></div>
		<div class='small' style="padding:10px 0 0 5px;"><font class="extext">( ��ü��ǰ�� �ϰ���ȯ�� �ҿ�ð����� ���� ��ȸ�� ����� 300�� �����϶��� �����մϴ� )</font></div>
	</td>
</tr>
</table>

<p style="text-align:center;"><a href="javascript:onclickMobileDesc();"><img src="../img/btn_modify.gif" border="0" /></a></p>

</div>

</form>

<? include "../_footer.php"; ?>