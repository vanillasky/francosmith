<?
// deprecated. redirect to new page;
header('location: ./adm_goods_manage_soldout.php?'.$_SERVER['QUERY_STRING']);
exit;
/**
	2011-01-18 by x-ta-c
	�ɼ��� ������ ��ǰ�� ǰ�� ���θ� ��ȸ�Ͽ� �̸� ������ �� �ִ�.

	��ǰ ������� �� "ǰ����ǰ"�� üũ�� ��ǰ�� ������ ��(��, gd_goods.runout �ʵ��� ���� 1�� ���ڵ常)
*/

$location = "��ǰ�ϰ����� > ���� ǰ������";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";



// ���� �ޱ� �� �⺻�� ����
	$_GET['cate'] = isset($_GET['cate']) ? $_GET['cate'] : array();
	$_GET['skey'] = isset($_GET['skey']) ? $_GET['skey'] : '';
	$_GET['sword'] = isset($_GET['sword']) ? $_GET['sword'] : '';
	$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;
	$_GET['runout'] = isset($_GET['runout']) ? $_GET['runout'] : 1;		// �⺻�� 1.


// ���� �����$where[] = '  a.runout = 1';	// ���� ǰ�� ��ǰ��
	$db_table = "
	".GD_GOODS." a
	left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and link = 1
	";


	if (!empty($_GET[cate])) {
		$category = array_notnull($_GET[cate]);
		$category = $category[count($category)-1];

		/// ī�װ��� �ִ� ��� ��� ���̺� ������
		if ($category) {
			$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";
			$where[] = "category like '$category%'";
			$groupby = "group by b.sno";
		}
	}

	if ($_GET['runout']) {
		$where[] = '  a.runout = 1';	// ǰ�� ��ǰ��
	}




	if ($_GET[sword]) $where[] = "$_GET[skey] like '%$_GET[sword]%'";


// ��ü ��ǰ�� (ǰ���Ǹ�)
	list ($total) = $db->fetch("select count(*) from ".$db_table." where a.runout = 1");


	$orderby = "a.goodsno desc";


// ���ڵ� ��������
	$pg = new Page($_GET[page],$_GET[page_num]);
	$pg->field = "a.goodsno,a.img_s,a.goodsnm,a.open,a.usestock,a.runout,b.*";
	$pg->setQuery($db_table,$where,$orderby,$groupby);
	$pg->exec();
	$res = $db->query($pg->query);
?>
<script><!--

function fnToggleGoodsStatAll() {
	$$('.goods_stat > input[type="checkbox"]').each(function(o){
		o.checked = (o.checked == true) ? false : true;
		<? /* �ش� ��ü�� �̺�Ʈ�� ���ε� �Ǿ� �����Ƿ� �Ʒ�ó�� trigger �� ���� �ְų�, ���� �Լ��� ȣ���ص� ��. */ ?>
		o.fireEvent('onClick');	// fnToggleGoodsStat(o);
	});
}


function fnToggleGoodsStat(o) {

	var indicator, css = 'hide';

	if (o.checked == true)
		css = 'show';

	for (indicator=o.parentNode.firstChild; indicator.nodeType !== 1; indicator=indicator.nextSibling);
	indicator.className = css;

	return;
}

function fnDeleteCheckedRow() {

	// üũ�Ȱ� Ȯ��
	var cnt=0,i,chk = document.getElementsByName('chk[]');
	for ( i =0;i<chk.length ;i++)
		if (chk[i].checked == true) cnt++;

	if (cnt == 0) {
		alert('�����Ͻ� ��ǰ�� ������ �ּ���.');
		return;
	}


	if (confirm('���� ��ǰ�� �����Ͻðڽ��ϱ�?'))
	{
		var f = document.fmList;
		f.mode.value = 'quickdelete';
		f.submit();
	}

}

function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
--></script>

<style>
div.goods_stat {}
div.goods_stat input {border:none;}
div.goods_stat span {display:block;width:30px;height:12px;}
div.goods_stat span.show {background:url(../img/icn_1.gif) no-repeat 50% 50%;}
div.goods_stat span.hide {background:url(../img/icn_0.gif) no-repeat 50% 50%;}

</style>

<div class="title title_top">���� ǰ������<span>��ϵ� ��ǰ�� ǰ�����θ� �ϰ������� ������ �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=18')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<!-- ��ǰ������� : start -->
<form name=frmFilter onsubmit="return chkForm(this)">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td><font class=small1>�з�����</td>
	<td><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
</tr>
<tr>
	<td><font class=small1>�˻���</td>
	<td>
	<select name=skey>
	<? foreach ( array('goodsnm'=>'��ǰ��','a.goodsno'=>'������ȣ','goodscd'=>'��ǰ�ڵ�','keyword'=>'����˻���') as $k => $v) { ?>
		<option value="<?=$k?>" <?=($k == $_GET['skey']) ? 'selected' : ''?>><?=$v?></option>
	<? } ?>
	<? unset($k,$v) ?>
	</select>
	<input type=text name=sword class=lline value="<?=$_GET[sword]?>" class="line">
	</td>
</tr>
<tr>
	<td><font class=small1>��ǰ��¿���</td>
	<td class="noline">
	<label><input type=radio name=runout value="1" <?=($_GET['runout'] == 1) ? 'checked' : '' ?>>ǰ����ǰ</label>
	<label><input type=radio name=runout value="0" <?=($_GET['runout'] == 0) ? 'checked' : '' ?>>��ü��ǰ</label>
	</td>
</tr>
</table>



<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
	<td align=right>

	<table cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td style="padding-left:20px">
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == $_GET['page_num']) ? 'selected' : ''?>><?=$v?>�� ���
		<? } ?>
		</select>
		</td>
	</tr>
	</table>

	</td>
</tr>
</table>


</form>
<!-- ��ǰ������� : end -->

<form name="fmList" method="post" action="./indb.php" target="_self">
<input type=hidden name=mode value="quickrunout">


<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=12></td></tr>
<tr class=rndbg>
	<th width=60><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
	<th><font class=small1><b>��ȣ</th>
	<th><font class=small1><b></th>
	<th><font class=small1><b>��ǰ��</th>
	<th><font class=small1><b><a href="javascript:fnToggleGoodsStatAll()" class="white">ǰ������</a></th>
</tr>
<tr><td class=rnd colspan=12></td></tr>
<col width=35><col width=50><col width=50><col><col width=70 span=5><col width=60><col width=35>
<?
while (is_resource($res) && $data=$db->fetch($res)){
?>
<tr><td height=4 colspan=12></td></tr>
<tr>
	<td align=center class="noline"><input type=checkbox name=chk[] value="<?=$data[goodsno]?>" onclick="iciSelect(this)"></td>

	<td align=center><font class="ver8" color="#616161"><?=$pg->idx--?></td>
	<td><a href="../../goods/goods_view.php?goodsno=<?=$data[goodsno]?>" target=_blank><?=goodsimg($data[img_s],40,'',1)?></a></td>
	<td><a href="javascript:popup('popup.register.php?mode=modify&goodsno=<?=$data[goodsno]?>',825,600)"><font class=small1 color=0074BA><?=$data[goodsnm]?></font></a></td>

	<td align=center>
		<div class="goods_stat">
			<span class="<?=($data['runout'] == 1) ? 'show' : 'hide'?>"></span>
			<input type="checkbox" name=runout[<?=$data['goodsno']?>] value="1" <?=($data['runout'] == 1) ? 'checked' : ''?> onClick="fnToggleGoodsStat(this);">
			<input type="hidden" name=target[] value="<?=$data['goodsno']?>">
		</div>
	</td>

</tr>
<tr><td height=4></td></tr>
<tr><td colspan=12 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=120 style="padding-left:12">
	<a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	<a href="javascript:fnDeleteCheckedRow();"><img src="../img/btn_all_delet.gif"></a>

</td>
<td width=80% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=120></td>
</tr></table>






<div class=button_top><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ϵ� ��ǰ�� ���¸� �ϰ������� ǰ��ó�� �Ǵ� ���� �� �� �ֽ��ϴ�. </td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰ���� ���������� ����ڰ� ���� ǰ���� üũ�� ��ǰ�� �˻��� �� ���¸� �ϰ� ������ �� �ֽ��ϴ�.</td></tr>
<tr><tr><td style="padding:4px 0 4px 0;font-weight:bold;"><img src="../img/icon_list.gif" align="absmiddle">YES: ǰ��ó������ NO: �ǸŻ���</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">üũ�ڽ��� �̿��Ͽ� ���¸� ������ �� ���� ��ư�� Ŭ���ϸ� ���°� ���� �Ǿ� ���θ� �������� �ݿ��˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>






<? include "../_footer.php"; ?>
