<?
/*********************************************************
* ���ϸ�     :  linkGoodsSalesStatusList.php
* ���α׷��� :  �ǸŻ��� ����
* �ۼ���     :  ����
* ������     :  2012.05.24
**********************************************************/
/*********************************************************
* ������     :
* ��������   :
**********************************************************/
$location = "���� > �ǸŻ��� ����";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
include "../../lib/sAPI.class.php";

list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

if(!$cust_seq || !$cust_seq) {
	msg("������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.");
	go("./setting.php");
}

$base_delivery = $set['delivery']['default'];
$base_delivery_type = $set['delivery']['deliveryType'];

//��/����|�⺻��ۺ�|~���̻󹫷�|���ҹ�۸޼���
$sAPI = new sAPI();

$grp_cd = Array("grp_cd"=>"MALL_CD");
$arr_mall_cd = $sAPI->getCode($grp_cd, 'hash');

$grp_cd = Array("grp_cd"=>"SALE_STATUS");
$arr_mall_status = $sAPI->getCode($grp_cd, 'hash');

$tmp_mall_set = $sAPI->getSetList();
$arr_mall_set = array();

if(is_array($tmp_mall_set) && !empty($tmp_mall_set)) {

	foreach($tmp_mall_set as $row_mall_set) {
		$arr_mall_set[$row_mall_set['mall_cd']][] = $row_mall_set;
	}
}


$arr_mall_goods_cd = $sAPI->getMallGoodsUrl();//���� url
$arr_mall_goods_cd = $arr_mall_goods_cd[0];
### ���� ����
$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_MARKET_GOODS." WHERE link_yn='y'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";

$mall_status = $_GET['status'];
if($mall_status) {
	foreach($mall_status as $mall) {
		$checked['status'][$mall] = 'checked';
	}
}

$mall_cd = $_GET['mall'];
if($mall_cd) {
	foreach($mall_cd as $mall) {
		$checked['mall'][$mall] = 'checked';
	}
}

$order_by = ($_GET['sort']) ? $_GET['sort'] : "-a.link_date";
$div = explode(" ",$order_by);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$order_by)) ? "��" : "��";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}

$db_table = GD_MARKET_GOODS.' a ';
$db_table .= 'left join '.GD_GOODS.' b on a.goodsno=b.goodsno ';
$db_table .= 'left join '.GD_GOODS_OPTION.' o on a.goodsno=o.goodsno AND o.link=1 and go_is_deleted <> \'1\' ';

if ($category){//�з�����
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}

if($_GET['brandno']) $where[] = $db->_query_print('b.brandno = [i]', $_GET['brandno']);//�귣��
//if($_GET['open']) $where[] = $db->_query_print('a.open = [i]', substr($_GET[open],-1));//��ǰ��¿���
if($_GET['skey'] == 'mall_goods_cd') $where[] = $db->_query_print('a.'.$_GET['skey'].' like [s]', '%'.$_GET['sword'].'%');//�˻���
else if($_GET['sword']) $where[] = $db->_query_print('b.'.$_GET['skey'].' like [s]', '%'.$_GET['sword'].'%');//�˻���

if($_GET['regdt'][0] && $_GET['regdt'][1]) {//��ǰ�����
	$tmp_sdate = substr($_GET['regdt'][0], 0, 4).'-'.substr($_GET['regdt'][0], 4, 2).'-'.substr($_GET['regdt'][0], 6, 2);
	$tmp_edate = substr($_GET['regdt'][1], 0, 4).'-'.substr($_GET['regdt'][1], 4, 2).'-'.substr($_GET['regdt'][1], 6, 2);
	$where[] = $db->_query_print('b.regdt >= [s] AND b.regdt <= [s]', $tmp_sdate.' 00:00:00', $tmp_edate.' 23:59:59');
}

if($_GET['mall']) {
	$where[] = $db->_query_print(' a.mall_cd in [v] ',$_GET['mall']);
}

if($_GET['status']) {
	$where[] = $db->_query_print(' a.sale_status in [v] ',$_GET['status']);
}

if($_GET['stock_off_goods']) {
	$where[] = $db->_query_print(' b.runout=[s]', '1');
	$checked['stock_off_goods']['y'] = 'checked';
}
$where[] = " a.link_yn='y'";

$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = 'DISTINCT a.mall_goods_cd, a.goodsno,a.glink_idx,a.mall_cd,a.set_cd,a.sale_start_date,a.sale_end_date,a.sale_status,a.link_date,b.goodsnm,b.delivery_type,b.goods_delivery,b.updatedt,b.runout,b.img_l, o.price';//,o.price';//�˻��ʵ�

$pg->setQuery($db_table,$where,$order_by);
$pg->exec();

$res = $db->query($pg->query);

$arr_delivery_type = array(
	0 => '�⺻�����å',
	1 => '������',
	2 => '��ǰ�� ��ۺ�',
	3 => '���� ��ۺ�',
	4 => '���� ��ۺ�',
	5 => '������ ��ۺ�',
);
?>

<script>

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

function all_check(name) {//���� ��ũ���� ��ü����
	var obj = document.getElementsByName(name);
	chkBox(document.getElementsByName(name),obj[0].checked);
}

var popup_no = 0;
function frm_check() {
	var change_status = document.getElementsByName('sale_status')[0].value;
	if(!change_status) {
		alert('������ ���¸� ������ �ּ���.');
		return;
	}

	popup_return('_blank.php', 'slink_pop'+popup_no, 800, 700, '', '', 1);//, left, top, scrollbars )

	var fm = document.frmGoodsList;
		fm.target = "slink_pop"+popup_no;
		fm.action = "goodsLinkPop.php";
		fm.submit();
	popup_no ++;
}

window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>

<form name="frmList">
	<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">�ǸŻ��� ����<span>��ũ �Ǿ��ִ� ��ǰ�� �ǸŻ��¸� �����ϴ� ����Ʈ �Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=9')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

	<table class="tb">
		<col class="cellC"><col class="cellL" style="width:500px">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>�з�����</td>
			<td colspan="3"><script>new categoryBox('cate[]',4,'<?=$category?>');</script></td>
		</tr>
		<tr>
			<td>�˻���</td>
			<td colspan="3">
			<select name="skey">
				<option value="goodsnm" <?=$selected['skey']['goodsnm']?>>��ǰ��</option>
				<option value="goodsno" <?=$selected['skey']['goodsno']?>>������ȣ</option>
				<option value="goodscd" <?=$selected['skey']['goodscd']?>>��ǰ�ڵ�</option>
				<option value="keyword" <?=$selected['skey']['keyword']?>>����˻���</option>
				<option value="mall_goods_cd" <?=$selected['skey']['mall_goods_cd']?>>���ϻ�ǰ�ڵ�</option>
			</select>
			<input type=text name="sword" value="<?=$_GET['sword']?>" class="line" style="height:22px">
			</td>
		</tr>
		<tr>
			<td>��ǰ�����</td>
			<td>
				<input type=text name="regdt[]" value="<?=$_GET['regdt'][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline"> -
				<input type=text name="regdt[]" value="<?=$_GET['regdt'][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="cline">
				<a href="javascript:setDate('regdt[]',<?=date('Ymd')?>,<?=date('Ymd')?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-7 day'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_week.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-15 day'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-1 month'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_month.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]',<?=date('Ymd',strtotime('-2 month'))?>,<?=date('Ymd')?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle"></a>
				<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
			</td>
			<td>�귣��</td>
			<td>
				<select name="brandno">
					<option value="">-- �귣�� ���� --</option>
					<?
					$bRes = $db->query("select * from gd_goods_brand order by sort");
					while ($tmp=$db->fetch($bRes)){ ?>
						<option value="<?=$tmp[sno]?>" <?=$selected[brandno][$tmp[sno]]?>><?=$tmp[brandnm]?></option>
					<? } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>���� �ǸŻ���</td>
			<td class=noline colspan="3">
				<label><input type="checkbox" name="status[]" value="all" <?=$checked['status']['all']?> onclick="all_check('status[]')">��ü</label>
				<? if(is_array($arr_mall_status) && !empty($arr_mall_status)) { ?>
				<? foreach($arr_mall_status as $key => $val) {?>
					<label><input type="checkbox" name="status[]" value="<?=$key?>" <?=$checked['status'][$key]?>><?=$val?></label>
				<? } ?>
				<? } ?>
			</td>
		</tr>
		<tr>
			<td>��ũ ����</td>
			<td class=noline colspan="3">
				<label><input type="checkbox" name="mall[]" value="all" <?=$checked['mall']['all']?> onclick="all_check('mall[]')">��ü</label>
				<? if(is_array($arr_mall_status) && !empty($arr_mall_status)) { ?>
				<? foreach($arr_mall_cd as $key => $val) {?>
					<? if($key == 'mall0005') continue; ?>
					<label><input type="checkbox" name="mall[]" value="<?=$key?>" <?=$checked['mall'][$key]?>><?=$val?></label>
				<? } ?>
				<? } ?>
			</td>
		</tr>
		<tr>
			<td>ǰ���� ��ǰ</td>
			<td class=noline colspan="3">
				<label><input type="checkbox" name="stock_off_goods" value="y" <?=$checked['stock_off_goods']['y']?>>e�������� ǰ���� ��ǰ�� �˻��մϴ�.</label>
			</td>
		</tr>
	</table>

	<div class="button_top"><input type="image" src="../img/btn_search2.gif"></div>

	<div style="padding-top:15px"></div>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td class="pageInfo"><font class="ver8">
			�� <b><?=$total?></b>��, �˻� <b><?=$pg->recode['total']?></b>��, <b><?=$pg->page['now']?></b> of <?=$pg->page['total']?> Pages
			</td>
			<td align="right">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td valign="bottom">
				<img src="../img/sname_date.gif"><a href="javascript:sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_product.gif"><a href="javascript:sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a><img src="../img/sname_dot.gif"><img src="../img/sname_price.gif"><a href="javascript:sort('price desc')"><img name="sort_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:sort('price')"><img name="sort_price" src="../img/list_down_off.gif"></a></td>
				<td style="padding-left:20px">
				<img src="../img/sname_output.gif" align="absmiddle">
				<select name="page_num" onchange="this.form.submit()">
				<?
				$r_pagenum = array(10,20,40,60,100);
				foreach ($r_pagenum as $v){
				?>
				<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
				<? } ?>
				</select>
				</td>
			</tr>
			</table>
			</td>
		</tr>
	</table>
</form>

<form name="frmGoodsList" action="" method="POST">
	<input type="hidden" name="mode" value="status">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr><td class="rnd" colspan="12"></td></tr>
		<tr class="rndbg">
			<th width="5%"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
			<th width="5%">ǰ��</th>
			<th width="5%">����</th>
			<th width="10%">���ϻ�ǰ�ڵ�</th>
			<th width="5%">�̹���</th>
			<th width="20%">��ǰ��</th>
			<th width="15%">��Ʈ��</th>
			<th width="8%">�ǸŰ�</th>
			<th width="7%">��۱���</th>
			<th width="15%">�ǸűⰣ</th>
			<th width="5%">�ǸŻ���</th>
		</tr>
		<tr><td class="rnd" colspan="12"></td></tr>

		<?
		while ($data=$db->fetch($res)) {
			if($data['sale_status'] == '0004' || ($data['goodsnm'] == '' && !$data['goodsnm'])) {
				if($data['sale_status'] == '0004') $goods_nm_show = true;
				$none_data = true;
				$disabled = 'disabled';
			}
		?>
		<tr><td height="4" colspan="12"></td></tr>
		<tr>
			<td align="center" class="noline"><!--����-->
				<input type="checkbox" name="chk[]" <?=$disabled?> value="<?=$data['glink_idx']?>" />
				<? unset($disabled); ?>
			</td>
			<td align="center" class="noline"><!--ǰ��-->
			<? if($data['runout'] == '1') {?>
				<div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div>
			<? } ?>
			</td>
			<td align="center" class="noline"><!--����-->
				<div><font class=ver81 color=444444><?=$arr_mall_cd[$data['mall_cd']]?></div>
				<?
				if(is_array($arr_mall_set[$data['mall_cd']]) && !empty($arr_mall_set[$data['mall_cd']])) {
					$mall_login_id = $set_nm = $etc4 = '';
					foreach($arr_mall_set[$data['mall_cd']] as $set_data) {//�α��� ���̵� �˻�
						if($set_data['set_cd'] == $data['set_cd']) {
							$mall_login_id = $set_data['mall_login_id'];
							$set_nm = $set_data['set_nm'];
							$etc4 = $set_data['etc4'];
							break;
						}
					}
				}
				?>
				<div><font class=ver81 color=444444><?=$mall_login_id?></div>
			</td>
			<td align="center" class="noline"><!--���ϻ�ǰ�ڵ�-->
			<?
				if($data['mall_cd'] == 'mall0007') {
					if($etc4 != '') $goods_url = $etc4.'/products/'.$data['mall_goods_cd'];
					else $goods_url = str_replace('{mall_login_id}', $mall_login_id, str_replace('{mall_goods_cd}', $data['mall_goods_cd'], $arr_mall_goods_cd[$data['mall_cd']]));
				}
				else $goods_url = str_replace('{mall_goods_cd}', $data['mall_goods_cd'], $arr_mall_goods_cd[$data['mall_cd']]);
			?>
				<div><font class=ver81 color=444444><a href="<?=$goods_url?>" target="_blank"><?=$data['mall_goods_cd']?></a></div>
			</td>
			<td align="center"><!--�̹���-->
			<? if(!$data['img_l']) { ?>
					<input type="image" src="../../data/skin/season3/img/common/noimg_100.gif" style="width:30px;height:30px;" onclick="return false;">
			<? }
				else {
					$arr_img = explode('|', $data['img_l']);
					if(strstr($arr_img[0], 'http://')) {
						$img_url = $arr_img[0];
					}
					else {
						$img_url = '../../data/goods/'.$arr_img[0];
					}
					?>
					<input type="image" src="<?=$img_url?>" style="width:30px;height:30px;" onclick="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600); return false;">
					<?
				}
			?>
			</td>
			<td><!--��ǰ��-->
				<? if($none_data == false || $goods_nm_show == true) { ?>
				<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600)"><font color="303030"><?=$data['goodsnm']?></font></a>
				<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
				<input type="hidden" name="goodsnm[<?=$data['glink_idx']?>]" value="<?=$data['goodsnm']?>" class="line" style="height:22px;width:60px;" />
				<? } else { ?>
					��ǰ�� �����Ǿ� �����Ͻ� �� �����ϴ�.
				<? } ?>
			</td>
			<td align="center"><!--��Ʈ��-->
				<font class=ver81 color=444444><?=$set_nm?>
			</td>
			<td align="center"><!--�ǸŰ�-->
				<? if($none_data == false || $goods_nm_show == true) {
					echo number_format($data['price']);
				} else { ?>
				-
				<? } ?>
			</td>
			<td align="center"><!--��۱���-->
			<? if($none_data == false || $goods_nm_show == true) { ?>
				<?=$arr_delivery_type[$data['delivery_type']]; ?>
				<? if($data['delivery_type'] >= 3) { //����, ����, ����, ������ ��ۺ񿡸� ����?>
					<?=$data['goods_delivery']?> ��
				<? }
					else {?>
						<?if($data['delivery_type'] == 1) {
							$goods_delivery = '0';
							$goods_delivery_type = '����';
						} else {
							$goods_delivery = $base_delivery;
							$goods_delivery_type = '����';
						}?>
					<input type="hidden" name="goods_delivery[<?=$data['glink_idx']?>]" value="<?=$goods_delivery?>" />
				<? } ?>
			<? } else { ?>
			-
			<? } ?>
			</td>
			<td align=center><!--�ǸűⰣ-->
				<div><font class=ver81 color=444444><?=substr($data['sale_start_date'], 0, 10)?></div>
				<div><font class=ver81 color=444444>~ <?=substr($data['sale_end_date'], 0, 10)?></div>
			</td>
			<td align=center><!--�ǸŻ���-->
				<? if($data['sale_status'] == '0001') { ?>
				<font color='#0033FF'><b><?=$arr_mall_status[$data['sale_status']]?></b></font>
				<? } else { ?>
				<font color='#CD0000'><b><?=$arr_mall_status[$data['sale_status']]?></b></font>
				<? } ?>
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=12 class=rndline></td></tr>
		<? unset($none_data); ?>
		<? } ?>
	</table>

	<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

	<table class=tb>
		<col class=cellC style="width:150px"><col class=cellL>
		<tr>
			<td>�ǸŻ��� ����</td>
			<td>
				������ ��ǰ
				<select name="sale_status">
					<option value=""> == �ǸŻ��¸� ������ �ּ���. == </option>
				<? foreach($arr_mall_status as $key => $val) {?>
					<option value="<?=$key?>"> <?=$val?> </option>
				<? } ?>
				</select>
				<span class="noline"><input type="image" src="../img/btn_linkgoods.gif" align="absbottom" onclick="frm_check();return false;" alt="�ǸŻ��� ����"></span>
			</td>
		</tr>
	</table>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
��ũ�� ��ǰ�� �ǸŻ��¸� �����Ͻ� �� �ֽ��ϴ�.<br/><br/><br/>

�ǸŻ��´� �Ǹ���, �Ǹ�����, ǰ��, �Ǹ����� �װ��� ���·� ���е˴ϴ�.<br/>
��ũ�� ������ ��ǰ�� �Ǹ��� ���¸� ������ �Ǹ�<br/>
�ǸŻ��� �������� �ٸ� ���·� �����Ͻ� �� �ֽ��ϴ�.<br/>
���Ͽ� ���� ���°� ���� �� �ֽ��ϴ�.<br/><br/><br/>

�˻����� �� ǰ���� ��ǰ�� �����ϰ� �˻��� e�������� ǰ��ó���� ��ǰ�� �˻��Ͻ� �� �ֽ��ϴ�.<br/>
���¸� ������ ��ǰ�� ����Ʈ �ϴܿ��� ������ ���¸� ���� �� �ǸŻ��� ���� ��ư�� ���� SELLY�� ������ �ǸŻ��¸� �����Ͻ� �� �ֽ��ϴ�.<br/><br/><br/>

��ũ�� ��ǰ�� e���� <a href="../goods/list.php"><font color=white><u>[��ǰ����Ʈ]</u></font></a>���� �����Ͻ� ��� �� �̻� e�������� ������ �Ұ����մϴ�.<br/>
e���� <a href="../goods/list.php"><font color=white><u>[��ǰ����Ʈ]</u></font></a>���� ��ǰ�� �����Ͻ� ��� SELLY �����ڿ� �����Ͽ� ������ �Ͻ� �� �ֽ��ϴ�.<br/>
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
