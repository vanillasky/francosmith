<?
/*********************************************************
* ���ϸ�     :  goodsLink.php
* ���α׷��� :  ��ǰ��ũ ����
* �ۼ���     :  ����
* ������     :  2012.05.08
**********************************************************/
/*********************************************************
* ������     :
* ��������   :
**********************************************************/
$location = "���� > ��ǰ��ũ ����";
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

$tmp_mall_set = $sAPI->getSetList();
$arr_mall_set = array();
if(is_array($tmp_mall_set) && !empty($tmp_mall_set)) {
	foreach($tmp_mall_set as $row_mall_set) {
		$arr_mall_set[$row_mall_set['mall_cd']][$row_mall_set['mall_login_in']][] = $row_mall_set;
	}
}

### �⺻���� ��ۺ�(����) �������� START ###
$query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'selly', 'basic_payment_delivery_price');
$tmp_data = $db->_select($query);
$base_payment_price = $tmp_data[0]['value'];
### �⺻���� ��ۺ�(����) �������� END ###

$_GET[sword] = trim($_GET[sword]);

list ($total) = $db->fetch("select count(*) from ".GD_GOODS." WHERE todaygoods='n'");

if (!$_GET[page_num]) $_GET[page_num] = 10;
$selected[page_num][$_GET[page_num]] = "selected";
$selected[skey][$_GET[skey]] = "selected";
$selected[brandno][$_GET[brandno]] = "selected";
$checked[open][$_GET[open]] = "checked";

$mall_cd = $_GET['mall'];
if($mall_cd) {
	foreach($mall_cd as $mall) {
		$checked['mall'][$mall] = 'checked';
	}
}

$selected['link_yn'][$_GET['link_yn']] = 'selected';

$order_by = ($_GET['sort']) ? $_GET['sort'] : "-a.goodsno";
$div = explode(" ",$order_by);
$flag['sort'][$div[0]] = (!preg_match("/desc$/i",$order_by)) ? "��" : "��";

if ($_GET[cate]){
	$category = array_notnull($_GET[cate]);
	$category = $category[count($category)-1];
}
$db_table = "
".GD_GOODS." a
left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and b.link=1 and go_is_deleted <> '1'
";

if ($category){//�з�����
	$db_table .= "left join ".GD_GOODS_LINK." c on a.goodsno=c.goodsno";

	// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
	$where[]	= getCategoryLinkQuery('c.category', $category, 'where');
}

if($_GET['brandno']) $where[] = $db->_query_print('a.brandno = [i]', $_GET['brandno']);//�귣��
if($_GET['open']) $where[] = $db->_query_print('a.open = [i]', substr($_GET[open],-1));//��ǰ��¿���
if($_GET['sword']) $where[] = $db->_query_print('a.'.$_GET['skey'].' like [s]', '%'.$_GET['sword'].'%');//�˻���

if($_GET['regdt'][0] && $_GET['regdt'][1]) {//��ǰ�����
	$tmp_sdate = substr($_GET['regdt'][0], 0, 4).'-'.substr($_GET['regdt'][0], 4, 2).'-'.substr($_GET['regdt'][0], 6, 2);
	$tmp_edate = substr($_GET['regdt'][1], 0, 4).'-'.substr($_GET['regdt'][1], 4, 2).'-'.substr($_GET['regdt'][1], 6, 2);
	$where[] = $db->_query_print('a.regdt >= [s] AND a.regdt <= [s]', $tmp_sdate.' 00:00:00', $tmp_edate.' 23:59:59');
}

if($_GET['mall'] && $_GET['link_yn']) {
	if($_GET['link_yn'] == 'n') $not = ' NOT ';
	$tmp_query = $db->_query_print('SELECT * FROM '.GD_MARKET_GOODS.' AS m WHERE  m.mall_cd in [v] AND m.goodsno=a.goodsno', $_GET['mall']);
	$where[] = $not.'EXISTS ('.$tmp_query.')';
}

$where[] = "a.todaygoods='n'";
$pg = new Page($_GET[page],$_GET[page_num]);
$pg->field = 'DISTINCT a.goodsno,a.goodsnm,a.regdt,a.delivery_type,a.goods_delivery,a.totstock,a.open,a.brandno,a.img_l,b.price';//�˻��ʵ�
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

function all_check() {//���� ��ũ���� ��ü����
	var obj = document.getElementsByName('mall[]');
	chkBox(document.getElementsByName('mall[]'),obj[0].checked);
}

var popup_no = 0;

function frm_check() {
	var ch_set = document.getElementsByName('set_cd')[0].value;
	if(!ch_set) {
		alert('��Ʈ�� �����ϼž� �մϴ�.');
		return;
	}
//	popupLayer('goodsLinkPop.php',800,700);
//	popup_return( theURL, winName, Width, Height, left, top, scrollbars )
	popup_return('_blank.php', 'link_pop' + popup_no, 800, 700, '', '', 1);//, left, top, scrollbars )


	var fm = document.frmGoodsList;
		fm.target = "link_pop" + popup_no;
		fm.action = "goodsLinkPop.php";
		fm.submit();
	popup_no ++;
}


window.onload = function(){ sort_chk('<?=$_GET[sort]?>'); }

</script>

<form name="frmList">
	<input type="hidden" name="sort" value="<?=$_GET['sort']?>">
	<div class="title title_top">��ǰ ��ũ<span>�̳����� ��ǰ�� ���¸��Ͽ� �ϰ� ��ũ �Ͻ� �� �ִ� ����Դϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=7')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>

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
			<td>��ǰ��¿���</td>
			<td class=noline colspan="3">
				<input type="radio" name="open" value="" <?=$checked['open']['']?>>��ü
				<input type="radio" name="open" value="11" <?=$checked['open'][11]?>>��»�ǰ
				<input type="radio" name="open" value="10" <?=$checked['open'][10]?>>����»�ǰ
			</td>
		</tr>
		<tr>
			<td>���� ��ũ����</td>
			<td class=noline colspan="3">
				<label><input type="checkbox" name="mall[]" value="all" <?=$checked['mall']['all']?> onclick="all_check()">��ü</label>
				<? if(is_array($arr_mall_cd) && !empty($arr_mall_cd)) { ?>
				<? foreach($arr_mall_cd as $key => $val) {?>
					<? if($key == 'mall0005') continue; ?>
					<label><input type="checkbox" name="mall[]" value="<?=$key?>" <?=$checked['mall'][$key]?>><?=$val?></label>
				<? } ?>
				<? } ?>
				<select name="link_yn">
					<option value="y" <?=$selected['link_yn']['y']?>>��ũ�� ��ǰ</option>
					<option value="n" <?=$selected['link_yn']['n']?>>��ũ���� ���� ��ǰ</option>
				</select>
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
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr><td class="rnd" colspan="12"></td></tr>
		<tr class="rndbg">
			<th width="60"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev')" class=white><font class=small1><b>��ü����</a></th>
			<th>�̹���</th>
			<th>��ǰ��</th>
			<th>�����</th>
			<th>�ǸŰ�</th>
			<th>��۱���</th>
			<th>���</th>
			<th>����</th>
		</tr>
		<tr><td class="rnd" colspan="12"></td></tr>

		<?
		while ($data=$db->fetch($res)) {
		?>
		<tr><td height="4" colspan="12"></td></tr>
		<tr>
			<td align="center" class="noline">
				<input type="checkbox" name="chk[]" value="<?=$data['goodsno']?>" />
			</td>
			<td>
			<? if(!$data['img_l']) { ?>
					<input type="image" src="../../data/skin/season3/img/common/noimg_100.gif" style="width:30px;height:30px;" onclick="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600); return false;">
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
			<td>
				<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$data['goodsno']?>',850,600)"><font color="303030"><?=$data['goodsnm']?></font></a>
				<? if ($icon){ ?><div style="padding-top:3px"><?=$icon?></div><? } ?>
				<? if ($data[runout]){ ?><div style="padding-top:3px"><img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif"></div><? } ?>
				<input type="hidden" name="goodsnm[<?=$data['goodsno']?>]" value="<?=$data['goodsnm']?>" class="line" style="height:22px;width:60px;" />
			</td>
			<td align="center">
				<font class=ver81 color=444444><?=substr($data[regdt],0,10)?>
			</td>
			<td align="center">
				<input type="text" name="price[<?=$data['goodsno']?>]" value="<?=$data['price']?>" class="line" style="height:22px;width:60px;" />
			</td>
			<td align="center">
				<?=$arr_delivery_type[$data['delivery_type']]?>
				<?
					$text_type = 'text';
					unset($type_text);
					switch($data['delivery_type']) {
						case '1' ://������
							$text_type = 'hidden';
							$goods_delivery = '0';
							break;
						case '0' ://�⺻�����å
							if($base_delivery_type == '�ĺ�') {//����
								$goods_delivery = $base_payment_price;
								$type_text = '���� ';
							}
							else {//����
								$goods_delivery = $base_delivery;
								$type_text = '���� ';
							}
							$read_only = 'readonly';
							break;
						default :
							$goods_delivery = $data['goods_delivery'];
					}
				?>

				<div><?=$type_text?></div><div><input type="<?=$text_type?>" name="goods_delivery[<?=$data['goodsno']?>]" value="<?=$goods_delivery?>" class="line" style="height:22px;width:60px;" <?=$read_only?> /></div>
				<?unset($read_only);?>
			</td>
			<td align=center>
				<font class=ver81 color=444444><?=number_format($data['totstock'])?>
			</td>
			<td align=center>
				<img src="../img/icn_<?=$data[open]?>.gif">
			</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colspan=12 class=rndline></td></tr>
		<? } ?>
	</table>

	<div align=center class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div>

	<div style="margin:10px 0"><font class=extext>�� ������ ��ǰ��Ͻ�, ������ �ʿ��� ��ǰ���� �׸��� Ȯ���� �ּ���.<br />
	�������� �ʿ���ϴ� �ʼ������� e���� ��ǰ������ ��ϵǾ� �־�� ������ ���������� ��ǰ�� ��ϵ˴ϴ�.<br />
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=selly&no=3')">[��ǰ���� �ʼ��׸� Ȯ���ϱ�]</a></font></div>

	<table class=tb>
		<col class=cellC style="width:150px"><col class=cellL>
		<tr>
			<td>��Ʈ ����(��ũ�ϱ�)</td>
			<td>
				<select name="set_cd">
					<option value="">��Ʈ�� ������ �ּ���.</option>
					<?
					if(is_array($arr_mall_cd) && !empty($arr_mall_cd)) {
						foreach($arr_mall_cd as $key => $val) {
							if($key == 'mall0005') continue;
							if(is_array($arr_mall_set[$key]) && !empty($arr_mall_set[$key])) {
						?>
						<option value="">=====================</option>
						<?
								foreach($arr_mall_set[$key] as $arr_login_id) {
									foreach($arr_login_id as $data) {
						?>
						<option value="<?=$data['set_cd']?>"><?=$val?>(<?=$data['mall_login_id']?>) : <?=$data['set_nm']?></option>
						<?
									}
								}
							}
						}
					}
					 ?>
				</select>
				������ ��Ʈ ������ �̿��Ͽ�
				<span class="noline"><input type="image" src="../img/btn_linkmarket.gif" onclick="frm_check();return false;" alt="���Ͽ� ��ũ�ϱ�" align="absbottom"></span>
			</td>
		</tr>
	</table>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr><td>
e������ ��ϵ� ��ǰ�� ��Ʈ������ �����Ͽ� ��ǰ��ũ�� �Ͻ� �� �ֽ��ϴ�.<br/>
��ǰ��ũ �õ��� SELLY�� e���� ��ǰ�� �ڵ����� ����� �Ǹ� �̹� ����� �Ǿ� �ִ� ��� �����˴ϴ�.<br/>
SELLY�� ��ǰ�� ���/������ �Ϸ�Ǹ� ���Ͽ� ��ũ�� �õ��ϸ� ��ũ������ ���Ͽ� ������ ��ǰ�� ��ϵ˴ϴ�.<br/><br/><br/>

����Ʈ���� �ǸŰ��� �����Ͽ� ��ǰ��ũ�� �Ͻø� e���� ��ǰ�� �ٸ� �ǸŰ��� ���� ��ǰ�� SELLY�� ���Ͽ� ����� �����մϴ�..<br/>
����Ʈ���� ��۱����� �������� �ƴ� ��� �ǸŰ��� �����ϰ� ��ۺ� ���� �Ͽ� ��ǰ��ũ�� �����մϴ�.<br/>
��ǰ�� �����å�� �⺻�����å�� ��� ����Ʈ���� ��ۺ� �����Ͽ� ��ǰ��ũ�� �Ͻ� �� �����ϴ�.<br/>

�������� �ƴ� ��ǰ�� ��ũ�ϱ� ���ؼ��� <a href="../selly/deliverySetting.php"><font color=white><u>[������ǰ ��ۺ� ����]</u></font></a>���� e���� ��۰��� SELLY ��۰��� ���ν��� �ּž� �մϴ�.<br/><br/><br/>

���Ͽ� ��ũ�� ��ǰ�� �����Ͻ� ���� �ϴܿ��� ��ũ�� ���� ��Ʈ�� �����մϴ�.<br/>
��ǰ�� ��Ʈ�� �����ϼ̴ٸ� ���Ͽ� ��ũ�ϱ� ��ư�� Ŭ���Ͻø� �˾��� ������� �˴ϴ�.<br/>
����� �˾������� ������ ��ϵ� ī�װ��� �����Ͻ� �� ������<br/>
ī�װ� ���� �� ��ũ�ϱ� ��ư�� Ŭ���ϸ� �������� ��ǰ��ũ�� �õ��մϴ�.<br/>
��ũ������ <a href="../selly/linkGoodsList.php"><font color=white><u>[��ũ��ǰ ����]</u></font></a>���� Ȯ��/������ũ�� �Ͻ� �� �ֽ��ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>
