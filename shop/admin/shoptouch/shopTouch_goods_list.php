<?
$location = "���θ� App���� > ���θ� App ��ǰ����Ʈ";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
}

### ���� ����
$_GET[sword] = trim($_GET[sword]);

$cnt_query = $db->_query_print('SELECT COUNT(*) total FROM '.GD_GOODS.' g JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno WHERE g.todaygoods=[s]', 'n');
$res_cnt = $db->_select($cnt_query);
$total = $res_cnt[0]['total'];

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";
$checked[open_shoptouch][$_GET[open_shoptouch]] = "checked";

$orderby = ($_GET[sort]) ? $_GET[sort] : "-g.goodsno";
$div = explode(" ",$orderby);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$orderby)) ? "��" : "��";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$table = '
	'.GD_GOODS.' g 
	JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno
	LEFT JOIN '.GD_GOODS_OPTION.' go ON g.goodsno = go.goodsno AND link
';

$arr_where[] = "g.todaygoods='n'";
if ($category){
	$table .= 'LEFT JOIN '.GD_GOODS_LINK.' gl on g.goodsno=gl.goodsno';
	$arr_where[] = sprintf("category like '%s%%'", $category);
}
if ($_GET[sword]) $arr_where[] = "$_GET[skey] like '%$_GET[sword]%'";
if ($_GET[price][0] && $_GET[price][1]) $arr_where[] = "go.price between {$_GET[price][0]} and {$_GET[price][1]}";
if ($_GET[brandno]) $arr_where[] = "g.brandno='$_GET[brandno]'";
if ($_GET[regdt][0] && $_GET[regdt][1]) $arr_where[] = "g.regdt between date_format({$_GET[regdt][0]},'%Y-%m-%d 00:00:00') and date_format({$_GET[regdt][1]},'%Y-%m-%d 23:59:59')";
if ($_GET[open]) $arr_where[] = "g.open=".substr($_GET[open],-1);
if ($_GET[open_shoptouch]) $arr_where[] = "sg.open_shoptouch=".substr($_GET[open_shoptouch],-1);

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = "
	DISTINCT g.goodsno, 
	g.goodsno,
	g.goodsnm,
	g.img_s,
	g.icon,
	g.open,
	g.goodsno,
	g.goodsnm,
	g.img_s,
	g.icon,
	g.open,
	g.regdt,
	g.runout,
	g.usestock,
	g.inpk_prdno,
	g.totstock,
	g.use_emoney,
	sg.img_shoptouch,
	sg.open_shoptouch,
	go.price,
	go.reserve
";

$pg->setQuery($table,$arr_where,$orderby);
$pg->exec();
$res = $db->query($pg->query);

?>

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
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function openGoods() {

	var bool_checked = false;

	var cmd_open = document.getElementsByName('cmd_open');
	var cmd_open_shoptouch = document.getElementsByName('cmd_open_shoptouch');

	for(var i =0; i< cmd_open.length; i++) {
		if(cmd_open[i].checked) {
			bool_checked = true;
		}
	}

	for(var i =0; i< cmd_open_shoptouch.length; i++) {
		if(cmd_open_shoptouch[i].checked) {
			bool_checked = true;
		}
	}

	if(!bool_checked) {
		alert('�����Ͻ� ��� ���θ� ������ �ּ���');
		return;
	}

	var frm = document.fmList;
	var org_action; 
	var org_method; 
	var org_mode;

	var tag_chk;
	var tcount = 0;
	tag_chk = document.getElementsByName("chk[]"); 
	
	for (i=0; i<tag_chk.length; i++) {
		if (tag_chk[i].checked) 	
			tcount ++; 
	}
	if(frm.range_type1.value == "query_select") {
		if(tcount < 1) {
			alert("��ǰ�� ������ �ֽñ� �ٶ��ϴ�.");
			return;
		}
	}

	org_action = frm.action ; 
	org_method = frm.method ; 
	org_mode = frm.mode.value;

	frm.action = "indb.php"; 
	frm.method = "post"; 
	frm.mode.value = "open_goods"
	frm.submit(); 
	
	frm.action = org_action; 
	frm.method = org_method; 
	frm.mode.value = org_mode;

	
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>
<? 
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=frmList>
<input type=hidden name=sort value="<?=$_GET['sort']?>">

<div class="title title_top">���θ� App ��ǰ����Ʈ <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=5')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
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
	<option value="g.goodsnm" <?=$selected['skey']['g.goodsnm']?>>��ǰ��
	<option value="g.goodsno" <?=$selected['skey']['g.goodsno']?>>������ȣ
	<option value="g.goodscd" <?=$selected['skey']['g.goodscd']?>>��ǰ�ڵ�
	<option value="g.keyword" <?=$selected['skey']['g.keyword']?>>����˻���
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
	<td>���θ���¿���</td>
	<td class=noline>
	<input type=radio name=open value="" <?=$checked[open]['']?>>��ü
	<input type=radio name=open value="11" <?=$checked[open][11]?>>��»�ǰ
	<input type=radio name=open value="10" <?=$checked[open][10]?>>����»�ǰ
	</td>
	<td>���θ� App��¿���</td>
	<td class=noline>
	<input type=radio name=open_shoptouch value="" <?=$checked[open_shoptouch]['']?>>��ü
	<input type=radio name=open_shoptouch value="11" <?=$checked[open_shoptouch][11]?>>��»�ǰ
	<input type=radio name=open_shoptouch value="10" <?=$checked[open_shoptouch][10]?>>����»�ǰ
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
		<img src="../img/sname_date.gif"><a href="javascript:sort('g.regdt desc')"><img name=sort_regdt_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('g.regdt')"><img name=sort_regdt src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('g.goodsnm desc')"><img name=sort_goodsnm_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('g.goodsnm')"><img name=sort_goodsnm src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('go.price desc')"><img name=sort_price_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('go.price')"><img name=sort_price src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_brand.gif"><a href="javascript:sort('g.brandno desc')"><img name=sort_brandno_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('g.brandno')"><img name=sort_brandno src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_company.gif"><a href="javascript:sort('g.maker desc')"><img name=sort_maker_desc src="../img/list_up_off.gif"></a><a href="javascript:sort('g.maker')"><img name=sort_maker src="../img/list_down_off.gif"></a></td>
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

<form name="fmList" method="post" onsubmit="return chkFormList(this)">
<input type="hidden" name="mode" />
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" required msgR="�ϰ�ó�� �� ��ǰ�� ���� �˻��ϼ���.">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=13></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
	<th width=60>��ȣ</th>
	<th></th>
	<th width=10></th>
	<th>��ǰ��</th>
	<th>�����</th>
	<th>����</th>
	<th>���</th>
	<th>���θ����</th>
	<th>���θ� App���</th>
	<th>����</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=13></td></tr>
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
<tr><td height=4 colspan=13></td></tr>
<tr height=25>
	<td class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>
	<td><font class=ver8 color=616161><?=$pg->idx--?></td>
	<td style="border:1px #e9e9e9 solid;"><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td></td>
	<td>
	<a href="javascript:popup('popup.shopTouch_goods_register.php?mode=modify&goodsno=<?=$data[goodsno]?>',850,600)"><font color=303030><?=$data[goodsnm]?></font></a>
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
	<td align=center><img src="../img/icn_<?=$data[open_shoptouch]?>.gif"></td>
	<td align=center><a href="indb.php?mode=copyShoptouchGoods&goodsno=<?=$data[goodsno]?>" onclick="return confirm('������ ��ǰ�� �ϳ� �� �ڵ�����մϴ�')"><img src="../img/i_copy.gif"></a></td>
	<td align=center><a href="shopTouch_goods_register.php?mode=modify&goodsno=<?=$data[goodsno]?>"><img src="../img/i_edit.gif"></a></td>
	<? if ($data[inpk_prdno] != '' && ($inpkCfg['use'] == 'Y'||$inpkOSCfg['use'] == 'Y')){ ?>
	<td align=center><span title="������ũ�� ��ϵ� ��ǰ�� ������ �� �����ϴ�.">��</span></td>
	<? } else { ?>
	<td align=center><a href="indb.php?mode=delShoptouchGoods&goodsno=<?=$data[goodsno]?>" onclick="return confirm('���θ� App ��ǰ������ ���� �˴ϴ�.\n�̳��� ��ǰ������ �����Ͻ÷��� ��ǰ����>��ǰ����Ʈ���� �����Ͻñ� �ٶ��ϴ�. \n\n������ �����Ͻðڽ��ϱ�?')"><img src="../img/i_del.gif"></a></td>
	<? } ?>
</tr>
<tr><td height=4></td></tr>
<tr><td colspan=13 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=6% style="padding-left:12"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=6%></td>
</tr></table>

<div style="padding-top:15px"></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td style="text-align:center;" rowspan="2">
		<select name='range_type1' style='margin-top:5px;width:150px'>
			<option value='query_select'>���õ� ��ǰ�� </option>
			<option value='query_all'>�˻��� ��� ��ǰ��</option>
		</select>
	</td>
	<td class="noline">
		<div><label><input type="radio" name="cmd_open" value="11" /> ���θ��� ����մϴ�.</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="cmd_open" value="10" /> ���θ��� ��� ���� �ʽ��ϴ�.</label></div>
	</td>
</tr>
<tr>
	<td class="noline">
		<div><label><input type="radio" name="cmd_open_shoptouch" value="11" /> ���θ� App�� ����մϴ�.</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="radio" name="cmd_open_shoptouch" value="10" /> ���θ� App�� ��� ���� �ʽ��ϴ�.</label></div>
	</td>	
</tr>
</table>

<p style="text-align:center;"><a href="javascript:openGoods();"><img src="../img/btn_modify.gif" border="0" /></a></p>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App �� ���� Ȥ�� ���θ� App�� ����� ��ǰ����Ʈ �Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�����ư�� ������ �ڵ����� �Ȱ��� ��ǰ�� �����˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ������ �����Ϸ��� ������ư�� ��������. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ� App ��ǰ ���� �̿ܿ� �ٸ������� ���� ��ǰ ������ ���� �ݿ��Ǵ� �����ϼ���.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


<? include "../_footer.php"; ?>
