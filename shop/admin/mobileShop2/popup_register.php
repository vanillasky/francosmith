<?
/*********************************************************
* ���ϸ�     :  popup_list.php
* ���α׷��� :	����ϼ� �˾�����Ʈ
* �ۼ���     :  dn
* ������     :  2012.05.08
**********************************************************/	

$location = "����ϼ� > �˾�â ����";
include "../_header.php";
include "../../conf/design.main.php";

$mpopup_no = $_GET['mpopup_no'];

if($mpopup_no) {
	$popup_query = $db->_query_print('SELECT * FROM '.GD_MOBILE_POPUP.' WHERE mpopup_no=[i]', $mpopup_no);
	$res_popup = $db->_select($popup_query);
	$popup_data = $res_popup[0];

	$popup_data['position_gap_'.$popup_data['position_type']] = $popup_data['position_gap'];
	$popup_data['start_date_'.$popup_data['open_type']] = substr($popup_data['start_date'], 0, 4).substr($popup_data['start_date'], 5, 2).substr($popup_data['start_date'], 8, 2);
	$popup_data['end_date_'.$popup_data['open_type']] = substr($popup_data['end_date'], 0, 4).substr($popup_data['end_date'], 5, 2).substr($popup_data['end_date'], 8, 2);
	$popup_data['start_time_'.$popup_data['open_type']] = $popup_data['start_time'];
	$popup_data['end_time_'.$popup_data['open_type']] = $popup_data['end_time'];
}
else {
	# �⺻�� ���� #
	$popup_data['open'] = '1';
	$popup_data['position_type'] = 'top';
	$popup_data['open_type'] = '0';
	$popup_data['popup_type'] = '0';
	$popup_data['page_type'] = 'main';
	$popup_data['link_type'] = '0';
}

$checked['open'][$popup_data['open']] = 'checked';
$checked['position_type'][$popup_data['position_type']] = 'checked';
$checked['open_type'][$popup_data['open_type']] = 'checked';
$checked['popup_type'][$popup_data['popup_type']] = 'checked';
$checked['page_type'][$popup_data['page_type']] = 'checked';
$checked['link_type'][$popup_data['link_type']] = 'checked';

$selected['start_time_0'][$popup_data['start_time_0']] = 'selected';
$selected['end_time_0'][$popup_data['end_time_0']] = 'selected';
$selected['start_time_1'][$popup_data['start_time_1']] = 'selected';
$selected['end_time_1'][$popup_data['end_time_1']] = 'selected';
$selected['cookie_renewal_time'][$popup_data['cookie_renewal_time']] = 'selected';

?>
<script type="text/javascript">
function setPageType(page_type) {
	$('tr-page-cate').style.display = 'none';

	if($('tr-page-'+page_type)) {
		$('tr-page-'+page_type).style.display = 'block';
	}
}

function setLinkType(link_type) {
	$('tr-link-0').style.display = 'none';
	$('tr-link-1').style.display = 'none';
	$('tr-link-2').style.display = 'none';

	if($('tr-link-'+link_type)) {
		$('tr-link-'+link_type).style.display = 'block';
	}
}

function list_goods() {
	var category = '';
	var goodsnm = document.getElementById('goodsnm').value;
	$('ifrm_goods').src = "iframe.goodslist.php?goodsnm=" + goodsnm;
}

function selectGoods(goodsnm, goodsno) {
	var frm = document.form;
	frm.link_goodsnm.value = goodsnm;
	frm.link_goodsno.value = goodsno;
}
document.observe('dom:loaded', function() {

	var arr_page_type = document.getElementsByName('page_type');
	
	var page_type = '';
	for (var i=0; i<arr_page_type.length; i++) {
		if(arr_page_type[i].checked == true) {
			page_type = arr_page_type[i].value;
		}
	}

	var arr_link_type = document.getElementsByName('link_type');
	
	var link_type = '';
	for (var i=0; i<arr_link_type.length; i++) {
		if(arr_link_type[i].checked == true) {
			link_type = arr_link_type[i].value;
		}
	}

	setPageType(page_type);
	setLinkType(link_type);
});

</script>
<div class="title title_top">�˾�â ��� </div>
<form name="form" method="post" action="indb.php" onsubmit="return chkForm(this)" enctype="multipart/form-data">
<input type="hidden" name="mode" value="popup_regist" />
<input type="hidden" name="mpopup_no" id="mpopup_no" value="<?=$mpopup_no?>" />
<table class="tb">
<col class="cellC"><col class="cellL">
<tbody style="height:26px;">
<tr>
	<td>�˾�����</td>
	<td>
		<input type="text" name="popup_title" size="50" value="<?=$popup_data['popup_title']?>" required="required" />
	</td>
</tr>
<tr>
	<td>�̹������ε�</td>
	<td>
		<div>
			<input type="file" name="popup_img" size="50" />
			<a href="javascript:webftpinfo( '<?=( $popup_data['popup_img'] != '' ? '/data/m/upload_img/'. $popup_data['popup_img'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="�̹��� ����" align="absmiddle"></a>
			<? if ( $popup_data['popup_img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="del_popup_img" value="Y">����</span><? } ?>
			<input type="hidden" name="popup_img_hidden" value="<?=$popup_data['popup_img']?>" />
		</div>
		<div>
			<span class="extext">���� ������ : 00px X 00px</span>
		</div>
	</td>
</tr>
<tr>
	<td>��¿���</td>
	<td class="noline">
		<label><input type="radio" name="open" value="1" <?=$checked['open']['1']?> required="required" /> ���</label>
		<label><input type="radio" name="open" value="0" <?=$checked['open']['0']?> required="required" /> �����</label>
	</td>
</tr>
<tr>
	<td>â��ġ</td>
	<td >
		<div>
			<label class="noline"><input type="radio" name="position_type" value="top" <?=$checked['position_type']['top']?> required="required" /> ��ܿ���</label>
			<input type="text" name="position_gap_top" value="<?=$popup_data['position_gap_top']?>" /> px
		</div>
		<div>
			<label class="noline"><input type="radio" name="position_type" value="bottom" <?=$checked['position_type']['bottom']?> required="required" /> �ϴܿ���</label>
			<input type="text" name="position_gap_bottom" value="<?=$popup_data['position_gap_bottom']?>" /> px
		</div>
	</td>
</tr>
<tr>
	<td>Ư���Ⱓ����<br />������ ����</td>
	<td >
		<div>
			<label class="noline"><input type="radio" name="open_type" value="0" <?=$checked['open_type']['0']?> required="required" /> Ư�� �Ⱓ ���� �˾�â�� �����ϴ�</label>
		</div>
		<div>
			<div>
				������: <input type="text" name="start_date_0" size="10" value="<?=$popup_data['start_date_0']?>" onclick="calendar(event);" class="cline" />&nbsp;&nbsp;&nbsp;
				���۽ð�: 
				<select name="start_time_0">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['start_time_0'][$i]?>><?=$i?>��</option>
					<? } ?>			
				</select>
			</div>
			<div>
				������: <input type="text" name="end_date_0" size="10" value="<?=$popup_data['end_date_0']?>" onclick="calendar(event);" class="cline" />&nbsp;&nbsp;&nbsp;
				����ð�: 
				<select name="end_time_0">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['end_time_0'][$i]?>><?=$i?>��</option>
					<? } ?>			
				</select>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>Ư���Ⱓ����<br />Ư���ð����� ����</td>
	<td >
		<div>
			<label class="noline"><input type="radio" name="open_type" value="1" <?=$checked['open_type']['1']?> required="required" /> Ư�� �Ⱓ ���� Ư���� �ð����� �˾�â�� �����ϴ�</label>
		</div>
		<div>
			<div>
				������: <input type="text" name="start_date_1" size="10" value="<?=$popup_data['start_date_1']?>" onclick="calendar(event);" class="cline" />&nbsp;&nbsp;&nbsp;
				���۽ð�: 
				<select name="start_time_1">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['start_time_1'][$i]?>><?=$i?>��</option>
					<? } ?>			
				</select>
			</div>
			<div>
				������: <input type="text" name="end_date_1" size="10" value="<?=$popup_data['end_date_1']?>" onclick="calendar(event);" class="cline" />&nbsp;&nbsp;&nbsp;
				����ð�: 
				<select name="end_time_1">
					<? for($i=0; $i<24; $i++) { ?>
					<option value="<?=$i?>" <?=$selected['end_time_1'][$i]?>><?=$i?>��</option>
					<? } ?>			
				</select>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>�����˾� ���Žð�</td>
	<td >
		<select name="cookie_renewal_time">
			<? for($i=0; $i<24; $i++) { ?>
			<option value="<?=$i?>" <?=$selected['cookie_renewal_time'][$i]?>><?=$i?></option>
			<? } ?>			
		</select>
		�ð����� �ٽ� ����
	</td>
</tr>
<tr>
	<td>�˾� Ÿ��</td>
	<td class="noline">
		<label><input type="radio" name="popup_type" value="1" <?=$checked['popup_type']['1']?> />�̵����̾�</label>&nbsp;&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="popup_type" value="0" <?=$checked['popup_type']['0']?> />�������̾�</label>
	</td>
</tr>
<tr>
	<td>���ȭ�� ����</td>
	<td class="noline">
		<label><input type="radio" name="page_type" value="main" <?=$checked['page_type']['main']?> onclick="javascript:setPageType(this.value);" />���ο� ���</label>&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="page_type" value="cate" <?=$checked['page_type']['cate']?> onclick="javascript:setPageType(this.value);" />ī�װ��� ���</label>
	</td>
</tr>
<tr id="tr-page-cate" style="display:none;">
	<td>ī�װ��� ���</td>
	<td>
		<script type="text/javascript">new categoryBox('category[]',4,'<?=$popup_data[category]?>');</script>	
	</td>
</tr>
<tr>
	<td>�˾���ũ����</td>
	<td class="noline">
		<label><input type="radio" name="link_type" value="1" onclick="javascript:setLinkType(this.value);" <?=$checked['link_type']['1']?> />��ǰ��������</label>
		<label><input type="radio" name="link_type" value="2" onclick="javascript:setLinkType(this.value);" <?=$checked['link_type']['2']?> />�з�������</label>
		<label><input type="radio" name="link_type" value="0" onclick="javascript:setLinkType(this.value);" <?=$checked['link_type']['0']?> />URL �����Է�</label>
	</td>
</tr>
<tr id="tr-link-1" style="display:none;">
	<td>��ǰ����</td>
	<td>
		<input type="text" id="goodsnm" name="goodsnm" value="" />
		<a href="javascript:list_goods('category')"><img src="../img/i_search.gif" align=absmiddle></a>
		<div style="margin-top:5px;">
			<iframe id="ifrm_goods" style="width:500px;border:solid 1px;border-color:#cccccc;oveflow-x:hidden" frameborder="0" scrolling="yes" ></iframe>
		</div>
		<div style="margin-top:5px;"> 
			<input type="text" name="link_goodsnm" size="100" value="<?=$popup_data['link_goodsnm']?>" readonly />
			<input type="hidden" name="link_goodsno" value="<?=$popup_data['link_goodsno']?>" />
		</div>
	</td>
</tr>
<tr id="tr-link-2" style="display:none;">
	<td>�з�������</td>
	<td class="noline">
		<script type="text/javascript">new categoryBox('link_category[]',4,'<?=$popup_data[link_category]?>');</script>
	</td>
</tr>
<tr id="tr-link-0" style="display:none;">
	<td>URL�Է�</td>
	<td>
		<input type=text name="link_url" size="100" value="<?=$popup_data['link_url']?>" />
	</td>
</tr>
</table>
<div class=button>
<? if($mpopup_no) { ?>
	<input type=image src="../img/btn_modify.gif">
<? }else{ ?>
	<input type=image src="../img/btn_register.gif">
<? } ?>
</div>
</form>
<? include "../_footer.php"; ?>