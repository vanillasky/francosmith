<?
/*********************************************************
* ���ϸ�     :  goodsLinkPop.php
* ���α׷��� :  ��ũ�˾�
* �ۼ���     :  ����
* ������     :  2012.05.08
**********************************************************/
/*********************************************************
* ������     :  
* ��������   :  
**********************************************************/
$location = "���� > ��ũ�˾�";
include "../_header.popup.php";
include "../../lib/sAPI.class.php";

$post_data = $_POST;

if(empty($post_data['chk'])) {//������ ��ǰ�� ���� ��� �˾� �ݱ�
	echo '<script>alert("������ ��ǰ�� �����ϴ�.");self.close();</script>';
}

if($_POST['mode']) {//modify = ������ũ, status = ���º���
	$mode = $_POST['mode'];
	unset($_POST['mode']);
}

$sAPI = new sAPI();

### �������� api START ###
$grp_cd = Array("grp_cd"=>"MALL_CD");
$arr_mall_cd = $sAPI->getcode($grp_cd, 'hash');
### �������� api END ###

### ��Ʈ���� api START ###
$set_arr = array();
$set_arr['set_cd'] = $post_data['set_cd'];
$set_data = $sAPI->getSetList($set_arr);
$set_data = $set_data[0];
$mall_cd = $set_data['mall_cd'];
### ��Ʈ���� api END ###

### ��庰 ���ڼ��� START ###
if($mode == 'modify') {//������ũ
	$title_nm = '��ǰ���� ��ũ ����';
	$chk_nm = 'glink_idx';
}
else if($mode == 'status') {//���º���
	$title_nm = '��ǰ���� ����';
	$chk_nm = 'glink_idx';
}
else if($mode == 'extend') {//�Ⱓ����
	$title_nm = '��ǰ�Ⱓ ����';
	$chk_nm = 'glink_idx';
	$mall_cd = $_POST['mall_cd'];
}
else {//��ǰ��ũ
	$title_nm = '��ǰ ��ũ ����';
	$chk_nm = 'goods_no';
}
### ��庰 ���ڼ��� END ###

$arr_delivery_type = Array(
	"���� ��ۺ�" => "�������� ����",
	"���� ��ۺ�" => "���Ҹ� ����",
	"����" => "�������� ����",
	"����" => "���Ҹ� ����",
	"����" => "����",
	"������ ��ۺ�" => "",
);
?>

<script src="js/selly.js"></script>

<script>
var mode = document.getElementsByName('mode');//modify = ������ũ, status = ���º���

var link_no = 0;
function successAjax(data) {//��ũ ���� �� ó��
	var json_data = eval( '(' + data + ')' );

	if(json_data['code'] == '000') {//��ũ���º� ó������

		if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//������ũ or ���º���
	
			var resMsg = '<span class="small" style="color:#0033FF;">' + json_data['msg'] + '</span>';
			var result_idx = json_data['glink_idx'];
		}
		else {//��ǰ��ũ
					
			var resMsg = '<span class="small" style="color:#0033FF;"><div>��ũ����</div><div>���� ��ǰ�ڵ� : ' + json_data['mall_goods_cd'] + '</div></span>';
			var result_idx = json_data['goods_no'];
		}
	}
	else if(json_data['code'] && json_data['code'] != '000') {//��ǰ��ũ���н� �޼��� ���
		if(json_data['msg'] == '' || json_data['msg'] == null || json_data['msg'] == 'null') {
			json_data['msg'] = 'return ���� �����ϴ�.';
		}

		if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//������ũ or ���º���
			var result_idx = json_data['glink_idx'];
		}
		else {//��ǰ��ũ
			var result_idx = json_data['goods_no'];
		}
		var resMsg = '<span class="small" style="color:#CD0000;"><div>��ũ����</div><div>���� �޼��� : ' + json_data['msg'] + '</div></span>';
	}
	else {//���� ī�װ� ���ϰ����� �ɼǻ���
		var obj = document.getElementsByName('mall_cate[]');
		createOption(obj, data);
		return;
	}

	document.getElementById('logBoard' + result_idx).style.color = '#0033FF';//�������
	document.getElementById('logBoard' + result_idx).innerHTML = '�Ϸ�';//�������
	document.getElementById('resBoard' + result_idx).innerHTML = resMsg;//��ũ���
	link_no++;

	if(json_data['mode'] && ((json_data['mode'] == 'modify') || (json_data['mode'] == 'status') || (json_data['mode'] == 'extend'))) {//������ũ or ���º���
		linkAjax(json_data['mode']);
	}
	else {//��ǰ��ũ
		linkAjax();
	}
}

function linkAjax(mode) {//��ũajax ȣ��
	var param = new Array();
	var pro_idx = link_no;

	if(!mode) var obj = document.getElementsByName('goods_no' + pro_idx);
	else var obj = document.getElementsByName('glink_idx' + pro_idx);

	if(obj.length == 0) {//��ũ����
		document.getElementsByName('link_complete_check')[0].value = 'N';

		if(!mode) {//��ǰ��ũ �Ϸ�
			//ī�װ� disabled ����
			var obj = document.getElementsByName('mall_cate[]');
			for(var i = 0; i < obj.length; i++) {
				document.getElementsByName('mall_cate[]')[i].disabled = false;
			}

			document.getElementById('link_page_btn').src = '../img/btn_linkbaro.gif';//��ǰ��ũ Ȱ��ȭ ��ư �̹���
			document.getElementById('link_goods_btn').src = '../img/btn_linkpro.gif';//��ǰ��ũ Ȱ��ȭ ��ư �̹���
			return;
		}
		else {//������ũ/���º��� �Ϸ�
			document.getElementById('link_pop_close').src = '../img/btn_delinum_close.gif';//�ݱ� Ȱ��ȭ ��ư �̹���
		}
		return;
	}

	if(mode == 'modify') {//��ǰ������ũ
		var glink_idx = obj[0].value;
		var price = document.getElementsByName('price' + glink_idx)[0].value;
		var delivery_price = document.getElementsByName('delivery_price' + glink_idx)[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">������.......</span>';
		sellyLink.linkModifyGoods(glink_idx, price, delivery_price);
	}
	else if(mode == 'status') {//��ǰ���º���
		var glink_idx = obj[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">������.......</span>';
		var sale_status = document.getElementsByName('sale_status')[0].value;
		sellyLink.linkGoodsStatus(glink_idx, sale_status);
	}
	else if(mode == 'extend') {//��ǰ�ǸűⰣ ����
		var glink_idx = obj[0].value;
		document.getElementById('logBoard' + glink_idx).innerHTML = '<span class="small" style="color:#228B22;">������.......</span>';
		var extend_term = document.getElementsByName('extend_term')[0].value;
		var extend_set = document.getElementsByName('extend_set')[0].value;
		var sale_term_start = document.getElementsByName('sale_term_start')[0].value;
		var sale_term_end = document.getElementsByName('sale_term_end')[0].value;
		var mall_cd = document.getElementsByName('mall_cd')[0].value;

		sellyLink.linkGoodsExtend(glink_idx, extend_term, extend_set, sale_term_start, sale_term_end, mall_cd);

	}
	else {//��ǰ��ũ
		var mall_cd = document.getElementsByName('mall_cd')[0].value;
		var set_cd = document.getElementsByName('set_cd')[0].value;
		var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
		var mall_category_cd = document.getElementsByName('mall_category_cd')[0].value;
		var mall_category_nm = document.getElementsByName('mall_category_nm')[0].value;
		var goods_no = obj[0].value;
		var price = document.getElementsByName('price' + goods_no)[0].value;
		var delivery_price = document.getElementsByName('delivery_price' + goods_no)[0].value;
		document.getElementById('logBoard' + goods_no).innerHTML = '<span class="small" style="color:#228B22;">������.......</span>';

		sellyLink.linkGoods(mall_cd, set_cd, mall_login_id, mall_category_cd, mall_category_nm, goods_no, price, delivery_price);
	}
}

function goodsLink() {//��ǰ��ũ
	var link_check = document.getElementsByName('link_check')[0].value;
	if(link_check == 'N') {//��ũ���� �õ���
		if(!cateSelectCheck()) {//ī�װ� üũ/hidden�Է�
			alert('ī�װ��� ������ �ּ���.');
			return;
		}
		if(!cateCheck()) {//ī�װ�üũ
			alert('������ ī�װ����� ������ �ּ���.');
			return;
		}

		//ī�װ� disabled ����
		var obj = document.getElementsByName('mall_cate[]');
		for(var i = 0; i < obj.length; i++) {
			document.getElementsByName('mall_cate[]')[i].disabled = true;
		}

		document.getElementById('link_btn').src = '../img/btn_link_out.gif';//��ǰ��ũ ��Ȱ��ȭ ��ư �̹���
		document.getElementById('link_page_btn').src = '../img/btn_linkbaro_out.gif';//��ǰ��ũ�ٷΰ��� ��Ȱ��ȭ ��ư �̹���
		document.getElementById('link_goods_btn').src = '../img/btn_linkpro_out.gif';//��ũ��ǰ�����ٷΰ��� ��Ȱ��ȭ ��ư �̹���
		document.getElementsByName('link_check')[0].value = 'Y';//��ũ��/��ũ�Ϸ�
		document.getElementsByName('link_complete_check')[0].value = 'P';//������
	}
	else return;//��ũ������� return

	linkAjax();
}

function cateCheck() {//������ ī�װ� ���� üũ

	var mall_cd = document.getElementsByName('mall_cd')[0].value;

	var form = document.linkInfo;
	var last_cate = form.last_cate.value;
	if(last_cate == 'N') return false;
	else return true;
}

function cateSelectCheck() {//��ǰ��ũ�� ī�װ� ����üũ/hidden�Է�
	var cate_obj = document.getElementsByName('mall_cate[]');
	var category_cd = '';
	var category_nm = '';
	var mall_cd = document.getElementsByName('mall_cd')[0].value;

	for(var i = 0; i < cate_obj.length; i++) {
		if(cate_obj[i].value) {
			category_cd += cate_obj[i].value + '>';
			category_nm += cate_obj[i].options[cate_obj[i].selectedIndex].text + ' > ';
		}
	}

	if(category_cd && category_nm) {
		document.getElementsByName('mall_category_cd')[0].value = category_cd;
		document.getElementsByName('mall_category_nm')[0].value = category_nm;
		return true;
	}
	else {
		return false;
	}
}

function cateSelect(obj, category_type) {//ī�װ� ���ý� ���� ī�װ� �ҷ�����
	var elements = document.getElementsByName(obj.name);
	for(var i = 0; i < elements.length; i++) {
		if(elements[i] == obj) {
			var idx = i+1;
		}
	}

	var tmp_obj = document.getElementsByName('mall_cate[]');
	var mall_cd = document.getElementsByName('mall_cd')[0].value;
	var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
	var category_cd = obj.value;
	var last_cate = document.getElementsByName('last_cate')[0];
	sellyLink.ajaxMallCategory(tmp_obj, mall_cd, mall_login_id, category_type, category_cd);
}

function page_move(mode) {//link_page = ��ǰ��ũ�ٷΰ���, link_goods = ��ũ��ǰ�����ٷΰ���
	var check = document.getElementsByName('link_complete_check')[0].value;
	if(check == 'P') return;//��ũ���϶� ������ �̵� ����(P = ��ũ������, N = ��ũ������/�Ϸ���)

	if(mode == 'link_page') {//��ǰ��ũ �ٷΰ���
		opener.parent.location.replace("goodsLink.php");
	}
	else if( mode == 'link_goods') {//��ũ��ǰ���� �ٷΰ���
		opener.parent.location.replace("linkGoodsList.php");
	}
	else {
		opener.parent.location.reload();
	}
	self.close();
}

function goodsModifyLink() {
	document.getElementById('link_pop_close').src = '../img/btn_delinum_close.gif';//�ݱ� ��Ȱ��ȭ ��ư �̹���
	document.getElementsByName('link_complete_check')[0].value = 'P';//������

	linkAjax(mode[0].value);
}

window.onload = function(){
	if(mode[0].value) {//��ǰ������ũ, ���º���, �Ⱓ����
		goodsModifyLink();

	}
	else {//��ǰ��ũ
		//ī�װ� �ε� START
		var obj = document.getElementsByName('mall_cate[]');
		var mall_cd = document.getElementsByName('mall_cd')[0].value;
		var mall_login_id = document.getElementsByName('mall_login_id')[0].value;
		var category_type = 'L';
		var last_cate = document.getElementsByName('last_cate')[0];
		sellyLink.ajaxMallCategory(obj, mall_cd, mall_login_id, category_type, '');
		//ī�װ� �ε� END

	}
	table_design_load();
}
</script>

<form name="linkInfo">
	<input type="hidden" id="mall_cd" name="mall_cd" value="<?=$mall_cd?>" />
	<input type="hidden" name="mall_login_id" value="<?=$set_data['mall_login_id']?>" />
	<input type="hidden" name="set_cd" value="<?=$set_data['set_cd']?>" />
	<input type="hidden" name="mall_category_cd" value=""><!--���� ī�װ�-->
	<input type="hidden" name="mall_category_nm" value=""><!--���� ī�װ�-->
	<input type="hidden" name="last_cate" value="N"><!--������ ī�װ����� -->
	<input type="hidden" name="link_check" value="N"><!-- ��ũ��ư Ȱ��ȭ����(N = Ȱ��ȭ, Y = ��Ȱ��ȭ -->
	<input type="hidden" name="link_complete_check" value="N"><!-- ��ũ�Ϸ� ����(N = ������/����Ϸ�, P = ������ -->
	<input type="hidden" name="mode" value="<?=$mode?>"><!-- '' = ��ǰ��ũ, modify = ����, status = ���º��� -->
	<input type="hidden" name="sale_status" value="<?=$_POST['sale_status']?>"><!-- ��ǰ���º���� ������ ���°� -->
	<input type="hidden" name="extend_term" value="<?=$_POST['extend_term']?>"><!-- �Ⱓ����(�Ⱓ������ - �ڵ尪) -->
	<input type="hidden" name="extend_set" value="<?=$_POST['extend_set']?>"><!-- �Ⱓ����(�Ⱓ��������) -->
	<input type="hidden" name="sale_term_start" value="<?=$_POST['sale_term_start']?>"><!-- �Ⱓ����(�Ⱓ������ - �ǸŽ�����) -->
	<input type="hidden" name="sale_term_end" value="<?=$_POST['sale_term_end']?>"><!-- �Ⱓ����(�Ⱓ������ - �Ǹ�������) -->

	<div class="title title_top"><?=$title_nm?><span>��ũ �������Դϴ�. �Ϸ� ���� â�� �ݰų� esc��ư�� �����ø� ��ũ�� �ߴܵ˴ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2"></a></div>
	<? if(!$mode) { //��ǰ��ũ ?>
	<table class="tb">
		<col class="cellC" style="wiidth:100px"><col class="cellC"  style="width:100px"><col class="cellL">
		<tr>
			<td rowspan="3">��Ʈ����</td>
			<td bgcolor="F8F8F8">����</td>
			<td><?=$arr_mall_cd[$mall_cd]?></td>
		</tr>
		<tr>
			<td>���� ID</td>
			<td><?=$set_data['mall_login_id']?></td>
		</tr>
		<tr>
			<td>��Ʈ��</td>
			<td><?=$set_data['set_nm']?></td>
		</tr>
		<tr>
			<td>ī�װ�����</td>
			<td>ī�װ�����</td>
			<td>
				<select name="mall_cate[]" onchange="cateSelect(this, 'M');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, 'S');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, 'D');">
				</select>
				<select name="mall_cate[]" onchange="cateSelect(this, '');">
				</select>
			</td>
		</tr>
	</table>
	<? } ?>
</form>

<div style="padding-top:15px"></div>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<col width="60%"><col width="20%" align="center"><col width="20%" align="center">
	<tr class="rndbg">
		<th>��ǰ��</th>
		<th>�������</th>
		<th>��ũ ���</th>
	</tr>
	<? for($i = 0; $i < count($post_data['chk']); $i++) {?>
	<tr height="40">
		<td style="padding:6px;">
			<?=$post_data['goodsnm'][$post_data['chk'][$i]]?>
			<input type="hidden" name="price<?=$post_data['chk'][$i]?>" value="<?=$post_data['price'][$post_data['chk'][$i]]?>" /><!--��ǰ��-->
			<input type="hidden" id="delivery_price<?=$post_data['chk'][$i]?>" name="delivery_price<?=$post_data['chk'][$i]?>" value="<?=$post_data['goods_delivery'][$post_data['chk'][$i]]?>" /><!--��ۺ�-->
			<input type="hidden" name="<?=$chk_nm?><?=$i?>" value="<?=$post_data['chk'][$i]?>" /><!--������ȣ(��ǰ�ڵ�)-->
		</td>
		<td id="process_<?=$post_data['chk'][$i]?>">
			<font id="logBoard<?=$post_data['chk'][$i]?>" class="small1" color="#AAAAAA">���</font>
		</td>
		<td id="link_<?=$post_data['chk'][$i]?>">
			<span id="resBoard<?=$post_data['chk'][$i]?>"></span>
		</td>
	</tr>
	<tr><td colspan="3" class="rndline"></td></tr>
	<? } ?>
</table>

<div align="right" style="margin:30px 20px 0px 0px;">
	<? if(!$mode) { //��ǰ��ũ ?>
	<span style="margin-right:10px;"><input id="link_btn" type="image" src="../img/btn_link_on.gif" align="absbottom" alt="��ũ�ϱ�" onclick="goodsLink();"></span><!--��ũ�ϱ�-->
	<span style="margin-right:10px;"><input id="link_page_btn" type="image" src="../img/btn_linkbaro.gif" align="absbottom" alt="��ǰ��ũ �ٷΰ���" onclick="page_move('link_page');"></span><!--��ǰ��ũ �ٷΰ���-->
	<input id="link_goods_btn" type="image" src="../img/btn_linkpro.gif" align="absbottom" alt="��ũ��ǰ���� �ٷΰ���" onclick="page_move('link_goods');"><!--��ũ��ǰ���� �ٷΰ���-->
	<? } else { //������ũ/���º��� ?>
	<input id="link_pop_close" type="image" src="../img/btn_delinum_close.gif" align="absbottom" alt="�ݱ�" onclick="page_move();"><!--�ݱ�-->
	<? } ?>
</div>